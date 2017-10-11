<?php
/*
* Plugin Name: OvRride Site Widgets
* Description:  Collection of widgets for the ovrride.com site
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 1.3.0
* License: MIT License
*/
require('classes/ovr-blog-feature-widget.php');
require('classes/ovr-contact-widget.php');
require('classes/ovr-dual-trip-feature-widget.php');
require('classes/ovr-email-signup-widget.php');
require('classes/ovr-events-widget.php');
require('classes/ovr-featured-video-widget.php');
require('classes/ovr-jumbotron-widget.php');
require('classes/ovr-medium-trip-feature-widget.php');
require('classes/ovr-small-ad-widget.php');
require('classes/ovr-trip-feature-widget.php');
require('classes/ovr-calendar-widget.php');

function ovr_load_widgets() {

	setup_calendar_table();

	register_widget( 'ovr_blog_feature_widget' );
	register_widget( 'ovr_contact_widget' );
	register_widget( 'ovr_dual_trip_feature_widget' );
	register_widget( 'ovr_email_signup_widget' );
	register_widget( 'ovr_events_widget' );
	register_widget( 'ovr_featured_video_widget' );
	register_widget( 'ovr_jumbotron_widget' );
	register_widget( 'ovr_medium_trip_feature_widget' );
	register_widget( 'ovr_small_ad_widget' );
	register_widget( 'ovr_trip_feature_widget' );
	register_widget( 'ovr_calendar_widget' );
}

function setup_calendar_table() {
	global $wpdb;

	$table_name = $wpdb->prefix . "ovr_calendar_custom_events";

	$sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
  label tinytext NOT NULL,
  status text NOT NULL,
  url varchar(55) DEFAULT '' NOT NULL,
  PRIMARY KEY  (id)
) $charset_collate;";

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
dbDelta( $sql );
}
function ovr_widgets_admin_setup_menu() {
	add_menu_page( 'OvRride Calendar Events', 'Calendar Events', 'manage_options', 'ovr-calendar-widget-menu', 'ovr_calendar_events', 'dashicons-calendar-alt', 59 );
}

function ovr_calendar_events() {
	echo "<h1>Is this thing on?</h1>";
}
add_action( 'widgets_init', 'ovr_load_widgets' );
add_action('admin_menu', 'ovr_widgets_admin_setup_menu');
