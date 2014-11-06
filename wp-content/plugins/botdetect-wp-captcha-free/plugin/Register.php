<?php
class BDWP_Register extends BDWP_CaptchaIntegration {

	public function RegisterForm() {
		$this->ShowCaptchaForm('register_captcha', 'register_captcha_field');
	}

	public function RegisterValidation($p_Error) {
		if ($_POST) {
			$isHuman = $this->ValidateCaptcha('register_captcha', 'register_captcha_field');
			if (!$isHuman) {
				if (!is_wp_error($p_Error)) {
					$p_Error = new WP_Error();
				}

				$p_Error->add('captcha_fail', __('<strong>ERROR</strong>: Please retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'), 'BotDetect');
				return $p_Error;
			} else {
				$this->ResetCaptcha('register_captcha', 'register_captcha_field');
				return $p_Error;
			}
		}
	}
}
