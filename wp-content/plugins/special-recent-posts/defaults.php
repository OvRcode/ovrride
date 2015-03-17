<?php
/**
 * PLUGIN DEFAULT PRESETS
 * NOTE: PLEASE DO NOT CHANGE THESE VALUES HERE!!!
 * USE THE WIDGET OPTIONS INSTEAD.
 *
 * This file contains all the plugin/widget default presets.
 * 
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 *
 * @global array $srp_default_widget_values
 * @global array $srp_default_plugin_values
 */
global $srp_default_widget_values;
global $srp_default_plugin_values;

/**
 * @var array $srp_default_widget_values The array containing all the default widget presets.
 */
$srp_default_widget_values = array(

	// BASIC OPTIONS
	'post_limit'                   => 5,                       // The max number of posts to display
	'post_type'                    => 'post',                  // The displayed post type
	'show_all_posts'               => 'no',                    // The 'Show All Posts/Pages' option value
	'show_sticky_posts'            => 'no',                    // The 'Show Sticky Posts?' option value
	'widget_title'                 => 'Special Recent Posts',  // The widget title

	// THUMBNAIL OPTIONS
	'display_thumbnail'            => 'yes',                   // The 'Display Thumbnails?' option value
	'thumbnail_height'             => 100,                     // The custom thumbnail height
	'thumbnail_link'               => 'yes',                   // The 'link thumbnails to post' option value
	'thumbnail_rotation'           => 'no',                    // The thumbnail rotation mode option value
	'thumbnail_width'              => 100,                     // The custom thumbnail width

	// POST OPTIONS
	'ext_shortcodes_compatibility' => 'no',                    // The 'Enable Shortcodes Compatibility' value
	'post_content_length'          => '100',                   // The post content length.
	'post_content_length_mode'     => 'chars',                 // The post content length mode
	'post_content_type'            => 'content',               // The post content type
	'post_order'                   => 'DESC',                  // The 'Post/Pages Order' option value
	'post_random'                  => 'no',                    // The 'Enable Random Mode' option value
	'post_title_length'            => '100',                   // The post title length. 
	'post_title_length_mode'       => 'fulltitle',             // The post title length mode.
	'wp_filters_enabled'           => 'no',                    // The 'Enable Wordpress Filters' option value.

	// ADVANCED POST OPTIONS 1
	'allowed_tags'                 => '',                     // The list of allowed tags to display in the post content
	'image_string_break'           => '',                     // The absolute path to the optional image string break
	'noposts_text'                 => 'No posts available',   // The text for when no posts are available
	'post_current_hide'            => 'yes',                  // The 'Hide Current Viewed Post' option value
	'post_offset'                  => 0,                      // The post offset.
	'string_break'                 => '[...]',                // The string break text.
	'string_break_link'            => 'yes',                  // The 'Link String/Image Break To Post?' option value.

	// ADVANCED POST OPTIONS 1
	'date_format'                  => 'F jS, Y',              // The post date format.
	'nofollow_links'               => 'no',                   // The 'Add 'rel=nofollow' Attribute On Links?' option value
	'post_date'                    => 'yes',                  // The 'Display post date' option value

	// FILTERING OPTIONS
	'category_include'             => '',                     // The comma separated list of categories IDs to filter posts by.
	'category_title'               => 'no',                   // The 'Use Category Name As Widget Title?' option value
	'custom_post_type'             => '',                     // The comma separated list of Custom Post Type names to filter posts by.
	'post_exclude'                 => '',                     // The comma separated list of post IDs to be excluded.
	'post_include'                 => '',                     // The comma separated list of post IDs to be included.
	'post_status'                  => 'publish',              // The 'Post Status Filter' option value

	// LAYOUT OPTIONS
	'post_content_mode'            => 'titleexcerpt',         // The layout content mode
	'post_title_header'            => 'h4',                   // The post title HTML header.
	'widget_title_header'          => 'h3',                   // The widget HTML header.
	'widget_title_header_classes'  => '',                     // The space separated list of additional widget title header classes.
	'widget_title_hide'            => 'no',                   // The 'Hide Widget Title' option value
	'widget_title_show_default_wp' => 'no'                    // This option lets SRP render the widget title as Wordpress would normally do. Without customization.
);

/**
 * @var array $srp_default_plugin_values The array containing all the default plugin presets.
 */
$srp_default_plugin_values = array(
	
	'srp_compatibility_mode'     => 'yes',                 // The 'Compatibility Mode' option value
	'srp_custom_css'             => 'Default CSS Comment', // The default CSS Editor comment text
	'srp_disable_theme_css'      => 'no',                  // The 'Disable Plugin CSS?' option value
	'srp_log_errors_screen'      => 'no',                  // Log errors on screen?
	'srp_noimage_url'            => SRP_DEFAULT_THUMB,     // The absolute URL to the no-image placeholder
	'srp_thumbnail_jpeg_quality' => '80',                  // The thumbnails jpeg image quality ratio
	'srp_version'                => SRP_PLUGIN_VERSION,    // The Special Recent Post current version.
);