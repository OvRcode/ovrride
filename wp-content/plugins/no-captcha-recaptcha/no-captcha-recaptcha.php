<?php

/*
Plugin Name: No CAPTCHA reCAPTCHA
Plugin URI: http://w3guy.com
Description: Protect WordPress login, registration and comment form from spam with the new No CAPTCHA reCAPTCHA
Version: 1.0.2
Author: Agbonghama Collins
Author URI: http://w3guy.com
License: GPL2
Text Domain: ncr-captcha
Domain Path: /lang/
*/

require_once 'base-class.php';
require_once 'registration.php';
require_once 'comment-form.php';
require_once 'login.php';
require_once 'settings.php';

register_activation_hook( __FILE__, array( 'Ncr_No_Captcha_Recaptcha', 'on_activation' ) );

register_uninstall_hook( __FILE__, array( 'Ncr_No_Captcha_Recaptcha', 'on_uninstall' ) );

Ncr_No_Captcha_Recaptcha::initialize();
Ncr_Registration_Captcha::initialize();
Ncr_Comment_Captcha::initialize();
Ncr_Login_Captcha::initialize();
Ncr_Settings_Page::initialize();

