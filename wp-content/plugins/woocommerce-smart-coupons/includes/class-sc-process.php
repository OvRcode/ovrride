<?php
/**
 * Processing of coupons
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Coupon_Process' ) ) {

	/**
	 * Class for handling processes of coupons
	 */
	class WC_SC_Coupon_Process {

		/**
		 * Variable to hold instance of WC_SC_Coupon_Process
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'woocommerce_new_order', array( $this, 'add_gift_certificate_receiver_details_in_order' ) );
			add_action( 'woocommerce_new_order', array( $this, 'smart_coupons_contribution' ), 8 );
			add_action( 'woocommerce_before_checkout_process', array( $this, 'verify_gift_certificate_receiver_details' ) );

			add_action( 'woocommerce_order_status_completed', array( $this, 'sa_add_coupons' ) );
			add_action( 'woocommerce_order_status_completed', array( $this, 'coupons_used' ), 10 );
			add_action( 'woocommerce_order_status_processing', array( $this, 'sa_add_coupons' ), 19 );
			add_action( 'woocommerce_order_status_processing', array( $this, 'coupons_used' ), 10 );
			add_action( 'woocommerce_order_status_refunded', array( $this, 'sa_remove_coupons' ), 19 );
			add_action( 'woocommerce_order_status_cancelled', array( $this, 'sa_remove_coupons' ), 19 );
			add_action( 'woocommerce_order_status_processing_to_refunded', array( $this, 'sa_restore_smart_coupon_amount' ), 19 );
			add_action( 'woocommerce_order_status_processing_to_cancelled', array( $this, 'sa_restore_smart_coupon_amount' ), 19 );
			add_action( 'woocommerce_order_status_completed_to_refunded', array( $this, 'sa_restore_smart_coupon_amount' ), 19 );
			add_action( 'woocommerce_order_status_completed_to_cancelled', array( $this, 'sa_restore_smart_coupon_amount' ), 19 );

			add_filter( 'woocommerce_gift_certificates_email_template', array( $this, 'woocommerce_gift_certificates_email_template_path' ) );

			add_action( 'woocommerce_order_status_on-hold', array( $this, 'update_smart_coupon_balance' ), 19 );
			add_action( 'update_smart_coupon_balance', array( $this, 'update_smart_coupon_balance' ) );

			add_filter( 'woocommerce_paypal_args', array( $this, 'modify_paypal_args' ), 11, 2 );

		}

		/**
		 * Get single instance of WC_SC_Coupon_Process
		 *
		 * @return WC_SC_Coupon_Process Singleton object of WC_SC_Coupon_Process
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
		 * Function to add gift certificate receiver's details in order itself
		 *
		 * @param int $order_id
		 */
		public function add_gift_certificate_receiver_details_in_order( $order_id ) {

			if ( ! isset( $_REQUEST['gift_receiver_email'] ) || count( $_REQUEST['gift_receiver_email'] ) <= 0 ) { return;
			}

			if ( isset( $_REQUEST['gift_receiver_email'] ) || ( isset( $_REQUEST['billing_email'] ) && $_REQUEST['billing_email'] != $_REQUEST['gift_receiver_email'] ) ) {

				if ( isset( $_REQUEST['is_gift'] ) && $_REQUEST['is_gift'] == 'yes' ) {
					if ( isset( $_REQUEST['sc_send_to'] ) && ! empty( $_REQUEST['sc_send_to'] ) ) {
						switch ( $_REQUEST['sc_send_to'] ) {
							case 'one':
								$email_for_one = ( isset( $_REQUEST['gift_receiver_email'][0][0] ) && ! empty( $_REQUEST['gift_receiver_email'][0][0] ) && is_email( $_REQUEST['gift_receiver_email'][0][0] ) ) ? $_REQUEST['gift_receiver_email'][0][0] : $_REQUEST['billing_email'];
								$message_for_one = ( isset( $_REQUEST['gift_receiver_message'][0][0] ) && ! empty( $_REQUEST['gift_receiver_message'][0][0] ) ) ? $_REQUEST['gift_receiver_message'][0][0] : '';
								unset( $_REQUEST['gift_receiver_email'][0][0] );
								unset( $_REQUEST['gift_receiver_message'][0][0] );
								foreach ( $_REQUEST['gift_receiver_email'] as $coupon_id => $emails ) {
									foreach ( $emails as $key => $email ) {
										$_REQUEST['gift_receiver_email'][ $coupon_id ][ $key ] = $email_for_one;
										$_REQUEST['gift_receiver_message'][ $coupon_id ][ $key ] = $message_for_one;
									}
								}
								if ( isset( $_REQUEST['gift_receiver_message'] ) && $_REQUEST['gift_receiver_message'] != '' ) {
									update_post_meta( $order_id, 'gift_receiver_message', $_REQUEST['gift_receiver_message'] );
								}
								break;

							case 'many':
								if ( isset( $_REQUEST['gift_receiver_email'][0][0] ) && ! empty( $_REQUEST['gift_receiver_email'][0][0] ) ) {
									unset( $_REQUEST['gift_receiver_email'][0][0] );
								}
								if ( isset( $_REQUEST['gift_receiver_message'][0][0] ) && ! empty( $_REQUEST['gift_receiver_message'][0][0] ) ) {
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
					if ( ! empty( $_REQUEST['gift_receiver_email'][0][0] ) && is_array( $_REQUEST['gift_receiver_email'][0][0] ) ) {
						unset( $_REQUEST['gift_receiver_email'][0][0] );
						foreach ( $_REQUEST['gift_receiver_email'] as $coupon_id => $emails ) {
							foreach ( $emails as $key => $email ) {
								$_REQUEST['gift_receiver_email'][ $coupon_id ][ $key ] = $_REQUEST['billing_email'];
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

			if ( empty( $_POST['gift_receiver_email'] ) || ! is_array( $_POST['gift_receiver_email'] ) ) { return;
			}

			foreach ( $_POST['gift_receiver_email'] as $key => $emails ) {
				if ( ! empty( $emails ) ) {
					foreach ( $emails as $index => $email ) {

						$placeholder = __( 'Email address', WC_SC_TEXT_DOMAIN );
						$placeholder .= '...';

						if ( empty( $email ) || $email == $placeholder ) {
							$_POST['gift_receiver_email'][ $key ][ $index ] = ( ! empty( $_POST['billing_email'] ) ) ? $_POST['billing_email'] : '';
						} elseif ( ! empty( $email ) && ! is_email( $email ) ) {
							wc_add_notice( __( 'Error: Gift Card Receiver&#146;s E-mail address is invalid.', WC_SC_TEXT_DOMAIN ), 'error' );
							return;
						}
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

			if ( ! empty( WC()->cart->applied_coupons ) ) {

				foreach ( WC()->cart->applied_coupons as $code ) {

					$smart_coupon = new WC_Coupon( $code );

					if ( $this->is_wc_gte_30() ) {
						$discount_type = $smart_coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $smart_coupon->discount_type ) ) ? $smart_coupon->discount_type : '';
					}

					if ( $discount_type == 'smart_coupon' ) {

						update_post_meta( $order_id, 'smart_coupons_contribution', WC()->cart->smart_coupon_credit_used );

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

			$order = wc_get_order( $order_id );

			$order_used_coupons = $order->get_used_coupons();

			if ( $order_used_coupons ) {

				$smart_coupons_contribution = get_post_meta( $order_id, 'smart_coupons_contribution', true );

				if ( ! isset( $smart_coupons_contribution ) || empty( $smart_coupons_contribution ) || ( is_array( $smart_coupons_contribution ) && count( $smart_coupons_contribution ) <= 0 ) ) { return;
				}

				foreach ( $order_used_coupons as $code ) {

					if ( array_key_exists( $code, $smart_coupons_contribution ) ) {

						$smart_coupon = new WC_Coupon( $code );

						if ( $this->is_wc_gte_30() ) {
							$coupon_id     = $smart_coupon->get_id();
							$coupon_amount = $smart_coupon->get_amount();
							$discount_type = $smart_coupon->get_discount_type();
						} else {
							$coupon_id     = ( ! empty( $smart_coupon->id ) ) ? $smart_coupon->id : 0;
							$coupon_amount = ( ! empty( $smart_coupon->amount ) ) ? $smart_coupon->amount : 0;
							$discount_type = ( ! empty( $smart_coupon->discount_type ) ) ? $smart_coupon->discount_type : '';
						}

						if ( $discount_type == 'smart_coupon' ) {

							$discount_amount = round( ( $coupon_amount - $smart_coupons_contribution[ $code ] ), get_option( 'woocommerce_price_num_decimals', 2 ) );
							$credit_remaining = max( 0, $discount_amount );

							if ( $credit_remaining <= 0 && get_option( 'woocommerce_delete_smart_coupon_after_usage' ) == 'yes' ) {
								update_post_meta( $coupon_id, 'coupon_amount', 0 );
								wp_trash_post( $coupon_id );
							} else {
								update_post_meta( $coupon_id, 'coupon_amount', $credit_remaining );
							}
						}
					}
				}

				delete_post_meta( $order_id, 'smart_coupons_contribution' );

			}
		}

		/**
		 * Update discount details in PayPal args if store credit is applied
		 *
		 * @param  array $args PayPal args
		 * @param  WC_Order $order
		 *
		 * @return array $args Modified PayPal args
		 */
		public function modify_paypal_args( $args, $order ) {

			$is_order_contains_store_credit = $this->is_order_contains_store_credit( $order );

			if ( ! $is_order_contains_store_credit ) {
				return $args;
			}

			$discount_amount_cart = ( ! empty( $args['discount_amount_cart'] ) ) ? $args['discount_amount_cart'] : 0;

			if ( empty( $discount_amount_cart ) ) {
				return $args;
			}

			$item_total = 0;

			foreach ( $args as $key => $value ) {
				if ( strpos( $key, 'amount_' ) === 0 ) {
					$item_total += $value;
				}
			}

			if ( $discount_amount_cart > $item_total ) {
				$difference = $discount_amount_cart - $item_total;
				$args['discount_amount_cart'] = $item_total;

				if ( $this->is_wc_gte_30() ) {
					$order_id = ( ! empty( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
				} else {
					$order_id = ( ! empty( $order->id ) ) ? $order->id : 0;
				}

				$coupons = $order->get_items( 'coupon' );
				$order_total = $order->get_total();
				$order_note = array();

				foreach ( $coupons as $item_id => $item ) {
					if ( empty( $difference ) ) {
						break;
					}
					$code = trim( $item['name'] );
					$coupon = new WC_Coupon( $code );
					if ( $this->is_wc_gte_30() ) {
						$discount_type = $coupon->get_discount_type();
					} else {
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
					}
					if ( $discount_type == 'smart_coupon' && ! empty( $item['discount_amount'] ) ) {
						$new_discount = 0;
						$item_discount = $item['discount_amount'];
						$cut_amount = min( $difference, $item_discount );
						$new_discount = $item_discount - $cut_amount;
						$difference -= $cut_amount;
						$item_args = array(
											'discount_amount' => $new_discount
										);
						if ( $this->is_wc_gte_30() ) {
							$item = $order->get_item( $item_id );

							if ( ! is_object( $item ) || ! $item->is_type( 'coupon' ) ) {
								$discount_updated = false;
							}
							if ( ! $order->get_id() ) {
								$order->save(); // Order must exist
							}

							// BW compatibility for old args
							if ( isset( $item_args['discount_amount'] ) ) {
								$item_args['discount'] = $item_args['discount_amount'];
							}
							if ( isset( $item_args['discount_amount_tax'] ) ) {
								$item_args['discount_tax'] = $item_args['discount_amount_tax'];
							}

							$item->set_order_id( $order->get_id() );
							$item->set_props( $item_args );
							$item->save();

							do_action( 'woocommerce_order_update_coupon', $order->get_id(), $item->get_id(), $item_args );
							$discount_updated = true;
						} else {
							$discount_updated = $order->update_coupon( $item_id, $item_args );
						}


						if ( $discount_updated ) {
							$order_total += $cut_amount;
							$smart_coupons_contribution = get_post_meta( $order_id, 'smart_coupons_contribution', true );
							if ( empty( $smart_coupons_contribution ) || ! is_array( $smart_coupons_contribution ) ) {
								$smart_coupons_contribution = array();
							}
							$smart_coupons_contribution[ $code ] = $item_args['discount_amount'];
							update_post_meta( $order_id, 'smart_coupons_contribution', $smart_coupons_contribution );
							$order_note[] = sprintf(__( '%1$s worth of Store Credit restored to coupon %2$s.', WC_SC_TEXT_DOMAIN ), '<strong>' . wc_price( $cut_amount ) . '</strong>', '<code>' . $code . '</code>' );
						}
					}
				}
				$order->set_total( $order_total, 'total' );
				if ( ! empty( $order_note ) ) {
					$note = sprintf(__( '%s Because PayPal doesn\'t accept discount on shipping & tax.', WC_SC_TEXT_DOMAIN ), implode( ', ', $order_note ) );
					$order->add_order_note( $note );
					if ( ! wc_has_notice( $note ) ) {
						wc_add_notice( $note );
					}
				}
			}
			return $args;
		}

		/**
		 * Function to track whether coupon is used or not
		 *
		 * @param int $order_id
		 */
		public function coupons_used( $order_id ) {

			// Update Smart Coupons balance when the order status is either 'processing' or 'completed'
			do_action( 'update_smart_coupon_balance', $order_id );

			$order = wc_get_order( $order_id );

			$email = get_post_meta( $order_id, 'gift_receiver_email', true );

			if ( $order->get_used_coupons() ) {
				$this->update_coupons( $order->get_used_coupons(), $email, '', 'remove' );
			}
		}

		/**
		 * Function to update details related to coupons
		 *
		 * @param array  $coupon_titles
		 * @param mixed  $email
		 * @param array  $product_ids array of product ids
		 * @param string $operation
		 * @param array  $order_item
		 * @param array  $gift_certificate_receiver array of gift receiver emails
		 * @param array  $gift_certificate_receiver_name array of gift receiver name
		 * @param string $message_from_sender
		 * @param string $gift_certificate_sender_name
		 * @param string $gift_certificate_sender_email
		 * @param int    $order_id
		 */
		public function update_coupons( $coupon_titles = array(), $email, $product_ids = '', $operation, $order_item = null, $gift_certificate_receiver = false, $gift_certificate_receiver_name = '', $message_from_sender = '', $gift_certificate_sender_name = '', $gift_certificate_sender_email = '', $order_id = '' ) {

			global $smart_coupon_codes;

			$temp_gift_card_receivers_emails = array();
			if ( ! empty( $order_id ) ) {
				$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
				$temp_gift_card_receivers_emails = get_post_meta( $order_id, 'temp_gift_card_receivers_emails', true );
			}

			$prices_include_tax = ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) ? true : false;

			if ( ! empty( $coupon_titles ) ) {

				if ( $this->is_wc_gte_30() ) {
					$item_qty              = ( ! empty( $order_item ) && is_callable( array( $order_item, 'get_quantity' ) ) ) ? $order_item->get_quantity() : 1;
					$item_sc_called_credit = ( ! empty( $order_item ) && is_callable( array( $order_item, 'get_meta' ) ) ) ? $order_item->get_meta( 'sc_called_credit' ) : 0;
					$item_total            = ( ! empty( $order_item ) && is_callable( array( $order_item, 'get_total' ) ) ) ? $order_item->get_total() : 0;
					$item_tax              = ( ! empty( $order_item ) && is_callable( array( $order_item, 'get_total_tax' ) ) ) ? $order_item->get_total_tax() : 0;
				} else {
					$item_qty              = ( ! empty( $order_item['qty'] ) ) ? $order_item['qty'] : 1;
					$item_sc_called_credit = ( ! empty( $order_item['sc_called_credit'] ) ) ? $order_item['sc_called_credit'] : 0;
					$item_total            = ( ! empty( $order_item['line_total'] ) ) ? $order_item['line_total'] : 0;
					$item_tax              = ( ! empty( $order_item['line_tax'] ) ) ? $order_item['line_tax'] : 0;
				}

				if ( ! empty( $item_qty ) ) {
					$qty = $item_qty;
				} else {
					$qty = 1;
				}

				foreach ( $coupon_titles as $coupon_title ) {

					$coupon = new WC_Coupon( $coupon_title );

					if ( $this->is_wc_gte_30() ) {
						$coupon_id        = $coupon->get_id();
						$coupon_amount    = $coupon->get_amount();
						$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
						$discount_type    = $coupon->get_discount_type();
						$expiry_date      = $coupon->get_date_expires();
						$coupon_code      = $coupon->get_code();
					} else {
						$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
						$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
						$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
					}

					$auto_generation_of_code = get_post_meta( $coupon_id, 'auto_generate_coupon', true );

					if ( ! empty( $item_sc_called_credit ) && $discount_type == 'smart_coupon' ) { continue;	// because it is already processed
					}

					$email_id = ( $auto_generation_of_code == 'yes' && $discount_type != 'smart_coupon' && ! empty( $temp_gift_card_receivers_emails[ $coupon_id ][0] ) ) ? $temp_gift_card_receivers_emails[ $coupon_id ][0] : $gift_certificate_sender_email;

					if ( ( $auto_generation_of_code == 'yes' || $discount_type == 'smart_coupon' ) && $operation == 'add' ) {

						if ( get_post_meta( $coupon_id, 'is_pick_price_of_product', true ) == 'yes' && $discount_type == 'smart_coupon' ) {
							$products_price = ( ! $prices_include_tax ) ? $item_total : $item_total + $item_tax;
							$amount = $products_price / $qty;
						} else {
							$amount = $coupon_amount;
						}

						if ( ! empty( $temp_gift_card_receivers_emails ) ) {
							$email = $temp_gift_card_receivers_emails;
						}

						if ( $amount > 0  || $is_free_shipping == 'yes' ) {
							$message_index = ( ! empty( $email[ $coupon_id ] ) && is_array( $email[ $coupon_id ] ) ) ? array_search( $email_id, $email[ $coupon_id ], true ) : false;

							if ( $message_index !== false && isset( $receivers_messages[ $coupon_id ][ $message_index ] ) && ! empty( $receivers_messages[ $coupon_id ][ $message_index ] ) ) {
								$message_from_sender = $receivers_messages[ $coupon_id ][ $message_index ];
								unset( $email[ $coupon_id ][ $message_index ] );
								update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
							} else {
								$message_from_sender = '';
							}
							for ( $i = 0; $i < $qty; $i++ ) {
								if ( $auto_generation_of_code == 'yes' || $discount_type == 'smart_coupon' ) {
									$email_id = ! empty( $temp_gift_card_receivers_emails[ $coupon_id ][ $i ] ) ? $temp_gift_card_receivers_emails[ $coupon_id ][ $i ] : $gift_certificate_sender_email;
									if ( isset( $receivers_messages[ $coupon_id ][ $i ] ) && ! empty( $receivers_messages[ $coupon_id ][ $i ] ) ) {
										$message_from_sender = $receivers_messages[ $coupon_id ][ $i ];
										unset( $email[ $coupon_id ][ $i ] );
										update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
									} else {
										$message_from_sender = '';
									}

									$this->generate_smart_coupon( $email_id, $amount, $order_id, $coupon, $discount_type, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email );
									$smart_coupon_codes = array();
								}
							}
						}
					} else {

						$coupon_receiver_email = ( ! empty( $temp_gift_card_receivers_emails[ $coupon_id ][0] ) ) ? $temp_gift_card_receivers_emails[ $coupon_id ][0] : $gift_certificate_sender_email;

						$sc_disable_email_restriction = get_post_meta( $coupon_id, 'sc_disable_email_restriction', true );

						if ( ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {
							$old_customers_email_ids = (array) maybe_unserialize( get_post_meta( $coupon_id, 'customer_email', true ) );
						}

						if ( $operation == 'add' && $auto_generation_of_code != 'yes' && $discount_type != 'smart_coupon' ) {
							$message_index = ( ! empty( $temp_gift_card_receivers_emails[ $coupon_id ] ) && is_array( $temp_gift_card_receivers_emails[ $coupon_id ] ) ) ? array_search( $email_id, $temp_gift_card_receivers_emails[ $coupon_id ], true ) : false;

							if ( $message_index !== false && isset( $receivers_messages[ $coupon_id ][ $message_index ] ) && ! empty( $receivers_messages[ $coupon_id ][ $message_index ] ) ) {
								$message_from_sender = $receivers_messages[ $coupon_id ][ $message_index ];
								unset( $temp_gift_card_receivers_emails[ $coupon_id ][ $message_index ] );
								update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $temp_gift_card_receivers_emails );
							} else {
								$message_from_sender = '';
							}

							for ( $i = 0; $i < $qty; $i++ ) {

								$coupon_details = array(
									$coupon_receiver_email  => array(
										'parent'    => $coupon_id,
										'code'      => $coupon_title,
										'amount'    => $coupon_amount,
									),
								);

								$receiver_name = '';

								$this->sa_email_coupon( $coupon_details, $discount_type, $order_id, $receiver_name, $message_from_sender );

							}

							if ( $qty > 0 && ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {
								for ( $i = 0; $i < $qty; $i++ ) {
									$old_customers_email_ids[] = $coupon_receiver_email;
								}
							}
						} elseif ( $operation == 'remove' && $discount_type != 'smart_coupon' && ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {

							$key = array_search( $coupon_receiver_email, $old_customers_email_ids );

							if ( $key !== false ) {
								unset( $old_customers_email_ids[ $key ] );
							}
						}

						if ( ( $sc_disable_email_restriction == 'no' || empty( $sc_disable_email_restriction ) ) ) {
							update_post_meta( $coupon_id, 'customer_email', $old_customers_email_ids );
						}
					}
				}
			}

		}

		/**
		 * Get receiver's email addresses
		 *
		 * @param array  $coupon_details
		 * @param string $gift_certificate_sender_email
		 * @return array $receivers_email array of receiver's email
		 */
		public function get_receivers_detail( $coupon_details = array(), $gift_certificate_sender_email = '' ) {

			if ( count( $coupon_details ) <= 0 ) { return 0;
			}

			global $woocommerce;

			$all_discount_types = wc_get_coupon_types();

			$receivers_email = array();

			foreach ( $coupon_details as $coupon_id => $emails ) {
				$discount_type = get_post_meta( $coupon_id, 'discount_type', true );
				if ( ! empty( $discount_type ) && array_key_exists( $discount_type, $all_discount_types ) ) {
					$receivers_email = array_merge( $receivers_email, array_diff( $emails, array( $gift_certificate_sender_email ) ) );
				}
			}

			return $receivers_email;
		}

		/**
		 * Function to process coupons based on change in order status
		 *
		 * @param int    $order_id
		 * @param string $operation
		 */
		public function process_coupons( $order_id, $operation ) {
			global $smart_coupon_codes;

			$smart_coupon_codes = array();
			$message_from_sender = '';

			$receivers_emails = get_post_meta( $order_id, 'gift_receiver_email', true );
			$receivers_messages = get_post_meta( $order_id, 'gift_receiver_message', true );
			$is_coupon_sent   = get_post_meta( $order_id, 'coupon_sent', true );

			if ( $is_coupon_sent == 'yes' ) { return;
			}

			$sc_called_credit_details = get_post_meta( $order_id, 'sc_called_credit_details', true );

			$order = wc_get_order( $order_id );
			$order_items = (array) $order->get_items();

			if ( count( $order_items ) <= 0 ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				$order_billing_email      = $order->get_billing_email();
				$order_billing_first_name = $order->get_billing_first_name();
				$order_billing_last_name  = $order->get_billing_last_name();
			} else {
				$order_billing_email      = ( ! empty( $order->billing_email ) ) ? $order->billing_email : '';
				$order_billing_first_name = ( ! empty( $order->billing_first_name ) ) ? $order->billing_first_name : '';
				$order_billing_last_name  = ( ! empty( $order->billing_last_name ) ) ? $order->billing_last_name : '';
			}

			if ( is_array( $receivers_emails ) && ! empty( $receivers_emails ) ) {

				foreach ( $receivers_emails as $coupon_id => $emails ) {
					foreach ( $emails as $key => $email ) {
						if ( empty( $email ) ) {
							$email = $order_billing_email;
							$receivers_emails[ $coupon_id ][ $key ] = $email;
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
			if ( ! empty( $email ) ) {
				update_post_meta( $order_id, 'temp_gift_card_receivers_emails', $email );
			}

			$gift_certificate_receiver = true;
			$gift_certificate_sender_name = $order_billing_first_name . ' ' . $order_billing_last_name;
			$gift_certificate_sender_email = $order_billing_email;
			$gift_certificate_receiver_name = '';

			$receivers_detail = array();
			$email_to_credit = array();
			$receiver_count = 0;

			if ( is_array( $sc_called_credit_details ) && count( $sc_called_credit_details ) > 0 && $operation == 'add' ) {

				foreach ( $order_items as $item_id => $item ) {

					$product = $order->get_product_from_item( $item );

					if ( $this->is_wc_gte_30() ) {
						$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
						$product_id = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) ) ) ? $product->get_parent_id() : $product->get_id();
					} else {
						$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
					}

					$coupon_titles = get_post_meta( $product_id, '_coupon_title', true );

					if ( $coupon_titles ) {

						foreach ( $coupon_titles as $coupon_title ) {
							$coupon = new WC_Coupon( $coupon_title );
							if ( $this->is_wc_gte_30() ) {
								$coupon_id     = $coupon->get_id();
								$coupon_amount = $coupon->get_amount();
							} else {
								$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
								$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
							}

							if ( ! isset( $receivers_emails[ $coupon_id ] ) ) { continue;
							}
							for ( $i = 0; $i < $item['qty']; $i++ ) {
								if ( isset( $receivers_emails[ $coupon_id ][0] ) ) {
									if ( ! isset( $email_to_credit[ $receivers_emails[ $coupon_id ][0] ] ) ) {
										$email_to_credit[ $receivers_emails[ $coupon_id ][0] ] = array();
									}
									if ( isset( $sc_called_credit_details[ $item_id ] ) && ! empty( $sc_called_credit_details[ $item_id ] ) ) {

										if ( $this->is_coupon_amount_pick_from_product_price( array( $coupon_title ) ) ) {
											$email_to_credit[ $receivers_emails[ $coupon_id ][0] ][] = $coupon_id . ':' . $sc_called_credit_details[ $item_id ];
										} else {
											$email_to_credit[ $receivers_emails[ $coupon_id ][0] ][] = $coupon_id . ':' . $coupon_amount;
										}

										unset( $receivers_emails[ $coupon_id ][0] );
										$receivers_emails[ $coupon_id ] = array_values( $receivers_emails[ $coupon_id ] );
									}
								}
							}
						}
					}
					if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && $product->get_price() >= 0 ) {
						$item['sc_called_credit'] = ( ! empty( $sc_called_credit_details[ $item_id ] ) ) ? $sc_called_credit_details[ $item_id ] : '';
					}
				}
			}

			if ( ! empty( $email_to_credit ) && count( $email_to_credit ) > 0 ) {
				$update_temp_email = false;
				foreach ( $email_to_credit as $email_id => $credits ) {
					$email_to_credit[ $email_id ] = array_count_values( $credits );
					foreach ( $email_to_credit[ $email_id ] as $coupon_credit => $qty ) {
						$coupon_details = explode( ':', $coupon_credit );
						$coupon_title = get_the_title( $coupon_details[0] );
						$coupon = new WC_Coupon( $coupon_title );
						$credit_amount = $coupon_details[1];
						if ( $this->is_wc_gte_30() ) {
							$coupon_id     = $coupon->get_id();
							$discount_type = $coupon->get_discount_type();
						} else {
							$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						}
						$message_index = array_search( $email_id, $email[ $coupon_id ], true );
						if ( $message_index !== false && isset( $receivers_messages[ $coupon_id ][ $message_index ] ) && ! empty( $receivers_messages[ $coupon_id ][ $message_index ] ) ) {
							$message_from_sender = $receivers_messages[ $coupon_id ][ $message_index ];
						} else {
							$message_from_sender = '';
						}
						for ( $i = 0; $i < $qty; $i++ ) {
							if ( $discount_type != 'smart_coupon' ) { continue;	// only process smart_coupon here, rest coupon will be processed by function update_coupon
							}
							$this->generate_smart_coupon( $email_id, $credit_amount, $order_id, $coupon, 'smart_coupon', $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email );
							$smart_coupon_codes = array();
						}
					}
				}
				foreach ( $email_to_credit as $email => $coupon_detail ) {
					if ( $email == $gift_certificate_sender_email ) { continue;
					}
					$receiver_count += count( $coupon_detail );
				}
			}

			if ( count( $order_items ) > 0 ) {

				$flag = false;

				foreach ( $order_items as $item_id => $item ) {

					$product = $order->get_product_from_item( $item );
					if ( $this->is_wc_gte_30() ) {
						$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
						$product_id = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) ) ) ? $product->get_parent_id() : $product->get_id();
					} else {
						$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
					}

					$coupon_titles = get_post_meta( $product_id, '_coupon_title', true );

					if ( $coupon_titles ) {

						$flag = true;

						if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && $product->get_price() >= 0 ) {
							$item['sc_called_credit'] = ( ! empty( $sc_called_credit_details[ $item_id ] ) ) ? $sc_called_credit_details[ $item_id ] : '';
						}

						$this->update_coupons( $coupon_titles, $email, '', $operation, $item, $gift_certificate_receiver, $gift_certificate_receiver_name, $message_from_sender, $gift_certificate_sender_name, $gift_certificate_sender_email, $order_id );

						if ( $operation == 'add' && ! empty( $receivers_emails_list ) ) {
							$receivers_detail += $this->get_receivers_detail( $receivers_emails_list, $gift_certificate_sender_email );
						}
					}
				}

				if ( $flag && $operation == 'add' ) {
					update_post_meta( $order_id, 'coupon_sent', 'yes' );              // to know whether coupon has sent or not
				}
			}

			$is_send_email = get_option( 'smart_coupons_is_send_email', 'yes' );

			if ( $is_send_email == 'yes' && ( count( $receivers_detail ) + $receiver_count ) > 0 ) {
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
		 * @param array  $receivers_detail
		 * @param string $gift_certificate_receiver_name
		 * @param mixed  $email
		 * @param string $gift_certificate_sender_email
		 * @param int    $receiver_count
		 */
		public function acknowledge_gift_certificate_sender( $receivers_detail = array(), $gift_certificate_receiver_name = '', $email = '', $gift_certificate_sender_email = '', $receiver_count = '' ) {

			if ( empty( $receiver_count ) ) { return;
			}

			ob_start();

			$subject = __( 'Gift Card sent successfully!', WC_SC_TEXT_DOMAIN );

			do_action( 'woocommerce_email_header', $subject );

			echo sprintf( __( 'You have successfully sent %1$d %2$s to %3$s (%4$s)', WC_SC_TEXT_DOMAIN ), $receiver_count, _n( 'Gift Card', 'Gift Cards', count( $receivers_detail ), WC_SC_TEXT_DOMAIN ), $gift_certificate_receiver_name, implode( ', ', array_unique( $receivers_detail ) ) );

			do_action( 'woocommerce_email_footer' );

			$message = ob_get_clean();

			if ( ! class_exists( 'WC_Email' ) ) {
				include_once dirname( WC_PLUGIN_FILE ) . '/includes/emails/class-wc-email.php';
			}

			$mailer = new WC_Email();
			$headers = $mailer->get_headers();
			$attachments = $mailer->get_attachments();

			wc_mail( $gift_certificate_sender_email, $subject, $message, $headers, $attachments );

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
		 * Function to Restore Smart Coupon Amount back, when an order which was created using this coupon, is refunded or cancelled,
		 *
		 * @param int $order_id
		 */
		public function sa_restore_smart_coupon_amount( $order_id = 0 ) {

			if ( empty( $order_id ) ) { return;
			}

			$order = wc_get_order( $order_id );

			$coupons = $order->get_items( 'coupon' );

			if ( ! empty( $coupons ) ) {

				foreach ( $coupons as $item_id => $item ) {

					if ( empty( $item['name'] ) ) { continue;
					}

					$coupon = new WC_Coupon( $item['name'] );

					if ( $this->is_wc_gte_30() ) {
						$coupon_id     = $coupon->get_id();
						$coupon_amount = $coupon->get_amount();
						$discount_type = $coupon->get_discount_type();
						$usage_count   = $coupon->get_usage_count();
					} else {
						$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$usage_count   = ( ! empty( $coupon->usage_count ) ) ? $coupon->usage_count : 0;
					}

					if ( empty( $discount_type ) || $discount_type != 'smart_coupon' ) { continue;
					}

					$update = false;
					if ( ! empty( $item['discount_amount'] ) ) {
						$coupon_amount += $item['discount_amount'];
						$usage_count -= 1;
						if ( $usage_count < 0 ) {
							$usage_count = 0;
						}
						$update = true;
					}

					if ( $update ) {
						update_post_meta( $coupon_id, 'coupon_amount', $coupon_amount );
						update_post_meta( $coupon_id, 'usage_count', $usage_count );
						wc_update_order_item_meta( $item_id, 'discount_amount', 0 );
					}
				}
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



	}

}

WC_SC_Coupon_Process::get_instance();
