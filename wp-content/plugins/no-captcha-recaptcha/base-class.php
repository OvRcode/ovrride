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

	static protected $script_handle;

	static protected $textdomain;

	public static function initialize() {

		self::$plugin_options = get_option( 'ncr_options' );

		self::$site_key = isset( self::$plugin_options['site_key'] ) ? self::$plugin_options['site_key'] : '';

		self::$secret_key = isset( self::$plugin_options['secrete_key'] ) ? self::$plugin_options['secrete_key'] : '';

		self::$theme = isset( self::$plugin_options['theme'] ) ? self::$plugin_options['theme'] : 'light';

		self::$language = isset( self::$plugin_options['language'] ) ? self::$plugin_options['language'] : '';

		self::$error_message = isset( self::$plugin_options['error_message'] ) ? self::$plugin_options['error_message'] : wp_kses( __( '<strong>ERROR</strong>: Please retry CAPTCHA', 'ncr-catpcha' ), array(  'strong' => array() ) );


		self::$script_handle = 'g-recaptcha';

		self::$textdomain = 'ncr-captcha';

		add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin_textdomain' ) );

		// initialize if login is activated
		if ( ( isset( self::$plugin_options['captcha_registration'] ) && self::$plugin_options['captcha_registration'] == 'yes' ) || ( isset( self::$plugin_options['captcha_login'] ) && self::$plugin_options['captcha_login'] == 'yes' ) ) {

			add_action( 'login_enqueue_scripts', array( __CLASS__, 'header_script' ) );

			add_action( 'login_enqueue_scripts', array( __CLASS__, 'default_wp_login_reg_css' ) );
		}

		// Add the "async" attribute to our registered script.
		add_filter( 'script_loader_tag',  array( __CLASS__, 'add_async_attribute' ), 10, 2 );

	}

	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'ncr-captcha', false, basename( dirname( __FILE__ ) ) . '/lang/' );
	}

	/** reCAPTCHA header script */
	public static function header_script() {

		$lang_option = self::$language;

		// if language is empty (auto detected chosen) do nothing otherwise add the lang query to the
		// reCAPTCHA script url
		if ( isset( $lang_option ) && ( ! empty( $lang_option ) ) ) {
			$lang = "?hl=$lang_option";
		} else {
			$lang = null;
		}

		echo '<script src="https://www.google.com/recaptcha/api.js' . $lang . '" async defer></script>' . "\r\n";
	}

	/**
	* Enqueue the Google ReCAPTCHA script using the WP system.
	*
	* @since 1.0.3
	*/
	public static function enqueue_header_script() {

		// if language is empty (auto detected chosen) do nothing otherwise add the lang query to the
		// reCAPTCHA script url
		if ( ! empty( self::$language ) ) {
			$lang = "?hl={self::$language}";
		} else {
			$lang = '';
		}

		$src = 'https://www.google.com/recaptcha/api.js' . $lang;

		wp_enqueue_script( self::$script_handle, $src, false, false, true );
	}

	/**
	* Add the "async" attribute to our registered script.
	*
	* @since 1.0.3
	*/
	public static function add_async_attribute( $tag, $handle ) {
	    if ( $handle == self::$script_handle ) {
	       $tag = str_replace( ' src', ' async="async" src', $tag );
	    }
	    return $tag;
	}

	/** Increase the width of login/registration form */
	public static function default_wp_login_reg_css() {
		echo '<style type="text/css">
					#login {
					width: 350px !important;
					}
					div.g-recaptcha {margin-bottom: 10px}
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

		$default_options = array(
			'captcha_registration' => 'yes',
			'captcha_registration_bp' => 'no',
			'captcha_comment'      => 'yes',
			'theme'                => 'light',
			'error_message'        => wp_kses( __( '<strong>ERROR</strong>: Please confirm you are not a robot', 'ncr-catpcha' ), array(  'strong' => array() ) ),
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
