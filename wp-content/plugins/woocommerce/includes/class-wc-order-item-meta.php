<?php
/**
 * Order Item Meta
 *
 * A Simple class for managing order item meta so plugins add it in the correct format
 *
 * @class 		order_item_meta
 * @version		2.2
 * @package		WooCommerce/Classes
 * @author 		WooThemes
 */
class WC_Order_Item_Meta {

	public $meta;
	public $product;

	/**
	 * Constructor
	 *
	 * @access public
	 * @param array $item_meta defaults to array()
	 * @param \WC_Product $product defaults to null
	 * @return \WC_Order_Item_Meta instance
	 */
	public function __construct( $item_meta = array(), $product = null ) {
		$this->meta    = $item_meta;
		$this->product = $product;
	}

	/**
	 * Display meta in a formatted list
	 *
	 * @access public
	 * @param bool $flat (default: false)
	 * @param bool $return (default: false)
	 * @param string $hideprefix (default: _)
	 * @return string
	 */
	public function display( $flat = false, $return = false, $hideprefix = '_' ) {

		$output = '';

		$formatted_meta = $this->get_formatted( $hideprefix );

		if ( ! empty( $formatted_meta ) ) {

			$meta_list = array();

			foreach ( $formatted_meta as $meta_key => $meta ) {

				if ( $flat ) {
					$meta_list[] = wp_kses_post( $meta['label'] . ': ' . $meta['value'] );
				} else {
					$meta_list[] = '
							<dt class="variation-' . sanitize_html_class( sanitize_text_field( $meta_key ) ) . '">' . wp_kses_post( $meta['label'] ) . ':</dt>
							<dd class="variation-' . sanitize_html_class( sanitize_text_field( $meta_key ) ) . '">' . wp_kses_post( wpautop( $meta['value'] ) ) . '</dd>
						';
				}
			}

			if ( ! empty( $meta_list ) ) {

				if ( $flat ) {
					$output .= implode( ", \n", $meta_list );
				} else {
					$output .= '<dl class="variation">' . implode( '', $meta_list ). '</dl>';
				}
			}
		}

		if ( $return ) {
			return $output;
		} else {
			echo $output;
		}
	}


	/**
	 * Return an array of formatted item meta in format:
	 *
	 * array(
	 *   $meta_key => array(
	 *     'label' => $label,
	 *     'value' => $value
	 *   )
	 * )
	 *
	 * e.g.
	 *
	 * array(
	 *   'pa_size' => array(
	 *     'label' => 'Size',
	 *     'value' => 'Medium',
	 *   )
	 * )
	 *
	 * @since 2.2
	 * @param string $hideprefix exclude meta when key is prefixed with this, defaults to `_`
	 * @return array
	 */
	public function get_formatted( $hideprefix = '_' ) {

		if ( empty( $this->meta ) ) {
			return array();
		}

		$formatted_meta = array();

		foreach ( (array) $this->meta as $meta_key => $meta_values ) {

			if ( empty( $meta_values ) || ( ! empty( $hideprefix ) && substr( $meta_key, 0, 1 ) == $hideprefix ) ) {
				continue;
			}

			foreach ( (array) $meta_values as $meta_value ) {

				// Skip serialised meta
				if ( is_serialized( $meta_value ) ) {
					continue;
				}

				$attribute_key = urldecode( str_replace( 'attribute_', '', $meta_key ) );

				// If this is a term slug, get the term's nice name
				if ( taxonomy_exists( $attribute_key ) ) {
					$term = get_term_by( 'slug', $meta_value, $attribute_key );

					if ( ! is_wp_error( $term ) && is_object( $term ) && $term->name ) {
						$meta_value = $term->name;
					}

				// If we have a product, and its not a term, try to find its non-sanitized name
				} elseif ( $this->product ) {
					$product_attributes = $this->product->get_attributes();

					if ( isset( $product_attributes[ $attribute_key ] ) ) {
						$meta_key = wc_attribute_label( $product_attributes[ $attribute_key ]['name'] );
					}
				}

				$formatted_meta[ $meta_key ] = array(
					'label'     => wc_attribute_label( $attribute_key ),
					'value'     => apply_filters( 'woocommerce_order_item_display_meta_value', $meta_value ),
				);
			}
		}

		return $formatted_meta;
	}
}
