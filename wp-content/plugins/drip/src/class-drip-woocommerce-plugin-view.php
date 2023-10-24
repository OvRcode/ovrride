<?php
/**
 * Plugin deactivation message
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Plugin deactivation message
 */
class Drip_Woocommerce_Plugin_View {
	/**
	 * Set up component
	 */
	public static function init() {
		$component = new self();
		$component->setup_actions();
	}

	/**
	 * Register deactivation hook
	 */
	public function setup_actions() {
		add_filter( 'plugin_action_links', array( $this, 'callback_plugin_action_links' ), 10, 2 );
	}

	/**
	 * Callback for plugin_action_links
	 *
	 * @param array $actions The actions for this plugin.
	 * @param array $plugin_file The files in the plugin.
	 */
	public function callback_plugin_action_links( $actions, $plugin_file ) {
		$is_this_plugin = in_array( $plugin_file, array( 'drip/drip.php' ), true );
		if ( $is_this_plugin ) {
			array_unshift( $actions, $this->integrations_url() );
		}
		return $actions;
	}

	/**
	 * The URL for the integration page
	 *
	 * Directs to the specific account id if we know it.
	 */
	private function integrations_url() {
		$account_id      = WC_Admin_Settings::get_option( Drip_Woocommerce_Settings::ACCOUNT_ID_KEY );
		$account_segment = '';
		if ( ! empty( $account_id ) ) {
			$account_segment = "${account_id}/";
		}
		return "<a href=\"https://www.getdrip.com/${account_segment}integrations/drip_woocommerce\">Integration Settings</a>";
	}
}
