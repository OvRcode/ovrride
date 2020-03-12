<?php
/**
 * Class to handle recovery of abandoned carts via URL.
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\AbandonedCarts;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * Class CartRecovery
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Database
 * @since   1.2.0
 */
class CartRecovery extends Service {

	/**
	 * Current cart hash key string.
	 *
	 * @var string
	 * @since  1.2.0
	 */
	protected $cart_hash = '';

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @return void
	 */
	public function register_hooks() {
		// Sanitize cart hash key string.
		$this->cart_hash = filter_input( INPUT_GET, 'recover-cart', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES );

		if ( empty( $this->cart_hash ) ) {
			return;
		}

		add_action( 'wp_loaded', [ $this, 'recover_cart' ] );
	}

	/**
	 * Generate cart recovery URL.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  int $cart_id ID of abandoned cart.
	 * @return string       Cart recovery URL on successful retrieval (void on failure).
	 */
	public function get_cart_url( int $cart_id ) {
		return add_query_arg(
			'recover-cart',
			CartHandler::get_cart_hash( $cart_id ),
			get_site_url()
		);
	}

	/**
	 * Recovery saved cart from hash key.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @return void
	 */
	public function recover_cart() {
		// Clear current cart contents.
		WC()->cart->empty_cart();

		// Get saved cart contents.
		$cart_contents = CartHandler::get_cart_contents( $this->cart_hash );

		if ( null === $cart_contents ) {
			return;
		}

		// Recover saved products.
		$this->recover_products( $cart_contents['products'] );

		// Apply coupons.
		foreach ( $cart_contents['coupons'] as $coupon ) {
			WC()->cart->apply_coupon( $coupon );
		}

		// Update customer info.
		$this->recover_customer_info( $cart_contents['customer'], 'billing' );
		$this->recover_customer_info( $cart_contents['customer'], 'shipping' );

		// Apply shipping method.
		WC()->session->set( 'chosen_shipping_methods', $cart_contents['shipping_method'] );

		// Update totals.
		WC()->cart->calculate_totals();
		WC()->cart->calculate_shipping();

		// Redirect to cart page.
		wp_safe_redirect( wc_get_page_permalink( 'cart' ) );
		exit();
	}

	/**
	 * Recover products from saved cart data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  array $products Array of product data.
	 */
	protected function recover_products( $products ) {
		// Programmatically add each product to cart.
		$products_added = [];
		foreach ( $products as $product ) {
			$added = WC()->cart->add_to_cart(
				$product['product_id'],
				$product['quantity'],
				empty( $product['variation_id'] ) ? 0 : $product['variation_id'],
				empty( $product['variation'] ) ? array() : $product['variation']
			);
			if ( false !== $added ) {
				$products_added[ ( empty( $product['variation_id'] ) ? $product['product_id'] : $product['variation_id'] ) ] = $product['quantity'];
			}
		}

		// Add product notices.
		if ( 0 < count( $products_added ) ) {
			wc_add_to_cart_message( $products_added );
		}
		if ( count( $products ) > count( $products_added ) ) {
			wc_add_notice(
				sprintf(
					/* translators: %d item count */
					_n(
						'%d item from your previous order is currently unavailable and could not be added to your cart.',
						'%d items from your previous order are currently unavailable and could not be added to your cart.',
						( count( $products ) - count( $products_added ) ),
						'cc-woo'
					),
					( count( $products ) - count( $products_added ) )
				),
				'error'
			);
		}
	}

	/**
	 * Recover and apply customer billing and shipping info from saved cart data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  array  $customer_info Array of customer billing and shipping info.
	 * @param  string $type          Type of customer info to recover (billing or shipping).
	 */
	protected function recover_customer_info( $customer_info, string $type ) {
		foreach ( $customer_info[ $type ] as $key => $value ) {
			call_user_func(
				[ WC()->customer, "set_{$type}_{$key}" ],
				$value
			);
		}
	}
}
