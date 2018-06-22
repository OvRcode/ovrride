<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WC_Trips_Cart {
    public $fields = array( "wc_trip_first" => "First", "wc_trip_middle" => "Middle",
      "wc_trip_last" => "Last", "wc_trip_gender"  => "Gender", "wc_trip_email" => "Email",
      "wc_trip_phone" => "Phone", "wc_trip_passport_num" => "Passport Number",
      "wc_trip_passport_country" => "Passport Country", "wc_trip_dob_field" => "Date of Birth",
      "wc_trip_age_check" => "Is this guest at least 18 years of age?", "wc_trip_primary_package" => "primary",
      "wc_trip_secondary_package" => "secondary", "wc_trip_tertiary_package" => "tertiary",
      "wc_trip_pickup_location" => "Pickup Location", "wc_trip_to_beach" => "To Beach",
      "wc_trip_from_beach" => "From Beach");

    public $package_types = array("primary", "secondary", "tertiary");
    public $orders_processed = array();

    public function __construct() {

       add_action( "woocommerce_trip_add_to_cart", array( $this, "add_to_cart" ), 30 );
       add_action( "woocommerce_add_to_cart", array( $this, "save_trip_fields" ), 1, 5 );
       add_filter( "woocommerce_cart_item_name", array( $this, "render_meta_on_cart_item"), 1, 3 );
       add_filter( "woocommerce_add_cart_item_data",array($this, "force_individual_cart_items" ), 10, 2 );
       add_action( "woocommerce_add_order_item_meta", array( $this, "order_item_meta" ), 10, 3 );
       add_action( "woocommerce_before_calculate_totals", array( $this, "add_costs" ), 1, 1 );
       add_filter( "woocommerce_add_to_cart_validation", array( $this , "validate_add_cart_item" ), 10, 3 );
       add_action( "woocommerce_check_cart_items", array( $this, "check_cart_items" ), 1 );
       add_action( "woocommerce_reduce_order_stock", array( $this, "trigger_package_stock" ), 1, 4);
    }
    public function check_cart_items(){
      global $woocommerce;
      // Result
			$return = true;

			// Check cart item validity
			$result = $woocommerce->cart->check_cart_item_validity();

			if ( is_wp_error( $result ) ) {
				wc_add_notice( $result->get_error_message(), "error" );
				$return = false;
			}

			// Check item stock
			$result = $this->check_cart_item_stock();

			if ( is_wp_error( $result ) ) {
				wc_add_notice( $result->get_error_message(), "error" );
				$return = false;
			}

			return $return;

    }
    public function check_cart_item_stock() {
      $error = new WP_Error();
      $stock_total = array();
      foreach( WC()->cart->get_cart() as $cart_item_key => $values ) {
        $_product = $values['data'];

        if ( ! $_product->is_in_stock() ) {
          if ( $_product->product_type !== "trip" ){
            $error->add( "Sorry, we don't have enough stock available for {$_product->get_title()}" );
          } else {
            $error->add( "Sorry, we don't have enough seats available for {$_product->get_title()}" );
          }
          return $error;
        }
        if ( "trip" === $_product->product_type && "beach_bus" === $_product->get_meta( "_wc_trip_type", true, "view" ) ) {
          $stock_total[$_product->get_id()]['count'] += 1;
            if ( WC()->session->__isset( "{$cart_item_key}_to_beach_route" ) ) {
              $toRoute = WC()->session->get( "{$cart_item_key}_to_beach_route" );
              $stock_total[$_product->get_id()]['secondary'][$toRoute]['count'] += 1;
              $stock_total[$_product->get_id()]['secondary'][$toRoute]['names'][] = WC()->session->get( "{$cart_item_key}_wc_trip_to_beach" );
            }
            if ( WC()->session->__isset($cart_item_key."_from_beach_route") ) {
              $fromRoute = WC()->session->get($cart_item_key."_from_beach_route");
              $stock_total[$_product->get_id()]['secondary'][$fromRoute]['count'] += 1;
              $stock_total[$_product->get_id()]['secondary'][$fromRoute]['names'][] = WC()->session->get( "{$cart_item_key}_wc_trip_from_beach" );
            }

        }
      }
      if ( "trip" === $_product->product_type && "beach_bus" === $_product->get_meta( "_wc_trip_type", true, "view" ) ) {
        foreach( $stock_total as $product_id => $data) {
          $product = wc_get_product( $product_id );

          if ( $product->get_manage_stock() && $product->get_stock_quantity() < $data['count'] ) {
            $error->add( "error", "Sorry, we don't have enough seats available for {$product->get_title()} only {$product->get_stock_quantity()} left");
            return $error;
          }
          $package_stock = $product->packages_stock();
          foreach( $data['secondary'] as $route => $routeData) {
            $index = array_search($route, array_column($package_stock['secondary'],'description'));
            if ( $routeData['count'] > $package_stock['secondary'][$index]['stock']) {
              $error_string = "Sorry, we don't have enough seats on ";
              foreach( $routeData['names'] as $name) {
                $error_string .= "{$name},";
              }
              if ( $package_stock['secondary'][$index]['stock'] > 0 ){
                $error_string .= " only {$package_stock['secondary'][$index]['stock']} available";
              } else {
                $error_string .= " none available";
              }
              $error_string .= ": {$product->get_title()}";
              $error->add( "package-out-of-stock", $error_string );
              return $error;
            }


          }
        }
      }
    }
    public function trigger_package_stock( $instance ) {
        if ( isset( WC()->cart ) ){
          $cart = WC()->cart->get_cart();
        }

        foreach( $cart as $cart_id => $cart_data ) {
          if ( ! in_array( $cart_id, $this->orders_processed ) ){
            $this->orders_processed[] = $cart_id;
            $product = wc_get_product( $cart_data['product_id'] );
            foreach( $this->package_types as $package ) {
                if ( WC()->session->__isset( "{$cart_id}_wc_trip_{$package}_package" ) ) {
                    $product->reduce_package_stock( $package, WC()->session->get( "{$cart_id}_wc_trip_{$package}_package" ) );
                }
            }

            if ( WC()->session->__isset( "{$cart_id}_to_beach_route" ) ) {
              $product->reduce_package_stock( "secondary", WC()->session->get( "{$cart_id}_to_beach_route" ) );
            }
            if ( WC()->session->__isset( "{$cart_id}_from_beach_route" ) ) {
              $product->reduce_package_stock( "secondary", WC()->session->get( "{$cart_id}_from_beach_route" ) );
            }
          }
        }

    }
    public function validate_add_cart_item( $passed, $product_id, $qty ) {
        $cart             = WC()->cart->get_cart();
        $product          = get_product( $product_id );
        $stock_management = $product->get_manage_stock();

        if ( $product->product_type == "trip" ){
          if ( ( $stock_management && "instock" == $product->get_stock_status() && $product->get_stock_quantity() >= 1 ) || ! $stock_management ) {
            // Product is in stock and potentially has enough stock to add item
            $packageStock = $product->packages_stock();
            if ( "beach_bus" === $product->wc_trip_type ) {
              if ( isset( $_REQUEST['wc_trip_primary_package'] ) ) {
                $package = $_REQUEST['wc_trip_primary_package'];
                // Primary package for beach bus has no stock, nothing to check here
              } else {
                // Package is missing for some reason, stop and fix that!
                wc_add_notice( "Please select a package" , "error" );
                return FALSE;
              }
              if ( isset( $_REQUEST['wc_trip_to_beach'] ) ) {
                $toBeach            = explode( ":" , $_REQUEST['wc_trip_to_beach'] );
                $toBeachDescription = get_the_title( $toBeach[0] );
                $toBeachTime        = get_post_meta( $toBeach[0] , "_pickup_location_time" , true );
                $toBeachTime        = date( "g:i a", strtotime( $toBeachTime ) );
                $toBeach            = $toBeach[1];
                $toBeachKey         = array_search( $toBeach, array_column( $packageStock['secondary'] , 'description' ) );
                $toBeachStock       = $packageStock['secondary'][$toBeachKey]['stock'];
                if ( $toBeachStock < 1 ) {
                  // Pickup has gone out of stock after page was loaded
                  wc_add_notice( "No seats left for {$toBeachDescription} at {$toBeachTime}", "error" );
                  return FALSE;
                }
              }
              elseif ( "One Way ( From Beach )" !== $package ) {
                wc_add_notice( "Missing To Beach option.", "error" );
                return FALSE;
              }
              if ( isset( $_REQUEST['wc_trip_from_beach'] ) ) {
                $fromBeach            = explode( ":" , $_REQUEST['wc_trip_from_beach'] );
                $fromBeachDescription = get_the_title( $fromBeach[0] );
                $fromBeachTime        = get_post_meta( $fromBeach[0] , "_pickup_location_time" , true );
                $fromBeachTime        = date( "g:i a", strtotime( $fromBeachTime ) );
                $fromBeach            = $fromBeach[1];
                $fromBeachKey         = array_search( $fromBeach , array_column( $packageStock['secondary'] , "description" ) );
                $fromBeachStock       = $packageStock['secondary'][$fromBeachKey]['stock'];
                if ( $fromBeachStock < 1 ) {
                  // Pickup has gone out of stock after page was loaded
                  wc_add_notice( "No seats left for {$fromBeachDescription} at {$fromBeachTime}", "error");
                  return FALSE;
                }
              }
              elseif ( "One Way ( To Beach )" !== $package ) {
                wc_add_notice( "Missing From Beach option." );
                return FALSE;
              }
              // Find out how many of to/from beach are in cart
              if ( empty( $cart ) ) {
                return TRUE;
              } else {
                $cart_master_stock  = 0;
                $cart_to_stock      = 0;
                $cart_from_stock    = 0;

                foreach( $cart as $cart_id => $entry_data ) {
                  if ( $product_id == $entry_data['product_id'] ) {
                    if ( $stock_management && WC()->session->__isset( "{$cart_id}_wc_trip_primary_package" ) ) {
                      if ( WC()->session->get( "{$cart_id}_wc_trip_primary_package") == "Round Trip" ) {
                        $cart_master_stock += 2;
                      }
                    } else if ( $stock_management ) {
                        $cart_master_stock++;
                    }

                    if ( WC()->session->__isset( "{$cart_id}_to_beach_route") && isset( $toBeach ) ){
                      $temp_to = WC()->session->get( "{$cart_id}_to_beach_route" );
                      if ( $temp_to == $toBeach ) {
                        $cart_to_stock++;
                      }
                    }
                    if ( WC()->session->__isset( "{$cart_id}_from_beach_route" ) && isset( $fromBeach ) ) {
                      $temp_from = WC()->session->get( "{$cart_id}_from_beach_route" );
                      if ( $temp_from == $fromBeach ) {
                        $cart_from_stock++;
                      }
                    }
                  }
                }
                // Make sure cart stock + form doesn't go over inventory
                if ( "Round Trip" === $package && ( $cart_master_stock + 2 ) > $product->stock ) {
                  wc_add_notice( "Sorry, this trip is now booked to capacity", "error" );
                  return FALSE;
                } elseif ( ( $cart_master_stock + 1 ) >  $product->stock ) {
                  wc_add_notice( "Sorry, this trip is now booked to capacity", "error" );
                  return FALSE;
                }

                if ( isset( $toBeachStock ) && ( $cart_to_stock + 1 ) > $toBeachStock ) {
                  error_log( "to beach cart check failure :-(" );
                  wc_add_notice( "Sorry, not enough seats left on {$toBeachDescription}  at {$toBeachTime}", "error" );
                  return FALSE;
                }
                if ( isset( $fromBeachStock ) && ( $cart_from_stock + 1 ) > $fromBeachStock ) {
                  error_log( "from beach cart check failure :-(" );
                  wc_add_notice( "Sorry, not enough seats left on {$fromBeachDescription}  at {$fromBeachTime}" , "error" );
                  return FALSE;
                }
                // If we've made it this far there are no stock conflicts
                return TRUE;
              }
            }
            else{
              return TRUE;
            }
          } else {
            // Product has gone out of stock since page was loaded.
            wc_add_notice( "Can't add item to cart, item is out of stock", "error" );
            return FALSE;
          }

        } else {
          return TRUE;
        }

    }

    public function add_costs( $cart_obj ) {
      if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
      }

      foreach ( $cart_obj->get_cart() as $key => $value ) {
        if ( "trip" == $value['data']->product_type ) {
          if( WC()->session->__isset( "{$key}_cost" ) ) {
            $additional_costs = WC()->session->get( "{$key}_cost" );
            $value['data']->set_price( $additional_costs );
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
        return get_post_meta( $id, "_pickup_location_cost", true);
    }
    public function order_item_meta( $item_id, $values, $cart_item_key ) {
        global $woocommerce;
        foreach ( $this->fields as $key => $value ) {
            if ( WC()->session->__isset( "{$cart_item_key}_{$key}" ) ) {

                if ( "primary" == $value || "secondary" == $value || "tertiary" == $value ) {
                    $label = WC()->session->get( "{$cart_item_key}_{$key}_label" );
                    $value = WC()->session->get( "{$cart_item_key}_{$key}" );
                    wc_add_order_item_meta( $item_id, $label, $value );
                } else if( "Pickup Location" == $value ){
                    $location_id     = WC()->session->get( "{$cart_item_key}_pickup_id");
                    $location_string = WC()->session->get( "{$cart_item_key}_{$key}" );
                    wc_add_order_item_meta( $item_id, $value, $location_string );
                    wc_add_order_item_meta( $item_id, "_pickup_id", $location_id );
                } else if( "To Beach" == $value) {
                  $toBeachId = WC()->session->get( "{$cart_item_key}_to_beach_id" );
                  $toBeachRoute = WC()->session->get( "{$cart_item_key}_to_beach_route" );
                  $toBeachString = WC()->session->get( "{$cart_item_key}_{$key}" );
                  wc_add_order_item_meta( $item_id, $value, $toBeachString );
                  wc_add_order_item_meta( $item_id, "_to_beach_id", $toBeachId );
                  wc_add_order_item_meta( $item_id, "_to_beach_route", $toBeachRoute );
                } else if( "From Beach" == $value) {
                  $fromBeachId = WC()->session->get( "{$cart_item_key}_from_beach_id" );
                  $fromBeachRoute = WC()->session->get( "{$cart_item_key}_from_beach_route" );
                  $fromBeachString = WC()->session->get( "{$cart_item_key}_{$key}" );
                  wc_add_order_item_meta( $item_id, $value, $fromBeachString );
                  wc_add_order_item_meta( $item_id, "_from_beach_id", $fromBeachId );
                  wc_add_order_item_meta( $item_id, "_from_beach_route", $fromBeachRoute );
                } else {
                    wc_add_order_item_meta( $item_id, $value, WC()->session->get( "{$cart_item_key}_{$key}" ));
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
        if ( ! WC()->session->__isset( "{$cart_item_key}_cost" ) ) {
            $base_price = get_post_meta($product_id, "_wc_trip_base_price", true);
            WC()->session->set( "{$cart_item_key}_cost", $base_price);
        }
        foreach( $this->fields as $key => $value ) {
            if( isset( $_REQUEST[$key]) ) {
                if ( "primary" == $value || "secondary" == $value || "tertiary" == $value) {
                    $packages = get_post_meta($product_id, "_{$key}s", true);
                    $cost = $this->get_package_cost( $_REQUEST[$key], $packages );
                    WC()->session->set( "{$cart_item_key}_{$key}_label", $_REQUEST["{$key}_label"] );
                    $stored_cost = floatval( WC()->session->get( "{$cart_item_key}_cost" ) );
                    $stored_cost += floatval( $cost );
                    WC()->session->set( "{$cart_item_key}_{$key}", $_REQUEST[$key] );
                    WC()->session->set( "{$cart_item_key}_cost", $stored_cost );
                } else if ( "wc_trip_pickup_location" == $key ) {
                    if ( WC()->session->__isset( "{$cart_item_key}_cost" ) ) {
                        $pickup_cost = $this->get_pickup_cost($_REQUEST[$key]);
                        $pickup_cost += WC()->session->get( "{$cart_item_key}_cost" );
                        WC()->session->set( "{$cart_item_key}_cost", $pickup_cost );
                    } else {
                        WC()->session->set( "{$cart_item_key}_cost", $this->get_pickup_cost( $_REQUEST[$key] ) );
                    }
                    $pickup_title   = get_the_title( $_REQUEST[$key] );
                    $pickup_time    = get_post_meta( $_REQUEST[$key], '_pickup_location_time', true );
                    $pickup_time    = (strval($pickup_time) == "" ? "" : " - " .date( "g:i a", strtotime( $pickup_time ) ) );
                    $pickup_string  = $pickup_title . $pickup_time;

                    WC()->session->set( $cart_item_key . "_{$key}", $pickup_string );
                    WC()->session->set( $cart_item_key . "_pickup_id", $_REQUEST[$key] );
                } elseif ( "wc_trip_to_beach" == $key ) {
                  $request  = $_REQUEST[$key];
                  $request  = explode( ":", $request );
                  $title    = get_the_title( $request[0] );
                  $time     = date("g:i a", strtotime( get_post_meta( $request[0], "_pickup_location_time", true ) ) );
                  WC()->session->set( "{$cart_item_key}_{$key}", "{$title} - {$time}");
                  WC()->session->set( "{$cart_item_key}_to_beach_id", $request[0] );
                  WC()->session->set( "{$cart_item_key}_to_beach_route", $request[1] );
                } elseif ( "wc_trip_from_beach" == $key ) {
                  $request = $_REQUEST[$key];
                  $request = explode( ":", $request );
                  $title = get_the_title( $request[0] );
                  $time = date( "g:i a", strtotime(get_post_meta( $request[0], "_pickup_location_time", true ) ) );
                  WC()->session->set( "{$cart_item_key}_{$key}", "{$title}  - {$time}" );
                  WC()->session->set( "{$cart_item_key}_from_beach_id", $request[0] );
                  WC()->session->set( "{$cart_item_key}_from_beach_route", $request[1] );
                } else {
                    if ( "" !== $_REQUEST[$key] ) {
                        WC()->session->set( "{$cart_item_key}_{$key}", $_REQUEST[$key] );
                    }
                }
            }
        }
    }
    public function render_meta_on_cart_item( $title = null, $cart_item = null, $cart_item_key = null ) {
        echo $title;
        echo "<dl class='variation'>";
        foreach( $this->fields as $key => $value ) {
            if ( $cart_item_key && WC()->session->__isset( "{$cart_item_key}_{$key}" ) ){
                $inputValue = WC()->session->get( "{$cart_item_key}_{$key}" );
                $key_parts = explode( "_", $key );
                if ( isset( $key_parts[3] ) && "package" == $key_parts[3] ) {
                    $label = WC()->session->get( "{$cart_item_key}_{$key}_label" );
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
        $stock_count = $product->get_stock_quantity();
        if ( !$product->is_in_stock() ) {
          $stock_text = "Sold Out";
        } else if ( $stock_count > 30 ) {
          $stock_text = "Seats Available";
        } else if ( $stock_count <= 30 && $stock_count > 0 ) {
          $stock_text = "Only {$stock_count} Left";
        } else {
          $stock_text = "";
        }
        $destination_name               = get_post_meta( $product->id, "_wc_trip_destination", true );
        $destination                    = get_page_by_title( $destination_name, "ARRAY_A", "destinations" );
        $destination_lesson_restriction = get_post_meta( $destination['ID'], "_lesson_age", true );
        $age_check                      = $product->get_meta( "_wc_trip_age_check", true, "view" );
        if ( "" == $age_check ) {
          $age_check = 18;
        }
        if ( substr( $age_check, -1) === "+" ) {
          $age_label = substr( $age_check, 0 , -1 );
        } else {
          $age_label = $age_check;
        }
        $template_data = [
          "trip_type" => get_post_meta( $product->id, "_wc_trip_type", true ),
          "pickups"   => $this->pickupField( $product->id ),
          "packages"  => [
            "primary"   => $product->output_packages( "primary" ),
        		"secondary" => $product->output_packages( "secondary" ),
        		"tertiary"  => $product->output_packages( "tertiary" )
          ],
          "base_price"  => floatval( get_post_meta( $product->id, "_wc_trip_base_price", true ) ),
          "stock"       => $stock_text,
          "lesson_age"  => $destination_lesson_restriction,
          "age_limit"   => $age_check,
          "age_label"   => $age_label
        ];

        $template = "";
        switch( $template_data['trip_type'] ) {
          case "bus":
            $template = "bus.php";
          break;
          case "international_flight":
          case "domestic_flight":
            $template = "flight.php";
          break;
          case "beach_bus":
            $template = "beach_bus.php";
            unset($template_data["packages"]["secondary"]);
          break;
          default:
            $template = "bus.php";
        }

        wc_get_template( "single-product/add-to-cart/{$template}" , $template_data, 'woocommerce-trips', WC_TRIPS_TEMPLATE_PATH );
    }
    private function pickupField( $post_id ) {
        $pickup_ids = get_post_meta( $post_id, "_wc_trip_pickups", true);
        if ( "array" == gettype( $pickup_ids ) && count( $pickup_ids ) > 0) {
            foreach( $pickup_ids as $key => $value ) {
                $pickup = get_post( absint( $key ) );
                $time = get_post_meta( $pickup->ID, "_pickup_location_time", true );
                $cost = get_post_meta( $pickup->ID, "_pickup_location_cost", true);
                if ( $time && "" != $time ) {
                    $time = " - " . date( "g:i a", strtotime( $time ) );
                }
                if ( "none" !== $value ) {
                  $route = "data-route='{$value}'";
                } else {
                  $route = "";
                }
                if ( "" !== $cost && floatval( $cost ) > 0 ) {
                    $data = "data-cost='{$cost}'";
                    $cost_string = " + ${$cost}";
                } else if ( "" !== $cost && floatval( $cost ) < 0 ) {
                    $data = $data = "data-cost='{$cost}'";
                    $cost_string = " " . substr_replace( $cost, "$", 1, 0 );
                } else {
                    $data = "";
                    $cost_string = "";
                }
                if ( !isset( $pickup_output[$value] ) ) {
                  $pickup_output[$value] = "<option value=''>Select Pickup Location</option>";
                }
                $pickup_output[$value] .= "<option value='{$pickup->ID}' {$data} {$route}>{$pickup->post_title} {$time} {$cost_string}</option>";
            }
            return $pickup_output;
        } else {
            return false;
        }
    }
}
new WC_Trips_Cart();
