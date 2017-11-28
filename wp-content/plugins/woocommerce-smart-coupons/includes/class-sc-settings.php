<?php
/**
 * Smart Coupons Storewide Settings
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Settings' ) ) {

	/**
	 * Class for handling storewide settings for Smart Coupons
	 */
	class WC_SC_Settings {

		/**
		 * Variable to hold instance of WC_SC_Settings
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * @var $sc_general_settings Array of Smart Coupons General Settings
		 */
		var $sc_general_settings;

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->sc_general_settings = array(
					array(
						'name'              => __( 'Store Credit / Gift Certificate', WC_SC_TEXT_DOMAIN ),
						'type'              => 'title',
						'desc'              => __( 'The following options are specific to Gift / Credit.', WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_options',
					),
					array(
						'name'              => __( 'Default Gift / Credit options', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( 'Show Credit on My Account page.', WC_SC_TEXT_DOMAIN ),
						'id'                => 'woocommerce_smart_coupon_show_my_account',
						'type'              => 'checkbox',
						'default'           => 'yes',
						'checkboxgroup'     => 'start',
					),
					array(
						'desc'              => __( 'Delete Gift / Credit, when credit is used up.', WC_SC_TEXT_DOMAIN ),
						'id'                => 'woocommerce_delete_smart_coupon_after_usage',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => '',
					),
					array(
						'desc'              => __( 'Individual use', WC_SC_TEXT_DOMAIN ),
						'id'                => 'woocommerce_smart_coupon_individual_use',
						'type'              => 'checkbox',
						'default'           => 'no',
						'checkboxgroup'     => '',
					),
					array(
						'name'              => __( 'E-mail subject', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( "This text will be used as subject for e-mails to be sent to customers. In case of empty value following message will be displayed <br/><b>Congratulations! You've received a coupon</b>", WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_email_subject',
						'type'              => 'textarea',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					 ),
					 array(
						'name'              => __( 'Product page text', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( 'Text to display associated coupon details on the product shop page. In case of empty value following message will be displayed <br/><b>By purchasing this product, you will get following coupon(s):</b> ', WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_product_page_text',
						'type'              => 'text',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					 ),
					 array(
						'name'              => __( 'Cart/Checkout page text', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( "Text to display as title of 'Available Coupons List' on Cart and Checkout page. In case of empty value following message will be displayed <br/><b>Available Coupons (Click on the coupon to use it)</b> ", WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_cart_page_text',
						'type'              => 'text',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					 ),
					 array(
						'name'              => __( 'My Account page text', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( 'Text to display as title of available coupons on My Account page. In case of empty value following message will be displayed <br/><b>Store Credit Available</b>', WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_myaccount_page_text',
						'type'              => 'text',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'Purchase Credit text', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( "Text for purchasing 'Store Credit of any amount' product. In case of empty value following message will be displayed <br/><b>Purchase Credit worth</b>", WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_store_gift_page_text',
						'type'              => 'text',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( "Title for Store Credit receiver's details form", WC_SC_TEXT_DOMAIN ),
						'desc'              => __( "Text to display as title of Receiver's details form. In case of empty value following message will be displayed <br/><b>Store Credit Receiver Details</b>", WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_gift_certificate_form_page_text',
						'type'              => 'text',
						'desc_tip'          => true,
						'css'               => 'min-width:300px;',
					),
					array(
						'name'              => __( 'Additional information about form', WC_SC_TEXT_DOMAIN ),
						'desc'              => __( "Text to display as additional information below 'Receiver's detail Form Title'. In case of empty value following message will be displayed <br/><b>Enter email address and optional message for Gift Card receiver</b>", WC_SC_TEXT_DOMAIN ),
						'id'                => 'smart_coupon_gift_certificate_form_details_text',
						'type'              => 'text',
						'css'               => 'min-width:300px;',
						'desc_tip'          => true,

					),
					array(
						'type'              => 'sectionend',
						'id'                => 'smart_coupon_options',
					),
				);

			add_action( 'woocommerce_update_options_general', array( $this, 'save_smart_coupon_admin_settings' ) );
			add_filter( 'woocommerce_general_settings', array( $this, 'smart_coupons_admin_settings' ) );

			add_action( 'admin_init', array( $this, 'add_delete_credit_after_usage_notice' ) );

		}

		/**
		 * Get single instance of WC_SC_Settings
		 *
		 * @return WC_SC_Settings Singleton object of WC_SC_Settings
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Function for saving settings for Gift Certificate
		 */
		public function save_smart_coupon_admin_settings() {
			woocommerce_update_options( $this->sc_general_settings );
		}

		/**
		 * Function to display Smart Coupons general settings fields
		 *
		 * @param array $wc_general_settings
		 * @return array $wc_general_settings including smart coupons general settings
		 */
		public function smart_coupons_admin_settings( $wc_general_settings = array() ) {
			if ( empty( $this->sc_general_settings ) ) { return $wc_general_settings;
			}
			return array_merge( $wc_general_settings, $this->sc_general_settings );
		}

		/**
		 * Function to Add Delete Credit After Usage Notice
		 */
		public function add_delete_credit_after_usage_notice() {

			$is_delete_smart_coupon_after_usage = get_option( 'woocommerce_delete_smart_coupon_after_usage' );

			if ( $is_delete_smart_coupon_after_usage != 'yes' ) { return;
			}

			$admin_email = get_option( 'admin_email' );

			$user = get_user_by( 'email', $admin_email );

			$current_user_id = get_current_user_id();

			if ( ! empty( $current_user_id ) && ! empty( $user->ID ) && $current_user_id == $user->ID ) {
				add_action( 'admin_notices', array( $this, 'delete_credit_after_usage_notice' ) );
				add_action( 'admin_footer', array( $this, 'ignore_delete_credit_after_usage_notice' ) );
			}

		}

		/**
		 * Function to Delete Credit After Usage Notice
		 */
		public function delete_credit_after_usage_notice() {

			$current_user_id = get_current_user_id();
			$is_hide_delete_after_usage_notice = get_user_meta( $current_user_id, 'hide_delete_credit_after_usage_notice', true );
			if ( $is_hide_delete_after_usage_notice !== 'yes' ) {
				echo '<div class="error"><p>';
				if ( ! empty( $_GET['page'] ) && $_GET['page'] == 'wc-settings' && empty( $_GET['tab'] ) ) {
					$page_based_text = __( 'Uncheck', WC_SC_TEXT_DOMAIN ) . ' &quot;<strong>' . __( 'Delete Gift / Credit, when credit is used up', WC_SC_TEXT_DOMAIN ) . '</strong>&quot;';
					$page_position = '#woocommerce_smart_coupon_show_my_account';
				} else {
					$page_based_text = '<strong>' . __( 'Important setting', WC_SC_TEXT_DOMAIN ) . '</strong>';
					$page_position = '';
				}
				echo sprintf( __( '%1$s: %2$s to avoid issues related to missing data for store credits. %3$s', WC_SC_TEXT_DOMAIN ), '<strong>' . __( 'WooCommerce Smart Coupons', WC_SC_TEXT_DOMAIN ) . '</strong>', $page_based_text, '<a href="' . admin_url( 'admin.php?page=wc-settings' . $page_position ) . '">' . __( 'Setting', WC_SC_TEXT_DOMAIN ) . '</a>' ) . ' <button type="button" class="button" id="hide_notice_delete_credit_after_usage">' . __( 'Hide this notice', WC_SC_TEXT_DOMAIN ) . '</button>';
				echo '</p></div>';
			}

		}

		/**
		 * Function to Ignore Delete Credit After Usage Notice
		 */
		public function ignore_delete_credit_after_usage_notice() {

			if ( ! wp_script_is( 'jquery' ) ) {
				wp_enqueue_script( 'jquery' );
			}

			?>
			<script type="text/javascript">
				jQuery(function(){
					jQuery('body').on('click', 'button#hide_notice_delete_credit_after_usage', function(){
						jQuery.ajax({
							url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
							type: 'post',
							dataType: 'json',
							data: {
								action: 'hide_notice_delete_after_usage',
								security: '<?php echo wp_create_nonce( 'hide-smart-coupons-notice' ); ?>'
							},
							success: function( response ) {
								if ( response.message == 'success' ) {
									jQuery('button#hide_notice_delete_credit_after_usage').parent().parent().remove();
								}
							}
						});
					});
				});
			</script>
			<?php

		}



	}

}

WC_SC_Settings::get_instance();
