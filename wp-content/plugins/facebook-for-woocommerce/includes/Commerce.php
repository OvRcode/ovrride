<?php
// phpcs:ignoreFile
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook;

defined( 'ABSPATH' ) or exit;

/**
 * Base handler for Commerce-specific functionality.
 *
 * @since 2.1.0
 */
class Commerce {


	/** @var string option that stores the plugin-level fallback Google product category ID */
	const OPTION_GOOGLE_PRODUCT_CATEGORY_ID = 'wc_facebook_google_product_category_id';


	/**
	 * Gets the plugin-level fallback Google product category ID.
	 *
	 * This will be used when the category or product-level settings don’t override it.
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public function get_default_google_product_category_id() {

		$category_id = get_option( self::OPTION_GOOGLE_PRODUCT_CATEGORY_ID, '' );

		/**
		 * Filters the plugin-level fallback Google product category ID.
		 *
		 * @since 2.1.0
		 *
		 * @param string $category_id default Google product category ID
		 * @param Commerce $commerce commerce handler instance
		 */
		return (string) apply_filters( 'wc_facebook_commerce_default_google_product_category_id', $category_id, $this );
	}


	/**
	 * Updates the plugin-level fallback Google product category ID.
	 *
	 * @since 2.1.0
	 *
	 * @param string $id category ID
	 */
	public function update_default_google_product_category_id( $id ) {

		update_option( self::OPTION_GOOGLE_PRODUCT_CATEGORY_ID, is_string( $id ) ? $id : '' );
	}


	/**
	 * Determines whether Commerce features should be available.
	 *
	 * @since 2.1.0
	 *
	 * @return bool whether Commerce features should be available
	 */
	public function is_available() {

		list( $country ) = explode( ':', get_option( 'woocommerce_default_country' ) );

		/**
		 * Filters whether Commerce features should be available.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $available whether commerce features should be available
		 * @param string $country country code
		 * @param Commerce $commerce commerce handler instance
		 */
		return (bool) apply_filters( 'wc_facebook_commerce_is_available', 'US' === $country, $country, $this );
	}


	/**
	 * Determines whether the site is connected.
	 *
	 * @since 2.1.0
	 *
	 * @return bool whether the site is connected
	 */
	public function is_connected() {

		$connection_handler = facebook_for_woocommerce()->get_connection_handler();

		$connected = (bool) strlen( $connection_handler->get_page_access_token() ) && ! empty( $connection_handler->get_commerce_manager_id() );

		/**
		 * Filters whether the site is connected.
		 *
		 * @since 2.1.0
		 *
		 * @param bool $connected whether the site is connected
		 * @param Commerce $commerce commerce handler instance
		 */
		return (bool) apply_filters( 'wc_facebook_commerce_is_connected', $connected, $this );
	}
}
