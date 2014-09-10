<?php
/**
 * WooCommerce Admin Hooks
 *
 * Action/filter hooks used for WooCommerce functions.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooCommerce/Admin
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Events
 *
 * @see woocommerce_delete_post()
 * @see woocommerce_trash_post()
 * @see woocommerce_untrash_post()
 * @see woocommerce_preview_emails()
 * @see woocommerce_prevent_admin_access()
 * @see woocommerce_check_download_folder_protection()
 * @see woocommerce_ms_protect_download_rewite_rules()
 */
add_action('delete_post', 'woocommerce_delete_post');
add_action('wp_trash_post', 'woocommerce_trash_post');
add_action('untrash_post', 'woocommerce_untrash_post');
add_action('admin_init', 'woocommerce_preview_emails');
add_action('admin_init', 'woocommerce_prevent_admin_access');
add_action('woocommerce_settings_saved', 'woocommerce_check_download_folder_protection');
add_filter('mod_rewrite_rules', 'woocommerce_ms_protect_download_rewite_rules');

/**
 * File uploads
 *
 * @see woocommerce_downloads_upload_dir()
 * @see woocommerce_media_upload_downloadable_product()
 */
add_filter('upload_dir', 'woocommerce_downloads_upload_dir');
add_action('media_upload_downloadable_product', 'woocommerce_media_upload_downloadable_product');

/**
 * Shortcode buttons
 *
 * @see woocommerce_add_shortcode_button()
 * @see woocommerce_refresh_mce()
 */
add_action( 'init', 'woocommerce_add_shortcode_button' );
add_filter( 'tiny_mce_version', 'woocommerce_refresh_mce' );

/**
 * Category/term ordering
 *
 * @see woocommerce_create_term()
 * @see woocommerce_delete_term()
 */
add_action( "create_term", 'woocommerce_create_term', 5, 3 );
add_action( "delete_term", 'woocommerce_delete_term', 5 );

/**
 * Bulk editing
 *
 * @see woocommerce_bulk_admin_footer()
 * @see woocommerce_order_bulk_action()
 * @see woocommerce_order_bulk_admin_notices()
 */
add_action( 'admin_footer', 'woocommerce_bulk_admin_footer', 10 );
add_action( 'load-edit.php', 'woocommerce_order_bulk_action' );
add_action( 'admin_notices', 'woocommerce_order_bulk_admin_notices' );

/**
 * Mijireh Gateway
 */
add_action( 'add_meta_boxes', array( 'WC_Gateway_Mijireh', 'add_page_slurp_meta' ) );
add_action( 'wp_ajax_page_slurp', array( 'WC_Gateway_Mijireh', 'page_slurp' ) );