<?php
/**
 * Class to handle filtering fields in the checkout billing form.
 *
 * @see     https://docs.woocommerce.com/document/tutorial-customising-checkout-fields-using-actions-and-filters/
 * @author  Michael Beckwith <michael@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-08-22
 */

namespace WebDevStudios\CCForWoo\View\Checkout;

use WebDevStudios\OopsWP\Utility\Hookable;

/**
 * Class CampaignId
 *
 * @author  Michael Beckwith <michael@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\View\Checkout
 * @since   2019-08-22
 */
class CampaignId implements Hookable {
	/**
	 * The name of the meta field for the customer's preference.
	 * This constant will be used both in usermeta (for users) and postmeta (for orders).
	 *
	 * @var string
	 * @since 2019-08-22
	 */
	const CUSTOMER_CAMPAIGN_ID_KEY = 'campaign_activity_id';

	/**
	 * Register actions and filters with WordPress.
	 *
	 * @author Michael Beckwith <michael@webdevstudios.com>
	 * @since  2019-08-22
	 */
	public function register_hooks() {
		add_action( 'init', [ $this, 'save_campaign_id' ], 11 );
		add_action( 'woocommerce_checkout_update_order_meta', [ $this, 'save_user_campaign_id_to_order' ] );
	}

	/**
	 * Save the user preference to the order meta.
	 *
	 * @param int $order_id The order ID.
	 *
	 * @author Michael Beckwith <michael@webdevstudios.com>
	 * @since  2019-08-22
	 * @return void
	 */
	public function save_user_campaign_id_to_order( $order_id ) {
		$preference = $this->get_stored_campaign_id();

		if ( empty( $preference ) ) {
			return;
		}

		add_post_meta( $order_id, self::CUSTOMER_CAMPAIGN_ID_KEY, $preference, true );
	}

	/**
	 * Save the campaign ID for the session.
	 *
	 * @throws \Exception DateTime exception.
	 *
	 * @author Michael Beckwith <michael@webdevstudios.com>
	 * @since  2019-08-22
	 * @return void
	 */
	public function save_campaign_id() {
		$campaign_id = filter_input( INPUT_GET, 'source', FILTER_SANITIZE_STRING );

		if ( ! empty( $campaign_id ) ) {
			setcookie( 'ctct_woo_campaign_id', $campaign_id, 0, '/' );
		}
	}

	/**
	 * Get the submitted customer newsletter preference.
	 *
	 * @author Michael Beckwith <michael@webdevstudios.com>
	 * @since  2019-08-22
	 * @return string
	 */
	private function get_stored_campaign_id() {
		return isset( $_COOKIE['ctct_woo_campaign_id'] )
			? sanitize_text_field( $_COOKIE['ctct_woo_campaign_id'] )
			: '';
	}
}
