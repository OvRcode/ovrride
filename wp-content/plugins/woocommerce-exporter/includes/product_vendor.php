<?php
function woo_ce_get_product_vendor_fields( $format = 'full' ) {

	$export_type = 'product_vendor';

	$fields = array();
	$fields[] = array(
		'name' => 'ID',
		'label' => __( 'Product Vendor ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'title',
		'label' => __( 'Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'slug',
		'label' => __( 'Slug', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'description',
		'label' => __( 'Description', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'url',
		'label' => __( 'Product Vendor URL', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'commission',
		'label' => __( 'Commission', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'paypal_email',
		'label' => __( 'PayPal E-mail Address', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_name',
		'label' => __( 'Vendor Username', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'user_id',
		'label' => __( 'Vendor User ID', 'woo_ce' )
	);

/*
	$fields[] = array(
		'name' => '',
		'label' => __( '', 'woo_ce' )
	);
*/

	// Allow Plugin/Theme authors to add support for additional columns
	$fields = apply_filters( 'woo_ce_' . $export_type . '_fields', $fields, $export_type );

	switch( $format ) {

		case 'summary':
			$output = array();
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $fields[$i] ) )
					$output[$fields[$i]['name']] = 'on';
			}
			return $output;
			break;

		case 'full':
		default:
			$sorting = woo_ce_get_option( $export_type . '_sorting', array() );
			$size = count( $fields );
			for( $i = 0; $i < $size; $i++ ) {
				$fields[$i]['reset'] = $i;
				$fields[$i]['order'] = ( isset( $sorting[$fields[$i]['name']] ) ? $sorting[$fields[$i]['name']] : $i );
			}
			// Check if we are using PHP 5.3 and above
			if( version_compare( phpversion(), '5.3' ) >= 0 )
				usort( $fields, woo_ce_sort_fields( 'order' ) );
			return $fields;
			break;

	}

}

function woo_ce_override_product_vendor_field_labels( $fields = array() ) {

	$labels = woo_ce_get_option( 'product_vendor_labels', array() );
	if( !empty( $labels ) ) {
		foreach( $fields as $key => $field ) {
			if( isset( $labels[$field['name']] ) )
				$fields[$key]['label'] = $labels[$field['name']];
		}
	}
	return $fields;

}
add_filter( 'woo_ce_product_vendor_fields', 'woo_ce_override_product_vendor_field_labels', 11 );

// Returns a list of Product Vendor Term IDs
function woo_ce_get_product_vendors( $args = array(), $output = 'term_id' ) {

	global $export;

	$term_taxonomy = 'shop_vendor';
	$defaults = array(
		'orderby' => 'name',
		'order' => 'ASC',
		'hide_empty' => 0
	);
	$args = wp_parse_args( $args, $defaults );
	$product_vendors = get_terms( $term_taxonomy, $args );
	if( !empty( $product_vendors ) && is_wp_error( $product_vendors ) == false ) {
		if( $output == 'term_id' ) {
			$vendor_ids = array();
			foreach( $product_vendors as $key => $product_vendor )
				$vendor_ids[] = $product_vendor->term_id;
			$export->total_rows = count( $vendor_ids );
			unset( $product_vendors, $product_vendor );
			return $vendor_ids;
		} else if( $output == 'full' ) {
			return $product_vendors;
		}
	}

}
?>