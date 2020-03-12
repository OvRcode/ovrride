<?php if ( 0 == $settings->item_spacing ) : ?>
.fl-node-<?php echo $id; ?> .fl-accordion-item:not(:last-child) {
	border-bottom: none;
	border-bottom-left-radius: 0;
	border-bottom-right-radius: 0;
}
.fl-node-<?php echo $id; ?> .fl-accordion-item:not(:first-child) {
	border-top-left-radius: 0;
	border-top-right-radius: 0;
}
<?php endif; ?>
<?php

// Item Spacing
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'item_spacing',
	'selector'     => ".fl-node-$id .fl-accordion-item",
	'prop'         => 'margin-bottom',
	'unit'         => 'px',
) );

// Item Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'item_border',
	'selector'     => ".fl-node-$id .fl-accordion-item",
) );

// Label BG Colors
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-accordion-button",
	'props'    => array(
		'background-color' => $settings->label_bg_color,
	),
) );

// Label Text Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-accordion-button-label",
	'props'    => array(
		'color' => $settings->label_text_color,
	),
) );

// Icon Colors
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-accordion-button-icon",
	'props'    => array(
		'background-color' => $settings->label_bg_color,
		'color'            => $settings->label_text_color,
	),
) );

// Label Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'label_padding',
	'selector'     => ".fl-node-$id .fl-accordion-button",
	'props'        => array(
		'padding-top'    => 'label_padding_top',
		'padding-right'  => 'label_padding_right',
		'padding-bottom' => 'label_padding_bottom',
		'padding-left'   => 'label_padding_left',
	),
) );

// Label Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-accordion-button, .fl-node-$id .fl-accordion-button-label",
	'setting_name' => 'label_typography',
	'settings'     => $settings,
) );

// Content Colors
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-accordion-content",
	'props'    => array(
		'background-color' => $settings->content_bg_color,
		'color'            => $settings->content_text_color,
	),
) );

// Content Padding
FLBuilderCSS::dimension_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'content_padding',
	'selector'     => ".fl-node-$id .fl-accordion-content",
	'props'        => array(
		'padding-top'    => 'content_padding_top',
		'padding-right'  => 'content_padding_right',
		'padding-bottom' => 'content_padding_bottom',
		'padding-left'   => 'content_padding_left',
	),
) );

// Content Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-accordion-content",
	'setting_name' => 'content_typography',
	'settings'     => $settings,
) );
