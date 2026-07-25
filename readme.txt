=== Catedral Elements for Elementor ===
Contributors: 61labs
Tags: elementor, slider, carousel, real estate, fullscreen
Requires at least: 5.9
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.3.0
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

= 1.3.0 =
* Correção: no modo pin, ao passar da seção o autoplay não puxa mais o usuário de volta. O tick do autoplay recalcula a posição real (via `getBoundingClientRect`) antes de rolar e, por segurança, nunca rola para trás da posição atual — respeitando onde o usuário está.
* Novo: controle de alinhamento do texto dos itens do menu (esquerda, centro, direita), responsivo.
* Novo: controles de borda dos itens do menu — espessura e cor por estado (inativo, hover e ativo), com transição suave.

= 1.2.1 =
* Correção: o menu de navegação não herda mais cores do tema/página. As cores (item, hover, ativo) e o fundo passam a vir exclusivamente dos controles do widget, com blindagem de especificidade que vence regras de tema aplicadas a `button`. Reset do "chrome" nativo do botão (appearance, sombra, borda).

= 1.2.0 =
* Reescrita do scroll horizontal para o padrão **pin sticky**: ao chegar na seção, ela encaixa e trava na tela; a rolagem vertical da página passa a avançar os slides na horizontal e, só depois de percorrer todos, a página segue para a próxima seção. Fim do travamento do scroll global (`overflow:hidden`) — agora é scroll nativo, sem sequestrar a roda do mouse, com barra de rolagem normal e reversão suave ao subir.
* Novo controle "Distância de rolagem por slide" (multiplicador da altura) para ajustar quanto scroll cada slide consome — mais tempo de leitura.
* Autoplay no modo pin caminha pelos slides rolando a página e para ao fim, liberando a continuação vertical.
* No editor do Elementor o widget usa o modo slider para não inflar o canvas; o pin vale no preview e no site publicado.
* Removido o controle "Continuar rolagem vertical ao fim" (agora é comportamento nativo do pin).

= 1.1.0 =
* Novo: ao terminar os slides no scroll horizontal, a seção libera o pin e continua a rolagem vertical da página (controle "Continuar rolagem vertical ao fim", em Scroll Horizontal). Ao subir de volta, o widget recaptura os slides. Passa a funcionar mesmo com o loop do autoplay ligado.
* Novo: controle de orientação do texto do menu de navegação — empilhado (horizontal), em linha (horizontal) ou em linha com texto vertical girado.
* Novo: controles de fundo dos itens do menu (inativo, hover e ativo), além de espaçamento interno e arredondamento.

= 1.0.1 =
* Correção: `get_title()` do widget deixa de injetar HTML/estilo inline no painel do Elementor, que desconfigurava os campos nativos (deixava-os maiores). O título volta a ser texto puro conforme o contrato do Elementor.

= 1.0.0 =
* Lançamento inicial com o widget Slide Carrossel — Scroll Horizontal.
