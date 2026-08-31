<?php
/**
 * Integração com o Elementor: categoria e registro dos widgets.
 *
 * @package Catedral_Elements_For_Elementor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CEE_Elementor {

	/**
	 * Inicializa a integração (chamado em elementor/init).
	 */
	public static function init() {
		add_action( 'elementor/elements/categories_registered', array( __CLASS__, 'register_category' ) );
		add_action( 'elementor/widgets/register', array( __CLASS__, 'register_widgets' ) );
	}

	/**
	 * Registra a categoria de widgets do plugin.
	 *
	 * @param \Elementor\Elements_Manager $elements_manager Gerenciador.
	 */
	public static function register_category( $elements_manager ) {
		$elements_manager->add_category(
			'catedral-elements',
			array(
				'title' => __( 'Catedral Elements', 'catedral-elements' ),
				'icon'  => 'eicon-slides',
			)
		);
	}

	/**
	 * Registra os widgets.
	 *
	 * @param \Elementor\Widgets_Manager $widgets_manager Gerenciador.
	 */
	public static function register_widgets( $widgets_manager ) {
		require_once CEE_DIR . 'widgets/class-slide-carousel-widget.php';
		$widgets_manager->register( new \CEE_Slide_Carousel_Widget() );

		require_once CEE_DIR . 'widgets/class-hotspots-widget.php';
		$widgets_manager->register( new \CEE_Hotspots_Widget() );

		require_once CEE_DIR . 'widgets/class-posts-carousel-widget.php';
		$widgets_manager->register( new \CEE_Posts_Carousel_Widget() );
	}
}
