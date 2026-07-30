<?php
/**
 * Widget Elementor: Pontos Interativos (Hotspots).
 *
 * Mapeia pontos de informação sobre uma imagem base. Cada ponto é um ícone
 * circular em SVG com efeito radial (pulso) e cores personalizáveis. Ao clicar,
 * abre uma caixa de conteúdo no padrão "Image Box" do Elementor (mídia + título
 * + texto), totalmente customizável, com a imagem suportando ajuste de encaixe
 * e posição (object-fit / object-position) e proporção.
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

class CEE_Hotspots_Widget extends Widget_Base {

	public function get_name() {
		return 'cee_hotspots';
	}

	public function get_title() {
		return __( 'Pontos Interativos', 'catedral-elements' );
	}

	public function get_icon() {
		return 'eicon-map-pin';
	}

	public function get_categories() {
		return array( 'catedral-elements' );
	}

	public function get_keywords() {
		return array( 'hotspot', 'ponto', 'pontos', 'tooltip', 'mapa', 'interativo', 'catedral' );
	}

	public function get_style_depends() {
		return array( 'cee-hotspots' );
	}

	public function get_script_depends() {
		return array( 'cee-hotspots' );
	}

	/* =====================================================================
	 * CONTROLES
	 * ===================================================================== */

	protected function register_controls() {
		// CONTENT.
		$this->section_map();
		$this->section_points();

		// STYLE.
		$this->style_trigger();
		$this->style_box();
		$this->style_box_image();
		$this->style_box_title();
		$this->style_box_text();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Imagem base
	 * ------------------------------------------------------------------- */

	protected function section_map() {
		$this->start_controls_section( 'sec_map', array(
			'label' => __( 'Palco', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'stage_mode', array(
			'label'        => __( 'Tipo de palco', 'catedral-elements' ),
			'type'         => Controls_Manager::SELECT,
			'default'      => 'image',
			'options'      => array(
				'image'   => __( 'Imagem base (âncora)', 'catedral-elements' ),
				'blank'   => __( 'Palco livre (sem imagem)', 'catedral-elements' ),
				'overlay' => __( 'Sobrepor o conteúdo', 'catedral-elements' ),
			),
			'description'  => __( 'Livre: uma área com altura própria onde você posiciona os pontos. Sobrepor: o widget vira uma camada sobre o conteúdo atrás dele (coloque-o dentro de um Contêiner com posição Relativa).', 'catedral-elements' ),
			'prefix_class' => 'cee-hs-mode-',
		) );

		$this->add_control( 'base_image', array(
			'label'     => __( 'Imagem', 'catedral-elements' ),
			'type'      => Controls_Manager::MEDIA,
			'default'   => array( 'url' => Utils::get_placeholder_image_src() ),
			'dynamic'   => array( 'active' => true ),
			'condition' => array( 'stage_mode' => 'image' ),
		) );

		$this->add_responsive_control( 'stage_height', array(
			'label'      => __( 'Altura do palco', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'vh' ),
			'range'      => array(
				'px' => array( 'min' => 120, 'max' => 1200 ),
				'vh' => array( 'min' => 10, 'max' => 100 ),
			),
			'default'    => array( 'size' => 400, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-stage-height: {{SIZE}}{{UNIT}};' ),
			'condition'  => array( 'stage_mode' => array( 'blank', 'overlay' ) ),
		) );

		$this->add_control( 'stage_bg', array(
			'label'     => __( 'Fundo do palco', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-hotspots__stage' => 'background-color: {{VALUE}};' ),
			'condition' => array( 'stage_mode' => array( 'blank', 'overlay' ) ),
		) );

		$this->add_responsive_control( 'stage_max_width', array(
			'label'      => __( 'Largura máxima', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', '%', 'vw' ),
			'range'      => array(
				'px' => array( 'min' => 200, 'max' => 1600 ),
				'%'  => array( 'min' => 20, 'max' => 100 ),
				'vw' => array( 'min' => 20, 'max' => 100 ),
			),
			'default'    => array( 'size' => 100, 'unit' => '%' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspots__stage' => 'max-width: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'stage_align', array(
			'label'                => __( 'Alinhamento', 'catedral-elements' ),
			'type'                 => Controls_Manager::CHOOSE,
			'default'              => 'center',
			'options'              => array(
				'flex-start' => array( 'title' => __( 'Esquerda', 'catedral-elements' ), 'icon' => 'eicon-h-align-left' ),
				'center'     => array( 'title' => __( 'Centro', 'catedral-elements' ), 'icon' => 'eicon-h-align-center' ),
				'flex-end'   => array( 'title' => __( 'Direita', 'catedral-elements' ), 'icon' => 'eicon-h-align-right' ),
			),
			'toggle'               => false,
			'selectors'            => array( '{{WRAPPER}} .cee-hotspots' => 'justify-content: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Pontos
	 * ------------------------------------------------------------------- */

	protected function section_points() {
		$this->start_controls_section( 'sec_points', array(
			'label' => __( 'Pontos', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$rep = new Repeater();

		$rep->add_control( 'point_label', array(
			'label'       => __( 'Rótulo (interno/acessibilidade)', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'Ponto', 'catedral-elements' ),
			'label_block' => true,
			'dynamic'     => array( 'active' => true ),
		) );

		$rep->add_responsive_control( 'point_x', array(
			'label'      => __( 'Posição horizontal', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
			'default'    => array( 'size' => 50, 'unit' => '%' ),
			'selectors'  => array( '{{WRAPPER}} {{CURRENT_ITEM}}.cee-hotspot' => 'left: {{SIZE}}{{UNIT}};' ),
		) );

		$rep->add_responsive_control( 'point_y', array(
			'label'      => __( 'Posição vertical', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( '%' ),
			'range'      => array( '%' => array( 'min' => 0, 'max' => 100 ) ),
			'default'    => array( 'size' => 50, 'unit' => '%' ),
			'selectors'  => array( '{{WRAPPER}} {{CURRENT_ITEM}}.cee-hotspot' => 'top: {{SIZE}}{{UNIT}};' ),
		) );

		$rep->add_control( 'box_placement', array(
			'label'   => __( 'Abertura da caixa', 'catedral-elements' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'top',
			'options' => array(
				'auto'   => __( 'Automático', 'catedral-elements' ),
				'top'    => __( 'Acima', 'catedral-elements' ),
				'bottom' => __( 'Abaixo', 'catedral-elements' ),
				'left'   => __( 'À esquerda', 'catedral-elements' ),
				'right'  => __( 'À direita', 'catedral-elements' ),
			),
		) );

		$rep->add_control( 'point_accent', array(
			'label'       => __( 'Cor do ponto (opcional)', 'catedral-elements' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'description' => __( 'Vazio = usa a cor global definida no Estilo.', 'catedral-elements' ),
			'selectors'   => array(
				'{{WRAPPER}} {{CURRENT_ITEM}}.cee-hotspot' => '--cee-hs-dot-edge: {{VALUE}}; --cee-hs-ring: {{VALUE}};',
			),
		) );

		$rep->add_control( 'box_image', array(
			'label'   => __( 'Imagem da caixa', 'catedral-elements' ),
			'type'    => Controls_Manager::MEDIA,
			'default' => array( 'url' => Utils::get_placeholder_image_src() ),
			'dynamic' => array( 'active' => true ),
		) );

		$rep->add_control( 'box_image_position', array(
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

		$rep->add_control( 'box_image_position_custom', array(
			'label'       => __( 'object-position personalizado', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'placeholder' => '50% 30%',
			'condition'   => array( 'box_image_position' => 'custom' ),
		) );

		$rep->add_control( 'box_title', array(
			'label'       => __( 'Título', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => __( 'Título do ponto', 'catedral-elements' ),
			'label_block' => true,
			'dynamic'     => array( 'active' => true ),
		) );

		$rep->add_control( 'box_text', array(
			'label'   => __( 'Conteúdo', 'catedral-elements' ),
			'type'    => Controls_Manager::WYSIWYG,
			'default' => __( 'Descreva aqui a informação deste ponto.', 'catedral-elements' ),
			'dynamic' => array( 'active' => true ),
		) );

		$this->add_control( 'points', array(
			'label'       => __( 'Pontos', 'catedral-elements' ),
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ point_label }}}',
			'default'     => $this->default_points(),
		) );

		$this->end_controls_section();
	}

	/**
	 * Dois pontos de exemplo.
	 *
	 * @return array
	 */
	protected function default_points() {
		$ph = Utils::get_placeholder_image_src();

		return array(
			array(
				'point_label'  => __( 'Ponto 1', 'catedral-elements' ),
				'point_x'      => array( 'size' => 32, 'unit' => '%' ),
				'point_y'      => array( 'size' => 40, 'unit' => '%' ),
				'box_placement' => 'top',
				'box_image'    => array( 'url' => $ph ),
				'box_title'    => __( 'Primeiro ponto', 'catedral-elements' ),
				'box_text'     => __( 'Clique para revelar a informação deste local.', 'catedral-elements' ),
			),
			array(
				'point_label'  => __( 'Ponto 2', 'catedral-elements' ),
				'point_x'      => array( 'size' => 68, 'unit' => '%' ),
				'point_y'      => array( 'size' => 62, 'unit' => '%' ),
				'box_placement' => 'bottom',
				'box_image'    => array( 'url' => $ph ),
				'box_title'    => __( 'Segundo ponto', 'catedral-elements' ),
				'box_text'     => __( 'Cada ponto abre a sua própria caixa de conteúdo.', 'catedral-elements' ),
			),
		);
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Ícone / Ponto
	 * ------------------------------------------------------------------- */

	protected function style_trigger() {
		$this->start_controls_section( 'style_trigger', array(
			'label' => __( 'Ícone do ponto', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'trigger_size', array(
			'label'      => __( 'Tamanho', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 10, 'max' => 80 ) ),
			'default'    => array( 'size' => 24, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-size: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_control( 'dot_core_color', array(
			'label'     => __( 'Cor do núcleo (centro)', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-dot-core: {{VALUE}};' ),
		) );

		$this->add_control( 'dot_edge_color', array(
			'label'     => __( 'Cor da borda (radial)', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#C8A24B',
			'selectors' => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-dot-edge: {{VALUE}};' ),
		) );

		$this->add_control( 'ring_color', array(
			'label'     => __( 'Cor do pulso', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(200,162,75,0.55)',
			'selectors' => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-ring: {{VALUE}};' ),
		) );

		$this->add_control( 'pulse_enable', array(
			'label'        => __( 'Efeito de pulso', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'pulse_speed', array(
			'label'      => __( 'Velocidade do pulso (s)', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 's' ),
			'range'      => array( 's' => array( 'min' => 0.5, 'max' => 6, 'step' => 0.1 ) ),
			'default'    => array( 'size' => 2.2, 'unit' => 's' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-pulse-speed: {{SIZE}}s;' ),
			'condition'  => array( 'pulse_enable' => 'yes' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Caixa de conteúdo
	 * ------------------------------------------------------------------- */

	protected function style_box() {
		$this->start_controls_section( 'style_box', array(
			'label' => __( 'Caixa de conteúdo', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'box_width', array(
			'label'      => __( 'Largura', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 180, 'max' => 520 ) ),
			'default'    => array( 'size' => 300, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-box-width: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'box_gap', array(
			'label'      => __( 'Distância do ponto', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 60 ) ),
			'default'    => array( 'size' => 14, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-gap: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_control( 'box_bg', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#101010',
			'selectors' => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-box-bg: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'box_padding', array(
			'label'      => __( 'Espaçamento interno (conteúdo)', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => 18, 'right' => 18, 'bottom' => 18, 'left' => 18, 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspot__content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'box_radius', array(
			'label'      => __( 'Arredondamento', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'default'    => array( 'top' => 10, 'right' => 10, 'bottom' => 10, 'left' => 10, 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspot__box-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'box_border',
			'selector' => '{{WRAPPER}} .cee-hotspot__box-inner',
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'     => 'box_shadow',
			'selector' => '{{WRAPPER}} .cee-hotspot__box-inner',
			'fields_options' => array(
				'box_shadow_type' => array( 'default' => 'yes' ),
				'box_shadow'      => array(
					'default' => array(
						'horizontal' => 0,
						'vertical'   => 18,
						'blur'       => 45,
						'spread'     => -12,
						'color'      => 'rgba(0,0,0,0.45)',
					),
				),
			),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Imagem da caixa
	 * ------------------------------------------------------------------- */

	protected function style_box_image() {
		$this->start_controls_section( 'style_box_image', array(
			'label' => __( 'Imagem da caixa', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'box_img_ratio', array(
			'label'                => __( 'Proporção', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => '16:9',
			'options'              => array(
				'padrao' => __( 'Natural (imagem)', 'catedral-elements' ),
				'1:1'    => '1:1',
				'3:2'    => '3:2',
				'4:3'    => '4:3',
				'16:9'   => '16:9',
				'21:9'   => '21:9',
				'9:16'   => '9:16',
			),
			'selectors_dictionary' => array(
				'padrao' => 'auto',
				'1:1'    => '1 / 1',
				'3:2'    => '3 / 2',
				'4:3'    => '4 / 3',
				'16:9'   => '16 / 9',
				'21:9'   => '21 / 9',
				'9:16'   => '9 / 16',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-img-ratio: {{VALUE}};' ),
		) );

		$this->add_control( 'box_img_fit', array(
			'label'                => __( 'Encaixe', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'cover',
			'options'              => array(
				'cover'   => __( 'Preencher (cover)', 'catedral-elements' ),
				'contain' => __( 'Conter (contain)', 'catedral-elements' ),
			),
			'selectors'            => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-img-fit: {{VALUE}};' ),
		) );

		$this->add_control( 'box_img_position', array(
			'label'     => __( 'Foco global', 'catedral-elements' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'center center',
			'options'   => array(
				'top left'      => __( 'Superior esquerda', 'catedral-elements' ),
				'top center'    => __( 'Superior centro', 'catedral-elements' ),
				'top right'     => __( 'Superior direita', 'catedral-elements' ),
				'center left'   => __( 'Centro esquerda', 'catedral-elements' ),
				'center center' => __( 'Centro', 'catedral-elements' ),
				'center right'  => __( 'Centro direita', 'catedral-elements' ),
				'bottom left'   => __( 'Inferior esquerda', 'catedral-elements' ),
				'bottom center' => __( 'Inferior centro', 'catedral-elements' ),
				'bottom right'  => __( 'Inferior direita', 'catedral-elements' ),
			),
			'selectors' => array( '{{WRAPPER}} .cee-hotspots' => '--cee-hs-img-pos: {{VALUE}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Título da caixa
	 * ------------------------------------------------------------------- */

	protected function style_box_title() {
		$this->start_controls_section( 'style_box_title', array(
			'label' => __( 'Título da caixa', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'title_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-hotspot__title' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'title_typo',
			'selector'       => '{{WRAPPER}} .cee-hotspot__title',
			'fields_options' => array(
				'typography'  => array( 'default' => 'yes' ),
				'font_family' => array( 'default' => 'Raleway' ),
				'font_weight' => array( 'default' => '500' ),
				'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 16 ) ),
			),
		) );

		$this->add_responsive_control( 'title_spacing', array(
			'label'      => __( 'Espaço abaixo', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 40 ) ),
			'default'    => array( 'size' => 8, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-hotspot__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Texto da caixa
	 * ------------------------------------------------------------------- */

	protected function style_box_text() {
		$this->start_controls_section( 'style_box_text', array(
			'label' => __( 'Texto da caixa', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'text_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(255,255,255,0.78)',
			'selectors' => array( '{{WRAPPER}} .cee-hotspot__text' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'           => 'text_typo',
			'selector'       => '{{WRAPPER}} .cee-hotspot__text',
			'fields_options' => array(
				'typography'  => array( 'default' => 'yes' ),
				'font_family' => array( 'default' => 'Raleway' ),
				'font_weight' => array( 'default' => '300' ),
				'font_size'   => array( 'default' => array( 'unit' => 'px', 'size' => 14 ) ),
				'line_height' => array( 'default' => array( 'unit' => 'em', 'size' => 1.6 ) ),
			),
		) );

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ===================================================================== */

	protected function render() {
		$settings = $this->get_settings_for_display();

		$stage_mode = in_array( $settings['stage_mode'] ?? 'image', array( 'image', 'blank', 'overlay' ), true ) ? $settings['stage_mode'] : 'image';
		$is_image   = 'image' === $stage_mode;

		$base_url = $is_image && isset( $settings['base_image']['url'] ) ? $settings['base_image']['url'] : '';
		$base_id  = isset( $settings['base_image']['id'] ) ? absint( $settings['base_image']['id'] ) : 0;
		$base_alt = $base_id ? get_post_meta( $base_id, '_wp_attachment_image_alt', true ) : '';

		$points = ! empty( $settings['points'] ) && is_array( $settings['points'] ) ? $settings['points'] : array();

		$pulse   = 'yes' === ( $settings['pulse_enable'] ?? 'yes' ) ? 'yes' : 'no';
		$ratio   = $settings['box_img_ratio'] ?? '16:9';
		$img_natural = 'padrao' === $ratio ? 'yes' : 'no';
		$uid     = $this->get_id();
		?>
		<div class="cee-hotspots" data-pulse="<?php echo esc_attr( $pulse ); ?>" data-img-natural="<?php echo esc_attr( $img_natural ); ?>" data-stage-mode="<?php echo esc_attr( $stage_mode ); ?>">
			<div class="cee-hotspots__stage">
				<?php if ( $is_image && $base_url ) : ?>
					<img class="cee-hotspots__base" src="<?php echo esc_url( $base_url ); ?>" alt="<?php echo esc_attr( $base_alt ); ?>" loading="lazy" decoding="async" />
				<?php endif; ?>

				<?php foreach ( $points as $i => $p ) : ?>
					<?php $this->render_point( $p, $i, $uid ); ?>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Renderiza um ponto interativo (ícone SVG + caixa de conteúdo).
	 *
	 * @param array  $p   Dados do ponto (item do repeater).
	 * @param int    $i   Índice.
	 * @param string $uid ID único do widget (para ids de gradiente/caixa).
	 */
	protected function render_point( $p, $i, $uid ) {
		$item_class = ! empty( $p['_id'] ) ? 'elementor-repeater-item-' . $p['_id'] : '';

		$placement = in_array( $p['box_placement'] ?? 'top', array( 'auto', 'top', 'bottom', 'left', 'right' ), true ) ? $p['box_placement'] : 'top';
		$runtime   = 'auto' === $placement ? 'top' : $placement;

		$label = isset( $p['point_label'] ) && '' !== $p['point_label']
			? $p['point_label']
			: sprintf( /* translators: %d: número do ponto */ __( 'Ponto %d', 'catedral-elements' ), $i + 1 );

		$img_url = isset( $p['box_image']['url'] ) ? $p['box_image']['url'] : '';
		$img_id  = isset( $p['box_image']['id'] ) ? absint( $p['box_image']['id'] ) : 0;
		$img_alt = $img_id ? get_post_meta( $img_id, '_wp_attachment_image_alt', true ) : '';
		if ( '' === $img_alt ) {
			$img_alt = $label;
		}

		// Foco por-ponto sobrescreve a posição global; "global"/vazio herda do Estilo.
		$pos        = $p['box_image_position'] ?? 'global';
		$emit_pos   = true;
		if ( 'global' === $pos || '' === $pos ) {
			$emit_pos = false;
		} elseif ( 'custom' === $pos ) {
			$pos = '' !== ( $p['box_image_position_custom'] ?? '' ) ? $p['box_image_position_custom'] : 'center center';
		}

		$title = $p['box_title'] ?? '';
		$text  = $p['box_text'] ?? '';

		$grad_id = 'cee-hs-grad-' . $uid . '-' . $i;
		$box_id  = 'cee-hs-box-' . $uid . '-' . $i;
		?>
		<div class="cee-hotspot <?php echo esc_attr( $item_class ); ?>"
			data-placement="<?php echo esc_attr( $placement ); ?>"
			data-runtime-placement="<?php echo esc_attr( $runtime ); ?>">

			<button type="button"
				class="cee-hotspot__trigger"
				aria-expanded="false"
				aria-controls="<?php echo esc_attr( $box_id ); ?>"
				aria-label="<?php echo esc_attr( sprintf( /* translators: %s: rótulo do ponto */ __( 'Abrir informação: %s', 'catedral-elements' ), $label ) ); ?>">
				<span class="cee-hotspot__pulse" aria-hidden="true"></span>
				<svg class="cee-hotspot__svg" viewBox="0 0 100 100" aria-hidden="true" focusable="false">
					<defs>
						<radialGradient id="<?php echo esc_attr( $grad_id ); ?>" cx="50%" cy="50%" r="50%">
							<stop offset="0%" class="cee-hotspot__stop--core" />
							<stop offset="100%" class="cee-hotspot__stop--edge" />
						</radialGradient>
					</defs>
					<circle class="cee-hotspot__core" cx="50" cy="50" r="42" fill="url(#<?php echo esc_attr( $grad_id ); ?>)" />
				</svg>
			</button>

			<div class="cee-hotspot__box" id="<?php echo esc_attr( $box_id ); ?>" role="dialog"
				aria-label="<?php echo esc_attr( '' !== $title ? $title : $label ); ?>">
				<span class="cee-hotspot__arrow" aria-hidden="true"></span>
				<div class="cee-hotspot__box-inner">
					<?php if ( $img_url ) : ?>
						<div class="cee-hotspot__media">
							<img class="cee-hotspot__img"
								src="<?php echo esc_url( $img_url ); ?>"
								alt="<?php echo esc_attr( $img_alt ); ?>"
								<?php if ( $emit_pos ) : ?>style="object-position: <?php echo esc_attr( $pos ); ?>;"<?php endif; ?>
								loading="lazy" decoding="async" />
						</div>
					<?php endif; ?>

					<div class="cee-hotspot__content">
						<?php if ( '' !== $title ) : ?>
							<div class="cee-hotspot__title"><?php echo esc_html( $title ); ?></div>
						<?php endif; ?>

						<?php if ( '' !== $text ) : ?>
							<div class="cee-hotspot__text"><?php echo wp_kses_post( $text ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
