<?php
function woo_ce_clean_html( $content ) {

	if( function_exists( 'mb_convert_encoding' ) ) {
		$output_encoding = 'ISO-8859-1';
		$content = mb_convert_encoding( trim( $content ), 'UTF-8', $output_encoding );
	} else {
		$content = trim( $content );
	}
	// $content = str_replace( ',', '&#44;', $content );
	// $content = str_replace( "\n", '<br />', $content );

	return $content;

}

if( !function_exists( 'escape_csv_value' ) ) {
	function escape_csv_value( $value ) {

		$value = str_replace( '"', '""', $value ); // First off escape all " and make them ""
		$value = str_replace( PHP_EOL, ' ', $value );
		return '"' . $value . '"'; // If I have new lines or commas escape them

	}
}

function woo_ce_display_memory( $memory = 0 ) {

	$output = '-';
	if( !empty( $output ) )
		$output = sprintf( __( '%s MB', 'woo_ce' ), $memory );
	echo $output;

}

function woo_ce_display_time_elapsed( $from, $to ) {

	$output = __( '1 second', 'woo_ce' );
	$time = $to - $from;
	$tokens = array (
		31536000 => __( 'year', 'woo_ce' ),
		2592000 => __( 'month', 'woo_ce' ),
		604800 => __( 'week', 'woo_ce' ),
		86400 => __( 'day', 'woo_ce' ),
		3600 => __( 'hour', 'woo_ce' ),
		60 => __( 'minute', 'woo_ce' ),
		1 => __( 'second', 'woo_ce' )
	);
	foreach ($tokens as $unit => $text) {
		if ($time < $unit) continue;
		$numberOfUnits = floor($time / $unit);
		$output = $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
	}
	return $output;

}

// This function escapes all cells in 'Excel' CSV escape formatting of a CSV file, also converts HTML entities to plain-text
function woo_ce_escape_csv_value( $value = '', $delimiter = ',', $format = 'all' ) {

	$output = $value;
	if( !empty( $output ) ) {
		$output = str_replace( '"', '""', $output );
		// output = str_replace( PHP_EOL, ' ', $output );
		$output = wp_specialchars_decode( $output );
		$output = str_replace( PHP_EOL, "\r\n", $output );
		switch( $format ) {
	
			case 'all':
				$output = '"' . $output . '"';
				break;

			case 'excel':
				if( strstr( $output, $delimiter ) !== false || strstr( $output, "\r\n" ) !== false )
					$output = '"' . $output . '"';
				break;
	
		}
	}
	return $output;

}

function woo_ce_count_object( $object = 0, $exclude_post_types = array() ) {

	$count = 0;
	if( is_object( $object ) ) {
		if( $exclude_post_types ) {
			$size = count( $exclude_post_types );
			for( $i = 0; $i < $size; $i++ ) {
				if( isset( $object->$exclude_post_types[$i] ) )
					unset( $object->$exclude_post_types[$i] );
			}
		}
		foreach( $object as $key => $item )
			$count = $item + $count;
	} else {
		$count = $object;
	}
	return $count;

}

function woo_ce_convert_product_ids( $product_ids = null ) {

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

function woo_ce_format_visibility( $visibility = '' ) {

	$output = '';
	if( $visibility ) {
		switch( $visibility ) {

			case 'visible':
				$output = __( 'Catalog & Search', 'woo_ce' );
				break;

			case 'catalog':
				$output = __( 'Catalog', 'woo_ce' );
				break;

			case 'search':
				$output = __( 'Search', 'woo_ce' );
				break;

			case 'hidden':
				$output = __( 'Hidden', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_product_status( $product_status ) {

	$output = $product_status;
	switch( $product_status ) {

		case 'publish':
			$output = __( 'Publish', 'woo_ce' );
			break;

		case 'draft':
			$output = __( 'Draft', 'woo_ce' );
			break;

		case 'trash':
			$output = __( 'Trash', 'woo_ce' );
			break;

	}
	return $output;

}

function woo_ce_format_comment_status( $comment_status ) {

	$output = $comment_status;
	switch( $comment_status ) {

		case 'open':
			$output = __( 'Open', 'woo_ce' );
			break;

		case 'closed':
			$output = __( 'Closed', 'woo_ce' );
			break;

	}
	return $output;

}

function woo_ce_format_switch( $input = '', $output_format = 'answer' ) {

	$input = strtolower( $input );
	switch( $input ) {

		case '1':
		case 'yes':
		case 'on':
		case 'open':
		case 'active':
			$input = '1';
			break;

		case '0':
		case 'no':
		case 'off':
		case 'closed':
		case 'inactive':
		default:
			$input = '0';
			break;

	}
	$output = '';
	switch( $output_format ) {

		case 'int':
			$output = $input;
			break;

		case 'answer':
			switch( $input ) {

				case '1':
					$output = __( 'Yes', 'woo_ce' );
					break;

				case '0':
					$output = __( 'No', 'woo_ce' );
					break;

			}
			break;

		case 'boolean':
			switch( $input ) {

				case '1':
					$output = 'on';
					break;

				case '0':
					$output = 'off';
					break;

			}
			break;

	}
	return $output;

}

function woo_ce_format_stock_status( $stock_status = '' ) {

	$output = '';
	if( $stock_status ) {
		switch( $stock_status ) {

			case 'instock':
				$output = __( 'In Stock', 'woo_ce' );
				break;

			case 'outofstock':
				$output = __( 'Out of Stock', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_tax_status( $tax_status = null ) {

	$output = '';
	if( $tax_status ) {
		switch( $tax_status ) {
	
			case 'taxable':
				$output = __( 'Taxable', 'woo_ce' );
				break;
	
			case 'shipping':
				$output = __( 'Shipping Only', 'woo_ce' );
				break;

			case 'none':
				$output = __( 'None', 'woo_ce' );
				break;

		}
	}
	return $output;

}

function woo_ce_format_tax_class( $tax_class = '' ) {

	global $export;

	$output = '';
	if( $tax_class ) {
		switch( $tax_class ) {

			case '*':
				$tax_class = __( 'Standard', 'woo_ce' );
				break;

			case 'reduced-rate':
				$tax_class = __( 'Reduced Rate', 'woo_ce' );
				break;

			case 'zero-rate':
				$tax_class = __( 'Zero Rate', 'woo_ce' );
				break;

		}
		$output = $tax_class;
	}
	return $output;

}

function woo_ce_format_product_filters( $product_filters = array() ) {

	$output = array();
	if( !empty( $product_filters ) ) {
		foreach( $product_filters as $product_filter ) {
			$output[] = $product_filter;
		}
	}
	return $output;

}

function woo_ce_format_product_type( $type_id = '' ) {

	$output = $type_id;
	if( $output ) {
		$product_types = apply_filters( 'woo_ce_format_product_types', array(
			'simple' => __( 'Simple', 'woocommerce' ),
			'downloadable' => __( 'Downloadable', 'woocommerce' ),
			'grouped' => __( 'Grouped', 'woocommerce' ),
			'virtual' => __( 'Virtual', 'woocommerce' ),
			'variable' => __( 'Variable', 'woocommerce' ),
			'external' => __( 'External / Affiliate', 'woocommerce' ),
			'variation' => __( 'Variation', 'woo_ce' )
		) );
		if( isset( $product_types[$type_id] ) )
			$output = $product_types[$type_id];
	}
	return $output;

}

function woo_ce_format_sale_price_dates( $sale_date = '' ) {

	$output = $sale_date;
	if( $sale_date )
		$output = date( 'd/m/Y', $sale_date );
	return $output;

}

if( !function_exists( 'woo_ce_expand_state_name' ) ) {
	function woo_ce_expand_state_name( $country_prefix = '', $state_prefix = '' ) {

		$output = $state_prefix;
		if( $output ) {
			$countries = new WC_Countries();
			$states = $countries->get_states( $country_prefix );
			if( $state = $states[$state_prefix] )
				$output = $state;
		}
		return $output;

	}
}

if( !function_exists( 'woo_ce_expand_country_name' ) ) {
	function woo_ce_expand_country_name( $country_prefix = '' ) {

		$output = $country_prefix;
		if( $output ) {
			$countries = new WC_Countries();
			if( $country = $countries->countries[$country_prefix] )
				$output = $country;
		}
		return $output;

	}
}
?>