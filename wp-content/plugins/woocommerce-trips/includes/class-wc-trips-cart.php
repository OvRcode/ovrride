<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Cart {
    public $fields;
    public function __construct() {
       add_action( 'woocommerce_trip_add_to_cart', array( $this, 'add_to_cart' ), 30 );
       add_filter( 'woocommerce_report_out_of_stock_query_from', array($this, 'out_of_stock'), 10, 1 );
       add_action( 'woocommerce_add_to_cart', array( $this, 'save_trip_fields'), 1, 5 );
       add_filter( 'woocommerce_cart_item_name', array( $this, 'render_meta_on_cart_item'), 1, 3 );
       add_filter( 'woocommerce_add_cart_item_data',array($this, 'force_individual_cart_items'), 10, 2 );
       add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 3 );
//       add_filter( 'woocommerce_get_availability', array( $this, 'custom_stock_totals' ), 20, 3);
       
       $this->fields = array( "wc_trip_first" => "First", "wc_trip_last" => "Last", "wc_trip_email" => "Email",
        "wc_trip_phone" => "Phone", "wc_trip_passport_num" => "Passport Number","wc_trip_passport_country" => "Passport Country",
        "wc_trip_dob" => "Date of birth", "wc_trip_primary_package" => "primary", "wc_trip_secondary_package" => "secondary",
        "wc_trip_tertiary_package" => "tertiary", "wc_trip_pickup_location" => "Pickup Location");
    }
    
    public function order_item_meta( $item_id, $values, $cart_item_key) {
        global $woocommerce;
        error_log("ORDER_ITEM_META!");
        foreach ( $this->fields as $key => $value ) {
            if ( WC()->session->__isset( $cart_item_key . "_" . $key ) ) {
                if ( "primary" == $value || "secondary" == $value || "tertiary" == $value) {
                    $label = WC()->session->get($cart_item_key . "_" . $key . "_label");
                    $value = WC()->session->get( $cart_item_key . "_" . $key );
                    wc_add_order_item_meta( $item_id, $label, $value);
                } else {
                    wc_add_order_item_meta( $item_id, $value, WC()->session->get( $cart_item_key . "_" . $key ));
                }
            }
        }
    }
    
    public function force_individual_cart_items( $cart_item_data, $product_id ) {
        $unique_cart_item_key = md5( microtime().rand() );
        $cart_item_data['unique_key'] = $unique_cart_item_key;
        
        return $cart_item_data;
    }
    public function save_trip_fields( $cart_item_key, $product_id = null, $quantity= null, $variation_id= null, $variation= null) {
        foreach( $this->fields as $key => $value ) {
            if( isset( $_REQUEST[$key]) ) {
                if ( "primary" == $value || "secondary" == $value || "tertiary" == $value) {
                    WC()->session->set( $cart_item_key . "_" . $key . "_label", $_REQUEST[$key . "_label"]);
                    WC()->session->set( $cart_item_key . "_" . $key . "_cost", $_REQUEST[$key . "_cost"]);
                }
                WC()->session->set( $cart_item_key . "_" . $key, $_REQUEST[$key] );
            }
        }
    }
    public function render_meta_on_cart_item( $title = null, $cart_item = null, $cart_item_key = null ) {
        echo $title;
        echo "<dl class='variation'>";
        foreach( $this->fields as $key => $value ) {
            if ( $cart_item_key && WC()->session->__isset( $cart_item_key . "_" . $key) ){
                $inputValue = WC()->session->get( $cart_item_key . "_" . $key);
                $key_parts = explode( "_", $key);
                if ( isset($key_parts[3]) && "package" == $key_parts[3] ) {
                    $label = WC()->session->get( $cart_item_key . "_" . $key . "_label");
                    $cost = WC()->session->get( $cart_item_key . "_" . $key . "_cost");
                    if ( "" !== $cost ) {
                        $cost = " +" . $cost;
                    }
                } else {
                    $label = $value;
                }
                if ( "wc_trip_pickup_location" == $key ) {
                    $time = get_post_meta( $inputValue, '_pickup_location_time', true);
                    if ( "" !== $time ) {
                        $time = ", " . date("g:i a", strtotime($time));
                    }
                    $inputValue = get_the_title( $inputValue );
                }
                echo<<<CARTMETA
                <dt class="variation-{$label}">{$label}: </dt>
                <dd class="variation-{$label}">{$inputValue}{$cost}{$time}</dd>
CARTMETA;
                unset($cost);
                unset($time);
            }
        }
        echo "</dl>";
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