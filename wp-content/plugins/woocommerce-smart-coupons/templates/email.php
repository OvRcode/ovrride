<?php if (!defined('ABSPATH')) exit; ?>

<?php 
	if ( function_exists( 'wc_get_template' ) ) {
		wc_get_template('emails/email-header.php', array( 'email_heading' => $email_heading ));
	} else {
		woocommerce_get_template('emails/email-header.php', array( 'email_heading' => $email_heading ));
	}
?>

<?php echo $message_from_sender; ?>

<p><?php echo sprintf(__("To redeem your discount use the following coupon code during checkout:", 'wc_smart_coupons'), $blogname); ?></p>

<?php
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

<strong style="margin: 10px 0; font-size: 2em; line-height: 1.2em; font-weight: bold; display: block; text-align: center;" title="<?php echo __( 'Click to apply', 'wc_smart_coupons' ); ?>">
	<a href="<?php echo $coupon_target; ?>" style="text-decoration: none; color: hsl(0, 0%, 45%);">
	<?php echo $coupon_code; ?>
	</a>
</strong>

<center><a href="<?php echo $url; ?>"><?php echo sprintf(__("Visit store",'wc_smart_coupons') ); ?></a></center>

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