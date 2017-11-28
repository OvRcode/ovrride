<?php
/**
 * Plugin Name: WooCommerce Smart Coupons
 * Plugin URI: https://woocommerce.com/products/smart-coupons/
 * Description: <strong>WooCommerce Smart Coupons</strong> lets customers buy gift certificates, store credits or coupons easily. They can use purchased credits themselves or gift to someone else.
 * Version: 3.3.8
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Developer: StoreApps
 * Developer URI: https://www.storeapps.org/
 * Requires at least: 3.5
 * Tested up to: 4.8.1
 * WC requires at least: 2.5.0
 * WC tested up to: 3.1.2
 * Text Domain: woocommerce-smart-coupons
 * Domain Path: /languages
 * Woo: 18729:05c45f2aa466106a466de4402fff9dde
 * Copyright (c) 2014-2017 WooCommerce, StoreApps All rights reserved.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '05c45f2aa466106a466de4402fff9dde', '18729' );

/**
 * Include class having function to execute during activation & deactivation of plugin
 */
require_once( 'includes/class-sc-act-deact.php' );

/**
 * On activation
 */
register_activation_hook( __FILE__, array( 'WC_SC_Act_Deact', 'smart_coupon_activate' ) );

/**
 * On deactivation
 */
register_deactivation_hook( __FILE__, array( 'WC_SC_Act_Deact', 'smart_coupon_deactivate' ) );

if ( is_woocommerce_active() ) {

	include_once 'includes/sc-functions.php';

	if ( ! class_exists( 'WC_Smart_Coupons' ) ) {

		/**
		 * class WC_Smart_Coupons
		 *
		 * @return object of WC_Smart_Coupons having all functionality of Smart Coupons
		 */
		class WC_Smart_Coupons {

			/**
			 * Text Domain
			 */
			static $text_domain;

			/**
			 * Variable to hold instance of Smart Coupons
			 * @var $instance
			 */
			private static $instance = null;

			/**
			 * Get single instance of Smart Coupons
			 * 
			 * @return WC_Smart_Coupons Singleton object of WC_Smart_Coupons
			 */
			public static function get_instance() {
				// Check if instance is already exists      
				if ( is_null( self::$instance ) ) {
					self::$instance = new self();
				}

				return self::$instance;
			}

			/**
			 * Cloning is forbidden.
			 * @since 3.3.0
			 */
			private function __clone() {
		        wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', WC_SC_TEXT_DOMAIN ), '3.3.0' );
		    }
		     
		    /**
		     * Unserializing instances of this class is forbidden.
		     * @since 3.3.0
		     */
		    private function __wakeup() {
		        wc_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', WC_SC_TEXT_DOMAIN ), '3.3.0' );
		    }

			/**
			 * Constructor
			 */
			private function __construct() {

				$this->define_constants();
				$this->includes();

				self::$text_domain = WC_SC_TEXT_DOMAIN;

				add_option( 'woocommerce_delete_smart_coupon_after_usage', 'no' );
				add_option( 'woocommerce_smart_coupon_apply_before_tax', 'no' );
				add_option( 'woocommerce_smart_coupon_individual_use', 'no' );
				add_option( 'woocommerce_smart_coupon_show_my_account', 'yes' );

				add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_smart_coupon_valid' ), 10, 2 );
				add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'smart_coupons_is_valid_for_product' ), 10, 4 );
				add_filter( 'woocommerce_apply_individual_use_coupon', array( $this, 'smart_coupons_override_individual_use' ), 10, 3 );
				add_filter( 'woocommerce_apply_with_individual_use_coupon', array( $this, 'smart_coupons_override_with_individual_use' ), 10, 4 );

				add_action( 'wp_loaded', array( $this, 'smart_coupons_discount_total_filters' ), 20 );

				add_action( 'parse_request', array( $this, 'woocommerce_admin_coupon_search' ) );
				add_filter( 'get_search_query', array( $this, 'woocommerce_admin_coupon_search_label' ) );

				add_action( 'restrict_manage_posts', array( $this, 'woocommerce_restrict_manage_smart_coupons' ), 20 );
				add_action( 'admin_init', array( $this, 'woocommerce_export_coupons' ) );

				add_action( 'personal_options_update', array( $this, 'my_profile_update' ) );
				add_action( 'edit_user_profile_update', array( $this, 'my_profile_update' ) );

				add_filter( 'generate_smart_coupon_action', array( $this, 'generate_smart_coupon_action' ), 1, 9 );

				if ( $this->is_wc_gte_26() ) {
					// Actions used to insert a new endpoint in the WordPress.
					add_action( 'init', array( $this, 'sc_add_endpoints' ) );
				}

				add_action( 'init', array( $this, 'register_plugin_styles' ) );
				add_action( 'init', array( $this, 'load_sc_textdomain' ) );

				add_filter( 'wc_smart_coupons_export_headers', array( $this, 'wc_smart_coupons_export_headers' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'smart_coupon_styles_and_scripts' ), 20 );

				add_filter( 'is_protected_meta', array( $this, 'make_sc_meta_protected' ), 10, 3 );

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

				if ( ! $this->is_wc_gte_25() ) {
	                add_action( 'admin_notices', array( $this, 'needs_wc_25_above' ) );
	            } 

			}

			/**
			 * to handle WC compatibility related function call from appropriate class
			 *
			 * @param $function_name string
			 * @param $arguments array of arguments passed while calling $function_name
			 * @return result of function call
			 */
			public function __call( $function_name, $arguments = array() ) {

				if ( ! is_callable( 'SA_WC_Compatibility_3_0', $function_name ) ) { return;
				}

				if ( ! empty( $arguments ) ) {
					return call_user_func_array( 'SA_WC_Compatibility_3_0::' . $function_name, $arguments );
				} else {
					return call_user_func( 'SA_WC_Compatibility_3_0::' . $function_name );
				}

			}

			/**
			 * Define constants
			 */
			public function define_constants() {
				if ( ! defined( 'WC_SC_PLUGIN_FILE' ) ) {
					define( 'WC_SC_PLUGIN_FILE', __FILE__ );
				}
				if ( ! defined( 'WC_SC_PLUGIN_DIRNAME' ) ) {
					define( 'WC_SC_PLUGIN_DIRNAME', dirname( plugin_basename(__FILE__) ) );
				}
				if ( ! defined( 'WC_SC_TEXT_DOMAIN' ) ) {
					define( 'WC_SC_TEXT_DOMAIN', 'woocommerce-smart-coupons' );
				}
			}

			/**
			 * Include files
			 */
			public function includes() {

				include_once 'includes/wc-compat/version-2-5.php';
				include_once 'includes/wc-compat/version-2-6.php';
				include_once 'includes/wc-compat/version-3-0.php';
				include_once 'includes/class-sc-admin-welcome.php';
				include_once 'includes/class-sc-admin-pages.php';

				include_once 'includes/class-sc-ajax.php';
				include_once 'includes/class-sc-display-coupons.php';

				include_once 'includes/class-sc-settings.php';
				include_once 'includes/class-wpml-compatibility.php';
				include_once 'includes/class-wcs-compatibility.php';
				include_once 'includes/class-sc-shortcode.php';
				include_once 'includes/class-sc-purchase-credit.php';
				include_once 'includes/class-sc-url-coupon.php';
				include_once 'includes/class-sc-coupon-fields.php';
				include_once 'includes/class-sc-product-fields.php';
				include_once 'includes/class-sc-order-fields.php';
				include_once 'includes/class-sc-process.php';
				include_once 'includes/class-sc-global-coupons.php';
				include_once 'includes/class-sc-duplicate-coupon.php';

			}

			/**
			 * Coupon's expiration date (formatted)
			 *
			 * @param int $expiry_date
			 * @return string $expires_string formatted expiry date
			 */
			public function get_expiration_format( $expiry_date ) {

				if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
					$expiry_date = $expiry_date->getTimestamp();
				} elseif ( ! is_int( $expiry_date ) ) {
					$expiry_date = strtotime( $expiry_date );
				}

				$expiry_days = ( int ) ( ( $expiry_date - time() ) / ( 24 * 60 * 60 ) );

				if ( $expiry_days < 1 ) {

					$expires_string = __( 'Expires Today ', WC_SC_TEXT_DOMAIN );

				} elseif ( $expiry_days < 31 ) {

					$expires_string = __( 'Expires in ', WC_SC_TEXT_DOMAIN ) . $expiry_days . __( ' days', WC_SC_TEXT_DOMAIN );

				} else {

					$expires_string = __( 'Expires on ', WC_SC_TEXT_DOMAIN ) . esc_html( date_i18n( get_option( 'date_format', 'F j, Y' ), $expiry_date ) );

				}
				return $expires_string;

			}

			/**
			 * Smart Coupons textdomain
			 */
			public function load_sc_textdomain() {

				$text_domains = array( WC_SC_TEXT_DOMAIN, 'wc_smart_coupons' );

				$plugin_dirname = dirname( plugin_basename( __FILE__ ) );

				foreach ( $text_domains as $text_domain ) {

					self::$text_domain = $text_domain;

					$locale = apply_filters( 'plugin_locale', get_locale(), self::$text_domain );

					$loaded = load_textdomain( self::$text_domain, WP_LANG_DIR . '/' . $plugin_dirname . '/' . self::$text_domain . '-' . $locale . '.mo' );

					if ( ! $loaded ) {
						$loaded = load_plugin_textdomain( self::$text_domain, false, $plugin_dirname . '/languages' );
					}

					if ( $loaded ) {
						break;
					}
				}

			}

			/**
			 * Function to send e-mail containing coupon code to customer
			 *
			 * @param array   $coupon_title associative array containing receiver's details
			 * @param string  $discount_type
			 * @param int     $order_id
			 * @param array   $gift_certificate_receiver_name array of receiver's name
			 * @param string  $message_from_sender
			 * @param string  $gift_certificate_sender_name
			 * @param string  $gift_certificate_sender_email
			 * @param boolean $is_gift whether it is a gift certificate or store credit
			 */
			public function sa_email_coupon( $coupon_title, $discount_type, $order_id = '', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $is_gift = '' ) {

				$blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

				$is_send_email = get_option( 'smart_coupons_is_send_email', 'yes' );

				if ( $this->is_wc_gte_30() ) {
					$page_id = wc_get_page_id( 'shop' );
				} else {
					$page_id = woocommerce_get_page_id( 'shop' );
				}

				$url = ( get_option( 'permalink_structure' ) ) ? get_permalink( $page_id ) : get_post_type_archive_link( 'product' );

				if ( $discount_type == 'smart_coupon' && $is_gift == 'yes' ) {
					$gift_certificate_sender_name = trim( $gift_certificate_sender_name );
					$sender = ( ! empty( $gift_certificate_sender_name ) ) ? $gift_certificate_sender_name : '';
					$sender .= ( ! empty( $gift_certificate_sender_name ) ) ? ' (' : '';
					$sender .= ( ! empty( $gift_certificate_sender_email ) ) ? $gift_certificate_sender_email : '';
					$sender .= ( ! empty( $gift_certificate_sender_name ) ) ? ')' : '';
					$from = ' ' . __( 'from', WC_SC_TEXT_DOMAIN ) . ' ';
					$smart_coupon_type = __( 'Gift Card', WC_SC_TEXT_DOMAIN );
				} else {
					$from = '';
					$smart_coupon_type = __( 'Store Credit', WC_SC_TEXT_DOMAIN );
				}

				$subject_string = sprintf( __( "Congratulations! You've received a %s ", WC_SC_TEXT_DOMAIN ), ( ( $discount_type == 'smart_coupon' && ! empty( $smart_coupon_type ) ) ? $smart_coupon_type : 'coupon' ) );
				$subject_string = ( get_option( 'smart_coupon_email_subject' ) && get_option( 'smart_coupon_email_subject' ) != '' ) ? __( get_option( 'smart_coupon_email_subject' ), WC_SC_TEXT_DOMAIN ): $subject_string;
				$subject_string .= ( ! empty( $gift_certificate_sender_name ) ) ? $from . $gift_certificate_sender_name : '';

				$subject = apply_filters( 'woocommerce_email_subject_gift_certificate', sprintf( '%1$s: %2$s', $blogname, $subject_string ) );

				$all_discount_types = wc_get_coupon_types();

				foreach ( $coupon_title as $email => $coupon ) {

					$_coupon = new WC_Coupon( $coupon['code'] );

					if ( $this->is_wc_gte_30() ) {
						$_is_free_shipping = ( $_coupon->get_free_shipping() ) ? 'yes' : 'no';
						$_discount_type    = $_coupon->get_discount_type();
					} else {
						$_is_free_shipping = ( ! empty( $_coupon->free_shipping ) ) ? $_coupon->free_shipping : '';
						$_discount_type    = ( ! empty( $_coupon->discount_type ) ) ? $_coupon->discount_type : '';
					}

					$amount = $coupon['amount'];
					$coupon_code = $coupon['code'];

					switch ( $discount_type ) {

						case 'smart_coupon':
							$email_heading  = sprintf( __( 'You have received a %1$s worth %2$s ', WC_SC_TEXT_DOMAIN ), $smart_coupon_type, wc_price( $amount ) );
							break;

						case 'fixed_cart':
							$email_heading  = sprintf( __( 'You have received a coupon worth %s (on entire purchase) ', WC_SC_TEXT_DOMAIN ), wc_price( $amount ) );
							break;

						case 'fixed_product':
							$email_heading  = sprintf( __( 'You have received a coupon worth %s (for a product) ', WC_SC_TEXT_DOMAIN ), wc_price( $amount ) );
							break;

						case 'percent_product':
							$email_heading  = sprintf( __( 'You have received a coupon worth %1$s%% (for a product) ', WC_SC_TEXT_DOMAIN ), $amount );
							break;

						case 'percent':
							$email_heading  = sprintf( __( 'You have received a coupon worth %1$s%% (on entire purchase) ', WC_SC_TEXT_DOMAIN ), $amount );
							break;

						default:
							$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
							$coupon_type = apply_filters( 'wc_sc_coupon_type', $default_coupon_type, $_coupon, $all_discount_types );
							$coupon_amount = apply_filters( 'wc_sc_coupon_amount', $amount, $_coupon );
							$email_heading = sprintf(__( 'You have received %s coupon of %s', WC_SC_TEXT_DOMAIN ), $coupon_type, $coupon_amount );
							$email_heading = apply_filters( 'wc_sc_email_heading', $email_heading, $_coupon );
							break;

					}

					if ( $_is_free_shipping == 'yes' && in_array( $_discount_type, array( 'fixed_cart', 'fixed_product', 'percent_product', 'percent' ) ) ) {
						$email_heading = sprintf( __( '%1$s Free Shipping%2$s', WC_SC_TEXT_DOMAIN ), ( ( ! empty( $amount ) ) ? $email_heading . __( '&', WC_SC_TEXT_DOMAIN ) : __( 'You have received a', WC_SC_TEXT_DOMAIN ) ), ( ( empty( $amount ) ) ? __( ' coupon', WC_SC_TEXT_DOMAIN ) : '' ) );
					}

					if ( empty( $email ) ) {
						$email = $gift_certificate_sender_email;
					}

					if ( ! empty( $order_id ) ) {
						$coupon_receiver_details = get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
						if ( ! is_array( $coupon_receiver_details ) || empty( $coupon_receiver_details ) ) {
							$coupon_receiver_details = array();
						}
						$coupon_receiver_details[] = array(
								'code'      => $coupon_code,
								'amount'    => $amount,
								'email'     => $email,
								'message'   => $message_from_sender,
							);
						update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details );
					}

					if ( $is_send_email == 'yes' ) {

						ob_start();

						include( apply_filters( 'woocommerce_gift_certificates_email_template', 'templates/email.php' ) );

						$message = ob_get_clean();

						if ( ! class_exists( 'WC_Email' ) ) {
							include_once dirname( WC_PLUGIN_FILE ) . '/includes/emails/class-wc-email.php';
						}

						$mailer = new WC_Email();
						$headers = $mailer->get_headers();
						$attachments = $mailer->get_attachments();

						wc_mail( $email, $subject, $message, $headers, $attachments );

					}
				}

			}

			/**
			 * Register new endpoint to use inside My Account page.
			 */
			public function sc_add_endpoints() {

				add_rewrite_endpoint( WC_SC_Display_Coupons::$endpoint, EP_ROOT | EP_PAGES );
				$this->sc_check_if_flushed_rules();
			}

			/**
			 * To register Smart Coupons Endpoint after plugin is activated - Necessary
			 */
			public function sc_check_if_flushed_rules() {
				$sc_check_flushed_rules = get_option( 'sc_flushed_rules', 'notfound' );
				if ( $sc_check_flushed_rules == 'notfound' ) {
					flush_rewrite_rules();
					update_option( 'sc_flushed_rules', 'found' );
				}
			}

			/**
			 * Register & enqueue Smart Coupons CSS
			 */
			public function register_plugin_styles() {
				global $pagenow;

				$is_frontend = ( ! is_admin() ) ? true : false;
				$is_valid_post_page = ( ! empty( $pagenow ) && in_array( $pagenow, array( 'edit.php', 'post.php', 'post-new.php' ) ) ) ? true : false;
				$is_valid_admin_page = ( ! empty( $_GET['page'] ) && $_GET['page'] == 'woocommerce_smart_coupon_csv_import' ) ? true : false;

				if ( $is_frontend || $is_valid_admin_page || $is_valid_post_page ) {

					$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

					wp_register_style( 'smart-coupon', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/assets/css/smart-coupon' . $suffix . '.css' );
					wp_enqueue_style( 'smart-coupon' );
				}

			}

			/**
			 * Formatted coupon data
			 *
			 * @param WC_Coupon $coupon
			 * @return array $coupon_data associative array containing formatted coupon data
			 */
			public function get_coupon_meta_data( $coupon ) {
				global $woocommerce;
				$all_discount_types = wc_get_coupon_types();

				if ( $this->is_wc_gte_30() ) {
					$coupon_amount = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
					$discount_type = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				} else {
					$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
					$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				}

				$coupon_data = array();
				switch ( $discount_type ) {
					case 'smart_coupon':
						$coupon_data['coupon_type'] = __( 'Store Credit', WC_SC_TEXT_DOMAIN );
						$coupon_data['coupon_amount'] = wc_price( $coupon_amount );
						break;

					case 'fixed_cart':
						$coupon_data['coupon_type'] = __( 'Cart Discount', WC_SC_TEXT_DOMAIN );
						$coupon_data['coupon_amount'] = wc_price( $coupon_amount );
						break;

					case 'fixed_product':
						$coupon_data['coupon_type'] = __( 'Product Discount', WC_SC_TEXT_DOMAIN );
						$coupon_data['coupon_amount'] = wc_price( $coupon_amount );
						break;

					case 'percent_product':
						$coupon_data['coupon_type'] = __( 'Product Discount', WC_SC_TEXT_DOMAIN );
						$coupon_data['coupon_amount'] = $coupon_amount . '%';
						break;

					case 'percent':
						$coupon_data['coupon_type'] = ( $this->is_wc_gte_30() ) ? __( 'Percentage Discount', WC_SC_TEXT_DOMAIN ) : __( 'Cart Discount', WC_SC_TEXT_DOMAIN );
						$coupon_data['coupon_amount'] = $coupon_amount . '%';
						break;

					default:
						$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
						$coupon_data['coupon_type'] = apply_filters( 'wc_sc_coupon_type', $default_coupon_type, $coupon, $all_discount_types );
						$coupon_data['coupon_amount'] = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
						break;

				}
				return $coupon_data;
			}

			/**
			 * Update coupon's email id with the updation of customer profile
			 *
			 * @param int $user_id
			 */
			public function my_profile_update( $user_id ) {

				global $wpdb;

				if ( current_user_can( 'edit_user', $user_id ) ) {

					$current_user = get_userdata( $user_id );

					$old_customers_email_id = $current_user->data->user_email;

					if ( isset( $_POST['email'] ) && $_POST['email'] != $old_customers_email_id ) {

						$query = "SELECT post_id
									FROM $wpdb->postmeta
									WHERE meta_key = 'customer_email'
									AND meta_value LIKE  '%$old_customers_email_id%'
									AND post_id IN ( SELECT ID
														FROM $wpdb->posts
														WHERE post_type =  'shop_coupon')";
						$result = $wpdb->get_col( $query );

						if ( ! empty( $result ) ) {

							foreach ( $result as $post_id ) {

								$coupon_meta = get_post_meta( $post_id, 'customer_email', true );

								foreach ( $coupon_meta as $key => $email_id ) {

									if ( $email_id == $old_customers_email_id ) {

										$coupon_meta[ $key ] = $_POST['email'];
									}
								}

								update_post_meta( $post_id, 'customer_email', $coupon_meta );

							} //end foreach
						}
					}
				}
			}

			/**
			 * Method to check whether 'pick_price_from_product' is set or not
			 *
			 * @param array $coupons array of coupon codes
			 * @return boolean
			 */
			public function is_coupon_amount_pick_from_product_price( $coupons ) {

				if ( empty( $coupons ) ) {
					return false;
				}

				foreach ( $coupons as $coupon_code ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( $this->is_wc_gte_30() ) {
						$coupon_id     = $coupon->get_id();
						$discount_type = $coupon->get_discount_type();
					} else {
						$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}
					if ( $discount_type == 'smart_coupon' && get_post_meta( $coupon_id, 'is_pick_price_of_product', true ) == 'yes' ) {
						return true;
					}
				}
				return false;
			}

			/**
			 * Function to find if order is discounted with store credit
			 * 
			 * @param  WC_Order  $order
			 * @return boolean
			 */
			public function is_order_contains_store_credit( $order = null ) {

				if ( empty( $order ) ) {
					return false;
				}

				$coupons = $order->get_items( 'coupon' );

				foreach ( $coupons as $item_id => $item ) {
					$code = trim( $item['name'] );
					$coupon = new WC_Coupon( $code );
					if ( $this->is_wc_gte_30() ) {
						$discount_type = $coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}
					if ( $discount_type == 'smart_coupon' ) {
						return true;
					}
				}

				return false;

			}

			/**
			 * function to validate smart coupon for product
			 *
			 * @param bool            $valid
			 * @param WC_Product|null $product
			 * @param WC_Coupon|null  $coupon
			 * @param array|null      $values
			 * @return bool $valid
			 */
			public function smart_coupons_is_valid_for_product( $valid, $product = null, $coupon = null, $values = null ) {

				if ( empty( $product ) || empty( $coupon ) ) { return $valid;
				}

				if ( $this->is_wc_gte_30() ) {
					$product_id                         = ( ! empty( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
					$product_parent_id                  = ( ! empty( $product ) && is_callable( array( $product, 'get_parent_id' ) ) ) ? $product->get_parent_id() : 0;
					$product_variation_id               = ( ! empty( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
					$discount_type                      = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
					$coupon_product_ids                 = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : '';
					$coupon_product_categories          = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : '';
					$coupon_excluded_product_ids        = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_ids' ) ) ) ? $coupon->get_excluded_product_ids() : '';
					$coupon_excluded_product_categories = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_categories' ) ) ) ? $coupon->get_excluded_product_categories() : '';
					$is_exclude_sale_items              = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_exclude_sale_items' ) ) ) ? ( ( $coupon->get_exclude_sale_items() ) ? 'yes' : 'no' ) : '';
				} else {
					$product_id                         = ( ! empty( $product->id ) ) ? $product->id : 0;
					$product_parent_id                  = ( ! empty( $product ) && is_callable( array( $product, 'get_parent' ) ) ) ? $product->get_parent() : 0;
					$product_variation_id               = ( ! empty( $product->variation_id ) ) ? $product->variation_id : 0;
					$discount_type                      = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$coupon_product_ids                 = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
					$coupon_product_categories          = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
					$coupon_excluded_product_ids        = ( ! empty( $coupon->exclude_product_ids ) ) ? $coupon->exclude_product_ids : array();
					$coupon_excluded_product_categories = ( ! empty( $coupon->exclude_product_categories ) ) ? $coupon->exclude_product_categories : array();
					$is_exclude_sale_items              = ( ! empty( $coupon->exclude_sale_items ) ) ? $coupon->exclude_sale_items : '';
				}

				if ( $discount_type == 'smart_coupon' ) {

					$product_cats = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

					// Specific products get the discount
					if ( sizeof( $coupon_product_ids ) > 0 ) {

						if ( in_array( $product_id, $coupon_product_ids ) || ( isset( $product_variation_id ) && in_array( $product_variation_id, $coupon_product_ids ) ) || in_array( $product_parent_id, $coupon_product_ids ) ) {
							$valid = true;
						}

						// Category discounts
					} elseif ( sizeof( $coupon_product_categories ) > 0 ) {

						if ( sizeof( array_intersect( $product_cats, $coupon_product_categories ) ) > 0 ) {
							$valid = true;
						}
					} else {
						// No product ids - all items discounted
						$valid = true;
					}

					// Specific product ID's excluded from the discount
					if ( sizeof( $coupon_excluded_product_ids ) > 0 ) {
						if ( in_array( $product_id, $coupon_excluded_product_ids ) || ( isset( $product_variation_id ) && in_array( $product_variation_id, $coupon_excluded_product_ids ) ) || in_array( $product_parent_id, $coupon_excluded_product_ids ) ) {
							$valid = false;
						}
					}

					// Specific categories excluded from the discount
					if ( sizeof( $coupon_excluded_product_categories ) > 0 ) {
						if ( sizeof( array_intersect( $product_cats, $coupon_excluded_product_categories ) ) > 0 ) {
							$valid = false;
						}
					}

					// Sale Items excluded from discount
					if ( $is_exclude_sale_items == 'yes' ) {
						$product_ids_on_sale = wc_get_product_ids_on_sale();

						if ( in_array( $product_id, $product_ids_on_sale, true ) || ( isset( $product_variation_id ) && in_array( $product_variation_id, $product_ids_on_sale, true ) ) || in_array( $product_parent_id, $product_ids_on_sale, true ) ) {
							$valid = false;
						}
					}
				}

				return $valid;
			}

			/**
			 * Function to keep valid coupons when individual use coupon is applied
			 * @param  array   				$coupons_to_keep
			 * @param  WC_Coupons|boolean 	$the_coupon
			 * @param  array   				$applied_coupons
			 * @return array
			 */
			public function smart_coupons_override_individual_use( $coupons_to_keep = array(), $the_coupon = false, $applied_coupons = array() ) {

				if ( $this->is_wc_gte_30() ) {
					foreach ( $applied_coupons as $code ) {
						$coupon = new WC_Coupon( $code );
						if ( 'smart_coupon' == $coupon->get_discount_type() && ! $coupon->get_individual_use() && ! in_array( $code, $coupons_to_keep ) ) {
							$coupons_to_keep[] = $code;
						}
					}
				}

				return $coupons_to_keep;
			}

			/**
			 * Force apply store credit even if the individual coupon already exists in cart
			 * @param  boolean 				$is_apply        
			 * @param  WC_Coupons|boolean 	$the_coupon      
			 * @param  WC_Coupons|boolean 	$applied_coupon  
			 * @param  array   				$applied_coupons 
			 * @return boolean              
			 */
			public function smart_coupons_override_with_individual_use( $is_apply = false, $the_coupon = false, $applied_coupon = false, $applied_coupons = array() ) {

				if ( $this->is_wc_gte_30() ) {
					if ( ! $is_apply && 'smart_coupon' == $the_coupon->get_discount_type() && ! $the_coupon->get_individual_use() ) {
						$is_apply = true;
					}
				}

				return $is_apply;
			}

			/**
			 * Function to add appropriate discount total filter
			 */
			public function smart_coupons_discount_total_filters() {
				if ( WC_SC_WCS_Compatibility::is_cart_contains_subscription() && WC_SC_WCS_Compatibility::is_wcs_gte( '2.0.0' ) ) {
					add_filter( 'woocommerce_subscriptions_calculated_total', array( $this, 'smart_coupons_discounted_totals' ) );
				} else {
					add_filter( 'woocommerce_calculated_total', array( $this, 'smart_coupons_discounted_totals' ), 10, 2 );
					add_filter( 'woocommerce_order_get_total', array( $this, 'smart_coupons_order_discounted_total' ), 10, 2 );
				}
			}

			/**
			 * function to apply smart coupons discount
			 *
			 * @param float          $total
			 * @param WC_Cart        $cart
			 * @return float $total
			 */
			public function smart_coupons_discounted_totals( $total = 0, $cart = null ) {

				if ( empty( $total ) ) { return $total;
				}

				$cart_contains_subscription = WC_SC_WCS_Compatibility::is_cart_contains_subscription();

				if ( $cart_contains_subscription ) {

					$calculation_type = WC_Subscriptions_Cart::get_calculation_type();

					if ( $calculation_type == 'recurring_total' ) {
						return $total;
					}
				}

				$applied_coupons = WC()->cart->get_applied_coupons();

				if ( ! empty( $applied_coupons ) ) {
					foreach ( $applied_coupons as $code ) {
						$coupon = new WC_Coupon( $code );
						if ( $this->is_wc_gte_30() ) {
							$coupon_amount             = $coupon->get_amount();
							$discount_type             = $coupon->get_discount_type();
							$coupon_code               = $coupon->get_code();
							$coupon_product_ids        = $coupon->get_product_ids();
							$coupon_product_categories = $coupon->get_product_categories();
						} else {
							$coupon_amount             = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
							$discount_type             = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$coupon_code               = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
							$coupon_product_ids        = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : array();
							$coupon_product_categories = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : array();
						}

						if ( $coupon->is_valid() && $discount_type == 'smart_coupon' ) {

							$calculated_total = $total;

							if ( sizeof( $coupon_product_ids ) > 0 || sizeof( $coupon_product_categories ) > 0 ) {

								$discount = 0;
								$line_totals = 0;
								$line_taxes = 0;
								$discounted_products = array();

								foreach ( WC()->cart->cart_contents as $cart_item_key => $product ) {

									if ( $discount >= $coupon_amount ) { break;
									}

									$product_cats = wp_get_post_terms( $product['product_id'], 'product_cat', array( 'fields' => 'ids' ) );

									if ( sizeof( $coupon_product_categories ) > 0 ) {

										$continue = false;

										if ( ! empty( $cart_item_key ) && ! empty( $discounted_products ) && is_array( $discounted_products ) && in_array( $cart_item_key, $discounted_products, true ) ) {
											$continue = true;
										}

										if ( ! $continue && sizeof( array_intersect( $product_cats, $coupon_product_categories ) ) > 0 ) {

											$discounted_products[] = ( ! empty( $cart_item_key ) ) ? $cart_item_key : '';

											$line_totals += $product['line_total'];
											$line_taxes += $product['line_tax'];

										}
									}

									if ( sizeof( $coupon_product_ids ) > 0 ) {

										$continue = false;

										if ( ! empty( $cart_item_key ) && ! empty( $discounted_products ) && is_array( $discounted_products ) && in_array( $cart_item_key, $discounted_products, true ) ) {
											$continue = true;
										}

										if ( ! $continue && in_array( $product['product_id'], $coupon_product_ids ) || in_array( $product['variation_id'], $coupon_product_ids ) || in_array( $product['data']->get_parent(), $coupon_product_ids ) ) {

											$discounted_products[] = ( ! empty( $cart_item_key ) ) ? $cart_item_key : '';

											$line_totals += $product['line_total'];
											$line_taxes += $product['line_tax'];

										}
									}
								}

								$calculated_total = round( ($line_totals + $line_taxes), wc_get_price_decimals() );

							}
							$discount = min( $calculated_total, $coupon_amount );
							$total = $total - $discount;

							if ( $cart_contains_subscription ) {
								if ( WC_SC_WCS_Compatibility::is_wcs_gte( '2.0.10' ) ) {
									if ( $this->is_wc_greater_than( '3.1.2' ) ) {
										$coupon_discount_totals = WC()->cart->get_coupon_discount_totals();
										if ( empty( $coupon_discount_totals ) || ! is_array( $coupon_discount_totals ) ) {
											$coupon_discount_totals = array();
										}
										if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
											$coupon_discount_totals[ $coupon_code ] = $discount;
										} else {
											$coupon_discount_totals[ $coupon_code ] += $discount;
										}
										WC()->cart->set_coupon_discount_totals( $coupon_discount_totals );
									} else {
										if ( empty( WC()->cart->coupon_discount_amounts ) ) {
											WC()->cart->coupon_discount_amounts = array();
										}
										if ( empty( WC()->cart->coupon_discount_amounts[ $coupon_code ] ) ) {
											WC()->cart->coupon_discount_amounts[ $coupon_code ] = $discount;
										} else {
											WC()->cart->coupon_discount_amounts[ $coupon_code ] += $discount;
										}
									}
								} elseif ( WC_SC_WCS_Compatibility::is_wcs_gte( '2.0.0' ) ) {
									WC_Subscriptions_Coupon::increase_coupon_discount_amount( WC()->cart, $coupon_code, $discount );
								} else {
									WC_Subscriptions_Cart::increase_coupon_discount_amount( $coupon_code, $discount );
								}
							} else {
								if ( $this->is_wc_greater_than( '3.1.2' ) ) {
									$coupon_discount_totals = WC()->cart->get_coupon_discount_totals();
									if ( empty( $coupon_discount_totals ) || ! is_array( $coupon_discount_totals ) ) {
										$coupon_discount_totals = array();
									}
									if ( empty( $coupon_discount_totals[ $coupon_code ] ) ) {
										$coupon_discount_totals[ $coupon_code ] = $discount;
									} else {
										$coupon_discount_totals[ $coupon_code ] += $discount;
									}
									WC()->cart->set_coupon_discount_totals( $coupon_discount_totals );
								} else {
									if ( empty( WC()->cart->coupon_discount_amounts ) ) {
										WC()->cart->coupon_discount_amounts = array();
									}
									if ( empty( WC()->cart->coupon_discount_amounts[ $coupon_code ] ) ) {
										WC()->cart->coupon_discount_amounts[ $coupon_code ] = $discount;
									} else {
										WC()->cart->coupon_discount_amounts[ $coupon_code ] += $discount;
									}
								}
							}

							if ( isset( WC()->session->reload_checkout ) ) {		// reload_checkout is triggered when customer is registered from checkout
								unset( WC()->cart->smart_coupon_credit_used );	// reset store credit used data for re-calculation
							}

							if ( empty( WC()->cart->smart_coupon_credit_used ) ) {
								WC()->cart->smart_coupon_credit_used = array();
							}
							if ( empty( WC()->cart->smart_coupon_credit_used[ $coupon_code ] ) || ( $cart_contains_subscription && ( $calculation_type == 'combined_total' || $calculation_type == 'sign_up_fee_total' ) ) ) {
								WC()->cart->smart_coupon_credit_used[ $coupon_code ] = $discount;
							} else {
								WC()->cart->smart_coupon_credit_used[ $coupon_code ] += $discount;
							}
						}
					}
				}

				return $total;
			}

			/**
			 * Apply store credit discount in order during recalculation
			 * 
			 * @param  float 		$total
			 * @param  WC_Order  	$order
			 * @return float 		$total
			 */
			public function smart_coupons_order_discounted_total( $total = 0, $order = null ) {

				$is_proceed = check_ajax_referer( 'calc-totals', 'security', false );

				if ( ! $is_proceed ) {
					return $total;
				}

				$called_by = ( ! empty( $_POST['action'] ) ) ? $_POST['action'] : '';

				if ( $called_by != 'woocommerce_calc_line_taxes' ) {
					return $total;
				}

				if ( empty( $order ) ) {
					return $total;
				}
				
				$coupons = ( is_object( $order ) && is_callable( array( $order, 'get_items' ) ) ) ? $order->get_items( 'coupon' ) : array();

				if ( ! empty( $coupons ) ) {
					foreach ( $coupons as $coupon ) {
						$code = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
						if ( empty( $code ) ) {
							continue;
						}
						$_coupon = new WC_Coupon( $code );
						$discount_type = ( is_object( $_coupon ) && is_callable( array( $_coupon, 'get_discount_type' ) ) ) ? $_coupon->get_discount_type() : '';
						if ( ! empty( $discount_type ) && 'smart_coupon' == $discount_type ) {
							$discount = ( is_object( $coupon ) && is_callable( array( $coupon, 'get_discount' ) ) ) ? $coupon->get_discount() : 0;
							$applied_discount = min( $total, $discount );
							$total = $total - $applied_discount;
						}
					}
				}

				return $total;
			}

			/**
			 * Function to return validity of Store Credit / Gift Certificate
			 *
			 * @param boolean   $valid
			 * @param WC_Coupon $coupon
			 * @return boolean $valid TRUE if smart coupon valid, FALSE otherwise
			 */
			public function is_smart_coupon_valid( $valid, $coupon ) {
				global $woocommerce;

				if ( $this->is_wc_gte_30() ) {
					$coupon_amount = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
					$discount_type = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
					$coupon_code   = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
				} else {
					$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
					$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				}

				if ( $discount_type != 'smart_coupon' ) { return $valid;
				}

				$applied_coupons = WC()->cart->get_applied_coupons();

				if ( empty( $applied_coupons ) || ( ! empty( $applied_coupons ) && ! in_array( $coupon_code, $applied_coupons ) ) ) { return $valid;
				}

				if ( $valid && $coupon_amount <= 0 ) {
					WC()->cart->remove_coupon( $coupon_code );
					wc_add_notice( sprintf( __( 'Coupon removed. There is no credit remaining in %s.', WC_SC_TEXT_DOMAIN ), '<strong>' . $coupon_code . '</strong>' ), 'error' );
					return false;
				}

				return $valid;
			}

			/**
			 * Locate template for Smart Coupons
			 *
			 * @param string $template_name
			 * @param mixed  $template
			 * @return mixed $template
			 */
			public function locate_template_for_smart_coupons( $template_name = '', $template = '' ) {

				$default_path   = untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/';

				$plugin_base_dir = substr( plugin_basename( __FILE__ ), 0, strpos( plugin_basename( __FILE__ ), '/' ) + 1 );

				// Look within passed path within the theme - this is priority
				$template = locate_template(
					array(
						'woocommerce/' . $plugin_base_dir . $template_name,
						$plugin_base_dir . $template_name,
						$template_name,
					)
				);

				// Get default template
				if ( ! $template ) {
					$template = $default_path . $template_name;
				}

				return $template;
			}

			/**
			 * Check whether credit is sent or not
			 *
			 * @param string    $email_id
			 * @param WC_Coupon $coupon
			 * @return boolean
			 */
			public function is_credit_sent( $email_id, $coupon ) {

				global $smart_coupon_codes;

				if ( isset( $smart_coupon_codes[ $email_id ] ) && count( $smart_coupon_codes[ $email_id ] ) > 0 ) {
					if ( $this->is_wc_gte_30() ) {
						$coupon_id = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
					} else {
						$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					}
					foreach ( $smart_coupon_codes[ $email_id ] as $generated_coupon_details ) {
						if ( $generated_coupon_details['parent'] == $coupon_id ) { return true;
						}
					}
				}

				return false;

			}

			/**
			 * Generate unique string to be used as coupon code. Also add prefix or suffix if already set
			 *
			 * @param string    $email
			 * @param WC_Coupon $coupon
			 * @return string $unique_code
			 */
			public function generate_unique_code( $email = '', $coupon = '' ) {
				$unique_code = ( ! empty( $email ) ) ? strtolower( uniqid( substr( preg_replace( '/[^a-z0-9]/i', '', sanitize_title( $email ) ), 0, 5 ) ) ) : strtolower( uniqid() );

				if ( $this->is_wc_gte_30() ) {
					$coupon_id = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
				} else {
					$coupon_id = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				}

				if ( ! empty( $coupon_id ) && get_post_meta( $coupon_id, 'auto_generate_coupon', true ) == 'yes' ) {
					 $prefix = get_post_meta( $coupon_id, 'coupon_title_prefix', true );
					 $suffix = get_post_meta( $coupon_id, 'coupon_title_suffix', true );
					 $unique_code = $prefix . $unique_code . $suffix;
				}

				return $unique_code;
			}

			/**
			 * Function for generating Gift Certificate
			 *
			 * @param mixed     $email
			 * @param float     $amount
			 * @param int       $order_id
			 * @param WC_Coupon $coupon
			 * @param string    $discount_type
			 * @param array     $gift_certificate_receiver_name
			 * @param string    $message_from_sender
			 * @param string    $gift_certificate_sender_name
			 * @param string    $gift_certificate_sender_email
			 * @return array of generated coupon details
			 */
			public function generate_smart_coupon( $email, $amount, $order_id = '', $coupon = '', $discount_type = 'smart_coupon', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '' ) {
				return apply_filters( 'generate_smart_coupon_action', $email, $amount, $order_id, $coupon, $discount_type, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email );
			}

			/**
			 * Function for generating Gift Certificate
			 *
			 * @param mixed     $email
			 * @param float     $amount
			 * @param int       $order_id
			 * @param WC_Coupon $coupon
			 * @param string    $discount_type
			 * @param array     $gift_certificate_receiver_name
			 * @param string    $message_from_sender
			 * @param string    $gift_certificate_sender_name
			 * @param string    $gift_certificate_sender_email
			 * @return array $smart_coupon_codes associative array containing generated coupon details
			 */
			public function generate_smart_coupon_action( $email, $amount, $order_id = '', $coupon = '', $discount_type = 'smart_coupon', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '' ) {

				if ( $email == '' ) { return false;
				}

				global $smart_coupon_codes;

				if ( $this->is_wc_gte_30() ) {
					$coupon_id                          = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : 0;
					$is_free_shipping                   = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? ( ( $coupon->get_free_shipping() ) ? 'yes' : 'no' ) : '';
					$discount_type                      = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
					$expiry_date                        = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_date_expires' ) ) ) ? $coupon->get_date_expires() : '';
					$coupon_product_ids                 = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_product_ids' ) ) ) ? $coupon->get_product_ids() : '';
					$coupon_product_categories          = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_product_categories' ) ) ) ? $coupon->get_product_categories() : '';
					$coupon_excluded_product_ids        = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_ids' ) ) ) ? $coupon->get_excluded_product_ids() : '';
					$coupon_excluded_product_categories = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_excluded_product_categories' ) ) ) ? $coupon->get_excluded_product_categories() : '';
					$coupon_minimum_amount              = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_minimum_amount' ) ) ) ? $coupon->get_minimum_amount() : '';
					$coupon_maximum_amount              = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_maximum_amount' ) ) ) ? $coupon->get_maximum_amount() : '';
					$coupon_usage_limit                 = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_usage_limit' ) ) ) ? $coupon->get_usage_limit() : '';
					$coupon_usage_limit_per_user        = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_usage_limit_per_user' ) ) ) ? $coupon->get_usage_limit_per_user() : '';
					$coupon_limit_usage_to_x_items      = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_limit_usage_to_x_items' ) ) ) ? $coupon->get_limit_usage_to_x_items() : '';
					$is_exclude_sale_items              = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_exclude_sale_items' ) ) ) ? ( ( $coupon->get_exclude_sale_items() ) ? 'yes' : 'no' ) : '';
					$is_individual_use                  = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_individual_use' ) ) ) ? ( ( $coupon->get_individual_use() ) ? 'yes' : 'no' ) : '';
				} else {
					$coupon_id                          = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
					$is_free_shipping                   = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
					$discount_type                      = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					$expiry_date                        = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
					$coupon_product_ids                 = ( ! empty( $coupon->product_ids ) ) ? $coupon->product_ids : '';
					$coupon_product_categories          = ( ! empty( $coupon->product_categories ) ) ? $coupon->product_categories : '';
					$coupon_excluded_product_ids        = ( ! empty( $coupon->exclude_product_ids ) ) ? $coupon->exclude_product_ids : '';
					$coupon_excluded_product_categories = ( ! empty( $coupon->exclude_product_categories ) ) ? $coupon->exclude_product_categories : '';
					$coupon_minimum_amount              = ( ! empty( $coupon->minimum_amount ) ) ? $coupon->minimum_amount : '';
					$coupon_maximum_amount              = ( ! empty( $coupon->maximum_amount ) ) ? $coupon->maximum_amount : '';
					$coupon_usage_limit                 = ( ! empty( $coupon->usage_limit ) ) ? $coupon->usage_limit : '';
					$coupon_usage_limit_per_user        = ( ! empty( $coupon->usage_limit_per_user ) ) ? $coupon->usage_limit_per_user : '';
					$coupon_limit_usage_to_x_items      = ( ! empty( $coupon->limit_usage_to_x_items ) ) ? $coupon->limit_usage_to_x_items : '';
					$is_exclude_sale_items              = ( ! empty( $coupon->exclude_sale_items ) ) ? $coupon->exclude_sale_items : '';
					$is_individual_use                  = ( ! empty( $coupon->individual_use ) ) ? $coupon->individual_use : '';
				}

				if ( ! is_array( $email ) ) {
					$emails = array( $email => 1 );
				} else {
					$temp_email = get_post_meta( $order_id, 'temp_gift_card_receivers_emails', true );
					if ( ! empty( $temp_email ) && count( $temp_email ) > 0 ) {
						$email = $temp_email;
					}
					$emails = ( ! empty( $coupon_id ) ) ? array_count_values( $email[ $coupon_id ] ) : array();
				}

				if ( ! empty( $order_id ) ) {
					$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
				}

				foreach ( $emails as $email_id => $qty ) {

					if ( $this->is_credit_sent( $email_id, $coupon ) ) { continue;
					}

					$smart_coupon_code = $this->generate_unique_code( $email_id, $coupon );

					$coupon_post = ( ! empty( $coupon_id ) ) ? get_post( $coupon_id ) : new stdClass();

					$smart_coupon_args = array(
										'post_title'    => strtolower( $smart_coupon_code ),
										'post_excerpt'	=> ( ! empty( $coupon_post->post_excerpt ) ) ? $coupon_post->post_excerpt : '',
										'post_content'  => '',
										'post_status'   => 'publish',
										'post_author'   => 1,
										'post_type'     => 'shop_coupon',
									);

					$smart_coupon_id = wp_insert_post( $smart_coupon_args );

					$type                           = ( ! empty( $discount_type ) ) ? $discount_type: 'smart_coupon';
					$individual_use                 = ( ! empty( $is_individual_use ) ) ?  $is_individual_use : get_option( 'woocommerce_smart_coupon_individual_use' );
					$minimum_amount                 = ( ! empty( $coupon_minimum_amount ) ) ?  $coupon_minimum_amount : '';
					$maximum_amount                 = ( ! empty( $coupon_maximum_amount ) ) ?  $coupon_maximum_amount : '';
					$product_ids                    = ( ! empty( $coupon_product_ids ) ) ?  implode( ',', $coupon_product_ids ) : '';
					$exclude_product_ids            = ( ! empty( $coupon_excluded_product_ids ) ) ?  implode( ',', $coupon_excluded_product_ids ) : '';
					$usage_limit                    = ( ! empty( $coupon_usage_limit ) ) ?  $coupon_usage_limit : '';
					$usage_limit_per_user           = ( ! empty( $coupon_usage_limit_per_user ) ) ?  $coupon_usage_limit_per_user : '';
					$limit_usage_to_x_items         = ( ! empty( $coupon_limit_usage_to_x_items ) ) ?  $coupon_limit_usage_to_x_items : '';
					$sc_coupon_validity             = ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'sc_coupon_validity', true ) : '';
					
					if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
						$expiry_date = $expiry_date->getTimestamp();
					} elseif ( ! is_int( $expiry_date ) ) {
						$expiry_date = strtotime( $expiry_date );
					} 
					
					if ( ! empty( $coupon_id ) && ! empty( $sc_coupon_validity ) ) {
						$is_parent_coupon_expired = ( ! empty( $expiry_date ) && ( $expiry_date < time() ) ) ? true : false;
						if ( ! $is_parent_coupon_expired ) {
							$validity_suffix = get_post_meta( $coupon_id, 'validity_suffix', true );
							$expiry_date = strtotime( "+$sc_coupon_validity $validity_suffix" );
						}
					}
					
					$expiry_date 					= ( ! empty( $expiry_date ) ) ?  date( 'Y-m-d', intval( $expiry_date ) ) : '';
					$free_shipping                  = ( ! empty( $is_free_shipping ) ) ?  $is_free_shipping : 'no';
					$product_categories             = ( ! empty( $coupon_product_categories ) ) ?  $coupon_product_categories : array();
					$exclude_product_categories     = ( ! empty( $coupon_excluded_product_categories ) ) ?  $coupon_excluded_product_categories : array();

					update_post_meta( $smart_coupon_id, 'discount_type', $type );
					update_post_meta( $smart_coupon_id, 'coupon_amount', $amount );
					update_post_meta( $smart_coupon_id, 'individual_use', $individual_use );
					update_post_meta( $smart_coupon_id, 'minimum_amount', $minimum_amount );
					update_post_meta( $smart_coupon_id, 'maximum_amount', $maximum_amount );
					update_post_meta( $smart_coupon_id, 'product_ids', $product_ids );
					update_post_meta( $smart_coupon_id, 'exclude_product_ids', $exclude_product_ids );
					update_post_meta( $smart_coupon_id, 'usage_limit', $usage_limit );
					update_post_meta( $smart_coupon_id, 'usage_limit_per_user', $usage_limit_per_user );
					update_post_meta( $smart_coupon_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );
					update_post_meta( $smart_coupon_id, 'expiry_date', $expiry_date );

					$is_disable_email_restriction = ( ! empty( $coupon_id ) ) ? get_post_meta( $coupon_id, 'sc_disable_email_restriction', true ) : '';
					if ( empty( $is_disable_email_restriction ) || $is_disable_email_restriction == 'no' ) {
						update_post_meta( $smart_coupon_id, 'customer_email', array( $email_id ) );
					}

					if ( ! $this->is_wc_gte_30() ) {
						$apply_before_tax = ( ! empty( $coupon->apply_before_tax ) ) ?  $coupon->apply_before_tax : 'no';
						update_post_meta( $smart_coupon_id, 'apply_before_tax', $apply_before_tax );
					}

					update_post_meta( $smart_coupon_id, 'free_shipping', $free_shipping );
					update_post_meta( $smart_coupon_id, 'product_categories', $product_categories );
					update_post_meta( $smart_coupon_id, 'exclude_product_categories', $exclude_product_categories );
					update_post_meta( $smart_coupon_id, 'exclude_sale_items', $is_exclude_sale_items );
					update_post_meta( $smart_coupon_id, 'generated_from_order_id', $order_id );

					$generated_coupon_details = array(
						'parent'    => ( ! empty( $coupon_id ) ) ? $coupon_id : 0,
						'code'      => $smart_coupon_code,
						'amount'    => $amount,
					);

					$smart_coupon_codes[ $email_id ][] = $generated_coupon_details;

					if ( ! empty( $order_id ) ) {
						$is_gift = get_post_meta( $order_id, 'is_gift', true );
					} else {
						$is_gift = 'no';
					}

					if ( is_array( $email ) && ! empty( $coupon_id ) && isset( $email[ $coupon_id ] ) ) {
						$message_index = array_search( $email_id, $email[ $coupon_id ], true );
						if ( $message_index !== false && isset( $receivers_messages[ $coupon_id ][ $message_index ] ) && ! empty( $receivers_messages[ $coupon_id ][ $message_index ] ) ) {
							$message_from_sender = $receivers_messages[ $coupon_id ][ $message_index ];
							unset( $email[ $coupon_id ][ $message_index ] );
							update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
						}
					}
					$this->sa_email_coupon( array( $email_id => $generated_coupon_details ), $type, $order_id, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $is_gift );

				}

				return $smart_coupon_codes;

			}

			/**
			 * Funtion to show search result based on email id included in customer email
			 *
			 * @param object $wp
			 */
			public function woocommerce_admin_coupon_search( $wp ) {
				global $pagenow, $wpdb;

				if ( 'edit.php' != $pagenow ) { return;
				}
				if ( ! isset( $wp->query_vars['s'] ) ) { return;
				}
				if ( $wp->query_vars['post_type'] != 'shop_coupon' ) { return;
				}

				$e = substr( $wp->query_vars['s'], 0, 6 );

				if ( 'Email:' == substr( $wp->query_vars['s'], 0, 6 ) ) {

					$email = trim( substr( $wp->query_vars['s'], 6 ) );

					if ( ! $email ) { return;
					}

					$post_ids = $wpdb->get_col( 'SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE meta_key="customer_email" AND meta_value LIKE "%' . $email . '%"; ' );

					if ( ! $post_ids ) { return;
					}

					unset( $wp->query_vars['s'] );

					$wp->query_vars['post__in'] = $post_ids;

					$wp->query_vars['email'] = $email;
				}

			}

			/**
			 * Function to show label of the search result on email
			 *
			 * @param string $query
			 * @return string $query
			 */
			public function woocommerce_admin_coupon_search_label( $query ) {
					global $pagenow, $typenow, $wp;

				if ( 'edit.php' != $pagenow ) { return $query;
				}
				if ( $typenow != 'shop_coupon' ) { return $query;
				}

					$s = get_query_var( 's' );
				if ( $s ) { return $query;
				}

					$email = get_query_var( 'email' );

				if ( $email ) {

					$post_type = get_post_type_object( $wp->query_vars['post_type'] );
					return sprintf( __( "[%1$s with email of %2$s]", WC_SC_TEXT_DOMAIN ), $post_type->labels->singular_name, $email );
				}

					return $query;
			}

			/**
			 * Add button to export coupons on Coupons admin page
			 */
			public function woocommerce_restrict_manage_smart_coupons() {
				global $typenow, $wp_query,$wp,$woocommerce_smart_coupon;

				if ( $typenow != 'shop_coupon' ) {
					return;
				}

				if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
					$background_position_x = 0.9;
					$background_size = 1.4;
					$padding_left = 2.5;
				} else {
					$background_position_x = 0.4;
					$background_size = 1.5;
					$padding_left = 2.2;
				}
				?>
					<style type="text/css">
						span.dashicons {
							vertical-align: text-top;
							margin-right: 2px;
						}
						button#export_coupons {
							padding: 0px 5px;
						}
				    </style>
					<div class="alignright" style="margin-top: 1px;" >
						<?php
						if ( ! empty( $_SERVER['QUERY_STRING'] ) ) {
							echo '<input type="hidden" name="sc_export_query_args" value="' . $_SERVER['QUERY_STRING'] . '">';
						}
						?>
						<button type="submit" class="button" id="export_coupons" name="export_coupons" value="<?php _e( 'Export', WC_SC_TEXT_DOMAIN ); ?>"><span class="dashicons dashicons-upload"></span><?php _e( 'Export', WC_SC_TEXT_DOMAIN ); ?></button>
					</div>
				<?php
			}


			/**
			 * Export coupons
			 */
			public function woocommerce_export_coupons() {
				global $typenow, $wp_query,$wp,$post;

				if ( isset( $_GET['export_coupons'] ) ) {

					$args = array(
					'post_status' => '',
									'post_type' => '',
									'm' => '',
									'posts_per_page' => -1,
									'fields' => 'ids',
						);

					if ( ! empty( $_REQUEST['sc_export_query_args'] ) ) {
						parse_str( $_REQUEST['sc_export_query_args'], $sc_args );
					}
					$args = array_merge( $args, $sc_args );

					if ( isset( $_GET['coupon_type'] ) && $_GET['coupon_type'] != '' ) {
						$args['meta_query'] = array(
									array(
											'key'   => 'discount_type',
											'value'     => $_GET['coupon_type'],
									),
							);
					}

					foreach ( $args as $key => $value ) {
						if ( array_key_exists( $key, $_GET ) ) {
							$args[ $key ] = $_GET[ $key ];
						}
					}

					if ( $args['post_status'] == 'all' ) {
						$args['post_status'] = array( 'publish', 'draft', 'pending', 'private','future' );

					}

					$query = new WP_Query( $args );

					$post_ids = $query->posts;

					$this->export_coupon( '', $_GET, $post_ids );
				}
			}

			/**
			 * Generate coupon code
			 *
			 * @param array $post
			 * @param array $get
			 * @param array $post_ids
			 * @param array $coupon_postmeta_headers
			 * @return array $data associative array of generated coupon
			 */
			public function generate_coupons_code( $post, $get, $post_ids, $coupon_postmeta_headers = array() ) {
				global $wpdb, $wp, $wp_query;

				$data = array();
				if ( ! empty( $post ) && isset( $post['generate_and_import'] ) ) {

					$customer_emails = array();
					$unique_code = '';
					if ( ! empty( $post['customer_email'] ) ) {
						$emails = explode( ',', $post['customer_email'] );
						if ( is_array( $emails ) && count( $emails ) > 0 ) {
							for ( $j = 1; $j <= $post['no_of_coupons_to_generate']; $j++ ) {
								$customer_emails[ $j ] = ( isset( $emails[ $j -1 ] ) && is_email( $emails[ $j -1 ] ) ) ? $emails[ $j -1 ] : '';
							}
						}
					}

					$all_discount_types = wc_get_coupon_types();
					$generated_codes = array();

					for ( $i = 1; $i <= $post['no_of_coupons_to_generate']; $i++ ) {
						$customer_email = ( ! empty( $customer_emails[ $i ] ) ) ? $customer_emails[ $i ] : '';
						$unique_code = $this->generate_unique_code( $customer_email );
						if ( ! empty( $generated_codes ) && in_array( $unique_code, $generated_codes ) ) {
						 	$max = ( $post['no_of_coupons_to_generate'] * 10 ) - 1;
						 	do {
						 		$unique_code_temp = $unique_code . mt_rand( 0, $max );
						 	} while ( in_array( $unique_code_temp, $generated_codes ) );
						 	$unique_code = $unique_code_temp;
						}
						$generated_codes[] = $unique_code;
						$code = $post['coupon_title_prefix'] . $unique_code . $post['coupon_title_suffix'];

						$data[ $i ]['post_title'] = strtolower( $code );

						$discount_type = ( ! empty( $post['discount_type'] ) ) ? $post['discount_type'] : 'percent';

						if ( ! empty( $all_discount_types[ $discount_type ] ) ) {
							$data[ $i ]['discount_type'] = $all_discount_types[ $discount_type ];
						} else {
							if ( $this->is_wc_gte_30() ) {
								$data[ $i ]['discount_type'] = 'Percentage discount';
							} else {
								$data[ $i ]['discount_type'] = 'Cart % Discount';
							}
						}

						if ( $this->is_wc_gte_30() ) {
							$post['product_ids'] = ( ! empty( $post['product_ids'] ) ) ? ( ( is_array( $post['product_ids'] ) ) ? implode( ',', $post['product_ids'] ) : $post['product_ids'] ) : '';
							$post['exclude_product_ids'] = ( ! empty( $post['exclude_product_ids'] ) ) ? ( ( is_array( $post['exclude_product_ids'] ) ) ? implode( ',', $post['exclude_product_ids'] ) : $post['exclude_product_ids'] ) : '';
						}

						$data[ $i ]['coupon_amount']                 = $post['coupon_amount'];
						$data[ $i ]['individual_use']                = ( isset( $post['individual_use'] ) ) ? 'yes' : 'no';
						$data[ $i ]['product_ids']                   = ( isset( $post['product_ids'] ) ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $post['product_ids'] ) : '';
						$data[ $i ]['exclude_product_ids']           = ( isset( $post['exclude_product_ids'] ) ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $post['exclude_product_ids'] ) : '';
						$data[ $i ]['usage_limit']                   = ( isset( $post['usage_limit'] ) ) ? $post['usage_limit'] : '';
						$data[ $i ]['usage_limit_per_user']          = ( isset( $post['usage_limit_per_user'] ) ) ? $post['usage_limit_per_user'] : '';
						$data[ $i ]['limit_usage_to_x_items']        = ( isset( $post['limit_usage_to_x_items'] ) ) ? $post['limit_usage_to_x_items'] : '';
						if ( empty( $post['expiry_date'] ) && ! empty( $post['sc_coupon_validity'] ) && ! empty( $post['validity_suffix'] ) ) {
							$data[ $i ]['expiry_date']                   = date( 'Y-m-d', strtotime( '+' . $post['sc_coupon_validity'] . ' ' . $post['validity_suffix'] ) );
						} else {
							$data[ $i ]['expiry_date']                   = $post['expiry_date'];
						}
						$data[ $i ]['free_shipping']                = ( isset( $post['free_shipping'] ) ) ? 'yes' : 'no';
						$data[ $i ]['product_categories']           = ( isset( $post['product_categories'] ) ) ? implode( '|', $post['product_categories'] ) : '';
						$data[ $i ]['exclude_product_categories']   = ( isset( $post['exclude_product_categories'] ) ) ? implode( '|', $post['exclude_product_categories'] ) : '';
						$data[ $i ]['exclude_sale_items']           = ( isset( $post['exclude_sale_items'] ) ) ? 'yes' : 'no';
						$data[ $i ]['minimum_amount']               = ( isset( $post['minimum_amount'] ) ) ? $post['minimum_amount'] : '';
						$data[ $i ]['maximum_amount']               = ( isset( $post['maximum_amount'] ) ) ? $post['maximum_amount'] : '';
						$data[ $i ]['customer_email']               = ( ! empty( $customer_emails ) ) ? $customer_emails[ $i ] : '';
						$data[ $i ]['sc_coupon_validity']           = ( isset( $post['sc_coupon_validity'] ) ) ? $post['sc_coupon_validity']: '';
						$data[ $i ]['validity_suffix']              = ( isset( $post['validity_suffix'] ) ) ? $post['validity_suffix']: '';
						$data[ $i ]['is_pick_price_of_product']     = ( isset( $post['is_pick_price_of_product'] ) ) ? 'yes': 'no';
						$data[ $i ]['sc_disable_email_restriction'] = ( isset( $post['sc_disable_email_restriction'] ) ) ? 'yes': 'no';
						$data[ $i ]['sc_is_visible_storewide']      = ( isset( $post['sc_is_visible_storewide'] ) ) ? 'yes': 'no';
						$data[ $i ]['coupon_title_prefix']          = ( isset( $post['coupon_title_prefix'] ) ) ? $post['coupon_title_prefix']: '';
						$data[ $i ]['coupon_title_suffix']          = ( isset( $post['coupon_title_suffix'] ) ) ? $post['coupon_title_suffix']: '';
						$data[ $i ]['post_status']                  = 'publish';

						 $data[ $i ] = apply_filters( 'sc_generate_coupon_meta', $data[ $i ], $post );

					}
				}

				if ( ! empty( $get ) && isset( $get['export_coupons'] ) ) {

					$query_to_fetch_data = " SELECT p.ID,
												  p.post_title,
												  p.post_excerpt,
												  p.post_status,
												  p.post_parent,
												  p.menu_order,
												  DATE_FORMAT(p.post_date,'%d-%m-%Y %h:%i') AS post_date,
												  GROUP_CONCAT(pm.meta_key order by pm.meta_id SEPARATOR '###') AS coupon_meta_key,
												  GROUP_CONCAT(pm.meta_value order by pm.meta_id SEPARATOR '###') AS coupon_meta_value
												  FROM {$wpdb->prefix}posts as p JOIN {$wpdb->prefix}postmeta as pm ON (p.ID = pm.post_id
												  AND pm.meta_key IN ('" . implode( "','", array_keys( $coupon_postmeta_headers ) ) . "') )
												  WHERE p.ID IN (" . implode( ',', $post_ids ) . ')
                                                  GROUP BY p.id  ORDER BY p.id

                                            ';

					$results = $wpdb->get_results( $query_to_fetch_data, ARRAY_A );

					foreach ( $results as $result ) {

						$coupon_meta_key = explode( '###', $result['coupon_meta_key'] );
						$coupon_meta_value = explode( '###', $result['coupon_meta_value'] );

						unset( $result['coupon_meta_key'] );
						unset( $result['coupon_meta_value'] );

						$coupon_meta_key_value = array_combine( $coupon_meta_key,$coupon_meta_value );

						$coupon_data = array_merge( $result,$coupon_meta_key_value );

						foreach ( $coupon_data as $key => $value ) {
							$id = $coupon_data['ID'];
							if ( $key == 'product_ids' || $key == 'exclude_product_ids' ) {
								$data[ $id ][ $key ] = ( isset( $coupon_data[ $key ] ) ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $coupon_data[ $key ] ) : '';
							} elseif ( $key == 'product_categories' || $key == 'exclude_product_categories' ) {
								$data[ $id ][ $key ] = ( isset( $coupon_data[ $key ] ) ) ? implode( '|', maybe_unserialize( $coupon_data[ $key ] ) ) : '';
							} elseif ( $key != 'ID' ) {
								$data[ $id ][ $key ] = (is_serialized( $value )) ? implode( ',',maybe_unserialize( $value ) ) : $value;
							}
						}
					}
				}

				return $data;

			}

			/**
			 * Export coupon CSV data
			 *
			 * @param array $columns_header
			 * @param array $data
			 * @return array $file_data
			 */
			public function export_coupon_csv( $columns_header, $data ) {

				$getfield = '';

				foreach ( $columns_header as $key => $value ) {
						$getfield .= $key . ',';
				}

				$fields = substr_replace( $getfield, '', -1 );

				$each_field = array_keys( $columns_header );

				$csv_file_name = get_bloginfo( 'name' ) . gmdate( 'd-M-Y_H_i_s' ) . '.csv';

				foreach ( (array) $data as $row ) {
					for ( $i = 0; $i < count( $columns_header ); $i++ ) {
						if ( $i == 0 ) { $fields .= "\n";
						}

						if ( array_key_exists( $each_field[ $i ], $row ) ) {
							$row_each_field = $row[ $each_field[ $i ] ];
						} else {
							$row_each_field = '';
						}

						$array = str_replace( array( "\n", "\n\r", "\r\n", "\r" ), "\t", $row_each_field );

						$array = str_getcsv( $array , ',', '"' , '\\' );

						$str = ( $array && is_array( $array ) ) ? implode( ', ', $array ) : '';
						$fields .= '"' . $str . '",';
					}
					$fields = substr_replace( $fields, '', -1 );
				}
				$upload_dir = wp_upload_dir();

				$file_data = array();
				$file_data['wp_upload_dir'] = $upload_dir['path'] . '/';
				$file_data['file_name'] = $csv_file_name;
				$file_data['file_content'] = $fields;

				return $file_data;
			}

			/**
			 * Smart Coupons export headers
			 *
			 * @param array $coupon_postmeta_headers existing
			 * @return array $coupon_postmeta_headers including additional headers
			 */
			public function wc_smart_coupons_export_headers( $coupon_postmeta_headers = array() ) {

				$sc_postmeta_headers = array(
				'sc_coupon_validity'         => __( 'Coupon Validity', WC_SC_TEXT_DOMAIN ),
											'validity_suffix'              => __( 'Validity Suffix', WC_SC_TEXT_DOMAIN ),
											'auto_generate_coupon'         => __( 'Auto Generate Coupon', WC_SC_TEXT_DOMAIN ),
											'coupon_title_prefix'          => __( 'Coupon Title Prefix', WC_SC_TEXT_DOMAIN ),
											'coupon_title_suffix'          => __( 'Coupon Title Suffix', WC_SC_TEXT_DOMAIN ),
											'is_pick_price_of_product'     => __( 'Is Pick Price of Product', WC_SC_TEXT_DOMAIN ),
											'sc_disable_email_restriction' => __( 'Disable Email Restriction', WC_SC_TEXT_DOMAIN ),
											'sc_is_visible_storewide'      => __( 'Coupon Is Visible Storewide', WC_SC_TEXT_DOMAIN ),
											);

				return array_merge( $coupon_postmeta_headers, $sc_postmeta_headers );

			}

			/**
			 * Write to file after exporting
			 *
			 * @param array $post
			 * @param array $get
			 * @param array $post_ids
			 */
			public function export_coupon( $post, $get, $post_ids ) {

				$coupon_posts_headers = array(
				'post_title'    => __( 'Coupon Code',WC_SC_TEXT_DOMAIN ),
												'post_excerpt'  => __( 'Post Excerpt',WC_SC_TEXT_DOMAIN ),
												'post_status'   => __( 'Post Status',WC_SC_TEXT_DOMAIN ),
												'post_parent'   => __( 'Post Parent',WC_SC_TEXT_DOMAIN ),
												'menu_order'    => __( 'Menu Order',WC_SC_TEXT_DOMAIN ),
												'post_date'     => __( 'Post Date', WC_SC_TEXT_DOMAIN ),
												);

				$coupon_postmeta_headers = apply_filters( 'wc_smart_coupons_export_headers',
					array(
					'discount_type'              => __( 'Discount Type',WC_SC_TEXT_DOMAIN ),
														'coupon_amount'                 => __( 'Coupon Amount',WC_SC_TEXT_DOMAIN ),
														'free_shipping'                 => __( 'Free shipping',WC_SC_TEXT_DOMAIN ),
														'expiry_date'                   => __( 'Expiry date',WC_SC_TEXT_DOMAIN ),
														'minimum_amount'                => __( 'Minimum Spend',WC_SC_TEXT_DOMAIN ),
														'maximum_amount'                => __( 'Maximum Spend',WC_SC_TEXT_DOMAIN ),
														'individual_use'                => __( 'Individual USe',WC_SC_TEXT_DOMAIN ),
														'exclude_sale_items'            => __( 'Exclude Sale Items',WC_SC_TEXT_DOMAIN ),
														'product_ids'                   => __( 'Product IDs',WC_SC_TEXT_DOMAIN ),
														'exclude_product_ids'           => __( 'Exclude product IDs',WC_SC_TEXT_DOMAIN ),
														'product_categories'            => __( 'Product categories',WC_SC_TEXT_DOMAIN ),
														'exclude_product_categories'    => __( 'Exclude Product categories',WC_SC_TEXT_DOMAIN ),
														'customer_email'                => __( 'Customer Email',WC_SC_TEXT_DOMAIN ),
														'usage_limit'                   => __( 'Usage Limit',WC_SC_TEXT_DOMAIN ),
														'usage_limit_per_user'          => __( 'Usage Limit Per User',WC_SC_TEXT_DOMAIN ),
														'limit_usage_to_x_items'        => __( 'Limit Usage to X Items',WC_SC_TEXT_DOMAIN ),
				) );

				$column_headers = array_merge( $coupon_posts_headers, $coupon_postmeta_headers );

				if ( ! empty( $post ) ) {
					$data = $this->generate_coupons_code( $post, '', '', array() );
				} elseif ( ! empty( $get ) ) {
					$data = $this->generate_coupons_code( '', $get, $post_ids, $coupon_postmeta_headers );
				}

					$file_data = $this->export_coupon_csv( $column_headers, $data );

				if ( ( isset( $post['generate_and_import'] ) && ! empty( $post['smart_coupons_generate_action'] ) && $post['smart_coupons_generate_action'] == 'sc_export_and_import' ) || isset( $get['export_coupons'] ) ) {

					if ( ob_get_level() ) {
						$levels = ob_get_level();
						for ( $i = 0; $i < $levels; $i++ ) {
							@ob_end_clean();
						}
					} else {
						@ob_end_clean();
					}
					nocache_headers();
					header( 'X-Robots-Tag: noindex, nofollow', true );
					header( 'Content-Type: text/x-csv; charset=UTF-8' );
					header( 'Content-Description: File Transfer' );
					header( 'Content-Transfer-Encoding: binary' );
					header( 'Content-Disposition: attachment; filename="' . sanitize_file_name( $file_data['file_name'] ) . '";' );

					echo $file_data['file_content'];
					exit;
				} else {

					// Create CSV file
					$csv_folder     = $file_data['wp_upload_dir'];
					$filename       = str_replace( array( '\'', '"', ',', ';', '<', '>', '/', ':' ), '', $file_data['file_name'] );
					$CSVFileName    = $csv_folder . $filename;
					$fp = fopen( $CSVFileName, 'w' );
					file_put_contents( $CSVFileName, $file_data['file_content'] );
					fclose( $fp );

					return $CSVFileName;
				}

			}

			/**
			 * function to enqueue additional styles & scripts for Smart Coupons
			 */
			public function smart_coupon_styles_and_scripts() {
				global $post, $pagenow, $typenow;

				if ( ! empty( $pagenow ) ) {
					$show_css_for_smart_coupon_tab = false;
					if ( $pagenow == 'edit.php' && ! empty( $_GET['post_type'] ) && $_GET['post_type'] == 'shop_coupon' ) {
						$show_css_for_smart_coupon_tab = true;
					}
					if ( $pagenow == 'admin.php' && ! empty( $_GET['page'] ) && $_GET['page'] == 'woocommerce_smart_coupon_csv_import' ) {
						$show_css_for_smart_coupon_tab = true;
					}
					if ( $show_css_for_smart_coupon_tab ) {
						?>
						<style type="text/css">
							div#smart_coupons_tabs h2 {
								margin-bottom: 10px;
							}
						</style>
						<?php
					}
				}

				if ( ! empty( $post->post_type ) && $post->post_type == 'product' ) {
					if ( wp_script_is( 'select2' ) ) {
						wp_localize_script( 'select2', 'smart_coupons_select_params', array(
							'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', WC_SC_TEXT_DOMAIN ),
							'ajax_url'                  => admin_url( 'admin-ajax.php' ),
							'search_products_nonce'     => wp_create_nonce( 'search-products' ),
							'search_customers_nonce'    => wp_create_nonce( 'search-customers' ),
						) );
					}
				}

			}

			/**
			 * Make meta data of this plugin, protected
			 *
			 * @param bool   $protected
			 * @param string $meta_key
			 * @param string $meta_type
			 * @return bool $protected
			 */
			public function make_sc_meta_protected( $protected, $meta_key, $meta_type ) {
	            $sc_meta = array(
	                                'auto_generate_coupon',
									'coupon_sent',
									'coupon_title_prefix',
									'coupon_title_suffix',
									'generated_from_order_id',
									'gift_receiver_email',
									'gift_receiver_message',
									'is_gift',
									'is_pick_price_of_product',
									'sc_called_credit_details',
									'sc_coupon_receiver_details',
									'sc_coupon_validity',
									'sc_disable_email_restriction',
									'sc_is_visible_storewide',
									'send_coupons_on_renewals',
									'smart_coupons_contribution',
									'temp_gift_card_receivers_emails',
									'validity_suffix',
	                            );
	            if ( in_array( $meta_key, $sc_meta, true ) ) {
	                return true;
	            }
	            return $protected;
	        }

	        /**
			 * Get the order from the PayPal 'Custom' variable.
			 *
			 * Credit: WooCommerce
			 * 
			 * @param  string $raw_custom JSON Data passed back by PayPal
			 * @return bool|WC_Order object
			 */
			public function get_paypal_order( $raw_custom ) {

				if ( ! class_exists( 'WC_Gateway_Paypal' ) ) {
					include_once( WC()->plugin_path() . '/includes/gateways/paypal/class-wc-gateway-paypal.php' );
				}
				// We have the data in the correct format, so get the order.
				if ( ( $custom = json_decode( $raw_custom ) ) && is_object( $custom ) ) {
					$order_id  = $custom->order_id;
					$order_key = $custom->order_key;

				// Fallback to serialized data if safe. This is @deprecated in 2.3.11
				} elseif ( preg_match( '/^a:2:{/', $raw_custom ) && ! preg_match( '/[CO]:\+?[0-9]+:"/', $raw_custom ) && ( $custom = maybe_unserialize( $raw_custom ) ) ) {
					$order_id  = $custom[0];
					$order_key = $custom[1];

				// Nothing was found.
				} else {
					WC_Gateway_Paypal::log( 'Error: Order ID and key were not found in "custom".' );
					return false;
				}

				if ( ! $order = wc_get_order( $order_id ) ) {
					// We have an invalid $order_id, probably because invoice_prefix has changed.
					$order_id = wc_get_order_id_by_order_key( $order_key );
					$order    = wc_get_order( $order_id );
				}

				if ( $this->is_wc_gte_30() ) {
					$_order_key = ( ! empty( $order ) && is_callable( array( $order, 'get_order_key' ) ) ) ? $order->get_order_key() : '';
				} else {
					$_order_key = ( ! empty( $order->order_key ) ) ? $order->order_key : '';
				}

				if ( ! $order || $_order_key !== $order_key ) {
					WC_Gateway_Paypal::log( 'Error: Order Keys do not match.' );
					return false;
				}

				return $order;
			}

			/**
			 * function to add more action on plugins page
			 *
			 * @param array $links
			 * @return array $links
			 */
			public function plugin_action_links( $links ) {
	            $action_links = array(
	                'about' => '<a href="' . admin_url( 'admin.php?page=sc-about' ) . '" title="' . esc_attr( __( 'Know Smart Coupons', WC_SC_TEXT_DOMAIN ) ) . '">' . __( 'About', WC_SC_TEXT_DOMAIN ) . '</a>',
	            );

	            return array_merge( $action_links, $links );
	        }

			/**
	         * Show notice on admin panel about minimum required version of WooCommerce
	         */
	        public function needs_wc_25_above() {
	        	$plugin_data = self::get_smart_coupons_plugin_data();
				$plugin_name = $plugin_data['Name'];
	            ?>
	            <div class="updated error">
	                <p><?php
	                    echo sprintf(__( '%1$s %2$s is active but it will only work with WooCommerce 2.5+. %3$s.', WC_SC_TEXT_DOMAIN ), '<strong>' . __( 'Important', WC_SC_TEXT_DOMAIN ) . ':</strong>', $plugin_name, '<a href="'.admin_url('plugins.php?plugin_status=upgrade').'" target="_blank" >' . __( 'Please update WooCommerce to the latest version', WC_SC_TEXT_DOMAIN ) . '</a>' );
	                ?></p>
	            </div>
	            <?php
	        }

	        /**
			 * function to fetch plugin's data
			 */
			public static function get_smart_coupons_plugin_data() {
				return get_plugin_data( __FILE__ );
			}

		}//end class

	} // End class exists check

	/**
	 * function to initiate Smart Coupons & its functionality
	 */
	function initialize_smart_coupons() {
		$GLOBALS['woocommerce_smart_coupon'] = WC_Smart_Coupons::get_instance();
	}
	add_action( 'woocommerce_loaded', 'initialize_smart_coupons' );

} // End woocommerce active check
