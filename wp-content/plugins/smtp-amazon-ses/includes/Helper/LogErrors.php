<?php

namespace YaySMTPAmazonSES\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class LogErrors {

	public static function writeLog( $message, $type_log = 'error', $name = 'log' ) {
		if ( ! is_string( $message ) ) {
			$message = print_r( $message, true );
		}

		$folder = YAY_SMTP_AMAZONSES_PLUGIN_PATH . '/includes/Logs';
		if ( ! file_exists( $folder ) ) {
			@mkdir( $folder, 0755 );
			@chmod( $folder, 0755 );

		}

		$filename = $folder . DIRECTORY_SEPARATOR . $name . '.txt';

		clearstatcache(); // Remove filesize cache

		$handle = fopen( $filename, 'a' );
		if ( filesize( $filename ) == 0 ) {
			fwrite( $handle, self::getSystemStats() );
		}

		fwrite( $handle, current_time( 'mysql' ) . ' [' . strtoupper( $type_log ) . '] ' . $message . PHP_EOL );
		fclose( $handle );
	}

	private static function getSystemStats() {
		global $wpdb;

		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$system_stats  = '====  SYSTEM STATS  ====' . PHP_EOL;
		$system_stats .= 'WordPress Version: ' . get_bloginfo( 'version' ) . PHP_EOL;
		$system_stats .= 'PHP Version: ' . phpversion() . PHP_EOL;
		$system_stats .= 'MySQL Version: ' . $wpdb->db_version() . PHP_EOL;
		$system_stats .= 'Website Name: ' . get_bloginfo() . PHP_EOL;
		$system_stats .= 'Theme: ' . wp_get_theme() . PHP_EOL;
		$system_stats .= 'WordPress URL: ' . site_url() . PHP_EOL;
		$system_stats .= 'Site URL: ' . home_url() . PHP_EOL;
		$system_stats .= 'Multisite: ' . ( is_multisite() ? 'yes' : 'no' ) . PHP_EOL;
		$system_stats .= 'PHP Extensions: ' . json_encode( get_loaded_extensions() ) . PHP_EOL;
		$system_stats .= 'WP Memory Limit: ' . WP_MEMORY_LIMIT . PHP_EOL;
		$system_stats .= 'WP Admin Memory Limit: ' . WP_MAX_MEMORY_LIMIT . PHP_EOL;
		$system_stats .= 'PHP Memory Limit: ' . ini_get( 'memory_limit' ) . PHP_EOL;
		$system_stats .= 'Max Execution Time: ' . ini_get( 'max_execution_time' ) . PHP_EOL;
		$system_stats .= '====  SYSTEM STATS  ====' . PHP_EOL . PHP_EOL;
		return $system_stats;
	}

	public static function getMessageException( $ex, $ajax = false ) {
		$message  = 'SYSTEM ERROR: ' . $ex->getCode() . ' : ' . $ex->getMessage();
		$message .= PHP_EOL . $ex->getFile() . '(' . $ex->getLine() . ')';
		$message .= PHP_EOL . $ex->getTraceAsString();
		self::writeLog( $message );
		if ( $ajax ) {
			wp_send_json_error( array( 'mess' => $message ) );
		}
	}

	// writeLog use show content when save email, save
	public static function writeLogContent( $content = '', $tailName = 'html' ) {
		$name     = 'log-' . current_time( 'timestamp' );
		$folder   = YAY_SMTP_AMAZONSES_PLUGIN_PATH . '/includes/Logs';
		$filename = $folder . DIRECTORY_SEPARATOR . $name . '.' . $tailName;
		$handle   = fopen( $filename, 'a' );
		fwrite( $handle, print_r( $content, true ) );
		fclose( $handle );
	}

	public static function clearLog() {
		$file = YAY_SMTP_AMAZONSES_PLUGIN_PATH . 'includes/Logs/log.txt';
		if ( file_exists( $file ) ) {
			file_put_contents( $file, '' );
		}
	}

	public static function clearErr() {
		update_option( YAY_SMTP_AMAZONSES_PREFIX . '_debug', array() );
	}

	public static function setErr( $mes ) {
		$mes    = ! is_string( $mes ) ? wp_json_encode( $mes ) : wp_strip_all_tags( $mes, false );
		$result = self::getErr();
		array_push( $result, $mes );
		update_option( YAY_SMTP_AMAZONSES_PREFIX . '_debug', array_unique( $result ) );
	}

	public static function getErr() {
		return (array) get_option( YAY_SMTP_AMAZONSES_PREFIX . '_debug', array() );
	}
}
