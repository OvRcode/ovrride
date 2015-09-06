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

//fix for pingback security issue
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );
function remove_xmlrpc_pingback_ping( $methods ) {
	unset( $methods['pingback.ping'] );
 	return $methods;
} ;

// Custom login
add_filter( 'login_headerurl', 'ovr_login_url' );
add_filter( 'login_headertitle', 'ovr_login_url_title' );
add_action( 'login_enqueue_scripts', 'ovr_login_stylesheet' );
function ovr_login_url() {
    return home_url();
}
function ovr_login_url_title() {
    return 'Get Away The Right Way!';
}
function ovr_login_stylesheet() {
    error_log(get_template_directory_uri());
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login.css' );
    //wp_enqueue_script( 'custom-login', get_template_directory_uri() . '/style-login.js' );
}

// check for empty-cart get param to clear the cart
add_action( 'init', 'woocommerce_clear_cart_url' );
function woocommerce_clear_cart_url() {
    global $woocommerce;
    
    if ( isset( $_GET['empty-cart'] ) ) {
        $woocommerce->cart->empty_cart(); 
    }
}

// Remove order again button
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );
