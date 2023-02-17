<?php
/**
 * Plugin Name: WooCommerce Simply Order Export
 * Plugin URI: http://sharethingz.com/
 * Description: A WooCommerce plugin to export order related information in csv format.
 * Version: 3.0.13
 * Author: Ankit Gade
 * Author URI: https://sharethingz.com/
 * Text Domain: woooe
 * Domain Path: /i18n/languages/
 *
 * @package WooCommerce
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'WOOOE_BASE' ) ) {
	define( 'WOOOE_BASE', dirname( __FILE__ ) );
}

if ( ! defined( 'WOOOE_BASENAME' ) ) {
	define( 'WOOOE_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'WOOOE_BASE_FILE' ) ) {
	define( 'WOOOE_BASE_FILE', __FILE__ );
}

if ( ! defined( 'WOOOE_BASE_URL' ) ) {
	define( 'WOOOE_BASE_URL', plugins_url( basename( dirname( __FILE__ ) ) ) );
}

//Include main class
if ( ! class_exists( 'WOOOE', false ) ) {
	include_once dirname( __FILE__ ) . '/lib/functions.php';
	include_once dirname( __FILE__ ) . '/classes/class-woooe.php';
}

function _WOOOE() {
	return WOOOE::instance();
}

$GLOBALS['woooe'] = _WOOOE();

register_activation_hook( __FILE__, array( 'WOOOE_File_Handler', 'create_folder' ) );
