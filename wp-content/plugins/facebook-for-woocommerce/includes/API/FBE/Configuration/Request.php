<?php
declare( strict_types=1 );

namespace WooCommerce\Facebook\API\FBE\Configuration;

defined( 'ABSPATH' ) || exit;

use WooCommerce\Facebook\API;

/**
 * FBE Configuration API request object.
 */
class Request extends API\Request {
	/**
	 * API request constructor.
	 *
	 * @param string $external_business_id external business ID
	 * @param string $method request method
	 */
	public function __construct( $external_business_id, $method ) {
		parent::__construct( '/fbe_business?fbe_external_business_id=' . $external_business_id, $method );
	}
}
