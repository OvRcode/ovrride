<?php
$parseUri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parseUri[0] . 'wp-load.php' );
require_once( dirname(dirname(__FILE__)). DIRECTORY_SEPARATOR . 'Diagnostics.php' );


// Request data from jquery ajax
$requestBDWPOptions = (isset($_REQUEST['bdwpOptions']))? $_REQUEST['bdwpOptions'] : '';
$requestProgress = (isset($_REQUEST['bdwpProgress']))? $_REQUEST['bdwpProgress'] : '';


/* = Handle and response data to client's
**************************************************************************************/

if (!empty($requestProgress)) {

	switch ($requestProgress) {
		case 'disable_login_form':
			echo DisableLoginForm();
			break;
		
		case 'session_and_query_string_check':
			echo SessionAndQueryStringCheck();
			break;
	}
}

/* = Functions
**************************************************************************************/

function DisableLoginForm() {
	global $requestBDWPOptions;
	$bdwp_workflow = get_option('bdwp_workflow');

	if (!empty($requestBDWPOptions) && ArrayKeyExistsCheck('bdphplib_is_installing', $bdwp_workflow) && $bdwp_workflow['bdphplib_is_installing'] == true) {
		$botdetect_options = (array)json_decode(stripslashes($requestBDWPOptions));
		DisableCaptchaForm($botdetect_options, 'on_login', false);
		RegisterUserEnded();
		return json_encode(array('status' => 'LOGIN_DISABLED'));
	}
}

// Check Session and Query Strings is working
function SessionAndQueryStringCheck() {
	global $requestBDWPOptions;
	RegisterUserEnded();

	$flagDisableLogin = false;

	if (IsEnableSuspiciousQueryStrings()) {
		$flagDisableLogin = true;
		$response = array('status' => 'ERROR_OPTIONS_QUERY_STRING_IS_ENABLED');
	} else if (!BDWP_Diagnostics::IsSessionEnabled()) {
		$flagDisableLogin = true;
		$response = array('status' => 'ERROR_SESSION_IS_DISABLED');
	} else {
		$response = array('status' => 'OK');
	}

	if ($flagDisableLogin) {
		$botdetect_options = (array)json_decode(stripslashes($requestBDWPOptions));
		DisableCaptchaForm($botdetect_options, 'on_login', false);
	}

	return json_encode($response);
}

// check status option: Suspicious Query Strings of iThemes Security Plugin
function IsEnableSuspiciousQueryStrings() {

	if (!IsExistsThemeAndActivated('iThemes Security')) return false;

	$itsecTweaks = get_option('itsec_tweaks');
	if (is_array($itsecTweaks) && 
			array_key_exists('suspicious_query_strings', $itsecTweaks) && 
				$itsecTweaks['suspicious_query_strings'] == true) {
		return true;
	}
	return false;
}

function IsExistsThemeAndActivated($p_ThemeName) {

	$allPlugin = BDWP_Diagnostics::GetPlugins();
	foreach ($allPlugin as $theme) {

		if ($theme['Name'] == $p_ThemeName) {
		 	if ($theme['Activated']) {
				return true;
			} else {
				return false;
			}
		}
	}
	return false;
}

function RegisterUserEnded() {
	update_option('bdwp_workflow', '');
}

function DisableCaptchaForm($p_Options, $p_Key, $p_Value) {
	$p_Options[$p_Key] = $p_Value;
	update_option('botdetect_options', $p_Options);
}

function ArrayKeyExistsCheck($p_Key, $p_Args) {
	if (!is_array($p_Args)) return false;
	if (!array_key_exists($p_Key, $p_Args)) return false;
	return true;
}
