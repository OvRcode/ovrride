<?php
/**
 * Trip product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $product;
do_action( 'woocommerce_before_add_to_cart_form' );

include("_noscript.php");

wp_enqueue_style("wc-trips-flight-styles", WC_TRIPS_PLUGIN_URL . "/assets/css/wc_trip_flight.css", null, WC_TRIPS_VERSION );
?>



<p class="stock">
    <?php echo $stock; ?>
</p>
<br />
<?php if ( $product->is_purchasable() ): ?>
<form class="cart" method="post" enctype='multipart/form-data'>

        <div id="wc-trips-form">
        <div id="errors">
        </div>
        <label><strong>Name</strong></label><br />
				<div class="name">
            <div id="firstGroup">
              <input type="text" id="wc_trip_first" name="wc_trip_first" data-required="true" />
              <label for="wc_trip_first">First <span class="required">*</span></label>
            </div>
            <div class="middleGroup">
						  <input type="text" id="wc_trip_middle" name="wc_trip_middle" data-required="false" />
              <label for="wc_trip_middle">Middle</label>
            </div>
            <div class="lastGroup">
              <input type="text" id="wc_trip_last" name="wc_trip_last" data-required="true" />
              <label for="wc_trip_last">Last <span class="required">*</span></label>
            </div>
				    <div class="nameComment">
              <br />
              <p>
                Please fill out full legal name with correct spelling as it appears on your passport or travel document. If you do not have a middle name, leave that field blank.
              </p>
            </div>
				</div>
        <div class="gender">
          <label for="wc_trip_gender"><strong>Gender</strong> <span class="required">*</span></label>
          <select id="wc_trip_gender" name="wc_trip_gender" data-required="true">
            <option value="">Select Gender</option>
            <option value="female">Female</option>
            <option value="male">Male</option>
          </select>
          <p>This information is required by our airline partners to finalize flight reservations.</p>
        </div>
        <?php include("_dob.php"); ?>
        <br />
        <br />
				<?php include("_email-phone.php"); ?>
        <?php if( "international_flight" === $trip_type ): ?>
        <div class="passport">
          <label><strong>Passport Information</strong></label>
          <br />
          <div class="passportNum">
            <label for="wc_trip_passport_num">Passport #</label><br />
            <input type="text" id="wc_trip_passport_num" name="wc_trip_passport_num" data-required="false" />
          </div>
          <div class="passportCountry">
            <label for="wc_trip_passport_country">Passport Country of Issue</label><br />
            <input type="text" id="wc_trip_passport_country" name="wc_trip_passport_country" data-required="false" />
          </div>
          <div class="passportComment">
            <p>
              Please fill out accurately, as this information will be used to secure flight and/or other international reservations. If you prefer to make your reservation without filling this information out online, please contact us at info@ovrride.com to get us this information as soon as possible.
            </p>
          </div>
        </div>
        <?php
        endif;
        echo "<input type='hidden' name='wc_trip_type' id='wc_trip_type' value='" . $trip_type . "' data-required='true' />";

        	foreach ( $packages as $type => $info ) {
            	if ( $info ) {
                	echo <<<PACKAGE
                  <br />
                    	<div class='packages'>
                        	<label for="wc_trip_{$type}_package" ><strong>{$info['label']}</strong> <span class="required">*</span></label>
                        	<select name="wc_trip_{$type}_package" id="wc_trip_{$type}_package" data-required="true">
                        	<option value="">Select option</option>
PACKAGE;
                	echo $info['html'];
                	echo "</select></div>";
                	echo "<input type='hidden' name='wc_trip_{$type}_package_label' value='{$info['label']}' />";
            	}
        	}
include("_add-to-cart.php");
endif;
do_action( 'woocommerce_after_add_to_cart_form' );
?>
