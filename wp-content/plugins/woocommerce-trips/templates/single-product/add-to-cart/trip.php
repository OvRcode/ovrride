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

        <?php 
        echo "<input type='hidden' name='wc_trip_type' id='wc_trip_type' value='" . get_post_meta( $product->id, '_wc_trip_type', true) . "' />";
        
        foreach ( $fields as $key => $field ) {
            echo <<<FIELD
                <p class="form-field form-field-wide">
                    <label for="wc_trip_{$field}">{$field}</label><br />
                    <input type="text" name="wc_trip_{$field}"></input>
                </p>
FIELD;
        }
        $packages = [
            "primary"   => $product->output_packages("primary"),
            "secondary" => $product->output_packages("secondary"),
            "tertiary"  => $product->output_packages("tertiary")
        ];
        foreach ( $packages as $type => $info ) {
            // only output packages if there is data there
            if ( $info ) {
                echo <<<PACKAGE
                    <p class='form-field'>
                        <label for="wc_trip_{$type}_package">{$info['label']}</label>
                        <select name="wc_trip_{$type}_package" id="wc_trip_{$type}_package">
                        <option value="">Select option</option>
PACKAGE;
                foreach ( $info['packages'] as $key => $array ) {
                    if ( "" !== $array['cost'] ) {
                        $data_cost = "data-cost='" . $array['cost'] . "'" ;
                        $cost_label = " +" . $array['cost'];
                    } else {
                        $data_cost = "";
                        $cost_label = "";
                    }
                    
                    echo <<<OPTION
                        <option value="{$array['description']}" {$data_cost}>{$array['description']} {$cost_label}</option>
OPTION;
                }
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