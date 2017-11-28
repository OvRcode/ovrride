<?php
/**
 * Compatibility file for WPML
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_WPML_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WPML
	 */
	class WC_SC_WPML_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_WPML_Compatibility
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'init', array( $this, 'woocommerce_wpml_compatibility' ), 11 );

		}

		/**
		 * Get single instance of WC_SC_WPML_Compatibility
		 *
		 * @return WC_SC_WPML_Compatibility Singleton object of WC_SC_WPML_Compatibility
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
         * Function to handle compatibility with WooCommerce Multilingual
         */
        public function woocommerce_wpml_compatibility() {
        	global $woocommerce_wpml;
        	if ( class_exists( 'woocommerce_wpml' ) && $woocommerce_wpml instanceof woocommerce_wpml && ! empty( $woocommerce_wpml->products ) ) {
        		remove_action( 'woocommerce_before_checkout_process', array( $woocommerce_wpml->products, 'wcml_refresh_cart_total' ) );
        	}
        }

	}

}

WC_SC_WPML_Compatibility::get_instance();
