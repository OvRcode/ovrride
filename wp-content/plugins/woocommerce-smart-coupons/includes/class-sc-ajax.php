<?php
/**
 * Smart Coupons Ajax Actions
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Ajax' ) ) {

	/**
	 * Class for handling ajax actions for Smart Coupons
	 */
	class WC_SC_Ajax {

		/**
		 * Variable to hold instance of WC_SC_Ajax
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'wp_ajax_sc_json_search_coupons', array( $this, 'sc_json_search_coupons' ) );
			add_action( 'wp_ajax_smart_coupons_json_search', array( $this, 'smart_coupons_json_search' ) );
			add_action( 'wp_ajax_hide_notice_delete_after_usage', array( $this, 'hide_notice_delete_after_usage' ) );

		}

		/**
		 * Get single instance of WC_SC_Ajax
		 *
		 * @return WC_SC_Ajax Singleton object of WC_SC_Ajax
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param $function_name string
		 * @param $arguments array of arguments passed while calling $function_name
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) { return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Function to search coupons
		 *
		 * @param string $x search term
		 * @param array  $post_types
		 */
		public function sc_json_search_coupons( $x = '', $post_types = array( 'shop_coupon' ) ) {
			global $woocommerce, $wpdb;

			check_ajax_referer( 'search-coupons', 'security' );

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

			if ( empty( $term ) ) { die();
			}

			$args = array(
				'post_type'     	=> $post_types,
				'post_status'       => 'publish',
				'posts_per_page'    => -1,
				's'             	=> $term,
				'fields'            => 'all',
			);

			$posts = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'shop_coupon' AND post_title LIKE '$term%' AND post_status = 'publish'" );

			$found_products = array();

			$all_discount_types = wc_get_coupon_types();

			if ( $posts ) { foreach ( $posts as $post ) {

					$discount_type = get_post_meta( $post->ID, 'discount_type', true );

					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {
						$discount_type = ' (Type: ' . $all_discount_types[ $discount_type ] . ')';
						$found_products[ get_the_title( $post->ID ) ] = get_the_title( $post->ID ) . $discount_type;
					}
			}
			}

			echo json_encode( $found_products );

			die();
		}

		/**
		 * JSON Search coupon via ajax
		 *
		 * @param string $x search text
		 * @param array  $post_types
		 */
		public function smart_coupons_json_search( $x = '', $post_types = array( 'shop_coupon' ) ) {
			global $woocommerce, $wpdb;

			check_ajax_referer( 'search-coupons', 'security' );

			$term = (string) urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );

			if ( empty( $term ) ) { die();
			}

			$posts = $wpdb->get_results("SELECT *
										FROM {$wpdb->prefix}posts
										WHERE post_type = 'shop_coupon'
											AND post_title LIKE '$term%'
											AND post_status = 'publish'");

			$found_products = array();

			$all_discount_types = wc_get_coupon_types();

			if ( $posts ) { foreach ( $posts as $post ) {

					$discount_type = get_post_meta( $post->ID, 'discount_type', true );
					if ( ! empty( $all_discount_types[ $discount_type ] ) ) {

						$coupon = new WC_Coupon( get_the_title( $post->ID ) );

						if ( $this->is_wc_gte_30() ) {
							$discount_type = $coupon->get_discount_type();
							$coupon_amount = $coupon->get_amount();
						} else {
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						}

						switch ( $discount_type ) {

							case 'smart_coupon':
								$coupon_type = __( 'Store Credit', WC_SC_TEXT_DOMAIN );
								$coupon_amount = wc_price( $coupon_amount );
								break;

							case 'fixed_cart':
								$coupon_type = __( 'Cart Discount', WC_SC_TEXT_DOMAIN );
								$coupon_amount = wc_price( $coupon_amount );
								break;

							case 'fixed_product':
								$coupon_type = __( 'Product Discount', WC_SC_TEXT_DOMAIN );
								$coupon_amount = wc_price( $coupon_amount );
								break;

							case 'percent_product':
								$coupon_type = __( 'Product Discount', WC_SC_TEXT_DOMAIN );
								$coupon_amount = $coupon_amount . '%';
								break;

							case 'percent':
								$coupon_type = ( $this->is_wc_gte_30() ) ? __( 'Percentage Discount', WC_SC_TEXT_DOMAIN ) : __( 'Cart Discount', WC_SC_TEXT_DOMAIN );
								$coupon_amount = $coupon_amount . '%';
								break;

							default:
								$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
								$coupon_type = apply_filters( 'wc_sc_coupon_type', $default_coupon_type, $coupon, $all_discount_types );
								$coupon_amount = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
								break;

						}

						$discount_type = ' ( ' . $coupon_amount . ' ' . $coupon_type . ' )';
						$discount_type = wp_strip_all_tags( $discount_type );

						$found_products[ get_the_title( $post->ID ) ] = get_the_title( $post->ID ) . ' ' . $discount_type;
					}
				}
			}

			if ( ! empty( $found_products ) ) {
				echo json_encode( $found_products );
			}

			die();
		}

		/**
		 * Function to Hide Notice Delete After Usage
		 */
		public function hide_notice_delete_after_usage() {

			check_ajax_referer( 'hide-smart-coupons-notice', 'security' );

			$current_user_id = get_current_user_id();
		    update_user_meta( $current_user_id, 'hide_delete_credit_after_usage_notice', 'yes' );

		    echo json_encode( array( 'message' => 'success' ) );
		    die();

        }

	}

}

WC_SC_Ajax::get_instance();
