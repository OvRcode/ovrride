<?php if (!defined('ABSPATH')) exit; ?>

<?php 
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template('emails/email-header.php', array( 'email_heading' => $email_heading ));
	} else {
		woocommerce_get_template('emails/email-header.php', array( 'email_heading' => $email_heading ));
	}
?>

<style type="text/css">
		.coupon-container {
			margin: .2em;
			box-shadow: 0 0 5px #e0e0e0;
			display: inline-table;
			text-align: center;
			cursor: pointer;
		}
		.coupon-container.blue { background-color: #e0f7ff }
		
		.coupon-container.medium {
			padding: .4em;
			line-height: 1.4em;
		}

		.coupon-content.small { padding: .2em 1.2em }
		.coupon-content.dashed { border: 2.3px dashed }
		.coupon-content.blue { border-color: #c0d7ee }
		.coupon-content .code {
			font-family: monospace;
			font-size: 1.2em;
			font-weight:700;
		}

		.coupon-content .coupon-expire,
		.coupon-content .discount-info {
			font-family: Helvetica, Arial, sans-serif;
			font-size: 1em;
		}
</style>

<?php echo $message_from_sender; ?>

<p><?php echo sprintf(__("To redeem your discount use the following coupon during checkout:", 'wc_smart_coupons'), $blogname); ?></p>

<?php

$coupon = new WC_Coupon( $coupon_code );

$coupon_data = $this->get_coupon_meta_data( $coupon );

	$coupon_target = '';
	$wc_url_coupons_active_urls = get_option( 'wc_url_coupons_active_urls' );
	if ( !empty( $wc_url_coupons_active_urls ) ) {
		$coupon = get_page_by_title( strtolower( $coupon_code ), 'ARRAY_A', 'shop_coupon' );
		$coupon_target = ( !empty( $wc_url_coupons_active_urls[ $coupon['ID'] ]['url'] ) ) ? $wc_url_coupons_active_urls[ $coupon['ID'] ]['url'] : '';
	}
	if ( !empty( $coupon_target ) ) {
		$coupon_target = home_url( '/' . $coupon_target );
	} else {
		$coupon_target = home_url( '/?sc-page=shop&coupon-code=' . $coupon_code );
	}
?>

<div style="margin: 10px 0; text-align: center;" title="<?php echo __( 'Click to visit store. This coupon will be applied automatically.', 'wc_smart_coupons' ); ?>">
	<a href="<?php echo $coupon_target; ?>" style="color: #444;">

		<div class="coupon-container blue medium" style="cursor:pointer; text-align:center">
			<?php
				echo '<div class="coupon-content blue dashed small">
					<div class="discount-info">'.( ( !empty( $coupon_data['coupon_amount'] ) ) ? $coupon_data['coupon_amount'] : '' )." ". ( ( !empty( $coupon_data['coupon_type'] ) ) ? $coupon_data['coupon_type'] : '' ).'</div>
					<div class="code">'. $coupon->code .'</div>';
					if( !empty( $coupon->expiry_date) ) {
						$expiry_date = $this->get_expiration_format( $coupon->expiry_date );
						echo '<div class="coupon-expire">'. $expiry_date .'</div>';    
					} else {
						echo '<div class="coupon-expire">'. __( 'Never Expires ', 'wc_smart_coupons' ).'</div>';    
					}
				echo '</div>';
			?>
		</div>
	</a>
</div>

<center><a href="<?php echo $url; ?>"><?php echo sprintf(__("Visit Store",'wc_smart_coupons') ); ?></a></center>

<?php if ( !empty( $from ) ) { ?>
	<p><?php echo __( 'You got this gift card', 'wc_smart_coupons' ) . ' ' . $from . $sender; ?></p>
<?php } ?>

<div style="clear:both;"></div>

<?php 
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template('emails/email-footer.php');
	} else {
		woocommerce_get_template('emails/email-footer.php');
	}
?>