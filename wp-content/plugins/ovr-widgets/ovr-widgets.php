<?php
/*
* Plugin Name: OvRride Site Widgets
* Description:  Collection of widgets for the ovrride.com site
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 1.0.0
* License: MIT License
*/
include_once('classes/ovr-jumbotron-widget.php');
include_once('classes/ovr-trip-feature-widget.php');

function ovr_load_widgets() {
	register_widget( 'ovr_jumbotron_widget' );
	register_widget( 'ovr_trip_feature_widget' );
}

add_action( 'widgets_init', 'ovr_load_widgets' );
