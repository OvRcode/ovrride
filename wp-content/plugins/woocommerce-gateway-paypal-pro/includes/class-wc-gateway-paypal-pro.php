<?php
/**
 * WC_Gateway_PayPal_Pro class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_PayPal_Pro extends WC_Payment_Gateway {

	/**
	 * Constuctor
	 */
	public function __construct() {
		$this->id                   = 'paypal_pro';
		$this->api_version          = '119';
		$this->method_title         = __( 'PayPal Pro', 'woocommerce-gateway-paypal-pro' );
		$this->method_description   = __( 'PayPal Pro works by adding credit card fields on the checkout and then sending the details to PayPal for verification.', 'woocommerce-gateway-paypal-pro' );
		$this->icon                 = apply_filters('woocommerce_paypal_pro_icon', plugins_url( '/assets/images/cards.png', plugin_basename( dirname( __FILE__ ) ) ) );
		$this->has_fields           = true;
		$this->supports             = array(
			'products',
			'refunds'
		);
		$this->liveurl               = 'https://api-3t.paypal.com/nvp';
		$this->testurl               = 'https://api-3t.sandbox.paypal.com/nvp';
		$this->liveurl_3ds           = 'https://paypal.cardinalcommerce.com/maps/txns.asp';
		$this->testurl_3ds           = 'https://centineltest.cardinalcommerce.com/maps/txns.asp';
		$this->available_card_types  = apply_filters( 'woocommerce_paypal_pro_available_card_types', array(
			'GB' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard',
				'Maestro'       => 'Maestro/Switch',
				'Solo'          => 'Solo'
			),
			'US' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard',
				'Discover'      => 'Discover',
				'AmEx'          => 'American Express'
			),
			'CA' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard'
			),
			'AU' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard'
			),
			'JP' => array(
				'Visa'          => 'Visa',
				'MasterCard'    => 'MasterCard',
				'JCB'           => 'JCB'
			)
		) );
		// this redundant filter is target previous typo'd filter name
		$this->available_card_types = apply_filters( 'woocommerce_paypal_pro_avaiable_card_types', $this->available_card_types );

		$this->iso4217 = apply_filters( 'woocommerce_paypal_pro_iso_currencies', array(
			'AUD' => '036',
			'CAD' => '124',
			'CZK' => '203',
			'DKK' => '208',
			'EUR' => '978',
			'HUF' => '348',
			'JPY' => '392',
			'NOK' => '578',
			'NZD' => '554',
			'PLN' => '985',
			'GBP' => '826',
			'SGD' => '702',
			'SEK' => '752',
			'CHF' => '756',
			'USD' => '840'
		) );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Get setting values
		$this->title           = $this->get_option( 'title' );
		$this->description     = $this->get_option( 'description' );
		$this->enabled         = $this->get_option( 'enabled' );
		$this->api_username    = $this->get_option( 'api_username' );
		$this->api_password    = $this->get_option( 'api_password' );
		$this->api_signature   = $this->get_option( 'api_signature' );
		$this->testmode        = $this->get_option( 'testmode', "no" ) === "yes" ? true : false;
		$this->enable_3dsecure = $this->get_option( 'enable_3dsecure', "no" ) === "yes" ? true : false;
		$this->liability_shift = $this->get_option( 'liability_shift', "no" ) === "yes" ? true : false;
		$this->debug           = $this->get_option( 'debug', "no" ) === "yes" ? true : false;
		$this->send_items      = $this->get_option( 'send_items', "no" ) === "yes" ? true : false;
		$this->soft_descriptor = str_replace( ' ', '-', preg_replace('/[^A-Za-z0-9\-\.]/', '', $this->get_option( 'soft_descriptor', "" ) ) );
		$this->paymentaction   = $this->get_option( 'paypal_pro_paymentaction', 'sale' );

		// 3DS
		if ( $this->enable_3dsecure ) {
			$this->centinel_pid = $this->get_option( 'centinel_pid' );
			$this->centinel_mid = $this->get_option( 'centinel_mid' );
			$this->centinel_pwd = $this->get_option( 'centinel_pwd' );

			if ( empty( $this->centinel_pid ) || empty( $this->centinel_mid ) || empty( $this->centinel_pwd ) ) {
				$this->enable_3dsecure = false;
			}

			$this->centinel_url = $this->testmode ? $this->testurl_3ds : $this->liveurl_3ds;
		}

		// Maestro
		if ( ! $this->enable_3dsecure ) {
			unset( $this->available_card_types['GB']['Maestro'] );
		}

		// Hooks
		add_action( 'woocommerce_api_wc_gateway_paypal_pro', array( $this, 'authorise_3dsecure') );
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
	}

	/**
     * Initialise Gateway Settings Form Fields
     */
    public function init_form_fields() {
    	$this->form_fields = array(
			'enabled' => array(
				'title'       => __( 'Enable/Disable', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Pro', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => '',
				'default'     => 'no'
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Credit card (PayPal)', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => __( 'Pay with your credit card via PayPal Website Payments Pro.', 'woocommerce-gateway-paypal-pro' ),
				'desc_tip'    => true
			),
			'testmode' => array(
				'title'       => __( 'Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable PayPal Sandbox/Test Mode', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Place the payment gateway in development mode.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'api_username' => array(
				'title'       => __( 'API Username', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'api_password' => array(
				'title'       => __( 'API Password', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'api_signature' => array(
				'title'       => __( 'API Signature', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'Get your API credentials from PayPal.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'paypal_pro_paymentaction' => array(
				'title'       => __( 'Payment Action', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'select',
				'description' => __( 'Choose whether you wish to capture funds immediately or authorize payment only.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'sale',
				'desc_tip'    => true,
				'options'     => array(
					'sale'          => __( 'Capture', 'woocommerce-gateway-paypal-pro' ),
					'authorization' => __( 'Authorize', 'woocommerce-gateway-paypal-pro' )
				)
			),
			'enable_3dsecure' => array(
				'title'       => __( '3DSecure', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Enable 3DSecure', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Allows UK merchants to pass 3-D Secure authentication data to PayPal for debit and credit cards. Updating your site with 3-D Secure enables your participation in the Verified by Visa and MasterCard SecureCode programs. (Required to accept Maestro)', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'centinel_pid' => array(
				'title'       => __( 'Centinel PID', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'If enabling 3D Secure, enter your Cardinal Centinel Processor ID.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'centinel_mid' => array(
				'title'       => __( 'Centinel MID', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'text',
				'description' => __( 'If enabling 3D Secure, enter your Cardinal Centinel Merchant ID.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'centinel_pwd' => array(
				'title'       => __( 'Transaction Password', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'password',
				'description' => __( 'If enabling 3D Secure, enter your Cardinal Centinel Transaction Password.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => '',
				'desc_tip'    => true
			),
			'liability_shift' => array(
				'title'       => __( 'Liability Shift', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Require liability shift', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Only accept payments when liability shift has occurred.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true
			),
			'send_items' => array(
				'title'       => __( 'Send Item Details', 'woocommerce-gateway-paypal-pro' ),
				'label'       => __( 'Send Line Items to PayPal', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'description' => __( 'Sends line items to PayPal. If you experience rounding errors this can be disabled.', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
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
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-gateway-paypal-pro' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-gateway-paypal-pro' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Log PayPal Pro events inside <code>woocommerce/logs/paypal-pro.txt</code>', 'woocommerce-gateway-paypal-pro' ),
			)
		);
    }

	/**
	 * Admin Panel Options
	 * - Options for bits like 'title' and availability on a country-by-country basis
	 *
	 * @access public
	 * @return void
	 */
  	public function admin_options() {
		parent::admin_options();
		?>
		<script type="text/javascript">
			jQuery( '#woocommerce_paypal_pro_enable_3dsecure' ).change( function () {
				var threedsec = jQuery( '#woocommerce_paypal_pro_centinel_pid, #woocommerce_paypal_pro_centinel_mid, #woocommerce_paypal_pro_centinel_pwd, #woocommerce_paypal_pro_liability_shift' ).closest( 'tr' );

				if ( jQuery( this ).is( ':checked' ) ) {
					threedsec.show();
				} else {
					threedsec.hide();
				}
			}).change();
		</script>
		<?php
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
			if ( ! in_array( get_option( 'woocommerce_currency' ), apply_filters( 'woocommerce_paypal_pro_allowed_currencies', array( 'AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD' ) ) ) ) {
				return false;
			}

			// Required fields check
			if ( ! $this->api_username || ! $this->api_password || ! $this->api_signature ) {
				return false;
			}

			return isset( $this->available_card_types[ WC()->countries->get_base_country() ] );
		}

		return false;
	}

	/**
     * Payment form on checkout page
     */
	public function payment_fields() {
		if ( $this->description ) {
			echo '<p>' . $this->description . ( $this->testmode ? ' ' . __( 'TEST/SANDBOX MODE ENABLED. In test mode, you can use the card number 4007000000027 with any CVC and a valid expiration date.  Note that you will get a faster processing result if you use a card from your developer\'s account.', 'woocommerce-gateway-paypal-pro' ) : '' ) . '</p>';
		}

		$fields = array();

		if ( isset( $this->available_card_types[ WC()->countries->get_base_country() ]['Maestro'] ) ) {
			$fields['card-startdate-field'] = '<p class="form-row form-row-first">
				<label for="' . esc_attr( $this->id ) . '-card-startdate">' . __( 'Start Date (MM/YY)', 'woocommerce' ) . '</label>
				<input id="' . esc_attr( $this->id ) . '-card-startdate" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . __( 'MM / YY', 'woocommerce' ) . '" name="' . $this->id . '-card-startdate' . '" />
			</p>';
		}

		$this->credit_card_form( array(), $fields );
	}

	/**
     * Validate the payment form
     */
	public function validate_fields() {
		try {

			$card_number    = isset( $_POST['paypal_pro-card-number'] ) ? woocommerce_clean( $_POST['paypal_pro-card-number'] ) : '';
			$card_cvc       = isset( $_POST['paypal_pro-card-cvc'] ) ? woocommerce_clean( $_POST['paypal_pro-card-cvc'] ) : '';
			$card_expiry    = isset( $_POST['paypal_pro-card-expiry'] ) ? woocommerce_clean( $_POST['paypal_pro-card-expiry'] ) : '';

			// Format values
			$card_number    = str_replace( array( ' ', '-' ), '', $card_number );
			$card_expiry    = array_map( 'trim', explode( '/', $card_expiry ) );
			$card_exp_month = str_pad( $card_expiry[0], 2, "0", STR_PAD_LEFT );
			$card_exp_year  = $card_expiry[1];

			if ( strlen( $card_exp_year ) == 2 ) {
				$card_exp_year += 2000;
			}

			// Validate values
			if ( ! ctype_digit( $card_cvc ) ) {
				throw new Exception( __( 'Card security code is invalid (only digits are allowed)', 'woocommerce-gateway-paypal-pro' ) );
			}

			if (
				! ctype_digit( $card_exp_month ) ||
				! ctype_digit( $card_exp_year ) ||
				$card_exp_month > 12 ||
				$card_exp_month < 1 ||
				$card_exp_year < date('y')
			) {
				throw new Exception( __( 'Card expiration date is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			if ( empty( $card_number ) || ! ctype_digit( $card_number ) ) {
				throw new Exception( __( 'Card number is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			return true;

		} catch( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return false;
		}
	}

	/**
     * Process the payment
     */
	public function process_payment( $order_id ) {
		$order = new WC_Order( $order_id );

		$this->log( 'Processing order #' . $order_id );

		$card_number    = isset( $_POST['paypal_pro-card-number'] ) ? woocommerce_clean( $_POST['paypal_pro-card-number'] ) : '';
		$card_cvc       = isset( $_POST['paypal_pro-card-cvc'] ) ? woocommerce_clean( $_POST['paypal_pro-card-cvc'] ) : '';
		$card_expiry    = isset( $_POST['paypal_pro-card-expiry'] ) ? woocommerce_clean( $_POST['paypal_pro-card-expiry'] ) : '';

		// Format values
		$card_number    = str_replace( array( ' ', '-' ), '', $card_number );
		$card_expiry    = array_map( 'trim', explode( '/', $card_expiry ) );
		$card_exp_month = str_pad( $card_expiry[0], 2, "0", STR_PAD_LEFT );
		$card_exp_year  = $card_expiry[1];

		if ( isset( $_POST['paypal_pro-card-start'] ) ) {
			$card_start       = woocommerce_clean( $_POST['paypal_pro-card-start'] );
			$card_start       = array_map( 'trim', explode( '/', $card_start ) );
			$card_start_month = str_pad( $card_start[0], 2, "0", STR_PAD_LEFT );
			$card_start_year  = $card_start[1];
		} else {
			$card_start_month = '';
			$card_start_year  = '';
		}

		if ( strlen( $card_exp_year ) == 2 ) {
			$card_exp_year += 2000;
		}

		/**
	     * 3D Secure Handling
	     */
		if ( $this->enable_3dsecure ) {

			if ( ! class_exists( 'CentinelClient' ) ) {
				include_once( 'lib/CentinelClient.php' );
			}

			$this->clear_centinel_session();

			$centinelClient = new CentinelClient;

			$centinelClient->add( "MsgType", "cmpi_lookup" );
			$centinelClient->add( "Version", "1.7" );
			$centinelClient->add( "ProcessorId", $this->centinel_pid );
			$centinelClient->add( "MerchantId", $this->centinel_mid );
			$centinelClient->add( "TransactionPwd", $this->centinel_pwd );
			$centinelClient->add( "UserAgent", $_SERVER["HTTP_USER_AGENT"] );
			$centinelClient->add( "BrowserHeader", $_SERVER["HTTP_ACCEPT"] );
			$centinelClient->add( "TransactionType", 'C' );

		    // Standard cmpi_lookup fields
		    $centinelClient->add( 'OrderNumber', $order_id );
		    $centinelClient->add( 'Amount', $order->order_total * 100 );
		    $centinelClient->add( 'CurrencyCode', $this->iso4217[ $order->get_order_currency() ] );
		    $centinelClient->add( 'TransactionMode', 'S' );

			// Items
			$item_loop = 0;

			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $item ) {
					$item_loop++;
					$centinelClient->add( 'Item_Name_' . $item_loop, $item['name'] );
					$centinelClient->add( 'Item_Price_' . $item_loop, number_format( $order->get_item_total( $item, true, true ) * 100 ) );
					$centinelClient->add( 'Item_Quantity_' . $item_loop, $item['qty'] );
					$centinelClient->add( 'Item_Desc_' . $item_loop, $item['name'] );
				}
			}

		    // Payer Authentication specific fields
		    $centinelClient->add( 'CardNumber', $card_number );
		    $centinelClient->add( 'CardExpMonth', $card_exp_month );
		    $centinelClient->add( 'CardExpYear', $card_exp_year );

		    // Send request
		    $centinelClient->sendHttp( $this->centinel_url, "5000", "15000" );

			$this->log( 'Centinal client request: ' . print_r( $centinelClient->request, true ) );
			$this->log( 'Centinal client response: ' . print_r( $centinelClient->response, true ) );

		    // Save response in session
		    WC()->session->set( "paypal_pro_orderid", $order_id ); // Save lookup response in session
		    WC()->session->set( "Centinel_cmpiMessageResp", $centinelClient->response );
			WC()->session->set( "Centinel_Enrolled", $centinelClient->getValue("Enrolled") );
		    WC()->session->set( "Centinel_TransactionId", $centinelClient->getValue("TransactionId") );
		    WC()->session->set( "Centinel_ACSUrl", $centinelClient->getValue("ACSUrl") );
		    WC()->session->set( "Centinel_Payload", $centinelClient->getValue("Payload") );
		    WC()->session->set( "Centinel_ErrorNo", $centinelClient->getValue("ErrorNo") );
		    WC()->session->set( "Centinel_ErrorDesc", $centinelClient->getValue("ErrorDesc") );
			WC()->session->set( "Centinel_EciFlag", $centinelClient->getValue("EciFlag") );
		    WC()->session->set( "Centinel_TransactionType", "C" );
		    WC()->session->set( 'Centinel_TermUrl', WC()->api_request_url( 'WC_Gateway_PayPal_Pro', true ) );
		    WC()->session->set( 'Centinel_OrderId', $centinelClient->getValue("OrderId") );

		    $this->log( '3dsecure Centinel_Enrolled: ' . WC()->session->get( 'Centinel_Enrolled' ) );

		    /******************************************************************************/
		    /*                                                                            */
		    /*                          Result Processing Logic                           */
		    /*                                                                            */
		    /******************************************************************************/

    		if ( WC()->session->get( 'Centinel_ErrorNo' ) == 0 ) {

    			if ( WC()->session->get( 'Centinel_Enrolled' ) == 'Y' ) {

    				$this->log( 'Doing 3dsecure payment authorization' );
    				$this->log( 'ASCUrl: ' . WC()->session->get("Centinel_ACSUrl") );
    				$this->log( 'PaReq: ' . WC()->session->get("Centinel_Payload") );
    				$this->log( 'TermUrl: ' . WC()->session->get("Centinel_TermUrl") );

			        @ob_clean();
					?>
					<html>
						<head>
							<title>3DSecure Payment Authorisation</title>
						</head>
						<body>
							<form name="frmLaunchACS" id="3ds_submit_form" method="POST" action="<?php echo WC()->session->get("Centinel_ACSUrl"); ?>">
						        <input type="hidden" name="PaReq" value="<?php echo WC()->session->get("Centinel_Payload"); ?>">
						        <input type="hidden" name="TermUrl" value="<?php echo WC()->session->get('Centinel_TermUrl'); ?>">
						        <input type="hidden" name="MD" value="<?php echo urlencode( json_encode( array(
									'card'             => $card_number,
									'csc'              => $card_cvc,
									'card_exp_month'   => $card_exp_month,
									'card_exp_year'    => $card_exp_year,
									'card_start_month' => $card_start_month,
									'card_start_year'  => $card_start_year
						        ) ) ); ?>">
						        <noscript>
						        	<div class="woocommerce_message"><?php _e( 'Processing your Payer Authentication Transaction', 'woocommerce-gateway-paypal-pro' ); ?> - <?php _e( 'Please click Submit to continue the processing of your transaction.', 'woocommerce-gateway-paypal-pro' ); ?>  <input type="submit" class="button" id="3ds_submit" value="Submit" /></div>
						        </noscript>
						    </form>
						    <script>
						    	document.frmLaunchACS.submit();
						    </script>
						</body>
					</html>
					<?php
					exit;

    			} elseif ( $this->liability_shift && WC()->session->get( 'Centinel_Enrolled' ) != 'N' ) {

    				wc_add_notice( __( 'Authentication unavailable. Please try a different payment method or card.','woocommerce-gateway-paypal-pro' ), 'error' );
					return;

				} else {

    				// Customer not-enrolled, so just carry on with PayPal process
    				return $this->do_payment( $order, $card_number, '', $card_exp_month, $card_exp_year, $card_cvc, $card_start_month, $card_start_year, '', WC()->session->get('Centinel_Enrolled'), '', WC()->session->get("Centinel_EciFlag"), '' );

    			}

    		} else {
    			wc_add_notice( __( 'Error in 3D secure authentication: ', 'woocommerce-gateway-paypal-pro' ) . WC()->session->get( 'Centinel_ErrorNo' ), 'error' );
    			return;
			}

		}

		// Do payment with paypal
		return $this->do_payment( $order, $card_number, '', $card_exp_month, $card_exp_year, $card_cvc, $card_start_month, $card_start_year );
	}

	/**
	 * Process a refund if supported
	 * @param  int $order_id
	 * @param  float $amount
	 * @param  string $reason
	 * @return  bool|wp_error True or false based on success, or a WP_Error object
	 */
	public function process_refund( $order_id, $amount = null, $reason = '' ) {
		$order = wc_get_order( $order_id );

		$url = $this->testmode ? $this->testurl : $this->liveurl;

		if ( ! $order || ! $order->get_transaction_id() || ! $this->api_username || ! $this->api_password || ! $this->api_signature ) {
			return false;
		}

		// get transaction details
		$details = $this->get_transaction_details( $order->get_transaction_id() );

		// check if it is authorized only we need to void instead
		if ( $details && strtolower( $details['PENDINGREASON'] ) === 'authorization' ) {
			$order->add_order_note( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );

			$this->log( 'Refund order # ' . $order_id . ': authorized only transactions need to use cancel/void instead.' );

			throw new Exception( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );
		}

		$post_data = array(
			'VERSION'       => $this->api_version,
			'SIGNATURE'     => $this->api_signature,
			'USER'          => $this->api_username,
			'PWD'           => $this->api_password,
			'METHOD'        => 'RefundTransaction',
			'TRANSACTIONID' => $order->get_transaction_id(),
			'REFUNDTYPE'    => is_null( $amount ) ? 'Full' : 'Partial'
		);

		if ( ! is_null( $amount ) ) {
			$post_data['AMT']          = number_format( $amount, 2, '.', '' );
			$post_data['CURRENCYCODE'] = $order->get_order_currency();
		}

		if ( $reason ) {
			if ( 255 < strlen( $reason ) ) {
				$reason = substr( $reason, 0, 252 ) . '...';
			}

			$post_data['NOTE'] = html_entity_decode( $reason, ENT_NOQUOTES, 'UTF-8' );
		}

		$response = wp_remote_post( $url, array(
			'method'      => 'POST',
			'headers'     => array( 'PAYPAL-NVP' => 'Y' ),
			'body'        => $post_data,
			'timeout'     => 70,
			'sslverify'   => false,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1'
		));

		if ( is_wp_error( $response ) ) {
			$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );

			throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
		}

		parse_str( $response['body'], $parsed_response );

		switch ( strtolower( $parsed_response['ACK'] ) ) {
			case 'success':
			case 'successwithwarning':
				$order->add_order_note( sprintf( __( 'Refunded %s - Refund ID: %s', 'woocommerce-gateway-paypal-pro' ), $parsed_response['GROSSREFUNDAMT'], $parsed_response['REFUNDTRANSACTIONID'] ) );
				return true;
			default:
				$this->log( 'Parsed Response (refund) ' . print_r( $parsed_response, true ) );
			break;
		}

		return false;
	}

	/**
	 * Auth 3dsecure
	 */
	public function authorise_3dsecure() {
		if ( ! class_exists( 'CentinelClient' ) ) {
			include_once( 'lib/CentinelClient.php' );
		}

		$pares         = ! empty( $_POST['PaRes'] ) ? $_POST['PaRes'] : '';
		$merchant_data = ! empty( $_POST['MD'] ) ? (array) json_decode( urldecode( $_POST['MD'] ) ) : '';
		$order_id      = WC()->session->get( "paypal_pro_orderid" );
		$order         = new WC_Order( $order_id );

		$this->log( 'authorise_3dsecure() for order ' . absint( $order_id ) );

	    /******************************************************************************/
	    /*                                                                            */
	    /*    If the PaRes is Not Empty then process the cmpi_authenticate message    */
	    /*                                                                            */
	    /******************************************************************************/

	    if (strcasecmp('', $pares )!= 0 && $pares != null) {

	        $centinelClient = new CentinelClient;

	        $centinelClient->add( 'MsgType', 'cmpi_authenticate' );
	        $centinelClient->add( "Version", "1.7" );
			$centinelClient->add( "ProcessorId", $this->centinel_pid );
			$centinelClient->add( "MerchantId", $this->centinel_mid );
			$centinelClient->add( "TransactionPwd", $this->centinel_pwd );
			$centinelClient->add( "TransactionType", 'C' );
	        $centinelClient->add( 'OrderId', WC()->session->get( 'Centinel_OrderId' ) );
	        $centinelClient->add( 'TransactionId', WC()->session->get( 'Centinel_TransactionId' ) );
	        $centinelClient->add( 'PAResPayload', $pares );
			$centinelClient->sendHttp( $this->centinel_url, "5000", "15000" );

			$this->log( 'Centinal transaction ID ' . WC()->session->get('Centinel_TransactionId') );
			$this->log( 'Centinal client request: ' . print_r( $centinelClient->request, true ) );
			$this->log( 'Centinal client response: ' . print_r( $centinelClient->response, true ) );

			WC()->session->set( "Centinel_cmpiMessageResp", $centinelClient->response ); // Save authenticate response in session
			WC()->session->set( "Centinel_PAResStatus", $centinelClient->getValue("PAResStatus") );
			WC()->session->set( "Centinel_SignatureVerification", $centinelClient->getValue("SignatureVerification") );
			WC()->session->set( "Centinel_ErrorNo", $centinelClient->getValue("ErrorNo") );
			WC()->session->set( "Centinel_ErrorDesc", $centinelClient->getValue("ErrorDesc") );
			WC()->session->set( "Centinel_EciFlag", $centinelClient->getValue("EciFlag") );
			WC()->session->set( "Centinel_Cavv", $centinelClient->getValue("Cavv") );
			WC()->session->set( "Centinel_Xid", $centinelClient->getValue("Xid") );
	    } else {
	    	WC()->session->set( "Centinel_ErrorNo", "0" );
			WC()->session->set( "Centinel_ErrorDesc", "NO PARES RETURNED" );
	    }

	    /******************************************************************************/
	    /*                                                                            */
	    /*                  Determine if the transaction resulted in                  */
	    /*                  an error.                                                 */
	    /*                                                                            */
	    /******************************************************************************/

		$redirect_url = $this->get_return_url( $order );

		try {

			$pa_res_status    = WC()->session->get( "Centinel_PAResStatus" );
			$eci_flag         = WC()->session->get( "Centinel_EciFlag" );
			$error_no         = WC()->session->get( 'Centinel_ErrorNo' );
			$error_desc       = WC()->session->get( "Centinel_ErrorDesc" );
			$cavv             = WC()->session->get( "Centinel_Cavv" );
			$xid              = WC()->session->get( "Centinel_Xid" );
			$sig_verification = WC()->session->get( "Centinel_SignatureVerification" );

			$this->log( '3dsecure pa_res_status: ' . $pa_res_status );

			if ( $this->liability_shift ) {
				if ( $eci_flag == '07' || $eci_flag == '01' ) {
					$order->update_status( 'failed', __('3D Secure error: No liability shift', 'woocommerce-gateway-paypal-pro' ) );
					throw new Exception( __( 'Authentication unavailable.  Please try a different payment method or card.', 'woocommerce-gateway-paypal-pro' ) );
				}
			}

			if ( $error_no == "0" ) {

				if ( ( $pa_res_status == "Y" || $pa_res_status == "A" || $pa_res_status == "U") && $sig_verification == "Y" ) {

					// If we are here we can process the card
					$this->do_payment( $order, $merchant_data['card'], $merchant_data['type'], $merchant_data['card_exp_month'], $merchant_data['card_exp_year'], $merchant_data['csc'], $merchant_data['card_start_month'], $merchant_data['card_start_year'], $pa_res_status, "Y", $cavv, $eci_flag, $xid );

					$this->clear_centinel_session();

				} else {
					$order->update_status( 'failed', sprintf(__('3D Secure error: %s', 'woocommerce-gateway-paypal-pro' ), $error_desc ) );
					throw new Exception( __( 'Payer Authentication failed.  Please try a different payment method.','woocommerce-gateway-paypal-pro' ) );
				}

			} else {
				$order->update_status( 'failed', sprintf(__('3D Secure error: %s', 'woocommerce-gateway-paypal-pro' ), $error_desc ) );
				throw new Exception( __( 'Error in 3D secure authentication: ', 'woocommerce-gateway-paypal-pro' ) . $error_desc );
			}

		} catch( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
		}

		wp_redirect( $redirect_url );
		exit;
	}

	/**
	 * do_payment function.
	 *
	 * @access public
	 * @param mixed $order
	 * @param mixed $card_number
	 * @param mixed $card_type
	 * @param mixed $card_exp_month
	 * @param mixed $card_exp_year
	 * @param mixed $card_cvc
	 * @param string $centinelPAResStatus (default: '')
	 * @param string $centinelEnrolled (default: '')
	 * @param string $centinelCavv (default: '')
	 * @param string $centinelEciFlag (default: '')
	 * @param string $centinelXid (default: '')
	 * @return void
	 */
	public function do_payment( $order, $card_number, $card_type, $card_exp_month, $card_exp_year, $card_cvc, $card_start_month, $card_start_year, $centinelPAResStatus = '', $centinelEnrolled = '', $centinelCavv = '', $centinelEciFlag = '', $centinelXid = '' ) {

		$card_exp   = $card_exp_month . $card_exp_year;
		$card_start = $card_start_month . $card_start_year;

		// Send request to paypal
		try {
			$url = $this->testmode ? $this->testurl : $this->liveurl;

			$post_data = array(
				'VERSION'           => $this->api_version,
				'SIGNATURE'         => $this->api_signature,
				'USER'              => $this->api_username,
				'PWD'               => $this->api_password,
				'METHOD'            => 'DoDirectPayment',
				'PAYMENTACTION'     => $this->paymentaction,
				'IPADDRESS'         => $this->get_user_ip(),
				'AMT'               => $order->get_total(),
				'INVNUM'            => $order->get_order_number(),
				'CURRENCYCODE'      => $order->get_order_currency(),
				'CREDITCARDTYPE'    => $card_type,
				'ACCT'              => $card_number,
				'EXPDATE'           => $card_exp,
				'STARTDATE'         => $card_start,
				'CVV2'              => $card_cvc,
				'EMAIL'             => $order->billing_email,
				'FIRSTNAME'         => $order->billing_first_name,
				'LASTNAME'          => $order->billing_last_name,
				'STREET'            => $order->billing_address_1 . ' ' . $order->billing_address_2,
				'CITY'              => $order->billing_city,
				'STATE'             => $order->billing_state,
				'ZIP'               => $order->billing_postcode,
				'COUNTRYCODE'       => $order->billing_country,
				'SHIPTONAME'        => $order->shipping_first_name . ' ' . $order->shipping_last_name,
				'SHIPTOSTREET'      => $order->shipping_address_1,
				'SHIPTOSTREET2'     => $order->shipping_address_2,
				'SHIPTOCITY'        => $order->shipping_city,
				'SHIPTOSTATE'       => $order->shipping_state,
				'SHIPTOCOUNTRYCODE' => $order->shipping_country,
				'SHIPTOZIP'         => $order->shipping_postcode,
				'BUTTONSOURCE'      => 'WooThemes_Cart'
			);

			if ( $this->soft_descriptor ) {
				$post_data['SOFTDESCRIPTOR'] = $this->soft_descriptor;
			}

			/* Send Item details - thanks Harold Coronado */
			if ( $this->send_items ) {

				/* Send Item details */
				$item_loop = 0;

				if ( sizeof( $order->get_items() ) > 0 ) {

					$ITEMAMT = 0;

					foreach ( $order->get_items() as $item ) {
						$_product = $order->get_product_from_item( $item );
						if ( $item['qty'] ) {
							$item_name = $item['name'];

							$item_meta = new WC_Order_Item_Meta( $item['item_meta'] );

							if ( $meta = $item_meta->display( true, true ) ) {
								$item_name .= ' ( ' . $meta . ' )';
							}

							$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
							$post_data[ 'L_NAME' . $item_loop ]   = $item_name;
							$post_data[ 'L_AMT' . $item_loop ]    = $order->get_item_total( $item, true );
							$post_data[ 'L_QTY' . $item_loop ]    = $item['qty'];

							$ITEMAMT += $order->get_item_total( $item, true ) * $item['qty'];

							$item_loop++;
						}
					}

					// Shipping
					if ( ( $order->get_total_shipping() + $order->get_shipping_tax() ) > 0 ) {
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = 'Shipping';
						$post_data[ 'L_AMT' . $item_loop ]    = round( $order->get_total_shipping() + $order->get_shipping_tax(), 2 );
						$post_data[ 'L_QTY' . $item_loop ]    = 1;

						$ITEMAMT += round( $order->get_total_shipping() + $order->get_shipping_tax(), 2 );

						$item_loop++;
					}

					// Discount
					if ( $order->get_order_discount() > 0 ) {
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = 'Order Discount';
						$post_data[ 'L_AMT' . $item_loop ]    = '-' . $order->get_order_discount();
						$post_data[ 'L_QTY' . $item_loop ]    = 1;

						$item_loop++;
					}

					$ITEMAMT = round( $ITEMAMT, 2 );

					// Fix rounding
					if ( absint( $order->get_total() * 100 ) !== absint( $ITEMAMT * 100 ) ) {
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = 'Rounding amendment';
						$post_data[ 'L_AMT' . $item_loop ]    = ( absint( $order->get_total() * 100 ) - absint( $ITEMAMT * 100 ) ) / 100;
						$post_data[ 'L_QTY' . $item_loop ]    = 1;
					}

					$post_data[ 'ITEMAMT' ] = $order->get_total();
				}
			}

			if ( $this->debug ) {
				$log         = $post_data;
				$log['ACCT'] = '****';
				$log['CVV2'] = '****';
				$this->log( 'Do payment request ' . print_r( $log, true ) );
			}

			/* 3D Secure */
			if ( $this->enable_3dsecure ) {
				$post_data['AUTHSTATUS3DS'] = $centinelPAResStatus;
				$post_data['MPIVENDOR3DS'] 	= $centinelEnrolled;
				$post_data['CAVV'] 			= $centinelCavv;
				$post_data['ECI3DS'] 		= $centinelEciFlag;
				$post_data['XID'] 			= $centinelXid;
			}

			$response = wp_remote_post( $url, array(
   				'method'		=> 'POST',
				'headers'       => array(
					'PAYPAL-NVP' => 'Y'
				),
    			'body' 			=> apply_filters( 'woocommerce-gateway-paypal-pro_request', $post_data, $order ),
    			'timeout' 		=> 70,
    			'sslverify' 	=> false,
    			'user-agent' 	=> 'WooCommerce',
    			'httpversion'   => '1.1'
			));

			if ( is_wp_error( $response ) ) {
				$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );

				throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
			}

			$this->log( 'Response ' . print_r( $response['body'], true ) );

			if ( empty( $response['body'] ) ) {
				$this->log( 'Empty response!' );

				throw new Exception( __( 'Empty Paypal response.', 'woocommerce-gateway-paypal-pro' ) );
			}

			parse_str( $response['body'], $parsed_response );

			$this->log( 'Parsed Response ' . print_r( $parsed_response, true ) );

			switch ( strtolower( $parsed_response['ACK'] ) ) {
				case 'success':
				case 'successwithwarning':
					$txn_id = ( ! empty( $parsed_response['TRANSACTIONID'] ) ) ? wc_clean( $parsed_response['TRANSACTIONID'] ) : '';
					$correlation_id = ( ! empty( $parsed_response['CORRELATIONID'] ) ) ? wc_clean( $parsed_response['CORRELATIONID'] ) : '';

					// get transaction details
					$details = $this->get_transaction_details( $txn_id );

					// check if it is captured or authorization only
					if ( $details && strtolower( $details['PAYMENTSTATUS'] ) === 'pending' && strtolower( $details['PENDINGREASON'] ) === 'authorization' ) {
						// Store captured value
						update_post_meta( $order->id, '_paypalpro_charge_captured', 'no' );
						add_post_meta( $order->id, '_transaction_id', $txn_id, true );

						// Mark as on-hold
						$order->update_status( 'on-hold', sprintf( __( 'PayPal Pro charge authorized (Charge ID: %s). Process order to take payment, or cancel to remove the pre-authorization.', 'woocommerce-gateway-paypal-pro' ), $txn_id ) );

						// Reduce stock levels
						$order->reduce_order_stock();
					} else {

						// Add order note
						$order->add_order_note( sprintf( __( 'PayPal Pro payment completed (Transaction ID: %s, Correlation ID: %s)', 'woocommerce-gateway-paypal-pro' ), $txn_id, $correlation_id ) );

						// Payment complete
						$order->payment_complete( $txn_id );
					}

					// Remove cart
					WC()->cart->empty_cart();

					if ( method_exists( $order, 'get_checkout_order_received_url' ) ) {
	                	$redirect = $order->get_checkout_order_received_url();
	                } else {
	                	$redirect = add_query_arg( 'key', $order->order_key, add_query_arg( 'order', $order->id, get_permalink( get_option( 'woocommerce_thanks_page_id' ) ) ) );
	                }

					// Return thank you page redirect
					return array(
						'result' 	=> 'success',
						'redirect'	=> $redirect
					);
				break;
				case 'failure':
				default:

					// Get error message
					if ( ! empty( $parsed_response['L_LONGMESSAGE0'] ) )
						$error_message = $parsed_response['L_LONGMESSAGE0'];
					elseif ( ! empty( $parsed_response['L_SHORTMESSAGE0'] ) )
						$error_message = $parsed_response['L_SHORTMESSAGE0'];
					elseif ( ! empty( $parsed_response['L_SEVERITYCODE0'] ) )
						$error_message = $parsed_response['L_SEVERITYCODE0'];
					elseif ( $this->testmode )
						$error_message = print_r( $parsed_response, true );

					// Payment failed :(
					$order->update_status( 'failed', sprintf(__('PayPal Pro payment failed (Correlation ID: %s). Payment was rejected due to an error: ', 'woocommerce-gateway-paypal-pro'), $parsed_response['CORRELATIONID'] ) . '(' . $parsed_response['L_ERRORCODE0'] . ') ' . '"' . $error_message . '"' );

					throw new Exception( $error_message );

				break;
			}

		} catch( Exception $e ) {
			wc_add_notice( '<strong>' . __( 'Payment error', 'woocommerce-gateway-paypal-pro' ) . '</strong>: ' . $e->getMessage(), 'error' );
			return;
		}
	}

	/**
	 * Get transaction details
	 */
	public function get_transaction_details( $transaction_id = 0 ) {
		$url = $this->testmode ? $this->testurl : $this->liveurl;

		$post_data = array(
			'VERSION'       => $this->api_version,
			'SIGNATURE'     => $this->api_signature,
			'USER'          => $this->api_username,
			'PWD'           => $this->api_password,
			'METHOD'        => 'GetTransactionDetails',
			'TRANSACTIONID' => $transaction_id
		);

		$response = wp_remote_post( $url, array(
			'method'      => 'POST',
			'headers'     => array(
				'PAYPAL-NVP' => 'Y'
			),
			'body'        => $post_data,
			'timeout'     => 70,
			'sslverify'   => false,
			'user-agent'  => 'WooCommerce',
			'httpversion' => '1.1'
		));

		if ( is_wp_error( $response ) ) {
			$this->log( 'Error ' . print_r( $response->get_error_message(), true ) );

			throw new Exception( __( 'There was a problem connecting to the payment gateway.', 'woocommerce-gateway-paypal-pro' ) );
		}

		parse_str( $response['body'], $parsed_response );

		switch ( strtolower( $parsed_response['ACK'] ) ) {
			case 'success':
			case 'successwithwarning':

				return $parsed_response;
			break;
		}

		return false;
	}

	/**
     * Get user's IP address
     */
	public function get_user_ip() {
		return ! empty( $_SERVER['HTTP_X_FORWARD_FOR'] ) ? $_SERVER['HTTP_X_FORWARD_FOR'] : $_SERVER['REMOTE_ADDR'];
	}

	/**
	 * clear_centinel_session function.
	 */
	public function clear_centinel_session() {
        WC()->session->set( "paypal_pro_orderid", null );
	    WC()->session->set( "Centinel_cmpiMessageResp", null );
		WC()->session->set( "Centinel_Enrolled", null );
	    WC()->session->set( "Centinel_TransactionId", null );
	    WC()->session->set( "Centinel_ACSUrl", null );
	    WC()->session->set( "Centinel_Payload", null );
	    WC()->session->set( "Centinel_ErrorNo", null );
	    WC()->session->set( "Centinel_ErrorDesc", null );
		WC()->session->set( "Centinel_EciFlag", null );
	    WC()->session->set( "Centinel_TransactionType", null );
	    WC()->session->set( 'Centinel_TermUrl', null );
	    WC()->session->set( 'Centinel_OrderId', null );
    }

    /**
     * Add a log entry
     */
    public function log( $message ) {
    	if ( $this->debug ) {
    		if ( ! isset( $this->log ) ) {
    			$this->log = new WC_Logger();
    		}
			$this->log->add( 'paypal-pro', $message );
    	}
    }
}
