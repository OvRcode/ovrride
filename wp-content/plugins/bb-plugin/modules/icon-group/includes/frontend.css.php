<?php

FLBuilder::render_module_css('icon', $id, array(
	'align'                => '',
	'bg_color'             => $settings->bg_color,
	'bg_hover_color'       => $settings->bg_hover_color,
	'color'                => $settings->color,
	'hover_color'          => $settings->hover_color,
	'icon'                 => '',
	'link'                 => '',
	'link_target'          => '',
	'size'                 => $settings->size,
	'size_unit'            => $settings->size_unit,
	'size_medium'          => $settings->size_medium,
	'size_medium_unit'     => $settings->size_medium_unit,
	'size_responsive'      => $settings->size_responsive,
	'size_responsive_unit' => $settings->size_responsive_unit,
	'text'                 => '',
	'three_d'              => $settings->three_d,
));

?>
<?php
foreach ( $settings->icons as $i => $icon ) :
	$index = $i + 1;

	if ( ! empty( $icon->bg_color ) ) {

		foreach ( array( '', 'medium', 'responsive' ) as $device ) {

			$key      = empty( $device ) ? 'size' : "size_{$device}";
			$unit_key = "{$key}_unit";

			if ( isset( $settings->{ $key } ) && ! empty( $settings->{ $key } ) ) {

				FLBuilderCSS::rule( array(
					'media'    => $device,
					'selector' => ".fl-node-$id .fl-module-content .fl-icon:nth-child($index) i",
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
			}
		}
	}

	?>
	<?php if ( isset( $icon->color ) && ! empty( $icon->color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) i,
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) i:before {
		color: <?php echo FLBuilderColor::hex_or_rgb( $icon->color ); ?>;
	}
	<?php endif; ?>
	<?php if ( isset( $icon->bg_color ) && ! empty( $icon->bg_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) i {
		background: <?php echo FLBuilderColor::hex_or_rgb( $icon->bg_color ); ?>;
		border-radius: 100%;
		-moz-border-radius: 100%;
		-webkit-border-radius: 100%;
		text-align: center;
	}
	<?php endif; ?>
	<?php if ( isset( $icon->hover_color ) && ! empty( $icon->hover_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) i:hover,
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) i:hover:before,
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) a:hover i,
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) a:hover i:before {
		color: <?php echo FLBuilderColor::hex_or_rgb( $icon->hover_color ); ?>;
	}
	<?php endif; ?>
	<?php if ( isset( $icon->bg_hover_color ) && ! empty( $icon->bg_hover_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) i:hover,
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-icon:nth-child(<?php echo $index; ?>) a:hover i {
		background: <?php echo FLBuilderColor::hex_or_rgb( $icon->bg_hover_color ); ?>;
	}
	<?php endif; ?>
<?php endforeach; ?>

/* Left */
.fl-node-<?php echo $id; ?> .fl-icon-group-left .fl-icon {
	margin-right: <?php echo $settings->spacing; ?>px;
}

/* Center */
.fl-node-<?php echo $id; ?> .fl-icon-group-center .fl-icon {
	margin-left: <?php echo $settings->spacing; ?>px;
	margin-right: <?php echo $settings->spacing; ?>px;
}

/* Right */
.fl-node-<?php echo $id; ?> .fl-icon-group-right .fl-icon {
	margin-left: <?php echo $settings->spacing; ?>px;
}
