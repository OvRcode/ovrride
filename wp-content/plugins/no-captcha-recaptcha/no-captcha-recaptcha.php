<?php

/*
Plugin Name: No CAPTCHA reCAPTCHA
Plugin URI: http://w3guy.com
Description: Protect WordPress login, registration and comment form from spam with the new No CAPTCHA reCAPTCHA
Version: 1.3.4
Author: MailOptin Team
Author URI: https://mailoptin.io
License: GPL2
Text Domain: ncr-captcha
Domain Path: /lang/
*/

require_once dirname(__FILE__). '/mo-admin-notice.php';
require_once dirname(__FILE__). '/base-class.php';
require_once dirname(__FILE__). '/registration.php';
require_once dirname(__FILE__). '/buddypress-registration.php';
require_once dirname(__FILE__). '/comment-form.php';
require_once dirname(__FILE__). '/login.php';
require_once dirname(__FILE__). '/settings.php';

register_activation_hook( __FILE__, array( 'Ncr_No_Captcha_Recaptcha', 'on_activation' ) );

register_uninstall_hook( __FILE__, array( 'Ncr_No_Captcha_Recaptcha', 'on_uninstall' ) );

Ncr_No_Captcha_Recaptcha::initialize();
Ncr_Registration_Captcha::initialize();
Ncr_BP_Registration_Captcha::initialize();
Ncr_Comment_Captcha::initialize();
Ncr_Login_Captcha::initialize();
Ncr_Settings_Page::initialize();

