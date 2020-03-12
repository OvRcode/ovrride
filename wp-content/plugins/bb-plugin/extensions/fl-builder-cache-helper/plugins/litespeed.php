<?php
namespace FLCacheClear;
class Litespeed {

	var $name = 'LiteSpeed Cache';
	var $url  = 'https://wordpress.org/plugins/litespeed-cache/';

	static function run() {
		if ( class_exists( '\LiteSpeed_Cache_API' ) ) {
			\LiteSpeed_Cache_API::purge_all();
		}
	}
}
