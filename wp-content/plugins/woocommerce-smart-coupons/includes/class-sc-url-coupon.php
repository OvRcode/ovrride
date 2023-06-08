<?php
/**
 * Coupons via URL
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_URL_Coupon' ) ) {

	/**
	 * Class for handling coupons applied via URL
	 */
	class WC_SC_URL_Coupon {

		/**
		 * Variable to hold instance of WC_SC_URL_Coupon
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'apply_coupon_from_url' ), 20 );
			add_action( 'wp_loaded', array( $this, 'apply_coupon_from_session' ), 20 );
			add_action( 'wp_loaded', array( $this, 'move_applied_coupon_from_cookies_to_account' ) );

		}

		/**
		 * Get single instance of WC_SC_URL_Coupon
		 *
		 * @return WC_SC_URL_Coupon Singleton object of WC_SC_URL_Coupon
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
		 * Apply coupon code if passed in url
		 */
		public function apply_coupon_from_url() {

			if ( empty( $_SERVER['QUERY_STRING'] ) ) { return;
			}

			parse_str( $_SERVER['QUERY_STRING'], $coupon_args );

			if ( isset( $coupon_args['coupon-code'] ) && ! empty( $coupon_args['coupon-code'] ) ) {

				if ( empty( WC()->cart ) || WC()->cart->is_empty() ) {
					$this->hold_applied_coupon( $coupon_args );
				} else {

					if ( ! WC()->cart->has_discount( $coupon_args['coupon-code'] ) ) {
						WC()->cart->add_discount( trim( $coupon_args['coupon-code'] ) );
					}
				}

				if ( empty( $coupon_args['sc-page'] ) ) {
					return;
				}

				$redirect_url = '';

				if ( in_array( $coupon_args['sc-page'], array( 'shop', 'cart', 'checkout', 'myaccount' ) ) ) {
					if ( $this->is_wc_gte_30() ) {
						$page_id = wc_get_page_id( $coupon_args['sc-page'] );
					} else {
						$page_id = woocommerce_get_page_id( $coupon_args['sc-page'] );
					}
					$redirect_url = get_permalink( $page_id );
				} else {
					$redirect_url = get_permalink( get_page_by_title( $coupon_args['sc-page'] ) );
				}

				if ( empty( $redirect_url ) ) {
					$redirect_url = home_url();
				}

				$redirect_url = $this->get_redirect_url_after_smart_coupons_process( $redirect_url );

				wp_safe_redirect( $redirect_url );

				exit;

			}

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function apply_coupon_from_session() {

			if ( empty( WC()->cart ) || WC()->cart->is_empty() ) { return;
			}

			$user_id = get_current_user_id();

			if ( $user_id == 0 ) {
				$unique_id = ( ! empty( $_COOKIE['sc_applied_coupon_profile_id'] ) ) ? $_COOKIE['sc_applied_coupon_profile_id'] : '';
                $applied_coupon_from_url = ( ! empty( $unique_id ) ) ? get_option( 'sc_applied_coupon_profile_' . $unique_id, array() ) : array();
            } else {
                $applied_coupon_from_url = get_user_meta( $user_id, 'sc_applied_coupon_from_url', true );
            }

            if ( empty( $applied_coupon_from_url ) ) { return;
			}

			foreach ( $applied_coupon_from_url as $index => $coupon_code ) {
				WC()->cart->add_discount( trim( $coupon_code ) );
				unset( $applied_coupon_from_url[ $index ] );
			}

			if ( $user_id == 0 ) {
				update_option( 'sc_applied_coupon_profile_' . $unique_id, $applied_coupon_from_url );
			} else {
				update_user_meta( $user_id, 'sc_applied_coupon_from_url', $applied_coupon_from_url );
			}

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function hold_applied_coupon( $coupon_args ) {

			$user_id = get_current_user_id();

			if ( $user_id == 0 ) {
                $applied_coupons = $this->save_applied_coupon_in_cookie( $coupon_args );
            } else {
                $applied_coupons = $this->save_applied_coupon_in_account( $coupon_args, $user_id );
            }

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function save_applied_coupon_in_cookie( $coupon_args ) {

			if ( ! empty( $coupon_args['coupon-code'] ) ) {

				if ( empty( $_COOKIE['sc_applied_coupon_profile_id'] ) ) {
	                $unique_id = $this->generate_unique_id();
	            } else {
	                $unique_id = $_COOKIE['sc_applied_coupon_profile_id'];
	            }

	            $applied_coupons = get_option( 'sc_applied_coupon_profile_' . $unique_id, array() );

	            if ( ! in_array( $coupon_args['coupon-code'], $applied_coupons ) ) {
	            	$applied_coupons[] = $coupon_args['coupon-code'];
	            }

	            update_option( 'sc_applied_coupon_profile_' . $unique_id, $applied_coupons );

	            wc_setcookie( 'sc_applied_coupon_profile_id', $unique_id, $this->get_cookie_life() );

			}

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function save_applied_coupon_in_account( $coupon_args, $user_id ) {

			if ( ! empty( $coupon_args['coupon-code'] ) ) {

				$applied_coupons = get_user_meta( $user_id, 'sc_applied_coupon_from_url', true );

				if ( empty( $applied_coupons ) ) {
					$applied_coupons = array();
				}

	            if ( ! in_array( $coupon_args['coupon-code'], $applied_coupons ) ) {
	            	$applied_coupons[] = $coupon_args['coupon-code'];
	            }

	            update_user_meta( $user_id, 'sc_applied_coupon_from_url', $applied_coupons );

			}

		}

		/**
		 * Apply coupon code from session, if any
		 */
		public function move_applied_coupon_from_cookies_to_account() {

			$user_id = get_current_user_id();

            if ( $user_id > 0 && ! empty( $_COOKIE['sc_applied_coupon_profile_id'] ) ) {

                $unique_id = $_COOKIE['sc_applied_coupon_profile_id'];

                $applied_coupons = get_option( 'sc_applied_coupon_profile_' . $unique_id );

                if ( $applied_coupons !== false && is_array( $applied_coupons ) && ! empty( $applied_coupons ) ) {

                    $saved_coupons = get_user_meta( $user_id, 'sc_applied_coupon_from_url', true );
                    if ( empty( $saved_coupons ) || ! is_array( $saved_coupons ) ) {
                        $saved_coupons = array();
                    }
                    $saved_coupons = array_merge( $saved_coupons, $applied_coupons );
                    update_user_meta( $user_id, 'sc_applied_coupon_from_url', $saved_coupons );
                    wc_setcookie( 'sc_applied_coupon_profile_id', '' );
                    delete_option( 'sc_applied_coupon_profile_' . $unique_id );

                }
			}

		}

		/**
		 * Function to get redirect URL after processing Smart Coupons params
		 *
		 * @param string $url
		 * @return string $url
		 */
		public function get_redirect_url_after_smart_coupons_process( $url = '' ) {

            if ( empty( $url ) ) {
                return $url;
            }

            $query_string = ( ! empty( $_SERVER['QUERY_STRING'] ) ) ? $_SERVER['QUERY_STRING'] : array();

            parse_str( $query_string, $url_args );

            $sc_params = array( 'coupon-code', 'sc-page' );
            $url_params = array_diff_key( $url_args, array_flip( $sc_params ) );

            return add_query_arg( $url_params, $url );
        }

		/**
         * To generate unique id
         *
         * Credit: WooCommerce
         */
        public function generate_unique_id() {

            require_once( ABSPATH . 'wp-includes/class-phpass.php' );
            $hasher = new PasswordHash( 8, false );
            return md5( $hasher->get_random_bytes( 32 ) );

        }

        /**
         * To get cookie life
         */
        public function get_cookie_life() {

            $life = get_option( 'wc_sc_coupon_cookie_life', 180 );

            return apply_filters( 'wc_sc_coupon_cookie_life', time() + (60 * 60 * 24 * $life) );

        }



	}

}

WC_SC_URL_Coupon::get_instance();
