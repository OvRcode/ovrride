<?php
class BDWP_Settings {

	private $m_BotDetectOptions;
	private $m_IsRegisteredUser = false;

	public function __construct($p_Options) {
		$this->m_BotDetectOptions = $p_Options;
		$this->m_IsRegisteredUser = BDWP_InstallCaptchaProvider::IsRegisteredUser();
	}

	public function RenderOptions() {
		?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php printf(__('BotDetect CAPTCHA WordPress Plugin (%s) -- %s', 'botdetect-wp-captcha'), BDWP_PluginInfo::GetVersion(), BDWP_PluginInfo::License());?></h2>
			<p></p>
			
      		<p id="lblMessageStatus"></p>
      		<p class="botdetect-license"><?php _e('The BotDetect Captcha WordPress Plugin is released under the \'BotDetect Captcha WordPress Plugin -- FREE\' license.<br>The Plugin is packaged with and dependent of the BotDetect PHP CAPTCHA library which is licensed under the BotDetect PHP CAPTCHA 3.0 Beta3 End User License Agreement.<br>In order to use the Plugin you have to accept the both licenses.', 'botdetect-wp-captcha'); ?></p>

      		<?php 
      			// Add register user new
      			$this->RegisterUserView(); 

      			// Add Captcha options
      			$this->CaptchaOptions($this->m_BotDetectOptions);
      		?>
        </div><!-- end: .wrap -->
		<?php
	}

	public function IntegrationOptions($p_BotDetectOptions) {
		$options = $p_BotDetectOptions;
		?>
		<tr valign="top">
			<th scope="row"><?php _e('Use BotDetect CAPTCHA with', 'botdetect-wp-captcha'); ?></th>
			<td>
				<label><input name="botdetect_options[on_login]" type="checkbox" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="true" <?php if (isset($options['on_login'])) { checked($options['on_login'], true); } ?> /> <?php _e('Login', 'botdetect-wp-captcha'); ?> </label><br />
				<label><input name="botdetect_options[on_registration]" type="checkbox" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="true" <?php if (isset($options['on_registration'])) { checked($options['on_registration'], true); } ?> /> <?php _e('User Registration', 'botdetect-wp-captcha'); ?> </label><br />
				<label><input name="botdetect_options[on_lost_password]" type="checkbox" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="true" <?php if (isset($options['on_lost_password'])) { checked($options['on_lost_password'], true); } ?> /> <?php _e('Lost Password', 'botdetect-wp-captcha'); ?> </label><br />
				<label><input name="botdetect_options[on_comments]" type="checkbox" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="true" <?php if (isset($options['on_comments'])) { checked($options['on_comments'], true); } ?> /> <?php _e('Wordpress Comments', 'botdetect-wp-captcha'); ?> </label><br />
			</td>
		</tr>
		<?php
    }

    public function CaptchaOptions($p_BotDetectOptions) {
		?>
		<form method="post" action="options.php">
			<?php settings_fields('botdetect_plugin_options'); ?>
			<?php $options = $p_BotDetectOptions; ?>

			<input type="hidden" name="botdetect_options[library_path]" value="<?php echo (isset($options['library_path']))? $options['library_path'] : ''; ?>" />
			<input type="hidden" name="botdetect_options[library_assets_url]" value="<?php echo (isset($options['library_assets_url']))? $options['library_assets_url'] : ''; ?>" />
    
			<table class="form-table">

		        <tr valign="top" >            
					<th scope="row"><h3><?php _e('Plugin settings', 'botdetect-wp-captcha'); ?></h3></th>
					<td></td>
				</tr>
				
				<?php 
					// Add integration options
					$this->IntegrationOptions($p_BotDetectOptions);
				?>

				<tr>
					<th scope="row"><?php _e('Captcha image width', 'botdetect-wp-captcha'); ?></th>
					<td>
						<input type="text" size="3" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> name="botdetect_options[image_width]" value="<?php echo (isset($options['image_width']))? $options['image_width'] : ''; ?>" />
						<span style="color:#666666;">px</span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Captcha image height', 'botdetect-wp-captcha'); ?></th>
					<td>
						<input type="text" size="3" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> name="botdetect_options[image_height]" value="<?php echo (isset($options['image_height']))? $options['image_height'] : ''; ?>" />
						<span style="color:#666666;">px</span>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Number of characters', 'botdetect-wp-captcha'); ?></th>
					<td>
						<input type="text" size="3" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> id="min_code_length" name="botdetect_options[min_code_length]" value="<?php echo (isset($options['min_code_length']))? $options['min_code_length'] : ''; ?>" /> &ndash;
                        <input type="text" size="3" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> id="max_code_length" name="botdetect_options[max_code_length]" value="<?php echo (isset($options['max_code_length']))? $options['max_code_length'] : ''; ?>" />
					</td>
				</tr>

				<tr>
					<th scope="row"><?php _e('Sound', 'botdetect-wp-captcha'); ?></th>
					<td>
						<label><input name="botdetect_options[audio]" type="checkbox" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="true" <?php if (isset($options['audio'])) { checked($options['audio'], true); } ?> /> <?php _e('Enable audio Captcha', 'botdetect-wp-captcha'); ?></label>
					</td>
				</tr>
      
      <?php
        $isFree = false; 
        if (class_exists('Captcha') && Captcha::IsFree()) $isFree = true; 
      ?>
				<tr>
                    <th scope="row"><?php _e('Remote Include', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <label><input name="botdetect_options[remote]" type="checkbox" value="true" 
						<?php if ((!class_exists('Captcha') && !$isFree) || $isFree) echo "disabled"; ?> <?php if (isset($options['remote'])) { checked($options['remote'], true); } ?> />
							<?php _e('Enable Remote Include -- used for statistics collection and proof-of-work confirmation (still work in progress)','botdetect-wp-captcha'); ?>
                            <br>
                            <?php _e('<i>Switching off is disabled with the Free version of BotDetect.', 'botdetect-wp-captcha'); ?> </label>
                    </td>
                </tr>
      
				<tr valign="top">
                    <th scope="row"><?php _e('Help link', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <label><input name="botdetect_options[helplink]" type="radio" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="image" <?php checked($options['helplink'], 'image'); ?> /> <?php _e('Image', 'botdetect-wp-captcha'); ?> <span style="color:#666666;margin-left:42px;"><?php _e('Clicking the Captcha image opens the help page in a new browser tab.', 'botdetect-wp-captcha'); ?></span></label><br />
                        <label><input name="botdetect_options[helplink]" type="radio" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="text" <?php checked($options['helplink'], 'text'); ?> /> <?php _e('Text', 'botdetect-wp-captcha'); ?> <span style="color:#666666;margin-left:56px;"><?php _e('A text link to the help page is rendered in the bottom 10 px of the Captcha image.', 'botdetect-wp-captcha'); ?></span></label><br />
                        <label><input name="botdetect_options[helplink]"
                                <?php if ((!class_exists('Captcha') && !$isFree) || $isFree) echo "disabled"; ?>
                                      type="radio" value="off" <?php checked($options['helplink'], 'off'); ?> /> <?php _e('Off', 'botdetect-wp-captcha'); ?> <span style="color:#666666;margin-left:63px;">
          <?php if ($isFree) { ?>
          <?php _e('<i>Not available with the Free version of BotDetect.', 'botdetect-wp-captcha'); ?> </span></label><br />
                        <?php } else { ?>
                            <?php _e('Help link is disabled.', 'botdetect-wp-captcha'); ?></span></label><br />
                        <?php } ?>
                    </td>
                </tr>

				<tr>
					<td colspan = "2">
						<p><?php printf(__('Additionally: Please note almost everything is customizable by editing BotDetect\'s <a href="%scaptcha.com/doc/php/howto/captcha-configuration.html?utm_source=plugin&amp;utm_medium=wp&amp;utm_campaign=%s" target="_blank">configuration file</a>.', 'botdetect-wp-captcha'), BDWP_HttpHelpers::GetProtocol(), BDWP_PluginInfo::GetVersion()); ?></p>
					</td>
				</tr>

				<tr><td colspan="2"><div style="margin-top:10px; border-top:#dddddd 1px solid;"></div></td></tr>
				<tr valign="top">
					<th scope="row"><?php _e('Misc Options', 'botdetect-wp-captcha'); ?></th>
					<td>
						<label><input name="botdetect_options[chk_default_options_db]" type="checkbox" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> value="true" <?php if (isset($options['chk_default_options_db'])) { checked($options['chk_default_options_db'], true); } ?> /> <?php _e(' Reset plugin settings to default values on \'Save Changes\'.', 'botdetect-wp-captcha'); ?></label>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" <?php echo (!$this->m_IsRegisteredUser)? 'disabled' : '' ?> class="button-primary" id="btnBDSettingsSaveChanges" value="<?php _e('Save Changes', 'botdetect-wp-captcha') ?>" />
			</p>
		</form>
		<?php
	}

	public function RegisterUserProgress() {
		
		$registerStatus = $registerMessage = '';

		// Process when submit register button
		if (isset($_REQUEST['btnRegisterUser'])) {

    		require_once(BDWP_INCLUDE_PATH . 'RegisterUserProvider.php');

      		// Starting register user
      		BDWP_InstallCaptchaProvider::StartingRegisterUser();

      		$customerEmail = wp_filter_nohtml_kses($_REQUEST['customerEmail']);

      		$registerData = array(
      			'customer_email' => $customerEmail,
      			'relay_url' => 'http://captcha.com/forms/integration/relay.php',
      			'plugin_version' => BDWP_PluginInfo::GetVersion()
      		);

      		$registerObj = new BDWP_RegisterUserProvider($registerData);
      		$result = $registerObj->DoRegisterUser();

      		$registerStatus = $result['register_status'];

      		if ($registerStatus == 'OK') {
      			BDWP_InstallCaptchaProvider::SaveCustomerEmail($customerEmail);
      			BDWP_InstallCaptchaProvider::HiddenNoticeCaptchaLibrary();
      			$this->m_IsRegisteredUser = true;

      			if (!$this->DetectIEBrowser()) {
                    add_action('admin_footer', array($this, 'BDWPCaptchaImageRenderCheckScripts'));
            	} else {
            		BDWP_InstallCaptchaProvider::RegisterUserEnded();
            	}

      		} else  {
      			BDWP_InstallCaptchaProvider::RegisterUserEnded();
      			$registerMessage = $result['register_message'];
      		}
    	}

    	return array(
      		'register_status' => $registerStatus,
      		'register_message' => $registerMessage
      	);
	}

	public function RegisterUserView() {

		$result = $this->RegisterUserProgress();
		$registerStatus = $result['register_status'];
		$registerMessage = $result['register_message'];
	?>
        <div id="bdwp-wrap-register-user" class="<?php echo ($this->m_IsRegisteredUser)? 'bdwp-hidden-install-form' : ''?>">

        	<form action="" method="post">
        		<input type="hidden" id="BDUrlCaptchaImage" value="<?php echo network_site_url('/');?>index.php?botdetect_request=1&get=image&c=login_captcha&t=8c5676e633435690f683ddab36a9efa4" >
        		<input type="hidden" id="BDPluginFolder" value="<?php echo plugin_dir_url( __FILE__ );?>">
        		<input type="hidden" id="BDOptions" value='<?php echo json_encode($this->m_BotDetectOptions);?>'>
        		<input type="hidden" id="BDMsgImageRenderError" value="<?php _e('An error occurred while generating the Captcha image. Captcha validation has been disabled in login forms to avoid locking all users out of the website.','botdetect-wp-captcha');?>">
        		<input type="hidden" id="BDMsgDisableSuspiciousQueryStrings" value="<?php _e('An error occurred while generating the Captcha image. Captcha validation has been disabled in login forms to avoid locking all users out of the website. Please turn off the \'Filter Suspicious Query Strings in the URL\' setting in iThemes Sercurity plugin settings.','botdetect-wp-captcha');?>">
        		<input type="hidden" id="BDMsgSessionIsDisabled" value="<?php _e('PHP Sessions are disabled on your server, and Captcha validation in any form cannot work until you (or your administrator) enable them. Captcha validation has been disabled in login forms to avoid locking all users out of the website.','botdetect-wp-captcha');?>">
        		<input type="hidden" id="BDMsgLoadingRenderCheck" value="<?php _e('Cheking render captcha image...', 'botdetect-wp-captcha');?>">
        		<input type="hidden" id="BDMsgWorkingRegisterUser" value="<?php _e('Working...<br>This may take a few minutes, please wait.', 'botdetect-wp-captcha');?>">
				
				<input type="text" size="40" class="bdwp-input-text" placeholder="<?php _e('Enter your email', 'botdetect-wp-captcha');?>" name="customerEmail" id="customerEmail"/>
            	<p><?php _e('We need your email to reference your deployment in our database. We will use it to inform you about security updates, new features, etc. We will never give your email to third parties, and you can easily unsubscribe (from our rare mailings) at any time.', 'botdetect-wp-captcha')?></p>

            	<div>
                    <input type="submit" class="button-primary" name="btnRegisterUser" id="btnRegisterUser" value="<?php _e('Register as a plugin user', 'botdetect-wp-captcha') ?>" />
                    <p class="bdwp-btn-register-user-disable" id="btnRegisterUserDisable"><?php _e('Register as a plugin user', 'botdetect-wp-captcha') ?></p>
                </div>
            </form>

            <?php
				if ($registerStatus == 'ERR_INVALIDEMAIL') {
            		printf(__('<p id="lblWrongEmail" class="bdwp-error-msg bdwp-res-msg">%s</p>', 'botdetect-wp-captcha'), $registerMessage);
            	}
            ?>

            <p class="res_msg" id="lblWaiting"></p>
        </div>
<?php
	}

	public function BDWPCaptchaImageRenderCheckScripts() {
        wp_enqueue_script( 'bdwp-captcha-image-render-check', plugin_dir_url( __FILE__ ) . 'public/js/captcha_image_render_check.js' );
    }

	public function DetectIEBrowser() {
		return (preg_match('/(?i)MSIE [1-7]/', $_SERVER['HTTP_USER_AGENT']))? true : false;
	}
}
