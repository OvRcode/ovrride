<?php
namespace YaySMTPAmazonSES;

defined( 'ABSPATH' ) || exit;

/**
 * Plugin activate/deactivate logic
 */
class Plugin {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function doHooks() {
		$optionVersion   = YAY_SMTP_AMAZONSES_PREFIX . '_version';
		$current_version = get_option( $optionVersion );
		if ( version_compare( YAY_SMTP_AMAZONSES_VERSION, $current_version, '>' ) ) {
			self::activate();
			update_option( $optionVersion, YAY_SMTP_AMAZONSES_VERSION );
		}
		Page\Settings::getInstance();
		PluginCore::getInstance();
		Functions::getInstance();
	}

	private function __construct() {}

	/** Plugin activated hook */
	public static function activate() {
		Helper\Installer::getInstance();
	}

	/** Plugin deactivate hook */
	public static function deactivate() {}
}
