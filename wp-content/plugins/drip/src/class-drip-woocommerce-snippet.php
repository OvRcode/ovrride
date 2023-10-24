<?php
/**
 * Injects Drip JS snippet
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

/**
 * Injects Drip JS snippet
 */
class Drip_Woocommerce_Snippet {
	/**
	 * Set up component
	 */
	public static function init() {
		add_action( 'wp_footer', __CLASS__ . '::render_snippet' );
	}

	/**
	 * Render JS snippet
	 */
	public static function render_snippet() {
		$account_id = self::get_account_id();
		if ( $account_id ) {
			include 'snippet.js.php';
		} else {
			echo '<!-- Add your woocommerce credentials in Drip to begin tracking -->';
		}
	}

	/**
	 * Get the account ID from settings
	 */
	public static function get_account_id() {
		return WC_Admin_Settings::get_option( Drip_Woocommerce_Settings::ACCOUNT_ID_KEY );
	}
}
