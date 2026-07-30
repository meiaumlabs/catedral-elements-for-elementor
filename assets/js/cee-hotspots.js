/**
 * Catedral Elements — Pontos Interativos (Hotspots)
 *
 * Abre/fecha a caixa de conteúdo no clique do ponto. Mantém apenas um ponto
 * aberto por vez, fecha ao clicar fora ou com ESC, e reposiciona a caixa para
 * não estourar os limites da imagem base (com flip automático no modo "auto").
 */
( function () {
	'use strict';

	var GAP_PAD = 8; // respiro mínimo das bordas do palco (px).

	function closeHotspot( h ) {
		h.classList.remove( 'is-open' );
		var t = h.querySelector( '.cee-hotspot__trigger' );
		if ( t ) {
			t.setAttribute( 'aria-expanded', 'false' );
		}
	}

	function positionBox( h, stage ) {
		var box = h.querySelector( '.cee-hotspot__box' );
		if ( ! box ) {
			return;
		}

		// Reseta ajustes anteriores.
		h.style.setProperty( '--cee-hs-shift', '0px' );
		h.style.setProperty( '--cee-hs-arrow-x', '50%' );

		var placement = h.getAttribute( 'data-placement' ) || 'top';

		// Modo automático: escolhe acima/abaixo conforme a posição vertical do ponto.
		if ( placement === 'auto' ) {
			var sr = stage.getBoundingClientRect();
			var hr = h.getBoundingClientRect();
			var dotCenter = hr.top + hr.height / 2 - sr.top;
			placement = dotCenter > sr.height * 0.45 ? 'top' : 'bottom';
		}
		h.setAttribute( 'data-runtime-placement', placement );

		// Clamp horizontal apenas para caixas acima/abaixo do ponto.
		if ( placement === 'top' || placement === 'bottom' ) {
			requestAnimationFrame( function () {
				var srect = stage.getBoundingClientRect();
				var brect = box.getBoundingClientRect();
				var shift = 0;

				if ( brect.left < srect.left + GAP_PAD ) {
					shift = srect.left + GAP_PAD - brect.left;
				} else if ( brect.right > srect.right - GAP_PAD ) {
					shift = srect.right - GAP_PAD - brect.right;
				}

				if ( shift ) {
					h.style.setProperty( '--cee-hs-shift', shift + 'px' );
					// A seta compensa o deslocamento para continuar sobre o ponto.
					h.style.setProperty( '--cee-hs-arrow-x', 'calc(50% - ' + shift + 'px)' );
				}
			} );
		}
	}

	function initRoot( root ) {
		if ( root.__ceeHotspots ) {
			return;
		}
		root.__ceeHotspots = true;

		var stage = root.querySelector( '.cee-hotspots__stage' );
		if ( ! stage ) {
			return;
		}

		var hotspots = Array.prototype.slice.call( root.querySelectorAll( '.cee-hotspot' ) );

		function closeAll( except ) {
			hotspots.forEach( function ( h ) {
				if ( h !== except ) {
					closeHotspot( h );
				}
			} );
		}

		hotspots.forEach( function ( h ) {
			var trigger = h.querySelector( '.cee-hotspot__trigger' );
			if ( ! trigger ) {
				return;
			}

			trigger.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				e.stopPropagation();

				var isOpen = h.classList.contains( 'is-open' );
				closeAll( h );

				if ( isOpen ) {
					closeHotspot( h );
				} else {
					h.classList.add( 'is-open' );
					trigger.setAttribute( 'aria-expanded', 'true' );
					positionBox( h, stage );
				}
			} );
		} );

		// Cliques dentro do widget não fecham as caixas.
		root.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
		} );

		root.__ceeCloseAll = closeAll;
		root.__ceeReposition = function () {
			hotspots.forEach( function ( h ) {
				if ( h.classList.contains( 'is-open' ) ) {
					positionBox( h, stage );
				}
			} );
		};
	}

	function initAll( ctx ) {
		var scope = ctx || document;
		var nodes = scope.querySelectorAll ? scope.querySelectorAll( '.cee-hotspots' ) : [];
		Array.prototype.forEach.call( nodes, initRoot );
	}

	// Fecha todas ao clicar fora / ESC; reposiciona no resize.
	document.addEventListener( 'click', function () {
		document.querySelectorAll( '.cee-hotspots' ).forEach( function ( root ) {
			if ( root.__ceeCloseAll ) {
				root.__ceeCloseAll( null );
			}
		} );
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' || e.key === 'Esc' ) {
			document.querySelectorAll( '.cee-hotspots' ).forEach( function ( root ) {
				if ( root.__ceeCloseAll ) {
					root.__ceeCloseAll( null );
				}
			} );
		}
	} );

	window.addEventListener( 'resize', function () {
		document.querySelectorAll( '.cee-hotspots' ).forEach( function ( root ) {
			if ( root.__ceeReposition ) {
				root.__ceeReposition();
			}
		} );
	} );

	if ( document.readyState !== 'loading' ) {
		initAll();
	} else {
		document.addEventListener( 'DOMContentLoaded', function () {
			initAll();
		} );
	}

	// Integração com o editor/preview do Elementor.
	if ( window.jQuery ) {
		jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && elementorFrontend.hooks ) {
				elementorFrontend.hooks.addAction( 'frontend/element_ready/cee_hotspots.default', function ( $scope ) {
					initAll( $scope[ 0 ] );
				} );
			}
		} );
	}
} )();
