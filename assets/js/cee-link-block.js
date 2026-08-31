/**
 * Catedral Elements — Bloco linkado.
 *
 * Torna clicável qualquer elemento marcado com a classe .cee-linked-block e o
 * atributo data-cee-link, sem envolver o conteúdo num <a> (o que quebraria links
 * aninhados). Clicar em links, botões, campos ou rótulos internos NÃO dispara a
 * navegação do bloco; clicar em qualquer outra área, sim. Arrastar (swipe) também
 * é ignorado, para não atrapalhar carrosséis.
 *
 * Aprimoramento progressivo: sem JS, os links internos continuam funcionando.
 */
(function () {
	'use strict';

	// Elementos internos cujo clique NÃO deve acionar o link do bloco.
	var INTERACTIVE = 'a, button, input, select, textarea, label, [role="button"], [role="link"], [contenteditable="true"], .cee-linked-block';

	var DRAG_THRESHOLD = 8; // px — acima disso é arrasto, não clique.

	function isInteractive(target, root) {
		var el = target;
		while ( el && el !== root ) {
			if ( el.nodeType === 1 && el.matches && el.matches( INTERACTIVE ) ) {
				return true;
			}
			el = el.parentNode;
		}
		return false;
	}

	function navigate(root) {
		var url = root.getAttribute( 'data-cee-link' );
		if ( ! url ) {
			return;
		}
		var target = root.getAttribute( 'data-cee-link-target' );
		if ( '_blank' === target ) {
			var rel = root.getAttribute( 'data-cee-link-rel' ) || 'noopener';
			var win = window.open( url, '_blank' );
			if ( win && rel.indexOf( 'noopener' ) !== -1 ) {
				win.opener = null;
			}
		} else {
			window.location.href = url;
		}
	}

	function bind(root) {
		if ( root.__ceeLinkedBound ) {
			return;
		}
		root.__ceeLinkedBound = true;

		var startX = 0;
		var startY = 0;
		var moved = false;

		root.addEventListener( 'pointerdown', function ( e ) {
			startX = e.clientX;
			startY = e.clientY;
			moved = false;
		}, true );

		root.addEventListener( 'pointermove', function ( e ) {
			if ( Math.abs( e.clientX - startX ) > DRAG_THRESHOLD || Math.abs( e.clientY - startY ) > DRAG_THRESHOLD ) {
				moved = true;
			}
		}, true );

		root.addEventListener( 'click', function ( e ) {
			if ( moved ) {
				return;
			}
			if ( isInteractive( e.target, root ) ) {
				return;
			}
			navigate( root );
		} );
	}

	function initAll(scope) {
		var context = scope || document;
		var nodes = context.querySelectorAll( '.cee-linked-block[data-cee-link]' );
		Array.prototype.forEach.call( nodes, bind );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () { initAll(); } );
	} else {
		initAll();
	}

	// Reinicializa no editor/preview do Elementor.
	if ( window.jQuery ) {
		window.jQuery( window ).on( 'elementor/frontend/init', function () {
			if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
				window.elementorFrontend.hooks.addAction( 'frontend/element_ready/global', function ( $scope ) {
					initAll( $scope && $scope[0] ? $scope[0] : undefined );
				} );
			}
		} );
	}
})();
