<?php
class WP_BotDetect_Plugin {
	public static $m_Instance;
	public $m_PluginInfo= array();
	public $m_Options = array();

	/**
	 * Init & setup hooks
	 */
	public function __construct($p_Options, $p_PluginInfo) {
		self::$m_Instance = $this;

		// OPTIONS & Plugin info
		$this->m_Options = $p_Options;
		$this->m_PluginInfo = $p_PluginInfo;

		register_activation_hook($p_PluginInfo['plugin_path'], array($this, 'AddDefaults'));
		register_uninstall_hook($p_PluginInfo['plugin_path'], array('WP_Botdetect_Plugin', 'DeleteOptions'));

		$this->Hook('admin_init', 'BDWP_WordPress', 'MinimalRequiredVersion');
		$this->Hook('init', $this, 'InitSessions');

		// Localized
		BDWP_Localization::Init();

		$bdwp_login = new BDWP_Login();
		$this->Hook('wp_logout', $bdwp_login, 'LoginReset');

		$this->Hook('admin_menu', $this, 'AddOptionsPage');
		$this->Hook('admin_init', $this, 'RegisterSetting');
		
		if ($this->IsBDWPSettingsPage()) {
			$this->Hook('admin_init', 'BDWP_BackwardCompatibility', 'ResolveBackwardCompatibility');
			$this->Hook('admin_print_styles', $this, 'RegisterUserStylesheet');
			$this->Hook('admin_footer', $this, 'SettingsPageScripts');
			$this->Hook('admin_init', $this, 'AddIntegrationOptions');
		}

		// Automatically redirect to settings page after activate
		$this->Hook('admin_init', $this, 'RedirectToSettingsPage');

		// Show update message when detect the new version of BDWP plugin
		$this->DetectNewVersion();

		add_filter('plugin_action_links', array($this, 'PluginActionLinks'), 10, 2);

		// GENERATOR NOTICES
		if (!$this->CheckUpgrade()) {
			$this->hook('admin_notices', $this, 'UpgradeInstructions');
			return;
		}
		
		if (!BDWP_InstallCaptchaProvider::LibraryIsInstalled() || !BDWP_InstallCaptchaProvider::IsRegisteredUser()) {
			$this->hook('admin_notices', $this, 'RegisterUserMissingNotice');
			return;
		}

		if ($this->m_Options['generator'] == 'service') {
			$this->Hook('admin_notices', $this, 'CaptchaServiceNotice');
			return;
		}

		$this->Hook('init', $this, 'RegisterScripts');

		// USE ON
		if ($this->m_Options['on_login']) {
			$this->Hook('login_head', $bdwp_login, 'LoginHead');
			$this->Hook('login_form', $bdwp_login, 'LoginForm');
			$this->Hook('authenticate', $bdwp_login, 'LoginValidate', 1);
		}

		if ($this->m_Options['on_comments']) {
			$bdwp_comments = new BDWP_Comments();
			$this->Hook('wp_enqueue_scripts', $bdwp_comments, 'CommentHead');
			$this->Hook('comment_form_after_fields', $bdwp_comments, 'CommentForm');
			$this->Hook('comment_form_logged_in_after', $bdwp_comments, 'CommentForm');
			$this->Hook('pre_comment_on_post', $bdwp_comments, 'CommentValidate', 1);
			$this->Hook('comment_post', $bdwp_comments, 'CommentReset');
		}

		if ($this->m_Options['on_lost_password']) {
			$bdwp_lostpassword = new BDWP_LostPassword();
			$this->Hook('login_head', $bdwp_login, 'LoginHead');
			$this->Hook('lostpassword_form', $bdwp_lostpassword, 'LostPasswordForm');
			$this->Hook('lostpassword_post', $bdwp_lostpassword, 'LostPasswordValidate');
		}

		if ($this->m_Options['on_registration']) {
			$bdwp_register = new BDWP_Register();
			$this->Hook('login_head', $bdwp_login, 'LoginHead');
			$this->Hook('register_form', $bdwp_register, 'RegisterForm');
			$this->Hook('registration_errors', $bdwp_register, 'RegisterValidation');
		}
	}

	public function InitSessions() {
		if (!session_id()) {
			session_start();
		}
	}

	public function RegisterScripts() {
		wp_register_style( 'botdetect-captcha-style', CaptchaUrls::LayoutStylesheetUrl());
	}

	/**
	 * Check upgrade from bdwp 3.0.beta3.3 -> bdwp free 3.0.0.0+ (overwrite files)
	 */
	public function CheckUpgrade() {
		$pluginFolder = dirname($this->m_PluginInfo['plugin_basename']);
		return ($pluginFolder != 'botdetect-wp-captcha')? true : false;
	}

	public function UpgradeInstructions() {
		echo '<div class="error"><p>' . __( 'When upgrading from BotDetect WP CAPTCHA Plugin v3.0.Beta3.3 or earlier to v3.0.Beta3.4 or higher, you should follow this procedure:<br><br>1) delete the BotDetect WordPress CAPTCHA Plugin (Deactivate/Delete)<br>2) install the BotDetect WordPress CAPTCHA Plugin by using the \'Add New/Upload Plugin\'<br><br>Please note this is an one time procedure. Further upgrades will be one-click procedure.', 'botdetect-wp-captcha') . '</p></div>';
	}

	/**
	 * Admin notices
	 */
	public function RegisterUserMissingNotice() {
        if ($this->IsBDWPSettingsPage()) {
            echo '<div class="error" id="notice-captcha-library"><p>' . sprintf(__( '<strong>You are almost done!</strong> BotDetect WordPress Captcha Plugin requires you to register.', 'botdetect-wp-captcha'), BDWP_HttpHelpers::GetProtocol(), BDWP_PluginInfo::GetVersion()) .'</p></div>';
        } else {
	  		echo '<div class="error" id="notice-captcha-library"><p>' . sprintf(__( '<strong>You are almost done!</strong> BotDetect WordPress Captcha Plugin requires you to register. Please go to the <a href="%s">plugin settings</a> to do it.', 'botdetect-wp-captcha'), admin_url('options-general.php?page='.plugin_basename(__FILE__))) . '</p></div>';
	  	}
	}

	public function CaptchaServiceNotice() {
	  	echo '<div class="updated"><p>' . __( 'The BotDetect Captcha service is currently in a closed Alpha testing phase. Please contact us if you wish to participate in testing.', 'botdetect-wp-captcha') . '</p></div>';
	}

	/**
	 * Add defaults on plugin activation
	 */
	public function AddDefaults() {

		$tmp = get_option('botdetect_options');
		if(!is_array($tmp)) {
			delete_option('botdetect_options');
			update_option('botdetect_options', $this->m_Options);
		}

		// Add bdwp_settings (generate guid)
		BDWP_InstallCaptchaProvider::AddBDWPSettings();

		// Add bdwp_diagnostics plugin install
		BDWP_InstallCaptchaProvider::AddDiagnosticsPluginInstall();

		add_option('bdwp_do_activation_redirect', true);
	}

	/**
	 * Delete options on deactivation
	 */
	public static function DeleteOptions() {
		delete_option('botdetect_options');
		delete_option('bdwp_diagnostics');
		delete_option('bdwp_settings');
		delete_option('bdwp_workflow');
		delete_option('bdwp_integration_wp_login');
		delete_option('bdwp_integration_wp_register');
		delete_option('bdwp_integration_wp_comments');
		delete_option('bdwp_integration_wp_lostpassword');
	}

	/**
	 * Add options page
	 */
	public function AddOptionsPage() {
		add_options_page('BotDetect CAPTCHA WordPress Plugin Settings', 'BotDetect CAPTCHA', 'manage_options', __FILE__, array($this,'RenderOptionsPage'));
	}

	public function PluginActionLinks($p_Links, $p_File) {
	
		if ($p_File == $this->m_PluginInfo['plugin_basename']) {
			$action_link = '<a href="'.get_admin_url().'options-general.php?page='.plugin_basename( __FILE__ ).'">'.__('Settings', 'botdetect-wp-captcha').'</a>';
			// make the 'Settings' link appear first
			array_unshift( $p_Links, $action_link );
		}
		return $p_Links;
	}

	public function RegisterSetting() {
		register_setting( 'botdetect_plugin_options', 'botdetect_options', array($this,'ValidateOptions'));
	}

	/**
	 * Sanitize & Validate
	 */
	public function ValidateOptions($p_Input) {
		 // strip html from textboxes
		$p_Input['image_width'] =  absint(wp_filter_nohtml_kses($p_Input['image_width'])) ;
		$p_Input['image_height'] =  absint(wp_filter_nohtml_kses($p_Input['image_height']));
		$p_Input['min_code_length'] =  absint(wp_filter_nohtml_kses($p_Input['min_code_length']));
		$p_Input['max_code_length'] =  absint(wp_filter_nohtml_kses($p_Input['max_code_length']));

		$p_Input['library_path'] =  trailingslashit($p_Input['library_path']);
		$p_Input['library_assets_url'] =  trailingslashit(wp_filter_nohtml_kses($p_Input['library_assets_url']));

		$p_Input['on_login'] =  (empty($p_Input['on_login']))? false : true;
		$p_Input['on_comments'] =  (empty($p_Input['on_comments']))? false : true;
		$p_Input['on_lost_password'] =  (empty($p_Input['on_lost_password']))? false : true;
		$p_Input['on_registration'] =  (empty($p_Input['on_registration']))? false : true;
		$p_Input['audio'] =  (empty($p_Input['audio']))? false : true;

		$p_Input['helplink'] =  ($p_Input['helplink'] == 'image' || $p_Input['helplink'] == 'text' || $p_Input['helplink'] == 'off')? $p_Input['helplink'] : 'image';

		$p_Input['chk_default_options_db'] =  (empty($p_Input['chk_default_options_db']))? false : true;

		return $p_Input;
	}
	
	/**
	 *  Current page is BDWP Settings page
	 */
	public function IsBDWPSettingsPage() {
		$current_page = (isset($_REQUEST['page']))? str_replace('.php','',$_REQUEST['page']) : '';
		$settings_page = str_replace('.php', '', plugin_basename(__FILE__));
        return ($current_page == $settings_page)? true : false;
    }

	/**
	 *  Redirect to BDWP settings after plugin activation
	 */
	public function RedirectToSettingsPage() {
		if (get_option('bdwp_do_activation_redirect', false)) {
	        delete_option('bdwp_do_activation_redirect');
	        wp_redirect(admin_url('options-general.php?page=' . plugin_basename(__FILE__)));
	    }
	}

    public function AddIntegrationOptions() {
    	update_option('bdwp_integration_wp_login', $this->m_Options);
        update_option('bdwp_integration_wp_register', $this->m_Options);
        update_option('bdwp_integration_wp_comments', $this->m_Options);
        update_option('bdwp_integration_wp_lostpassword', $this->m_Options);
    }

	/**
	 * Output the options page & form HTML
	 */
	public function RenderOptionsPage() {
		$settings_page = new BDWP_Settings($this->m_Options);
		$settings_page->RenderOptions();
	}

	public function SettingsPageScripts() {
		wp_enqueue_script( 'bdwp-settings-validation', plugin_dir_url(__FILE__) . 'public/js/bdwp_settings_validation.js' );
		wp_enqueue_script( 'bdwp-register-user-progress', plugin_dir_url(__FILE__) . 'public/js/captcha_provider_register_user_progress.js' );
	}

	public function RegisterUserStylesheet() {
        wp_enqueue_style( 'bdwp-register-user-stylesheet', plugin_dir_url(__FILE__) . 'public/css/style.css' );
    }

    public function ShowUpdateMessage($p_PluginData, $p_R) {
		echo '<p style="color: red">After updating please just open plugin settings, and the required changes will be applied automatically.</p>';
	}

    /** 
	 * Detect the new version of BotDetect WP plugin
	 */
	public function DetectNewVersion() {
		global $pagenow;
		if ($pagenow === 'plugins.php') {
			$folder = plugin_basename(BDWP_PLUGIN_PATH);
		    $file = basename($this->m_PluginInfo['plugin_path']);
		    $hook = "in_plugin_update_message-{$folder}/{$file}";
		    add_action($hook, array($this,'ShowUpdateMessage'), 10, 2);
		}
	}

	/**
	 * Add action helper
	 */
	public function Hook($p_Hook) {
		$priority = 10;
		$method = $this->sanitizeMethod($p_Hook);
		$additional_args = func_get_args();

		$object = $additional_args[1];

		unset($additional_args[0]);
		unset($additional_args[1]);

		// set priority
		foreach ((array)$additional_args as $a) {
			if (is_int($a)) {
				$priority = $a;
			} else {
				$method = $a;
			}
		}
		return add_action($p_Hook, array($object, $method), $priority, 999);
	}

	/**
	 * Sanitize hooks
	 */
	private function sanitizeMethod($p_Method) {
		return str_replace(array('.','-'), array('_DOT_','_DASH_'), $p_Method);
	}
}
