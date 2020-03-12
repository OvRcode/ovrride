<?php
/**
 * Constant Contact + WooCommerce
 *
 * @since 0.0.1
 * @author WebDevStudios <https://www.webdevstudios.com/>
 * @package cc-woo
 */

namespace WebDevStudios\CCForWoo;

use Exception;

use WebDevStudios\CCForWoo\Utility\PluginCompatibilityCheck;
use WebDevStudios\OopsWP\Structure\ServiceRegistrar;
use WebDevStudios\CCForWoo\View\ViewRegistrar;
use WebDevStudios\CCForWoo\View\Admin\Notice;
use WebDevStudios\CCForWoo\View\Admin\NoticeMessage;
use WebDevStudios\CCForWoo\Meta\ConnectionStatus;
use WebDevStudios\CCForWoo\Api\KeyManager;
use WebDevStudios\CCForWoo\WebHook\Disconnect;
use WebDevStudios\CCForWoo\View\Admin\MenuItem;
use WebDevStudios\CCForWoo\AbandonedCarts\CartHandler;
use WebDevStudios\CCForWoo\AbandonedCarts\CartsTable;
use WebDevStudios\CCForWoo\AbandonedCarts\CartRecovery;
use WebDevStudios\CCForWoo\Rest\Registrar as RestRegistrar;

/**
 * "Core" plugin class.
 *
 * @since 0.0.1
 */
final class Plugin extends ServiceRegistrar {

	/**
	 * The plugin name.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	const PLUGIN_NAME = 'Constant Contact + WooCommerce';

	/**
	 * The plugin version.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const PLUGIN_VERSION = '1.2.0';

	/**
	 * Whether the plugin is currently active.
	 *
	 * @since 0.0.1
	 * @var bool
	 */
	private $is_active = false;

	/**
	 * The plugin file path, should be __FILE__ of the main entry point script.
	 *
	 * @since 0.0.1
	 * @var string
	 */
	private $plugin_file;

	/**
	 * Services to register.
	 *
	 * @since 0.0.1
	 * @var array
	 */
	protected $services = [
		ViewRegistrar::class,
		KeyManager::class,
		Disconnect::class,
		MenuItem::class,
		CartHandler::class,
		CartsTable::class,
		CartRecovery::class,
		RestRegistrar::class,
	];

	/**
	 * Setup the instance of this class.
	 *
	 * Prepare some things for later.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $plugin_file The plugin file path of the entry script.
	 * @package cc-woo
	 */
	public function __construct( string $plugin_file ) {
		$this->plugin_file = $plugin_file;
	}

	/**
	 * Deactivate this plugin.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @param string $reason The reason for deactivating.
	 * @throws Exception If the plugin isn't active, throw an Exception.
	 */
	private function deactivate( $reason ) {
		unset( $_GET['activate'] ); // phpcs:ignore -- Ok use of $_GET.

		if ( ! $this->is_active() ) {
			throw new Exception( $reason );
		}

		do_action( 'cc_woo_disconnect', esc_html__( 'Plugin deactivated.', 'cc-woo' ) );

		$this->do_deactivation_process();

		new Notice(
			new NoticeMessage( $reason, 'error', true )
		);

		Notice::set_notices();

		add_action( 'admin_notices', [ '\WebDevStudios\CCForWoo\View\Admin\Notice', 'maybe_display_notices' ] );

		deactivate_plugins( $this->plugin_file );
	}

	/**
	 * Maybe deactivate the plugin if certain conditions aren't met.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios.com>
	 * @throws Exception When WooCommerce is not found or compatible.
	 */
	public function check_for_required_dependencies() {
		try {
			$compatibility_checker = new PluginCompatibilityCheck( '\\WooCommerce' );

			// Ensure requirements.
			if ( ! $compatibility_checker->is_available() ) {
				// translators: placeholder is the minimum supported WooCommerce version.
				$message = sprintf( esc_html__( 'WooCommerce version "%1$s" or greater must be installed and activated to use %2$s.', 'cc-woo' ), PluginCompatibilityCheck::MINIMUM_WOO_VERSION, self::PLUGIN_NAME );
				throw new Exception( $message );
			}

			if ( ! $compatibility_checker->is_compatible( \WooCommerce::instance() ) ) {
				// translators: placeholder is the minimum supported WooCommerce version.
				$message = sprintf( esc_html__( 'WooCommerce version "%1$s" or greater is required to use %2$s.', 'cc-woo' ), PluginCompatibilityCheck::MINIMUM_WOO_VERSION, self::PLUGIN_NAME );
				throw new Exception( $message );
			}
		} catch ( Exception $e ) {
			$this->deactivate( $e->getMessage() );
		}
	}

	/**
	 * Run things once the plugin instance is ready.
	 *
	 * @since 0.0.1
	 * @author Zach Owen <zach@webdevstudios>
	 */
	public function run() {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->is_active = is_plugin_active( plugin_basename( $this->plugin_file ) );
		$this->register_hooks();

		parent::run();
	}

	/**
	 * Register the plugin's hooks with WordPress.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  0.0.1
	 */
	public function register_hooks() {
		add_action( 'plugins_loaded', [ $this, 'check_for_required_dependencies' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_scripts' ] );

		register_activation_hook( $this->plugin_file, [ $this, 'do_activation_process' ] );
		register_deactivation_hook( $this->plugin_file, [ $this, 'do_deactivation_process' ] );
	}

	/**
	 * Returns whether the plugin is active.
	 *
	 * @since 0.0.1
	 * @author Zach Owen Zach Owen <zach@webdevstudios>
	 * @return bool
	 */
	public function is_active() : bool {
		return $this->is_active;
	}

	/**
	 * Get the plugin file path.
	 *
	 * @since 0.0.1
	 * @author Zach Owen Zach Owen <zach@webdevstudios>
	 * @return string
	 */
	public function get_plugin_file() : string {
		return $this->plugin_file;
	}

	/**
	 * Activate WooCommerce along with Constant Contact + WooCommerce if it's present and not already active.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	private function maybe_activate_woocommerce() {
		$woocommerce = 'woocommerce/woocommerce.php';

		if ( ! is_plugin_active( $woocommerce ) && in_array( $woocommerce, array_keys( get_plugins() ), true ) ) {
			activate_plugin( $woocommerce );
		}
	}

	/**
	 * Callback for register_activation_hook.
	 *
	 * Performs the plugin's activation routines.
	 *
	 * @see register_activation_hook()
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 */
	public function do_activation_process() {
		$this->maybe_activate_woocommerce();

		$this->create_abandoned_carts_table();
		$this->create_abandoned_carts_expiration_check();

		flush_rewrite_rules();
	}

	/**
	 * Creates the database table for Abandoned Carts.
	 *
	 * @author Rebekah Van Epps <rebekah.vanepps@webdevstudios.com>
	 * @since 2019-10-24
	 */
	private function create_abandoned_carts_table() {
		( new CartsTable() )->create_table();
	}

	/**
	 * Schedules the daily check for abandoned carts that have sat in the DB longer than 30 days (by default...).
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-24
	 */
	private function create_abandoned_carts_expiration_check() {
		if ( ! wp_next_scheduled( 'cc_woo_check_expired_carts' ) ) {
			wp_schedule_event( strtotime( 'today' ), 'daily', 'cc_woo_check_expired_carts' );
		}
	}

	/**
	 * Removes the scheduled daily check for expired abandoned carts.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 2019-10-24
	 */
	private function clear_abandoned_carts_expiration_check() {
		wp_clear_scheduled_hook( 'cc_woo_check_expired_carts' );
	}

	/**
	 * Callback for register_deactivation_hook.
	 *
	 * Performs the plugin's deactivation routines, including notifying Constant Contact of disconnection.
	 *
	 * @see register_deactivation_hook()
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-18
	 * @return void
	 */
	public function do_deactivation_process() {
		do_action( 'wc_ctct_disconnect' );

		$this->clear_abandoned_carts_expiration_check();

		if ( ! get_option( ConnectionStatus::CC_CONNECTION_ESTABLISHED_KEY ) ) {
			return;
		}

		delete_option( ConnectionStatus::CC_CONNECTION_ESTABLISHED_KEY );
	}

	/**
	 * Registers public scripts.
	 *
	 * @author George Gecewicz <george.gecewicz@webdevstudios.com>
	 * @since 1.2.0
	 */
	public function register_scripts() {
		wp_register_script( 'cc-woo-public', trailingslashit( plugin_dir_url( $this->get_plugin_file() ) ) . 'app/bundle.js', [ 'wp-util' ], self::PLUGIN_VERSION, false );
	}
}
