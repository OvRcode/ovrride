<?php
/**
 * Smart Coupons fields in orders
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Order_Fields' ) ) {

	/**
	 * Class for handling Smart Coupons' field in orders
	 */
	class WC_SC_Order_Fields {

		/**
		 * Variable to hold instance of WC_SC_Order_Fields
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'woocommerce_admin_order_totals_after_tax', array( $this, 'admin_order_totals_add_smart_coupons_discount_details' ) );
			add_filter( 'woocommerce_get_order_item_totals', array( $this, 'add_smart_coupons_discount_details' ), 10, 2 );

			add_action( 'wp_loaded', array( $this, 'order_fields_hooks' ) );

			add_filter( 'woocommerce_cart_totals_coupon_label', array( $this, 'cart_totals_smart_coupons_label' ), 10, 2 );
			add_filter( 'woocommerce_cart_totals_order_total_html', array( $this, 'cart_totals_order_total_html' ), 99 );
			add_filter( 'woocommerce_get_formatted_order_total', array( $this, 'get_formatted_order_total' ), 99, 2 );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'show_store_credit_balance' ), 10, 3 );

		}

		/**
		 * Get single instance of WC_SC_Order_Fields
		 *
		 * @return WC_SC_Order_Fields Singleton object of WC_SC_Order_Fields
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
		 * function to get total credit used in an order
		 *
		 * @param WC_Order $order
		 * @return float $total_credit_used
		 */
		public function get_total_credit_used_in_order( $order = null ) {

			if ( empty( $order ) ) { return 0;
			}

			$total_credit_used = 0;

			$coupons = $order->get_items( 'coupon' );

			if ( ! empty( $coupons ) ) {

				foreach ( $coupons as $item_id => $item ) {

					if ( empty( $item['name'] ) ) { continue;
					}

					$coupon = new WC_Coupon( $item['name'] );

					if ( $this->is_wc_gte_30() ) {
						$discount_type = $coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}

					if ( $discount_type != 'smart_coupon' ) { continue;
					}

					$total_credit_used += $item['discount_amount'];

				}
			}

			return $total_credit_used;

		}

		/**
		 * function to show store credit used in order admin panel
		 *
		 * @param int $order_id
		 */
		public function admin_order_totals_add_smart_coupons_discount_details( $order_id = 0 ) {

			if ( empty( $order_id ) ) { return;
			}

			$order = wc_get_order( $order_id );

			$total_credit_used = $this->get_total_credit_used_in_order( $order );

			if ( empty( $total_credit_used ) ) { return;
			}

			?>

			<tr>
				<td class="label"><?php _e( 'Store Credit Used', WC_SC_TEXT_DOMAIN ); ?> <span class="tips" data-tip="<?php _e( 'This is the total credit used.', WC_SC_TEXT_DOMAIN ); ?>">[?]</span>:</td>
				<td width="1%"></td>
				<td class="total">
					<?php
						if ( $this->is_wc_gte_30() ) {
							echo wc_price( $total_credit_used, array( 'currency' => $order->get_currency() ) );
						} else {
							echo wc_price( $total_credit_used, array( 'currency' => $order->get_order_currency() ) );
						}
					?>
				</td>
			</tr>

			<?php

		}

		/**
		 * Function to add hooks based on conditions
		 */
		public function order_fields_hooks() {

			if ( $this->is_wc_gte_30() ) {
				add_filter( 'woocommerce_order_get_total_discount', array( $this, 'smart_coupons_order_amount_total_discount' ), 10, 2 );
			} else {
				add_filter( 'woocommerce_order_amount_total_discount', array( $this, 'smart_coupons_order_amount_total_discount' ), 10, 2 );
			}

		}

		/**
		 * function to add details of discount coming from smart coupons
		 *
		 * @param array    $total_rows
		 * @param WC_Order $order
		 * @return array $total_rows
		 */
		public function add_smart_coupons_discount_details( $total_rows = array(), $order = null ) {

			if ( empty( $order ) ) { return $total_rows;
			}

			$total_credit_used = $this->get_total_credit_used_in_order( $order );

			$offset = array_search( 'order_total', array_keys( $total_rows ) );

			if ( $offset !== false && ! empty( $total_credit_used ) && ! empty( $total_rows ) ) {
				$total_rows = array_merge(
					array_slice( $total_rows, 0, $offset ),
					array(
					'smart_coupon' => array(
																'label' => __( 'Store Credit Used:', WC_SC_TEXT_DOMAIN ),
																'value'	=> '-' . wc_price( $total_credit_used ),
															),
					),
					array_slice( $total_rows, $offset, null )
				);

				$total_discount = $order->get_total_discount();
				// code to check and manipulate 'Discount' amount based on Store Credit used
				if ( $total_discount == $total_credit_used ) {
					unset( $total_rows['discount'] );
				} else {
					$total_discount = $total_discount - $total_credit_used;
					$total_rows['discount']['value'] = '-' . wc_price( $total_discount );
				}
			}

			return $total_rows;

		}

		/**
		 * function to include discounts from smart coupons in total discount of order
		 *
		 * @param float    $total_discount
		 * @param WC_Order $order
		 * @return float $total_discount
		 */
		public function smart_coupons_order_amount_total_discount( $total_discount, $order = null ) {

			// To avoid adding store credit in 'Discount' field on order admin panel
			if ( did_action( 'woocommerce_admin_order_item_headers' ) >= 1 ) { return $total_discount;
			}

			$total_credit_used = $this->get_total_credit_used_in_order( $order );
			if ( $total_credit_used > 0 ) {
				$total_discount += $total_credit_used;
			}
			return $total_discount;

		}

		/**
		 * function to add label for smart_coupons in cart total
		 *
		 * @param string    $default_label
		 * @param WC_Coupon $coupon
		 * @return string $new_label
		 */
		public function cart_totals_smart_coupons_label( $default_label = '', $coupon = null ) {

			if ( empty( $coupon ) ) { return $default_label;
			}

			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
				$coupon_code   = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
				$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
			}

			if ( ! empty( $discount_type ) && $discount_type == 'smart_coupon' ) {
				return esc_html( __( 'Store Credit:', WC_SC_TEXT_DOMAIN ) . ' ' . $coupon_code );
			}

			return $default_label;

		}

		/**
		 * Modify Tax detail HTML if store credit is applied, in cart
		 *
		 * @param string $html
		 *
		 * @return string $html
		 */
		public function cart_totals_order_total_html( $html = null ) {

			if ( empty( $html ) ) {
				return $html;
			}

			if ( ! class_exists( 'WC_SC_WCS_Compatibility' ) ) {
				include_once 'class-wcs-compatibility.php';
			}

			if ( WC_SC_WCS_Compatibility::is_cart_contains_subscription() ) {
				return $html;
			}

			if ( wc_tax_enabled() && WC()->cart->tax_display_cart == 'incl' ) {

				$applied_coupons = WC()->cart->get_applied_coupons();

				if ( empty( $applied_coupons ) ) {
					return $html;
				}

				foreach ( $applied_coupons as $code ) {
					$coupon = new WC_Coupon( $code );
					if ( $this->is_wc_gte_30() ) {
						$discount_type = $coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}
					if ( ! is_a( $coupon, 'WC_Coupon' ) || $discount_type != 'smart_coupon' ) {
						continue;
					}
					if ( WC()->cart->get_total() == 0 || WC()->cart->get_total() <= WC()->cart->get_taxes_total() ) {
						return '<strong>' . WC()->cart->get_total() . '</strong> ';
					}
				}
			}

			return $html;
		}


		/**
		 * Modify Tax detail HTML if store credit is applied, in order
		 *
		 * @param string   $html
		 * @param WC_Order $order (optional)
		 *
		 * @return string $html
		 */
		public function get_formatted_order_total( $html = null, $order = null ) {

			if ( empty( $html ) || empty( $order ) ) {
				return $html;
			}

			if ( $this->is_wc_gte_30() ) {
				$tax_display = get_option( 'woocommerce_tax_display_cart' );
			} else {
				$tax_display = ( ! empty( $order->tax_display_cart ) ) ? $order->tax_display_cart : '';
			}

			if ( wc_tax_enabled() && $tax_display == 'incl' ) {

				$applied_coupons = $order->get_used_coupons();

				if ( empty( $applied_coupons ) ) {
					return $html;
				}

				foreach ( $applied_coupons as $code ) {
					$coupon = new WC_Coupon( $code );
					if ( $this->is_wc_gte_30() ) {
						$discount_type = $coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}
					if ( ! is_a( $coupon, 'WC_Coupon' ) || $discount_type != 'smart_coupon' ) {
						continue;
					}
					if ( $order->get_total() == 0 || $order->get_total() <= $order->get_total_tax() ) {
						if ( strpos( $html, '(' ) !== false && strpos( $html, ')' ) !== false ) {
							$position = strpos( $html, '(' ) - 1;
							$part = substr( $html, 0, $position ) . substr( $html, ( strrpos( $html, ')' ) + 1 ) );
							return $part;
						}
					}
				}
			}

			return $html;
		}

		/**
		 * Function to notifiy user about remaining balance in Store Credit in "Order Complete" email
		 *
		 * @param WC_Order $order
		 * @param boolean  $send_to_admin
		 */
		public function show_store_credit_balance( $order = false, $send_to_admin = false, $plain_text = false ) {

			if ( $send_to_admin ) { return;
			}

			if ( $this->is_wc_gte_30() ) {
				$order_refunds = ( ! empty( $order ) && is_callable( array( $order, 'get_refunds' ) ) ) ? $order->get_refunds() : array();
			} else {
				$order_refunds = ( ! empty( $order->refunds ) ) ? $order->refunds : array();
			}

			if ( ! empty( $order_refunds ) ) { return;
			}

			if ( sizeof( $order->get_used_coupons() ) > 0 ) {
				$store_credit_balance = '';
				foreach ( $order->get_used_coupons() as $code ) {
					if ( ! $code ) { continue;
					}
					$coupon = new WC_Coupon( $code );
					if ( $this->is_wc_gte_30() ) {
						$coupon_amount = $coupon->get_amount();
						$discount_type = $coupon->get_discount_type();
						$coupon_code   = $coupon->get_code();
					} else {
						$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_code   = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					if ( $discount_type == 'smart_coupon' && $coupon_amount > 0 ) {
						$store_credit_balance .= '<li><strong>' . $coupon_code . '</strong> &mdash; ' . wc_price( $coupon_amount ) . '</li>';
					}
				}

				if ( ! empty( $store_credit_balance ) ) {
					echo '<br /><h3>' . __( 'Store Credit / Gift Card Balance', WC_SC_TEXT_DOMAIN ) . ': </h3>';
					echo '<ul>' . $store_credit_balance . '</ul><br />';
				}
			}
		}



	}

}

WC_SC_Order_Fields::get_instance();
