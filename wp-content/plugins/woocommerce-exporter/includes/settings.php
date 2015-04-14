<?php
function woo_ce_export_settings_quicklinks() {

	ob_start(); ?>
<li>| <a href="#xml-settings"><?php _e( 'XML Settings', 'woo_ce' ); ?></a> |</li>
<li><a href="#rss-settings"><?php _e( 'RSS Settings', 'woo_ce' ); ?></a> |</li>
<li><a href="#scheduled-exports"><?php _e( 'Scheduled Exports', 'woo_ce' ); ?></a> |</li>
<li><a href="#cron-exports"><?php _e( 'CRON Exports', 'woo_ce' ); ?></a> |</li>
<li><a href="#orders-screen"><?php _e( 'Orders Screen', 'woo_ce' ); ?></a></li>
<?php
	ob_end_flush();

}

function woo_ce_export_settings_csv() {

	$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

	ob_start(); ?>
<tr>
	<th>
		<label for="header_formatting"><?php _e( 'Header formatting', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" name="header_formatting" value="1"<?php checked( 1, 1 ); ?> />&nbsp;<?php _e( 'Include export field column headers', 'woo_ce' ); ?></label></li>
			<li><label><input type="radio" name="header_formatting" value="0" disabled="disabled" />&nbsp;<?php _e( 'Do not include export field column headers', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
		</ul>
		<p class="description"><?php _e( 'Choose the header format that suits your spreadsheet software (e.g. Excel, OpenOffice, etc.). This rule applies to CSV, XLS and XLSX export types.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();
	
}

// Returns the disabled HTML template for the Enable CRON and Secret Export Key options for the Settings screen
function woo_ce_export_settings_cron() {

	$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

	// RSS settings
	$rss_title = __( 'Title of your RSS feed', 'woo_ce' );
	$rss_link = __( 'URL to your RSS feed', 'woo_ce' );
	$rss_description = __( 'Summary description of your RSS feed', 'woo_ce' );

	// Scheduled exports
	$auto_commence_date = date( 'd/m/Y H:i', current_time( 'timestamp', 1 ) );
	// Override to enable the Export Type to include all export types
	$types = array(
		'product' => __( 'Products', 'woo_ce' ),
		'category' => __( 'Categories', 'woo_ce' ),
		'tag' => __( 'Tags', 'woo_ce' ),
		'brand' => __( 'Brands', 'woo_ce' ),
		'order' => __( 'Orders', 'woo_ce' ),
		'customer' => __( 'Customers', 'woo_ce' ),
		'user' => __( 'Users', 'woo_ce' ),
		'coupon' => __( 'Coupons', 'woo_ce' ),
		'subscription' => __( 'Subscriptions', 'woo_ce' ),
		'product_vendor' => __( 'Product Vendors', 'woo_ce' ),
		'shipping_class' => __( 'Shipping Classes', 'woo_ce' )
	);
	$order_statuses = woo_ce_get_order_statuses();
	$product_types = woo_ce_get_product_types();
	$args = array(
		'hide_empty' => 1
	);
	$product_categories = woo_ce_get_product_categories( $args );
	$product_tags = woo_ce_get_product_tags( $args );

	$auto_interval = 1440;
	$auto_format = 'csv';
	$order_filter_date_variable = '';

	// Send to e-mail
	$email_to = get_option( 'admin_email', '' );
	$email_subject = __( '[%store_name%] Export: %export_type% (%export_filename%)', 'woo_ce' );

	// Post to remote URL
	$post_to = 'http://www.domain.com/custom-post-form-processor.php';

	// Export to FTP
	$ftp_method_host = 'ftp.domain.com';
	$ftp_method_port = '';
	$ftp_method_protocol = 'ftp';
	$ftp_method_user = 'export';
	$ftp_method_pass = '';
	$ftp_method_path = 'wp-content/uploads/export/';
	$ftp_method_filename = 'fixed-filename';
	$ftp_method_passive = 'auto';
	$ftp_method_timeout = '';

	$scheduled_fields = 'all';

	// CRON exports
	$secret_key = '-';
	$cron_fields = 'all';

	$cron_fields = 'all';

	// Orders Screen
	$order_actions_csv = 1;
	$order_actions_xml = 0;
	$order_actions_xls = 1;
	$order_actions_xlsx = 1;

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';

	ob_start(); ?>
<tr id="xml-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'XML Settings', 'woo_ce' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Attribute display', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="xml_attribute_url" value="1" disabled="disabled" /> <?php _e( 'Site Address', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_title" value="1" disabled="disabled" /> <?php _e( 'Site Title', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_date" value="1" disabled="disabled" /> <?php _e( 'Export Date', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_time" value="1" disabled="disabled" /> <?php _e( 'Export Time', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_export" value="1" disabled="disabled" /> <?php _e( 'Export Type', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_orderby" value="1" disabled="disabled" /> <?php _e( 'Export Order By', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_order" value="1" disabled="disabled" /> <?php _e( 'Export Order', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_limit" value="1" disabled="disabled" /> <?php _e( 'Limit Volume', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="xml_attribute_offset" value="1" disabled="disabled" /> <?php _e( 'Volume Offset', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different attributes in the XML export.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="rss-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-media-code"></div>&nbsp;<?php _e( 'RSS Settings', 'woo_ce' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_title"><?php _e( 'Title element', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="rss_title" type="text" id="rss_title" value="<?php echo esc_attr( $rss_title ); ?>" class="regular-text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		<p class="description"><?php _e( 'Defines the title of the data feed (e.g. Product export for WordPress Shop).', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_link"><?php _e( 'Link element', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="rss_link" type="text" id="rss_link" value="<?php echo esc_attr( $rss_link ); ?>" class="regular-text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		<p class="description"><?php _e( 'A link to your website, this doesn\'t have to be the location of the RSS feed.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="rss_description"><?php _e( 'Description element', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="rss_description" type="text" id="rss_description" value="<?php echo esc_attr( $rss_description ); ?>" class="large-text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		<p class="description"><?php _e( 'A description of your data feed.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="scheduled-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3>
			<div class="dashicons dashicons-calendar"></div>&nbsp;<?php _e( 'Scheduled Exports', 'woo_ce' ); ?>
		</h3>
		<p class="description"><?php _e( 'Configure Store Exporter Deluxe to automatically generate exports, apply filters to export just what you need.<br />Adjusting options within the Scheduling sub-section will after clicking Save Changes refresh the scheduled export engine, editing filters, formats, methods, etc. will not affect the scheduling of the current scheduled export.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_auto"><?php _e( 'Enable scheduled exports', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="enable_auto" name="enable_auto">
			<option value="1" disabled="disabled"><?php _e( 'Yes', 'woo_ce' ); ?></option>
			<option value="0" selected="selected"><?php _e( 'No', 'woo_ce' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Enabling Scheduled Exports will trigger automated exports at the interval specified under Once every (minutes).', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="auto_type"><?php _e( 'Export type', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="auto_type" name="auto_type">
<?php if( !empty( $types ) ) { ?>
	<?php foreach( $types as $key => $type ) { ?>
			<option value="<?php echo $key; ?>"><?php echo $type; ?></option>
	<?php } ?>
<?php } else { ?>
			<option value=""><?php _e( 'No export types were found.', 'woo_ce' ); ?></option>
<?php } ?>
		</select>
		<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Select the data type you want to export.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr class="auto_type_options">
	<th>
		<label><?php _e( 'Export filters', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product category', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></p>
<?php if( !empty( $product_categories ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Category...', 'woo_ce' ); ?>" name="product_filter_category[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_categories as $product_category ) { ?>
					<option><?php echo woo_ce_format_product_category_label( $product_category->name, $product_category->parent_name ); ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_category->term_id ); ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Categories were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Category\'s you want to filter exported Products by. Default is to include all Product Categories.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product tag', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></p>
<?php if( !empty( $product_tags ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Tag...', 'woo_ce' ); ?>" name="product_filter_tag[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_tags as $product_tag ) { ?>
					<option><?php echo $product_tag->name; ?> (<?php printf( __( 'Term ID: %d', 'woo_ce' ), $product_tag->term_id ); ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Tags were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Tag\'s you want to filter exported Products by. Default is to include all Product Tags.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Product type', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></p>
<?php if( !empty( $product_types ) ) { ?>
				<select data-placeholder="<?php _e( 'Choose a Product Type...', 'woo_ce' ); ?>" name="product_filter_type[]" multiple class="chzn-select" style="width:95%;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
					<option><?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)</option>
	<?php } ?>
				</select>
<?php } else { ?>
				<?php _e( 'No Product Types were found.', 'woo_ce' ); ?>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types and Variations.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options product-options">
				<p class="label"><?php _e( 'Stock status', 'woo_ce' ); ?></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" name="product_filter_stock" value="" disabled="disabled" /> <?php _e( 'Include both', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="product_filter_stock" value="instock" disabled="disabled" /> <?php _e( 'In stock', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="product_filter_stock" value="outofstock" disabled="disabled" /> <?php _e( 'Out of stock', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
				</ul>
				<p class="description"><?php _e( 'Select the Stock Status\'s you want to filter exported Products by. Default is to include all Stock Status\'s.', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Order date', 'woo_ce' ); ?></p>
				<ul style="margin-top:0.2em;">
					<li><label><input type="radio" name="order_dates_filter" value="" disabled="disabled" /><?php _e( 'All', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="order_dates_filter" value="today" disabled="disabled" /><?php _e( 'Today', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="order_dates_filter" value="yesterday" disabled="disabled" /><?php _e( 'Yesterday', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="order_dates_filter" value="current_week" disabled="disabled" /><?php _e( 'Current week', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="order_dates_filter" value="last_week" disabled="disabled" /><?php _e( 'Last week', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="order_dates_filter" value="current_month" disabled="disabled" /><?php _e( 'Current month', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li><label><input type="radio" name="order_dates_filter" value="last_month" disabled="disabled" /><?php _e( 'Last month', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
					<li>
						<label><input type="radio" name="order_dates_filter" value="variable" disabled="disabled" /><?php _e( 'Variable date', 'woo_ce' ); ?></label>
						<div style="margin-top:0.2em;">
							<?php _e( 'Last', 'woo_ce' ); ?>
							<input type="text" name="order_dates_filter_variable" class="text" size="4" value="<?php echo $order_filter_date_variable; ?>" disabled="disabled" />
							<select name="order_dates_filter_variable_length">
								<option value="">&nbsp;</option>
								<option value="second" disabled="disabled"><?php _e( 'second(s)', 'woo_ce' ); ?></option>
								<option value="minute" disabled="disabled"><?php _e( 'minute(s)', 'woo_ce' ); ?></option>
								<option value="hour" disabled="disabled"><?php _e( 'hour(s)', 'woo_ce' ); ?></option>
								<option value="day" disabled="disabled"><?php _e( 'day(s)', 'woo_ce' ); ?></option>
								<option value="week" disabled="disabled"><?php _e( 'week(s)', 'woo_ce' ); ?></option>
								<option value="month" disabled="disabled"><?php _e( 'month(s)', 'woo_ce' ); ?></option>
								<option value="year" disabled="disabled"><?php _e( 'year(s)', 'woo_ce' ); ?></option>
							</select>
							<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
						</div>
					</li>
					<li>
						<label><input type="radio" name="order_dates_filter" value="manual" disabled="disabled" /><?php _e( 'Fixed date', 'woo_ce' ); ?></label>
						<div style="margin-top:0.2em;">
							<input type="text" size="10" maxlength="10" class="text datepicker" /> to <input type="text" size="10" maxlength="10" class="text datepicker" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
						</div>
					</li>
				</ul>
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. If manually selecting dates ensure the Fixed date radio field is checked, likewise for variable dates. Default is to include all Orders made.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Order status', 'woo_ce' ); ?></p>
				<select data-placeholder="<?php _e( 'Choose a Order Status...', 'woo_ce' ); ?>" name="order_filter_status[]" multiple class="chzn-select" style="width:95%;">
					<option value="" selected="selected"><?php _e( 'All', 'woo_ce' ); ?></option>
<?php if( !empty( $order_statuses ) ) { ?>
	<?php foreach( $order_statuses as $order_status ) { ?>
					<option value="<?php echo $order_status->name; ?>" disabled="disabled"><?php echo ucfirst( $order_status->name ); ?></option>
	<?php } ?>
<?php } ?>
				</select>
				<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Payment gateway', 'woo_ce' ); ?></p>
				<select data-placeholder="<?php _e( 'Choose a Payment Gateway...', 'woo_ce' ); ?>" name="order_filter_payment[]" multiple class="chzn-select" style="width:95%;">
					<option value="" selected="selected"><?php _e( 'All', 'woo_ce' ); ?></option>
				</select>
				<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				<p class="description"><?php _e( 'Select the Payment Gateways you want to filter exported Orders by. Default is to include all Payment Gateways.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Shipping method', 'woo_ce' ); ?></p>
				<select data-placeholder="<?php _e( 'Choose a Shipping Method...', 'woo_ce' ); ?>" name="order_filter_shipping[]" multiple class="chzn-select" style="width:95%;">
					<option value="" selected="selected"><?php _e( 'All', 'woo_ce' ); ?></option>
				</select>
				<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				<p class="description"><?php _e( 'Select the Shipping Methods you want to filter exported Orders by. Default is to include all Shipping Methods.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Billing country', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></p>
				<select data-placeholder="<?php _e( 'Choose a Billing Country...', 'woo_ce' ); ?>" name="order_filter_billing_country[]" multiple class="chzn-select" style="width:95%;">
					<option value="" selected="selected"><?php _e( 'All', 'woo_ce' ); ?></option>
				</select>
				<p class="description"><?php _e( 'Filter Orders by Billing Country to be included in the export. Default is to include all Countries.', 'woo_ce' ); ?></p>
				<hr />
			</li>

			<li class="export-options order-options">
				<p class="label"><?php _e( 'Shipping country', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></p>
				<select data-placeholder="<?php _e( 'Choose a Shipping Country...', 'woo_ce' ); ?>" id="order_filter_shipping_country" name="order_filter_shipping_country" class="chzn-select">
					<option value="" selected="selected"><?php _e( 'All', 'woo_ce' ); ?></option>
				</select>
				<p class="description"><?php _e( 'Filter Orders by Shipping Country to be included in the export. Default is to include all Countries.', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options category-options tag-options brand-options customer-options user-options coupon-options subscription-options product_vendor-options commission-options shipping_class-options">
				<p><?php _e( 'No export filter options are available for this export type.', 'woo_ce' ); ?></p>
			</li>

		</ul>
	</td>
</tr>

<tr>
	<th>
		<label><?php _e( 'Scheduling', 'woo_ce' ); ?></label>
	</th>
	<td>
		<p><?php _e( 'How often do you want the export to run?', 'woo_ce' ); ?></p>
		<ul>
			<li>
				<label><input type="radio" name="auto_schedule" value="custom" disabled="disabled" /> <?php _e( 'Once every ', 'woo_ce' ); ?></label>
				<input name="auto_interval" type="text" id="auto_interval" value="<?php echo esc_attr( $auto_interval ); ?>" size="6" maxlength="6" class="text" disabled="disabled" />
				<?php _e( 'minutes', 'woo_ce' ); ?>
				<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
			</li>
			<li><label><input type="radio" name="auto_schedule" value="daily" disabled="disabled" /> <?php _e( 'Daily', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
			<li><label><input type="radio" name="auto_schedule" value="weekly" disabled="disabled" /> <?php _e( 'Weekly', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
			<li><label><input type="radio" name="auto_schedule" value="monthly" disabled="disabled" /> <?php _e( 'Monthly', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
			<li><label><input type="radio" name="auto_schedule" value="one-time" disabled="disabled" /> <?php _e( 'One time', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
		</ul>
		<p class="description"><?php _e( 'Choose how often Store Exporter Deluxe generates new exports. Default is every 1440 minutes (once every 24 hours).', 'woo_ce' ); ?></p>
		<hr />
		<p><?php _e( 'When do you want scheduled exports to start?', 'woo_ce' ); ?></p>
		<ul>
			<li><label><input type="radio" name="auto_commence" value="now" disabled="disabled" /><?php _e( 'From now', 'woo_ce' ); ?></label><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
			<li><label><input type="radio" name="auto_commence" value="future" disabled="disabled" /><?php _e( 'From the following', 'woo_ce' ); ?></label>: <input type="text" name="auto_commence_date" size="20" maxlength="20" class="text datetimepicker" value="<?php echo $auto_commence_date; ?>" /><!--, <?php _e( 'at this time', 'woo_ce' ); ?>: <input type="text" name="auto_interval_time" size="10" maxlength="10" class="text timepicker" />--><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></li>
		</ul>
	</td>
</tr>

<tr>
	<th>
		<label><?php _e( 'Export format', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" name="auto_format" value="csv" disabled="disabled" /> <?php _e( 'CSV', 'woo_ce' ); ?> <span class="description"><?php _e( '(Comma Separated Values)', 'woo_ce' ); ?> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="radio" name="auto_format" value="xml" disabled="disabled" /> <?php _e( 'XML', 'woo_ce' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woo_ce' ); ?> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="radio" name="auto_format" value="xls" disabled="disabled" /> <?php _e( 'Excel (XLS)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 97-2003)', 'woo_ce' ); ?> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="radio" name="auto_format" value="xlsx" disabled="disabled" /> <?php _e( 'Excel (XLSX)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Excel 2007-2013)', 'woo_ce' ); ?> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats. Default is CSV.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="auto_method"><?php _e( 'Export method', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="auto_method" name="auto_method">
			<option value="archive"><?php _e( 'Archive to WordPress Media', 'woo_ce' ); ?></option>
			<option value="email"><?php _e( 'Send as e-mail', 'woo_ce' ); ?></option>
			<option value="post"><?php _e( 'POST to remote URL', 'woo_ce' ); ?></option>
			<option value="ftp"><?php _e( 'Upload to remote FTP', 'woo_ce' ); ?></option>
		</select>
		<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Choose what Store Exporter Deluxe does with the generated export. Default is to archive the export to the WordPress Media for archival purposes.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr class="auto_method_options">
	<th>
		<label><?php _e( 'Export method options', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>

			<li class="export-options email-options">
				<p>
					<label for="email_to"><?php _e( 'Default e-mail recipient', 'woo_ce' ); ?></label><br />
					<input name="email_to" type="text" id="email_to" value="<?php echo esc_attr( $email_to ); ?>" class="regular-text code" disabled="disabled" /><br /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				</p>
				<p class="description"><?php _e( 'Set the default recipient of scheduled export e-mails, multiple recipients can be added using the <code><attr title="comma">,</attr></code> separator. This option can be overriden via CRON using the <code>to</code> argument.<br />Default is the Blog Administrator e-mail address set on the WordPress &raquo; Settings screen.', 'woo_ce' ); ?></p>

				<p>
					<label for="email_subject"><?php _e( 'Default e-mail subject', 'woo_ce' ); ?></label><br />
					<input name="email_subject" type="text" id="email_subject" value="<?php echo esc_attr( $email_subject ); ?>" class="large-text code" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				</p>
				<p class="description"><?php _e( 'Set the default subject of scheduled export e-mails, can be overriden via CRON using the <code>subject</code> argument. Tags can be used: <code>%store_name%</code>, <code>%export_type%</code>, <code>%export_filename%</code>.', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options post-options">
				<p>
					<label for="post_to"><?php _e( 'Default remote POST URL', 'woo_ce' ); ?></label><br />
					<input name="post_to" type="text" id="post_to" value="<?php echo esc_url( $post_to ); ?>" class="large-text code" disabled="disabled" /><br /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				</p>
				<p class="description"><?php printf( __( 'Set the default remote POST address for scheduled exports, can be overriden via CRON using the <code>to</code> argument. Default is empty. See our <a href="%s" target="_blank">Usage</a> document for more information on Default remote POST URL.', 'woo_ce' ), $troubleshooting_url ); ?></p>
			</li>

			<li class="export-options ftp-options">
				<label for="ftp_method_host"><?php _e( 'Host', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_host" name="ftp_method_host" size="15" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_host ); ?>" disabled="disabled" />&nbsp;
				<label for="ftp_method_port" style="width:auto;"><?php _e( 'Port', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_port" name="ftp_method_port" size="5" class="short-text code" value="<?php echo sanitize_text_field( $ftp_method_port ); ?>" disabled="disabled" maxlength="5" /><br />
				<label for="ftp_method_protocol"><?php _e( 'Protocol', 'woo_ce' ); ?></label>
				<select name="ftp_method_protocol">
					<option><?php _e( 'FTP - File Transfer Protocol', 'woo_ce' ); ?></option>
					<option disabled="disabled"><?php _e( 'SFTP - SSH File Transfer Protocol', 'woo_ce' ); ?></option>
				</select><br />
				<label for="ftp_method_user"><?php _e( 'Username', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_user" name="ftp_method_user" size="15" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_user ); ?>" disabled="disabled" /><br />
				<label for="ftp_method_pass"><?php _e( 'Password', 'woo_ce' ); ?>:</label> <input type="password" id="ftp_method_pass" name="ftp_method_pass" size="15" class="regular-text code" value="" disabled="disabled" /><?php if( !empty( $ftp_method_pass ) ) { echo ' ' . __( '(password is saved)', 'woo_ce' ); } ?><br />
				<label for="ftp_method_file_path"><?php _e( 'File path', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_file_path" name="ftp_method_path" size="25" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_path ); ?>" disabled="disabled" /><br />
				<label for="ftp_method_filename"><?php _e( 'Fixed filename', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_filename" name="ftp_method_filename" size="25" class="regular-text code" value="<?php echo sanitize_text_field( $ftp_method_filename ); ?>" disabled="disabled" /><br />
				<label for="ftp_method_passive"><?php _e( 'Transfer mode', 'woo_ce' ); ?>:</label> 
				<select id="ftp_method_passive" name="ftp_method_passive">
					<option value="auto"><?php _e( 'Auto', 'woo_ce' ); ?></option>
					<option value="active" disabled="disabled"><?php _e( 'Active', 'woo_ce' ); ?></option>
					<option value="passive" disabled="disabled"><?php _e( 'Passive', 'woo_ce' ); ?></option>
				</select><br />
				<label for="ftp_method_timeout"><?php _e( 'Timeout', 'woo_ce' ); ?>:</label> <input type="text" id="ftp_method_timeout" name="ftp_method_timeout" size="5" class="short-text code" value="<?php echo sanitize_text_field( $ftp_method_timeout ); ?>" disabled="disabled" /><br />
				<p class="description"><?php _e( 'Enter the FTP host (minus <code>ftp://</code>), login details and path of where to save the export file, do not provide the filename, the export filename can be set on General Settings above. For file path example: <code>wp-content/uploads/exports/</code>', 'woo_ce' ); ?></p>
			</li>

			<li class="export-options archive-options">
				<p><?php _e( 'No export method options are available for this export method.', 'woo_ce' ); ?></p>
			</li>

		</ul>
	</td>
</tr>
<tr>
	<th>
		<label for="scheduled_fields"><?php _e( 'Export fields', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="scheduled_fields" name="scheduled_fields" value="all"<?php checked( $scheduled_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="radio" name="scheduled_fields" value="saved"<?php checked( $scheduled_fields, 'saved' ); ?> disabled="disabled" /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="cron-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-clock"></div>&nbsp;<?php _e( 'CRON Exports', 'woo_ce' ); ?></h3>
		<p class="description"><?php printf( __( 'Store Exporter Deluxe supports exporting via a command line request. For sample CRON requests and supported arguments consult our <a href="%s" target="_blank">online documentation</a>.', 'woo_ce' ), $troubleshooting_url ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="enable_cron"><?php _e( 'Enable CRON', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="enable_cron" name="enable_cron">
			<option value="1" disabled="disabled"><?php _e( 'Yes', 'woo_ce' ); ?></option>
			<option value="0" selected="selected"><?php _e( 'No', 'woo_ce' ); ?></option>
		</select>
		<p class="description"><?php _e( 'Enabling CRON allows developers to schedule automated exports and connect with Store Exporter Deluxe remotely.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="secret_key"><?php _e( 'Export secret key', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="secret_key" type="text" id="secret_key" value="<?php echo esc_attr( $secret_key ); ?>" class="large-text code" disabled="disabled" /><br /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'This secret key (can be left empty to allow unrestricted access) limits access to authorised developers who provide a matching key when working with Store Exporter Deluxe.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="cron_fields"><?php _e( 'Export fields', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul style="margin-top:0.2em;">
			<li><label><input type="radio" id="cron_fields" name="cron_fields" value="all"<?php checked( $cron_fields, 'all' ); ?> /> <?php _e( 'Include all Export Fields for the requested Export Type', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="radio" name="cron_fields" value="saved"<?php checked( $cron_fields, 'saved' ); ?> disabled="disabled" /> <?php _e( 'Use the saved Export Fields preference set on the Export screen for the requested Export Type', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control whether all known export fields are included or only checked fields from the Export Fields section on the Export screen for each Export Type. Default is to include all export fields.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="orders-screen">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><div class="dashicons dashicons-admin-settings"></div>&nbsp;<?php _e( 'Orders Screen', 'woo_ce' ); ?></h3>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Actions display', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>
			<li><label><input type="checkbox" name="order_actions_csv" value="1"<?php checked( $order_actions_csv ); ?> /> <?php _e( 'Export to CSV', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="order_actions_xml" value="1"<?php checked( $order_actions_xml ); ?> /> <?php _e( 'Export to XML', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="order_actions_xls" value="1"<?php checked( $order_actions_xls ); ?> /> <?php _e( 'Export to XLS', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
			<li><label><input type="checkbox" name="order_actions_xlsx" value="1"<?php checked( $order_actions_xlsx ); ?> /> <?php _e( 'Export to XLSX', 'woo_ce' ); ?><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different Order actions on the WooCommerce &raquo; Orders screen.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}
?>