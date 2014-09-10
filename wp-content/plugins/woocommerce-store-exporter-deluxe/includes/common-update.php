<?php
/*

Filename: common-update.php
Description: common-update.php loads commonly accessed functions required for non-Codex Plugin updates.
Version: 1.1

*/

$api_url = 'http://wpsc-updates.visser.com.au/';

function woo_cd_plugin_update_check( $checked_data ) {

	global $woo_cd, $api_url;

	$plugin_slug = $woo_cd['dirname'];
	$plugin_path = $woo_cd['relpath'];

	if( empty( $checked_data->checked ) )
		return $checked_data;

	$request_args = array(
		'slug' => $plugin_slug,
		'version' => $checked_data->checked[$plugin_path],
	);
	$request_string = woo_vl_plugin_update_prepare( 'basic_check', $request_args );

	$raw_response = wp_remote_post( $api_url, $request_string );
	if( !is_wp_error( $raw_response ) && ( $raw_response['response']['code'] == 200 ) )
		$response = unserialize( $raw_response['body'] );
	else
		$response = false;

	if( is_object( $response ) && !empty( $response ) )
		$checked_data->response[$plugin_path] = $response;

	return $checked_data;

}
add_filter( 'pre_set_site_transient_update_plugins', 'woo_cd_plugin_update_check' );

function woo_cd_plugin_update_call( $def, $action, $args ) {

	global $woo_cd, $api_url;

	$plugin_slug = $woo_cd['dirname'];
	$plugin_path = $woo_cd['relpath'];

	if( $args->slug != $plugin_slug )
		return false;

	$plugin_info = get_site_transient( 'update_plugins' );
	$current_version = $plugin_info->checked[$plugin_path];
	$args->version = $current_version;

	$request_string = woo_vl_plugin_update_prepare( $action, $args );
	$request = wp_remote_post( $api_url, $request_string );
	if( is_wp_error( $request ) ) {
		$res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $request->get_error_message() );
	} else {
		$res = unserialize( $request['body'] );
		if( $res === false )
			$res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $request['body'] );
	}

	return $res;

}
add_filter( 'plugins_api', 'woo_cd_plugin_update_call', 10, 3 );
?>