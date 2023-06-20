<?php
/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$pickups 				= array();
$toBeach 				= array();
$fromBeach			= array();
$has_trip 		 	= false;
$has_flight		= false;
$has_beach_bus	= false;
$wework         = false;

foreach($order->get_items() as $order_item_id ) {
  
    if ( has_term( 'wework', 'product_cat', $order_item_id['product_id'] ) ) {
      $wework = true;
      $email_heading = '<img src="https://lh3.googleusercontent.com/EyHauB03faA07sgKthv3zkmNTVUf5hWTaXgEi61gGEroaUFGrhb2OhQYgI28TILNPAnDKlK2YQ=w600"/>'.$email_heading;
    }
  

    $product = wc_get_product( $order_item_id['product_id']);
		$subType = $product->get_meta( '_wc_trip_type', true, 'view');
		if ( "beach_bus" == $subType ) {
			$has_beach_bus = true;
		}
    if ( $product->is_type('trip') ) {
        $has_trip = true;
    }
		if ( "domestic_flight" === $subType || "international_flight" === $subType) {
			$has_flight = true;
		}
		if ( isset($order_item_id['to_beach_id'])) {
			$toBeach[$order_item_id['to_beach_id']]['title'] = get_the_title( $order_item_id['to_beach_id'] );
			$toBeach[$order_item_id['to_beach_id']]['address'] = get_post_meta( $order_item_id['to_beach_id'], '_pickup_location_address', true);
			$toBeach[$order_item_id['to_beach_id']]['cross_st'] = get_post_meta( $order_item_id['to_beach_id'], '_pickup_location_cross_st', true);
			$toBeach[$order_item_id['to_beach_id']]['time'] = get_post_meta( $order_item_id['to_beach_id'], '_pickup_location_time', true);
			$toBeach[$order_item_id['to_beach_id']]['time'] = (strval($toBeach[$order_item_id['to_beach_id']]['time']) == "" ? "" : date("g:i a", strtotime($toBeach[$order_item_id['to_beach_id']]['time'])));
		}
		if ( isset($order_item_id['from_beach_id'])) {
		  $fromBeach[$order_item_id['from_beach_id']]['title'] = get_the_title( $order_item_id['from_beach_id'] );
		  $fromBeach[$order_item_id['from_beach_id']]['address'] = get_post_meta( $order_item_id['from_beach_id'], '_pickup_location_address', true);
		  $fromBeach[$order_item_id['from_beach_id']]['cross_st'] = get_post_meta( $order_item_id['from_beach_id'], '_pickup_location_cross_st', true);
		  $fromBeach[$order_item_id['from_beach_id']]['time'] = get_post_meta( $order_item_id['from_beach_id'], '_pickup_location_time', true);
		  $fromBeach[$order_item_id['from_beach_id']]['time'] = (strval($fromBeach[$order_item_id['from_beach_id']]['time']) == "" ? "" : date("g:i a", strtotime($fromBeach[$order_item_id['from_beach_id']]['time'])));
		}
    if ( isset($order_item_id['Pickup Location']) ) {
        $pickups[$order_item_id['Pickup Location']]['title']    = get_the_title( $order_item_id['pickup_id'] );
        $pickups[$order_item_id['Pickup Location']]['address']  = get_post_meta( $order_item_id['pickup_id'], '_pickup_location_address', true);
        $pickups[$order_item_id['Pickup Location']]['cross_st'] = get_post_meta( $order_item_id['pickup_id'], '_pickup_location_cross_st', true);
        $pickups[$order_item_id['Pickup Location']]['time']     = get_post_meta( $order_item_id['pickup_id'], '_pickup_location_time', true);
        $pickups[$order_item_id['Pickup Location']]['time'] = (strval($pickups[$order_item_id['Pickup Location']]['time']) == "" ? "" : date("g:i a", strtotime($pickups[$order_item_id['Pickup Location']]['time'])));
    }
}

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php if ($has_trip && !$has_beach_bus && !$wework): ?>
<p><?php echo "Psyched you’ll be joining us for a trip! Your recent order on OvRride has been completed.  No ticket is needed, we’ll have your information on file when you appear at the designated time and location for the trip you’ve reserved. Your order details are shown below for your reference:"; ?></p>
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

                        <a href="<?php echo get_site_url();?>/wp-content/uploads/2018/01/ovr-waiver.pdf.zip" style="background-color:#2BC9F1;border:1px solid ##2BC9F1;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:44px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Download Waiver</a>
                    </div>
                </td>
            </tr>
        </table>
    <p>For a smooth and prompt departure on the day of your trip, please download and print out a copy of our waiver. If you bring this 2 sided filled out and signed copy to the trip, we’ll surely appreciate it, as it will speed up our check-in process.  If you don’t have access to a printer, additional waivers will be available on the bus.</p>
<?php elseif ($has_flight): ?>
	<p>Psyched you’ll be joining us for a trip! Your recent order on OvRride has been completed.  No ticket is needed, we’ll have your information on file when you appear at the designated time and location for the trip you’ve reserved. Your order details are shown below for your reference:"; ?></p>
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

	                        <a href="<?php echo get_site_url();?>/wp-content/uploads/2018/01/ovr-waiver.pdf.zip" style="background-color:#2BC9F1;border:1px solid ##2BC9F1;border-radius:3px;color:#ffffff;display:inline-block;font-family:sans-serif;font-size:16px;line-height:44px;text-align:center;text-decoration:none;width:150px;-webkit-text-size-adjust:none;mso-hide:all;">Download Waiver</a>
													<p>For all trips including flight a group meetup at the airport will be setup closer to the date of travel and after all final reservations have been made.</p>
	                    </div>
	                </td>
	            </tr>
	        </table>
	    <p>Since we'll be flying please print, sign scan and email your completed waiver to <a href="mailto:info@ovrride.com">info@ovrride.com</a> . Please send in your waiver as soon as possible, at least 48 hours before your scheduled departure.</p>
<?php elseif ($wework): ?>

	<p>If you booked a Private Room for multiple roommates be sure to send your roommates this confirmation email, prompting them to make their reservation. Balances will be charged to your account on file for any empty space in your unit equal to the cost for that package at the rate for the number of guests it was reserved for. As long as all your roommates sign on and pay, you’re set! We will send a notification before we take off to confirm the roommates on board.</p>

	<p>If you have any questions about your reservation, or if you need to make updates to your reservation please call (347) 559-1787.</p>

<?php else: ?>
<p><?php printf( __( "Hi there. Your recent order on %s has been completed. Your order details are shown below for your reference:", 'woocommerce' ), get_option( 'blogname' ) ); ?></p>
<?php endif; ?>

<?php

/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );
if (! empty($pickups) ) {
    echo "<h2>Pickup Details</h2>";
    foreach($pickups as $id => $values) {
				$urlAddress = urlencode($values['address']);
        echo <<<PICKUP
            <p><strong>{$values['title']}</strong><br />
            {$values['address']}<br />
            {$values['cross_st']}<br />
            <strong>Bus departs at {$values['time']}</strong><br />
            <a href='http://maps.google.com/?q={$urlAddress}' target='_blank'>View Map</a></p>
PICKUP;
    }
    }
	if ( ! empty($toBeach) ) {
		echo "<h2>To Beach Pickup Details</h2>";
		foreach($toBeach as $id => $values) {
			$urlAddress = urlencode($values['address']);
			echo <<<TOBEACH
			<p><strong>{$values['title']}</strong><br />
			{$values['address']}<br />
			{$values['cross_st']}<br />
			<strong>Bus departs at {$values['time']}</strong><br />
			<a href='http://maps.google.com/?q={$urlAddress}' target='_blank'>View Map</a></p
TOBEACH;
			}
	}

	if ( ! empty($fromBeach) ) {
		echo "<h2>From Beach Pickup Details</h2>";
		foreach($fromBeach as $id => $values) {
			$urlAddress = urlencode($values['address']);
			echo <<<TOBEACH
			<p><strong>{$values['title']}</strong><br />
			{$values['address']}<br />
			{$values['cross_st']}<br />
			<strong>Bus departs at {$values['time']}</strong><br />
			<a href='http://maps.google.com/?q={$urlAddress}' target='_blank'>View Map</a></p
TOBEACH;
			}
	}
/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
if($wework) : ?>
	<style>
		#wrapper { background-color: #ffffff;  }
		#template_header { background:#fafafa; }
		#template_header h1 { color: #111111; }
		#template_header_image { display: none; }
	</style>
	<?php
	endif;