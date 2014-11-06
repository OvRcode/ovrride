<?php
class BDWP_CaptchaIntegration {

	public function ResetCaptcha($p_Captcha_ID = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode') {
		if (class_exists('Captcha')) {
			$captcha = &$this->InitCaptcha($p_Captcha_ID, $p_UserInputId);
			$captcha->Reset();
		}
	}

	public function &InitCaptcha($p_Captcha_ID = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode') {
		$captcha = new Captcha($p_Captcha_ID);
		$captcha->UserInputId = $p_UserInputId;
		return $captcha;
	}

	public function ValidateCaptcha($p_Captcha_ID = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode') {
		$captcha = &$this->InitCaptcha($p_Captcha_ID, $p_UserInputId);

	    $UserInput = $_POST[$p_UserInputId];
	    $isHuman = $captcha->Validate($UserInput);
	 
		return $isHuman;
	}

	public function GetCaptchaForm($p_Captcha_ID = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode'){
		$captcha = &$this->InitCaptcha($p_Captcha_ID, $p_UserInputId);
	   
	    $output = $captcha->Html();
	    $output .= '<input name="' . $p_UserInputId . '" type="text" id="' . $p_UserInputId .'" />';

		return $output;
	}

	public function ShowCaptchaForm($p_Captcha_ID = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode', $p_Options = array()){
		$elements = array();
		$elements[] = $this->GetCaptchaForm($p_Captcha_ID, $p_UserInputId);
		if (isset($p_Options) && count($p_Options) != 0 && isset($p_Options[0])) {

			if (array_key_exists('label', $p_Options)){
				array_unshift($elements, '<label for="' . $p_UserInputId. '">' . $p_Options['label']. '</label>');
			}

			if (array_key_exists('prepend', $p_Options)){
				array_unshift($elements, $p_Options['prepend']);
			}

			if (array_key_exists('append', $p_Options)){
				$elements[] = $p_Options['append'];
			}
		}
		echo implode('', $elements);
	}
}
