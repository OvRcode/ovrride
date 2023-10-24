<?php
/**
 * Value object for cart event products
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Value object for cart event products
 */
class Drip_Woocommerce_Cart_Event_Product {
	/**
	 * Product ID
	 *
	 * @var string
	 */
	public $product_id;

	/**
	 * Product Variant ID
	 *
	 * @var string
	 */
	public $product_variant_id;

	/**
	 * Taxes
	 *
	 * @var float
	 */
	public $taxes;

	/**
	 * Total
	 *
	 * @var float
	 */
	public $total;

	/**
	 * Quantity
	 *
	 * @var int
	 */
	public $quantity;
}
