<?php
/**
 * Cart event handling
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

require_once __DIR__ . '/class-drip-woocommerce-cart-event.php';
require_once __DIR__ . '/class-drip-woocommerce-cart-event-product.php';
require_once __DIR__ . '/class-drip-woocommerce-cookie-parser.php';

/**
 * Cart event handling
 */
class Drip_Woocommerce_Cart_Events {
	const CART_SESSION_KEY    = 'drip_cart_session_id';
	const CART_UPDATED_ACTION = 'updated';

	/**
	 * Set up component
	 */
	public static function init() {
		$cart_events = new Drip_Woocommerce_Cart_Events();
		$cart_events->setup_cart_actions();
	}

	/**
	 * Set up cart actions ensuring that actions will be registered after WordPress has fully loaded
	 */
	public function setup_cart_actions() {
		add_action('wp_loaded', array($this, 'register_cart_actions'));
	}
	
	/**
	 * Register cart action callbacks
	 */
	public function register_cart_actions() {
		add_action('woocommerce_after_cart_item_quantity_update', array($this, 'drip_woocommerce_cart_updated'), 10, 0);
		add_action('woocommerce_cart_item_removed', array($this, 'drip_woocommerce_cart_updated'), 10, 0);
		add_action('woocommerce_add_to_cart', array($this, 'drip_woocommerce_cart_updated'), 10, 0);
		add_action('woocommerce_cart_item_restored', array($this, 'drip_woocommerce_cart_updated'), 10, 0);
		add_action('woocommerce_cart_emptied', array($this, 'drip_woocommerce_cart_updated'), 10, 0);
	}

	/**
	 * Callback for cart actions
	 */
	public function drip_woocommerce_cart_updated() {
		if ( $this->user_invalid() && is_null( $this->find_drip_visitor_uuid() ) ) {
			return;
		}

		$event = $this->base_event();

		$cart_contents = WC()->cart->get_cart();
		foreach ( $cart_contents as $product_id => $cart_item_info ) {
			$product_event_data = $this->product_event_data( $cart_item_info );
			if ( $product_event_data ) {
				$event->cart_data[] = $product_event_data;
			}
		}
		// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound, WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		do_action( 'wc_drip_woocommerce_cart_event', $event );

		if ( WC()->cart->is_empty() ) {
			$this->remove_drip_cart_session_id();
		}
	}

	/**
	 * Create a basic event
	 */
	private function base_event() {
		WC()->cart->calculate_totals();

		$event               = new Drip_Woocommerce_Cart_Event();
		$event->event_action = self::CART_UPDATED_ACTION; // TODO: we can trap cart created events if we have to generate a new cart session id.
		if ( $this->user_invalid() ) {
			$event->visitor_uuid = $this->find_drip_visitor_uuid();
		} else {
			$event->customer_email = wp_get_current_user()->user_email;
		}
		$event->session         = $this->drip_cart_session_id();
		$event->grand_total     = WC()->cart->get_total( 'edit' );
		$event->total_discounts = WC()->cart->get_discount_total( 'edit' );
		$event->total_taxes     = WC()->cart->get_total_tax( 'edit' );
		$event->total_fees      = WC()->cart->get_fee_total( 'edit' );
		$event->total_shipping  = WC()->cart->get_shipping_total( 'edit' );
		$event->currency        = get_option( 'woocommerce_currency' );
		$event->occurred_at     = $this->current_time();
		return $event;
	}

	/**
	 * Set up product event data
	 *
	 * @param array $cart_item_info information about the cart.
	 */
	private function product_event_data( $cart_item_info ) {
		$product_key  = $this->product_key( $cart_item_info );
		$product_data = WC()->product_factory->get_product( $product_key );

		if ( ! $product_data ) {
			return false;
		}

		$cart_event_product                     = new Drip_Woocommerce_Cart_Event_Product();
		$cart_event_product->product_id         = $cart_item_info['product_id'];
		$cart_event_product->product_variant_id = $cart_item_info['variation_id'];
		$cart_event_product->taxes              = $cart_item_info['line_tax'];
		$cart_event_product->total              = $cart_item_info['line_total'];
		$cart_event_product->quantity           = $cart_item_info['quantity'];

		return $cart_event_product;
	}

	/**
	 * Check for user validity
	 */
	private function user_invalid() {
		$wp_user = wp_get_current_user();
		if ( $wp_user && ! empty( $wp_user->user_email ) ) {
			return false;
		}
		return true; // no way to identify a Drip person.
	}

	/**
	 * Get the product's key
	 *
	 * @param array $cart_product_info information about the cart.
	 */
	private function product_key( $cart_product_info ) {
		return empty( $cart_product_info['variation_id'] ) ? $cart_product_info['product_id'] : $cart_product_info['variation_id'];
	}

	/**
	 * Obtain categories for product
	 *
	 * @param mixed $product_data Product data.
	 */
	private function product_categories( $product_data ) {
		$categories = array();
		foreach ( $product_data->get_category_ids() as $cid ) {
			$woo_category = get_term_by( 'id', absint( $cid ), 'product_cat' );
			array_push( $categories, $woo_category->name );
		}
		return $categories;
	}

	/**
	 * Get the drip cart id from the session
	 */
	private function drip_cart_session_id() {
		$cid = WC()->session->get( self::CART_SESSION_KEY, false );
		if ( $cid ) {
			return $cid;
		}
		$cid = $this->generate_drip_cart_session_id();
		WC()->session->set( self::CART_SESSION_KEY, $cid );
		return $cid;
	}

	/**
	 * Unset the drip cart id from the session
	 */
	private function remove_drip_cart_session_id() {
		WC()->session->__unset( self::CART_SESSION_KEY );
	}

	/**
	 * Generate a drip cart id in the session
	 */
	private function generate_drip_cart_session_id() {
		$random_data = random_bytes( 32 ); // as of php7, random_bytes is advertised as cryptographically secure.
		return hash( 'sha256', $random_data );
	}


	/**
	 * Retrieve Drip visitor_uuid from cookie
	 */
	private function find_drip_visitor_uuid() {
		$account_id = WC_Admin_Settings::get_option( Drip_Woocommerce_Settings::ACCOUNT_ID_KEY );
		if ( empty( $account_id ) || empty( $_COOKIE[ "_drip_client_{$account_id}" ] ) ) {
			return;
		}

		$parser = new Drip_Woocommerce_Cookie_Parser( wp_kses_data( wp_unslash( $_COOKIE[ "_drip_client_{$account_id}" ] ) ) );

		return $parser->get_vid();
	}

	/**
	 * Retrieve the current date & time in ISO8601 format
	 **/
	private function current_time()
	{
		$time_obj = new DateTime('NOW');

		return $time_obj->format('c');
	}
}
