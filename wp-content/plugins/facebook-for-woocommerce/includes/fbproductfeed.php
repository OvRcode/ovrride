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

defined( 'ABSPATH' ) || exit;

use WooCommerce\Facebook\Framework\Plugin\Exception as PluginException;
use WooCommerce\Facebook\Products;
use WooCommerce\Facebook\Products\Feed;
use WooCommerce\Facebook\Framework\Api\Exception as ApiException;


/**
 * Initial Sync by Facebook feed class
 */
class WC_Facebook_Product_Feed {


	/** @var string product catalog feed file directory inside the uploads folder */
	const UPLOADS_DIRECTORY              = 'facebook_for_woocommerce';
	const FILE_NAME                      = 'product_catalog_%s.csv';
	const FACEBOOK_CATALOG_FEED_FILENAME = 'fae_product_catalog.csv';
	const FB_ADDITIONAL_IMAGES_FOR_FEED  = 5;
	const FEED_NAME                      = 'Initial product sync from WooCommerce. DO NOT DELETE.';
	const FB_PRODUCT_GROUP_ID            = 'fb_product_group_id';
	const FB_VISIBILITY                  = 'fb_visibility';

	private $has_default_product_count = 0;
	private $no_default_product_count  = 0;

	/**
	 * WC_Facebook_Product_Feed constructor.
	 *
	 * @param string|null $facebook_catalog_id Facebook catalog ID, if any
	 * @param string|null $feed_id Facebook feed ID, if any
	 */
	public function __construct( $facebook_catalog_id = null, $feed_id = null ) {
		$this->facebook_catalog_id = $facebook_catalog_id;
		$this->feed_id             = $feed_id;
	}

	/**
	 * Generates the product catalog feed.
	 *
	 * This replaces any previously generated feed file.
	 *
	 * @since 1.11.0
	 */
	public function generate_feed() {
		$profiling_logger = facebook_for_woocommerce()->get_profiling_logger();
		$profiling_logger->start( 'generate_feed' );

		\WC_Facebookcommerce_Utils::log( 'Generating a fresh product feed file' );

		try {

			$start_time = microtime( true );

			$this->generate_productfeed_file();

			$generation_time = microtime( true ) - $start_time;
			facebook_for_woocommerce()->get_tracker()->track_feed_file_generation_time( $generation_time );

			\WC_Facebookcommerce_Utils::log( 'Product feed file generated' );

		} catch ( \Exception $exception ) {

			\WC_Facebookcommerce_Utils::log( $exception->getMessage() );
			// Feed generation failed - clear the generation time to track that there's an issue.
			facebook_for_woocommerce()->get_tracker()->track_feed_file_generation_time( -1 );

		}

		$profiling_logger->stop( 'generate_feed' );
	}

	/**
	 * Gets the product catalog feed file path.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_file_path() {

		/**
		 * Filters the product catalog feed file path.
		 *
		 * @since 1.11.0
		 *
		 * @param string $file_path the file path
		 */
		return apply_filters( 'wc_facebook_product_catalog_feed_file_path', "{$this->get_file_directory()}/{$this->get_file_name()}" );
	}


	/**
	 * Gets the product catalog temporary feed file path.
	 *
	 * @since 1.11.3
	 *
	 * @return string
	 */
	public function get_temp_file_path() {

		/**
		 * Filters the product catalog temporary feed file path.
		 *
		 * @since 1.11.3
		 *
		 * @param string $file_path the temporary file path
		 */
		return apply_filters( 'wc_facebook_product_catalog_temp_feed_file_path', "{$this->get_file_directory()}/{$this->get_temp_file_name()}" );
	}


	/**
	 * Gets the product catalog feed file directory.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_file_directory() {

		$uploads_directory = wp_upload_dir( null, false );

		return trailingslashit( $uploads_directory['basedir'] ) . self::UPLOADS_DIRECTORY;
	}


	/**
	 * Gets the product catalog feed file name.
	 *
	 * @since 1.11.0
	 *
	 * @return string
	 */
	public function get_file_name() {

		$file_name = sprintf( self::FILE_NAME, wp_hash( Feed::get_feed_secret() ) );

		/**
		 * Filters the product catalog feed file name.
		 *
		 * @since 1.11.0
		 *
		 * @param string $file_name the file name
		 */
		return apply_filters( 'wc_facebook_product_catalog_feed_file_name', $file_name );
	}


	/**
	 * Gets the product catalog temporary feed file name.
	 *
	 * @since 1.11.3
	 *
	 * @return string
	 */
	public function get_temp_file_name() {

		$file_name = sprintf( self::FILE_NAME, 'temp_' . wp_hash( Feed::get_feed_secret() ) );

		/**
		 * Filters the product catalog temporary feed file name.
		 *
		 * @since 1.11.3
		 *
		 * @param string $file_name the temporary file name
		 */
		return apply_filters( 'wc_facebook_product_catalog_temp_feed_file_name', $file_name );
	}


	/**
	 * Gets the product IDs that will be included in the feed file.
	 *
	 * @since 1.11.0
	 *
	 * @return int[]
	 */
	private function get_product_ids() {
		return \WC_Facebookcommerce_Utils::get_all_product_ids_for_sync();
	}


	/**
	 * Generates the product catalog feed file.
	 *
	 * @return bool
	 * @throws PluginException
	 */
	public function generate_productfeed_file() {

		if ( ! wp_mkdir_p( $this->get_file_directory() ) ) {
			throw new PluginException( __( 'Could not create product catalog feed directory', 'facebook-for-woocommerce' ), 500 );
		}

		$this->create_files_to_protect_product_feed_directory();

		return $this->write_product_feed_file( $this->get_product_ids() );
	}


	/**
	 * Creates files in the catalog feed directory to prevent directory listing and hotlinking.
	 *
	 * @since 1.11.0
	 */
	public function create_files_to_protect_product_feed_directory() {

		$catalog_feed_directory = trailingslashit( $this->get_file_directory() );

		$files = array(
			array(
				'base'    => $catalog_feed_directory,
				'file'    => 'index.html',
				'content' => '',
			),
			array(
				'base'    => $catalog_feed_directory,
				'file'    => '.htaccess',
				'content' => 'deny from all',
			),
		);

		foreach ( $files as $file ) {

			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {

				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {

					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}


	/**
	 * Writes the product catalog feed file with data for the given product IDs.
	 *
	 * @since 1.11.0
	 *
	 * @param int[] $wp_ids product IDs
	 * @return bool
	 */
	public function write_product_feed_file( $wp_ids ) {

		try {

			// Step 1: Prepare the temporary empty feed file with header row.
			$temp_feed_file = $this->prepare_temporary_feed_file();

			// Step 2: Write products feed into the temporary feed file.
			$this->write_products_feed_to_temp_file( $wp_ids, $temp_feed_file );

			// Step 3: Rename temporary feed file to final feed file.
			$this->rename_temporary_feed_file_to_final_feed_file();

			$written = true;

		} catch ( Exception $e ) {

			WC_Facebookcommerce_Utils::log( json_encode( $e->getMessage() ) );

			$written = false;

			// close the temporary file
			if ( ! empty( $temp_feed_file ) && is_resource( $temp_feed_file ) ) {

				fclose( $temp_feed_file );
			}

			// delete the temporary file
			if ( ! empty( $temp_file_path ) && file_exists( $temp_file_path ) ) {

				unlink( $temp_file_path );
			}
		}

		return $written;
	}

	/**
	 * Prepare a fresh empty temporary feed file with the header row.
	 *
	 * @since 2.6.6
	 *
	 * @throws PluginException We can't open the file or the file is not writable.
	 * @return resource A file pointer resource.
	 */
	public function prepare_temporary_feed_file() {
		$temp_file_path = $this->get_temp_file_path();
		$temp_feed_file = @fopen( $temp_file_path, 'w' );

		// check if we can open the temporary feed file
		if ( false === $temp_feed_file || ! is_writable( $temp_file_path ) ) {
			throw new PluginException( __( 'Could not open the product catalog temporary feed file for writing', 'facebook-for-woocommerce' ), 500 );
		}

		$file_path = $this->get_file_path();

		// check if we will be able to write to the final feed file
		if ( file_exists( $file_path ) && ! is_writable( $file_path ) ) {
			throw new PluginException( __( 'Could not open the product catalog feed file for writing', 'facebook-for-woocommerce' ), 500 );
		}

		fwrite( $temp_feed_file, $this->get_product_feed_header_row() );
		return $temp_feed_file;
	}

	/**
	 * Write products feed into a file.
	 *
	 * @since 2.6.6
	 *
	 * @return void
	 */
	public function write_products_feed_to_temp_file( $wp_ids, $temp_feed_file ) {
		$product_group_attribute_variants = array();

		foreach ( $wp_ids as $wp_id ) {

			$woo_product = new WC_Facebook_Product( $wp_id );

			// Skip if we don't have a valid product object.
			if ( ! $woo_product->woo_product instanceof \WC_Product ) {
				continue;
			}

			// Skip if not enabled for sync.
			if ( ! facebook_for_woocommerce()->get_product_sync_validator( $woo_product->woo_product )->passes_all_checks() ) {
				continue;
			}

			$product_data_as_feed_row = $this->prepare_product_for_feed(
				$woo_product,
				$product_group_attribute_variants
			);

			if ( ! empty( $temp_feed_file ) ) {
				fwrite( $temp_feed_file, $product_data_as_feed_row );
			}
		}

		wp_reset_postdata();

		if ( ! empty( $temp_feed_file ) ) {
			fclose( $temp_feed_file );
		}
	}

	/**
	 * Rename temporary feed file into the final feed file.
	 * This is the last step fo the feed generation procedure.
	 *
	 * @since 2.6.6
	 *
	 * @return void
	 */
	public function rename_temporary_feed_file_to_final_feed_file() {
		$file_path      = $this->get_file_path();
		$temp_file_path = $this->get_temp_file_path();
		if ( ! empty( $temp_file_path ) && ! empty( $file_path ) ) {

			$renamed = rename( $temp_file_path, $file_path );

			if ( empty( $renamed ) ) {
				throw new PluginException( __( 'Could not rename the product catalog feed file', 'facebook-for-woocommerce' ), 500 );
			}
		}
	}

	public function get_product_feed_header_row() {
		return 'id,title,description,image_link,link,product_type,' .
		'brand,price,availability,item_group_id,checkout_url,' .
		'additional_image_link,sale_price_effective_date,sale_price,condition,' .
		'visibility,gender,color,size,pattern,google_product_category,default_product,variant' . PHP_EOL;
	}


	/**
	 * Assembles product payload in feed upload for initial sync.
	 *
	 * @param \WC_Facebook_Product $woo_product WooCommerce product object normalized by Facebook
	 * @param array                $attribute_variants passed by reference
	 * @return string product feed line data
	 */
	private function prepare_product_for_feed( $woo_product, &$attribute_variants ) {

		$product_data  = $woo_product->prepare_product( null, \WC_Facebook_Product::PRODUCT_PREP_TYPE_FEED );
		$item_group_id = $product_data['retailer_id'];

		// prepare variant column for variable products
		$product_data['variant'] = '';

		if ( $woo_product->is_type( 'variation' ) ) {

			$parent_id = $woo_product->get_parent_id();

			if ( ! isset( $attribute_variants[ $parent_id ] ) ) {

				$parent_product          = new \WC_Facebook_Product( $parent_id );
				$gallery_urls            = array_filter( $parent_product->get_gallery_urls() );
				$variation_id            = $parent_product->find_matching_product_variation();
				$variants_for_group      = $parent_product->prepare_variants_for_group( true );
				$parent_attribute_values = array(
					'gallery_urls'       => $gallery_urls,
					'default_variant_id' => $variation_id,
					'item_group_id'      => \WC_Facebookcommerce_Utils::get_fb_retailer_id( $parent_product ),
				);

				foreach ( $variants_for_group as $variant ) {
					if ( isset( $variant['product_field'], $variant['options'] ) ) {
						$parent_attribute_values[ $variant['product_field'] ] = $variant['options'];
					}
				}

				// cache product group variants
				$attribute_variants[ $parent_id ] = $parent_attribute_values;

			} else {

				$parent_attribute_values = $attribute_variants[ $parent_id ];
			}

			$variants_for_item   = $woo_product->prepare_variants_for_item( $product_data );
			$variant_feed_column = array();

			foreach ( $variants_for_item as $variant_array ) {

				static::format_variant_for_feed(
					$variant_array['product_field'],
					$variant_array['options'][0],
					$parent_attribute_values,
					$variant_feed_column
				);
			}

			if ( isset( $product_data['custom_data'] ) && is_array( $product_data['custom_data'] ) ) {

				foreach ( $product_data['custom_data'] as $product_field => $value ) {

					static::format_variant_for_feed(
						$product_field,
						$value,
						$parent_attribute_values,
						$variant_feed_column
					);
				}
			}

			if ( ! empty( $variant_feed_column ) ) {
				$product_data['variant'] = '"' . implode( ',', $variant_feed_column ) . '"';
			}

			if ( isset( $parent_attribute_values['gallery_urls'] ) ) {
				$product_data['additional_image_urls'] = array_merge( $product_data['additional_image_urls'], $parent_attribute_values['gallery_urls'] );
			}

			if ( isset( $parent_attribute_values['item_group_id'] ) ) {
				$item_group_id = $parent_attribute_values['item_group_id'];
			}

			$product_data['default_product'] = $parent_attribute_values['default_variant_id'] == $woo_product->id ? 'default' : '';

			// If this group has default variant value, log this product item
			if ( isset( $parent_attribute_values['default_variant_id'] ) && ! empty( $parent_attribute_values['default_variant_id'] ) ) {
				$this->has_default_product_count++;
			} else {
				$this->no_default_product_count++;
			}
		}

		// log simple product
		if ( ! isset( $product_data['default_product'] ) ) {

			$this->no_default_product_count++;

			$product_data['default_product'] = '';
		}

		// when dealing with the feed file, only set out-of-stock products as hidden
		if ( Products::product_should_be_deleted( $woo_product->woo_product ) ) {
			$product_data['visibility'] = \WC_Facebookcommerce_Integration::FB_SHOP_PRODUCT_HIDDEN;
		}

		// Sale price, only format if we have a sale price set for the product, else leave as empty ('').
		$sale_price                = static::get_value_from_product_data( $product_data, 'sale_price', '' );
		$sale_price_effective_date = '';
		if ( is_numeric( $sale_price ) && $sale_price > 0 ) {
			$sale_price_effective_date = static::get_value_from_product_data( $product_data, 'sale_price_start_date' ) . '/' . $this->get_value_from_product_data( $product_data, 'sale_price_end_date' );
			$sale_price                = static::format_price_for_feed(
				$sale_price,
				static::get_value_from_product_data( $product_data, 'currency' )
			);
		}

		return $product_data['retailer_id'] . ',' .
		static::format_string_for_feed( static::get_value_from_product_data( $product_data, 'name' ) ) . ',' .
		static::format_string_for_feed( static::get_value_from_product_data( $product_data, 'description' ) ) . ',' .
		static::get_value_from_product_data( $product_data, 'image_url' ) . ',' .
		static::get_value_from_product_data( $product_data, 'url' ) . ',' .
		static::format_string_for_feed( static::get_value_from_product_data( $product_data, 'category' ) ) . ',' .
		static::format_string_for_feed( static::get_value_from_product_data( $product_data, 'brand' ) ) . ',' .
		static::format_price_for_feed(
			static::get_value_from_product_data( $product_data, 'price', 0 ),
			static::get_value_from_product_data( $product_data, 'currency' )
		) . ',' .
		static::get_value_from_product_data( $product_data, 'availability' ) . ',' .
		$item_group_id . ',' .
		static::get_value_from_product_data( $product_data, 'checkout_url' ) . ',' .
		static::format_additional_image_url( static::get_value_from_product_data( $product_data, 'additional_image_urls' ) ) . ',' .
		$sale_price_effective_date . ',' .
		$sale_price . ',' .
		'new' . ',' .
		static::get_value_from_product_data( $product_data, 'visibility' ) . ',' .
		static::get_value_from_product_data( $product_data, 'gender' ) . ',' .
		static::get_value_from_product_data( $product_data, 'color' ) . ',' .
		static::get_value_from_product_data( $product_data, 'size' ) . ',' .
		static::get_value_from_product_data( $product_data, 'pattern' ) . ',' .
		static::get_value_from_product_data( $product_data, 'google_product_category' ) . ',' .
		static::get_value_from_product_data( $product_data, 'default_product' ) . ',' .
		static::get_value_from_product_data( $product_data, 'variant' ) . PHP_EOL;
	}

	private static function format_additional_image_url( $product_image_urls ) {
		// returns the top 10 additional image urls separated by a comma
		// according to feed api rules
		$product_image_urls = array_slice(
			$product_image_urls,
			0,
			self::FB_ADDITIONAL_IMAGES_FOR_FEED
		);
		if ( $product_image_urls ) {
			return '"' . implode( ',', $product_image_urls ) . '"';
		} else {
			return '';
		}
	}

	private static function format_string_for_feed( $text ) {
		if ( (bool) $text ) {
			return '"' . str_replace( '"', "'", $text ) . '"';
		} else {
			return '';
		}
	}

	private static function format_price_for_feed( $value, $currency ) {
		return (string) ( round( $value / (float) 100, 2 ) ) . $currency;
	}

	private static function format_variant_for_feed(
	$product_field,
	$value,
	$parent_attribute_values,
	&$variant_feed_column ) {
		if ( ! array_key_exists( $product_field, $parent_attribute_values ) ) {
			return;
		}
		array_push(
			$variant_feed_column,
			$product_field . ':' .
			implode( '/', $parent_attribute_values[ $product_field ] ) . ':' .
			$value
		);
	}


	/**
	 * Gets the value from the product data.
	 *
	 * This method is used to avoid PHP undefined index notices.
	 *
	 * @since 2.1.0
	 *
	 * @param array  $product_data the product data retrieved from a Woo product passed by reference
	 * @param string $index the data index
	 * @param mixed  $return_if_not_set the value to be returned if product data has no index (default to '')
	 * @return mixed|string the data value or an empty string
	 */
	private static function get_value_from_product_data( &$product_data, $index, $return_if_not_set = '' ) {

		return isset( $product_data[ $index ] ) ? $product_data[ $index ] : $return_if_not_set;
	}


	/**
	 * Gets the status of the configured feed upload.
	 *
	 * The status indicator is one of 'in progress', 'complete', or 'error'.
	 *
	 * @param array $settings
	 * @return string
	 */
	public function is_upload_complete( &$settings ) {
		try {
			$upload_status = 'error';
			$upload_id     = facebook_for_woocommerce()->get_integration()->get_upload_id();
			$result        = facebook_for_woocommerce()->get_api()->read_upload( $upload_id );

			if ( is_wp_error( $result ) || ! isset( $result['body'] ) ) {
				$this->log_feed_progress( json_encode( $result ) );
				return $upload_status;
			}

			if ( isset( $result->end_time ) ) {
				$settings['upload_end_time'] = $result->end_time;
				$upload_status = 'complete';
			} elseif ( 200 === (int) wp_remote_retrieve_response_code( $result ) ) {
				$upload_status = 'in progress';
			}
		} catch ( ApiException $e ) {
			$message = sprintf( 'There was an error trying to upload the configured feed: %s', $e->getMessage() );
			facebook_for_woocommerce()->log( $message );
		}

		return $upload_status;
	}


	// Log progress in local log file and FB.
	public function log_feed_progress( $msg, $object = array() ) {
		WC_Facebookcommerce_Utils::fblog( $msg, $object );
		$msg = empty( $object ) ? $msg : $msg . json_encode( $object );
		WC_Facebookcommerce_Utils::log( $msg );
	}
}
