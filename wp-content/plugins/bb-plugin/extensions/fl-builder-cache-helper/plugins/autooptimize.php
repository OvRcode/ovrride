<?php
namespace FLCacheClear;
class Autooptimize {

	var $name = 'AutoOptimize';
	var $url  = 'https://wordpress.org/plugins/autoptimize/';

	var $filters = array( 'fl_builder_init_ui' );

	static function run() {
		if ( class_exists( '\autoptimizeCache' ) ) {
			\autoptimizeCache::clearall();
		}
	}

	function filters() {
		add_filter( 'autoptimize_filter_noptimize', '__return_true' );
	}
}
