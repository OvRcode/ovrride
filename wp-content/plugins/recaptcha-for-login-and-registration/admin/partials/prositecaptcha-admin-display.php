<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.prositeweb.ca/
 * @since      1.0.0
 *
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="prositecaptcha">
    	<h2 class=""><?php echo esc_html__('Google Recaptcha Main settings', 'prositecaptcha'); ?></h2>
	<p><?php echo esc_html__('You can generate or edit your reCAPTCHA Key with this link', 'prositecaptcha'); ?> <a href="https://www.google.com/recaptcha/intro/v3.html" target="_blank">google.com/recaptcha</a>.<?php echo esc_html__('For more information about how to generate the key, please visit this', 'prositecaptcha'); ?>  
	<a href="https://www.prositeweb.ca/comment-proteger-votre-site-web-contre-les-spams-avec-google-captcha-v3-php/" target="_blank"><?php echo esc_html__('link', 'prositecaptcha'); ?></a></p>
<form method="POST" action='options.php'>
   <?php
         settings_fields($this->plugin_name);
         do_settings_sections('prositecaptcha-settings-page');

         submit_button();  
   ?>
</form>
</div>