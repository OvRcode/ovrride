<?php
use YaySMTPAmazonSES\Helper\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$templatePart     = YAY_SMTP_AMAZONSES_PLUGIN_PATH . 'includes/Views/template-part';
$currentMailer    = Utils::getCurrentMailer();
$isMailerComplete = Utils::isMailerComplete();
$yaysmtpSettings  = Utils::getYaySmtpSetting();

// SHOW/HIDE Amazon SES veriry Email Sender Description. - start
$regionAmazonSES = 'us-east-1';
if ( ! empty( $yaysmtpSettings ) ) {
	if ( ! empty( $yaysmtpSettings['amazonses'] ) && ! empty( $yaysmtpSettings['amazonses']['region'] ) ) {
		$regionAmazonSES = $yaysmtpSettings['amazonses']['region'];
	}
}
$verifyAmazonSesEmailLink = 'https://' . $regionAmazonSES . '.console.aws.amazon.com/ses/home?region=' . $regionAmazonSES . '#verified-senders-email:';
$amozonSesDesShow         = 'none';
if ( 'amazonses' == Utils::getCurrentMailer() ) {
	$amozonSesDesShow = 'block';
}
// SHOW/HIDE Amazon SES veriry Email Sender Description. - end

$yaySMTPerList = array(
	'amazonses' => 'Amazon SES',
);

?>
<!-- Mail Setting page - start -->
<div class="<?php echo esc_attr( YAY_SMTP_AMAZONSES_PREFIX ); ?> yay-smtp-wrap send-mail-settings-wrap">
  <div class="yay-smtp-header">
	<div class="yay-smtp-title">
	  <h2>YaySMTP for Amazon SES</h2>
	</div>
	<div class="yay-button-wrap">
	  <div class="yay-tooltip">
		<button type="button" class="yay-smtp-button panel-tab-btn send-test-mail-panel">
		  <svg viewBox="64 64 896 896" data-icon="mail" width="15" height="15" fill="currentColor" aria-hidden="true" focusable="false" class=""><path d="M928 160H96c-17.7 0-32 14.3-32 32v640c0 17.7 14.3 32 32 32h832c17.7 0 32-14.3 32-32V192c0-17.7-14.3-32-32-32zm-40 110.8V792H136V270.8l-27.6-21.5 39.3-50.5 42.8 33.3h643.1l42.8-33.3 39.3 50.5-27.7 21.5zM833.6 232L512 482 190.4 232l-42.8-33.3-39.3 50.5 27.6 21.5 341.6 265.6a55.99 55.99 0 0 0 68.7 0L888 270.8l27.6-21.5-39.3-50.5-42.7 33.2z"></path></svg>
		  <span class="text">Send Test Email</span>
		</button>
		<!-- <span class="yay-tooltiptext yay-tooltip-bottom">Send test mail</span> -->
	  </div>
	  <div class="yay-tooltip">
		<a class="yay-smtp-button panel-tab-btn other mail-logs-button" href="<?php //echo YAY_SMTP_AMAZONSES_SITE_URL . '/wp-admin/admin.php?page=yaysmtp&tab=email-logs' ?>">
		  <svg width="15" height="15" fill="currentColor" aria-hidden="true" focusable="false" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" >
			<g>
			  <g>
				<g>
				  <path d="M32,464V48c0-8.837,7.163-16,16-16h240v64c0,17.673,14.327,32,32,32h64v48h32v-64c0.025-4.253-1.645-8.341-4.64-11.36
					l-96-96C312.341,1.645,308.253-0.024,304,0H48C21.49,0,0,21.491,0,48v416c0,26.51,21.49,48,48,48h112v-32H48
					C39.164,480,32,472.837,32,464z"/>
				  <path d="M480,320h-32v32h32v32h-64v-96h96c0-17.673-14.327-32-32-32h-64c-17.673,0-32,14.327-32,32v96c0,17.673,14.327,32,32,32
					h64c17.673,0,32-14.327,32-32v-32C512,334.327,497.673,320,480,320z"/>
				  <path d="M304,256c-35.346,0-64,28.654-64,64v32c0,35.346,28.654,64,64,64c35.346,0,64-28.654,64-64v-32
					C368,284.654,339.346,256,304,256z M336,352c0,17.673-14.327,32-32,32c-17.673,0-32-14.327-32-32v-32c0-17.673,14.327-32,32-32
					c17.673,0,32,14.327,32,32V352z"/>
				  <path d="M160,256h-32v144c0,8.837,7.163,16,16,16h80v-32h-64V256z"/>
				</g>
			  </g>
			</g>
			<g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g>
		  </svg>
		</a>
		<span class="yay-tooltiptext yay-tooltip-bottom">Email Logs</span>
	  </div>
	  <div class="yay-tooltip">
		<a class="yay-smtp-button panel-tab-btn other" href="https://yaycommerce.com/support/" target="_blank">
		  <svg viewBox="64 64 896 896" data-icon="message" width="15" height="15" fill="currentColor" aria-hidden="true" focusable="false" class=""><path d="M464 512a48 48 0 1 0 96 0 48 48 0 1 0-96 0zm200 0a48 48 0 1 0 96 0 48 48 0 1 0-96 0zm-400 0a48 48 0 1 0 96 0 48 48 0 1 0-96 0zm661.2-173.6c-22.6-53.7-55-101.9-96.3-143.3a444.35 444.35 0 0 0-143.3-96.3C630.6 75.7 572.2 64 512 64h-2c-60.6.3-119.3 12.3-174.5 35.9a445.35 445.35 0 0 0-142 96.5c-40.9 41.3-73 89.3-95.2 142.8-23 55.4-34.6 114.3-34.3 174.9A449.4 449.4 0 0 0 112 714v152a46 46 0 0 0 46 46h152.1A449.4 449.4 0 0 0 510 960h2.1c59.9 0 118-11.6 172.7-34.3a444.48 444.48 0 0 0 142.8-95.2c41.3-40.9 73.8-88.7 96.5-142 23.6-55.2 35.6-113.9 35.9-174.5.3-60.9-11.5-120-34.8-175.6zm-151.1 438C704 845.8 611 884 512 884h-1.7c-60.3-.3-120.2-15.3-173.1-43.5l-8.4-4.5H188V695.2l-4.5-8.4C155.3 633.9 140.3 574 140 513.7c-.4-99.7 37.7-193.3 107.6-263.8 69.8-70.5 163.1-109.5 262.8-109.9h1.7c50 0 98.5 9.7 144.2 28.9 44.6 18.7 84.6 45.6 119 80 34.3 34.3 61.3 74.4 80 119 19.4 46.2 29.1 95.2 28.9 145.8-.6 99.6-39.7 192.9-110.1 262.7z"></path></svg>
		</a>
		<span class="yay-tooltiptext yay-tooltip-bottom">Support</span>
	  </div>
	  <div class="yay-tooltip">
		<a class="yay-smtp-button panel-tab-btn other" href="https://yaycommerce.gitbook.io/yaysmtp/how-to-set-up-smtps/how-to-connect-amazonses" target="_blank">
		  <svg viewBox="64 64 896 896" data-icon="book" width="15" height="15" fill="currentColor" aria-hidden="true" focusable="false" class=""><path d="M832 64H192c-17.7 0-32 14.3-32 32v832c0 17.7 14.3 32 32 32h640c17.7 0 32-14.3 32-32V96c0-17.7-14.3-32-32-32zm-260 72h96v209.9L621.5 312 572 347.4V136zm220 752H232V136h280v296.9c0 3.3 1 6.6 3 9.3a15.9 15.9 0 0 0 22.3 3.7l83.8-59.9 81.4 59.4c2.7 2 6 3.1 9.4 3.1 8.8 0 16-7.2 16-16V136h64v752z"></path></svg>
		</a>
		<span class="yay-tooltiptext yay-tooltip-bottom">Documentation</span>
	  </div>
	</div>
	<!-- Send test mail drawer - start -->
	<?php Utils::getTemplatePart( $templatePart, 'send-test-mail', array( 'isMailerComplete' => $isMailerComplete ) ); ?>
	<!-- Send test mail drawer - end -->
  </div>
  <div class="yay-smtp-content">
	<!-- Debug card - start -->
	<?php Utils::getTemplatePart( $templatePart, 'debug-card' ); ?>
	<!-- Debug card - end -->
	<div class="yay-smtp-card">
	  <div class="yay-smtp-card-header">
		<div class="yay-smtp-card-title-wrapper">
		  <h3 class="yay-smtp-card-title yay-smtp-card-header-item">Sender Settings</h3>
		</div>
	  </div>
	  <div class="yay-smtp-card-body">
		<div class="setting-from-email">
		  <div class="setting-label">
			<label for="yay_smtp_setting_mail_from_email">From Email</label>
		  </div>
		  <div class="setting-field">
			<input type="text" id="yay_smtp_setting_mail_from_email" value="<?php echo esc_attr( Utils::getCurrentFromEmail() ); ?>" />
			<p class="error-message-email" style="display:none"></p>
			<p class="setting-description">
			  The email displayed in the "From" field.
			</p>
			<div>
			  <input
				id="yay_smtp_setting_mail_force_from_email"
				type="checkbox"
				name="force_from_email"
				<?php checked( Utils::getForceFromEmail(), 1 ); ?>
			  />
			  <label for="yay_smtp_setting_mail_force_from_email">Force From Email</label>
			  <div class="yay-tooltip icon-tootip-wrap">
				<span class="icon-inst-tootip"></span>
				<span class="yay-tooltiptext yay-tooltip-bottom"><?php echo esc_html__( 'Always send emails with the above From Email address, overriding other plugins settings.', 'yay-smtp-amazonses' ); ?></span>
			  </div>
			</div>
			<p class="setting-description yay-amazon-ses-des" style="display: <?php echo esc_attr( $amozonSesDesShow ); ?>">
			  Please note: If your account is still in Amazon SES sandbox mode.<br>
			  - You can only send mail from verified email addresses.<br>
			  <a href="<?php echo esc_attr( $verifyAmazonSesEmailLink ); ?>" target="_blank" rel="noopener noreferrer">Click to verify Email From</a>
			</p>
		  </div>
		</div>
		<div class="setting-from-name">
		  <div class="setting-label">
			<label for="yay_smtp_setting_mail_from_name">From Name</label>
		  </div>
		  <div class="setting-field">
			<input type="text" id="yay_smtp_setting_mail_from_name" value="<?php echo esc_attr( Utils::getCurrentFromName() ); ?>"/>
			<p class="setting-description">
			  The name displayed in emails
			</p>
			<div>
			  <input
				id="yay_smtp_setting_mail_force_from_name"
				type="checkbox"
				name="force_from_name"
				<?php checked( Utils::getForceFromName(), 1 ); ?>
			  />
			  <label for="yay_smtp_setting_mail_force_from_name">Force From Name</label>
			  <div class="yay-tooltip icon-tootip-wrap">
				<span class="icon-inst-tootip"></span>
				<span class="yay-tooltiptext yay-tooltip-bottom"><?php echo esc_html__( 'Always send emails with the above From Name, overriding other plugins settings.', 'yay-smtp-amazonses' ); ?></span>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
	<!-- Mailer Settings-->
	<div class="mailer-settings-component">
	  <?php
		Utils::getTemplatePart(
			$templatePart,
			'amazonses-tpl',
			array(
				'currentMailer' => $currentMailer,
				'params'        => $yaysmtpSettings,
			)
		);
		?>
	</div>
  </div>
  <div>
	<button type="button" class="yay-smtp-button yay-smtp-save-settings-action">Save Changes</button>
  </div>
</div>
<!-- Mail Setting page - end -->


<!-- Mail Logs page - start -->
<?php Utils::getTemplatePart( YAY_SMTP_AMAZONSES_PLUGIN_PATH . 'includes/Views', 'mail-logs' ); ?>
<!-- Mail Logs page - end -->





