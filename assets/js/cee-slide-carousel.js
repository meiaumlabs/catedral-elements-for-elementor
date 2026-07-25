/**
 * Catedral Elements — Slide Carrossel (Scroll Horizontal).
 *
 * Vanilla JS, zero dependências. Lê TODA a configuração dos data-* attributes
 * expostos pelo markup PHP — nenhum valor hardcoded de comportamento.
 * Suporta múltiplas instâncias na mesma página e reinicialização no editor
 * Elementor.
 */
( function () {
	'use strict';

	var REDUCED_MOTION = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/**
	 * Controlador de uma instância do carrossel.
	 *
	 * @param {HTMLElement} root Elemento raiz .cee-slide-carousel.
	 */
	function Carousel( root ) {
		this.root = root;
		this.track = root.querySelector( '.cee-slide-carousel__track' );
		this.slides = Array.prototype.slice.call( root.querySelectorAll( '.cee-slide' ) );
		this.navItems = Array.prototype.slice.call( root.querySelectorAll( '.cee-nav__item' ) );

		if ( ! this.track || this.slides.length === 0 ) {
			return;
		}

		// --- Configuração lida EXCLUSIVAMENTE dos data-* ---
		var d = root.dataset;
		this.cfg = {
			autoplay: d.autoplay === 'true',
			autoplayInterval: parseInt( d.autoplayInterval, 10 ) || 5000,
			pauseHover: d.autoplayPauseHover === 'true',
			pauseInteraction: d.autoplayPauseInteraction === 'true',
			loop: d.loop === 'true',
			speed: parseInt( d.transitionSpeed, 10 ) || 800,
			easing: d.transitionEasing || 'cubic-bezier(0.76, 0, 0.24, 1)',
			scrollEnable: d.scrollEnable === 'true',
			scrollPin: d.scrollPin === 'true',
			scrollSnap: d.scrollSnap === 'true',
			scrollRelease: d.scrollRelease !== 'false',
			sensitivity: parseInt( d.scrollSensitivity, 10 ) || 150,
			scrollTouch: d.scrollTouch === 'true',
			count: parseInt( d.slidesCount, 10 ) || this.slides.length
		};

		this.currentIndex = parseInt( d.currentIndex, 10 ) || 0;
		this.animating = false;
		this.pinned = false;
		this.inView = false;
		this.wheelBuffer = 0;
		this.autoplayTimer = null;
		this.resumeTimer = null;
		this.lockTimer = null;
		this.hovering = false;

		// Aplica variáveis de transição (do data-*) no track.
		this.applyTrackTransition();

		this.bind();
		this.goToSlide( this.currentIndex, { source: 'init', immediate: true } );
		this.observe();

		if ( this.cfg.autoplay && ! REDUCED_MOTION ) {
			// Delay inicial permite a pinagem estabilizar.
			var self = this;
			window.setTimeout( function () {
				self.startAutoplay();
			}, 500 );
		}

		root.__ceeCarousel = this;
	}

	Carousel.prototype.applyTrackTransition = function () {
		if ( REDUCED_MOTION ) {
			this.track.style.transition = 'none';
			return;
		}
		this.track.style.transition = 'transform ' + this.cfg.speed + 'ms ' + this.cfg.easing;
	};

	/* =================================================================
	 * Núcleo: goToSlide
	 * ============================================================== */

	Carousel.prototype.goToSlide = function ( index, opts ) {
		opts = opts || {};
		var total = this.slides.length;

		// Clamp / wrap conforme loop.
		if ( this.cfg.loop ) {
			index = ( ( index % total ) + total ) % total;
		} else {
			index = Math.max( 0, Math.min( total - 1, index ) );
		}

		if ( index === this.currentIndex && opts.source !== 'init' ) {
			return;
		}

		// Zera o buffer de scroll ao navegar por qualquer source diferente de scroll.
		if ( opts.source !== 'scroll' ) {
			this.wheelBuffer = 0;
		}

		this.currentIndex = index;
		this.root.dataset.currentIndex = String( index );

		// Move a track.
		var offset = -100 * index;
		if ( opts.immediate || REDUCED_MOTION ) {
			var prev = this.track.style.transition;
			this.track.style.transition = 'none';
			this.track.style.transform = 'translate3d(' + offset + '%, 0, 0)';
			// Força reflow e restaura a transição.
			void this.track.offsetWidth;
			this.track.style.transition = prev;
		} else {
			this.track.style.transform = 'translate3d(' + offset + '%, 0, 0)';
		}

		this.syncSlides( index );
		this.syncNav( index );
		this.setAnimating();

		// (Re)inicia autoplay se a mudança não veio do próprio autoplay.
		if ( opts.source !== 'autoplay' && opts.source !== 'init' ) {
			if ( this.cfg.pauseInteraction ) {
				this.pauseAutoplayTemporarily();
			} else {
				this.restartAutoplay();
			}
		}
	};

	Carousel.prototype.setAnimating = function () {
		var self = this;
		this.animating = true;
		this.root.classList.add( 'cee-slide-carousel--animating' );

		if ( this.lockTimer ) {
			window.clearTimeout( this.lockTimer );
		}
		var lock = REDUCED_MOTION ? 0 : this.cfg.speed;
		this.lockTimer = window.setTimeout( function () {
			self.animating = false;
			self.root.classList.remove( 'cee-slide-carousel--animating' );
		}, lock );
	};

	Carousel.prototype.syncSlides = function ( index ) {
		this.slides.forEach( function ( slide, i ) {
			var isActive = i === index;
			slide.classList.toggle( 'cee-slide--active', isActive );
			slide.setAttribute( 'aria-hidden', isActive ? 'false' : 'true' );
		} );
	};

	Carousel.prototype.syncNav = function ( index ) {
		this.navItems.forEach( function ( btn, i ) {
			var isActive = i === index;
			btn.classList.toggle( 'cee-nav__item--active', isActive );
			btn.setAttribute( 'aria-current', isActive ? 'true' : 'false' );
		} );
	};

	Carousel.prototype.next = function ( source ) {
		this.goToSlide( this.currentIndex + 1, { source: source || 'api' } );
	};

	Carousel.prototype.prev = function ( source ) {
		this.goToSlide( this.currentIndex - 1, { source: source || 'api' } );
	};

	Carousel.prototype.isAtEnd = function () {
		return this.currentIndex >= this.slides.length - 1;
	};

	Carousel.prototype.isAtStart = function () {
		return this.currentIndex <= 0;
	};

	/* =================================================================
	 * Autoplay
	 * ============================================================== */

	Carousel.prototype.startAutoplay = function () {
		if ( ! this.cfg.autoplay || REDUCED_MOTION ) {
			return;
		}
		this.stopAutoplay();
		var self = this;
		this.root.classList.add( 'cee-slide-carousel--autoplay' );
		this.autoplayTimer = window.setInterval( function () {
			// Não avança se fora da viewport ou em hover pausado.
			if ( ! self.inView ) {
				return;
			}
			if ( self.hovering && self.cfg.pauseHover ) {
				return;
			}
			self.goToSlide( self.currentIndex + 1, { source: 'autoplay' } );
		}, this.cfg.autoplayInterval );
	};

	Carousel.prototype.stopAutoplay = function () {
		if ( this.autoplayTimer ) {
			window.clearInterval( this.autoplayTimer );
			this.autoplayTimer = null;
		}
		this.root.classList.remove( 'cee-slide-carousel--autoplay' );
	};

	Carousel.prototype.restartAutoplay = function () {
		if ( this.cfg.autoplay && ! REDUCED_MOTION ) {
			this.startAutoplay();
		}
	};

	/**
	 * Pausa após interação e retoma depois de 3s de inatividade.
	 */
	Carousel.prototype.pauseAutoplayTemporarily = function () {
		if ( ! this.cfg.autoplay ) {
			return;
		}
		this.stopAutoplay();
		if ( this.resumeTimer ) {
			window.clearTimeout( this.resumeTimer );
		}
		var self = this;
		this.resumeTimer = window.setTimeout( function () {
			self.startAutoplay();
		}, 3000 );
	};

	/* =================================================================
	 * IntersectionObserver — viewport + pin
	 * ============================================================== */

	Carousel.prototype.observe = function () {
		var self = this;
		if ( ! ( 'IntersectionObserver' in window ) ) {
			this.inView = true;
			return;
		}

		// Observer de visibilidade parcial (para o autoplay não contar fora da tela).
		this.visObserver = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				self.inView = entry.isIntersecting;
				if ( ! entry.isIntersecting ) {
					self.unpin();
					// Para o autoplay quando o widget sai da viewport.
					if ( self.autoplayTimer ) {
						self._viewportPaused = true;
						self.stopAutoplay();
					}
				} else if ( self._viewportPaused ) {
					// Retoma ao voltar à viewport se foi pausado por ela.
					self._viewportPaused = false;
					self.restartAutoplay();
				}
			} );
		}, { threshold: 0.15 } );
		this.visObserver.observe( this.root );

		if ( ! this.cfg.scrollEnable || ! this.cfg.scrollPin ) {
			return;
		}

		// Observer de pin — quando o widget ocupa quase toda a viewport.
		this.pinObserver = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( entry.isIntersecting && entry.intersectionRatio >= 0.9 ) {
					self.pin();
				} else {
					self.unpin();
				}
			} );
		}, { threshold: [ 0.9, 1 ] } );
		this.pinObserver.observe( this.root );
	};

	Carousel.prototype.pin = function () {
		if ( this.pinned || REDUCED_MOTION ) {
			return;
		}
		this.pinned = true;
		this.root.dataset.pinned = 'true';
		this.root.classList.add( 'cee-slide-carousel--pinned' );
		document.body.style.overflow = 'hidden';
		document.body.style.scrollbarGutter = 'stable';
	};

	Carousel.prototype.unpin = function () {
		if ( ! this.pinned ) {
			return;
		}
		this.pinned = false;
		this.root.dataset.pinned = 'false';
		this.root.classList.remove( 'cee-slide-carousel--pinned' );
		document.body.style.overflow = '';
		document.body.style.scrollbarGutter = '';
	};

	/* =================================================================
	 * Listeners
	 * ============================================================== */

	Carousel.prototype.bind = function () {
		var self = this;

		// Navegação por clique.
		this.navItems.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var idx = parseInt( btn.dataset.slideIndex, 10 );
				if ( ! isNaN( idx ) ) {
					self.goToSlide( idx, { source: 'nav' } );
				}
			} );
		} );

		// Hover — pausa/retoma autoplay.
		this.root.addEventListener( 'mouseenter', function () {
			self.hovering = true;
			if ( self.cfg.pauseHover ) {
				self.stopAutoplay();
			}
		} );
		this.root.addEventListener( 'mouseleave', function () {
			self.hovering = false;
			if ( self.cfg.pauseHover ) {
				self.restartAutoplay();
			}
		} );

		// Wheel / scroll horizontal com pin.
		if ( this.cfg.scrollEnable ) {
			this.root.addEventListener( 'wheel', function ( e ) {
				self.onWheel( e );
			}, { passive: false } );
		}

		// Teclado (acessibilidade): setas quando o widget tem foco.
		this.root.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowRight' || e.key === 'ArrowDown' ) {
				e.preventDefault();
				self.next( 'keyboard' );
			} else if ( e.key === 'ArrowLeft' || e.key === 'ArrowUp' ) {
				e.preventDefault();
				self.prev( 'keyboard' );
			}
		} );

		// Touch / swipe horizontal.
		if ( this.cfg.scrollTouch ) {
			this.bindTouch();
		}
	};

	Carousel.prototype.onWheel = function ( e ) {
		if ( REDUCED_MOTION ) {
			return;
		}

		// Só captura scroll enquanto pinado (ou se pin desativado, quando em view).
		var shouldCapture = this.cfg.scrollPin ? this.pinned : this.inView;
		if ( ! shouldCapture ) {
			return;
		}

		var delta = e.deltaY;
		var dir = delta > 0 ? 1 : -1;

		// Continuação vertical: ao percorrer todos os slides, libera o scroll da
		// página nos limites. Prevalece sobre o loop (que segue valendo só para o
		// autoplay). Sem release, cai no comportamento antigo de limite sem loop.
		if ( this.cfg.scrollRelease || ! this.cfg.loop ) {
			if ( dir > 0 && this.isAtEnd() ) {
				this.unpin();
				return;
			}
			if ( dir < 0 && this.isAtStart() ) {
				this.unpin();
				return;
			}
		}

		// Enquanto pinado (ou capturando), impede a rolagem da página.
		e.preventDefault();

		// Lock durante animação (snap).
		if ( this.animating && this.cfg.scrollSnap ) {
			return;
		}

		this.wheelBuffer += delta;

		if ( Math.abs( this.wheelBuffer ) >= this.cfg.sensitivity ) {
			var step = this.wheelBuffer > 0 ? 1 : -1;
			this.wheelBuffer = 0;
			this.goToSlide( this.currentIndex + step, { source: 'scroll' } );
		}
	};

	Carousel.prototype.bindTouch = function () {
		var self = this;
		var startX = 0;
		var startY = 0;
		var horizontal = false;

		this.root.addEventListener( 'touchstart', function ( e ) {
			var t = e.touches[ 0 ];
			startX = t.clientX;
			startY = t.clientY;
			horizontal = false;
		}, { passive: true } );

		this.root.addEventListener( 'touchmove', function ( e ) {
			var t = e.touches[ 0 ];
			var dx = t.clientX - startX;
			var dy = t.clientY - startY;
			if ( Math.abs( dx ) > Math.abs( dy ) ) {
				horizontal = true;
				// Impede rolagem da página no swipe horizontal.
				if ( e.cancelable ) {
					e.preventDefault();
				}
			}
		}, { passive: false } );

		this.root.addEventListener( 'touchend', function ( e ) {
			if ( ! horizontal ) {
				return;
			}
			var t = e.changedTouches[ 0 ];
			var dx = t.clientX - startX;
			if ( Math.abs( dx ) >= 50 ) {
				// Swipe para a esquerda → próximo.
				self.goToSlide( self.currentIndex + ( dx < 0 ? 1 : -1 ), { source: 'touch' } );
			}
		}, { passive: true } );
	};

	Carousel.prototype.destroy = function () {
		this.stopAutoplay();
		if ( this.resumeTimer ) {
			window.clearTimeout( this.resumeTimer );
		}
		if ( this.lockTimer ) {
			window.clearTimeout( this.lockTimer );
		}
		if ( this.visObserver ) {
			this.visObserver.disconnect();
		}
		if ( this.pinObserver ) {
			this.pinObserver.disconnect();
		}
		this.unpin();
		this.root.__ceeCarousel = null;
	};

	/* =================================================================
	 * Bootstrap
	 * ============================================================== */

	function initAll( context ) {
		var scope = context || document;
		var nodes = scope.querySelectorAll( '.cee-slide-carousel' );
		Array.prototype.forEach.call( nodes, function ( root ) {
			if ( root.__ceeCarousel || root.classList.contains( 'cee-slide-carousel--empty' ) ) {
				return;
			}
			// eslint-disable-next-line no-new
			new Carousel( root );
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			initAll( document );
		} );
	} else {
		initAll( document );
	}

	// Integração com o editor / frontend do Elementor.
	window.addEventListener( 'elementor/frontend/init', function () {
		if ( ! window.elementorFrontend || ! window.elementorFrontend.hooks ) {
			return;
		}
		window.elementorFrontend.hooks.addAction(
			'frontend/element_ready/cee_slide_carousel.default',
			function ( $scope ) {
				var el = $scope && $scope[ 0 ] ? $scope[ 0 ] : null;
				if ( ! el ) {
					return;
				}
				var root = el.querySelector( '.cee-slide-carousel' );
				if ( ! root ) {
					return;
				}
				// Reinicializa (editor recria o markup a cada mudança).
				if ( root.__ceeCarousel ) {
					root.__ceeCarousel.destroy();
				}
				// eslint-disable-next-line no-new
				new Carousel( root );
			}
		);
	} );
} )();
