<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook\API\Catalog\Product_Item\Find;

defined( 'ABSPATH' ) || exit;

use WooCommerce\Facebook\API;

/**
 * Find Product Item API request object.
 *
 * @since 2.0.0
 */
class Request extends API\Request {


	/**
	 * Gets the rate limit ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function get_rate_limit_id() {

		return 'ads_management';
	}


	/**
	 * API request constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $catalog_id catalog ID
	 * @param string $retailer_id retailer ID of the product
	 */
	public function __construct( $catalog_id, $retailer_id ) {

		parent::__construct( "catalog:{$catalog_id}:" . base64_encode( $retailer_id ), 'GET' ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}


	/**
	 * Gets the request parameters.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_params() {

		return array( 'fields' => 'id,product_group{id}' );
	}


}
