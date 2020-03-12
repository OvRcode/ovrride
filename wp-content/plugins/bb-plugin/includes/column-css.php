<?php if ( ! empty( $col->settings->text_color ) ) : // Text Color ?>
.fl-node-<?php echo $col->node; ?> {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->text_color ); ?>;
}
.fl-builder-content .fl-node-<?php echo $col->node; ?> *:not(input):not(textarea):not(select):not(a):not(h1):not(h2):not(h3):not(h4):not(h5):not(h6):not(.fl-menu-mobile-toggle) {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $col->settings->link_color ) ) : // Link Color ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->link_color ); ?>;
}
<?php elseif ( ! empty( $col->settings->text_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $col->settings->hover_color ) ) : // Link Hover Color ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->hover_color ); ?>;
}
<?php elseif ( ! empty( $col->settings->text_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->text_color ); ?>;
}
<?php endif; ?>

<?php if ( ! empty( $col->settings->heading_color ) ) : // Heading Color ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> h1,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h2,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h3,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h4,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h5,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h6,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h1 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h2 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h3 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h4 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h5 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h6 a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->heading_color ); ?>;
}
<?php elseif ( ! empty( $col->settings->text_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $col->node; ?> h1,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h2,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h3,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h4,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h5,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h6,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h1 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h2 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h3 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h4 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h5 a,
.fl-builder-content .fl-node-<?php echo $col->node; ?> h6 a {
	color: <?php echo FLBuilderColor::hex_or_rgb( $col->settings->text_color ); ?>;
}
<?php endif; ?>

<?php

$responsive_enabled = $global_settings->responsive_enabled;

// Width - Desktop
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id",
	'props'    => array(
		'width' => "{$settings->size}%",
	),
) );

// Width - Medium
FLBuilderCSS::rule( array(
	'media'    => 'medium',
	'selector' => ".fl-builder-content .fl-node-$id",
	'enabled'  => '' !== $settings->size_medium && $responsive_enabled,
	'props'    => array(
		'width'            => "{$settings->size_medium}% !important",
		'max-width'        => 'none',
		'-webkit-box-flex' => '0 1 auto',
		'-moz-box-flex'    => '0 1 auto',
		'-webkit-flex'     => '0 1 auto',
		'-ms-flex'         => '0 1 auto',
		'flex'             => '0 1 auto',
	),
) );

// Width - Responsive
FLBuilderCSS::rule( array(
	'media'    => 'responsive',
	'selector' => ".fl-builder-content .fl-node-$id",
	'enabled'  => '' !== $settings->size_responsive && $responsive_enabled,
	'props'    => array(
		'width'     => "{$settings->size_responsive}% !important",
		'max-width' => 'none',
		'clear'     => 'none',
		'float'     => 'left',
	),
) );

// Stacking Order - Responsive
FLBuilderCSS::rule( array(
	'media'    => 'responsive',
	'selector' => ".fl-col-group-custom-width.fl-col-group-responsive-reversed .fl-node-$id",
	'enabled'  => 'reversed' == $settings->responsive_order && '' !== $settings->size_responsive && $responsive_enabled,
	'props'    => array(
		'flex-basis' => "{$settings->size_responsive}%",
	),
) );

// Background Color
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-col-content",
	'enabled'  => 'color' == $settings->bg_type,
	'props'    => array(
		'background-color' => $settings->bg_color,
	),
) );

// Background Gradient
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-col-content",
	'enabled'  => 'gradient' === $settings->bg_type,
	'props'    => array(
		'background-image' => FLBuilderColor::gradient( $settings->bg_gradient ),
	),
) );

// Background Color Overlay
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-col-content:after",
	'enabled'  => 'none' !== $settings->bg_overlay_type && in_array( $settings->bg_type, array( 'photo' ) ),
	'props'    => array(
		'background-color' => 'color' === $settings->bg_overlay_type ? $settings->bg_overlay_color : '',
		'background-image' => 'gradient' === $settings->bg_overlay_type ? FLBuilderColor::gradient( $settings->bg_overlay_gradient ) : '',
	),
) );

// Background Photo - Desktop
FLBuilderCSS::rule( array(
	'selector' => ".fl-node-$id > .fl-col-content",
	'enabled'  => 'photo' === $settings->bg_type,
	'props'    => array(
		'background-image'      => $settings->bg_image_src,
		'background-repeat'     => $settings->bg_repeat,
		'background-position'   => $settings->bg_position,
		'background-attachment' => $settings->bg_attachment,
		'background-size'       => $settings->bg_size,
	),
) );

// Background Photo - Medium
FLBuilderCSS::rule( array(
	'media'    => 'medium',
	'selector' => ".fl-node-$id > .fl-col-content",
	'enabled'  => 'photo' === $settings->bg_type,
	'props'    => array(
		'background-image'      => $settings->bg_image_medium_src,
		'background-repeat'     => $settings->bg_repeat_medium,
		'background-position'   => $settings->bg_position_medium,
		'background-attachment' => $settings->bg_attachment_medium,
		'background-size'       => $settings->bg_size_medium,
	),
) );

// Background Photo - Responsive
FLBuilderCSS::rule( array(
	'media'    => 'responsive',
	'selector' => ".fl-node-$id > .fl-col-content",
	'enabled'  => 'photo' === $settings->bg_type,
	'props'    => array(
		'background-image'      => $settings->bg_image_responsive_src,
		'background-repeat'     => $settings->bg_repeat_responsive,
		'background-position'   => $settings->bg_position_responsive,
		'background-attachment' => $settings->bg_attachment_responsive,
		'background-size'       => $settings->bg_size_responsive,
	),
) );

// Border
FLBuilderCSS::border_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'border',
	'selector'     => ".fl-node-$id > .fl-col-content",
) );

// Minimum Height
FLBuilderCSS::responsive_rule( array(
	'settings'     => $col->settings,
	'setting_name' => 'min_height',
	'selector'     => ".fl-builder-content .fl-node-$id > .fl-col-content",
	'prop'         => 'min-height',
) );
