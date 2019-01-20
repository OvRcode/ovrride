<?php foreach ( $addon['options'] as $key => $option ) :
	$addon_key     = 'addon-' . sanitize_title( $addon['field-name'] );
	$option_key    = empty( $option['label'] ) ? $key : sanitize_title( $option['label'] );
	$current_value = isset( $_POST[ $addon_key ] ) && isset( $_POST[ $addon_key ][ $option_key ] ) ? wc_clean( $_POST[ $addon_key ][ $option_key ] ) : '';
	$price = apply_filters( 'woocommerce_product_addons_option_price',
		$option['price'] ? '(' . wc_price( WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ) ) . ')' : '',
		$option,
		$key,
		'custom_textarea'
	);
	?>

	<p class="form-row form-row-wide addon-wrap-<?php echo sanitize_title( $addon['field-name'] ); ?>">
		<?php if ( ! empty( $option['label'] ) ) : ?>
			<label><?php echo wptexturize( $option['label'] ) . ' ' . $price; ?></label>
		<?php endif; ?>
		<textarea type="text" class="input-text addon addon-custom-textarea" data-raw-price="<?php echo esc_attr( $option['price'] ); ?>" data-price="<?php echo WC_Product_Addons_Helper::get_product_addon_price_for_display( $option['price'] ); ?>" name="<?php echo $addon_key ?>[<?php echo $option_key; ?>]" rows="4" cols="20" <?php if ( ! empty( $option['max'] ) ) echo 'maxlength="' . $option['max'] .'"'; ?>><?php echo esc_textarea( $current_value ); ?></textarea>
	</p>

<?php endforeach; ?>
