<?php
/**
 * Trip product add to cart
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $woocommerce, $product;
if ( ! $product->is_purchasable() ) {
	return;
}
do_action( 'woocommerce_before_add_to_cart_form' ); 
?>

<noscript><?php _e( 'Your browser must support JavaScript in order to make a booking.', 'woocommerce-bookings' ); ?></noscript>

<form class="cart" method="post" enctype='multipart/form-data'>

        <div id="wc-trips-form">
        <p class="form-field form-field-wide" id="wc_trip_first">
            <label for="wc_trip_first">First <span class="required">*</span></label>
            <input type="text" name="wc_trip_first" data-required="true" />
        </p>
        <p class="form-field form-field-wide" id="wc_trip_last">
            <label for="wc_trip_last">Last <span class="required">*</span></label>
            <input type="text" name="wc_trip_last" data-required="true" />
        </p><br /><br />
        <p id="firstValidation"></p>
        <p class="form-field form-field-wide" id="wc_trip_email">
            <label for="wc_trip_email">Email <span class="required">*</span></label>
            <input type="text" name="wc_trip_email" data-required="true" />
        </p>
        <p id="emailValidation"></p>
        <p class="form-field form-field-wide" id="wc_trip_phone">
            <label for="wc_trip_phone">Phone <span class="required">*</span></label>
            <input type="text" name="wc_trip_phone" data-required="true" />
        </p>
        <?php 
        echo "<input type='hidden' name='wc_trip_type' id='wc_trip_type' value='" . $trip_type . "' data-required='true' />";
        switch ( $trip_type ) {
            case "international_flight":
                echo <<<PASSPORT
                    <p class="form-field form-field-wide" id="wc_trip_passport_num">
                        <label for="wc_trip_passport_num">Passport #</label>
                        <input type="text" name="wc_trip_passport_num" data-required="false" />
                    </p>
                    <p class="form-field form-field-wide" id="wc_trip_passport_country">
                        <label for="wc_trip_passport_country">Passport Country of Issue</label>
                        <input type="text" name="wc_trip_passport_country" data-required="false" />
                    </p>
PASSPORT;
            case "domestic_flight":
                echo <<<DOB
                    <p class="form-field form-field-wide" id="wc_trip_dob">
                    <div class="DOB">
                        <label for="wc_trip_dob" class="dob_label">Date of Birth<span class="required">*</span></label>
                        <span style="float: left;"><input type="text" maxlength="2" name="wc_trip_dob_month" id="wc_trip_dob_month"/><label class="dob_label">MM</label></span>
                        <span style="float: left;"><input type="text" maxlength="2" name="wc_trip_dob_day" id="wc_trip_dob_day"/><label class="dob_label">DD</label></span>
                        <span style="float: left;"><input type="text" maxlength="4" name="wc_trip_dob_year" id="wc_trip_dob_year"/><label class="dob_label">YYYY</label></span>
                    </div>
                    </p>
DOB;
                break;
            default:
            echo <<<AGECHECK
                <p class="form-field form-field-wide" id="wc_trip_age_check">
                    <label for="wc_trip_age_check">Is this guest at least 18 years of age?<span class="required">*</span>
                    <br />
                    <input type="radio" name="wc_trip_age_check" value="yes" data-required="true"> Yes
                    <br />
                    <input type="radio" name="wc_trip_age_check" value="no" data-required="true"> No
                    <br />
                    <div class="DOB">
                    	<label for="wc_trip_dob" class="dob_label">Date of Birth<span class="required">*</span></label>
                        <span style="float: left;"><input type="text" maxlength="2" name="wc_trip_dob_month" id="wc_trip_dob_month"/><label class="dob_label">MM</label></span>
                        <span style="float: left;"><input type="text" maxlength="2" name="wc_trip_dob_day" id="wc_trip_dob_day"/><label class="dob_label">DD</label></span>
                        <span style="float: left;"><input type="text" maxlength="4" name="wc_trip_dob_year" id="wc_trip_dob_year"/><label class="dob_label">YYYY</label></span>
                        <input type="hidden" id="wc_trip_dob_field" value="" />
                    </div>
                    <p>Guests under 18 years of age are welcome to join us as long as they abide by the terms of the <a href="http://ovrride.com/ovrride-age-policy/" target="_blank"><strong>OvRride Age Policy</strong></a>, and we are aware of the underage guest.</p>
                </p>
AGECHECK;
                break;
        }
        $packages = [
            "primary"   => $product->output_packages("primary"),
            "secondary" => $product->output_packages("secondary"),
            "tertiary"  => $product->output_packages("tertiary")
        ];
        foreach ( $packages as $type => $info ) {
            if ( $info ) {
                echo <<<PACKAGE
                    <p class='form-field'>
                        <label for="wc_trip_{$type}_package" >{$info['label']} <span class="required">*</span></label>
                        <select name="wc_trip_{$type}_package" id="wc_trip_{$type}_package" data-required="true">
                        <option value="">Select option</option>
PACKAGE;
                echo $info['html'];
                echo "</select></p>";
                echo "<input type='hidden' name='wc_trip_{$type}_package_label' value='{$info['label']}' />";
            }
        }
        
        if ( $pickups ) {
            echo <<<PICKUPS
                <p class='form-field'>
                    <label for="wc_trip_pickup_location">Pickup Location <span class="required">*</span></label>
                    <select name="wc_trip_pickup_location" id="wc_trip_pickup_location" data-required="true">
                        {$pickups}
                    </select>
                </p>
PICKUPS;
        }
        ?>
        
        <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
        
        <div class="wc-trip-cost" style="display:none"></div>

    </div>

    <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->id ); ?>" />
    
    <button type="submit" class="single_add_to_cart_button button alt"><?php echo $product->single_add_to_cart_text(); ?></button>
    
    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>