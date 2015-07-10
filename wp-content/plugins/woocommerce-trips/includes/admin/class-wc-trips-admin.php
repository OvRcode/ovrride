<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Admin {
    public function __construct() {
        //add_action( 'admin_init', array( $this, 'include_meta_box_handlers' ) );
        //add_action( 'admin_init', array( $this, 'redirect_new_add_booking_url' ) );
        add_filter( 'product_type_options', array( $this, 'product_type_options' ) );
        add_filter( 'product_type_selector' , array( $this, 'product_type_selector' ) );
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_tab' ), 5 );
        //add_action( 'woocommerce_product_write_panels', array( $this, 'booking_panels' ) );
        //add_action( 'admin_enqueue_scripts', array( $this, 'styles_and_scripts' ) );
        //add_action( 'woocommerce_process_product_meta', array( $this,'save_product_data' ), 20 );
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
        include( 'views/html-trip-general.php' );
    }
}
new WC_Trips_Admin();