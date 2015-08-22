<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Cart {
    public function __construct() {
       add_action( 'woocommerce_trip_add_to_cart', array( $this, 'add_to_cart' ), 30 );
       add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 10, 1 );
       add_filter( 'woocommerce_report_out_of_stock_query_from', 'out_of_stock', 10, 1 );
    }
    public function out_of_stock() {
        echo "<h1>Out of Stock</h1>";
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
        $trip_type = get_post_meta( $product->id, '_wc_trip_type', true);
        $pickups = $this->pickupField( $product->id );
        $template_data = array('fields' => $fields, 'trip_type' => $trip_type, 'pickups' => $pickups);
        wc_get_template( 'single-product/add-to-cart/trip.php', $template_data, 'woocommerce-trips', WC_TRIPS_TEMPLATE_PATH );
        
    }
    public function add_cart_item( $passed) {
        var_dump($passed);
        $product = wc_get_product($passed->product_id);
        var_dump($product);
        /*var_dump(print_r($product, true));
        if ( $product->product_type !== 'trip' ) {
            return $passed;
        }
        error_log(" PASSED PRODUCT CHECK");
        $this->getPostedData();
        */
        return $passed;
    }
    public function getPostedData() {
        var_dump($_POST);
        //error_log( print_r($_POST, true));
    }
    private function pickupField( $post_id ) {
        $pickup_ids = get_post_meta( $post_id, '_wc_trip_pickups', true);
        if ( "array" == gettype($pickup_ids) && count($pickup_ids) > 0) {
            $pickup_output = "<option value=''>Select Pickup Location</option>";
            foreach( $pickup_ids as $key => $value ) {
                $pickup = get_post( absint($value) );
                $time = get_post_meta( $pickup->ID, '_pickup_location_time', true );
                if ( $time && "" != $time ) {
                    $time = " - " . date("g:i a", strtotime( $time ) );
                }
                $pickup_output .= "<option value='" . $pickup->ID . "'>" . $pickup->post_title . $time . "</option>";
            }
            return $pickup_output;
        } else { 
            return false;
        }
    }
}
new WC_Trips_Cart();