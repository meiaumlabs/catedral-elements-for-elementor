<?php
/**
 * Widget Elementor: Slide Carrossel — Scroll Horizontal.
 *
 * Apresentação imobiliária de alto padrão: slides fullscreen com navegação
 * vertical, scroll horizontal com pin, autoplay e transições premium.
 *
 * @package Catedral_Elements_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Utils;

class CEE_Slide_Carousel_Widget extends Widget_Base {

	public function get_name() {
		return 'cee_slide_carousel';
	}

	public function get_title() {
		return __( 'Slide Carrossel', 'catedral-elements' );
	}

	public function get_icon() {
		return 'eicon-slides';
	}

	public function get_categories() {
		return array( 'catedral-elements' );
	}

	public function get_keywords() {
		return array( 'slide', 'carrossel', 'scroll', 'horizontal', 'imóvel', 'imovel', 'catedral', 'fullscreen' );
	}

	public function get_style_depends() {
		return array( 'cee-slide-carousel' );
	}

	public function get_script_depends() {
		return array( 'cee-slide-carousel' );
	}

	/* =====================================================================
	 * CONTROLES
	 * ===================================================================== */

	protected function register_controls() {
		// CONTENT.
		$this->section_skin();
		$this->section_slides();
		$this->section_autoplay();
		$this->section_scroll();
		$this->section_nav();
		$this->section_container();

		// STYLE.
		$this->style_height();
		$this->style_image();
		$this->style_overlay();
		$this->style_scrim();
		$this->style_nav();
		$this->style_title();
		$this->style_title_position();
		$this->style_subtitle();
		$this->style_content();
		$this->style_button();
		$this->style_content_block();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Estilo (Skin)
	 * ------------------------------------------------------------------- */

	protected function section_skin() {
		$this->start_controls_section( 'sec_skin', array(
			'label' => __( 'Estilo', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'skin', array(
			'label'       => __( 'Estilo do widget', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'padrao',
			'options'     => array(
				'padrao'    => __( 'Padrão', 'catedral-elements' ),
				'editorial' => __( 'Editorial · Full Screen', 'catedral-elements' ),
			),
			'description' => __( 'O estilo "Editorial · Full Screen" adota o layout imobiliário de tela cheia: menu vertical no topo-esquerda com itens em caixa alta e espaçados (ativo em destaque, demais esmaecidos), título ancorado na base e scrim de legibilidade. Os controles de cor, tipografia, offsets e demais ajustes continuam funcionando por cima do preset.', 'catedral-elements' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Slides
	 * ------------------------------------------------------------------- */

	protected function section_slides() {
		$this->start_controls_section( 'sec_slides', array(
			'label' => __( 'Slides', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$repeater = new Repeater();

		$repeater->add_control( 'slide_name', array(
			'label'       => __( 'Nome (rótulo no menu)', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'SALA', 'catedral-elements' ),
			'label_block' => true,
			'dynamic'     => array( 'active' => true ),
		) );

		$repeater->add_control( 'slide_image', array(
			'label'   => __( 'Imagem de fundo', 'catedral-elements' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			'dynamic' => array( 'active' => true ),
		) );

		$repeater->add_control( 'slide_image_position', array(
			'label'   => __( 'Foco da imagem', 'catedral-elements' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'global',
			'options' => array(
				'global'        => __( 'Usar posição global (Estilo)', 'catedral-elements' ),
				'top left'      => __( 'Superior esquerda', 'catedral-elements' ),
				'top center'    => __( 'Superior centro', 'catedral-elements' ),
				'top right'     => __( 'Superior direita', 'catedral-elements' ),
				'center left'   => __( 'Centro esquerda', 'catedral-elements' ),
				'center center' => __( 'Centro', 'catedral-elements' ),
				'center right'  => __( 'Centro direita', 'catedral-elements' ),
				'bottom left'   => __( 'Inferior esquerda', 'catedral-elements' ),
				'bottom center' => __( 'Inferior centro', 'catedral-elements' ),
				'bottom right'  => __( 'Inferior direita', 'catedral-elements' ),
				'custom'        => __( 'Personalizado', 'catedral-elements' ),
			),
		) );

		$repeater->add_control( 'slide_image_position_custom', array(
			'label'       => __( 'object-position personalizado', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => '50% 30%',
			'condition'   => array( 'slide_image_position' => 'custom' ),
		) );

		$repeater->add_control( 'slide_title', array(
			'label'       => __( 'Título', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXTAREA,
			'rows'        => 3,
			'default'     => '',
			'dynamic'     => array( 'active' => true ),
			'description' => __( 'Suporta quebra de linha. Renderizado em caixa alta.', 'catedral-elements' ),
		) );

		$repeater->add_control( 'slide_title_tag', array(
			'label'   => __( 'Tag do título', 'catedral-elements' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'h2',
			'options' => array(
				'h1'  => 'H1',
				'h2'  => 'H2',
				'h3'  => 'H3',
				'h4'  => 'H4',
				'p'   => 'p',
				'div' => 'div',
			),
		) );

		$repeater->add_control( 'slide_subtitle', array(
			'label'   => __( 'Subtítulo', 'catedral-elements' ),
			'type'    => Controls_Manager::TEXT,
			'default' => '',
			'dynamic' => array( 'active' => true ),
		) );

		$repeater->add_control( 'slide_content', array(
			'label'   => __( 'Conteúdo', 'catedral-elements' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => '',
			'dynamic' => array( 'active' => true ),
		) );

		$repeater->add_control( 'slide_btn_text', array(
			'label'       => __( 'Texto do botão (vazio = oculto)', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'dynamic'     => array( 'active' => true ),
		) );

		$repeater->add_control( 'slide_btn_link', array(
			'label'       => __( 'Link do botão', 'catedral-elements' ),
			'type'        => Controls_Manager::URL,
			'default'     => array(
				'url'         => '',
				'is_external' => false,
				'nofollow'    => false,
			),
			'placeholder' => 'https://',
			'dynamic'     => array( 'active' => true ),
		) );

		$this->add_control( 'slides', array(
			'label'       => __( 'Slides', 'catedral-elements' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $repeater->get_controls(),
			'title_field' => '{{{ slide_name }}}',
			'default'     => $this->default_slides(),
		) );

		$this->end_controls_section();
	}

	/**
	 * 4 slides de exemplo (SALA, QUARTO SUÍTE, PISCINA, BICICLETÁRIO).
	 *
	 * @return array
	 */
	protected function default_slides() {
		$ph = Utils::get_placeholder_image_src();

		return array(
			array(
				'slide_name'           => __( 'SALA', 'catedral-elements' ),
				'slide_image'          => array( 'url' => $ph ),
				'slide_image_position' => 'center center',
				'slide_title'          => __( "CADA AMBIENTE DO ÂMBAR CARREGA A MARCA\nDE QUEM JÁ CONQUISTOU ONDE QUER ESTAR", 'catedral-elements' ),
				'slide_title_tag'      => 'h2',
				'slide_subtitle'       => '',
				'slide_content'        => '',
				'slide_btn_text'       => '',
			),
			array(
				'slide_name'           => __( 'QUARTO SUÍTE', 'catedral-elements' ),
				'slide_image'          => array( 'url' => $ph ),
				'slide_image_position' => 'center center',
				'slide_title'          => __( "REPOUSO ABSOLUTO EM\nSUÍTES DE PROPORÇÕES GENEROSAS", 'catedral-elements' ),
				'slide_title_tag'      => 'h2',
				'slide_subtitle'       => '',
				'slide_content'        => '',
				'slide_btn_text'       => '',
			),
			array(
				'slide_name'           => __( 'PISCINA', 'catedral-elements' ),
				'slide_image'          => array( 'url' => $ph ),
				'slide_image_position' => 'center center',
				'slide_title'          => __( "LÂMINA D'ÁGUA SUSPENSA\nSOBRE A LINHA DO HORIZONTE", 'catedral-elements' ),
				'slide_title_tag'      => 'h2',
				'slide_subtitle'       => '',
				'slide_content'        => '',
				'slide_btn_text'       => '',
			),
			array(
				'slide_name'           => __( 'BICICLETÁRIO', 'catedral-elements' ),
				'slide_image'          => array( 'url' => $ph ),
				'slide_image_position' => 'center center',
				'slide_title'          => __( "MOBILIDADE PENSADA\nPARA O SEU DIA A DIA", 'catedral-elements' ),
				'slide_title_tag'      => 'h2',
				'slide_subtitle'       => '',
				'slide_content'        => '',
				'slide_btn_text'       => '',
			),
		);
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Autoplay & Transição
	 * ------------------------------------------------------------------- */

	protected function section_autoplay() {
		$this->start_controls_section( 'sec_autoplay', array(
			'label' => __( 'Autoplay & Transição', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'autoplay_enable', array(
			'label'        => __( 'Autoplay', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'autoplay_interval', array(
			'label'      => __( 'Intervalo (ms)', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'ms' ),
			'range'      => array( 'ms' => array( 'min' => 1000, 'max' => 15000, 'step' => 250 ) ),
			'default'    => array( 'size' => 5000, 'unit' => 'ms' ),
			'condition'  => array( 'autoplay_enable' => 'yes' ),
		) );

		$this->add_control( 'autoplay_pause_on_hover', array(
			'label'        => __( 'Pausar ao passar o mouse', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => array( 'autoplay_enable' => 'yes' ),
		) );

		$this->add_control( 'autoplay_pause_on_interaction', array(
			'label'        => __( 'Pausar ao interagir', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => array( 'autoplay_enable' => 'yes' ),
		) );

		$this->add_control( 'loop', array(
			'label'        => __( 'Loop infinito', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'transition_speed', array(
			'label'      => __( 'Duração da transição (ms)', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'ms' ),
			'range'      => array( 'ms' => array( 'min' => 200, 'max' => 2000, 'step' => 50 ) ),
			'default'    => array( 'size' => 800, 'unit' => 'ms' ),
		) );

		$this->add_control( 'transition_easing', array(
			'label'   => __( 'Easing', 'catedral-elements' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'premium',
			'options' => array(
				'ease'        => __( 'Ease', 'catedral-elements' ),
				'ease-in-out' => __( 'Ease in-out', 'catedral-elements' ),
				'premium'     => __( 'Premium (cubic-bezier)', 'catedral-elements' ),
				'linear'      => __( 'Linear', 'catedral-elements' ),
			),
		) );

		$this->add_control( 'transition_overlap', array(
			'label'        => __( 'Efeito de sobreposição', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => __( 'Ligado (padrão): o próximo slide entra ACIMA do anterior com fade in up + deslize horizontal (transição empilhada e suave). Desligado: deslize horizontal clássico, com os slides passando lado a lado.', 'catedral-elements' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Scroll Horizontal
	 * ------------------------------------------------------------------- */

	protected function section_scroll() {
		$this->start_controls_section( 'sec_scroll', array(
			'label' => __( 'Scroll Horizontal', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'scroll_enable', array(
			'label'        => __( 'Capturar scroll do mouse', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'scroll_pin', array(
			'label'        => __( 'Travar seção (pin) durante o scroll', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => __( 'Ao chegar na seção, ela encaixa e trava na tela; a rolagem vertical passa a avançar os slides na horizontal. Só depois de percorrer todos os slides a página segue para a próxima seção.', 'catedral-elements' ),
			'condition'    => array( 'scroll_enable' => 'yes' ),
		) );

		$this->add_control( 'scroll_snap', array(
			'label'        => __( 'Snap slide a slide', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => array( 'scroll_enable' => 'yes' ),
		) );

		$this->add_control( 'scroll_pin_length', array(
			'label'       => __( 'Distância de rolagem por slide', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'range'       => array( '' => array( 'min' => 0.5, 'max' => 3, 'step' => 0.1 ) ),
			'default'     => array( 'size' => 1, 'unit' => '' ),
			'description' => __( 'Multiplicador da altura do widget por slide. Maior = mais rolagem, os slides passam mais devagar e há mais tempo de leitura.', 'catedral-elements' ),
			'condition'   => array( 'scroll_enable' => 'yes', 'scroll_pin' => 'yes' ),
		) );

		$this->add_responsive_control( 'scroll_pin_offset', array(
			'label'       => __( 'Ancoragem no topo ao travar (offset)', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px', 'vh' ),
			'range'       => array(
				'px' => array( 'min' => 0, 'max' => 300, 'step' => 1 ),
				'vh' => array( 'min' => 0, 'max' => 50, 'step' => 1 ),
			),
			'default'     => array( 'size' => 0, 'unit' => 'px' ),
			'description' => __( 'Distância do topo da tela onde a seção ancora ao travar. Use para deixar espaço a um cabeçalho fixo (sticky), por exemplo. A altura útil do slide encolhe pelo mesmo valor para caber abaixo do topo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-slide-carousel' => '--cee-pin-offset: {{SIZE}}{{UNIT}};' ),
			'condition'   => array( 'scroll_enable' => 'yes', 'scroll_pin' => 'yes' ),
		) );

		$this->add_control( 'scroll_sensitivity', array(
			'label'       => __( 'Sensibilidade (px) — modo slider', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => 50, 'max' => 500, 'step' => 10 ) ),
			'default'     => array( 'size' => 150, 'unit' => 'px' ),
			'description' => __( 'Usada apenas quando o pin está desligado.', 'catedral-elements' ),
			'condition'   => array( 'scroll_enable' => 'yes', 'scroll_pin!' => 'yes' ),
		) );

		$this->add_control( 'scroll_pin_mobile', array(
			'label'        => __( 'Manter pin/trava no mobile', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
			'description'  => __( 'Desligado (padrão): em telas pequenas o widget vira um carrossel navegado por swipe, evitando o "sequestro" da rolagem — melhor experiência de toque. Ligado: mantém a trava e o scroll horizontal também no mobile.', 'catedral-elements' ),
			'condition'    => array( 'scroll_enable' => 'yes', 'scroll_pin' => 'yes' ),
		) );

		$this->add_control( 'scroll_touch', array(
			'label'        => __( 'Swipe horizontal (touch)', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Menu de Navegação
	 * ------------------------------------------------------------------- */

	protected function section_nav() {
		$this->start_controls_section( 'sec_nav', array(
			'label' => __( 'Menu de Navegação', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'nav_enable', array(
			'label'        => __( 'Mostrar menu', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'nav_orientation', array(
			'label'     => __( 'Orientação do texto', 'catedral-elements' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'vertical',
			'options'   => array(
				'vertical'      => __( 'Empilhado — texto horizontal', 'catedral-elements' ),
				'horizontal'    => __( 'Em linha — texto horizontal', 'catedral-elements' ),
				'vertical-text' => __( 'Em linha — texto vertical (girado)', 'catedral-elements' ),
			),
			'description' => __( 'Define como os rótulos do menu são dispostos e a orientação do texto.', 'catedral-elements' ),
			'condition' => array( 'nav_enable' => 'yes' ),
		) );

		$this->add_responsive_control( 'nav_placement', array(
			'label'                => __( 'Posição do menu', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'top-right',
			'options'              => array(
				'top-left'     => __( 'Sobre o slide — topo esquerda', 'catedral-elements' ),
				'top-right'    => __( 'Sobre o slide — topo direita', 'catedral-elements' ),
				'middle-left'  => __( 'Sobre o slide — meio esquerda', 'catedral-elements' ),
				'middle-right' => __( 'Sobre o slide — meio direita', 'catedral-elements' ),
				'bottom-left'  => __( 'Sobre o slide — base esquerda', 'catedral-elements' ),
				'bottom-right' => __( 'Sobre o slide — base direita', 'catedral-elements' ),
				'bottom-bar'   => __( 'Barra abaixo do slide (largura total)', 'catedral-elements' ),
			),
			'description'          => __( 'Escolha onde o menu fica em cada dispositivo. "Barra abaixo do slide" ancora o menu numa faixa de largura total no rodapé do slide; as demais opções sobrepõem o menu sobre a imagem. Ex.: barra no desktop e sobre o slide no mobile.', 'catedral-elements' ),
			'selectors_dictionary' => array(
				'top-left'     => '--cee-nav-top:var(--cee-nav-offset-v);--cee-nav-right:auto;--cee-nav-bottom:auto;--cee-nav-left:var(--cee-nav-offset-h);--cee-nav-translate:none;--cee-nav-align:flex-start;--cee-nav-justify:flex-start;--cee-nav-width:auto;--cee-nav-pad:0;',
				'top-right'    => '--cee-nav-top:var(--cee-nav-offset-v);--cee-nav-right:var(--cee-nav-offset-h);--cee-nav-bottom:auto;--cee-nav-left:auto;--cee-nav-translate:none;--cee-nav-align:flex-end;--cee-nav-justify:flex-start;--cee-nav-width:auto;--cee-nav-pad:0;',
				'middle-left'  => '--cee-nav-top:50%;--cee-nav-right:auto;--cee-nav-bottom:auto;--cee-nav-left:var(--cee-nav-offset-h);--cee-nav-translate:translateY(-50%);--cee-nav-align:flex-start;--cee-nav-justify:flex-start;--cee-nav-width:auto;--cee-nav-pad:0;',
				'middle-right' => '--cee-nav-top:50%;--cee-nav-right:var(--cee-nav-offset-h);--cee-nav-bottom:auto;--cee-nav-left:auto;--cee-nav-translate:translateY(-50%);--cee-nav-align:flex-end;--cee-nav-justify:flex-start;--cee-nav-width:auto;--cee-nav-pad:0;',
				'bottom-left'  => '--cee-nav-top:auto;--cee-nav-right:auto;--cee-nav-bottom:var(--cee-nav-offset-v);--cee-nav-left:var(--cee-nav-offset-h);--cee-nav-translate:none;--cee-nav-align:flex-start;--cee-nav-justify:flex-start;--cee-nav-width:auto;--cee-nav-pad:0;',
				'bottom-right' => '--cee-nav-top:auto;--cee-nav-right:var(--cee-nav-offset-h);--cee-nav-bottom:var(--cee-nav-offset-v);--cee-nav-left:auto;--cee-nav-translate:none;--cee-nav-align:flex-end;--cee-nav-justify:flex-start;--cee-nav-width:auto;--cee-nav-pad:0;',
				'bottom-bar'   => '--cee-nav-top:auto;--cee-nav-right:0;--cee-nav-bottom:0;--cee-nav-left:0;--cee-nav-translate:none;--cee-nav-align:flex-end;--cee-nav-justify:center;--cee-nav-width:100%;--cee-nav-pad:14px 20px;',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-nav' => '{{VALUE}}' ),
			'condition'            => array( 'nav_enable' => 'yes' ),
		) );

		$this->add_control( 'nav_bar_bg', array(
			'label'     => __( 'Fundo da barra (abaixo do slide)', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-bar-bg: {{VALUE}};' ),
			'description' => __( 'Cor de fundo aplicada apenas quando o menu está no modo "Barra abaixo do slide". Deixe vazio para barra transparente.', 'catedral-elements' ),
			'condition' => array( 'nav_enable' => 'yes' ),
		) );

		$this->add_responsive_control( 'nav_offset_v', array(
			'label'          => __( 'Deslocamento vertical', 'catedral-elements' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => array( 'px' ),
			'range'          => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
			'default'        => array( 'size' => 40, 'unit' => 'px' ),
			'tablet_default' => array( 'size' => 28, 'unit' => 'px' ),
			'mobile_default' => array( 'size' => 18, 'unit' => 'px' ),
			'selectors'      => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-offset-v: {{SIZE}}{{UNIT}};' ),
			'condition'      => array( 'nav_enable' => 'yes' ),
		) );

		$this->add_responsive_control( 'nav_offset_h', array(
			'label'          => __( 'Deslocamento horizontal', 'catedral-elements' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => array( 'px' ),
			'range'          => array( 'px' => array( 'min' => 0, 'max' => 200 ) ),
			'default'        => array( 'size' => 40, 'unit' => 'px' ),
			'tablet_default' => array( 'size' => 28, 'unit' => 'px' ),
			'mobile_default' => array( 'size' => 18, 'unit' => 'px' ),
			'selectors'      => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-offset-h: {{SIZE}}{{UNIT}};' ),
			'condition'      => array( 'nav_enable' => 'yes' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Largura do Conteúdo
	 * ------------------------------------------------------------------- */

	protected function section_container() {
		$this->start_controls_section( 'sec_container', array(
			'label' => __( 'Largura do Conteúdo', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'content_width_mode', array(
			'label'     => __( 'Modo de largura', 'catedral-elements' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'boxed',
			'options'   => array(
				'boxed' => array( 'title' => __( 'Boxed', 'catedral-elements' ), 'icon' => 'eicon-container' ),
				'full'  => array( 'title' => __( 'Full', 'catedral-elements' ), 'icon' => 'eicon-full-width' ),
			),
			'toggle'    => false,
		) );

		$this->add_responsive_control( 'content_boxed_max_width', array(
			'label'      => __( 'Largura máxima (boxed)', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 600, 'max' => 1920 ) ),
			'default'    => array( 'size' => 1140, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__inner' => '--cee-content-boxed-max-width: {{SIZE}}{{UNIT}};' ),
			'condition'  => array( 'content_width_mode' => 'boxed' ),
		) );

		$this->add_control( 'container_hint', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => __( 'No modo boxed, o bloco de texto respeita a largura definida aqui (equivalente ao container do Elementor). A imagem de fundo sempre ocupa 100% da viewport.', 'catedral-elements' ),
			'content_classes' => 'elementor-descriptor',
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Altura do Widget
	 * ------------------------------------------------------------------- */

	protected function style_height() {
		$this->start_controls_section( 'style_height', array(
			'label' => __( 'Altura do Widget', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'widget_height', array(
			'label'      => __( 'Altura', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'vh' ),
			'range'      => array(
				'px' => array( 'min' => 300, 'max' => 1200 ),
				'vh' => array( 'min' => 30, 'max' => 100 ),
			),
			'default'    => array( 'size' => 100, 'unit' => 'vh' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide-carousel' => '--cee-widget-height: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Imagem do Slide
	 * ------------------------------------------------------------------- */

	protected function style_image() {
		$this->start_controls_section( 'style_image', array(
			'label' => __( 'Imagem do Slide', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'image_ratio', array(
			'label'                => __( 'Proporção da tela', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'padrao',
			'options'              => array(
				'padrao' => __( 'Padrão (usa a altura do widget)', 'catedral-elements' ),
				'1:1'    => '1:1',
				'3:2'    => '3:2',
				'4:3'    => '4:3',
				'16:9'   => '16:9',
				'21:9'   => '21:9',
				'9:16'   => '9:16 (vertical)',
			),
			'description'          => __( 'Trava a altura do slide numa proporção (baseada na largura da tela), responsiva por dispositivo. "Padrão" usa a altura definida na seção "Altura do Widget".', 'catedral-elements' ),
			'selectors_dictionary' => array(
				'padrao' => 'var(--cee-widget-height)',
				'1:1'    => '100vw',
				'3:2'    => '66.6667vw',
				'4:3'    => '75vw',
				'16:9'   => '56.25vw',
				'21:9'   => '42.8571vw',
				'9:16'   => '177.778vw',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-slide-carousel' => '--cee-ratio-height: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'image_fit', array(
			'label'                => __( 'Tamanho de exibição', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'cover',
			'options'              => array(
				'cover'      => __( 'Cobertura (cover)', 'catedral-elements' ),
				'contain'    => __( 'Conter (mostra imagem inteira)', 'catedral-elements' ),
				'fill'       => __( 'Esticar (fill)', 'catedral-elements' ),
				'scale-down' => __( 'Reduzir se necessário (scale-down)', 'catedral-elements' ),
			),
			'description'          => __( 'Como a imagem preenche o slide. "Conter" mostra a imagem inteira sem cortar (pode sobrar espaço nas bordas — use a cor de fundo abaixo).', 'catedral-elements' ),
			'selectors'            => array( '{{WRAPPER}} .cee-slide__image' => 'object-fit: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'image_position', array(
			'label'       => __( 'Posição', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'center center',
			'options'     => array(
				'top left'      => __( 'Superior esquerda', 'catedral-elements' ),
				'top center'    => __( 'Superior centro', 'catedral-elements' ),
				'top right'     => __( 'Superior direita', 'catedral-elements' ),
				'center left'   => __( 'Centro esquerda', 'catedral-elements' ),
				'center center' => __( 'Centro ao centro', 'catedral-elements' ),
				'center right'  => __( 'Centro direita', 'catedral-elements' ),
				'bottom left'   => __( 'Inferior esquerda', 'catedral-elements' ),
				'bottom center' => __( 'Inferior centro', 'catedral-elements' ),
				'bottom right'  => __( 'Inferior direita', 'catedral-elements' ),
			),
			'description' => __( 'Posição/foco padrão da imagem, por dispositivo. Cada slide pode sobrescrever no controle "Foco da imagem" (deixe em "Usar posição global" para herdar esta).', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-slide__image' => 'object-position: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'image_scale', array(
			'label'       => __( 'Tamanho / zoom da imagem', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( '%' ),
			'range'       => array( '%' => array( 'min' => 50, 'max' => 200, 'step' => 1 ) ),
			'default'     => array( 'size' => 100, 'unit' => '%' ),
			'description' => __( 'Escala a imagem exibida. 100% = tamanho natural do ajuste; acima aproxima (zoom), abaixo afasta. Responsivo por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-slide__media' => '--cee-image-scale: calc({{SIZE}} / 100);' ),
		) );

		$this->add_control( 'image_size', array(
			'label'       => __( 'Resolução da imagem', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'full',
			'options'     => $this->get_image_size_options(),
			'description' => __( 'Tamanho do arquivo servido para cada imagem. "Completo" usa a imagem original; tamanhos menores aliviam o carregamento. Não se aplica a imagens dinâmicas.', 'catedral-elements' ),
		) );

		$this->add_control( 'image_bg', array(
			'label'     => __( 'Cor de fundo atrás da imagem', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#0d0d0d',
			'description' => __( 'Aparece quando a imagem não cobre todo o slide (modo "Conter" ou zoom para fora).', 'catedral-elements' ),
			'selectors' => array( '{{WRAPPER}} .cee-slide__media' => 'background-color: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}

	/**
	 * Opções de tamanho de imagem registradas no WordPress (+ "Completo").
	 *
	 * @return array
	 */
	protected function get_image_size_options() {
		$options = array();
		foreach ( get_intermediate_image_sizes() as $size ) {
			$options[ $size ] = ucwords( str_replace( array( '_', '-' ), ' ', $size ) );
		}
		$options['full'] = __( 'Completo', 'catedral-elements' );
		return $options;
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Gradiente de contraste do texto
	 * ------------------------------------------------------------------- */

	protected function style_scrim() {
		$this->start_controls_section( 'style_scrim', array(
			'label' => __( 'Contraste do Texto', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'scrim_enable', array(
			'label'        => __( 'Gradiente de contraste', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => __( 'Adiciona um degradê sobre a imagem, atrás do texto, para o título/subtítulo terem contraste e legibilidade sobre qualquer foto.', 'catedral-elements' ),
		) );

		$this->add_control( 'scrim_color', array(
			'label'     => __( 'Cor do degradê', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.65)',
			'selectors' => array( '{{WRAPPER}} .cee-slide__scrim' => '--cee-scrim-color: {{VALUE}};' ),
			'condition' => array( 'scrim_enable' => 'yes' ),
		) );

		$this->add_responsive_control( 'scrim_size', array(
			'label'      => __( 'Altura do degradê', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'range'      => array( '%' => array( 'min' => 15, 'max' => 100 ) ),
			'default'    => array( 'size' => 55, 'unit' => '%' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__scrim' => 'height: {{SIZE}}{{UNIT}};' ),
			'condition'  => array( 'scrim_enable' => 'yes' ),
		) );

		$this->add_control( 'scrim_from', array(
			'label'     => __( 'Origem do degradê', 'catedral-elements' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'bottom',
			'options'   => array(
				'bottom' => __( 'De baixo (texto no rodapé)', 'catedral-elements' ),
				'top'    => __( 'De cima (texto no topo)', 'catedral-elements' ),
			),
			'selectors_dictionary' => array(
				'bottom' => 'bottom:0;top:auto;--cee-scrim-dir:to top;',
				'top'    => 'top:0;bottom:auto;--cee-scrim-dir:to bottom;',
			),
			'selectors' => array( '{{WRAPPER}} .cee-slide__scrim' => '{{VALUE}}' ),
			'condition' => array( 'scrim_enable' => 'yes' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Overlay
	 * ------------------------------------------------------------------- */

	protected function style_overlay() {
		$this->start_controls_section( 'style_overlay', array(
			'label' => __( 'Overlay', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'overlay_type', array(
			'label'   => __( 'Tipo', 'catedral-elements' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'gradient',
			'options' => array(
				'none'     => __( 'Nenhum', 'catedral-elements' ),
				'solid'    => __( 'Sólido', 'catedral-elements' ),
				'gradient' => __( 'Gradiente', 'catedral-elements' ),
			),
		) );

		$this->add_control( 'overlay_color', array(
			'label'     => __( 'Cor sólida', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.40)',
			'selectors' => array( '{{WRAPPER}} .cee-slide__overlay' => 'background: {{VALUE}};' ),
			'condition' => array( 'overlay_type' => 'solid' ),
		) );

		$this->add_control( 'overlay_gradient_start', array(
			'label'     => __( 'Cor inicial', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0)',
			'condition' => array( 'overlay_type' => 'gradient' ),
		) );

		$this->add_control( 'overlay_gradient_end', array(
			'label'     => __( 'Cor final', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(0,0,0,0.40)',
			'selectors' => array(
				'{{WRAPPER}} .cee-slide__overlay' => 'background: linear-gradient({{overlay_gradient_direction.VALUE}}, {{overlay_gradient_start.VALUE}} 0%, {{VALUE}} 100%);',
			),
			'condition' => array( 'overlay_type' => 'gradient' ),
		) );

		$this->add_control( 'overlay_gradient_direction', array(
			'label'     => __( 'Direção', 'catedral-elements' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'to bottom',
			'options'   => array(
				'to bottom' => __( 'Para baixo', 'catedral-elements' ),
				'to top'    => __( 'Para cima', 'catedral-elements' ),
				'to left'   => __( 'Para a esquerda', 'catedral-elements' ),
				'to right'  => __( 'Para a direita', 'catedral-elements' ),
				'135deg'    => __( 'Diagonal (135°)', 'catedral-elements' ),
			),
			'condition' => array( 'overlay_type' => 'gradient' ),
		) );

		$this->add_control( 'overlay_opacity', array(
			'label'     => __( 'Opacidade', 'catedral-elements' ),
			'type'      => Controls_Manager::SLIDER,
			'range'     => array( '' => array( 'min' => 0, 'max' => 1, 'step' => 0.01 ) ),
			'default'   => array( 'size' => 1, 'unit' => '' ),
			'selectors' => array( '{{WRAPPER}} .cee-slide__overlay' => 'opacity: {{SIZE}};' ),
			'condition' => array( 'overlay_type!' => 'none' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Menu de Navegação
	 * ------------------------------------------------------------------- */

	protected function style_nav() {
		$this->start_controls_section( 'style_nav', array(
			'label'     => __( 'Menu de Navegação', 'catedral-elements' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'nav_enable' => 'yes' ),
		) );

		$this->add_control( 'nav_active_color', array(
			'label'     => __( 'Cor do item ativo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-active-color: {{VALUE}};' ),
		) );

		$this->add_control( 'nav_inactive_color', array(
			'label'     => __( 'Cor dos itens inativos', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.35)',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-inactive-color: {{VALUE}};' ),
		) );

		$this->add_control( 'nav_hover_color', array(
			'label'     => __( 'Cor no hover', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.70)',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-hover-color: {{VALUE}};' ),
		) );

		$this->add_control( 'nav_item_bg_heading', array(
			'label'     => __( 'Fundo dos itens', 'catedral-elements' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		) );

		$this->add_control( 'nav_item_bg', array(
			'label'     => __( 'Fundo (inativo)', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'transparent',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-item-bg: {{VALUE}};' ),
		) );

		$this->add_control( 'nav_item_bg_hover', array(
			'label'     => __( 'Fundo no hover', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'transparent',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-item-bg-hover: {{VALUE}};' ),
		) );

		$this->add_control( 'nav_item_bg_active', array(
			'label'     => __( 'Fundo do item ativo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'transparent',
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-item-bg-active: {{VALUE}};' ),
		) );

		$this->add_control( 'nav_item_border_heading', array(
			'label'     => __( 'Borda e sombra dos itens', 'catedral-elements' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		) );

		$this->start_controls_tabs( 'nav_item_border_tabs' );

		/* Normal. */
		$this->start_controls_tab( 'nav_item_border_tab_normal', array(
			'label' => __( 'Normal', 'catedral-elements' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'nav_item_border',
			'selector' => '{{WRAPPER}} .cee-nav .cee-nav__item',
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'     => 'nav_item_box_shadow',
			'selector' => '{{WRAPPER}} .cee-nav .cee-nav__item',
		) );

		$this->end_controls_tab();

		/* Hover. */
		$this->start_controls_tab( 'nav_item_border_tab_hover', array(
			'label' => __( 'Hover', 'catedral-elements' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'nav_item_border_hover',
			'selector' => '{{WRAPPER}} .cee-nav .cee-nav__item:hover, {{WRAPPER}} .cee-nav .cee-nav__item:focus-visible',
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'     => 'nav_item_box_shadow_hover',
			'selector' => '{{WRAPPER}} .cee-nav .cee-nav__item:hover, {{WRAPPER}} .cee-nav .cee-nav__item:focus-visible',
		) );

		$this->end_controls_tab();

		/* Ativo. */
		$this->start_controls_tab( 'nav_item_border_tab_active', array(
			'label' => __( 'Ativo', 'catedral-elements' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'nav_item_border_active',
			'selector' => '{{WRAPPER}} .cee-nav .cee-nav__item--active',
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'     => 'nav_item_box_shadow_active',
			'selector' => '{{WRAPPER}} .cee-nav .cee-nav__item--active',
		) );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control( 'nav_item_text_align', array(
			'label'     => __( 'Alinhamento do texto', 'catedral-elements' ),
			'type'      => Controls_Manager::CHOOSE,
			'separator' => 'before',
			'options'   => array(
				'left'   => array( 'title' => __( 'Esquerda', 'catedral-elements' ), 'icon' => 'eicon-text-align-left' ),
				'center' => array( 'title' => __( 'Centro', 'catedral-elements' ), 'icon' => 'eicon-text-align-center' ),
				'right'  => array( 'title' => __( 'Direita', 'catedral-elements' ), 'icon' => 'eicon-text-align-right' ),
			),
			'selectors' => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-item-text-align: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'nav_item_padding', array(
			'label'      => __( 'Espaçamento interno dos itens', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => 2, 'right' => 0, 'bottom' => 2, 'left' => 0, 'unit' => 'px', 'isLinked' => false ),
			'selectors'  => array( '{{WRAPPER}} .cee-nav__item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'nav_item_radius', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array( '{{WRAPPER}} .cee-nav__item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'nav_typo',
			'selector'       => '{{WRAPPER}} .cee-nav__item',
			'fields_options' => array(
				'typography'     => array( 'default' => 'yes' ),
				'font_family'    => array( 'default' => 'Raleway' ),
				'font_weight'    => array( 'default' => '300' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 12 ) ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.3 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->add_responsive_control( 'nav_item_gap', array(
			'label'      => __( 'Espaço entre itens', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 4, 'max' => 60 ) ),
			'default'    => array( 'size' => 14, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-nav__list' => 'gap: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_control( 'nav_state_transition', array(
			'label'      => __( 'Transição ativo/inativo (ms)', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'ms' ),
			'range'      => array( 'ms' => array( 'min' => 50, 'max' => 800, 'step' => 10 ) ),
			'default'    => array( 'size' => 300, 'unit' => 'ms' ),
			'selectors'  => array( '{{WRAPPER}} .cee-nav' => '--cee-nav-state-transition: {{SIZE}}ms;' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Título do Slide
	 * ------------------------------------------------------------------- */

	protected function style_title() {
		$this->start_controls_section( 'style_title', array(
			'label' => __( 'Título do Slide', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'title_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-slide__title' => 'color: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'title_text_align', array(
			'label'       => __( 'Alinhamento', 'catedral-elements' ),
			'type'        => Controls_Manager::CHOOSE,
			'options'     => array(
				'left'   => array( 'title' => __( 'Esquerda', 'catedral-elements' ), 'icon' => 'eicon-text-align-left' ),
				'center' => array( 'title' => __( 'Centro', 'catedral-elements' ), 'icon' => 'eicon-text-align-center' ),
				'right'  => array( 'title' => __( 'Direita', 'catedral-elements' ), 'icon' => 'eicon-text-align-right' ),
			),
			'description' => __( 'Alinhamento do título por dispositivo (independente do bloco). Deixe vazio para herdar o alinhamento do conteúdo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-slide__title' => 'text-align: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'title_typo',
			'selector'       => '{{WRAPPER}} .cee-slide__title',
			'fields_options' => array(
				'typography'     => array( 'default' => 'yes' ),
				'font_family'    => array( 'default' => 'Raleway' ),
				'font_weight'    => array( 'default' => '200' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 20 ) ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.15 ) ),
				'line_height'    => array( 'default' => array( 'unit' => 'em', 'size' => 1.4 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->add_responsive_control( 'title_spacing_bottom', array(
			'label'      => __( 'Espaço abaixo', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
			'default'    => array( 'size' => 8, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Posição do Título
	 * ------------------------------------------------------------------- */

	protected function style_title_position() {
		$this->start_controls_section( 'style_title_position', array(
			'label' => __( 'Posição do Título', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'title_position_hint', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => __( 'Vertical do título sobre o carrossel (em cima / meio / em baixo): use "Âncora vertical" na seção <strong>Posição do Bloco de Conteúdo</strong> — já responsiva por dispositivo. Abaixo você faz o posicionamento horizontal e o ajuste fino (X/Y) do título em cada tela.', 'catedral-elements' ),
			'content_classes' => 'elementor-descriptor',
		) );

		$this->add_responsive_control( 'title_h_pos', array(
			'label'                => __( 'Posição horizontal do título', 'catedral-elements' ),
			'type'                 => Controls_Manager::CHOOSE,
			'options'              => array(
				'left'   => array( 'title' => __( 'Esquerda', 'catedral-elements' ), 'icon' => 'eicon-h-align-left' ),
				'center' => array( 'title' => __( 'Centro', 'catedral-elements' ), 'icon' => 'eicon-h-align-center' ),
				'right'  => array( 'title' => __( 'Direita', 'catedral-elements' ), 'icon' => 'eicon-h-align-right' ),
			),
			'description'          => __( 'Posiciona a caixa do título dentro do bloco, independente do subtítulo/texto. Deixe vazio para largura total.', 'catedral-elements' ),
			'selectors_dictionary' => array(
				'left'   => 'margin-left:0;margin-right:auto;',
				'center' => 'margin-left:auto;margin-right:auto;',
				'right'  => 'margin-left:auto;margin-right:0;',
			),
			'selectors'            => array(
				'{{WRAPPER}} .cee-slide__title' => 'width:-webkit-fit-content;width:fit-content;max-width:100%;{{VALUE}}',
			),
		) );

		$this->add_responsive_control( 'title_offset_x', array(
			'label'       => __( 'Ajuste fino horizontal (X)', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px', '%', 'vw' ),
			'range'       => array(
				'px' => array( 'min' => -400, 'max' => 400 ),
				'%'  => array( 'min' => -100, 'max' => 100 ),
				'vw' => array( 'min' => -50, 'max' => 50 ),
			),
			'default'     => array( 'size' => 0, 'unit' => 'px' ),
			'description' => __( 'Move o título na horizontal (aceita valores negativos). Ajuste por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-slide__title' => '--cee-title-x: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'title_offset_y', array(
			'label'       => __( 'Ajuste fino vertical (Y)', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px', '%', 'vh' ),
			'range'       => array(
				'px' => array( 'min' => -400, 'max' => 400 ),
				'%'  => array( 'min' => -100, 'max' => 100 ),
				'vh' => array( 'min' => -50, 'max' => 50 ),
			),
			'default'     => array( 'size' => 0, 'unit' => 'px' ),
			'description' => __( 'Move o título na vertical (aceita valores negativos). Ajuste por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-slide__title' => '--cee-title-y: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Subtítulo do Slide
	 * ------------------------------------------------------------------- */

	protected function style_subtitle() {
		$this->start_controls_section( 'style_subtitle', array(
			'label' => __( 'Subtítulo do Slide', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'subtitle_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.80)',
			'selectors' => array( '{{WRAPPER}} .cee-slide__subtitle' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'subtitle_typo',
			'selector'       => '{{WRAPPER}} .cee-slide__subtitle',
			'fields_options' => array(
				'typography'     => array( 'default' => 'yes' ),
				'font_family'    => array( 'default' => 'Raleway' ),
				'font_weight'    => array( 'default' => '300' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.05 ) ),
			),
		) );

		$this->add_responsive_control( 'subtitle_spacing_bottom', array(
			'label'      => __( 'Espaço abaixo', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
			'default'    => array( 'size' => 16, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__subtitle' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Conteúdo (Rich Text)
	 * ------------------------------------------------------------------- */

	protected function style_content() {
		$this->start_controls_section( 'style_content', array(
			'label' => __( 'Conteúdo (Rich Text)', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'content_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.75)',
			'selectors' => array( '{{WRAPPER}} .cee-slide__text' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'content_typo',
			'selector'       => '{{WRAPPER}} .cee-slide__text',
			'fields_options' => array(
				'typography'  => array( 'default' => 'yes' ),
				'font_family' => array( 'default' => 'Raleway' ),
				'font_weight' => array( 'default' => '300' ),
				'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
			),
		) );

		$this->add_responsive_control( 'content_spacing_bottom', array(
			'label'      => __( 'Espaço abaixo', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 80 ) ),
			'default'    => array( 'size' => 24, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__text' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Botão CTA
	 * ------------------------------------------------------------------- */

	protected function style_button() {
		$this->start_controls_section( 'style_button', array(
			'label' => __( 'Botão CTA', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'btn_bg_color', array(
			'label'     => __( 'Fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'transparent',
			'selectors' => array( '{{WRAPPER}} .cee-slide__btn' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_text_color', array(
			'label'     => __( 'Cor do texto', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-slide__btn' => 'color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_border_color', array(
			'label'     => __( 'Cor da borda', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.70)',
			'selectors' => array( '{{WRAPPER}} .cee-slide__btn' => 'border-color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_bg_hover', array(
			'label'     => __( 'Fundo no hover', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.15)',
			'selectors' => array( '{{WRAPPER}} .cee-slide__btn:hover, {{WRAPPER}} .cee-slide__btn:focus-visible' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_text_hover', array(
			'label'     => __( 'Texto no hover', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-slide__btn:hover, {{WRAPPER}} .cee-slide__btn:focus-visible' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'btn_typo',
			'selector'       => '{{WRAPPER}} .cee-slide__btn',
			'fields_options' => array(
				'typography'     => array( 'default' => 'yes' ),
				'font_family'    => array( 'default' => 'Raleway' ),
				'font_weight'    => array( 'default' => '300' ),
				'font_size'      => array( 'default' => array( 'unit' => 'px', 'size' => 12 ) ),
				'letter_spacing' => array( 'default' => array( 'unit' => 'em', 'size' => 0.2 ) ),
				'text_transform' => array( 'default' => 'uppercase' ),
			),
		) );

		$this->add_responsive_control( 'btn_padding', array(
			'label'      => __( 'Espaçamento interno', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => 12, 'right' => 28, 'bottom' => 12, 'left' => 28, 'unit' => 'px', 'isLinked' => false ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'btn_border_radius', array(
			'label'      => __( 'Arredondamento', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'default'    => array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_control( 'btn_border_width', array(
			'label'      => __( 'Espessura da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 5 ) ),
			'default'    => array( 'size' => 1, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__btn' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Posição do Bloco de Conteúdo
	 * ------------------------------------------------------------------- */

	protected function style_content_block() {
		$this->start_controls_section( 'style_content_block', array(
			'label' => __( 'Posição do Bloco de Conteúdo', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'content_block_h', array(
			'label'                => __( 'Âncora horizontal', 'catedral-elements' ),
			'type'                 => Controls_Manager::CHOOSE,
			'default'              => 'left',
			'options'              => array(
				'left'   => array( 'title' => __( 'Esquerda', 'catedral-elements' ), 'icon' => 'eicon-h-align-left' ),
				'center' => array( 'title' => __( 'Centro', 'catedral-elements' ), 'icon' => 'eicon-h-align-center' ),
				'right'  => array( 'title' => __( 'Direita', 'catedral-elements' ), 'icon' => 'eicon-h-align-right' ),
			),
			'toggle'               => false,
			'selectors_dictionary' => array(
				'left'   => 'flex-start',
				'center' => 'center',
				'right'  => 'flex-end',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-slide__inner' => 'justify-content: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'content_block_v', array(
			'label'                => __( 'Âncora vertical', 'catedral-elements' ),
			'type'                 => Controls_Manager::CHOOSE,
			'default'              => 'bottom',
			'options'              => array(
				'top'    => array( 'title' => __( 'Topo', 'catedral-elements' ), 'icon' => 'eicon-v-align-top' ),
				'middle' => array( 'title' => __( 'Meio', 'catedral-elements' ), 'icon' => 'eicon-v-align-middle' ),
				'bottom' => array( 'title' => __( 'Base', 'catedral-elements' ), 'icon' => 'eicon-v-align-bottom' ),
			),
			'toggle'               => false,
			'selectors_dictionary' => array(
				'top'    => 'flex-start',
				'middle' => 'center',
				'bottom' => 'flex-end',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-slide__inner' => 'align-items: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'content_block_offset_h', array(
			'label'      => __( 'Offset horizontal', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ), '%' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 48, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__inner' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'content_block_offset_v', array(
			'label'      => __( 'Offset vertical', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 200 ), '%' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 48, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__inner' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'content_block_max_width', array(
			'label'      => __( 'Largura máxima do bloco', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%' ),
			'range'      => array( 'px' => array( 'min' => 200, 'max' => 1200 ), '%' => array( 'min' => 20, 'max' => 100 ) ),
			'default'    => array( 'size' => 600, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-slide__content' => 'max-width: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'content_block_text_align', array(
			'label'     => __( 'Alinhamento do texto', 'catedral-elements' ),
			'type'      => Controls_Manager::CHOOSE,
			'default'   => 'left',
			'options'   => array(
				'left'   => array( 'title' => __( 'Esquerda', 'catedral-elements' ), 'icon' => 'eicon-text-align-left' ),
				'center' => array( 'title' => __( 'Centro', 'catedral-elements' ), 'icon' => 'eicon-text-align-center' ),
				'right'  => array( 'title' => __( 'Direita', 'catedral-elements' ), 'icon' => 'eicon-text-align-right' ),
			),
			'selectors' => array( '{{WRAPPER}} .cee-slide__content' => 'text-align: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ===================================================================== */

	/**
	 * Mapeia o valor do easing para o CSS real.
	 *
	 * @param string $key Chave do control.
	 * @return string
	 */
	protected function easing_css( $key ) {
		$map = array(
			'ease'        => 'ease',
			'ease-in-out' => 'ease-in-out',
			'premium'     => 'cubic-bezier(0.76, 0, 0.24, 1)',
			'linear'      => 'linear',
		);
		return isset( $map[ $key ] ) ? $map[ $key ] : $map['premium'];
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$slides   = isset( $settings['slides'] ) && is_array( $settings['slides'] ) ? $settings['slides'] : array();

		if ( empty( $slides ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="cee-slide-carousel cee-slide-carousel--empty">' . esc_html__( 'Adicione ao menos um slide no painel de conteúdo.', 'catedral-elements' ) . '</div>';
			}
			return;
		}

		$autoplay        = 'yes' === ( $settings['autoplay_enable'] ?? 'yes' );
		$interval        = isset( $settings['autoplay_interval']['size'] ) ? (int) $settings['autoplay_interval']['size'] : 5000;
		$pause_hover     = 'yes' === ( $settings['autoplay_pause_on_hover'] ?? 'yes' );
		$pause_inter     = 'yes' === ( $settings['autoplay_pause_on_interaction'] ?? 'yes' );
		$loop            = 'yes' === ( $settings['loop'] ?? 'yes' );
		$speed           = isset( $settings['transition_speed']['size'] ) ? (int) $settings['transition_speed']['size'] : 800;
		$easing          = $this->easing_css( $settings['transition_easing'] ?? 'premium' );
		$overlap         = 'yes' === ( $settings['transition_overlap'] ?? 'yes' );
		$scroll_enable   = 'yes' === ( $settings['scroll_enable'] ?? 'yes' );
		$scroll_pin      = 'yes' === ( $settings['scroll_pin'] ?? 'yes' );
		$scroll_snap       = 'yes' === ( $settings['scroll_snap'] ?? 'yes' );
		$sensitivity       = isset( $settings['scroll_sensitivity']['size'] ) ? (int) $settings['scroll_sensitivity']['size'] : 150;
		$scroll_touch      = 'yes' === ( $settings['scroll_touch'] ?? 'yes' );
		$scroll_pin_mobile = 'yes' === ( $settings['scroll_pin_mobile'] ?? '' );

		$count      = count( $slides );
		$pin_factor = isset( $settings['scroll_pin_length']['size'] ) ? (float) $settings['scroll_pin_length']['size'] : 1.0;
		if ( $pin_factor <= 0 ) {
			$pin_factor = 1.0;
		}
		// Comprimento total do wrapper (múltiplo da altura do widget): 1 viewport
		// fixa + (N-1) trechos de rolagem, um por transição entre slides.
		$pin_len = 1 + max( 0, $count - 1 ) * $pin_factor;
		$nav_enable      = 'yes' === ( $settings['nav_enable'] ?? 'yes' );
		$nav_orientation = in_array( $settings['nav_orientation'] ?? 'vertical', array( 'vertical', 'horizontal', 'vertical-text' ), true ) ? $settings['nav_orientation'] : 'vertical';
		$content_width   = 'full' === ( $settings['content_width_mode'] ?? 'boxed' ) ? 'full' : 'boxed';
		$overlay_type    = $settings['overlay_type'] ?? 'gradient';
		$scrim_enable    = 'yes' === ( $settings['scrim_enable'] ?? 'yes' );
		$image_size      = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'full';

		$root_style = sprintf(
			'--cee-transition-speed:%dms;--cee-easing:%s;--cee-track-length:%s;',
			$speed,
			esc_attr( $easing ),
			esc_attr( rtrim( rtrim( number_format( $pin_len, 3, '.', '' ), '0' ), '.' ) )
		);

		$skin = in_array( $settings['skin'] ?? 'padrao', array( 'padrao', 'editorial' ), true ) ? $settings['skin'] : 'padrao';

		$root_classes = 'cee-slide-carousel cee-skin-' . $skin;
		if ( $autoplay ) {
			$root_classes .= ' cee-slide-carousel--autoplay';
		}
		?>
		<div class="<?php echo esc_attr( $root_classes ); ?>"
			data-skin="<?php echo esc_attr( $skin ); ?>"
			style="<?php echo esc_attr( $root_style ); ?>"
			data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>"
			data-autoplay-interval="<?php echo esc_attr( $interval ); ?>"
			data-autoplay-pause-hover="<?php echo esc_attr( $pause_hover ? 'true' : 'false' ); ?>"
			data-autoplay-pause-interaction="<?php echo esc_attr( $pause_inter ? 'true' : 'false' ); ?>"
			data-loop="<?php echo esc_attr( $loop ? 'true' : 'false' ); ?>"
			data-transition-speed="<?php echo esc_attr( $speed ); ?>"
			data-transition-easing="<?php echo esc_attr( $easing ); ?>"
			data-transition-overlap="<?php echo esc_attr( $overlap ? 'true' : 'false' ); ?>"
			data-scroll-enable="<?php echo esc_attr( $scroll_enable ? 'true' : 'false' ); ?>"
			data-scroll-pin-enabled="<?php echo esc_attr( ( $scroll_enable && $scroll_pin ) ? 'true' : 'false' ); ?>"
			data-scroll-snap="<?php echo esc_attr( $scroll_snap ? 'true' : 'false' ); ?>"
			data-scroll-sensitivity="<?php echo esc_attr( $sensitivity ); ?>"
			data-scroll-touch="<?php echo esc_attr( $scroll_touch ? 'true' : 'false' ); ?>"
			data-scroll-pin-mobile="<?php echo esc_attr( $scroll_pin_mobile ? 'true' : 'false' ); ?>"
			data-slides-count="<?php echo esc_attr( $count ); ?>"
			data-content-width="<?php echo esc_attr( $content_width ); ?>"
			data-scroll-mode="slider"
			data-current-index="0">

			<div class="cee-slide-carousel__pin">
				<div class="cee-slide-carousel__viewport">
					<div class="cee-slide-carousel__track">
						<?php foreach ( $slides as $index => $slide ) : ?>
							<?php $this->render_slide( $slide, $index, $overlay_type, $scrim_enable, $image_size ); ?>
						<?php endforeach; ?>
					</div>
				</div>

				<?php if ( $nav_enable ) : ?>
					<nav class="cee-nav" aria-label="<?php echo esc_attr__( 'Navegação dos slides', 'catedral-elements' ); ?>"
						data-nav-orientation="<?php echo esc_attr( $nav_orientation ); ?>">
						<ul class="cee-nav__list">
							<?php foreach ( $slides as $index => $slide ) : ?>
								<?php
								$name      = isset( $slide['slide_name'] ) && '' !== $slide['slide_name'] ? $slide['slide_name'] : sprintf( /* translators: %d: slide number */ __( 'Slide %d', 'catedral-elements' ), $index + 1 );
								$is_active = 0 === $index;
								?>
								<li class="cee-nav__row">
									<button type="button"
										class="cee-nav__item<?php echo $is_active ? ' cee-nav__item--active' : ''; ?>"
										data-slide-index="<?php echo esc_attr( $index ); ?>"
										aria-current="<?php echo esc_attr( $is_active ? 'true' : 'false' ); ?>"
										aria-label="<?php echo esc_attr( sprintf( /* translators: %s: slide name */ __( 'Ir para slide: %s', 'catedral-elements' ), $name ) ); ?>">
										<?php echo esc_html( $name ); ?>
									</button>
								</li>
							<?php endforeach; ?>
						</ul>
					</nav>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Renderiza um slide individual.
	 *
	 * @param array  $slide        Dados do slide (item do repeater).
	 * @param int    $index        Índice.
	 * @param string $overlay_type Tipo de overlay configurado.
	 * @param bool   $scrim_enable Exibir gradiente de contraste do texto.
	 * @param string $image_size   Tamanho de imagem registrado a servir ("full" = original).
	 */
	protected function render_slide( $slide, $index, $overlay_type, $scrim_enable = true, $image_size = 'full' ) {
		$image_url = isset( $slide['slide_image']['url'] ) ? $slide['slide_image']['url'] : '';
		$image_id  = isset( $slide['slide_image']['id'] ) ? absint( $slide['slide_image']['id'] ) : 0;

		// Resolução da imagem: serve o tamanho registrado escolhido (quando houver anexo).
		if ( $image_id && 'full' !== $image_size ) {
			$sized = wp_get_attachment_image_src( $image_id, $image_size );
			if ( is_array( $sized ) && ! empty( $sized[0] ) ) {
				$image_url = $sized[0];
			}
		}

		// Foco por-slide sobrescreve a posição global; "global" (ou vazio) herda do Estilo.
		$position     = $slide['slide_image_position'] ?? 'global';
		$emit_position = true;
		if ( 'global' === $position || '' === $position ) {
			$emit_position = false;
		} elseif ( 'custom' === $position ) {
			$position = '' !== ( $slide['slide_image_position_custom'] ?? '' ) ? $slide['slide_image_position_custom'] : 'center center';
		}

		$alt = $image_id ? get_post_meta( $image_id, '_wp_attachment_image_alt', true ) : '';
		if ( '' === $alt ) {
			$alt = isset( $slide['slide_name'] ) ? $slide['slide_name'] : '';
		}

		$title_tag = $slide['slide_title_tag'] ?? 'h2';
		$title_tag = in_array( $title_tag, array( 'h1', 'h2', 'h3', 'h4', 'p', 'div' ), true ) ? $title_tag : 'h2';

		$is_active = 0 === $index;

		$link      = $slide['slide_btn_link'] ?? array();
		$btn_text  = $slide['slide_btn_text'] ?? '';
		$btn_url   = isset( $link['url'] ) ? $link['url'] : '';
		$btn_attrs = '';
		$rel_parts = array();
		if ( ! empty( $link['is_external'] ) ) {
			$btn_attrs .= ' target="_blank"';
			$rel_parts[] = 'noopener';
			$rel_parts[] = 'noreferrer';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$rel_parts[] = 'nofollow';
		}
		if ( ! empty( $rel_parts ) ) {
			$btn_attrs .= ' rel="' . esc_attr( implode( ' ', $rel_parts ) ) . '"';
		}
		?>
		<div class="cee-slide<?php echo $is_active ? ' cee-slide--active' : ''; ?>"
			data-slide-index="<?php echo esc_attr( $index ); ?>"
			aria-hidden="<?php echo esc_attr( $is_active ? 'false' : 'true' ); ?>">

			<div class="cee-slide__media">
				<?php if ( $image_url ) : ?>
					<img class="cee-slide__image"
						src="<?php echo esc_url( $image_url ); ?>"
						alt="<?php echo esc_attr( $alt ); ?>"
						<?php if ( $emit_position ) : ?>style="object-position: <?php echo esc_attr( $position ); ?>;"<?php endif; ?>
						loading="<?php echo esc_attr( $is_active ? 'eager' : 'lazy' ); ?>"
						decoding="async" />
				<?php endif; ?>

				<?php if ( 'none' !== $overlay_type ) : ?>
					<div class="cee-slide__overlay" aria-hidden="true"></div>
				<?php endif; ?>

				<?php if ( $scrim_enable ) : ?>
					<div class="cee-slide__scrim" aria-hidden="true"></div>
				<?php endif; ?>
			</div>

			<div class="cee-slide__inner">
				<div class="cee-slide__content">
					<?php if ( ! empty( $slide['slide_title'] ) ) : ?>
						<<?php echo esc_attr( $title_tag ); ?> class="cee-slide__title"><?php echo nl2br( esc_html( $slide['slide_title'] ) ); ?></<?php echo esc_attr( $title_tag ); ?>>
					<?php endif; ?>

					<?php if ( ! empty( $slide['slide_subtitle'] ) ) : ?>
						<div class="cee-slide__subtitle"><?php echo esc_html( $slide['slide_subtitle'] ); ?></div>
					<?php endif; ?>

					<?php if ( ! empty( $slide['slide_content'] ) ) : ?>
						<div class="cee-slide__text"><?php echo wp_kses_post( $slide['slide_content'] ); ?></div>
					<?php endif; ?>

					<?php if ( '' !== $btn_text && '' !== $btn_url ) : ?>
						<a class="cee-slide__btn" href="<?php echo esc_url( $btn_url ); ?>"<?php echo $btn_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attrs are hardcoded literals. ?>>
							<?php echo esc_html( $btn_text ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php
	}
}
