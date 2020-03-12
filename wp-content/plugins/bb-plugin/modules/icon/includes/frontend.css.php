<?php

// Font Size
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'size',
	'selector'     => ".fl-node-$id .fl-module-content .fl-icon i, .fl-node-$id .fl-module-content .fl-icon i:before",
	'prop'         => 'font-size',
) );

foreach ( array( '', 'medium', 'responsive' ) as $device ) {

	$key      = empty( $device ) ? 'size' : "size_{$device}";
	$unit_key = "{$key}_unit";

	if ( isset( $settings->{ $key } ) && ! empty( $settings->{ $key } ) ) {

		FLBuilderCSS::rule( array(
			'media'    => $device,
			'selector' => ".fl-node-$id .fl-module-content .fl-icon-text",
			'props'    => array(
				'height' => array(
					'value' => $settings->{ $key } * 1.75,
					'unit'  => $settings->{ $unit_key },
				),
			),
		) );

		if ( $settings->bg_color ) {
			FLBuilderCSS::rule( array(
				'media'    => $device,
				'selector' => ".fl-node-$id .fl-module-content .fl-icon i",
				'props'    => array(
					'line-height' => array(
						'value' => $settings->{ $key } * 1.75,
						'unit'  => $settings->{ $unit_key },
					),
					'height'      => array(
						'value' => $settings->{ $key } * 1.75,
						'unit'  => $settings->{ $unit_key },
					),
					'width'       => array(
						'value' => $settings->{ $key } * 1.75,
						'unit'  => $settings->{ $unit_key },
					),
				),
			) );
			FLBuilderCSS::rule( array(
				'media'    => $device,
				'selector' => ".fl-node-$id .fl-module-content .fl-icon i::before",
				'props'    => array(
					'line-height' => array(
						'value' => $settings->{ $key } * 1.75,
						'unit'  => $settings->{ $unit_key },
					),
				),
			) );
		}
	}
}

// Overall Alignment
FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'align',
	'selector'     => ".fl-node-$id.fl-module-icon",
	'prop'         => 'text-align',
) );

// Text Spacing
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-icon-text",
	'props'    => array(
		'padding-left' => array(
			'value' => $settings->text_spacing,
			'unit'  => 'px',
		),
	),
) );

// Text Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id .fl-icon-text, .fl-node-$id .fl-icon-text-link",
	'props'    => array(
		'color' => $settings->text_color,
	),
) );

// Text Typography
FLBuilderCSS::typography_field_rule( array(
	'selector'     => ".fl-node-$id .fl-icon-text, .fl-node-$id .fl-icon-text-link",
	'setting_name' => 'text_typography',
	'settings'     => $settings,
) );

// Background and border colors
if ( $settings->three_d ) {
	$bg_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_color, 30, 'lighten' );
	$border_color  = FLBuilderColor::adjust_brightness( $settings->bg_color, 20, 'darken' );
}
if ( $settings->three_d && ! empty( $settings->bg_hover_color ) ) {
	$bg_hover_grad_start = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 30, 'lighten' );
	$border_hover_color  = FLBuilderColor::adjust_brightness( $settings->bg_hover_color, 20, 'darken' );
}

?>
<?php if ( $settings->color ) : ?>
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon i,
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon i:before {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->color ); ?>;
}
<?php endif; ?>
<?php if ( $settings->bg_color ) : ?>
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon i {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?>;
	border-radius: 100%;
	-moz-border-radius: 100%;
	-webkit-border-radius: 100%;
	text-align: center;
	<?php if ( $settings->three_d ) : ?>
	background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $bg_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_color ); ?> 100%);
	border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( $border_color ); ?>;
	<?php endif; ?>
}
<?php endif; ?>
<?php if ( ! empty( $settings->hover_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon i:hover,
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon i:hover:before,
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon a:hover i,
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon a:hover i:before {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->hover_color ); ?>;
}
<?php endif; ?>
<?php if ( ! empty( $settings->bg_hover_color ) ) : ?>
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon i:hover,
.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon a:hover i {
	background: <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_hover_color ); ?>;
	<?php if ( $settings->three_d ) : ?>
	background: linear-gradient(to bottom,  <?php echo FLBuilderColor::hex_or_rgb( $bg_hover_grad_start ); ?> 0%, <?php echo FLBuilderColor::hex_or_rgb( $settings->bg_hover_color ); ?> 100%);
	border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( $border_hover_color ); ?>;
	<?php endif; ?>
}
<?php endif; ?>
