<?php
/**
 * Class to listen to WooCommerce checkouts and possibly store carts that are "abandoned".
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCarts
 * @since   1.2.0
 */

namespace WebDevStudios\CCForWoo\AbandonedCarts;

use WebDevStudios\OopsWP\Structure\Service;
use WC_Customer;
use WC_Order;
use DateTime;
use DateInterval;

/**
 * Class CartHandler
 *
 * @author  Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\AbandonedCarts
 * @since   1.2.0
 */
class CartHandler extends Service {

	/**
	 * Register hooks with WordPress.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function register_hooks() {
		add_action( 'woocommerce_before_checkout_form', [ $this, 'enqueue_scripts' ] );
		add_action( 'woocommerce_after_template_part', [ $this, 'save_or_clear_cart_data' ], 10, 4 );
		add_action( 'woocommerce_checkout_process', [ $this, 'update_cart_data' ] );
		add_action( 'cc_woo_check_expired_carts', [ $this, 'delete_expired_carts' ] );
		add_action( 'woocommerce_calculate_totals', [ $this, 'update_cart_data' ] );
		add_action( 'woocommerce_cart_item_removed', [ $this, 'update_cart_data' ] );

		add_action( 'wp_ajax_cc_woo_abandoned_carts_capture_guest_cart', [ $this, 'maybe_capture_guest_checkout' ] );
		add_action( 'wp_ajax_nopriv_cc_woo_abandoned_carts_capture_guest_cart', [ $this, 'maybe_capture_guest_checkout' ] );
	}


	/**
	 * Load front-end scripts.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function enqueue_scripts() {
		if ( is_user_logged_in() ) {
			return;
		}

		wp_enqueue_script( 'cc-woo-public' );
	}

	/**
	 * AJAX handler for attempting to capture guest checkouts.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function maybe_capture_guest_checkout() {
		$data = filter_input_array( INPUT_POST, [
			'nonce' => FILTER_SANITIZE_STRING,
			'email' => FILTER_SANITIZE_EMAIL,
		] );

		if ( empty( $data['nonce'] ) || ! wp_verify_nonce( $data['nonce'], 'woocommerce-process_checkout' ) ) {
			wp_send_json_error( esc_html__( 'Invalid nonce.', 'cc-woo' ) );
		}

		if ( ! filter_var( $data['email'], FILTER_VALIDATE_EMAIL ) ) {
			wp_send_json_error( esc_html__( 'Invalid email.', 'cc-woo' ) );
		}

		$this->update_cart_data( $data['email'] );

		wp_send_json_success();
	}

	/**
	 * Either call an update of cart data which will be saved or remove cart data based on what template we arrive at.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  string $template_name Current template file name.
	 * @param  string $template_path Current template path.
	 * @param  string $located       Full local path to current template file.
	 * @param  array  $args          Template args.
	 */
	public function save_or_clear_cart_data( $template_name, $template_path, $located, $args ) {
		// If checkout page displayed, save cart data.
		if ( 'checkout/form-checkout.php' === $template_name ) {
			$this->update_cart_data();
		}

		// If thankyou page displayed, clear cart data.
		if ( isset( $args['order'] ) && 'checkout/thankyou.php' === $template_name ) {
			$this->clear_purchased_data( $args['order'] );
		}
	}

	/**
	 * Update current cart session data in db.
	 *
	 * Param type "mixed" is specified for $billing_email param here because we cannot type hint this,
	 * as some Woo hooks that this is a callback to will pass unused objects and other data as first param.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 *
	 * @param mixed $billing_email Optional, default empty. A billing email to specify.
	 * @return void
	 */
	public function update_cart_data( $billing_email = '' ) {
		$user_id       = get_current_user_id();
		$customer_data = [
			'billing'  => [],
			'shipping' => [],
		];

		// Get saved customer data if exists. If guest user, blank customer data will be generated.
		$customer = new WC_Customer( $user_id );

		$customer_data['billing']  = $customer->get_billing();
		$customer_data['shipping'] = $customer->get_shipping();

		// Update customer data from user session data.
		$customer_data['billing']  = array_merge( $customer_data['billing'], WC()->customer->get_billing() );
		$customer_data['shipping'] = array_merge( $customer_data['shipping'], WC()->customer->get_shipping() );

		// Check if submission attempted.
		if ( isset( $_POST['billing_email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification -- Okay use of $_POST data.
			// Update customer data from posted data.
			array_walk( $customer_data['billing'], [ $this, 'merge_customer_data' ], 'billing' );
			array_walk( $customer_data['shipping'], [ $this, 'merge_customer_data' ], 'shipping' );
		} else if ( ! empty( $billing_email ) && is_string( $billing_email ) ) {
			$customer_data['billing']['email'] = $billing_email;
		} else {
			// Retrieve cart data for current user, if exists.
			$cart_data = $this::get_cart_data(
				'cart_contents',
				[
					'user_id = %d',
					'user_email = %s',
				],
				[
					$user_id,
					WC()->checkout->get_value( 'billing_email' ),
				]
			);

			if ( null !== $cart_data && ! empty( $cart_data['customer'] ) ) {
				// Update customer data from saved cart data.
				$customer_data['billing']  = array_merge( $customer_data['billing'], $cart_data['customer']['billing'] );
				$customer_data['shipping'] = array_merge( $customer_data['shipping'], $cart_data['customer']['shipping'] );
			}
		}

		if ( empty( $customer_data['billing']['email'] ) ) {
			return;
		}

		// Delete saved cart if cart emptied; update otherwise.
		if ( false === WC()->cart->is_empty() ) {
			$this->save_cart_data( $user_id, $customer_data );
		} else {
			$this->remove_cart_data( $user_id, $customer_data['billing']['email'] );
		}
	}

	/**
	 * Array_walk callback that merges Customer data fields with data from db or session.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  string $value  Value of posted array item.
	 * @param  string $key    Key of posted array item.
	 * @param  string $type   Type of array (billing or shipping).
	 */
	protected function merge_customer_data( &$value, $key, $type ) {
		$posted = WC()->checkout()->get_posted_data();
		$value  = isset( $posted[ "{$type}_{$key}" ] ) ? $posted[ "{$type}_{$key}" ] : $value;
	}

	/**
	 * Retrieve specific user's cart data.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  string $select        Field to return.
	 * @param  mixed  $where         String or array of WHERE clause predicates, using placeholders for values.
	 * @param  array  $where_values  Array of WHERE clause values.
	 * @return string Cart data.
	 */
	public static function get_cart_data( $select, $where, $where_values ) {
		global $wpdb;

		$table_name = CartsTable::get_table_name();
		$where      = is_array( $where ) ? implode( ' AND ', $where ) : $where;

		// Construct query to return cart data.
		// phpcs:disable -- Disabling a number of sniffs that erroneously flag following block of code.
		// $where often includes placeholders for replacement via $wpdb->prepare(). $where_values provides those values.
		return maybe_unserialize(
			$wpdb->get_var(
				$wpdb->prepare(
					"SELECT {$select}
					FROM {$table_name}
					WHERE {$where}",
					$where_values
				)
			)
		);
		// phpcs:enable
	}

	/**
	 * Helper function to retrieve cart contents based on cart hash key.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  int $cart_id ID of abandoned cart.
	 * @return string       Hash key string of abandoned cart.
	 */
	public static function get_cart_hash( int $cart_id ) {
		return self::get_cart_data(
			'cart_hash',
			'cart_id = %d',
			[
				intval( $cart_id ),
			]
		);
	}

	/**
	 * Helper function to retrieve cart contents based on cart hash key.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  string $cart_hash Cart key hash string.
	 * @return array             Cart contents.
	 */
	public static function get_cart_contents( $cart_hash ) {
		return self::get_cart_data(
			'cart_contents',
			'cart_hash = %s',
			[
				$cart_hash,
			]
		);
	}

	/**
	 * Save current cart data to db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  int   $user_id       Current user ID.
	 * @param  array $customer_data Customer billing and shipping data.
	 */
	protected function save_cart_data( $user_id, $customer_data ) {
		global $wpdb;

		$current_time = current_time( 'mysql', 1 );
		$table_name   = CartsTable::get_table_name();

		// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
		$wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$table_name} (
					`user_id`,
					`user_email`,
					`cart_contents`,
					`cart_updated`,
					`cart_updated_ts`,
					`cart_created`,
					`cart_created_ts`,
					`cart_hash`
				) VALUES (
					%d,
					%s,
					%s,
					%s,
					%d,
					%s,
					%d,
					MD5(CONCAT(user_id, user_email))
				) ON DUPLICATE KEY UPDATE `cart_updated` = VALUES(`cart_updated`), `cart_updated_ts` = VALUES(`cart_updated_ts`), `cart_contents` = VALUES(`cart_contents`)",
				$user_id,
				$customer_data['billing']['email'],
				maybe_serialize( [
					'products'        => array_values( WC()->cart->get_cart() ),
					'coupons'         => WC()->cart->get_applied_coupons(),
					'customer'        => $customer_data,
					'shipping_method' => WC()->checkout()->get_posted_data()['shipping_method'],
				] ),
				$current_time,
				strtotime( $current_time ),
				$current_time,
				strtotime( $current_time )
			)
		);
		// phpcs:enable
	}

	/**
	 * Remove current cart session data from db upon successful order submission.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  WC_Order $order Newly submitted order object.
	 * @return void
	 */
	public function clear_purchased_data( $order ) {
		if ( empty( $order ) ) {
			return;
		}

		$this->remove_cart_data( $order->get_user_id(), $order->get_billing_email() );
	}

	/**
	 * Helper function to remove cart session data from db.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 * @param  int    $user_id    ID of cart owner.
	 * @param  string $user_email Email of cart owner.
	 */
	protected function remove_cart_data( $user_id, $user_email ) {
		global $wpdb;

		// Delete current cart data.
		$wpdb->delete(
			CartsTable::get_table_name(),
			[
				'user_id'    => $user_id,
				'user_email' => $user_email,
			],
			[
				'%d',
				'%s',
			]
		);
	}

	/**
	 * Delete expired carts.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since  1.2.0
	 */
	public function delete_expired_carts() {
		global $wpdb;

		// Delete all carts at least 30 days old.
		$table_name = CartsTable::get_table_name();

		$wpdb->query(
			$wpdb->prepare(
				// phpcs:disable WordPress.DB.PreparedSQL -- Okay use of unprepared variable for table name in SQL.
				"DELETE FROM {$table_name}
				WHERE `cart_updated_ts` <= %s",
				// phpcs:enable
				( new DateTime() )->sub( new DateInterval( 'P30D' ) )->format( 'U' )
			)
		);
	}
}
