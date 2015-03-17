<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<br /><br />
<div id="call_for_credit">
	<?php
		$currency_symbol = get_woocommerce_currency_symbol();
	?>
	<p style="float: left">
	<?php 
		_e( stripslashes( $smart_coupon_store_gift_page_text ) , 'wc_smart_coupons') . '(' . $currency_symbol . ')'; 
		echo '</p><br /><br />';
		echo "<input id='credit_called' step='any' type='number' min='1' name='credit_called' value='' autocomplete='off' autofocus />";	// This line is required in this template
	?>
	<p id="error_message" style="color: red;"></p>
</div><br />