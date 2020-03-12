<?php

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'number_size',
	'selector'     => ".fl-node-$id .fl-countdown .fl-countdown-unit-number",
	'prop'         => 'font-size',
) );

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'label_size',
	'selector'     => ".fl-node-$id .fl-countdown .fl-countdown-unit-label",
	'prop'         => 'font-size',
) );

?>

<?php if ( isset( $settings->number_spacing ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-countdown .fl-countdown-number {
		font-size: 1px;
		margin-left: <?php echo $settings->number_spacing; ?>px;
		margin-right: <?php echo $settings->number_spacing; ?>px;
	}
<?php endif; ?>

.fl-node-<?php echo $id; ?> .fl-countdown .fl-countdown-unit-number {
	<?php
	if ( ! empty( $settings->number_color ) ) {
		echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->number_color ) . ';';
	}
	?>
}

.fl-node-<?php echo $id; ?> .fl-countdown .fl-countdown-unit-label {
	<?php
	if ( ! empty( $settings->label_color ) ) {
		echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->label_color ) . ';';
	}
	?>
}

<?php if ( isset( $settings->layout ) && 'default' == $settings->layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-countdown .fl-countdown-unit {
		<?php
		if ( isset( $settings->vertical_padding ) ) {
			echo 'padding-top: ' . $settings->vertical_padding . 'px;';
			echo 'padding-bottom: ' . $settings->vertical_padding . 'px;';
		}
		if ( isset( $settings->horizontal_padding ) ) {
			echo 'padding-left: ' . $settings->horizontal_padding . 'px;';
			echo 'padding-right: ' . $settings->horizontal_padding . 'px;';
		}
		if ( ! empty( $settings->number_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->number_bg_color ) . ';';
		}
		if ( isset( $settings->border_radius ) ) {
			echo 'border-radius: ' . $settings->border_radius . 'px;';
		}
		?>
	}

	<?php if ( 'yes' == $settings->show_separator && 'colon' == $settings->separator_type ) : ?>
		.fl-node-<?php echo $id; ?> .fl-countdown .fl-countdown-number:after {
			<?php
			if ( isset( $settings->number_spacing ) ) {
				echo 'width: ' . ( $settings->number_spacing * 2 ) . 'px;';
				echo 'right: -' . ( $settings->number_spacing * 2 ) . 'px;';
			}
			if ( isset( $settings->separator_color ) ) {
				echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->separator_color ) . ';';
			}

			?>
		}
	<?php endif; ?>

	<?php if ( 'yes' == $settings->show_separator && 'line' == $settings->separator_type ) : ?>
		.fl-node-<?php echo $id; ?> .fl-countdown .fl-countdown-number:after {
			<?php
			if ( isset( $settings->number_spacing ) ) {
				echo 'right: -' . $settings->number_spacing . 'px;';
			}
			if ( isset( $settings->separator_color ) ) {
				echo 'border-color: ' . FLBuilderColor::hex_or_rgb( $settings->separator_color ) . ';';
			}

			?>
		}
	<?php endif; ?>

<?php elseif ( isset( $settings->layout ) && 'circle' == $settings->layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-countdown-unit{
		position: absolute;
		top: 50%;
		left: 50%;
		-webkit-transform: translate(-50%,-50%);
			-moz-transform: translate(-50%,-50%);
			-ms-transform: translate(-50%,-50%);
				transform: translate(-50%,-50%);
	}
	.fl-node-<?php echo $id; ?> .fl-countdown-number{
		<?php
		if ( ! empty( $settings->circle_width ) ) {
			echo 'width: ' . $settings->circle_width . 'px;';
			echo 'height: ' . $settings->circle_width . 'px;';
		} else {
			echo 'max-width: 100px;';
			echo 'max-height: 100px;';
		}
		?>
	}
	.fl-node-<?php echo $id; ?> .fl-countdown-circle-container{
		<?php
		if ( ! empty( $settings->circle_width ) ) {
			echo 'max-width: ' . $settings->circle_width . 'px;';
			echo 'max-height: ' . $settings->circle_width . 'px;';
		} else {
			echo 'max-width: 100px;';
			echo 'max-height: 100px;';
		}
		?>
	}

	.fl-node-<?php echo $id; ?> .fl-countdown .svg circle{
	<?php
	if ( ! empty( $settings->circle_dash_width ) ) {
		echo 'stroke-width: ' . $settings->circle_dash_width . 'px;';
	}
	?>
	}

	.fl-node-<?php echo $id; ?> .fl-countdown .svg .fl-number-bg{
	<?php
	if ( ! empty( $settings->circle_bg_color ) ) {
		echo 'stroke: ' . FLBuilderColor::hex_or_rgb( $settings->circle_bg_color ) . ';';
	} else {
		echo 'stroke: transparent;';
	}
	?>
	}

	.fl-node-<?php echo $id; ?> .fl-countdown .svg .fl-number{
	<?php
	if ( ! empty( $settings->circle_color ) ) {
		echo 'stroke: ' . FLBuilderColor::hex_or_rgb( $settings->circle_color ) . ';';
	} else {
		echo 'stroke: transparent;';
	}
	?>
	}
<?php endif; ?>
