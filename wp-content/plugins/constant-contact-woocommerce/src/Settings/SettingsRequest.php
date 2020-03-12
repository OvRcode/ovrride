<?php
/**
 * Concrete implementation of the Request class specific to submitting settings.
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   2019-03-07
 */

namespace WebDevStudios\CCForWoo\Api;

use WebDevStudios\CCForWoo\Settings\SettingsModel;

/**
 * Class SettingsSubmitter
 *
 * @author  Jeremy Ward <jeremy.ward@webdevstudios.com>
 * @package WebDevStudios\CCForWoo\Api
 * @since   2019-03-07
 */
class SettingsRequest extends Request {
	/**
	 * Instance of the settings model.
	 *
	 * @var SettingsModel
	 * @since 2019-03-07
	 */
	private $settings;

	/**
	 * SettingsSubmitter constructor.
	 *
	 * @param SettingsModel $settings Instance of the settings model.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 */
	public function __construct( SettingsModel $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Prepare the request data.
	 *
	 * @author Jeremy Ward <jeremy.ward@webdevstudios.com>
	 * @since  2019-03-07
	 */
	public function prepare_data() {
		// TODO: Implement prepare_data() method - this should take the SettingsModel and convert it into an array for the API.
	}
}
