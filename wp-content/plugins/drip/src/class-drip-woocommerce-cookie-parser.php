<?php
/**
 * Cookie parsing
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Cookie parsing
 */
class Drip_Woocommerce_Cookie_Parser {
	/**
	 * The cookie string
	 *
	 * @var string
	 */
	private $cookie_string;

	/**
	 * Constructor
	 *
	 * @param string $cookie_string The cookie string.
	 */
	public function __construct( $cookie_string ) {
		$this->cookie_string = $cookie_string;
	}

	/**
	 * Obtain visitor id using key
	 *
	 * @return string|null
	 */
	public function get_vid() {
		$drip_cookie_array = $this->drip_cookie_array();

		foreach ( $drip_cookie_array as $cookie ) {
			if ( strpos( $cookie, '=' ) === false ) {
				continue;
			}
			list($key, $value) = explode( '=', $cookie, 2 );
			if ( 'vid' === $key ) {
				return $value;
			}
		}
	}

	/**
	 * Parsed cookie string
	 */
	private function drip_cookie_string() {
		return empty( $this->cookie_string ) ? '' : urldecode( $this->cookie_string );
	}

	/**
	 * Split parsed string into array
	 */
	private function drip_cookie_array() {
		if ( empty( $this->drip_cookie_string() ) ) {
			return array();
		}

		return explode( '&', $this->drip_cookie_string() );
	}
}
