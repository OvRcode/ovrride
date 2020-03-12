<?php

FLBuilderCSS::responsive_rule( array(
	'settings'     => $settings,
	'setting_name' => 'number_size',
	'selector'     => ".fl-node-$id .fl-number-string",
	'prop'         => 'font-size',
) );

?>

<?php if ( ! empty( $settings->number_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-number-string{
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->number_color ); ?>;
	}
<?php endif; ?>

<?php if ( ! empty( $settings->text_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-number-before-text,
	.fl-node-<?php echo $id; ?> .fl-module-content .fl-number-after-text{
		color: <?php echo FLBuilderColor::hex_or_rgb( $settings->text_color ); ?>;
	}
<?php endif; ?>


<?php if ( isset( $settings->layout ) && 'circle' == $settings->layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-number .fl-number-text{
		position: absolute;
		top: 50%;
		left: 50%;
		-webkit-transform: translate(-50%,-50%);
			-moz-transform: translate(-50%,-50%);
			-ms-transform: translate(-50%,-50%);
				transform: translate(-50%,-50%);
	}
	.fl-node-<?php echo $id; ?> .fl-number-circle-container{
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

	.fl-node-<?php echo $id; ?> .svg circle{
	<?php
	if ( ! empty( $settings->circle_dash_width ) ) {
		echo 'stroke-width: ' . $settings->circle_dash_width . 'px;';
	}
	?>
	}

	.fl-node-<?php echo $id; ?> .svg .fl-bar-bg{
	<?php
	if ( ! empty( $settings->circle_bg_color ) ) {
		echo 'stroke: ' . FLBuilderColor::hex_or_rgb( $settings->circle_bg_color ) . ';';
	} else {
		echo 'stroke: transparent;';
	}
	?>
	}

	.fl-node-<?php echo $id; ?> .svg .fl-bar{
	<?php
	if ( ! empty( $settings->circle_color ) ) {
		echo 'stroke: ' . FLBuilderColor::hex_or_rgb( $settings->circle_color ) . ';';
	} else {
		echo 'stroke: transparent;';
	}
	?>
	}
<?php elseif ( isset( $settings->layout ) && 'bars' == $settings->layout ) : ?>
	.fl-node-<?php echo $id; ?> .fl-number-bars-container{
		width: 100%;
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->bar_bg_color ); ?>;
	}
	.fl-node-<?php echo $id; ?> .fl-number-bar{
		width: 0;
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->bar_color ); ?>;
		<?php if ( empty( $settings->number ) ) : ?>
		padding-left: 0px;
		padding-right: 0px;
		<?php endif; ?>
	}
<?php endif; ?>
