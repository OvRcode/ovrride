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
        define( 'WC_TRIPS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
        add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'trip_form_styles' ) );
        add_action( 'init', array( $this, 'init_post_types' ) );
        add_filter( 'woocommerce_product_tabs', array( $this, 'pickup_tab'), 98 );
        
        if ( is_admin() ) {
            include( 'includes/admin/class-wc-trips-admin.php' );
        }
        register_activation_hook( __FILE__, array( $this, 'install' ) );
        
        include( 'includes/class-wc-trips-cart.php' );
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
    
    public function trip_form_styles() {
        wp_enqueue_style( 'wc-trips-styles', WC_TRIPS_PLUGIN_URL . '/assets/css/trip_frontend.css', null, WC_TRIPS_VERSION );
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'); 
        wp_enqueue_script( 'wc-trips-frontend-js', WC_TRIPS_PLUGIN_URL . '/assets/js/front_end.js', array('jquery'), WC_TRIPS_VERSION, TRUE );
    }
    public function init_post_types() {
        $labels = array(
          'name'               => _x( 'Pickup Locations', 'woocommerce-trips' ),
          'singular_name'      => _x( 'Pickup Location', 'woocommerce-trips'),
          'add_new'            => _x( 'Add Location', 'woocommerce-trips'),
          'add_new_item'       => __( 'Add New Location' ),
          'edit_item'          => __( 'Edit Location' ),
          'new_item'           => __( 'New Location' ),
          'all_items'          => __( 'All Pickup Locations' ),
          'view_item'          => __( 'View Pickup Locations' ),
          'search_items'       => __( 'Search Pickup Locations' ),
          'not_found'          => __( 'No pickup locations found' ),
          'not_found_in_trash' => __( 'No pickup locations found in the Trash' ), 
          'parent_item_colon'  => '',
          'menu_name'          => 'Pickup Locations'
        );
        $args = array(
          'labels'        => $labels,
          'description'   => 'Pickup Locations for all trips',
          'public'        => true,
          'menu_position' => 40,
          'supports'      => array( 'title', 'thumbnail'),
          'has_archive'   => true,
        );
        register_post_type( 'pickup_locations', $args );
    }
    public function pickup_tab( $tabs ) {
        global $product;
        
        $pickups = get_post_meta( $product->id, '_wc_trip_pickups', true);
        if ( ! empty( $pickups ) ){
            $tabs['pickups'] = array(
                'title' 	=> 'Bus Times',
                'priority' 	=> 50,
                'callback' 	=> array( $this, 'bus_times_content')
                );
            }
        return $tabs;
    }
    public function bus_times_content() {
        global $product;
        
        $pickups = get_post_meta( $product->id, '_wc_trip_pickups', true);
        
        echo "<h2> Bus Times</h2>";
        foreach ( $pickups as $pickup ) {
            echo $this->pickup_html($pickup);
        }
    }
    public function pickup_html( $post_id ) {
        $pickup = get_post( $post_id );
        $address = get_post_meta( $post_id, '_pickup_location_address', true );
        $output = "";
        if ( $address ) {
            $cross_st = get_post_meta( $post_id, '_pickup_location_cross_st', true);
            $address = explode(",", ucwords( strtolower( $address ) ), 2);
            $time = get_post_meta( $post_id, '_pickup_location_time', true);
            $output = <<<PICKUPHTML
            <div class="pickup">
                <strong>{$pickup->post_title}</strong><br />
                {$address[0]}<br />
                {$cross_st}<br />
                {$address[1]}<br />
                <strong><a href="http://maps.google.com/?q={$address[0]}{$address[1]}" target="_blank">View Map</a></strong>
            </div>
PICKUPHTML;
        }

        return $output;
    }
}
$GLOBALS['wc_trips'] = new WC_Trips();
}