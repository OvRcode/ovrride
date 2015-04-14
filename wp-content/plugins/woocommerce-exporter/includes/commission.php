<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	// HTML template for disabled Filter Commissions by Commission Date widget on Store Exporter screen
	function woo_ce_commissions_filter_by_date() {

		$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
		$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

		$today = date( 'l' );
		$yesterday = date( 'l', strtotime( '-1 days' ) );
		$current_month = date( 'F' );
		$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
		$commission_dates_variable = '';
		$commission_dates_variable_length = '';
		$commission_dates_from = woo_ce_get_commission_first_date();
		$commission_dates_to = date( 'd/m/Y' );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-date" /> <?php _e( 'Filter Commissions by Commission Date', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></p>
<div id="export-commissions-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="today" disabled="disabled" /> <?php _e( 'Today', 'woo_ce' ); ?> (<?php echo $today; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="yesterday" disabled="disabled" /> <?php _e( 'Yesterday', 'woo_ce' ); ?> (<?php echo $yesterday; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="current_week" disabled="disabled" /> <?php _e( 'Current week', 'woo_ce' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_week" disabled="disabled" /> <?php _e( 'Last week', 'woo_ce' ); ?></label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="current_month" disabled="disabled" /> <?php _e( 'Current month', 'woo_ce' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_month" disabled="disabled" /> <?php _e( 'Last month', 'woo_ce' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
<!--
		<li>
			<label><input type="radio" name="commission_dates_filter" value="last_quarter" disabled="disabled" /> <?php _e( 'Last quarter', 'woo_ce' ); ?> (Nov. - Jan.)</label>
		</li>
-->
		<li>
			<label><input type="radio" name="commission_dates_filter" value="variable" disabled="disabled" /> <?php _e( 'Variable date', 'woo_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<?php _e( 'Last', 'woo_ce' ); ?>
				<input type="text" name="commission_dates_filter_variable" class="text code" size="4" maxlength="4" value="<?php echo $commission_dates_variable; ?>" disabled="disabled" />
				<select name="commission_dates_filter_variable_length" style="vertical-align:top;">
					<option value=""<?php selected( $commission_dates_variable_length, '' ); ?>>&nbsp;</option>
					<option value="second"<?php selected( $commission_dates_variable_length, 'second' ); ?> disabled="disabled"><?php _e( 'second(s)', 'woo_ce' ); ?></option>
					<option value="minute"<?php selected( $commission_dates_variable_length, 'minute' ); ?> disabled="disabled"><?php _e( 'minute(s)', 'woo_ce' ); ?></option>
					<option value="hour"<?php selected( $commission_dates_variable_length, 'hour' ); ?> disabled="disabled"><?php _e( 'hour(s)', 'woo_ce' ); ?></option>
					<option value="day"<?php selected( $commission_dates_variable_length, 'day' ); ?> disabled="disabled"><?php _e( 'day(s)', 'woo_ce' ); ?></option>
					<option value="week"<?php selected( $commission_dates_variable_length, 'week' ); ?> disabled="disabled"><?php _e( 'week(s)', 'woo_ce' ); ?></option>
					<option value="month"<?php selected( $commission_dates_variable_length, 'month' ); ?> disabled="disabled"><?php _e( 'month(s)', 'woo_ce' ); ?></option>
					<option value="year"<?php selected( $commission_dates_variable_length, 'year' ); ?> disabled="disabled"><?php _e( 'year(s)', 'woo_ce' ); ?></option>
				</select>
			</div>
		</li>
		<li>
			<label><input type="radio" name="commission_dates_filter" value="manual" disabled="disabled" /> <?php _e( 'Fixed date', 'woo_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="commission_dates_from" name="commission_dates_from" value="<?php echo esc_attr( $commission_dates_from ); ?>" class="text code datepicker" disabled="disabled" /> to <input type="text" size="10" maxlength="10" id="commission_dates_to" name="commission_dates_to" value="<?php echo esc_attr( $commission_dates_to ); ?>" class="text code datepicker" disabled="disabled" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first Commission to today.', 'woo_ce' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-commissions-filters-date -->
<?php
		ob_end_flush();

	}

	// Returns date of first Commission received, any status
	function woo_ce_get_commission_first_date() {

		$output = date( 'd/m/Y', mktime( 0, 0, 0, date( 'n' ), 1 ) );
		$post_type = 'shop_commission';
		$args = array(
			'post_type' => $post_type,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'numberposts' => 1
		);
		$commissions = get_posts( $args );
		if( $commissions ) {
			$commission = strtotime( $commissions[0]->post_date );
			$output = date( 'd/m/Y', $commission );
			unset( $commissions, $commission );
		}
		return $output;

	}

	// HTML template for disabled Commission Sorting widget on Store Exporter screen
	function woo_ce_commission_sorting() {

		ob_start(); ?>
<p><label><?php _e( 'Commission Sorting', 'woo_ce' ); ?></label></p>
<div>
	<select name="commission_orderby" disabled="disabled">
		<option value="ID"><?php _e( 'Commission ID', 'woo_ce' ); ?></option>
		<option value="title"><?php _e( 'Commission Title', 'woo_ce' ); ?></option>
		<option value="date"><?php _e( 'Date Created', 'woo_ce' ); ?></option>
		<option value="modified"><?php _e( 'Date Modified', 'woo_ce' ); ?></option>
		<option value="rand"><?php _e( 'Random', 'woo_ce' ); ?></option>
	</select>
	<select name="commission_order" disabled="disabled">
		<option value="ASC"><?php _e( 'Ascending', 'woo_ce' ); ?></option>
		<option value="DESC"><?php _e( 'Descending', 'woo_ce' ); ?></option>
	</select>
	<p class="description"><?php _e( 'Select the sorting of Commissions within the exported file. By default this is set to export Commissions by Commission ID in Desending order.', 'woo_ce' ); ?></p>
</div>
<?php
		ob_end_flush();

	}

	// HTML template for disabled Filter Commissions by Product Vendor widget on Store Exporter screen
	function woo_ce_commissions_filter_by_product_vendor() {

		$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
		$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

		$product_vendors = woo_ce_get_product_vendors( array(), 'full' );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-product_vendor" /> <?php _e( 'Filter Commissions by Product Vendors', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></p>
<div id="export-commissions-filters-product_vendor" class="separator">
<?php if( $product_vendors ) { ?>
	<ul>
	<?php foreach( $product_vendors as $product_vendor ) { ?>
		<li>
			<label><input type="checkbox" name="commission_filter_product_vendor[<?php echo $product_vendor->term_id; ?>]" value="<?php echo $product_vendor->term_id; ?>" title="<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_vendor->term_id ); ?>"<?php disabled( $product_vendor->count, 0 ); ?> disabled="disabled" /> <?php echo $product_vendor->name; ?></label>
			<span class="description">(<?php echo $product_vendor->count; ?>)</span>
		</li>
	<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Product Vendors you want to filter exported Commissions by. Default is to include all Product Vendors.', 'woo_ce' ); ?></p>
<?php } else { ?>
	<p><?php _e( 'No Product Vendors were found.', 'woo_ce' ); ?></p>
<?php } ?>
</div>
<!-- #export-commissions-filters-product_vendor -->
<?php
		ob_end_flush();

	}

	// HTML template for disabled Filter Commissions by Commission Status widget on Store Exporter screen
	function woo_ce_commissions_filter_by_commission_status() {

		$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
		$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

		ob_start(); ?>
<p><label><input type="checkbox" id="commissions-filters-commission_status" /> <?php _e( 'Filter Commissions by Commission Status', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></p>
<div id="export-commissions-filters-commission_status" class="separator">
	<ul>
		<li>
			<label><input type="checkbox" name="commission_filter_commission_status[]" value="unpaid"<?php disabled( woo_ce_commissions_stock_status_count( 'unpaid' ), 0 ); ?> disabled="disabled" /> <?php _e( 'Unpaid', 'woo_ce' ); ?></label>
			<span class="description">(<?php echo woo_ce_commissions_stock_status_count( 'unpaid' ); ?>)</span>
		</li>
		<li>
			<label><input type="checkbox" name="commission_filter_commission_status[]" value="paid"<?php disabled( woo_ce_commissions_stock_status_count( 'paid' ), 0 ); ?> disabled="disabled" /> <?php _e( 'Paid', 'woo_ce' ); ?></label>
			<span class="description">(<?php echo woo_ce_commissions_stock_status_count( 'paid' ); ?>)</span>
		</li>
	</ul>
	<p class="description"><?php _e( 'Select the Commission Status you want to filter exported Commissions by. Default is to include all Commission Statuses.', 'woo_ce' ); ?></p>
</div>
<!-- #export-commissions-filters-commission_status -->
<?php
		ob_end_flush();

	}

	// HTML template for displaying the number of each export type filter on the Archives screen
	function woo_ce_commissions_stock_status_count( $type = '' ) {

		$output = 0;
		$post_type = 'shop_commission';
		$meta_key = '_paid_status';
		$args = array(
			'post_type' => $post_type,
			'meta_key' => $meta_key,
			'meta_value' => null,
			'numberposts' => -1,
			'fields' => 'ids'
		);
		if( $type )
			$args['meta_value'] = $type;
		$commission_ids = new WP_Query( $args );
		if( !empty( $commission_ids->posts ) )
			$output = count( $commission_ids->posts );
		return $output;

	}

	/* End of: WordPress Administration */

}

function woo_ce_get_commission_fields( $format = 'full' ) {

	$export_type = 'commission';

	$fields = array();
	$fields[] = array(
		'name' => 'ID',
		'label' => __( 'Commission ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'post_date',
		'label' => __( 'Commission Date', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'title',
		'label' => __( 'Commission Title', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_id',
		'label' => __( 'Product ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_name',
		'label' => __( 'Product Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_sku',
		'label' => __( 'Product SKU', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_vendor_id',
		'label' => __( 'Product Vendor ID', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'product_vendor_name',
		'label' => __( 'Product Vendor Name', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'commission_amount',
		'label' => __( 'Commission Amount', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'paid_status',
		'label' => __( 'Commission Status', 'woo_ce' )
	);
	$fields[] = array(
		'name' => 'post_status',
		'label' => __( 'Post Status', 'woo_ce' )
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
?>