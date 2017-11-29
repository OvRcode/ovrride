<?php
/**
 * Smart Coupons Display 
 *
 * @author 		StoreApps
 * @since 		3.3.0
 * @version 	1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_SC_Display_Coupons' ) ) {

	/**
	 * Class for handling display feature for coupons
	 */
	class WC_SC_Display_Coupons {

		/**
		 * Variable to hold instance of WC_SC_Display_Coupons
		 * @var $instance
		 */
		private static $instance = null;

		/**
		 * Custom endpoint name.
		 *
		 * @var string
		 */
		public static $endpoint = 'wc-smart-coupons';

		/**
		 * Constructor
		 */
		public function __construct() {

			add_action( 'wp_ajax_sc_get_available_coupons', array( $this, 'get_available_coupons_html' ) );
			add_action( 'wp_ajax_nopriv_sc_get_available_coupons', array( $this, 'get_available_coupons_html' ) );

			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'show_attached_gift_certificates' ) );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'remove_add_to_cart_button_from_shop_page' ) );

			add_action( 'woocommerce_after_cart_table', array( $this, 'show_available_coupons_after_cart_table' ) );
			add_action( 'woocommerce_before_checkout_form', array( $this, 'show_available_coupons_before_checkout_form' ), 11 );

			add_action( 'wp_loaded', array( $this, 'myaccount_display_coupons' ) );

			add_action( 'add_meta_boxes', array( $this, 'add_generated_coupon_details' ) );
			add_action( 'woocommerce_view_order', array( $this, 'generated_coupon_details_view_order' ) );
			add_action( 'woocommerce_email_after_order_table', array( $this, 'generated_coupon_details_after_order_table' ), 10, 3 );

			add_action( 'wp_footer', array( $this, 'frontend_styles_and_scripts' ) );

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
		 * Get single instance of WC_SC_Display_Coupons
		 *
		 * @return WC_SC_Display_Coupons Singleton object of WC_SC_Display_Coupons
		 */
		public static function get_instance() {
			// Check if instance is already exists
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Function to show available coupons on Cart & Checkout page
		 *
		 * @param string $available_coupons_heading
		 * @param string $page
		 */
		public function show_available_coupons( $available_coupons_heading = '', $page = 'checkout' ) {

			$coupons = $this->sc_get_available_coupons_list( array() );

			if ( empty( $coupons ) ) { return false;
			}

			?>
			<div id="coupons_list" style="display: none;"><h3><?php _e( stripslashes( $available_coupons_heading ), WC_SC_TEXT_DOMAIN ) ?></h3><div id="all_coupon_container">
				<?php

				$max_coupon_to_show = get_option( 'wc_sc_setting_max_coupon_to_show', 5 );
				$show_max = apply_filters( 'wc_sc_max_coupon_to_show', $max_coupon_to_show );

				$coupons_applied = WC()->cart->get_applied_coupons();

				foreach ( $coupons as $code ) {

					if ( $max_coupon_to_show <= 0 ) {
						break;
					}

					if ( in_array( strtolower( $code->post_title ), array_map( 'strtolower', $coupons_applied ) ) ) { continue;
					}

					$coupon = new WC_Coupon( $code->post_title );

					if ( 'woocommerce_before_my_account' != current_filter() && ! $coupon->is_valid() ) {
						continue;
					}

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

					if ( ( empty( $coupon_amount ) || $coupon_amount == 0 ) && $is_free_shipping == 'no' && ! empty( $discount_type ) && $discount_type != 'free_gift' ) {
						continue;
					}

					if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
						$expiry_date = $expiry_date->getTimestamp();
					} elseif ( ! is_int( $expiry_date ) ) {
						$expiry_date = strtotime( $expiry_date );
					}

					if ( empty( $discount_type ) || ( ! empty( $expiry_date ) && current_time( 'timestamp' ) > $expiry_date ) ) {
						continue;
					}

					$coupon_post = get_post( $coupon_id );

					$coupon_data = $this->get_coupon_meta_data( $coupon );

					echo '<div class="coupon-container apply_coupons_credits blue medium" name="' . $coupon_code . '" style="cursor: pointer">
						<div class="coupon-content blue dashed small" name="' . $coupon_code . '">
							<div class="discount-info" >';

					if ( ! empty( $coupon_data['coupon_amount'] ) && $coupon_amount != 0 ) {
						echo $coupon_data['coupon_amount'] . ' ' . $coupon_data['coupon_type'];
						if ( $is_free_shipping == 'yes' ) {
							echo __( ' &amp; ', WC_SC_TEXT_DOMAIN );
						}
					}

					if ( $is_free_shipping == 'yes' ) {
						echo __( 'Free Shipping', WC_SC_TEXT_DOMAIN );
					}
					echo '</div>';

					echo '<div class="code">' . $coupon_code . '</div>';

					$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
					if ( ! empty( $coupon_post->post_excerpt ) && $show_coupon_description == 'yes' ) {
						echo '<div class="discount-description">' . $coupon_post->post_excerpt . '</div>';
					}

					if ( ! empty( $expiry_date ) ) {

						$expiry_date = $this->get_expiration_format( $expiry_date );

						echo '<div class="coupon-expire">' . $expiry_date . '</div>';

					} else {

						echo '<div class="coupon-expire">' . __( 'Never Expires', WC_SC_TEXT_DOMAIN ) . '</div>';

					}

					echo '</div>
						</div>';

					$max_coupon_to_show--;

				}

				if ( did_action( 'wc_smart_coupons_frontend_styles_and_scripts' ) <= 0 || ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
					$this->frontend_styles_and_scripts( array( 'page' => $page ) );
				}
				?>
			</div></div>
			<?php

		}

		/**
		 * Get available coupon's HTML
		 */
		public function get_available_coupons_html() {
			check_ajax_referer( 'sc-get-available-coupons', 'security' );
			$this->show_available_coupons_before_checkout_form();
			die();
		}

		/**
		 * Function to show available coupons before checkout form
		 */
		public function show_available_coupons_before_checkout_form() {

			$smart_coupon_cart_page_text = get_option( 'smart_coupon_cart_page_text' );
			$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (Click on the coupon to use it)', WC_SC_TEXT_DOMAIN );
			$this->show_available_coupons( $smart_coupon_cart_page_text, 'checkout' );

		}

		/**
		 * Hooks for handling display of coupons on My Account page
		 */
		public function myaccount_display_coupons() {

			if ( $this->is_wc_gte_26() ) {
				add_filter( 'query_vars', array( $this, 'sc_add_query_vars' ), 0 );
				// Change the My Account page title.
				add_filter( 'the_title', array( $this, 'sc_endpoint_title' ) );
				// Insering our new tab/page into the My Account page.
				add_filter( 'woocommerce_account_menu_items', array( $this, 'sc_new_menu_items' ) );
				add_action( 'woocommerce_account_' . self::$endpoint . '_endpoint', array( $this, 'sc_endpoint_content' ) );
			} else {
				add_action( 'woocommerce_before_my_account', array( $this, 'show_smart_coupon_balance' ) );
				add_action( 'woocommerce_before_my_account', array( $this, 'generated_coupon_details_before_my_account' ) );
			}

		}

		/**
		 * Function to show gift certificates that are attached with the product
		 */
		public function show_attached_gift_certificates() {
			global $post, $woocommerce, $wp_rewrite;

			$is_show_associated_coupons = get_option( 'smart_coupons_is_show_associated_coupons', 'no' );

			if ( $is_show_associated_coupons != 'yes' ) { return;
			}

			$coupon_titles = get_post_meta( $post->ID, '_coupon_title', true );

			$_product = wc_get_product( $post->ID );

			if ( $this->is_wc_gte_30() ) {
				$product_type = ( is_object( $_product ) && is_callable( array( $_product, 'get_type' ) ) ) ? $_product->get_type() : '';
			} else {
				$product_type = ( ! empty( $_product->product_type ) ) ? $_product->product_type : '';
			}

			$price = $_product->get_price();

			if ( $coupon_titles && count( $coupon_titles ) > 0 && ! empty( $price ) ) {

				$all_discount_types              = wc_get_coupon_types();
				$smart_coupons_product_page_text = get_option( 'smart_coupon_product_page_text' );
				$smart_coupons_product_page_text = ( ! empty( $smart_coupons_product_page_text ) ) ? $smart_coupons_product_page_text : __( 'By purchasing this product, you will get following coupon(s):', WC_SC_TEXT_DOMAIN );

				$list_started = true;
				$js = '';

				foreach ( $coupon_titles as $coupon_title ) {

					$coupon = new WC_Coupon( $coupon_title );

					if ( $this->is_wc_gte_30() ) {
						$coupon_id        = $coupon->get_id();
						$discount_type    = $coupon->get_discount_type();
						$coupon_amount    = $coupon->get_amount();
						$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
					} else {
						$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
						$discount_type    = ( ! empty( $coupon->discount_type ) ) ? $coupon->discount_type : '';
						$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
						$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
					}

					$is_pick_price_of_product = get_post_meta( $coupon_id, 'is_pick_price_of_product', true );

					if ( $list_started && ! empty( $discount_type ) ) {
						echo '<div class="clear"></div>';
						echo '<div class="gift-certificates">';
						echo '<br /><p>' . __( stripslashes( $smart_coupons_product_page_text ) ) . '';
						echo '<ul>';
						$list_started = false;
					}

					switch ( $discount_type ) {

						case 'smart_coupon':

							if ( $is_pick_price_of_product == 'yes' ) {

								if ( $product_type == 'variable' ) {

									$js = " jQuery('div.gift-certificates').hide();

											var reload_gift_certificate_div = function( variation ) {
												jQuery('div.gift-certificates').show().fadeTo( 100, 0.4 );
												var amount = jQuery(variation.price_html).text();
												jQuery('div.gift-certificates').find('li.pick_price_from_product').remove();
												jQuery('div.gift-certificates').find('ul').append( '<li class=\"pick_price_from_product\" >' + '" . __( 'Store Credit of ', WC_SC_TEXT_DOMAIN ) . "' + amount + '</li>');
												jQuery('div.gift-certificates').fadeTo( 100, 1 );
											};

											jQuery('input[name=variation_id]').on('change', function(){
												var variation;
                            					var variation_id = jQuery('input[name=variation_id]').val();
                            					if ( variation_id != '' && variation_id != undefined ) {
                            						if ( variation != '' && variation != undefined ) {
	                            						jQuery('form.variations_form.cart').one( 'found_variation', function( event, variation ) {
															if ( variation_id = variation.variation_id ) {
																reload_gift_certificate_div( variation );
															}
														});
                            						} else {
                            							var variations = jQuery('form.variations_form.cart').data('product_variations');
                            							jQuery.each( variations, function( index, value ){
                            								if ( variation_id == value.variation_id ) {
																reload_gift_certificate_div( value );
																return false;
                            								}
                            							});
                            						}

												}
											});

											setTimeout(function(){
												var default_variation_id = jQuery('input[name=variation_id]').val();
												if ( default_variation_id != '' && default_variation_id != undefined ) {
													jQuery('input[name=variation_id]').val( default_variation_id ).trigger( 'change' );
												}
											}, 10);

											jQuery('a.reset_variations').on('click', function(){
												jQuery('div.gift-certificates').find('li.pick_price_from_product').remove();
												jQuery('div.gift-certificates').hide();
											});";

									$amount = '';

								} else {

									$amount = ( $price > 0 ) ? __( 'Store Credit of ', WC_SC_TEXT_DOMAIN ) . wc_price( $price ) : '' ;

								}
							} else {
								$amount = ( ! empty( $coupon_amount ) ) ? __( 'Store Credit of ', WC_SC_TEXT_DOMAIN ) . wc_price( $coupon_amount ) : '';
							}

							break;

						case 'fixed_cart':
							$amount = wc_price( $coupon_amount ) . __( ' discount on your entire purchase', WC_SC_TEXT_DOMAIN );
							break;

						case 'fixed_product':
							$amount = wc_price( $coupon_amount ) . __( ' discount on product', WC_SC_TEXT_DOMAIN );
							break;

						case 'percent_product':
							$amount = $coupon_amount . '%' . __( ' discount on product', WC_SC_TEXT_DOMAIN );
							break;

						case 'percent':
							$amount = $coupon_amount . '%' . __( ' discount on your entire purchase', WC_SC_TEXT_DOMAIN );
							break;

						default:
							$default_coupon_type = ( ! empty( $all_discount_types[ $discount_type ] ) ) ? $all_discount_types[ $discount_type ] : ucwords( str_replace( array( '_', '-' ), ' ', $discount_type ) );
							$coupon_amount = apply_filters( 'wc_sc_coupon_amount', $coupon_amount, $coupon );
							$amount = sprintf(__( '%s coupon of %s', WC_SC_TEXT_DOMAIN ), $default_coupon_type, $coupon_amount );
							$amount = apply_filters( 'wc_sc_coupon_description', $amount, $coupon );
							break;

					}

					if ( $is_free_shipping == 'yes' && in_array( $discount_type, array( 'fixed_cart', 'fixed_product', 'percent_product', 'percent' ) ) ) {
						$amount = sprintf( __( '%s Free Shipping', WC_SC_TEXT_DOMAIN ), ( ( ! empty( $coupon_amount ) ) ? $amount . __( ' &', WC_SC_TEXT_DOMAIN ) : '' ) );
					}

					if ( ! empty( $amount ) ) { echo '<li>' . $amount . '</li>';
					}
				}

				if ( ! $list_started ) {
					echo '</ul></p></div>';
				}

				if ( ! empty( $js ) ) {
					wc_enqueue_js( $js );
				}
			}
		}

		/**
		 * Replace Add to cart button with Select Option button for products which are created for purchasing credit, on shop page
		 */
		public function remove_add_to_cart_button_from_shop_page() {
			global $product, $woocommerce;

			if ( $this->is_wc_gte_30() ) {
				$product_id = $product->get_id();
			} else {
				$product_id = ( ! empty( $product->id ) ) ? $product->id : 0;
			}

			$coupons = get_post_meta( $product_id, '_coupon_title', true );

			if ( ! empty( $coupons ) && $this->is_coupon_amount_pick_from_product_price( $coupons ) && ! ( $product->get_price() > 0 ) ) {

				$js = " jQuery('a[data-product_id=" . $product_id . "]').remove(); ";

				wc_enqueue_js( $js );

				?>
				<a href="<?php echo the_permalink(); ?>" class="button"><?php echo get_option( 'sc_gift_certificate_shop_loop_button_text', __( 'Select options', WC_SC_TEXT_DOMAIN ) ); ?></a>
				<?php
			}
		}

		/**
		 * Function to show available coupons after cart table
		 */
		public function show_available_coupons_after_cart_table() {

			$smart_coupon_cart_page_text = get_option( 'smart_coupon_cart_page_text' );
			$smart_coupon_cart_page_text = ( ! empty( $smart_coupon_cart_page_text ) ) ? $smart_coupon_cart_page_text : __( 'Available Coupons (Click on the coupon to use it)', WC_SC_TEXT_DOMAIN );
			$this->show_available_coupons( $smart_coupon_cart_page_text, 'cart' );

		}

		/**
		 * Function to display current balance associated with Gift Certificate
		 */
		public function show_smart_coupon_balance() {

			$smart_coupon_myaccount_page_text  = get_option( 'smart_coupon_myaccount_page_text' );
			$smart_coupons_myaccount_page_text = ( ! empty( $smart_coupon_myaccount_page_text ) ) ? $smart_coupon_myaccount_page_text: __( 'Available Store Credit / Coupons', WC_SC_TEXT_DOMAIN );
			$this->show_available_coupons( $smart_coupons_myaccount_page_text, 'myaccount' );

		}

		/**
		 * Display generated coupon's details on My Account page
		 */
		public function generated_coupon_details_before_my_account() {
			$show_coupon_received_on_my_account = get_option( 'show_coupon_received_on_my_account', 'no' );

			if ( is_user_logged_in() && $show_coupon_received_on_my_account == 'yes' ) {
				$user_id = get_current_user_id();
				$this->get_generated_coupon_data( '', $user_id, true, true );
			}
		}

		/**
		 * Add new query var.
		 *
		 * @param array $vars
		 * @return array
		 */
		public function sc_add_query_vars( $vars ) {

			$vars[] = self::$endpoint;
			return $vars;
		}

		/**
		 * Set endpoint title.
		 *
		 * @param string $title
		 * @return string
		 */
		public function sc_endpoint_title( $title ) {
			global $wp_query;

			$is_endpoint = isset( $wp_query->query_vars[ self::$endpoint ] );

			if ( $is_endpoint && ! is_admin() && is_main_query() && in_the_loop() && is_account_page() ) {
				// New page title.
				$title = __( 'Coupons', WC_SC_TEXT_DOMAIN );
				remove_filter( 'the_title', array( $this, 'sc_endpoint_title' ) );
			}

			return $title;
		}

		/**
		 * Insert the new endpoint into the My Account menu.
		 *
		 * @param array $items
		 * @return array
		 */
		public function sc_new_menu_items( $items ) {

			// Remove the menu items.
			if ( isset( $items['edit-address'] ) ) {
				$edit_address = $items['edit-address'];
				unset( $items['edit-address'] );
			}

			if ( isset( $items['payment-methods'] ) ) {
				$payment_methods = $items['payment-methods'];
				unset( $items['payment-methods'] );
			}

			if ( isset( $items['edit-account'] ) ) {
				$edit_account = $items['edit-account'];
				unset( $items['edit-account'] );
			}

			if ( isset( $items['customer-logout'] ) ) {
				$logout = $items['customer-logout'];
				unset( $items['customer-logout'] );
			}

			// Insert our custom endpoint.
			$items[ self::$endpoint ] = __( 'Coupons', WC_SC_TEXT_DOMAIN );

			// Insert back the items.
			if ( ! empty( $edit_address ) ) { $items['edit-address'] = $edit_address;
			}
			if ( ! empty( $payment_methods ) ) { $items['payment-methods'] = $payment_methods;
			}
			if ( ! empty( $edit_account ) ) { $items['edit-account'] = $edit_account;
			}
			if ( ! empty( $logout ) ) { $items['customer-logout'] = $logout;
			}

			return $items;
		}

		/**
		 * Endpoint HTML content.
		 * To show available coupons on My Account page
		 */
		public function sc_endpoint_content() {

			$coupons = $this->sc_get_available_coupons_list( array() );

			if ( empty( $coupons ) ) { return false;
			}

			$coupons_applied = WC()->cart->get_applied_coupons();

			$available_coupons_heading  = get_option( 'smart_coupon_myaccount_page_text' );
			$available_coupons_heading = ( ! empty( $available_coupons_heading ) ) ? $available_coupons_heading: __( 'Available Store Credit / Coupons', WC_SC_TEXT_DOMAIN );
			?>
			<h2><?php echo __( stripslashes( $available_coupons_heading ), WC_SC_TEXT_DOMAIN ); ?></h2>

			<div class="woocommerce-Message woocommerce-Message--info woocommerce-info" style="display:none;">
				<?php echo __( 'Sorry, No coupons available for you.', WC_SC_TEXT_DOMAIN ); ?>
			</div>

			<div id='sc_coupons_list'>
				<h5><?php echo __( 'Store Credits', WC_SC_TEXT_DOMAIN ); ?></h5>
				<div id="all_coupon_container">
					<?php

					$total_store_credit = 0;
					foreach ( $coupons as $code ) {
						if ( in_array( $code->post_title, $coupons_applied ) ) { continue;
						}

						$coupon = new WC_Coupon( $code->post_title );

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

						if ( ( empty( $coupon_amount ) || $coupon_amount == 0 ) && $is_free_shipping == 'no' && ! empty( $discount_type ) && $discount_type != 'free_gift' ) {
							continue;
						}

						if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
							$expiry_date = $expiry_date->getTimestamp();
						} elseif ( ! is_int( $expiry_date ) ) {
							$expiry_date = strtotime( $expiry_date );
						}

						if ( empty( $discount_type ) || ( ! empty( $expiry_date ) && current_time( 'timestamp' ) > $expiry_date ) ) {
							continue;
						}

						$coupon_post = get_post( $coupon_id );

						$coupon_data = $this->get_coupon_meta_data( $coupon );

						if ( $discount_type == 'smart_coupon' ) {
							$total_store_credit += $coupon_amount;

							echo '<div class="coupon-container apply_coupons_credits red medium" name="' . $coupon_code . '" style="cursor: pointer">
							<div class="coupon-content red dashed small" name="' . $coupon_code . '">
								<div class="discount-info" >';

							if ( ! empty( $coupon_data['coupon_amount'] ) && $coupon_amount != 0 ) {
								echo $coupon_data['coupon_amount'] . ' ' . $coupon_data['coupon_type'];
								if ( $is_free_shipping == 'yes' ) {
									echo __( ' &amp; ', WC_SC_TEXT_DOMAIN );
								}
							}

							if ( $is_free_shipping == 'yes' ) {
								echo __( 'Free Shipping', WC_SC_TEXT_DOMAIN );
							}
							echo '</div>';

							echo '<div class="code">' . $coupon_code . '</div>';

							$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
							if ( ! empty( $coupon_post->post_excerpt ) && $show_coupon_description == 'yes' ) {
								echo '<div class="discount-description">' . $coupon_post->post_excerpt . '</div>';
							}

							if ( ! empty( $expiry_date ) ) {

								$expiry_date = $this->get_expiration_format( $expiry_date );

								echo '<div class="coupon-expire">' . $expiry_date . '</div>';

							} else {

								echo '<div class="coupon-expire">' . __( 'Never Expires', WC_SC_TEXT_DOMAIN ) . '</div>';

							}

							echo '</div>
								</div>';
						}
					}
					?>
				</div>
				<?php
				if ( ! empty( $total_store_credit ) && $total_store_credit != 0 ) {
					?>
					<div class="wc_sc_total_available_store_credit"><?php echo sprintf( __( 'Total Credit Amount: %s', WC_SC_TEXT_DOMAIN ), wc_price( $total_store_credit ) ); ?></div>
					<?php
				}
				?>
			<br><hr />
			</div>
			<div id='coupons_list'>
				<h5><?php echo __( 'Discount Coupons', WC_SC_TEXT_DOMAIN ); ?></h5>
				<div id="all_coupon_container">
					<?php

					foreach ( $coupons as $code ) {

						if ( in_array( $code->post_title, $coupons_applied ) ) { continue;
						}

						$coupon = new WC_Coupon( $code->post_title );

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

						if ( ( empty( $coupon_amount ) || $coupon_amount == 0 ) && $is_free_shipping == 'no' && ! empty( $discount_type ) && $discount_type != 'free_gift' ) {
							continue;
						}

						if ( $this->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
							$expiry_date = $expiry_date->getTimestamp();
						} elseif ( ! is_int( $expiry_date ) ) {
							$expiry_date = strtotime( $expiry_date );
						}

						if ( empty( $discount_type ) || ( ! empty( $expiry_date ) && current_time( 'timestamp' ) > $expiry_date ) ) {
							continue;
						}

						$coupon_post = get_post( $coupon_id );

						$coupon_data = $this->get_coupon_meta_data( $coupon );

						if ( $discount_type != 'smart_coupon' ) {
							echo '<div class="coupon-container apply_coupons_credits blue medium" name="' . $coupon_code . '" style="cursor: pointer">
							<div class="coupon-content blue dashed small" name="' . $coupon_code . '">
								<div class="discount-info" >';

							if ( ! empty( $coupon_data['coupon_amount'] ) && $coupon_amount != 0 ) {
								echo $coupon_data['coupon_amount'] . ' ' . $coupon_data['coupon_type'];
								if ( $is_free_shipping == 'yes' ) {
									echo __( ' &amp; ', WC_SC_TEXT_DOMAIN );
								}
							}

							if ( $is_free_shipping == 'yes' ) {
								echo __( 'Free Shipping', WC_SC_TEXT_DOMAIN );
							}
							echo '</div>';

							echo '<div class="code">' . $coupon_code . '</div>';

							$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
							if ( ! empty( $coupon_post->post_excerpt ) && $show_coupon_description == 'yes' ) {
								echo '<div class="discount-description">' . $coupon_post->post_excerpt . '</div>';
							}

							if ( ! empty( $expiry_date ) ) {

								$expiry_date = $this->get_expiration_format( $expiry_date );

								echo '<div class="coupon-expire">' . $expiry_date . '</div>';

							} else {

								echo '<div class="coupon-expire">' . __( 'Never Expires', WC_SC_TEXT_DOMAIN ) . '</div>';

							}

							echo '</div>
								</div>';
						}
					}
				?>
				</div>
			</div>
			<?php
			// to show user specific coupons on My Account
			$this->generated_coupon_details_before_my_account();

			if ( did_action( 'wc_smart_coupons_frontend_styles_and_scripts' ) <= 0 || ! defined( 'DOING_AJAX' ) || DOING_AJAX !== true ) {
				$this->frontend_styles_and_scripts( array( 'page' => 'myaccount' ) );
			}

			$js = "var total_store_credit = '" . $total_store_credit . "';
					if ( total_store_credit == 0 ) {
						jQuery('#sc_coupons_list').hide();
					}

					jQuery( document ).ready(function() {
						if( jQuery('div#all_coupon_container').children().length == 0 ) {
							jQuery('#coupons_list').hide();
						}
					});

					jQuery( document ).ready(function() {
						if( jQuery('div.woocommerce-MyAccount-content').children().length == 0 ) {
							jQuery('.woocommerce-MyAccount-content').append(jQuery('.woocommerce-Message.woocommerce-Message--info.woocommerce-info'));
							jQuery('.woocommerce-Message.woocommerce-Message--info.woocommerce-info').show();
						}
					});

					/* to show scroll bar for core coupons */
					var coupons_list = jQuery('#coupons_list');
					var coupons_list_height = coupons_list.height();

					if ( coupons_list_height > 400 ) {
						coupons_list.css('height', '400px');
						coupons_list.css('overflow-y', 'scroll');
					} else {
						coupons_list.css('height', '');
						coupons_list.css('overflow-y', '');
					}
			";

			wc_enqueue_js( $js );

		}

		/**
		 * Function to get available coupons list
		 */
		public function sc_get_available_coupons_list( $coupons = array() ) {

			global $wpdb;

			$global_coupons = array();

			if ( get_option( 'woocommerce_smart_coupon_show_my_account' ) == 'no' ) { return false;
			}

			$global_coupons = $wpdb->get_results("SELECT * 
												FROM {$wpdb->prefix}posts
												WHERE FIND_IN_SET (ID, (SELECT GROUP_CONCAT(option_value SEPARATOR ',') FROM {$wpdb->prefix}options WHERE option_name = 'sc_display_global_coupons')) > 0
												GROUP BY ID 
												ORDER BY post_date DESC");

			$global_coupons = apply_filters( 'wc_smart_coupons_global_coupons', $global_coupons );

			if ( is_user_logged_in() ) {

				global $current_user;

				if ( ! empty( $current_user->user_email ) && ! empty( $current_user->ID ) ) {
					$count_option_current_user = $wpdb->get_col("SELECT option_name FROM {$wpdb->prefix}options
																WHERE option_name LIKE 'sc_display_custom_credit_" . $current_user->ID . "_%'
																ORDER BY option_id DESC");

					if ( count( $count_option_current_user ) > 0 ) {
						$count_option_current_user = substr( strrchr( $count_option_current_user[0], '_' ), 1 );
						$count_option_current_user = ( ! empty( $count_option_current_user ) ) ? $count_option_current_user + 2 : 1;
					} else {
						$count_option_current_user = 1;
					}

					$option_nm = 'sc_display_custom_credit_' . $current_user->ID . '_' . $count_option_current_user;
					$wpdb->query( 'SET SESSION group_concat_max_len=999999' );

					$wpdb->query("INSERT INTO {$wpdb->prefix}options (option_name, option_value, autoload)
									SELECT '" . $option_nm . "',
										GROUP_CONCAT(id SEPARATOR ','),
										'no'
									FROM {$wpdb->prefix}posts
									WHERE post_type = 'shop_coupon'
										AND post_status = 'publish'");

					$wpdb->query("UPDATE {$wpdb->prefix}options
									SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
														FROM {$wpdb->prefix}postmeta
														WHERE meta_key = 'customer_email'
															AND CAST(meta_value AS CHAR) LIKE '%" . $wpdb->esc_like( $current_user->user_email ) . "%'
															AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = '" . $option_nm . "') as temp )) > 0 )
									WHERE option_name = '" . $option_nm . "'");

					$wpdb->query("UPDATE {$wpdb->prefix}options
									SET option_value = (SELECT GROUP_CONCAT(post_id SEPARATOR ',')
														FROM {$wpdb->prefix}postmeta
														WHERE meta_key = 'coupon_amount'
															AND CAST(meta_value AS SIGNED) >= '0'
															AND FIND_IN_SET(post_id, (SELECT option_value FROM (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = '" . $option_nm . "') as temp )) > 0 )
									WHERE option_name = '" . $option_nm . "'");

					$coupons = $wpdb->get_results("SELECT * 
													FROM {$wpdb->prefix}posts
													WHERE FIND_IN_SET (ID, (SELECT option_value FROM {$wpdb->prefix}options WHERE option_name = '" . $option_nm . "')) > 0
													GROUP BY ID 
													ORDER BY post_date DESC");

					$wpdb->query( "DELETE FROM {$wpdb->prefix}options WHERE option_name = '" . $option_nm . "'" );
				}
			}

			$coupons = array_merge( $coupons, $global_coupons );

			return $coupons;

		}

		/**
		 * Include frontend styles & scripts
		 */
		public function frontend_styles_and_scripts( $args = array() ) {

			if ( empty( $args['page'] ) ) {
				return;
			}

			$js = " 	jQuery('div').on('click', '.apply_coupons_credits', function() {

							coupon_code = jQuery(this).find('div.code').text();

							if( coupon_code != '' && coupon_code != undefined ) {

								jQuery(this).addClass( 'smart-coupon-loading' );
								var url = '" . trailingslashit( home_url() ) . ( ( strpos( home_url(), '?' ) === false ) ? '?' : '&' ) . ( ( ! empty( $args['page'] ) ) ? 'sc-page=' . $args['page'] : '' ) . "&coupon-code='+coupon_code;
								jQuery(location).attr('href', url);

							}
						});

						var show_hide_coupon_list = function() {
							if( jQuery('div#coupons_list').find('div.coupon-container').length > 0 ) {
								jQuery('div#coupons_list').slideDown(800);
							} else {
								jQuery('div#coupons_list').hide();
							}
						};
						show_hide_coupon_list();

						var coupon_container_height = jQuery('#all_coupon_container').height();
						if ( coupon_container_height > 400 ) {
							jQuery('#all_coupon_container').css('height', '400px');
							jQuery('#all_coupon_container').css('overflow-y', 'scroll');
						} else {
							jQuery('#all_coupon_container').css('height', '');
							jQuery('#all_coupon_container').css('overflow-y', '');
						}

						jQuery('.checkout_coupon').next('#coupons_list').hide();

						jQuery('a.showcoupon').on('click', function() {
							jQuery('#coupons_list').slideToggle();
						});

					";

			if ( $this->is_wc_gte_26() ) {
				$js .= "
						jQuery(document.body).on('updated_cart_totals', function(){
							jQuery('div#coupons_list').css('opacity', '0.5');
							jQuery.ajax({
								url: '" . admin_url( 'admin-ajax.php' ) . "',
								type: 'post',
								dataType: 'html',
								data: {
									action: 'sc_get_available_coupons',
									security: '" . wp_create_nonce( 'sc-get-available-coupons' ) . "'
								},
								success: function( response ) {
									if ( response != undefined && response != '' ) {
										jQuery('div#coupons_list').replaceWith( response );
									}
									show_hide_coupon_list();
									jQuery('div#coupons_list').css('opacity', '1');
								}
							});
						});";
			} else {
				$js .= "
						jQuery('body').on( 'update_checkout', function( e ){
							var coupon_code = jQuery('.woocommerce-remove-coupon').data( 'coupon' );
							if ( coupon_code != undefined && coupon_code != '' ) {
								jQuery('div[name=\"'+coupon_code+'\"].apply_coupons_credits').show();
							}
						});";
			}

			wc_enqueue_js( $js );

			do_action( 'wc_smart_coupons_frontend_styles_and_scripts' );

		}

		/**
		 * Fetch generated coupon's details
		 *
		 * @param array|int $order_ids
		 * @param array|int $user_ids
		 * @param boolean   $html optional default:false whether to return only data or html code
		 * @param boolean   $header optional default:false whether to add a header above the list of generated coupon details
		 * @param string    $layout optional default:box Possible values 'box' or 'table' layout to show generated coupons details
		 *
		 *    Either order_ids or user_ids required
		 * @return array $generated_coupon_data associative array containing generated coupon's details
		 */
		public function get_generated_coupon_data( $order_ids = '', $user_ids = '', $html = false, $header = false, $layout = 'box' ) {
			global $wpdb, $woocommerce;

			if ( ! is_array( $order_ids ) ) {
				$order_ids = ( ! empty( $order_ids ) ) ? array( $order_ids ) : array();
			}

			if ( ! is_array( $user_ids ) ) {
				$user_ids = ( ! empty( $user_ids ) ) ? array( $user_ids ) : array();
			}

			$user_order_ids = array();

			if ( ! empty( $user_ids ) ) {

				$user_order_ids_query = "SELECT DISTINCT postmeta.post_id FROM {$wpdb->prefix}postmeta AS postmeta
												WHERE postmeta.meta_key = '_customer_user'
												AND postmeta.meta_value";

				if ( count( $user_ids ) == 1 ) {
					$user_order_ids_query .= ' = ' . current( $user_ids );
				} else {
					$user_order_ids_query .= ' IN ( ' . implode( ',', $user_ids ) . ' )';
				}

				$user_order_ids = $wpdb->get_col( $user_order_ids_query );

			}

			$new_order_ids = array_unique( array_merge( $user_order_ids, $order_ids ) );

			$generated_coupon_data = array();
			foreach ( $new_order_ids as $id ) {
				$data = get_post_meta( $id, 'sc_coupon_receiver_details', true );
				if ( empty( $data ) ) { continue;
				}
				$from = get_post_meta( $id, '_billing_email', true );
				if ( empty( $generated_coupon_data[ $from ] ) ) {
					$generated_coupon_data[ $from ] = array();
				}
				$generated_coupon_data[ $from ] = array_merge( $generated_coupon_data[ $from ], $data );
			}

			if ( empty( $generated_coupon_data ) ) {
				return;
			}

			if ( $html ) {

				ob_start();
				if ( $layout == 'table' ) {
					$this->get_generated_coupon_data_table( $generated_coupon_data );
				} else {
					$this->get_generated_coupon_data_box( $generated_coupon_data );
				}
				$coupon_details_html_content = ob_get_clean();

				$found_coupon = ( $layout == 'table' ) ? ( strpos( $coupon_details_html_content, 'coupon_received_row' ) !== false ) : ( strpos( $coupon_details_html_content, '<details' ) !== false );

				if ( $found_coupon ) {

					echo '<div id="generated_coupon_data_container" style="padding: 2em 0 2em;">';

					if ( $header ) {
						echo '<h2>' . __( 'Coupon Received', WC_SC_TEXT_DOMAIN ) . '</h2>';
					}

					echo $coupon_details_html_content;

					echo '</div>';

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
			if ( empty( $generated_coupon_data ) ) { return;
			}
			global $woocommerce;
			$email = $this->get_current_user_email();
			$js = "
					var switchMoreLess = function() {
						var total = jQuery('details').length;
						var open = jQuery('details[open]').length;
						if ( open == total ) {
							jQuery('a#more_less').text('" . __( 'Less details', WC_SC_TEXT_DOMAIN ) . "');
						} else {
							jQuery('a#more_less').text('" . __( 'More details', WC_SC_TEXT_DOMAIN ) . "');
						}
					};
					switchMoreLess();

					jQuery('a#more_less').on('click', function(){
						var current = jQuery('details').attr('open');
						if ( current == '' || current == undefined ) {
							jQuery('details').attr('open', 'open');
							jQuery('a#more_less').text('" . __( 'Less details', WC_SC_TEXT_DOMAIN ) . "');
						} else {
							jQuery('details').removeAttr('open');
							jQuery('a#more_less').text('" . __( 'More details', WC_SC_TEXT_DOMAIN ) . "');
						}
					});

					jQuery('summary.generated_coupon_summary').on('mouseup', function(){
						setTimeout( switchMoreLess, 10 );
					});

					jQuery('span.expand_collapse').show();

					var generated_coupon_element = jQuery('#all_generated_coupon');
					var generated_coupon_container_height = generated_coupon_element.height();
					if ( generated_coupon_container_height > 400 ) {
						generated_coupon_element.css('height', '400px');
						generated_coupon_element.css('overflow-y', 'scroll');
					} else {
						generated_coupon_element.css('height', '');
						generated_coupon_element.css('overflow-y', '');
					}

				";

			wc_enqueue_js( $js );

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
				.coupon-container.blue { background-color: #D7E9FC }
				.coupon-container.red { background-color: #FFE7E1 }
				.coupon-container.green { background-color: #DCFADC }
				.coupon-container.yellow { background-color: #F7F6D8 }

				.coupon-container.small {
					padding: .3em;
					line-height: 1.2em;
				}
				.coupon-container.medium {
					padding: .55em;
					line-height: 1.4em;
				}
				.coupon-container.large {
					padding: .6em;
					line-height: 1.6em;
				}

				.coupon-content.small { padding: .2em 1.2em }
				.coupon-content.medium { padding: .4em 1.4em }
				.coupon-content.large { padding: .6em 1.6em }

				.coupon-content.dashed { border: 1px dashed }
				.coupon-content.dotted { border: 2.3px dotted }
				.coupon-content.groove { border: 1px groove }
				.coupon-content.solid { border: 2.3px solid }
				.coupon-content.none { border: 2.3px none }

				.coupon-content.blue { border-color: rgba(0,0,0,.28) }
				.coupon-content.red { border-color: rgba(0,0,0,.28) }
				.coupon-content.green { border-color: rgba(0,0,0,.28) }
				.coupon-content.yellow { border-color: rgba(0,0,0,.28) }

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
				.coupon-content .discount-description {
				    font: .7em/1 Helvetica, Arial, sans-serif;
				    width: 250px;
				    margin: 10px inherit;
				    display: inline-block;
				}

				.generated_coupon_details { padding: 0.6em 1em 0.4em 1em; text-align: left; }
				.generated_coupon_data { border: solid 1px lightgrey; margin-bottom: 5px; margin-right: 5px; width: 50%; }
				.generated_coupon_details p { margin: 0; }
				span.expand_collapse { text-align: right; display: block; margin-bottom: 1em; cursor: pointer; }
				.float_right_block { float: right; }
				summary::-webkit-details-marker { display: none; }
				details[open] summary::-webkit-details-marker { display: none; }
			</style>
			<div class="generated_coupon_data_wrapper">
				<span class="expand_collapse" style="display: none;">
					<a id="more_less"><?php _e( 'More details', WC_SC_TEXT_DOMAIN ); ?></a>
				</span>
				<div id="all_generated_coupon">
				<?php
				foreach ( $generated_coupon_data as $from => $data ) {
					foreach ( $data as $coupon_data ) {
						
						if ( ! is_admin() && ! empty( $coupon_data['email'] ) && $coupon_data['email'] != $email ) { continue;
						}

						$coupon = new WC_Coupon( $coupon_data['code'] );

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

						if ( empty( $coupon_id ) || empty( $discount_type ) ) { continue;
						}

						$coupon_post = get_post( $coupon_id );

						$coupon_meta = $this->get_coupon_meta_data( $coupon );

						?>
						<div class="coupon-container blue medium">
							<details>
								<summary class="generated_coupon_summary">
									<?php
										echo '<div class="coupon-content blue dashed small">
												<div class="discount-info">';

									if ( ! empty( $coupon_meta['coupon_amount'] ) && $coupon_amount != 0 ) {
										echo $coupon_meta['coupon_amount'] . ' ' . $coupon_meta['coupon_type'];
										if ( $is_free_shipping == 'yes' ) {
											echo __( ' &amp; ', WC_SC_TEXT_DOMAIN );
										}
									}

									if ( $is_free_shipping == 'yes' ) {
										echo __( 'Free Shipping', WC_SC_TEXT_DOMAIN );
									}
										echo '</div>';

										echo '<div class="code">' . $coupon_code . '</div>';

										$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
									if ( ! empty( $coupon_post->post_excerpt ) && $show_coupon_description == 'yes' ) {
										echo '<div class="discount-description">' . $coupon_post->post_excerpt . '</div>';
									}

									if ( ! empty( $expiry_date ) ) {

										$expiry_date = $this->get_expiration_format( $expiry_date );

										echo '<div class="coupon-expire">' . $expiry_date . '</div>';
									} else {

										echo '<div class="coupon-expire">' . __( 'Never Expires ', WC_SC_TEXT_DOMAIN ) . '</div>';
									}

										echo '</div>';
									?>
									</summary>
									<div class="generated_coupon_details">
									<p><strong><?php _e( 'Sender', WC_SC_TEXT_DOMAIN ); ?>:</strong> <?php echo $from; ?></p>
										<p><strong><?php _e( 'Receiver', WC_SC_TEXT_DOMAIN ); ?>:</strong> <?php echo $coupon_data['email']; ?></p>
										<?php if ( ! empty( $coupon_data['message'] ) ) { ?>
											<p><strong><?php _e( 'Message', WC_SC_TEXT_DOMAIN ); ?>:</strong> <?php echo $coupon_data['message']; ?></p>
										<?php } ?>
									</div>
								</details>
							</div>
							<?php
					}
				}
				?>
				</div>
			</div>
			<?php
		}

		/**
		 * HTML code to display generated coupon's details is table layout
		 *
		 * @param array $generated_coupon_data associative array of generated coupon's details
		 */
		public function get_generated_coupon_data_table( $generated_coupon_data = array() ) {
			if ( empty( $generated_coupon_data ) ) { return;
			}
			$email = $this->get_current_user_email();
			?>
				<div class="woocommerce_order_items_wrapper">
					<table class="woocommerce_order_items">
						<thead>
							<tr>
								<th><?php _e( 'Code', WC_SC_TEXT_DOMAIN ); ?></th>
								<th><?php _e( 'Amount', WC_SC_TEXT_DOMAIN ); ?></th>
								<th><?php _e( 'Receiver', WC_SC_TEXT_DOMAIN ); ?></th>
								<th><?php _e( 'Message', WC_SC_TEXT_DOMAIN ); ?></th>
								<th><?php _e( 'Sender', WC_SC_TEXT_DOMAIN ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $generated_coupon_data as $from => $data ) {
								foreach ( $data as $coupon_data ) {
									if ( ! is_admin() && ! empty( $coupon_data['email'] ) && $coupon_data['email'] != $email ) { continue;
									}
									echo '<tr class="coupon_received_row">';
									echo '<td>' . $coupon_data['code'] . '</td>';
									echo '<td>' . wc_price( $coupon_data['amount'] ) . '</td>';
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
		 * Get current user's email
		 *
		 * @return string $email
		 */
		public function get_current_user_email() {
			$current_user = wp_get_current_user();
			if ( ! $current_user instanceof WP_User ) { return;
			}
			$billing_email = get_user_meta( $current_user->ID, 'billing_email', true );
			$email = ( ! empty( $billing_email ) ) ? $billing_email : $current_user->user_email;
			return $email;
		}

		/**
		 * Display generated coupons details after Order table
		 *
		 * @param mixed $order expecting WC_Order's object
		 */
		public function generated_coupon_details_after_order_table( $order = false, $sent_to_admin = false, $plain_text = false ) {

			if ( $this->is_wc_gte_30() ) {
				$order_id      = ( ! empty( $order ) && is_callable( array( $order, 'get_id' ) ) ) ? $order->get_id() : 0;
				$order_refunds = ( ! empty( $order ) && is_callable( array( $order, 'get_refunds' ) ) ) ? $order->get_refunds() : array();
			} else {
				$order_id      = ( ! empty( $order->id ) ) ? $order->id : 0;
				$order_refunds = ( ! empty( $order->refunds ) ) ? $order->refunds : array();
			}

			if ( ! empty( $order_refunds ) ) { return;
			}

			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, true );
			}
		}

		/**
		 * Display generated coupons details on View Order page
		 *
		 * @param int $order_id
		 */
		public function generated_coupon_details_view_order( $order_id = 0 ) {
			if ( ! empty( $order_id ) ) {
				$this->get_generated_coupon_data( $order_id, '', true, true );
			}
		}

		/**
		 * Metabox on Order Edit Admin page to show generated coupons during the order
		 */
		public function add_generated_coupon_details() {
			global $post;

			if ( $post->post_type !== 'shop_order' ) { return;
			}

			add_meta_box( 'sc-generated-coupon-data', __( 'Coupon Sent', WC_SC_TEXT_DOMAIN ), array( $this, 'sc_generated_coupon_data_metabox' ), 'shop_order', 'normal' );
		}

		/**
		 * Metabox content (Generated coupon's details)
		 */
		public function sc_generated_coupon_data_metabox() {
			global $post;
			if ( ! empty( $post->ID ) ) {
				$this->get_generated_coupon_data( $post->ID, '', true, false );
			}
		}

		

	}

}

WC_SC_Display_Coupons::get_instance();
