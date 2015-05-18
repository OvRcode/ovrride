<?php
/**
 * Outputs the opening wrapper for the WooCommerce content
 *
 * @since Quark 1.3
 *
 * @return void
 */
if ( ! function_exists( 'quark_woocommerce_before_main_content' ) ) {
	function quark_woocommerce_before_main_content() {
		if( ( is_shop() && !of_get_option( 'woocommerce_shopsidebar', '1' ) ) || ( is_product() && !of_get_option( 'woocommerce_productsidebar', '1' ) ) ) {
			echo '<div class="col grid_12_of_12">';
		}
		else {
			echo '<div class="col grid_10_of_12 offset_1">';
		}
	}
	add_action( 'woocommerce_before_main_content', 'quark_woocommerce_before_main_content', 10 );
}

add_action( 'after_setup_theme', 'woocommerce_support' );
function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
//fix for pingback security issue
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );
function remove_xmlrpc_pingback_ping( $methods ) {
	unset( $methods['pingback.ping'] );
 	return $methods;
} ;