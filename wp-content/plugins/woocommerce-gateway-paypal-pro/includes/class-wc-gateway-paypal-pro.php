<?php
/**
 * WC_Gateway_PayPal_Pro class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Gateway_PayPal_Pro extends WC_Payment_Gateway {

	/**
	 * Store client
	 */
	private $centinel_client = false;

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
		$this->liveurl              = 'https://api-3t.paypal.com/nvp';
		$this->testurl              = 'https://api-3t.sandbox.paypal.com/nvp';
		$this->liveurl_3ds          = 'https://paypal.cardinalcommerce.com/maps/txns.asp';
		$this->testurl_3ds          = 'https://centineltest.cardinalcommerce.com/maps/txns.asp';
		$this->available_card_types = apply_filters( 'woocommerce_paypal_pro_available_card_types', array(
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
		add_action( 'woocommerce_api_wc_gateway_paypal_pro', array( $this, 'handle_3dsecure' ) );
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
			if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paypal_pro_allowed_currencies', array( 'AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HUF', 'JPY', 'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD' ) ) ) ) {
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
			echo '<p>' . wp_kses_post( $this->description ) . ( $this->testmode ? ' ' . __( 'TEST/SANDBOX MODE ENABLED. In test mode, you can use the card number 4007000000027 with any CVC and a valid expiration date.  Note that you will get a faster processing result if you use a card from your developer\'s account.', 'woocommerce-gateway-paypal-pro' ) : '' ) . '</p>';
		}

		$fields = array();

		if ( isset( $this->available_card_types[ WC()->countries->get_base_country() ]['Maestro'] ) ) {
			$fields = array(
				'card-number-field' => '<p class="form-row form-row-first">
					<label for="' . esc_attr( $this->id ) . '-card-number">' . __( 'Card Number', 'woocommerce' ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="' . $this->id . '-card-number' . '" />
				</p>',
				'card-expiry-field' => '<p class="form-row form-row-last">
					<label for="' . esc_attr( $this->id ) . '-card-expiry">' . __( 'Expiry (MM/YY)', 'woocommerce' ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . esc_attr__( 'MM / YY', 'woocommerce' ) . '" name="' . $this->id . '-card-expiry' . '" />
				</p>',
				'card-cvc-field' => '<p class="form-row form-row-first">
					<label for="' . esc_attr( $this->id ) . '-card-cvc">' . __( 'Card Code', 'woocommerce' ) . ' <span class="required">*</span></label>
					<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . esc_attr__( 'CVC', 'woocommerce' ) . '" name="' . $this->id . '-card-cvc' . '" />
				</p>',
				'card-startdate-field' => '<p class="form-row form-row-last">
					<label for="' . esc_attr( $this->id ) . '-card-startdate">' . __( 'Start Date (MM/YY)', 'woocommerce-gateway-paypal-pro' ) . '</label>
					<input id="' . esc_attr( $this->id ) . '-card-startdate" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . __( 'MM / YY', 'woocommerce-gateway-paypal-pro' ) . '" name="' . $this->id . '-card-startdate' . '" />
				</p>'
			);
		}

		$this->credit_card_form( array(), $fields );
	}

	/**
	 * Format and get posted details
	 * @return object
	 */
	private function get_posted_card() {
		$card_number    = isset( $_POST['paypal_pro-card-number'] ) ? wc_clean( $_POST['paypal_pro-card-number'] ) : '';
		$card_cvc       = isset( $_POST['paypal_pro-card-cvc'] ) ? wc_clean( $_POST['paypal_pro-card-cvc'] ) : '';
		$card_expiry    = isset( $_POST['paypal_pro-card-expiry'] ) ? wc_clean( $_POST['paypal_pro-card-expiry'] ) : '';

		// Format values
		$card_number    = str_replace( array( ' ', '-' ), '', $card_number );
		$card_expiry    = array_map( 'trim', explode( '/', $card_expiry ) );
		$card_exp_month = str_pad( $card_expiry[0], 2, "0", STR_PAD_LEFT );
		$card_exp_year  = isset( $card_expiry[1] ) ? $card_expiry[1] : '';

		if ( isset( $_POST['paypal_pro-card-start'] ) ) {
			$card_start       = wc_clean( $_POST['paypal_pro-card-start'] );
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

		if ( strlen( $card_start_year ) == 2 ) {
			$card_start_year += 2000;
		}

		return (object) array(
			'number'      => $card_number,
			'type'        => '',
			'cvc'         => $card_cvc,
			'exp_month'   => $card_exp_month,
			'exp_year'    => $card_exp_year,
			'start_month' => $card_start_month,
			'start_year'  => $card_start_year
		);
	}

	/**
     * Validate the payment form
     */
	public function validate_fields() {
		try {
			$card = $this->get_posted_card();

			if ( empty( $card->exp_month ) || empty( $card->exp_year ) ) {
				throw new Exception( __( 'Card expiration date is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			// Validate values
			if ( ! ctype_digit( $card->cvc ) ) {
				throw new Exception( __( 'Card security code is invalid (only digits are allowed)', 'woocommerce-gateway-paypal-pro' ) );
			}

			if (
				! ctype_digit( $card->exp_month ) ||
				! ctype_digit( $card->exp_year ) ||
				$card->exp_month > 12 ||
				$card->exp_month < 1 ||
				$card->exp_year < date( 'y' )
			) {
				throw new Exception( __( 'Card expiration date is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			if ( empty( $card->number ) || ! ctype_digit( $card->number ) ) {
				throw new Exception( __( 'Card number is invalid', 'woocommerce-gateway-paypal-pro' ) );
			}

			return true;

		} catch( Exception $e ) {
			wc_add_notice( $e->getMessage(), 'error' );
			return false;
		}
	}

	/**
	 * Get and clean a value from $this->centinel_client because the SDK does a poor job of cleaning.
	 * @return string
	 */
	public function get_centinel_value( $key ) {
		$value = $this->centinel_client->getValue( $key );
		$value = wc_clean( $value );
		return $value;
	}

	/**
     * Process the payment
     */
	public function process_payment( $order_id ) {
		$order = wc_get_order( $order_id );
		$card  = $this->get_posted_card();

		$this->log( 'Processing order #' . $order_id );

		/**
	     * 3D Secure Handling
	     */
		if ( $this->enable_3dsecure ) {

			if ( ! class_exists( 'CentinelClient' ) ) {
				include_once( 'lib/CentinelClient.php' );
			}

			$this->clear_centinel_session();

			$this->centinel_client = new CentinelClient;
			$this->centinel_client->add( "MsgType", "cmpi_lookup" );
			$this->centinel_client->add( "Version", "1.7" );
			$this->centinel_client->add( "ProcessorId", $this->centinel_pid );
			$this->centinel_client->add( "MerchantId", $this->centinel_mid );
			$this->centinel_client->add( "TransactionPwd", $this->centinel_pwd );
			$this->centinel_client->add( "TransactionType", 'CC' );
		    $this->centinel_client->add( 'OrderNumber', $order_id );
		    $this->centinel_client->add( 'Amount', $order->get_total() * 100 );
		    $this->centinel_client->add( 'CurrencyCode', $this->iso4217[ $order->get_order_currency() ] );
		    $this->centinel_client->add( 'TransactionMode', 'S' );
			$this->centinel_client->add( 'ProductCode', 'PHY' );
			$this->centinel_client->add( 'CardNumber', $card->number );
		    $this->centinel_client->add( 'CardExpMonth', $card->exp_month );
		    $this->centinel_client->add( 'CardExpYear', $card->exp_year );
			$this->centinel_client->add( 'CardCode', $card->cvc );
			$this->centinel_client->add( 'BillingFirstName', $order->billing_first_name );
			$this->centinel_client->add( 'BillingLastName', $order->billing_last_name );
			$this->centinel_client->add( 'BillingAddress1', $order->billing_address_1 );
			$this->centinel_client->add( 'BillingAddress2', $order->billing_address_2 );
			$this->centinel_client->add( 'BillingCity', $order->billing_city );
			$this->centinel_client->add( 'BillingState', $order->billing_state );
			$this->centinel_client->add( 'BillingPostalCode', $order->billing_postcode );
			$this->centinel_client->add( 'BillingCountryCode', $order->billing_country );
			$this->centinel_client->add( 'BillingPhone', $order->billing_phone );
			$this->centinel_client->add( 'ShippingFirstName', $order->shipping_first_name );
			$this->centinel_client->add( 'ShippingLastName', $order->shipping_last_name );
			$this->centinel_client->add( 'ShippingAddress1', $order->shipping_address_1 );
			$this->centinel_client->add( 'ShippingAddress2', $order->shipping_address_2 );
			$this->centinel_client->add( 'ShippingCity', $order->shipping_city );
			$this->centinel_client->add( 'ShippingState', $order->shipping_state );
			$this->centinel_client->add( 'ShippingPostalCode', $order->shipping_postcode );
			$this->centinel_client->add( 'ShippingCountryCode', $order->shipping_country );

			// Items
			$item_loop = 0;

			if ( sizeof( $order->get_items() ) > 0 ) {
				foreach ( $order->get_items() as $item ) {
					$item_loop++;
					$this->centinel_client->add( 'Item_Name_' . $item_loop, $item['name'] );
					$this->centinel_client->add( 'Item_Price_' . $item_loop, number_format( $order->get_item_total( $item, true, true ) * 100 ), 2, '.', '' );
					$this->centinel_client->add( 'Item_Quantity_' . $item_loop, $item['qty'] );
					$this->centinel_client->add( 'Item_Desc_' . $item_loop, $item['name'] );
				}
			}

		    // Send request
		    $this->centinel_client->sendHttp( $this->centinel_url, "5000", "15000" );

			$this->log( 'Centinal client request: ' . print_r( $this->centinel_client->request, true ) );
			$this->log( 'Centinal client response: ' . print_r( $this->centinel_client->response, true ) );

			// Store response
			WC()->session->set( 'Centinel_ErrorNo', $this->get_centinel_value( "ErrorNo" ) );
			WC()->session->set( 'Centinel_ErrorDesc', $this->get_centinel_value( "ErrorDesc" ) );
			WC()->session->set( 'Centinel_TransactionId', $this->get_centinel_value( "TransactionId" ) );
			WC()->session->set( 'Centinel_OrderId', $this->get_centinel_value( "OrderId" ) );
			WC()->session->set( 'Centinel_Enrolled', $this->get_centinel_value( "Enrolled" ) );
			WC()->session->set( 'Centinel_ACSUrl', $this->get_centinel_value( "ACSUrl" ) );
			WC()->session->set( 'Centinel_Payload', $this->get_centinel_value( "Payload" ) );
			WC()->session->set( 'Centinel_EciFlag', $this->get_centinel_value( "EciFlag" ) );
			WC()->session->set( 'Centinel_card_start_month', $card->start_month );
			WC()->session->set( 'Centinel_card_start_year', $card->start_year );

    		if ( $this->get_centinel_value( "ErrorNo" ) ) {
				wc_add_notice( __( 'Error in 3D secure authentication: ', 'woocommerce-gateway-paypal-pro' ) . $this->get_centinel_value( "ErrorDesc" ), 'error' );
    			return;
			}

    		if ( 'Y' === $this->get_centinel_value( "Enrolled" ) ) {

				$this->log( 'Doing 3dsecure payment authorization' );
				$this->log( 'ASCUrl: ' . $this->get_centinel_value( "ACSUrl" ) );
				$this->log( 'PaReq: ' . $this->get_centinel_value( "Payload" ) );

				return array(
					'result'   => 'success',
					'redirect' => add_query_arg( array( 'acs' => $order_id ), WC()->api_request_url( 'WC_Gateway_PayPal_Pro', true ) )
				);

			} elseif ( $this->liability_shift && 'N' !== $this->get_centinel_value( "Enrolled" ) ) {
				wc_add_notice( __( 'Authentication unavailable. Please try a different payment method or card.','woocommerce-gateway-paypal-pro' ), 'error' );
				return;
			}
		}

		// Do payment with paypal
		return $this->do_payment( $order, $card );
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

		if ( ! $order || ! $order->get_transaction_id() || ! $this->api_username || ! $this->api_password || ! $this->api_signature ) {
			return false;
		}

		// get transaction details
		$details = $this->get_transaction_details( $order->get_transaction_id() );

		// check if it is authorized only we need to void instead
		if ( $details && strtolower( $details['PENDINGREASON'] ) === 'authorization' ) {
			$order->add_order_note( __( 'This order cannot be refunded due to an authorized only transaction.  Please use cancel instead.', 'woocommerce-gateway-paypal-pro' ) );
			$this->log( 'Refund order # ' . absint( $order_id ) . ': authorized only transactions need to use cancel/void instead.' );
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

		$response = wp_safe_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
			'method'      => 'POST',
			'headers'     => array( 'PAYPAL-NVP' => 'Y' ),
			'body'        => $post_data,
			'timeout'     => 70,
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
	public function handle_3dsecure() {
		if ( ! empty( $_GET['acs'] ) ) {
			$order_id = wc_clean( $_GET['acs'] );
			$acsurl   = WC()->session->get( 'Centinel_ACSUrl' );
			$payload  = WC()->session->get( 'Centinel_Payload' );
			?>
			<html>
				<head>
					<title>3DSecure Payment Authorisation</title>
				</head>
				<body>
					<form name="frmLaunchACS" id="3ds_submit_form" method="POST" action="<?php echo esc_url( $acsurl ); ?>">
						<input type="hidden" name="PaReq" value="<?php echo esc_attr( $payload ); ?>">
						<input type="hidden" name="TermUrl" value="<?php echo esc_attr( WC()->api_request_url( 'WC_Gateway_PayPal_Pro', true ) ); ?>">
						<input type="hidden" name="MD" value="<?php echo absint( $order_id ); ?>">
						<noscript>
							<input type="submit" class="button" id="3ds_submit" value="Submit" />
						</noscript>
					</form>
					<script>
						document.frmLaunchACS.submit();
					</script>
				</body>
			</html>
			<?php
			exit;
		} else {
			$this->auth_3dsecure();
		}
	}

	/**
	 * cmpi_authenticate 3dsecure
	 */
	public function auth_3dsecure() {
		if ( ! class_exists( 'CentinelClient' ) ) {
			include_once( 'lib/CentinelClient.php' );
		}

		$pares        = ! empty( $_POST['PaRes'] ) ? $_POST['PaRes']   : '';
		$order_id     = absint( ! empty( $_POST['MD'] ) ? $_POST['MD'] : 0 );
		$order        = wc_get_order( $order_id );
		$redirect_url = $this->get_return_url( $order );

		$this->log( 'authorise_3dsecure() for order ' . absint( $order_id ) );
		$this->log( 'authorise_3dsecure() PARes ' . print_r( $pares, true ) );

		try {
		    // If the PaRes is Not Empty then process the cmpi_authenticate message
		    if ( ! empty( $pares ) ) {
		        $this->centinel_client = new CentinelClient;
		        $this->centinel_client->add( 'MsgType', 'cmpi_authenticate' );
		        $this->centinel_client->add( "Version", "1.7" );
				$this->centinel_client->add( "ProcessorId", $this->centinel_pid );
				$this->centinel_client->add( "MerchantId", $this->centinel_mid );
				$this->centinel_client->add( "TransactionPwd", $this->centinel_pwd );
				$this->centinel_client->add( "TransactionType", 'CC' );
				$this->centinel_client->add( 'TransactionId', WC()->session->get( 'Centinel_TransactionId' ) );
				$this->centinel_client->add( 'PAResPayload', $pares );
				$this->centinel_client->sendHttp( $this->centinel_url, "5000", "15000" );

				$response_to_log = $this->centinel_client->response;
				$response_to_log['CardNumber'] = 'XXX';
				$response_to_log['CardCode']   = 'XXX';
				$this->log( 'Centinal transaction ID ' . WC()->session->get('Centinel_TransactionId') );
				$this->log( 'Centinal client request : ' . print_r( $this->centinel_client->request, true ) );
				$this->log( 'Centinal client response: ' . print_r( $response_to_log, true ) );
				$this->log( '3dsecure pa_res_status: ' . $this->get_centinel_value( "PAResStatus" ) );

				if ( $this->liability_shift && ( $this->get_centinel_value( "EciFlag" ) == '07' || $this->get_centinel_value( "EciFlag" ) == '01' ) ) {
					$order->update_status( 'failed', __( '3D Secure error: No liability shift', 'woocommerce-gateway-paypal-pro' ) );
					throw new Exception( __( 'Authentication unavailable.  Please try a different payment method or card.', 'woocommerce-gateway-paypal-pro' ) );
				}

				if ( ! $this->get_centinel_value( "ErrorNo" ) && in_array( $this->get_centinel_value( "PAResStatus" ), array( 'Y', 'A', 'U' ) ) && "Y" === $this->get_centinel_value( "SignatureVerification" ) ) {
					$card              = new stdClass();
					$card->number      = $this->get_centinel_value( "CardNumber" );
					$card->type        = '';
					$card->cvc         = $this->get_centinel_value( "CardCode" );
					$card->exp_month   = $this->get_centinel_value( "CardExpMonth" );
					$card->exp_year    = $this->get_centinel_value( "CardExpYear" );
					$card->start_month = WC()->session->get( 'Centinel_card_start_month' );
					$card->start_year  = WC()->session->get( 'Centinel_card_start_year' );

					$centinel              = new stdClass();
					$centinel->paresstatus = $this->get_centinel_value( "PAResStatus" );
					$centinel->xid         = $this->get_centinel_value( "Xid" );
					$centinel->cavv        = $this->get_centinel_value( "Cavv" );
					$centinel->eciflag     = $this->get_centinel_value( "EciFlag" );
					$centinel->enrolled    = WC()->session->get( 'Centinel_Enrolled' );

					// If we are here we can process the card
					$this->do_payment( $order, $card, $centinel );

				} else {
					$order->update_status( 'failed', sprintf( __( '3D Secure error: %s', 'woocommerce-gateway-paypal-pro' ), $this->get_centinel_value( "ErrorDesc" ) ) );
					throw new Exception( __( 'Payer Authentication failed. Please try a different payment method.','woocommerce-gateway-paypal-pro' ) );
				}

			} else {
				$order->update_status( 'failed', sprintf( __( '3D Secure error: %s', 'woocommerce-gateway-paypal-pro' ), $this->get_centinel_value( "ErrorDesc" ) ) );
				throw new Exception( __( 'Payer Authentication failed. Please try a different payment method.','woocommerce-gateway-paypal-pro' ) );
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
	 * @param object $order
	 * @param object $card
	 * @param object|bool $centinel
	 */
	public function do_payment( $order, $card, $centinel = false ) {
		try {
			$post_data = array(
				'VERSION'           => $this->api_version,
				'SIGNATURE'         => $this->api_signature,
				'USER'              => $this->api_username,
				'PWD'               => $this->api_password,
				'METHOD'            => 'DoDirectPayment',
				'PAYMENTACTION'     => $this->paymentaction,
				'IPADDRESS'         => $this->get_user_ip(),
				'AMT'               => number_format( $order->get_total(), 2, '.', ',' ),
				'INVNUM'            => $order->get_order_number(),
				'CURRENCYCODE'      => $order->get_order_currency(),
				'CREDITCARDTYPE'    => $card->type,
				'ACCT'              => $card->number,
				'EXPDATE'           => $card->exp_month . $card->exp_year,
				'STARTDATE'         => $card->start_month . $card->start_year,
				'CVV2'              => $card->cvc,
				'EMAIL'             => $order->billing_email,
				'FIRSTNAME'         => $order->billing_first_name,
				'LASTNAME'          => $order->billing_last_name,
				'STREET'            => trim( $order->billing_address_1 . ' ' . $order->billing_address_2 ),
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
					$fee_total = 0;

					foreach ( $order->get_items() as $item ) {
						$_product = $order->get_product_from_item( $item );

						if ( $item['qty'] ) {
							$item_name = $item['name'];
							$item_meta = new WC_Order_Item_Meta( $item );

							if ( $meta = $item_meta->display( true, true ) ) {
								$item_name .= ' ( ' . $meta . ' )';
							}

							$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
							$post_data[ 'L_NAME' . $item_loop ]   = $item_name;
							$post_data[ 'L_AMT' . $item_loop ]    = $order->get_item_subtotal( $item, false );
							$post_data[ 'L_QTY' . $item_loop ]    = $item['qty'];

							$ITEMAMT += $order->get_item_total( $item, true ) * $item['qty'];
							$item_loop++;
						}
					}

					// Fees
					foreach ( $order->get_fees() as $fee ) {
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ] = trim( substr( $fee['name'], 0, 127 ) );
						$post_data[ 'L_AMT' . $item_loop ] = $fee['line_total'];
						$post_data[ 'L_QTY' . $item_loop ] = 1;

						$ITEMAMT += $fee['line_total'];
						$fee_total += $fee['line_total'];

						$item_loop++;
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
					if ( $order->get_total_discount() > 0 ) {
						$post_data[ 'L_NUMBER' . $item_loop ] = $item_loop;
						$post_data[ 'L_NAME' . $item_loop ]   = 'Order Discount';
						$post_data[ 'L_AMT' . $item_loop ]    = '-' . round( $order->get_total_discount(), 2 );
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

					$post_data['ITEMAMT'] = round( ( $order->get_subtotal() + $order->get_total_shipping() + $fee_total ) - $order->get_total_discount(), 2 );
					$post_data['TAXAMT']  = round( $order->get_total_tax(), 2 );
				}
			}

			if ( $this->debug ) {
				$log         = $post_data;
				$log['ACCT'] = '****';
				$log['CVV2'] = '****';
				$this->log( 'Do payment request ' . print_r( $log, true ) );
			}

			/* 3D Secure */
			if ( $centinel ) {
				$post_data['AUTHSTATUS3DS']  = $centinel->paresstatus;
				$post_data['MPIVENDOR3DS'] 	 = $centinel->enrolled;
				$post_data['CAVV'] 			 = $centinel->cavv;
				$post_data['ECI3DS'] 		 = $centinel->eciflag;
				$post_data['XID'] 			 = $centinel->xid;
			}

			$response = wp_safe_remote_post( $this->testmode ? $this->testurl : $this->liveurl, array(
   				'method'		=> 'POST',
				'headers'       => array(
					'PAYPAL-NVP' => 'Y'
				),
    			'body' 			=> apply_filters( 'woocommerce-gateway-paypal-pro_request', $post_data, $order ),
    			'timeout' 		=> 70,
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
					$txn_id         = ( ! empty( $parsed_response['TRANSACTIONID'] ) ) ? wc_clean( $parsed_response['TRANSACTIONID'] ) : '';
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
					if ( ! empty( $parsed_response['L_LONGMESSAGE0'] ) ) {
						$error_message = $parsed_response['L_LONGMESSAGE0'];
					} elseif ( ! empty( $parsed_response['L_SHORTMESSAGE0'] ) ) {
						$error_message = $parsed_response['L_SHORTMESSAGE0'];
					} elseif ( ! empty( $parsed_response['L_SEVERITYCODE0'] ) ) {
						$error_message = $parsed_response['L_SEVERITYCODE0'];
					} elseif ( $this->testmode ) {
						$error_message = print_r( $parsed_response, true );
					}

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

		$response = wp_safe_remote_post( $url, array(
			'method'      => 'POST',
			'headers'     => array(
				'PAYPAL-NVP' => 'Y'
			),
			'body'        => $post_data,
			'timeout'     => 70,
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
		return WC_Geolocation::get_ip_address();
	}

	/**
	 * clear_centinel_session function.
	 */
	public function clear_centinel_session() {
		WC()->session->set( 'Centinel_ErrorNo', null );
		WC()->session->set( 'Centinel_ErrorDesc', null );
		WC()->session->set( 'Centinel_TransactionId', null );
		WC()->session->set( 'Centinel_OrderId', null );
		WC()->session->set( 'Centinel_Enrolled', null );
		WC()->session->set( 'Centinel_ACSUrl', null );
		WC()->session->set( 'Centinel_Payload', null );
		WC()->session->set( 'Centinel_EciFlag', null );
		WC()->session->set( 'Centinel_card_start_month', null );
		WC()->session->set( 'Centinel_card_start_year', null );
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
