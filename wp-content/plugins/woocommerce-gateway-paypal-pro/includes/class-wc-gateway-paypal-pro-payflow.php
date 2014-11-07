<?php

/**
 * WC_Gateway_PayPal_Pro_PayFlow class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_PayPal_Pro_PayFlow extends WC_Payment_Gateway {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->id					= 'paypal_pro_payflow';
		$this->method_title 		= __( 'PayPal Pro PayFlow', 'woocommerce-gateway-paypal-pro' );
		$this->method_description 	= __( 'PayPal Pro PayFlow Edition works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'woocommerce-gateway-paypal-pro' );
		$this->icon 				= WP_PLUGIN_URL . "/" . plugin_basename( dirname( dirname( __FILE__ ) ) ) . '/assets/images/cards.png';
		$this->has_fields 			= true;
		$this->liveurl				= 'https://payflowpro.paypal.com';
		$this->testurl				= 'https://pilot-payflowpro.paypal.com';
		$this->allowed_currencies   = apply_filters( 'woocommerce_paypal_pro_allowed_currencies', array( 'USD', 'EUR', 'GBP', 'CAD', 'JPY', 'AUD' ) );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values
		$this->title                = $this->get_option( 'title' );
		$this->description          = $this->get_option( 'description' );
		$this->enabled              = $this->get_option( 'enabled' );
		$this->paypal_vendor        = $this->get_option( 'paypal_vendor' );
		$this->paypal_partner       = $this->get_option( 'paypal_partner', 'PayPal' );
		$this->paypal_password      = $this->get_option( 'paypal_password' );
		$this->paypal_user          = $this->get_option( 'paypal_user', $this->paypal_vendor );
		$this->testmode             = $this->get_option( 'testmode' ) === "yes" ? true : false;
		$this->transparent_redirect = $this->get_option( 'transparent_redirect' ) === "yes" ? true : false;
		$this->soft_descriptor      = str_replace( ' ', '-', preg_replace('/[^A-Za-z0-9\-\.]/', '', $this->get_option( 'soft_descriptor', "" ) ) );

		if ( $this->transparent_redirect ) {
			$this->order_button_text    = __( 'Enter payment details', 'woocommerce-gateway-paypal-pro' );
		}

		// Actions
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_receipt_paypal_pro_payflow', array( $this, 'receipt_page' ) );
		add_action( 'woocommerce_api_wc_gateway_paypal_pro_payflow', array( $this, 'return_handler' ) );
	}

	/**
     * Initialise Gateway Settings Form Fields
     */
	public function init_form_fields() {
    	$this->form_fields = array(
			'enabled'         => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Pro Payflow Edition', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title'           => array(
				'title'       => __( 'Title', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Credit card (PayPal)', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true
			),
			'description'     => array(
				'title'       => __( 'Description', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Pay with your credit card.', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true
			),
			'soft_descriptor' => array(
				'title'             => __( 'Soft Descriptor', 'woocommerce-gateway-paypal-pro' ),
				'type'              => 'text',
				'description'       => __( '(Optional) Information that is usually displayed in the account holder\'s statement, for example your website name. Only 23 alphanumeric characters can be included, including the special characters dash (-) and dot (.) . Asterisks (*) and spaces ( ) are NOT permitted.', 'woocommerce-gateway-paypal-pro' ),
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'maxlength' => 23,
					'pattern' => '[a-zA-Z0-9.-]+'
				)
			),
			'testmode'        => array(
				'title'       => __( 'Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Sandbox/Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in development mode.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'transparent_redirect' => array(
				'title'       => __( 'Transparent Redirect', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable Transparent Redirect', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Rather than showing a credit card form on your checkout, this shows hte form on it\'s own page and posts straight to PayPal, thus making the process more secure and more PCI friendly. "Enable Secure Token" needs to be enabled on your PayFlow account to work.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'paypal_vendor'   => array(
				'title'       => __( 'PayPal Vendor', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Your merchant login ID that you created when you registered for the account.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_password' => array(
				'title'       => __( 'PayPal Password', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'password',
				'description' => __( 'The password that you defined while registering for the account.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_user'     => array(
				'title'       => __( 'PayPal User', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'If you set up one or more additional users on the account, this value is the ID
			of the user authorized to process transactions. Otherwise, leave this field blank.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_partner'  => array(
				'title'       => __( 'PayPal Partner', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'The ID provided to you by the authorized PayPal Reseller who registered you
			for the Payflow SDK. If you purchased your account directly from PayPal, use PayPal or leave blank.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'PayPal',
				'desc_tip'    => true
			),
		);
    }

	/**
     * Check if this gateway is enabled and available in the user's country
     *
     * This method no is used anywhere??? put above but need a fix below
     */
	public function is_available() {
		if ( $this->enabled === "yes" ) {

			if ( ! is_ssl() && ! $this->testmode ) {
				return false;
			}

			// Currency check
			if ( ! in_array( get_option('woocommerce_currency'), $this->allowed_currencies ) ) {
				return false;
			}

			// Required fields check
			if ( ! $this->paypal_vendor || ! $this->paypal_password ) {
				return false;
			}

			return true;
		}
		return false;
	}

	/**
     * Process the payment
     */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		if ( $this->transparent_redirect ) {

			return array(
				'result' 	=> 'success',
				'redirect'	=> $order->get_checkout_payment_url( true )
			);

		} else {
			$card_number    = isset( $_POST['paypal_pro_payflow-card-number'] ) ? wc_clean( $_POST['paypal_pro_payflow-card-number'] ) : '';
			$card_cvc       = isset( $_POST['paypal_pro_payflow-card-cvc'] ) ? wc_clean( $_POST['paypal_pro_payflow-card-cvc'] ) : '';
			$card_expiry    = isset( $_POST['paypal_pro_payflow-card-expiry'] ) ? wc_clean( $_POST['paypal_pro_payflow-card-expiry'] ) : '';

			// Format values
			$card_number    = str_replace( array( ' ', '-' ), '', $card_number );
			$card_expiry    = array_map( 'trim', explode( '/', $card_expiry ) );
			$card_exp_month = str_pad( $card_expiry[0], 2, "0", STR_PAD_LEFT );
			$card_exp_year  = $card_expiry[1];

			if ( strlen( $card_exp_year ) == 4 ) {
				$card_exp_year = $card_exp_year - 2000;
			}

			// Do payment with paypal
			return $this->do_payment( $order, $card_number, $card_exp_month . $card_exp_year, $card_cvc );
		}
	}

	/**
	 * Receipt_page for showing the payment form which sends data to authorize.net
	 */
	public function receipt_page( $order_id ) {
		if ( $this->transparent_redirect ) {
			// Get the order
			$order     = new WC_Order( $order_id );
			$url       = $this->testmode ? 'https://pilot-payflowlink.paypal.com' : 'https://payflowlink.paypal.com';
			$post_data = $this->get_post_data( $order );

			// Request token
			$token     = $this->get_token( $order, $post_data );

			if ( ! $token ) {
				wc_print_notices();
				return;
			}

			echo wpautop( __( 'Enter your payment details below and click "Confirm and pay" to securely pay for your order.', 'woocommerce-gateway-paypal-pro' ) );
			?>
			<form method="POST" action="<?php echo $url; ?>">
				<div>
					<?php $this->credit_card_form( array( 'fields_have_names' => false ) ); ?>
				   	<input type="hidden" name="CARDNUM" autocomplete="off" />
				   	<input type="hidden" name="EXPMONTH" autocomplete="off" />
				   	<input type="hidden" name="EXPYEAR" autocomplete="off" />
				   	<input type="hidden" name="CVV2" autocomplete="off" />
					<input type="hidden" name="SECURETOKEN" value="<?php echo esc_attr( $token['SECURETOKEN'] ); ?>" />
					<input type="hidden" name="SECURETOKENID" value="<?php echo esc_attr( $token['SECURETOKENID'] ); ?>" />
					<input type="submit" value="<?php _e( 'Confirm and pay', 'woocommerce-gateway-paypal-pro' ); ?>" class="submit buy button" />
				</div>
				<script type="text/javascript">
					jQuery(function(){
						jQuery('input#paypal_pro_payflow-card-number').change(function(){
							jQuery('input[name=CARDNUM]').val( jQuery('input#paypal_pro_payflow-card-number').val().replace(/ /g,'') );
						});
						jQuery('input#paypal_pro_payflow-card-cvc').change(function(){
							jQuery('input[name=CVV2]').val( jQuery('input#paypal_pro_payflow-card-cvc').val() );
						});
						jQuery('input#paypal_pro_payflow-card-expiry').change(function(){
							var expires = jQuery('input#paypal_pro_payflow-card-expiry').payment('cardExpiryVal');

							var month = expires['month'];
							if ( month < 10 ) {
								month = '0' + month;
							}

							jQuery('input[name=EXPMONTH]').val( month );
							jQuery('input[name=EXPYEAR]').val( ( expires['year'] - 2000 ) );
						});
					});
				</script>
			</form>
			<?php
		}
	}

	/**
	 * handles return data and does redirects
	 */
	public function return_handler() {
		// Clean
		@ob_clean();

		// Header
		header('HTTP/1.1 200 OK');

		$result   = isset( $_POST['RESULT'] ) ? absint( $_POST['RESULT'] ) : null;
		$order_id = isset( $_POST['INVOICE'] ) ? $_POST['INVOICE'] : 0;

		if ( is_null( $result ) || empty( $order_id ) ) {
			echo "Invalid request.";
			exit;
		}

		// Get the order
		$order = new WC_Order( $order_id );

		switch ( $result ) {
			// Approved or screening service was down
			case 0 :
			case 127 :
				// Verify
				$paypal_args = array(
					'USER'         => $this->paypal_user,
					'VENDOR'       => $this->paypal_vendor,
					'PARTNER'      => $this->paypal_partner,
					'PWD'          => trim( $this->paypal_password ),
					'ORIGID'       => $_POST['PNREF'],
					'TENDER'       => 'C',
					'TRXTYPE'      => 'I',
					'BUTTONSOURCE' => 'WooThemes_Cart'
				);

				$response = wp_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
					'method'      => 'POST',
					'body'        => urldecode( http_build_query( $paypal_args, null, '&' ) ),
					'timeout'     => 70,
					'sslverify'   => false,
					'user-agent'  => 'WooCommerce',
					'httpversion' => '1.1'
				));

				if ( is_wp_error( $response ) ) {
					throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
				}

				if ( empty( $response['body'] ) ) {
					throw new Exception( __( 'Empty Paypal response.', 'woocommerce-gateway-paypal-pro' ) );
				}

				parse_str( $response['body'], $parsed_response );

				if ( $parsed_response['result'] == 0 ) {
					$order->payment_complete();
					$order->add_order_note( sprintf( __( 'PayPal Pro payment completed (PNREF: %s)', 'woocommerce-gateway-paypal-pro' ), $_POST['PNREF'] ) );
				} else {
					$order->update_status( 'on-hold', sprintf( __( 'The payment could not be verified (PNREF: %s). Please check your PayPal Manager account to review the payment and then mark this order "processing" or "cancelled".', 'woocommerce-gateway-paypal-pro' ), $_POST['PNREF'] ) );
				}

				WC()->cart->empty_cart();
				$redirect = $order->get_checkout_order_received_url();
			break;
			// Under Review by Fraud Service
			case 126 :
				$order->add_order_note( $_POST['RESPMSG'] );
				$order->add_order_note( $_POST['PREFPSMSG'] );
				$order->update_status( 'on-hold', __( 'The payment was flagged by a fraud filter. Please check your PayPal Manager account to review and accept or deny the payment and then mark this order "processing" or "cancelled".', 'woocommerce-gateway-paypal-pro' ) );
				WC()->cart->empty_cart();
				$redirect = $order->get_checkout_order_received_url();
			break;
			default :
				// Mark failed
				$order->update_status( 'failed', $_POST['RESPMSG'] );

				$redirect = $order->get_checkout_payment_url( true );
				$redirect = add_query_arg( 'wc_error', urlencode( wp_kses_post( $_POST['RESPMSG'] ) ), $redirect );

				if ( is_ssl() || get_option( 'woocommerce_force_ssl_checkout' ) == 'yes' ) {
					$redirect = str_replace( 'http:', 'https:', $redirect );
				}
			break;
		}

		wp_redirect( $redirect );
		exit;
	}

	/**
	 * Get a token for transparent redirect
	 * @param  object $order
	 * @param  array $post_data
	 * @return bool or array
	 */
	public function get_token( $order, $post_data, $force_new_token = false ) {
		if ( ! $force_new_token && get_post_meta( $order->id, '_SECURETOKENHASH', true ) == md5( json_encode( $post_data ) ) ) {
			return array(
				'SECURETOKEN'   => get_post_meta( $order->id, '_SECURETOKEN', true ),
				'SECURETOKENID' => get_post_meta( $order->id, '_SECURETOKENID', true )
			);
		}
		$post_data['SECURETOKENID']     = uniqid() . md5( $order->order_key );
		$post_data['CREATESECURETOKEN'] = 'Y';
		$post_data['SILENTTRAN']        = 'TRUE';
		$post_data['ERRORURL']          = WC()->api_request_url( get_class() );
		$post_data['RETURNURL']         = WC()->api_request_url( get_class() );
		$post_data['URLMETHOD']         = 'POST';

		$response = wp_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
			'method'      => 'POST',
			'body'        => urldecode( http_build_query( apply_filters( 'woocommerce-gateway-paypal-pro_payflow_request', $post_data, $order ), null, '&' ) ),
			'timeout'     => 70,
			'sslverify'   => false,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1'
		));

		if ( is_wp_error( $response ) ) {
			wc_add_notice( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
			return false;
		}

		if ( empty( $response['body'] ) ) {
			wc_add_notice( __( 'Empty Paypal response.', 'woocommerce-gateway-paypal-pro' ) );
			return false;
		}

		parse_str( $response['body'], $parsed_response );

		if ( isset( $parsed_response['RESULT'] ) && in_array( $parsed_response['RESULT'], array( 160, 161, 162 ) ) ) {
			return $this->get_token( $order, $post_data, $force_new_token );
		} elseif ( isset( $parsed_response['RESULT'] ) && $parsed_response['RESULT'] == 0 && ! empty( $parsed_response['SECURETOKEN'] ) ) {
			update_post_meta( $order->id, '_SECURETOKEN', $parsed_response['SECURETOKEN'] );
			update_post_meta( $order->id, '_SECURETOKENID', $parsed_response['SECURETOKENID'] );
			update_post_meta( $order->id, '_SECURETOKENHASH', md5( json_encode( $post_data ) ) );

			return array(
				'SECURETOKEN'   => $parsed_response['SECURETOKEN'],
				'SECURETOKENID' => $parsed_response['SECURETOKENID']
			);
		} else {
			$order->update_status( 'failed', __( 'PayPal Pro token generation failed: ', 'woocommerce-gateway-paypal-pro' ) . '(' . $parsed_response['RESULT'] . ') ' . '"' . $parsed_response['RESPMSG'] . '"' );

			wc_add_notice( __( 'Payment error:', 'woocommerce-gateway-paypal-pro' ) . ' ' . $parsed_response['RESPMSG'], 'error' );

			return false;
		}
	}

	/**
	 * Get a list of parameters to send to paypal
	 * @param object $order
	 * @return array
	 */
	public function get_post_data( $order ) {
		$post_data                 = array();
		$post_data['USER']         = $this->paypal_user;
		$post_data['VENDOR']       = $this->paypal_vendor;
		$post_data['PARTNER']      = $this->paypal_partner;
		$post_data['PWD']          = trim( $this->paypal_password );
		$post_data['TENDER']       = 'C'; // Credit card
		$post_data['TRXTYPE']      = 'S'; // Sale
		$post_data['AMT']          = $order->get_total(); // Order total
		$post_data['CURRENCY']     = get_option('woocommerce_currency'); // Currency code
		$post_data['CUSTIP']       = $this->get_user_ip(); // User IP Address
		$post_data['EMAIL']        = $order->billing_email;
		$post_data['INVNUM']       = $order->id;
		$post_data['BUTTONSOURCE'] = 'WooThemes_Cart';

		if ( $this->soft_descriptor ) {
			$post_data['MERCHDESCR'] = $this->soft_descriptor;
		}

		/* Send Item details */
		$item_loop = 0;

		if ( sizeof( $order->get_items() ) > 0 ) {

			$ITEMAMT = 0;

			foreach ( $order->get_items() as $item ) {
				$_product = $order->get_product_from_item( $item );
				if ( $item['qty'] ) {
					$post_data[ 'L_NAME' . $item_loop ] = $item['name'];
					$post_data[ 'L_COST' . $item_loop ] = $order->get_item_total( $item, true );
					$post_data[ 'L_QTY' . $item_loop ]  = $item['qty'];

					if ( $_product->get_sku() ) {
						$post_data[ 'L_SKU' . $item_loop ] = $_product->get_sku();
					}

					$ITEMAMT += $order->get_item_total( $item, true ) * $item['qty'];

					$item_loop++;
				}
			}

			// Shipping
			if ( ( $order->get_total_shipping() + $order->get_shipping_tax() ) > 0 ) {
				$post_data[ 'L_NAME' . $item_loop ] = 'Shipping';
				$post_data[ 'L_DESC' . $item_loop ] = 'Shipping and shipping taxes';
				$post_data[ 'L_COST' . $item_loop ] = $order->get_total_shipping() + $order->get_shipping_tax();
				$post_data[ 'L_QTY' . $item_loop ]  = 1;

				$ITEMAMT += $order->get_total_shipping() + $order->get_shipping_tax();

				$item_loop++;
			}

			// Discount
			if ( $order->get_order_discount() > 0 ) {
				$post_data[ 'L_NAME' . $item_loop ] = 'Order Discount';
				$post_data[ 'L_DESC' . $item_loop ] = 'Discounts after tax';
				$post_data[ 'L_COST' . $item_loop ] = '-' . $order->get_order_discount();
				$post_data[ 'L_QTY' . $item_loop ]  = 1;

				$item_loop++;
			}

			$ITEMAMT = round( $ITEMAMT, 2 );

			// Fix rounding
			if ( absint( $order->get_total() * 100 ) !== absint( $ITEMAMT * 100 ) ) {
				$post_data[ 'L_NAME' . $item_loop ] = 'Rounding amendment';
				$post_data[ 'L_DESC' . $item_loop ] = 'Correction if rounding is off (this can happen with tax inclusive prices)';
				$post_data[ 'L_COST' . $item_loop ] = ( absint( $order->get_total() * 100 ) - absint( $ITEMAMT * 100 ) ) / 100;
				$post_data[ 'L_QTY' . $item_loop ]  = 1;
			}

			$post_data[ 'ITEMAMT' ] = $order->get_total();
		}

		$post_data['ORDERDESC']      = 'Order ' . $order->get_order_number() . ' on ' . get_bloginfo( 'name' );
		$post_data['FIRSTNAME']      = $order->billing_first_name;
		$post_data['LASTNAME']       = $order->billing_last_name;
		$post_data['STREET']         = $order->billing_address_1 . ' ' . $order->billing_address_2;
		$post_data['CITY']           = $order->billing_city;
		$post_data['STATE']          = $order->billing_state;
		$post_data['COUNTRY']        = $order->billing_country;
		$post_data['ZIP']            = $order->billing_postcode;

		if ( $order->shipping_address_1 ) {
			$post_data['SHIPTOFIRSTNAME'] = $order->shipping_first_name;
			$post_data['SHIPTOLASTNAME']  = $order->shipping_last_name;
			$post_data['SHIPTOSTREET']    = $order->shipping_address_1;
			$post_data['SHIPTOCITY']      = $order->shipping_city;
			$post_data['SHIPTOSTATE']     = $order->shipping_state;
			$post_data['SHIPTOCOUNTRY']   = $order->shipping_country;
			$post_data['SHIPTOZIP']       = $order->shipping_postcode;
		}

		return $post_data;
	}

	/**
	 * do_payment function.
	 *
	 * @param object $order
	 * @param string $card_number
	 * @param string $card_exp
	 * @param string $card_cvc
	 */
	public function do_payment( $order, $card_number, $card_exp, $card_cvc ) {

		// Send request to paypal
		try {
			$url                  = $this->testmode ? $this->testurl : $this->liveurl;
			$post_data            = $this->get_post_data( $order );
			$post_data['ACCT']    = $card_number; // Credit Card
			$post_data['EXPDATE'] = $card_exp; //MMYY
			$post_data['CVV2']    = $card_cvc; // CVV code

			$response = wp_remote_post( $url, array(
				'method'      => 'POST',
				'body'        => urldecode( http_build_query( apply_filters( 'woocommerce-gateway-paypal-pro_payflow_request', $post_data, $order ), null, '&' ) ),
				'timeout'     => 70,
				'sslverify'   => false,
				'user-agent'  => 'WooCommerce',
				'httpversion' => '1.1'
			));

			if ( is_wp_error( $response ) ) {
				throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
			}

			if ( empty( $response['body'] ) ) {
				throw new Exception( __( 'Empty Paypal response.', 'woocommerce-gateway-paypal-pro' ) );
			}

			parse_str( $response['body'], $parsed_response );

			if ( isset( $parsed_response['RESULT'] ) && in_array( $parsed_response['RESULT'], array( 0, 126, 127 ) ) ) {

				switch ( $parsed_response['RESULT'] ) {
					// Approved or screening service was down
					case 0 :
					case 127 :
						$order->add_order_note( sprintf( __( 'PayPal Pro payment completed (PNREF: %s)', 'woocommerce-gateway-paypal-pro' ), $parsed_response['PNREF'] ) );

						// Payment complete
						$order->payment_complete();
					break;
					// Under Review by Fraud Service
					case 126 :
						$order->add_order_note( $parsed_response['RESPMSG'] );
						$order->add_order_note( $parsed_response['PREFPSMSG'] );
						$order->update_status( 'on-hold', __( 'The payment was flagged by a fraud filter. Please check your PayPal Manager account to review and accept or deny the payment and then mark this order "processing" or "cancelled".', 'woocommerce-gateway-paypal-pro' ) );
					break;
				}

				// Remove cart
				WC()->cart->empty_cart();

				$redirect = $order->get_checkout_order_received_url();

				// Return thank you page redirect
				return array(
					'result' 	=> 'success',
					'redirect'	=> $redirect
				);

			} else {

				// Payment failed :(
				$order->update_status( 'failed', __( 'PayPal Pro payment failed. Payment was rejected due to an error: ', 'woocommerce-gateway-paypal-pro' ) . '(' . $parsed_response['RESULT'] . ') ' . '"' . $parsed_response['RESPMSG'] . '"' );

				wc_add_notice( __( 'Payment error:', 'woocommerce-gateway-paypal-pro' ) . ' ' . $parsed_response['RESPMSG'], 'error' );
				return;
			}

		} catch( Exception $e ) {
			wc_add_notice( __('Connection error:', 'woocommerce-gateway-paypal-pro' ) . ': "' . $e->getMessage() . '"', 'error' );
			return;
		}
	}

	/**
     * Payment form on checkout page
     */
	public function payment_fields() {
		if ( $this->description ) {
			echo '<p>' . $this->description . ( $this->testmode ? ' ' . __('TEST MODE/SANDBOX ENABLED', 'woocommerce-gateway-paypal-pro') : '' ) . '</p>';
		}
		if ( ! $this->transparent_redirect ) {
			$this->credit_card_form();
		}
	}

	/**
     * Get user's IP address
     */
	public function get_user_ip() {
		return ! empty( $_SERVER['HTTP_X_FORWARD_FOR'] ) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
	}
}