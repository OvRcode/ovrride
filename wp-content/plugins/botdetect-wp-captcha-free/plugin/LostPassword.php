<?php
class BDWP_LostPassword extends BDWP_CaptchaIntegration {

	public function LostPasswordForm() {
		$this->ShowCaptchaForm('lost_password_captcha', 'lost_password_captcha_field');
	}

	public function LostPasswordValidate() {
		if ($_POST) {
			$isHuman = $this->ValidateCaptcha('lost_password_captcha', 'lost_password_captcha_field');
			if (!$isHuman) {
				wp_die(__('<strong>ERROR</strong>: Please browser\'s back button and retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'), 'BotDetect');
			} else {
				$this->ResetCaptcha('lost_password_captcha', 'lost_password_captcha_field');
			}
		}
	}
}
