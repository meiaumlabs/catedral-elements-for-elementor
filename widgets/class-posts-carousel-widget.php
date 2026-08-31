<?php
/**
 * Widget Elementor: Posts em Carrossel.
 *
 * Loop de posts (qualquer CPT) em carrossel horizontal, com consulta
 * configurável, cards personalizáveis e setas de navegação com ícone,
 * posição e estilo ajustáveis por dispositivo e por estado.
 *
 * @package Catedral_Elements_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Icons_Manager;

class CEE_Posts_Carousel_Widget extends Widget_Base {

	public function get_name() {
		return 'cee_posts_carousel';
	}

	public function get_title() {
		return __( 'Posts em Carrossel', 'catedral-elements' );
	}

	public function get_icon() {
		return 'eicon-post-list';
	}

	public function get_categories() {
		return array( 'catedral-elements' );
	}

	public function get_keywords() {
		return array( 'post', 'posts', 'blog', 'carrossel', 'carousel', 'cpt', 'catedral', 'loop' );
	}

	public function get_style_depends() {
		return array( 'cee-posts-carousel' );
	}

	public function get_script_depends() {
		return array( 'cee-posts-carousel' );
	}

	/* =====================================================================
	 * CONTROLES
	 * ===================================================================== */

	protected function register_controls() {
		// CONTENT.
		$this->section_query();
		$this->section_card();
		$this->section_layout();
		$this->section_link();

		// STYLE.
		$this->style_card();
		$this->style_image();
		$this->style_title();
		$this->style_meta();
		$this->style_excerpt();
		$this->style_button();
		$this->style_arrows();
		$this->style_dots();
	}

	/* =====================================================================
	 * HELPERS DE OPÇÕES
	 * ===================================================================== */

	/**
	 * Tipos de post públicos registrados.
	 *
	 * @return array
	 */
	protected function get_post_type_options() {
		$options = array();
		$types   = get_post_types( array( 'public' => true ), 'objects' );

		if ( is_array( $types ) ) {
			foreach ( $types as $slug => $object ) {
				if ( 'attachment' === $slug ) {
					continue;
				}
				$label            = isset( $object->labels->singular_name ) && $object->labels->singular_name ? $object->labels->singular_name : $slug;
				$options[ $slug ] = $label;
			}
		}

		if ( empty( $options ) ) {
			$options['post'] = __( 'Post', 'catedral-elements' );
		}

		return $options;
	}

	/**
	 * Taxonomias públicas registradas (com opção "Nenhuma").
	 *
	 * @return array
	 */
	protected function get_taxonomy_options() {
		$options = array( '' => __( 'Nenhuma (todos os posts)', 'catedral-elements' ) );
		$taxes   = get_taxonomies( array( 'public' => true ), 'objects' );

		if ( is_array( $taxes ) ) {
			foreach ( $taxes as $slug => $object ) {
				$label            = isset( $object->labels->singular_name ) && $object->labels->singular_name ? $object->labels->singular_name : $slug;
				$options[ $slug ] = $label . ' (' . $slug . ')';
			}
		}

		return $options;
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
	 * CONTENT · Consulta
	 * ------------------------------------------------------------------- */

	protected function section_query() {
		$this->start_controls_section( 'sec_query', array(
			'label' => __( 'Consulta', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'post_type', array(
			'label'       => __( 'Tipo de conteúdo', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'post',
			'options'     => $this->get_post_type_options(),
			'description' => __( 'Escolha o tipo de post a listar. Tipos personalizados (CPT) públicos aparecem automaticamente nesta lista.', 'catedral-elements' ),
		) );

		$this->add_control( 'taxonomy', array(
			'label'       => __( 'Taxonomia (filtro)', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => '',
			'options'     => $this->get_taxonomy_options(),
			'description' => __( 'Selecione a taxonomia usada para filtrar os posts (ex.: Categoria, Tag ou uma taxonomia do seu CPT). Deixe em "Nenhuma" para não filtrar.', 'catedral-elements' ),
		) );

		$this->add_control( 'terms', array(
			'label'       => __( 'Termos (slugs separados por vírgula)', 'catedral-elements' ),
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'label_block' => true,
			'placeholder' => 'noticias, eventos',
			'description' => __( 'Informe os slugs dos termos separados por vírgula — ex.: "noticias, eventos". O slug é a versão do nome usada na URL (sem acentos e com hifens). Vazio = todos os termos da taxonomia.', 'catedral-elements' ),
			'condition'   => array( 'taxonomy!' => '' ),
		) );

		$this->add_control( 'posts_per_page', array(
			'label'       => __( 'Quantidade de posts', 'catedral-elements' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 8,
			'min'         => 1,
			'max'         => 24,
			'step'        => 1,
			'description' => __( 'Total de posts carregados no carrossel (não confundir com quantos aparecem por vez na tela).', 'catedral-elements' ),
		) );

		$this->add_control( 'orderby', array(
			'label'   => __( 'Ordenar por', 'catedral-elements' ),
			'type'    => Controls_Manager::SELECT,
			'default' => 'date',
			'options' => array(
				'date'          => __( 'Data de publicação', 'catedral-elements' ),
				'title'         => __( 'Título', 'catedral-elements' ),
				'menu_order'    => __( 'Ordem manual (menu order)', 'catedral-elements' ),
				'rand'          => __( 'Aleatório', 'catedral-elements' ),
				'comment_count' => __( 'Número de comentários', 'catedral-elements' ),
			),
		) );

		$this->add_control( 'order', array(
			'label'     => __( 'Direção', 'catedral-elements' ),
			'type'      => Controls_Manager::SELECT,
			'default'   => 'DESC',
			'options'   => array(
				'DESC' => __( 'Decrescente (mais recentes primeiro)', 'catedral-elements' ),
				'ASC'  => __( 'Crescente (mais antigos primeiro)', 'catedral-elements' ),
			),
			'condition' => array( 'orderby!' => 'rand' ),
		) );

		$this->add_control( 'offset', array(
			'label'       => __( 'Pular os primeiros (offset)', 'catedral-elements' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 0,
			'min'         => 0,
			'max'         => 100,
			'step'        => 1,
			'description' => __( 'Ignora os N primeiros resultados da consulta. Útil para não repetir posts já exibidos em outro bloco da página.', 'catedral-elements' ),
		) );

		$this->add_control( 'exclude_current', array(
			'label'        => __( 'Excluir o post atual', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
			'description'  => __( 'Quando o widget é usado dentro de um post individual, remove esse mesmo post da lista.', 'catedral-elements' ),
		) );

		$this->add_control( 'ignore_sticky', array(
			'label'        => __( 'Ignorar posts fixados', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => __( 'Ligado: posts marcados como "fixos no topo" não furam a ordenação escolhida.', 'catedral-elements' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Card / Conteúdo
	 * ------------------------------------------------------------------- */

	protected function section_card() {
		$this->start_controls_section( 'sec_card', array(
			'label' => __( 'Card / Conteúdo', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'show_image', array(
			'label'        => __( 'Mostrar imagem destacada', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'image_size', array(
			'label'       => __( 'Resolução da imagem', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'large',
			'options'     => $this->get_image_size_options(),
			'description' => __( 'Tamanho do arquivo servido para cada card. Tamanhos menores aliviam o carregamento da página.', 'catedral-elements' ),
			'condition'   => array( 'show_image' => 'yes' ),
		) );

		$this->add_control( 'show_date', array(
			'label'        => __( 'Mostrar data', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'show_author', array(
			'label'        => __( 'Mostrar autor', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
		) );

		$this->add_control( 'show_terms', array(
			'label'        => __( 'Mostrar termos da taxonomia', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
			'description'  => __( 'Exibe os termos do post na taxonomia escolhida na seção "Consulta". Sem taxonomia selecionada, usa as categorias (quando o tipo for Post).', 'catedral-elements' ),
		) );

		$this->add_control( 'show_excerpt', array(
			'label'        => __( 'Mostrar resumo', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'excerpt_length', array(
			'label'       => __( 'Tamanho do resumo (palavras)', 'catedral-elements' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 18,
			'min'         => 3,
			'max'         => 100,
			'step'        => 1,
			'description' => __( 'Número máximo de palavras exibidas no resumo de cada card.', 'catedral-elements' ),
			'condition'   => array( 'show_excerpt' => 'yes' ),
		) );

		$this->add_control( 'show_button', array(
			'label'        => __( 'Mostrar botão', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'button_text', array(
			'label'     => __( 'Texto do botão', 'catedral-elements' ),
			'type'      => Controls_Manager::TEXT,
			'default'   => __( 'Ler mais', 'catedral-elements' ),
			'dynamic'   => array( 'active' => true ),
			'condition' => array( 'show_button' => 'yes' ),
		) );

		$this->add_control( 'title_tag', array(
			'label'       => __( 'Tag do título', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'h3',
			'options'     => array(
				'h2'  => 'H2',
				'h3'  => 'H3',
				'h4'  => 'H4',
				'div' => 'div',
			),
			'description' => __( 'Escolha a tag semântica do título do card conforme a hierarquia de cabeçalhos da página.', 'catedral-elements' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Layout do Carrossel
	 * ------------------------------------------------------------------- */

	protected function section_layout() {
		$this->start_controls_section( 'sec_layout', array(
			'label' => __( 'Layout do Carrossel', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_responsive_control( 'slides_per_view', array(
			'label'          => __( 'Cards visíveis por vez', 'catedral-elements' ),
			'type'           => Controls_Manager::SLIDER,
			'size_units'     => array( '' ),
			'range'          => array( '' => array( 'min' => 1, 'max' => 6, 'step' => 1 ) ),
			'default'        => array( 'size' => 3, 'unit' => '' ),
			'tablet_default' => array( 'size' => 2, 'unit' => '' ),
			'mobile_default' => array( 'size' => 1, 'unit' => '' ),
			'description'    => __( 'Quantos cards ficam visíveis simultaneamente. Ajuste separadamente para desktop, tablet e celular usando o seletor de dispositivo ao lado do rótulo.', 'catedral-elements' ),
			'selectors'      => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-per-view: {{SIZE}};' ),
		) );

		$this->add_responsive_control( 'column_gap', array(
			'label'       => __( 'Espaço entre os cards', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => 0, 'max' => 120, 'step' => 1 ) ),
			'default'     => array( 'size' => 24, 'unit' => 'px' ),
			'description' => __( 'Distância horizontal entre um card e outro, por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-gap: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_control( 'autoplay', array(
			'label'        => __( 'Autoplay', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
		) );

		$this->add_control( 'autoplay_delay', array(
			'label'       => __( 'Intervalo do autoplay (ms)', 'catedral-elements' ),
			'type'        => Controls_Manager::NUMBER,
			'default'     => 4000,
			'min'         => 1000,
			'max'         => 20000,
			'step'        => 250,
			'description' => __( 'Tempo de espera entre um avanço e outro, em milissegundos (4000 = 4 segundos).', 'catedral-elements' ),
			'condition'   => array( 'autoplay' => 'yes' ),
		) );

		$this->add_control( 'pause_on_hover', array(
			'label'        => __( 'Pausar ao passar o mouse', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'condition'    => array( 'autoplay' => 'yes' ),
		) );

		$this->add_control( 'loop', array(
			'label'        => __( 'Loop infinito', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
			'description'  => __( 'Ligado: ao chegar no fim, volta ao início. Desligado: as setas ficam desabilitadas nos extremos.', 'catedral-elements' ),
		) );

		$this->add_control( 'speed', array(
			'label'       => __( 'Duração da transição (ms)', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'ms' ),
			'range'       => array( 'ms' => array( 'min' => 100, 'max' => 2000, 'step' => 50 ) ),
			'default'     => array( 'size' => 500, 'unit' => 'ms' ),
			'description' => __( 'Tempo da animação de deslize entre um card e outro.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-speed: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * CONTENT · Link do Card
	 * ------------------------------------------------------------------- */

	protected function section_link() {
		$this->start_controls_section( 'sec_link', array(
			'label' => __( 'Link do Card', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_CONTENT,
		) );

		$this->add_control( 'card_link_mode', array(
			'label'       => __( 'Onde fica o link para o post', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'card',
			'options'     => array(
				'none'   => __( 'Sem link no card', 'catedral-elements' ),
				'card'   => __( 'Card inteiro clicável', 'catedral-elements' ),
				'button' => __( 'Somente título e botão', 'catedral-elements' ),
			),
			'description' => __( 'O link sempre aponta para o próprio post. "Card inteiro clicável" envolve todo o card num link — nesse modo o botão vira um rótulo visual, para não aninhar links.', 'catedral-elements' ),
		) );

		$this->add_control( 'link_target_blank', array(
			'label'        => __( 'Abrir em nova aba', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
			'condition'    => array( 'card_link_mode!' => 'none' ),
		) );

		$this->add_control( 'link_nofollow', array(
			'label'        => __( 'Adicionar nofollow', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
			'condition'    => array( 'card_link_mode!' => 'none' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Card
	 * ------------------------------------------------------------------- */

	protected function style_card() {
		$this->start_controls_section( 'style_card', array(
			'label' => __( 'Card', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_responsive_control( 'card_padding', array(
			'label'      => __( 'Espaçamento interno', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em', '%' ),
			'default'    => array( 'top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0, 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'card_radius', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'default'    => array( 'top' => 8, 'right' => 8, 'bottom' => 8, 'left' => 8, 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'card_content_gap', array(
			'label'       => __( 'Espaço entre os elementos do card', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px', 'em' ),
			'range'       => array(
				'px' => array( 'min' => 0, 'max' => 60 ),
				'em' => array( 'min' => 0, 'max' => 5 ),
			),
			'default'     => array( 'size' => 12, 'unit' => 'px' ),
			'description' => __( 'Distância vertical entre imagem, meta, título, resumo e botão dentro do card.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-card-gap: {{SIZE}}{{UNIT}};' ),
		) );

		$this->start_controls_tabs( 'card_state_tabs' );

		/* Normal. */
		$this->start_controls_tab( 'card_tab_normal', array(
			'label' => __( 'Normal', 'catedral-elements' ),
		) );

		$this->add_control( 'card_bg', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-pc__card' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'card_border',
			'selector' => '{{WRAPPER}} .cee-pc__card',
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'     => 'card_box_shadow',
			'selector' => '{{WRAPPER}} .cee-pc__card',
		) );

		$this->end_controls_tab();

		/* Hover. */
		$this->start_controls_tab( 'card_tab_hover', array(
			'label' => __( 'Hover', 'catedral-elements' ),
		) );

		$this->add_control( 'card_bg_hover', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-pc__card:hover, {{WRAPPER}} .cee-pc__card:focus-visible' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'card_border_hover',
			'selector' => '{{WRAPPER}} .cee-pc__card:hover, {{WRAPPER}} .cee-pc__card:focus-visible',
		) );

		$this->add_group_control( Group_Control_Box_Shadow::get_type(), array(
			'name'     => 'card_box_shadow_hover',
			'selector' => '{{WRAPPER}} .cee-pc__card:hover, {{WRAPPER}} .cee-pc__card:focus-visible',
		) );

		$this->add_control( 'card_translate_hover', array(
			'label'       => __( 'Elevação no hover', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => -40, 'max' => 40, 'step' => 1 ) ),
			'default'     => array( 'size' => -6, 'unit' => 'px' ),
			'description' => __( 'Deslocamento vertical do card ao passar o mouse. Valores negativos sobem o card.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-pc__card:hover, {{WRAPPER}} .cee-pc__card:focus-visible' => 'transform: translateY({{SIZE}}{{UNIT}});' ),
		) );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Imagem
	 * ------------------------------------------------------------------- */

	protected function style_image() {
		$this->start_controls_section( 'style_image', array(
			'label'     => __( 'Imagem', 'catedral-elements' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'show_image' => 'yes' ),
		) );

		$this->add_responsive_control( 'image_ratio', array(
			'label'                => __( 'Proporção da imagem', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => '4:3',
			'options'              => array(
				'natural' => __( 'Natural (proporção do arquivo)', 'catedral-elements' ),
				'1:1'     => '1:1',
				'4:3'     => '4:3',
				'3:2'     => '3:2',
				'16:9'    => '16:9',
				'9:16'    => '9:16 (vertical)',
			),
			'description'          => __( 'Trava a altura da imagem numa proporção fixa, por dispositivo. "Natural" respeita as dimensões originais do arquivo.', 'catedral-elements' ),
			'selectors_dictionary' => array(
				'natural' => 'auto',
				'1:1'     => '1 / 1',
				'4:3'     => '4 / 3',
				'3:2'     => '3 / 2',
				'16:9'    => '16 / 9',
				'9:16'    => '9 / 16',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-ratio: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'image_fit', array(
			'label'       => __( 'Ajuste da imagem', 'catedral-elements' ),
			'type'        => Controls_Manager::SELECT,
			'default'     => 'cover',
			'options'     => array(
				'cover'   => __( 'Cobertura (corta o excedente)', 'catedral-elements' ),
				'contain' => __( 'Conter (mostra a imagem inteira)', 'catedral-elements' ),
			),
			'description' => __( 'Como a imagem preenche a área da proporção escolhida.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-fit: {{VALUE}};' ),
		) );

		$this->add_responsive_control( 'image_radius', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__media' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Título
	 * ------------------------------------------------------------------- */

	protected function style_title() {
		$this->start_controls_section( 'style_title', array(
			'label' => __( 'Título', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'title_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111111',
			'selectors' => array(
				'{{WRAPPER}} .cee-pc__title'   => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__title a' => 'color: {{VALUE}};',
			),
		) );

		$this->add_control( 'title_color_hover', array(
			'label'       => __( 'Cor no hover', 'catedral-elements' ),
			'type'        => Controls_Manager::COLOR,
			'default'     => '',
			'description' => __( 'Aplicada ao passar o mouse sobre o card (ou sobre o próprio título, quando ele é o link).', 'catedral-elements' ),
			'selectors'   => array(
				'{{WRAPPER}} .cee-pc__card:hover .cee-pc__title' => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__title a:hover'             => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__title a:focus-visible'     => 'color: {{VALUE}};',
			),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .cee-pc__title',
		) );

		$this->add_responsive_control( 'title_margin_bottom', array(
			'label'      => __( 'Distância abaixo do título', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px', 'em' ),
			'range'      => array(
				'px' => array( 'min' => 0, 'max' => 60 ),
				'em' => array( 'min' => 0, 'max' => 5 ),
			),
			'default'    => array( 'size' => 0, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__title' => 'margin-bottom: {{SIZE}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Meta
	 * ------------------------------------------------------------------- */

	protected function style_meta() {
		$this->start_controls_section( 'style_meta', array(
			'label' => __( 'Meta (data, autor, termos)', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'meta_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#7A7A7A',
			'selectors' => array(
				'{{WRAPPER}} .cee-pc__meta'   => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__meta a' => 'color: {{VALUE}};',
			),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'meta_typo',
			'selector' => '{{WRAPPER}} .cee-pc__meta',
		) );

		$this->add_responsive_control( 'meta_gap', array(
			'label'       => __( 'Espaço entre os itens do meta', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px', 'em' ),
			'range'       => array(
				'px' => array( 'min' => 0, 'max' => 40 ),
				'em' => array( 'min' => 0, 'max' => 4 ),
			),
			'default'     => array( 'size' => 10, 'unit' => 'px' ),
			'description' => __( 'Distância horizontal entre data, autor e termos.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-pc__meta' => 'gap: {{SIZE}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'meta_margin', array(
			'label'      => __( 'Margem', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Resumo
	 * ------------------------------------------------------------------- */

	protected function style_excerpt() {
		$this->start_controls_section( 'style_excerpt', array(
			'label'     => __( 'Resumo', 'catedral-elements' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'show_excerpt' => 'yes' ),
		) );

		$this->add_control( 'excerpt_color', array(
			'label'     => __( 'Cor', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#4A4A4A',
			'selectors' => array( '{{WRAPPER}} .cee-pc__excerpt' => 'color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'     => 'excerpt_typo',
			'selector' => '{{WRAPPER}} .cee-pc__excerpt',
		) );

		$this->add_responsive_control( 'excerpt_margin', array(
			'label'      => __( 'Margem', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__excerpt' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Botão
	 * ------------------------------------------------------------------- */

	protected function style_button() {
		$this->start_controls_section( 'style_button', array(
			'label'     => __( 'Botão', 'catedral-elements' ),
			'tab'       => Controls_Manager::TAB_STYLE,
			'condition' => array( 'show_button' => 'yes' ),
		) );

		$this->start_controls_tabs( 'button_state_tabs' );

		/* Normal. */
		$this->start_controls_tab( 'button_tab_normal', array(
			'label' => __( 'Normal', 'catedral-elements' ),
		) );

		$this->add_control( 'btn_color', array(
			'label'     => __( 'Cor do texto', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111111',
			'selectors' => array( '{{WRAPPER}} .cee-pc__btn' => 'color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_bg', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'transparent',
			'selectors' => array( '{{WRAPPER}} .cee-pc__btn' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'btn_border',
			'selector' => '{{WRAPPER}} .cee-pc__btn',
		) );

		$this->end_controls_tab();

		/* Hover. */
		$this->start_controls_tab( 'button_tab_hover', array(
			'label' => __( 'Hover', 'catedral-elements' ),
		) );

		$this->add_control( 'btn_color_hover', array(
			'label'     => __( 'Cor do texto', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-pc__btn:hover, {{WRAPPER}} .cee-pc__btn:focus-visible, {{WRAPPER}} .cee-pc__card:hover .cee-pc__btn' => 'color: {{VALUE}};' ),
		) );

		$this->add_control( 'btn_bg_hover', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-pc__btn:hover, {{WRAPPER}} .cee-pc__btn:focus-visible, {{WRAPPER}} .cee-pc__card:hover .cee-pc__btn' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'btn_border_hover',
			'selector' => '{{WRAPPER}} .cee-pc__btn:hover, {{WRAPPER}} .cee-pc__btn:focus-visible, {{WRAPPER}} .cee-pc__card:hover .cee-pc__btn',
		) );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control( Group_Control_Typography::get_type(), array(
			'name'      => 'btn_typo',
			'selector'  => '{{WRAPPER}} .cee-pc__btn',
			'separator' => 'before',
		) );

		$this->add_responsive_control( 'btn_padding', array(
			'label'      => __( 'Espaçamento interno', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', 'em' ),
			'default'    => array( 'top' => 10, 'right' => 18, 'bottom' => 10, 'left' => 18, 'unit' => 'px', 'isLinked' => false ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->add_responsive_control( 'btn_radius', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'default'    => array( 'top' => 4, 'right' => 4, 'bottom' => 4, 'left' => 4, 'unit' => 'px', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Setas de Navegação
	 * ------------------------------------------------------------------- */

	protected function style_arrows() {
		$this->start_controls_section( 'style_arrows', array(
			'label' => __( 'Setas de Navegação', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'show_arrows', array(
			'label'        => __( 'Mostrar setas', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		) );

		$this->add_control( 'arrow_prev_icon', array(
			'label'       => __( 'Ícone "anterior"', 'catedral-elements' ),
			'type'        => Controls_Manager::ICONS,
			'default'     => array(
				'value'   => 'eicon-chevron-left',
				'library' => 'eicons',
			),
			'description' => __( 'Escolha qualquer ícone da biblioteca — Elementor Icons, Font Awesome, SVG enviado ou pacotes adicionais instalados (ex.: Lucide).', 'catedral-elements' ),
			'condition'   => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_control( 'arrow_next_icon', array(
			'label'       => __( 'Ícone "próximo"', 'catedral-elements' ),
			'type'        => Controls_Manager::ICONS,
			'default'     => array(
				'value'   => 'eicon-chevron-right',
				'library' => 'eicons',
			),
			'description' => __( 'Escolha qualquer ícone da biblioteca — Elementor Icons, Font Awesome, SVG enviado ou pacotes adicionais instalados (ex.: Lucide).', 'catedral-elements' ),
			'condition'   => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_responsive_control( 'arrows_position', array(
			'label'                => __( 'Posição das setas', 'catedral-elements' ),
			'type'                 => Controls_Manager::SELECT,
			'default'              => 'inside',
			'options'              => array(
				'inside'  => __( 'Sobre os cards (dentro)', 'catedral-elements' ),
				'outside' => __( 'Nas laterais (fora dos cards)', 'catedral-elements' ),
			),
			'description'          => __( 'Definido separadamente para cada dispositivo (desktop, tablet e celular). "Dentro" sobrepõe as setas aos cards; "Fora" reserva uma faixa lateral só para elas.', 'catedral-elements' ),
			'selectors_dictionary' => array(
				'inside'  => '--cee-pc-arrow-gutter: 0px; --cee-pc-arrow-inset: var(--cee-pc-arrow-x, 12px);',
				'outside' => '--cee-pc-arrow-gutter: calc(var(--cee-pc-arrow-size, 44px) + var(--cee-pc-arrow-x, 12px)); --cee-pc-arrow-inset: 0px;',
			),
			'selectors'            => array( '{{WRAPPER}} .cee-posts-carousel' => '{{VALUE}}' ),
			'condition'            => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_responsive_control( 'arrow_offset_h', array(
			'label'       => __( 'Deslocamento horizontal', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => -100, 'max' => 100, 'step' => 1 ) ),
			'default'     => array( 'size' => 12, 'unit' => 'px' ),
			'description' => __( 'Distância das setas até a borda lateral, por dispositivo. No modo "Dentro", valores maiores afastam a seta da borda; no modo "Fora", aumentam o respiro entre a seta e os cards.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-arrow-x: {{SIZE}}{{UNIT}};' ),
			'condition'   => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_responsive_control( 'arrow_offset_v', array(
			'label'       => __( 'Deslocamento vertical', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => -100, 'max' => 100, 'step' => 1 ) ),
			'default'     => array( 'size' => 0, 'unit' => 'px' ),
			'description' => __( 'Sobe (valores negativos) ou desce (valores positivos) as setas em relação ao centro vertical, por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-arrow-y: {{SIZE}}{{UNIT}};' ),
			'condition'   => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_responsive_control( 'arrow_size', array(
			'label'       => __( 'Tamanho do botão', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => 24, 'max' => 120, 'step' => 1 ) ),
			'default'     => array( 'size' => 44, 'unit' => 'px' ),
			'description' => __( 'Diâmetro da área clicável da seta, por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-arrow-size: {{SIZE}}{{UNIT}};' ),
			'condition'   => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_responsive_control( 'arrow_icon_size', array(
			'label'       => __( 'Tamanho do ícone', 'catedral-elements' ),
			'type'        => Controls_Manager::SLIDER,
			'size_units'  => array( 'px' ),
			'range'       => array( 'px' => array( 'min' => 8, 'max' => 64, 'step' => 1 ) ),
			'default'     => array( 'size' => 18, 'unit' => 'px' ),
			'description' => __( 'Tamanho do desenho do ícone dentro do botão, por dispositivo.', 'catedral-elements' ),
			'selectors'   => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-arrow-icon-size: {{SIZE}}{{UNIT}};' ),
			'condition'   => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_control( 'arrow_states_heading', array(
			'label'     => __( 'Cores e borda por estado', 'catedral-elements' ),
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
			'condition' => array( 'show_arrows' => 'yes' ),
		) );

		$this->add_control( 'arrow_states_hint', array(
			'type'            => Controls_Manager::RAW_HTML,
			'raw'             => __( 'Cada aba estiliza um estado diferente da seta: <strong>Normal</strong> (parada), <strong>Hover</strong> (mouse em cima ou foco pelo teclado) e <strong>Ativo</strong> (no instante do clique).', 'catedral-elements' ),
			'content_classes' => 'elementor-descriptor',
			'condition'       => array( 'show_arrows' => 'yes' ),
		) );

		$this->start_controls_tabs( 'arrow_state_tabs', array(
			'condition' => array( 'show_arrows' => 'yes' ),
		) );

		/* Normal. */
		$this->start_controls_tab( 'arrow_tab_normal', array(
			'label' => __( 'Normal', 'catedral-elements' ),
		) );

		$this->add_control( 'arrow_color', array(
			'label'     => __( 'Cor do ícone', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111111',
			'selectors' => array(
				'{{WRAPPER}} .cee-pc__arrow'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__arrow svg' => 'fill: {{VALUE}};',
			),
		) );

		$this->add_control( 'arrow_bg', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => array( '{{WRAPPER}} .cee-pc__arrow' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'arrow_border',
			'selector' => '{{WRAPPER}} .cee-pc__arrow',
		) );

		$this->add_responsive_control( 'arrow_radius', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'default'    => array( 'top' => 50, 'right' => 50, 'bottom' => 50, 'left' => 50, 'unit' => '%', 'isLinked' => true ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__arrow' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_tab();

		/* Hover. */
		$this->start_controls_tab( 'arrow_tab_hover', array(
			'label' => __( 'Hover', 'catedral-elements' ),
		) );

		$this->add_control( 'arrow_color_hover', array(
			'label'     => __( 'Cor do ícone', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array(
				'{{WRAPPER}} .cee-pc__arrow:hover, {{WRAPPER}} .cee-pc__arrow:focus-visible'         => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__arrow:hover svg, {{WRAPPER}} .cee-pc__arrow:focus-visible svg' => 'fill: {{VALUE}};',
			),
		) );

		$this->add_control( 'arrow_bg_hover', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-pc__arrow:hover, {{WRAPPER}} .cee-pc__arrow:focus-visible' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'arrow_border_hover',
			'selector' => '{{WRAPPER}} .cee-pc__arrow:hover, {{WRAPPER}} .cee-pc__arrow:focus-visible',
		) );

		$this->add_responsive_control( 'arrow_radius_hover', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__arrow:hover, {{WRAPPER}} .cee-pc__arrow:focus-visible' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_tab();

		/* Ativo (:active — no instante do clique). */
		$this->start_controls_tab( 'arrow_tab_active', array(
			'label' => __( 'Ativo', 'catedral-elements' ),
		) );

		$this->add_control( 'arrow_color_active', array(
			'label'     => __( 'Cor do ícone', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array(
				'{{WRAPPER}} .cee-pc__arrow:active'     => 'color: {{VALUE}};',
				'{{WRAPPER}} .cee-pc__arrow:active svg' => 'fill: {{VALUE}};',
			),
		) );

		$this->add_control( 'arrow_bg_active', array(
			'label'     => __( 'Cor de fundo', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'selectors' => array( '{{WRAPPER}} .cee-pc__arrow:active' => 'background-color: {{VALUE}};' ),
		) );

		$this->add_group_control( Group_Control_Border::get_type(), array(
			'name'     => 'arrow_border_active',
			'selector' => '{{WRAPPER}} .cee-pc__arrow:active',
		) );

		$this->add_responsive_control( 'arrow_radius_active', array(
			'label'      => __( 'Raio da borda', 'catedral-elements' ),
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => array( 'px', '%' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__arrow:active' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
		) );

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	/* ---------------------------------------------------------------------
	 * STYLE · Paginação (dots)
	 * ------------------------------------------------------------------- */

	protected function style_dots() {
		$this->start_controls_section( 'style_dots', array(
			'label' => __( 'Paginação (bolinhas)', 'catedral-elements' ),
			'tab'   => Controls_Manager::TAB_STYLE,
		) );

		$this->add_control( 'show_dots', array(
			'label'        => __( 'Mostrar bolinhas', 'catedral-elements' ),
			'type'         => Controls_Manager::SWITCHER,
			'label_on'     => __( 'Sim', 'catedral-elements' ),
			'label_off'    => __( 'Não', 'catedral-elements' ),
			'return_value' => 'yes',
			'default'      => '',
		) );

		$this->add_control( 'dot_color', array(
			'label'     => __( 'Cor (inativa)', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => 'rgba(17,17,17,0.25)',
			'selectors' => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-dot-color: {{VALUE}};' ),
			'condition' => array( 'show_dots' => 'yes' ),
		) );

		$this->add_control( 'dot_color_active', array(
			'label'     => __( 'Cor (ativa)', 'catedral-elements' ),
			'type'      => Controls_Manager::COLOR,
			'default'   => '#111111',
			'selectors' => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-dot-color-active: {{VALUE}};' ),
			'condition' => array( 'show_dots' => 'yes' ),
		) );

		$this->add_responsive_control( 'dot_size', array(
			'label'      => __( 'Tamanho', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 4, 'max' => 30, 'step' => 1 ) ),
			'default'    => array( 'size' => 8, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-posts-carousel' => '--cee-pc-dot-size: {{SIZE}}{{UNIT}};' ),
			'condition'  => array( 'show_dots' => 'yes' ),
		) );

		$this->add_responsive_control( 'dots_margin_top', array(
			'label'      => __( 'Distância acima das bolinhas', 'catedral-elements' ),
			'type'       => Controls_Manager::SLIDER,
			'size_units' => array( 'px' ),
			'range'      => array( 'px' => array( 'min' => 0, 'max' => 100, 'step' => 1 ) ),
			'default'    => array( 'size' => 24, 'unit' => 'px' ),
			'selectors'  => array( '{{WRAPPER}} .cee-pc__dots' => 'margin-top: {{SIZE}}{{UNIT}};' ),
			'condition'  => array( 'show_dots' => 'yes' ),
		) );

		$this->end_controls_section();
	}

	/* =====================================================================
	 * RENDER
	 * ===================================================================== */

	/**
	 * Monta os argumentos da WP_Query a partir das configurações.
	 *
	 * @param array $settings Configurações do widget.
	 * @return array
	 */
	protected function build_query_args( $settings ) {
		$post_type = ! empty( $settings['post_type'] ) ? sanitize_key( $settings['post_type'] ) : 'post';
		$per_page  = isset( $settings['posts_per_page'] ) ? (int) $settings['posts_per_page'] : 8;
		$per_page  = max( 1, min( 24, $per_page ) );
		$offset    = isset( $settings['offset'] ) ? max( 0, (int) $settings['offset'] ) : 0;

		$allowed_orderby = array( 'date', 'title', 'menu_order', 'rand', 'comment_count' );
		$orderby         = ( ! empty( $settings['orderby'] ) && in_array( $settings['orderby'], $allowed_orderby, true ) ) ? $settings['orderby'] : 'date';
		$order           = ( ! empty( $settings['order'] ) && 'ASC' === strtoupper( $settings['order'] ) ) ? 'ASC' : 'DESC';

		$args = array(
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => $per_page,
			'orderby'             => $orderby,
			'order'               => $order,
			'ignore_sticky_posts' => ( 'yes' === ( isset( $settings['ignore_sticky'] ) ? $settings['ignore_sticky'] : 'yes' ) ),
			'no_found_rows'       => true,
		);

		if ( $offset > 0 ) {
			$args['offset'] = $offset;
		}

		$taxonomy = ! empty( $settings['taxonomy'] ) ? sanitize_key( $settings['taxonomy'] ) : '';
		$terms    = ! empty( $settings['terms'] ) ? (string) $settings['terms'] : '';

		if ( $taxonomy && '' !== trim( $terms ) && taxonomy_exists( $taxonomy ) ) {
			$slugs = array_map( 'sanitize_title', array_map( 'trim', explode( ',', $terms ) ) );
			$slugs = array_values( array_filter( $slugs ) );

			if ( ! empty( $slugs ) ) {
				$args['tax_query'] = array(
					array(
						'taxonomy' => $taxonomy,
						'field'    => 'slug',
						'terms'    => $slugs,
					),
				);
			}
		}

		$exclude_current = isset( $settings['exclude_current'] ) ? $settings['exclude_current'] : '';
		if ( 'yes' === $exclude_current && function_exists( 'is_singular' ) && is_singular() ) {
			$current_id = get_the_ID();
			if ( $current_id ) {
				$args['post__not_in'] = array( (int) $current_id );
			}
		}

		return $args;
	}

	/**
	 * Taxonomia usada para exibir os termos no meta do card.
	 *
	 * @param array $settings Configurações do widget.
	 * @return string Slug da taxonomia, ou string vazia quando não há.
	 */
	protected function meta_taxonomy( $settings ) {
		if ( ! empty( $settings['taxonomy'] ) ) {
			return sanitize_key( $settings['taxonomy'] );
		}

		$post_type = ! empty( $settings['post_type'] ) ? sanitize_key( $settings['post_type'] ) : 'post';
		return ( 'post' === $post_type ) ? 'category' : '';
	}

	/**
	 * Lê um switcher com valor padrão (compatível com configurações antigas).
	 *
	 * @param array  $settings Configurações do widget.
	 * @param string $key      Chave do control.
	 * @param string $fallback Valor assumido quando a chave não existe.
	 * @return bool
	 */
	protected function is_on( $settings, $key, $fallback = '' ) {
		$value = isset( $settings[ $key ] ) ? $settings[ $key ] : $fallback;
		return 'yes' === $value;
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$query = new \WP_Query( $this->build_query_args( $settings ) );

		if ( ! $query->have_posts() ) {
			wp_reset_postdata();
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="cee-posts-carousel cee-posts-carousel--empty">' . esc_html__( 'Nenhum post encontrado para esta consulta.', 'catedral-elements' ) . '</div>';
			}
			return;
		}

		$autoplay    = $this->is_on( $settings, 'autoplay', '' );
		$delay       = isset( $settings['autoplay_delay'] ) ? max( 1000, (int) $settings['autoplay_delay'] ) : 4000;
		$loop        = $this->is_on( $settings, 'loop', 'yes' );
		$pause_hover = $this->is_on( $settings, 'pause_on_hover', 'yes' );
		$speed       = isset( $settings['speed']['size'] ) ? max( 0, (int) $settings['speed']['size'] ) : 500;
		$show_arrows = $this->is_on( $settings, 'show_arrows', 'yes' );
		$show_dots   = $this->is_on( $settings, 'show_dots', '' );

		$link_modes = array( 'none', 'card', 'button' );
		$link_mode  = ( ! empty( $settings['card_link_mode'] ) && in_array( $settings['card_link_mode'], $link_modes, true ) ) ? $settings['card_link_mode'] : 'card';

		$count = (int) $query->post_count;
		?>
		<div class="cee-posts-carousel"
			data-autoplay="<?php echo esc_attr( $autoplay ? 'true' : 'false' ); ?>"
			data-autoplay-delay="<?php echo esc_attr( $delay ); ?>"
			data-loop="<?php echo esc_attr( $loop ? 'true' : 'false' ); ?>"
			data-speed="<?php echo esc_attr( $speed ); ?>"
			data-pause-hover="<?php echo esc_attr( $pause_hover ? 'true' : 'false' ); ?>"
			data-count="<?php echo esc_attr( $count ); ?>">

			<div class="cee-pc__viewport">
				<div class="cee-pc__track">
					<?php
					$index = 0;
					while ( $query->have_posts() ) {
						$query->the_post();
						$this->render_card( $settings, $link_mode, $index );
						$index++;
					}
					?>
				</div>
			</div>

			<?php if ( $show_arrows ) : ?>
				<button type="button" class="cee-pc__arrow cee-pc__arrow--prev"
					aria-label="<?php echo esc_attr__( 'Anterior', 'catedral-elements' ); ?>">
					<?php
					if ( ! empty( $settings['arrow_prev_icon'] ) ) {
						Icons_Manager::render_icon( $settings['arrow_prev_icon'], array( 'aria-hidden' => 'true' ) );
					}
					?>
				</button>
				<button type="button" class="cee-pc__arrow cee-pc__arrow--next"
					aria-label="<?php echo esc_attr__( 'Próximo', 'catedral-elements' ); ?>">
					<?php
					if ( ! empty( $settings['arrow_next_icon'] ) ) {
						Icons_Manager::render_icon( $settings['arrow_next_icon'], array( 'aria-hidden' => 'true' ) );
					}
					?>
				</button>
			<?php endif; ?>

			<?php if ( $show_dots && $count > 1 ) : ?>
				<div class="cee-pc__dots" role="group" aria-label="<?php echo esc_attr__( 'Paginação do carrossel', 'catedral-elements' ); ?>">
					<?php for ( $i = 0; $i < $count; $i++ ) : ?>
						<button type="button"
							class="cee-pc__dot<?php echo 0 === $i ? ' cee-pc__dot--active' : ''; ?>"
							data-index="<?php echo esc_attr( $i ); ?>"
							aria-label="<?php echo esc_attr( sprintf( /* translators: %d: número da página do carrossel */ __( 'Ir para a página %d', 'catedral-elements' ), $i + 1 ) ); ?>"></button>
					<?php endfor; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
		wp_reset_postdata();
	}

	/**
	 * Renderiza um card do carrossel (dentro do loop da WP_Query).
	 *
	 * @param array  $settings  Configurações do widget.
	 * @param string $link_mode Modo de link: 'none', 'card' ou 'button'.
	 * @param int    $index     Índice do card no carrossel.
	 */
	protected function render_card( $settings, $link_mode, $index ) {
		$post_id   = get_the_ID();
		$permalink = get_permalink( $post_id );

		$show_image   = $this->is_on( $settings, 'show_image', 'yes' );
		$show_date    = $this->is_on( $settings, 'show_date', 'yes' );
		$show_author  = $this->is_on( $settings, 'show_author', '' );
		$show_terms   = $this->is_on( $settings, 'show_terms', '' );
		$show_excerpt = $this->is_on( $settings, 'show_excerpt', 'yes' );
		$show_button  = $this->is_on( $settings, 'show_button', 'yes' );

		$image_size    = ! empty( $settings['image_size'] ) ? $settings['image_size'] : 'large';
		$excerpt_words = isset( $settings['excerpt_length'] ) ? max( 3, (int) $settings['excerpt_length'] ) : 18;
		$button_text   = ( isset( $settings['button_text'] ) && '' !== $settings['button_text'] ) ? $settings['button_text'] : __( 'Ler mais', 'catedral-elements' );

		$allowed_tags = array( 'h2', 'h3', 'h4', 'div' );
		$title_tag    = ( ! empty( $settings['title_tag'] ) && in_array( $settings['title_tag'], $allowed_tags, true ) ) ? $settings['title_tag'] : 'h3';

		// Atributos target/rel compartilhados por todos os links do card.
		$link_attrs = '';
		if ( 'none' !== $link_mode ) {
			$rel = array();
			if ( $this->is_on( $settings, 'link_target_blank', '' ) ) {
				$link_attrs .= ' target="_blank"';
				$rel[]       = 'noopener';
				$rel[]       = 'noreferrer';
			}
			if ( $this->is_on( $settings, 'link_nofollow', '' ) ) {
				$rel[] = 'nofollow';
			}
			if ( ! empty( $rel ) ) {
				$link_attrs .= ' rel="' . esc_attr( implode( ' ', array_unique( $rel ) ) ) . '"';
			}
		}

		$is_card_link = ( 'card' === $link_mode );
		$card_tag     = $is_card_link ? 'a' : 'div';

		echo '<' . esc_html( $card_tag ) . ' class="cee-pc__card" data-index="' . esc_attr( $index ) . '"';
		if ( $is_card_link ) {
			echo ' href="' . esc_url( $permalink ) . '"';
			echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- valores escapados com esc_attr acima.
		}
		echo '>';

		// Imagem destacada.
		if ( $show_image && has_post_thumbnail( $post_id ) ) {
			echo '<div class="cee-pc__media">';
			echo get_the_post_thumbnail( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup gerado e escapado pelo WordPress.
				$post_id,
				$image_size,
				array(
					'class'   => 'cee-pc__img',
					'loading' => 'lazy',
					'alt'     => esc_attr( wp_strip_all_tags( get_the_title( $post_id ) ) ),
				)
			);
			echo '</div>';
		}

		echo '<div class="cee-pc__body">';

		// Meta: data, autor e termos (cada um sob o seu toggle).
		if ( $show_date || $show_author || $show_terms ) {
			$meta_items = array();

			if ( $show_date ) {
				$meta_items[] = '<span class="cee-pc__meta-item cee-pc__meta-date">' . esc_html( get_the_date( '', $post_id ) ) . '</span>';
			}

			if ( $show_author ) {
				$meta_items[] = '<span class="cee-pc__meta-item cee-pc__meta-author">' . esc_html( get_the_author() ) . '</span>';
			}

			if ( $show_terms ) {
				$tax = $this->meta_taxonomy( $settings );
				if ( $tax && taxonomy_exists( $tax ) ) {
					$term_list = get_the_term_list( $post_id, $tax, '', ', ', '' );
					if ( ! is_wp_error( $term_list ) && ! empty( $term_list ) ) {
						$meta_items[] = '<span class="cee-pc__meta-item cee-pc__meta-terms">' . wp_kses_post( $term_list ) . '</span>';
					}
				}
			}

			if ( ! empty( $meta_items ) ) {
				echo '<div class="cee-pc__meta">' . implode( '', $meta_items ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cada item escapado individualmente acima.
			}
		}

		// Título — vira link apenas no modo "Somente título e botão".
		echo '<' . esc_html( $title_tag ) . ' class="cee-pc__title">';
		if ( 'button' === $link_mode ) {
			echo '<a href="' . esc_url( $permalink ) . '"' . $link_attrs . '>' . esc_html( get_the_title( $post_id ) ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- valores escapados individualmente acima.
		} else {
			echo esc_html( get_the_title( $post_id ) );
		}
		echo '</' . esc_html( $title_tag ) . '>';

		// Resumo.
		if ( $show_excerpt ) {
			$raw = (string) get_the_excerpt( $post_id );
			if ( '' !== trim( $raw ) ) {
				echo '<div class="cee-pc__excerpt">' . esc_html( wp_trim_words( wp_strip_all_tags( $raw ), $excerpt_words, '…' ) ) . '</div>';
			}
		}

		// Botão — <span> dentro de card clicável, para nunca aninhar <a>.
		if ( $show_button ) {
			if ( $is_card_link ) {
				echo '<span class="cee-pc__btn">' . esc_html( $button_text ) . '</span>';
			} elseif ( 'button' === $link_mode ) {
				echo '<a class="cee-pc__btn" href="' . esc_url( $permalink ) . '"' . $link_attrs . '>' . esc_html( $button_text ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- valores escapados individualmente acima.
			} else {
				echo '<span class="cee-pc__btn">' . esc_html( $button_text ) . '</span>';
			}
		}

		echo '</div>'; // .cee-pc__body

		echo '</' . esc_html( $card_tag ) . '>';
	}
}
