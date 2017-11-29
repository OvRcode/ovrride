<?php
/**
 * Compatibility file for WooCommerce Subscriptions
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_WCS_Compatibility' ) ) {

	/**
	 * Class for handling compatibility with WooCommerce Subscriptions
	 */
	class WC_SC_WCS_Compatibility {

		/**
		 * Variable to hold instance of WC_SC_WCS_Compatibility
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
				add_action( 'wp_loaded', array( $this, 'sc_wcs_renewal_filters' ), 20 );
				add_filter( 'woocommerce_subscriptions_validate_coupon_type', array( $this, 'smart_coupon_as_valid_subscription_coupon_type' ), 10, 3 );
			}

		}

		/**
		 * Get single instance of WC_SC_WCS_Compatibility
		 *
		 * @return WC_SC_WCS_Compatibility Singleton object of WC_SC_WCS_Compatibility
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
		 * Function to manage appropriate filter for applying Smart Coupons feature in renewal order
		 */
		public function sc_wcs_renewal_filters() {
			if ( self::is_wcs_gte( '2.0.0' ) ) {
				add_filter( 'wcs_get_subscription', array( $this, 'sc_wcs_modify_subscription' ) );
				add_filter( 'wcs_renewal_order_meta', array( $this, 'sc_wcs_renewal_order_meta' ), 10, 3 );
				add_filter( 'wcs_new_order_created', array( $this, 'sc_wcs_modify_renewal_order_meta' ), 10, 2 );
				add_filter( 'wcs_renewal_order_items', array( $this, 'sc_wcs_modify_renewal_order' ), 10, 3 );
				add_filter( 'wcs_renewal_order_items', array( $this, 'sc_wcs_renewal_order_items' ), 10, 3 );
				add_filter( 'wcs_renewal_order_created', array( $this, 'sc_wcs_renewal_complete_payment' ), 10, 2 );
			} else {
				add_filter( 'woocommerce_subscriptions_renewal_order_items', array( $this, 'sc_modify_renewal_order' ), 10, 5 );
				add_filter( 'woocommerce_subscriptions_renewal_order_items', array( $this, 'sc_subscriptions_renewal_order_items' ), 10, 5 );
				add_action( 'woocommerce_subscriptions_renewal_order_created', array( $this, 'sc_renewal_complete_payment' ), 10, 4 );
			}
		}

		/**
		 * Function to manage payment method for renewal orders based on availability of store credit (WCS 2.0+)
		 *
		 * @param WC_Subscription $subscription
		 * @return WC_Subscription $subscription
		 */
		public function sc_wcs_modify_subscription( $subscription = null ) {

			if ( did_action( 'woocommerce_scheduled_subscription_payment' ) < 1 ) { return $subscription;
			}

			if ( ! empty( $subscription ) && $subscription instanceof WC_Subscription ) {

				$pay_from_credit_of_original_order = get_option( 'pay_from_smart_coupon_of_original_order', 'yes' );

				if ( $pay_from_credit_of_original_order != 'yes' ) { return $subscription;
				}

				if ( $this->is_wc_gte_30() ) {
					$subscription_parent_order = $subscription->get_parent();
					$original_order_id = ( ! empty( $subscription_parent_order ) && is_callable( array( $subscription_parent_order, 'get_id' ) ) ) ? $subscription_parent_order->get_id() : 0;
				} else {
					$original_order_id = ( ! empty( $subscription->order->id ) ) ? $subscription->order->id : 0;
				}

				if ( empty( $original_order_id ) ) { return $subscription;
				}

				$renewal_total                 = $subscription->get_total();
				$original_order                = wc_get_order( $original_order_id );
				$coupon_used_in_original_order = ( is_object( $original_order ) && is_callable( array( $original_order, 'get_used_coupons' ) ) ) ? $original_order->get_used_coupons() : array();

				if ( $this->is_wc_gte_30() ) {
					$order_payment_method = $original_order->get_payment_method();
				} else {
					$order_payment_method = ( ! empty( $original_order->payment_method ) ) ? $original_order->payment_method : 0;
				}

				if ( sizeof( $coupon_used_in_original_order ) > 0 ) {
					foreach ( $coupon_used_in_original_order as $coupon_code ) {
						$coupon = new WC_Coupon( $coupon_code );
						if ( $this->is_wc_gte_30() ) {
							$coupon_amount = $coupon->get_amount();
							$discount_type = $coupon->get_discount_type();
						} else {
							$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						}
						if ( ! empty( $discount_type ) && $discount_type == 'smart_coupon' && ! empty( $coupon_amount ) ) {
							if ( $coupon_amount >= $renewal_total ) {
								$subscription->set_payment_method( '' );
							} else {
								$payment_gateways = WC()->payment_gateways->get_available_payment_gateways();
								if ( ! empty( $payment_gateways[ $order_payment_method ] ) ) {
									$payment_method = $payment_gateways[ $order_payment_method ];
									$subscription->set_payment_method( $payment_method );
								}
							}
						}
					}
				}
			}

			return $subscription;
		}

		/**
		 * Function to add meta which is necessary for coupon processing, in order
		 *
		 * @param   array           $meta
		 * @param   WC_Order        $to_order
		 * @param   WC_Subscription $from_order
		 * @return  array $meta
		 */
		public function sc_wcs_renewal_order_meta( $meta, $to_order, $from_order ) {

			if ( $this->is_wc_gte_30() ) {
				$order = $from_order->get_parent();
				$order_id = ( is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
			} else {
				$order = $from_order->order;
				$order_id = ( ! empty( $order->id ) ) ? $order->id : 0;
			}

			if ( empty( $order_id ) ) {
				return $meta;
			}

			$meta_exists = array(
								'coupon_sent' 				=> false,
								'gift_receiver_email' 		=> false,
								'gift_receiver_message' 	=> false,
								'sc_called_credit_details' 	=> false,
							);

			foreach ( $meta as $index => $data ) {
				if ( $this->is_wcs_gte('2.2.0') ) {
					if ( ! empty( $data['meta_key'] ) ) {
						$unprefixed_key = substr( $data['meta_key'], 1 );
						if ( array_key_exists( $unprefixed_key, $meta_exists ) ) {
							unset( $meta[ $index ] );
						}
					}
				} else {
					if ( ! empty( $data['meta_key'] ) && array_key_exists( $data['meta_key'], $meta_exists ) ) {
						$meta_exists[ $data['meta_key'] ] = true;
					}
				}
			}

			foreach ( $meta_exists as $key => $value ) {
				if ( $value ) {
					continue;
				}
				$meta_value = get_post_meta( $order_id, $key, true );

				if ( empty( $meta_value ) ) {
					continue;
				}

				if ( $this->is_wcs_gte('2.2.0') ) {
					$prefixed_key = wcs_maybe_prefix_key( $key );
					$renewal_order_id = ( ! empty( $to_order ) && is_callable( array( $to_order, 'get_id' ) ) ) ? $to_order->get_id() : 0;
					// TODO: Enable this only after checking is coupon to be issues recursively
					if ( $key == 'coupon_sent' ) {
					 	update_post_meta( $renewal_order_id, $key, 'no' );
					} else {
						update_post_meta( $renewal_order_id, $key, $meta_value );
					}
				} else {
					if ( ! isset( $meta ) || ! is_array( $meta ) ) {
						$meta = array();
					}
					$meta[] = array(
									'meta_key' 		=> $key,
									'meta_value'	=> $meta_value,
								);
				}

			}

			return $meta;
		}

		/**
		 * Function to modify renewal order meta
		 *
		 * @param array           $order_items
		 * @param WC_Order        $renewal_order
		 * @param WC_Subscription $subscription
		 * @return array $order_items
		 */
		public function sc_wcs_modify_renewal_order_meta( $renewal_order = null, $subscription = null ) {
			global $wpdb;

			if ( $this->is_wc_gte_30() ) {
				$renewal_order_id = ( ! empty( $renewal_order ) && is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$renewal_order_id = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			if ( empty( $renewal_order_id ) ) {
				return $renewal_order;
			}

			$sc_called_credit_details = get_post_meta( $renewal_order_id, 'sc_called_credit_details', true );
			if ( empty( $sc_called_credit_details ) ) {
				return $renewal_order;
			}

			$old_order_item_ids = ( ! empty( $sc_called_credit_details ) ) ? array_keys( $sc_called_credit_details ) : array();

			if ( ! empty( $old_order_item_ids ) ) {
				$query_to_fetch_product_ids = "SELECT woim.order_item_id,
													(CASE
														WHEN woim.meta_key = '_variation_id' AND woim.meta_value > 0 THEN woim.meta_value
														WHEN woim.meta_key = '_product_id' AND woim.meta_value > 0 THEN woim.meta_value
													END) AS product_id
													FROM {$wpdb->prefix}woocommerce_order_itemmeta AS woim
													WHERE woim.order_item_id IN ( " . implode( ',', $old_order_item_ids ) . " )
														AND woim.meta_key IN ( '_product_id', '_variation_id' )
													GROUP BY woim.order_item_id";

				$product_ids_results = $wpdb->get_results( $query_to_fetch_product_ids, 'ARRAY_A' );

				if ( ! is_wp_error( $product_ids_results ) && ! empty( $product_ids_results ) ) {
					$product_to_old_item = array();
					foreach ( $product_ids_results as $result ) {
						$product_to_old_item[ $result['product_id'] ] = $result['order_item_id'];
					}

					$found_product_ids = ( ! empty( $product_to_old_item ) ) ? $product_to_old_item : array();

					$query_to_fetch_new_order_item_ids = "SELECT woim.order_item_id,
															(CASE
																WHEN woim.meta_value > 0 THEN woim.meta_value
															END) AS product_id
															FROM {$wpdb->prefix}woocommerce_order_items AS woi
																LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim
																	ON (woim.order_item_id = woi.order_item_id AND woim.meta_key IN ( '_product_id', '_variation_id' ))
															WHERE woi.order_id = {$renewal_order_id}
																AND woim.order_item_id IS NOT NULL
															GROUP BY woim.order_item_id";

					$new_order_item_ids_result = $wpdb->get_results( $query_to_fetch_new_order_item_ids, 'ARRAY_A' );

					if ( ! is_wp_error( $new_order_item_ids_result ) && ! empty( $new_order_item_ids_result ) ) {
						$product_to_new_item = array();
						foreach ( $new_order_item_ids_result as $result ) {
							$product_to_new_item[ $result['product_id'] ] = $result['order_item_id'];
						}
					}
				}
			}
			foreach ( $sc_called_credit_details as $item_id => $credit_amount ) {
				$product_id = array_search( $item_id, $product_to_old_item );
				if ( $product_id !== false ) {
					$sc_called_credit_details[ $product_to_new_item[ $product_id ] ] = $credit_amount;
					unset( $sc_called_credit_details[ $product_to_old_item[ $product_id ] ] );
				}
			}

			update_post_meta( $renewal_order_id, 'sc_called_credit_details', $sc_called_credit_details );
			return $renewal_order;
		}

		/**
		 * New function to handle auto generation of coupon from renewal orders (WCS 2.0+)
		 *
		 * @param array           $order_items
		 * @param WC_Order        $renewal_order
		 * @param WC_Subscription $subscription
		 * @return array $order_items
		 */
		public function sc_wcs_modify_renewal_order( $order_items = null, $renewal_order = null, $subscription = null ) {

			if ( $this->is_wc_gte_30() ) {
				$subscription_parent_order = $subscription->get_parent();
				$subscription_order_id = ( ! empty( $subscription_parent_order ) && is_callable( array( $subscription_parent_order, 'get_id' ) ) ) ? $subscription_parent_order->get_id() : 0;
				$renewal_order_id = ( is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$subscription_order_id = ( ! empty( $subscription->order->id ) ) ? $subscription->order->id : 0;
				$renewal_order_id = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			$order_items = $this->sc_modify_renewal_order( $order_items, $subscription_order_id, $renewal_order_id );
			return $order_items;
		}

		/**
		 * New function to modify order_items of renewal order (WCS 2.0+)
		 *
		 * @param array           $order_items
		 * @param WC_Order        $renewal_order
		 * @param WC_Subscription $subscription
		 * @return array $order_items
		 */
		public function sc_wcs_renewal_order_items( $order_items = null, $renewal_order = null, $subscription = null ) {

			if ( $this->is_wc_gte_30() ) {
				$subscription_parent_order = $subscription->get_parent();
				$subscription_order_id = ( ! empty( $subscription_parent_order ) && is_callable( array( $subscription_parent_order, 'get_id' ) ) ) ? $subscription_parent_order->get_id() : 0;
				$renewal_order_id = ( is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$subscription_order_id = ( ! empty( $subscription->order->id ) ) ? $subscription->order->id : 0;
				$renewal_order_id = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			$order_items = $this->sc_subscriptions_renewal_order_items( $order_items, $subscription_order_id, $renewal_order_id, 0, 'child' );
			return $order_items;
		}

		/**
		 * New function to mark payment complete for renewal order (WCS 2.0+)
		 *
		 * @param WC_Order        $renewal_order
		 * @param WC_Subscription $subscription
		 * @return WC_Order $renewal_order
		 */
		public function sc_wcs_renewal_complete_payment( $renewal_order = null, $subscription = null ) {
			$this->sc_renewal_complete_payment( $renewal_order );
			return $renewal_order;
		}

		/**
		 * Set 'coupon_sent' as 'no' for renewal order to allow auto generation of coupons (if applicable)
		 *
		 * @param array  $order_items associative array of order items
		 * @param int    $original_order_id
		 * @param int    $renewal_order_id
		 * @param int    $product_id
		 * @param string $new_order_role
		 * @return array $order_items
		 */
		public function sc_modify_renewal_order( $order_items = null, $original_order_id = 0, $renewal_order_id = 0, $product_id = 0, $new_order_role = null ) {

			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_subscription_order 	= wcs_order_contains_subscription( $original_order_id );
			} else {
				$is_subscription_order 	= WC_Subscriptions_Order::order_contains_subscription( $original_order_id );
			}
			if ( $is_subscription_order ) {
				$return = false;
			} else {
				$return = true;
			}
			if ( $return ) {
				return $order_items;
			}

			$is_recursive = false;
			if ( ! empty( $order_items ) ) {
				foreach ( $order_items as $order_item ) {
					$send_coupons_on_renewals = ( ! empty( $order_item['product_id'] ) ) ? get_post_meta( $order_item['product_id'], 'send_coupons_on_renewals', true ) : 'no';
					if ( $send_coupons_on_renewals === 'yes' ) {
						$is_recursive = true;
						break;  // if in any order item recursive is enabled, it will set coupon_sent as 'no'
					}
				}
			}
			$stop_recursive_coupon_generation = get_option( 'stop_recursive_coupon_generation', 'no' );
			if ( ( empty( $stop_recursive_coupon_generation ) || $stop_recursive_coupon_generation == 'no' ) && $is_recursive ) {
				update_post_meta( $renewal_order_id, 'coupon_sent', 'no' );
			} else {
				update_post_meta( $renewal_order_id, 'coupon_sent', 'yes' );
			}

			return $order_items;
		}

		/**
		 * function to modify order_items of renewal order
		 *
		 * @param array  $order_items
		 * @param int    $original_order_id
		 * @param int    $renewal_order_id
		 * @param int    $product_id
		 * @param string $new_order_role
		 * @return array $order_items
		 */
		public function sc_subscriptions_renewal_order_items( $order_items = null, $original_order_id = 0, $renewal_order_id = 0, $product_id = 0, $new_order_role = null ) {

			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_subscription_order 	= wcs_order_contains_subscription( $original_order_id );
			} else {
				$is_subscription_order 	= WC_Subscriptions_Order::order_contains_subscription( $original_order_id );
			}
			if ( $is_subscription_order ) {
				$return = false;
			} else {
				$return = true;
			}
			if ( $return ) {
				return $order_items;
			}

			$pay_from_credit_of_original_order = get_option( 'pay_from_smart_coupon_of_original_order', 'yes' );

			if ( $pay_from_credit_of_original_order != 'yes' ) { return $order_items;
			}
			if ( $new_order_role != 'child' ) { return $order_items;
			}
			if ( empty( $renewal_order_id ) || empty( $original_order_id ) ) { return $order_items;
			}

			$original_order = wc_get_order( $original_order_id );
			$renewal_order = wc_get_order( $renewal_order_id );

			$coupon_used_in_original_order = ( is_object( $original_order ) && is_callable( array( $original_order, 'get_used_coupons' ) ) ) ? $original_order->get_used_coupons() : array();
			$coupon_used_in_renewal_order = ( is_object( $renewal_order ) && is_callable( array( $renewal_order, 'get_used_coupons' ) ) ) ? $renewal_order->get_used_coupons() : array();

			if ( $this->is_wc_gte_30() ) {
				$renewal_order_billing_email = ( is_callable( array( $renewal_order, 'get_billing_email' ) ) ) ? $renewal_order->get_billing_email() : '';
			} else {
				$renewal_order_billing_email = ( ! empty( $renewal_order->billing_email ) ) ? $renewal_order->billing_email : '';
			}

			if ( sizeof( $coupon_used_in_original_order ) > 0 ) {
				$smart_coupons_contribution = array();
				foreach ( $coupon_used_in_original_order as $coupon_code ) {
					$coupon = new WC_Coupon( $coupon_code );
					if ( $this->is_wc_gte_30() ) {
						$coupon_amount = $coupon->get_amount();
						$discount_type = $coupon->get_discount_type();
					} else {
						$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}
					if ( ! empty( $discount_type ) && $discount_type == 'smart_coupon' && ! empty( $coupon_amount ) && ! in_array( $coupon_code, $coupon_used_in_renewal_order, true ) ) {
						$renewal_order_total = $renewal_order->get_total();
						if ( $coupon_amount < $renewal_order_total ) {
							continue;
						}
						$discount = min( $renewal_order_total, $coupon_amount );
						if ( $discount > 0 ) {
							$new_order_total = $renewal_order_total - $discount;
							update_post_meta( $renewal_order_id, '_order_total', $new_order_total );
							update_post_meta( $renewal_order_id, '_order_discount', $discount );
							if ( $new_order_total <= floatval( 0 ) ) {
								update_post_meta( $renewal_order_id, '_renewal_paid_by_smart_coupon', 'yes' );
							}
							if ( $this->is_wc_gte_30() ) {
								$item = new WC_Order_Item_Coupon();
								$item->set_props( array(
									'code'         => $coupon_code,
									'discount'     => $discount,
									'order_id'     => $renewal_order->get_id(),
								) );
								$item->save();
								$renewal_order->add_item( $item );
							} else {
								$renewal_order->add_coupon( $coupon_code, $discount );
							}
							$smart_coupons_contribution[ $coupon_code ] = $discount;
							$used_by = $renewal_order->get_user_id();
							if ( ! $used_by ) {
								$used_by = $renewal_order_billing_email;
							}
							$coupon->inc_usage_count( $used_by );
						}
					}
				}
				if ( ! empty( $smart_coupons_contribution ) ) {
					update_post_meta( $renewal_order_id, 'smart_coupons_contribution', $smart_coupons_contribution );
				}
			}

			return $order_items;
		}

		/**
		 * function to trigger complete payment for renewal if it's paid by smart coupons
		 *
		 * @param WC_Order $renewal_order
		 * @param WC_Order $original_order
		 * @param int      $product_id
		 * @param string   $new_order_role
		 */
		public function sc_renewal_complete_payment( $renewal_order = null, $original_order = null, $product_id = 0, $new_order_role = null ) {

			if ( $this->is_wc_gte_30() ) {
				$renewal_order_id = ( ! empty( $renewal_order ) && is_callable( array( $renewal_order, 'get_id' ) ) ) ? $renewal_order->get_id() : 0;
			} else {
				$renewal_order_id = ( ! empty( $renewal_order->id ) ) ? $renewal_order->id : 0;
			}

			if ( empty( $renewal_order_id ) ) {
				return;
			}
			if ( self::is_wcs_gte( '2.0.0' ) ) {
				$is_renewal_order 	= wcs_order_contains_renewal( $renewal_order_id );
			} else {
				$is_renewal_order 	= WC_Subscriptions_Renewal_Order::is_renewal( $renewal_order_id );
			}
			if ( $is_renewal_order ) {
				$return = false;
			} else {
				$return = true;
			}
			if ( $return ) {
				return;
			}

			$order_needs_processing = false;

			if ( sizeof( $renewal_order->get_items() ) > 0 ) {
				foreach ( $renewal_order->get_items() as $item ) {
					if ( $_product = $renewal_order->get_product_from_item( $item ) ) {
						$virtual_downloadable_item = $_product->is_downloadable() && $_product->is_virtual();

						if ( apply_filters( 'woocommerce_order_item_needs_processing', ! $virtual_downloadable_item, $_product, $renewal_order_id ) ) {
							$order_needs_processing = true;
							break;
						}
					} else {
						$order_needs_processing = true;
						break;
					}
				}
			}

			$is_renewal_paid_by_smart_coupon = get_post_meta( $renewal_order_id, '_renewal_paid_by_smart_coupon', true );
			if ( ! empty( $is_renewal_paid_by_smart_coupon ) && $is_renewal_paid_by_smart_coupon == 'yes' ) {
				$renewal_order->update_status( apply_filters( 'woocommerce_payment_complete_order_status', $order_needs_processing ? 'processing' : 'completed', $renewal_order_id ), __( 'Order paid by store credit.', WC_SC_TEXT_DOMAIN ) );
			}
		}

		/**
		 * Get valid_subscription_coupon array and add smart_coupon type
		 *
		 * @param bool      $is_validate_for_subscription
		 * @param WC_Coupon $coupon
		 * @param bool      $valid
		 * @return bool $is_validate_for_subscription whether to validate coupon for subscription or not
		 */
		public function smart_coupon_as_valid_subscription_coupon_type( $is_validate_for_subscription, $coupon, $valid ) {

			if ( $this->is_wc_gte_30() ) {
				$discount_type = ( ! empty( $coupon ) && is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : 0;
			} else {
				$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			if ( ! empty( $discount_type ) && $discount_type == 'smart_coupon' ) {
				$is_validate_for_subscription = false;
			}

			return $is_validate_for_subscription;
		}

		/**
		 * Function to check if cart contains subscription
		 *
		 * @return bool whether cart contains subscription or not
		 */
		public static function is_cart_contains_subscription() {
			if ( class_exists( 'WC_Subscriptions_Cart' ) && WC_Subscriptions_Cart::cart_contains_subscription() ) {
				return true;
			}
			return false;
		}

		/**
		 * Function to check WooCommerce Subscription version
		 *
		 * @param string $version
		 * @return bool whether passed version is greater than or equal to current version of WooCommerce Subscription
		 */
		public static function is_wcs_gte( $version = null ) {
			if ( $version === null ) { return false;
			}
			if ( ! class_exists( 'WC_Subscriptions' ) || empty( WC_Subscriptions::$version ) ) { return false;
			}
			return version_compare( WC_Subscriptions::$version, $version, '>=' );
		}



	}

}

WC_SC_WCS_Compatibility::get_instance();
