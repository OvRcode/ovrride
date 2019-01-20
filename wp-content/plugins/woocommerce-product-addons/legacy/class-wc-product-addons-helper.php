<?php
/**
 * Product Add-ons helper
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Addons_Helper {
	/**
	 * Gets addons assigned to a product by ID.
	 *
	 * @param  int    $post_id ID of the product to get addons for.
	 * @param  string $prefix for addon field names. Defaults to postid.
	 * @param  bool   $inc_parent Set to false to not include parent product addons.
	 * @param  bool   $inc_global Set to false to not include global addons.
	 * @return array
	 */
	public static function get_product_addons( $post_id, $prefix = false, $inc_parent = true, $inc_global = true ) {
		if ( ! $post_id ) {
			return array();
		}

		$addons     = array();
		$raw_addons = array();
		$parent_id  = wp_get_post_parent_id( $post_id );

		if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$product_terms  = apply_filters( 'get_product_addons_product_terms', wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) ), $post_id );
			$exclude        = get_post_meta( $post_id, '_product_addons_exclude_global', true );
			$product_addons = array_filter( (array) get_post_meta( $post_id, '_product_addons', true ) );
		} else {
			$product        = wc_get_product( $post_id );
			$product_terms  = apply_filters( 'get_product_addons_product_terms', wc_get_object_terms( $product->get_id(), 'product_cat', 'term_id' ), $product->get_id() );
			$exclude        = $product->get_meta( '_product_addons_exclude_global' );
			$product_addons = array_filter( (array) $product->get_meta( '_product_addons' ) );
		}

		// Product Parent Level Addons.
		if ( $inc_parent && $parent_id ) {
			$raw_addons[10]['parent'] = apply_filters( 'get_parent_product_addons_fields', self::get_product_addons( $parent_id, $parent_id . '-', false, false ), $post_id, $parent_id );
		}

		// Product Level Addons.
		$raw_addons[10]['product'] = apply_filters( 'get_product_addons_fields', $product_addons, $post_id );

		// Global level addons (all products).
		if ( '1' !== $exclude && $inc_global ) {
			$args = array(
				'posts_per_page'   => -1,
				'orderby'          => 'meta_value',
				'order'            => 'ASC',
				'meta_key'         => '_priority',
				'post_type'        => 'global_product_addon',
				'post_status'      => 'publish',
				'suppress_filters' => true,
				'meta_query' => array(
					array(
						'key'   => '_all_products',
						'value' => '1',
					),
				),
			);

			$global_addons = get_posts( $args );

			if ( $global_addons ) {
				foreach ( $global_addons as $global_addon ) {
					$priority                                     = get_post_meta( $global_addon->ID, '_priority', true );
					$raw_addons[ $priority ][ $global_addon->ID ] = apply_filters( 'get_product_addons_fields', array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) ), $global_addon->ID );
				}
			}

			// Global level addons (categories).
			if ( $product_terms ) {
				$args = apply_filters( 'get_product_addons_global_query_args', array(
					'posts_per_page'   => -1,
					'orderby'          => 'meta_value',
					'order'            => 'ASC',
					'meta_key'         => '_priority',
					'post_type'        => 'global_product_addon',
					'post_status'      => 'publish',
					'suppress_filters' => true,
					'tax_query'        => array(
						array(
							'taxonomy'         => 'product_cat',
							'field'            => 'id',
							'terms'            => $product_terms,
							'include_children' => false,
						),
					),
				), $product_terms );

				$global_addons = get_posts( $args );

				if ( $global_addons ) {
					foreach ( $global_addons as $global_addon ) {
						$priority                                     = get_post_meta( $global_addon->ID, '_priority', true );
						$raw_addons[ $priority ][ $global_addon->ID ] = apply_filters( 'get_product_addons_fields', array_filter( (array) get_post_meta( $global_addon->ID, '_product_addons', true ) ), $global_addon->ID );
					}
				}
			}
		}

		ksort( $raw_addons );

		foreach ( $raw_addons as $addon_group ) {
			if ( $addon_group ) {
				foreach ( $addon_group as $addon ) {
					$addons = array_merge( $addons, $addon );
				}
			}
		}

		// Generate field names with unqiue prefixes.
		if ( ! $prefix ) {
			$prefix = apply_filters( 'product_addons_field_prefix', "{$post_id}-", $post_id );
		}

		// Let's avoid exceeding the suhosin default input element name limit of 64 characters.
		$max_addon_name_length = 45 - strlen( $prefix );

		// If the product_addons_field_prefix filter results in a very long prefix, then
		// go ahead and enforce sanity, exceed the default suhosin limit, and just use
		// the prefix and the field counter for the input element name.
		if ( $max_addon_name_length < 0 ) {
			$max_addon_name_length = 0;
		}

		$addon_field_counter = 0;

		foreach ( $addons as $addon_key => $addon ) {
			if ( empty( $addon['name'] ) ) {
				unset( $addons[ $addon_key ] );
				continue;
			}
			if ( empty( $addons[ $addon_key ]['field-name'] ) ) {
				$addon_name = substr( $addon['name'], 0, $max_addon_name_length );
				$addons[ $addon_key ]['field-name'] = sanitize_title( $prefix . $addon_name . '-' . $addon_field_counter );
				$addon_field_counter++;
			}
		}

		return apply_filters( 'get_product_addons', $addons );
	}

	/**
	 * Display prices according to shop settings.
	 *
	 * @version 2.8.2
	 *
	 * @param  float      $price     Price to display.
	 * @param  WC_Product $cart_item Product from cart.
	 *
	 * @return float
	 */
	public static function get_product_addon_price_for_display( $price, $cart_item = null ) {
		$product = ! empty( $GLOBALS['product'] ) && is_object( $GLOBALS['product'] ) ? clone $GLOBALS['product'] : null;

		if ( '' === $price || '0' == $price ) {
			return;
		}

		$neg = false;

		if ( $price < 0 ) {
			$neg = true;
			$price *= -1;
		}

		if ( ( is_cart() || is_checkout() ) && null !== $cart_item ) {
			$product = wc_get_product( $cart_item->get_id() );
		}

		if ( is_object( $product ) ) {
			// Support new wc_get_price_excluding_tax() and wc_get_price_excluding_tax() functions.
			if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
				$display_price = self::get_product_addon_tax_display_mode() === 'incl' ? wc_get_price_including_tax( $product, array( 'qty' => 1, 'price' => $price ) ) : wc_get_price_excluding_tax( $product, array( 'qty' => 1, 'price' => $price ) );
			} else {
				$display_price = self::get_product_addon_tax_display_mode() === 'incl' ? $product->get_price_including_tax( 1, $price ) : $product->get_price_excluding_tax( 1, $price );
			}
		} else {
			$display_price = $price;
		}

		if ( $neg ) {
			$display_price = '-' . $display_price;
		}

		return $display_price;
	}

	/**
	 * Return tax display mode depending on context.
	 *
	 * @return string
	 */
	public static function get_product_addon_tax_display_mode() {
		if ( is_cart() || is_checkout() ) {
			return get_option( 'woocommerce_tax_display_cart' );
		}

		return get_option( 'woocommerce_tax_display_shop' );
	}
}
