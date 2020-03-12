<?php
/**
 * Class for managing the Constant Contact <-> WooCommerce API key.
 *
 * @since 2019-03-21
 * @package cc-woo-api
 */

namespace WebDevStudios\CCForWoo\Api;

use WebDevStudios\OopsWP\Structure\Service;

/**
 * KeyManager class
 *
 * @uses Hookable
 * @since 2019-03-21
 * @author Zach Owen <zach@webdevstudios>
 */
class KeyManager extends Service {
	/**
	 * Register hooks with WordPress
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function register_hooks() {
		add_action( 'admin_init', function() {
			add_filter( 'query', [ $this, 'maybe_revoke_api_key' ] );
		} );

		add_action( 'cc_woo_key_revoked', [ $this, 'disconnect_cc_woo' ] );
	}

	/**
	 * Check the database query to see if we're removing a Woo API key.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $query Database query.
	 * @return string
	 */
	public function maybe_revoke_api_key( string $query ) : string {
		if ( ! $this->is_cc_api_revocation_query( $query ) ) {
			return $query;
		}

		if ( ! $this->user_has_cc_key() ) {
			return $query;
		}

		/**
		 * Fires when a WooCommerce API key is revoked.
		 *
		 * @since 2019-03-21
		 */
		do_action( 'cc_woo_key_revoked' );

		return $query;
	}

	/**
	 * Check whether the query meets our criteria.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $query The database query.
	 * @return bool
	 */
	private function is_cc_api_revocation_query( string $query ) : bool {
		if ( ! $this->is_delete_query( $query ) ) {
			return false;
		}

		if ( ! $this->is_woo_commerce_api_key_query( $query ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Check if the current user has a Constant Contact API key.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	private function user_has_cc_key() : bool {
		$user_id = get_current_user_id();

		if ( ! $user_id ) {
			return false;
		}

		$query = <<<SQL
SELECT
	key_id
FROM
{$GLOBALS['wpdb']->prefix}woocommerce_api_keys
WHERE
	user_id = %d
AND
	(
		description LIKE '%Constant Contact%'
	OR
		description LIKE '%ConstantContact%'
	)
SQL;

		return ! empty( $GLOBALS['wpdb']->get_col( $GLOBALS['wpdb']->prepare( $query, $user_id ) ) );
	}

	/**
	 * Check if the query is a DELETE query.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $query The query to test.
	 * @return bool
	 */
	private function is_delete_query( string $query ) {
		return false !== stripos( $query, 'DELETE' );
	}

	/**
	 * Check if the query is hitting Woo's API key table.
	 *
	 * @since 2019-03-21
	 * @author Zach Owen <zach@webdevstudios>
	 * @param string $query The query to test.
	 * @return bool
	 */
	private function is_woo_commerce_api_key_query( $query ) {
		return false !== stripos( $query, 'woocommerce_api_keys' );
	}

	/**
	 * Alert CTCT that the API key has been revoked.
	 *
	 * @author Zach Owen <zach@webdevstudios>
	 * @since 2019-05-22
	 */
	public function disconnect_cc_woo() {
		do_action( 'cc_woo_disconnect', esc_html__( 'REST API Key Revoked.', 'cc-woo' ) );
		do_action( 'wc_ctct_disconnect' );
	}
}
