<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Admin {
    public function __construct() {
        global $post;

        $post_id = $post->ID;
        
        //add_action( 'admin_init', array( $this, 'include_meta_box_handlers' ) );
        //add_action( 'admin_init', array( $this, 'redirect_new_add_booking_url' ) );
        add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
        add_filter( 'product_type_selector' , array( $this, 'product_type_selector' ) );
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
        add_action( 'woocommerce_product_write_panels', array( $this, 'trip_panels' ) );
        //add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
        add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'general_tab' ) );
        //add_filter( 'product_type_options', array( $this, 'booking_product_type_options' ) );
        //add_action( 'load-options-general.php', array( $this, 'reset_ics_exporter_timezone_cache' ) );

        // Ajax
        //add_action( 'wp_ajax_woocommerce_add_bookable_resource', array( $this, 'add_bookable_resource' ) );
        //add_action( 'wp_ajax_woocommerce_remove_bookable_resource', array( $this, 'remove_bookable_resource' ) );

        //add_action( 'wp_ajax_woocommerce_add_bookable_person', array( $this, 'add_bookable_person' ) );
        //add_action( 'wp_ajax_woocommerce_remove_bookable_person', array( $this, 'remove_bookable_person' ) );
    }
    
    public function product_type_options( $options ) {
        $options['virtual']['wrapper_class'] .= ' show_if_trip';
        return $options;
    }
    
    public function product_type_selector( $types ) {
        $types[ 'trip' ] = 'Trips product';
        return $types;
    }
    
    public function add_tab() {
        include( 'views/html-trip-tab.php' );
    }
    public function general_tab() {
		global $post;
		$post_id = $post->ID;
        include( 'views/html-trip-general.php' );
    }
    public function save_product_data() {
        global $wpdb;
        global $post;
        $post_id = $post->ID;
        $product_type = empty( $_POST['product-type'] ) ? 'simple' : sanitize_title( stripslashes( $_POST['product-type'] ) );

        if ( 'trip' !== $product_type ) {
            return;
        }

        // Save meta from general tab
        $meta_to_save = array(
            '_wc_trip_base_price'               => 'float',
            '_wc_trip_destination'              => 'string',
            '_wc_trip_type'                     => 'string',
            '_wc_trip_start_date'               => 'date',
            '_wc_trip_end_date'                 => 'date',
            '_wc_trip_stock'                    => 'int'
            );
        foreach ( $meta_to_save as $meta_key => $sanitize ) {
            $value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
            switch ( $sanitize ) {
                case 'int' :
                    $value = absint( $value );
                    break;
                case 'float' :
                    $value = floatval( $value );
                    break;
                default :
                    $value = sanitize_text_field( $value );
            }
            update_post_meta( $post_id, $meta_key, $value );
        }
    }
    
    public function trip_panels() {
        global $post;
        $post_id = $post->ID;
        include( 'views/html-trip-primary-packages.php' );
        include( 'views/html-trip-secondary-packages.php' );
        //include( 'views/html-trip-pickup-location.php');
    }
}
new WC_Trips_Admin();