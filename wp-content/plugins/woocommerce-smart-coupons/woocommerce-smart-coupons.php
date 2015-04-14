<?php
/*
Plugin Name: WooCommerce Smart Coupons
Plugin URI: http://woothemes.com/woocommerce
Description: <strong>WooCommerce Smart Coupons</strong> lets customers buy gift certificates, store credits or coupons easily. They can use purchased credits themselves or gift to someone else.
Version: 2.8.6
Author: Store Apps
Author URI: http://www.storeapps.org/
Copyright (c) 2012, 2013, 2014, 2015 Store Apps All rights reserved.
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '05c45f2aa466106a466de4402fff9dde', '18729' );

/**
 * On activation
 */
register_activation_hook ( __FILE__, 'smart_coupon_activate' );

/**
 * Database changes required for Smart Coupons
 * 
 * Add option 'smart_coupon_email_subject' if not exists
 * Enable 'Auto Generation' for Store Credit (discount_type: 'smart_coupon') not having any customer_email
 * Disable 'apply_before_tax' for all Store Credit (discount_type: 'smart_coupon')
 */
function smart_coupon_activate() {
	global $wpdb, $blog_id;

	if (is_multisite()) {
		$blog_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}", 0);
	} else {
		$blog_ids = array($blog_id);
	}

	if ( !get_option( 'smart_coupon_email_subject' ) ) {
		add_option( 'smart_coupon_email_subject' );
	}

	foreach ($blog_ids as $blog_id) {

		if (( file_exists(WP_PLUGIN_DIR . '/woocommerce/woocommerce.php') ) && ( is_plugin_active('woocommerce/woocommerce.php') )) {

			$wpdb_obj = clone $wpdb;
			$wpdb->blogid = $blog_id;
			$wpdb->set_prefix($wpdb->base_prefix);

			$query = "SELECT postmeta.post_id FROM {$wpdb->prefix}postmeta as postmeta WHERE postmeta.meta_key = 'discount_type' AND postmeta.meta_value = 'smart_coupon' AND postmeta.post_id IN
					(SELECT p.post_id FROM {$wpdb->prefix}postmeta AS p WHERE p.meta_key = 'customer_email' AND p.meta_value = 'a:0:{}') ";

			$results = $wpdb->get_col($query);

			foreach ($results as $result) {
				update_post_meta($result, 'auto_generate_coupon', 'yes');
			}
			// To disable apply_before_tax option for Gift Certificates / Store Credit.
			$post_id_tax_query = "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = 'discount_type' AND meta_value = 'smart_coupon'";

			$tax_post_ids = $wpdb->get_col($post_id_tax_query);

			foreach ( $tax_post_ids as $tax_post_id ) {
				update_post_meta($tax_post_id, 'apply_before_tax', 'no');
			}

			$wpdb = clone $wpdb_obj;
		}
	}
}

if ( is_woocommerce_active() ) {

	/**
	 * For PHP version lower than 5.3.0
	 */ 
	if (!function_exists('str_getcsv')) {
		function str_getcsv($input, $delimiter = ",", $enclosure = '"', $escape = "\\") {
			$fiveMBs = 5 * 1024 * 1024;
			$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');
			fputs($fp, $input);
			rewind($fp);

			$data = fgetcsv($fp, 0, $delimiter, $enclosure); //  $escape only got added in 5.3.0

			fclose($fp);
			return $data;
		}
	}

	if ( ! class_exists( 'WC_Smart_Coupons' ) ) {

		/**
		 * class WC_Smart_Coupons
		 * 
		 * @return object of WC_Smart_Coupons having all functionality of Smart Coupons
		 * 
		 */
		class WC_Smart_Coupons {

			/**
			 * @var $sc_general_settings Array of SMart Coupons General Settings
			 */
			var $sc_general_settings;
			
			/**
			 * Constructor
			 */
			public function __construct() {

				include_once 'classes/class-wc-compatibility.php';
				include_once 'classes/class-wc-compatibility-2-3.php';
				
				add_action( 'woocommerce_product_options_general_product_data', array( $this, 'woocommerce_product_options_coupons') );
				add_action( 'save_post', array( $this, 'woocommerce_process_product_meta_coupons'), 10, 2 );
				add_action( 'wp_ajax_sc_json_search_coupons', array(  $this, 'sc_json_search_coupons' ) );

				add_action( 'woocommerce_order_status_completed', array( $this, 'sa_add_coupons') );
				add_action( 'woocommerce_order_status_completed', array( $this, 'coupons_used'), 19 );
				add_action( 'woocommerce_order_status_processing', array( $this, 'sa_add_coupons'), 19 );
				add_action( 'woocommerce_order_status_processing', array( $this, 'coupons_used'), 19 );
				add_action( 'woocommerce_order_status_refunded', array( $this, 'sa_remove_coupons'), 19 );
				add_action( 'woocommerce_order_status_cancelled', array( $this, 'sa_remove_coupons'), 19 );
				add_action( 'woocommerce_order_status_on-hold', array( $this, 'update_smart_coupon_balance'), 19 );
				add_action( 'update_smart_coupon_balance', array( $this, 'update_smart_coupon_balance') );

				add_option('woocommerce_delete_smart_coupon_after_usage', 'yes');
				add_option('woocommerce_smart_coupon_apply_before_tax', 'no');
				add_option('woocommerce_smart_coupon_individual_use', 'no');
				add_option('woocommerce_smart_coupon_show_my_account', 'yes');

				$this->sc_general_settings = array(
					array(
						'name'              => __( 'Store Credit / Gift Certificate', 'wc_smart_coupons' ),
						'type'              => 'title',
						'desc'              => __('The following options are specific to Gift / Credit.', 'wc_smart_coupons'),
						'id'                => 'smart_coupon_options'
					),
					array(
						'name'              => __('Default Gift / Credit options', 'wc_smart_coupons'),
						'desc'              => __('Show Credit on My Account page.', 'wc_smart_coupons'),
						'id'                => 'woocommerce_smart_coupon_show_my_account',
						'type'              => 'checkbox',
						'default'           => 'yes',
						'checkboxgroup'     => 'start'
					),
					array(
						'desc'              => __('Delete Gift / Credit, when credit is used up.', 'wc_smart_coupons'),
						'id'                => 'woocommerce_delete_smart_coupon_after_usage',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => ''
					),
					array(
						'desc'              => __('Individual use', 'wc_smart_coupons'),
						'id'                => 'woocommerce_smart_coupon_individual_use',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => ''
					),
					array(                     
						'name'              => __( "E-mail subject", 'wc_smart_coupons' ),
						'desc'              => __( "This text will be used as subject for e-mails to be sent to customers. In case of empty value following message will be displayed <br/><b>Congratulations! You've received a coupon</b>", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_email_subject',
						'type'              => 'textarea',
						'desc_tip'          =>  true,
						'css'               => 'min-width:300px;'
					 ),
					 array(                     
						'name'              => __( "Product page text", 'wc_smart_coupons' ),
						'desc'              => __( "Text to display associated coupon details on the product shop page. In case of empty value following message will be displayed <br/><b>By purchasing this product, you will get following coupon(s):</b> ", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_product_page_text',
						'type'              => 'text',
						'desc_tip'          =>  true,
						'css'               => 'min-width:300px;'
					 ),  
					 array(
						'name'              => __( "Cart/Checkout page text", 'wc_smart_coupons' ),
						'desc'              => __( "Text to display as title of 'Available Coupons List' on Cart and Checkout page. In case of empty value following message will be displayed <br/><b>Available Coupons (Click on the coupon to use it)</b> ", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_cart_page_text',
						'type'              => 'text',
						'desc_tip'          =>  true,
						'css'               => 'min-width:300px;'
					 ),
					 array(                    
						'name'              => __( "My Account page text", 'wc_smart_coupons' ),
						'desc'              => __( "Text to display as title of available coupons on My Account page. In case of empty value following message will be displayed <br/><b>Store Credit Available</b>", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_myaccount_page_text',
						'type'              => 'text',
						'desc_tip'          =>  true,
						'css'               => 'min-width:300px;'
					),
					array(                    
						'name'              => __( "Purchase Credit text", 'wc_smart_coupons' ),
						'desc'              => __( "Text for purchasing 'Store Credit of any amount' product. In case of empty value following message will be displayed <br/><b>Purchase Credit worth</b>", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_store_gift_page_text',
						'type'              => 'text',
						'desc_tip'          =>  true,
						'css'               => 'min-width:300px;'
					),
					array(
						'name'              => __( "Title for Store Credit receiver's details form", 'wc_smart_coupons' ),
						'desc'              => __( "Text to display as title of Receiver's details form. In case of empty value following message will be displayed <br/><b>Store Credit Receiver Details</b>", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_gift_certificate_form_page_text',
						'type'              => 'text',
						'desc_tip'          =>  true,
						'css'               => 'min-width:300px;'
					),
					array(
						'name'              => __( "Additional information about form", 'wc_smart_coupons' ),
						'desc'              => __( "Text to display as additional information below 'Receiver's detail Form Title'. In case of empty value following message will be displayed <br/><b>Enter email address and optional message for Gift Card receiver</b>", 'wc_smart_coupons' ),
						'id'                => 'smart_coupon_gift_certificate_form_details_text',
						'type'              => 'text',
						'css'               => 'min-width:300px;',
						'desc_tip'          =>  true
						
					),
					array(
						'type'              => 'sectionend',
						'id'                => 'smart_coupon_options'
					)
				);

				add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_smart_coupon_discount_type') );
				add_filter( 'woocommerce_coupon_is_valid', array( $this, 'is_smart_coupon_valid'), 10, 2 );

				add_action( 'woocommerce_new_order', array( $this, 'smart_coupons_contribution') );

				if ( $this->is_wc_gte_23() ) {
					add_filter( 'woocommerce_coupon_is_valid_for_product', array( $this, 'smart_coupons_is_valid_for_product' ), 10, 4 );
					add_filter( 'woocommerce_calculated_total', array( $this, 'smart_coupons_discounted_totals' ), 10, 2 );
					add_filter( 'woocommerce_get_order_item_totals', array( $this, 'add_smart_coupons_discount_details' ), 10, 2 );
					add_action( 'woocommerce_admin_order_totals_after_tax', array( $this, 'admin_order_totals_add_smart_coupons_discount_details' ) );
				} else {
					add_action( 'woocommerce_calculate_totals', array( $this, 'apply_smart_coupon_to_cart') );
				}

				add_action( 'woocommerce_before_my_account', array( $this, 'show_smart_coupon_balance') );
				add_action( 'woocommerce_email_after_order_table', array( $this, 'show_store_credit_balance'), 10, 3 );

				add_action( 'woocommerce_update_options_general', array( $this, 'save_smart_coupon_admin_settings'));

				add_action( 'woocommerce_after_add_to_cart_button', array(  $this, 'show_attached_gift_certificates' ) );
				add_action( 'woocommerce_checkout_after_customer_details', array(  $this, 'gift_certificate_receiver_detail_form' ) );
				add_action( 'woocommerce_before_checkout_process', array(  $this, 'verify_gift_certificate_receiver_details' ) );
				add_action( 'woocommerce_new_order', array(  $this, 'add_gift_certificate_receiver_details_in_order' ) );

				add_action( 'woocommerce_after_cart_table', array(  $this, 'show_available_coupons_after_cart_table' ) );
				add_action( 'woocommerce_before_checkout_form', array(  $this, 'show_available_coupons_before_checkout_form' ), 11 );

				add_filter( 'post_row_actions', array(  $this,'woocommerce_duplicate_coupon_link_row'), 1, 2 );

				add_action( 'admin_action_duplicate_coupon', array(  $this,'woocommerce_duplicate_coupon_action') );

				add_action( 'parse_request', array(  $this,'woocommerce_admin_coupon_search' ) );
				add_filter( 'get_search_query', array(  $this,'woocommerce_admin_coupon_search_label' ) );

				add_action( 'admin_menu', array( $this, 'woocommerce_coupon_admin_menu') );
				add_action( 'admin_init', array( $this, 'woocommerce_coupon_admin_init') );
				add_action( 'admin_notices', array( $this, 'woocommerce_show_import_message') );

				if ( $this->is_wc_gte_21() ) {

					add_filter( 'woocommerce_general_settings', array(  $this, 'smart_coupons_admin_settings' ) );
					add_action( 'woocommerce_coupon_options_usage_restriction', array(  $this, 'sc_woocommerce_coupon_options_usage_restriction' ) );
					add_filter( 'woocommerce_cart_item_price', array(  $this, 'woocommerce_cart_item_price_html' ), 10, 3 );

				} else {

					add_action( 'woocommerce_settings_digital_download_options_after', array(  $this, 'smart_coupon_admin_settings') );  
					add_filter( 'woocommerce_cart_item_price_html', array(  $this, 'woocommerce_cart_item_price_html' ), 10, 3 );
				
				}

				if ( $this->is_wc_greater_than( '2.1.2' ) ) {
					add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'cart_totals_smart_coupons_label' ), 10, 2 );
				}

				add_action( 'woocommerce_coupon_options', array( $this, 'woocommerce_smart_coupon_options') );
				add_action( 'save_post', array( $this, 'woocommerce_process_smart_coupon_meta'), 10, 2 );

				add_action( 'woocommerce_single_product_summary', array( $this, 'call_for_credit_form') );
				add_filter( 'woocommerce_is_purchasable', array( $this, 'make_product_purchasable'), 10, 2 );
				add_action( 'woocommerce_before_calculate_totals', array( $this, 'override_price_before_calculate_totals') );
				
				add_action( 'woocommerce_after_shop_loop_item', array( $this, 'remove_add_to_cart_button_from_shop_page') );

				if ( !function_exists( 'is_plugin_active' ) ) {
					if ( ! defined('ABSPATH') ) {
						include_once ('../../../wp-load.php');
					}
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				if( is_plugin_active( 'woocommerce-gateway-paypal-express/woocommerce-gateway-paypal-express.php' ) ) {
					add_action( 'woocommerce_ppe_checkout_order_review', array(  $this, 'gift_certificate_receiver_detail_form' ) );
					add_action( 'woocommerce_ppe_do_payaction', array(  $this, 'ppe_save_called_credit_details_in_order' ) );
				}
				if( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
					add_filter( 'woocommerce_subscriptions_renewal_order_items', array(  $this, 'sc_modify_renewal_order' ), 10, 5 );
				}
				
				add_action( 'restrict_manage_posts', array( $this, 'woocommerce_restrict_manage_smart_coupons'), 20 );
				add_action( 'admin_init', array( $this,'woocommerce_export_coupons') );
				
				add_action( 'personal_options_update', array(  $this, 'my_profile_update' ) );
				add_action( 'edit_user_profile_update', array(  $this, 'my_profile_update' ) );

				add_action( 'woocommerce_checkout_order_processed', array(  $this, 'save_called_credit_details_in_order' ), 10, 2 );
				add_action( 'woocommerce_add_order_item_meta', array(  $this, 'save_called_credit_details_in_order_item_meta' ), 10, 2 );
				add_filter( 'woocommerce_add_cart_item_data', array(  $this, 'call_for_credit_cart_item_data' ), 10, 3 );
				add_filter( 'woocommerce_add_to_cart_validation', array(  $this, 'sc_woocommerce_add_to_cart_validation' ), 10, 6 );
				add_action( 'woocommerce_add_to_cart', array(  $this, 'save_called_credit_in_session' ), 10, 6 );
								
				add_filter( 'generate_smart_coupon_action', array(  $this, 'generate_smart_coupon_action' ), 1, 9 );
				add_action( 'wp_ajax_smart_coupons_json_search', array( $this, 'smart_coupons_json_search') );

				add_action( 'admin_enqueue_scripts', array(  $this, 'smart_coupon_shortcode_button_init' ) );	// Use 'admin_enqueue_scripts' instead of 'init' // Credit: Jonathan Desrosiers <jdesrosiers@linchpinagency.com>
				add_action( 'init', array(  $this, 'register_smart_coupon_shortcode' ) );
				add_action( 'init', array(  $this, 'register_plugin_styles' ) );
				add_action( 'after_wp_tiny_mce', array(  $this, 'smart_coupons_after_wp_tiny_mce' ) );
				add_action( 'init', array(  $this, 'load_sc_textdomain' ) );
				add_action( 'wp_loaded', array(  $this, 'apply_coupon_from_url' ), 20 );

				add_filter( 'woocommerce_gift_certificates_email_template', array(  $this, 'woocommerce_gift_certificates_email_template_path' ) );
				add_filter( 'woocommerce_call_for_credit_form_template', array(  $this, 'woocommerce_call_for_credit_form_template_path' ) );
				add_filter( 'wc_smart_coupons_export_headers', array(  $this, 'wc_smart_coupons_export_headers' ) );

				add_action( 'add_meta_boxes', array($this, 'add_generated_coupon_details') );
				add_action( 'woocommerce_email_after_order_table', array( $this, 'generated_coupon_details_after_order_table' ), 10, 3 );
				add_action( 'woocommerce_view_order', array( $this, 'generated_coupon_details_view_order' ) );
				add_action( 'woocommerce_before_my_account', array( $this, 'generated_coupon_details_before_my_account' ) );

				add_action( 'admin_enqueue_scripts', array( $this, 'smart_coupon_styles_and_scripts' ), 20 );

            	add_filter( 'is_protected_meta', array( $this, 'make_sc_meta_protected' ), 10, 3 );

            	add_filter( 'views_edit-shop_coupon', array( $this, 'smart_coupons_views_row' ) );
            	add_action( 'smart_coupons_display_views', array( $this, 'smart_coupons_display_views' ) );

            	if( isset( $_GET['import'] ) && $_GET['import'] == "woocommerce_smart_coupon_csv" ||
					isset( $_GET['page']) && $_GET['page'] == 'woocommerce_smart_coupon_csv_import' ) {
					ob_start();
				}

			}

			/**
			 * to handle WC compatibility related function call from appropriate class
			 * 
			 * @param $function_name string
			 * @param $arguments array of arguments passed while calling $function_name
			 * @return result of function call
			 * 
			 */
			public function __call( $function_name, $arguments = array() ) {

				if ( ! is_callable( 'Smart_Coupons_WC_Compatibility_2_3', $function_name ) ) return;

				if ( ! empty( $arguments ) ) {
					return call_user_func_array( 'Smart_Coupons_WC_Compatibility_2_3::'.$function_name, $arguments );
				} else {
					return call_user_func( 'Smart_Coupons_WC_Compatibility_2_3::'.$function_name );
				}

			}

			/**
			 * Metabox on Order Edit Admin page to show generated coupons during the order
			 */
			public function add_generated_coupon_details() {
				global $post;
		
				if ( $post->post_type !== 'shop_order' ) return;
				
				add_meta_box( 'sc-generated-coupon-data', __('Coupon Sent', 'wc_smart_coupons'), array( $this, 'sc_generated_coupon_data_metabox' ), 'shop_order', 'normal');
			}
			
			/**
			 * Metabox content (Generated coupon's details)
			 */
			public function sc_generated_coupon_data_metabox() {
				global $post;
				if ( !empty( $post->ID ) ) {
					$this->get_generated_coupon_data( $post->ID, '', true, false );
				}
			}

			/**
			 * Fetch generated coupon's details
			 * 
			 * @param array|int $order_ids 
			 * @param array|int $user_ids 
			 * @param boolean $html optional default:false whether to return only data or html code 
			 * @param boolean $header optional default:false whether to add a header above the list of generated coupon details
			 * @param string $layout optional default:box Possible values 'box' or 'table' layout to show generated coupons details
			 * 
			 * Either order_ids or user_ids required
			 * @return array $generated_coupon_data associative array containing generated coupon's details
			 */
			public function get_generated_coupon_data( $order_ids = '', $user_ids = '', $html = false, $header = false, $layout = 'box' ) {
				global $wpdb, $woocommerce;

				if ( !is_array( $order_ids ) ) {
					$order_ids = ( !empty( $order_ids ) ) ? array( $order_ids ) : array();
				}

				if ( !is_array( $user_ids ) ) {
					$user_ids = ( !empty( $user_ids ) ) ? array( $user_ids ) : array();
				}
				
				$user_order_ids = array();

				if ( !empty( $user_ids ) ) {

					$user_order_ids_query = "SELECT DISTINCT postmeta.post_id FROM {$wpdb->prefix}postmeta AS postmeta 
													WHERE postmeta.meta_key = '_customer_user'
													AND postmeta.meta_value IN ( " . implode( ',', $user_ids ) . " )";

					$user_order_ids = $wpdb->get_col( $user_order_ids_query );

				}

				$new_order_ids = array_unique( array_merge( $user_order_ids, $order_ids ) );
				
				$generated_coupon_data = array();
				foreach ( $new_order_ids as $id ) {
					$data = get_post_meta( $id, 'sc_coupon_receiver_details', true );
					if ( empty( $data ) ) continue;
					$from = get_post_meta( $id, '_billing_email', true );
					$generated_coupon_data[$from] = $data;
				}

				if ( empty( $generated_coupon_data ) ) {
					return;
				}

				if ( $html ) {

					if ( $header ) {
						echo '<h3>' . __( 'Coupon Received', 'wc_smart_coupons' ) . '</h3>';
					}

					if ( $layout == 'table' ) {
						$this->get_generated_coupon_data_table( $generated_coupon_data );
					} else {
						$this->get_generated_coupon_data_box( $generated_coupon_data );
					}

					return;

				}

				return $generated_coupon_data;
			}

			/**
			 * HTML code to display generated coupon's data in box layout
			 * 
			 * @param array $generated_coupon_data associative array containing generated coupon's details
			 */
			public function get_generated_coupon_data_box( $generated_coupon_data = array() ) {
				if ( empty( $generated_coupon_data ) ) return;
				global $woocommerce;
				$js = "
						var switchMoreLess = function() {
							var total = jQuery('details').length;
							var open = jQuery('details[open]').length;
							if ( open == total ) {
								jQuery('a#more_less').text('" .__( 'Less details', 'wc_smart_coupons' ) . "');
							} else {
								jQuery('a#more_less').text('" . __( 'More details', 'wc_smart_coupons' ) . "');
							}
						};
						switchMoreLess();

						jQuery('a#more_less').on('click', function(){
							var current = jQuery('details').attr('open');
							if ( current == '' || current == undefined ) {
								jQuery('details').attr('open', 'open');
								jQuery('a#more_less').text('" .__( 'Less details', 'wc_smart_coupons' ) . "');
							} else {
								jQuery('details').removeAttr('open');
								jQuery('a#more_less').text('" . __( 'More details', 'wc_smart_coupons' ) . "');
							}
						});

						jQuery('summary.generated_coupon_summary').on('mouseup', function(){
							setTimeout( switchMoreLess, 10 );
						});

						jQuery('span.expand_collapse').show();
					";

				if ( $this->is_wc_gte_21() ) {
					wc_enqueue_js( $js );
				} else {
					$woocommerce->add_inline_js( $js );
				}

				?>
				<style type="text/css">
					.coupon-container {
						margin: .2em;
						box-shadow: 0 0 5px #e0e0e0;
						display: inline-table;
						text-align: center;
						cursor: pointer;
					}
					.coupon-container.previews { cursor: inherit }
					.coupon-container.blue { background-color: #e0f7ff }
					.coupon-container.red { background-color: #ffe0e0 }
					.coupon-container.green { background-color: #e0ffe0 }
					.coupon-container.yellow { background-color: #f7f7e0 }

					.coupon-container.small {
						padding: .3em;
						line-height: 1.2em;
					}
					.coupon-container.medium {
						padding: .4em;
						line-height: 1.4em;
					}
					.coupon-container.large {
						padding: .5em;
						line-height: 1.6em;
					}

					.coupon-content.small { padding: .2em 1.2em }
					.coupon-content.medium { padding: .4em 1.4em }
					.coupon-content.large { padding: .6em 1.6em }
					.coupon-content.dashed { border: 2.3px dashed }
					.coupon-content.dotted { border: 2.3px dotted }
					.coupon-content.groove { border: 2.3px groove }
					.coupon-content.solid { border: 2.3px solid }
					.coupon-content.none { border: 2.3px none }
					.coupon-content.blue { border-color: #c0d7ee }
					.coupon-content.red { border-color: #eec0c0 }
					.coupon-content.green { border-color: #c0eec0 }
					.coupon-content.yellow { border-color: #e0e0c0 }
					.coupon-content .code {
						font-family: monospace;
						font-size: 1.2em;
						font-weight:700;
					}

					.coupon-content .coupon-expire,
					.coupon-content .discount-info {
						font-family: Helvetica, Arial, sans-serif;
						font-size: 1em;
					}
					.generated_coupon_summary { margin: 0.8em 0.8em; }
					.generated_coupon_details { margin-left: 2em; margin-bottom: 1em; margin-right: 2em; text-align: left; }
					.generated_coupon_data { border: solid 1px lightgrey; margin-bottom: 5px; margin-right: 5px; width: 50%; }
					.generated_coupon_details p { margin: 0; }
					span.expand_collapse { text-align: right; display: block; margin-bottom: 1em; cursor: pointer; }
					.float_right_block { float: right; }
					summary::-webkit-details-marker { display: none; }
					details[open] summary::-webkit-details-marker { display: none; }
				</style>
				<div class="generated_coupon_data_wrapper">
					<span class="expand_collapse" style="display: none;">
						<a id="more_less"><?php _e( 'More details', 'wc_smart_coupons' ); ?></a>
					</span>
					<?php
						foreach ( $generated_coupon_data as $from => $data ) {
							foreach ( $data as $coupon_data ) {

								$coupon = new WC_Coupon( $coupon_data['code'] );

								if ( empty( $coupon->id ) || empty( $coupon->discount_type ) ) continue;

								$coupon_meta = $this->get_coupon_meta_data( $coupon );

								?>
								<div class="coupon-container blue medium">
									<details>
										<summary class="generated_coupon_summary">
											<?php
												echo '<div class="coupon-content blue dashed small">
													<div class="discount-info">'.( ( !empty( $coupon_meta['coupon_amount'] ) ) ? $coupon_meta['coupon_amount'] : '' )." ". ( ( !empty( $coupon_meta['coupon_type'] ) ) ? $coupon_meta['coupon_type'] : '' ).'</div>
													<div class="code">'. $coupon->code .'</div>';
												if( !empty( $coupon->expiry_date) ) {

													$expiry_date = $this->get_expiration_format( $coupon->expiry_date );

													echo '<div class="coupon-expire">'. $expiry_date .'</div>';    
												} else {

													echo '<div class="coupon-expire">'. __( 'Never Expires ', 'wc_smart_coupons' ).'</div>';    
												}    
													
												echo '</div>';
											?>
										</summary>
										<div class="generated_coupon_details">
											<p><strong><?php _e( 'Sender', 'wc_smart_coupons' ); ?>:</strong> <?php echo $from; ?></p>
											<p><strong><?php _e( 'Receiver', 'wc_smart_coupons' ); ?>:</strong> <?php echo $coupon_data['email']; ?></p>
											<?php if ( !empty( $coupon_data['message'] ) ) { ?>                                        
												<p><strong><?php _e( 'Message', 'wc_smart_coupons' ); ?>:</strong> <?php echo $coupon_data['message']; ?></p>
											<?php } ?>
										</div>
									</details>
								</div>
								<?php
							}
						}
					?>
				</div>
				<?php
			}

			/**
			 * HTML code to display generated coupon's details is table layout
			 * 
			 * @param array $generated_coupon_data associative array of generated coupon's details
			 */
			public function get_generated_coupon_data_table( $generated_coupon_data = array() ) {
				if ( empty( $generated_coupon_data ) ) return;
				?>
					<div class="woocommerce_order_items_wrapper">
						<table class="woocommerce_order_items">
							<thead>
								<tr>
									<th><?php _e( 'Code', 'wc_smart_coupons' ); ?></th>
									<th><?php _e( 'Amount', 'wc_smart_coupons' ); ?></th>
									<th><?php _e( 'Receiver', 'wc_smart_coupons' ); ?></th>
									<th><?php _e( 'Message', 'wc_smart_coupons' ); ?></th>
									<th><?php _e( 'Sender', 'wc_smart_coupons' ); ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach ( $generated_coupon_data as $from => $data ) {
										foreach ( $data as $coupon_data ) {
											echo '<tr>';
											echo '<td>' . $coupon_data['code'] . '</td>';
											echo '<td>' . $this->wc_price( $coupon_data['amount'] ) . '</td>';
											echo '<td>' . $coupon_data['email'] . '</td>';
											echo '<td>' . $coupon_data['message'] . '</td>';
											echo '<td>' . $from . '</td>';
											echo '</tr>';
										}
									}
								?>
							</tbody>
						</table>
					</div>
				<?php
			}

			/**
			 * Display generated coupons details after Order table
			 * 
			 * @param mixed $order expecting WC_Order's object
			 */
			public function generated_coupon_details_after_order_table( $order = false, $sent_to_admin = false, $plain_text = false ) {
				if ( !empty( $order->id ) ) {
					$this->get_generated_coupon_data( $order->id, '', true, true );
				}
			}

			/**
			 * Display generated coupons details on View Order page
			 * 
			 * @param int $order_id 
			 */
			public function generated_coupon_details_view_order( $order_id = 0 ) {
				if ( !empty( $order_id ) ) {
					$this->get_generated_coupon_data( $order_id, '', true, true );
				}
			}

			/**
			 * Display generated coupon's details on My Account page
			 */
			public function generated_coupon_details_before_my_account() {
				if ( is_user_logged_in() ) {
					$user_id = get_current_user_id();
					$this->get_generated_coupon_data( '', $user_id, true, true );
				}
			}

			/**
			 * Set 'coupon_sent' as 'no' for renewal order to allow auto generation of coupons (if applicable)
			 * 
			 * @param array $order_items associative array of order items
			 * @param int $original_order_id
			 * @param int $renewal_order_id
			 * @param int $product_id
			 * @param string $new_order_role
			 * @return array $order_items
			 */
			public function sc_modify_renewal_order( $order_items, $original_order_id, $renewal_order_id, $product_id, $new_order_role ) {
				$is_coupon_sent = get_post_meta( $renewal_order_id, 'coupon_sent', true );
				if ( $is_coupon_sent === 'yes' ) {
					$is_recursive = false;
					if ( !empty( $order_items ) ) {
						foreach ( $order_items as $order_item ) {
							$send_coupons_on_renewals = ( !empty( $order_item['product_id'] ) ) ? get_post_meta( $order_item['product_id'], 'send_coupons_on_renewals', true ) : 'no';
							if ( $send_coupons_on_renewals === 'yes' ) {
								$is_recursive = true;
								break;  // if in any order item recursive is enabled, it will set coupon_sent as 'no'
							}
						}
					}
					$stop_recursive_coupon_generation = get_option( 'stop_recursive_coupon_generation', 'no' );
					if ( ( empty( $stop_recursive_coupon_generation ) || $stop_recursive_coupon_generation == 'no' ) && $is_recursive ) {
						update_post_meta( $renewal_order_id, 'coupon_sent', 'no' );
					}
				}
				return $order_items;
			}

			/**
			 * Display store credit's value as cart item's price
			 * 
			 * @param string $product_price
			 * @param array $cart_item associative array of cart item
			 * @param string $cart_item_key
			 * @return string product's price with currency symbol
			 */
			public function woocommerce_cart_item_price_html( $product_price, $cart_item, $cart_item_key ) {

				$gift_certificate = $this->global_wc()->session->credit_called;

				if( ! empty( $gift_certificate ) && isset( $gift_certificate[$cart_item_key] ) && ! empty( $gift_certificate[$cart_item_key] ) )
					return woocommerce_price( $gift_certificate[$cart_item_key] );
				
				return $product_price;
				
			}

			/**
			 * function to add label for smart_coupons in cart total
			 * 
			 * @param string $default_label
			 * @param WC_Coupon $coupon
			 * @return string $new_label
			 */
			public function cart_totals_smart_coupons_label( $default_label = '', $coupon = null ) {

				if ( empty( $coupon ) ) return $default_label;

				if ( ! empty( $coupon->discount_type ) && $coupon->discount_type == 'smart_coupon' ) {
					return esc_html( __( 'Store Credit:', 'wc_smart_coupons' ) . ' ' . $coupon->code );
				}

				return $default_label;

			}

			/**
			 * Validate addition of product for purchasing store credit to cart
			 * 
			 * @param boolean $validation
			 * @param int $product_id
			 * @param int $quantity
			 * @param int $variation_id optional default:''
			 * @param array $variations optional default:'' associative array containing variations attributes & values
			 * @param array $cart_item_data optional default:array() associative array containing additional data
			 * @return boolean $validation
			 */
			public function sc_woocommerce_add_to_cart_validation( $validation, $product_id, $quantity, $variation_id = '', $variations = '', $cart_item_data = array() ) {
				
				if( ! isset( $_POST['credit_called'] ) )
					return $validation;
							
				$cart_item_data['credit_amount'] = $_POST['credit_called'][$product_id];

				$cart_id = $this->global_wc()->cart->generate_cart_id( $product_id, $variation_id, $variations, $cart_item_data );

				if ( function_exists( 'get_product' ) ) {
					if ( isset( $this->global_wc()->session->credit_called[$cart_id] ) && empty( $this->global_wc()->session->credit_called[$cart_id] ) ) {
						return false;
					}
				} else {
					if ( isset( $_SESSION['credit_called'][$cart_id] ) && empty( $_SESSION['credit_called'][$cart_id] ) ) {
						return false;
					}
				}
				
				return $validation;
			}

			/**
			 * Apply coupon code if passed in url
			 */
			public function apply_coupon_from_url() {

				parse_str( $_SERVER['QUERY_STRING'], $coupon_args );

				if ( isset( $coupon_args['coupon-code'] ) && ! empty( $coupon_args['coupon-code'] ) ) {

					if ( $this->global_wc()->cart->has_discount( $coupon_args['coupon-code'] ) ) {
						return;
					}

					$this->global_wc()->cart->add_discount( trim( $coupon_args['coupon-code'] ) ); 

					if( ! empty( $coupon_args['sc-page'] ) ) {
						
						if( in_array( $coupon_args['sc-page'], array( 'shop', 'cart', 'checkout', 'myaccount' ) ) ) {
							$redirect_url = get_permalink( woocommerce_get_page_id( $coupon_args['sc-page'] ) );                            
						} else {                            
							$redirect_url = get_permalink( get_page_by_title( $coupon_args['sc-page'] ) );                            
						}

					} elseif ( ! empty( $this->global_wc()->cart ) ) {
						
						$redirect_url = get_permalink( woocommerce_get_page_id( 'cart' ) );

					} else {
						
						$redirect_url = get_permalink( woocommerce_get_page_id( 'shop' ) );

					}

					if ( empty( $redirect_url ) ) {
						$redirect_url = home_url();
					}

					wp_safe_redirect( $redirect_url );

					exit;
					
				}

			}

			/**
			 * Coupon's expiration date (formatted)
			 * 
			 * @param int $expiry_date
			 * @return string $expires_string formatted expiry date
			 */
			public function get_expiration_format( $expiry_date ) {

				$expiry_days = ( int )( ( $expiry_date - time() )/( 24*60*60 ) );

				if( $expiry_days < 1 ) {
					
					$expires_string = __( 'Expires Today ', 'wc_smart_coupons' );

				} elseif ( $expiry_days < 31 ) {

					$expires_string = __( 'Expires in ', 'wc_smart_coupons' ) . $expiry_days . __( ' days', 'wc_smart_coupons' );
					
				} else {

					$expires_string = __( 'Expires on ', 'wc_smart_coupons' ) . esc_html( date_i18n( 'F j, Y', $expiry_date ) );

				}
				return $expires_string;

			}

			/**
			 * Smart Coupons textdomain
			 */
			public function load_sc_textdomain() {

				$smart_coupons_translations_file_path = apply_filters( 'smart_coupons_translations_file_path', dirname( plugin_basename( __FILE__ ) ) . '/languages' );
				load_plugin_textdomain( 'wc_smart_coupons', false, $smart_coupons_translations_file_path );
		
			}

			/**
			 * Save entered credit value by customer in order for further processing
			 * 
			 * @param int $order_id
			 * @param array $posted associative array of posted data
			 */
			public function save_called_credit_details_in_order( $order_id, $posted ) {

				$order = $this->get_order( $order_id );
				$order_items = $order->get_items();
				
				$sc_called_credit = array();
				$update = false;
				foreach ( $order_items as $item_id => $order_item ) {
					if ( isset( $order_item['sc_called_credit'] ) && !empty( $order_item['sc_called_credit'] ) ) {
						$sc_called_credit[$item_id] = $order_item['sc_called_credit'];
						woocommerce_delete_order_item_meta( $item_id, 'sc_called_credit' );
						$update = true;
					}
				}
				if ( $update ) {
					update_post_meta( $order_id, 'sc_called_credit_details', $sc_called_credit );
				}

				if( function_exists( 'get_product' ) ) {
					if ( isset( $this->global_wc()->session->credit_called ) ) unset( $this->global_wc()->session->credit_called );
				} else {                         
					if ( isset( $_SESSION['credit_called'] ) ) unset( $_SESSION['credit_called'] );
				}

			}

			/**
			 * Save entered credit value by customer in order item meta
			 * 
			 * @param int $item_id 
			 * @param array $values associative array containing item's details
			 */
			public function save_called_credit_details_in_order_item_meta( $item_id, $values ) {

				$coupon_titles = get_post_meta( $values['product_id'], '_coupon_title', true );

				if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && isset( $values['data']->price ) && $values['data']->price > 0 ) {
					woocommerce_add_order_item_meta( $item_id, 'sc_called_credit', $values['data']->price );
				}
			}

			/**
			 * Save entered credit value by customer in order for PayPal Express Checkout
			 * 
			 * @param WC_Order $order
			 */
			public function ppe_save_called_credit_details_in_order( $order ) {
				$this->save_called_credit_details_in_order( $order->id, null );
			}

			/**
			 * Save entered credit value by customer in session
			 * 
			 * @param string $cart_item_key
			 * @param int $product_id
			 * @param int $quantity
			 * @param int $variation_id
			 * @param array $variation
			 * @param array $cart_item_data
			 */
			public function save_called_credit_in_session( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
				if ( !empty( $variation_id ) && $variation_id > 0 ) return;
				if ( !isset( $cart_item_data['credit_amount'] ) || empty( $cart_item_data['credit_amount'] ) ) return;

				$_product = $this->get_product( $product_id );
				
				$coupons = get_post_meta( $product_id, '_coupon_title', true );

				if ( !empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && !( $_product->get_price() > 0 ) ) {
					if ( function_exists( 'get_product' ) ) {
						if ( !isset( $this->global_wc()->session->credit_called ) ) {
							$this->global_wc()->session->credit_called = array();
						}
						$this->global_wc()->session->credit_called += array( $cart_item_key => $cart_item_data['credit_amount'] );
					} else {
						if ( !isset( $_SESSION['credit_called'] ) ) {
							$_SESSION['credit_called'] = array();
						}
						$_SESSION['credit_called'] += array( $cart_item_key => $cart_item_data['credit_amount'] );
					}
				}
		  
			}

			/**
			 * Save entered credit value by customer in cart item data
			 * 
			 * @param array $cart_item_data
			 * @param int $product_id
			 * @param int $variation_id
			 * @return array $cart_item_data
			 */
			public function call_for_credit_cart_item_data( $cart_item_data = array(), $product_id = '', $variation_id = '' ) {
				if ( !empty( $variation_id ) && $variation_id > 0 || empty( $product_id ) ) return $cart_item_data;
			   
				$_product = $this->get_product( $product_id );
				
				$coupons = get_post_meta( $product_id, '_coupon_title', true );

				if ( !empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && !( $_product->get_price() > 0 ) ) {
					$cart_item_data['credit_amount'] = $_REQUEST['credit_called'][$_REQUEST['add-to-cart']];
					return $cart_item_data;
				}

				return $cart_item_data;
			}

			/**
			 * Register & enqueue Smart Coupons CSS
			 */
			public function register_plugin_styles() {
				wp_register_style( 'smart-coupon', untrailingslashit( plugins_url( '/', __FILE__ ) ) . '/assets/css/smart-coupon.css' );
				wp_enqueue_style( 'smart-coupon' );
			}

			/**
			 * Smart coupon button after TinyMCE
			 */
			public function smart_coupons_after_wp_tiny_mce( $mce_settings ) {
				$this->sc_attributes_dialog();
			}

			/**
			 * Register shortcode for Smart Coupons
			 */
			public function register_smart_coupon_shortcode() {
				add_shortcode( 'smart_coupons', array(  $this, 'execute_smart_coupons_shortcode' ) );
			}

			/**
			 * Execute Smart Coupons shortcode
			 * 
			 * @param array $atts
			 * @return HTML code for coupon to be displayed
			 */
			public function execute_smart_coupons_shortcode( $atts ) {
				ob_start();
				global $wpdb;
				
				$current_user   = wp_get_current_user();
				$customer_id    = $current_user->ID;

				if( $customer_id == 0 )
					return;
				
				extract( shortcode_atts( array(
					'coupon_code'                   => '',
					'discount_type'                 => 'smart_coupon',
					'coupon_amount'                 => '',
					'individual_use'                => 'no',
					'product_ids'                   => '',
					'exclude_product_ids'           => '',
					'usage_limit'                   => '',
					'usage_limit_per_user'          => '',
					'limit_usage_to_x_items'        => '',
					'expiry_date'                   => '',
					'apply_before_tax'              => 'no',
					'free_shipping'                 => 'no',
					'product_categories'            => '',
					'exclude_product_categories'    => '',
					'minimum_amount'                => '',
					'maximum_amount'                => '',
					'exclude_sale_items'            => 'no',
					'auto_generate'                 => 'no',
					'coupon_prefix'                 => '',
					'coupon_suffix'                 => '',
					'customer_email'                => '',
					'coupon_style'                  => '',
					'disable_email'                 => 'no'
				), $atts ) );
			
				if ( empty( $customer_email ) ) {
					
					if ( !($current_user instanceof WP_User) ) {
						$current_user   = wp_get_current_user();
						$customer_email = ( isset($current_user->user_email) ) ? $current_user->user_email : '';
					} else {
						$customer_email = $current_user->data->user_email;
					}

				}
			   
				$coupon_exists = $wpdb->get_var("SELECT ID
													FROM {$wpdb->prefix}posts AS posts
														LEFT JOIN {$wpdb->prefix}postmeta AS postmeta
														ON ( postmeta.post_id = posts.ID )
													WHERE posts.post_title = '" . strtolower( $coupon_code ) . "'
													AND posts.post_type = 'shop_coupon'
													AND posts.post_status = 'publish'
													AND postmeta.meta_key = 'customer_email'
													AND postmeta.meta_value LIKE '%$customer_email%'"); 
				$expiry_date = "";

				if ( $coupon_exists == null ) {

					if ( !empty( $coupon_code ) ) {
						$coupon = new WC_Coupon( $coupon_code );
					  
						if ( !empty( $coupon->discount_type ) ) {

							$is_auto_generate = get_post_meta( $coupon->id, 'auto_generate_coupon', true );
							$is_disable_email_restriction = get_post_meta( $coupon->id, 'sc_disable_email_restriction', true );

							if ( ( empty( $is_disable_email_restriction ) || $is_disable_email_restriction == 'no' ) && ( empty( $is_auto_generate ) || $is_auto_generate == 'no' ) ) {
								$existing_customer_emails = get_post_meta( $coupon->id, 'customer_email', true );
								$existing_customer_emails[] = $customer_email;
								update_post_meta( $coupon->id, 'customer_email', $existing_customer_emails );
							}
					 
							if ( !empty( $is_auto_generate ) && $is_auto_generate == 'yes' ) {

								$generated_coupon_details = apply_filters( 'generate_smart_coupon_action', $customer_email, $coupon->amount, '', $coupon );
								$new_generated_coupon_code = $generated_coupon_details[$customer_email][0]['code'];
							  
							} else {
								
								$new_generated_coupon_code = $coupon_code;

							}
						}
					}

					if ( ( !empty( $coupon_code ) && empty( $coupon->discount_type ) ) || ( empty( $coupon_code ) ) ) {

						if ( empty( $coupon_code ) ) {
							$coupon_code = $this->generate_unique_code( $customer_email );
							$coupon_code = $coupon_prefix . $coupon_code . $coupon_suffix;
						}

						$coupon_args = array(
							'post_title'    => strtolower( $coupon_code ),
							'post_content'  => '',
							'post_status'   => 'publish',
							'post_author'   => 1,
							'post_type'     => 'shop_coupon'
						);

						$new_coupon_id = wp_insert_post( $coupon_args );                        
						if ( !empty( $expiry_days ) ) {
							$expiry_date = date( 'Y-m-d', strtotime( "+$expiry_days days" ) );
						}
						
						// Add meta for coupons
						update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
						update_post_meta( $new_coupon_id, 'coupon_amount', $coupon_amount );
						update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
						update_post_meta( $new_coupon_id, 'minimum_amount', $minimum_total );
						update_post_meta( $new_coupon_id, 'maximum_amount', $maximum_total );
						update_post_meta( $new_coupon_id, 'product_ids', array() );
						update_post_meta( $new_coupon_id, 'exclude_product_ids', array() );
						update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );
						update_post_meta( $new_coupon_id, 'expiry_date', $expiry_date );
						update_post_meta( $new_coupon_id, 'customer_email', array( $customer_email ) );
						update_post_meta( $new_coupon_id, 'apply_before_tax', $apply_before_tax  );
						update_post_meta( $new_coupon_id, 'free_shipping', $free_shipping );
						update_post_meta( $new_coupon_id, 'product_categories', array()  );
						update_post_meta( $new_coupon_id, 'exclude_product_categories', array() );
						update_post_meta( $new_coupon_id, 'sc_disable_email_restriction', $disable_email );

						$new_generated_coupon_code = $coupon_code;

					}

				} else {

					$new_generated_coupon_code = $coupon_code;

				}

				if ( !empty( $new_generated_coupon_code ) ) {
					$coupon = new WC_Coupon( $new_generated_coupon_code );
				}

				switch( $coupon->discount_type ) {
					case 'smart_coupon':
						$coupon_type = __( 'Store Credit', 'wc_smart_coupons' );
						$coupon_amount = woocommerce_price( $coupon->amount );
						break;

					case 'fixed_cart':
						$coupon_type = __( 'Cart Discount', 'wc_smart_coupons' );
						$coupon_amount = woocommerce_price( $coupon->amount );
						break;

					case 'fixed_product':
						$coupon_type = __( 'Product Discount', 'wc_smart_coupons' );
						$coupon_amount = woocommerce_price( $coupon->amount );
						break;

					case 'percent_product':
						$coupon_type = __( 'Product Discount', 'wc_smart_coupons' );
						$coupon_amount = $coupon->amount . '%';
						break;

					case 'percent':
						$coupon_type = __( 'Cart Discount', 'wc_smart_coupons' );
						$coupon_amount = $coupon->amount . '%';
						break;

				}
				$discount_text = $coupon_amount . ' '. $coupon_type;
				$discount_text = wp_strip_all_tags( $discount_text );

				echo '<div class="coupon-container '.$atts["coupon_style"].'" style="cursor:inherit">
							<div class="coupon-content '.$atts["coupon_style"].'">
								<div class="discount-info">'. $discount_text.'</div>
								<div class="code">'. $new_generated_coupon_code .'</div>';
				
				$expiry_date = get_post_meta( $coupon->id, 'expiry_date', true );

				if ( ! empty( $expiry_date ) ) {
					$expiry_date_text = $this->get_expiration_format( strtotime( $expiry_date ) );
					echo ' <div class="coupon-expire">' . $expiry_date_text .'</div>';
				} else {
					echo ' <div class="coupon-expire">' . __( 'Never Expires ', 'wc_smart_coupons' ) . '</div>';
				}
				
				echo '</div>
					</div>';
				
				return ob_get_clean();
			}

			/**
			 * Formatted coupon data
			 * 
			 * @param WC_Coupon $coupon
			 * @return array $coupon_data associative array containing formatted coupon data
			 */
			public function get_coupon_meta_data( $coupon ) {
				$coupon_data = '';
				switch( $coupon->discount_type ) {
					case 'smart_coupon':
						$coupon_data['coupon_type'] = __( 'Store Credit', 'wc_smart_coupons' );
						$coupon_data['coupon_amount'] = woocommerce_price( $coupon->amount );
						break;

					case 'fixed_cart':
						$coupon_data['coupon_type'] = __( 'Cart Discount', 'wc_smart_coupons' );
						$coupon_data['coupon_amount'] = woocommerce_price( $coupon->amount );
						break;

					case 'fixed_product':
						$coupon_data['coupon_type'] = __( 'Product Discount', 'wc_smart_coupons' );
						$coupon_data['coupon_amount'] = woocommerce_price( $coupon->amount );
						break;

					case 'percent_product':
						$coupon_data['coupon_type'] = __( 'Product Discount', 'wc_smart_coupons' );
						$coupon_data['coupon_amount'] = $coupon->amount . '%';
						break;

					case 'percent':
						$coupon_data['coupon_type'] = __( 'Cart Discount', 'wc_smart_coupons' );
						$coupon_data['coupon_amount'] = $coupon->amount . '%';
						break;

				}
				return $coupon_data;
			}

			/**
			 * Add Smart Coupons shortcode button in WP editor
			 */
			public function smart_coupon_shortcode_button_init() {

				if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') && get_user_option('rich_editing') == 'true') {
					return;
				}

				if ( ! wp_script_is( 'wpdialogs' ) ) {
					wp_enqueue_script( 'wpdialogs' );
				}

				if ( ! wp_style_is( 'wp-jquery-ui-dialog' ) ) {
					wp_enqueue_style( 'wp-jquery-ui-dialog' );
				}

				add_filter( 'mce_external_plugins', array(  $this, 'smart_coupon_register_tinymce_plugin' ) );
				add_filter( 'mce_buttons', array(  $this, 'smart_coupon_add_tinymce_button' ) );

			}

			/**
			 * Add Smart Coupon short code button in TinyMCE
			 * 
			 * @param array $plugin_array existing plugin
			 * @return array $plugin array with SMart Coupon shortcode
			 */
			public function smart_coupon_register_tinymce_plugin( $plugin_array ) {
				$plugin_array['sc_shortcode_button'] = plugins_url( 'assets/js/sc_shortcode.js', __FILE__ );
				return $plugin_array;
			}

			/**
			 * Add Smart coupon shortcode button in TinyMCE
			 * 
			 * @param array $button existing button
			 * @return array $button whith smart coupons shortcode button
			 */
			public function smart_coupon_add_tinymce_button( $buttons ) {
				$buttons[] = 'sc_shortcode_button';
				return $buttons;
			}

			/**
			 * JSON Search coupon via ajax
			 * 
			 * @param string $x search text
			 * @param array $post_types
			 */
			public function smart_coupons_json_search( $x = '', $post_types = array( 'shop_coupon' ) ) {
				global $woocommerce, $wpdb;

				check_ajax_referer( 'search-coupons', 'security' );

				$term = (string) urldecode(stripslashes(strip_tags($_GET['term'])));

				if (empty($term)) die();

				$posts = $wpdb->get_results("SELECT * 
											FROM {$wpdb->prefix}posts 
											WHERE post_type = 'shop_coupon' 
												AND post_title LIKE '$term%' 
												AND post_status = 'publish'");

				$found_products = array();

				$all_discount_types = ( $this->is_wc_gte_21() ) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();

				if ($posts) foreach ($posts as $post) {

					$discount_type = get_post_meta($post->ID, 'discount_type', true);
					if ( !empty( $all_discount_types[$discount_type] ) ) {

						$coupon = new WC_Coupon( get_the_title( $post->ID ) );
						switch ( $coupon->discount_type ) {

							case 'smart_coupon':
								$coupon_type = 'Store Credit';
								$coupon_amount = woocommerce_price( $coupon->amount );
								break;

							case 'fixed_cart':
								$coupon_type = 'Cart Discount';
								$coupon_amount = woocommerce_price( $coupon->amount );
								break;

							case 'fixed_product':
								$coupon_type = 'Product Discount';
								$coupon_amount = woocommerce_price( $coupon->amount );
								break;

							case 'percent_product':
								$coupon_type = 'Product Discount';
								$coupon_amount = $coupon->amount . '%';
								break;

							case 'percent':
								$coupon_type = 'Cart Discount';
								$coupon_amount = $coupon->amount . '%';
								break;

						}

						$discount_type = ' ( ' . $coupon_amount . ' '. $coupon_type . ' )';
						$discount_type = wp_strip_all_tags( $discount_type );

						$additional_info = '';
						$is_auto_generate = get_post_meta( $coupon->id, 'auto_generate_coupon', true );
						if ( ! empty( $is_auto_generate ) && $is_auto_generate == 'yes' ) {
							$additional_info = '<span class="dashicons dashicons-info" title="'.__( 'Note: This will generate a new coupon on every refresh of page.', 'wc_smart_coupons' ).'"></span>';
						}

						$found_products[get_the_title( $post->ID )] = get_the_title( $post->ID ) .' '. $discount_type . $additional_info;
					}

				}

				if( ! empty( $found_products ) ) {
					echo json_encode( $found_products );
				}

				die();
			}

			/**
			 * Smart Coupons dialog content for shortcode
			 * 
			 * @static
			 */
			public static function sc_attributes_dialog() {

				wp_enqueue_style( 'coupon-style' );

				?>
				<div style="display:none;">
					<form id="sc_coupons_attributes" tabindex="-1" style="background-color: #F5F5F5;">
					<?php wp_nonce_field( 'internal_coupon_shortcode', '_ajax_coupon_shortcode_nonce', false ); ?>

					<script type="text/javascript">
						jQuery(function(){
							jQuery('input#search-coupon-field').on('keyup',function() {

								jQuery('div#search-results ul').empty();
								var searchString = jQuery(this).val().trim();

								if ( searchString.length == 0 ) {
									jQuery('#default-text').html('<?php _e( "No search term specified.", "wc_smart_coupons" ); ?>');
									return true;
								}
								if ( searchString.length == 1 ) {
									jQuery('#default-text').html('<?php _e( "Enter more than one character to search.", "wc_smart_coupons" ); ?>');
									return true;
								}

								jQuery.ajax({
									url: '<?php echo admin_url( "admin-ajax.php" ); ?>',
									method: 'GET',
									afterTypeDelay: 100,
									data: {
										action        : 'smart_coupons_json_search',
										security      : '<?php echo wp_create_nonce("search-coupons"); ?>',
										term          : searchString
									},
									dataType: 'json',
									success: function( response ) {
										if ( response ) {
											jQuery('#default-text').html('<?php _e( "Click to select coupon code.", "wc_smart_coupons" ); ?>');
										} else {
											jQuery('#default-text').html('<?php _e( "No coupon code found.", "wc_smart_coupons" ); ?>');
											return;
										}
										jQuery.each(response, function (i, val) {
										   
											jQuery('div#search-results ul').append('<li class="'+i+'">'+ i +val.substr(val.indexOf('(')-1)+'</li>');
										});
									}
								});
							});
						
							jQuery('div#sc_shortcode_cancel a').on('click', function() {
								emptyAllFormElement();
								jQuery('.ui-dialog-titlebar-close').trigger('click');
							});

							function emptyAllFormElement() {
								jQuery('#search-coupon-field').val('');
								jQuery('#default-text').html('<?php _e( "No search term specified.", "wc_smart_coupons" ); ?>');
								jQuery('#search-results ul').empty();
							}

							jQuery('div#search-results ul li').live('click', function() {
								var couponCode = jQuery(this).attr('class');
								jQuery('input#search-coupon-field').val(couponCode);
							});

							jQuery('input#sc_shortcode_submit').on('click', function() {

								var couponShortcode = '[smart_coupons';
								var couponCode      = jQuery('#search-coupon-field').val();
								var coupon_border   = jQuery('select#coupon-border').find('option:selected').val();
								var coupon_color    = jQuery('select#coupon-color').find('option:selected').val();
								var coupon_size     = jQuery('select#coupon-size').find('option:selected').val();
								var coupon_style    = coupon_border+' '+coupon_color+' '+coupon_size;

								if ( couponCode != undefined && couponCode != '' ) {
									couponShortcode += ' coupon_code="'+couponCode.trim()+'"';
								}
								if ( coupon_style != undefined && coupon_style != '' ) {
									couponShortcode += ' coupon_style="'+coupon_style+'"';    
								}
								
								couponShortcode += ']';
								tinyMCE.execCommand("mceInsertContent", false, couponShortcode);
								emptyAllFormElement();
								jQuery('.ui-dialog-titlebar-close').trigger('click');

							});

							//Shortcode Styles
							apply_preview_style();

							jQuery('select').on('change', function() {
								apply_preview_style();
							});

							function apply_preview_style() {
								var coupon_border   = jQuery('select#coupon-border').find('option:selected').val();
								var coupon_color    = jQuery('select#coupon-color').find('option:selected').val();
								var coupon_size     = jQuery('select#coupon-size').find('option:selected').val();
								
								jQuery('div.coupon-container').removeClass().addClass('coupon-container previews');
								jQuery('div.coupon-container').addClass(coupon_color+' '+coupon_size);

								jQuery('div.coupon-content').removeClass().addClass('coupon-content');
								jQuery('div.coupon-content').addClass(coupon_border+' '+coupon_size+' '+coupon_color);
							}
						
					});

					</script>
				  
						<div id="coupon-selector">
							<div id="coupon-option">
								<div>
									<label><span><?php _e( 'Coupon code', 'wc_smart_coupons' ); ?></span><input id="search-coupon-field" type="text" name="search_coupon_code" placeholder="<?php _e( 'Search coupon...', 'wc_smart_coupons' )?>"/></label>
								</div>
								<div id="search-panel">
									<div id="search-results">
										<div id="default-text"><?php _e( 'No search term specified.', 'wc_smart_coupons' ); ?></div>                                        
										<ul></ul>
									</div>
								</div>
								<div>
									<div>
										<label><span><?php _e( 'Color', 'wc_smart_coupons' ); ?></span>
											<select id="coupon-color" name="coupon-color">
												<option value="green" selected="selected"><?php _e( 'Light Green', 'wc_smart_coupons' ) ?></option>
												<option value="blue"><?php _e( 'Light Blue', 'wc_smart_coupons' ) ?></option>
												<option value="red"><?php _e( 'Light Red', 'wc_smart_coupons' ) ?></option>
												<option value="yellow"><?php _e( 'Light Yellow', 'wc_smart_coupons' ) ?></option>
											</select>
										</label>
									</div>
								   <div>
										<label><span><?php _e( 'Border', 'wc_smart_coupons' ); ?></span>
											<select id="coupon-border" name="coupon-border">
												<option value="dashed" selected="selected">- - - - - - - - -</option>
												<option value="dotted">-----------------</option>
												<option value="solid">––––––––––</option>
												<option value="groove">––––––––––</option>
												<option value="none">         </option>
											</select>
										</label>
									</div>
									<div>
										<label><span><?php _e( 'Size', 'wc_smart_coupons' ); ?></span>
											<select id="coupon-size" name="coupon-size">
												<option value="small"><?php _e( 'Small', 'wc_smart_coupons' ) ?></option>
												<option value="medium" selected="selected"><?php _e( 'Medium', 'wc_smart_coupons' ) ?></option>
												<option value="large"><?php _e( 'Large', 'wc_smart_coupons' ) ?></option>
											</select>
										</label>
									</div>
								</div>

							</div>
						</div>
						<div class="coupon-preview">
							<div class="preview-heading">
								<?php _e( 'Preview', 'wc_smart_coupons' ) ?>
							</div>
							<div class="coupon-container">
								<div class="coupon-content">
									<div class="discount-info"><?php _e( 'XX Discount type', 'wc_smart_coupons' ) ?></div>
									<div class="code"><?php _e( 'coupon-code', 'wc_smart_coupons' ) ?></div>
									<div class="coupon-expire"><?php _e( 'Expires on xx date', 'wc_smart_coupons' ) ?></div>

								</div>
							</div>
						</div>
						<div class="submitbox">
							<div id="sc_shortcode_update">
								<input type="button" value="<?php esc_attr_e( 'Insert Shortcode', 'wc_smart_coupons' ); ?>" class="button-primary" id="sc_shortcode_submit" name="sc_shortcode_submit">
							</div>
							<div id="sc_shortcode_cancel">
								<a class="submitdelete deletion" href="#"><?php _e( 'Cancel', 'wc_smart_coupons' ); ?></a>
							</div>
						</div>
					</form>
				</div>
				<?php
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
				
					if( isset( $_POST['email'] ) && $_POST['email'] != $old_customers_email_id ) {

						$query = "SELECT post_id
									FROM $wpdb->postmeta
									WHERE meta_key = 'customer_email'
									AND meta_value LIKE  '%$old_customers_email_id%'
									AND post_id IN ( SELECT ID
														FROM $wpdb->posts 
														WHERE post_type =  'shop_coupon')";
						$result = $wpdb->get_col( $query ); 

						if( ! empty( $result ) ) {

							foreach ( $result as $post_id ) {

								$coupon_meta = get_post_meta( $post_id, 'customer_email', true );
								
								foreach ( $coupon_meta as $key => $email_id ) {
									
									if( $email_id == $old_customers_email_id ) {

										$coupon_meta[$key] = $_POST['email'];
									}
								}

								update_post_meta( $post_id, 'customer_email', $coupon_meta );

							} //end foreach
						}                                      
					}
				}
			}

			/**
			 * Replace Add to cart button with Select Option button for products which are created for purchasing credit, on shop page
			 */
			public function remove_add_to_cart_button_from_shop_page() {
				global $product, $woocommerce;
				
				$coupons = get_post_meta( $product->id, '_coupon_title', true );

				if ( !empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && !( $product->get_price() > 0 ) ) {

					$js = " jQuery('a[data-product_id=". $product->id ."]').remove(); ";

					if ( $this->is_wc_gte_21() ) {
						wc_enqueue_js( $js );
					} else {
						$woocommerce->add_inline_js( $js );
					}
					?>
					<a href="<?php echo the_permalink(); ?>" class="button"><?php echo get_option( 'sc_gift_certificate_shop_loop_button_text', __( 'Select options', 'wc_smart_coupons' ) ); ?></a>
					<?php                          
				}
			}
						
			/**
			 * Set price for store credit to be purchased before calculating total in car
			 * 
			 * @param WC_Cart $cart_object
			 */
			public function override_price_before_calculate_totals( $cart_object ) {
				
				foreach ( $cart_object->cart_contents as $key => $value ) {

					$coupons = get_post_meta( $value['data']->id, '_coupon_title', true );

					if ( !empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && !( $value['data']->price > 0 ) ) {
					  
						// NEWLY ADDED CODE TO MAKE COMPATIBLE.
						if( function_exists( 'get_product' ) ) {
							$price = ( isset( $this->global_wc()->session->credit_called[$key] ) ) ? $this->global_wc()->session->credit_called[$key]: '';
						} else {                         
							$price = ( isset( $_SESSION['credit_called'][$key] ) ) ? $_SESSION['credit_called'][$key]: '';
						}

						if ( $price <= 0 ) {
							$this->global_wc()->cart->set_quantity( $key, 0 );    // Remove product from cart if price is not found either in session or in product
							continue;
						}

						$cart_object->cart_contents[$key]['data']->price = $price;
				
					}

				}

			}

			/**
			 * Make product whose price is set as zero but is for purchasing credit, purchasable
			 * 
			 * @param boolean $purchasable
			 * @param WC_Product $product
			 * @return boolean $purchasable
			 */
			public function make_product_purchasable( $purchasable, $product ) {

				$coupons = get_post_meta( $product->id, '_coupon_title', true );

				if ( !empty( $coupons ) && $product instanceof WC_Product && $product->get_price() === '' && $this->is_coupon_amount_pick_from_product_price( $coupons ) && !( $product->get_price() > 0 ) ) {
					return true;
				}

				return $purchasable;
			}

			/**
			 * Method to check whether 'pick_price_from_product' is set or not
			 * 
			 * @param array $coupons array of coupon codes
			 * @return boolean 
			 */
			public function is_coupon_amount_pick_from_product_price( $coupons ) {

				if( empty( $coupons ) )
					return false;

				foreach ( $coupons as $coupon_code ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( $coupon->discount_type == 'smart_coupon' && get_post_meta( $coupon->id, 'is_pick_price_of_product', true ) == 'yes' ) {
						return true;
					}
				}
				return false;
			}

			/**
			 * Display form to enter value of the store credit to be purchased
			 */
			public function call_for_credit_form() {
				global $product, $woocommerce;

				if ( $product instanceof WC_Product_Variation ) return;

				$coupons = get_post_meta( $product->id, '_coupon_title', true );

				if ( !function_exists( 'is_plugin_active' ) ) {
					if ( ! defined('ABSPATH') ) {
						include_once ('../../../wp-load.php');
					}
					require_once ABSPATH . 'wp-admin/includes/plugin.php';
				}

				// MADE CHANGES IN THE CONDITION TO SHOW INPUT FIELDFOR PRICE ONLY FOR COUPON AS A PRODUCT
				if ( !empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ( !( $product->get_price() != '' || ( is_plugin_active( 'woocommerce-name-your-price/woocommerce-name-your-price.php' ) && ( get_post_meta( $product->id, '_nyp', true ) == 'yes' ) ) ) ) ) {

					$js = " 
								var validateCreditCalled = function(){
									var enteredCreditAmount = jQuery('input#credit_called').val();
									if ( enteredCreditAmount < 0.01 ) {
										jQuery('p#error_message').text('" . __( 'Invalid amount', 'wc_smart_coupons' ) . "');
										jQuery('input#credit_called').css('border-color', 'red');
										return false;
									} else {
										jQuery('form.cart').unbind('submit');
										jQuery('p#error_message').text('');
										jQuery('input#credit_called').css('border-color', '');
										return true;
									}
								};

								jQuery('input#credit_called').bind('change keyup', function(){
									validateCreditCalled();
									jQuery('input#hidden_credit').remove();
									jQuery('input[name=quantity]').append('<input type=\"hidden\" id=\"hidden_credit\" name=\"credit_called[". $product->id ."]\" value=\"'+jQuery('input#credit_called').val()+'\" />');
								});

				   
								jQuery('button.single_add_to_cart_button').on('click', function(e) {
									if ( validateCreditCalled() == false ) {
										e.preventDefault();
									}
								});

								jQuery('input#credit_called').on( 'keypress', function (e) {
									if (e.which == 13) {
										jQuery('form.cart').submit();
									}
								});
								
							";

					if ( $this->is_wc_gte_21() ) {
						wc_enqueue_js( $js );
					} else {
						$woocommerce->add_inline_js( $js );
					}
					
					$smart_coupon_store_gift_page_text = get_option('smart_coupon_store_gift_page_text');
					$smart_coupon_store_gift_page_text = ( !empty( $smart_coupon_store_gift_page_text ) ) ? $smart_coupon_store_gift_page_text.' ' :  __( 'Purchase Credit worth', 'wc_smart_coupons');

					include(apply_filters('woocommerce_call_for_credit_form_template', 'templates/call-for-credit-form.php'));

				}
			}

			/**
			 * Function to notifiy user about remaining balance in Store Credit in "Order Complete" email
			 * 
			 * @param WC_Order $order
			 * @param boolean $send_to_admin
			 */
			public function show_store_credit_balance( $order = false, $send_to_admin = false, $plain_text = false ) {

				if ( $send_to_admin ) return;
				
				if ( sizeof( $order->get_used_coupons() ) > 0 ) {
					$store_credit_balance = '';
					foreach ( $order->get_used_coupons() as $code ) {
						if ( ! $code ) continue;
						$coupon = new WC_Coupon( $code );

						if ( $coupon->discount_type == 'smart_coupon' && $coupon->amount > 0 ) {
							$store_credit_balance .= '<li><strong>'. $coupon->code .'</strong> &mdash; '. woocommerce_price( $coupon->amount ) .'</li>';
						}
					}

					if ( !empty( $store_credit_balance ) ) {
						echo "<br /><h3>" . __( 'Store Credit / Gift Card Balance', 'wc_smart_coupons' ) . ": </h3>";
						echo "<ul>" . $store_credit_balance . "</ul><br />";
					}
				}
			}

			/**
			 * Function to show available coupons after cart table
			 */
			public function show_available_coupons_after_cart_table() {

				$smart_coupon_cart_page_text = get_option('smart_coupon_cart_page_text');
				$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (Click on the coupon to use it)', 'wc_smart_coupons' );
				$this->show_available_coupons( $smart_coupon_cart_page_text, 'cart' );

			}

			/**
			 * Function to show available coupons before checkout form
			 */
			public function show_available_coupons_before_checkout_form() {

				$smart_coupon_cart_page_text = get_option('smart_coupon_cart_page_text');
				$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (Click on the coupon to use it)', 'wc_smart_coupons' );
				$this->show_available_coupons( $smart_coupon_cart_page_text, 'checkout' );

			}

			/**
			 * Function to show available coupons
			 * 
			 * @param string $available_coupons_heading
			 * @param string $page
			 */
			public function show_available_coupons( $available_coupons_heading = '', $page = 'checkout' ) {

				$global_coupons = apply_filters( 'wc_smart_coupons_global_coupons', $this->get_global_coupons() );

				if ( is_user_logged_in() ) {
					$coupons    = $this->get_customer_credit();
				} else {
					$coupons    = array();
				}                

				$coupons = array_merge( $coupons, $global_coupons );

				if ( empty( $coupons ) ) return false;

				?>
				<div id='coupons_list'><h2><?php _e( stripslashes( $available_coupons_heading ), 'wc_smart_coupons' ) ?></h2>
					<?php
											
					// NEWLY ADDED CODE TO MAKE COMPATIBLE.
					if( function_exists( 'get_product' ) ){
						$coupons_applied = $this->global_wc()->cart->get_applied_coupons();
					} else {
						$coupons_applied = $_SESSION['coupons'];
					}
											
					foreach ( $coupons as $code ) {

						if ( in_array( $code->post_title, $coupons_applied ) ) continue;

						$coupon = new WC_Coupon( $code->post_title );

						if ( ! $coupon->is_valid() ) {
							continue;
						}

						if( ( empty( $coupon->amount ) || $coupon->amount == 0 ) && $coupon->free_shipping == "no" )
							continue;

						if ( empty( $coupon->discount_type ) || ( ! empty( $coupon->expiry_date  ) && current_time( 'timestamp' ) > $coupon->expiry_date ) )
							continue;

						$coupon_data = $this->get_coupon_meta_data( $coupon );

						echo '<div class="coupon-container apply_coupons_credits blue medium" name="'.$coupon->code.'" style="cursor: pointer">
							<div class="coupon-content blue dashed small" name="'.$coupon->code.'">
								<div class="discount-info" >'.( ( !empty( $coupon_data['coupon_amount'] ) ) ? $coupon_data['coupon_amount'] : '' ) ." ". ( ( !empty( $coupon_data['coupon_type'] ) ) ? $coupon_data['coupon_type'] : '' ).'</div>
								<div class="code">'. $coupon->code .'</div>';

							if( !empty( $coupon->expiry_date ) ) {

								$expiry_date = $this->get_expiration_format( $coupon->expiry_date );

								echo '<div class="coupon-expire">'. $expiry_date .'</div>';    

							} else {

								echo '<div class="coupon-expire">'. __( 'Never Expires', 'wc_smart_coupons' ) . '</div>';    

							}    
								
						echo '</div>
							</div>';

					}

					$js = " 	jQuery('div.apply_coupons_credits').on('click', function() {

									coupon_code = jQuery(this).find('div.code').text();

									if( coupon_code != '' && coupon_code != undefined ) {

										jQuery(this).addClass( 'smart-coupon-loading' );
										var url = '". trailingslashit( home_url() ) ."?sc-page=". $page ."&coupon-code='+coupon_code;   
										jQuery(location).attr('href', url);
										
									}
								});

								if( jQuery('div#coupons_list').find('div.coupon-container').length == 0 ) {
									jQuery('div#coupons_list h2').css('display' , 'none');
								}

								jQuery('.checkout_coupon').next('#coupons_list').hide();

								jQuery('a.showcoupon').on('click', function() {
									jQuery('#coupons_list').slideToggle();
								});

							";

					if ( $this->is_wc_gte_23() ) {
						$js .= "jQuery('body').on( 'update_checkout', function( e ){
									var coupon_code = jQuery('.woocommerce-remove-coupon').data( 'coupon' );
									if ( coupon_code != undefined && coupon_code != '' ) {
										jQuery('div[name='+coupon_code+'].apply_coupons_credits').show();
									}
								});";
					}

					if ( $this->is_wc_gte_21() ) {
						wc_enqueue_js( $js );
					} else {
						$woocommerce->add_inline_js( $js );
					}
					?>
				</div>
				<?php

			}

			/**
			 * Function to add gift certificate receiver's details in order itself
			 * 
			 * @param int $order_id
			 */
			public function add_gift_certificate_receiver_details_in_order( $order_id ) {

				if ( !isset( $_REQUEST['gift_receiver_email'] ) || count( $_REQUEST['gift_receiver_email'] ) <= 0 ) return;

				if ( isset( $_REQUEST['gift_receiver_email'] ) || ( isset( $_REQUEST['billing_email'] ) && $_REQUEST['billing_email'] != $_REQUEST['gift_receiver_email'] ) ) {

					if ( isset( $_REQUEST['is_gift'] ) && $_REQUEST['is_gift'] == 'yes' ) {
						if ( isset( $_REQUEST['sc_send_to'] ) && !empty( $_REQUEST['sc_send_to'] ) ) {
							switch ( $_REQUEST['sc_send_to'] ) {
								case 'one':
									$email_for_one = ( isset( $_REQUEST['gift_receiver_email'][0][0] ) && !empty( $_REQUEST['gift_receiver_email'][0][0] ) && is_email( $_REQUEST['gift_receiver_email'][0][0] ) ) ? $_REQUEST['gift_receiver_email'][0][0] : $_REQUEST['billing_email'];
									$message_for_one = ( isset( $_REQUEST['gift_receiver_message'][0][0] ) && !empty( $_REQUEST['gift_receiver_message'][0][0] ) ) ? $_REQUEST['gift_receiver_message'][0][0] : '';
									unset( $_REQUEST['gift_receiver_email'][0][0] );
									unset( $_REQUEST['gift_receiver_message'][0][0] );
									foreach ( $_REQUEST['gift_receiver_email'] as $coupon_id => $emails ) {
										foreach ( $emails as $key => $email ) {
											$_REQUEST['gift_receiver_email'][$coupon_id][$key] = $email_for_one;
											$_REQUEST['gift_receiver_message'][$coupon_id][$key] = $message_for_one;
										}
									}
									if ( isset( $_REQUEST['gift_receiver_message'] ) && $_REQUEST['gift_receiver_message'] != '' ) {
										update_post_meta( $order_id, 'gift_receiver_message', $_REQUEST['gift_receiver_message'] );
									}
									break;

								case 'many':
									if ( isset( $_REQUEST['gift_receiver_email'][0][0] ) && !empty( $_REQUEST['gift_receiver_email'][0][0] ) ) {
										unset( $_REQUEST['gift_receiver_email'][0][0] );
									}
									if ( isset( $_REQUEST['gift_receiver_message'][0][0] ) && !empty( $_REQUEST['gift_receiver_message'][0][0] ) ) {
										unset( $_REQUEST['gift_receiver_message'][0][0] );
									}
									if ( isset( $_REQUEST['gift_receiver_message'] ) && $_REQUEST['gift_receiver_message'] != '' ) {
										update_post_meta( $order_id, 'gift_receiver_message', $_REQUEST['gift_receiver_message'] );
									}
									break;
							}
						}
						update_post_meta( $order_id, 'is_gift', 'yes' );
					} else {
						if ( !empty ( $_REQUEST['gift_receiver_email'][0][0] ) ) {
							unset( $_REQUEST['gift_receiver_email'][0][0] );
							foreach ( $_REQUEST['gift_receiver_email'] as $coupon_id => $emails ) {
								foreach ( $emails as $key => $email ) {
									$_REQUEST['gift_receiver_email'][$coupon_id][$key] = $_REQUEST['billing_email'];
								}
							}
						}
						update_post_meta( $order_id, 'is_gift', 'no' );
					}

					update_post_meta( $order_id, 'gift_receiver_email', $_REQUEST['gift_receiver_email'] );

				}

			}

			/**
			 * Function to verify gift certificate form details
			 */
			public function verify_gift_certificate_receiver_details() {
				global $woocommerce;

				if ( empty( $_POST['gift_receiver_email'] ) ) return;

				foreach ( $_POST['gift_receiver_email'] as $key => $emails ) {
					if ( !empty ($emails) ) {
						foreach ( $emails as $index => $email ) {

						$placeholder = __( 'Email address', 'wc_smart_coupons' );
						$placeholder .= '...';

							if ( empty( $email ) || $email == $placeholder ) {
								$_POST['gift_receiver_email'][$key][$index] = $_POST['billing_email'];
							} elseif ( !empty( $email ) && !is_email( $email ) ) {
								if ( $this->is_wc_gte_21() ) {
									wc_add_notice( __( 'Error: Gift Card Receiver&#146;s E-mail address is invalid.', 'wc_smart_coupons' ), 'error' );
								} else {
									$woocommerce->add_error( __( 'Error: Gift Card Receiver&#146;s E-mail address is invalid.', 'wc_smart_coupons' ) );
								}
								return;
							}
						}
					}
				}

			}

			/**
			 * Display form to enter receiver's details on checkout page
			 * 
			 * @param WC_Coupon $coupon
			 * @param array $product
			 */
			public function add_text_field_for_email( $coupon = '', $product = '' ) {
				global $total_coupon_amount;

				if ( empty( $coupon ) ) return;

					for ( $i = 1; $i <= $product['quantity']; $i++ ) {

					$coupon_amount = ( $this->is_coupon_amount_pick_from_product_price( $coupon ) ) ? $product['data']->price: $coupon->amount;

					// NEWLY ADDED CONDITION TO NOT TO SHOW TEXTFIELD IF COUPON AMOUNT IS "0"
					if($coupon_amount != '' || $coupon_amount > 0) {
									
						$total_coupon_amount += $coupon_amount;
						?>
						<div class="form_table">
							<div class="email_amount">
								<div class="amount"><p class="coupon_amount_label"><?php echo woocommerce_price( $coupon_amount ); ?></p></div>
								<div class="email"><input class="gift_receiver_email" type="text" placeholder="<?php _e( 'Email address', 'wc_smart_coupons' ); ?>..." name="gift_receiver_email[<?php echo $coupon->id; ?>][]" value="" /></div>
							</div>
							<div class="message_row">
								<div class="sc_message"><textarea placeholder="<?php _e('Message', 'wc_smart_coupons'); ?>..." class="gift_receiver_message" name="gift_receiver_message[<?php echo $coupon->id; ?>][]" cols="50" rows="5"></textarea></div>
							</div>
						</div>
						<?php
					}

				}

			}
						
			/**
			 * Function to display form for entering details of the gift certificate's receiver
			 */
			public function gift_certificate_receiver_detail_form() {
				global $woocommerce, $total_coupon_amount;

				$form_started = false;

				foreach ( $this->global_wc()->cart->cart_contents as $product ) {

					$coupon_titles = get_post_meta( $product['product_id'], '_coupon_title', true );

					$_product = $this->get_product( $product['product_id'] );
										
					$price = $_product->get_price();

					if ( $coupon_titles ) {

						foreach ( $coupon_titles as $coupon_title ) {

							$coupon = new WC_Coupon( $coupon_title );

							$pick_price_of_prod = get_post_meta( $coupon->id, 'is_pick_price_of_product', true ) ;
							$smart_coupon_gift_certificate_form_page_text  = get_option('smart_coupon_gift_certificate_form_page_text');
							$smart_coupon_gift_certificate_form_page_text  = ( !empty( $smart_coupon_gift_certificate_form_page_text ) ) ? $smart_coupon_gift_certificate_form_page_text : __( 'Store Credit Receiver Details', 'wc_smart_coupons');
							$smart_coupon_gift_certificate_form_details_text  = get_option('smart_coupon_gift_certificate_form_details_text');
							$smart_coupon_gift_certificate_form_details_text  = ( !empty( $smart_coupon_gift_certificate_form_details_text ) ) ? $smart_coupon_gift_certificate_form_details_text : '';     // Enter email address and optional message for Gift Card receiver

							// MADE CHANGES IN THE CONDITION TO SHOW FORM
							if ( $coupon->discount_type == 'smart_coupon' || ( $pick_price_of_prod == 'yes' &&  $price == '' ) || ( $pick_price_of_prod == 'yes' &&  $price != '' && $coupon->amount > 0)  ) {

								if ( !$form_started ) {

									$js = " 
												var is_multi_form = function() {
													var creditCount = jQuery('div#gift-certificate-receiver-form-multi div.form_table').length;
													
													if ( creditCount <= 1 ) {
														return false;
													} else {
														return true;
													}
												};

												jQuery('input#show_form').on('click', function(){
													if ( is_multi_form() ) {
														jQuery('ul.single_multi_list').slideDown();
													}
													jQuery('div.gift-certificate-receiver-detail-form').slideDown();
												});
												jQuery('input#hide_form').on('click', function(){
													if ( is_multi_form() ) {
														jQuery('ul.single_multi_list').slideUp();
													}
													jQuery('div.gift-certificate-receiver-detail-form').slideUp();
												});
												jQuery('input[name=sc_send_to]').on('change', function(){
													jQuery('div#gift-certificate-receiver-form-single').slideToggle(1);
													jQuery('div#gift-certificate-receiver-form-multi').slideToggle(1);
												});
											";

											if ( $this->is_wc_gte_21() ) {
												wc_enqueue_js( $js );
											} else {
												$woocommerce->add_inline_js( $js );
											}
									?>

									<div class="gift-certificate sc_info_box">
										<h3><?php _e( stripslashes( $smart_coupon_gift_certificate_form_page_text ) ); ?></h3>
											<?php if ( !empty( $smart_coupon_gift_certificate_form_details_text ) ) { ?>
											<p><?php _e( stripslashes( $smart_coupon_gift_certificate_form_details_text ) , 'wc_smart_coupons' ); ?></p>
											<?php } ?>
											<div class="gift-certificate-show-form">
												<p><?php _e( 'Your order contains store credit. What would you like to do?', 'wc_smart_coupons' ); ?></p>
												<ul class="show_hide_list" style="list-style-type: none;">
													<li><input type="radio" id="hide_form" name="is_gift" value="no" checked="checked" /> <label for="hide_form"><?php _e( 'Send store credit to me', 'wc_smart_coupons' ); ?></label></li>
													<li>
													<input type="radio" id="show_form" name="is_gift" value="yes" /> <label for="show_form"><?php _e( 'Gift store credit to someone else', 'wc_smart_coupons' ); ?></label>
													<ul class="single_multi_list" style="list-style-type: none;">
													<li><input type="radio" id="send_to_one" name="sc_send_to" value="one" checked="checked" /> <label for="send_to_one"><?php _e( 'Send to one person', 'wc_smart_coupons' ); ?></label>
													<input type="radio" id="send_to_many" name="sc_send_to" value="many" /> <label for="send_to_many"><?php _e( 'Send to different people', 'wc_smart_coupons' ); ?></label></li>
													</ul>
													</li>
												</ul>
											</div>
									<div class="gift-certificate-receiver-detail-form">
									<div class="clear"></div>
									<div id="gift-certificate-receiver-form-multi">
									<?php

									$form_started = true;

								}

								$this->add_text_field_for_email( $coupon, $product );

							}

						}

					}

				}

				if ( $form_started ) {
					?>
					</div>
					<div id="gift-certificate-receiver-form-single">
						<div class="form_table">
							<div class="email_amount">
								<div class="amount"><p class="coupon_amount_label"><?php echo woocommerce_price( $total_coupon_amount ); ?></p></div>
								<div class="email"><input class="gift_receiver_email" type="text" placeholder="<?php _e( 'Email address', 'wc_smart_coupons' ); ?>..." name="gift_receiver_email[0][0]" value="" /></div>
							</div>
							<div class="message_row">
								<div class="message"><textarea placeholder="<?php _e('Message', 'wc_smart_coupons'); ?>..." class="gift_receiver_message" name="gift_receiver_message[0][0]" cols="50" rows="5"></textarea></div>
							</div>
						</div>
					</div>                                 
					</div></div>
					<?php
				}

			}

			/**
			 * Function to show gift certificates that are attached with the product
			 */
			public function show_attached_gift_certificates() {
				global $post, $woocommerce, $wp_rewrite;

				$coupon_titles = get_post_meta( $post->ID, '_coupon_title', true );

				$_product = $this->get_product( $post->ID );
				
				$price = $_product->get_price();

				if ( $coupon_titles && count( $coupon_titles ) > 0 && !empty( $price ) ) {

					$all_discount_types = ( $this->is_wc_gte_21() ) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();
					$smart_coupons_product_page_text = get_option('smart_coupon_product_page_text');
					$smart_coupons_product_page_text = ( !empty( $smart_coupons_product_page_text ) ) ? $smart_coupons_product_page_text : __('By purchasing this product, you will get following coupon(s):', 'wc_smart_coupons');

					$list_started = true;
					$js = "";

					foreach ( $coupon_titles as $coupon_title ) {

						$coupon = new WC_Coupon( $coupon_title );
						$is_pick_price_of_product = get_post_meta( $coupon->id, 'is_pick_price_of_product', true );

						if ( $_product->product_type == 'variable' && $is_pick_price_of_product == 'yes' ) {
							$js = "
								jQuery('div.gift-certificates').hide();
							";
						}

						if ( $list_started && !empty( $coupon->discount_type ) ) {
							echo '<div class="clear"></div>';
							echo '<div class="gift-certificates">';
							echo '<br /><p>' . __( stripslashes( $smart_coupons_product_page_text ) ) . '';
							echo '<ul>';
							$list_started = false;
						}

						switch ( $coupon->discount_type ) {

							case 'smart_coupon':
															
								//NEWLY ADDED TO EVENSHOW COUPON THAT HAS "is_pick_price_of_product" : TRUE
								if( get_post_meta( $coupon->id, 'is_pick_price_of_product', true ) == 'yes' ){
									$amount = ($_product->price > 0) ? __( 'Store Credit of ', 'wc_smart_coupons' ) . $_product->price : "" ;
								} else {
									$amount = __( 'Store Credit of ', 'wc_smart_coupons' ) . woocommerce_price( $coupon->amount );
								}
								
								break;

							case 'fixed_cart':
								$amount = woocommerce_price( $coupon->amount ).__( ' discount on your entire purchase.', 'wc_smart_coupons' );
								break;

							case 'fixed_product':
								$amount = woocommerce_price( $coupon->amount ).__( ' discount on product.', 'wc_smart_coupons' );
								break;

							case 'percent_product':
								$amount = $coupon->amount.'%'.__( ' discount on product.', 'wc_smart_coupons' );
								break;

							case 'percent':
								$amount = $coupon->amount.'%'.__( ' discount on your entire purchase.', 'wc_smart_coupons' );
								break;
						}
						if(!empty($amount)) echo '<li>' . $amount . '</li>';
					}
					if ( !$list_started ) {
					echo '</ul></p></div>';
					}
					if ( !empty( $js ) ) {
						if ( $this->is_wc_gte_21() ) {
							wc_enqueue_js( $js );
						} else {
							$woocommerce->add_inline_js( $js );
						}
					}
				}
			}

			/**
			 * Function for saving settings for Gift Certificate
			 */
			public function save_smart_coupon_admin_settings() {
				woocommerce_update_options( $this->sc_general_settings );
			}

			/**
			 * Function to display fields for configuring settings for Gift Certificate
			 */
			public function smart_coupon_admin_settings() {
				woocommerce_admin_fields( $this->sc_general_settings );
			}

			/**
			 * Function to display Smart Coupons general settings fields
			 * 
			 * @param array $wc_general_settings
			 * @return array $wc_general_settings including smart coupons general settings
			 */
			public function smart_coupons_admin_settings( $wc_general_settings = array() ) {
				if ( empty( $this->sc_general_settings ) ) return $wc_general_settings;
				return array_merge( $wc_general_settings, $this->sc_general_settings );
			}

			/**
			 * Function to display current balance associated with Gift Certificate
			 */
			public function show_smart_coupon_balance() {

				$smart_coupon_myaccount_page_text  = get_option( 'smart_coupon_myaccount_page_text' );
				$smart_coupons_myaccount_page_text = ( !empty( $smart_coupon_myaccount_page_text ) ) ? $smart_coupon_myaccount_page_text: __( 'Available Store Credit / Coupons', 'wc_smart_coupons' );
				$this->show_available_coupons( $smart_coupons_myaccount_page_text, 'myaccount' );

			}

			/**
			 * function to validate smart coupon for product
			 * 
			 * @param bool $valid
			 * @param WC_Product|null $product
			 * @param WC_Coupon|null $coupon
			 * @param array|null $values
			 * @return bool $valid
			 */
			public function smart_coupons_is_valid_for_product( $valid, $product = null, $coupon = null, $values = null ) {

				if ( empty( $product ) || empty( $coupon ) ) return $valid;

				if ( $coupon->discount_type == 'smart_coupon' ) {

					$product_cats = wp_get_post_terms( $product->id, 'product_cat', array( "fields" => "ids" ) );

					// Specific products get the discount
					if ( sizeof( $coupon->product_ids ) > 0 ) {

						if ( in_array( $product->id, $coupon->product_ids ) || ( isset( $product->variation_id ) && in_array( $product->variation_id, $coupon->product_ids ) ) || in_array( $product->get_parent(), $coupon->product_ids ) ) {
							$valid = true;
						}

					// Category discounts
					} elseif ( sizeof( $coupon->product_categories ) > 0 ) {

						if ( sizeof( array_intersect( $product_cats, $coupon->product_categories ) ) > 0 ) {
							$valid = true;
						}

					} else {
						// No product ids - all items discounted
						$valid = true;
					}

					// Specific product ID's excluded from the discount
					if ( sizeof( $coupon->exclude_product_ids ) > 0 ) {
						if ( in_array( $product->id, $coupon->exclude_product_ids ) || ( isset( $product->variation_id ) && in_array( $product->variation_id, $coupon->exclude_product_ids ) ) || in_array( $product->get_parent(), $coupon->exclude_product_ids ) ) {
							$valid = false;
						}
					}

					// Specific categories excluded from the discount
					if ( sizeof( $coupon->exclude_product_categories ) > 0 ) {
						if ( sizeof( array_intersect( $product_cats, $coupon->exclude_product_categories ) ) > 0 ) {
							$valid = false;
						}
					}

					// Sale Items excluded from discount
					if ( $coupon->exclude_sale_items == 'yes' ) {
						$product_ids_on_sale = wc_get_product_ids_on_sale();

						if ( in_array( $product->id, $product_ids_on_sale, true ) || ( isset( $product->variation_id ) && in_array( $product->variation_id, $product_ids_on_sale, true ) ) || in_array( $product->get_parent(), $product_ids_on_sale, true ) ) {
							$valid = false;
						}
					}

					
				}

				return $valid;
			}

			/**
			 * function to get discount amount for smart coupon
			 * 
			 * @param float $discount
			 * @param float $discounting_amount
			 * @param array|null $cart_item
			 * @param bool $single
			 * @param WC_Coupon|null $coupon
			 * @return float $discount
			 */
			public function smart_coupons_discounted_totals( $total = 0, $cart = null ) {

				if ( empty( $total ) ) return $total;

				$cart_contains_subscription = false;
				if ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) {
					$cart_contains_subscription = true;
				}

				if ( $cart_contains_subscription ) {

					$calculation_type = WC_Subscriptions_Cart::get_calculation_type();

					if ( $calculation_type == 'recurring_total' ) {
						return $total;
					}
					
				}

				$applied_coupons = $this->global_wc()->cart->get_applied_coupons();

				if ( ! empty( $applied_coupons ) ) {
					foreach ( $applied_coupons as $code ) {
						$coupon = new WC_Coupon( $code );
						if ( $coupon->is_valid() && $coupon->discount_type == 'smart_coupon' ) {
							$cart_discount_total = $this->global_wc()->cart->get_cart_discount_total();
							if ( $total > 0 && $coupon->amount > $total ) {
								$coupon->amount = $total;
							}
							$discount = min( $total, $coupon->amount );
							$total = $total - $discount;
							if ( $cart_contains_subscription ) {
								WC_Subscriptions_Cart::increase_coupon_discount_amount( $coupon->code, $discount, 0 );
							} else {
								if ( empty( $this->global_wc()->cart->coupon_discount_amounts ) ) {
									$this->global_wc()->cart->coupon_discount_amounts = array();
								}
								if ( empty( $this->global_wc()->cart->coupon_discount_amounts[ $coupon->code ] ) ) {
									$this->global_wc()->cart->coupon_discount_amounts[ $coupon->code ] = $discount;
								} else {
									$this->global_wc()->cart->coupon_discount_amounts[ $coupon->code ] += $discount;
								}
							}
							if ( empty( $this->global_wc()->cart->smart_coupon_credit_used ) ) {
								$this->global_wc()->cart->smart_coupon_credit_used = array();
							}
							if ( empty( $this->global_wc()->cart->smart_coupon_credit_used[$coupon->code] ) ) {
								$this->global_wc()->cart->smart_coupon_credit_used[$coupon->code] = $discount;
							} else {
								$this->global_wc()->cart->smart_coupon_credit_used[$coupon->code] += $discount;
							}
						}
					}
				}

				return $total;
			}

			/**
			 * function to get total credit used in an order 
			 * 
			 * @param WC_Order $order
			 * @return float $total_credit_used
			 */
			public function get_total_credit_used_in_order( $order = null ) {

				if ( empty( $order ) ) return 0;

				$total_credit_used = 0;

				$coupons = $order->get_items( 'coupon' );

				if ( ! empty( $coupons ) ) {

					foreach ( $coupons as $item_id => $item ) {

						if ( empty( $item['name'] ) ) continue;

						$coupon = new WC_Coupon( $item['name'] );

						if ( $coupon->discount_type != 'smart_coupon' ) continue;

						$total_credit_used += $item['discount_amount'];

					}

				}
				
				return $total_credit_used;

			}

			/**
			 * function to add details of discount coming from smart coupons
			 * 
			 * @param array $total_rows
			 * @param WC_Order $order
			 * @return array $total_rows
			 */
			public function add_smart_coupons_discount_details( $total_rows = array(), $order = null ) {

				if ( empty( $order ) ) return $total_rows;

				$total_credit_used = $this->get_total_credit_used_in_order( $order );

				$offset = array_search( 'order_total', array_keys( $total_rows ) );

				if ( $offset !== false && ! empty( $total_credit_used ) ) {

					$total_rows = array_merge(
							            array_slice( $total_rows, 0, $offset ),
							            array( 'smart_coupon' => array(
																	'label' => __( 'Store Credit Used:', 'wc_smart_coupons' ),
																	'value'	=> '-' . wc_price( $total_credit_used )
																)),
							            array_slice( $total_rows, $offset, null)
							        );

				}

				return $total_rows;

			}

			/**
			 * function to show store credit used in order admin panel
			 * 
			 * @param int $order_id
			 */
			public function admin_order_totals_add_smart_coupons_discount_details( $order_id = 0 ) {

				if ( empty( $order_id ) ) return;

				$order = $this->get_order( $order_id );

				$total_credit_used = $this->get_total_credit_used_in_order( $order );

				if ( empty( $total_credit_used ) ) return;

				?>

				<tr>
					<td class="label"><?php _e( 'Store Credit Used', 'wc_smart_coupons' ); ?> <span class="tips" data-tip="<?php _e( 'This is the total credit used.', 'wc_smart_coupons' ); ?>">[?]</span>:</td>
					<td class="total">
						<?php echo wc_price( $total_credit_used, array( 'currency' => $order->get_order_currency() ) ); ?>
					</td>
					<td width="1%"></td>
				</tr>

				<?php

			}

			/**
			 * Function to apply Gift Certificate's credit to cart
			 */
			public function apply_smart_coupon_to_cart() {

				$this->global_wc()->cart->smart_coupon_credit_used = array();

				$cart_contains_subscription = false;
				if ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) {
					$cart_contains_subscription = true;
				}

				if ( $cart_contains_subscription ) {

					$calculation_type = WC_Subscriptions_Cart::get_calculation_type();

					if ( $calculation_type == 'recurring_total' ) {
						return;
					}
					
				}
				
				if ( $this->global_wc()->cart->applied_coupons ) {

					foreach ( $this->global_wc()->cart->applied_coupons as $code ) {

						$smart_coupon = new WC_Coupon( $code );

						if ( $smart_coupon->is_valid() && $smart_coupon->discount_type == 'smart_coupon' ) {

							$order_total = $this->global_wc()->cart->cart_contents_total + $this->global_wc()->cart->tax_total + $this->global_wc()->cart->shipping_tax_total + $this->global_wc()->cart->shipping_total;

							if ( $this->global_wc()->cart->discount_total != 0 && ( $this->global_wc()->cart->discount_total + $smart_coupon->amount ) > $order_total ) {
								$smart_coupon->amount = $order_total - $this->global_wc()->cart->discount_total;
							} elseif( $smart_coupon->amount > $order_total ) {
								$smart_coupon->amount = $order_total;
							}

							$this->global_wc()->cart->discount_total      = $this->global_wc()->cart->discount_total + $smart_coupon->amount;
							if ( $cart_contains_subscription ) {
								WC_Subscriptions_Cart::increase_coupon_discount_amount( $code, $smart_coupon->amount );
							}
							$this->global_wc()->cart->smart_coupon_credit_used[$code]     = $smart_coupon->amount;
							
							//Code for displaying the price label for the store credit coupons
							if (empty($this->global_wc()->cart->coupon_discount_amounts)) {
								$this->global_wc()->cart->coupon_discount_amounts = array();
							}
							$this->global_wc()->cart->coupon_discount_amounts[$code]     = $smart_coupon->amount;
						}
					}
				}
			}

			/**
			 * Function to save Smart Coupon's contribution in discount
			 * 
			 * @param int $order_id
			 */
			public function smart_coupons_contribution( $order_id ) {

				if( $this->global_wc()->cart->applied_coupons ) {

					foreach( $this->global_wc()->cart->applied_coupons as $code ) {

						$smart_coupon = new WC_Coupon( $code );

						if($smart_coupon->discount_type == 'smart_coupon' ) {
													
							update_post_meta( $order_id, 'smart_coupons_contribution', $this->global_wc()->cart->smart_coupon_credit_used );

						}

					}

				}
			}

			/**
			 * Function to update Store Credit / Gift Ceritficate balance
			 * 
			 * @param int $order_id
			 */
			public function update_smart_coupon_balance( $order_id ) {

				$order = $this->get_order( $order_id );
				
				$order_used_coupons = $order->get_used_coupons();
							
				if( $order_used_coupons ) {

					$smart_coupons_contribution = get_post_meta( $order_id, 'smart_coupons_contribution', true );
					
					if ( ! isset( $smart_coupons_contribution ) || empty( $smart_coupons_contribution ) || ( is_array( $smart_coupons_contribution ) && count( $smart_coupons_contribution ) <= 0 ) ) return; 
				
					foreach( $order_used_coupons as $code ) {

						if ( array_key_exists( $code, $smart_coupons_contribution ) ) {
							
							$smart_coupon = new WC_Coupon( $code );

							if($smart_coupon->discount_type == 'smart_coupon' ) {

								$discount_amount = round( ( $smart_coupon->amount - $smart_coupons_contribution[$code] ), get_option( 'woocommerce_price_num_decimals', 2 ) );
								$credit_remaining = max( 0, $discount_amount );

								if ( $credit_remaining <= 0 && get_option( 'woocommerce_delete_smart_coupon_after_usage' ) == 'yes' ) {
										wp_delete_post( $smart_coupon->id );
								} else {
										update_post_meta( $smart_coupon->id, 'coupon_amount', $credit_remaining );
								}

							}
							
						}

					}
										
					delete_post_meta( $order_id, 'smart_coupons_contribution' );

				}
			}

			/**
			 * Function to return validity of Store Credit / Gift Certificate
			 * 
			 * @param boolean $valid
			 * @param WC_Coupon $coupon
			 * @return boolean $valid TRUE if smart coupon valid, FALSE otherwise
			 */
			public function is_smart_coupon_valid( $valid, $coupon ) {
				global $woocommerce;

				$applied_coupons = $this->global_wc()->cart->get_applied_coupons();
				if ( ! in_array( $coupon->code, $applied_coupons ) ) return $valid;

				if ( $valid && $coupon->discount_type == 'smart_coupon' && $coupon->amount <= 0 ) {
					$this->global_wc()->cart->remove_coupon( $coupon->code );
					if ( $this->is_wc_gte_21() ) {
						wc_add_notice( sprintf(__( 'Coupon removed. There is no credit remaining in %s.', 'wc_smart_coupons' ), '<strong>' . $coupon->code . '</strong>' ), 'error' );
					} else {
						$woocommerce->add_error( sprintf(__( 'Coupon removed. There is no credit remaining in %s.', 'wc_smart_coupons' ), '<strong>' . $coupon->code . '</strong>' ) );
					}
					return false;
				}

				return $valid;
			}

			/**
			 * Function to add new discount type 'smart_coupon'
			 * 
			 * @param array $discount_types existing discount types
			 * @return array $discount_types including smart coupon discount type
			 */
			public function add_smart_coupon_discount_type( $discount_types ) {
				$discount_types['smart_coupon'] = __('Store Credit / Gift Certificate', 'wc_smart_coupons');
				return $discount_types;
			}

			/**
			 * Function to search coupons
			 * 
			 * @param string $x search term
			 * @param array $post_types
			 */
			public function sc_json_search_coupons( $x = '', $post_types = array( 'shop_coupon' ) ) {
				global $woocommerce, $wpdb;

				check_ajax_referer( 'search-coupons', 'security' );

				$term = (string) urldecode(stripslashes(strip_tags($_GET['term'])));

				if (empty($term)) die();

				$args = array(
					'post_type'     	=> $post_types,
					'post_status'       => 'publish',
					'posts_per_page'    => -1,
					's'             	=> $term,
					'fields'            => 'all'
				);

				$posts = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'shop_coupon' AND post_title LIKE '$term%' AND post_status = 'publish'");

				$found_products = array();

				$all_discount_types = ( $this->is_wc_gte_21() ) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();

				if ($posts) foreach ($posts as $post) {

					$discount_type = get_post_meta($post->ID, 'discount_type', true);

					if ( !empty( $all_discount_types[$discount_type] ) ) {
						$discount_type = ' (Type: ' . $all_discount_types[$discount_type] . ')';
						$found_products[get_the_title( $post->ID )] = get_the_title( $post->ID ) . $discount_type;
					}

				}

				echo json_encode( $found_products );

				die();
			}

			/**
			 * Function to provide area for entering coupon code
			 */
			public function woocommerce_product_options_coupons() {
				global $post, $woocommerce;

				$all_discount_types = ( $this->is_wc_gte_21() ) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();

				?>
				<p class="form-field" id="sc-field"><label for="_coupon_title"><?php _e('Coupons', 'wc_smart_coupons'); ?></label>

				<?php if ( $this->is_wc_gte_23() ) { ?>

					<input type="hidden" class="wc-coupon-search" style="width: 50%;" id="_coupon_title" name="_coupon_title" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wc_smart_coupons' ); ?>" data-action="sc_json_search_coupons" data-multiple="true" data-selected="<?php
						
						$coupon_titles = array_filter( array_map( 'trim', (array) get_post_meta( $post->ID, '_coupon_title', true ) ) );
						$json_coupons    = array();

						if ( ! empty( $coupon_titles ) ) {

							foreach ( $coupon_titles as $coupon_title ) {

								$coupon = new WC_Coupon( $coupon_title );

								$discount_type = $coupon->discount_type;

								if ( ! empty( $discount_type ) ) $discount_type = sprintf(__( ' ( %s: %s )', 'wc_smart_coupons' ), __( 'Type', 'wc_smart_coupons' ), $all_discount_types[$discount_type] );

								$json_coupons[ $coupon_title ] = $coupon_title . $discount_type;

							}
						}

						echo esc_attr( json_encode( $json_coupons ) );

					?>" value="<?php echo implode( ',', array_keys( $json_coupons ) ); ?>" />

				<?php } else { ?>

					<select id="_coupon_title" name="_coupon_title[]" class="ajax_chosen_select_coupons" multiple="multiple" data-placeholder="<?php _e('Search for a coupon...', 'wc_smart_coupons'); ?>">

					<?php

					$coupon_titles = get_post_meta( $post->ID, '_coupon_title', true );

					if ( ! empty( $coupon_titles ) ) {

						foreach ( $coupon_titles as $coupon_title ) {

							$coupon = new WC_Coupon( $coupon_title );

							$discount_type = $coupon->discount_type;

							if ( ! empty( $discount_type ) ) $discount_type = sprintf(__( ' ( %s: %s )', 'wc_smart_coupons' ), __( 'Type', 'wc_smart_coupons' ), $all_discount_types[$discount_type] );

							echo '<option value="'.$coupon_title.'" selected="selected">'. $coupon_title . $discount_type .'</option>';

						}
					}
						?>
					</select>

				<?php } ?>

					<img class="help_tip" data-tip='<?php _e('These coupon/s will be given to customers who buy this product. The coupon code will be automatically sent to their email address on purchase.', 'wc_smart_coupons'); ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" width="16" height="16"/>

					</p>

					<script type="text/javascript">

						jQuery(function(){
						
						var updateSendCouponOnRenewals = function() {
							var prodType = jQuery('select#product-type').find('option:selected').val();
							<?php if( $this->is_wc_gte_23() ) { ?>
								var associatedCouponCount = jQuery('div[id^="s2id__coupon_title"] ul.select2-choices li.select2-search-choice').length;
							<?php } elseif( $this->is_wc_gte_21() ) { ?>
								var associatedCouponCount = jQuery('div[id^="_coupon_title_chosen"] ul.chosen-choices li.search-choice').length;
							<?php } else { ?>
								var associatedCouponCount = jQuery('div[id^="_coupon_title_chzn"] ul.chzn-choices li.search-choice').length;
							<?php } ?>
							if ( ( prodType == 'subscription' || prodType == 'variable-subscription' ) && associatedCouponCount > 0 ) {
								jQuery('p.send_coupons_on_renewals_field').show();
							} else {
								jQuery('p.send_coupons_on_renewals_field').hide();
							}
						};

						setTimeout(function(){updateSendCouponOnRenewals();}, 100);

						<?php if ( $this->is_wc_gte_23() ) { ?>

							if ( typeof getEnhancedSelectFormatString == "undefined" ) {
								function getEnhancedSelectFormatString() {
									var formatString = {
										formatMatches: function( matches ) {
											if ( 1 === matches ) {
												return smart_coupons_select_params.i18n_matches_1;
											}

											return smart_coupons_select_params.i18n_matches_n.replace( '%qty%', matches );
										},
										formatNoMatches: function() {
											return smart_coupons_select_params.i18n_no_matches;
										},
										formatAjaxError: function( jqXHR, textStatus, errorThrown ) {
											return smart_coupons_select_params.i18n_ajax_error;
										},
										formatInputTooShort: function( input, min ) {
											var number = min - input.length;

											if ( 1 === number ) {
												return smart_coupons_select_params.i18n_input_too_short_1
											}

											return smart_coupons_select_params.i18n_input_too_short_n.replace( '%qty%', number );
										},
										formatInputTooLong: function( input, max ) {
											var number = input.length - max;

											if ( 1 === number ) {
												return smart_coupons_select_params.i18n_input_too_long_1
											}

											return smart_coupons_select_params.i18n_input_too_long_n.replace( '%qty%', number );
										},
										formatSelectionTooBig: function( limit ) {
											if ( 1 === limit ) {
												return smart_coupons_select_params.i18n_selection_too_long_1;
											}

											return smart_coupons_select_params.i18n_selection_too_long_n.replace( '%qty%', number );
										},
										formatLoadMore: function( pageNumber ) {
											return smart_coupons_select_params.i18n_load_more;
										},
										formatSearching: function() {
											return smart_coupons_select_params.i18n_searching;
										}
									};

									return formatString;
								}
							}

							// Ajax product search box
							jQuery( ':input.wc-coupon-search' ).filter( ':not(.enhanced)' ).each( function() {
								var select2_args = {
									allowClear:  jQuery( this ).data( 'allow_clear' ) ? true : false,
									placeholder: jQuery( this ).data( 'placeholder' ),
									minimumInputLength: jQuery( this ).data( 'minimum_input_length' ) ? jQuery( this ).data( 'minimum_input_length' ) : '3',
									escapeMarkup: function( m ) {
										return m;
									},
									ajax: {
								        url:         '<?php echo admin_url("admin-ajax.php"); ?>',
								        dataType:    'json',
								        quietMillis: 250,
								        data: function( term, page ) {
								            return {
												term:     term,
												action:   jQuery( this ).data( 'action' ) || 'sc_json_search_coupons',
												security: '<?php echo wp_create_nonce("search-coupons"); ?>'
								            };
								        },
								        results: function( data, page ) {
								        	var terms = [];
									        if ( data ) {
												jQuery.each( data, function( id, text ) {
													terms.push( { id: id, text: text } );
												});
											}
								            return { results: terms };
								        },
								        cache: true
								    }
								};

								if ( jQuery( this ).data( 'multiple' ) === true ) {
									select2_args.multiple = true;
									select2_args.initSelection = function( element, callback ) {
										var data     = jQuery.parseJSON( element.attr( 'data-selected' ) );
										var selected = [];

										jQuery( element.val().split( "," ) ).each( function( i, val ) {
											selected.push( { id: val, text: data[ val ] } );
										});
										return callback( selected );
									};
									select2_args.formatSelection = function( data ) {
										return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
									};
								} else {
									select2_args.multiple = false;
									select2_args.initSelection = function( element, callback ) {
										var data = {id: element.val(), text: element.attr( 'data-selected' )};
										return callback( data );
									};
								}

								select2_args = jQuery.extend( select2_args, getEnhancedSelectFormatString() );

								jQuery( this ).select2( select2_args ).addClass( 'enhanced' );
							});

						<?php } else { ?>

							// Ajax Chosen Coupon Selectors
							jQuery("select.ajax_chosen_select_coupons").ajaxChosen({
								method:     'GET',
								url:        '<?php echo admin_url('admin-ajax.php'); ?>',
								dataType:   'json',
								afterTypeDelay: 100,
								data:       {
									action:         'sc_json_search_coupons',
									security:       '<?php echo wp_create_nonce("search-coupons"); ?>'
								}
							}, function (data) {

								var terms = {};

								jQuery.each(data, function (i, val) {
									terms[i] = val;
								});

								return terms;
							});

						<?php } ?>

						jQuery('select#product-type').on('change', function() {

							var productType = jQuery(this).find('option:selected').val();
							
							if ( productType == 'simple' || productType == 'variable' || productType == 'subscription' || productType == 'variable-subscription' ) {
								jQuery('p#sc-field').show();
							} else {
								jQuery('p#sc-field').hide();
							}

							updateSendCouponOnRenewals();

						});

						jQuery('#_coupon_title').on('change', function(){
							setTimeout( function() {
								updateSendCouponOnRenewals();
							}, 10 );
						});

					});

					</script>


				<?php

				woocommerce_wp_checkbox( array( 'id' => 'send_coupons_on_renewals', 'label' => __('Send coupons on renewals?', 'wc_smart_coupons'), 'description' => __('Check this box to send above coupons on each renewal order.', 'wc_smart_coupons') ) );

			}

			/**
			 * Function to save coupon code to database
			 * 
			 * @param int $post_id
			 * @param object $post
			 */
			public function woocommerce_process_product_meta_coupons( $post_id, $post ) {
				if ( empty($post_id) || empty($post) || empty($_POST) ) return;
				if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
				if ( is_int( wp_is_post_revision( $post ) ) ) return;
				if ( is_int( wp_is_post_autosave( $post ) ) ) return;
				if ( empty($_POST['woocommerce_meta_nonce']) || !wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' )) return;
				if ( !current_user_can( 'edit_post', $post_id )) return;
				if ( $post->post_type != 'product' ) return;

				if ( $_POST['product-type'] == "simple" || $_POST['product-type'] == "variable" || $_POST['product-type'] == "subscription" || $_POST['product-type'] == "variable-subscription" ) {
					if ( ! empty( $_POST['_coupon_title'] ) ) {
						if ( $this->is_wc_gte_23() ) {
							$coupon_titles = array_filter( array_map( 'trim', explode( ',', $_POST['_coupon_title'] ) ) );
						} else {
							$coupon_titles = $_POST['_coupon_title'];
						}
						update_post_meta( $post_id, '_coupon_title', $coupon_titles );
					} else {
						update_post_meta( $post_id, '_coupon_title', array() );
					}
				}

				if ( $_POST['product-type'] == "subscription" || $_POST['product-type'] == "variable-subscription" ) {
					if (isset($_POST['send_coupons_on_renewals'])) {
						update_post_meta( $post_id, 'send_coupons_on_renewals', $_POST['send_coupons_on_renewals'] );
					} else {
						update_post_meta( $post_id, 'send_coupons_on_renewals', 'no' );
					}
				}
			}

			/**
			 * Function to track whether coupon is used or not
			 * 
			 * @param int $order_id
			 */
			public function coupons_used( $order_id ) {
				
				// Update Smart Coupons balance when the order status is either 'processing' or 'completed'
				do_action( 'update_smart_coupon_balance', $order_id );
			
				$order = $this->get_order( $order_id );

				$email = get_post_meta( $order_id, 'gift_receiver_email', true );
								
				if ( $order->get_used_coupons() ) {
					$this->update_coupons( $order->get_used_coupons(), $email, '', 'remove' );
				}
			}

			/**
			 * Function to update details related to coupons
			 * 
			 * @param array $coupon_titles
			 * @param mixed $email
			 * @param array $product_ids array of product ids
			 * @param string $operation
			 * @param array $order_item
			 * @param array $gift_certificate_receiver array of gift receiver emails
			 * @param array $gift_certificate_receiver_name array of gift receiver name
			 * @param string $message_from_sender
			 * @param string $gift_certificate_sender_name
			 * @param string $gift_certificate_sender_email
			 * @param int $order_id
			 */
			public function update_coupons( $coupon_titles = array(), $email, $product_ids = '', $operation, $order_item = null, $gift_certificate_receiver = false, $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $order_id = '' ) {

				global $smart_coupon_codes;
				if ( !empty( $order_id ) ) {
					$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
				}
		
				if ( isset( $order_item['sc_called_credit'] ) ) return; // because it is already processed

				$prices_include_tax = (get_option('woocommerce_prices_include_tax')=='yes') ? true : false;

				if ( !empty( $coupon_titles ) ) {

					if ( isset( $order_item['qty'] ) && $order_item['qty'] > 1 ) {
						$qty = $order_item['qty'];
					} else {
						$qty = 1;
					}

					foreach ( $coupon_titles as $coupon_title ) {

						$coupon = new WC_Coupon( $coupon_title );

						$auto_generation_of_code = get_post_meta( $coupon->id, 'auto_generate_coupon', true);

						if ( ( $auto_generation_of_code == 'yes' || $coupon->discount_type == 'smart_coupon' ) && $operation == 'add' ) {

							if ( get_post_meta( $coupon->id, 'is_pick_price_of_product', true ) == 'yes' && $coupon->discount_type == 'smart_coupon' ) {
								$products_price = ( !$prices_include_tax ) ? $order_item['line_total'] : $order_item['line_total'] + $order_item['line_tax'];
								$amount = $products_price / $qty;
							} else {
								if ( $coupon->discount_type == 'fixed_cart' || $coupon->discount_type == 'fixed_product' ) {
									$amount = $coupon->amount * $qty;
								} else {
									$amount = $coupon->amount;
								}
							}

							$temp_gift_card_receivers_emails = get_post_meta( $order_id, 'temp_gift_card_receivers_emails', true );

							if ( !empty( $temp_gift_card_receivers_emails ) ) {
								$email = $temp_gift_card_receivers_emails;
							}

							$email_id = ( $auto_generation_of_code == 'yes' && $coupon->discount_type != 'smart_coupon' && !empty( $gift_certificate_sender_email ) ) ? $gift_certificate_sender_email : $email;

							if( $amount > 0 ) {
								$message_index = ( !empty( $email[$coupon->id] ) && is_array( $email[$coupon->id] ) ) ? array_search( $email_id, $email[$coupon->id], true ) : false;

								if ( $message_index !== false && isset( $receivers_messages[$coupon->id][$message_index] ) && !empty( $receivers_messages[$coupon->id][$message_index] ) ) {
									$message_from_sender = $receivers_messages[$coupon->id][$message_index];
									unset( $email[$coupon->id][$message_index] );
									update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
								} else {
									$message_from_sender = '';
								}
								$coupon_title =  $this->generate_smart_coupon( $email_id, $amount, $order_id, $coupon, $coupon->discount_type, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email );
							}

						} else {

							$coupon_receiver_email = ( $gift_certificate_sender_email != '' ) ? $gift_certificate_sender_email : $email;

							$sc_disable_email_restriction = get_post_meta( $coupon->id, 'sc_disable_email_restriction', true );

							if ( ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {
								$old_customers_email_ids = (array) maybe_unserialize( get_post_meta( $coupon->id, 'customer_email', true ) );
							}

							if ( $operation == 'add' && $auto_generation_of_code != 'yes' && $coupon->discount_type != 'smart_coupon') {

								if ( $qty && $operation == 'add' && ! ( $coupon->discount_type == 'percent_product' || $coupon->discount_type == 'percent' ) ) {
									$amount = $coupon->amount * $qty;
								} else {
									$amount = $coupon->amount;
								}

								if ( $qty > 0 && ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {
									for ( $i = 0; $i < $qty; $i++ ) 
										$old_customers_email_ids[] = $coupon_receiver_email;
								}

								$coupon_details = array(
									$coupon_receiver_email  =>  array(
										'parent'    => $coupon->id,
										'code'      => $coupon_title,
										'amount'    => $amount
									)
								);

								$this->sa_email_coupon( $coupon_details, $coupon->discount_type, $order_id );

							} elseif ( $operation == 'remove' && $coupon->discount_type != 'smart_coupon' && ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {

								$key = array_search( $coupon_receiver_email, $old_customers_email_ids );

								if ($key !== false) {
									unset( $old_customers_email_ids[$key] );
								}

							}

							if ( ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {
								update_post_meta( $coupon->id, 'customer_email', $old_customers_email_ids );
							}

						}

					}

				}

			}

			/**
			 * Get receiver's email addresses
			 * 
			 * @param array $coupon_details
			 * @param string $gift_certificate_sender_email
			 * @return array $receivers_email array of receiver's email
			 */
			public function get_receivers_detail( $coupon_details = array(), $gift_certificate_sender_email = '' ) {

				if ( count( $coupon_details ) <= 0 ) return 0;

				$receivers_email = array();

				foreach ( $coupon_details as $coupon_id => $emails ) {
					$discount_type = get_post_meta( $coupon_id, 'discount_type', true );
					if ( $discount_type == 'smart_coupon' ) {
						$receivers_email = array_merge( $receivers_email, array_diff( $emails, array( $gift_certificate_sender_email ) ) );
					}
				}

				return $receivers_email;
			}

			/**
			 * Function to process coupons based on change in order status
			 * 
			 * @param int $order_id
			 * @param string $operation
			 */
			public function process_coupons( $order_id, $operation ) {
				global $smart_coupon_codes;

				$smart_coupon_codes = array();
				$message_from_sender = '';

				$receivers_emails = get_post_meta( $order_id, 'gift_receiver_email', true );
				$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
				$is_coupon_sent   = get_post_meta( $order_id, 'coupon_sent', true );

				if ( $is_coupon_sent == 'yes' ) return;

				$sc_called_credit_details = get_post_meta( $order_id, 'sc_called_credit_details', true );
				
				$order = $this->get_order( $order_id );
				$order_items = (array) $order->get_items();

				if ( count( $order_items ) <= 0 ) {
					return;
				}
				
				if ( is_array( $receivers_emails ) && !empty( $receivers_emails ) ) {

					foreach ( $receivers_emails as $coupon_id => $emails ) {
						foreach ( $emails as $key => $email ) {
							if ( empty( $email ) ) {
								$email = $order->billing_email;
								$receivers_emails[$coupon_id][$key] = $email;
							}
						}
					}
					
					if ( count( $receivers_emails ) > 1 && isset( $receivers_emails[0][0] ) ) {
						unset( $receivers_emails[0] );   // Disable sending to one customer
					}
					$email = $receivers_emails;
				} else {
					$email = '';
				}

				$receivers_emails_list = $receivers_emails;
				if ( !empty( $email ) ) {
					update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
				}

				$gift_certificate_receiver = true;
				$gift_certificate_sender_name = $order->billing_first_name . ' ' . $order->billing_last_name;
				$gift_certificate_sender_email = $order->billing_email;
				$gift_certificate_receiver_name = '';

				$receivers_detail = array();
				$receiver_count = 0;

				if ( is_array( $sc_called_credit_details ) && count( $sc_called_credit_details ) > 0 && $operation == 'add' ) {
					
					$email_to_credit = array();

					foreach ( $order_items as $item_id => $item ) {
						
						$product = $order->get_product_from_item( $item );

						$coupon_titles = get_post_meta( $product->id, '_coupon_title', true );

						if ( $coupon_titles ) {
							
							foreach ( $coupon_titles as $coupon_title ) {
								$coupon = new WC_Coupon( $coupon_title );

								if ( !isset( $receivers_emails[$coupon->id] ) ) continue;
								for ( $i = 0; $i < $item['qty']; $i++ ) {
									if ( isset( $receivers_emails[$coupon->id][0] ) ) {
										if ( !isset( $email_to_credit[$receivers_emails[$coupon->id][0]] ) ) {
											$email_to_credit[$receivers_emails[$coupon->id][0]] = array();
										}
										if ( isset( $sc_called_credit_details[$item_id] ) && !empty( $sc_called_credit_details[$item_id] ) ) {

												if( $this->is_coupon_amount_pick_from_product_price( array( $coupon_title ) ) ) {
											$email_to_credit[$receivers_emails[$coupon->id][0]][] = $coupon->id . ':' . $sc_called_credit_details[$item_id];
												} else {
												$email_to_credit[$receivers_emails[$coupon->id][0]][] = $coupon->id . ':' . $coupon->amount;
												}
											
											unset( $receivers_emails[$coupon->id][0] );
											$receivers_emails[$coupon->id] = array_values( $receivers_emails[$coupon->id] );
										}
									}
								}

							}

						}
						if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && $product->get_price() >= 0 ) {
							$item['sc_called_credit'] = $sc_called_credit_details[$item_id];
						}
					}
				}
					
				if ( !empty( $email_to_credit ) && count( $email_to_credit ) > 0 ) {
					$update_temp_email = false;
					foreach ( $email_to_credit as $email_id => $credits ) {
						$email_to_credit[$email_id] = array_count_values( $credits );
						foreach ( $email_to_credit[$email_id] as $coupon_credit => $qty ) {
							$coupon_details = explode( ':', $coupon_credit );
							$coupon_title = get_the_title( $coupon_details[0] );
							$coupon = new WC_Coupon( $coupon_title );
							$credit_amount = $coupon_details[1];
							$message_index = array_search( $email_id, $email[$coupon->id], true );
							if ( $message_index !== false && isset( $receivers_messages[$coupon->id][$message_index] ) && !empty( $receivers_messages[$coupon->id][$message_index] ) ) {
								$message_from_sender = $receivers_messages[$coupon->id][$message_index];
								unset( $email[$coupon->id][$message_index] );
								$update_temp_email = true;
							} else {
								$message_from_sender = '';
							}
							$this->generate_smart_coupon( $email_id, ( $credit_amount * $qty ), $order_id, $coupon, 'smart_coupon', $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email );
							$smart_coupon_codes = array();
						}
					}
					if ( $update_temp_email ) {
						update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
					}
					
					foreach ( $email_to_credit as $email => $coupon_detail ) {
						if ( $email == $gift_certificate_sender_email ) continue;
						$receiver_count += count( $coupon_detail );
					}
				}
				
				if ( count( $order_items ) > 0 ) {
					 
					$flag = false;

					foreach( $order_items as $item_id => $item ) {

						$product = $order->get_product_from_item( $item );

						$coupon_titles = get_post_meta( $product->id, '_coupon_title', true );

						if ( $coupon_titles ) {

							$flag = true;

							if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && $product->get_price() >= 0 ) {
								$item['sc_called_credit'] = $sc_called_credit_details[$item_id];
							}

							$this->update_coupons( $coupon_titles, $email, '', $operation, $item, $gift_certificate_receiver, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $order_id );

							if ( $operation == 'add' && ! empty( $receivers_emails_list ) ) {
								$receivers_detail += $this->get_receivers_detail( $receivers_emails_list, $gift_certificate_sender_email );
							}

						}
					}

					if ( $flag && $operation == 'add' ) {
						update_post_meta($order_id, 'coupon_sent', 'yes');              // to know whether coupon has sent or not
					}
				}
								
				if ( ( count( $receivers_detail ) + $receiver_count ) > 0 ) {
					$this->acknowledge_gift_certificate_sender( $receivers_detail, $gift_certificate_receiver_name, $email, $gift_certificate_sender_email, ( count( $receivers_detail ) ) );
				}

				if ( $operation == 'add' ) {
					delete_post_meta( $order_id, 'temp_gift_card_receivers_emails' );
				}
				unset( $smart_coupon_codes );
			}

			/**
			 * Function to acknowledge sender of gift credit
			 * 
			 * @param array $receivers_detail
			 * @param string $gift_certificate_receiver_name
			 * @param mixed $email
			 * @param string $gift_certificate_sender_email
			 * @param int $receiver_count
			 */
			public function acknowledge_gift_certificate_sender( $receivers_detail = array(), $gift_certificate_receiver_name = '', $email = '', $gift_certificate_sender_email = '', $receiver_count = '' ) {

				if ( empty( $receiver_count ) ) return;

				ob_start();

				$subject = __( 'Gift Card sent successfully!', 'wc_smart_coupons' );

				do_action('woocommerce_email_header', $subject);

				echo sprintf(__('You have successfully sent %d %s to %s (%s)', 'wc_smart_coupons'), $receiver_count, _n( 'Gift Card', 'Gift Cards', count( $receivers_detail ), 'wc_smart_coupons'), $gift_certificate_receiver_name, implode( ', ', array_unique( $receivers_detail ) ) );

				do_action('woocommerce_email_footer');

				$message = ob_get_clean();
				woocommerce_mail( $gift_certificate_sender_email, $subject, $message );

			}

			/**
			 * Function to add details to coupons
			 * 
			 * @param int $order_id
			 */
			public function sa_add_coupons( $order_id ) {
				$this->process_coupons( $order_id, 'add' );
			}

			/**
			 * Function to remove details from coupons
			 * 
			 * @param int $order_id
			 */
			public function sa_remove_coupons( $order_id ) {
				$this->process_coupons( $order_id, 'remove' );
			}

			/**
			 * Function to send e-mail containing coupon code to customer
			 * 
			 * @param array $coupon_title associative array containing receiver's details
			 * @param string $discount_type
			 * @param int $order_id
			 * @param array $gift_certificate_receiver_name array of receiver's name
			 * @param string $message_from_sender
			 * @param string $gift_certificate_sender_name
			 * @param string $gift_certificate_sender_email
			 * @param boolean $is_gift whether it is a gift certificate or store credit
			 */
			public function sa_email_coupon( $coupon_title, $discount_type, $order_id = '', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $is_gift = '' ) {

				$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

				$url = ( get_option('permalink_structure') ) ? get_permalink( woocommerce_get_page_id('shop') ) : get_post_type_archive_link('product');

				if ( $discount_type == 'smart_coupon' && $is_gift == 'yes' ) {
					$gift_certificate_sender_name = trim( $gift_certificate_sender_name );
					$sender = ( !empty( $gift_certificate_sender_name ) ) ? $gift_certificate_sender_name : '';
					$sender .= ( !empty( $gift_certificate_sender_name ) ) ? ' (' : '';
					$sender .= ( !empty( $gift_certificate_sender_email ) ) ? $gift_certificate_sender_email : '';
					$sender .= ( !empty( $gift_certificate_sender_name ) ) ? ')' : '';
					$from = ' ' . __( 'from', 'wc_smart_coupons' ) . ' ';
					$smart_coupon_type = __( 'Gift Card', 'wc_smart_coupons' );
				} else {
					$from = '';
					$smart_coupon_type = __( 'Store Credit', 'wc_smart_coupons' );
				}

				$subject_string = sprintf(__( "Congratulations! You've received a %s ", 'wc_smart_coupons' ), ( ( $discount_type == 'smart_coupon' && ! empty( $smart_coupon_type ) ) ? $smart_coupon_type : 'coupon' ) );
				$subject_string = ( get_option( 'smart_coupon_email_subject' ) && get_option( 'smart_coupon_email_subject' ) != '' ) ? __( get_option( 'smart_coupon_email_subject' ), 'wc_smart_coupons' ): $subject_string;
				$subject_string .= ( !empty( $gift_certificate_sender_name ) ) ? $from . $gift_certificate_sender_name : '';

				$subject = apply_filters( 'woocommerce_email_subject_gift_certificate', sprintf( '%s: %s', $blogname, $subject_string ) );

				foreach ( $coupon_title as $email => $coupon ) {

					$amount = $coupon['amount'];
					$coupon_code = $coupon['code'];

					switch ( $discount_type ) {

							case 'smart_coupon':
									$email_heading  =  sprintf(__('You have received %s worth %s ', 'wc_smart_coupons'), $smart_coupon_type, woocommerce_price($amount) );
									break;

							case 'fixed_cart':
									$email_heading  =  sprintf(__('You have received a coupon worth %s (on entire purchase) ', 'wc_smart_coupons'), woocommerce_price($amount) );
									break;

							case 'fixed_product':
									$email_heading  =  sprintf(__('You have received a coupon worth %s (for a product) ', 'wc_smart_coupons'), woocommerce_price($amount) );
									break;

							case 'percent_product':
									$email_heading  =  sprintf(__('You have received a coupon worth %s%% (for a product) ', 'wc_smart_coupons'), $amount );
									break;

							case 'percent':
									$email_heading  =  sprintf(__('You have received a coupon worth %s%% (on entire purchase) ', 'wc_smart_coupons'), $amount );
									break;

					}
					
					if ( empty( $email ) ) {
						$email = $gift_certificate_sender_email;
					}   
					
					if ( !empty( $order_id ) ) {
						$coupon_receiver_details = get_post_meta( $order_id, 'sc_coupon_receiver_details', true );
						if ( !is_array( $coupon_receiver_details ) || empty( $coupon_receiver_details ) ) {
							$coupon_receiver_details = array();
						}
						$coupon_receiver_details[] = array(
								'code'      => $coupon_code,
								'amount'    => $amount,
								'email'     => $email,
								'message'   => $message_from_sender
							);
						update_post_meta( $order_id, 'sc_coupon_receiver_details', $coupon_receiver_details );
					}

					ob_start();

					include(apply_filters('woocommerce_gift_certificates_email_template', 'templates/email.php'));
					
					$message = ob_get_clean();

					woocommerce_mail( $email, $subject, $message );

				}

			}

			/**
			 * Allow overridding of Smart Coupon's template for email
			 * 
			 * @param string $template
			 * @return mixed $template
			 */
			public function woocommerce_gift_certificates_email_template_path( $template ) {

				$template_name  = 'email.php';

				$template = $this->locate_template_for_smart_coupons( $template_name, $template );

				// Return what we found
				return $template;

			}

			/**
			 * Allow overridding of Smart Coupon's template for credit of any amount
			 * 
			 * @param string $template
			 * @return mixed $template
			 */
			public function woocommerce_call_for_credit_form_template_path( $template ) {

				$template_name  = 'call-for-credit-form.php';

				$template = $this->locate_template_for_smart_coupons( $template_name, $template );

				// Return what we found
				return $template;
			}

			/**
			 * Locate template for Smart Coupons
			 * 
			 * @param string $template_name
			 * @param mixed $template
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
						$template_name
					)
				);

				// Get default template
				if ( ! $template )
					$template = $default_path . $template_name;

				return $template;
			}

			/**
			 * Check whether credit is sent or not
			 * 
			 * @param string $email_id
			 * @param WC_Coupon $coupon
			 * @return boolean
			 */
			public function is_credit_sent( $email_id, $coupon ) {

				global $smart_coupon_codes;

				if ( isset( $smart_coupon_codes[$email_id] ) && count( $smart_coupon_codes[$email_id] ) > 0 ) {
					foreach ( $smart_coupon_codes[$email_id] as $generated_coupon_details ) {
						if ( $generated_coupon_details['parent'] == $coupon->id ) return true;
					}
				}

				return false;

			}

			/**
			 * Generate unique string to be used as coupon code. Also add prefix or suffix if already set
			 * 
			 * @param string $email
			 * @param WC_Coupon $coupon
			 * @return string $unique_code
			 */
			public function generate_unique_code( $email = '', $coupon = '' ) {
				$unique_code = ( !empty( $email ) ) ? strtolower( uniqid( substr( preg_replace('/[^a-z0-9]/i', '', sanitize_title( $email ) ), 0, 5 ) ) ) : strtolower( uniqid() );

				if ( !empty( $coupon ) && get_post_meta( $coupon->id, 'auto_generate_coupon', true) == 'yes' ) {
					 $prefix = get_post_meta( $coupon->id, 'coupon_title_prefix', true);
					 $suffix = get_post_meta( $coupon->id, 'coupon_title_suffix', true);
					 $unique_code = $prefix . $unique_code . $suffix;
				}

				return $unique_code;
			}

			/**
			 * Function for generating Gift Certificate
			 * 
			 * @param mixed $email
			 * @param float $amount
			 * @param int $order_id
			 * @param WC_Coupon $coupon
			 * @param string $discount_type
			 * @param array $gift_certificate_receiver_name
			 * @param string $message_from_sender
			 * @param string $gift_certificate_sender_name
			 * @param string $gift_certificate_sender_email
			 * @return array of generated coupon details
			 */
			public function generate_smart_coupon( $email, $amount, $order_id = '', $coupon = '', $discount_type = 'smart_coupon', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '' ) {
				return apply_filters( 'generate_smart_coupon_action', $email, $amount, $order_id, $coupon, $discount_type, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email );
			}

			/**
			 * Function for generating Gift Certificate
			 * 
			 * @param mixed $email
			 * @param float $amount
			 * @param int $order_id
			 * @param WC_Coupon $coupon
			 * @param string $discount_type
			 * @param array $gift_certificate_receiver_name
			 * @param string $message_from_sender
			 * @param string $gift_certificate_sender_name
			 * @param string $gift_certificate_sender_email
			 * @return array $smart_coupon_codes associative array containing generated coupon details
			 */
			public function generate_smart_coupon_action( $email, $amount, $order_id = '', $coupon = '', $discount_type = 'smart_coupon', $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '' ) {

				if ( $email == '' ) return false;

				global $smart_coupon_codes;

				if ( !is_array( $email ) ) {
					$emails = array( $email => 1 );
				} else {
					$temp_email = get_post_meta( $order_id, 'temp_gift_card_receivers_emails', true );
					if ( !empty( $temp_email ) && count( $temp_email ) > 0 ) {
						$email = $temp_email;
					}
					$emails = array_count_values( $email[$coupon->id] );
				}

				if ( !empty( $order_id ) ) {
					$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
				}
				foreach ( $emails as $email_id => $qty ) {

					if ( $this->is_credit_sent( $email_id, $coupon ) ) continue;

					$smart_coupon_code = $this->generate_unique_code( $email_id, $coupon );

					$smart_coupon_args = array(
										'post_title'    => strtolower( $smart_coupon_code ),
										'post_content'  => '',
										'post_status'   => 'publish',
										'post_author'   => 1,
										'post_type'     => 'shop_coupon'
									);

					$smart_coupon_id = wp_insert_post( $smart_coupon_args );

					$type                           = ( !empty( $coupon ) && !empty( $coupon->discount_type ) ) ?  $coupon->discount_type: 'smart_coupon';
					$individual_use                 = ( !empty( $coupon ) ) ?  $coupon->individual_use: get_option('woocommerce_smart_coupon_individual_use');
					$minimum_amount                 = ( !empty( $coupon ) ) ?  $coupon->minimum_amount: '';
					$maximum_amount                 = ( !empty( $coupon ) ) ?  $coupon->maximum_amount: '';
					$product_ids                    = ( !empty( $coupon ) ) ?  implode( ',', $coupon->product_ids ): '';
					$exclude_product_ids            = ( !empty( $coupon ) ) ?  implode( ',', $coupon->exclude_product_ids ): '';
					$usage_limit                    = ( !empty( $coupon ) ) ?  $coupon->usage_limit: '';
					
					if ( $this->is_wc_gte_21() ) {
						$usage_limit_per_user           = ( !empty( $coupon ) ) ?  $coupon->usage_limit_per_user: '';
						$limit_usage_to_x_items         = ( !empty( $coupon ) ) ?  $coupon->limit_usage_to_x_items: '';
					}
					
					$expiry_date                    = ( !empty( $coupon ) && !empty( $coupon->expiry_date ) ) ?  date( 'Y-m-d', intval( $coupon->expiry_date ) ): '';
					
					$sc_coupon_validity             = ( !empty( $coupon ) ) ? get_post_meta( $coupon->id, 'sc_coupon_validity', true ) : '';

					if ( !empty( $sc_coupon_validity ) ) {
						$is_parent_coupon_expired = ( !empty( $expiry_date ) && ( strtotime( $expiry_date ) < time() ) ) ? true : false;
						if ( !$is_parent_coupon_expired ) {
							$validity_suffix = get_post_meta( $coupon->id, 'validity_suffix', true );
							$expiry_date = date( 'Y-m-d', strtotime( "+$sc_coupon_validity $validity_suffix" ) );
						}
					}

					$apply_before_tax               = ( !empty( $coupon ) ) ?  $coupon->apply_before_tax: 'no';
					$free_shipping                  = ( !empty( $coupon ) ) ?  $coupon->free_shipping: 'no';
					$product_categories             = ( !empty( $coupon ) ) ?  $coupon->product_categories: '';
					$exclude_product_categories     = ( !empty( $coupon ) ) ?  $coupon->exclude_product_categories: '';

					update_post_meta( $smart_coupon_id, 'discount_type', $type );
					update_post_meta( $smart_coupon_id, 'coupon_amount', ( $amount * $qty ) );
					update_post_meta( $smart_coupon_id, 'individual_use', $individual_use );
					update_post_meta( $smart_coupon_id, 'minimum_amount', $minimum_amount );
					update_post_meta( $smart_coupon_id, 'maximum_amount', $maximum_amount );
					update_post_meta( $smart_coupon_id, 'product_ids', $product_ids );
					update_post_meta( $smart_coupon_id, 'exclude_product_ids', $exclude_product_ids );
					update_post_meta( $smart_coupon_id, 'usage_limit', $usage_limit );

					if ( $this->is_wc_gte_21() ) {
						update_post_meta( $smart_coupon_id, 'usage_limit_per_user', $usage_limit_per_user );
						update_post_meta( $smart_coupon_id, 'limit_usage_to_x_items', $limit_usage_to_x_items );
					}

					update_post_meta( $smart_coupon_id, 'expiry_date', $expiry_date );

					$is_disable_email_restriction = ( !empty( $coupon ) ) ? get_post_meta( $coupon->id, 'sc_disable_email_restriction', true ) : '';
					if ( empty( $is_disable_email_restriction ) || $is_disable_email_restriction == 'no' ) {
						update_post_meta( $smart_coupon_id, 'customer_email', array( $email_id ) );
					}

					update_post_meta( $smart_coupon_id, 'apply_before_tax', $apply_before_tax  );
					update_post_meta( $smart_coupon_id, 'free_shipping', $free_shipping );
					update_post_meta( $smart_coupon_id, 'product_categories', $product_categories  );
					update_post_meta( $smart_coupon_id, 'exclude_product_categories', $exclude_product_categories );
					update_post_meta( $smart_coupon_id, 'generated_from_order_id', $order_id );

					$generated_coupon_details = array(
						'parent'    => ( !empty( $coupon ) ) ? $coupon->id : '',
						'code'      => $smart_coupon_code,
						'amount'    => ( $amount * $qty )
					);

					$smart_coupon_codes[$email_id][] = $generated_coupon_details;

					if ( !empty( $order_id ) ) {
						$is_gift = get_post_meta( $order_id, 'is_gift', true );
					} else {
						$is_gift = 'no';
					}

					if( is_array( $email ) && isset( $email[$coupon->id] ) ) {
						$message_index = array_search( $email_id, $email[$coupon->id], true );
						if ( $message_index !== false && isset( $receivers_messages[$coupon->id][$message_index] ) && !empty( $receivers_messages[$coupon->id][$message_index] ) ) {
							$message_from_sender = $receivers_messages[$coupon->id][$message_index];
							unset( $email[$coupon->id][$message_index] );
							update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
						}
					}
					$this->sa_email_coupon( array( $email_id => $generated_coupon_details ), $discount_type, $order_id, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $is_gift );

				}

				return $smart_coupon_codes;

			}

			/**
			 * Function to get current user's Credit amount
			 * 
			 * @return array $coupons Found valid coupons for specific customer
			 */
			public function get_customer_credit() {

				global $current_user;
				if ( get_option( 'woocommerce_smart_coupon_show_my_account' ) == 'no' || ! $current_user->user_email ) return array();

				get_currentuserinfo();

				$args = array(
					'post_type'         => 'shop_coupon',
					'post_status'       => 'publish',
					'posts_per_page'    => -1,
					'meta_query'        => array(
						'relation'  => 'AND',
						array(
							'key'       => 'customer_email',
							'value'     => $current_user->user_email,
							'compare'   => 'LIKE'
						),
						array(
							'key'       => 'coupon_amount',
							'value'     => 0,
							'compare'   => '>=',
							'type'      => 'NUMERIC'
						)
					)
				);

				$coupons = get_posts( $args );

				return $coupons;
			}

			/**
			 * Function to get globally available coupons to be shown
			 * 
			 * @return array $coupons Found list of valid coupons for everyone
			 */
			public function get_global_coupons() {

				if ( get_option( 'woocommerce_smart_coupon_show_my_account' ) == 'no' ) return array();

				$args = array(
					'post_type'         => 'shop_coupon',
					'post_status'       => 'publish',
					'posts_per_page'    => -1,
					'meta_query'        => array(
						'relation'  => 'AND',
						array(
							'key'       => 'customer_email',
							'value'     => serialize( array() ),
							'compare'   => 'LIKE'
						),
						array(
							'key'       => 'sc_is_visible_storewide',
							'value'     => 'yes',
							'compare'   => 'LIKE'
						),
						array(
							'key'       => 'auto_generate_coupon',
							'value'     => 'yes',
							'compare'   => 'NOT LIKE'
						),
						array(
							'key'       => 'discount_type',
							'value'     => 'smart_coupon',
							'compare'   => 'NOT LIKE'
						)
					)
				);

				$coupons = get_posts( $args );

				return $coupons;

			}

			/**
			 * Funtion to add "duplicate" action for coupons
			 * 
			 * @param array $action array of existing actions
			 * @param object $post
			 * @return array $actions including duplicate action of coupons
			 */
			public function woocommerce_duplicate_coupon_link_row($actions, $post){

				if ( function_exists( 'duplicate_post_plugin_activation' ) )
				return $actions;

				if ( ! current_user_can( 'manage_woocommerce' ) ) return $actions;

				if ( $post->post_type != 'shop_coupon' )
				return $actions;

				$actions['duplicate'] = '<a href="' . wp_nonce_url( admin_url( 'admin.php?action=duplicate_coupon&amp;post=' . $post->ID ), 'woocommerce-duplicate-coupon_' . $post->ID ) . '" title="' . __("Make a duplicate from this coupon", 'wc_smart_coupons')
				. '" rel="permalink">' .  __("Duplicate", 'wc_smart_coupons') . '</a>';

				return $actions;
			}

			/**
			 * Function to insert post meta values for duplicate coupon
			 * 
			 * @param int $id id of parent coupon
			 * @param int $new_id id of duplicated coupon
			 */
			public function woocommerce_duplicate_coupon_post_meta($id, $new_id){
				global $wpdb;
				$post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$id AND meta_key NOT IN ('expiry_date','usage_count')");

				if (count($post_meta_infos)!=0) {
					$sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
					foreach ($post_meta_infos as $meta_info) {
							$meta_key = $meta_info->meta_key;
							$meta_value = addslashes($meta_info->meta_value);
							$sql_query_sel[]= "SELECT $new_id, '$meta_key', '$meta_value'";
					}
					$sql_query.= implode(" UNION ALL ", $sql_query_sel);
					$wpdb->query($sql_query);
				}
			}


			/**
			 * Function to duplicate post taxonomies for the duplicate coupon
			 * 
			 * @param int $id id of parent coupon
			 * @param int $new_id id of duplicated coupon
			 * @param string $post_type
			 */
			public function woocommerce_duplicate_coupon_post_taxonomies($id, $new_id, $post_type){
				global $wpdb;
				$taxonomies = get_object_taxonomies($post_type);
				foreach ($taxonomies as $taxonomy) {
					$post_terms = wp_get_object_terms($id, $taxonomy);
					$post_terms_count = sizeof( $post_terms );
					for ($i=0; $i<$post_terms_count; $i++) {
							wp_set_object_terms($new_id, $post_terms[$i]->slug, $taxonomy, true);
					}
				}
			}

			/**
			 * Function to create duplicate coupon and copy all properties of the coupon to duplicate coupon
			 * 
			 * @param object $post
			 * @param int $post_parent
			 * @param string $post_status
			 * @return int $new_post_id
			 */
			public function woocommerce_create_duplicate_from_coupon( $post, $parent = 0, $post_status = '' ){
					global $wpdb;

					$new_post_author    = wp_get_current_user();
					$new_post_date      = current_time('mysql');
					$new_post_date_gmt  = get_gmt_from_date($new_post_date);

					if ( $parent > 0 ) {
							$post_parent        = $parent;
							$post_status        = $post_status ? $post_status : 'publish';
							$suffix             = '';
					} else {
							$post_parent        = $post->post_parent;
							$post_status        = $post_status ? $post_status : 'draft';
							$suffix             = __("(Copy)", 'wc_smart_coupons');
					}

					$new_post_type          = $post->post_type;
					$post_content           = str_replace("'", "''", $post->post_content);
					$post_content_filtered  = str_replace("'", "''", $post->post_content_filtered);
					$post_excerpt           = str_replace("'", "''", $post->post_excerpt);
					$post_title             = strtolower( str_replace("'", "''", $post->post_title).$suffix );
					$post_name              = str_replace("'", "''", $post->post_name);
					$comment_status         = str_replace("'", "''", $post->comment_status);
					$ping_status            = str_replace("'", "''", $post->ping_status);

					$wpdb->insert(
									$wpdb->posts,
									array(
											'post_author'               => $new_post_author->ID,
											'post_date'                 => $new_post_date,
											'post_date_gmt'             => $new_post_date_gmt,
											'post_content'              => $post_content,
											'post_content_filtered'     => $post_content_filtered,
											'post_title'                => $post_title,
											'post_excerpt'              => $post_excerpt,
											'post_status'               => $post_status,
											'post_type'                 => $new_post_type,
											'comment_status'            => $comment_status,
											'ping_status'               => $ping_status,
											'post_password'             => $post->post_password,
											'to_ping'                   => $post->to_ping,
											'pinged'                    => $post->pinged,
											'post_modified'             => $new_post_date,
											'post_modified_gmt'         => $new_post_date_gmt,
											'post_parent'               => $post_parent,
											'menu_order'                => $post->menu_order,
											'post_mime_type'            => $post->post_mime_type
										)
								);

					$new_post_id = $wpdb->insert_id;

					$this->woocommerce_duplicate_coupon_post_taxonomies( $post->ID, $new_post_id, $post->post_type );

					$this->woocommerce_duplicate_coupon_post_meta( $post->ID, $new_post_id );

					return $new_post_id;
			}

			/**
			 * Function to return post id of the duplicate coupon to be created
			 * 
			 * @param int $id id of the coupon to duplicate
			 * @return object $post Duplicated post object
			 */
			public function woocommerce_get_coupon_to_duplicate( $id ){
				global $wpdb;
					$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
					if (isset($post->post_type) && $post->post_type == "revision"){
							$id = $post->post_parent;
							$post = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID=$id");
					}
					return $post[0];
			}

			/**
			 * Function to validate condition and create duplicate coupon
			 */
			public function woocommerce_duplicate_coupon(){

				if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_page' == $_REQUEST['action'] ) ) ) {
					wp_die(__('No coupon to duplicate has been supplied!', 'wc_smart_coupons'));
				}

				// Get the original page
				$id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
				check_admin_referer( 'woocommerce-duplicate-coupon_' . $id );
				$post = $this->woocommerce_get_coupon_to_duplicate($id);

				if (isset($post) && $post!=null) {
					$new_id = $this->woocommerce_create_duplicate_from_coupon($post);

					// If you have written a plugin which uses non-WP database tables to save
					// information about a page you can hook this action to dupe that data.
					do_action( 'woocommerce_duplicate_coupon', $new_id, $post );

					// Redirect to the edit screen for the new draft page
					wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
					exit;
				} else {
					wp_die(__('Coupon creation failed, could not find original product:', 'wc_smart_coupons') . ' ' . $id);
				}

			}

			/**
			 * Function to call function to create duplicate coupon
			 */
			public function woocommerce_duplicate_coupon_action(){
				$this->woocommerce_duplicate_coupon();
			}


			/**
			 * Funtion to show search result based on email id included in customer email
			 * 
			 * @param object $wp
			 */
			public function woocommerce_admin_coupon_search( $wp ){
				global $pagenow, $wpdb;

				if( 'edit.php' != $pagenow ) return;
				if( !isset( $wp->query_vars['s'] ) ) return;
				if ($wp->query_vars['post_type']!='shop_coupon') return;

				$e = substr( $wp->query_vars['s'], 0, 6 );

				if( 'Email:' == substr( $wp->query_vars['s'], 0, 6 ) ) {

					$email = trim( substr( $wp->query_vars['s'], 6 ) );

					if( !$email ) return;

					$post_ids = $wpdb->get_col( 'SELECT post_id FROM '.$wpdb->postmeta.' WHERE meta_key="customer_email" AND meta_value LIKE "%'.$email.'%"; ' );

					if( !$post_ids ) return;

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

					if ( 'edit.php' != $pagenow ) return $query;
					if ( $typenow!='shop_coupon' ) return $query;

					$s = get_query_var( 's' );
					if ($s) return $query;

					$email = get_query_var( 'email' );

					if( $email ) {

						$post_type = get_post_type_object($wp->query_vars['post_type']);
						return sprintf(__("[%s with email of %s]", 'wc_smart_coupons'), $post_type->labels->singular_name, $email);
					}

					return $query;
			}

			/**
			 * funtion to register the coupon importer
			 */
			public function woocommerce_coupon_admin_init(){
				global $wpdb;

				if ( defined( 'WP_LOAD_IMPORTERS' ) ) {
					register_importer( 'woocommerce_smart_coupon_csv', __('WooCommerce Coupons', 'wc_smart_coupons'), __('Import <strong>coupons</strong> to your store using CSV file.', 'wc_smart_coupons'), array(  $this, 'coupon_importer')  );
				}

				if ( !empty( $_GET['action'] ) && ( $_GET['action'] == 'sent_gift_certificate' ) && !empty( $_GET['page'] ) && ( $_GET['page']=='woocommerce_smart_coupon_csv_import' ) ) {
					$email = $_POST['smart_coupon_email'];
					$amount = $_POST['smart_coupon_amount'];
					$message = stripslashes( $_POST['smart_coupon_message'] );
					$this->send_gift_certificate( $email, $amount, $message );
				}
			}

			/**
			 * Function to show import message
			 */
			public function woocommerce_show_import_message(){
				global $pagenow,$typenow;
				
				if( ! isset($_GET['show_import_message'])) return;
				
				if( isset($_GET['show_import_message']) && $_GET['show_import_message'] == true ){
					if( 'edit.php' == $pagenow && 'shop_coupon' == $typenow ){
						
						$imported = (!empty($_GET['imported'])) ? $_GET['imported'] : 0;
						$skipped = (!empty($_GET['skipped'])) ? $_GET['skipped'] : 0;
												
						echo '<div id="message" class="updated fade"><p>
								'.sprintf(__('Import complete - imported <strong>%s</strong>, skipped <strong>%s</strong>', 'wc_smart_coupons'), $imported, $skipped  ).'
						</p></div>';
					}
				}
			}

			/**
			 * Function to process & send gift certificate
			 * 
			 * @param string $email comma separated email address
			 * @param float $amount coupon amount
			 * @param string $message optional
			 */
			public function send_gift_certificate( $email, $amount, $message = '' ) {

				$emails = explode( ',', $email );

				foreach ( $emails as $email ) {

					$email = trim( $email );

					if ( count( $emails ) == 1 && ( !$email || !is_email($email) ) ) {

						$location = admin_url('admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate&email_error=yes');

					} elseif ( count( $emails ) == 1 && ( !$amount || !is_numeric($amount) ) ) {

						$location = admin_url('admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate&amount_error=yes');

					} elseif ( is_email( $email ) && is_numeric( $amount ) ) {

						$coupon_title = $this->generate_smart_coupon( $email, $amount, null, null, 'smart_coupon', null, $message );

						$location = admin_url('admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate&sent=yes');

					}
				}

				wp_safe_redirect($location);
			}

			/**
			 * Function to add submenu page for Coupon CSV Import
			 */
			public function woocommerce_coupon_admin_menu(){
				add_submenu_page('woocommerce', __( 'Smart Coupon', 'wc_smart_coupons' ), __( 'Smart Coupon', 'wc_smart_coupons' ), 'manage_woocommerce', 'woocommerce_smart_coupon_csv_import', array( $this, 'admin_page') );
			}

			/**
			 * funtion to show content on the Coupon CSV Importer page
			 */
			public function admin_page(){

				$tab = ( !empty($_GET['tab'] ) ? ( $_GET['tab'] == 'send_certificate' ? 'send_certificate': 'import' ) : 'generate_bulk_coupons' );

				?>

				<div class="wrap woocommerce">
					<h2>
						<?php echo __( 'Coupons', 'wc_smart_coupons' ); ?>
						<a href="<?php echo trailingslashit( admin_url() ) . 'post-new.php?post_type=shop_coupon'; ?>" class="add-new-h2"><?php echo __( 'Add Coupon', 'wc_smart_coupons' ); ?></a>
					</h2>
					<div id="smart_coupons_tabs">
						<h2 class="nav-tab-wrapper">
							<a href="<?php echo admin_url('edit.php?post_type=shop_coupon') ?>" class="nav-tab"><?php _e('Coupons', 'wc_smart_coupons'); ?></a>
							<a href="<?php echo admin_url('admin.php?page=woocommerce_smart_coupon_csv_import') ?>" class="nav-tab <?php echo ($tab == 'generate_bulk_coupons') ? 'nav-tab-active' : ''; ?>"><?php _e('Bulk Generate / Import Coupons', 'wc_smart_coupons'); ?></a>
							<a href="<?php echo admin_url('admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate') ?>" class="nav-tab <?php echo ($tab == 'send_certificate') ? 'nav-tab-active' : ''; ?>"><?php _e('Send Store Credit', 'wc_smart_coupons'); ?></a>
						</h2>
					</div>
					<?php
						if ( ! function_exists( 'mb_detect_encoding' ) && $_GET['tab'] != 'send_certificate' ) {
							echo '<div class="message error"><p>'.sprintf( __( '%s Please install and enable PHP extension %s', 'wc_smart_coupons' ), '<strong>'.__( 'Required', 'wc_smart_coupons' ).':</strong> ', '<code>mbstring</code>' ) . '<a href="http://www.php.net/manual/en/mbstring.installation.php" target="_blank">'. __( 'Click here', 'wc_smart_coupons' ) .'</a> '. __( 'for more details.', 'wc_smart_coupons' ) .'</p></div>';
						}

						switch ($tab) {
							case "send_certificate" :
								$this->admin_send_certificate();
							break;
								case "import" :
								$this->admin_import_page();
							break;
							default :
								$this->admin_generate_bulk_coupons_and_export();
							break;
						}
					?>

				</div>
				<?php

			}

			/**
			 * Coupon Import page content
			 */
			public function admin_import_page() {
				?>
				<div class="tool-box">
					<h3 class="title"><?php _e('Bulk Upload / Import Coupons using CSV file', 'wc_smart_coupons'); ?></h3>
					<p class="description"><?php _e('Upload a CSV file & click \'Import\' . Importing requires <code>post_title</code> column.', 'wc_smart_coupons'); ?></p>
					<p class="submit"><a class="button" href="<?php echo admin_url('admin.php?import=woocommerce_smart_coupon_csv'); ?>"><?php _e('Import Coupons', 'wc_smart_coupons'); ?></a> </p>
				</div>
				<?php
			}

			/**
			 * Send Gift Certificate page content
			 */
			public function admin_send_certificate() {

				if( !empty($_GET['sent']) && $_GET['sent']=='yes' ){
					echo '<div id="message" class="updated fade"><p><strong>' . __( 'Store Credit / Gift Card sent successfully.', 'wc_smart_coupons' ) . '</strong></p></div>';
				}

				?>
				<div class="tool-box">

					<h3 class="title"><?php _e('Send Store Credit / Gift Card', 'wc_smart_coupons'); ?></h3>
					<p class="description"><?php _e('Click "Send" to send Store Credit / Gift Card. *All field are compulsary.', 'wc_smart_coupons'); ?></p>

					<form action="<?php echo admin_url('admin.php?page=woocommerce_smart_coupon_csv_import&action=sent_gift_certificate'); ?>" method="post">

						<table class="form-table">
							<tr>
								<th>
									<label for="smart_coupon_email"><?php _e( 'Email ID', 'wc_smart_coupons' ); ?> *</label>
								</th>
								<td>
									<input type="text" name="smart_coupon_email" id="email" class="input-text" style="width: 100%;" />
								</td>
								<td>
									<?php
										if( !empty($_GET['email_error']) && $_GET['email_error']=='yes' ){
										  echo '<div id="message" class="error fade"><p><strong>' . __( 'Invalid email address.', 'wc_smart_coupons' ) . '</strong></p></div>';
										}
									?>
									<span class="description"><?php _e( 'Use comma "," as separator for multiple e-mail ids', 'wc_smart_coupons' ); ?></span>
								</td>
							</tr>

							<tr>
								<th>
									<label for="smart_coupon_amount"><?php _e( 'Coupon Amount', 'wc_smart_coupons' ); ?> *</label>
								</th>
								<td>
									<input type="number" min="0.01" step="any" name="smart_coupon_amount" id="amount" placeholder="<?php _e('0.00', 'wc_smart_coupons'); ?>" class="input-text" style="width: 30%;" />
								</td>
								<td>
									<?php
										if( !empty($_GET['amount_error']) && $_GET['amount_error']=='yes' ){
											  echo '<div id="message" class="error fade"><p><strong>' . __( 'Invalid amount.', 'wc_smart_coupons' ) . '</strong></p></div>';
										}
									?>
								</td>
							</tr>

							<?php
								$message = '';
								$editor_args = array(
									'textarea_name' => 'smart_coupon_message',
									'textarea_rows' => 10,
									'editor_class' => 'wp-editor-message',
									'media_buttons' => true,
									'tinymce' => false
								);
							?>

							<tr>
								<th>
									<label for="smart_coupon_message"><?php _e( 'Message', 'wc_smart_coupons' ); ?></label>
								</th>
								<td colspan="2">
									<?php wp_editor( $message, 'edit_smart_coupon_message', $editor_args ); ?>
								</td>
							</tr>

						</table>

						<p class="submit"><input type="submit" class="button" value="<?php _e('Send', 'wc_smart_coupons'); ?>" /></p>

					</form>
				</div>
				<?php
			}

			/**
			 * Form to show 'Auto generate Bulk Coupons' with other fields
			 */
			public function admin_generate_bulk_coupons_and_export(){
				global $woocommerce, $woocommerce_smart_coupon;
				
				$upload_url     = wp_upload_dir();
				$upload_path    = $upload_url['path'];
				$suffix         = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				$assets_path    = str_replace( array( 'http:', 'https:' ), '', $this->global_wc()->plugin_url() ) . '/assets/';

				if ( $this->is_wc_gte_21() ) {

					wp_register_script( 'woocommerce_admin', $this->global_wc()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), $this->global_wc()->version );
					wp_register_script( 'woocommerce_admin_meta_boxes', $this->global_wc()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'accounting', 'round' ), $this->global_wc()->version );
					
					$params = array ('ajax_url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce( "search-products" ) );
					
					if ( $this->is_wc_gte_23() ) {
						wp_register_script( 'select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js', array( 'jquery' ), '3.5.2' );
						wp_register_script( 'wc-enhanced-select', WC()->plugin_url() . '/assets/js/admin/wc-enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), WC_VERSION );
						wp_localize_script( 'select2', 'wc_select_params', array(
							'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'wc_smart_coupons' ),
						) );
						wp_localize_script( 'wc-enhanced-select', 'wc_enhanced_select_params', $params );

						$locale  = localeconv();
						$decimal = isset( $locale['decimal_point'] ) ? $locale['decimal_point'] : '.';

						$woocommerce_admin_params = array(
							'i18n_decimal_error'                => sprintf( __( 'Please enter in decimal (%s) format without thousand separators.', 'wc_smart_coupons' ), $decimal ),
							'i18n_mon_decimal_error'            => sprintf( __( 'Please enter in monetary decimal (%s) format without thousand separators and currency symbols.', 'wc_smart_coupons' ), wc_get_price_decimal_separator() ),
							'i18n_country_iso_error'            => __( 'Please enter in country code with two capital letters.', 'wc_smart_coupons' ),
							'i18_sale_less_than_regular_error'  => __( 'Please enter in a value less than the regular price.', 'wc_smart_coupons' ),
							'decimal_point'                     => $decimal,
							'mon_decimal_point'                 => wc_get_price_decimal_separator()
						);

						wp_localize_script( 'woocommerce_admin', 'woocommerce_admin', $woocommerce_admin_params );
					} else {
						wp_register_script( 'ajax-chosen', $this->global_wc()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array('jquery', 'chosen'), $this->global_wc()->version );
						wp_register_script( 'chosen', $this->global_wc()->plugin_url() . '/assets/js/chosen/chosen.jquery' . $suffix . '.js', array('jquery'), $this->global_wc()->version );
					}
					
					wp_enqueue_script( 'woocommerce_admin' );
					wp_enqueue_script( 'woocommerce_admin_meta_boxes' );

					if ( $this->is_wc_gte_23() ) {
						wp_enqueue_script( 'select2' );
						wp_enqueue_script( 'wc-enhanced-select' );
					} else {
						wp_enqueue_script( 'ajax-chosen' );
						wp_enqueue_script( 'chosen' );
					}

					wp_localize_script( 'woocommerce_admin_meta_boxes', 'woocommerce_admin_meta_boxes', $params );
					wp_localize_script( 'woocommerce_admin_meta_boxes_variations', 'woocommerce_admin_meta_boxes_variations', $params );

					if ( $this->is_wc_gte_23() ) {
						wp_enqueue_style( 'select2', $assets_path . 'css/select2.css' );
					} else {
						wp_enqueue_style( 'woocommerce_chosen_styles', $assets_path . 'css/chosen.css' );
					}
					
				} else {
					
					wp_register_script( 'woocommerce_admin', $this->global_wc()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array ('jquery', 'jquery-ui-widget', 'jquery-ui-core' ), '1.0' );
					wp_register_script( 'woocommerce_writepanel', $this->global_wc()->plugin_url() . '/assets/js/admin/write-panels' . $suffix . '.js', array ('jquery' ) );
					wp_register_script( 'ajax-chosen', $this->global_wc()->plugin_url() . '/assets/js/chosen/ajax-chosen.jquery' . $suffix . '.js', array ('jquery' ), '1.0' );
					
					wp_enqueue_script( 'woocommerce_admin' );
					wp_enqueue_script( 'woocommerce_writepanel' );
					wp_enqueue_script( 'ajax-chosen' );
					
					$woocommerce_witepanel_params = array ('ajax_url' => admin_url( 'admin-ajax.php' ), 'search_products_nonce' => wp_create_nonce( "search-products" ) );
					
					wp_localize_script( 'woocommerce_writepanel', 'woocommerce_writepanel_params', $woocommerce_witepanel_params );
					
					wp_enqueue_style( 'woocommerce_chosen_styles', $this->global_wc()->plugin_url() . '/assets/css/chosen.css' );
					
				}
				
				wp_enqueue_style( 'woocommerce_admin_styles', $this->global_wc()->plugin_url() . '/assets/css/admin.css' );
				wp_enqueue_style( 'jquery-ui-style', (is_ssl()) ? 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' : 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
				
				// Adding style for help tip for WC 2.0
				if ( $this->is_wc_gte_20() ) {
					$style = "width:16px;height=16px;" ; 
				} else {
					$style = '';
				}

				if( isset($_POST['generate_and_import'] ) && isset($_POST['sc_export_and_import'])) {  
					
					$this->export_coupon( $_POST, '', '' );
				}                            
				?>
						
				<script type="text/javascript">
					jQuery(function(){
								
						jQuery('input#sc_expiry_date').datepicker({
							dateFormat: 'yy-mm-dd',
							numberOfMonths: 1,
							showButtonPanel: true
						});

						// jQuery('.message').hide();
						jQuery('input#generate_and_import').on('click', function(){
																					
							if( jQuery('input#no_of_coupons_to_generate').val() == "" ){
								jQuery("div#message").removeClass("updated fade").addClass("error fade");
								jQuery('div#message p').html( "<?php _e('Please enter a valid value for Number of Coupons to Generate', 'wc_smart_coupons'); ?>");
								return false;
							} else {
								jQuery("div#message").removeClass("error fade").addClass("updated fade").hide();
								return true;
							}                                                  
						});
								
						jQuery("input#sc_export_and_import").on('change', function() {

						   if(jQuery("input#sc_export_and_import").attr("checked") ) {
							   
							   jQuery('span#desc_for_file').text("") ;
							   jQuery('input#generate_and_import').val("<?php echo __( 'Generate and Export .CSV file', 'wc_smart_coupons' ); ?>") ;
							   jQuery('form#generate_coupons').attr('action', '<?php echo admin_url('admin.php?page=woocommerce_smart_coupon_csv_import'); ?>');
							   jQuery('p#woo_sc_is_email_imported_coupons_row').hide();
							   jQuery('input#woo_sc_is_email_imported_coupons').removeAttr('checked');

						   } else {

							   jQuery('input#generate_and_import').val("<?php echo __( 'Generate and Add to the Store', 'wc_smart_coupons' ); ?>") ;
							   jQuery('form#generate_coupons').attr('action', '<?php echo admin_url( 'admin.php?import=woocommerce_smart_coupon_csv&step=2'); ?>');
							   jQuery('p#woo_sc_is_email_imported_coupons_row').show();
							   
						   }
						   
						});

					});   
				</script> 
						
				<div id="message"><p></p></div>
				<div class="tool-box">

					<h3 class="title"><?php _e('Generate Coupons', 'wc_smart_coupons'); ?> | <small><a href="<?php echo trailingslashit( admin_url() ) . 'admin.php?import=woocommerce_smart_coupon_csv' ?>"><?php echo __( 'Import Coupons', 'wc_smart_coupons' ); ?></a></small></h3>
					<p class="description"><?php _e('You can bulk generate coupons using options below.', 'wc_smart_coupons'); ?></p>

					<form id="generate_coupons" action="<?php echo admin_url( 'admin.php?import=woocommerce_smart_coupon_csv&step=2'); ?>" method="post">
						<?php wp_nonce_field( 'import-woocommerce-coupon' ); ?>
						<div class="panel woocommerce_options_panel">
							<div class="option_group">
								<p class="form-field">                                
									<label for="no_of_coupons_to_generate"><?php _e( 'Number of Coupons to Generate ', 'wc_smart_coupons' ); ?> *</label>
									<input type="number" name="no_of_coupons_to_generate" id="no_of_coupons_to_generate" placeholder="<?php _e('10', 'wc_smart_coupons'); ?>" class="short" min="1" />                                
								</p>
														
								<p class="form-field">
									<label for="discount_type"><?php _e( 'Discount Type', 'wc_smart_coupons' ); ?> </label>
									<select id="discount_type" name="discount_type" class="select short">
									<?php
										$coupon_types = ( $this->is_wc_gte_21() ) ? wc_get_coupon_types() : $woocommerce->get_coupon_discount_types();
										foreach ( $coupon_types as $key => $value ) {
											echo '<option value="' . esc_attr( $key ) . '" >' . esc_html( $value ) . '</option>';
										}
									?>
									</select>
								</p>

								<p class="form-field">
									<label for="smart_coupon_amount"><?php _e( 'Coupon Amount', 'wc_smart_coupons' ); ?> </label>
									<input type="number" name="smart_coupon_amount" id="amount" placeholder="<?php _e('0.00', 'wc_smart_coupons'); ?>" class="short" min="0" step="any" />
									<img class="help_tip" data-tip='<?php _e( 'Value of the coupon.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>

								<p class="form-field">
									<label><?php _e( 'Allow free shipping', 'wc_smart_coupons' ); ?></label>
									<input type="checkbox" name="sc_free_shipping" id="sc_free_shipping"  />
									<span class="description"><?php echo sprintf(__( 'Check this box if the coupon grants free shipping. The <a href="%s">free shipping method</a> must be enabled with the "must use coupon" setting.', 'wc_smart_coupons' ), admin_url('admin.php?page=wc-settings&tab=shipping&section=WC_Shipping_Free_Shipping')); ?></span>
								</p>  

								<?php if ( ! $this->is_wc_gte_23() ) { ?>
									<p class="form-field">
										<label for="sc_apply_before_tax"><?php _e( 'Apply before tax', 'wc_smart_coupons' ); ?></label>
										<input type="checkbox" name="sc_apply_before_tax" id="sc_apply_before_tax"  />
										<span class="description"><?php _e( 'Check this box if the coupon should be applied before calculating cart tax.', 'wc_smart_coupons' ); ?></span>
									</p>
								<?php } ?>

								<p class="form-field">
									<label for="sc_expiry_date"><?php _e( 'Coupon expiry date', 'wc_smart_coupons' ); ?></label>
									<input type="text" name="sc_expiry_date" id="sc_expiry_date" style="width: 15%;" placeholder="<?php _e( 'YYYY-MM-DD', 'wc_smart_coupons' );?>" />                                                
								</p>

								<p class="form-field">
									<label for="sc_minimum_amount"><?php _e( 'Minimum spend', 'wc_smart_coupons' ); ?></label>
									<input type="number" name="sc_minimum_amount" id="sc_minimum_amount" min="0" step="any" placeholder="<?php _e( "No minimum", "wc_smart_coupons" ); ?>"/>
									<img class="help_tip" data-tip='<?php _e( 'This field allows you to set the minimum subtotal needed to use the coupon.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>

								<p class="form-field">
									<label for="sc_maximum_amount"><?php _e( 'Maximum spend', 'wc_smart_coupons' ); ?></label>
									<input type="number" name="sc_maximum_amount" id="sc_maximum_amount" min="0" step="any" placeholder="<?php _e( "No maximum", "wc_smart_coupons" ); ?>"/>
									<img class="help_tip" data-tip='<?php _e( 'This field allows you to set the maximum subtotal allowed when using the coupon.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>

								<p class="form-field">
									<label for="sc_individual_use"><?php _e( 'Individual use only', 'wc_smart_coupons' ); ?></label>
									<input type="checkbox" name="sc_individual_use" id="sc_individual_use"  />
									<span class="description"><?php _e( 'Check this box if the coupon cannot be used in conjunction with other coupons.', 'wc_smart_coupons' ); ?></span>
								</p>                                            
								
								<p class="form-field">
									<label for="sc_exclude_sale_items"><?php _e( 'Exclude sale items', 'wc_smart_coupons' ); ?></label>
									<input type="checkbox" name="sc_exclude_sale_items" id="sc_exclude_sale_items"  />
									<span class="description"><?php _e( 'Check this box if the coupon should not apply to items on sale. Per-item coupons will only work if the item is not on sale. Per-cart coupons will only work if there are no sale items in the cart.', 'wc_smart_coupons' ); ?></span>
								</p>
							
								<p class="form-field">
									<label for="product_ids"><?php _e( 'Products', 'wc_smart_coupons' ) ?></label>
									<?php if ( $this->is_wc_gte_23() ) { ?>
										<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="product_ids" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wc_smart_coupons' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="" value="" />
									<?php } else { ?>
										<select id="product_ids" name="product_ids[]" style="width: 50%;" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wc_smart_coupons' ); ?>"></select> 
									<?php } ?>
									<img class="help_tip" data-tip='<?php _e( 'Products which need to be in the cart to use this coupon or, for "Product Discounts", which products are discounted.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>
								
								<p class="form-field">
									<label for="exclude_product_ids"><?php _e( 'Exclude products', 'wc_smart_coupons' ) ?></label>
									<?php if ( $this->is_wc_gte_23() ) { ?>
										<input type="hidden" class="wc-product-search" data-multiple="true" style="width: 50%;" name="exclude_product_ids" data-placeholder="<?php _e( 'Search for a product&hellip;', 'wc_smart_coupons' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-selected="" value="" /> 
									<?php } else { ?>
										<select id="exclude_product_ids" name="exclude_product_ids[]" style="width: 50%;" class="ajax_chosen_select_products_and_variations" multiple="multiple" data-placeholder="<?php _e( 'Search for a product…', 'wc_smart_coupons' ); ?>"></select>
									<?php } ?>
									<img class="help_tip" data-tip='<?php _e( 'Products which must not be in the cart to use this coupon or, for "Product Discounts", which products are not discounted.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>
								
								<p class="form-field">
									<label for="product_ids"><?php _e( 'Product categories', 'wc_smart_coupons' ) ?></label>
									<select id="product_categories" name="product_categories[]" style="width: 50%;" class="<?php echo ( $this->is_wc_gte_23() ) ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'Any category', 'wc_smart_coupons' ); ?>">
										<?php
											$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

											if ( $categories ) foreach ( $categories as $cat ) {
												echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>';
											}
										?>
									</select> <img class="help_tip" data-tip='<?php _e( 'A product must be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will be discounted.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>
								
								<p class="form-field">
									<label for="exclude_product_categories"><?php _e( 'Exclude categories', 'wc_smart_coupons' ) ?></label>
									<select id="exclude_product_categories" name="exclude_product_categories[]" style="width: 50%;" class="<?php echo ( $this->is_wc_gte_23() ) ? 'wc-enhanced-select' : 'chosen_select'; ?>" multiple="multiple" data-placeholder="<?php _e( 'No categories', 'wc_smart_coupons' ); ?>">
										<?php
											$categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

											if ( $categories ) foreach ( $categories as $cat ) {
												echo '<option value="' . esc_attr( $cat->term_id ) . '">' . esc_html( $cat->name ) . '</option>';
											}
										?>
										</select> <img class="help_tip" data-tip='<?php _e( 'Product must not be in this category for the coupon to remain valid or, for "Product Discounts", products in these categories will not be discounted.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>
								  
						
								<p class="form-field">
									<label for="sc_customer_emails"><?php _e( 'Email restrictions', 'wc_smart_coupons' ); ?> </label>
									<input type="text" name="sc_customer_emails" id="sc_customer_emails" style="width: 50%;" class="input-text" placeholder="<?php _e( "No restrictions", "wc_smart_coupons" ); ?>"/>
									<img class="help_tip" data-tip='<?php _e( 'List of emails to check against the customer&#39;s billing email when an order is placed. Enter comma (,) separated e-mail ids. One coupon code will be assigned to one e-mail. Number of e-mails should be equal to number of coupons to be generated.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>

								<p class="form-field">
									<label for="sc_disable_email_restriction"><?php _e( 'Disable email restriction?', 'wc_smart_coupons' ); ?></label>
									<input type="checkbox" name="sc_disable_email_restriction" id="sc_disable_email_restriction"  />
									<span class="description"><?php _e( 'When checked, no e-mail id will be added through Smart Coupons plugin.', 'wc_smart_coupons' ); ?></span>
								</p>                                            
								
								<p class="form-field">
									<label for="sc_usage_limit_per_coupon"><?php _e( 'Usage limit per coupon', 'wc_smart_coupons' ); ?></label>
									<input type="number" name="sc_usage_limit_per_coupon" id="sc_usage_limit_per_coupon" min="0" placeholder="<?php _e( "Unlimited usage", "wc_smart_coupons" ); ?>"/>
									<img class="help_tip" data-tip='<?php _e( 'How many times this coupon can be used before it is void.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>

								<p class="form-field">
									<label for="limit_usage_to_x_items"><?php _e( 'Limit usage to X items', 'wc_smart_coupons' ); ?></label>
									<input type="number" name="limit_usage_to_x_items" id="limit_usage_to_x_items" min="0" placeholder="<?php _e( "Apply to all qualifying items in cart", "wc_smart_coupons" ); ?>" step="1" min="0">
									<img class="help_tip" data-tip='<?php _e( 'The maximum number of individual items this coupon can apply to when using product discounts. Leave blank to apply to all qualifying items in cart.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>

								<p class="form-field">
									<label for="sc_usage_limit_per_user"><?php _e( 'Usage limit per user', 'wc_smart_coupons' ); ?></label>
									<input type="number" name="sc_usage_limit_per_user" id="sc_usage_limit_per_user" min="0" placeholder="<?php _e( "Unlimited usage", "wc_smart_coupons" ); ?>"/>
									<img class="help_tip" data-tip='<?php _e( 'How many times this coupon can be used by an invidual user. Uses billing email for guests, and user ID for logged in users.', 'wc_smart_coupons' ) ?>' src="<?php echo $this->global_wc()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" /></p>
								</p>  
								   
								<?php
									if ( !wp_script_is( 'jquery-ui-datepicker' ) ) {
										wp_enqueue_script('jquery-ui-datepicker');
										wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
									}

								?>

								<p class="form-field">
									<label for="sc_prefix_for_code"><?php _e( 'Prefix for Coupon Code', 'wc_smart_coupons' ); ?> </label>
									<input type="text" name="sc_prefix_for_code" id="sc_prefix" style="width: 15%;" class="input-text" />
								</p>

								<p class="form-field">
									<label for="sc_suffix_for_code"><?php _e( 'Suffix for Coupon Code', 'wc_smart_coupons' ); ?> </label>
									<input type="text" name="sc_suffix_for_code" id="sc_suffix" style="width: 15%;" class="input-text" />
								</p>

								<p class="form-field">
									<label><?php echo '&nbsp;'; ?></label>
									<input type="checkbox" name="sc_export_and_import" id="sc_export_and_import"  /> 
									<span class="description"><?php _e( 'Generate only, do not add these coupons in WooCommerce. This will download a .CSV file which you can later import from ', 'wc_smart_coupons' ); 
										echo '<a href="' . trailingslashit( admin_url() ) . 'admin.php?import=woocommerce_smart_coupon_csv' . '">' . __( 'Import Coupons', 'wc_smart_coupons' ) . '</a>'; ?></span>
								</p>

								<p class="form-field" id="woo_sc_is_email_imported_coupons_row">
									<label><?php echo '&nbsp;'; ?></label>
									<input type="checkbox" name="woo_sc_is_email_imported_coupons" id="woo_sc_is_email_imported_coupons"  /> 
									<span class="description"><?php _e( 'E-mail imported coupon codes to respective customers/users.', 'wc_smart_coupons' ); ?></span>
								</p>

							</div>
						</div>
												
						<p class="submit"><input id="generate_and_import" name="generate_and_import" type="submit" class="button" value="<?php _e('Generate and Add to the Store', 'wc_smart_coupons'); ?>" /></p>

					</form>
				</div>
				<?php  
							
			}

			/**
			 * Add button to export coupons on Coupons admin page
			 */
			public function woocommerce_restrict_manage_smart_coupons(){
				global $typenow, $wp_query,$wp,$woocommerce_smart_coupon;

				$array = $wp_query->query;
				
				$sc_query = new WP_Query($array);
				
				if ( $typenow != 'shop_coupon' )
					return;

				if( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
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
						<button type="submit" class="button" id="export_coupons" name="export_coupons" value="<?php _e('Export', 'wc_smart_coupons'); ?>"><span class="dashicons dashicons-upload"></span><?php _e('Export', 'wc_smart_coupons'); ?></button>
					</div>
				<?php
			}
						
						
			/**
			 * Export coupons
			 */
			public function woocommerce_export_coupons(){
				global $typenow, $wp_query,$wp,$post;

				if(isset($_GET['export_coupons'])){

					$args = array(  'post_status' => '',
									'post_type' => '',
									'm' => '',
									'posts_per_page' => -1,
									'fields' => 'ids',
						); 

					if ( ! empty( $_REQUEST['sc_export_query_args'] ) ) {
						parse_str( $_REQUEST['sc_export_query_args'], $sc_args );
					}
					$args = array_merge( $args, $sc_args );
					 
					if(isset($_GET['coupon_type']) && $_GET['coupon_type'] != ''){
						$args['meta_query'] = array(
									array(
											'key'   => 'discount_type',
											'value'     => $_GET['coupon_type']
									)
							);
					}

					foreach($args as $key => $value){
						if(array_key_exists($key, $_GET)){
							$args[$key] = $_GET[$key];
						}
					}

					if($args['post_status'] == "all"){
						$args['post_status'] =  array("publish", "draft", "pending", "private","future");

					}

					$query = new WP_Query($args);
				  
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
				if( !empty( $post ) && isset( $post['generate_and_import'] ) ) {

					$customer_emails = array();
					$unique_code = '';
					if ( isset( $post['sc_customer_emails'] ) && !empty( $post['sc_customer_emails'] ) ) {
						$emails = explode( ',', $post['sc_customer_emails'] );
						if ( is_array( $emails ) && count( $emails ) > 0 ) {
							for ( $j = 1; $j <= $post['no_of_coupons_to_generate']; $j++ ) {
								$customer_emails[$j] = ( isset( $emails[$j-1] ) && is_email( $emails[$j-1] ) ) ? $emails[$j-1] : '';
							}
						}
					}

					$generated_codes = array();

					for( $i = 1; $i <= $post['no_of_coupons_to_generate']; $i++ ){
						 $customer_email = ( !empty( $customer_emails[$i] ) ) ? $customer_emails[$i] : '';
						 $unique_code = $this->generate_unique_code( $customer_email );
						 if ( ! empty( $generated_codes ) && in_array( $unique_code, $generated_codes ) ) {
						 	$max = ( $post['no_of_coupons_to_generate'] * 10 ) - 1;
						 	do {
						 		$unique_code_temp = $unique_code . mt_rand( 0, $max );
						 	} while ( in_array( $unique_code_temp, $generated_codes ) );
						 	$unique_code = $unique_code_temp;
						 }
						 $generated_codes[] = $unique_code;
						 $code = $post['sc_prefix_for_code'] . $unique_code . $post['sc_suffix_for_code'];

						 $data[$i]['post_title'] = strtolower( $code );
						  if( "fixed_cart" == $post['discount_type'] ){
							  $data[$i]['discount_type'] = "Cart Discount";
						  } elseif( "percent" == $post['discount_type'] ) {
							  $data[$i]['discount_type'] = "Cart % Discount";
						  } elseif( "fixed_product" == $post['discount_type'] ) {
							  $data[$i]['discount_type'] = "Product Discount";
						  } elseif( "percent_product" == $post['discount_type'] ) {
							  $data[$i]['discount_type'] = "Product % Discount";
						  } elseif( "smart_coupon" == $post['discount_type'] ) {
							  $data[$i]['discount_type'] = "Store Credit / Gift Certificate";
						  }

						 $data[$i]['coupon_amount']                 = $post['smart_coupon_amount'];
						 $data[$i]['individual_use']                = ( isset( $post['sc_individual_use'] ) ) ? 'yes' : 'no';
						 $data[$i]['product_ids']                   = ( isset( $post['product_ids'] ) ) ? ( ( $this->is_wc_gte_23() ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $post['product_ids'] ) : implode( '|', $post['product_ids'] ) ) : '';
						 $data[$i]['exclude_product_ids']           = ( isset( $post['exclude_product_ids'] ) ) ? ( ( $this->is_wc_gte_23() ) ? str_replace( array( ',', ' ' ), array( '|', '' ), $post['exclude_product_ids'] ) : implode( '|', $post['exclude_product_ids'] ) ) : '';
						 $data[$i]['usage_limit']                   = ( isset( $post['sc_usage_limit_per_coupon'] ) ) ? $post['sc_usage_limit_per_coupon'] : '';
						 $data[$i]['usage_limit_per_user']          = ( isset( $post['sc_usage_limit_per_user'] ) ) ? $post['sc_usage_limit_per_user'] : '';
						 $data[$i]['limit_usage_to_x_items']        = ( isset( $post['limit_usage_to_x_items'] ) ) ? $post['limit_usage_to_x_items'] : '';
						 $data[$i]['expiry_date']                   = ( isset( $post['sc_expiry_date'] ) ) ? $post['sc_expiry_date']: '';
						 if ( ! $this->is_wc_gte_23() ) {
						 	$data[$i]['apply_before_tax']              = ( isset( $post['sc_apply_before_tax'] ) ) ? 'yes' : 'no';
						 }
						 $data[$i]['free_shipping']                 = ( isset( $post['sc_free_shipping'] ) ) ? 'yes' : 'no';
						 $data[$i]['product_categories']            = ( isset( $post['product_categories'] ) ) ? implode( '|', $post['product_categories'] ) : '';
						 $data[$i]['exclude_product_categories']    = ( isset( $post['exclude_product_categories'] ) ) ? implode( '|', $post['exclude_product_categories'] ) : '';
						 $data[$i]['exclude_sale_items']            = ( isset( $post['sc_exclude_sale_items'] ) ) ? 'yes' : 'no';
						 $data[$i]['minimum_amount']                = ( isset( $post['sc_minimum_amount'] ) ) ? $post['sc_minimum_amount'] : '';
						 $data[$i]['maximum_amount']                = ( isset( $post['sc_maximum_amount'] ) ) ? $post['sc_maximum_amount'] : '';
						 $data[$i]['customer_email']                = ( ! empty( $customer_emails ) ) ? $customer_emails[$i] : '';
						 $data[$i]['sc_coupon_validity']            = ( isset( $post['sc_coupon_validity'] ) ) ? $post['sc_coupon_validity']: '';
						 $data[$i]['validity_suffix']               = ( isset( $post['validity_suffix'] ) ) ? $post['validity_suffix']: '';
						 $data[$i]['is_pick_price_of_product']		= ( isset( $post['is_pick_price_of_product'] ) ) ? 'yes': 'no';
						 $data[$i]['sc_disable_email_restriction']	= ( isset( $post['sc_disable_email_restriction'] ) ) ? 'yes': 'no';
						 $data[$i]['sc_is_visible_storewide']		= ( isset( $post['sc_is_visible_storewide'] ) ) ? 'yes': 'no';
						 $data[$i]['coupon_title_prefix']           = ( isset( $post['sc_prefix_for_code'] ) ) ? $post['sc_prefix_for_code']: '';
						 $data[$i]['coupon_title_suffix']           = ( isset( $post['sc_suffix_for_code'] ) ) ? $post['sc_suffix_for_code']: '';
						 $data[$i]['post_status']                   = "publish";

					 }
				}
			   
			   if( !empty( $get ) && isset( $get['export_coupons'] ) ) {
				   
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
												  AND pm.meta_key IN ('". implode( "','", array_keys( $coupon_postmeta_headers ) ) ."') )
												  WHERE p.ID IN (" . implode(',', $post_ids ) . ")
												  GROUP BY p.id  ORDER BY p.id

											";
												  
					$results = $wpdb->get_results ( $query_to_fetch_data, ARRAY_A );
				
					foreach($results as $result){

						$coupon_meta_key = explode('###', $result['coupon_meta_key']);
						$coupon_meta_value =  explode('###', $result['coupon_meta_value']) ;

						unset($result['coupon_meta_key']);
						unset($result['coupon_meta_value']);

						$coupon_meta_key_value = array_combine($coupon_meta_key,$coupon_meta_value);

						$coupon_data = array_merge($result,$coupon_meta_key_value);

						foreach($coupon_data as $key => $value){

							$id = $coupon_data['ID'];
							if($key != "ID"){
								$data[$id][$key] = (is_serialized($value)) ? implode(',',maybe_unserialize($value) ) : $value;
								
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
			public function export_coupon_csv( $columns_header, $data ){
				
				$getfield = '';
				
				foreach ( $columns_header as $key => $value ) {
						$getfield .= $key . ',';
				}

				$fields = substr_replace($getfield, '', -1);
				
				$each_field = array_keys( $columns_header );
				
				$csv_file_name = get_bloginfo( 'name' ) . gmdate('d-M-Y_H_i_s') . ".csv";

				foreach( (array) $data as $row ){
					for($i = 0; $i < count ( $columns_header ); $i++){
						if($i == 0) $fields .= "\n";
							
						if( array_key_exists($each_field[$i], $row) ){
							$row_each_field = $row[$each_field[$i]];
						} else {
							$row_each_field = '';
						}
							
						$array = str_replace(array("\n", "\n\r", "\r\n", "\r"), "\t", $row_each_field);
						
						$array = str_getcsv ( $array , ",", "\"" , "\\");
						
						$str = ( $array && is_array( $array ) ) ? implode( ', ', $array ) : '';
						$fields .= '"'. $str . '",'; 
					}           
					$fields = substr_replace($fields, '', -1); 
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
				
				$sc_postmeta_headers = array( 'sc_coupon_validity'         => __( 'Coupon Validity', 'wc_smart_coupons' ),
											'validity_suffix'              => __( 'Validity Suffix', 'wc_smart_coupons' ),
											'auto_generate_coupon'         => __( 'Auto Generate Coupon', 'wc_smart_coupons' ),
											'coupon_title_prefix'          => __( 'Coupon Title Prefix', 'wc_smart_coupons' ),
											'coupon_title_suffix'          => __( 'Coupon Title Suffix', 'wc_smart_coupons' ),
											'is_pick_price_of_product'     => __( 'Is Pick Price of Product', 'wc_smart_coupons' ),
											'sc_disable_email_restriction' => __( 'Disable Email Restriction', 'wc_smart_coupons' ),
											'sc_is_visible_storewide'      => __( 'Coupon Is Visible Storewide', 'wc_smart_coupons' )
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
			public function export_coupon( $post, $get, $post_ids ){
				
				$coupon_posts_headers = array ( 'post_title'    => __( 'Coupon Code','wc_smart_coupons' ),
												'post_excerpt'  => __( 'Post Excerpt','wc_smart_coupons' ),
												'post_status'   => __( 'Post Status','wc_smart_coupons' ),
												'post_parent'   => __( 'Post Parent','wc_smart_coupons' ),
												'menu_order'    => __( 'Menu Order','wc_smart_coupons' ),
												'post_date'     => __( 'Post Date', 'wc_smart_coupons')
												);

				$coupon_postmeta_headers = apply_filters( 'wc_smart_coupons_export_headers', 
												array( 'discount_type'              => __( 'Discount Type','wc_smart_coupons' ),
														'coupon_amount'                 => __( 'Coupon Amount','wc_smart_coupons' ),
														'free_shipping'                 => __( 'Free shipping','wc_smart_coupons' ),
														'expiry_date'                   => __( 'Expiry date','wc_smart_coupons' ),
														'minimum_amount'                => __( 'Minimum Spend','wc_smart_coupons' ),
														'maximum_amount'                => __( 'Maximum Spend','wc_smart_coupons' ),
														'individual_use'                => __( 'Individual USe','wc_smart_coupons' ),
														'exclude_sale_items'            => __( 'Exclude Sale Items','wc_smart_coupons' ),
														'product_ids'                   => __( 'Product IDs','wc_smart_coupons' ),
														'exclude_product_ids'           => __( 'Exclude product IDs','wc_smart_coupons' ),
														'product_categories'            => __( 'Product categories','wc_smart_coupons' ),
														'exclude_product_categories'    => __( 'Exclude Product categories','wc_smart_coupons' ),
														'customer_email'                => __( 'Customer Email','wc_smart_coupons' ),
														'usage_limit'                   => __( 'Usage Limit','wc_smart_coupons' ),
														'usage_limit_per_user'          => __( 'Usage Limit Per User','wc_smart_coupons' ),
														'limit_usage_to_x_items'        => __( 'Limit Usage to X Items','wc_smart_coupons' )
													) );

				$column_headers = array_merge( $coupon_posts_headers, $coupon_postmeta_headers );

				if(!empty($post)){
					$data = $this->generate_coupons_code( $post, '', '', array() );
				} else if(!empty($get)){
					$data = $this->generate_coupons_code( '', $get, $post_ids, $coupon_postmeta_headers );
				}

					$file_data = $this->export_coupon_csv( $column_headers, $data );
		   
					if( ( isset($post['generate_and_import']) && isset($post['sc_export_and_import']) ) || isset($get['export_coupons'])){
						
							ob_clean();
							header("Content-type: text/x-csv; charset=UTF-8"); 
							header("Content-Transfer-Encoding: binary");
							header("Content-Disposition: attachment; filename=".$file_data['file_name']); 
							header("Pragma: no-cache");
							header("Expires: 0");

							echo $file_data['file_content'];
							exit;
					} else {
						
							//Create CSV file
							$csv_folder     = $file_data['wp_upload_dir'];
							$filename       = str_replace( array( '\'', '"', ',' , ';', '<', '>','/',':' ), '', $file_data['file_name'] );
							$CSVFileName    = $csv_folder.$filename;
							$fp = fopen($CSVFileName, 'w');
							file_put_contents($CSVFileName, $file_data['file_content']);
							fclose($fp);

							return $CSVFileName;
					}
					
			}
						
			/**
			 * Funtion to perform importing of coupon from csv file
			 */
			public function coupon_importer(){

				if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) return;

				// Load Importer API
				require_once ABSPATH . 'wp-admin/includes/import.php';

				if ( ! class_exists( 'WP_Importer' ) ) {

					$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';

					if ( file_exists( $class_wp_importer ) ){
						require $class_wp_importer;

					}
				}

				// includes
				require dirname(__FILE__) . '/classes/class-wc-csv-coupon-import.php' ;
				require dirname(__FILE__) . '/classes/class-wc-coupon-parser.php' ;

				$wc_csv_coupon_import = new WC_CSV_Coupon_Import();

				$wc_csv_coupon_import->dispatch();

			}

			/**
			 * function to display the coupon data meta box.
			 */
			public function woocommerce_smart_coupon_options(){
				global $post;

				?>
				<script type="text/javascript">
					jQuery(function(){
						var customerEmails;
						var showHideApplyBeforeTax = function() {
							if ( jQuery('select#discount_type').val() == 'smart_coupon' ) {
								jQuery('p.apply_before_tax_field').hide();
								jQuery('input#is_pick_price_of_product').parent('p').show();
								jQuery('input#auto_generate_coupon').attr('checked', 'checked');
								jQuery('div#for_prefix_sufix').show();
								jQuery('div#sc_is_visible_storewide').hide();
								jQuery("p.auto_generate_coupon_field").hide();
								jQuery('p.sc_coupon_validity').show();
								jQuery('#free_shipping').parent('p').hide();
							} else {
								jQuery('p.apply_before_tax_field').show();
								jQuery('input#is_pick_price_of_product').parent('p').hide();
								jQuery('div#sc_is_visible_storewide').show();
								customerEmails = jQuery('input#customer_email').val();
								if ( customerEmails != undefined || customerEmails != '' ) {
									customerEmails = customerEmails.trim();
									if ( customerEmails == '' ) {
										jQuery('input#sc_is_visible_storewide').parent('p').show();
									} else {
										jQuery('input#sc_is_visible_storewide').parent('p').hide();
									}
								}
								jQuery("p.auto_generate_coupon_field").show();
								if (jQuery("#auto_generate_coupon").is(":checked")){
									jQuery('p.sc_coupon_validity').show();
								} else {
									jQuery('p.sc_coupon_validity').hide();
								}
								jQuery('#free_shipping').parent('p').show();
							}
						};

						var showHidePrefixSuffix = function() {
							if (jQuery("#auto_generate_coupon").is(":checked")){
								//show the hidden div
								jQuery("#for_prefix_sufix").show("fast");
								jQuery("div#sc_is_visible_storewide").hide();
								jQuery('p.sc_coupon_validity').show();
							} else {
								//otherwise, hide it
								jQuery("#for_prefix_sufix").hide("fast");
								jQuery("div#sc_is_visible_storewide").show();
								jQuery('p.sc_coupon_validity').hide();
							}
						}
						showHidePrefixSuffix();

						jQuery("#auto_generate_coupon").on('click', function(){
								showHidePrefixSuffix();
						});

						jQuery('select#discount_type').on('change', function(){
							showHideApplyBeforeTax();
							showHidePrefixSuffix();
						});

						jQuery('input#customer_email').on('keyup', function(){
							showHideApplyBeforeTax();
						});
					});
				</script>
				<p class="form-field sc_coupon_validity ">
					<label for="sc_coupon_validity"><?php _e('Valid for', 'wc_smart_coupons'); ?></label>
					<input type="number" class="short" name="sc_coupon_validity" id="sc_coupon_validity" value="<?php echo get_post_meta( $post->ID, 'sc_coupon_validity', true ); ?>" placeholder="0">
					<select name="validity_suffix" style="float: none;">
						<option value="days" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'days' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Days', 'wc_smart_coupons' ); ?></option>
						<option value="weeks" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'weeks' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Weeks', 'wc_smart_coupons' ); ?></option>
						<option value="months" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'months' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Months', 'wc_smart_coupons' ); ?></option>
						<option value="years" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'years' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Years', 'wc_smart_coupons' ); ?></option>
					</select>
				</p>
				<?php woocommerce_wp_checkbox( array( 'id' => 'is_pick_price_of_product', 'label' => __('Pick Product\'s Price?', 'wc_smart_coupons'), 'description' => __('Check this box to allow overwriting coupon\'s amount with Product\'s Price.', 'wc_smart_coupons') ) ); ?>

				<?php
				// autogeneration of coupon for store credit/gift certificate
				woocommerce_wp_checkbox( array( 'id' => 'auto_generate_coupon', 'label' => __('Auto Generation of Coupon', 'wc_smart_coupons'), 'description' => __('Check this box if the coupon needs to be auto generated', 'wc_smart_coupons') ) );

				echo '<div id="for_prefix_sufix">';
				// text field for coupon prefix
				woocommerce_wp_text_input( array( 'id' => 'coupon_title_prefix', 'label' => __('Prefix for Coupon Title', 'wc_smart_coupons'), 'placeholder' => _x('Prefix', 'placeholder', 'wc_smart_coupons'), 'description' => __('Adding prefix to the coupon title', 'wc_smart_coupons') ) );

				// text field for coupon suffix
				woocommerce_wp_text_input( array( 'id' => 'coupon_title_suffix', 'label' => __('Suffix for Coupon Title', 'wc_smart_coupons'), 'placeholder' => _x('Suffix', 'placeholder', 'wc_smart_coupons'), 'description' => __('Adding suffix to the coupon title', 'wc_smart_coupons') ) );

				echo '</div>';

				echo '<div id="sc_is_visible_storewide">';
				// for disabling e-mail restriction
				woocommerce_wp_checkbox( array( 'id' => 'sc_is_visible_storewide', 'label' => __( 'Show on cart / checkout?', 'wc_smart_coupons' ), 'description' => __('When checked, this coupon will be visible on cart / checkout page for everyone.', 'wc_smart_coupons') ) );

				echo '</div>';

				if ( !$this->is_wc_22() && !$this->is_wc_21() ) {
					woocommerce_wp_checkbox( array( 'id' => 'sc_disable_email_restriction', 'label' => __( 'Disable Email restriction?', 'wc_smart_coupons' ), 'description' => __('When checked, no e-mail id will be added through Smart Coupons plugin.', 'wc_smart_coupons') ) );
				}
								
			}

			/**
			 * function add additional field to disable email restriction
			 */
			public function sc_woocommerce_coupon_options_usage_restriction() {

				woocommerce_wp_checkbox( array( 'id' => 'sc_disable_email_restriction', 'label' => __( 'Disable Email restriction?', 'wc_smart_coupons' ), 'description' => __('When checked, no e-mail id will be added through Smart Coupons plugin.', 'wc_smart_coupons') ) );

			}

			/**
			 * function to process smart coupon meta
			 * 
			 * @param int $post_id
			 * @param object $post
			 */
			public function woocommerce_process_smart_coupon_meta( $post_id, $post ){
				if ( empty($post_id) || empty($post) || empty($_POST) ) return;
				if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
				if ( is_int( wp_is_post_revision( $post ) ) ) return;
				if ( is_int( wp_is_post_autosave( $post ) ) ) return;
				if ( empty($_POST['woocommerce_meta_nonce']) || !wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' )) return;
				if ( !current_user_can( 'edit_post', $post_id )) return;
				if ( $post->post_type != 'shop_coupon' ) return;

				if ( isset( $_POST['auto_generate_coupon'] ) ) {
					update_post_meta( $post_id, 'auto_generate_coupon', $_POST['auto_generate_coupon'] );
				} else {
					if ( get_post_meta( $post_id, 'discount_type', true ) == 'smart_coupon' ) {
						update_post_meta( $post_id, 'auto_generate_coupon', 'yes' );
					} else {
						update_post_meta( $post_id, 'auto_generate_coupon', 'no' );
					}
				}

				if ( $this->is_wc_gte_21() ) {

					if ( isset( $_POST['usage_limit_per_user']) ) {
						update_post_meta( $post_id, 'usage_limit_per_user', $_POST['usage_limit_per_user'] );
					}

					if ( isset( $_POST['limit_usage_to_x_items']) ) {
						update_post_meta( $post_id, 'limit_usage_to_x_items', $_POST['limit_usage_to_x_items'] );
					}
					
				}

				if ( get_post_meta( $post_id, 'discount_type', true ) == 'smart_coupon' ) {
					update_post_meta( $post_id, 'apply_before_tax', 'no' );
				}

				if ( isset( $_POST['coupon_title_prefix'] ) ) {
					update_post_meta( $post_id, 'coupon_title_prefix', $_POST['coupon_title_prefix'] );
				}

				if ( isset( $_POST['coupon_title_suffix'] ) ) {
					update_post_meta( $post_id, 'coupon_title_suffix', $_POST['coupon_title_suffix'] );
				}

				if ( isset( $_POST['sc_coupon_validity'] ) ) {
					update_post_meta( $post_id, 'sc_coupon_validity', $_POST['sc_coupon_validity'] );
					update_post_meta( $post_id, 'validity_suffix', $_POST['validity_suffix'] );
				}

				if ( isset( $_POST['sc_is_visible_storewide'] ) ) {
					update_post_meta( $post_id, 'sc_is_visible_storewide', $_POST['sc_is_visible_storewide'] );
				} else {
					update_post_meta( $post_id, 'sc_is_visible_storewide', 'no' );
				}

				if ( isset( $_POST['sc_disable_email_restriction'] ) ) {
					update_post_meta( $post_id, 'sc_disable_email_restriction', $_POST['sc_disable_email_restriction'] );
				} else {
					update_post_meta( $post_id, 'sc_disable_email_restriction', 'no' );
				}

				if ( isset( $_POST['is_pick_price_of_product'] ) ) {
					update_post_meta( $post_id, 'is_pick_price_of_product', $_POST['is_pick_price_of_product'] );
				} else {
					update_post_meta( $post_id, 'is_pick_price_of_product', 'no' );
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
							'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'wc_smart_coupons' ),
							'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'wc_smart_coupons' ),
							'ajax_url'                  => admin_url( 'admin-ajax.php' ),
							'search_products_nonce'     => wp_create_nonce( 'search-products' ),
							'search_customers_nonce'    => wp_create_nonce( 'search-customers' )
						) );
					}
				}

			} 

			/**
			 * Make meta data of this plugin, protected
			 * 
			 * @param bool $protected
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
									'validity_suffix'
	                            );
	            if ( in_array( $meta_key, $sc_meta, true ) ) {
	                return true;
	            }
	            return $protected;
	        }

	        /**
			 * function to trigger an additional hook while creating different views
			 * 
			 * @param array $views available views
			 * @return array $views
			 */
			public function smart_coupons_views_row( $views = null ) {

				global $typenow;

				if ( $typenow == 'shop_coupon' ) {
					do_action( 'smart_coupons_display_views' );
				}

				return $views;

			}

			/**
			 * function to add tabs to access Smart Coupons' feature
			 */
			public function smart_coupons_display_views() {
				?>
				<div id="smart_coupons_tabs">
					<h2 class="nav-tab-wrapper">
						<?php 
							echo '<a href="'.trailingslashit( admin_url() ) . 'edit.php?post_type=shop_coupon" class="nav-tab nav-tab-active">'.__( 'Coupons', 'wc_smart_coupons' ).'</a>';
							echo '<a href="'.trailingslashit( admin_url() ) . 'admin.php?page=woocommerce_smart_coupon_csv_import" class="nav-tab">'.__( 'Bulk Generate / Import Coupons', 'wc_smart_coupons' ).'</a>';
							echo '<a href="'.trailingslashit( admin_url() ) . 'admin.php?page=woocommerce_smart_coupon_csv_import&tab=send_certificate" class="nav-tab">'.__( 'Send Store Credit', 'wc_smart_coupons' ).'</a>';
						?>
					</h2>
				</div>
				<?php
			}

		}// End of class WC_Smart_Coupons

		/**
		 * function to initiate Smart Coupons & its functionality
		 */
		function initialize_smart_coupons() {
			$GLOBALS['woocommerce_smart_coupon'] = new WC_Smart_Coupons();
		}
		add_action( 'plugins_loaded', 'initialize_smart_coupons' );

	} // End class exists check

} // End woocommerce active check