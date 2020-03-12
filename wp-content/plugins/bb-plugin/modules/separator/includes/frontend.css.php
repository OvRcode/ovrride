.fl-node-<?php echo $id; ?> .fl-separator {
	border-top:<?php echo $settings->height; ?>px <?php echo $settings->style; ?> <?php echo FLBuilderColor::hex_or_rgb( $settings->color ); ?>;
	max-width: <?php echo $settings->width . $settings->width_unit; ?>;
	margin: <?php echo $settings->align; ?>;
}
