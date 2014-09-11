<?php
function woo_ce_export_settings_quicklinks() {

	ob_start(); ?>
<li>| <a href="#xml-settings"><?php _e( 'XML Settings', 'woo_ce' ); ?></a> |</li>
<li><a href="#scheduled-exports"><?php _e( 'Scheduled Exports', 'woo_ce' ); ?></a> |</li>
<li><a href="#cron-exports"><?php _e( 'CRON Exports', 'woo_ce' ); ?></a></li>
<?php
	ob_end_flush();

}

function woo_ce_export_settings_additional() {

	$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

	$email_to = '-';
	$post_to = '-';
	ob_start(); ?>
<tr>
	<th>
		<label for="email_to"><?php _e( 'Default e-mail recipient', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="email_to" type="text" id="email_to" value="<?php echo esc_attr( $email_to ); ?>" class="regular-text code" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Set the default recipient of scheduled export e-mails, can be overriden via CRON using the <code>to</code> argument. Default is the WordPress Blog administrator e-mail address.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="post_to"><?php _e( 'Default remote POST URL', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="post_to" type="text" id="post_to" value="<?php echo esc_url( $post_to ); ?>" class="regular-text code" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Set the default remote POST address for scheduled exports, can be overriden via CRON using the <code>to</code> argument. Default is empty.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();
	
}

// Returns the disabled HTML template for the Enable CRON and Secret Export Key options for the Settings screen
function woo_ce_export_settings_cron() {

	$woo_cd_url = 'http://www.visser.com.au/woocommerce/plugins/exporter-deluxe/';
	$woo_cd_link = sprintf( '<a href="%s" target="_blank">' . __( 'Store Exporter Deluxe', 'woo_ce' ) . '</a>', $woo_cd_url );

	// Scheduled exports
	$order_statuses = woo_ce_get_order_statuses();
	$product_types = woo_ce_get_product_types();
	$auto_interval = 1440;
	$auto_format = 'csv';

	// CRON exports
	$secret_key = '-';

	$troubleshooting_url = 'http://www.visser.com.au/documentation/store-exporter-deluxe/usage/';
	ob_start(); ?>
<tr id="xml-settings">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><?php _e( 'XML Settings', 'woo_ce' ); ?></h3>
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
		</ul>
		<p class="description"><?php _e( 'Control the visibility of different attributes in the XML export.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="scheduled-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><?php _e( 'Scheduled Exports', 'woo_ce' ); ?></h3>
		<p class="description"><?php _e( 'Configure Store Exporter Deluxe to automatically generate exports.', 'woo_ce' ); ?></p>
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
			<option value="product"><?php _e( 'Products', 'woo_ce' ); ?></option>
			<option value="category"><?php _e( 'Categories', 'woo_ce' ); ?></option>
			<option value="tag"><?php _e( 'Tags', 'woo_ce' ); ?></option>
			<option value="brand"><?php _e( 'Brands', 'woo_ce' ); ?></option>
			<option value="order"><?php _e( 'Orders', 'woo_ce' ); ?></option>
			<option value="customer"><?php _e( 'Customers', 'woo_ce' ); ?></option>
			<option value="coupon"><?php _e( 'Coupons', 'woo_ce' ); ?></option>
			<!-- <option value="attribute"><?php _e( 'Attributes', 'woo_ce' ); ?></option> -->
		</select>
		<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Select the data type you want to export.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="auto_type"><?php _e( 'Export filters', 'woo_ce' ); ?></label>
	</th>
	<td>
		<ul>
			<li class="export-options product-options">
				<label><?php _e( 'Product Type', 'woo_ce' ); ?></label>
<?php if( $product_types ) { ?>
				<ul style="margin-top:0.2em;">
	<?php foreach( $product_types as $key => $product_type ) { ?>
					<li><label><input type="checkbox" name="product_filter_type[<?php echo $key; ?>]" value="<?php echo $key; ?>" disabled="disabled" /> <?php echo woo_ce_format_product_type( $product_type['name'] ); ?> (<?php echo $product_type['count']; ?>)<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label></li>
	<?php } ?>
				</ul>
<?php } ?>
				<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types and Variations.', 'woo_ce' ); ?></p>
			</li>
			<li class="export-options order-options">
				<label><?php _e( 'Order Status', 'woo_ce' ); ?></label>
				<select name="order_filter_status">
					<option value="" selected="selected"><?php _e( 'All', 'woo_ce' ); ?></option>
<?php if( $order_statuses ) { ?>
	<?php foreach( $order_statuses as $order_status ) { ?>
					<option value="<?php echo $order_status->name; ?>" disabled="disabled"><?php echo ucfirst( $order_status->name ); ?></option>
	<?php } ?>
<?php } ?>
				</select>
				<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woo_ce' ); ?></p>
			</li>
			<li class="export-options order-options">
				<label><?php _e( 'Order Date', 'woo_ce' ); ?></label>
				<input type="text" size="10" maxlength="10" class="text" disabled="disabled"> to <input type="text" size="10" maxlength="10" class="text" disabled="disabled"><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is empty.', 'woo_ce' ); ?></p>
			</li>
		</ul>
	</td>
</tr>
<tr>
	<th>
		<label for="auto_interval"><?php _e( 'Once every (minutes)', 'woo_ce' ); ?></label>
	</th>
	<td>
		<input name="auto_interval" type="text" id="auto_interval" value="<?php echo esc_attr( $auto_interval ); ?>" size="4" maxlength="4" class="text" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Choose how often Store Exporter Deluxe generates new exports. Default is every 1440 minutes (once every 24 hours).', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label><?php _e( 'Export format', 'woo_ce' ); ?></label>
	</th>
	<td>
		<label><input type="radio" name="auto_format" value="csv"<?php checked( $auto_format, 'csv' ); ?> disabled="disabled" /> <?php _e( 'CSV', 'woo_ce' ); ?> <span class="description"><?php _e( '(Comma separated values)', 'woo_ce' ); ?></span><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label><br />
		<label><input type="radio" name="auto_format" value="xml"<?php checked( $auto_format, 'xml' ); ?> disabled="disabled" /> <?php _e( 'XML', 'woo_ce' ); ?> <span class="description"><?php _e( '(EXtensible Markup Language)', 'woo_ce' ); ?></span><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label><br />
		<label><input type="radio" name="auto_format" value="xls"<?php checked( $auto_format, 'xls' ); ?> disabled="disabled" /> <?php _e( 'Excel (XLS)', 'woo_ce' ); ?> <span class="description"><?php _e( '(Microsoft Excel 2007)', 'woo_ce' ); ?></span><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span></label>
		<p class="description"><?php _e( 'Adjust the export format to generate different export file formats. Default is CSV.', 'woo_ce' ); ?></p>
	</td>
</tr>
<tr>
	<th>
		<label for="auto_method"><?php _e( 'Export method', 'woo_ce' ); ?></label>
	</th>
	<td>
		<select id="auto_method" name="auto_method">
			<option value="archive" selected="selected"><?php _e( 'Archive to WordPress Media', 'woo_ce' ); ?></option>
			<option value="email" disabled="disabled"><?php _e( 'Send as e-mail', 'woo_ce' ); ?></option>
			<option value="post" disabled="disabled"><?php _e( 'POST to Remote URL', 'woo_ce' ); ?></option>
		</select>
		<span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'Choose what Store Exporter Deluxe does with the generated export. Default is to archive the export to the WordPress Media for archival purposes.', 'woo_ce' ); ?></p>
	</td>
</tr>

<tr id="cron-exports">
	<td colspan="2" style="padding:0;">
		<hr />
		<h3><?php _e( 'CRON Exports', 'woo_ce' ); ?></h3>
		<p class="description"><?php printf( __( 'Store Exporter Deluxe supports exporting via a command line request, to do this you need to prepare a specific URL and pass it the following required inline parameters. For sample CRON requests and supported arguments consult our <a href="%s" target="_blank">online documentation</a>.', 'woo_ce' ), $troubleshooting_url ); ?></p>
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
		<input name="secret_key" type="text" id="secret_key" value="<?php echo esc_attr( $secret_key ); ?>" class="regular-text code" disabled="disabled" /><span class="description"> - <?php printf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
		<p class="description"><?php _e( 'This secret key (can be left empty to allow unrestricted access) limits access to authorised developers who provide a matching key when working with Store Exporter Deluxe.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
	ob_end_flush();

}
?>