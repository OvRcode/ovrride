<?php
/**
 * Plugin Name: SMTP for AmazonSES - YaySMTP
 * Plugin URI: https://yaycommerce.com/amazonses-smtp
 * Description: This plugin helps you send emails from your WordPress website via your Amazon SES SMTP.
 * Version: 1.5
 * Author: YayCommerce
 * Author URI: https://yaycommerce.com
 * Text Domain: yaysmtp_amazonses
 * Domain Path: /i18n/languages/
 */

namespace YaySMTPAmazonSES;

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'YAY_SMTP_AMAZONSES_PREFIX' ) ) {
	define( 'YAY_SMTP_AMAZONSES_PREFIX', 'yay_smtp_amazonses' );
}
if ( ! defined( 'YAY_SMTP_AMAZONSES_VERSION' ) ) {
	define( 'YAY_SMTP_AMAZONSES_VERSION', '1.5' );
}

if ( ! defined( 'YAY_SMTP_AMAZONSES_PLUGIN_URL' ) ) {
	define( 'YAY_SMTP_AMAZONSES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YAY_SMTP_AMAZONSES_PLUGIN_PATH' ) ) {
	define( 'YAY_SMTP_AMAZONSES_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YAY_SMTP_AMAZONSES_PLUGIN_BASENAME' ) ) {
	define( 'YAY_SMTP_AMAZONSES_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YAY_SMTP_AMAZONSES_SITE_URL' ) ) {
	define( 'YAY_SMTP_AMAZONSES_SITE_URL', site_url() );
}

spl_autoload_register(
	function ( $class ) {
		$prefix   = __NAMESPACE__; // project-specific namespace prefix
		$base_dir = __DIR__ . '/includes'; // base directory for the namespace prefix

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) { // does the class use the namespace prefix?
			return; // no, move to the next registered autoloader
		}

		$relative_class_name = substr( $class, $len );

		// replace the namespace prefix with the base directory, replace namespace
		// separators with directory separators in the relative class name, append
		// with .php
		$file = $base_dir . str_replace( '\\', '/', $relative_class_name ) . '.php';

		if ( file_exists( $file ) ) {
			require $file;
		}
	}
);

require_once __DIR__ . '/vendor_amazon/autoload.php';

if ( version_compare( get_bloginfo( 'version' ), '5.5-alpha', '<' ) ) {
	if ( ! class_exists( '\PHPMailer', false ) ) {
		require_once ABSPATH . 'wp-includes/class-phpmailer.php';
	}
} else {
	if ( ! class_exists( '\PHPMailer\PHPMailer\PHPMailer', false ) ) {
		require_once ABSPATH . 'wp-includes/PHPMailer/PHPMailer.php';
	}
	if ( ! class_exists( '\PHPMailer\PHPMailer\Exception', false ) ) {
		require_once ABSPATH . 'wp-includes/PHPMailer/Exception.php';
	}
	if ( ! class_exists( '\PHPMailer\PHPMailer\SMTP', false ) ) {
		require_once ABSPATH . 'wp-includes/PHPMailer/SMTP.php';
	}
}

function init() {
	Schedule::getInstance();
	Plugin::getInstance();
}
add_action( 'plugins_loaded', 'YaySMTPAmazonSES\\init' );

register_activation_hook( __FILE__, array( 'YaySMTPAmazonSES\\Plugin', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'YaySMTPAmazonSES\\Plugin', 'deactivate' ) );
