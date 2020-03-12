<?php
namespace FLCacheClear;
class Cloudflare {

	var $name = 'Cloudflare';
	var $url  = 'https://wordpress.org/plugins/cloudflare/';

	static function run() {
		if ( class_exists( '\CF' ) ) {
			$cloudflare = new \CF\WordPress\Hooks();
			$cloudflare->purgeCacheEverything();
		}
	}
}
