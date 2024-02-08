<?php
/*
* Plugin Name: OvRride Site Widgets
* Description:  Collection of widgets for the ovrride.com site
* Author: Ada Lambrecht
* Author URI:  https://github.com/ada-lambrecht
* Version: 1.7.4
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
require('classes/ovr-wide-ad-widget.php');

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
	register_widget( 'ovr_wide_ad_widget' );
	register_widget( 'ovr_trip_feature_widget' );
	register_widget( 'ovr_calendar_widget' );
}

function ovr_widgets_admin_setup_menu() {
	add_menu_page( 'OvRride Calendar Events', 'Calendar Events', 'manage_options', 'ovr-calendar-widget-menu', 'ovr_calendar_events', 'dashicons-calendar-alt', 59 );
}

function ovr_calendar_events() {
	wp_enqueue_script('ovr_calendar_add_events', plugin_dir_url( __FILE__ ).'js/ovr_calendar_custom_events.js', array('jquery','jquery-ui-datepicker'), "1.0", true);
	wp_localize_script( 'ovr_calendar_add_events', 'ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ),
	 'add_nonce' => wp_create_nonce("ovr_calendar_add_events"),
	 'remove_nonce' => wp_create_nonce("ovr_calendar_remove_events"),
	  'update_nonce' => wp_create_nonce("ovr_calendar_update_events") ) );
	wp_enqueue_style('ovr-calendar-admin-ui-css','http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
	echo <<<CALADMIN
	<h1>OvRride Calendar Widget</h1>
	<h3> Add/Remove non-trip events here</h3>
	<form name="ovr_calendar_add_event">
		<h4>Event Info: </h4>
		<div><label>Name <label><input type='text' name='ovr_calendar_event_name' id='ovr_calendar_event_name'></input></div>
		<div><label>Url </label><input type='text' name='ovr_calendar_event_url' id='ovr_calendar_event_url'></input></div>
		<div><label>Start </label><input id="ovr_calendar_event_start" name="ovr_calendar_event_start" class="datepicker" /></div>
		<div><label>End </label><input id="ovr_calendar_event_end" name="ovr_calendar_event_end" class="datepicker" /></div>
		<input type="button" value="Add Event" />
	</form>
CALADMIN;
	$events = get_option("ovr_custom_events");
	echo "<hr><h4>Calendar Events</h4><hr>";
	if ( ! $events ) {
		echo "No Custom events set";
	} else {
		$events = maybe_unserialize($events);
		echo "<table id='ovr_calendar_custom_events_table'><tr><th>Name</th><th>URL</th><th>Start</th><th>End</th><th>Season</th><th>Active</th></tr>";
		foreach ($events as $id => $info ) {
			if ( $info['active'] === 0 ) {
				$activeOptions = "<option value=0 selected>Inactive</option><option value=1>Active</option>";
			} else if ( $info['active'] == 1) {
				$activeOptions = "<option value=0>Inactive</option><option value=1 selected>Active</option>";
			}
			if ( $info['season'] === "winter" ) {
				$seasonOptions = "<option value='winter' selected>Winter</option><option value='summer'>Summer</option>";
			} else if ( $info['season'] === "summer" ) {
				$seasonOptions = "<option value='winter'>Winter</option><option value='summer' selected>Summer</option>";
			}
			echo <<<TABLELINE
				<tr><td>{$info['name']}</td>
				<td>{$info['url']}</td>
				<td>{$info['start']}</td>
				<td>{$info['end']}</td>
				<td>
					<select class="season" data-id="{$id}">{$seasonOptions}</select>
				</td>
				<td>
					<select class="activeInactive" data-id="{$id}">{$activeOptions}</select>
				</td>
				<td><i class='dashicons dashicons-no' data-id="{$id}"></td></tr>
TABLELINE;
		}
		echo "</table>";
	}
}
function ovr_calendar_add_event() {
	$nonce = $_POST['add_nonce'];
	if ( ! wp_verify_nonce( $nonce, 'ovr_calendar_add_events' ) )
		die('Nonce verification failed');

	$existing_events = get_option("ovr_custom_events", array());
	$event = [ "name" => $_POST['name'], "url"	=> $_POST["url"],
		"start"	=> $_POST["start"], "end"	=> $_POST["end"], "active" => 0, "season" => "winter" ];
	if ( preg_match("/^http[s]{0,1}:\/\//", $event["url"]) == false ) {
		$event["url"] = "http://" . $event["url"];
	}
	if ( count($existing_events) == 0 ) {
		$events[0] = $event;
	} else {
		$id = count($existing_events);
		while( isset($existing_events[$id]) ) {
			$id++;
		}
		$events = $existing_events;
		$events[$id] = $event;
	}
	if ( update_option("ovr_custom_events", $events) )
		echo "true";
	else
		echo "false";
	exit;
}
function ovr_calendar_remove_event() {
	$nonce = $_POST['remove_nonce'];
	if ( !wp_verify_nonce($nonce, 'ovr_calendar_remove_events') ) {
		die('Nonce verification failed');
	}

	$existing_events = maybe_unserialize(get_option("ovr_custom_events"));

	if ( isset($existing_events[$_POST['id']]) ) {
		unset($existing_events[$_POST['id']]);
		if ( update_option('ovr_custom_events', $existing_events) ) {
			echo "true";
		} else {
			echo "false";
		}
	} else {
		echo "false";
	}
	exit;
}
function ovr_calendar_update_event() {
	$nonce = $_POST['update_nonce'];
	if ( !wp_verify_nonce($nonce, 'ovr_calendar_update_events') ) {
		die("Nonce verification failed!");
	}

	$existing_events = maybe_unserialize( get_option("ovr_custom_events") );

	if ( isset( $existing_events[$_POST['id']] ) ) {
			$existing_events[$_POST['id']]['active'] = $_POST['active'];
			$existing_events[$_POST['id']]['season'] = $_POST['season'];
			if ( update_option("ovr_custom_events", $existing_events) ) {
				do_action("ovr_calendar_refresh");
				return "true";
			} else {
				return "false";
			}
	} else {
		return "false";
	}
}
add_action( 'widgets_init', 'ovr_load_widgets' );
add_action('admin_menu', 'ovr_widgets_admin_setup_menu');
add_action( 'wp_ajax_ovr_calendar_add_event', 'ovr_calendar_add_event' );
add_action( 'wp_ajax_ovr_calendar_remove_event', 'ovr_calendar_remove_event');
add_action( 'wp_ajax_ovr_calendar_update_event', 'ovr_calendar_update_event');
