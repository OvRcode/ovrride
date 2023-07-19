<div class="wrap woocommerce">
	<h2><?php _e( 'Woo Product Expiry Settings', 'product-expiry-for-woocommerce' ); ?></h2>
	<hr>
	<?php
        $default = array(
            'single_hook'   =>  '',
            'archive_hook'  =>  '',
            'date_format'   =>  get_option( 'date_format' ),
            'notify_emails' =>  '',
            'display'   	=>  'enable',
            'orderdetails'   	=>  'disable',
            'markup'   =>  __( 'Expiry Date: %date%', 'product-expiry-for-woocommerce' ),
        );
		$savedSettings = get_option( 'woope_admin_settings', $default );
	?>
	<form action="#" class="woope-form">
		<input type="hidden" name="action" value="woope_save_admin_settings">
		<table class="wp-list-table widefat fixed table-view-list posts">
			<tr>
				<th><?php _e( 'Display on Frontend', 'product-expiry-for-woocommerce' ); ?></th>
				<td>
					<select name="display">
						<option value="enable" <?php echo ($savedSettings['display'] == 'enable') ? 'selected' : '' ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
						<option value="disable" <?php echo ($savedSettings['display'] == 'disable') ? 'selected' : '' ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
					</select>
				</td>
				<td>
					<?php _e( 'Display the expiry date on single product page.', 'product-expiry-for-woocommerce' ); ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Expiry Text Markup', 'product-expiry-for-woocommerce' ); ?></th>
				<td>
					<input type="text" name="markup" class="widefat" value="<?php echo esc_attr( $savedSettings['markup'] ) ?>">
				</td>
				<td>
					<?php _e( 'Provide text to show on frontend. Use %date% for expiry date', 'product-expiry-for-woocommerce' ); ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Position of Date on single product page', 'product-expiry-for-woocommerce' ); ?></th>
				<td>
					<input type="text" name="single_hook" class="widefat" value="<?php echo esc_attr( $savedSettings['single_hook'] ) ?>">
				</td>
				<td>
					<?php _e( 'Provide hook name here.', 'product-expiry-for-woocommerce' ); ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Position of Date on shop archive page', 'product-expiry-for-woocommerce' ) ?></th>
				<td>
					<input type="text" name="archive_hook" class="widefat" value="<?php echo esc_attr( $savedSettings['archive_hook'] ) ?>">
				</td>
				<td>
					<?php _e( 'Provide hook name here.', 'product-expiry-for-woocommerce' ) ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Date Format', 'product-expiry-for-woocommerce' ) ?></th>
				<td>
					<input type="text" name="date_format" value="<?php echo esc_attr( $savedSettings['date_format'] ) ?>">
				</td>
				<td>
					<?php _e( 'Provide format for date.', 'product-expiry-for-woocommerce' ) ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Notification Emails', 'product-expiry-for-woocommerce' ) ?></th>
				<td>
					<input type="text" class="widefat" name="notify_emails" value="<?php echo esc_attr( $savedSettings['notify_emails'] ) ?>">
				</td>
				<td>
					<?php _e( 'Provide comma separated email addresses for expiry notification.', 'product-expiry-for-woocommerce' ) ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Display in Emails', 'product-expiry-for-woocommerce' ); ?></th>
				<td>
					<select name="orderdetails">
						<option value="enable" <?php echo (isset($savedSettings['orderdetails']) && $savedSettings['orderdetails'] == 'enable') ? 'selected' : '' ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
						<option value="disable" <?php echo (isset($savedSettings['orderdetails']) && $savedSettings['orderdetails'] == 'disable') ? 'selected' : '' ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
					</select>
				</td>
				<td>
					<?php _e( 'Display the expiry date in order details email.', 'product-expiry-for-woocommerce' ); ?>
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Display in Order Details', 'product-expiry-for-woocommerce' ); ?></th>
				<td>
					<select name="orderdetailsadmin">
						<option value="enable" <?php echo (isset($savedSettings['orderdetailsadmin']) && $savedSettings['orderdetailsadmin'] == 'enable') ? 'selected' : '' ?>><?php _e( 'Enable', 'product-expiry-for-woocommerce' ) ?></option>
						<option value="disable" <?php echo (isset($savedSettings['orderdetailsadmin']) && $savedSettings['orderdetailsadmin'] == 'disable') ? 'selected' : '' ?>><?php _e( 'Disable', 'product-expiry-for-woocommerce' ) ?></option>
					</select>
				</td>
				<td>
					<?php _e( 'Display the expiry date in order details admin and front.', 'product-expiry-for-woocommerce' ); ?>
				</td>
			</tr>
			
			<tr>
				<td colspan="3" class="textright">
					<input type="submit" value="<?php _e( 'Save Settings', 'product-expiry-for-woocommerce' ); ?>" class="button button-primary">
					<div class="spinner"></div>
					<span class="success-text">Settings Saved!</span>
				</td>
			</tr>
		</table>
	</form>
</div>