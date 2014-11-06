<?php
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BDWP_WordPress {

    public static function GetWordPressVersion() {
    	global $wp_version;
        return $wp_version;
    }

    /**
	 * Minimum WP version
	 */
    public static function MinimalRequiredVersion() {
    	
    	$pluginPath = BDWP_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'wp-botdetect-captcha.php';
		$plugin = plugin_basename($pluginPath);
		$pluginData = get_plugin_data($pluginPath, false );

		if (version_compare(self::GetWordPressVersion(), "3.3", "<")) {
			if (is_plugin_active($plugin)) {
				deactivate_plugins($plugin);
				wp_die(sprintf(__('\'%s\' requires WordPress 3.3 or higher, and has been deactivated! Please upgrade WordPress and try again.<br /><br />Back to <a href="%s">WordPress admin</a>.', 'botdetect-wp-captcha'), $pluginData['Name'], admin_url()));
			}
		}
    }
}
