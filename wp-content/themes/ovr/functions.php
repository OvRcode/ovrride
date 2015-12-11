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
// clear cart on user logout, temp fix for issue with trip item meta data clearing on logout
function clear_cart_on_logout() {
    if( function_exists('WC') ){
        WC()->cart->empty_cart();
    }
}
add_action('wp_logout', 'clear_cart_on_logout');
//fix for pingback security issue
add_filter( 'xmlrpc_methods', 'remove_xmlrpc_pingback_ping' );
function remove_xmlrpc_pingback_ping( $methods ) {
	unset( $methods['pingback.ping'] );
 	return $methods;
} ;

// Sort products by date picker field on category page
add_filter( 'woocommerce_shortcode_products_query', 'ovr_shortcode_products_orderby' );
function ovr_shortcode_products_orderby( $args ) {
	$args['meta_key'] = 'date_picker';
	$args['orderby'] = 'meta_value';
	$args['order'] = 'asc';

	return $args;
}

// Remove company field from checkout
add_filter( 'woocommerce_checkout_fields' , 'ovr_checkout_fields' );
function ovr_checkout_fields( $fields ) {
	unset($fields['billing']['billing_company']);
	unset($fields['shipping']['shipping_company']);

	return $fields;
}

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
    wp_enqueue_style( 'custom-login', get_stylesheet_directory_uri() . '/login.css' );
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

// Theme mode selection
$themename = "OvRride";
$shortname = "ovr";
$options = array (
array( "name" => "Style Sheet",
    "desc" => "Summer or winter theme?",
    "id" => $shortname."_style_sheet",
    "type" => "select",
    "options" => array("default", "winter", "summer"),
    "std" => "default"),
);

function ovr_add_admin() {

global $themename, $shortname, $options;

if ( $_GET['page'] == basename(__FILE__) ) {

if ( 'save' == $_REQUEST['action'] ) {

foreach ($options as $value) {
update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

foreach ($options as $value) {
if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

header("Location: themes.php?page=functions.php&saved=true");
die;

} else if( 'reset' == $_REQUEST['action'] ) {

foreach ($options as $value) {
delete_option( $value['id'] ); }

header("Location: themes.php?page=functions.php&reset=true");
die;

}
}

add_theme_page($themename." Options", "".$themename." Options", 'edit_themes', basename(__FILE__), 'ovr_admin');

}

function ovr_admin() {
	global $themename, $shortname, $options;
	include("ovr_admin_template.php");
}
add_action('admin_menu', 'ovr_add_admin');
