<?php
/**
 * Trip product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global  $product;

wp_enqueue_style("wc-trips-beach-bus-styles", WC_TRIPS_PLUGIN_URL . "/assets/css/wc_trip_beach_bus.min.css", null, WC_TRIPS_VERSION );

do_action( 'woocommerce_before_add_to_cart_form' );

include("_noscript.php");
?>
<p class="stock"> <?php echo $stock; ?> </p>
<br />
<?php if ( $product->is_purchasable() ): ?>
<form class="cart" method="post" enctype='multipart/form-data'>

        <div id="wc-trips-form">
        <div id="errors">
        </div>
        <?php
          include("_name.php");
          include("_email-phone.php");
        ?>

        <?php
        echo "<input type='hidden' name='wc_trip_type' id='wc_trip_type' value='" . $trip_type . "' data-required='true' />";


        	foreach ( $packages as $type => $info ) {
            	if ( $info ) include("_package.php");
        	}
        echo $product->beach_bus_pickups();
				include("_dob-check.php");
        include("_dob.php");
        include("_add-to-cart.php");
      endif;
      do_action( 'woocommerce_after_add_to_cart_form' );
?>
