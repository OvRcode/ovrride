<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Cart {
    public function __construct() {
       add_action( 'woocommerce_trip_add_to_cart', array( $this, 'add_to_cart' ), 30 );
    }
    public function add_to_cart() {
        global $product;
        
        $meta = get_post_meta($product->id);
        $type = get_post_meta( $product->id, '_wc_trip_type', true );
        $fields = array("first","last","email","phone");
        switch( $type ) {
            case "flight_domestic":
                $fields[] = "dob";
                break;
            case "flight_international":
                $fields[] = "passport_num";
                $fields[] = "passport_country";
                break;
        }
        
        wc_get_template( 'single-product/add-to-cart/trip.php', array( 'fields' => $fields ), 'woocommerce-trips', WC_TRIPS_TEMPLATE_PATH );
        
    }
}
new WC_Trips_Cart();