<?php 
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

class BDWP_Diagnostics {

	/**
	 * Get all themes in client's wordpress and activated status
	 */
	public static function GetThemes() {
		$themes = array();
		$allThemes = wp_get_themes();
		$currentTheme = get_stylesheet();

		foreach ($allThemes as $theme => $info) {
			$tempTheme = array(
				'Name'      => $info->display('Name'),
				'Version'   => $info->display('Version'),
				'Activated' => $theme == $currentTheme
			);
			array_push($themes, $tempTheme);
		}
		return $themes;	
	}

	/**
	 * Get all plugins that have been installed and activated status
	 */
	public static function GetPlugins() {
		$plugins = array();
		$allPlugins = get_plugins();
		$activePlugins = get_option('active_plugins');
		
		foreach ($allPlugins as $plugin => $info) {
			$tempPlugin = array(
				'Name'      => $info['Name'],
				'Version'   => $info['Version'],
				'Activated' => in_array($plugin, $activePlugins)
			);
			array_push($plugins, $tempPlugin);
		}
		return $plugins;
	}

	/**
	 * Get WordPress version
	 */
	public static function GetWordPressVersion() {
		global $wp_version;
		return $wp_version;
	}

	/**
	 * Check Session is enabled
	 */
	public static function IsSessionEnabled() {

	    if (function_exists('session_status')) { // PHP >= 5.4.0
	    	return (session_status() === PHP_SESSION_ACTIVE); 
		} else {
			return session_id() === '' ? false : true;
		}
	}

	/**
	 * Check Multisite is enabled
	 */
	public static function IsWordPressConfiguredAsMultisite() {
		return is_multisite();
	}
}
