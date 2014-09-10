<?php
/*

Filename: common.php
Description: common.php loads commonly accessed functions across the Visser Labs suite.

- woo_vl_plugin_update_prepare

*/

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( 'common-update.php' );
	include_once( 'common-dashboard_widgets.php' );

	if( !function_exists( 'woo_vl_plugin_update_prepare' ) ) {

		function woo_vl_plugin_update_prepare( $action, $args ) {

			global $wp_version;

			return array(
				'body' => array(
					'action' => $action,
					'request' => serialize( $args ),
					'api-key' => md5( get_bloginfo( 'url' ) ),
					'site' => get_bloginfo( 'url' )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
			);	

		}

	}

	/* End of: WordPress Administration */

}
