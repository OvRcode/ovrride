<?php

namespace YaySMTPAmazonSES\Controller;

use YaySMTPAmazonSES\Aws3\Aws\Ses\SesClient;
use YaySMTPAmazonSES\Helper\LogErrors;
use YaySMTPAmazonSES\Helper\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AmazonWebServicesController {

	private $client;

	public function get_region() {
		$region          = 'us-east-1';
		$yaysmtpSettings = Utils::getYaySmtpSetting();
		if ( ! empty( $yaysmtpSettings ) ) {
			if ( ! empty( $yaysmtpSettings['amazonses'] ) && ! empty( $yaysmtpSettings['amazonses']['region'] ) ) {
				$region = $yaysmtpSettings['amazonses']['region'];
			}
		}
		return $region;
	}

	public function get_access_key_id() {
		$accessKeyId     = '';
		$yaysmtpSettings = Utils::getYaySmtpSetting();
		if ( ! empty( $yaysmtpSettings ) ) {
			if ( ! empty( $yaysmtpSettings['amazonses'] ) && ! empty( $yaysmtpSettings['amazonses']['access_key_id'] ) ) {
				$accessKeyId = $yaysmtpSettings['amazonses']['access_key_id'];
			}
		}
		return $accessKeyId;
	}

	public function get_secret_access_key() {
		$secretAccessKey = '';
		$yaysmtpSettings = Utils::getYaySmtpSetting();
		if ( ! empty( $yaysmtpSettings ) ) {
			if ( ! empty( $yaysmtpSettings['amazonses'] ) && ! empty( $yaysmtpSettings['amazonses']['secret_access_key'] ) ) {
				$secretAccessKey = $yaysmtpSettings['amazonses']['secret_access_key'];
			}
		}
		return $secretAccessKey;
	}

	public function getClient() {
		if ( is_null( $this->client ) ) {
			$args = array(
				'credentials' => array(
					'key'    => $this->get_access_key_id(),
					'secret' => $this->get_secret_access_key(),
				),
			);

			$args['version'] = '2010-12-01';
			$args['region']  = $this->get_region();

			try {
				$this->client = new SesClient( $args );
			} catch ( \Exception $e ) {
				LogErrors::clearErr();
				LogErrors::setErr( 'Mailer: Amazon SES' );
				LogErrors::setErr( 'Missing access keys, region.' );
			}
		}

		return $this->client;
	}

}
