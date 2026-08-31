<?php
/**
 * "Linkar bloco inteiro": torna clicável, por completo, um Contêiner do Elementor.
 *
 * Injeta controles ("Linkar contêiner inteiro" + URL) na seção de Layout dos
 * Contêineres e marca o wrapper com a classe/atributos que o script
 * assets/js/cee-link-block.js usa para navegar ao clicar em qualquer área
 * "vazia" do bloco — respeitando links, botões e campos internos.
 *
 * O mesmo mecanismo (.cee-linked-block[data-cee-link]) é reaproveitado pelos
 * widgets do plugin (slide clicável, hotspots).
 *
 * @package Catedral_Elements_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Elementor\Controls_Manager;

class CEE_Link_Block {

	/**
	 * Handle do script compartilhado.
	 */
	const HANDLE = 'cee-link-block';

	/**
	 * Inicializa os hooks.
	 */
	public static function init() {
		// Controles no Contêiner (logo após a seção de Layout).
		add_action( 'elementor/element/container/section_layout/after_section_end', array( __CLASS__, 'register_container_controls' ), 10, 2 );

		// Aplica a marcação ao renderizar o Contêiner (hook genérico, filtrando pelo tipo).
		add_action( 'elementor/frontend/before_render', array( __CLASS__, 'before_container_render' ), 10, 1 );
	}

	/**
	 * Adiciona a seção de controles de link ao Contêiner.
	 *
	 * @param \Elementor\Element_Base $element Elemento (Contêiner).
	 */
	public static function register_container_controls( $element ) {
		$element->start_controls_section(
			'cee_link_block_section',
			array(
				'label' => __( 'Link do bloco (Catedral)', 'catedral-elements' ),
				'tab'   => Controls_Manager::TAB_LAYOUT,
			)
		);

		$element->add_control(
			'cee_container_link_enable',
			array(
				'label'        => __( 'Linkar contêiner inteiro', 'catedral-elements' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => __( 'Sim', 'catedral-elements' ),
				'label_off'    => __( 'Não', 'catedral-elements' ),
				'return_value' => 'yes',
				'default'      => '',
				'description'  => __( 'Torna todo o contêiner clicável. Links, botões e campos internos continuam funcionando normalmente; o restante da área leva ao link definido abaixo.', 'catedral-elements' ),
			)
		);

		$element->add_control(
			'cee_container_link',
			array(
				'label'         => __( 'Link', 'catedral-elements' ),
				'type'          => Controls_Manager::URL,
				'dynamic'       => array( 'active' => true ),
				'placeholder'   => 'https://',
				'show_external' => true,
				'condition'     => array( 'cee_container_link_enable' => 'yes' ),
			)
		);

		$element->end_controls_section();
	}

	/**
	 * Marca o wrapper do Contêiner com os atributos de link.
	 *
	 * @param \Elementor\Element_Base $element Elemento (Contêiner).
	 */
	public static function before_container_render( $element ) {
		if ( ! is_object( $element ) || ! method_exists( $element, 'get_name' ) || 'container' !== $element->get_name() ) {
			return;
		}

		$settings = $element->get_settings_for_display();

		if ( empty( $settings['cee_container_link_enable'] ) || 'yes' !== $settings['cee_container_link_enable'] ) {
			return;
		}

		$link = isset( $settings['cee_container_link'] ) ? $settings['cee_container_link'] : array();
		$url  = isset( $link['url'] ) ? trim( $link['url'] ) : '';

		if ( '' === $url ) {
			return;
		}

		self::apply_link_attributes( $element, '_wrapper', $link );

		// Garante script e estilo (registrados no arquivo principal do plugin).
		if ( wp_script_is( self::HANDLE, 'registered' ) ) {
			wp_enqueue_script( self::HANDLE );
		}
		if ( wp_style_is( self::HANDLE, 'registered' ) ) {
			wp_enqueue_style( self::HANDLE );
		}
	}

	/**
	 * Aplica os atributos padronizados de "bloco linkado" a um render_attribute.
	 *
	 * @param \Elementor\Element_Base $element Elemento.
	 * @param string                  $key     Chave do render attribute (ex.: '_wrapper').
	 * @param array                   $link    Array de link do Elementor (url/is_external/nofollow).
	 */
	public static function apply_link_attributes( $element, $key, $link ) {
		$url = isset( $link['url'] ) ? $link['url'] : '';

		$attrs = array(
			'class'         => 'cee-linked-block',
			'data-cee-link' => esc_url( $url ),
		);

		if ( ! empty( $link['is_external'] ) ) {
			$attrs['data-cee-link-target'] = '_blank';
		}

		$rel = array();
		if ( ! empty( $link['is_external'] ) ) {
			$rel[] = 'noopener';
			$rel[] = 'noreferrer';
		}
		if ( ! empty( $link['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}
		if ( ! empty( $rel ) ) {
			$attrs['data-cee-link-rel'] = implode( ' ', $rel );
		}

		$element->add_render_attribute( $key, $attrs );
	}
}
