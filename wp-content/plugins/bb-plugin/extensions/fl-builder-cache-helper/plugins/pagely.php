<?php
namespace FLCacheClear;
class Pagely {

	var $name = 'Pagely Hosting';
	var $url  = 'https://pagely.com/plans-pricing/';

	static function run() {
		if ( class_exists( '\PagelyCachePurge' ) ) {
			$purger = new \PagelyCachePurge();
			$purger->purgeAll();
		}
	}
}
