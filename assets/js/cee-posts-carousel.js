/**
 * Catedral Elements — Posts em Carrossel.
 *
 * Vanilla JS, zero dependências (sem jQuery, sem bibliotecas de slider).
 * Toda a configuração de comportamento vem dos data-* do markup PHP; a
 * quantidade de cards visíveis vem da CSS variable --cee-pc-per-view, que o
 * Elementor troca por dispositivo — por isso é lida do computed style e
 * recalculada a cada resize.
 *
 * Suporta múltiplas instâncias e reinicialização dentro do editor Elementor.
 */
( function () {
	'use strict';

	var RESIZE_DEBOUNCE = 150;
	// Distância mínima (px) para um arrasto valer como swipe.
	var SWIPE_THRESHOLD = 45;

	function clamp( value, min, max ) {
		return Math.max( min, Math.min( max, value ) );
	}

	/**
	 * Controlador de uma instância do carrossel.
	 *
	 * @param {HTMLElement} root Elemento raiz .cee-posts-carousel.
	 */
	function PostsCarousel( root ) {
		this.root = root;
		this.viewport = root.querySelector( '.cee-pc__viewport' );
		this.track = root.querySelector( '.cee-pc__track' );
		this.cards = Array.prototype.slice.call( root.querySelectorAll( '.cee-pc__card' ) );
		this.prevBtn = root.querySelector( '.cee-pc__arrow--prev' );
		this.nextBtn = root.querySelector( '.cee-pc__arrow--next' );
		this.dots = Array.prototype.slice.call( root.querySelectorAll( '.cee-pc__dot' ) );

		if ( ! this.track || this.cards.length === 0 ) {
			return;
		}

		// --- Configuração lida EXCLUSIVAMENTE dos data-* ---
		var d = root.dataset;
		this.cfg = {
			autoplay: d.autoplay === 'true',
			delay: parseInt( d.autoplayDelay, 10 ) || 4000,
			loop: d.loop === 'true',
			speed: parseInt( d.speed, 10 ) || 500,
			pauseHover: d.pauseHover === 'true'
		};

		this.index = 0;
		this.perView = 1;
		this.maxIndex = 0;
		this.autoplayTimer = null;
		this.resizeTimer = null;
		this.hovering = false;
		this.dragging = false;
		this.pointerId = null;
		this.startX = 0;
		this.startY = 0;
		this.deltaX = 0;
		this.baseOffset = 0;

		this.bind();
		this.measure();
		this.goTo( 0, true );
		this.startAutoplay();

		root.__ceePostsCarousel = this;
	}

	/* =================================================================
	 * Medição
	 * ============================================================== */

	/**
	 * Lê --cee-pc-per-view do computed style (o Elementor troca esse valor
	 * por dispositivo) e recalcula o índice máximo navegável.
	 */
	PostsCarousel.prototype.measure = function () {
		var raw = window.getComputedStyle( this.root ).getPropertyValue( '--cee-pc-per-view' );
		var parsed = parseFloat( ( raw || '' ).trim() );

		if ( ! parsed || parsed < 1 ) {
			parsed = 1;
		}

		this.perView = clamp( Math.round( parsed ), 1, this.cards.length );
		this.maxIndex = Math.max( 0, this.cards.length - this.perView );

		// Passo horizontal = largura de um card + o espaço entre cards.
		var cardWidth = this.cards[ 0 ] ? this.cards[ 0 ].getBoundingClientRect().width : 0;
		var gap = parseFloat( window.getComputedStyle( this.track ).columnGap );
		if ( isNaN( gap ) ) {
			gap = parseFloat( window.getComputedStyle( this.track ).gap ) || 0;
		}

		this.step = cardWidth + gap;
		this.index = clamp( this.index, 0, this.maxIndex );

		this.updateArrows();
		this.updateDots();
	};

	/* =================================================================
	 * Navegação
	 * ============================================================== */

	/**
	 * Desloca o track até o índice pedido.
	 *
	 * @param {number}  index   Índice do primeiro card visível.
	 * @param {boolean} instant Quando verdadeiro, pula a animação.
	 */
	PostsCarousel.prototype.goTo = function ( index, instant ) {
		this.index = clamp( index, 0, this.maxIndex );

		var offset = -( this.index * this.step );

		if ( instant ) {
			this.track.style.transition = 'none';
		}

		this.track.style.transform = 'translate3d(' + offset + 'px, 0, 0)';

		if ( instant ) {
			// Força o reflow para que a próxima transição volte a valer.
			void this.track.offsetWidth;
			this.track.style.transition = '';
		}

		this.updateArrows();
		this.updateDots();
	};

	PostsCarousel.prototype.next = function () {
		if ( this.index >= this.maxIndex ) {
			if ( this.cfg.loop ) {
				this.goTo( 0 );
			}
			return;
		}
		this.goTo( this.index + 1 );
	};

	PostsCarousel.prototype.prev = function () {
		if ( this.index <= 0 ) {
			if ( this.cfg.loop ) {
				this.goTo( this.maxIndex );
			}
			return;
		}
		this.goTo( this.index - 1 );
	};

	/**
	 * Sem loop, as setas ficam desabilitadas nos extremos. Quando todos os
	 * cards já cabem na tela, não há o que navegar: as setas somem.
	 */
	PostsCarousel.prototype.updateArrows = function () {
		var noNavigation = this.maxIndex === 0;

		[ this.prevBtn, this.nextBtn ].forEach( function ( btn ) {
			if ( btn ) {
				btn.hidden = noNavigation;
			}
		} );

		if ( noNavigation || this.cfg.loop ) {
			this.setDisabled( this.prevBtn, false );
			this.setDisabled( this.nextBtn, false );
			return;
		}

		this.setDisabled( this.prevBtn, this.index <= 0 );
		this.setDisabled( this.nextBtn, this.index >= this.maxIndex );
	};

	PostsCarousel.prototype.setDisabled = function ( btn, disabled ) {
		if ( ! btn ) {
			return;
		}
		btn.disabled = !! disabled;
		btn.setAttribute( 'aria-disabled', disabled ? 'true' : 'false' );
	};

	/**
	 * Mostra apenas as bolinhas correspondentes a posições navegáveis.
	 */
	PostsCarousel.prototype.updateDots = function () {
		if ( this.dots.length === 0 ) {
			return;
		}

		var current = this.index;
		var max = this.maxIndex;

		this.dots.forEach( function ( dot, i ) {
			var usable = i <= max && max > 0;
			dot.hidden = ! usable;
			if ( i === current ) {
				dot.classList.add( 'cee-pc__dot--active' );
				dot.setAttribute( 'aria-current', 'true' );
			} else {
				dot.classList.remove( 'cee-pc__dot--active' );
				dot.removeAttribute( 'aria-current' );
			}
		} );
	};

	/* =================================================================
	 * Autoplay
	 * ============================================================== */

	PostsCarousel.prototype.startAutoplay = function () {
		if ( ! this.cfg.autoplay || this.maxIndex === 0 ) {
			return;
		}
		this.stopAutoplay();

		var self = this;
		this.autoplayTimer = window.setInterval( function () {
			if ( self.cfg.pauseHover && self.hovering ) {
				return;
			}
			if ( document.hidden || self.dragging ) {
				return;
			}
			// Sem loop, o autoplay reinicia ao chegar no fim.
			if ( ! self.cfg.loop && self.index >= self.maxIndex ) {
				self.goTo( 0 );
				return;
			}
			self.next();
		}, this.cfg.delay );
	};

	PostsCarousel.prototype.stopAutoplay = function () {
		if ( this.autoplayTimer ) {
			window.clearInterval( this.autoplayTimer );
			this.autoplayTimer = null;
		}
	};

	/* =================================================================
	 * Eventos
	 * ============================================================== */

	PostsCarousel.prototype.bind = function () {
		var self = this;

		this._onPrev = function ( e ) {
			e.preventDefault();
			self.prev();
		};
		this._onNext = function ( e ) {
			e.preventDefault();
			self.next();
		};

		if ( this.prevBtn ) {
			this.prevBtn.addEventListener( 'click', this._onPrev );
		}
		if ( this.nextBtn ) {
			this.nextBtn.addEventListener( 'click', this._onNext );
		}

		this._onDot = function ( e ) {
			e.preventDefault();
			var target = e.currentTarget;
			var i = parseInt( target.getAttribute( 'data-index' ), 10 );
			if ( ! isNaN( i ) ) {
				self.goTo( i );
			}
		};
		this.dots.forEach( function ( dot ) {
			dot.addEventListener( 'click', self._onDot );
		} );

		this._onEnter = function () {
			self.hovering = true;
		};
		this._onLeave = function () {
			self.hovering = false;
		};
		this.root.addEventListener( 'mouseenter', this._onEnter );
		this.root.addEventListener( 'mouseleave', this._onLeave );

		this._onResize = function () {
			window.clearTimeout( self.resizeTimer );
			self.resizeTimer = window.setTimeout( function () {
				self.measure();
				self.goTo( self.index, true );
			}, RESIZE_DEBOUNCE );
		};
		window.addEventListener( 'resize', this._onResize );

		this._onVisibility = function () {
			// Aba oculta: nada de avançar em segundo plano.
			if ( document.hidden ) {
				self.stopAutoplay();
			} else {
				self.startAutoplay();
			}
		};
		document.addEventListener( 'visibilitychange', this._onVisibility );

		this.bindSwipe();
	};

	/**
	 * Swipe com pointer events (cobre mouse, toque e caneta).
	 */
	PostsCarousel.prototype.bindSwipe = function () {
		if ( ! this.viewport || ! window.PointerEvent ) {
			return;
		}

		var self = this;

		this._onPointerDown = function ( e ) {
			if ( self.maxIndex === 0 || e.button > 0 ) {
				return;
			}
			self.dragging = true;
			self.pointerId = e.pointerId;
			self.startX = e.clientX;
			self.startY = e.clientY;
			self.deltaX = 0;
			self.baseOffset = -( self.index * self.step );
			self.track.classList.add( 'is-dragging' );
		};

		this._onPointerMove = function ( e ) {
			if ( ! self.dragging || e.pointerId !== self.pointerId ) {
				return;
			}

			var dx = e.clientX - self.startX;
			var dy = e.clientY - self.startY;

			// Gesto claramente vertical: devolve o controle à rolagem da página.
			if ( Math.abs( dy ) > Math.abs( dx ) && Math.abs( dy ) > 12 ) {
				self.cancelDrag();
				return;
			}

			self.deltaX = dx;
			self.track.style.transform = 'translate3d(' + ( self.baseOffset + dx ) + 'px, 0, 0)';
		};

		this._onPointerUp = function ( e ) {
			if ( ! self.dragging || ( self.pointerId !== null && e.pointerId !== self.pointerId ) ) {
				return;
			}

			var dx = self.deltaX;
			self.finishDrag();

			if ( dx <= -SWIPE_THRESHOLD ) {
				self.next();
			} else if ( dx >= SWIPE_THRESHOLD ) {
				self.prev();
			} else {
				self.goTo( self.index );
			}
		};

		this.viewport.addEventListener( 'pointerdown', this._onPointerDown );
		this.viewport.addEventListener( 'pointermove', this._onPointerMove );
		this.viewport.addEventListener( 'pointerup', this._onPointerUp );
		this.viewport.addEventListener( 'pointercancel', this._onPointerUp );
		this.viewport.addEventListener( 'pointerleave', this._onPointerUp );

		// Um arrasto de verdade não deve virar clique no card.
		this._onClickCapture = function ( e ) {
			if ( Math.abs( self.deltaX ) > SWIPE_THRESHOLD / 2 ) {
				e.preventDefault();
				e.stopPropagation();
				self.deltaX = 0;
			}
		};
		this.viewport.addEventListener( 'click', this._onClickCapture, true );
	};

	PostsCarousel.prototype.finishDrag = function () {
		this.dragging = false;
		this.pointerId = null;
		this.track.classList.remove( 'is-dragging' );
	};

	PostsCarousel.prototype.cancelDrag = function () {
		this.deltaX = 0;
		this.finishDrag();
		this.goTo( this.index );
	};

	/* =================================================================
	 * Destruição (reinicialização no editor)
	 * ============================================================== */

	PostsCarousel.prototype.destroy = function () {
		this.stopAutoplay();
		window.clearTimeout( this.resizeTimer );

		if ( this.prevBtn && this._onPrev ) {
			this.prevBtn.removeEventListener( 'click', this._onPrev );
		}
		if ( this.nextBtn && this._onNext ) {
			this.nextBtn.removeEventListener( 'click', this._onNext );
		}

		var self = this;
		if ( this._onDot ) {
			this.dots.forEach( function ( dot ) {
				dot.removeEventListener( 'click', self._onDot );
			} );
		}

		if ( this._onEnter ) {
			this.root.removeEventListener( 'mouseenter', this._onEnter );
		}
		if ( this._onLeave ) {
			this.root.removeEventListener( 'mouseleave', this._onLeave );
		}
		if ( this._onResize ) {
			window.removeEventListener( 'resize', this._onResize );
		}
		if ( this._onVisibility ) {
			document.removeEventListener( 'visibilitychange', this._onVisibility );
		}

		if ( this.viewport && this._onPointerDown ) {
			this.viewport.removeEventListener( 'pointerdown', this._onPointerDown );
			this.viewport.removeEventListener( 'pointermove', this._onPointerMove );
			this.viewport.removeEventListener( 'pointerup', this._onPointerUp );
			this.viewport.removeEventListener( 'pointercancel', this._onPointerUp );
			this.viewport.removeEventListener( 'pointerleave', this._onPointerUp );
			this.viewport.removeEventListener( 'click', this._onClickCapture, true );
		}

		this.root.__ceePostsCarousel = null;
	};

	/* =================================================================
	 * Bootstrap
	 * ============================================================== */

	function initAll( context ) {
		var scope = context || document;
		var nodes = scope.querySelectorAll( '.cee-posts-carousel' );

		Array.prototype.forEach.call( nodes, function ( root ) {
			if ( root.__ceePostsCarousel || root.classList.contains( 'cee-posts-carousel--empty' ) ) {
				return;
			}
			// eslint-disable-next-line no-new
			new PostsCarousel( root );
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
			'frontend/element_ready/cee_posts_carousel.default',
			function ( $scope ) {
				var el = $scope && $scope[ 0 ] ? $scope[ 0 ] : null;
				if ( ! el ) {
					return;
				}

				var root = el.querySelector( '.cee-posts-carousel' );
				if ( ! root ) {
					return;
				}

				if ( root.__ceePostsCarousel ) {
					root.__ceePostsCarousel.destroy();
				}
				// eslint-disable-next-line no-new
				new PostsCarousel( root );
			}
		);
	} );
} )();
