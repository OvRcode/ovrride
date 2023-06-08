<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Coupon_Parser' ) ) {

	/**
	 * class to parse values for WC_Coupon for importing
	 */
	class WC_Coupon_Parser {

		/**
		 * @var string $post_type
		 */
		var $post_type;

		/**
		 * @var array $reserved_fields
		 */
		var $reserved_fields;		// Fields we map/handle (not custom fields)

		/**
		 * @var array $post_defaults
		 */
		var $post_defaults;			// Default post data

		/**
		 * @var array $postmeta_defaults
		 */
		var $postmeta_defaults;		// default post meta

		/**
		 * @var int $row
		 */
		var $row;

		/**
		 * Constructor
		 *
		 * @param string $post_type
		 */
		public function __construct( $post_type = 'shop_coupon' ) {

			$this->post_type = $post_type;

			$this->reserved_fields = array(
				'id',
				'post_id',
				'post_type',
				'menu_order',
				'postmeta',
				'post_status',
				'post_title',
				'post_name',
				'comment_status',
				'post_date',
				'post_date_gmt',
				'post_content',
				'post_excerpt',
				'post_parent',
				'post_password',
				'discount_type',
				'coupon_amount',
				'free_shipping',
				'expiry_date',
				'minimum_amount',
				'maximum_amount',
				'individual_use',
				'exclude_sale_items',
				'product_ids',
				'exclude_product_ids',
				'product_categories',
				'exclude_product_categories',
				'customer_email',
				'usage_limit',
				'usage_limit_per_user',
				'limit_usage_to_x_items',
			);

			$this->post_defaults = array(
				'post_type'         => $this->post_type,
				'menu_order'        => '',
				'postmeta'          => array(),
				'post_status'       => 'publish',
				'post_title'        => '',
				'post_name'         => '',
				'comment_status'    => 'open',
				'post_date'         => '',
				'post_date_gmt'     => '',
				'post_content'      => '',
				'post_excerpt'      => '',
				'post_parent'       => 0,
				'post_password'     => '',
			);

			$this->postmeta_defaults = apply_filters( 'smart_coupons_parser_postmeta_defaults', array(
				'discount_type'                 => 'fixed_cart',
				'coupon_amount'                 => '',
				'free_shipping'                 => '',
				'expiry_date'                   => '',
				'sc_coupon_validity'            => '',
				'validity_suffix'               => '',
				'auto_generate_coupon'          => '',
				'coupon_title_prefix'           => '',
				'coupon_title_suffix'           => '',
				'is_pick_price_of_product'      => '',
				'minimum_amount'                => '',
				'maximum_amount'                => '',
				'individual_use'                => '',
				'exclude_sale_items'            => '',
				'product_ids'                   => '',
				'exclude_product_ids'           => '',
				'product_categories'            => '',
				'exclude_product_categories'    => '',
				'customer_email'                => '',
				'sc_disable_email_restriction'  => '',
				'usage_limit'                   => '',
				'usage_limit_per_user'          => '',
				'limit_usage_to_x_items'        => '',
				'sc_is_visible_storewide'       => '',
			));

		}

		/**
		 * Handle call to functions which is not available in this class
		 *
		 * @param $function_name string
		 * @param $arguments array of arguments passed while calling $function_name
		 * @return result of function call
		 */
		public function __call( $function_name, $arguments = array() ) {

			global $woocommerce_smart_coupon;

			if ( ! is_callable( array( $woocommerce_smart_coupon, $function_name ) ) ) { return;
			}

			if ( ! empty( $arguments ) ) {
				return call_user_func_array( array( $woocommerce_smart_coupon, $function_name ), $arguments );
			} else {
				return call_user_func( array( $woocommerce_smart_coupon, $function_name ) );
			}

		}

		/**
		 * Format data passed from CSV
		 *
		 * @param array  $data
		 * @param string $enc encoding
		 */
		public function format_data_from_csv( $data, $enc ) {
			return ( $enc == 'UTF-8' ) ? $data : utf8_encode( $data );
		}

		/**
		 * Parse data
		 *
		 * @param file imported file
		 * @return array parsed data with headers
		 */
		public function parse_data( $file ) {

			// Set locale
			$enc = mb_detect_encoding( $file, 'UTF-8, ISO-8859-1', true );
			if ( $enc ) { setlocale( LC_ALL, 'en_US.' . $enc );
			}
			@ini_set( 'auto_detect_line_endings', true );

			$parsed_data = array();

			// Put all CSV data into an associative array
			if ( ( $handle = fopen( $file, 'r' ) ) !== false ) {

				$header = fgetcsv( $handle, 0 );

				while ( ( $postmeta = fgetcsv( $handle, 0 ) ) !== false ) {
					$row = array();
					foreach ( $header as $key => $heading ) {

						$s_heading = strtolower( $heading );

						if ( isset( $_POST['map_to'][ $s_heading ] ) ) {
							if ( $_POST['map_to'][ $s_heading ] == 'import_as_meta' ) {

									$s_heading = 'meta:' . $s_heading;

							} elseif ( $_POST['map_to'][ $s_heading ] == 'import_as_images' ) {

								$s_heading = 'images';

							} else {
								$s_heading = esc_attr( $_POST['map_to'][ $s_heading ] );
							}
						}

						if ( $s_heading == '' ) { continue;
						}

						$row[ $s_heading ] = ( isset( $postmeta[ $key ] ) ) ? $this->format_data_from_csv( $postmeta[ $key ], $enc ) : '';

						$raw_headers[ $s_heading ] = $heading;
					}

					$parsed_data[] = $row;

					unset( $postmeta, $row );

				}

				fclose( $handle );
			}

			return array( $parsed_data, $raw_headers );
		}

		/**
		 * Parse coupon
		 *
		 * @param array $item
		 * @return array $coupon
		 */
		public function parse_coupon( $item ) {
			global $wc_csv_coupon_import, $wpdb;

			$this->row++;
			$postmeta = $coupon = array();

			$post_id = ( ! empty( $item['id'] ) ) ? $item['id'] : 0;
			$post_id = ( ! empty( $item['post_id'] ) ) ? $item['post_id'] : $post_id;

			$product['post_id'] = $post_id;

			// Get post fields
			foreach ( $this->post_defaults as $column => $default ) {
				if ( isset( $item[ $column ] ) ) { $product[ $column ] = $item[ $column ];
				}
			}

			// Get custom fields
			foreach ( $this->postmeta_defaults as $column => $default ) {
				if ( isset( $item[ $column ] ) ) {
						$postmeta[ $column ] = (string) $item[ $column ];
				} elseif ( isset( $item[ '_' . $column ] ) ) {
						$postmeta[ $column ] = (string) $item[ '_' . $column ];
				}
			}

			// Merge post meta with defaults
			$coupon = wp_parse_args( $product, $this->post_defaults );
			$postmeta = wp_parse_args( $postmeta, $this->postmeta_defaults );

			if ( ! empty( $postmeta['discount_type'] ) ) {
				$discount_type = $postmeta['discount_type'];
			} else {
				if ( $this->is_wc_gte_30() ) {
					$discount_type = 'Percentage discount';
				} else {
					$discount_type = 'Cart % Discount';
				}
			}

			$all_discount_types = wc_get_coupon_types();

			// discount types
			if ( ! empty( $discount_type ) ) {

				if ( in_array( $discount_type, $all_discount_types ) ) {
					$postmeta['discount_type'] = array_search( $discount_type, $all_discount_types );
				}

				if ( empty( $postmeta['discount_type'] ) ) {
					$postmeta['discount_type'] = 'percent';
				}

				// if ( 'Fixed cart discount' == $postmeta['discount_type'] || 'Cart Discount' == $postmeta['discount_type'] ) {
				// 	$postmeta['discount_type'] = 'fixed_cart';
				// } elseif ( 'Percentage discount' == $postmeta['discount_type'] || 'Cart % Discount' == $postmeta['discount_type'] ) {
				// 	$postmeta['discount_type'] = 'percent';
				// } elseif ( 'Fixed product discount' == $postmeta['discount_type'] || 'Product Discount' == $postmeta['discount_type'] ) {
				// 	$postmeta['discount_type'] = 'fixed_product';
				// } elseif ( ! $this->is_wc_gte_30() && 'Product % Discount' == $postmeta['discount_type'] ) {
				// 	$postmeta['discount_type'] = 'percent_product';
				// } elseif ( 'Store Credit / Gift Certificate' == $postmeta['discount_type'] ) {
				// 	$postmeta['discount_type'] = 'smart_coupon';
				// }
			}

			// product_ids
			if ( isset( $postmeta['product_ids'] ) && ! is_array( $postmeta['product_ids'] ) ) {
					$ids = array_filter( array_map( 'trim', explode( '|', $postmeta['product_ids'] ) ) );
					$ids = implode( ',',$ids );
					$postmeta['product_ids'] = $ids;
			}

			// exclude_product_ids
			if ( isset( $postmeta['exclude_product_ids'] ) && ! is_array( $postmeta['exclude_product_ids'] ) ) {
					$ids = array_filter( array_map( 'trim', explode( '|', $postmeta['exclude_product_ids'] ) ) );
					$ids = implode( ',',$ids );
					$postmeta['exclude_product_ids'] = $ids;
			}

			// product_categories
			if ( isset( $postmeta['product_categories'] ) && ! is_array( $postmeta['product_categories'] ) ) {
					$ids = array_filter( array_map( 'trim', explode( '|', $postmeta['product_categories'] ) ) );
					$postmeta['product_categories'] = $ids;
			}

			// exclude_product_categories
			if ( isset( $postmeta['exclude_product_categories'] ) && ! is_array( $postmeta['exclude_product_categories'] ) ) {
					$ids = array_filter( array_map( 'trim', explode( '|', $postmeta['exclude_product_categories'] ) ) );
					$postmeta['exclude_product_categories'] = $ids;
			}

			// customer_email
			if ( isset( $postmeta['customer_email'] ) && ! is_array( $postmeta['customer_email'] ) ) {
					$email_ids = array_filter( array_map( 'trim', explode( ',', $postmeta['customer_email'] ) ) );
					$postmeta['customer_email'] = $email_ids;
			}

			// expiry date
			if ( isset( $postmeta['expiry_date'] ) ) {
				$postmeta['expiry_date'] = ( ! empty( $postmeta['expiry_date'] )) ? date_i18n( 'Y-m-d', strtotime( $postmeta['expiry_date'] ) ) : '';
			}

			// Put set core product postmeta into product array
			foreach ( $postmeta as $key => $value ) {
					$coupon['postmeta'][] = array( 'key' => esc_attr( $key ), 'value' => $value );
			}

			unset( $item, $postmeta );

			return $coupon;

		}
	}

}
