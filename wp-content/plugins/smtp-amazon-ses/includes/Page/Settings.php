<?php
namespace YaySMTPAmazonSES\Page;

use YaySMTPAmazonSES\Helper\Utils;

defined( 'ABSPATH' ) || exit;

class Settings {
	protected static $instance = null;
	private $hook_suffix;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}

		return self::$instance;
	}

	private $pageId = null;

	private function doHooks() {
		$this->hook_suffix = array( YAY_SMTP_AMAZONSES_PREFIX . '_main_page' );
		add_action( 'admin_menu', array( $this, 'settingsMenu' ) );
		add_filter( 'plugin_action_links_' . YAY_SMTP_AMAZONSES_PLUGIN_BASENAME, array( $this, 'pluginActionLinks' ) );

		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdminScripts' ) );
		}
	}

	private function __construct() {}

	public function settingsMenu() {
		$this->hook_suffix[ YAY_SMTP_AMAZONSES_PREFIX . '_main_page' ] = add_menu_page(
			__( 'YaySMTP for Amazon SES', 'yay-smtp-amazonses' ),
			__( 'YaySMTP', 'yay-smtp-amazonses' ),
			'manage_options',
			'yaysmtp-amazonses',
			array( $this, 'settingsPage' ),
			'dashicons-email'
		);
	}

	public function pluginActionLinks( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=yaysmtp-amazonses' ) . '" aria-label="' . esc_attr__( 'YaySMTP', 'yay-smtp-amazonses' ) . '">' . esc_html__( 'Settings', 'yay-smtp-amazonses' ) . '</a>',
		);
		return array_merge( $action_links, $links );
	}

	public function settingsPage() {
		include_once YAY_SMTP_AMAZONSES_PLUGIN_PATH . 'includes/Views/yay-smtp.php';
	}

	public function enqueueAdminScripts( $screenId ) {
		$scriptId = $this->getPageId();
		wp_enqueue_style( $scriptId, YAY_SMTP_AMAZONSES_PLUGIN_URL . 'assets/css/yay-smtp-admin.css', array(), YAY_SMTP_AMAZONSES_VERSION );
		if ( $screenId == $this->hook_suffix[ YAY_SMTP_AMAZONSES_PREFIX . '_main_page' ] ) {
			$succ_sent_mail_last = 'yes';
			$yaysmtpSettings     = Utils::getYaySmtpSetting();
			if ( ! empty( $yaysmtpSettings ) && isset( $yaysmtpSettings['succ_sent_mail_last'] ) && false === $yaysmtpSettings['succ_sent_mail_last'] ) {
				$succ_sent_mail_last = 'no';
			}

			wp_enqueue_script( $scriptId, YAY_SMTP_AMAZONSES_PLUGIN_URL . 'assets/js/yay-smtp-admin.js', array(), YAY_SMTP_AMAZONSES_VERSION, true );
			$amazonses_settings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
			wp_localize_script(
				$scriptId,
				'yay_smtp_amazonses_wp_data',
				array(
					'YAY_SMTP_PLUGIN_PATH' => YAY_SMTP_AMAZONSES_PLUGIN_PATH,
					'YAY_SMTP_PLUGIN_URL'  => YAY_SMTP_AMAZONSES_PLUGIN_URL,
					'YAY_SMTP_SITE_URL'    => YAY_SMTP_AMAZONSES_SITE_URL,
					'YAY_ADMIN_AJAX'       => admin_url( 'admin-ajax.php' ),
					'ajaxNonce'            => wp_create_nonce( 'ajax-nonce' ),
					'currentMailer'        => Utils::getCurrentMailer(),
					'yaysmtpSettings'      => ( ! empty( $amazonses_settings ) && is_array( $amazonses_settings ) ) ? $amazonses_settings : array(),
					'succ_sent_mail_last'  => $succ_sent_mail_last,
				)
			);
			wp_enqueue_media();
		}
	}

	public function getPageId() {
		if ( null == $this->pageId ) {
			$this->pageId = YAY_SMTP_AMAZONSES_PREFIX . '-settings';
		}
		return $this->pageId;
	}
}
