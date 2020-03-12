<?php
/**
 * WooCommerce Compatibility Class
 *
 * Tests to see if WooCommerce is available and compatible.
 *
 * @since 0.0.1
 * @author Zach Owen <zach@webdevstudios.com>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo\Utility;

/**
 * Tests if WooCommerce is available and compatible.
 *
 * @since 0.0.1
 */
class PluginCompatibilityCheck {
	/**
	 * The minimum WooCommerce version.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const MINIMUM_WOO_VERSION = '3.5.4';

	/**
	 * The classname we'll be using for compatibility testing.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $classname = '';

	/**
	 * Construct our compatibility checker with the main plugin class.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $classname The classname to use for testing.
	 */
	public function __construct( string $classname ) {
		$this->classname = $classname;
	}

	/**
	 * Check whether WooCommerce is available.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @return bool
	 */
	public function is_available() : bool {
		return class_exists( $this->classname );
	}

	/**
	 * Check whether WooCommerce is compatible
	 *
	 * @since  0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 *
	 * @param \WooCommerce $woocommerce An instance of the WooCommerce class.
	 *
	 * @return bool
	 */
	public function is_compatible( \WooCommerce $woocommerce ) : bool {
		return 0 >= version_compare( self::MINIMUM_WOO_VERSION, $woocommerce->version );
	}
}
