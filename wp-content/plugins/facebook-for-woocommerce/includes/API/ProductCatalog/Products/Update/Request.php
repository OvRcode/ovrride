<?php
declare( strict_types=1 );

namespace WooCommerce\Facebook\API\ProductCatalog\Products\Update;

use WooCommerce\Facebook\API\Request as ApiRequest;

defined( 'ABSPATH' ) || exit;

/**
 * Request object for Product Catalog > Product Groups > Products > Update Graph Api.
 *
 * @link https://developers.facebook.com/docs/marketing-api/reference/product-group/products/
 */
class Request extends ApiRequest {

	/**
	 * @param string $facebook_product_id Facebook Product ID.
	 * @param array  $data Facebook Product Data.
	 */
	public function __construct( string $facebook_product_id, array $data ) {
		parent::__construct( "/{$facebook_product_id}", 'POST' );
		parent::set_data( $data );
	}
}
