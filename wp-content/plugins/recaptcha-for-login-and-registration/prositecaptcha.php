<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.prositeweb.ca/
 * @since             1.0.0
 * @package           Prositecaptcha
 *
 * @wordpress-plugin
 * Plugin Name:       Recaptcha for Login and registration
 * Plugin URI:        https://www.prositeweb.ca/en/google-recaptcha-for-wordpress-login-registration
 * Description:       This plugin with add google Recaptcha on your registration or Login page. The purpose is to avoid spam and better improve your website security
 * Version:           1.10
 * Author:            Prositeweb Inc
 * Author URI:        https://www.prositeweb.ca/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       prositecaptcha
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PROSITECAPTCHA_VERSION', '1.10' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-prositecaptcha-activator.php
 */
function activate_prositecaptcha() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prositecaptcha-activator.php';
	Prositecaptcha_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-prositecaptcha-deactivator.php
 */
function deactivate_prositecaptcha() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-prositecaptcha-deactivator.php';
	Prositecaptcha_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_prositecaptcha' );
register_deactivation_hook( __FILE__, 'deactivate_prositecaptcha' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-prositecaptcha.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_prositecaptcha() {

	$plugin = new Prositecaptcha();
	$plugin->run();

}
run_prositecaptcha();
