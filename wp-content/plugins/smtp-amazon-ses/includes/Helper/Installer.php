<?php
namespace YaySMTPAmazonSES\Helper;

defined( 'ABSPATH' ) || exit;

class Installer {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->setupPages();
		$this->createTables();
	}

	public function setupPages() {

	}

	public function pageExit( $postTitle ) {
		$foundPost = post_exists( $postTitle );
		return $foundPost;
	}

	public function createTables() {
		include_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$this->createYaySMTPEmailLogs();
	}

	public function createYaySMTPEmailLogs() {
		global $wpdb;
		$table = $wpdb->prefix . YAY_SMTP_AMAZONSES_PREFIX . '_email_logs';
		$sql   = "CREATE TABLE $table (
    `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `subject` varchar(1000) DEFAULT NULL,
    `email_from` varchar(300) DEFAULT NULL,
    `email_to` longtext DEFAULT NULL,
    `mailer` varchar(300) DEFAULT NULL,
    `date_time` datetime NOT NULL,
    `status` int(1) DEFAULT NULL,
    `content_type` varchar(300) DEFAULT NULL,
    `body_content` longtext DEFAULT NULL,
    `reason_error` varchar(300) DEFAULT NULL,
    `flag_delete` int(1) DEFAULT 0,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ) != $table ) {
			dbDelta( $sql );
		}
	}
}
