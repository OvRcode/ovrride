<?php

/*  Copyright 2014  Captcha, Inc. (email : development@captcha.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * WordPress DB defaults & options
 */
define('BDWP_PLUGIN_PATH', dirname(dirname(__FILE__)));
define('BDWP_INCLUDE_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

$LBD_WP_Defaults['generator'] = 'library';
$LBD_WP_Defaults['library_path'] = BDWP_PLUGIN_PATH . DIRECTORY_SEPARATOR;
$LBD_WP_Defaults['library_assets_url'] = plugin_dir_url(BDWP_INCLUDE_PATH) . 'botdetect/public/';
$LBD_WP_Defaults['on_login'] = true;
$LBD_WP_Defaults['on_comments'] = true;
$LBD_WP_Defaults['on_lost_password'] = true;
$LBD_WP_Defaults['on_registration'] = true;
$LBD_WP_Defaults['audio'] = true;
$LBD_WP_Defaults['image_width'] = 235;
$LBD_WP_Defaults['image_height'] = 50;
$LBD_WP_Defaults['min_code_length'] = 3;
$LBD_WP_Defaults['max_code_length'] = 5;
$LBD_WP_Defaults['helplink'] = 'image';
$LBD_WP_Defaults['remote'] = true;

$LBD_WP_Options = get_option('botdetect_options');
if (is_array($LBD_WP_Options)) {
	if (array_key_exists('chk_default_options_db', $LBD_WP_Options) && $LBD_WP_Options['chk_default_options_db'] == true) {
		$LBD_WP_Options = $LBD_WP_Defaults;
	} else {
		$LBD_WP_Options = array_merge($LBD_WP_Defaults, $LBD_WP_Options);
	}
} else {
	$LBD_WP_Options = $LBD_WP_Defaults;
}

/**
 * In case of a local library generator, include the required library files and route the request.
 */
if ($LBD_WP_Options['generator'] == 'library' && is_file($LBD_WP_Options['library_path'] . 'botdetect/CaptchaIncludes.php')) {

	define('LBD_INCLUDE_PATH', $LBD_WP_Options['library_path'] . 'botdetect/');
	define('LBD_URL_ROOT', $LBD_WP_Options['library_assets_url']);

	require_once($LBD_WP_Options['library_path'] . 'botdetect/CaptchaIncludes.php');
	require_once($LBD_WP_Options['library_path'] . 'botdetect/CaptchaConfig.php');

	// Configure Botdetect with WP settings
	$LBD_CaptchaConfig = CaptchaConfiguration::GetSettings();
	$LBD_CaptchaConfig->HandlerUrl = home_url( '/' ) . 'index.php?botdetect_request=1'; //handle trough the WP stack
	$LBD_CaptchaConfig->ReloadIconUrl = $LBD_WP_Options['library_assets_url'] . 'lbd_reload_icon.gif';
	$LBD_CaptchaConfig->SoundIconUrl = $LBD_WP_Options['library_assets_url'] . 'lbd_sound_icon.gif';
	$LBD_CaptchaConfig->LayoutStylesheetUrl = $LBD_WP_Options['library_assets_url'] . 'lbd_layout.css';
	$LBD_CaptchaConfig->ScriptIncludeUrl = $LBD_WP_Options['library_assets_url'] . 'lbd_scripts.js';

	$LBD_CaptchaConfig->CodeLength = CaptchaRandomization::GetRandomCodeLength($LBD_WP_Options['min_code_length'], $LBD_WP_Options['max_code_length']);
	$LBD_CaptchaConfig->ImageWidth = $LBD_WP_Options['image_width'];
	$LBD_CaptchaConfig->ImageHeight = $LBD_WP_Options['image_height'];

	$LBD_CaptchaConfig->SoundEnabled = $LBD_WP_Options['audio'];
	$LBD_CaptchaConfig->RemoteScriptEnabled = $LBD_WP_Options['remote'];  

	switch ($LBD_WP_Options['helplink']) {
		case 'image':
			$LBD_CaptchaConfig->HelpLinkMode = HelpLinkMode::Image;
			break;

		case 'text':
			$LBD_CaptchaConfig->HelpLinkMode = HelpLinkMode::Text;
			break;

		case 'off':
			$LBD_CaptchaConfig->HelpLinkEnabled = false;
			break;

		default:
			$LBD_CaptchaConfig->HelpLinkMode = HelpLinkMode::Image;
			break;
	}

	// Route the request
	if (isset($_GET['botdetect_request']) && $_GET['botdetect_request']) {
	  	// direct access, proceed as Captcha handler (serving images and sounds), terminates on output.
	  	require_once(LBD_INCLUDE_PATH . 'CaptchaHandler.php');
	} else {
	  	// included in another file, proceed as Captcha class (form helper)
	  	require_once(LBD_INCLUDE_PATH . 'CaptchaClass.php');
	}
}

// Included in another file
require_once(BDWP_INCLUDE_PATH . 'BDWPIncludes.php');

// Update plugin
if (is_admin()) {
	$pluginInfoForUpdate = array(
		'plugin_basename' => plugin_basename(BDWP_PLUGIN_PATH) . '/' . basename(__FILE__),
		'plugin_folder' => plugin_basename(BDWP_PLUGIN_PATH),
		'plugin_version' => BDWP_PluginInfo::GetVersion()
	);
   	new BDWP_Update($pluginInfoForUpdate);
}

// BotDetect Plugin
$pluginInfo = array(
	'plugin_basename' => plugin_basename(BDWP_PLUGIN_PATH) . '/' . basename(__FILE__),
	'plugin_path' => BDWP_PLUGIN_PATH . DIRECTORY_SEPARATOR . basename(__FILE__)
);
new WP_BotDetect_Plugin($LBD_WP_Options, $pluginInfo);
