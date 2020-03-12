<?php

// Button Styles
if ( 'button' == $settings->cta_type ) {
	FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() );
}

// Icon Styles
if ( 'icon' == $settings->image_type ) {
	FLBuilder::render_module_css( 'icon', $id, $module->get_icon_settings() );
}

// Photo Styles
if ( 'photo' == $settings->image_type && ! empty( $settings->photo ) ) {
	FLBuilder::render_module_css( 'photo', $id, $module->get_photo_settings() );
}

// Background Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-module-content",
	'props'    => array(
		'background-color' => $settings->bg_color,
	),
) );

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .fl-module-content",
) );

// Wrapper Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'padding',
	'selector'     => ".fl-node-$id .fl-module-content",
	'props'        => array(
		'padding-top'    => 'padding_top',
		'padding-right'  => 'padding_right',
		'padding-bottom' => 'padding_bottom',
		'padding-left'   => 'padding_left',
	),
) );

// Title Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-callout-title, .fl-node-$id .fl-callout-title-text, .fl-node-$id .fl-callout-title-text:hover",
	'props'    => array(
		'color' => $settings->title_color,
	),
) );

// Title Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-callout-title",
	'setting_name' => 'title_typography',
	'settings'     => $settings,
) );

// Content Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-callout-text, .fl-node-$id .fl-callout-cta-link",
	'props'    => array(
		'color' => $settings->content_color,
	),
) );

// Content Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-callout-text, .fl-node-$id .fl-callout-cta-link",
	'setting_name' => 'content_typography',
	'settings'     => $settings,
) );

// Link Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id a.fl-callout-cta-link",
	'props'    => array(
		'color' => $settings->link_color,
	),
) );

// Link Hover Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id a.fl-callout-cta-link:hover, .fl-node-$id a.fl-callout-cta-link:focus",
	'props'    => array(
		'color' => $settings->link_hover_color,
	),
) );

// Link Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id a.fl-callout-cta-link",
	'setting_name' => 'link_typography',
	'settings'     => $settings,
) );
