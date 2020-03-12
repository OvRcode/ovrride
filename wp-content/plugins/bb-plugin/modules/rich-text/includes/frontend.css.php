<?php

if ( ! empty( $settings->color ) ) {
	FLBuilderCSS::rule( array(
		'selector' => ".fl-node-$id .fl-rich-text, .fl-node-$id .fl-rich-text *",
		'props'    => array(
			'color' => $settings->color,
		),
	) );
}

FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-node-$id .fl-rich-text, .fl-node-$id .fl-rich-text *",
) );

FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-rich-text strong",
	'props'    => array(
		'font-weight' => 'bold',
	),
) );
