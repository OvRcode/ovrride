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
        add_action( 'wp_enqueue_scripts', array( $this, 'trip_scripts_and_styles' ) );
        add_action( 'init', array( $this, 'init_post_types' ) );
        add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs'), 98 );
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
    
    public function trip_scripts_and_styles() {
        wp_enqueue_style( 'wc-trips-styles', WC_TRIPS_PLUGIN_URL . '/assets/css/trip_frontend.css', null, WC_TRIPS_VERSION );
        wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css'); 
        wp_enqueue_script( 'wc-trips-frontend-js', WC_TRIPS_PLUGIN_URL . '/assets/js/front_end.js', array('jquery'), WC_TRIPS_VERSION, TRUE );
        wp_enqueue_script( 'verimail-jquery', WC_TRIPS_PLUGIN_URL . '/assets/js/verimail.jquery.min.js', array('jquery'), WC_TRIPS_VERSION, TRUE);
    }
    
    public function init_post_types() {
        $pickupLabels = array(
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
        $pickupArgs = array(
          'labels'        => $pickupLabels,
          'description'   => 'Pickup Locations for all trips',
          'public'        => true,
          'menu_position' => 40,
          'supports'      => array( 'title', 'thumbnail'),
          'has_archive'   => true,
        );
        register_post_type( 'pickup_locations', $pickupArgs );
        $destinationLabels = array(
          'name'               => _x( 'Destinations', 'woocommerce-trips' ),
          'singular_name'      => _x( 'Destination', 'woocommerce-trips'),
          'add_new'            => _x( 'Add Destination', 'woocommerce-trips'),
          'add_new_item'       => __( 'Add New Destination' ),
          'edit_item'          => __( 'Edit Destination' ),
          'new_item'           => __( 'New Destination' ),
          'all_items'          => __( 'All Destinations' ),
          'view_item'          => __( 'View Destinations' ),
          'search_items'       => __( 'Search Destinations' ),
          'not_found'          => __( 'No Destinations found' ),
          'not_found_in_trash' => __( 'No Destinations found in the Trash' ), 
          'parent_item_colon'  => '',
          'menu_name'          => 'Destinations'
        );
        $destinationArgs = array(
          'labels'        => $destinationLabels,
          'description'   => 'Destinations for all trips',
          'public'        => true,
          'menu_position' => 40,
          'supports'      => array( 'title', 'thumbnail'),
          'has_archive'   => true,
        );
        register_post_type( 'destinations', $destinationArgs );
    }
    
    public function product_tabs( $tabs ) {
        global $product, $wpdb;

        $destination = get_post_meta( $product->id, '_wc_trip_destination', true);
        $query = "SELECT ID FROM {$wpdb->posts} WHERE post_title='" . $destination . "' and post_type='destinations'";
        $destination_id = $wpdb->get_var( $query );
        $destination_map = get_post_meta( $destination_id, '_trail_map', true);
        $trip_includes = get_post_meta( $product->id, '_wc_trip_includes', true);
        $pickups = get_post_meta( $product->id, '_wc_trip_pickups', true);
        $trip_rates = get_post_meta( $product->id, '_wc_trip_rates', true);
        
        if ( $pickups ) {
            $tabs['pickups'] = array(
                'title'     => 'Bus Times',
                'priority'  => 50,
                'callback'  => array( $this, 'bus_times_content')
                );
            }
            if ( $destination_map ) {
                $tabs['trail_map'] = array(
                    'title'     => 'Trail Map',
                    'priority'  => 45,
                    'callback'  => array( $this, 'trail_map_content')
                );
            }
            if ( $trip_includes ) {
                $tabs['includes'] = array(
                    'title'     => 'Includes',
                    'priority'  => 40,
                    'callback'  => array( $this, 'includes_content')
                );
            }
            if ( $trip_rates ) {
                $tabs['rates'] = array(
                    'title'     => 'Rates',
                    'priority'  => 42,
                    'callback'  => array( $this, 'rates_content')
                );
            }
        return $tabs;
    }
    public function rates_content() {
        global $product;
        $rates_data = do_shortcode(shortcode_unautop(get_post_meta( $product->id, '_wc_trip_rates', true)));
        echo $rates_data;
    }
    public function trail_map_content() {
        global $product, $wpdb;
        wp_enqueue_style("featherlight-css", WC_TRIPS_PLUGIN_URL . "/assets/css/featherlight.min.css");
        wp_enqueue_script("featherlight-js", WC_TRIPS_PLUGIN_URL . "/assets/js/featherlight.min.js", array('jquery'));
        $destination = get_post_meta( $product->id, '_wc_trip_destination', true);
        $query = "SELECT ID FROM {$wpdb->posts} WHERE post_title='" . $destination . "' and post_type='destinations'";
        $destination_id = $wpdb->get_var( $query );
        $destination_map = get_post_meta( $destination_id, '_trail_map', true);
        echo <<<MAP
            <p>
                <a href="#" data-featherlight="#wc_trip_trail_map">
                    <img  src="{$destination_map}" id="wc_trip_trail_map" alt="mtsnow" />
                    </a>
            </p>
MAP;
    }
    public function bus_times_content() {
        global $product;
        
        $pickups = get_post_meta( $product->id, '_wc_trip_pickups', true);
        
        echo "<h2> Bus Times</h2>";
        $leftRight = "left";
        $count = 0;
        $leftColumnContent = "";
        $rightColumnContent = "";
        foreach ( $pickups as $pickup ) {
            $pickupHtml = $this->pickup_html($pickup);
            $tempHtml =<<<TEMPHTML
                <div class="pickup">
                    {$pickupHtml}
                </div>
TEMPHTML;
            if ( $count & 1 ) {
                $rightColumnContent .= $tempHtml;
            } else {
                $leftColumnContent .= $tempHtml;
            }
            $count++;
        }
        echo <<<TESTING
            <div class="busLeftColumn">{$leftColumnContent}</div>
            <div class="busRightColumn">{$rightColumnContent}</div>
TESTING;
    }
    public function includes_content() {
        global $product;
        $includes_data = do_shortcode(shortcode_unautop(get_post_meta( $product->id, '_wc_trip_includes', true)));
        echo $includes_data;
    }
    public function pickup_html( $post_id ) {
        $pickup = get_post( $post_id );
        $address = get_post_meta( $post_id, '_pickup_location_address', true );
        $output = "";
        if ( $address ) {
            $cross_st = get_post_meta( $post_id, '_pickup_location_cross_st', true);
            $address = explode(",", ucwords( strtolower( $address ) ), 2);
            $time = date("g:i a", strtotime(get_post_meta( $post_id, '_pickup_location_time', true)));
            $output = <<<PICKUPHTML
                <strong>{$pickup->post_title}</strong><br />
                {$address[0]}<br />
                {$cross_st}<br />
                {$address[1]}<br />
                Bus departs at {$time}<br />
                <strong><a href="http://maps.google.com/?q={$address[0]}{$address[1]}" target="_blank">View Map</a></strong>
PICKUPHTML;
        }

        return $output;
    }
    
}
$GLOBALS['wc_trips'] = new WC_Trips();
}