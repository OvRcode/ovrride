<?php

// Background Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-panels, .fl-node-$id .fl-tabs-label.fl-tab-active",
	'props'    => array(
		'background-color' => $settings->bg_color,
	),
) );

// Label Border
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-labels .fl-tabs-label",
	'props'    => array(
		'border-width' => array(
			'value' => $settings->border_width,
			'unit'  => 'px',
		),
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-labels .fl-tabs-label.fl-tab-active",
	'props'    => array(
		'border-color' => $settings->border_color,
		'border-width' => array(
			'value' => $settings->border_width,
			'unit'  => 'px',
		),
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-horizontal .fl-tabs-labels .fl-tabs-label.fl-tab-active:after",
	'props'    => array(
		'bottom' => array(
			'value' => '' === $settings->border_width ? '' : -$settings->border_width,
			'unit'  => 'px',
		),
		'height' => array(
			'value' => $settings->border_width,
			'unit'  => 'px',
		),
	),
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-vertical .fl-tabs-labels .fl-tabs-label.fl-tab-active:after",
	'props'    => array(
		'right' => array(
			'value' => '' === $settings->border_width ? '' : -$settings->border_width,
			'unit'  => 'px',
		),
		'width' => array(
			'value' => $settings->border_width,
			'unit'  => 'px',
		),
	),
) );

// Label Text Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-label",
	'props'    => array(
		'color' => $settings->label_text_color,
	),
) );

// Label Active Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-label.fl-tab-active, .fl-node-$id .fl-tabs-panel-label",
	'props'    => array(
		'color' => $settings->label_active_color,
	),
) );

// Label Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'label_padding',
	'selector'     => ".fl-node-$id .fl-tabs-label, .fl-node-$id .fl-tabs-label.fl-tab-active",
	'props'        => array(
		'padding-top'    => 'label_padding_top',
		'padding-right'  => 'label_padding_right',
		'padding-bottom' => 'label_padding_bottom',
		'padding-left'   => 'label_padding_left',
	),
) );

// Label Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-tabs-label",
	'setting_name' => 'label_typography',
	'settings'     => $settings,
) );

// Content Text Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-panel-content",
	'props'    => array(
		'color' => $settings->content_text_color,
	),
) );

// Content Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'content_padding',
	'selector'     => ".fl-node-$id .fl-tabs-panel-content",
	'props'        => array(
		'padding-top'    => 'content_padding_top',
		'padding-right'  => 'content_padding_right',
		'padding-bottom' => 'content_padding_bottom',
		'padding-left'   => 'content_padding_left',
	),
) );

// Content Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-tabs-panel-content",
	'setting_name' => 'content_typography',
	'settings'     => $settings,
) );

// Panel Border
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-tabs-panels, .fl-node-$id .fl-tabs-panel",
	'props'    => array(
		'border-color' => $settings->border_color,
		'border-width' => array(
			'value' => $settings->border_width,
			'unit'  => 'px',
		),
	),
) );
