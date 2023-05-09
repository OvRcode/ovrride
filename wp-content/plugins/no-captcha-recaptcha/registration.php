<?php

class Ncr_Registration_Captcha extends Ncr_No_Captcha_Recaptcha
{

    public static function initialize()
    {
        // initialize if login is activated
        if (isset(self::$plugin_options['captcha_registration']) && self::$plugin_options['captcha_registration'] == 'yes') {
            // adds the captcha to the registration form
            add_action('register_form', array(__CLASS__, 'display_captcha'), 999999999999999);

            // authenticate the captcha answer
            add_action('registration_errors', array(__CLASS__, 'validate_captcha_registration_field'), 10, 3);
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
    public static function validate_captcha_registration_field($errors, $sanitized_user_login, $user_email)
    {
        if ( ! isset($_POST['g-recaptcha-response']) || ! self::captcha_verification()) {
            $errors->add('failed_verification', self::$error_message);
        }

        return $errors;
    }
}