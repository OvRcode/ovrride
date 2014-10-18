<?php
/*
| ----------------------------------------------------------
| File        : srp-config.php
| Project     : Special Recent Posts FREE Edition plugin for Wordpress
| Version     : 1.9.9
| Description : The main config file.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.specialrecentposts.com
| Copyright (C) 2011-2012  Luca Grandicelli
| ----------------------------------------------------------
*/

/*
| ----------------------------------------------------
|
| GLOBAL ENVIROMENT VALUES
| These are the default plugin settings.
|
| ****************************************************
| ATTENTION: DO NOT CHANGE THESE VALUES HERE.
| ALL THESE VALUES CAN BE CHANGED IN THE WIDGET PANEL
| OR IN THE SETTINGS PAGE.
| ****************************************************
| ----------------------------------------------------
*/

// Defining global default widget values.
global $srp_default_widget_values;

// The global widget options array.
$srp_default_widget_values = array(
	'widget_title'                => 'Special Recent Posts', // Default widget title. 
	'display_thumbnail'           => 'yes',                  // Display thumbnails?
	'widget_title_hide'           => 'no',                   // Hide widget title?
	'widget_title_header'         => 'h3',                   // Default widget html header.
	'widget_title_header_classes' => '',                     // Additional widget title header classes.
	'thumbnail_width'             => 100,                    // Default thumbnails width.
	'thumbnail_height'            => 100,                    // Default thumbnails height.
	'thumbnail_link'              => 'yes',                  // Link thumbnails to post?
	'thumbnail_rotation'          => 'no',                   // Default thumbnails rotation option.
	'post_type'                   => 'post',                 // Default displayed post types.
	'post_status'                 => 'publish',              // Default displayed post status.
	'post_limit'                  => 5,                      // Default max number of posts to display.
	'post_content_type'           => 'content',              // Default post content type.
	'post_content_length'         => '100',                  // Default displayed post content length. 
	'post_content_length_mode'    => 'chars',                // Default displayed post content length mode.
	'post_title_length'           => '100',                  // Default displayed post title length. 
	'post_title_length_mode'      => 'fulltitle',            // Default displayed post title length mode. 
	'post_order'                  => 'DESC',                 // Default displayed post order.
	'post_offset'                 => 0,                      // Default post offset.
	'post_random'                 => 'no',                   // Randomize displayed posts?
	'post_current_hide'           => 'yes',                  // Hide current post from visualization when in single post view?
	'post_content_mode'           => 'titleexcerpt',         // Default layout content mode.
	'post_date'                   => 'yes',                  // Display post date?
	'post_include'                => '',                     // Filter posts by including post IDs.
	'post_exclude'                => '',                     // Exclude posts from visualization by IDs.
	'custom_post_type'            => '',                     // Filter post by Custom Post Type.
	'noposts_text'                => 'No posts available',   // Default 'No posts available' text.
	'allowed_tags'                => '',                     // List of allowed tags to display in the excerpt visualization.
	'string_break'                => '[...]',                // Default string break text.
	'image_string_break'          => '',                     // Path to optional image string break.
	'string_break_link'           => 'yes',                  // Link (image)string break to post?
	'date_format'                 => 'F jS, Y',              // Post date format.
	'category_include'            => '',                     // Filter posts by including categories IDs.
	'category_title'              => 'no',                   // When filtering by caqtegories, switch the widget title to a linked category title.
	'nofollow_links'              => 'no'                    // Add the 'no-follow' attribute to all widget links.
);

// Defining global default plugin values.
global $srp_default_plugin_values;

// The global plugin options array.
$srp_default_plugin_values = array(
	'srp_version'               => SRP_PLUGIN_VERSION,                 // The Special Recent Post current version.
	'srp_global_post_limit'     => 3,                                  // *** DO NOT CHANGE THIS ***.
	'srp_compatibility_mode'    => 'yes',                              // Compatibility Mode Option.
	'srp_noimage_url'           => SRP_PLUGIN_URL . SRP_DEFAULT_THUMB, // Defaul URL to the no-image placeholder.
	'srp_log_errors_screen'     => 'no',
	'srp_disable_theme_css'     => 'no'                                // Disable plugin CSS?
);
?>