<?php
require_once(BDWP_INCLUDE_PATH . 'WordPress.php');
require_once(BDWP_INCLUDE_PATH . 'PluginInfo.php');
require_once(BDWP_INCLUDE_PATH . 'HttpHelpers.php');
require_once(BDWP_INCLUDE_PATH . 'Tools.php');
require_once(BDWP_INCLUDE_PATH . 'Localization.php');
require_once(BDWP_INCLUDE_PATH . 'Database.php');
require_once(BDWP_INCLUDE_PATH . 'BackwardCompatibility.php');
require_once(BDWP_INCLUDE_PATH . 'Diagnostics.php');

// Add show and handlers captcha forms
require_once(BDWP_INCLUDE_PATH . 'CaptchaIntegration.php');
require_once(BDWP_INCLUDE_PATH . 'Login.php');
require_once(BDWP_INCLUDE_PATH . 'Comments.php');
require_once(BDWP_INCLUDE_PATH . 'LostPassword.php');
require_once(BDWP_INCLUDE_PATH . 'Register.php');

require_once(BDWP_INCLUDE_PATH . 'InstallCaptchaProvider.php');

// Render options page
require_once(BDWP_INCLUDE_PATH . 'Settings.php');

// Add BD WordPress plugin
require_once(BDWP_INCLUDE_PATH . 'WordPressPlugin.php');

// Add Update class
if (is_admin()) { require_once(BDWP_INCLUDE_PATH . 'Update.php'); }
