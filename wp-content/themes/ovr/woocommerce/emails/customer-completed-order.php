<?php
/**
 * Customer completed order email
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
$pickups["Skate Brooklyn"]["url"] = "https://www.google.com/maps/place/Skate+Brooklyn/@40.68163,-73.979789,15z/";
$pickups["Skate Brooklyn"]["instructions"] = "corner of St. Marks & 4th Ave.";
$pickups["Union Square"]["url"] = "https://www.google.com/maps/place/41+Union+Square+W,+New+York,+NY+10003/";
$pickups["Union Square"]["instructions"] = "NW side of USQ Park";
$pickups["Homage Brooklyn"]["url"] = "https://www.google.com/maps/place/64+Bergen+St,+Brooklyn,+NY+11201/@40.6867357,-73.9911531,17z/";
$pickups["Homage Brooklyn"]["instructions"] = "corner of Bergen & Smith";
$pickups["Homage"]["url"] = "https://www.google.com/maps/place/64+Bergen+St,+Brooklyn,+NY+11201/@40.6867357,-73.9911531,17z/";
$pickups["Homage"]["instructions"] = "corner of Bergen & Smith";
$pickups["Blades West"]["url"] = "https://www.google.com/maps/place/156+W+72nd+St,+New+York,+NY+10023/";
$pickups["Blades West"]["instructions"] = "between Broadway & Columbus";
$pickups["Blades Downtown"]["url"] = "https://www.google.com/maps/place/659+Broadway,+New+York,+NY+10012/";
$pickups["Blades Downtown"]["instructions"] = "between Bleeker & 3rd St.";
$pickups["Burton NYC"]["url"] = "https://www.google.com/maps/place/106+Spring+St,+New+York,+NY+10012/";
$pickups["Burton NYC"]["instructions"] = "between Green & Mercer";
$pickups["Upper East Side"]["url"] = "https://www.google.com/maps/place/150+E+86th+St,+New+York,+NY+10028/";
$pickups["Upper East Side"]["instructions"] = "corner of 86th & Lexington Ave.";
$pickups["REI Soho"]["url"] = "https://www.google.com/maps/place/303+Lafayette+St,+New+York,+NY+10012/";
$pickups["REI Soho"]["instructions"] = "On Lafayette South of Houston";
$pickups["Wburg, BK Union Pool"]["url"] = "https://www.google.com/maps/place/484+Union+Ave,+Brooklyn,+NY+11211/";
$pickups["Wburg, BK Union Pool"]["instructions"] = "Corner of Union & Rodney";
$pickups["Aegir Boardworks"]["url"] = "https://www.google.com/maps/place/Aegir+Boardworks/@40.703339,-73.990287,15z/";
$pickups["Aegir Boardworks"]["instructions"] = "corner of Water St & Main St.";
$pickups["Bedford Stuyvesant"]["url"] = "https://www.google.com/maps/place/54+MacDonough+St,+Brooklyn,+NY+11216/";
$pickups["Bedford Stuyvesant"]["instructions"] = "btwn Marcy & Tompkins";
$pickups["The Cliffs at LIC"]["url"] = "https://www.google.com/maps/place/11-11+44th+Dr,+Long+Island+City,+NY+11101/";
$pickups["The Cliffs at LIC"]["instructions"] = "corner of 44th Drive & 11th St";
$pickups["NJ Burton Store Menlo Park Mall"]["url"] = "https://www.google.com/maps/place/55+Parsonage+Rd,+Menlo+Park+Mall,+Edison,+NJ+08837/";
$pickups["NJ Burton Store Menlo Park Mall"]["instructions"] = "Cheesecake Factory parking lot of the Menlo Park Mall";
$pickups["Hoboken"]["url"] = "https://www.google.com/maps/place/CVS+Pharmacy+-+Photo/@40.736511,-74.030911,15z/";
$pickups["Hoboken"]["instructions"] = "CVS on the Corner of Washington & Newark";
$pickups["REI Paramus"]["url"] = "https://www.google.com/maps/place/REI/@40.914131,-74.055459,15z/";
$pickups["REI Paramus"]["instructions"] = "On Forest Ave. Off RT 4, next to Lowes";
$pickups["Freehold Raceway Mall"]["url"] = "https://www.google.com/maps/place/3681+U.S.+9,+Freehold,+NJ+07728/";
$pickups["Freehold Raceway Mall"]["instructions"] = "In far parking lot facing Dick’s Sporting Goods";
$pickups["Mount Everest"]["url"] = "https://www.google.com/maps/place/Mount+Everest/@40.991212,-74.034272,15z/";
$pickups["Mount Everest"]["instructions"] = "corner of Washington & 3rd St";
?>

<?php do_action('woocommerce_email_header', $email_heading); ?>

<p><?php echo "Psyched you’ll be joining us for a trip! Your recent order on OvRride has been completed.  No ticket is needed, we’ll have your information on file when you appear at the designated time and location for the trip you’ve reserved. Your order details are shown below for your reference:"; ?></p>
        <?php if ( preg_match("/Package/",$order->email_order_items_table( true, false, true )) ): ?>
        <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div>
                        <!--[if mso]>
                            <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="http://litmus.com" style="height:36px;v-text-anchor:middle;width:150px;" arcsize="5%" strokecolor="#EB7035" fillcolor="#EB7035">
                            <w:anchorlock/>
                            <center style="color:#ffffff;font-family:Helvetica, Arial,sans-serif;font-size:16px;">I am a button &rarr;</center>
                            </v:roundrect>
                        <![endif]-->
                        <a href="<?php echo get_site_url();?>/OvR%202014-15WAIVER.pdf.zip" style="background-color:#2BC9F1;border:1px solid ##2BC9F1;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:44px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Download Waiver</a>
                    </div>
                </td>
            </tr>
        </table>
    <p>For a smooth and prompt departure on the day of your trip, please download and print out a copy of our waiver. If you bring this 2 sided filled out and signed copy to the trip, we’ll surely appreciate it, as it will speed up our check-in process.  If you don’t have access to a printer, additional waivers will be available on the bus.</p>
    <?php endif; ?>
<?php 
// Pickup Matching
if (preg_match("/Pickup Location:.(.*).-.*/", $order->email_order_items_table( true, false, true ), $match)){
    if ( isset($pickups[$match[1]]) ){
        preg_match("/.*-.(.*(a|p)m)/", $match[0], $matchTime);
        echo "<h2>Pickup Location: " . $match[1] . ",&nbsp;". $matchTime[1]."</h2>";
        echo "<p>" . $pickups[$match[1]]["instructions"] . "<br><a href='" . $pickups[$match[1]]["url"] ."'>Directions</a></p>";
    }
}

do_action('woocommerce_email_before_order_table', $order, false); ?>

<h2><?php echo __( 'Order:', 'woocommerce' ) . ' ' . $order->get_order_number(); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php echo $order->email_order_items_table( true, false, true ); ?>
        <?php error_log($order->email_order_items_table( true, false, true )); ?>
	</tbody>
	<tfoot>
		<?php
			if ( $totals = $order->get_order_item_totals() ) {
				$i = 0;
				foreach ( $totals as $total ) {
					$i++;
					?><tr>
						<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['label']; ?></th>
						<td style="text-align:left; border: 1px solid #eee; <?php if ( $i == 1 ) echo 'border-top-width: 4px;'; ?>"><?php echo $total['value']; ?></td>
					</tr><?php
				}
			}
		?>
	</tfoot>
</table>

<?php do_action('woocommerce_email_after_order_table', $order, false); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, false ); ?>

<h2><?php _e( 'Customer details', 'woocommerce' ); ?></h2>

<?php if ($order->billing_email) : ?>
	<p><strong><?php _e( 'Email:', 'woocommerce' ); ?></strong> <?php echo $order->billing_email; ?></p>
<?php endif; ?>
<?php if ($order->billing_phone) : ?>
	<p><strong><?php _e( 'Tel:', 'woocommerce' ); ?></strong> <?php echo $order->billing_phone; ?></p>
<?php endif; ?>

<?php woocommerce_get_template('emails/email-addresses.php', array( 'order' => $order )); ?>

<?php do_action('woocommerce_email_footer'); ?>