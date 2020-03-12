<?php
namespace FLCacheClear;
class Kinsta {

	var $name = 'Kinsta Hosting';
	var $url  = 'https://kinsta.com/';

	static function run() {
		global $kinsta_cache;
		if ( class_exists( '\Kinsta\CDN_Enabler' ) && is_object( $kinsta_cache ) && isset( $kinsta_cache->kinsta_cache_purge ) ) {
			$kinsta_cache->kinsta_cache_purge->purge_complete_caches();
		}
	}
}
