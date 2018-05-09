<?php
if( !defined('ABSPATH') ){ exit();}
function fbap_free_network_install($networkwide) {
	global $wpdb;

	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				fbap_install_free();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	fbap_install_free();
}

function fbap_install_free()
{
	/*$pluginName = 'xyz-wp-smap/xyz-wp-smap.php';
	if (is_plugin_active($pluginName)) {
		wp_die( "The plugin WP Facebook Auto Publish cannot be activated unless the premium version of this plugin is deactivated. Back to <a href='".admin_url()."plugins.php'>Plugin Installation</a>." );
	}*/
	if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	
		wp_die( "The plugin WP Facebook Auto Publish  requires PHP version 5.4 or higher. Back to <a href='".admin_url()."plugins.php'>Plugin Installation</a>." );
	
	}
	
	global $current_user;
	wp_get_current_user();
	if(get_option('xyz_credit_link')=="")
	{
		add_option("xyz_credit_link", '0');
	}
	
	$fbap_installed_date = get_option('fbap_installed_date');
	if ($fbap_installed_date=="") {
		$fbap_installed_date = time();
		update_option('fbap_installed_date', $fbap_installed_date);
	}
	add_option('xyz_fbap_application_name','');
	add_option('xyz_fbap_application_id','');
	add_option('xyz_fbap_application_secret', '');
	//add_option('xyz_fbap_fb_id', '');
	add_option('xyz_fbap_message', 'New post added at {BLOG_TITLE} - {POST_TITLE}');
 	add_option('xyz_fbap_po_method', '2');
	add_option('xyz_fbap_post_permission', '1');
	add_option('xyz_fbap_current_appln_token', '');
	add_option('xyz_fbap_af', '1'); //authorization flag
	add_option('xyz_fbap_pages_ids','-1');
	add_option('xyz_fbap_future_to_publish', '1');
	add_option('xyz_fbap_apply_filters', '');
	add_option('xyz_fbap_fb_numericid','');
	$version=get_option('xyz_fbap_free_version');
	$currentversion=xyz_fbap_plugin_get_version();
	update_option('xyz_fbap_free_version', $currentversion);
	
	add_option('xyz_fbap_include_pages', '0');
	add_option('xyz_fbap_include_posts', '1');
	add_option('xyz_fbap_include_categories', 'All');
	add_option('xyz_fbap_include_customposttypes', '');
	
	add_option('xyz_fbap_peer_verification', '1');
	add_option('xyz_fbap_post_logs', '');
	add_option('xyz_fbap_premium_version_ads', '1');
	add_option('xyz_fbap_default_selection_edit', '0');
// 	add_option('xyz_fbap_utf_decode_enable', '0');
	add_option('xyz_fbap_dnt_shw_notice','0');
	if(get_option('xyz_fbap_credit_dismiss') == "")
		add_option("xyz_fbap_credit_dismiss",0);
}


register_activation_hook(XYZ_FBAP_PLUGIN_FILE,'fbap_free_network_install');
?>