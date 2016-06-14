<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Cart {
    public $fields = array( "wc_trip_first" => "First", "wc_trip_last" => "Last", "wc_trip_email" => "Email",
        "wc_trip_phone" => "Phone", "wc_trip_passport_num" => "Passport Number","wc_trip_passport_country" => "Passport Country",
        "wc_trip_dob_field" => "Date of Birth", "wc_trip_age_check" => "Is this guest at least 18 years of age?",
        "wc_trip_primary_package" => "primary", "wc_trip_secondary_package" => "secondary",
        "wc_trip_tertiary_package" => "tertiary", "wc_trip_pickup_location" => "Pickup Location");

    public $package_types = array("primary", "secondary", "tertiary");
    public $orders_processed = array();

    public function __construct() {

       add_action( 'woocommerce_trip_add_to_cart', array( $this, 'add_to_cart' ), 30 );
       add_action( 'woocommerce_add_to_cart', array( $this, 'save_trip_fields'), 1, 5 );
       add_filter( 'woocommerce_cart_item_name', array( $this, 'render_meta_on_cart_item'), 1, 3 );
       add_filter( 'woocommerce_add_cart_item_data',array($this, 'force_individual_cart_items'), 10, 2 );
       add_action( 'woocommerce_add_order_item_meta', array( $this, 'order_item_meta' ), 10, 3 );
       add_action( 'woocommerce_before_calculate_totals', array($this, 'add_costs'), 1, 1 );
       add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 10, 3 );
       add_action( 'woocommerce_product_set_stock', array( $this, 'trigger_package_stock'), 10, 4);
    }

    public function trigger_package_stock( $instance ) {
        global $woocommerce;
        $cart = $woocommerce->cart->get_cart();
        foreach( $cart as $cart_id => $cart_data ) {
          if ( !in_array($cart_id,$this->orders_processed) ){
            $this->orders_processed[] = $cart_id;
            $product = wc_get_product($cart_data['product_id']);
            foreach( $this->package_types as $package ) {
                if ( WC()->session->__isset($cart_id . "_wc_trip_" . $package . "_package") ) {
                    $product->reduce_package_stock( $package, WC()->session->get($cart_id . "_wc_trip_" . $package . "_package"));
                }
            }
          }
        }

    }
    public function validate_add_cart_item( $passed, $product_id, $qty ) {
        $product      = get_product( $product_id );
        if ( $product->product_type !== 'trip' ) {
            return $passed;
        }

        $stockOk = false;
        foreach( $this->package_types as $package ) {
            if ( isset($_POST['wc_trip_' . $package . "_package"]) ){
                $post = $_POST['wc_trip_' . $package . "_package"];
                $stockCheck = $product->check_package_stock( $package, $post);
                if ( $stockCheck ) {
                    $stockOk = true;
                } else {
                    wc_add_notice("Sorry, " . $post . " is out of stock", 'error');
                    return false;
                }
            }
        }
        if ( $stockOk ) {
            return true;
        } else {
            wc_add_notice("Couldn't find specified packages, try again", 'error');
            return false;
        }
    }
    public function add_costs( $cart_object ) {
        global $woocommerce;
        foreach ( $cart_object->cart_contents as $key => $value ) {
            if ( "trip" == $value['data']->product_type) {
                if( WC()->session->__isset( $key.'_cost' ) ) {
                    $additional_costs = WC()->session->get( $key.'_cost' );
                    $value['data']->price = $additional_costs;
                }
            }
        }
    }
    private function get_package_cost( $description, $packages ) {
        foreach( $packages as $key => $array ) {
            if ( $description == $array['description'] ) {
                if ( isset( $array['cost']) ) {
                    return $array['cost'];
                } else {
                    return 0;
                }
            }
        }
    }
    private function get_pickup_cost( $id ) {
        return get_post_meta( $id, '_pickup_location_cost', true);
    }
    public function order_item_meta( $item_id, $values, $cart_item_key) {
        global $woocommerce;
        foreach ( $this->fields as $key => $value ) {
            if ( WC()->session->__isset( $cart_item_key . "_" . $key ) ) {

                if ( "primary" == $value || "secondary" == $value || "tertiary" == $value) {
                    $label = WC()->session->get($cart_item_key . "_" . $key . "_label");
                    $value = WC()->session->get( $cart_item_key . "_" . $key );
                    wc_add_order_item_meta( $item_id, $label, $value);
                } else if( "Pickup Location" == $value ){
                    $location_id = WC()->session->get($cart_item_key . "_pickup_id");
                    $location_string = WC()->session->get($cart_item_key . "_" . $key);
                    wc_add_order_item_meta( $item_id, $value, $location_string);
                    wc_add_order_item_meta( $item_id, "_pickup_id", $location_id);
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
        if ( ! WC()->session->__isset($cart_item_key . "_cost") ) {
            $base_price = get_post_meta($product_id, '_wc_trip_base_price', true);
            WC()->session->set( $cart_item_key . "_cost", $base_price);
        }
        foreach( $this->fields as $key => $value ) {
            if( isset( $_REQUEST[$key]) ) {
                if ( "primary" == $value || "secondary" == $value || "tertiary" == $value) {
                    $packages = get_post_meta($product_id, '_' . $key ."s", true);
                    $cost = $this->get_package_cost( $_REQUEST[$key], $packages );
                    WC()->session->set( $cart_item_key . "_" . $key . "_label", $_REQUEST[$key . "_label"]);
                    $stored_cost = WC()->session->get( $cart_item_key . "_cost" );
                    $stored_cost += $cost;
                    WC()->session->set( $cart_item_key . "_" . $key, $_REQUEST[$key] );
                    WC()->session->set( $cart_item_key . "_cost", $stored_cost );
                } else if ( "wc_trip_pickup_location" == $key ) {
                    if ( WC()->session->__isset($cart_item_key . "_cost") ) {
                        $pickup_cost = $this->get_pickup_cost($_REQUEST[$key]);
                        $pickup_cost += WC()->session->get($cart_item_key . "_cost");
                        WC()->session->set($cart_item_key . "_cost", $pickup_cost);
                    } else {
                        WC()->session->set($cart_item_key . "_cost", $this->get_pickup_cost($_REQUEST[$key]));
                    }
                    $pickup_title = get_the_title($_REQUEST[$key]);
                    $pickup_time = get_post_meta($_REQUEST[$key], '_pickup_location_time', true);
                    $pickup_time = (strval($pickup_time) == "" ? "" : " - " .date("g:i a", strtotime($pickup_time)));
                    $pickup_string = $pickup_title . $pickup_time;

                    WC()->session->set( $cart_item_key . "_" . $key, $pickup_string );
                    WC()->session->set( $cart_item_key . "_pickup_id", $_REQUEST[$key] );
                } else {
                    if ( "" !== $_REQUEST[$key] ) {
                        WC()->session->set( $cart_item_key . "_" . $key, $_REQUEST[$key] );
                    }
                }
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
                } else {
                    $label = $value;
                }
                echo<<<CARTMETA
                <dt class="variation-{$label}">{$label}: </dt>
                <dd class="variation-{$label}">{$inputValue}</dd>
CARTMETA;
                unset($cost);
                unset($time);
            }
        }
        echo "</dl>";
    }
    public function add_to_cart( $cart_item_key ) {
        global $product;

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
                $pickup = get_post( absint($key) );
                $time = get_post_meta( $pickup->ID, '_pickup_location_time', true );
                $cost = get_post_meta( $pickup->ID, '_pickup_location_cost', true);
                if ( $time && "" != $time ) {
                    $time = " - " . date("g:i a", strtotime( $time ) );
                }
                if ( "none" !== $value ) {
                  $route = "data-route='" . $value . "'";
                } else {
                  $route = "";
                }
                if ( "" !== $cost && floatval($cost) > 0 ) {
                    $data = "data-cost='" . $cost . "'";
                    $cost_string = " + $" . $cost;
                } else if ("" !== $cost && floatval($cost) < 0 ) {
                    $data = $data = "data-cost='" . $cost . "'";
                    $cost_string = " " . substr_replace($cost, "$", 1, 0);
                } else {
                    $data = "";
                    $cost_string = "";
                }
                $pickup_output .= "<option value='" . $pickup->ID . "' {$data} {$route}>" . $pickup->post_title . $time . $cost_string . "</option>";
            }
            return $pickup_output;
        } else {
            return false;
        }
    }
}
new WC_Trips_Cart();
