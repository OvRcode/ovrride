<?php
function woo_cd_convert_product_ids( $product_ids = null ) {

	global $export;

	$output = '';
	if( $product_ids ) {
		if( is_array( $product_ids ) ) {
			$size = count( $product_ids );
			for( $i = 0; $i < $size; $i++ )
				$output .= $product_ids[$i] . $export->category_separator;
			$output = substr( $output, 0, -1 );
		} else if( strstr( $product_ids, ',' ) ) {
			$output = str_replace( ',', $export->category_separator, $product_ids );
		}
	}
	return $output;

}

function woo_cd_format_discount_type( $discount_type = '' ) {

	$output = $discount_type;
	switch( $discount_type ) {

		case 'fixed_cart':
			$output = __( 'Cart Discount', 'woo_cd' );
			break;

		case 'percent':
			$output = __( 'Cart % Discount', 'woo_cd' );
			break;

		case 'fixed_product':
			$output = __( 'Product Discount', 'woo_cd' );
			break;

		case 'percent_product':
			$output = __( 'Product % Discount', 'woo_cd' );
			break;

	}
	return $output;

}

function woo_cd_format_order_status( $status_id = '' ) {

	$output = $status_id;
	$order_statuses = woo_ce_get_order_statuses();
	if( $order_statuses ) {
		foreach( $order_statuses as $order_status ) {
			if( $order_status->name == $status_id )
				$output = ucfirst( $order_status->name );
		}
	}
	return $output;

}

function woo_cd_format_order_payment_gateway( $payment_id = '' ) {

	global $woocommerce;

	$output = $payment_id;
	if( $woocommerce->payment_gateways() ) {
		$payment_gateways = $woocommerce->payment_gateways()->payment_gateways();
		foreach( $payment_gateways as $payment_gateway ) {
			if( $payment_gateway->id == $payment_id )
				$output = $payment_gateway->get_title();
		}
		if( empty( $payment_id ) )
			$output = __( 'N/A', 'woo_cd' );
	}
	return $output;

}

function woo_cd_format_shipping_method( $shipping_id = '' ) {

	global $woocommerce;

	$output = $shipping_id;
	if( $woocommerce->shipping() ) {
		$shipping_methods = $woocommerce->shipping->load_shipping_methods();
		foreach( $shipping_methods as $shipping_method ) {
			if( $shipping_method->id == $shipping_id )
				$output = $shipping_method->get_title();
		}
		if( empty( $shipping_id ) )
			$output = __( 'N/A', 'woo_cd' );
	}
	return $output;

}

function woo_cd_format_order_item_type( $line_type = '' ) {

	$output = $line_type;
	switch( $line_type ) {

		case 'line_item':
			$output = __( 'Product', 'woo_cd' );
			break;

		case 'fee':
			$output = __( 'Fee', 'woo_cd' );
			break;

	}
	return $output;

}

function woo_cd_format_order_item_tax_class( $tax_class = '' ) {

	$output = $tax_class;
	switch( $tax_class ) {

		case 'zero-rate':
			$output = __( 'Zero Rate', 'woo_cd' );
			break;

		case 'reduced-rate':
			$output = __( 'Reduced Rate', 'woo_cd' );
			break;

		case '':
			$output = __( 'Standard', 'woo_cd' );
			break;

		case '0':
			$output = __( 'N/A', 'woo_cd' );
			break;

	}
	return $output;

}
?>