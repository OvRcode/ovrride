<?php
/**
 * Plugin Name: WooCommerce Customer/Order/Coupon CSV Import Suite
 * Plugin URI: http://www.woocommerce.com/extension/customerorder-csv-import-suite/
 * Description: Import customers, coupons and orders straight from the WordPress admin
 * Author: SkyVerge
 * Author URI: http://www.woocommerce.com
 * Version: 3.5.5
 * Text Domain: woocommerce-csv-import-suite
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2012-2018, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   WC-CSV-Import-Suite
 * @author    SkyVerge
 * @category  Importer
 * @copyright Copyright (c) 2012-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 18709:eb00ca8317a0f64dbe185c995e5ea3df
 * WC requires at least: 2.6.14
 * WC tested up to: 3.5.0
 */

defined( 'ABSPATH' ) or exit;

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), 'eb00ca8317a0f64dbe185c995e5ea3df', '18709' );

// WC active check/is admin
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library classss
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( plugin_dir_path( __FILE__ ) . 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '4.9.0', __( 'WooCommerce Customer/Order/Coupon CSV Import', 'woocommerce-csv-import-suite' ), __FILE__, 'init_woocommerce_csv_import_suite', array(
	'minimum_wc_version'   => '2.6.14',
	'minimum_wp_version'   => '4.4',
	'backwards_compatible' => '4.4',
) );

function init_woocommerce_csv_import_suite() {

/**
 * Customer/Order/Coupon CSV Import Suite Main Class.  This class is responsible
 * for registering the importers and setting up the admin start page/menu
 * items.  The actual import process is handed off to the various parse
 * and import classes.
 *
 * Adapted from the WordPress post importer by the WordPress team
 */
class WC_CSV_Import_Suite extends SV_WC_Plugin {


	/** version number */
	const VERSION = '3.5.5';

	/** @var WC_CSV_Import_Suite single instance of this plugin */
	protected static $instance;

	/** string the plugin id */
	const PLUGIN_ID = 'csv_import_suite';

	/** @var \WC_CSV_Import_Suite_Admin instance */
	protected $admin;

	/** @var \WC_CSV_Import_Suite_Importers instance */
	protected $importers;

	/** @var \WC_CSV_Import_Suite_Background_Import instance */
	protected $background_import;

	/** @var \WC_CSV_Import_Suite_AJAX instance */
	protected $ajax;

	/** @var bool $logging_enabled whether debug logging is enabled for the import type */
	private $logging_enabled;


	/**
	 * Construct and initialize the main plugin class
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			array(
				'dependencies'       => array( 'mbstring' ),
				'text_domain'        => 'woocommerce-csv-import-suite',
				'display_php_notice' => true,
			)
		);

		// initialize
		add_action( 'init', array( $this, 'includes' ) );

		// schedule cleanup of imported files which are older than 14 days
		add_action( 'init', array( $this, 'schedule_import_cleanup' ) );

		// cleanup expired imports
		add_action( 'wc_customer_order_csv_import_scheduled_import_cleanup', array( $this, 'cleanup_imports' ) );
	}


	/**
	 * Include required files
	 *
	 * @since 3.0.0
	 */
	public function includes() {

		// importers and background import must be loaded all the time, because
		// otherwise background jobs simply won't work
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-async-request.php' );
		require_once( $this->get_framework_path() . '/utilities/class-sv-wp-background-job-handler.php' );

		$this->background_import = $this->load_class( '/includes/class-wc-csv-import-suite-background-import.php', 'WC_CSV_Import_Suite_Background_Import' );
		$this->importers         = $this->load_class( '/includes/class-wc-csv-import-suite-importers.php', 'WC_CSV_Import_Suite_Importers' );

		if ( is_admin() ) {
			$this->admin_includes();
		}

		if ( is_ajax() ) {
			$this->ajax_includes();
		}
	}


	/**
	 * Include required admin files
	 *
	 * @since 3.0.0
	 */
	private function admin_includes() {
		$this->admin = $this->load_class( '/includes/admin/class-wc-csv-import-suite-admin.php', 'WC_CSV_Import_Suite_Admin' );
	}


	/**
	 * Include required AJAX files
	 *
	 * @since 3.0.0
	 */
	private function ajax_includes() {

		require_once( $this->get_plugin_path() . '/includes/class-wc-csv-import-suite-parser.php' );
		$this->ajax = $this->load_class( '/includes/class-wc-csv-import-suite-ajax.php', 'WC_CSV_Import_Suite_AJAX' );
	}


	/**
	 * Return admin class instance
	 *
	 * @since 3.0.0
	 * @return \WC_CSV_Import_Suite_Admin
	 */
	public function get_admin_instance() {
		return $this->admin;
	}


	/**
	 * Return importers class instance
	 *
	 * @since 3.0.0
	 * @return \WC_CSV_Import_Suite_Importers
	 */
	public function get_importers_instance() {
		return $this->importers;
	}


	/**
	 * Return background import class instance
	 *
	 * @since 3.0.0
	 * @return \WC_CSV_Import_Suite_Background_Import
	 */
	public function get_background_import_instance() {
		return $this->background_import;
	}


	/**
	 * Return the ajax class instance
	 *
	 * @since 3.0.0
	 * @return \WC_CSV_Import_Suite_AJAX
	 */
	public function get_ajax_instance() {
		return $this->ajax;
	}


	/**
	 * Returns the "Import" plugin action link to go directly to the plugin
	 * settings page (if any)
	 *
	 * @since 2.3
	 * @see SV_WC_Plugin::get_settings_link()
	 * @param string $plugin_id the plugin identifier.  Note that this can be a
	 *        sub-identifier for plugins with multiple parallel settings pages
	 *        (ie a gateway that supports both credit cards and echecks)
	 * @return string plugin configure link
	 */
	public function get_settings_link( $plugin_id = null ) {

		$settings_url = $this->get_settings_url( $plugin_id );

		if ( $settings_url ) {
			return sprintf( '<a href="%s">%s</a>', $settings_url, __( 'Import', 'woocommerce-csv-import-suite' ) );
		}

		// no settings
		return '';
	}


	/**
	 * Gets the plugin configuration URL
	 *
	 * @since 2.3
	 * @see SV_WC_Plugin::get_settings_url()
	 * @param string $plugin_id the plugin identifier.
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $plugin_id = null ) {

		// link to the import page
		return admin_url( 'admin.php?page=' . self::PLUGIN_ID );
	}


	/**
	 * Gets the plugin documentation url, which is non-standard for this plugin
	 *
	 * @since 2.3.0
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woocommerce.com/document/customer-order-csv-import-suite/';
	}


	/**
	 * Gets the plugin support URL
	 *
	 * @since VERSION
	 * @see SV_WC_Plugin::get_support_url()
	 * @return string
	 */
	public function get_support_url() {
		return 'https://woocommerce.com/my-account/marketplace-ticket-form/';
	}


	/**
	 * Returns true if on the Customer/Order/CouponImport page
	 *
	 * @since 2.3
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the plugin admin settings page
	 */
	public function is_plugin_settings() {
		return isset( $_GET['page'] ) && self::PLUGIN_ID == $_GET['page'];
	}


	/**
	 * Adds an entry to the debug log if enabled.
	 *
	 * @since 3.1.1
	 * @param string $message the log message
	 * @param null $_ unused
	 */
	public function log( $message, $_ = null ) {

		if ( $this->logging_enabled() ) {
			parent::log( $message );
		}
	}


	/**
	 * Determine if debug logging is enabled for a given importer.
	 *
	 * @since 3.1.1
	 * @return bool
	 */
	public function logging_enabled() {

		$this->logging_enabled = 'yes' === get_option( 'wc_csv_import_suite_debug_mode', 'no' );

		/**
		 * Filter whether debug logging is enabled.
		 *
		 * @since 3.1.1
		 * @param bool whether debug logging is enabled.
		 */
		return apply_filters( 'wc_csv_import_suite_logging_enabled', $this->logging_enabled );
	}


	/** Helper methods ******************************************************/


	/**
	 * Remove import finished notice from user meta
	 *
	 * @since 3.1.0
	 * @param string $import_id Import job ID
	 * @param int $user_id
	 */
	public function remove_import_finished_notice( $import_id, $user_id ) {

		$import_notices = get_user_meta( $user_id, '_wc_csv_import_suite_notices', true );

		if ( ! empty( $import_notices ) && in_array( $import_id, $import_notices, true ) ) {

			unset( $import_notices[ array_search( $import_id, $import_notices ) ] );

			update_user_meta( $user_id, '_wc_csv_import_suite_notices', $import_notices );
		}

		// also remove the message from user dismissed notices
		$dismissed_notices = wc_csv_import_suite()->get_admin_notice_handler()->get_dismissed_notices( $user_id );
		$message_id        = 'wc_csv_import_suite_finished_' . $import_id;

		if ( ! empty( $dismissed_notices ) && isset( $dismissed_notices[ $message_id ] ) ) {
			unset( $dismissed_notices[ $message_id ] );

			update_user_meta( $user_id, '_wc_plugin_framework_csv_import_suite_dismissed_messages', $dismissed_notices );
		}
	}


	/**
	* Main Customer/Order/Coupon CSV Import Suite Instance, ensures only one instance is/can be loaded
	*
	* @since 2.7.0
	* @see wc_csv_import_suite()
	* @return WC_CSV_Import_Suite
	*/
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 2.3
	 * @see SV_WC_Payment_Gateway::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Customer/Order/Coupon CSV Import', 'woocommerce-csv-import-suite' );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 2.3
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/** Lifecycle Methods ******************************************************/


	/**
	 * Install default settings.
	 *
	 * @since 3.4.0
	 * @see SV_WC_Plugin::install()
	 */
	protected function install() {

		// set up csv imports folder
		self::create_files();
	}


	/**
	 * Create files/directories.
	 *
	 * Based on WC_Install::create_files()
	 *
	 * @since 3.4.0
	 */
	private static function create_files() {

		// install files and folders for exported files and prevent hotlinking
		$upload_dir      = wp_upload_dir();
		$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = array(
			array(
				'base'    => $upload_dir['basedir'] . '/csv_imports',
				'file'    => 'index.html',
				'content' => ''
			),
		);

		if ( 'redirect' !== $download_method ) {
			$files[] = array(
				'base'    => $upload_dir['basedir'] . '/csv_imports',
				'file'    => '.htaccess',
				'content' => 'deny from all'
			);
		}

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
	 * Upgrade to $installed_version.
	 *
	 * @since 3.4.0
	 * @see SV_WC_Plugin::upgrade()
	 */
	protected function upgrade( $installed_version ) {

		// upgrade to 3.4.0
		if ( version_compare( $installed_version, '3.4.0', '<' ) ) {

			// set up csv imports folder
			self::create_files();
		}
	}


	/**
	 * Schedule once-daily cleanup of old import jobs.
	 *
	 * @since 3.4.0
	 */
	public function schedule_import_cleanup() {

		if ( ! wp_next_scheduled( 'wc_customer_order_csv_import_scheduled_import_cleanup' ) ) {

			wp_schedule_event( strtotime( 'tomorrow +15 minutes' ), 'daily', 'wc_customer_order_csv_import_scheduled_import_cleanup' );
		}
	}


	/**
	 * Clean up (remove) imported files which are older than 14 days.
	 *
	 * @since 3.4.0
	 */
	public function cleanup_imports() {

		wc_csv_import_suite()->get_background_import_instance()->remove_expired_imports();
	}


} // class WC_CSV_Import_Suite


/**
 * Returns the One True Instance of Customer/Order/Coupon CSV Import Suite
 *
 * @since 2.7.0
 * @return WC_CSV_Import_Suite
*/
function wc_csv_import_suite() {
	return WC_CSV_Import_Suite::instance();
}


// fire it up!
wc_csv_import_suite();

} // init_woocommerce_csv_import_suite()
