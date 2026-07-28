<?php
/**
 * Plugin Name:       Catedral Elements for Elementor
 * Plugin URI:        https://61labs.com.br/catedral-elements
 * Description:       Addon de widgets premium para o Elementor desenvolvido pela 61 Labs. Primeiro widget: Slide Carrossel com Scroll Horizontal para apresentações imobiliárias de alto padrão.
 * Version:           1.12.1
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            61 Labs
 * Author URI:        https://61labs.com.br
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       catedral-elements
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CEE_VERSION', '1.12.1' );
define( 'CEE_FILE', __FILE__ );
define( 'CEE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CEE_URL', plugin_dir_url( __FILE__ ) );
define( 'CEE_BASENAME', plugin_basename( __FILE__ ) );

/*
 * URL do repositório público no GitHub usado para atualizações automáticas.
 * Ajuste OWNER/REPO para o repositório real antes do primeiro push.
 */
define( 'CEE_GITHUB_URL', 'https://github.com/meiaumlabs/catedral-elements-for-elementor/' );

/*
 * ------------------------------------------------------------------
 * Atualização automática via GitHub (Plugin Update Checker).
 * Fluxo de release: publique uma Release com a tag "vX.Y.Z" (ex.: v1.0.0)
 * e anexe o ZIP do plugin como asset. O WordPress detecta e oferece a
 * atualização no painel, igual a um plugin do repositório oficial.
 * ------------------------------------------------------------------
 */
require_once CEE_DIR . 'includes/plugin-update-checker/plugin-update-checker.php';

$cee_update_checker = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
	CEE_GITHUB_URL,
	CEE_FILE,
	'catedral-elements-for-elementor'
);
$cee_update_checker->getVcsApi()->enableReleaseAssets();

require_once CEE_DIR . 'includes/class-cee-elementor.php';

/**
 * Ativação: limpa rewrite rules.
 */
function cee_activate() {
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'cee_activate' );

/**
 * Desativação: limpa rewrite rules.
 */
function cee_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'cee_deactivate' );

add_action( 'plugins_loaded', function () {
	load_plugin_textdomain( 'catedral-elements', false, dirname( CEE_BASENAME ) . '/languages' );

	// A integração com o Elementor só inicializa se o Elementor estiver ativo.
	add_action( 'elementor/init', array( 'CEE_Elementor', 'init' ) );
} );

/**
 * Registra os assets públicos (CSS/JS) usados pelo widget.
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style(
		'cee-slide-carousel',
		CEE_URL . 'assets/css/cee-slide-carousel.css',
		array(),
		CEE_VERSION
	);
	wp_register_script(
		'cee-slide-carousel',
		CEE_URL . 'assets/js/cee-slide-carousel.js',
		array(),
		CEE_VERSION,
		true
	);
}, 5 );

/**
 * Garante que os assets também sejam registrados no preview/editor do Elementor.
 */
add_action( 'elementor/frontend/after_register_scripts', function () {
	if ( ! wp_script_is( 'cee-slide-carousel', 'registered' ) ) {
		wp_register_script(
			'cee-slide-carousel',
			CEE_URL . 'assets/js/cee-slide-carousel.js',
			array(),
			CEE_VERSION,
			true
		);
	}
} );

add_action( 'elementor/frontend/after_register_styles', function () {
	if ( ! wp_style_is( 'cee-slide-carousel', 'registered' ) ) {
		wp_register_style(
			'cee-slide-carousel',
			CEE_URL . 'assets/css/cee-slide-carousel.css',
			array(),
			CEE_VERSION
		);
	}
} );

/**
 * Aviso caso o Elementor não esteja ativo.
 */
add_action( 'admin_notices', function () {
	if ( did_action( 'elementor/loaded' ) ) {
		return;
	}
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}
	echo '<div class="notice notice-warning is-dismissible"><p>';
	echo esc_html__( 'Catedral Elements precisa do Elementor ativo para exibir os widgets. Ative o Elementor para usar o Slide Carrossel.', 'catedral-elements' );
	echo '</p></div>';
} );

/**
 * Link "Elementor" na lista de plugins.
 */
add_filter( 'plugin_action_links_' . CEE_BASENAME, function ( $links ) {
	$url  = admin_url( 'edit.php?post_type=elementor_library' );
	$link = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Elementor', 'catedral-elements' ) . '</a>';
	array_unshift( $links, $link );
	return $links;
} );
