<?php
/**
 * PLUGIN DEFAULT PRESETS
 *
 * This file contains a super array which maps all the previous SRP version option keys with the current ones.
 * @author Luca Grandicelli <lgrandicelli@gmail.com>
 * @copyright (C) 2011-2014 Luca Grandicelli
 * @package special-recent-posts-free
 * @version 2.0.4
 *
 */

/**
 * @var array $srp_version_map Defining the version map super array.
 */
$srp_version_map = array(
	
	// Mapping the widget options for the 1.x SRP versions.
	'srp_filter_cat_option'         => 'category_include',
	'srp_custom_post_type_option'   => 'custom_post_type',
	'srp_thumbnail_option'          => 'display_thumbnail',
	'srp_add_nofollow_option'       => 'nofollow_links',
	'srp_wdg_excerpt_length'        => 'post_content_length',
	'srp_wdg_excerpt_length_mode'   => 'post_content_length_mode',
	'srp_content_post_option'       => 'post_content_mode',
	'srp_post_date_option'          => 'post_date',
	'srp_exclude_option'            => 'post_exclude',
	'srp_include_option'            => 'post_include',
	'srp_number_post_option'        => 'post_limit',
	'srp_post_global_offset_option' => 'post_offset',
	'srp_order_post_option'         => 'post_order',
	'srp_orderby_post_option'       => 'post_random',
	'srp_post_status_option'        => 'post_status',
	'srp_wdg_title_length'          => 'post_title_length',
	'srp_wdg_title_length_mode'     => 'post_title_length_mode',
	'srp_post_type'                 => 'post_type',
	'srp_thumbnail_wdg_height'      => 'thumbnail_height',
	'srp_thumbnail_rotation'        => 'thumbnail_rotation',
	'srp_thumbnail_wdg_width'       => 'thumbnail_width',
	'srp_widget_title'              => 'widget_title',
	'srp_widget_title_hide_option'  => 'widget_title_hide'
);
	