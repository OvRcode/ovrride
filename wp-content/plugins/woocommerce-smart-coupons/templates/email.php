<?php if (!defined('ABSPATH')) exit; ?>

<?php woocommerce_get_template('emails/email-header.php', array( 'email_heading' => $email_heading )); ?>

<?php echo $message_from_sender; ?>

<p><?php echo sprintf(__("To redeem your discount use the following coupon code during checkout:", 'wc_smart_coupons'), $blogname); ?></p>

<strong style="margin: 10px 0; font-size: 2em; line-height: 1.2em; font-weight: bold; display: block; text-align: center;" title="<?php echo __( 'Click to apply', 'wc_smart_coupons' ); ?>">
	<a href="<?php echo home_url() . '?sc-page=shop&coupon-code=' . $coupon_code; ?>" style="text-decoration: none; color: hsl(0, 0%, 45%);">
	<?php echo $coupon_code; ?>
	</a>
</strong>

<center><a href="<?php echo $url; ?>"><?php echo sprintf(__("Visit store",'wc_smart_coupons') ); ?></a></center>

<?php if ( !empty( $from ) ) { ?>
	<p><?php echo __( 'You got this gift card', 'wc_smart_coupons' ) . ' ' . $from . $sender; ?></p>
<?php } ?>

<div style="clear:both;"></div>

<?php woocommerce_get_template('emails/email-footer.php'); ?>