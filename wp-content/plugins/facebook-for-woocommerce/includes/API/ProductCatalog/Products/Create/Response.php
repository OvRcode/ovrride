<?php
declare( strict_types=1 );

namespace WooCommerce\Facebook\API\ProductCatalog\Products\Create;

use WooCommerce\Facebook\API\Response as ApiResponse;

defined( 'ABSPATH' ) || exit;

/**
 * Response object for Product Catalog > Product Groups > Products > Create Graph Api.
 *
 * @link https://developers.facebook.com/docs/marketing-api/reference/product-group/products/#Creating
 * @property-read string id Facebook Product ID.
 */
class Response extends ApiResponse {}
