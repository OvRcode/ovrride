<?php
/**
* Add the Google No ReCAPTCHA to the BuddyPress registration form.
*
* @since 1.0.3
*/

class Ncr_BP_Registration_Captcha extends Ncr_No_Captcha_Recaptcha {

	public static function initialize() {
		// Initialize if the site admin has enabled the CAPTCHA for the BP registration page.
		if ( isset( self::$plugin_options['captcha_registration_bp'] ) && self::$plugin_options['captcha_registration_bp'] == 'yes' ) {
			// Add scripts to BuddyPress registration page.
			// We fire BP-specific code on a BP-specific action hook.
			add_action( 'bp_init',  array( __CLASS__, 'enqueue_script' )  );

			// Adds the CAPTCHA to the BuddyPress registration form
			// Allow theme/plugin authors the opportunity to change the display priority,
			// so that the CAPTCHA appears where needed. (We assume near the submit button.)
			$hook_priority = apply_filters( 'ncr_bp_captcha_display_priority', 95 );
			add_action( 'bp_before_registration_submit_buttons', array( __CLASS__, 'display_captcha' ), $hook_priority );

			// Authenticate the captcha answer.
			add_action( 'bp_signup_validate', array( __CLASS__, 'validate_captcha_registration_field' ) );
		}
	}

	/**
	 * Enqueue needed scripts on BuddyPress' registration page.
	 *
	 * @since 1.0.3
	 */
	public static function enqueue_script() {
		if ( bp_is_register_page() ) {
			// Add the Google reCAPTCHA script.
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_header_script' ) );
		}
	}

	/**
	 * Wrap the standard reCAPTCHA form field in typical BP registration form container markup.
	 *
	 * @since 1.0.3
	 */
	public static function display_captcha() {
		$section_class = apply_filters( 'ncr_bp_register_section_class', 'register-section' );
		?>
		<div class="<?php echo $section_class; ?>" id="ncr-robot-check">
			<h4><?php _e( 'Verify that you are a human.', self::$textdomain ); ?></h4>
			<?php
			parent::display_captcha();
			do_action( 'bp_failed_recaptcha_verification_errors' );
			?>
		</div>
		<?php
	}

	/**
	 * Verify the captcha answer
	 *
	 * @since 1.0.3
	 *
	 * @return void Adds error element to BP's signup errors object.
	 */
	public static function validate_captcha_registration_field() {
		if ( ! isset( $_POST['g-recaptcha-response'] ) || ! ( $validated = self::captcha_verification() ) ) {
			buddypress()->signup->errors['failed_recaptcha_verification'] = self::$error_message;
		}
		do_action( 'ncr_failed_recaptcha_verification', $validated );
	}
}