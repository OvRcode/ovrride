<?php
if( !defined('ABSPATH') ){ exit();}
function fbap_free_network_destroy($networkwide) {
	global $wpdb;
	if (function_exists('is_multisite') && is_multisite()) {
		// check if it is a network activation - if so, run the activation function for each blog id
		if ($networkwide) {
			$old_blog = $wpdb->blogid;
			// Get all blog ids
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				fbap_free_destroy();
			}
			switch_to_blog($old_blog);
			return;
		}
	}
	fbap_free_destroy();
}

function fbap_free_destroy()
{
	global $wpdb;
	
	if(get_option('xyz_credit_link')=="fbap")
	{
		update_option("xyz_credit_link", '0');
	}
	delete_option('xyz_fbap_application_name');
	delete_option('xyz_fbap_application_id');
	delete_option('xyz_fbap_application_secret');
	//delete_option('xyz_fbap_fb_id');
	delete_option('xyz_fbap_message');
	delete_option('xyz_fbap_po_method');
	delete_option('xyz_fbap_post_permission');
	delete_option('xyz_fbap_current_appln_token');
	delete_option('xyz_fbap_af');
	delete_option('xyz_fbap_pages_ids');
	delete_option('xyz_fbap_future_to_publish');
	delete_option('xyz_fbap_apply_filters');
	
	delete_option('xyz_fbap_free_version');
	
	delete_option('xyz_fbap_include_pages');
	delete_option('xyz_fbap_include_posts');
	delete_option('xyz_fbap_include_categories');
	delete_option('xyz_fbap_include_customposttypes');
	delete_option('xyz_fbap_peer_verification');
	delete_option('xyz_fbap_post_logs');
	delete_option('xyz_twap_premium_version_ads');
	delete_option('xyz_fbap_default_selection_edit');
// 	delete_option('xyz_fbap_utf_decode_enable');
	delete_option('xyz_fbap_fb_numericid');
	delete_option('fbap_installed_date');
	delete_option('xyz_fbap_dnt_shw_notice');
	delete_option('xyz_fbap_credit_dismiss');
}

register_uninstall_hook(XYZ_FBAP_PLUGIN_FILE,'fbap_free_network_destroy');


?>