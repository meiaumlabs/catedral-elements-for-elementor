=== Catedral Elements for Elementor ===
Contributors: 61labs
Tags: elementor, slider, carousel, real estate, fullscreen
Requires at least: 5.9
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 1.12.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Addon de widgets premium para o Elementor pela 61 Labs. Primeiro widget: Slide Carrossel com Scroll Horizontal para apresentações imobiliárias de alto padrão.

== Description ==

Catedral Elements adiciona ao Elementor um widget de **Slide Carrossel — Scroll Horizontal** com estética de imobiliário de luxo: slides fullscreen com imagem de fundo em `object-fit: cover`, overlay gradiente sutil, menu de navegação vertical em caixa alta e título editorial sobreposto.

Recursos:

* Repetidor de slides com nome, imagem, foco da imagem, título, subtítulo, conteúdo rich-text e botão CTA.
* Scroll horizontal com pin da seção, snap slide a slide e sensibilidade configurável.
* Autoplay com pausa ao hover e ao interagir, loop opcional e easing premium.
* Menu de navegação sincronizado com posição (horizontal e vertical) responsiva por dispositivo e offsets configuráveis.
* Âncora do bloco de conteúdo (horizontal e vertical) responsiva por dispositivo.
* Modo boxed/full para o bloco de conteúdo.
* Experiência mobile dedicada: swipe em vez de scroll-jack por padrão (opcional) e tipografia fluida.
* Swipe horizontal em touch e navegação por teclado.
* Respeita `prefers-reduced-motion`.
* Compatível com Elementor 3.x (não exige Elementor Pro).

== Installation ==

1. Envie a pasta `catedral-elements-for-elementor` para `/wp-content/plugins/`.
2. Ative o plugin no menu "Plugins".
3. Certifique-se de que o Elementor está ativo.
4. No editor do Elementor, procure a categoria "Catedral Elements" e arraste o widget "Slide Carrossel".

== Changelog ==

= 1.12.0 =
* Novo: controles de imagem no padrão do "Plano de fundo" do Elementor, na seção "Imagem do Slide" (Estilo), todos responsivos por dispositivo:
  * "Proporção da tela" — trava a altura do slide numa proporção (1:1, 3:2, 4:3, 16:9, 21:9, 9:16) baseada na largura da tela; "Padrão" mantém a altura do widget.
  * "Tamanho de exibição" (cobertura/conter/esticar/reduzir) e "Posição" (foco) agora disponíveis também como padrão global do widget.
  * "Resolução da imagem" — escolhe o tamanho de arquivo servido (Completo ou tamanhos registrados) para aliviar o carregamento.
* Melhoria: o "Foco da imagem" de cada slide ganhou a opção "Usar posição global (Estilo)" (novo padrão) — os slides herdam a posição global e só sobrescrevem quando você escolhe um foco específico.

= 1.11.0 =
* Novo: seletor "Estilo do widget" (em Conteúdo) com o preset **Editorial · Full Screen**. Ao selecionar, o widget adota o layout imobiliário de tela cheia: menu vertical no topo-esquerda com itens em caixa alta e espaçados (ativo em destaque, demais esmaecidos), título ancorado na base e um scrim superior sutil para legibilidade do menu sobre fotos claras. Todos os controles (cor, tipografia, offsets, tamanho, etc.) continuam ajustando o resultado por cima do preset; voltar para "Padrão" restaura o comportamento do controle "Posição do menu".

= 1.10.0 =
* Novo: controle "Efeito de sobreposição" (em Autoplay & Transição) para ligar/desligar a transição empilhada. Ligado (padrão): o próximo slide entra acima do anterior com fade in up + deslize horizontal. Desligado: volta ao deslize horizontal clássico, com os slides passando lado a lado.

= 1.9.0 =
* Novo: transição dos slides por sobreposição — em vez de deslizar o conjunto, os slides ficam empilhados e o próximo entra ACIMA do anterior com fade in up + deslize horizontal, resultando numa troca mais suave e sofisticada. No modo pin (scroll travado) a sobreposição é dirigida pelo scroll de forma contínua; no modo slider é uma transição CSS. Respeita `prefers-reduced-motion` (troca instantânea).

= 1.8.0 =
* Novo: controle "Ancoragem no topo ao travar (offset)" no Scroll Horizontal — define a que distância do topo da tela a seção encaixa ao travar (pin), responsivo por dispositivo. Ideal para deixar espaço a um cabeçalho fixo. A altura útil do slide e o motor de progresso do scroll se ajustam automaticamente ao offset.
* Melhoria (mobile): posicionamento do menu de navegação mais fluido — o deslocamento H/V passa a ter valores próprios para tablet e mobile (aplicados pelo Elementor com a especificidade correta, sem serem sobrepostos pelos valores de desktop), espaçamento e tipografia dos itens escalam com a largura da tela e o menu em linha rola na horizontal em vez de quebrar/estourar a largura.

= 1.7.0 =
* Correção (mobile): o scroll da tela voltou a funcionar sobre o slide. O swipe agora trava a direção do gesto uma única vez (com limiar), então um toque que começa levemente na diagonal não sequestra mais a rolagem vertical da página — gestos verticais nunca são bloqueados.
* Novo: posição do menu de navegação unificada e responsiva num único controle, incluindo o modo "Barra abaixo do slide" (faixa de largura total no rodapé) além das seis ancoragens sobre o slide (topo/meio/base × esquerda/direita). Ex.: barra no desktop e sobre o slide no mobile. Cor de fundo opcional para a barra.
* Novo: ajuste do tamanho da imagem em exibição — "Ajuste da imagem" (cover/conter/esticar/reduzir), "Tamanho / zoom da imagem" (responsivo) e cor de fundo atrás da imagem para quando ela não cobre todo o slide.
* Novo: gradiente de contraste do texto (scrim) sobre a imagem e atrás do conteúdo, com cor, altura e origem (de baixo/de cima) configuráveis, para garantir a legibilidade do título sobre qualquer foto.

= 1.6.0 =
* Novo: posicionamento completo do título do slide — alinhamento horizontal (esquerda/centro/direita) e deslocamento fino em X e Y (px, %, vw/vh), tudo responsivo por dispositivo, para ajustar a posição do título de forma independente em desktop, tablet e mobile.
* Novo: borda dos itens do menu de navegação no padrão do Elementor — controle de grupo "Borda" (tipo, largura e cor) e "Sombra da caixa", com abas Normal/Hover/Ativo. O arredondamento passa a ser "Raio da borda" com os quatro cantos controláveis (responsivo).

= 1.5.0 =
* Novo: controle de alinhamento do título do slide, responsivo por dispositivo — alinhe o título de forma independente do restante do conteúdo (ex.: centralizado no mobile). Somado à tipografia e ao espaçamento (que já eram responsivos), o título passa a ter ajuste e posicionamento completos por dispositivo.

= 1.4.0 =
* Novo: melhor experiência no mobile. Controle "Manter pin/trava no mobile" (em Scroll Horizontal) — desligado por padrão, telas pequenas passam a ser um carrossel navegado por swipe em vez de sequestrar a rolagem vertical; ligado, mantém o pin também no mobile.
* Novo: posição do menu de navegação (horizontal e vertical) agora é responsiva — configure ancoragem diferente para desktop, tablet e mobile.
* Novo: âncora do bloco de conteúdo (horizontal e vertical) agora é responsiva por dispositivo.
* Melhoria: estilo mobile refinado — tipografia fluida do título/subtítulo/texto, mais respiro no bloco de conteúdo, alvos de toque maiores no menu e escala intermediária para tablet.

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
