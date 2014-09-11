<?php

if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );

class MyGallery
{
	function __construct() {
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
	}
	
	function action_admin_init() {
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );
		}
	}
	
	function filter_mce_button( $buttons ) {
		array_push( $buttons, '|', 'mygallery_button' );
		return $buttons;
	}
	
	function filter_mce_plugin( $plugins ) {
		$plugins['mygallery'] = plugins_url('shortcode_button.js',__FILE__);
		return $plugins;
	}
}

$mygallery = new MyGallery();