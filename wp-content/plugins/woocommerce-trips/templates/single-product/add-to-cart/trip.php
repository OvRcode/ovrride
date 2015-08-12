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
            <label for="wc_trip_first">First</label>
            <input type="text" name="wc_trip_first" />
        </p>
        <p class="form-field form-field-wide" id="wc_trip_last">
            <label for="wc_trip_last">Last</label>
            <input type="text" name="wc_trip_last" />
        </p><br /><br />
        <p class="form-field form-field-wide" id="wc_trip_email">
            <label for="wc_trip_email">Email</label>
            <input type="text" name="wc_trip_email" />
        </p>
        <p class="form-field form-field-wide" id="wc_trip_phone">
            <label for="wc_trip_phone">Phone</label>
            <input type="text" name="wc_trip_phone" />
        </p>
        <?php 
        $trip_type = get_post_meta( $product->id, '_wc_trip_type', true);
        echo "<input type='hidden' name='wc_trip_type' id='wc_trip_type' value='" . $trip_type . "' />";
        switch ( $trip_type ) {
            case "international_flight":
                echo <<<PASSPORT
                    <p class="form-field form-field-wide" id="wc_trip_passport_num">
                        <label for="wc_trip_passport_num">Passport #</label>
                        <input type="text" name="wc_trip_passport_num">
                    </p>
                    <p class="form-field form-field-wide" id="wc_trip_passport_country">
                        <label for="wc_trip_passport_country">Passport Country of Issue</label>
                        <input type="text" name="wc_trip_passport_country" />
                    </p>
PASSPORT;
            case "domestic_flight":
                echo <<<DOB
                    <p class="form-field form-field-wide" id="wc_trip_dob">
                        <label for="wc_trip_dob">Date of Birth ( MM/DD/YYYY )</label>
                        <input type="text" name="wc_trip_dob" max-length="9" />
                    </p>
DOB;
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
                        <label for="wc_trip_{$type}_package">{$info['label']}</label>
                        <select name="wc_trip_{$type}_package" id="wc_trip_{$type}_package">
                        <option value="">Select option</option>
PACKAGE;
                echo $info['html'];
                echo "</select></p>";
            }
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