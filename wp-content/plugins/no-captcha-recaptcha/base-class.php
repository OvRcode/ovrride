<?php


class Ncr_No_Captcha_Recaptcha {

	/** @var string captcha site key */
	static private $site_key;

	/** @var string captcha secrete key */
	static private $secret_key;

	static private $theme;

	static private $language;

	static protected $error_message;

	static protected $plugin_options;

	public static function initialize() {

		self::$plugin_options = get_option( 'ncr_options' );

		self::$site_key = self::$plugin_options['site_key'];

		self::$secret_key = self::$plugin_options['secrete_key'];

		self::$theme = self::$plugin_options['theme'];

		self::$language = self::$plugin_options['language'];

		self::$error_message = self::$plugin_options['error_message'];


		add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin_textdomain' ) );

		// initialize if login is activated
		if ( ( isset( self::$plugin_options['captcha_registration'] ) && self::$plugin_options['captcha_registration'] == 'yes' ) || ( isset( self::$plugin_options['captcha_login'] ) && self::$plugin_options['captcha_login'] == 'yes' ) ) {

			add_action( 'login_enqueue_scripts', array( __CLASS__, 'header_script' ) );

			add_action( 'login_enqueue_scripts', array( __CLASS__, 'default_wp_login_reg_css' ) );
		}
	}

	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'espw-plugin', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/** reCAPTCHA header script */
	public static function header_script() {

		$lang_option = self::$plugin_options['language'];

		// if language is empty (auto detected chosen) do nothing otherwise add the lang query to the
		// reCAPTCHA script url
		if ( isset( $lang_option ) && ( ! empty( $lang_option ) ) ) {
			$lang = "?hl=$lang_option";
		} else {
			$lang = null;
		}

		echo '<script src="https://www.google.com/recaptcha/api.js' . $lang . '" async defer></script>' . "\r\n";
	}


	/** Increase the width of login/registration form */
	public static function default_wp_login_reg_css() {
		echo '<style type="text/css">
					#login {
					width: 350px !important;
					}
				</style>' . "\r\n";
	}


	/** Output the reCAPTCHA form field. */
	public static function display_captcha() {

		if ( isset( $_GET['captcha'] ) && $_GET['captcha'] == 'failed' ) {
			echo self::$error_message;
		}

		echo '<div class="g-recaptcha" data-sitekey="' . self::$site_key . '" data-theme="' . self::$theme . '"></div>';
	}

	/**
	 * Send a GET request to verify captcha challenge
	 *
	 * @return bool
	 */
	public static function captcha_verification() {

		$response = isset( $_POST['g-recaptcha-response'] ) ? esc_attr( $_POST['g-recaptcha-response'] ) : '';

		$remote_ip = $_SERVER["REMOTE_ADDR"];

		// make a GET request to the Google reCAPTCHA Server
		$request = wp_remote_get(
			'https://www.google.com/recaptcha/api/siteverify?secret=' . self::$secret_key . '&response=' . $response . '&remoteip=' . $remote_ip
		);

		// get the request response body
		$response_body = wp_remote_retrieve_body( $request );

		$result = json_decode( $response_body, true );

		return $result['success'];
	}


	public static function on_activation() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		$plugin = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : '';
		check_admin_referer( "activate-plugin_{$plugin}" );

		$default_options = array(
			'captcha_registration' => 'yes',
			'captcha_comment'      => 'yes',
			'theme'                => 'light',
			'error_message'        => '<strong>ERROR</strong>: Please retry CAPTCHA'
		);

		add_option( 'ncr_options', $default_options );
	}

	public static function on_uninstall() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		delete_option( 'ncr_options' );
	}
}