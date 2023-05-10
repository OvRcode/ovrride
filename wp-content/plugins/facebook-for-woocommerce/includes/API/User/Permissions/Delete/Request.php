<?php
declare( strict_types=1 );

namespace WooCommerce\Facebook\API\User\Permissions\Delete;

defined( 'ABSPATH' ) || exit;

use WooCommerce\Facebook\API;

/**
 * Request object for the Business Manager API.
 *
 * @since 2.0.0
 */
class Request extends API\Request {

	/**
	 * API request constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $user_id user ID
	 * @param string $permission permission to revoke
	 */
	public function __construct( $user_id, $permission ) {
		parent::__construct( "/{$user_id}/permissions/{$permission}", 'DELETE' );
	}
}
