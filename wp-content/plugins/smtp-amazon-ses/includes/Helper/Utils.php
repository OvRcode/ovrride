<?php
namespace YaySMTPAmazonSES\Helper;

defined( 'ABSPATH' ) || exit;

class Utils {
	public static function getTemplatePart( $templateFolder, $slug = null, array $params = array() ) {
		global $wp_query;
		$_template_file = $templateFolder . "/{$slug}.php";
		if ( is_array( $wp_query->query_vars ) ) {
			extract( $wp_query->query_vars, EXTR_SKIP );
		}
		extract( $params, EXTR_SKIP );
		require $_template_file;
	}

	public static function saniVal( $val ) {
		return sanitize_text_field( $val );
	}

	public static function saniValArray( $array ) {
		$newArray = array();
		foreach ( $array as $key => $val ) { // level 1
			if ( is_array( $val ) ) {
				foreach ( $val as $key_1 => $val_1 ) { // level 2
					if ( is_array( $val_1 ) ) {
						foreach ( $val_1 as $key_2 => $val_2 ) { // level 3
							$newArray[ $key ][ $key_1 ][ $key_2 ] = ( isset( $array[ $key ][ $key_1 ][ $key_2 ] ) ) ? sanitize_text_field( $val_2 ) : '';
						}
					} else {
						$newArray[ $key ][ $key_1 ] = ( isset( $array[ $key ][ $key_1 ] ) ) ? sanitize_text_field( $val_1 ) : '';
					}
				}
			} else {
				$newArray[ $key ] = ( isset( $array[ $key ] ) ) ? sanitize_text_field( $val ) : '';
			}
		}
		return $newArray;
	}

	public static function isJson( $string ) {
		return is_string( $string ) && is_array( json_decode( $string, true ) ) && ( json_last_error() === JSON_ERROR_NONE ) ? true : false;
	}

	public static function checkNonce() {
		$nonce = sanitize_text_field( $_POST['nonce'] );
		if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( array( 'mess' => 'Nonce is invalid' ) );
		}
	}

	public static function getCurrentMailer() {
		$mailer = 'amazonses';
		return $mailer;
	}

	public static function getCurrentFromEmail() {
		$mailer          = get_option( 'admin_email' );
		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) ) {
			if ( ! empty( $yaysmtpSettings['fromEmail'] ) ) {
				$mailer = $yaysmtpSettings['fromEmail'];
			}
		}
		return $mailer;
	}

	public static function getCurrentFromName() {
		$mailer          = get_bloginfo( 'name' );
		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) ) {
			if ( ! empty( $yaysmtpSettings['fromName'] ) ) {
				$mailer = $yaysmtpSettings['fromName'];
			}
		}
		return str_replace( '\\', '', $mailer );
	}

	public static function getForceFromName() {
		$forceFromName   = 0;
		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) ) {
			if ( isset( $yaysmtpSettings['forceFromName'] ) ) {
				$forceFromName = $yaysmtpSettings['forceFromName'];
			}
		}
		return $forceFromName;
	}

	public static function getForceFromEmail() {
		$forceFromEmail  = 1;
		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) ) {
			if ( isset( $yaysmtpSettings['forceFromEmail'] ) ) {
				$forceFromEmail = $yaysmtpSettings['forceFromEmail'];
			}
		}
		return $forceFromEmail;
	}

	public static function getAdminEmail() {
		return get_option( 'admin_email' );
	}

	public static function getAdminFromName() {
		return get_bloginfo( 'name' );
	}

	public static function getAllMailerSetting() {
		return array(
			'amazonses' => array( 'region', 'access_key_id', 'secret_access_key' ),
		);
	}

	public static function isMailerComplete() {
		$isComplete    = true;
		$currentMailer = self::getCurrentMailer();
		if ( 'mail' === $currentMailer ) {
			return true;
		}

		$mailerSettingAll = self::getAllMailerSetting();

		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) && ! empty( $mailerSettingAll[ $currentMailer ] ) ) {
			$settingArrRequireds = $mailerSettingAll[ $currentMailer ];
			if ( ! empty( $yaysmtpSettings[ $currentMailer ] ) ) {
				foreach ( $settingArrRequireds as $setting ) {
					if ( empty( $yaysmtpSettings[ $currentMailer ][ $setting ] ) ) {
						$isComplete = false;
					}
				}
			}
		}
		return $isComplete;
	}

	/** ----------------------------------- Auth - start -----------------------*/

	public static function getYaySmtpSetting() {
		$rst             = array();
		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) ) {
			$rst = $yaysmtpSettings;
		}
		return $rst;
	}

	public static function setYaySmtpSetting( $key, $value = '', $mailer = '' ) {
		if ( empty( $mailer ) && ! empty( $key ) ) { // Update: fromEmail / fromName / currentMailer. Ex: ['fromEmail' => 'admin']
			$setting         = self::getYaySmtpSetting();
			$setting[ $key ] = $value;
			update_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings', $setting );
		} elseif ( ! empty( $mailer ) && ! empty( $key ) ) { // Update settings of mailer. Ex: ['sendgrid' => ['api_key' => '123abc']]
			$setting                    = self::getYaySmtpSetting();
			$setting[ $mailer ][ $key ] = $value;
			update_option( YAY_SMTP_AMAZONSES_PREFIX . '_settings', $setting );
		}
	}

	public static function getYaySmtpEmailLogSetting() {
		$rst             = array();
		$yaysmtpSettings = get_option( YAY_SMTP_AMAZONSES_PREFIX . '_email_log_settings' );
		if ( ! empty( $yaysmtpSettings ) && is_array( $yaysmtpSettings ) ) {
			$rst = $yaysmtpSettings;
		}
		return $rst;
	}

	public static function setYaySmtpEmailLogSetting( $key, $value = '' ) {
		if ( ! empty( $key ) ) {
			$setting         = self::getYaySmtpEmailLogSetting();
			$setting[ $key ] = $value;
			update_option( YAY_SMTP_AMAZONSES_PREFIX . '_email_log_settings', $setting );
		}
	}

	public static function getAdminPageUrl( $page = '' ) {
		if ( empty( $page ) ) {
			$page = YAY_SMTP_AMAZONSES_PREFIX;
		}

		return add_query_arg(
			'page',
			$page,
			self::adminUrl( 'admin.php' )
		);
	}

	public static function adminUrl( $path = '', $scheme = 'admin' ) {
		return \admin_url( $path, $scheme );
	}

	/** ----------------------------------- Auth - end -----------------------*/

	public static function encrypt( $string, $class = '' ) {
		return base64_encode( $string . '-' . substr( sha1( $class . $string . 'yay_smtp123098' ), 0, 6 ) );
	}

	public static function decrypt( $string, $class = '' ) {
		$parts = explode( '-', base64_decode( $string ) );
		if ( count( $parts ) != 2 ) {
			return 0;
		}

		$string = $parts[0];
		return substr( sha1( $class . $string . 'yay_smtp123098' ), 0, 6 ) === $parts[1] ? $string : 0;
	}

	public static function insertEmailLogs( $data ) {
		$emailLogSetting = self::getYaySmtpEmailLogSetting();
		$saveSetting     = isset( $emailLogSetting ) && isset( $emailLogSetting['save_email_log'] ) ? $emailLogSetting['save_email_log'] : 'yes';
		$infTypeSetting  = isset( $emailLogSetting ) && isset( $emailLogSetting['email_log_inf_type'] ) ? $emailLogSetting['email_log_inf_type'] : 'full_inf';

		if ( 'yes' === $saveSetting && ! empty( $data ) && is_array( $data['email_to'] ) ) {
			global $wpdb;
			$tableName = $wpdb->prefix . YAY_SMTP_AMAZONSES_PREFIX . '_email_logs';
			$content   = array(
				'subject'    => $data['subject'],
				'email_from' => $data['email_from'],
				'email_to'   => maybe_serialize( $data['email_to'] ),
				'mailer'     => $data['mailer'],
				'date_time'  => $data['date_time'],
				'status'     => $data['status'],
			);

			if ( ! empty( $data['reason_error'] ) ) {
				$content['reason_error'] = $data['reason_error'];
			}

			if ( 'basic_inf' !== $infTypeSetting ) {
				$content['content_type'] = $data['content_type'];
				$content['body_content'] = maybe_serialize( $data['body_content'] );
			}

			$wpdb->insert( $tableName, $content );
		}
	}
}
