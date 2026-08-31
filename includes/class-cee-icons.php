<?php
/**
 * Integração da biblioteca de ícones Lucide ao seletor de ícones do Elementor.
 *
 * Registra a fonte "lucide" (empacotada em assets/lucide/) como uma aba adicional
 * no gerenciador de ícones do Elementor, deixando os ~2.000 ícones Lucide
 * disponíveis em QUALQUER controle de ícone do site (botão, ícone, lista de
 * ícones, etc.) — não apenas nos widgets deste plugin.
 *
 * @package Catedral_Elements_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CEE_Icons {

	/**
	 * Handle do estilo da fonte Lucide.
	 */
	const HANDLE = 'cee-lucide';

	/**
	 * URL do CSS da fonte.
	 *
	 * @return string
	 */
	protected static function css_url() {
		return CEE_URL . 'assets/lucide/lucide.css';
	}

	/**
	 * Inicializa os hooks. Seguro mesmo sem o Elementor ativo (os filtros do
	 * Elementor simplesmente não disparam).
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_style' ), 5 );
		add_action( 'elementor/frontend/after_register_styles', array( __CLASS__, 'register_style' ) );

		// Aba nova no seletor de ícones do Elementor.
		add_filter( 'elementor/icons_manager/additional_tabs', array( __CLASS__, 'register_tab' ) );

		// Garante a fonte no editor e no preview para o ícone já posicionado renderizar.
		add_action( 'elementor/editor/after_enqueue_styles', array( __CLASS__, 'enqueue_style' ) );
		add_action( 'elementor/preview/enqueue_styles', array( __CLASS__, 'enqueue_style' ) );
	}

	/**
	 * Registra o estilo da fonte (o Elementor o enfileira quando um ícone Lucide
	 * é efetivamente renderizado; registramos para o handle existir).
	 */
	public static function register_style() {
		if ( ! wp_style_is( self::HANDLE, 'registered' ) ) {
			wp_register_style( self::HANDLE, self::css_url(), array(), CEE_VERSION );
		}
	}

	/**
	 * Enfileira o estilo (editor/preview).
	 */
	public static function enqueue_style() {
		self::register_style();
		wp_enqueue_style( self::HANDLE );
	}

	/**
	 * Adiciona a aba "Lucide" ao gerenciador de ícones do Elementor.
	 *
	 * @param array $tabs Abas registradas.
	 * @return array
	 */
	public static function register_tab( $tabs ) {
		$tabs['lucide'] = array(
			'name'          => 'lucide',
			'label'         => __( 'Lucide', 'catedral-elements' ),
			'labelIcon'     => 'icon-shapes',
			'url'           => self::css_url(),
			'enqueue'       => array( self::css_url() ),
			'prefix'        => 'icon-',
			'displayPrefix' => '',
			'ver'           => CEE_VERSION,
			'fetchJson'     => CEE_URL . 'assets/lucide/lucide-elementor.json',
			'native'        => false,
		);

		return $tabs;
	}
}
