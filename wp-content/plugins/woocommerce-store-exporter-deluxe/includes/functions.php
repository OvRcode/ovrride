<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Administration menu */
	function woo_cd_admin_menu() {

		if( !function_exists( 'woo_ce_admin_init' ) )
			add_submenu_page( 'woocommerce', __( 'Store Exporter Deluxe', 'woo_cd' ), __( 'Store Export', 'woo_cd' ), 'view_woocommerce_reports', 'woo_ce', 'woo_cd_html_page' );

	}
	add_action( 'admin_menu', 'woo_cd_admin_menu', 11 );

	function woo_cd_template_header( $title = '', $icon = 'woocommerce' ) {

		global $woo_cd;

		if( $title )
			$output = $title;
		else
			$output = $woo_cd['menu']; ?>
<div class="wrap">
	<div id="icon-<?php echo $icon; ?>" class="icon32 icon32-woocommerce-importer"><br /></div>
	<h2><?php echo $output; ?></h2>
<?php
	}

	function woo_cd_template_footer() { ?>
</div>
<?php
	}

	function woo_cd_template_header_title() {

		$output = __( 'Store Exporter Deluxe', 'woo_cd' );
		return $output;

	}
	add_filter( 'woo_ce_template_header', 'woo_cd_template_header_title' );

	function woo_cd_orders_filter_by_date() {

		$current_month = date( 'F' );
		$last_month = date( 'F', mktime( 0, 0, 0, date( 'n' )-1, 1, date( 'Y' ) ) );
		$order_dates_from = woo_cd_get_order_first_date();
		$order_dates_to = date( 'd/m/Y' );

		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-date" /> <?php _e( 'Filter Orders by Order Date', 'woo_ce' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="order_dates_filter" value="current_month" /> <?php _e( 'Current month', 'woo_ce' ); ?> (<?php echo $current_month; ?>)</label>
		</li>
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_month" /> <?php _e( 'Last month', 'woo_ce' ); ?> (<?php echo $last_month; ?>)</label>
		</li>
<!--
		<li>
			<label><input type="radio" name="order_dates_filter" value="last_quarter" /> <?php _e( 'Last quarter', 'woo_ce' ); ?> (Nov. - Jan.)</label>
		</li>
-->
		<li>
			<label><input type="radio" name="order_dates_filter" value="manual" checked="checked" /> <?php _e( 'Manual', 'woo_ce' ); ?></label>
			<div style="margin-top:0.2em;">
				<input type="text" size="10" maxlength="10" id="order_dates_from" name="order_dates_from" value="<?php echo $order_dates_from; ?>" class="text datepicker" /> to <input type="text" size="10" maxlength="10" id="order_dates_to" name="order_dates_to" value="<?php echo $order_dates_to; ?>" class="text datepicker" />
				<p class="description"><?php _e( 'Filter the dates of Orders to be included in the export. Default is the date of the first order to today.', 'woo_ce' ); ?></p>
			</div>
		</li>
	</ul>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	function woo_cd_orders_filter_by_customer() {

		$customers = woo_cd_get_customers_list();
		ob_start(); ?>
<p><label for="order_customer"><?php _e( 'Filter Orders by Customer', 'woo_ce' ); ?></label></p>
<div id="export-orders-filters-date" class="separator">
	<ul>
		<li>
			<select id="order_customer" name="order_customer" class="chzn-select">
				<option value=""><?php _e( 'Show all customers', 'woo_ce' ); ?></option>
<?php if( $customers ) { ?>
	<?php foreach( $customers as $customer ) { ?>
				<option value="<?php echo $customer->ID; ?>"><?php echo sprintf( '%s (#%s - %s)', $customer->display_name, $customer->ID, $customer->user_email ); ?></option>
	<?php } ?>
<?php } ?>
			</select>
		</li>
	</ul>
	<p class="description"><?php _e( 'Filter Orders by Customer (unique e-mail address) to be included in the export. Default is to include all Orders.', 'woo_ce' ); ?></p>
</div>
<!-- #export-orders-filters-date -->
<?php
		ob_end_flush();

	}

	function woo_cd_orders_items_formatting() {

		$order_items_formatting = woo_ce_get_option( 'order_items_formatting', 'unique' );

		ob_start(); ?>
<p><label for="order_items"><?php _e( 'Order items formatting', 'woo_ce' ); ?></label></p>
<div id="export-orders-items-formatting" class="separator">
	<ul>
		<li>
			<label><input type="radio" name="order_items" value="combined"<?php checked( $order_items_formatting, 'combined' ); ?> />&nbsp;<?php _e( 'Place Order Items within a grouped single Order row', 'woo_ce' ); ?></label>
			<p class="description"><?php _e( 'For example: <code>Cart Items: SKU</code> cell might contain <code>SPECK-IPHONE|INCASE-NANO|-</code> for 3 Order items within an Order', 'woo_cd' ); ?></p>
		</li>
		<li>
			<label><input type="radio" name="order_items" value="unique"<?php checked( $order_items_formatting, 'unique' ); ?> />&nbsp;<?php _e( 'Place Order Items on individual cells within a single Order row', 'woo_ce' ); ?></label>
			<p class="description"><?php _e( 'For example: <code>Cart Items: SKU</code> would become <code>Cart Item #1: SKU</code> with <codeSPECK-IPHONE</code> for the first Order item within an Order', 'woo_ce' ); ?></p>
		</li>
		<li>
			<label><input type="radio" name="order_items" value="individual"<?php checked( $order_items_formatting, 'individual' ); ?> />&nbsp;<?php _e( 'Place each Order Item within their own Order row', 'woo_ce' ); ?></label>
			<p class="description"><?php _e( 'For example: An Order with 3 Order items will display a single Order item on each row', 'woo_ce' ); ?></p>
		</li>
	</ul>
	<p class="description"><?php _e( 'Choose how you would like Order Items to be presented within Orders.', 'woo_ce' ); ?></p>
</div>
<!-- #export-orders-items-formatting -->
<?php
		ob_end_flush();

	}

	function woo_cd_orders_max_order_items() {

		$max_size = woo_ce_get_option( 'max_order_items', 10 );

		ob_start(); ?>
<tr>
	<th>
		<label for="max_order_items"><?php _e( 'Max unique Order items', 'woo_ce' ); ?>: </label>
	</th>
	<td>
		<input type="text" id="max_order_items" name="max_order_items" size="3" class="text" value="<?php echo $max_size; ?>" />
		<p class="description"><?php _e( 'Manage the number of Order cart item colums displayed when the \'Place Order Items on individual cells within a single Order row\' Order items formatting option is selected.', 'woo_ce' ); ?></p>
	</td>
</tr>
<?php
		ob_end_flush();

	}

	function woo_cd_orders_filter_by_status() {

		$order_statuses = woo_ce_get_order_statuses();
		ob_start(); ?>
<p><label><input type="checkbox" id="orders-filters-status" /> <?php _e( 'Filter Orders by Order Status', 'woo_ce' ); ?></label></p>
<div id="export-orders-filters-status" class="separator">
	<ul>
<?php foreach( $order_statuses as $order_status ) { ?>
		<li><label><input type="checkbox" name="order_filter_status[<?php echo $order_status->name; ?>]" value="<?php echo $order_status->name; ?>" /> <?php echo ucfirst( $order_status->name ); ?></label></li>
<?php } ?>
	</ul>
	<p class="description"><?php _e( 'Select the Order Status you want to filter exported Orders by. Default is to include all Order Status options.', 'woo_ce' ); ?></p>
</div>
<!-- #export-orders-filters-status -->
<?php
		ob_end_flush();

	}

	function woo_cd_quicklink_custom_fields() {

		ob_start(); ?>
<li>| <a href="#export-orders-custom-fields"><?php _e( 'Custom Fields', 'woo_ce' ); ?></a></li>
<?php
		ob_end_flush();

	}

	function woo_cd_orders_custom_fields_link() {

		ob_start(); ?>
<div id="export-orders-custom-fields-link" class="separator">
	<p><a href="#export-orders-custom-fields"><?php _e( 'Manage Custom Order Fields', 'woo_cd' ); ?></a></p>
</div><input type="hidden" >
<!-- #export-orders-custom-fields-link -->
<?php
		ob_end_flush();

	}

	function woo_cd_orders_custom_fields() {

		$custom_orders = woo_ce_get_option( 'custom_orders', '' );
		if( $custom_orders )
			$custom_orders = implode( "\n", $custom_orders );
		$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
		if( $custom_order_items )
			$custom_order_items = implode( "\n", $custom_order_items );
		ob_start(); ?>
<form method="post" id="export-orders-custom-fields">
	<div id="poststuff">

		<div class="postbox" id="export-options">
			<h3 class="hndle"><?php _e( 'Custom Order Fields', 'woo_ce' ); ?></h3>
			<div class="inside">
				<p class="description"><?php _e( 'To include additional custom Order and Order Item meta in the Export Orders table above fill the Orders and Order Items text box then click Save Custom Fields.', 'woo_ce' ); ?></p>
				<table class="form-table">

					<tr>
						<th>
							<label><?php _e( 'Order Meta', 'woo_ce' ); ?></label>
						</th>
						<td>
							<textarea name="custom_orders" rows="5" cols="70"><?php echo $custom_orders; ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Order meta in your exported CSV by adding each custom Order meta name to a new line above.<br />For example: <code>Customer UA, Customer IP Address</code>', 'woo_ce' ); ?></p>
						</td>
					</tr>

					<tr>
						<th>
							<label><?php _e( 'Order Item Meta', 'woo_ce' ); ?></label>
						</th>
						<td>
							<textarea name="custom_order_items" rows="5" cols="70"><?php echo $custom_order_items; ?></textarea>
							<p class="description"><?php _e( 'Include additional custom Order Item meta in your exported CSV by adding each custom Order Item meta name to a new line above.<br />For example: <code>Personalized Message</code>.', 'woo_ce' ); ?></p>
						</td>
					</tr>

				</table>
				<p class="submit">
					<input type="submit" value="<?php _e( 'Save Custom Fields', 'woo_ce' ); ?>" class="button-primary" />
				</p>
				<p class="description"><?php _e( 'For more information on custom Order and Order Item meta consult our online documentation.', 'woo_ce' ); ?></p>
			</div>
		</div>
		<!-- .postbox -->

	</div>
	<input type="hidden" name="action" value="update" />
</form>
<?php
		ob_end_flush();

	}

	function woo_cd_get_order_first_date() {

		$output = date( 'd/m/Y', mktime( 0, 0, 0, date( 'n' ), 1 ) );
		$post_type = 'shop_order';
		$args = array(
			'post_type' => $post_type,
			'orderby' => 'post_date',
			'order' => 'ASC',
			'numberposts' => 1
		);
		$orders = get_posts( $args );
		if( $orders ) {
			$order = strtotime( $orders[0]->post_date );
			$output = date( 'd/m/Y', $order );
		}
		return $output;

	}

	function woo_cd_order_fields( $fields ) {

		if( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) {
			$product_addons = woo_cd_get_product_addons();
			if( $product_addons ) {
				foreach( $product_addons as $product_addon ) {
					$fields[] = array(
						'name' => sprintf( 'order_items_product_addon_%s', $product_addon->post_name ),
						'label' => sprintf( __( 'Order Items: %s', 'woo_ce' ), $product_addon->post_title ),
						'default' => 1
					);
				}
			}
		}
		$custom_orders = woo_ce_get_option( 'custom_orders', '' );
		if( $custom_orders ) {
			foreach( $custom_orders as $custom_order ) {
				$fields[] = array(
					'name' => $custom_order,
					'label' => $custom_order,
					'default' => 1
				);
			}
		}

		return $fields;

	}
	add_filter( 'woo_ce_order_fields', 'woo_cd_order_fields' );

	function woo_cd_get_product_addons() {

		$output = array();
		if( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) {
			$post_type = 'global_product_addon';
			$args = array(
				'post_type' => $post_type,
				'numberposts' => -1
			);
			$product_addons = get_posts( $args );
			if( !$product_addons )
				$product_addons = array();
			$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
			if( $custom_order_items ) {
				foreach( $custom_order_items as $custom_order_item ) {
					$product_addons[] = (object)array(
						'post_name' => $custom_order_item,
						'post_title' => $custom_order_item
					);
				}
			}
			$output = $product_addons;
		}
		return $output;

	}

	function woo_cd_get_orders( $export_type = 'orders', $export_args = array() ) {

		global $export;

		$limit_volume = -1;
		$offset = 0;
		if( $export_args ) {
			$limit_volume = $export_args['limit_volume'];
			$offset = $export_args['offset'];
			switch( $export_args['order_dates_filter'] ) {

				case 'current_month':
					$export_args['order_dates_from'] = date( 'd-m-Y', mktime( 0, 0, 0, date( 'm' ), 1, date( 'Y' ) ) );
					$export_args['order_dates_to'] = date( 'd-m-Y', mktime( 0, 0, 0, ( date( 'm' ) + 1 ), 0, date( 'Y' ) ) );
					break;

				case 'last_month':
					$export_args['order_dates_from'] = date( 'd-m-Y', mktime( 0, 0, 0, ( date( 'm' ) - 1 ), 1, date( 'Y' ) ) );
					$export_args['order_dates_to'] = date( 'd-m-Y', mktime( 0, 0, 0, date( 'm' ), 0, date( 'Y' ) ) );
					break;

				case 'last_quarter':
					break;

				case 'manual':
					break;

				default:
					$export_args['order_dates_from'] = false;
					$export_args['order_dates_to'] = false;
					break;

			}
			if( $export_args['order_dates_from'] && $export_args['order_dates_to'] ) {
				$export_args['order_dates_from'] = strtotime( $export_args['order_dates_from'] );
				$order_dates_to = explode( '-', $export_args['order_dates_to'] );
				$export_args['order_dates_to'] = date( 'd-m-Y', mktime( 0, 0, 0, $order_dates_to[1], $order_dates_to[0]+1, $order_dates_to[2] ) );
				$export_args['order_dates_to'] = strtotime( $export_args['order_dates_to'] );
			}
		}
		$output = '';
		$post_type = 'shop_order';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => $limit_volume,
			'offset' => $offset,
			'post_status' => woo_ce_post_statuses()
		);
		if( !empty( $export_args['order_status'] ) ) {
			$args['tax_query'] = array(
				array(
					'taxonomy' => 'shop_order_status',
					'field' => 'slug',
					'terms' => $export_args['order_status']
				)
			);
		}
		if( !empty( $export_args['order_customer'] ) ) {
			$user = get_userdata( $export_args['order_customer'] );
			if( $user ) {
				$args['meta_key'] = '_billing_email';
				$args['meta_value'] = $user->user_email;
			}
		}
		$orders = get_posts( $args );
		if( $orders ) {
			foreach( $orders as $key => $order ) {
				if( $export_args['order_dates_from'] && $export_args['order_dates_to'] ) {
					if( ( strtotime( $orders[$key]->post_date ) > $export_args['order_dates_from'] ) && ( strtotime( $orders[$key]->post_date ) < $export_args['order_dates_to'] ) ) {
						// Do nothing
					} else {
						unset( $orders[$key] );
						continue;
					}
				}
				$orders[$key]->purchase_total = get_post_meta( $order->ID, '_order_total', true );
				$orders[$key]->payment_status = woo_cd_get_order_status( $order->ID );
				$orders[$key]->user_id = get_post_meta( $order->ID, '_customer_user', true );
				if( $orders[$key]->user_id == 0 )
					$orders[$key]->user_id = '';
				$orders[$key]->user_name = woo_cd_get_username( $orders[$key]->user_id );
				$orders[$key]->billing_first_name = get_post_meta( $order->ID, '_billing_first_name', true );
				$orders[$key]->billing_last_name = get_post_meta( $order->ID, '_billing_last_name', true );
				$orders[$key]->billing_full_name = $order->billing_first_name . ' ' . $order->billing_last_name;
				$orders[$key]->billing_company = get_post_meta( $order->ID, '_billing_company', true );
				$orders[$key]->billing_address = get_post_meta( $order->ID, '_billing_address_1', true );
				$orders[$key]->billing_address_alt = get_post_meta( $order->ID, '_billing_address_2', true );
				if( $order->billing_address_alt )
					$orders[$key]->billing_address .= ' ' . $order->billing_address_alt;
				$orders[$key]->billing_city = get_post_meta( $order->ID, '_billing_city', true );
				$orders[$key]->billing_postcode = get_post_meta( $order->ID, '_billing_postcode', true );
				$orders[$key]->billing_state = get_post_meta( $order->ID, '_billing_state', true );
				$orders[$key]->billing_country = get_post_meta( $order->ID, '_billing_country', true );
				$orders[$key]->billing_state_full = woo_ce_expand_state_name( $orders[$key]->billing_country, $orders[$key]->billing_state );
				$orders[$key]->billing_country_full = woo_ce_expand_country_name( $orders[$key]->billing_country );
				$orders[$key]->billing_phone = get_post_meta( $order->ID, '_billing_phone', true );
				$orders[$key]->billing_email = get_post_meta( $order->ID, '_billing_email', true );
				$orders[$key]->shipping_first_name = get_post_meta( $order->ID, '_shipping_first_name', true );
				$orders[$key]->shipping_last_name = get_post_meta( $order->ID, '_shipping_last_name', true );
				$orders[$key]->shipping_full_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
				$orders[$key]->shipping_company = get_post_meta( $order->ID, '_shipping_company', true );
				$orders[$key]->shipping_address = get_post_meta( $order->ID, '_shipping_address_1', true );
				$orders[$key]->shipping_address_alt = get_post_meta( $order->ID, '_shipping_address_2', true );
				if( $order->shipping_address_alt )
					$orders[$key]->shipping_address .= ' ' . $order->shipping_address_alt;
				$orders[$key]->shipping_city = get_post_meta( $order->ID, '_shipping_city', true );
				$orders[$key]->shipping_postcode = get_post_meta( $order->ID, '_shipping_postcode', true );
				$orders[$key]->shipping_state = get_post_meta( $order->ID, '_shipping_state', true );
				$orders[$key]->shipping_country = get_post_meta( $order->ID, '_shipping_country', true );
				$orders[$key]->shipping_state_full = woo_ce_expand_state_name( $orders[$key]->shipping_country, $orders[$key]->shipping_state );
				$orders[$key]->shipping_country_full = woo_ce_expand_country_name( $orders[$key]->shipping_country );
				$orders[$key]->shipping_phone = get_post_meta( $order->ID, '_shipping_phone', true );
				switch( $export_type ) {

					case 'orders':
						// Order
						$orders[$key]->purchase_id = $order->ID;
						$orders[$key]->order_discount = get_post_meta( $order->ID, '_order_discount', true );
						$orders[$key]->order_shipping_tax = get_post_meta( $order->ID, '_order_shipping_tax', true );
						$orders[$key]->payment_status = woo_cd_format_order_status( $orders[$key]->payment_status );
						$orders[$key]->payment_gateway = woo_cd_format_order_payment_gateway( get_post_meta( $order->ID, '_payment_method', true ) );
						$orders[$key]->shipping_method = woo_cd_format_shipping_method( get_post_meta( $order->ID, '_shipping_method', true ) );
						$orders[$key]->order_key = get_post_meta( $order->ID, '_order_key', true );
						$orders[$key]->purchase_date = mysql2date( 'd/m/Y H:i:s', $order->post_date );
						$orders[$key]->customer_note = $order->post_excerpt;
						$order_notes = woo_cd_get_order_assoc_notes( $order->ID );
						$orders[$key]->order_notes = '';
						if( $order_notes ) {
							foreach( $order_notes as $order_note )
								$orders[$key]->order_notes .= $order_note->comment_content . $export->category_separator;
							$orders[$key]->order_notes = $substr( $orders[$key]->order_notes, 0, -1 );
						}
						$orders[$key]->order_items_size = 0;
						if( $orders[$key]->order_items = woo_cd_get_order_items( $order->ID ) ) {
							$orders[$key]->order_items_size = count( $orders[$key]->order_items );
							if( $export_args['order_items'] == 'combined' ) {
								$orders[$key]->order_items_product_id = '';
								$orders[$key]->order_items_variation_id = '';
								$orders[$key]->order_items_sku = '';
								$orders[$key]->order_items_name = '';
								$orders[$key]->order_items_variation = '';
								$orders[$key]->order_items_tax_class = '';
								$orders[$key]->order_items_quantity = '';
								$orders[$key]->order_items_total = '';
								$orders[$key]->order_items_subtotal = '';
								$orders[$key]->order_items_tax = '';
								$orders[$key]->order_items_tax_subtotal = '';
								$orders[$key]->order_items_type = '';
								foreach( $orders[$key]->order_items as $order_item ) {
									if( empty( $order_item->sku ) )
										$order_item->sku = '-';
									$orders[$key]->order_items_product_id .= $order_item->product_id . $export->category_separator;
									$orders[$key]->order_items_variation_id .= $order_item->variation_id . $export->category_separator;
									$orders[$key]->order_items_sku .= $order_item->sku . $export->category_separator;
									$orders[$key]->order_items_name .= $order_item->name . $export->category_separator;
									$orders[$key]->order_items_variation .= $order_item->variation . $export->category_separator;
									$orders[$key]->order_items_tax_class .= $order_item->tax_class . $export->category_separator;
									if( empty( $order_item->quantity ) )
										$order_item->quantity = '-';
									$orders[$key]->order_items_quantity .= $order_item->quantity . $export->category_separator;
									$orders[$key]->order_items_total .= $order_item->total . $export->category_separator;
									$orders[$key]->order_items_subtotal .= $order_item->subtotal . $export->category_separator;
									$orders[$key]->order_items_tax .= $order_item->tax . $export->category_separator;
									$orders[$key]->order_items_tax_subtotal .= $order_item->tax_subtotal . $export->category_separator;
									$orders[$key]->order_items_type .= $order_item->type . $export->category_separator;
								}
								$orders[$key]->order_items_product_id = substr( $orders[$key]->order_items_product_id, 0, -1 );
								$orders[$key]->order_items_variation_id = substr( $orders[$key]->order_items_variation_id, 0, -1 );
								$orders[$key]->order_items_sku = substr( $orders[$key]->order_items_sku, 0, -1 );
								$orders[$key]->order_items_name = substr( $orders[$key]->order_items_name, 0, -1 );
								$orders[$key]->order_items_variation = substr( $orders[$key]->order_items_variation, 0, -1 );
								$orders[$key]->order_items_tax_class = substr( $orders[$key]->order_items_tax_class, 0, -1 );
								$orders[$key]->order_items_quantity = substr( $orders[$key]->order_items_quantity, 0, -1 );
								$orders[$key]->order_items_total = substr( $orders[$key]->order_items_total, 0, -1 );
								$orders[$key]->order_items_subtotal = substr( $orders[$key]->order_items_subtotal, 0, -1 );
								$orders[$key]->order_items_type = substr( $orders[$key]->order_items_type, 0, -1 );
								$orders[$key] = apply_filters( 'woo_cd_order_items_combined', $orders[$key] );
							} else if( $export_args['order_items'] == 'unique' ) {
								$i = 1;
								foreach( $orders[$key]->order_items as $order_item ) {
									if( empty( $order_item->sku ) )
										$order_item->sku = '-';
									$orders[$key]->{sprintf( 'order_item_%d_product_id', $i )} = $order_item->product_id;
									$orders[$key]->{sprintf( 'order_item_%d_variation_id', $i )} = $order_item->variation_id;
									$orders[$key]->{sprintf( 'order_item_%d_sku', $i )} = $order_item->sku;
									$orders[$key]->{sprintf( 'order_item_%d_name', $i )} = $order_item->name;
									$orders[$key]->{sprintf( 'order_item_%d_variation', $i )} = $order_item->variation;
									$orders[$key]->{sprintf( 'order_item_%d_tax_class', $i )} = $order_item->tax_class;
									if( empty( $order_item->quantity ) )
										$order_item->quantity = '-';
									$orders[$key]->{sprintf( 'order_item_%d_quantity', $i )} = $order_item->quantity;
									$orders[$key]->{sprintf( 'order_item_%d_total', $i )} = $order_item->total;
									$orders[$key]->{sprintf( 'order_item_%d_subtotal', $i )} = $order_item->subtotal;
									$orders[$key]->{sprintf( 'order_item_%d_tax', $i )} = $order_item->tax;
									$orders[$key]->{sprintf( 'order_item_%d_tax_subtotal', $i )} = $order_item->tax_subtotal;
									$orders[$key]->{sprintf( 'order_item_%d_type', $i )} = $order_item->type;
									$orders[$key] = apply_filters( 'woo_cd_order_items_unique', $orders[$key], $i, $order_item );
									$i++;
								}
							}
						}

						// Custom
						$custom_order_items = woo_ce_get_option( 'custom_order_items', '' );
						if( $custom_order_items ) {
							foreach( $custom_order_items as $custom_order_item )
								$orders[$key]->$custom_order_item = get_post_meta( $order->ID, $custom_order_item, true );
						}
						$custom_orders = woo_ce_get_option( 'custom_orders', '' );
						if( $custom_orders ) {
							foreach( $custom_orders as $custom_order )
								$orders[$key]->$custom_order = get_post_meta( $order->ID, $custom_order, true );
						}
						break;

				}
			}
		}
		if( $export_type == 'customers' ) {
			$customers = array();
			foreach( $orders as $key => $order ) {
				if( $duplicate_key = woo_cd_is_duplicate_customer( $customers, $order ) ) {
					$customers[$duplicate_key]->total_spent = $customers[$duplicate_key]->total_spent + $order->purchase_total;
					$customers[$duplicate_key]->total_orders++;
					if( $order->payment_status == 'completed' )
						$customers[$duplicate_key]->completed_orders++;
				} else {
					$customers[$order->ID] = $order;
					$customers[$order->ID]->total_spent = $order->purchase_total;
					$customers[$order->ID]->completed_orders = 0;
					if( $order->payment_status == 'completed' )
						$customers[$order->ID]->completed_orders = 1;
					$customers[$order->ID]->total_orders = 1;
				}
			}
			$output = $customers;
		} else {
			$output = $orders;
		}
		return $output;

	}

	function woo_cd_get_order_assoc_notes( $order_id = 0 ) {

		$output = '';
		if( $order_id ) {
			$args = array(
				'post_id' => $order_id,
				'approve' => 'approve',
				'comment_type' => 'order_note'
			);
			$order_notes = get_comments( $args );
			if( $order_notes )
				$output = $order_notes;
		}
		return $output;

	}

	function woo_cd_max_order_items( $orders = array() ) {
		$output = 0;
		if( $orders ) {
			foreach( $orders as $order ) {
				if( $order->order_items )
					$output = count( $order->order_items[0]->name );
			}
		}
		return $output;
	}

	function woo_cd_get_customers_list() {

		$output = array();
		$args = array(
/*
			'fields' => 'all',
			'orderby' => 'display_name',
			'meta_key' => 'billing_email',
			'meta_value' => null,
			'search_columns'	=> array( 'ID', 'user_login', 'user_email', 'user_nicename' )
*/
		);
		$customers = get_users( $args );
		if( $customers )
			$output = $customers;
		return $output;

	}

	function woo_cd_get_username( $user_id = 0 ) {

		$output = '';
		if( $user_id ) {
			$user = get_userdata( $user_id );
			if( $user )
				$output = $user->user_login;
		}
		return $output;

	}

	function woo_cd_get_order_items( $order_id = 0 ) {

		global $wpdb;

		$output = array();
		if( $order_id ) {
			$order_items_sql = $wpdb->prepare( "SELECT `order_item_id` as id, `order_item_name` as name, `order_item_type` as type FROM `" . $wpdb->prefix . "woocommerce_order_items` WHERE `order_id` = %d", $order_id );
			$order_items = $wpdb->get_results( $order_items_sql );
			if( $order_items ) {
				foreach( $order_items as $key => $order_item ) {
					$order_item_meta_sql = $wpdb->prepare( "SELECT `meta_key`, `meta_value` FROM `" . $wpdb->prefix . "woocommerce_order_itemmeta` WHERE `order_item_id` = %d", $order_item->id );
					$order_item_meta = $wpdb->get_results( $order_item_meta_sql );
					if( $order_item_meta ) {
						$order_items[$key]->product_id = '';
						$order_items[$key]->variation_id = '';
						$order_items[$key]->variation = '';
						$order_items[$key]->subtotal = '';
						$order_items[$key]->tax_subtotal = '';
						$size = count( $order_item_meta );
						for( $i = 0; $i < $size; $i++ ) {
							switch( $order_item_meta[$i]->meta_key ) {

								case '_qty':
									$order_items[$key]->quantity = $order_item_meta[$i]->meta_value;
									break;

								case '_product_id':
									$order_items[$key]->product_id = $order_item_meta[$i]->meta_value;
									if( $order_items[$key]->product_id )
										$order_items[$key]->sku = get_post_meta( $order_items[$key]->product_id, '_sku', true );
									break;

								case '_tax_class':
									$order_items[$key]->tax_class = woo_cd_format_order_item_tax_class( $order_item_meta[$i]->meta_value );
									break;

								case '_line_subtotal':
									$order_items[$key]->subtotal = $order_item_meta[$i]->meta_value;
									break;

								case '_line_subtotal_tax':
									$order_items[$key]->tax_subtotal = $order_item_meta[$i]->meta_value;
									break;

								case '_line_total':
									$order_items[$key]->total = $order_item_meta[$i]->meta_value;
									break;

								case '_line_tax':
									$order_items[$key]->tax = $order_item_meta[$i]->meta_value;
									break;

								case '_variation_id':
									$order_items[$key]->variation = '';
									$order_items[$key]->variation_id = $order_item_meta[$i]->meta_value;
									if( $order_items[$key]->variation_id ) {
										$order_items[$key]->sku = get_post_meta( $order_items[$key]->variation_id, '_sku', true );
										$variation_sql = $wpdb->prepare( "SELECT `meta_key` FROM `" . $wpdb->postmeta . "` WHERE `post_id` = %d AND `meta_key` LIKE 'attribute_pa_%' LIMIT 1", $order_items[$key]->variation_id );
										$variation = $wpdb->get_var( $variation_sql );
										if( $variation ) {
											$variation = str_replace( 'attribute_pa_', '', $variation );
											$variation_label_sql = $wpdb->prepare( "SELECT `attribute_label` FROM `" . $wpdb->prefix . "woocommerce_attribute_taxonomies` WHERE `attribute_name` = '%s' LIMIT 1", $variation );
											$variation_label = $wpdb->get_var( $variation_label_sql );
											$slug = get_post_meta( $order_items[$key]->variation_id, sprintf( 'attribute_pa_%s', $variation ), true );
											$taxonomy = 'pa_' . $variation;
											if( taxonomy_exists( $taxonomy ) ) {
												$term = get_term_by( 'slug', $slug, $taxonomy );
												if( $term )
													$order_items[$key]->variation = sprintf( '%s: %s', $variation_label, $term->name );
											}
										}
									}
									break;

								default:
									$order_items[$key] = apply_filters( 'woo_cd_order_items', $order_items[$key], $order_item_meta[$i]->meta_key, $order_item_meta[$i]->meta_value );
									break;

							}
						}
					}
					if( $order_items[$key]->type == 'fee' )
						$order_items[$key]->quantity = 1;
					$order_items[$key]->type = woo_cd_format_order_item_type( $order_items[$key]->type );
				}
				$output = $order_items;
			}
		}
		return $output;

	}

	/* Start of: Product Add Ons integration */

	function woo_cd_order_items_product_addons( $order_item, $meta_key = '', $meta_value = '' ) {

		if( class_exists( 'Product_Addon_Admin' ) || class_exists( 'Product_Addon_Display' ) ) {
			$product_addons = woo_cd_get_product_addons();
			if( $product_addons ) {
				foreach( $product_addons as $product_addon ) {
					$product_addon->meta = '';
					$product_addon->label = '';
					if( $product_addon->ID )
						$product_addon->meta = get_post_meta( $product_addon->ID, '_product_addons', true );
					else
						$product_addon->label = $product_addon->post_name;
					if( $product_addon->meta )
						$product_addon->label = $product_addon->meta[0]['options'][0]['label'];
					if( $product_addon->label ) {
						if( strpos( $meta_key, $product_addon->label ) !== false )
							$order_item->product_addons[$product_addon->post_name] = $meta_value;
					}
				}
			}
		}
		return $order_item;

	}
	add_filter( 'woo_cd_order_items', 'woo_cd_order_items_product_addons', 10, 3 );

	function woo_cd_order_items_product_addons_fields_exclusion( $fields = array() ) {

		$product_addons = woo_cd_get_product_addons();
		if( $product_addons ) {
			foreach( $product_addons as $product_addon )
				$fields[] = sprintf( 'order_items_product_addon_%s', $product_addon->post_name );
		}
		return $fields;

	}
	add_filter( 'woo_cd_add_unique_order_item_fields_exclusion', 'woo_cd_order_items_product_addons_fields_exclusion' );

	/* Order items formatting: Combined */

	function woo_cd_order_items_combined_product_addons( $order ) {

		global $export;

		$product_addons = woo_cd_get_product_addons();
		if( $product_addons ) {
			foreach( $product_addons as $product_addon ) {
				if( $order_items = $order->order_items ) {
					foreach( $order_items as $order_item ) {
						if( isset( $order_item->product_addons[$product_addon->post_name] ) )
							$order->{'order_items_product_addon_' . $product_addon->post_name} = $order_item->product_addons[$product_addon->post_name] . $export->category_separator;
					}
				}
				if( isset( $order->{'order_items_product_addon_' . $product_addon->post_name} ) )
					$order->{'order_items_product_addon_' . $product_addon->post_name} = substr( $order->{'order_items_product_addon_' . $product_addon->post_name}, 0, -1 );
			}
		}
		return $order;

	}
	add_filter( 'woo_cd_order_items_combined', 'woo_cd_order_items_combined_product_addons' );

	/* Order items formatting: Unique */
	function woo_cd_order_items_product_addons_fields_on( $fields = array(), $i = 0 ) {

		$product_addons = woo_cd_get_product_addons();
		if( $product_addons ) {
			foreach( $product_addons as $product_addon )
				$fields[sprintf( 'order_item_%d_product_addon_%s', $i, $product_addon->post_name )] = 'on';
		}
		return $fields;

	}
	add_filter( 'woo_cd_add_unique_order_item_fields_on', 'woo_cd_order_items_product_addons_fields_on', 10, 2 );

	function woo_cd_order_items_product_addons_columns( $fields = array(), $i = 0 ) {

		$product_addons = woo_cd_get_product_addons();
		if( $product_addons ) {
			foreach( $product_addons as $product_addon )
				$fields[] = sprintf( __( 'Cart Item #%d: %s', 'woo_cd' ), $i, $product_addon->post_title );
		}
		return $fields;

	}
	add_filter( 'woo_cd_add_unique_order_item_columns', 'woo_cd_order_items_product_addons_columns', 10, 2 );

	function woo_cd_order_items_product_addon_unique( $order, $i = 0, $order_item = array() ) {

		$product_addons = woo_cd_get_product_addons();
		if( $product_addons ) {
			foreach( $product_addons as $product_addon ) {
				if( isset( $order_item->product_addons[$product_addon->post_name] ) )
					$order->{sprintf( 'order_item_%d_product_addon_%s', $i, $product_addon->post_name )} = $order_item->product_addons[$product_addon->post_name];
			}
		}
		return $order;

	}
	add_filter( 'woo_cd_order_items_unique', 'woo_cd_order_items_product_addon_unique', 10, 3 );

	/* Order items formatting: Individual */

	function woo_cd_order_items_product_addon_individual( $order, $order_item ) {

		$product_addons = woo_cd_get_product_addons();
		if( $product_addons ) {
			foreach( $product_addons as $product_addon ) {
				if( isset( $order_item->product_addons[$product_addon->post_name] ) )
					$order->{'order_items_product_addon_' . $product_addon->post_name} = $order_item->product_addons[$product_addon->post_name];
			}
		}
		return $order;

	}
	add_filter( 'woo_cd_order_items_individual', 'woo_cd_order_items_product_addon_individual', 10, 2 );

	/* End of: Product Add Ons integration */

	function woo_cd_add_unique_order_item_fields( $fields = array() ) {

		$max_size = woo_ce_get_option( 'max_order_items', 10 );
		foreach( $fields as $key => $field ) {
			$excluded_fields = apply_filters( 'woo_cd_add_unique_order_item_fields_exclusion', array(
				'order_items_product_id',
				'order_items_variation_id',
				'order_items_sku',
				'order_items_name',
				'order_items_variation',
				'order_items_tax_class',
				'order_items_quantity',
				'order_items_total',
				'order_items_subtotal',
				'order_items_tax',
				'order_items_tax_subtotal',
				'order_items_type'
			) );
			if( in_array( $key, $excluded_fields ) )
				unset( $fields[$key] );
		}
		for( $i = 1; $i < $max_size; $i++ ) {
			$fields[sprintf( 'order_item_%d_product_id', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_variation_id', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_sku', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_name', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_variation', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_tax_class', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_quantity', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_total', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_subtotal', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_tax', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_tax_subtotal', $i )] = 'on';
			$fields[sprintf( 'order_item_%d_type', $i )] = 'on';
			$fields = apply_filters( 'woo_cd_add_unique_order_item_fields_on', $fields, $i );
		}
		return $fields;

	}

	function woo_cd_add_unique_order_item_columns( $fields = array() ) {

		$max_size = woo_ce_get_option( 'max_order_items', 10 );
		foreach( $fields as $key => $field ) {
			if( strpos( $field, 'Order Items' ) !== false )
				unset( $fields[$key] );
		}
		for( $i = 1; $i < $max_size; $i++ ) {
			$fields[] = sprintf( __( 'Cart Item #%d: Product ID', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Variation ID', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: SKU', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Product Name', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Product Variation', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Tax Class', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Quantity', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Total', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Subtotal', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Tax', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Tax Subtotal', 'woo_cd' ), $i );
			$fields[] = sprintf( __( 'Cart Item #%d: Type', 'woo_cd' ), $i );
			$fields = apply_filters( 'woo_cd_add_unique_order_item_columns', $fields, $i );
		}
		return $fields;

	}

	function woo_cd_is_duplicate_customer( $customers = array(), $order = array() ) {

		foreach( $customers as $key => $customer ) {
			if( $customer->user_id == $order->user_id || $customer->billing_email == $order->billing_email ) {
				return $key;
				break;
			}
		}
		return 0;

	}

	function woo_cd_get_order_status( $order_id = 0 ) {

		global $export;

		$output = '';
		$term_taxonomy = 'shop_order_status';
		$status = wp_get_object_terms( $order_id, $term_taxonomy );
		if( $status ) {
			$size = count( $status );
			for( $i = 0; $i < $size; $i++ ) {
				$term = get_term( $status[$i]->term_id, $term_taxonomy );
				if( $term )
					$output .= $term->name . $export->category_separator;
			}
			$output = substr( $output, 0, -1 );
		}
		return $output;

	}

	function woo_cd_get_coupons() {

		$output = '';
		$post_type = 'shop_coupon';
		$args = array(
			'post_type' => $post_type,
			'numberposts' => -1,
			'post_status' => woo_ce_post_statuses()
		);
		$coupons = get_posts( $args );
		if( $coupons ) {
			foreach( $coupons as $key => $coupon ) {
				$coupons[$key]->coupon_code = $coupon->post_title;
				$coupons[$key]->discount_type = woo_cd_format_discount_type( get_post_meta( $coupon->ID, 'discount_type', true ) );
				$coupons[$key]->coupon_description = $coupon->post_excerpt;
				$coupons[$key]->coupon_amount = get_post_meta( $coupon->ID, 'coupon_amount', true );
				$coupons[$key]->individual_use = woo_ce_format_switch( get_post_meta( $coupon->ID, 'individual_use', true ) );
				$coupons[$key]->apply_before_tax = woo_ce_format_switch( get_post_meta( $coupon->ID, 'apply_before_tax', true ) );
				$coupons[$key]->exclude_sale_items = woo_ce_format_switch( get_post_meta( $coupon->ID, 'exclude_sale_items', true ) );
				$coupons[$key]->minimum_amount = get_post_meta( $coupon->ID, 'minimum_amount', true );
				$coupons[$key]->product_ids = woo_cd_convert_product_ids( get_post_meta( $coupon->ID, 'product_ids', true ) );
				$coupons[$key]->exclude_product_ids = woo_cd_convert_product_ids( get_post_meta( $coupon->ID, 'exclude_product_ids', true ) );
				$coupons[$key]->product_categories = woo_cd_convert_product_ids( get_post_meta( $coupon->ID, 'product_categories', true ) );
				$coupons[$key]->exclude_product_categories = woo_cd_convert_product_ids( get_post_meta( $coupon->ID, 'exclude_product_categories', true ) );
				$coupons[$key]->customer_email = woo_cd_convert_product_ids( get_post_meta( $coupon->ID, 'customer_email', true ) );
				$coupons[$key]->usage_limit = get_post_meta( $coupon->ID, 'usage_limit', true );
				$coupons[$key]->expiry_date = get_post_meta( $coupon->ID, 'expiry_date', true );
				if( $coupons[$key]->expiry_date )
					$coupons[$key]->expiry_date = mysql2date( 'd/m/Y', $coupons[$key]->expiry_date );
			}
			$output = $coupons;
		}
		return $output;

	}

	function woo_cd_export_dataset( $datatype = null, $export = null ) {

		global $wpdb, $woo_ce, $woo_cd, $export;

		include_once( $woo_ce['abspath'] . '/includes/formatting.php' );
		include_once( $woo_cd['abspath'] . '/includes/formatting.php' );

		$csv = '';
		$separator = $export->delimiter;
		switch( $datatype ) {

			case 'orders':
				$fields = woo_ce_get_order_fields( 'summary' );
				$export->fields = array_intersect_assoc( $fields, $export->fields );
				if( $export->fields ) {
					foreach( $export->fields as $key => $field )
						$export->columns[] = woo_ce_get_order_field( $key );
				}
				if( $export->args['order_items'] == 'unique' ) {
					$export->fields = woo_cd_add_unique_order_item_fields( $export->fields );
					$export->columns = woo_cd_add_unique_order_item_columns( $export->columns );
				}
				$orders = woo_cd_get_orders( 'orders', $export->args );
				if( $orders ) {
					$size = count( $export->columns );
					$i = 0;
					foreach( $export->columns as $column ) {
						if( $i == ( $size - 1 ) )
							$csv .= woo_ce_escape_csv_value( $column, $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$csv .= woo_ce_escape_csv_value( $column, $export->delimiter, $export->escape_formatting ) . $separator;
						$i++;
					}
					foreach( $orders as $order ) {
						if( $export->args['order_items'] == 'combined' || $export->args['order_items'] == 'unique' ) {

							/* Order items formatting: SPECK-IPHONE|INCASE-NANO|- */

							foreach( $export->fields as $key => $field ) {
								if( isset( $order->$key ) ) {
									if( is_array( $field ) ) {
										foreach( $field as $array_key => $array_value ) {
											if( !is_array( $array_value ) )
												$csv .= woo_ce_escape_csv_value( $array_value, $export->delimiter, $export->escape_formatting );
										}
									} else {
										$csv .= woo_ce_escape_csv_value( $order->$key, $export->delimiter, $export->escape_formatting );
									}
								}
								$csv .= $separator;
							}
							$csv = substr( $csv, 0, -1 ) . "\n";
						} else if( $export->args['order_items'] == 'individual' ) {

							/* Order items formatting: SPECK-IPHONE<br />INCASE-NANO<br />- */

							$order->order_items_count = 0;
							foreach( $order->order_items as $order_item ) {
								$order->order_items_product_id = '';
								$order->order_items_variation_id = '';
								$order->order_items_sku = '';
								$order->order_items_name = '';
								$order->order_items_variation = '';
								$order->order_items_tax_class = '';
								$order->order_items_quantity = '';
								$order->order_items_total = '';
								$order->order_items_subtotal = '';
								$order->order_items_tax = '';
								$order->order_items_tax_subtotal = '';
								$order->order_items_type = '';
								if( empty( $order_item->sku ) )
									$order_item->sku = '-';
								$order->order_items_product_id .= $order_item->product_id;
								$order->order_items_variation_id .= $order_item->variation_id;
								$order->order_items_sku .= $order_item->sku;
								$order->order_items_name .= $order_item->name;
								$order->order_items_variation .= $order_item->variation;
								$order->order_items_tax_class .= $order_item->tax_class;
								if( empty( $order_item->quantity ) )
									$order_item->quantity = '-';
								$order->order_items_quantity .= $order_item->quantity;
								$order->order_items_total .= $order_item->total;
								$order->order_items_subtotal .= $order_item->subtotal;
								$order->order_items_tax .= $order_item->tax;
								$order->order_items_tax_subtotal .= $order_item->tax_subtotal;
								$order->order_items_type .= $order_item->type;
								$order = apply_filters( 'woo_cd_order_items_individual', $order, $order_item );
								foreach( $export->fields as $key => $field ) {
									if( isset( $order->$key ) ) {
										if( is_array( $field ) ) {
											foreach( $field as $array_key => $array_value ) {
												if( !is_array( $array_value ) )
													$csv .= woo_ce_escape_csv_value( $array_value, $export->delimiter, $export->escape_formatting );
											}
										} else {
											$csv .= woo_ce_escape_csv_value( $order->$key, $export->delimiter, $export->escape_formatting );
										}
									}
									$csv .= $separator;
								}
								$csv = substr( $csv, 0, -1 ) . "\n";
							}
						}
					}
					unset( $orders, $order );
				}
				break;

			case 'customers':
				$fields = woo_ce_get_customer_fields( 'summary' );
				$export->fields = array_intersect_assoc( $fields, $export->fields );
				if( $export->fields ) {
					foreach( $export->fields as $key => $field )
						$export->columns[] = woo_ce_get_customer_field( $key );
				}
				$customers = woo_cd_get_orders( 'customers', $export->args );
				if( $customers ) {
					$size = count( $export->columns );
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
					}
					foreach( $customers as $customer ) {
						foreach( $export->fields as $key => $field ) {
							if( isset( $customer->$key ) )
								$csv .= woo_ce_escape_csv_value( $customer->$key, $export->delimiter, $export->escape_formatting );
							$csv .= $separator;
						}
						$csv = substr( $csv, 0, -1 ) . "\n";
					}
					unset( $customers, $customer );
				}
				break;

			case 'coupons':
				$fields = woo_ce_get_coupon_fields( 'summary' );
				$export->fields = array_intersect_assoc( $fields, $export->fields );
				if( $export->fields ) {
					foreach( $export->fields as $key => $field )
						$export->columns[] = woo_ce_get_coupon_field( $key );
				}
				$coupons = woo_cd_get_coupons();
				if( $coupons ) {
					$size = count( $export->columns );
					for( $i = 0; $i < $size; $i++ ) {
						if( $i == ( $size - 1 ) )
							$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . "\n";
						else
							$csv .= woo_ce_escape_csv_value( $export->columns[$i], $export->delimiter, $export->escape_formatting ) . $separator;
					}
					foreach( $coupons as $coupon ) {
						foreach( $export->fields as $key => $field ) {
							if( isset( $coupon->$key ) )
								$csv .= woo_ce_escape_csv_value( $coupon->$key, $export->delimiter, $export->escape_formatting );
							$csv .= $separator;
						}
						$csv = substr( $csv, 0, -1 ) . "\n";
					}
					unset( $coupons, $coupon );
				}
				break;

		}
		return $csv;

	}
	add_filter( 'woo_ce_export_dataset', 'woo_cd_export_dataset', 10, 2 );

	/* End of: WordPress Administration */

}
?>