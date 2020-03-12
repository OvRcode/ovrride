<?php

$input_selector = ".fl-node-$id .fl-subscribe-form .fl-form-field input, .fl-node-$id .fl-subscribe-form .fl-form-field input[type=text]";

// Default input styles
FLBuilderCSS::rule( array(
	'selector' => $input_selector,
	'props'    => array(
		'border-radius' => '4px',
		'font-size'     => '16px',
		'line-height'   => '16px',
		'padding'       => '12px 24px',
	),
) );

// Input typography
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'btn_typography',
	'selector'     => $input_selector,
) );

// Input padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'btn_padding',
	'selector'     => $input_selector,
	'unit'         => 'px',
	'props'        => array(
		'padding-top'    => 'btn_padding_top',
		'padding-right'  => 'btn_padding_right',
		'padding-bottom' => 'btn_padding_bottom',
		'padding-left'   => 'btn_padding_left',
	),
) );

// We only need border radius for inputs.
if ( is_array( $settings->btn_border ) ) {
	$settings->input_border           = $settings->btn_border;
	$settings->input_border['style']  = '';
	$settings->input_border['color']  = '';
	$settings->input_border['shadow'] = '';
}
if ( is_array( $settings->btn_border_medium ) ) {
	$settings->input_border_medium           = $settings->btn_border_medium;
	$settings->input_border_medium['style']  = '';
	$settings->input_border_medium['color']  = '';
	$settings->input_border_medium['shadow'] = '';
}
if ( is_array( $settings->btn_border_responsive ) ) {
	$settings->input_border_responsive           = $settings->btn_border_responsive;
	$settings->input_border_responsive['style']  = '';
	$settings->input_border_responsive['color']  = '';
	$settings->input_border_responsive['shadow'] = '';
}

// Input border radius
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'input_border',
	'selector'     => $input_selector,
) );

// Button CSS
FLBuilder::render_module_css( 'button', $id, $module->get_button_settings() );
