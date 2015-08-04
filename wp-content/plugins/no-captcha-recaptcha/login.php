<?php

class Ncr_Login_Captcha extends Ncr_No_Captcha_Recaptcha {

	public static function initialize() {

		// initialize if login is activated
		if ( isset(self::$plugin_options['captcha_login']) && self::$plugin_options['captcha_login'] == 'yes') {
			// adds the captcha to the login form
			add_action( 'login_form', array( __CLASS__, 'display_captcha' ) );

			// authenticate the captcha answer
			add_action( 'wp_authenticate_user', array( __CLASS__, 'validate_captcha' ), 10, 2 );
		}
	}

	/**
	 * Verify the captcha answer
	 *
	 * @param $user string login username
	 * @param $password string login password
	 *
	 * @return WP_Error|WP_user
	 */
	public static function validate_captcha( $user, $password ) {

		if ( ! isset( $_POST['g-recaptcha-response'] ) || ! self::captcha_verification() ) {
			return new WP_Error( 'empty_captcha', self::$error_message );
		}

		return $user;
	}
}