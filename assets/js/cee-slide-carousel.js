/**
 * Catedral Elements — Slide Carrossel (Scroll Horizontal com Pin).
 *
 * Vanilla JS, zero dependências. Lê TODA a configuração dos data-* attributes
 * expostos pelo markup PHP — nenhum valor hardcoded de comportamento.
 *
 * Dois modos:
 *  - "pin"    : a seção encaixa e trava (sticky) enquanto a rolagem vertical da
 *               página avança os slides na horizontal. Só depois de percorrer
 *               todos os slides a página segue para a próxima seção. É o motor
 *               scroll-driven (a posição horizontal deriva do scroll vertical).
 *  - "slider" : fallback (pin desligado, reduced-motion ou 1 slide) — carrossel
 *               clássico navegado por menu, teclado, swipe e autoplay.
 *
 * Suporta múltiplas instâncias e reinicialização no editor Elementor.
 */
( function () {
	'use strict';

	var REDUCED_MOTION = window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	// Breakpoint mobile (alinhado ao @media max-width:767px da folha de estilo).
	var MOBILE_BREAKPOINT = 768;

	function clamp( v, min, max ) {
		return Math.max( min, Math.min( max, v ) );
	}

	/**
	 * Controlador de uma instância do carrossel.
	 *
	 * @param {HTMLElement} root Elemento raiz .cee-slide-carousel.
	 */
	function Carousel( root ) {
		this.root = root;
		this.pinEl = root.querySelector( '.cee-slide-carousel__pin' );
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
			scrollPin: d.scrollPinEnabled === 'true',
			scrollPinMobile: d.scrollPinMobile === 'true',
			scrollTouch: d.scrollTouch === 'true',
			count: parseInt( d.slidesCount, 10 ) || this.slides.length
		};

		this.currentIndex = parseInt( d.currentIndex, 10 ) || 0;
		this.animating = false;
		this.inView = false;
		this.hovering = false;
		this.progress = 0;
		this.autoplayTimer = null;
		this.resumeTimer = null;
		this.lockTimer = null;
		this._raf = 0;
		this.pinOffset = 0;

		// Dentro do editor Elementor o wrapper alto sticky atrapalha o canvas —
		// usa o modo slider na edição e reserva o pin para o preview/site.
		var isEditor = !! ( window.elementorFrontend && window.elementorFrontend.isEditMode && window.elementorFrontend.isEditMode() );

		// Em telas pequenas o scroll-jack do pin costuma ser desconfortável ao
		// toque. Por padrão (scrollPinMobile off) o mobile cai para o modo slider
		// navegado por swipe; o pin só vale no mobile se o usuário optar por ele.
		var isSmallScreen = window.innerWidth <= MOBILE_BREAKPOINT;
		var pinAllowedHere = ! isSmallScreen || this.cfg.scrollPinMobile;

		// Decide o modo. Pin exige scroll + pin ligados, movimento permitido e >1 slide.
		this.pinMode = this.cfg.scrollEnable && this.cfg.scrollPin && ! REDUCED_MOTION && this.slides.length > 1 && ! isEditor && pinAllowedHere;

		if ( this.pinMode ) {
			root.dataset.scrollMode = 'pin';
			this.track.style.transition = 'none';
			// No pin o empilhamento é dirigido quadro a quadro pelo scroll —
			// desliga a transição CSS de cada slide para não haver atraso.
			this.slides.forEach( function ( s ) {
				s.style.transition = 'none';
			} );
		} else {
			root.dataset.scrollMode = 'slider';
		}

		this.bindCommon();

		if ( this.pinMode ) {
			this.bindPin();
			// Sincroniza a posição inicial após o layout assentar.
			this.updatePin();
			var self = this;
			window.setTimeout( function () {
				self.updatePin();
			}, 50 );
		} else {
			this.bindSlider();
			this.goToSlide( this.currentIndex, { source: 'init', immediate: true } );
		}

		this.observe();

		if ( this.cfg.autoplay && ! REDUCED_MOTION ) {
			var s = this;
			window.setTimeout( function () {
				s.startAutoplay();
			}, 500 );
		}

		root.__ceeCarousel = this;
	}

	/* =================================================================
	 * Sincronização de estado (comum aos dois modos)
	 * ============================================================== */

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

	Carousel.prototype.setActive = function ( index ) {
		if ( index === this.currentIndex ) {
			return;
		}
		this.currentIndex = index;
		this.root.dataset.currentIndex = String( index );
		this.syncSlides( index );
		this.syncNav( index );
	};

	/* =================================================================
	 * Modo PIN — motor scroll-driven
	 * ============================================================== */

	/**
	 * Lê o offset de ancoragem (top do sticky) resolvido pelo CSS. Como a variável
	 * --cee-pin-offset é responsiva, basta reler no init e no resize.
	 */
	Carousel.prototype.measurePin = function () {
		var pin = this.pinEl || this.root;
		var t = parseFloat( window.getComputedStyle( pin ).top );
		this.pinOffset = isNaN( t ) ? 0 : t;
	};

	/**
	 * Atualiza a translação horizontal a partir do progresso do scroll vertical.
	 * progress 0 → primeiro slide; progress 1 → último slide.
	 */
	Carousel.prototype.updatePin = function () {
		var pin = this.pinEl || this.root;
		var vh = pin.offsetHeight;
		var scrollable = this.root.offsetHeight - vh;
		var last = this.slides.length - 1;

		var progress = 0;
		if ( scrollable > 0 ) {
			var rect = this.root.getBoundingClientRect();
			// A seção começa a travar quando o topo chega em pinOffset; o progresso
			// avança até que o wrapper termine (scrollable percorrido).
			progress = clamp( ( this.pinOffset - rect.top ) / scrollable, 0, 1 );
		}
		this.progress = progress;

		var pos = progress * last;
		this.renderStack( Math.floor( pos ), pos - Math.floor( pos ) );

		this.setActive( Math.round( pos ) );
	};

	/**
	 * Posiciona a pilha de slides a partir do progresso contínuo do scroll.
	 * Slides já vistos (i <= base) ficam assentados; o próximo (base+1) sobe e
	 * entra ACIMA, interpolando opacidade e deslocamento pela fração f; os
	 * seguintes ficam ocultos aguardando a vez.
	 *
	 * @param {number} base Índice do slide de baixo da transição corrente.
	 * @param {number} f    Fração da transição para o próximo (0..1).
	 */
	Carousel.prototype.renderStack = function ( base, f ) {
		for ( var i = 0; i < this.slides.length; i++ ) {
			var s = this.slides[ i ];
			var op, k;
			if ( i <= base ) {
				op = 1;
				k = 0;
			} else if ( i === base + 1 ) {
				op = f;
				k = 1 - f;
			} else {
				op = 0;
				k = 1;
			}
			s.style.opacity = op;
			s.style.transform = 'translate3d(calc(' + k + ' * var(--cee-ov-x, 6%)), calc(' + k + ' * var(--cee-ov-y, 26px)), 0)';
			s.style.zIndex = String( i + 1 );
		}
	};

	/**
	 * Rola a página até a posição de scroll correspondente a um slide.
	 *
	 * @param {number} index Índice do slide alvo.
	 */
	Carousel.prototype.scrollToIndex = function ( index, opts ) {
		opts = opts || {};
		var pin = this.pinEl || this.root;
		var scrollable = this.root.offsetHeight - pin.offsetHeight;
		if ( scrollable <= 0 ) {
			return;
		}
		var last = this.slides.length - 1;
		index = clamp( index, 0, last );
		var frac = last > 0 ? index / last : 0;
		var sectionTop = window.pageYOffset + this.root.getBoundingClientRect().top;
		var target = Math.round( sectionTop + frac * scrollable - this.pinOffset );
		// forwardOnly (autoplay): nunca puxar o usuário pra trás. Se o alvo está
		// na posição atual ou acima dela, o usuário já passou desse ponto —
		// não rola, para respeitar a posição atual dele.
		if ( opts.forwardOnly && target <= window.pageYOffset ) {
			return;
		}
		window.scrollTo( { top: target, behavior: REDUCED_MOTION ? 'auto' : 'smooth' } );
	};

	Carousel.prototype.bindPin = function () {
		var self = this;

		this.measurePin();

		this._onScroll = function () {
			if ( self._raf ) {
				return;
			}
			self._raf = window.requestAnimationFrame( function () {
				self._raf = 0;
				self.updatePin();
			} );
		};
		// O offset (top do sticky) pode mudar por breakpoint — remede no resize.
		this._onResize = function () {
			self.measurePin();
			self._onScroll();
		};
		window.addEventListener( 'scroll', this._onScroll, { passive: true } );
		window.addEventListener( 'resize', this._onResize );

		// Menu → rola até o slide.
		this.navItems.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var idx = parseInt( btn.dataset.slideIndex, 10 );
				if ( ! isNaN( idx ) ) {
					self.scrollToIndex( idx );
				}
			} );
		} );

		// Teclado: setas rolam um slide por vez.
		this.root.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowRight' || e.key === 'ArrowDown' ) {
				e.preventDefault();
				self.scrollToIndex( self.currentIndex + 1 );
			} else if ( e.key === 'ArrowLeft' || e.key === 'ArrowUp' ) {
				e.preventDefault();
				self.scrollToIndex( self.currentIndex - 1 );
			}
		} );

		// Interação manual pausa o autoplay temporariamente.
		if ( this.cfg.autoplay && this.cfg.pauseInteraction ) {
			this._onUserScroll = function () {
				self.pauseAutoplayTemporarily();
			};
			window.addEventListener( 'wheel', this._onUserScroll, { passive: true } );
			window.addEventListener( 'touchmove', this._onUserScroll, { passive: true } );
		}
	};

	/* =================================================================
	 * Modo SLIDER — carrossel clássico
	 * ============================================================== */

	Carousel.prototype.goToSlide = function ( index, opts ) {
		opts = opts || {};
		var total = this.slides.length;

		if ( this.cfg.loop ) {
			index = ( ( index % total ) + total ) % total;
		} else {
			index = clamp( index, 0, total - 1 );
		}

		if ( index === this.currentIndex && opts.source !== 'init' ) {
			return;
		}

		this.currentIndex = index;
		this.root.dataset.currentIndex = String( index );

		// A entrada empilhada (fade in up + sobreposição) vem do CSS via a classe
		// .cee-slide--active. Na carga inicial suprime a animação do primeiro slide.
		if ( opts.immediate ) {
			var active = this.slides[ index ];
			if ( active ) {
				var prevT = active.style.transition;
				active.style.transition = 'none';
				active.classList.add( 'cee-slide--active' );
				void active.offsetWidth;
				active.style.transition = prevT;
			}
		}

		this.syncSlides( index );
		this.syncNav( index );
		this.setAnimating();

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

	Carousel.prototype.bindSlider = function () {
		var self = this;

		this.navItems.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				var idx = parseInt( btn.dataset.slideIndex, 10 );
				if ( ! isNaN( idx ) ) {
					self.goToSlide( idx, { source: 'nav' } );
				}
			} );
		} );

		this.root.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'ArrowRight' || e.key === 'ArrowDown' ) {
				e.preventDefault();
				self.goToSlide( self.currentIndex + 1, { source: 'keyboard' } );
			} else if ( e.key === 'ArrowLeft' || e.key === 'ArrowUp' ) {
				e.preventDefault();
				self.goToSlide( self.currentIndex - 1, { source: 'keyboard' } );
			}
		} );

		if ( this.cfg.scrollTouch ) {
			this.bindTouch();
		}
	};

	Carousel.prototype.bindTouch = function () {
		var self = this;
		var startX = 0;
		var startY = 0;
		var axis = null;       // 'x' (swipe de slide) | 'y' (rolar a página) | null
		var TOUCH_THRESHOLD = 8; // px de movimento antes de travar a direção do gesto

		this.root.addEventListener( 'touchstart', function ( e ) {
			var t = e.touches[ 0 ];
			startX = t.clientX;
			startY = t.clientY;
			axis = null;
		}, { passive: true } );

		this.root.addEventListener( 'touchmove', function ( e ) {
			var t = e.touches[ 0 ];
			var dx = t.clientX - startX;
			var dy = t.clientY - startY;
			// Trava a direção UMA única vez por gesto, só depois de passar o limiar.
			// Assim um começo levemente diagonal não sequestra (e cancela) a rolagem
			// vertical da página no mobile — a tela volta a rolar normalmente.
			if ( axis === null ) {
				if ( Math.abs( dx ) < TOUCH_THRESHOLD && Math.abs( dy ) < TOUCH_THRESHOLD ) {
					return;
				}
				axis = Math.abs( dx ) > Math.abs( dy ) ? 'x' : 'y';
			}
			// Só bloqueia o gesto quando é claramente horizontal (troca de slide).
			// Gestos verticais nunca são bloqueados → o scroll da tela é preservado.
			if ( axis === 'x' && e.cancelable ) {
				e.preventDefault();
			}
		}, { passive: false } );

		this.root.addEventListener( 'touchend', function ( e ) {
			if ( axis !== 'x' ) {
				return;
			}
			var t = e.changedTouches[ 0 ];
			var dx = t.clientX - startX;
			if ( Math.abs( dx ) >= 50 ) {
				self.goToSlide( self.currentIndex + ( dx < 0 ? 1 : -1 ), { source: 'touch' } );
			}
		}, { passive: true } );
	};

	/* =================================================================
	 * Comum: hover
	 * ============================================================== */

	Carousel.prototype.bindCommon = function () {
		var self = this;
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
			if ( ! self.inView ) {
				return;
			}
			if ( self.hovering && self.cfg.pauseHover ) {
				return;
			}
			if ( self.pinMode ) {
				// No pin, o autoplay caminha pelos slides rolando a página.
				// Recalcula a posição REAL antes de decidir: o tick do
				// setInterval pode disparar com progress/currentIndex defasados
				// (updatePin é throttled por rAF). Agir sobre valores velhos faz
				// o window.scrollTo puxar o usuário de volta pra dentro da seção
				// depois que ele já rolou pra fora. updatePin lê a posição via
				// getBoundingClientRect (sempre atual).
				self.updatePin();
				// Ao chegar no fim, para e deixa a página seguir naturalmente.
				if ( self.progress >= 0.999 || self.currentIndex >= self.slides.length - 1 ) {
					self.stopAutoplay();
					return;
				}
				self.scrollToIndex( self.currentIndex + 1, { forwardOnly: true } );
			} else {
				self.goToSlide( self.currentIndex + 1, { source: 'autoplay' } );
			}
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
	 * IntersectionObserver — visibilidade (autoplay)
	 * ============================================================== */

	Carousel.prototype.observe = function () {
		var self = this;
		if ( ! ( 'IntersectionObserver' in window ) ) {
			this.inView = true;
			return;
		}

		this.visObserver = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				self.inView = entry.isIntersecting;
				if ( ! entry.isIntersecting ) {
					if ( self.autoplayTimer ) {
						self._viewportPaused = true;
						self.stopAutoplay();
					}
				} else if ( self._viewportPaused ) {
					self._viewportPaused = false;
					self.restartAutoplay();
				}
			} );
		}, { threshold: 0.15 } );
		this.visObserver.observe( this.root );
	};

	/* =================================================================
	 * Destroy
	 * ============================================================== */

	Carousel.prototype.destroy = function () {
		this.stopAutoplay();
		if ( this.resumeTimer ) {
			window.clearTimeout( this.resumeTimer );
		}
		if ( this.lockTimer ) {
			window.clearTimeout( this.lockTimer );
		}
		if ( this._raf ) {
			window.cancelAnimationFrame( this._raf );
			this._raf = 0;
		}
		if ( this._onScroll ) {
			window.removeEventListener( 'scroll', this._onScroll );
		}
		if ( this._onResize ) {
			window.removeEventListener( 'resize', this._onResize );
		}
		if ( this._onUserScroll ) {
			window.removeEventListener( 'wheel', this._onUserScroll );
			window.removeEventListener( 'touchmove', this._onUserScroll );
		}
		if ( this.visObserver ) {
			this.visObserver.disconnect();
		}
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
				if ( root.__ceeCarousel ) {
					root.__ceeCarousel.destroy();
				}
				// eslint-disable-next-line no-new
				new Carousel( root );
			}
		);
	} );
} )();
