<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// Add Store Export to WordPress Administration menu
	function woo_ce_admin_menu() {

		add_submenu_page( 'woocommerce', __( 'Store Exporter', 'woo_ce' ), __( 'Store Export', 'woo_ce' ), 'manage_woocommerce', 'woo_ce', 'woo_ce_html_page' );

	}
	add_action( 'admin_menu', 'woo_ce_admin_menu' );

	// HTML template header on Store Exporter screen
	function woo_ce_template_header( $title = '', $icon = 'woocommerce' ) {

		global $woo_ce;

		if( $title )
			$output = $title;
		else
			$output = $woo_ce['menu']; ?>
<div class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-woocommerce-importer"><br /></div>
	<h2>
		<?php echo $output; ?>
		<a href="<?php echo add_query_arg( 'tab', 'export' ); ?>" class="add-new-h2"><?php _e( 'Add New', 'woo_ce' ); ?></a>
	</h2>
<?php
	}

	// HTML template footer on Store Exporter screen
	function woo_ce_template_footer() { ?>
</div>
<?php
	}

	// HTML template for header prompt on Store Exporter screen
	function woo_ce_support_donate() {

		global $woo_ce;

		$output = '';
		$show = true;
		if( function_exists( 'woo_vl_we_love_your_plugins' ) ) {
			if( in_array( $woo_ce['dirname'], woo_vl_we_love_your_plugins() ) )
				$show = false;
		}
		if( function_exists( 'woo_cd_admin_init' ) )
			$show = false;
		if( $show ) {
			$donate_url = 'http://www.visser.com.au/#donations';
			$rate_url = 'http://wordpress.org/support/view/plugin-reviews/' . $woo_ce['dirname'];
			$output = '
	<div id="support-donate_rate" class="support-donate_rate">
		<p>' . sprintf( __( '<strong>Like this Plugin?</strong> %s and %s.', 'woo_ce' ), '<a href="' . $donate_url . '" target="_blank">' . __( 'Donate to support this Plugin', 'woo_ce' ) . '</a>', '<a href="' . add_query_arg( array( 'rate' => '5' ), $rate_url ) . '#postform" target="_blank">rate / review us on WordPress.org</a>' ) . '</p>
	</div>
';
		}
		echo $output;

	}

	// Saves the state of Export fields for next export
	function woo_ce_save_fields( $dataset, $fields = array() ) {

		if( $dataset && !empty( $fields ) ) {
			$type = $dataset[0];
			woo_ce_update_option( $type . '_fields', $fields );
		}

	}

	// File output header for CSV file
	function woo_ce_generate_csv_header( $dataset = '' ) {

		$filename = woo_ce_generate_csv_filename( $dataset );
		if( $filename ) {
			header( 'Content-Encoding: UTF-8' );
			header( 'Content-Type: text/csv; charset=UTF-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Pragma: no-cache' );
			header( 'Expires: 0' );
		}

	}

	// Function to generate filename of CSV file based on the Export type
	function woo_ce_generate_csv_filename( $dataset = '' ) {

		$date = date( 'Ymd' );
		$output = 'woo-export_default-' . $date . '.csv';
		if( $dataset ) {
			$filename = 'woo-export_' . $dataset . '-' . $date . '.csv';
			if( $filename )
				$output = $filename;
		}
		return $output;

	}

	function woo_ce_unload_export_global() {

		global $export;
		unset( $export );

	}

	// HTML template for disabled Filter Orders by Date widget on Store Exporter screen
	function woo_ce_orders_filter_by_date() {

		$current_month = date( 'F' );
		$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
		$order_dates_from = '-';
		$order_dates_to = '-';

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-date" /> <?php _e( 'Filter Orders by Order Date', 'woo_ce' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_month" disabled="disabled" /> <?php _e( 'Current month', 'woo_ce' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_month" disabled="disabled" /> <?php _e( 'Last month', 'woo_ce' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="manual" disabled="disabled" /> <?php _e( 'Manual', 'woo_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="order_dates_from" name="order_dates_from" value="<?php echo $order_dates_from; ?>" class="text" disabled="disabled" /> to <input type="text" size="10" maxlength="10" id="order_dates_to" name="order_dates_to" value="<?php echo $order_dates_to; ?>" class="text" disabled="disabled" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first order to today.', 'woo_ce' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	// HTML template for disabled Filter Orders by Customer widget on Store Exporter screen
	function woo_ce_orders_filter_by_customer() {

		ob_start(); ?>
<p><label for="order_customer"><?php _e( 'Filter Orders by Customer', 'woo_ce' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<select id="order_customer" name="order_customer" disabled="disabled">
		<option value=""><?php _e( 'Show all customers', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Filter Orders by Customer (unique e-mail address) to be included in the export. Default is to include all Orders.', 'woo_ce' ); ?></p>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	// HTML template for disabled Filter Orders by Order Status widget on Store Exporter screen
	function woo_ce_orders_filter_by_status() {

		$order_statuses = woo_ce_get_order_statuses();
		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-status" /> <?php _e( 'Filter Orders by Order Status', 'woo_ce' ); ?></label></p>
<div id="export-orders-filters-status" class="separator">
	<ul>
<?php foreach( $order_statuses as $order_status ) { ?>
		<li><label><input type="checkbox" name="order_filter_status[<?php echo $order_status->name; ?>]" value="<?php echo $order_status->name; ?>" disabled="disabled" /> <?php echo ucfirst( $order_status->name ); ?></label></li>
<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woo_ce' ); ?></p>
</div>
<!-- #export-orders-filters-status -->
<?php
		ob_end_flush();

	}

	// Add Store Export to filter types on the WordPress Media screen
	function woo_ce_add_post_mime_type( $post_mime_types = array() ) {

		$post_mime_types['text/csv'] = array( __( 'Store Exports', 'woo_ce' ), __( 'Manage Store Exports', 'woo_ce' ), _n_noop( 'Store Export <span class="count">(%s)</span>', 'Store Exports <span class="count">(%s)</span>' ) );
		return $post_mime_types;

	}
	add_filter( 'post_mime_types', 'woo_ce_add_post_mime_type' );

	// In-line display of CSV file and export details when viewed via WordPress Media screen
	function woo_ce_read_csv_file( $post = null ) {

		global $woo_ce;

		if( !$post ) {
			if( isset( $_GET['post'] ) )
				$post = get_post( $_GET['post'] );
		}

		if( $post->post_type != 'attachment' )
			return false;

		if( $post->post_mime_type != 'text/csv' )
			return false;

		$filename = $post->post_name;
		$filepath = get_attached_file( $post->ID );
		$contents = __( 'No export entries were found, please try again with different export filters.', 'woo_ce' );
		if( file_exists( $filepath ) ) {
			$handle = fopen( $filepath, "r" );
			$contents = stream_get_contents( $handle );
			fclose( $handle );
		}
		if( $contents )
			include_once( $woo_ce['abspath'] . '/templates/admin/woo-admin_ce-media_csv_file.php' );

		$dataset = get_post_meta( $post->ID, '_woo_export_type', true );
		$columns = get_post_meta( $post->ID, '_woo_columns', true );
		$rows = get_post_meta( $post->ID, '_woo_rows', true );
		$start_time = get_post_meta( $post->ID, '_woo_start_time', true );
		$end_time = get_post_meta( $post->ID, '_woo_end_time', true );
		$idle_memory_start = get_post_meta( $post->ID, '_woo_idle_memory_start', true );
		$data_memory_start = get_post_meta( $post->ID, '_woo_data_memory_start', true );
		$data_memory_end = get_post_meta( $post->ID, '_woo_data_memory_end', true );
		$idle_memory_end = get_post_meta( $post->ID, '_woo_idle_memory_end', true );
		include_once( $woo_ce['abspath'] . '/templates/admin/woo-admin_ce-media_export_details.php' );

	}
	add_action( 'edit_form_after_editor', 'woo_ce_read_csv_file' );

	if( !function_exists( 'woo_ce_current_memory_usage' ) ) {
		function woo_ce_current_memory_usage() {

			$output = '';
			if( function_exists( 'memory_get_usage' ) )
				$output = round( memory_get_usage() / 1024 / 1024, 2 );
			return $output;

		}
	}

	// List of Export types used on Store Exporter screen
	function woo_ce_return_export_types() {

		$export_types = array();
		$export_types['products'] = __( 'Products', 'woo_ce' );
		$export_types['categories'] = __( 'Categories', 'woo_ce' );
		$export_types['tags'] = __( 'Tags', 'woo_ce' );
		$export_types['orders'] = __( 'Orders', 'woo_ce' );
		$export_types['customers'] = __( 'Customers', 'woo_ce' );
		$export_types['coupons'] = __( 'Coupons', 'woo_ce' );
		$export_types = apply_filters( 'woo_ce_export_types', $export_types );
		return $export_types;

	}

	// Returns label of Export type slug used on Store Exporter screen
	function woo_ce_export_type_label( $export_type = '', $echo = false ) {

		$output = '';
		if( !empty( $export_type ) ) {
			$export_types = woo_ce_return_export_types();
			if( array_key_exists( $export_type, $export_types ) )
				$output = $export_types[$export_type];
		}
		if( $echo )
			echo $output;
		else
			return $output;

	}

	// Returns number of an Export type prior to export, used on Store Exporter screen
	function woo_ce_return_count( $dataset ) {

		global $wpdb;

		$count_sql = null;
		switch( $dataset ) {

			// WooCommerce

			case 'products':
				$post_type = 'product';
				$count = wp_count_posts( $post_type );
				break;

			case 'categories':
				$term_taxonomy = 'product_cat';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'tags':
				$term_taxonomy = 'product_tag';
				$count = wp_count_terms( $term_taxonomy );
				break;

			case 'orders':
				$post_type = 'shop_order';
				$count = wp_count_posts( $post_type );
				$exclude_post_types = array( 'auto-draft' );
				if( woo_ce_count_object( $count, $exclude_post_types ) > 100 ) {
					$count = '~' . woo_ce_count_object( $count, $exclude_post_types ) . ' *';
				} else {
					$count = woo_ce_count_object( $count, $exclude_post_types );
				}
				break;

			case 'customers':
				$post_type = 'shop_order';
				$count = wp_count_posts( $post_type );
				$exclude_post_types = array( 'auto-draft' );
				if( woo_ce_count_object( $count, $exclude_post_types ) > 100 ) {
						$count = '~' . woo_ce_count_object( $count, $exclude_post_types ) . ' *';
				} else {
					$count = 0;
					$args = array(
						'post_type' => $post_type,
						'numberposts' => -1,
						'post_status' => woo_ce_post_statuses(),
						'cache_results' => false,
						'tax_query' => array(
							array(
								'taxonomy' => 'shop_order_status',
								'field' => 'slug',
								'terms' => array( 'pending', 'on-hold', 'processing', 'completed' )
							)
						)
					);
					$orders = get_posts( $args );
					if( $orders ) {
						$customers = array();
						foreach( $orders as $order ) {
							$order->email = get_post_meta( $order->ID, '_billing_email', true );
							if( empty( $order->email ) ) {
								if( $order->user_id = get_post_meta( $order->ID, '_customer_user', true ) ) {
									$user = get_userdata( $order->user_id );
									if( $user )
										$order->email = $user->user_email;
									unset( $user );
								} else {
									$order->email = '-';
								}
							}
							if( !in_array( $order->email, $customers ) ) {
								$customers[$order->ID] = $order->email;
								$count++;
							}
						}
					}
				}
				break;

			case 'coupons':
				$post_type = 'shop_coupon';
				$count = wp_count_posts( $post_type );
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				$count = woo_ce_count_object( $count );
				return $count;
			} else {
				if( $count_sql )
					$count = $wpdb->get_var( $count_sql );
				else
					$count = 0;
			}
			return $count;
		} else {
			return 0;
		}

	}

	// Export process for CSV file
	function woo_ce_export_dataset( $dataset, $args = array() ) {

		global $wpdb, $woo_ce, $export;

		$csv = '';
		if( $export->bom )
			$csv .= chr(239) . chr(187) . chr(191) . '';
		$separator = $export->delimiter;
		$export->args = $args;
		foreach( $dataset as $datatype ) {

			$csv = '';
			switch( $datatype ) {

				// Products
				case 'products':
					$fields = woo_ce_get_product_fields( 'summary' );
					if( $export->fields = array_intersect_assoc( $fields, $export->fields ) ) {
						foreach( $export->fields as $key => $field )
							$export->columns[] = woo_ce_get_product_field( $key );
					}
					$export->data_memory_start = woo_ce_current_memory_usage();
					if( $products = woo_ce_get_products( $export->args ) ) {
						$export->total_rows = count( $products );
						$size = count( $export->columns );
						$export->total_columns = $size;
						for( $i = 0; $i < $size; $i++ ) {
							if( $i == ( $size - 1 ) )
								$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
							else
								$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
						}
						unset( $export->columns );
						$weight_unit = get_option( 'woocommerce_weight_unit' );
						$dimension_unit = get_option( 'woocommerce_dimension_unit' );
						$height_unit = $dimension_unit;
						$width_unit = $dimension_unit;
						$length_unit = $dimension_unit;
						foreach( $products as $product ) {
							foreach( $export->fields as $key => $field ) {
								if( isset( $product->$key ) ) {
									if( is_array( $field ) ) {
										foreach( $field as $array_key => $array_value ) {
											if( !is_array( $array_value ) )
												$csv .= woo_ce_escape_csv_value( $array_value, $export->delimiter, $export->escape_formatting );
										}
									} else {
										$csv .= woo_ce_escape_csv_value( $product->$key, $export->delimiter, $export->escape_formatting );
									}
								}
								$csv .= $separator;
							}
							$csv = substr( $csv, 0, -1 ) . "\n";
						}
						unset( $products, $product );
					}
					$export->data_memory_end = woo_ce_current_memory_usage();
					break;

				// Categories
				case 'categories':
					$fields = woo_ce_get_category_fields( 'summary' );
					if( $export->fields = array_intersect_assoc( $fields, $export->fields ) ) {
						foreach( $export->fields as $key => $field )
							$export->columns[] = woo_ce_get_category_field( $key );
					}
					$export->data_memory_start = woo_ce_current_memory_usage();
					if( $categories = woo_ce_get_product_categories() ) {
						$export->total_rows = count( $categories );
						$size = count( $export->columns );
						$export->total_columns = $size;
						for( $i = 0; $i < $size; $i++ ) {
							if( $i == ( $size - 1 ) )
								$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
							else
								$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
						}
						unset( $export->columns );
						foreach( $categories as $category ) {
							foreach( $export->fields as $key => $field ) {
								if( isset( $category->$key ) )
									$csv .= woo_ce_escape_csv_value( $category->$key, $export->delimiter, $export->escape_formatting );
								$csv .= $separator;
							}
							$csv = substr( $csv, 0, -1 ) . "\n";
						}
						unset( $categories, $category );
					}
					$export->data_memory_end = woo_ce_current_memory_usage();
					break;

				// Tags
				case 'tags':
					$fields = woo_ce_get_tag_fields( 'summary' );
					if( $export->fields = array_intersect_assoc( $fields, $export->fields ) ) {
						foreach( $export->fields as $key => $field )
							$export->columns[] = woo_ce_get_tag_field( $key );
					}
					$export->data_memory_start = woo_ce_current_memory_usage();
					if( $tags = woo_ce_get_product_tags() ) {
						$export->total_rows = count( $tags );
						$size = count( $export->columns );
						$export->total_columns = $size;
						for( $i = 0; $i < $size; $i++ ) {
							if( $i == ( $size - 1 ) )
								$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
							else
								$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
						}
						unset( $export->columns );
						foreach( $tags as $tag ) {
							foreach( $export->fields as $key => $field ) {
								if( isset( $tag->$key ) )
									$csv .= woo_ce_escape_csv_value( $tag->$key, $export->delimiter, $export->escape_formatting );
								$csv .= $separator;
							}
							$csv = substr( $csv, 0, -1 ) . "\n";
						}
						unset( $tags, $tag );
					}
					$export->data_memory_end = woo_ce_current_memory_usage();
					break;

				// Orders
				case 'orders':
				// Customers
				case 'customers':
				// Coupons
				case 'coupons':
					$csv = apply_filters( 'woo_ce_export_dataset', $datatype, $export );
					break;

			}
			if( $csv ) {
				$csv = utf8_decode( $csv );
				if( isset( $woo_ce['debug'] ) && $woo_ce['debug'] )
					$woo_ce['debug_log'] = $csv;
				else
					return $csv;
			} else {
				return false;
			}

		}

	}

	// Returns a list of WooCommerce Products to export process
	function woo_ce_get_products( $args = array() ) {

		$limit_volume = -1;
		$offset = 0;
		$product_categories = false;
		$product_tags = false;
		$product_status = false;
		$product_type = false;
		if( $args ) {
			$limit_volume = $args['limit_volume'];
			$offset = $args['offset'];
			if( !empty( $args['product_categories'] ) )
				$product_categories = $args['product_categories'];
			if( !empty( $args['product_tags'] ) )
				$product_tags = $args['product_tags'];
			if( !empty( $args['product_status'] ) )
				$product_status = $args['product_status'];
			if( !empty( $args['product_type'] ) )
				$product_type = $args['product_type'];
		}
		$post_type = array( 'product', 'product_variation' );
		$args = array(
			'post_type' => $post_type,
			'numberposts' => $limit_volume,
			'offset' => $offset,
			'orderby' => 'ID',
			'order' => 'ASC',
			'post_status' => woo_ce_post_statuses(),
			'cache_results' => false
		);
		if( $product_categories ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'field' => 'id',
					'terms' => $product_categories
				)
			);
		}
		if( $product_tags ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'product_tag',
					'field' => 'id',
					'terms' => $product_tags
				)
			);
		}
		if( $product_status )
			$args['post_status'] = woo_ce_post_statuses( $product_status, true );
		if( $product_type ) {
			if( in_array( 'variation', $product_type ) ) {
				$args['post_type'] = 'product_variation';
			} else {
				$args['tax_query'] = array(
					array(
						'taxonomy' => 'product_type',
						'field' => 'slug',
						'terms' => $product_type
					)
				);
			}
		}
		$products = get_posts( $args );
		if( $products ) {
			$weight_unit = get_option( 'woocommerce_weight_unit' );
			$dimension_unit = get_option( 'woocommerce_dimension_unit' );
			$height_unit = $dimension_unit;
			$width_unit = $dimension_unit;
			$length_unit = $dimension_unit;
			foreach( $products as $key => $product ) {
				$products[$key]->parent_id = '';
				$products[$key]->parent_sku = '';
				if( $product->post_type == 'product_variation' ) {
					// Assign Parent ID for Variants then check if Parent exists
					if( $products[$key]->parent_id = $product->post_parent ) {
						if( !get_posts( 'p=' . $products[$key]->parent_id ) )
							unset( $products[$key] );
					}
				}
				$products[$key]->parent_sku = get_post_meta( $product->post_parent, '_sku', true );
				$products[$key]->product_id = $product->ID;
				$products[$key]->sku = get_post_meta( $product->ID, '_sku', true );
				$products[$key]->name = get_the_title( $product->ID );
				$products[$key]->description = woo_ce_clean_html( $product->post_content );
				$products[$key]->regular_price = get_post_meta( $product->ID, '_regular_price', true );
				$products[$key]->price = get_post_meta( $product->ID, '_price', true );
				if( !empty( $products[$key]->regular_price ) && ( $products[$key]->regular_price <> $products[$key]->price ) )
					$products[$key]->price = $products[$key]->regular_price;
				$products[$key]->sale_price = get_post_meta( $product->ID, '_sale_price', true );
				$products[$key]->sale_price_dates_from = woo_ce_format_sale_price_dates( get_post_meta( $product->ID, '_sale_price_dates_from', true ) );
				$products[$key]->sale_price_dates_to = woo_ce_format_sale_price_dates( get_post_meta( $product->ID, '_sale_price_dates_to', true ) );
				$products[$key]->slug = $product->post_name;
				$products[$key]->permalink = get_permalink( $product->ID );
				$products[$key]->excerpt = woo_ce_clean_html( $product->post_excerpt );
				$products[$key]->type = woo_ce_get_product_assoc_type( $product->ID );
				if( $product->post_type == 'product_variation' )
					$products[$key]->type = __( 'Variation', 'woo_ce' );
				$products[$key]->visibility = woo_ce_format_visibility( get_post_meta( $product->ID, '_visibility', true ) );
				$products[$key]->featured = woo_ce_format_switch( get_post_meta( $product->ID, '_featured', true ) );
				$products[$key]->virtual = woo_ce_format_switch( get_post_meta( $product->ID, '_virtual', true ) );
				$products[$key]->downloadable = woo_ce_format_switch( get_post_meta( $product->ID, '_downloadable', true ) );
				$products[$key]->weight = get_post_meta( $product->ID, '_weight', true );
				$products[$key]->weight_unit = $weight_unit;
				$products[$key]->height = get_post_meta( $product->ID, '_height', true );
				$products[$key]->height_unit = $height_unit;
				$products[$key]->width = get_post_meta( $product->ID, '_width', true );
				$products[$key]->width_unit = $width_unit;
				$products[$key]->length = get_post_meta( $product->ID, '_length', true );
				$products[$key]->length_unit = $length_unit;
				$products[$key]->category = woo_ce_get_product_assoc_categories( $product->ID );
				$products[$key]->tag = woo_ce_get_product_assoc_tags( $product->ID );
				$products[$key]->manage_stock = woo_ce_format_switch( get_post_meta( $product->ID, '_manage_stock', true ) );
				$products[$key]->allow_backorders = woo_ce_format_switch( get_post_meta( $product->ID, '_backorders', true ) );
				$products[$key]->sold_individually = woo_ce_format_switch( get_post_meta( $product->ID, '_sold_individually', true ) );
				$products[$key]->upsell_ids = woo_ce_convert_product_ids( get_post_meta( $product->ID, '_upsell_ids', true ) );
				$products[$key]->crosssell_ids = woo_ce_convert_product_ids( get_post_meta( $product->ID, '_crosssell_ids', true ) );
				$products[$key]->quantity = get_post_meta( $product->ID, '_stock', true );
				$products[$key]->stock_status = woo_ce_format_stock_status( get_post_meta( $product->ID, '_stock_status', true ) );
				$products[$key]->image = woo_ce_get_product_assoc_featured_image( $product->ID );
				$products[$key]->product_gallery = woo_ce_get_product_assoc_product_gallery( $product->ID );
				$products[$key]->tax_status = woo_ce_format_tax_status( get_post_meta( $product->ID, '_tax_status', true ) );
				$products[$key]->tax_class = woo_ce_format_tax_class( get_post_meta( $product->ID, '_tax_class', true ) );
				$products[$key]->product_url = get_post_meta( $product->ID, '_product_url', true );
				$products[$key]->button_text = get_post_meta( $product->ID, '_button_text', true );
				$products[$key]->file_download = woo_ce_get_product_assoc_file_downloads( $product->ID );
				$products[$key]->download_limit = get_post_meta( $product->ID, '_download_limit', true );
				$products[$key]->download_expiry = get_post_meta( $product->ID, '_download_expiry', true );
				$products[$key]->purchase_note = get_post_meta( $product->ID, '_purchase_note', true );
				$products[$key]->product_status = woo_ce_format_product_status( $product->post_status );
				$products[$key]->comment_status = woo_ce_format_comment_status( $product->comment_status );
				if( $attributes = woo_ce_get_product_attributes() ) {
					if( $product->post_type == 'product_variation' ) {
						foreach( $attributes as $attribute ) {
							$products[$key]->{'attribute_' . $attribute->attribute_name} = get_post_meta( $product->ID, sprintf( 'attribute_pa_%s', $attribute->attribute_name ), true );
						}
					} else {
						$products[$key]->attributes = maybe_unserialize( get_post_meta( $product->ID, '_product_attributes', true ) );
						if( !empty( $products[$key]->attributes ) ) {
							foreach( $attributes as $attribute ) {
								if( isset( $products[$key]->attributes['pa_' . $attribute->attribute_name] ) )
									$products[$key]->{'attribute_' . $attribute->attribute_name} = woo_ce_get_product_assoc_attributes( $product->ID, $products[$key]->attributes['pa_' . $attribute->attribute_name] );
							}
						}
					}
				}
				$products[$key] = apply_filters( 'woo_ce_product_item', $products[$key], $product->ID );
			}
		}
		return $products;

	}

	// Returns Product Categories associated to a specific Product
	function woo_ce_get_product_assoc_categories( $product_id = 0 ) {

		global $export;

		$output = '';
		$term_taxonomy = 'product_cat';
		if( $product_id )
			$categories = wp_get_object_terms( $product_id, $term_taxonomy );
		if( $categories ) {
			$size = count( $categories );
			for( $i = 0; $i < $size; $i++ ) {
				if( $categories[$i]->parent == '0' ) {
					$output .= $categories[$i]->name . $export->category_separator;
				} else {
					// Check if Parent -> Child
					$parent_category = get_term( $categories[$i]->parent, $term_taxonomy );
					// Check if Parent -> Child -> Subchild
					if( $parent_category->parent == '0' ) {
						$output .= $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
						$output = str_replace( $parent_category->name . $export->category_separator, '', $output );
					} else {
						$root_category = get_term( $parent_category->parent, $term_taxonomy );
						$output .= $root_category->name . '>' . $parent_category->name . '>' . $categories[$i]->name . $export->category_separator;
						$output = str_replace( array(
							$root_category->name . '>' . $parent_category->name . $export->category_separator,
							$parent_category->name . $export->category_separator
						), '', $output );
					}
					unset( $root_category, $parent_category );
				}
			}
			$output = substr( $output, 0, -1 );
		} else {
			$output .= __( 'Uncategorized', 'woo_ce' );
		}
		return $output;

	}

	// Returns Product Tags associated to a specific Product
	function woo_ce_get_product_assoc_tags( $product_id = 0 ) {

		global $export;

		$output = '';
		$term_taxonomy = 'product_tag';
		$tags = wp_get_object_terms( $product_id, $term_taxonomy );
		if( $tags ) {
			$size = count( $tags );
			for( $i = 0; $i < $size; $i++ ) {
				$tag = get_term( $tags[$i]->term_id, $term_taxonomy );
				$output .= $tag->name . $export->category_separator;
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	// Returns the Featured Image associated to a specific Product
	function woo_ce_get_product_assoc_featured_image( $product_id = 0 ) {

		$output = '';
		if( $product_id ) {
			$thumbnail_id = get_post_meta( $product_id, '_thumbnail_id', true );
			if( $thumbnail_id ) {
				$thumbnail_post = get_post( $thumbnail_id );
				if( $thumbnail_post )
					$output = $thumbnail_post->guid;
			}
		}
		return $output;

	}

	// Returns the Product Galleries associated to a specific Product
	function woo_ce_get_product_assoc_product_gallery( $product_id = 0 ) {

		global $export;

		$output = '';
		if( $product_id ) {
			$images = get_post_meta( $product_id, '_product_image_gallery', true );
			if( $images ) {
				$images = str_replace( ',', $export->category_separator, $images );
				$output = $images;
			}
		}
		return $output;

	}

	// Returns the Product Type of a specific Product
	function woo_ce_get_product_assoc_type( $product_id = 0 ) {

		global $export;

		$output = '';
		$term_taxonomy = 'product_type';
		$types = wp_get_object_terms( $product_id, $term_taxonomy );
		if( $types ) {
			$size = count( $types );
			for( $i = 0; $i < $size; $i++ ) {
				$type = get_term( $types[$i]->term_id, $term_taxonomy );
				$output .= woo_ce_format_product_type( $type->name ) . $export->category_separator;
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	// Returns Product Attributes associated to a specific Product
	function woo_ce_get_product_assoc_attributes( $product_id = 0, $attribute = array() ) {

		global $export;

		$output = '';
		if( $product_id ) {
			if( $attribute['is_taxonomy'] == 1 )
				$terms = wp_get_object_terms( $product_id, $attribute['name'] );
			if( $terms && !is_wp_error( $terms ) ) {
				$size = count( $terms );
				for( $i = 0; $i < $size; $i++ )
					$output .= $terms[$i]->slug . $export->category_separator;
				unset( $terms );
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	// Returns File Downloads associated to a specific Product
	function woo_ce_get_product_assoc_file_downloads( $product_id = 0 ) {

		global $export;

		$output = '';
		if( $product_id ) {
			$file_downloads = maybe_unserialize( get_post_meta( $product_id, '_file_paths', true ) );
			if( $file_downloads ) {
				foreach( $file_downloads as $file_download )
					$output .= $file_download . $export->category_separator;
				unset( $file_downloads );
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	// Returns a list of allowed Export type statuses, can be overridden on a per-Export type basis
	function woo_ce_post_statuses( $extra_status = array(), $override = false ) {

		$output = array(
			'publish',
			'pending',
			'draft',
			'future',
			'private',
			'trash'
		);
		if( $override ) {
			$output = $extra_status;
		} else {
			if( $extra_status )
				$output = array_merge( $output, $extra_status );
		}
		return $output;

	}

	// Returns a list of Product export columns
	function woo_ce_get_product_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'parent_id',
			'label' => __( 'Parent ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'parent_sku',
			'label' => __( 'Parent SKU', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_id',
			'label' => __( 'Product ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sku',
			'label' => __( 'Product SKU', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'name',
			'label' => __( 'Product Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'slug',
			'label' => __( 'Slug', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'permalink',
			'label' => __( 'Permalink', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'description',
			'label' => __( 'Description', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'excerpt',
			'label' => __( 'Excerpt', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'type',
			'label' => __( 'Type', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'visibility',
			'label' => __( 'Visibility', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'featured',
			'label' => __( 'Featured', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'virtual',
			'label' => __( 'Virtual', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'downloadable',
			'label' => __( 'Downloadable', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'price',
			'label' => __( 'Price', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sale_price',
			'label' => __( 'Sale Price', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sale_price',
			'label' => __( 'Sale Price', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sale_price_dates_from',
			'label' => __( 'Sale Price Dates From', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sale_price_dates_to',
			'label' => __( 'Sale Price Dates To', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'weight',
			'label' => __( 'Weight', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'weight_unit',
			'label' => __( 'Weight Unit', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'height',
			'label' => __( 'Height', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'height_unit',
			'label' => __( 'Height Unit', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'width',
			'label' => __( 'Width', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'width_unit',
			'label' => __( 'Width Unit', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'length',
			'label' => __( 'Length', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'length_unit',
			'label' => __( 'Length Unit', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'category',
			'label' => __( 'Category', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'tag',
			'label' => __( 'Tag', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'image',
			'label' => __( 'Featured Image', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_gallery',
			'label' => __( 'Product Gallery', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'tax_status',
			'label' => __( 'Tax Status', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'tax_class',
			'label' => __( 'Tax Class', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'file_download',
			'label' => __( 'File Download', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'download_limit',
			'label' => __( 'Download Limit', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'download_expiry',
			'label' => __( 'Download Expiry', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'manage_stock',
			'label' => __( 'Manage Stock', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'quantity',
			'label' => __( 'Quantity', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'stock_status',
			'label' => __( 'Stock Status', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'allow_backorders',
			'label' => __( 'Allow Backorders', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'sold_individually',
			'label' => __( 'Sold Individually', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'upsell_ids',
			'label' => __( 'Up-Sells', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'crosssell_ids',
			'label' => __( 'Cross-Sells', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_url',
			'label' => __( 'Product URL', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'button_text',
			'label' => __( 'Button Text', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'purchase_note',
			'label' => __( 'Purchase Note', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_status',
			'label' => __( 'Product Status', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'comment_status',
			'label' => __( 'Comment Status', 'woo_ce' ),
			'default' => 1
		);
		if( $attributes = woo_ce_get_product_attributes() ) {
			foreach( $attributes as $attribute ) {
				if( empty( $attribute->attribute_label ) )
					$attribute->attribute_label = $attribute->attribute_name;
				$fields[] = array(
					'name' => sprintf( 'attribute_%s', $attribute->attribute_name ),
					'label' => sprintf( __( 'Attribute: %s', 'woo_ce' ), ucwords( $attribute->attribute_label ) ),
					'default' => 1
				);
			}
		}
/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'woo_ce' ),
			'default' => 1
		);
*/

		// Allow Plugin/Theme authors to add support for additional Product columns
		$fields = apply_filters( 'woo_ce_product_fields', $fields );

		$remember = woo_ce_get_option( 'products_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
			}
		}

		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;

			case 'full':
			default:
				return $fields;

		}

	}

	// Returns the export column header label based on an export column slug
	function woo_ce_get_product_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = woo_ce_get_product_fields();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i]['name'] == $name ) {
					switch( $format ) {

						case 'name':
							$output = $fields[$i]['label'];
							break;

						case 'full':
							$output = $fields[$i];
							break;

					}
					$i = $size;
				}
			}
		}
		return $output;

	}

	// Returns a list of WooCommerce Product Categories to export process
	function woo_ce_get_product_categories() {

		$output = '';
		$term_taxonomy = 'product_cat';
		$args = array(
			'hide_empty' => 0
		);
		$categories = get_terms( $term_taxonomy, $args );
		if( $categories )
			$output = $categories;
		return $output;

	}

	// Returns a list of Category export columns
	function woo_ce_get_category_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'term_id',
			'label' => __( 'Term ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'name',
			'label' => __( 'Category Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'slug',
			'label' => __( 'Category Slug', 'woo_ce' ),
			'default' => 1
		);

/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'woo_ce' ),
			'default' => 1
		);
*/

		// Allow Plugin/Theme authors to add support for additional Category columns
		$fields = apply_filters( 'woo_ce_category_fields', $fields );

		$remember = woo_ce_get_option( 'categories_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
			}
		}

		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;

			case 'full':
			default:
				return $fields;

		}

	}

	// Returns the export column header label based on an export column slug
	function woo_ce_get_category_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = woo_ce_get_category_fields();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i]['name'] == $name ) {
					switch( $format ) {

						case 'name':
							$output = $fields[$i]['label'];
							break;

						case 'full':
							$output = $fields[$i];
							break;

					}
					$i = $size;
				}
			}
		}
		return $output;

	}

	// Returns a list of WooCommerce Product Types to export process
	function woo_ce_get_product_types() {

		$output = '';
		$term_taxonomy = 'product_type';
		$args = array(
			'hide_empty' => 0
		);
		$types = get_terms( $term_taxonomy, $args );
		if( $types ) {
			$size = count( $types );
			for( $i = 0; $i < $size; $i++ )
				$output[$types[$i]->slug] = $types[$i]->name;
			$output['variation'] = __( 'variation', 'woo_ce' );
			asort( $output );
		}
		return $output;

	}

	// Returns a list of WooCommerce Product Tags to export process
	function woo_ce_get_product_tags() {

		$output = '';
		$term_taxonomy = 'product_tag';
		$args = array(
			'hide_empty' => 0
		);
		$tags = get_terms( $term_taxonomy, $args );
		if( $tags )
			$output = $tags;
		return $output;

	}

	// Returns a list of Product Tag export columns
	function woo_ce_get_tag_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'term_id',
			'label' => __( 'Term ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'name',
			'label' => __( 'Tag Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'slug',
			'label' => __( 'Tag Slug', 'woo_ce' ),
			'default' => 1
		);

/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'woo_ce' ),
			'default' => 1
		);
*/

		// Allow Plugin/Theme authors to add support for additional Product Tag columns
		$fields = apply_filters( 'woo_ce_tag_fields', $fields );

		$remember = woo_ce_get_option( 'categories_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
			}
		}

		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;

			case 'full':
			default:
				return $fields;

		}

	}

	// Returns the export column header label based on an export column slug
	function woo_ce_get_tag_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = woo_ce_get_tag_fields();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i]['name'] == $name ) {
					switch( $format ) {

						case 'name':
							$output = $fields[$i]['label'];
							break;

						case 'full':
							$output = $fields[$i];
							break;

					}
					$i = $size;
				}
			}
		}
		return $output;

	}

	// Returns a list of WooCommerce Product Categories to export process
	function woo_ce_get_product_attributes() {

		global $wpdb;

		$output = '';
		$attributes_sql = "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies";
		$attributes = $wpdb->get_results( $attributes_sql );
		$wpdb->flush();
		if( $attributes )
			$output = $attributes;
		unset( $attributes );
		return $output;

	}

	// Returns a list of Order export columns
	function woo_ce_get_order_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'purchase_id',
			'label' => __( 'Purchase ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'purchase_total',
			'label' => __( 'Order Total', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_discount',
			'label' => __( 'Order Discount', 'woo_ce' ),
			'default' => 1
		);
/*
		$fields[] = array(
			'name' => 'order_incl_tax',
			'label' => __( 'Order Incl. Tax', 'woo_ce' ),
			'default' => ''
		);
*/
		$fields[] = array(
			'name' => 'order_excl_tax',
			'label' => __( 'Order Excl. Tax', 'woo_ce' ),
			'default' => ''
		);
/*
		$fields[] = array(
			'name' => 'order_tax_rate',
			'label' => __( 'Order Tax Rate', 'woo_ce' ),
			'default' => ''
		);
*/
		$fields[] = array(
			'name' => 'order_sales_tax',
			'label' => __( 'Sales Tax Total', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_shipping_tax',
			'label' => __( 'Shipping Tax Total', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'payment_gateway',
			'label' => __( 'Payment Gateway', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'shipping_method',
			'label' => __( 'Shipping Method', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'payment_status',
			'label' => __( 'Payment Status', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_key',
			'label' => __( 'Order Key', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'purchase_date',
			'label' => __( 'Purchase Date', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'purchase_time',
			'label' => __( 'Purchase Time', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'customer_note',
			'label' => __( 'Customer Note', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_notes',
			'label' => __( 'Order Notes', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'user_id',
			'label' => __( 'User ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'user_name',
			'label' => __( 'Username', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_full_name',
			'label' => __( 'Billing: Full Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_first_name',
			'label' => __( 'Billing: First Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_last_name',
			'label' => __( 'Billing: Last Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_company',
			'label' => __( 'Billing: Company', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_address',
			'label' => __( 'Billing: Street Address', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_city',
			'label' => __( 'Billing: City', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_postcode',
			'label' => __( 'Billing: ZIP Code', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_state',
			'label' => __( 'Billing: State (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_state_full',
			'label' => __( 'Billing: State', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_country',
			'label' => __( 'Billing: Country (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_country_full',
			'label' => __( 'Billing: Country', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_phone',
			'label' => __( 'Billing: Phone Number', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'billing_email',
			'label' => __( 'Billing: E-mail Address', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_full_name',
			'label' => __( 'Shipping: Full Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_first_name',
			'label' => __( 'Shipping: First Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_last_name',
			'label' => __( 'Shipping: Last Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_company',
			'label' => __( 'Shipping: Company', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_address',
			'label' => __( 'Shipping: Street Address', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_city',
			'label' => __( 'Shipping: City', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_postcode',
			'label' => __( 'Shipping: ZIP Code', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_state',
			'label' => __( 'Shipping: State (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_state_full',
			'label' => __( 'Shipping: State', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_country',
			'label' => __( 'Shipping: Country (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_country_full',
			'label' => __( 'Shipping: Country', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_product_id',
			'label' => __( 'Order Items: Product ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_variation_id',
			'label' => __( 'Order Items: Variation ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_sku',
			'label' => __( 'Order Items: SKU', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_name',
			'label' => __( 'Order Items: Product Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_variation',
			'label' => __( 'Order Items: Product Variation', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_tax_class',
			'label' => __( 'Order Items: Tax Class', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_quantity',
			'label' => __( 'Order Items: Quantity', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_total',
			'label' => __( 'Order Items: Total', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_subtotal',
			'label' => __( 'Order Items: Subtotal', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_tax',
			'label' => __( 'Order Items: Tax', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_tax_subtotal',
			'label' => __( 'Order Items: Tax Subtotal', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'order_items_type',
			'label' => __( 'Order Items: Type', 'woo_ce' ),
			'default' => 1
		);

/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'woo_ce' ),
			'default' => 1
		);
*/

		// Allow Plugin/Theme authors to add support for additional Order columns
		$fields = apply_filters( 'woo_ce_order_fields', $fields );

		$remember = woo_ce_get_option( 'orders_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i] ) {
					if( !array_key_exists( $fields[$i]['name'], $remember ) )
						$fields[$i]['default'] = 0;
				}
			}
		}

		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;

			case 'full':
			default:
				return $fields;

		}

	}

	// Returns the export column header label based on an export column slug
	function woo_ce_get_order_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = woo_ce_get_order_fields();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i]['name'] == $name ) {
					switch( $format ) {

						case 'name':
							$output = $fields[$i]['label'];
							break;

						case 'full':
							$output = $fields[$i];
							break;

					}
					$i = $size;
				}
			}
		}
		return $output;

	}

	if( !function_exists( 'woo_ce_format_order_date' ) ) {
		function woo_ce_format_order_date( $date ) {

			$output = $date;
			if( $date )
				$output = str_replace( '/', '-', $date );
			return $output;

		}
	}

	// Returns a list of Customer export columns
	function woo_ce_get_customer_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'user_id',
			'label' => __( 'User ID', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'user_name',
			'label' => __( 'Username', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_full_name',
			'label' => __( 'Billing: Full Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_first_name',
			'label' => __( 'Billing: First Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_last_name',
			'label' => __( 'Billing: Last Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_company',
			'label' => __( 'Billing: Company', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_address',
			'label' => __( 'Billing: Street Address', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_city',
			'label' => __( 'Billing: City', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_postcode',
			'label' => __( 'Billing: ZIP Code', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_state',
			'label' => __( 'Billing: State (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_state_full',
			'label' => __( 'Billing: State', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_country',
			'label' => __( 'Billing: Country', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_phone',
			'label' => __( 'Billing: Phone Number', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'billing_email',
			'label' => __( 'Billing: E-mail Address', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_full_name',
			'label' => __( 'Shipping: Full Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_first_name',
			'label' => __( 'Shipping: First Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_last_name',
			'label' => __( 'Shipping: Last Name', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_company',
			'label' => __( 'Shipping: Company', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_address',
			'label' => __( 'Shipping: Street Address', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_city',
			'label' => __( 'Shipping: City', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_postcode',
			'label' => __( 'Shipping: ZIP Code', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_state',
			'label' => __( 'Shipping: State (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_state_full',
			'label' => __( 'Shipping: State', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_country',
			'label' => __( 'Shipping: Country (prefix)', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array( 
			'name' => 'shipping_country_full',
			'label' => __( 'Shipping: Country', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'total_spent',
			'label' => __( 'Total Spent', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'completed_orders',
			'label' => __( 'Completed Orders', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'total_orders',
			'label' => __( 'Total Orders', 'woo_ce' ),
			'default' => 1
		);

		// Allow Plugin/Theme authors to add support for additional Customer columns
		$fields = apply_filters( 'woo_ce_customer_fields', $fields );

		$remember = woo_ce_get_option( 'customers_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
			}
		}

		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;

			case 'full':
			default:
				return $fields;

		}

	}

	// Returns the export column header label based on an export column slug
	function woo_ce_get_customer_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = woo_ce_get_customer_fields();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i]['name'] == $name ) {
					switch( $format ) {

						case 'name':
							$output = $fields[$i]['label'];
							break;

						case 'full':
							$output = $fields[$i];
							break;

					}
					$i = $size;
				}
			}
		}
		return $output;

	}

	// Returns a list of Coupon export columns
	function woo_ce_get_coupon_fields( $format = 'full' ) {

		$fields = array();
		$fields[] = array(
			'name' => 'coupon_code',
			'label' => __( 'Coupon Code', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'coupon_description',
			'label' => __( 'Coupon Description', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'discount_type',
			'label' => __( 'Discount Type', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'coupon_amount',
			'label' => __( 'Coupon Amount', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'individual_use',
			'label' => __( 'Individual Use', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'apply_before_tax',
			'label' => __( 'Apply before tax', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'exclude_sale_items',
			'label' => __( 'Exclude sale items', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'minimum_amount',
			'label' => __( 'Minimum Amount', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_ids',
			'label' => __( 'Products', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'exclude_product_ids',
			'label' => __( 'Exclude Products', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'product_categories',
			'label' => __( 'Product Categories', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'exclude_product_categories',
			'label' => __( 'Exclude Product Categories', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'customer_email',
			'label' => __( 'Customer e-mails', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'usage_limit',
			'label' => __( 'Usage Limit', 'woo_ce' ),
			'default' => 1
		);
		$fields[] = array(
			'name' => 'expiry_date',
			'label' => __( 'Expiry Date', 'woo_ce' ),
			'default' => 1
		);

/*
		$fields[] = array(
			'name' => '',
			'label' => __( '', 'woo_ce' ),
			'default' => 1
		);
*/

		// Allow Plugin/Theme authors to add support for additional Coupon columns
		$fields = apply_filters( 'woo_ce_coupon_fields', $fields );

		$remember = woo_ce_get_option( 'coupons_fields' );
		if( $remember ) {
			$remember = maybe_unserialize( $remember );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( !array_key_exists( $fields[$i]['name'], $remember ) )
					$fields[$i]['default'] = 0;
			}
		}

		switch( $format ) {

			case 'summary':
				$output = array();
				$size = count( $fields );
				for( $i = 0; $i < $size; $i++ )
					$output[$fields[$i]['name']] = 'on';
				return $output;
				break;

			case 'full':
			default:
				return $fields;

		}

	}

	// Returns the export column header label based on an export column slug
	function woo_ce_get_coupon_field( $name = null, $format = 'name' ) {

		$output = '';
		if( $name ) {
			$fields = woo_ce_get_coupon_fields();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( $fields[$i]['name'] == $name ) {
					switch( $format ) {

						case 'name':
							$output = $fields[$i]['label'];
							break;

						case 'full':
							$output = $fields[$i];
							break;

					}
					$i = $size;
				}
			}
		}
		return $output;

	}

	// HTML active class for the currently selected tab on the Store Exporter screen
	function woo_ce_admin_active_tab( $tab_name = null, $tab = null ) {

		if( isset( $_GET['tab'] ) && !$tab )
			$tab = $_GET['tab'];
		else
			$tab = 'overview';

		$output = '';
		if( isset( $tab_name ) && $tab_name ) {
			if( $tab_name == $tab )
				$output = ' nav-tab-active';
		}
		echo $output;

	}

	// HTML template for each tab on the Store Exporter screen
	function woo_ce_tab_template( $tab = '' ) {

		global $woo_ce;

		if( !$tab )
			$tab = 'overview';

		// Store Exporter Deluxe
		$woo_cd_exists = false;
		if( !function_exists( 'woo_cd_admin_init' ) ) {
			$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
			$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );
		} else {
			$woo_cd_exists = true;
		}
		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/';

		switch( $tab ) {

			case 'export':
				$dataset = 'products';
				if( isset( $_POST['dataset'] ) )
					$dataset = $_POST['dataset'];

				$products = woo_ce_return_count( 'products' );
				$categories = woo_ce_return_count( 'categories' );
				$tags = woo_ce_return_count( 'tags' );
				$orders = woo_ce_return_count( 'orders' );
				$coupons = woo_ce_return_count( 'coupons' );
				$customers = woo_ce_return_count( 'customers' );

				$product_fields = woo_ce_get_product_fields();
				if( $product_fields ) {
					foreach( $product_fields as $key => $product_field ) {
						if( !isset( $product_fields[$key]['disabled'] ) )
							$product_fields[$key]['disabled'] = 0;
					}
					$product_categories = woo_ce_get_product_categories();
					$product_tags = woo_ce_get_product_tags();
					$product_statuses = get_post_statuses();
					$product_statuses['trash'] = __( 'Trash', 'woo_ce' );
					$product_types = woo_ce_get_product_types();
				}
				$category_fields = woo_ce_get_category_fields();
				$tag_fields = woo_ce_get_tag_fields();
				$order_fields = woo_ce_get_order_fields();
				$customer_fields = woo_ce_get_customer_fields();
				$coupon_fields = woo_ce_get_coupon_fields();

				$delimiter = woo_ce_get_option( 'delimiter', ',' );
				$category_separator = woo_ce_get_option( 'category_separator', '|' );
				$bom = woo_ce_get_option( 'bom', 1 );
				$escape_formatting = woo_ce_get_option( 'escape_formatting', 'all' );
				$limit_volume = woo_ce_get_option( 'limit_volume' );
				$offset = woo_ce_get_option( 'offset' );
				$timeout = woo_ce_get_option( 'timeout', 0 );
				$delete_csv = woo_ce_get_option( 'delete_csv', 0 );
				$file_encodings = false;
				if( function_exists( 'mb_list_encodings' ) )
					$file_encodings = mb_list_encodings();
				break;

			case 'tools':
				// Product Importer Deluxe
				if( function_exists( 'woo_pd_init' ) ) {
					$woo_pd_url = add_query_arg( 'page', 'woo_pd' );
					$woo_pd_target = false;
				} else {
					$woo_pd_url = 'http://www.visser.com.au/woocommerce/plugins/product-importer-deluxe/';
					$woo_pd_target = ' target="_blank"';
				}
				break;

			case 'archive':
				$files = woo_ce_get_archive_files();
				if( $files ) {
					foreach( $files as $key => $file )
						$files[$key] = woo_ce_get_archive_file( $file );
				}
				break;

		}
		if( $tab )
			include_once( $woo_ce['abspath'] . '/templates/admin/woo-admin_ce-export_' . $tab . '.php' );

	}

	// Returns the Post object of the CSV file saved as an attachment to the WordPress Media library
	function woo_ce_save_csv_file_attachment( $filename = '' ) {

		$output = 0;
		if( !empty( $filename ) ) {
			$post_type = 'woo-export';
			$object = array(
				'post_title' => $filename,
				'post_type' => $post_type,
				'post_mime_type' => 'text/csv'
			);
			$post_ID = wp_insert_attachment( $object, $filename );
			if( $post_ID )
				$output = $post_ID;
		}
		return $output;

	}

	// Updates the GUID of the CSV file attachment to match the correct CSV URL
	function woo_ce_save_csv_file_guid( $post_ID, $export_type, $upload_url = '' ) {

		add_post_meta( $post_ID, '_woo_export_type', $export_type );
		if( !empty( $upload_url ) ) {
			$object = array(
				'ID' => $post_ID,
				'guid' => $upload_url
			);
			wp_update_post( $object );
		}

	}

	// Save critical export details against the archived export
	function woo_ce_save_csv_file_details( $post_ID ) {

		global $export;

		add_post_meta( $post_ID, '_woo_start_time', $export->start_time );
		add_post_meta( $post_ID, '_woo_idle_memory_start', $export->idle_memory_start );
		add_post_meta( $post_ID, '_woo_columns', $export->total_columns );
		add_post_meta( $post_ID, '_woo_rows', $export->total_rows );
		add_post_meta( $post_ID, '_woo_data_memory_start', $export->data_memory_start );
		add_post_meta( $post_ID, '_woo_data_memory_end', $export->data_memory_end );

	}

	// Update detail of existing archived export
	function woo_ce_update_csv_file_detail( $post_ID, $detail, $value ) {

		if( strstr( $detail, '_woo_' ) !== false )
			update_post_meta( $post_ID, $detail, $value );

	}

	// Returns a list of WooCommerce Order statuses
	function woo_ce_get_order_statuses() {

		$args = array(
			'hide_empty' => false
		);
		$terms = get_terms( 'shop_order_status', $args );
		return $terms;

	}

	// Displays a HTML notice where the memory allocated to WordPress falls below 64MB
	function woo_ce_memory_prompt() {

		if( !woo_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = (int)( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				ob_start();
				$memory_url = add_query_arg( 'action', 'dismiss_memory_prompt' );
				$message = sprintf( __( 'We recommend setting memory to at least %dMB, your site has %dMB currently allocated. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'woo_ce' ), $minimum_memory_limit, $memory_limit, 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ); ?>
<div class="error settings-error">
	<p>
		<strong><?php echo $message; ?></strong>
		<span style="float:right;"><a href="<?php echo $memory_url; ?>"><?php _e( 'Dismiss', 'woo_ce' ); ?></a></span>
	</p>
</div>
<?php
				ob_end_flush();
			}
		}

	}

	// Displays a HTML notice when a WordPress or Store Exporter error is encountered
	function woo_ce_fail_notices() {

		$message = false;
		if( isset( $_GET['failed'] ) )
			$message = __( 'A WordPress error caused the exporter to fail, please get in touch.', 'woo_ce' );
		if( isset( $_GET['empty'] ) )
			$message = __( 'No export entries were found, please try again with different export filters.', 'woo_ce' );
		if( $message ) {
			ob_start(); ?>
<div class="updated settings-error">
	<p>
		<strong><?php echo $message; ?></strong>
	</p>
</div>
<?php
			ob_end_flush();
		}
	}

	// Returns a list of archived exports
	function woo_ce_get_archive_files() {

		$args = array(
			'post_type' => 'attachment',
			'post_mime_type' => 'text/csv',
			'meta_key' => '_woo_export_type',
			'meta_value' => null,
			'posts_per_page' => -1
		);
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( !empty( $filter ) )
				$args['meta_value'] = $filter;
		}
		$files = get_posts( $args );
		return $files;

	}

	// Returns an archived export with additional details
	function woo_ce_get_archive_file( $file = '' ) {

		$wp_upload_dir = wp_upload_dir();
		$file->export_type = get_post_meta( $file->ID, '_woo_export_type', true );
		$file->export_type_label = woo_ce_export_type_label( $file->export_type );
		if( empty( $file->export_type ) )
			$file->export_type = __( 'Unassigned', 'woo_ce' );
		if( empty( $file->guid ) )
			$file->guid = $wp_upload_dir['url'] . '/' . basename( $file->post_title );
		$file->post_mime_type = get_post_mime_type( $file->ID );
		if( !$file->post_mime_type )
			$file->post_mime_type = __( 'N/A', 'woo_ce' );
		$file->media_icon = wp_get_attachment_image( $file->ID, array( 80, 60 ), true );
		$author_name = get_user_by( 'id', $file->post_author );
		$file->post_author_name = $author_name->display_name;
		$t_time = strtotime( $file->post_date, current_time( 'timestamp' ) );
		$time = get_post_time( 'G', true, $file->ID, false );
		if( ( abs( $t_diff = time() - $time ) ) < 86400 )
			$file->post_date = sprintf( __( '%s ago' ), human_time_diff( $time ) );
		else
			$file->post_date = mysql2date( __( 'Y/m/d' ), $file->post_date );
		unset( $author_name, $t_time, $time );
		return $file;

	}

	// HTML template for displaying the current export type filter on the Archives screen
	function woo_ce_archives_quicklink_current( $current = '' ) {

		$output = '';
		if( isset( $_GET['filter'] ) ) {
			$filter = $_GET['filter'];
			if( $filter == $current )
				$output = ' class="current"';
		} else if( $current == 'all' ) {
			$output = ' class="current"';
		}
		echo $output;

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_archives_quicklink_count( $type = '' ) {

		$output = '0';
		$args = array(
			'post_type' => 'attachment',
			'meta_key' => '_woo_export_type',
			'meta_value' => null,
			'numberposts' => -1
		);
		if( $type )
			$args['meta_value'] = $type;
		$posts = get_posts( $args );
		if( $posts )
			$output = count( $posts );
		echo $output;

	}

	/* End of: WordPress Administration */

}

/* Start of: Common */

function woo_ce_get_option( $option = null, $default = false ) {

	global $woo_ce;

	$output = '';
	if( isset( $option ) ) {
		$separator = '_';
		$output = get_option( $woo_ce['prefix'] . $separator . $option, $default );
	}
	return $output;

}

function woo_ce_update_option( $option = null, $value = null ) {

	global $woo_ce;

	$output = false;
	if( isset( $option ) && isset( $value ) ) {
		$separator = '_';
		$output = update_option( $woo_ce['prefix'] . $separator . $option, $value );
	}
	return $output;

}

/* End of: Common */
?>