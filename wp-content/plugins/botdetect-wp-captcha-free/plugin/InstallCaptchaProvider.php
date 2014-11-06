<?php
class BDWP_InstallCaptchaProvider {

	public static function InitDiagnostics() {
		return array(
			'database_version' => BDWP_PluginInfo::GetVersion(),
			'first_plugin_install' => array(
				'datetime' => '',
				'plugin_version' => '',
				'wp_version' => ''
			),
			'last_plugin_install' => array(
				'datetime' => '',
				'plugin_version' => '',
				'wp_version' => ''
			),
			'first_bdphplib_install' => array(
				'datetime' => '',
				'bdphplib_version' => '',
				'bdphplib_is_free' => true,
				'plugin_version' => '',
				'wp_version' => ''
			),
			'last_bdphplib_install' => array(
				'datetime' => '',
				'bdphplib_version' => '',
				'bdphplib_is_free' => true,
				'plugin_version' => '',
				'wp_version' => ''
			)
		);
	}

	public static function InitSettings() {
		return array(
			'bdwp_instance_id' => BDWP_Tools::GenerateGuid(),
			'install_lib_automatically_on_plugin_update' => null,
			'customer_email' => '',
			'captcha_provider' => 'bdphplib'
		);
	}

	public static function AddDiagnosticsPluginInstall() {

		$bdwp_diagnostics = get_option('bdwp_diagnostics');
		if (!is_array($bdwp_diagnostics)) {
			$bdwp_diagnostics = self::InitDiagnostics();
		}

		$last_plugin_install = array(
			'datetime' => current_time('mysql'),
			'plugin_version' => BDWP_PluginInfo::GetVersion(),
			'wp_version' => BDWP_WordPress::GetWordPressVersion()
		);

		if (empty($bdwp_diagnostics['first_plugin_install']['plugin_version'])) {
			$bdwp_diagnostics['first_plugin_install'] = $last_plugin_install;
		}

		$bdwp_diagnostics['last_plugin_install'] = $last_plugin_install;
		update_option('bdwp_diagnostics', $bdwp_diagnostics);
	}

	public static function AddDiagnosticsBDPHPLibInstall() {

		$bdwp_diagnostics = get_option('bdwp_diagnostics');
		if (!is_array($bdwp_diagnostics)) return;

		$bdphplib_info = Captcha::GetProductInfo();
			
		$last_bdphplib_install = array(
			'datetime' => current_time('mysql'),
			'bdphplib_version' => $bdphplib_info['version'],
			'bdphplib_is_free' => Captcha::IsFree(),
			'plugin_version' => BDWP_PluginInfo::GetVersion(),
			'wp_version' => BDWP_WordPress::GetWordPressVersion()
		);

		if (empty($bdwp_diagnostics['first_bdphplib_install']['bdphplib_version'])) {
			$bdwp_diagnostics['first_bdphplib_install'] = $last_bdphplib_install;
		}

		$bdwp_diagnostics['last_bdphplib_install'] = $last_bdphplib_install;
		update_option('bdwp_diagnostics', $bdwp_diagnostics);
	}

	/**
	 * Starting register user when press button
	 */
	public static function StartingRegisterUser() {
		$bdwp_workflow = array('bdphplib_is_installing' => true);
		update_option('bdwp_workflow', $bdwp_workflow);
	}

	public static function RegisterUserEnded() {
		update_option('bdwp_workflow', '');
	}

	/**
     *  Add bdwp_settings (generate guid) when first install plugin
     */
	public static function AddBDWPSettings() {
		$bdwp_settings = get_option('bdwp_settings');
		if (!is_array($bdwp_settings)) {
			$bdwp_settings = self::InitSettings();
			update_option('bdwp_settings', $bdwp_settings);
		}
	}

	public static function GetCustomerEmail() {
		$bdwp_settings = get_option('bdwp_settings');
        $customerEmail = (is_array($bdwp_settings))? $bdwp_settings['customer_email'] : '';
        return $customerEmail;
	}

	public static function IsRegsiterUser() {
		$email = self::GetCustomerEmail();
		return (!empty($email))? true : false;
	}

	/**
     *  Email store on client's WordPress database
     */
    public static function SaveCustomerEmail($p_Email) {
        $bdwp_settings = get_option('bdwp_settings');
        if (!is_array($bdwp_settings)) {
        	$bdwp_settings = self::InitSettings();
        }
        
        $bdwp_settings['customer_email'] = $p_Email;
        update_option('bdwp_settings', $bdwp_settings);
    }

    /**
	 * Check the BotDetect Captcha Library is installed
	 */
	public static function LibraryIsInstalled() {
		$generator = 'library';
		$bdwp_options = get_option('botdetect_options');
		if (is_array($bdwp_options) && array_key_exists('generator', $bdwp_options)) {
			$generator = $bdwp_options['generator'];
		}

		return ($generator == 'library' && !class_exists('LBD_CaptchaBase'))? false : true;
	}

	public static function DeleteFile($p_FilePath) {
        if (!empty($p_FilePath)) { return @unlink($p_FilePath); }
        return false;
    }

	/**
	 * Hidden notice captcha library after installation
	 */
	public static function HiddenNoticeCaptchaLibrary() {
		echo '<script> $(document).ready(function(){ document.getElementById("notice-captcha-library").style.cssText= "display:none!important;"  }); </script>';
	}
}
