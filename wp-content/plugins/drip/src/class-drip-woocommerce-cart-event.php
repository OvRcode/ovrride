<?php
/**
 * Value object for cart events
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Value object for cart events
 */
class Drip_Woocommerce_Cart_Event {
	/**
	 * Event action
	 *
	 * @var string
	 */
	public $event_action;

	/**
	 * Customer email
	 *
	 * @var string
	 */
	public $customer_email;

	/**
	 * Visitor UUID
	 *
	 * @var string
	 */
	public $visitor_uuid;

	/**
	 * Session
	 *
	 * @var string
	 */
	public $session;

	/**
	 * Grand total
	 *
	 * @var float
	 */
	public $grand_total;

	/**
	 * Total discounts
	 *
	 * @var float
	 */
	public $total_discounts;

	/**
	 * Total taxes
	 *
	 * @var float
	 */
	public $total_taxes;

	/**
	 * Total fees
	 *
	 * @var float
	 */
	public $total_fees;

	/**
	 * Total shipping
	 *
	 * @var float
	 */
	public $total_shipping;

	/**
	 * Currency
	 *
	 * @var string
	 */
	public $currency;

	/**
	 * Cart data
	 *
	 * @var array
	 */
	public $cart_data = array();

	/**
	 * Occurred at (timestamp of the event)
	 *
	 * @var string
	 */
	public $occurred_at;
}
