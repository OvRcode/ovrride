<?php
/*
Plugin Name: WooCommerce Trips
Description: Setup trip products based on packages
Version: 1.1.3
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
        define( 'WC_TRIPS_VERSION', '1.0.0' );
        define( 'WC_TRIPS_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
        define( 'WC_TRIPS_MAIN_FILE', __FILE__ );
        define( 'WC_TRIPS_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );
        add_action( 'woocommerce_loaded', array( $this, 'includes' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'trip_scripts_and_styles' ) );
        add_action( 'init', array( $this, 'init_post_types' ) );
        add_filter( 'woocommerce_product_tabs', array( $this, 'product_tabs'), 98 );
        add_filter('woocommerce_product_description_heading',array( $this, 'remove_description_header'));
        if ( is_admin() ) {
            include( 'includes/admin/class-wc-trips-admin.php' );
        }
        register_activation_hook( __FILE__, array( $this, 'install' ) );

        include( 'includes/class-wc-trips-cart.php' );

        add_action( 'rest_api_init', function() {
          register_rest_route('trips/v1','beach-bus/', array(
            'methods'   =>  'GET',
            'callback'  =>  array($this, 'beach_bus_api'),
          ));
        });

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

    public function beach_bus_api() {
      global $wpdb;
      // Find all beach bus trips
      $results = $wpdb->get_results("SELECT `ID`, `post_title`, `guid`
      FROM `wp_posts`
      JOIN `wp_postmeta` on `wp_posts`.`ID` = `wp_postmeta`.`post_id`
      WHERE `post_type` = 'product' AND `post_status` = 'publish'
      AND `meta_key` = '_wc_trip_type'
      AND `meta_value` = 'beach_bus'", ARRAY_A);

      foreach( $results as $index => $array ) {
        $rowResult = $wpdb->get_results("SELECT `meta_key`, `meta_value`
        FROM `wp_postmeta`
        WHERE `post_id` = '" . $array['ID'] . "'
        AND (`meta_key` = '_wc_trip_start_date'
        OR `meta_key` = '_stock_status')", ARRAY_A);
        $tempArray['ID'] = $array['ID'];
        $tempArray['title'] = $array['post_title'];
        $tempArray['url'] = $array['guid'] . "?bb=1";
        foreach( $rowResult as $index => $metaArray ) {
          switch( $metaArray['meta_key'] ) {
            case '_wc_trip_start_date':
              $tempArray['date'] = date("Y-m-d",strtotime($metaArray['meta_value']));
              break;
            case '_stock_status':
              $tempArray['stock'] = $metaArray['meta_value'];
          }
        }
        $beach_bus_data[] = $tempArray;
      }
      // Sort beach bus data by trip date
      usort($beach_bus_data, function($a, $b){
        return strcasecmp($a['date'], $b['date']);
      });
      return $beach_bus_data;
    }
    public function product_tabs( $tabs ) {
        global $product, $wpdb;
        $product_tabs = array(
          "videos" => array(
            "meta_key" => "_wc_trip_videos",
            "title" => "Videos",
            "priority"  => 47,
            "callback" => array($this, 'video_content')
          ),
          "routes" => array(
            "meta_key" => "_wc_trip_routes",
            "title" => "Bus Routes",
            "priority"  => 38,
            "callback" => array($this, 'routes_content')
          ),
          "partners" => array(
            "meta_key" => "_wc_trip_partners",
            "title" => "Partners",
            "priority"  => 50,
            "callback" => array($this, 'partners_content')
          ),
          "pickups" => array(
            "meta_key" => "_wc_trip_pickups",
            "title" => "Bus Times",
            "priority"  => 50,
            "callback" => array($this, 'bus_times_content')
          ),
          "partners" => array(
            "meta_key" => "_wc_trip_partners",
            "title" => "Partners",
            "priority"  => 50,
            "callback" => array($this, 'partners_content')
          ),
          "trail_map_content" => array(
            "meta_key" => "_trail_map",
            "title" => "Trail Map",
            "priority"  => 45,
            "callback" => array($this, 'trail_map_content')
          ),
          "includes" => array(
            "meta_key" => "_wc_trip_includes",
            "title" => "Includes",
            "priority"  => 40,
            "callback" => array($this, 'includes_content')
          ),
          "rates" => array(
            "meta_key" => "_wc_trip_rates",
            "title" => "Rates",
            "priority"  => 42,
            "callback" => array($this, 'rates_content')
          ),
          "flight_times" => array(
            "meta_key" => "_wc_trip_flight_times",
            "title" => "Flight Times",
            "priority"  => 43,
            "callback" => array($this, 'flight_times_content')
          ),
          "pics" => array(
            "meta_key" => "_wc_trip_pics",
            "title" => "Pics",
            "priority"  => 46,
            "callback" => array($this, 'pics_content')
          )
        );
        foreach ( $product_tabs as $name => $array) {
          if ( "destination" !== $name ) {
              $value = get_post_meta( $product->id, $array['meta_key'], true);
          } else {
            $destination = get_post_meta( $product->id, '_wc_trip_destination', true);
            $query = "SELECT ID FROM {$wpdb->posts} WHERE post_title='" . $destination . "' and post_type='destinations'";
            $destination_id     = $wpdb->get_var( $query );
            $value = get_post_meta( $destination_id, '_trail_map', true);
          }

          if ( "" !== $value && FALSE !== $value ) {
            $tab_array = $array;
            unset($tab_array["meta_key"]);
            $tabs[$name] = $tab_array;
          }
        }


        return $tabs;
    }
    public function video_content(){
      global $product;
      $video_data = do_shortcode( get_post_meta( $product->id, '_wc_trip_videos', true ) );

      //echo apply_filters('the_content', $video_data);
      echo $video_data;
    }
    public function routes_content(){
      global $product;
      $routes_data = do_shortcode( get_post_meta( $product->id, '_wc_trip_routes', true) );

      echo apply_filters('the_content', $routes_data);
    }

    public function partners_content(){
      global $product;
      $partners_data = do_shortcode( get_post_meta( $product->id, '_wc_trip_partners', true) );

      echo apply_filters('the_content', $partners_data);
    }
    public function pics_content(){
        global $product;
        $pics_data = do_shortcode( get_post_meta( $product->id, '_wc_trip_pics', true) );
        //echo do_shortcode($pics_data, FALSE);
        echo apply_filters('the_content', $pics_data);
    }

    public function flight_times_content(){
        global $product;
        $flight_times_data = do_shortcode( shortcode_unautop( get_post_meta( $product->id, '_wc_trip_flight_times', true) ) );
        echo apply_filters('the_content', $flight_times_data);
    }

    public function rates_content() {
        global $product;
        $rates_data = do_shortcode( shortcode_unautop( get_post_meta( $product->id, '_wc_trip_rates', true) ) );
        echo apply_filters('the_content', $rates_data );
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

        echo "<h4>&nbsp;&nbsp;Bus Times:</h4>";
        $leftRight = "left";
        $count = 0;
        $leftColumnContent = "";
        $rightColumnContent = "";
        foreach ( $pickups as $pickup => $route ) {
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
        $includes_data = do_shortcode( shortcode_unautop( get_post_meta( $product->id, '_wc_trip_includes', true) ) );
        echo apply_filters('the_content', $includes_data );
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

    public function remove_description_header() {
        return '';
    }
}
$GLOBALS['wc_trips'] = new WC_Trips();
}
