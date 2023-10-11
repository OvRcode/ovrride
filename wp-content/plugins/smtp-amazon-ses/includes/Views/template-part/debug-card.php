<?php
use YaySMTPAmazonSES\Helper\LogErrors;
use YaySMTPAmazonSES\Helper\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$yayDebugText = implode( '<br>', LogErrors::getErr() );

$yay_succ_sent_mail_last = true;
$yaysmtpSettings         = Utils::getYaySmtpSetting();

if ( ! empty( $yaysmtpSettings ) && isset( $yaysmtpSettings['succ_sent_mail_last'] ) && false === $yaysmtpSettings['succ_sent_mail_last'] ) {
	$yay_succ_sent_mail_last = false;
}

?>
<div class="yay-smtp-card yay-smtp-card-debug" style="display: <?php echo true === $yay_succ_sent_mail_last ? 'none' : 'block'; ?>">
  <div class="yay-smtp-card-header">
	<div class="yay-smtp-card-title-wrapper">
	  <h3 class="yay-smtp-card-title yay-smtp-card-header-item">
		<?php echo esc_html__( 'Email Delivery Issue', 'yay-smtp-amazonses' ); ?>
	  </h3>
	</div>
  </div>
  <div class="yay-smtp-card-body">
	<p class="setting-description">
	  <?php echo esc_html__( 'YaySMTP noticed this error (yikes!) while trying to send the most recent emails:', 'yay-smtp-amazonses' ); ?>
	</p>
	<p class="setting-description yay-smtp-card-debug-text">
	  <?php echo wp_kses_post( $yayDebugText ); ?>
	</p>
	<p class="setting-description" style="font-weight: 450;">
	  <?php echo wp_kses_post( 'Please fix it and send a test email afterwards. <a href="https://yaycommerce.com/support/" target="_blank">Contact us</a> if you need any helps.', 'yay-smtp-amazonses' ); ?>
	</p>
  </div>
</div>
