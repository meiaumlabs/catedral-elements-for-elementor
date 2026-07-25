<?php
/**
 * Rotina de desinstalação do Catedral Elements for Elementor.
 *
 * O plugin não cria opções persistentes nem tipos de post próprios: toda a
 * configuração vive nos dados do próprio widget do Elementor (removidos com a
 * página/template). Mantemos o guard padrão e a limpeza de transients do PUC.
 *
 * @package Catedral_Elements_For_Elementor
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Remove transients de verificação de atualização do Plugin Update Checker.
global $wpdb;
$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '_transient_puc_%catedral-elements-for-elementor%'
	    OR option_name LIKE '_transient_timeout_puc_%catedral-elements-for-elementor%'
	    OR option_name LIKE 'external_updates-catedral-elements-for-elementor%'"
);
