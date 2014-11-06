<?php
class BDWP_Localization {

    public static function Init() {
    	load_plugin_textdomain('botdetect-wp-captcha', false, plugin_basename(BDWP_INCLUDE_PATH) . '/languages/');
    }
}
