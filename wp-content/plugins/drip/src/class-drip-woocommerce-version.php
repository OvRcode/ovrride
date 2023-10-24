<?php
/**
 * Injects Drip plugin version header into relevant webhooks.
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

require_once ABSPATH . 'wp-admin/includes/plugin.php';

/**
 * Injects Drip plugin version header into relevant webhooks.
 */
class Drip_Woocommerce_Version {
	const HEADER = 'X-WC-Drip-Version';

	/**
	 * Set up component
	 */
	public static function init() {
		add_filter( 'woocommerce_webhook_http_args', __CLASS__ . '::add_version_header', 50, 3 );
	}

	/**
	 * Callback for woocommerce_webhook_http_args filter
	 *
	 * @param  array  $http_args argument for the http request.
	 * @param  mixed  $_arg      unused.
	 * @param string $this_id   the ID of the webhook.
	 */
	public static function add_version_header( $http_args, $_arg, $this_id ) {
		$webhook_name = ( new WC_Webhook( $this_id ) )->get_name();
		if ( strpos( strtolower( $webhook_name ), 'drip' ) !== false ) {
			$version = self::my_plugin_data( 'Version' );
			if ( $version ) {
				$http_args['headers'][ self::HEADER ] = $version;
			}
		}
		return $http_args;
	}

	/**
	 * Get data out of plugin frontmatter.
	 *
	 * @param string $attribute a plugin attribute.
	 */
	private static function my_plugin_data( $attribute ) {
		$path        = realpath( __DIR__ . '/../drip.php' );
		$plugin_data = get_plugin_data( $path );
		return trim( $plugin_data[ $attribute ] );
	}
}
