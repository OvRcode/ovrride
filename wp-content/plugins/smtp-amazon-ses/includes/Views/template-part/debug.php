<?php

use YaySMTPAmazonSES\Helper\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="yay-smtp-debug">
  <div>
	<p>Oops! Your email can't be sent.</p>
	<p>
	  <strong>What happened?</strong><br>
	  <span>This error might be caused by:</span><br>
	  <span>- Incorrect SMTP settings or Mailer configuration</span><br>
	  <span>- Connection blocked by your web server</span><br>
	  <span>- Connection rejected by your host (many shared hosts block certain ports)</span>
	</p>
	<p>
	  <strong>What to do next?</strong><br>
	  <span>- Double check all configuration and settings</span><br>
	  <span>- Try another mailer</span><br>
	  <span>- Ask your hosting provider to enable your external connections</span>
	</p>
	<p>
	  <strong>Can't figure it out?</strong><br>
	  <span>Please <a href="https://yaycommerce.com/support/" target="_blank">contact us</a> to ask for help. We'll get back to you ASAP.</span>
	</p>
  </div>
  <h3>Debug</h3>
  <strong>Versions:</strong><br>
  <strong>WordPress:</strong><?php echo ' ' . wp_kses_post( get_bloginfo( 'version' ) ); ?> <br>
  <strong>WordPress MS:</strong><?php echo is_multisite() ? ' Yes' : ' No'; ?> <br>
  <strong>PHP:</strong><?php echo ' ' . PHP_VERSION; ?> <br>
  <strong>YaySMTP:</strong><?php echo ' ' . wp_kses_post( YAY_SMTP_AMAZONSES_VERSION ); ?> <br><br>

  <strong>Params:</strong><br>
  <strong>Mailer:</strong><?php echo ' ' . wp_kses_post( Utils::getCurrentMailer() ); ?><br><br>

  <div class="yay-smtp-debug-text"></div>

</div>
