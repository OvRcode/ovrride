<?php
namespace FLCacheClear;
class ACF {

	var $name = 'Advanced Custom Fields';
	var $url  = 'https://wordpress.org/plugins/advanced-custom-fields/';

	var $filters = array( 'admin_init' );

	function run() {
		// nothing here
	}

	function filters() {
		add_filter( 'acf/save_post', function( $post_id ) {
			\FLBuilderModel::delete_all_asset_cache( $post_id );
		});
	}
}
