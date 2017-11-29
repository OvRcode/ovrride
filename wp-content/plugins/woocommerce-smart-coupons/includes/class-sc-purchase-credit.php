<?php
/**
 * Purchase Credit Features
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Purchase_Credit' ) ) {

	/**
	 * Class for handling Purchase credit feature
	 */
	class WC_SC_Purchase_Credit {

		/**
		 * Variable to hold instance of WC_SC_Purchase_Credit
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'woocommerce_single_product_summary', array( $this, 'call_for_credit_form' ), 20 );
			add_filter( 'woocommerce_call_for_credit_form_template', array( $this, 'woocommerce_call_for_credit_form_template_path' ) );

			add_filter( 'woocommerce_is_purchasable', array( $this, 'make_product_purchasable' ), 10, 2 );
			add_filter( 'woocommerce_get_price_html', array( $this, 'price_html_for_purchasing_credit' ), 10, 2 );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'override_price_before_calculate_totals' ) );

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			if ( is_plugin_active( 'woocommerce-gateway-paypal-express/woocommerce-gateway-paypal-express.php' ) ) {
				add_action( 'woocommerce_ppe_checkout_order_review', array( $this, 'gift_certificate_receiver_detail_form' ) );
				add_action( 'woocommerce_ppe_do_payaction', array( $this, 'ppe_save_called_credit_details_in_order' ) );
			}

			add_filter( 'woocommerce_cart_item_price', array( $this, 'woocommerce_cart_item_price_html' ), 10, 3 );
			add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'gift_certificate_receiver_detail_form' ) );

			add_action( 'wp_loaded', array( $this, 'purchase_credit_hooks' ) );

			add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_called_credit_details_in_order' ), 10, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'call_for_credit_cart_item_data' ), 10, 3 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'sc_woocommerce_add_to_cart_validation' ), 10, 6 );
			add_action( 'woocommerce_add_to_cart', array( $this, 'save_called_credit_in_session' ), 10, 6 );


		}

		/**
		 * Get single instance of WC_SC_Purchase_Credit
		 *
		 * @return WC_SC_Purchase_Credit Singleton object of WC_SC_Purchase_Credit
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
		 * Display form to enter value of the store credit to be purchased
		 */
		public function call_for_credit_form() {
			global $product, $woocommerce;

			if ( $product instanceof WC_Product_Variation ) { return;
			}

			if ( $this->is_wc_gte_30() ) {
				$product_id = $product->get_id();
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}

			// MADE CHANGES IN THE CONDITION TO SHOW INPUT FIELDFOR PRICE ONLY FOR COUPON AS A PRODUCT
			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ( ! ( $product->get_price() != '' || ( is_plugin_active( 'woocommerce-name-your-price/woocommerce-name-your-price.php' ) && ( get_post_meta( $product_id, '_nyp', true ) == 'yes' ) ) ) ) ) {

				$js = "
							var validateCreditCalled = function(){
								var enteredCreditAmount = jQuery('input#credit_called').val();
								if ( enteredCreditAmount < 0.01 ) {
									jQuery('p#error_message').text('" . __( 'Invalid amount', WC_SC_TEXT_DOMAIN ) . "');
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
								if ( jQuery('input[name=quantity]').length ) {
									jQuery('input[name=quantity]').append('<input type=\"hidden\" id=\"hidden_credit\" name=\"credit_called[" . $product_id . "]\" value=\"'+jQuery('input#credit_called').val()+'\" />');
								} else {
									jQuery('input[name=\"add-to-cart\"]').after('<input type=\"hidden\" id=\"hidden_credit\" name=\"credit_called[" . $product_id . "]\" value=\"'+jQuery('input#credit_called').val()+'\" />');
								}
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

				wc_enqueue_js( $js );

				$smart_coupon_store_gift_page_text = get_option( 'smart_coupon_store_gift_page_text' );
				$smart_coupon_store_gift_page_text = ( ! empty( $smart_coupon_store_gift_page_text ) ) ? $smart_coupon_store_gift_page_text . ' ' :  __( 'Purchase Credit worth', WC_SC_TEXT_DOMAIN ) . ' ';

				include( apply_filters( 'woocommerce_call_for_credit_form_template', 'templates/call-for-credit-form.php' ) );

			}
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
		 * Make product whose price is set as zero but is for purchasing credit, purchasable
		 *
		 * @param boolean    $purchasable
		 * @param WC_Product $product
		 * @return boolean $purchasable
		 */
		public function make_product_purchasable( $purchasable, $product ) {

			if ( $this->is_wc_gte_30() ) {
				$product_id = ( ! empty( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}
			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $product instanceof WC_Product && $product->get_price() === '' && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product->get_price() > 0 ) ) {
				return true;
			}

			return $purchasable;
		}

		/**
		 * Remove price html for product which is selling any amount of storecredit
		 *
		 * @param string     $price
		 * @param WC_Product $product
		 *
		 * @return string $price
		 */
		public function price_html_for_purchasing_credit( $price = null, $product = null ) {

			if ( $this->is_wc_gte_30() ) {
				$product_id = ( ! empty( $product ) && is_callable( array( $product, 'get_id' ) ) ) ? $product->get_id() : 0;
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}
			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			$is_product = is_a( $product, 'WC_Product' );
			$is_purchasable_credit = $this->is_coupon_amount_pick_from_product_price( $coupons );
			$product_price = $product->get_price();

			if ( ! empty( $coupons ) && $is_product === true && $is_purchasable_credit === true && ( ! ( $product_price > 0 ) || empty( $product_price ) ) ) {
				return '';
			}

			return $price;
		}

		/**
		 * Set price for store credit to be purchased before calculating total in cart
		 *
		 * @param WC_Cart $cart_object
		 */
		public function override_price_before_calculate_totals( $cart_object ) {

			foreach ( $cart_object->cart_contents as $key => $value ) {

				$product = $value['data'];
				if ( $this->is_wc_gte_30() ) {
					$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
					$product_id = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) ) ) ? $product->get_parent_id() : $product->get_id();
					$product_price = ( ! empty( $product ) && is_callable( array( $product, 'get_price' ) ) ) ? $product->get_price() : 0;
				} else {
					$product_id    = ( ! empty( $product->id ) ) ? $product->id : 0;
					$product_price = ( ! empty( $product->price ) ) ? $product->price : 0;
				}

				$coupons = get_post_meta( $product_id, '_coupon_title', true );

				if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product_price > 0 ) ) {

					$price = ( isset( WC()->session->credit_called[ $key ] ) ) ? WC()->session->credit_called[ $key ]: '';

					if ( $price <= 0 ) {
						WC()->cart->set_quantity( $key, 0 );    // Remove product from cart if price is not found either in session or in product
						continue;
					}

					if ( $this->is_wc_gte_30() ) {
						$cart_object->cart_contents[ $key ]['data']->set_price( $price );
					} else {
						$cart_object->cart_contents[ $key ]['data']->price = $price;
					}

				}
			}

		}

		/**
		 * Display store credit's value as cart item's price
		 *
		 * @param string $product_price
		 * @param array  $cart_item associative array of cart item
		 * @param string $cart_item_key
		 * @return string product's price with currency symbol
		 */
		public function woocommerce_cart_item_price_html( $product_price, $cart_item, $cart_item_key ) {

			$gift_certificate = WC()->session->credit_called;

			if ( ! empty( $gift_certificate ) && isset( $gift_certificate[ $cart_item_key ] ) && ! empty( $gift_certificate[ $cart_item_key ] ) ) {
				return wc_price( $gift_certificate[ $cart_item_key ] );
			}

			return $product_price;

		}

		/**
		 * Function to display form for entering details of the gift certificate's receiver
		 */
		public function gift_certificate_receiver_detail_form() {
			global $woocommerce, $total_coupon_amount;

			$form_started = false;

			$all_discount_types = wc_get_coupon_types();

			foreach ( WC()->cart->cart_contents as $product ) {

				$coupon_titles = get_post_meta( $product['product_id'], '_coupon_title', true );

				$_product = wc_get_product( $product['product_id'] );

				$price = $_product->get_price();

				if ( $coupon_titles ) {

					foreach ( $coupon_titles as $coupon_title ) {

						$coupon = new WC_Coupon( $coupon_title );
						if ( $this->is_wc_gte_30() ) {
							$coupon_id     = $coupon->get_id();
							$discount_type = $coupon->get_discount_type();
							$coupon_amount = $coupon->get_amount();
						} else {
							$coupon_id     = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
							$discount_type = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
							$coupon_amount = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						}

						$pick_price_of_prod = get_post_meta( $coupon_id, 'is_pick_price_of_product', true );
						$smart_coupon_gift_certificate_form_page_text  = get_option( 'smart_coupon_gift_certificate_form_page_text' );
						$smart_coupon_gift_certificate_form_page_text  = ( ! empty( $smart_coupon_gift_certificate_form_page_text ) ) ? $smart_coupon_gift_certificate_form_page_text : __( 'Coupon Receiver Details', WC_SC_TEXT_DOMAIN );
						$smart_coupon_gift_certificate_form_details_text  = get_option( 'smart_coupon_gift_certificate_form_details_text' );
						$smart_coupon_gift_certificate_form_details_text  = ( ! empty( $smart_coupon_gift_certificate_form_details_text ) ) ? $smart_coupon_gift_certificate_form_details_text : '';     // Enter email address and optional message for Gift Card receiver

						// MADE CHANGES IN THE CONDITION TO SHOW FORM
						if ( array_key_exists( $discount_type, $all_discount_types ) || ( $pick_price_of_prod == 'yes' && $price == '' ) || ( $pick_price_of_prod == 'yes' &&  $price != '' && $coupon_amount > 0)  ) {

							if ( ! $form_started ) {

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

								wc_enqueue_js( $js );

								?>

								<div class="gift-certificate sc_info_box">
									<h3><?php _e( stripslashes( $smart_coupon_gift_certificate_form_page_text ) ); ?></h3>
										<?php if ( ! empty( $smart_coupon_gift_certificate_form_details_text ) ) { ?>
										<p><?php _e( stripslashes( $smart_coupon_gift_certificate_form_details_text ) , WC_SC_TEXT_DOMAIN ); ?></p>
										<?php } ?>
										<div class="gift-certificate-show-form">
											<p><?php _e( 'Your order contains coupons. What would you like to do?', WC_SC_TEXT_DOMAIN ); ?></p>
											<ul class="show_hide_list" style="list-style-type: none;">
												<li><input type="radio" id="hide_form" name="is_gift" value="no" checked="checked" /> <label for="hide_form"><?php _e( 'Send coupons to me', WC_SC_TEXT_DOMAIN ); ?></label></li>
												<li>
												<input type="radio" id="show_form" name="is_gift" value="yes" /> <label for="show_form"><?php _e( 'Gift coupons to someone else', WC_SC_TEXT_DOMAIN ); ?></label>
												<ul class="single_multi_list" style="list-style-type: none;">
												<li><input type="radio" id="send_to_one" name="sc_send_to" value="one" checked="checked" /> <label for="send_to_one"><?php _e( 'Send to one person', WC_SC_TEXT_DOMAIN ); ?></label>
												<input type="radio" id="send_to_many" name="sc_send_to" value="many" /> <label for="send_to_many"><?php _e( 'Send to different people', WC_SC_TEXT_DOMAIN ); ?></label></li>
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
							<div class="amount"></div>
							<div class="email"><input class="gift_receiver_email" type="text" placeholder="<?php _e( 'Email address', WC_SC_TEXT_DOMAIN ); ?>..." name="gift_receiver_email[0][0]" value="" /></div>
						</div>
						<div class="message_row">
							<div class="message"><textarea placeholder="<?php _e( 'Message', WC_SC_TEXT_DOMAIN ); ?>..." class="gift_receiver_message" name="gift_receiver_message[0][0]" cols="50" rows="5"></textarea></div>
						</div>
					</div>
				</div>
				</div></div>
				<?php
			}

		}

		/**
		 * Function to add hooks based on conditions
		 */
		public function purchase_credit_hooks() {

			if ( $this->is_wc_gte_30() ) {
				add_action( 'woocommerce_new_order_item', array( $this, 'save_called_credit_details_in_order_item' ), 10, 3 );
			} else {
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'save_called_credit_details_in_order_item_meta' ), 10, 2 );
			}

		}

		/**
		 * Display form to enter receiver's details on checkout page
		 *
		 * @param WC_Coupon $coupon
		 * @param array     $product
		 */
		public function add_text_field_for_email( $coupon = '', $product = '' ) {
			global $total_coupon_amount;

			if ( empty( $coupon ) ) { return;
			}

			$coupon_data = $this->get_coupon_meta_data( $coupon );

			if ( $this->is_wc_gte_30() ) {
				$coupon_id        = ( is_callable( array( $coupon, 'get_id' ) ) ) ? $coupon->get_id() : '';
				$coupon_code      = ( is_callable( array( $coupon, 'get_code' ) ) ) ? $coupon->get_code() : '';
				$product_price    = ( is_callable( array( $product['data'], 'get_price' ) ) ) ? $product['data']->get_price() : 0;
				$coupon_amount    = ( is_callable( array( $coupon, 'get_amount' ) ) ) ? $coupon->get_amount() : 0;
				$is_free_shipping = ( is_callable( array( $coupon, 'get_free_shipping' ) ) ) ? ( ( $coupon->get_free_shipping() ) ? 'yes' : 'no' ) : '';
				$discount_type    = ( is_callable( array( $coupon, 'get_discount_type' ) ) ) ? $coupon->get_discount_type() : '';
			} else {
				$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
				$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
				$product_price    = ( ! empty( $product['data']->price ) ) ? $product['data']->price : 0;
				$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
				$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
				$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
			}

			for ( $i = 1; $i <= $product['quantity']; $i++ ) {

				$_coupon_amount = ( $this->is_coupon_amount_pick_from_product_price( array( $coupon_code ) ) ) ? $product_price : $coupon_amount;

				// NEWLY ADDED CONDITION TO NOT TO SHOW TEXTFIELD IF COUPON AMOUNT IS "0"
				if ( $_coupon_amount != '' || $_coupon_amount > 0 || $coupon_amount > 0 || $is_free_shipping == 'yes' ) {

					$total_coupon_amount += $_coupon_amount;

					$formatted_coupon_text = '';
					if ( ! empty( $_coupon_amount ) || ! empty( $coupon_amount ) ) {
						$formatted_coupon_amount = ( $coupon_amount <= 0 ) ? wc_price( $_coupon_amount ) : $coupon_data['coupon_amount'];
						$formatted_coupon_text .= $coupon_data['coupon_type'];
						if ( $is_free_shipping == 'yes' ) {
							$formatted_coupon_text .= ' &amp; ';
						}
					}
					if ( $is_free_shipping == 'yes' ) {
						$formatted_coupon_text .= __( 'Free Shipping coupon', WC_SC_TEXT_DOMAIN );
					}
					if ( $discount_type != 'smart_coupon' && strpos( $formatted_coupon_text, 'coupon' ) === false ) {
						$formatted_coupon_text .= ' ' . __( 'coupon', WC_SC_TEXT_DOMAIN );
					}
					?>
					<div class="form_table">
						<div class="email_amount">
							<div class="amount"><?php echo sprintf( __( 'Send %1$s of %2$s to', WC_SC_TEXT_DOMAIN ), $formatted_coupon_text, $formatted_coupon_amount ); ?></div>
							<div class="email"><input class="gift_receiver_email" type="text" placeholder="<?php _e( 'Email address', WC_SC_TEXT_DOMAIN ); ?>..." name="gift_receiver_email[<?php echo $coupon_id; ?>][]" value="" /></div>
						</div>
						<div class="message_row">
							<div class="sc_message"><textarea placeholder="<?php _e( 'Message', WC_SC_TEXT_DOMAIN ); ?>..." class="gift_receiver_message" name="gift_receiver_message[<?php echo $coupon_id; ?>][]" cols="50" rows="5"></textarea></div>
						</div>
					</div>
					<?php
				}
			}

		}

		/**
		 * Save entered credit value by customer in order for further processing
		 *
		 * @param int   $order_id
		 * @param array $posted associative array of posted data
		 */
		public function save_called_credit_details_in_order( $order_id, $posted ) {

			$order = wc_get_order( $order_id );
			$order_items = $order->get_items();

			$sc_called_credit = array();
			$update = false;

			$prices_include_tax = ( get_option( 'woocommerce_prices_include_tax' ) == 'yes' ) ? true : false;

			foreach ( $order_items as $item_id => $order_item ) {

				if ( $this->is_wc_gte_30() ) {
					$item_sc_called_credit = $order_item->get_meta( 'sc_called_credit' );
				} else {
					$item_sc_called_credit = ( ! empty( $order_item['sc_called_credit'] ) ) ? $order_item['sc_called_credit'] : 0;
				}

				if ( ! empty( $item_sc_called_credit ) ) {

					$product = $order->get_product_from_item( $order_item );

					if ( $this->is_wc_gte_30() ) {
						$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ? $product->get_type() : '';
						$product_id   = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) ) ) ? $product->get_parent_id() : $product->get_id();
						$item_qty     = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_quantity' ) ) ) ? $order_item->get_quantity() : 1;
						$item_total   = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_subtotal' ) ) ) ? $order_item->get_subtotal() : 0;
						$item_tax     = ( is_object( $order_item ) && is_callable( array( $order_item, 'get_subtotal_tax' ) ) ) ? $order_item->get_subtotal_tax() : 0;
					} else {
						$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
						$item_qty   = ( ! empty( $order_item['qty'] ) ) ? $order_item['qty'] : 1;
						$item_total = ( ! empty( $order_item['line_total'] ) ) ? $order_item['line_total'] : 0;
						$item_tax   = ( ! empty( $order_item['line_tax'] ) ) ? $order_item['line_tax'] : 0;
					}

					if ( ! empty( $item_qty ) ) {
						$qty = $item_qty;
					} else {
						$qty = 1;
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

							if ( $this->is_coupon_amount_pick_from_product_price( array( $coupon_title ) ) ) {
								$products_price = ( ! $prices_include_tax ) ? $item_total : $item_total + $item_tax;
								$amount = $products_price / $qty;
								$sc_called_credit[ $item_id ] = $amount;
							}

						}

					}

					if ( $this->is_wc_gte_30() ) {
						wc_delete_order_item_meta( $item_id, 'sc_called_credit' );
					} else {
						woocommerce_delete_order_item_meta( $item_id, 'sc_called_credit' );
					}
					$update = true;
				}
			}
			if ( $update ) {
				update_post_meta( $order_id, 'sc_called_credit_details', $sc_called_credit );
			}

			if ( isset( WC()->session->credit_called ) ) { unset( WC()->session->credit_called );
			}

		}

		/**
		 * Save entered credit value by customer in order item meta
		 *
		 * @param int   $item_id
		 * @param array $values associative array containing item's details
		 */
		public function save_called_credit_details_in_order_item_meta( $item_id = 0, $values = array() ) {

			if ( empty( $item_id ) || empty( $values ) ) {
				return;
			}

			if ( $this->is_wc_gte_30() ) {
				if ( ! $values instanceof WC_Order_Item_Product ) {
					return;
				}
				$product = $values->get_product();
				if ( ! is_object( $product ) || ! is_a( $product, 'WC_Product' ) ) {
					return;
				}
				$product_type = ( is_object( $product ) && is_callable( array( $product, 'get_type' ) ) ) ?  $product->get_type() : '';
				$product_id = ( in_array( $product_type, array( 'variable', 'variable-subscription', 'variation', 'subscription_variation' ) ) ) ? $product->get_parent_id() : $product->get_id();
				$qty = ( is_callable( array( $values, 'get_quantity' ) ) ) ? $values->get_quantity() : 1;
				$qty = ( ! empty( $qty ) ) ? $qty : 1;
				$subtotal = ( is_callable( array( $values, 'get_subtotal' ) ) ) ? $values->get_subtotal() : 0;
				$product_price = $subtotal / $qty;
			} else {
				if ( empty( $values['data'] ) ) {
					return;
				}
				$product = $values['data'];
				$product_id = ( ! empty( $values['product_id'] ) ) ? $values['product_id'] : 0;
				$product_price = ( ! empty( $values['data']->price ) ) ? $values['data']->price : 0;
			}

			if ( empty( $product_id ) ) {
				return;
			}

			$coupon_titles = get_post_meta( $product_id, '_coupon_title', true );

			if ( $this->is_coupon_amount_pick_from_product_price( $coupon_titles ) && $product_price > 0 ) {
				if ( $this->is_wc_gte_30() ) {
					wc_add_order_item_meta( $item_id, 'sc_called_credit', $product_price );
				} else {
					woocommerce_add_order_item_meta( $item_id, 'sc_called_credit', $product_price );
				}
			}
		}

		/**
		 * Save entered credit value by customer in order item
		 *
		 * @param int   $item_id
		 * @param array $values associative array containing item's details
		 * @param int 	$order_id
		 */
		public function save_called_credit_details_in_order_item( $item_id = 0, $values = array(), $order_id = 0 ) {

			$this->save_called_credit_details_in_order_item_meta( $item_id, $values );

		}

		/**
		 * Save entered credit value by customer in order for PayPal Express Checkout
		 *
		 * @param WC_Order $order
		 */
		public function ppe_save_called_credit_details_in_order( $order ) {
			if ( $this->is_wc_gte_30() ) {
				$order_id = ( ! empty( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
			} else {
				$order_id = ( ! empty( $order->id ) ) ? $order->id : 0;
			}
			$this->save_called_credit_details_in_order( $order_id, null );
		}

		/**
		 * Save entered credit value by customer in cart item data
		 *
		 * @param array $cart_item_data
		 * @param int   $product_id
		 * @param int   $variation_id
		 * @return array $cart_item_data
		 */
		public function call_for_credit_cart_item_data( $cart_item_data = array(), $product_id = '', $variation_id = '' ) {
			if ( ! empty( $variation_id ) && $variation_id > 0 || empty( $product_id ) ) { return $cart_item_data;
			}

			$_product = wc_get_product( $product_id );

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $_product->get_price() > 0 ) ) {
				$cart_item_data['credit_amount'] = ( ! empty( $_REQUEST['credit_called'] ) && ! empty( $_REQUEST['add-to-cart'] ) && ! empty( $_REQUEST['credit_called'][ $_REQUEST['add-to-cart'] ] ) ) ? $_REQUEST['credit_called'][ $_REQUEST['add-to-cart'] ] : 0;
				return $cart_item_data;
			}

			return $cart_item_data;
		}

		/**
		 * Validate addition of product for purchasing store credit to cart
		 *
		 * @param boolean $validation
		 * @param int     $product_id
		 * @param int     $quantity
		 * @param int     $variation_id optional default:''
		 * @param array   $variations optional default:'' associative array containing variations attributes & values
		 * @param array   $cart_item_data optional default:array() associative array containing additional data
		 * @return boolean $validation
		 */
		public function sc_woocommerce_add_to_cart_validation( $validation, $product_id, $quantity, $variation_id = '', $variations = '', $cart_item_data = array() ) {

			if ( ! isset( $_POST['credit_called'] ) ) {
				return $validation;
			}

			$cart_item_data['credit_amount'] = $_POST['credit_called'][ $product_id ];

			$cart_id = WC()->cart->generate_cart_id( $product_id, $variation_id, $variations, $cart_item_data );

			if ( isset( WC()->session->credit_called[ $cart_id ] ) && empty( WC()->session->credit_called[ $cart_id ] ) ) {
				return false;
			}

			return $validation;
		}

		/**
		 * Save entered credit value by customer in session
		 *
		 * @param string $cart_item_key
		 * @param int    $product_id
		 * @param int    $quantity
		 * @param int    $variation_id
		 * @param array  $variation
		 * @param array  $cart_item_data
		 */
		public function save_called_credit_in_session( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			if ( ! empty( $variation_id ) && $variation_id > 0 ) { return;
			}
			if ( ! isset( $cart_item_data['credit_amount'] ) || empty( $cart_item_data['credit_amount'] ) ) { return;
			}

			$_product = wc_get_product( $product_id );

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $_product->get_price() > 0 ) ) {
				if ( ! isset( WC()->session->credit_called ) ) {
					WC()->session->credit_called = array();
				}
				WC()->session->credit_called += array( $cart_item_key => $cart_item_data['credit_amount'] );

			}

		}

	}

}

WC_SC_Purchase_Credit::get_instance();
