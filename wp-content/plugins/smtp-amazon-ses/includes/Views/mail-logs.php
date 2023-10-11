<?php
use YaySMTPAmazonSES\Helper\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$templatePart = YAY_SMTP_AMAZONSES_PLUGIN_PATH . 'includes/Views/template-part';

$yaySmtpEmailLogSetting = Utils::getYaySmtpEmailLogSetting();
$yaySmtpShowSubjectCol  = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['show_subject_cl'] ) ? (int) $yaySmtpEmailLogSetting['show_subject_cl'] : 1;
$yaySmtpShowToCol       = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['show_to_cl'] ) ? (int) $yaySmtpEmailLogSetting['show_to_cl'] : 1;
$yaySmtpShowStatusCol   = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['show_status_cl'] ) ? (int) $yaySmtpEmailLogSetting['show_status_cl'] : 1;
$yaySmtpShowDatetimeCol = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['show_datetime_cl'] ) ? (int) $yaySmtpEmailLogSetting['show_datetime_cl'] : 1;
$yaySmtpShowActionCol   = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['show_action_cl'] ) ? (int) $yaySmtpEmailLogSetting['show_action_cl'] : 1;

$yaySmtpEmailStatus      = isset( $yaySmtpEmailLogSetting ) && isset( $yaySmtpEmailLogSetting['status'] ) ? $yaySmtpEmailLogSetting['status'] : 'all';
$yayStatusSentChecked    = true;
$yayStatusNotsendChecked = true;
if ( 'not_send' === $yaySmtpEmailStatus ) {
	$yayStatusSentChecked = false;
} elseif ( 'sent' === $yaySmtpEmailStatus ) {
	$yayStatusNotsendChecked = false;
} elseif ( 'empty' === $yaySmtpEmailStatus ) {
	$yayStatusSentChecked    = false;
	$yayStatusNotsendChecked = false;
}

?>

<div class="<?php echo esc_attr( YAY_SMTP_AMAZONSES_PREFIX ); ?> yay-smtp-wrap mail-logs" style="display:none">
  <div class="yay-button-first-header">
	<div class="yay-button-header-child-left">
	  <span class="dashicons dashicons-arrow-left-alt"></span>
	  <span><a class="mail-setting-redirect">Back to Settings page</a></span>
	</div>
	<div class="yay-button-header-child-right">
	  <button class="yay-smtp-button yaysmtp-email-log-settings">Email log settings</a>
	</div>
  </div>

  <!-- Mail log settings drawer - start -->
  <?php Utils::getTemplatePart( $templatePart, 'email-log-settings', array( 'yaySmtpEmailLogSetting' => $yaySmtpEmailLogSetting ) ); ?>
  <!-- Mail log settings drawer  - end -->

  <div class="yay-smtp-card yay-smtp-mail-logs-wrap">
	<div class="yay-smtp-card-header">
	  <div class="yay-smtp-header">
		<div class="yay-smtp-title">
		  <h2>Email Log List</h2>
		</div>
		<div class="yay-button-wrap">
		  <div class="select-control bulk-action-control">
			<button type="button" class="yay-smtp-button delete-selected-button"> Delete Selected</button>
		  </div>
		  <div class="select-control search is-focused is-searchable">
			<div class="components-base-control select-control__control empty">
			  <i class="dashicons dashicons-search material-icons-outlined"></i>
			  <div class="components-base-control__field">
				<input class="select-control__control-input search-imput" type="text" placeholder="Search key as Subject field or To field">
			  </div>
			</div>
		  </div>
		  <div class="components-dropdown">
			<button type="button" title="Choose which values to display" class="components-button components-dropdown-button ellipsis-menu__toggle has-icon">
			  <span class="dashicon dashicons dashicons-ellipsis"></span>
			</button>
			<div class="components-popover components-dropdown__content">
			  <div class="components-popover__content">
				<div class="ellipsis-menu__content">
				  <div class="ellipsis-menu__title">Columns:</div>
				  <div class="components-base-control components-toggle-control">
					<label class="components-base-control__field" for="yaysmtp_logs_subject_control">
					  <div class="switch">
						<input type="checkbox" id="yaysmtp_logs_subject_control" <?php echo 1 == $yaySmtpShowSubjectCol ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>Subject</span>
					  </div>
					</label>
					<label class="components-base-control__field" for="yaysmtp_logs_to_control">
					  <div class="switch" >
						<input type="checkbox" id="yaysmtp_logs_to_control" <?php echo 1 == $yaySmtpShowToCol ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>To</span>
					  </div>
					</label>
					<label class="components-base-control__field" for="yaysmtp_logs_status_control">
					  <div class="switch">
						<input type="checkbox" id="yaysmtp_logs_status_control" <?php echo 1 == $yaySmtpShowStatusCol ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>Status</span>
					  </div>
					</label>
					<label class="components-base-control__field" for="yaysmtp_logs_datetime_control">
					  <div class="switch">
						<input type="checkbox" id="yaysmtp_logs_datetime_control" <?php echo 1 == $yaySmtpShowDatetimeCol ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>Time</span>
					  </div>
					</label>
					<label class="components-base-control__field" for="yaysmtp_logs_action_control">
					  <div class="switch">
						<input type="checkbox" id="yaysmtp_logs_action_control" <?php echo 1 == $yaySmtpShowActionCol ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>Action</span>
					  </div>
					</label>
				  </div>

				  <div class="ellipsis-menu__title">Status:</div>
				  <div class="components-base-control components-toggle-control">
					<label class="components-base-control__field" for="yaysmtp_logs_status_sent">
					  <div class="switch">
						<input type="checkbox" id="yaysmtp_logs_status_sent" <?php echo $yayStatusSentChecked ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>Success</span>
					  </div>
					</label>
					<label class="components-base-control__field" for="yaysmtp_logs_status_not_send">
					  <div class="switch" >
						<input type="checkbox" id="yaysmtp_logs_status_not_send" <?php echo $yayStatusNotsendChecked ? 'checked' : ''; ?>>
						<span class="slider round"></span>
					  </div>
					  <div class="toggle-label">
						<span>Fail</span>
					  </div>
					</label>
				  </div>
				  <div class="components-other-action-control">
					<div class="components-base-control components-toggle-control">
					  <label class="components-base-control__field">
						<div class="">
						  <span class="dashicons dashicons-trash"></span>
						</div>
						<div class="toggle-label">
						  <span class="yay-smtp-delete-all-mail-logs"><?php echo esc_html__( 'Delete All Mail Logs', 'yay-smtp-amazonses' ); ?></span>
						</div>
					  </label>
					</div>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
	</div>
	<div class="yay-smtp-card-body">
	  <div class="yay-smtp-content">
		<div class="components-body">
		  <div class="wrap-table">
			<table>
			  <thead>
				<tr>
				  <th class="table-header is-checkbox-column">
					<div class="components-base-control">
					  <div class="components-base-control__field">
						<span class="checkbox-control-input-container">
						  <input id="input-check-all" class="checkbox-control-input-all checkbox-control-input" type="checkbox" aria-label="Select All">
						</span>
					  </div>
					</div>
				  </th>
				  <th class="table-header is-left-aligned is-sortable subject-col <?php echo 0 == $yaySmtpShowSubjectCol ? 'hiden' : ''; ?>" data-sort-col="subject" data-sort="none"> <!-- none, descending, ascending-->
					<button type="button" class="components-button">
					  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="img" aria-hidden="true" focusable="false">
						  <path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
					  </svg>
					  <span>Subject</span>
					</button>
				  </th>
				  <th class="table-header is-left-aligned is-sortable to-col <?php echo 0 == $yaySmtpShowToCol ? 'hiden' : ''; ?>" data-sort-col="email_to" data-sort="none">
					<button type="button" class="components-button">
					  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="img" aria-hidden="true" focusable="false">
						  <path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
					  </svg>
					  <span>To</span>
					</button>
				  </th>
				  <th class="table-header is-left-aligned is-sortable status-col <?php echo 0 == $yaySmtpShowStatusCol ? 'hiden' : ''; ?>" data-sort-col="status" data-sort="none">
					<button type="button" class="components-button">
					  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="img" aria-hidden="true" focusable="false">
						  <path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
					  </svg>
					  <span>Status</span>
					</button>
				  </th>
				  <th class="table-header is-sortable datetime-col <?php echo 0 == $yaySmtpShowDatetimeCol ? 'hiden' : ''; ?>" data-sort-col="date_time" data-sort="none">
					<button type="button" class="components-button">
					  <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" width="24" height="24" role="img" aria-hidden="true" focusable="false">
						  <path d="M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z"></path>
					  </svg>
					  <span>Time</span>
					</button>
				  </th>
				  <th class="table-header action-col action-col <?php echo 0 == $yaySmtpShowActionCol ? 'hiden' : ''; ?>">
					<span>Action</span>
				  </th>
				</tr>
			  </thead>
			  <tbody class="yaysmtp-body"></tbody>
			</table>
		  </div>
		</div>
		<!-- Mail log detail drawer - start -->
		<?php Utils::getTemplatePart( $templatePart, 'mail-details', array() ); ?>
		<!-- Mail log detail drawer  - end -->
		<div class="components-footer">
		  <div class="pagination">
			<div class="pagination-page-arrows">
			  <span class="pagination-page-arrows-label"></span>
			  <div class="pagination-page-arrows-buttons">
				<button type="button" class="components-button pagination-link previous-btn" aria-label="Previous Page">
				  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" role="img" aria-hidden="true" focusable="false" style="flex:none">
					  <path d="M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z"></path>
				  </svg>
				</button>
				<button type="button" class="components-button pagination-link next-btn" aria-label="Next Page">
				  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" role="img" aria-hidden="true" focusable="false" style="flex:none">
					  <path d="M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z"></path>
				  </svg>
				</button>
			  </div>
			</div>
			<div class="pagination-page-picker">
			  <label class="pagination-page-picker-label">
				Go to page
				<input id="" class="pagination-page-picker-input pag-page-current" aria-invalid="false" type="number" min="1" max="15" value="1">
			  </label>
			</div>
			<div class="pagination-per-page-picker">
			  <select class="components-select-control-input pag-per-page-sel">
				<option value="10">10/page</option>
				<option value="20">20/page</option>
				<option value="30">30/page</option>
				<option value="40">40/page</option>
				<option value="50">50/page</option>
			  </select>
			</div>
		  </div>
		</div>
	  </div>
	</div>
  </div>
</div>





