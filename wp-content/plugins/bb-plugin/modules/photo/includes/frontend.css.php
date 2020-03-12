<?php

// Align
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'align',
	'selector'     => ".fl-node-$id .fl-photo",
	'prop'         => 'text-align',
) );

// Width
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'width',
	'selector'     => ".fl-node-$id .fl-photo-img, .fl-node-$id .fl-photo-content",
	'prop'         => 'width',
) );

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id .fl-photo-img",
) );

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'caption_typography',
	'selector'     => ".fl-node-$id.fl-module-photo .fl-photo-caption",
) );
