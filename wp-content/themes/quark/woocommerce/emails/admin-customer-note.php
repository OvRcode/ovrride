<?php
/**
 * Admin new order email
 *
 * @author WooThemes
 * @package WooCommerce/Templates/Emails/HTML
 * @version 2.0.0
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php printf( __( 'You have received an order from %s with a note.', 'woocommerce' ), $order->billing_first_name . ' ' . $order->billing_last_name ); ?></p>
<p>Order <?php echo $order->get_order_number(); ?></p>
<p>Note:<?php print($order->customer_note);?></p>
<p><a href='<?php echo home_url( $path = '/', $scheme = https )."wp-admin/post.php?post=".substr($order->get_order_number(),1)."&action=edit" ?>'>View/Edit Order</a></p>
<?php do_action( 'woocommerce_email_before_order_table', $order, true ); ?>

<?php do_action( 'woocommerce_email_footer' ); ?>