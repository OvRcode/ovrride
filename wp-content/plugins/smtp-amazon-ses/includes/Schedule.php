<?php
namespace YaySMTPAmazonSES;

defined( 'ABSPATH' ) || exit;

use YaySMTPAmazonSES\Helper\Utils;

class Schedule {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private function doHooks() {
		add_filter( 'cron_schedules', array( $this, 'datetime_custom_cron_schedule' ), 5, 1 );
		add_action( YAY_SMTP_AMAZONSES_PREFIX . '_delete_email_log_schedule_hook', array( $this, 'delete_email_log_schedule' ) );

		if ( ! wp_next_scheduled( YAY_SMTP_AMAZONSES_PREFIX . '_delete_email_log_schedule_hook' ) ) {
			$timeSch = YAY_SMTP_AMAZONSES_PREFIX . '_specific_delete_time';
			wp_schedule_event( time(), $timeSch, YAY_SMTP_AMAZONSES_PREFIX . '_delete_email_log_schedule_hook' );
		}
	}

	private function __construct() {}

	public function datetime_custom_cron_schedule( $schedules ) {
		$emailLogSetting       = Utils::getYaySmtpEmailLogSetting();
		$deleteDatetimeSetting = isset( $emailLogSetting ) && isset( $emailLogSetting['email_log_delete_time'] ) ? (int) $emailLogSetting['email_log_delete_time'] : 60;
		if ( 0 != $deleteDatetimeSetting ) {
			$disPlayText           = 'Every ' . $deleteDatetimeSetting . ' days';
			$timeSch               = YAY_SMTP_AMAZONSES_PREFIX . '_specific_delete_time';
			$schedules[ $timeSch ] = array(
				'interval' => 86400 * $deleteDatetimeSetting, // Every 6 hours
				'display'  => __( $disPlayText ),
			);
		}

		return $schedules;
	}

	public function delete_email_log_schedule() {
		global $wpdb;
		$wpdb->query( 'DELETE FROM ' . $wpdb->prefix . 'yay_smtp_amazonses_email_logs' );
	}

}
