<?php
namespace FLCacheClear;
class Varnish {

	static function run() {

		$settings = \FLCacheClear\Plugin::get_settings();
		if ( ! $settings['varnish'] ) {
			return false;
		}
		// @codingStandardsIgnoreLine
		@wp_remote_request( get_site_url(), array(
			'method' => 'BAN',
		) );
	}
}
