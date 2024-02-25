<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.prositeweb.ca/
 * @since      1.0.0
 *
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/includes
 * @author     Prositeweb Inc <contact@prositeweb.ca>
 */
class Prositecaptcha_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'prositecaptcha',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
