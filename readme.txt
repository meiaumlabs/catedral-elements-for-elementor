=== Catedral Elements for Elementor ===
Contributors: 61labs
Tags: elementor, slider, carousel, real estate, fullscreen
Requires at least: 5.9
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Addon de widgets premium para o Elementor pela 61 Labs. Primeiro widget: Slide Carrossel com Scroll Horizontal para apresentações imobiliárias de alto padrão.

== Description ==

Catedral Elements adiciona ao Elementor um widget de **Slide Carrossel — Scroll Horizontal** com estética de imobiliário de luxo: slides fullscreen com imagem de fundo em `object-fit: cover`, overlay gradiente sutil, menu de navegação vertical em caixa alta e título editorial sobreposto.

Recursos:

* Repetidor de slides com nome, imagem, foco da imagem, título, subtítulo, conteúdo rich-text e botão CTA.
* Scroll horizontal com pin da seção, snap slide a slide e sensibilidade configurável.
* Autoplay com pausa ao hover e ao interagir, loop opcional e easing premium.
* Menu de navegação sincronizado (posição e offsets configuráveis).
* Modo boxed/full para o bloco de conteúdo.
* Swipe horizontal em touch e navegação por teclado.
* Respeita `prefers-reduced-motion`.
* Compatível com Elementor 3.x (não exige Elementor Pro).

== Installation ==

1. Envie a pasta `catedral-elements-for-elementor` para `/wp-content/plugins/`.
2. Ative o plugin no menu "Plugins".
3. Certifique-se de que o Elementor está ativo.
4. No editor do Elementor, procure a categoria "Catedral Elements" e arraste o widget "Slide Carrossel".

== Changelog ==

= 1.1.0 =
* Novo: ao terminar os slides no scroll horizontal, a seção libera o pin e continua a rolagem vertical da página (controle "Continuar rolagem vertical ao fim", em Scroll Horizontal). Ao subir de volta, o widget recaptura os slides. Passa a funcionar mesmo com o loop do autoplay ligado.
* Novo: controle de orientação do texto do menu de navegação — empilhado (horizontal), em linha (horizontal) ou em linha com texto vertical girado.
* Novo: controles de fundo dos itens do menu (inativo, hover e ativo), além de espaçamento interno e arredondamento.

= 1.0.1 =
* Correção: `get_title()` do widget deixa de injetar HTML/estilo inline no painel do Elementor, que desconfigurava os campos nativos (deixava-os maiores). O título volta a ser texto puro conforme o contrato do Elementor.

= 1.0.0 =
* Lançamento inicial com o widget Slide Carrossel — Scroll Horizontal.
