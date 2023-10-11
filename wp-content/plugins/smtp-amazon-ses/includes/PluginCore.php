<?php
namespace YaySMTPAmazonSES;

use YaySMTPAmazonSES\Helper\Utils;

defined( 'ABSPATH' ) || exit;

class PluginCore {
	protected static $instance = null;

	public static function getInstance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
			self::$instance->doHooks();
		}
		return self::$instance;
	}

	private function doHooks() {
		$this->getProcessor();
		global $phpmailer;
		$phpmailer = new PhpMailerExtends();
	}

	private function __construct() {}

	public function getProcessor() {
		add_action( 'phpmailer_init', array( $this, 'doSmtperInit' ) );
		add_filter( 'wp_mail_from', array( $this, 'getFromAddress' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'getFromName' ) );
	}

	public function getDefaultMailFrom() {
		$sitename = \wp_parse_url( \network_home_url(), PHP_URL_HOST );
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;

		return $from_email;
	}

	public function getFromAddress( $email ) {
		$emailDefault = $this->getDefaultMailFrom();
		$fromEmail    = Utils::getCurrentFromEmail();
		if ( Utils::getForceFromEmail() == 1 ) {
			return $fromEmail;
		}
		if ( ! empty( $emailDefault ) && $email !== $emailDefault ) {
			return $email;
		}

		return $fromEmail;
	}

	public function getFromName( $name ) {
		$nameDefault   = 'WordPress';
		$forceFromName = Utils::getForceFromName();
		if ( 0 == $forceFromName && $name !== $nameDefault ) {
			return $name;
		}

		return Utils::getCurrentFromName();
	}

	public function doSmtperInit( $obj ) {
		$currentMailer    = Utils::getCurrentMailer();
		$obj->Mailer      = $currentMailer;
		$obj->SMTPSecure  = '';
	}
}
