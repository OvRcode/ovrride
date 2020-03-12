/*Features Min Height*/
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-features  {
	min-height: <?php echo $settings->min_height; ?>px;
}

<?php
// Loop through and style each pricing box
for ( $i = 0; $i < count( $settings->pricing_columns ); $i++ ) :

	if ( ! is_object( $settings->pricing_columns[ $i ] ) ) {
		continue;
	}

	// Pricing Box Settings
	$pricing_column = $settings->pricing_columns[ $i ];

	?>

/*Pricing Box Style*/
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> {
	border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $pricing_column->background, 30, 'darken' ) ); ?>;
	background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->background ); ?>;
	margin-top: <?php echo $pricing_column->margin; ?>px;
}
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-inner-wrap {
	background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->foreground ); ?>;
	border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( FLBuilderColor::adjust_brightness( $pricing_column->background, 30, 'darken' ) ); ?>;
}
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> h2 {
	font-size: <?php echo $pricing_column->title_size; ?>px;
}
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
	font-size: <?php echo $pricing_column->price_size; ?>px;
}

/*Pricing Box Highlight*/
	<?php if ( 'price' == $settings->highlight ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
	background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?>;
	color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_color ); ?>;
}
<?php elseif ( 'title' == $settings->highlight ) : ?>

.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-title {
	background: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?>;
	color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_color ); ?>;
}
<?php endif; ?>

/*Fix when price is NOT highlighted*/
	<?php if ( 'title' == $settings->highlight || 'none' == $settings->highlight ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
	margin-bottom: 0;
	padding-bottom: 0;
}
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-features {
	margin-top: 10px;
}
<?php endif; ?>

/*Fix when NOTHING is highlighted*/
	<?php if ( 'none' == $settings->highlight ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-title {
	padding-bottom: 0;
}
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> .fl-pricing-table-price {
	padding-top: 0;
}
<?php endif; ?>

/*Button CSS*/
.fl-builder-content .fl-node-<?php echo $id; ?> .fl-pricing-table-column-<?php echo $i; ?> a.fl-button {

	<?php if ( empty( $pricing_column->btn_bg_color ) ) : ?>
		background-color: <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?> !important;
		border: 1px solid <?php echo FLBuilderColor::hex_or_rgb( $pricing_column->column_background ); ?> !important;
	<?php endif; ?>

	<?php if ( empty( $pricing_column->btn_width ) ) : ?>
		display:block;
		margin: 0 30px 5px;
	<?php endif; ?>
}

	<?php FLBuilder::render_module_css( 'button', $id . ' .fl-pricing-table-column-' . $i, $module->get_button_settings( $pricing_column ) ); ?>

<?php endfor; ?>
