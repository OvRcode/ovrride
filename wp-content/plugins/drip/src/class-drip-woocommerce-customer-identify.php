<?php
/**
 * Injects JS script to make a call to identify a customer during checkout
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Injects Drip JS snippet
 */
class Drip_Woocommerce_Customer_Identify {
	/**
	 * Set up component
	 */
	public static function init() {
		add_action( 'woocommerce_thankyou', __CLASS__ . '::exec' );
	}

	/**
	 * Set up to render JS snippet
	 *
	 * @param int $order_id the order ID.
	 */
	public static function exec( $order_id ) {
		if ( self::not_integrated() ) {
			return; }

		$order = wc_get_order( $order_id );
		if ( $order && $order->get_billing_email( 'edit' ) ) {
			$dwci = new Drip_Woocommerce_Customer_Identify();
			$dwci->render( $order->get_billing_email( 'edit' ) );
		}
	}

	/**
	 * See if the account id is preesent from the woocommerce settings
	 */
	public static function not_integrated() {
		return ! (bool) WC_Admin_Settings::get_option( Drip_Woocommerce_Settings::ACCOUNT_ID_KEY );
	}

	/**
	 * Render js snippet based on the customer's e-mail
	 *
	 * @param string $customer_email the customer e-mail.
	 */
	public function render( $customer_email ) {
		if ( $customer_email ) {
			$customer_email     = is_email( trim( $customer_email ) );
			$drip_identify_data = array(
				'found' => (bool) $customer_email,
				'id'    => (string) $customer_email,
			);

			if ( $drip_identify_data['found'] ) {
				$this->scriptinator( $drip_identify_data );
			}
		}
	}

	/**
	 * Generates the wp script handle
	 *
	 * @return string the unique handle for the customer identify script
	 */
	private function script_handle() {
		return 'Drip customer identify';
	}

	/**
	 * Generate the url for the script
	 *
	 * @return string the url to the customer_identify.js script
	 */
	private function script_url() {
		return (string) plugin_dir_url( __FILE__ ) . 'customer_identify.js';
	}

	/**
	 * Generate the lineline script that passes data to the customer identify js
	 *
	 * @param array $data contains the data necessary for customer_identify.js.
	 * @return string javascript that will be added to the wp page.
	 */
	private function inline_js( $data ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		return 'var drip_woocommerce_identify_data=' . json_encode( $data ) . ';';
	}

	/**
	 * Register, enqueue, and inline the customer identification scripts
	 *
	 * @param array $data contains the data necessary for customer_identify.js.
	 */
	private function scriptinator( $data ) {
		$deps = array();
		// phpcs:disable
		// the following lines are designed to allow browser caching, but PHPCompatibilyWP
		// whines about it, and if I'm going to have to put in multiple comment lines,
		// *I AM* going to whine!
		wp_register_script( $this->script_handle(), $this->script_url(), $deps );
		wp_enqueue_script( $this->script_handle(), $this->script_url(), $deps, null, true );
		wp_add_inline_script( $this->script_handle(), $this->inline_js( $data ), 'before' );
		// phpcs:enable
	}
}
