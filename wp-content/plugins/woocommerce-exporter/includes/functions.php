<?php
include_once( WOO_CE_PATH . 'includes/product.php' );
include_once( WOO_CE_PATH . 'includes/category.php' );
include_once( WOO_CE_PATH . 'includes/tag.php' );
include_once( WOO_CE_PATH . 'includes/brand.php' );
include_once( WOO_CE_PATH . 'includes/order.php' );
include_once( WOO_CE_PATH . 'includes/customer.php' );
include_once( WOO_CE_PATH . 'includes/user.php' );
include_once( WOO_CE_PATH . 'includes/coupon.php' );
include_once( WOO_CE_PATH . 'includes/subscription.php' );
include_once( WOO_CE_PATH . 'includes/product_vendor.php' );
include_once( WOO_CE_PATH . 'includes/commission.php' );
include_once( WOO_CE_PATH . 'includes/shipping_class.php' );

// Check if we are using PHP 5.3 and above
if( version_compare( phpversion(), '5.3' ) >= 0 )
	include_once( WOO_CE_PATH . 'includes/legacy.php' );
include_once( WOO_CE_PATH . 'includes/formatting.php' );

include_once( WOO_CE_PATH . 'includes/export-csv.php' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( WOO_CE_PATH . 'includes/admin.php' );
	include_once( WOO_CE_PATH . 'includes/settings.php' );

	function woo_ce_detect_non_woo_install() {

		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter/usage/';
		if( !woo_is_woo_activated() && ( woo_is_jigo_activated() || woo_is_wpsc_activated() ) ) {
			$message = sprintf( __( 'We have detected another e-Commerce Plugin than WooCommerce activated, please check that you are using Store Exporter for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
			woo_ce_admin_notice( $message, 'error', 'plugins.php' );
		} else if( !woo_is_woo_activated() ) {
			$message = sprintf( __( 'We have been unable to detect the WooCommerce Plugin activated on this WordPress site, please check that you are using Store Exporter for the correct platform. <a href="%s" target="_blank">Need help?</a>', 'woo_ce' ), $troubleshooting_url );
			woo_ce_admin_notice( $message, 'error', 'plugins.php' );
		}
		woo_ce_plugin_page_notices();

	}

	// Displays a HTML notice when a WordPress or Store Exporter error is encountered
	function woo_ce_fail_notices() {

		$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter/usage/';

		// If the failed flag is set then prepare for an error notice
		if( isset( $_GET['failed'] ) ) {
			$message = '';
			if( isset( $_GET['message'] ) )
				$message = urldecode( $_GET['message'] );
			if( $message )
				$message = sprintf( __( 'A WordPress or server error caused the exporter to fail, the exporter was provided with a reason: <em>%s</em>', 'woo_ce' ), $message ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			else
				$message = __( 'A WordPress or server error caused the exporter to fail, no reason was provided, please get in touch so we can reproduce and resolve this.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			woo_ce_admin_notice_html( $message, 'error' );
		}

		// Displays a HTML notice where the memory allocated to WordPress falls below 64MB
		if( !woo_ce_get_option( 'dismiss_execution_time_prompt', 0 ) ) {
			$max_execution_time = absint( ini_get( 'max_execution_time' ) );
			$response = ini_set( 'max_execution_time', 120 );
			if( $response == false || ( $response != $max_execution_time ) ) {
				$dismiss_url = add_query_arg( 'action', 'dismiss_execution_time_prompt' );
				$message = sprintf( __( 'We could not override the PHP configuration option <code>max_execution_time</code>, this will limit the size of possible exports. See: <a href="%s" target="_blank">Increasing PHP max_execution_time configuration option</a>', 'woo_ce' ), $troubleshooting_url ) . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
				woo_ce_admin_notice_html( $message, 'error' );
			}
			ini_set( 'max_execution_time', $max_execution_time );
		}

		// Displays a HTML notice where the memory allocated to WordPress falls below 64MB
		if( !woo_ce_get_option( 'dismiss_memory_prompt', 0 ) ) {
			$memory_limit = absint( ini_get( 'memory_limit' ) );
			$minimum_memory_limit = 64;
			if( $memory_limit < $minimum_memory_limit ) {
				$dismiss_url = add_query_arg( 'action', 'dismiss_memory_prompt' );
				$message = sprintf( __( 'We recommend setting memory to at least %dMB, your site has only %dMB allocated to it. See: <a href="%s" target="_blank">Increasing memory allocated to PHP</a>', 'woo_ce' ), $minimum_memory_limit, $memory_limit, $troubleshooting_url ) . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
				woo_ce_admin_notice_html( $message, 'error' );
			}
		}

		// Displays a HTML notice if PHP 5.2 is installed
		if( version_compare( phpversion(), '5.3', '<' ) && !woo_ce_get_option( 'dismiss_php_legacy', 0 ) ) {
			$dismiss_url = add_query_arg( 'action', 'dismiss_php_legacy' );
			$message = sprintf( __( 'Your PHP version (%s) is not supported and is very much out of date, since 2010 all users are strongly encouraged to upgrade to PHP 5.3+ and above. Contact your hosting provider to make this happen. See: <a href="%s" target="_blank">Migrating from PHP 5.2 to 5.3</a>', 'woo_ce' ), phpversion(), $troubleshooting_url ) . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';
			woo_ce_admin_notice_html( $message, 'error' );
		}

		// Displays HTML notice if there are more than 2500 Subscriptions
		if( !woo_ce_get_option( 'dismiss_subscription_prompt', 0 ) ) {
			if( class_exists( 'WC_Subscriptions' ) ) {
				if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
					// Does this store have roughly more than 3000 Subscriptions
					if( WC_Subscriptions::is_large_site() ) {
						$dismiss_url = add_query_arg( 'action', 'dismiss_subscription_prompt' );
						$message = __( 'We\'ve detected the <em>is_large_site</em> flag has been set within WooCommerce Subscriptions. Please get in touch if exports are incomplete as we need to spin up an alternative export process to export Subscriptions from large stores.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)' . '<span style="float:right;"><a href="' . $dismiss_url . '">' . __( 'Dismiss', 'woo_ce' ) . '</a></span>';;
						woo_ce_admin_notice_html( $message, 'error' );
					}
				}
			}
		}

		// If the export failed the WordPress Transient will still exist
		if( get_transient( WOO_CE_PREFIX . '_running' ) ) {
			$message = __( 'A WordPress or server error caused the exporter to fail with a blank screen, this is either a memory or timeout issue, please get in touch so we can reproduce and resolve this.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
			woo_ce_admin_notice_html( $message, 'error' );
			delete_transient( WOO_CE_PREFIX . '_running' );
		}

	}

	// Saves the state of Export fields for next export
	function woo_ce_save_fields( $type = '', $fields = array(), $sorting = array() ) {

		// Default fields
		if( $fields == false && !is_array( $fields ) )
			$fields = array();
		$types = array_keys( woo_ce_return_export_types() );
		if( in_array( $type, $types ) && !empty( $fields ) ) {
			woo_ce_update_option( $type . '_fields', array_map( 'sanitize_text_field', $fields ) );
			woo_ce_update_option( $type . '_sorting', array_map( 'absint', $sorting ) );
		}

	}

	// Returns number of an Export type prior to export, used on Store Exporter screen
	function woo_ce_return_count( $export_type = '', $args = array() ) {

		global $wpdb;

		$count_sql = null;
		$woocommerce_version = woo_get_woo_version();
		switch( $export_type ) {

			case 'product':
				$post_type = array( 'product', 'product_variation' );
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'fields' => 'ids'
				);
				$count_query = new WP_Query( $args );
				$count = $count_query->found_posts;
				break;

			case 'category':
				$term_taxonomy = 'product_cat';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'tag':
				$term_taxonomy = 'product_tag';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'brand':
				$term_taxonomy = apply_filters( 'woo_ce_brand_term_taxonomy', 'product_brand' );
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'order':
				$post_type = 'shop_order';
				// Check if this is a WooCommerce 2.2+ instance (new Post Status)
				if( version_compare( $woocommerce_version, '2.2' ) >= 0 )
					$post_status = ( function_exists( 'wc_get_order_statuses' ) ? apply_filters( 'woo_ce_order_post_status', array_keys( wc_get_order_statuses() ) ) : 'any' );
				else
					$post_status = apply_filters( 'woo_ce_order_post_status', woo_ce_post_statuses() );
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => 1,
					'post_status' => $post_status,
					'fields' => 'ids'
				);
				$count_query = new WP_Query( $args );
				$count = $count_query->found_posts;
				break;

			case 'customer':
				if( $users = woo_ce_return_count( 'user' ) > 1000 ) {
					$count = sprintf( '~%s+', 1000 );
				} else {
					$post_type = 'shop_order';
					$args = array(
						'post_type' => $post_type,
						'posts_per_page' => -1,
						'fields' => 'ids'
					);
					// Check if this is a WooCommerce 2.2+ instance (new Post Status)
					if( version_compare( $woocommerce_version, '2.2' ) >= 0 ) {
						$args['post_status'] = apply_filters( 'woo_ce_customer_post_status', array( 'wc-pending', 'wc-on-hold', 'wc-processing', 'wc-completed' ) );
					} else {
						$args['post_status'] = apply_filters( 'woo_ce_customer_post_status', woo_ce_post_statuses() );
						$args['tax_query'] = array(
							array(
								'taxonomy' => 'shop_order_status',
								'field' => 'slug',
								'terms' => array( 'pending', 'on-hold', 'processing', 'completed' )
							),
						);
					}
					$order_ids = new WP_Query( $args );
					$count = $order_ids->found_posts;
					if( $count > 100 ) {
						$count = sprintf( '~%s', $count );
					} else {
						$customers = array();
						if( $order_ids->posts ) {
							foreach( $order_ids->posts as $order_id ) {
								$email = get_post_meta( $order_id, '_billing_email', true );
								if( !in_array( $email, $customers ) )
									$customers[$order_id] = $email;
								unset( $email );
							}
							$count = count( $customers );
						}
					}
				}
/*
				if( false ) {
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
						unset( $orders, $order );
					}
				}
*/
				break;

			case 'user':
				if( $users = count_users() )
					$count = $users['total_users'];
				break;

			case 'coupon':
				$post_type = 'shop_coupon';
				if( post_type_exists( $post_type ) )
					$count = wp_count_posts( $post_type );
				break;

			case 'subscription':
				$count = 0;
				// Check that WooCommerce Subscriptions exists
				if( class_exists( 'WC_Subscriptions' ) ) {
					if( method_exists( 'WC_Subscriptions', 'is_large_site' ) ) {
						// Does this store have roughly more than 3000 Subscriptions
						if( false === WC_Subscriptions::is_large_site() ) {
							if( class_exists( 'WC_Subscriptions_Manager' ) ) {
								// Check that the get_all_users_subscriptions() function exists
								if( method_exists( 'WC_Subscriptions_Manager', 'get_all_users_subscriptions' ) ) {
									if( $subscriptions = WC_Subscriptions_Manager::get_all_users_subscriptions() ) {
										foreach( $subscriptions as $key => $user_subscription ) {
											if( !empty( $user_subscription ) ) {
												foreach( $user_subscription as $subscription )
													$count++;
											}
										}
										unset( $subscriptions, $subscription, $user_subscription );
									}
								}
							}
						} else {
							if( method_exists( 'WC_Subscriptions', 'get_total_subscription_count' ) )
								$count = WC_Subscriptions::get_total_subscription_count();
							else
								$count = "~2500";
						}
					}
				}
				break;

			case 'product_vendor':
				$term_taxonomy = 'shop_vendor';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'commission':
				$post_type = 'shop_commission';
				if( post_type_exists( $post_type ) )
					$count = wp_count_posts( $post_type );
				break;

			case 'shipping_class':
				$term_taxonomy = 'product_shipping_class';
				if( taxonomy_exists( $term_taxonomy ) )
					$count = wp_count_terms( $term_taxonomy );
				break;

			case 'attribute':
				$attributes = ( function_exists( 'wc_get_attribute_taxonomies' ) ? wc_get_attribute_taxonomies() : array() );
				$count = count( $attributes );
				break;

		}
		if( isset( $count ) || $count_sql ) {
			if( isset( $count ) ) {
				if( is_object( $count ) ) {
					$count = (array)$count;
					$count = (int)array_sum( $count );
				}
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

	// In-line display of export file and export details when viewed via WordPress Media screen
	function woo_ce_read_export_file( $post = false ) {

		if( empty( $post ) ) {
			if( isset( $_GET['post'] ) )
				$post = get_post( $_GET['post'] );
		}

		if( $post->post_type != 'attachment' )
			return false;

		// Check if the Post matches one of our Post Mime Types
		if( !in_array( $post->post_mime_type, array( 'text/csv', 'application/xml', 'application/vnd.ms-excel' ) ) )
			return false;

		$filename = $post->post_name;
		$filepath = get_attached_file( $post->ID );
		$contents = __( 'No export entries were found, please try again with different export filters.', 'woo_ce' );
		if( file_exists( $filepath ) ) {
			$handle = fopen( $filepath, "r" );
			$contents = stream_get_contents( $handle );
			fclose( $handle );
		} else {
			// This resets the _wp_attached_file Post meta key to the correct value
			update_attached_file( $post->ID, $post->guid );
			// Try grabbing the file contents again
			$filepath = get_attached_file( $post->ID );
			if( file_exists( $filepath ) ) {
				$handle = fopen( $filepath, "r" );
				$contents = stream_get_contents( $handle );
				fclose( $handle );
			}
		}
		if( !empty( $contents ) )
			include_once( WOO_CE_PATH . 'templates/admin/media-csv_file.php' );


		// We can still show the Export Details for any supported Post Mime Type
		$export_type = get_post_meta( $post->ID, '_woo_export_type', true );
		$columns = get_post_meta( $post->ID, '_woo_columns', true );
		$rows = get_post_meta( $post->ID, '_woo_rows', true );
		$start_time = get_post_meta( $post->ID, '_woo_start_time', true );
		$end_time = get_post_meta( $post->ID, '_woo_end_time', true );
		$idle_memory_start = get_post_meta( $post->ID, '_woo_idle_memory_start', true );
		$data_memory_start = get_post_meta( $post->ID, '_woo_data_memory_start', true );
		$data_memory_end = get_post_meta( $post->ID, '_woo_data_memory_end', true );
		$idle_memory_end = get_post_meta( $post->ID, '_woo_idle_memory_end', true );

		include_once( WOO_CE_PATH . 'templates/admin/media-export_details.php' );

	}
	add_action( 'edit_form_after_editor', 'woo_ce_read_export_file' );

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

	// Returns a list of archived exports
	function woo_ce_get_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array( 'text/csv', 'application/xml', 'application/vnd.ms-excel' ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
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

	function woo_ce_nuke_archive_files() {

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'post_mime_type' => array( 'text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/xml', 'application/rss+xml' ),
			'meta_key' => $meta_key,
			'meta_value' => null,
			'post_status' => 'any',
			'posts_per_page' => -1,
			'fields' => 'ids'
		);
		$post_query = new WP_Query( $args );
		if( !empty( $post_query->found_posts ) ) {
			foreach( $post_query->posts as $post )
				wp_delete_attachment( $post, true );
			return true;
		}

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
		if( $author_name = get_user_by( 'id', $file->post_author ) )
			$file->post_author_name = $author_name->display_name;
		$file->post_date = woo_ce_format_archive_date( $file->ID );
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

		$post_type = 'attachment';
		$meta_key = '_woo_export_type';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'post_status' => 'any',
			'fields' => 'ids'
		);
		if( !empty( $type ) )
			$args['meta_value'] = $type;
		$post_query = new WP_Query( $args );
		return absint( $post_query->found_posts );

	}

	/* End of: WordPress Administration */

}

// Export process for CSV file
function woo_ce_export_dataset( $export_type = null, &$output = null ) {

	global $export;

	$separator = $export->delimiter;
	$export->columns = array();
	$export->total_rows = 0;
	$export->total_columns = 0;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	set_transient( WOO_CE_PREFIX . '_running', time(), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );

	// Load up the fatal error notice if we 500 Internal Server Error (memory), hit a server timeout or encounter a fatal PHP error
	add_action( 'shutdown', 'woo_ce_fatal_error' );

	// Drop in our content filters here
	add_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	switch( $export_type ) {

		// Products
		case 'product':
			$fields = woo_ce_get_product_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_product_field( $key );
			}
			$export->total_columns = $size = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $products = woo_ce_get_products( $export->args ) ) {
				$export->total_rows = count( $products );
				// Generate the export headers
				if( in_array( $export->export_format, array( 'csv' ) ) ) {
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
					}
				}
				$weight_unit = get_option( 'woocommerce_weight_unit' );
				$dimension_unit = get_option( 'woocommerce_dimension_unit' );
				$height_unit = $dimension_unit;
				$width_unit = $dimension_unit;
				$length_unit = $dimension_unit;
				if( !empty( $export->fields ) ) {
					foreach( $products as $product ) {

						$product = woo_ce_get_product_data( $product, $export->args );
						foreach( $export->fields as $key => $field ) {
							if( isset( $product->$key ) ) {
								if( is_array( $field ) ) {
									foreach( $field as $array_key => $array_value ) {
										if( !is_array( $array_value ) ) {
											if( in_array( $export->export_format, array( 'csv' ) ) )
												$output .= woo_ce_escape_csv_value( $array_value, $export->delimiter, $export->escape_formatting );
										}
									}
								} else {
									if( in_array( $export->export_format, array( 'csv' ) ) )
										$output .= woo_ce_escape_csv_value( $product->$key, $export->delimiter, $export->escape_formatting );
								}
							}
							if( in_array( $export->export_format, array( 'csv' ) ) )
								$output .= $separator;
						}

						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output = substr( $output, 0, -1 ) . "\n";
					}
				}
				unset( $products, $product );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Categories
		case 'category':
			$fields = woo_ce_get_category_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_category_field( $key );
			}
			$export->total_columns = $size = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$category_args = array(
				'orderby' => ( isset( $export->args['category_orderby'] ) ? $export->args['category_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['category_order'] ) ? $export->args['category_order'] : 'ASC' ),
			);
			if( $categories = woo_ce_get_product_categories( $category_args ) ) {
				$export->total_rows = count( $categories );
				// Generate the export headers
				if( in_array( $export->export_format, array( 'csv' ) ) ) {
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
					}
				}
				if( !empty( $export->fields ) ) {
					foreach( $categories as $category ) {

						foreach( $export->fields as $key => $field ) {
							if( isset( $category->$key ) ) {
								if( in_array( $export->export_format, array( 'csv' ) ) )
									$output .= woo_ce_escape_csv_value( $category->$key, $export->delimiter, $export->escape_formatting );
							}
							if( in_array( $export->export_format, array( 'csv' ) ) )
								$output .= $separator;
						}
						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output = substr( $output, 0, -1 ) . "\n";
					}
				}
				unset( $categories, $category );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Tags
		case 'tag':
			$fields = woo_ce_get_tag_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_tag_field( $key );
			}
			$export->total_columns = $size = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			$tag_args = array(
				'orderby' => ( isset( $export->args['tag_orderby'] ) ? $export->args['tag_orderby'] : 'ID' ),
				'order' => ( isset( $export->args['tag_order'] ) ? $export->args['tag_order'] : 'ASC' ),
			);
			if( $tags = woo_ce_get_product_tags( $tag_args ) ) {
				$export->total_rows = count( $tags );
				// Generate the export headers
				if( in_array( $export->export_format, array( 'csv' ) ) ) {
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$output .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
					}
				}
				if( !empty( $export->fields ) ) {
					foreach( $tags as $tag ) {

						foreach( $export->fields as $key => $field ) {
							if( isset( $tag->$key ) ) {
								if( in_array( $export->export_format, array( 'csv' ) ) )
									$output .= woo_ce_escape_csv_value( $tag->$key, $export->delimiter, $export->escape_formatting );
							}
							if( in_array( $export->export_format, array( 'csv' ) ) )
								$output .= $separator;
						}
						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output = substr( $output, 0, -1 ) . "\n";
					}
				}
				unset( $tags, $tag );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

		// Users
		case 'user':
			$fields = woo_ce_get_user_fields( 'summary' );
			if( $export->fields = array_intersect_assoc( (array)$export->fields, $fields ) ) {
				foreach( $export->fields as $key => $field )
					$export->columns[] = woo_ce_get_user_field( $key );
			}
			$export->total_columns = $size = count( $export->columns );
			$export->data_memory_start = woo_ce_current_memory_usage();
			if( $users = woo_ce_get_users( $export->args ) ) {
				// Generate the export headers
				if( in_array( $export->export_format, array( 'csv' ) ) ) {
					$i = 0;
					foreach( $export->columns as $column ) {
						if( $i == ( $size - 1 ) )
							$output .= woo_ce_escape_csv_value( $column, $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$output .= woo_ce_escape_csv_value( $column, $export->delimiter, $export->escape_formatting ) . $separator;
						$i++;
					}
				}
				if( !empty( $export->fields ) ) {
					foreach( $users as $user ) {

						$user = woo_ce_get_user_data( $user, $export->args );

						foreach( $export->fields as $key => $field ) {
							if( isset( $user->$key ) ) {
								if( in_array( $export->export_format, array( 'csv' ) ) )
									$output .= woo_ce_escape_csv_value( $user->$key, $export->delimiter, $export->escape_formatting );
							}
							if( in_array( $export->export_format, array( 'csv' ) ) )
								$output .= $separator;
						}
						if( in_array( $export->export_format, array( 'csv' ) ) )
							$output = substr( $output, 0, -1 ) . "\n";

					}
				}
				unset( $users, $user );
			}
			$export->data_memory_end = woo_ce_current_memory_usage();
			break;

	}

	// Remove our content filters here to play nice with other Plugins
	remove_filter( 'sanitize_key', 'woo_ce_sanitize_key' );

	// Remove our fatal error notice so not to conflict with the CRON or scheduled export engine	
	remove_action( 'shutdown', 'woo_ce_fatal_error' );

	// Export completed successfully
	delete_transient( WOO_CE_PREFIX . '_running' );

	// Check that the export file is populated, export columns have been assigned and rows counted
	if( $output && $export->total_rows && $export->total_columns ) {
		if( in_array( $export->export_format, array( 'csv' ) ) ) {
			$output = woo_ce_file_encoding( $output );
			if( $export->export_format == 'csv' && $export->bom && ( WOO_CE_DEBUG == false ) )
				$output = "\xEF\xBB\xBF" . $output;
		}
		if( WOO_CE_DEBUG && !$export->cron ) {
			$response = set_transient( WOO_CE_PREFIX . '_debug_log', base64_encode( $output ), woo_ce_get_option( 'timeout', MINUTE_IN_SECONDS ) );
			if( $response !== true ) {
				$message = __( 'The export contents were too large to store in a single WordPress transient, use the Volume offset / Limit volume options to reduce the size of your export and try again.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
				if( function_exists( 'woo_ce_admin_notice' ) )
					woo_ce_admin_notice( $message, 'error' );
				else
					error_log( sprintf( '[store-exporter] woo_ce_export_dataset() - %s', $message ) );
			}
		} else {
			return $output;
		}
	}

}

function woo_ce_fatal_error() {

	global $export;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter/usage/';

	$error = error_get_last();
	if( $error !== null ) {
		$message = '';
		$notice = sprintf( __( 'Refer to the following error and contact us on http://wordpress.org/plugins/woocommerce-exporter/ for further assistance. Error: <code>%s</code>', 'woo_ce' ), $error['message'] );
		if ( substr( $error['message'], 0, 22 ) === 'Maximum execution time' ) {
			$message = __( 'The server\'s maximum execution time is too low to complete this export. This is commonly due to a low timeout limit set by your hosting provider or PHP Safe Mode being enabled.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
		} elseif ( substr( $error['message'], 0, 19 ) === 'Allowed memory size' ) {
			$message = __( 'The server\'s maximum memory size is too low to complete this export. Consider increasing available memory to WordPress or reducing the size of your export.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
		} else if( $error['type'] === E_ERROR ) {
			$message = __( 'A fatal PHP error was encountered during the export process, we couldn\'t detect or diagnose it further.', 'woo_ce' ) . ' (<a href="' . $troubleshooting_url . '" target="_blank">' . __( 'Need help?', 'woo_ce' ) . '</a>)';
		}
		if( !empty( $message ) ) {

			// Save a record to the PHP error log
			error_log( sprintf( __( '[store-exporter] Fatal error: %s - PHP response: %s in %s on line %s', 'woo_ce' ), $message, $error['message'], $error['file'], $error['line'] ) );

			// Only display the message if this is a manual export
			if( ( !$export->cron && !$export->scheduled_export ) ) {
				$output = '<div id="message" class="error"><p>' . sprintf( __( '<strong>[store-exporter]</strong> An unexpected error occurred. %s', 'woo_ce' ), $message ) . '</p><p>' . $notice . '</p></div>';
				echo $output;
			}

		}
	}

}

// List of Export types used on Store Exporter screen
function woo_ce_return_export_types() {

	$types = array(
		'product' => __( 'Products', 'woo_ce' ),
		'category' => __( 'Categories', 'woo_ce' ),
		'tag' => __( 'Tags', 'woo_ce' ),
		'user' => __( 'Users', 'woo_ce' )
	);
	$types = apply_filters( 'woo_ce_export_types', $types );
	return $types;

}

// Returns the Post object of the export file saved as an attachment to the WordPress Media library
function woo_ce_save_file_attachment( $filename = '', $post_mime_type = 'text/csv' ) {

	if( !empty( $filename ) ) {
		$post_type = 'woo-export';
		$args = array(
			'post_title' => $filename,
			'post_type' => $post_type,
			'post_mime_type' => $post_mime_type
		);
		$post_ID = wp_insert_attachment( $args, $filename );
		if( is_wp_error( $post_ID ) )
			error_log( sprintf( '[store-exporter] save_file_attachment() - $s: %s', $filename, $result->get_error_message() ) );
		else
			return $post_ID;
	}

}

// Updates the GUID of the export file attachment to match the correct file URL
function woo_ce_save_file_guid( $post_ID, $export_type, $upload_url = '' ) {

	add_post_meta( $post_ID, '_woo_export_type', $export_type );
	if( !empty( $upload_url ) ) {
		$args = array(
			'ID' => $post_ID,
			'guid' => $upload_url
		);
		wp_update_post( $args );
	}

}

// Save critical export details against the archived export
function woo_ce_save_file_details( $post_ID ) {

	global $export;

	add_post_meta( $post_ID, '_woo_start_time', $export->start_time );
	add_post_meta( $post_ID, '_woo_idle_memory_start', $export->idle_memory_start );
	add_post_meta( $post_ID, '_woo_columns', $export->total_columns );
	add_post_meta( $post_ID, '_woo_rows', $export->total_rows );
	add_post_meta( $post_ID, '_woo_data_memory_start', $export->data_memory_start );
	add_post_meta( $post_ID, '_woo_data_memory_end', $export->data_memory_end );

}

// Update detail of existing archived export
function woo_ce_update_file_detail( $post_ID, $detail, $value ) {

	if( strstr( $detail, '_woo_' ) !== false )
		update_post_meta( $post_ID, $detail, $value );

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

function woo_ce_return_mime_type_extension( $mime_type, $search_by = 'extension' ) {

	$mime_types = array(
		'csv' => 'text/csv',
		'xls' => 'application/vnd.ms-excel',
		'xml' => 'application/xml'
	);
	if( $search_by == 'extension' ) {
		if( isset( $mime_types[$mime_type] ) )
			return $mime_types[$mime_type];
	} else if( $search_by == 'mime_type' ) {
		if( $key = array_search( $mime_type, $mime_types ) )
			return strtoupper( $key );
	}

}

function woo_ce_add_missing_mime_type( $mime_types = array() ) {

	// Add CSV mime type if it has been removed
	if( !isset( $mime_types['csv'] ) )
		$mime_types['csv'] = 'text/csv';
	return $mime_types;

}
add_filter( 'upload_mimes', 'woo_ce_add_missing_mime_type', 10, 1 );

if( !function_exists( 'woo_ce_sort_fields' ) ) {
	function woo_ce_sort_fields( $key ) {

		return $key;

	}
}


// Add Store Export to filter types on the WordPress Media screen
function woo_ce_add_post_mime_type( $post_mime_types = array() ) {

	$post_mime_types['text/csv'] = array( __( 'Store Exports (CSV)', 'woo_ce' ), __( 'Manage Store Exports (CSV)', 'woo_ce' ), _n_noop( 'Store Export - CSV <span class="count">(%s)</span>', 'Store Exports - CSV <span class="count">(%s)</span>' ) );
	return $post_mime_types;

}
add_filter( 'post_mime_types', 'woo_ce_add_post_mime_type' );

function woo_ce_current_memory_usage() {

	$output = '';
	if( function_exists( 'memory_get_usage' ) )
		$output = round( memory_get_usage( true ) / 1024 / 1024, 2 );
	return $output;

}

function woo_ce_get_start_of_week_day() {

	global $wp_locale;

	$output = 'Monday';
	$start_of_week = get_option( 'start_of_week', 0 );
	for( $day_index = 0; $day_index <= 6; $day_index++ ) {
		if( $start_of_week == $day_index ) {
			$output = $wp_locale->get_weekday( $day_index );
			break;
		}
	}
	return $output;

}

function woo_ce_get_option( $option = null, $default = false, $allow_empty = false ) {

	$output = false;
	if( $option !== null ) {
		$separator = '_';
		$output = get_option( WOO_CE_PREFIX . $separator . $option, $default );
		if( $allow_empty == false && $output != 0 && ( $output == false || $output == '' ) )
			$output = $default;
	}
	return $output;

}

function woo_ce_update_option( $option = null, $value = null ) {

	$output = false;
	if( $option !== null && $value !== null ) {
		$separator = '_';
		$output = update_option( WOO_CE_PREFIX . $separator . $option, $value );
	}
	return $output;

}
?>