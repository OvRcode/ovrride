<?php
/*
* Plugin Name: OvRride Site Widgets
* Description:  Collection of widgets for the ovrride.com site
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 1.2.2
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

add_action( 'widgets_init', 'ovr_load_widgets' );
