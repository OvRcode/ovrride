<?php
/**
 * Compatibility class for WooCommerce 2.5
 *
 * @category	Class
 * @package		WC-compat
 * @author 		StoreApps
 * @version  	1.0.0
 * @since 	 	WooCommerce 2.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'SA_WC_Compatibility_2_5' ) ) {

	/**
	 * Class to check for WooCommerce versions & return variables accordingly
	 */
	class SA_WC_Compatibility_2_5 {

		/**
		 * Function to check if WooCommerce version is greater than and equal To 2.5
		 *
		 * @return boolean
		 */
		public static function is_wc_gte_25() {
			return self::is_wc_greater_than( '2.4.13' );
		}

		/**
		 * Is WooCommerce Greater Than And Equal To 2.4
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_24() {
			return self::is_wc_greater_than( '2.3.13' );
		}

		/**
		 * Is WooCommerce Greater Than And Equal To 2.3
		 * 
		 * @return boolean 
		 */
		public static function is_wc_gte_23() {
			return self::is_wc_greater_than( '2.2.11' );
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_product( $the_product = false, $args = array() ) {

			if ( self::is_wc_gte_22() ) {
				return wc_get_product( $the_product, $args );
			} elseif ( self::is_wc_21() ) {
				return get_product( $the_product, $args );
			} else {
				return new WC_Product( $the_product );
			}

		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_formatted_product_name( $product = false ) {

			if ( self::is_wc_gte_21() ) {
				return $product->get_formatted_name();
			} else {
				return woocommerce_get_formatted_product_name( $product );
			}
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function get_order( $the_order = false ) {

			if ( self::is_wc_gte_22() ) {
				return wc_get_order( $the_order );
			} else {

				global $post;

				if ( false === $the_order ) {
					$order_id = $post->ID;
				} elseif ( $the_order instanceof WP_Post ) {
					$order_id = $the_order->ID;
				} elseif ( is_numeric( $the_order ) ) {
					$order_id = $the_order;
				}

				return new WC_Order( $order_id );

			}

		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function enqueue_js( $js = false ) {

			if ( self::is_wc_gte_21() ) {
				wc_enqueue_js( $js );
			} else {
				global $woocommerce;
				$woocommerce->add_inline_js( $js );
			}

		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_gte_22() {
			return self::is_wc_greater_than( '2.1.12' );
		}
		
		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_gte_21() {
			return self::is_wc_greater_than( '2.0.20' );
		}
		
		/**
		 * @since 1.0.0 of SA_WC_Compatibility_2_2
		 */
		public static function is_wc_gte_20() {
			return self::is_wc_greater_than( '1.6.6' );
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility
		 */
		public static function global_wc() {
			if ( self::is_wc_21() ) {
				return WC();
			} else {
				global $woocommerce;
				return $woocommerce;
			}
		}

		/**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_get_formatted_name( $product = false ) {
			if ( self::is_wc_21() ) {
				return $product->get_formatted_name();
			} else {
				return woocommerce_get_formatted_product_name( $product );
			}
		}

		/**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_get_template( $template_path ) {

			if ( self::is_wc_21() ) {
				return wc_get_template( $template_path );
			} else {
				return woocommerce_get_template( $template_path );
			}
		}

		/**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_get_coupon_types() {

			if ( self::is_wc_21() ) {
				return wc_get_coupon_types();
			} else {
				global $woocommerce;
				return $woocommerce->get_coupon_discount_types();
			}
		}

		/**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_add_notice( $message, $notice_type = 'success' ) {

			if ( self::is_wc_21() ) {
				wc_add_notice( $message, $notice_type );
			} else {
				global $woocommerce;

				if ( 'error' == $notice_type ) {
					$woocommerce->add_error( $message );
				} else {
					$woocommerce->add_message( $message );
				}
			}
		}

		/**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_notice_count( $notice_type = '' ) {

			if ( self::is_wc_21() ) {
				return wc_notice_count( $notice_type );
			} else {
				global $woocommerce;

				if ( 'error' == $notice_type ) {
					return $woocommerce->error_count();
				} else {
					return $woocommerce->message_count();
				}
			}
		}
        
        /**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function get_checkout_pay_page_order_id() {

			if (self::is_wc_21()) {
				global $wp;
				return isset($wp->query_vars['order-received']) ? absint($wp->query_vars['order-received']) : 0;
			} else {
				return isset($_GET['order']) ? absint($_GET['order']) : 0;
			}
		}
                
        /**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_format_decimal($number, $dp = false, $trim_zeros = false) {

			if (self::is_wc_21()) {
				return wc_format_decimal($number, get_option( 'woocommerce_price_num_decimals' ), $trim_zeros);
			} else {
				return woocommerce_format_total($number);
			}
		}
                
        /**
		 * @since 1.0.0 of SA_WC_Compatibility
		 */
		public static function wc_price($price) {
			if (self::is_wc_21()) {
				return wc_price($price);
			} else {
				return woocommerce_price($price);
			}
		}
                
        /**
		 * @since 1.0.0 of SA_WC_Compatibility
		 */
		public static function wc_attribute_label($label) {

			if (self::is_wc_21()) {
				return wc_attribute_label($label);
			} else {
				global $woocommerce;
				return $woocommerce->attribute_label($label);
			}
		}
                
        /**
		 * @since 1.1.0 of SA_WC_Compatibility
		 */
		public static function wc_attribute_orderby($label) {

			if (self::is_wc_21()) {
				return wc_attribute_orderby($label);
			} else {
				global $woocommerce;
				return $woocommerce->attribute_orderby($label);
			}
		}

		/**
		 * @since 1.0.0 of SA_WC_Compatibility
		 */
		public static function is_wc_21() {
			return self::is_wc_greater_than( '2.0.20' );
		}

		/**
		 * Function to get WooCommerce version
		 */
		public static function get_wc_version() {
			if ( defined( 'WC_VERSION' ) && WC_VERSION ) {
				return WC_VERSION;
			}
			if ( defined( 'WOOCOMMERCE_VERSION' ) && WOOCOMMERCE_VERSION ) {
				return WOOCOMMERCE_VERSION;
			}
			return null;
		}

		/**
		 * Function to compare current version of WooCommerce on site with active version of WooCommerce
		 *
		 * @param int $version Version number to compare.
		 */
		public static function is_wc_greater_than( $version ) {
			return version_compare( self::get_wc_version(), $version, '>' );
		}
	}
}
