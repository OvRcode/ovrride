<?php
/**
 * Class to handle filtering fields in the checkout billing form.
 *
 * @see https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */

namespace WebDevStudios\CCForWoo\View\Checkout;

use WebDevStudios\CCForWoo\Utility\NonceVerification;
use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class NewsletterPreferenceCheckbox
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-03-13
 */
class NewsletterPreferenceCheckbox implements Hookable {
	use NonceVerification;

	/**
	 * The name of the option for the store's default preference state.
	 *
	 * @var string
	 * @since 2019-03-18
	 */
	const STORE_NEWSLETTER_DEFAULT_OPTION = 'cc_woo_customer_data_email_opt_in_default';

	/**
	 * The name of the meta field for the customer's preference.
	 *
	 * This constant will be used both in usermeta (for users) and postmeta (for orders).
	 *
	 * @var string
	 * @since 2019-03-18
	 */
	const CUSTOMER_PREFERENCE_META_FIELD = 'cc_woo_customer_agrees_to_marketing';

	/**
	 * NewsletterPreferenceCheckbox constructor.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	public function __construct() {
		$this->nonce_name   = 'cc_woo_customer_newsletter_preference';
		$this->nonce_action = 'cc-woo-customer-newsletter-preference-action';
	}

	/**
	 * Register actions and filters with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since 2019-03-13
	 */
	public function register_hooks() {
		add_action( 'woocommerce_after_checkout_billing_form', [ $this, 'add_field_to_billing_form' ] );
		add_action( 'woocommerce_checkout_update_user_meta', [ $this, 'save_user_preference' ] );
		add_action( 'woocommerce_created_customer', [ $this, 'save_user_preference' ] );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_user_preference_to_order' ] );
	}

	/**
	 * Add the newsletter checkbox to the set of fields in the billing form.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 */
	public function add_field_to_billing_form() {
		wp_nonce_field( $this->nonce_action, $this->nonce_name );

		woocommerce_form_field( 'customer_newsletter_opt_in', [
			'custom_attributes' => [
				'name' => 'cc_woo_customer_newsletter_preference',
			],
			'type'              => 'checkbox',
			'class'             => [ 'input-checkbox' ],
			'label'             => esc_html__( 'Sign me up to receive marketing emails', 'cc-woo' ),
		], $this->get_default_checked_state() );
	}

	/**
	 * Get the default state of the newsletter opt-in checkbox.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_default_checked_state() : bool {
		return is_user_logged_in() ? $this->get_user_default_checked_state() : $this->get_store_default_checked_state();
	}

	/**
	 * Get the default checkbox state from a user's preferences.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_user_default_checked_state() : bool {
		$user_preference = get_user_meta( get_current_user_id(), self::CUSTOMER_PREFERENCE_META_FIELD, true );

		return ! empty( $user_preference ) ? 'true' === $user_preference : $this->get_store_default_checked_state();
	}

	/**
	 * Get the store's default checkbox state.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return bool
	 */
	private function get_store_default_checked_state() : bool {
		return 'true' === get_option( self::STORE_NEWSLETTER_DEFAULT_OPTION );
	}

	/**
	 * Save the user's newsletter preferences to meta.
	 *
	 * @param int $user_id ID of the user.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-13
	 * @return void
	 */
	public function save_user_preference( $user_id ) {
		if ( ! $user_id ) {
			return;
		}

		$preference = $this->get_submitted_customer_preference();

		if ( empty( $preference ) ) {
			return;
		}

		update_user_meta( $user_id, self::CUSTOMER_PREFERENCE_META_FIELD, $preference );
	}

	/**
	 * Save the user preference to the order meta.
	 *
	 * @param int $order_id The order ID.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 * @return void
	 */
	public function save_user_preference_to_order( $order_id ) {
		$preference = $this->get_submitted_customer_preference();

		if ( empty( $preference ) ) {
			return;
		}

		add_post_meta( $order_id, self::CUSTOMER_PREFERENCE_META_FIELD, $preference, true );
	}

	/**
	 * Get the submitted customer newsletter preference.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 * @return string
	 */
	private function get_submitted_customer_preference() {
		if ( ! $this->has_valid_nonce() ) {
			return '';
		}

		// @codingStandardsIgnoreStart - Nonce verification in guard clause.
		return isset( $_POST['customer_newsletter_opt_in'] ) && 1 === filter_var( $_POST['customer_newsletter_opt_in'], FILTER_VALIDATE_INT )
			? 'true'
			: 'false';
		// @codingStandardsIgnoreEnd
	}
}
