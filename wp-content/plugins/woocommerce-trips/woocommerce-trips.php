<?php
/*
Plugin Name: WooCommerce Trips
Description: Setup trip products based on packages
Version: 0.0.1
Author: Mike Barnard
Author URI: http://github.com/barnardm
Text Domain: woocommerce-trips

*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
include( 'includes/wc-checks.php' );

if ( ! function_exists( 'is_woocommerce_active' ) ) {
	function is_woocommerce_active() {
		return WC_Checks::woocommerce_active_check();
	}
}

if ( is_woocommerce_active() ) {
class WC_Trips {
    
    public function __construct() {
        define( 'WC_TRIPS_VERSION', '0.0.1' );
        define( 'WC_TRIPS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
        define( 'WC_TRIPS_MAIN_FILE', __FILE__ );

        add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
        
        if ( is_admin() ) {
            include( 'includes/admin/class-wc-trips-admin.php' );
        }
        register_activation_hook( __FILE__, array( $this, 'install' ) );
    }
    
    public function install() {
        add_action( 'shutdown', array( $this, 'delayed_install' ) );
    }
    
    public function delayed_install() {
        if ( ! get_term_by( 'slug', sanitize_title( 'trip' ), 'product_type' ) ) {
            wp_insert_term( 'trip', 'product_type' );
        }
    }
    
    public function includes() {
        include( 'includes/class-wc-product-trip.php' );
        // More includes here eventually
    }
}
$GLOBALS['wc_trips'] = new WC_Trips();
}