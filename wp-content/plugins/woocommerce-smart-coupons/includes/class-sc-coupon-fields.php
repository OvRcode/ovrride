<?php
/**
 * Smart Coupons fields in coupons
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Coupon_Fields' ) ) {

	/**
	 * Class for handling Smart Coupons' field in coupons
	 */
	class WC_SC_Coupon_Fields {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Fields
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'woocommerce_coupon_options', array( $this, 'woocommerce_smart_coupon_options' ) );
			add_action( 'woocommerce_coupon_options_usage_restriction', array( $this, 'sc_woocommerce_coupon_options_usage_restriction' ) );
			add_filter( 'woocommerce_coupon_discount_types', array( $this, 'add_smart_coupon_discount_type' ) );
			add_action( 'save_post', array( $this, 'woocommerce_process_smart_coupon_meta' ), 10, 2 );

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
		 * Get single instance of WC_SC_Coupon_Fields
		 *
		 * @return WC_SC_Coupon_Fields Singleton object of WC_SC_Coupon_Fields
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * function to display the coupon data meta box.
		 */
		public function woocommerce_smart_coupon_options() {
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

					jQuery("#auto_generate_coupon").on('change', function(){
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
				<label for="sc_coupon_validity"><?php _e( 'Valid for', WC_SC_TEXT_DOMAIN ); ?></label>
				<input type="number" class="short" name="sc_coupon_validity" id="sc_coupon_validity" value="<?php echo get_post_meta( $post->ID, 'sc_coupon_validity', true ); ?>" placeholder="0">
				<select name="validity_suffix" style="float: none;">
					<option value="days" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'days' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Days', WC_SC_TEXT_DOMAIN ); ?></option>
					<option value="weeks" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'weeks' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Weeks', WC_SC_TEXT_DOMAIN ); ?></option>
					<option value="months" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'months' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Months', WC_SC_TEXT_DOMAIN ); ?></option>
					<option value="years" <?php echo ( ( get_post_meta( $post->ID, 'validity_suffix', true ) == 'years' ) ? 'selected="selected"' : '' ); ?>><?php _e( 'Years', WC_SC_TEXT_DOMAIN ); ?></option>
				</select>
			</p>
			<?php woocommerce_wp_checkbox( array( 'id' => 'is_pick_price_of_product', 'label' => __( 'Pick Product\'s Price?', WC_SC_TEXT_DOMAIN ), 'description' => __( 'Check this box to allow overwriting coupon\'s amount with Product\'s Price.', WC_SC_TEXT_DOMAIN ) ) ); ?>

			<?php
			// autogeneration of coupon for store credit/gift certificate
			woocommerce_wp_checkbox( array( 'id' => 'auto_generate_coupon', 'label' => __( 'Auto Generation of Coupon', WC_SC_TEXT_DOMAIN ), 'description' => __( 'Check this box if the coupon needs to be auto generated', WC_SC_TEXT_DOMAIN ) ) );

			echo '<div id="for_prefix_sufix">';
			// text field for coupon prefix
			woocommerce_wp_text_input( array( 'id' => 'coupon_title_prefix', 'label' => __( 'Prefix for Coupon Title', WC_SC_TEXT_DOMAIN ), 'placeholder' => _x( 'Prefix', 'placeholder', WC_SC_TEXT_DOMAIN ), 'description' => __( 'Adding prefix to the coupon title', WC_SC_TEXT_DOMAIN ) ) );

			// text field for coupon suffix
			woocommerce_wp_text_input( array( 'id' => 'coupon_title_suffix', 'label' => __( 'Suffix for Coupon Title', WC_SC_TEXT_DOMAIN ), 'placeholder' => _x( 'Suffix', 'placeholder', WC_SC_TEXT_DOMAIN ), 'description' => __( 'Adding suffix to the coupon title', WC_SC_TEXT_DOMAIN ) ) );

			echo '</div>';

			echo '<div id="sc_is_visible_storewide">';
			// for disabling e-mail restriction
			woocommerce_wp_checkbox( array( 'id' => 'sc_is_visible_storewide', 'label' => __( 'Show on cart / checkout?', WC_SC_TEXT_DOMAIN ), 'description' => __( 'When checked, this coupon will be visible on cart / checkout page for everyone.', WC_SC_TEXT_DOMAIN ) ) );

			echo '</div>';

		}

		/**
		 * function add additional field to disable email restriction
		 */
		public function sc_woocommerce_coupon_options_usage_restriction() {

			woocommerce_wp_checkbox( array( 'id' => 'sc_disable_email_restriction', 'label' => __( 'Disable Email restriction?', WC_SC_TEXT_DOMAIN ), 'description' => __( 'When checked, no e-mail id will be added through Smart Coupons plugin.', WC_SC_TEXT_DOMAIN ) ) );

		}

		/**
		 * function to process smart coupon meta
		 *
		 * @param int    $post_id
		 * @param object $post
		 */
		public function woocommerce_process_smart_coupon_meta( $post_id, $post ) {
			if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) { return;
			}
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return;
			}
			if ( is_int( wp_is_post_revision( $post ) ) ) { return;
			}
			if ( is_int( wp_is_post_autosave( $post ) ) ) { return;
			}
			if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( $_POST['woocommerce_meta_nonce'], 'woocommerce_save_data' ) ) { return;
			}
			if ( ! current_user_can( 'edit_post', $post_id ) ) { return;
			}
			if ( $post->post_type != 'shop_coupon' ) { return;
			}

			if ( isset( $_POST['auto_generate_coupon'] ) ) {
				update_post_meta( $post_id, 'auto_generate_coupon', $_POST['auto_generate_coupon'] );
			} else {
				if ( get_post_meta( $post_id, 'discount_type', true ) == 'smart_coupon' ) {
					update_post_meta( $post_id, 'auto_generate_coupon', 'yes' );
				} else {
					update_post_meta( $post_id, 'auto_generate_coupon', 'no' );
				}
			}

			if ( isset( $_POST['usage_limit_per_user'] ) ) {
				update_post_meta( $post_id, 'usage_limit_per_user', $_POST['usage_limit_per_user'] );
			}

			if ( isset( $_POST['limit_usage_to_x_items'] ) ) {
				update_post_meta( $post_id, 'limit_usage_to_x_items', $_POST['limit_usage_to_x_items'] );
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
		 * Function to add new discount type 'smart_coupon'
		 *
		 * @param array $discount_types existing discount types
		 * @return array $discount_types including smart coupon discount type
		 */
		public function add_smart_coupon_discount_type( $discount_types ) {
			$discount_types['smart_coupon'] = __( 'Store Credit / Gift Certificate', WC_SC_TEXT_DOMAIN );
			return $discount_types;
		}

		

	}

}

WC_SC_Coupon_Fields::get_instance();
