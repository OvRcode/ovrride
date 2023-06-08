<?php
declare( strict_types=1 );

namespace WooCommerce\Facebook\API\ProductCatalog\ProductGroups\Create;

use WooCommerce\Facebook\API\Request as ApiRequest;

defined( 'ABSPATH' ) || exit;

/**
 * Request object for Product Catalog > Product Groups > Create Graph Api.
 *
 * @link https://developers.facebook.com/docs/marketing-api/reference/product-catalog/product_groups/#Creating
 */
class Request extends ApiRequest {

	/**
	 * @param string $product_catalog_id Facebook Product Catalog ID.
	 * @param array  $data Facebook Product Group Data.
	 */
	public function __construct( string $product_catalog_id, array $data ) {
		parent::__construct( "/{$product_catalog_id}/product_groups", 'POST' );
		parent::set_data( $data );
	}
}
