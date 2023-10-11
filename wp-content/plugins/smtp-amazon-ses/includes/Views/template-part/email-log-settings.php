<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$yaySmtpSettingSave           = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['save_email_log'] ) ? $yaySmtpEmailLogSetting['save_email_log'] : 'yes';
$yaySmtpSettingInfType        = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['email_log_inf_type'] ) ? $yaySmtpEmailLogSetting['email_log_inf_type'] : 'full_inf';
$yaySmtpSettingDeleteDatetime = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['email_log_delete_time'] ) ? (int) $yaySmtpEmailLogSetting['email_log_delete_time'] : 60;

$yaySmtpDaleteTimes = array(
	'7'   => 'Last 7 Days',
	'30'  => 'Last 30 Days',
	'60'  => 'Last 60 Days',
	'180' => 'Last 180 Days',
	'365' => 'Last 365 Days',
	'0'   => 'Forever',
);
?>
<div class="yay-sidenav yay-smtp-mail-log-settings-drawer">
  <a href="javascript:void(0)" class="closebtn">&times;</a>
  <div class="yay-smtp-layout-activity-panel-content">
	<div class="yay-smtp-activity-panel-header">
	  <h3 class="yay-smtp-activity-panel-header-title">Email Log Settings</h3>
	</div>
	<div class="yay-smtp-activity-panel-content panel-content">
	  <div class="yay-smtp-card-body yay-smtp-mailer-settings">
		<div class="setting-el save-setting-el">
		  <div class="setting-label">
			<label for="yay_smtp_mail_log_setting_save">Save Email Logs</label>
		  </div>
		  <div class="setting-field components-popover__content">
			<label class="switch">
			  <input type="checkbox" id="yay_smtp_mail_log_setting_save" <?php echo 'yes' === $yaySmtpSettingSave ? 'checked' : ''; ?>>
			  <span class="slider round"></span>
			</label>
		  </div>
		</div>
		<div class="setting-el-other-wrap" style="display: <?php echo 'yes' === $yaySmtpSettingSave ? 'block' : 'none'; ?>">
		  <div class="setting-el information-type-el">
			<div class="setting-label">
			  <label for="yay_smtp_mail_log_setting_basic_infomation">Basic/Full Information</label>
			</div>
			<div class="setting-field">
			  <label class="radio-setting">
				<input type="radio" id="yay_smtp_mail_log_setting_basic_infomation" name="information_type" value="basic_inf" <?php echo 'basic_inf' === $yaySmtpSettingInfType ? 'checked' : ''; ?>>
				Basic Information
			  </label>
			  <label class="radio-setting">
				<input type="radio" id="yay_smtp_mail_log_setting_full_infomation" name="information_type" value="full_inf" <?php echo 'full_inf' === $yaySmtpSettingInfType ? 'checked' : ''; ?>>
				Full Information
			  </label>
			</div>
		  </div>
		  <div class="setting-el delete-time-el">
			<div class="setting-label">
			  <label>Save Logs</label>
			</div>
			<div class="setting-field">
			  <select class="yay-smtp-email-log-setting-delete-time">
				<?php
				foreach ( $yaySmtpDaleteTimes as $val => $text ) {
					$selected = '';
					if ( $val == $yaySmtpSettingDeleteDatetime ) {
						$selected = 'selected';
					}
					echo '<option value="' . esc_attr( $val ) . '" ' . esc_attr( $selected ) . '>' . esc_attr( $text ) . '</option>';
				}
				?>
			  </select>
			</div>
		  </div>
		</div>
	  </div>
	  <div>
		<button type="button" class="yay-smtp-button yay-smtp-email-log-settings-save-action">Save Changes</button>
	  </div>
	</div>
  </div>
</div>
