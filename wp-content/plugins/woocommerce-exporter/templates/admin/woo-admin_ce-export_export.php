<ul class="subsubsub">
	<li><a href="#export-type"><?php _e( 'Export Type', 'woo_ce' ); ?></a> |</li>
	<li><a href="#export-options"><?php _e( 'Export Options', 'woo_ce' ); ?></a></li>
	<?php do_action( 'woo_ce_export_quicklinks' ); ?>
</ul>
<br class="clear" />
<p><?php _e( 'Select an export type from the list below to export entries. Once you have selected an export type you may select the fields you would like to export and optional filters available for each export type. When you click the export button below, Store Exporter will create a CSV file for you to save to your computer.', 'woo_ce' ); ?></p>
<form method="post" action="<?php echo add_query_arg( array( 'failed' => null, 'empty' => null ) ); ?>" id="postform">
	<div id="poststuff">

		<div class="postbox" id="export-type">
			<h3 class="hndle"><?php _e( 'Export Type', 'woo_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Select the data type you want to export.', 'woo_ce' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<input type="radio" id="products" name="dataset" value="products"<?php disabled( $products, 0 ); ?><?php checked( $dataset, 'products' ); ?> />
							<label for="products"><?php _e( 'Products', 'woo_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $products; ?>)</span>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="categories" name="dataset" value="categories"<?php disabled( $categories, 0 ); ?><?php checked( $dataset, 'categories' ); ?> />
							<label for="categories"><?php _e( 'Categories', 'woo_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $categories; ?>)</span>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="tags" name="dataset" value="tags"<?php disabled( $tags, 0 ); ?><?php checked( $dataset, 'tags' ); ?> />
							<label for="tags"><?php _e( 'Tags', 'woo_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $tags; ?>)</span>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="orders" name="dataset" value="orders"<?php disabled( $orders, 0 ); ?><?php checked( $dataset, 'orders' ); ?>/>
							<label for="orders"><?php _e( 'Orders', 'woo_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $orders; ?>)</span>
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
							<span class="description"> - <?php echo sprintf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
<?php } ?>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="customers" name="dataset" value="customers"<?php disabled( $customers, 0 ); ?><?php checked( $dataset, 'customers' ); ?>/>
							<label for="customers"><?php _e( 'Customers', 'woo_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $customers; ?>)</span>
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
							<span class="description"> - <?php echo sprintf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
<?php } ?>
						</td>
					</tr>

					<tr>
						<th>
							<input type="radio" id="coupons" name="dataset" value="coupons"<?php disabled( $coupons, 0 ); ?><?php checked( $dataset, 'coupons' ); ?> />
							<label for="coupons"><?php _e( 'Coupons', 'woo_ce' ); ?></label>
						</th>
						<td>
							<span class="description">(<?php echo $coupons; ?>)</span>
<?php if( !function_exists( 'woo_cd_admin_init' ) ) { ?>
							<span class="description"> - <?php echo sprintf( __( 'available in %s', 'woo_ce' ), $woo_cd_link ); ?></span>
<?php } ?>
						</td>
					</tr>

				</table>
<!--
				<p class="submit">
					<input type="submit" value="<?php _e( 'Export', 'woo_ce' ); ?>" class="button-primary" />
				</p>
-->
			</div>
		</div>
		<!-- .postbox -->

<?php if( $product_fields ) { ?>
		<div id="export-products">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Product Fields', 'woo_ce' ); ?></h3>
				<div class="inside">
	<?php if( $products ) { ?>
					<p class="description"><?php _e( 'Select the Product fields you would like to export.', 'woo_ce' ); ?></p>
					<p><a href="javascript:void(0)" id="products-checkall" class="checkall"><?php _e( 'Check All', 'woo_ce' ); ?></a> | <a href="javascript:void(0)" id="products-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woo_ce' ); ?></a></p>
					<table>

		<?php foreach( $product_fields as $product_field ) { ?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="product_fields[<?php echo $product_field['name']; ?>]" class="product_field"<?php checked( $product_field['default'], 1 ); ?><?php disabled( $product_field['disabled'], 1 ); ?> />
									<?php echo $product_field['label']; ?>
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
						<input type="submit" id="export_products" value="<?php _e( 'Export Products', 'woo_ce' ); ?> " class="button-primary" />
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Product field in the above export list?', 'woo_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woo_ce' ); ?></a></p>
	<?php } else { ?>
					<p><?php _e( 'No Products have been found.', 'woo_ce' ); ?></p>
	<?php } ?>
				</div>
			</div>
			<!-- .postbox -->

			<div id="export-products-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Product Filters', 'woo_ce' ); ?></h3>
				<div class="inside">

					<p><label><input type="checkbox" id="products-filters-categories" /> <?php _e( 'Filter Products by Product Categories', 'woo_ce' ); ?></label></p>
					<div id="export-products-filters-categories" class="separator">
<?php if( $product_categories ) { ?>
						<ul>
	<?php foreach( $product_categories as $product_category ) { ?>
							<li><label><input type="checkbox" name="product_filter_categories[<?php echo $product_category->term_id; ?>]" value="<?php echo $product_category->term_id; ?>" /> <?php echo $product_category->name; ?> (#<?php echo $product_category->term_id; ?>)</label></li>
	<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Categories you want to filter exported Products by. Default is to include all Product Categories.', 'woo_ce' ); ?></p>
<?php } else { ?>
						<p><?php _e( 'No Product Categories have been found.', 'woo_ce' ); ?></p>
<?php } ?>
					</div>
					<!-- #export-products-filters-categories -->

					<p><label><input type="checkbox" id="products-filters-tags" /> <?php _e( 'Filter Products by Product Tags', 'woo_ce' ); ?></label></p>
					<div id="export-products-filters-tags" class="separator">
<?php if( $product_tags ) { ?>
						<ul>
	<?php foreach( $product_tags as $product_tag ) { ?>
							<li><label><input type="checkbox" name="product_filter_tags[<?php echo $product_tag->term_id; ?>]" value="<?php echo $product_tag->term_id; ?>" /> <?php echo $product_tag->name; ?> (#<?php echo $product_tag->term_id; ?>)</label></li>
	<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Tags you want to filter exported Products by. Default is to include all Product Tags.', 'woo_ce' ); ?></p>
<?php } else { ?>
						<p><?php _e( 'No Product Tags have been found.', 'woo_ce' ); ?></p>
<?php } ?>
					</div>
					<!-- #export-products-filters-tags -->

					<p><label><input type="checkbox" id="products-filters-status" /> <?php _e( 'Filter Products by Product Status', 'woo_ce' ); ?></label></p>
					<div id="export-products-filters-status" class="separator">
						<ul>
<?php foreach( $product_statuses as $key => $product_status ) { ?>
							<li><label><input type="checkbox" name="product_filter_status[<?php echo $key; ?>]" value="<?php echo $key; ?>" /> <?php echo $product_status; ?></label></li>
<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Status options you want to filter exported Products by. Default is to include all Product Status options.', 'woo_ce' ); ?></p>
					</div>
					<!-- #export-products-filters-status -->

					<p><label><input type="checkbox" id="products-filters-type" /> <?php _e( 'Filter Products by Product Type', 'woo_ce' ); ?></label></p>
					<div id="export-products-filters-type" class="separator">
						<ul>
<?php foreach( $product_types as $key => $product_type ) { ?>
							<li><label><input type="checkbox" name="product_filter_type[<?php echo $key; ?>]" value="<?php echo $key; ?>" /> <?php echo woo_ce_format_product_type( $product_type ); ?></label></li>
<?php } ?>
						</ul>
						<p class="description"><?php _e( 'Select the Product Type\'s you want to filter exported Products by. Default is to include all Product Types and Variations.', 'woo_ce' ); ?></p>
					</div>
					<!-- #export-products-filters-type -->

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-products -->

<?php } ?>
		<div class="postbox" id="export-categories">
			<h3 class="hndle"><?php _e( 'Category Fields', 'woo_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Select the Category fields you would like to export.', 'woo_ce' ); ?></p>
				<p><a href="javascript:void(0)" id="categories-checkall" class="checkall"><?php _e( 'Check All', 'woo_ce' ); ?></a> | <a href="javascript:void(0)" id="categories-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woo_ce' ); ?></a></p>
				<table>

	<?php foreach( $category_fields as $category_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="category_fields[<?php echo $category_field['name']; ?>]" class="category_field"<?php checked( $category_field['default'], 1 ); ?><?php disabled( $category_field['disabled'], 1 ); ?> />
								<?php echo $category_field['label']; ?>
							</label>
						</td>
					</tr>

	<?php } ?>
				</table>
				<p class="submit">
					<input type="submit" id="export_categories" value="<?php _e( 'Export Categories', 'woo_ce' ); ?> " class="button-primary" />
				</p>
				<p class="description"><?php _e( 'Can\'t find a particular Category field in the above export list?', 'woo_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woo_ce' ); ?></a>.</p>
			</div>
		</div>
		<!-- #export-categories -->

		<div class="postbox" id="export-tags">
			<h3 class="hndle"><?php _e( 'Tag Fields', 'woo_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'Select the Tag fields you would like to export.', 'woo_ce' ); ?></p>
				<p><a href="javascript:void(0)" id="tags-checkall" class="checkall"><?php _e( 'Check All', 'woo_ce' ); ?></a> | <a href="javascript:void(0)" id="tags-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woo_ce' ); ?></a></p>
				<table>

	<?php foreach( $tag_fields as $tag_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="tag_fields[<?php echo $tag_field['name']; ?>]" class="tag_field"<?php checked( $tag_field['default'], 1 ); ?><?php disabled( $tag_field['disabled'], 1 ); ?> />
								<?php echo $tag_field['label']; ?>
							</label>
						</td>
					</tr>

	<?php } ?>
				</table>
				<p class="submit">
					<input type="submit" id="export_tags" value="<?php _e( 'Export Tags', 'woo_ce' ); ?> " class="button-primary" />
				</p>
				<p class="description"><?php _e( 'Can\'t find a particular Product Tag field in the above export list?', 'woo_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woo_ce' ); ?></a>.</p>
			</div>
		</div>
		<!-- #export-tags -->

<?php if( $order_fields ) { ?>
		<div id="export-orders">

			<div class="postbox">
				<h3 class="hndle"><?php _e( 'Order Fields', 'woo_ce' ); ?></h3>
				<div class="inside">

	<?php if( $orders ) { ?>
					<p class="description"><?php _e( 'Select the Order fields you would like to export.', 'woo_ce' ); ?></p>
					<p><a href="javascript:void(0)" id="orders-checkall" class="checkall"><?php _e( 'Check All', 'woo_ce' ); ?></a> | <a href="javascript:void(0)" id="orders-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woo_ce' ); ?></a></p>
					<table>

		<?php foreach( $order_fields as $order_field ) { ?>
						<tr>
							<td>
								<label>
									<input type="checkbox" name="order_fields[<?php echo $order_field['name']; ?>]" class="order_field"<?php checked( $order_field['default'], 1 ); ?><?php disabled( $woo_cd_exists, false ); ?> />
									<?php echo $order_field['label']; ?>
								</label>
							</td>
						</tr>

		<?php } ?>
					</table>
					<p class="submit">
		<?php if( function_exists( 'woo_cd_admin_init' ) ) { ?>
						<input type="submit" id="export_orders" value="<?php _e( 'Export Orders', 'woo_ce' ); ?> " class="button-primary" />
		<?php } else { ?>
						<input type="button" class="button button-disabled" value="<?php _e( 'Export Orders', 'woo_ce' ); ?>" />
		<?php } ?>
					</p>
					<p class="description"><?php _e( 'Can\'t find a particular Order field in the above export list?', 'woo_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woo_ce' ); ?></a>.</p>
	<?php } else { ?>
					<p><?php _e( 'No Orders have been found.', 'woo_ce' ); ?></p>
	<?php } ?>

				</div>
			</div>
			<!-- .postbox -->

			<div id="export-orders-filters" class="postbox">
				<h3 class="hndle"><?php _e( 'Order Filters', 'woo_ce' ); ?></h3>
				<div class="inside">

					<?php do_action( 'woo_ce_export_order_options_before_table' ); ?>

					<table class="form-table">
						<?php do_action( 'woo_ce_export_order_options_table' ); ?>
					</table>

					<?php do_action( 'woo_ce_export_order_options_after_table' ); ?>

				</div>
				<!-- .inside -->
			</div>
			<!-- .postbox -->

		</div>
		<!-- #export-orders -->

<?php } ?>
<?php if( $customer_fields ) { ?>
		<div class="postbox" id="export-customers">
			<h3 class="hndle"><?php _e( 'Customer Fields', 'woo_ce' ); ?></h3>
			<div class="inside">
	<?php if( $customers ) { ?>
				<p class="description"><?php _e( 'Select the Customer fields you would like to export.', 'woo_ce' ); ?></p>
				<p><a href="javascript:void(0)" id="customers-checkall" class="checkall"><?php _e( 'Check All', 'woo_ce' ); ?></a> | <a href="javascript:void(0)" id="customers-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woo_ce' ); ?></a></p>
				<table>

		<?php foreach( $customer_fields as $customer_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="customer_fields[<?php echo $customer_field['name']; ?>]" class="customer_field"<?php checked( $customer_field['default'], 1 ); ?><?php disabled( $woo_cd_exists, false ); ?> />
								<?php echo $customer_field['label']; ?>
							</label>
						</td>
					</tr>

		<?php } ?>
				</table>
				<p class="submit">
		<?php if( function_exists( 'woo_cd_admin_init' ) ) { ?>
					<input type="submit" id="export_customers" value="<?php _e( 'Export Customers', 'woo_ce' ); ?>" class="button-primary" />
		<?php } else { ?>
					<input type="button" class="button button-disabled" value="<?php _e( 'Export Customers', 'woo_ce' ); ?>" />
		<?php } ?>
				</p>
					<p class="description"><?php _e( 'Can\'t find a particular Customer field in the above export list?', 'woo_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woo_ce' ); ?></a>.</p>
	<?php } else { ?>
				<p><?php _e( 'No Customers have been found.', 'woo_ce' ); ?></p>
	<?php } ?>
			</div>
		</div>
		<!-- .postbox -->

<?php } ?>
<?php if( $coupon_fields ) { ?>
		<div class="postbox" id="export-coupons">
			<h3 class="hndle"><?php _e( 'Coupon Fields', 'woo_ce' ); ?></h3>
			<div class="inside">
	<?php if( $coupons ) { ?>
				<p class="description"><?php _e( 'Select the Coupon fields you would like to export.', 'woo_ce' ); ?></p>
				<p><a href="javascript:void(0)" id="coupons-checkall" class="checkall"><?php _e( 'Check All', 'woo_ce' ); ?></a> | <a href="javascript:void(0)" id="coupons-uncheckall" class="uncheckall"><?php _e( 'Uncheck All', 'woo_ce' ); ?></a></p>
				<table>

		<?php foreach( $coupon_fields as $coupon_field ) { ?>
					<tr>
						<td>
							<label>
								<input type="checkbox" name="coupon_fields[<?php echo $coupon_field['name']; ?>]" class="coupon_field"<?php checked( $coupon_field['default'], 1 ); ?><?php disabled( $woo_cd_exists, false ); ?> />
								<?php echo $coupon_field['label']; ?>
							</label>
						</td>
					</tr>

		<?php } ?>
				</table>
				<p class="submit">
		<?php if( function_exists( 'woo_cd_admin_init' ) ) { ?>
					<input type="submit" id="export_coupons" value="<?php _e( 'Export Coupons', 'woo_ce' ); ?>" class="button-primary" />
		<?php } else { ?>
					<input type="button" class="button button-disabled" value="<?php _e( 'Export Coupons', 'woo_ce' ); ?>" />
		<?php } ?>
				</p>
				<p class="description"><?php _e( 'Can\'t find a particular Coupon field in the above export list?', 'woo_ce' ); ?> <a href="<?php echo $troubleshooting_url; ?>" target="_blank"><?php _e( 'Get in touch', 'woo_ce' ); ?></a>.</p>
	<?php } else { ?>
				<p><?php _e( 'No Coupons have been found.', 'woo_ce' ); ?></p>
	<?php } ?>
			</div>
		</div>
		<!-- .postbox -->

<?php } ?>
		<div class="postbox" id="export-options">
			<h3 class="hndle"><?php _e( 'Export Options', 'woo_ce' ); ?></h3>
			<div class="inside">
				<table class="form-table">

					<?php do_action( 'woo_ce_export_options' ); ?>

					<tr>
						<th>
							<label for="delimiter"><?php _e( 'Field delimiter', 'woo_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="delimiter" name="delimiter" value="<?php echo $delimiter; ?>" size="1" maxlength="1" class="text" />
							<p class="description"><?php _e( 'The field delimiter is the character separating each cell in your CSV. This is typically the \',\' (comma) character.', 'woo_pc' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="category_separator"><?php _e( 'Category separator', 'woo_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="category_separator" name="category_separator" value="<?php echo $category_separator; ?>" size="1" class="text" />
							<p class="description"><?php _e( 'The Product Category separator allows you to assign individual Products to multiple Product Categories/Tags/Images at a time. It is suggested to use the \'|\' (vertical pipe) character between each item. For instance: <code>Clothing|Mens|Shirts</code>.', 'woo_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="bom"><?php _e( 'Add BOM character', 'woo_ce' ); ?>: </label>
						</th>
						<td>
							<select id="bom" name="bom">
								<option value="1"<?php selected( $bom, 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
								<option value="0"<?php selected( $bom, 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Mark the CSV file as UTF8 by adding a byte order mark (BOM) to the export, useful for non-English character sets.', 'woo_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="escape_formatting"><?php _e( 'Field escape formatting', 'woo_ce' ); ?>: </label>
						</th>
						<td>
							<label><input type="radio" name="escape_formatting" value="all"<?php checked( $escape_formatting, 'all' ); ?> />&nbsp;<?php _e( 'Escape all fields', 'woo_ce' ); ?></label><br />
							<label><input type="radio" name="escape_formatting" value="excel"<?php checked( $escape_formatting, 'excel' ); ?> />&nbsp;<?php _e( 'Escape fields as Excel would', 'woo_ce' ); ?></label>
							<p class="description"><?php _e( 'Choose the field escape format that suits your spreadsheet software (e.g. Excel).', 'woo_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="offset"><?php _e( 'Volume offset', 'woo_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="offset" name="offset" value="<?php echo $offset; ?>" size="5" class="text" />
							<p class="description"><?php _e( 'Volume offset allows for partial exporting of a dataset, to be used in conjuction with Limit volme option above. By default this is not used and is left empty.', 'woo_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="limit_volume"><?php _e( 'Limit volume', 'woo_ce' ); ?></label>
						</th>
						<td>
							<input type="text" size="3" id="limit_volume" name="limit_volume" value="<?php echo $limit_volume; ?>" size="5" class="text" />
							<p class="description"><?php _e( 'Limit volume allows for partial exporting of a dataset. This is useful when encountering timeout and/or memory errors during the default export. By default this is not used and is left empty.', 'woo_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label for="encoding"><?php _e( 'Character encoding', 'woo_ce' ); ?>: </label>
						</th>
						<td>
<?php if( $file_encodings ) { ?>
							<select id="encoding" name="encoding">
								<option><?php _e( 'System default', 'woo_ce' ); ?></option>
	<?php foreach( $file_encodings as $key => $chr ) { ?>
								<option value="<?php echo $chr; ?>"><?php echo $chr; ?></option>
	<?php } ?>
							</select>
<?php } else { ?>
							<p class="description"><?php _e( 'Character encoding options are unavailable in PHP 4, contact your hosting provider to update your site install to use PHP 5 or higher.', 'woo_ce' ); ?></p>
<?php } ?>
						</td>
					</tr>

					<tr>
						<th>
							<label for="delete_temporary_csv"><?php _e( 'Delete temporary CSV after export', 'woo_ce' ); ?></label>
						</th>
						<td>
							<select id="delete_temporary_csv" name="delete_temporary_csv">
								<option value="1"<?php selected( $delete_csv, 1 ); ?>><?php _e( 'Yes', 'woo_ce' ); ?></option>
								<option value="0"<?php selected( $delete_csv, 0 ); ?>><?php _e( 'No', 'woo_ce' ); ?></option>
							</select>
						</td>
					</tr>

<?php if( !ini_get( 'safe_mode' ) ) { ?>
					<tr>
						<th>
							<label for="timeout"><?php _e( 'Script timeout', 'woo_ce' ); ?>: </label>
						</th>
						<td>
							<select id="timeout" name="timeout">
								<option value="600"<?php selected( $timeout, 600 ); ?>><?php echo sprintf( __( '%s minutes', 'woo_ce' ), 10 ); ?></option>
								<option value="1800"<?php selected( $timeout, 1800 ); ?>><?php echo sprintf( __( '%s minutes', 'woo_ce' ), 30 ); ?></option>
								<option value="3600"<?php selected( $timeout, 3600 ); ?>><?php echo sprintf( __( '%s hour', 'woo_ce' ), 1 ); ?></option>
								<option value="0"<?php selected( $timeout, 0 ); ?>><?php _e( 'Unlimited', 'woo_ce' ); ?></option>
							</select>
							<p class="description"><?php _e( 'Script timeout defines how long WooCommerce Exporter is \'allowed\' to process your CSV file, once the time limit is reached the export process halts.', 'woo_ce' ); ?></p>
						</td>
					</tr>
<?php } ?>

					<?php do_action( 'woo_ce_export_options_after' ); ?>

				</table>
			</div>
		</div>
		<!-- .postbox -->

	</div>
	<!-- #poststuff -->
	<input type="hidden" name="action" value="export" />
</form>

<?php do_action( 'woo_ce_export_after_form' ); ?>