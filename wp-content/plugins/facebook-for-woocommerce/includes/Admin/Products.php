<?php
/**
 * Copyright (c) Facebook, Inc. and its affiliates. All Rights Reserved
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package FacebookCommerce
 */

namespace WooCommerce\Facebook\Admin;

defined( 'ABSPATH' ) || exit;

use WooCommerce\Facebook\Framework\Helper;
use WooCommerce\Facebook\Products as Products_Handler;

/**
 * General handler for product admin functionality.
 *
 * @since 2.1.0
 */
class Products {

	/** @var string Commerce enabled field */
	const FIELD_COMMERCE_ENABLED = 'wc_facebook_commerce_enabled';

	/** @var string Google Product category ID field */
	const FIELD_GOOGLE_PRODUCT_CATEGORY_ID = 'wc_facebook_google_product_category_id';

	/** @var string gender field */
	const FIELD_GENDER = 'wc_facebook_gender';

	/** @var string color field */
	const FIELD_COLOR = 'wc_facebook_color';

	/** @var string size field */
	const FIELD_SIZE = 'wc_facebook_size';

	/** @var string pattern field */
	const FIELD_PATTERN = 'wc_facebook_pattern';

	public static function render_google_product_category_fields_and_enhanced_attributes( \WC_Product $product ) {
		?>
		<div class='wc_facebook_commerce_fields'>
			<p class="form-field">
				<span><?php echo esc_html( Product_Categories::get_enhanced_catalog_explanation_text() ); ?></span>
			</p>
			<?php Enhanced_Catalog_Attribute_Fields::render_hidden_input_can_show_attributes(); ?>
			<?php self::render_google_product_category_fields( $product ); ?>
			<?php
			self::render_enhanced_catalog_attributes_fields(
				Products_Handler::get_google_product_category_id( $product ),
				$product
			);
			?>
		</div>
		<?php
	}

	public static function render_enhanced_catalog_attributes_fields( $category_id, \WC_Product $product ) {
		$category_handler          = facebook_for_woocommerce()->get_facebook_category_handler();
		$enhanced_attribute_fields = new Enhanced_Catalog_Attribute_Fields(
			Enhanced_Catalog_Attribute_Fields::PAGE_TYPE_EDIT_PRODUCT,
			null,
			$product
		);
		if (
			empty( $category_id ) ||
			$category_handler->is_category( $category_id ) &&
			$category_handler->is_root_category( $category_id )
		) {
			// show nothing
			return;
		}
		?>
			<p class="form-field wc-facebook-enhanced-catalog-attribute-row">
				<label for="<?php echo esc_attr( Enhanced_Catalog_Attribute_Fields::FIELD_ENHANCED_CATALOG_ATTRIBUTES_ID ); ?>">
					<?php echo esc_html( self::render_enhanced_catalog_attributes_title() ); ?>
					<?php self::render_enhanced_catalog_attributes_tooltip(); ?>
				</label>
			</p>
			<?php $enhanced_attribute_fields->render( $category_id ); ?>
		<?php
	}

	/**
	 * Renders the common tooltip markup.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 */
	public static function render_enhanced_catalog_attributes_tooltip() {
		$tooltip_text = __( 'Select values for enhanced attributes for this product', 'facebook-for-woocommerce' );
		?>
			<span class="woocommerce-help-tip" data-tip="<?php echo esc_attr( $tooltip_text ); ?>"></span>
		<?php
	}

	/**
	 * Gets the common field title.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @return string
	 */
	public static function render_enhanced_catalog_attributes_title() {
		return __( 'Category Specific Attributes', 'facebook-for-woocommerce' );
	}

	/**
	 * Renders the Google product category fields.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Product $product product object
	 */
	public static function render_google_product_category_fields( \WC_Product $product ) {
		$field = new Google_Product_Category_Field();
		$field->render( self::FIELD_GOOGLE_PRODUCT_CATEGORY_ID );
		?>
		<p class="form-field">
			<label for="<?php echo esc_attr( self::FIELD_GOOGLE_PRODUCT_CATEGORY_ID ); ?>">
				<?php esc_html_e( 'Google product category', 'facebook-for-woocommerce' ); ?>
				<?php echo wc_help_tip( __( 'Choose the Google product category and (optionally) sub-categories associated with this product.', 'facebook-for-woocommerce' ) ); ?>
			</label>
			<input
				id="<?php echo esc_attr( self::FIELD_GOOGLE_PRODUCT_CATEGORY_ID ); ?>"
				type="hidden"
				name="<?php echo esc_attr( self::FIELD_GOOGLE_PRODUCT_CATEGORY_ID ); ?>"
				value="<?php echo esc_attr( Products_Handler::get_google_product_category_id( $product ) ); ?>"
			/>
		</p>
		<?php
	}

	/**
	 * Gets a list of attribute names and labels that match any of the given words.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Product $product the product object
	 * @param array       $words a list of words used to filter attributes
	 * @return array
	 */
	private static function filter_available_product_attribute_names( \WC_Product $product, $words ) {
		$attributes = array();
		foreach ( self::get_available_product_attribute_names( $product ) as $name => $label ) {
			foreach ( $words as $word ) {
				if ( Helper::str_exists( wc_strtolower( $label ), $word ) || Helper::str_exists( wc_strtolower( $name ), $word ) ) {
					$attributes[ $name ] = $label;
				}
			}
		}
		return $attributes;
	}

	/**
	 * Gets a indexed list of available product attributes with the name of the attribute as key and the label as the value.
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Product $product the product object
	 * @return array
	 */
	public static function get_available_product_attribute_names( \WC_Product $product ) {
		return array_map(
			function( $attribute ) use ( $product ) {
				return wc_attribute_label( $attribute->get_name(), $product );
			},
			Products_Handler::get_available_product_attributes( $product )
		);
	}

	/**
	 * Renders the Commerce settings fields.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Product $product product object
	 */
	public static function render_commerce_fields( \WC_Product $product ) {
		?>
		<p class="form-field <?php echo esc_attr( self::FIELD_COMMERCE_ENABLED ); ?>_field">
			<label for="<?php echo esc_attr( self::FIELD_COMMERCE_ENABLED ); ?>">
				<?php echo esc_html_e( 'Sell on Instagram', 'facebook-for-woocommerce' ); ?>
				<span class="woocommerce-help-tip"
					data-tip="<?php echo esc_attr_e( 'Enable to sell this product on Instagram. Products that are hidden in the Facebook catalog can be synced, but won’t be available for purchase.', 'facebook-for-woocommerce' ); ?>"></span>
			</label>
			<input type="checkbox" class="enable-if-sync-enabled"
					name="<?php echo esc_attr( self::FIELD_COMMERCE_ENABLED ); ?>"
					id="<?php echo esc_attr( self::FIELD_COMMERCE_ENABLED ); ?>" value="yes"
					checked="<?php echo Products_Handler::is_commerce_enabled_for_product( $product ) ? 'checked' : ''; ?>">
		</p>

		<div id="product-not-ready-notice" style="display:none;">
			<p>
				<?php esc_html_e( 'This product does not meet the requirements to sell on Instagram.', 'facebook-for-woocommerce' ); ?>
				<a href="#" id="product-not-ready-notice-open-modal"><?php esc_html_e( 'Click here to learn more.', 'facebook-for-woocommerce' ); ?></a>
			</p>
		</div>

		<div id="variable-product-not-ready-notice" style="display:none;">
			<p>
			<?php
			echo sprintf(
				/* translators: Placeholders %1$s - strong opening tag, %2$s - strong closing tag */
				esc_html__( 'To sell this product on Instagram, at least one variation must be synced to Facebook. You can control variation sync on the %1$sVariations%2$s tab with the %1$sFacebook Sync%2$s setting.', 'facebook-for-woocommerce' ),
				'<strong>',
				'</strong>'
			);
			?>
			</p>
		</div>
		<?php
	}

	/**
	 * Saves the Commerce settings.
	 *
	 * @internal
	 *
	 * @since 2.1.0
	 *
	 * @param \WC_Product $product product object
	 */
	public static function save_commerce_fields( \WC_Product $product ) {
		$commerce_enabled            = wc_string_to_bool( Helper::get_posted_value( self::FIELD_COMMERCE_ENABLED ) );
		$google_product_category_id  = wc_clean( Helper::get_posted_value( self::FIELD_GOOGLE_PRODUCT_CATEGORY_ID ) );
		$enhanced_catalog_attributes = Products_Handler::get_enhanced_catalog_attributes_from_request();
		foreach ( $enhanced_catalog_attributes as $key => $value ) {
			Products_Handler::update_product_enhanced_catalog_attribute( $product, $key, $value );
		}
		if ( ! isset( $enhanced_catalog_attributes[ Enhanced_Catalog_Attribute_Fields::OPTIONAL_SELECTOR_KEY ] ) ) {
			// This is a checkbox so won't show in the post data if it's been unchecked,
			// hence if it's unset we should clear the term meta for it.
			Products_Handler::update_product_enhanced_catalog_attribute( $product, Enhanced_Catalog_Attribute_Fields::OPTIONAL_SELECTOR_KEY, null );
		}
		Products_Handler::update_commerce_enabled_for_product( $product, $commerce_enabled );
		if ( Products_Handler::get_google_product_category_id( $product ) !== $google_product_category_id ) {
			Products_Handler::update_google_product_category_id( $product, $google_product_category_id );
		}
	}
}
