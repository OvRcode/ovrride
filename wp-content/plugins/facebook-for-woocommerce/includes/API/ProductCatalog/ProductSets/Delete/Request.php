<?php
declare( strict_types=1 );

namespace WooCommerce\Facebook\API\ProductCatalog\ProductSets\Delete;

use WooCommerce\Facebook\API\Request as ApiRequest;

defined( 'ABSPATH' ) || exit;

/**
 * Request object for Product Catalog > Product Sets > Delete Graph Api.
 *
 * @link https://developers.facebook.com/docs/marketing-api/reference/product-catalog/product_sets/
 */
class Request extends ApiRequest {

	/**
	 * @param string $product_set_id Facebook Product Set ID.
	 * @param bool   $allow_live_deletion Allow live Facebook Product Set Deletion.
	 */
	public function __construct( string $product_set_id, bool $allow_live_deletion = false ) {
		$path  = "/{$product_set_id}";
		$path .= $allow_live_deletion ? '?allow_live_product_set_deletion=true' : '';
		parent::__construct( $path, 'DELETE' );
	}
}
