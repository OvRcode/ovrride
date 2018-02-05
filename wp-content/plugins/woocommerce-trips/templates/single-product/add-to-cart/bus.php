<?php
/**
 * Trip product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;

do_action( 'woocommerce_before_add_to_cart_form' );

wp_enqueue_style("wc-trips-bus-styles", WC_TRIPS_PLUGIN_URL . "/assets/css/wc_trip_bus.min.css", null, WC_TRIPS_VERSION );

include("_noscript.php");
error_log($lesson_age);
?>

<p class="stock"><?php echo $stock; ?> </p>
<br />
<?php if ( $product->is_purchasable() ): ?>
<form class="cart" method="post" enctype='multipart/form-data'>

        <div id="wc-trips-form">
        <div id="errors">
        </div>
        <?php

        include("_name.php");
        include("_email-phone.php");
        echo "<input type='hidden' name='wc_trip_type' id='wc_trip_type' value='" . $trip_type . "' data-required='true' />";
				echo "<input type='hidden' name='wc_trip_lesson_restriction' id='wc_trip_lesson_restriction' value='" . $lesson_age . "' />";

        	foreach ( $packages as $type => $info ) {
            	if ( $info ) {
                	include("_package.php");
            	}
        	}

        if ( $pickups ) {
            echo <<<PICKUPS
                <div class="pickups">
                    <label for="wc_trip_pickup_location"><strong>Pickup Location</strong> <span class="required">*</span></label>
                    <select name="wc_trip_pickup_location" id="wc_trip_pickup_location" data-required="true">
                        {$pickups['none']}
                    </select>
                </div>
PICKUPS;
        }
        include("_dob-check.php");
        include("_add-to-cart.php");
     endif;
do_action( 'woocommerce_after_add_to_cart_form' );
?>
