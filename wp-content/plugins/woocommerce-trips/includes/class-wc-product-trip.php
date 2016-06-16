<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Trip extends WC_Product {

    public function __construct( $product ) {
        $this->product_type = 'trip';
        $this->manage_stock = 'yes';
        parent::__construct( $product );
    }

    public function get_availability() {
        if ( $this->is_purchasable() ) {
            if ( $this->get_stock_quantity() < 10 ) {
                return "only " . $this->get_stock_quantity() . " left";
            } else {
                return "";
            }
        } else {
            return "sold out";
        }
    }

    public function reduce_package_stock( $type, $description ) {
        $packages = $this->{"wc_trip_" . $type . "_packages"};

        if ( "yes" == $this->{"wc_trip_" . $type . "_package_stock"} ) {
            $foundKey = "";
            foreach( $packages as $key => $values ) {
                if ( $description == $values['description'] ) {
                    $foundKey = $key;
                    break;
                }
            }

            if ( "" !== strval($foundKey) ) {
                if ( "" !== strval($packages[$foundKey]['stock']) ) {
										$this->{"wc_trip_" . $type . "_packages"}[$foundKey]['stock']--;
                    if ( update_post_meta( $this->id, '_wc_trip_'.$type."_packages", $this->{"wc_trip_" . $type . "_packages"})) {
											return true;
										}
                }
            }
						return false;
        }
				return true;
    }

    public function check_package_stock( $type, $description ) {
        $packages = $this->{"wc_trip_" . $type . "_packages"};
				if ( "oneWay" == $description) {
					return true;
				}
        if ( "" == $this->{"wc_trip_" . $type . "_package_stock"} ) {
            return true;
        } else {
            foreach( $packages as $key => $values ) {
                if ( $values['description'] == $description ) {
                    if ( "" === strval($values['stock']) || intval($values['stock']) > 0 ) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
            return false;
        }
    }

    public function check_all_package_stock() {
        $package_types = array("wc_trip_primary_package", "wc_trip_secondary_package", "wc_trip_tertiary_package");
        foreach( $package_types as $package ) {
            if ( "yes" == strval($this->{$package . "_stock"}) ) {
								$outOfStockCount = 0;
                foreach( $this->{$package . "s"} as $key => $values ) {
										if ( "" === strval($values['stock']) || intval($values['stock']) > 0 ) {
                        $outOfStockCount++;
                    }
                }
								if ($outOfStockCount == 0 ) {
									return false;
								}
            }
        }

				return true;
    }

    public function is_purchasable() {
        if ( $this->is_in_stock() && $this->check_all_package_stock()) {
            return true;
        } else {
            return false;
        }
    }

    public function is_sold_individually() {
        return true;
    }
		public function beach_bus_pickups() {
			// Check through pickups
			// Split into to/from beach with
			$toBeach= "<option>Select To Beach pickup location</option>";
			$fromBeach="<option>Select From Beach pickup location</option>";
			$pickups = get_post_meta( $this->id, "_wc_trip_pickups", true);
			$tempTo = array();
			$tempFrom = array();
			foreach( $pickups as $id => $route ) {
				// Match to or From Beach
				if ( preg_match("/^To|From|none/", $route, $match) ) {
					$enabled = ( $this->pickup_stock_check($route) ? "":" disabled");
					$title = get_the_title($id);
					$rawTime = get_post_meta( $id, '_pickup_location_time', true);
					$time = date("g:i a", strtotime($rawTime));
					$temp = "<option value='{$id}:{$route}'{$enabled}>{$title} at {$time}</option>";

					if ( $match[0] == "To" ) {
						$tempTo[$rawTime] = $temp;
					} elseif( $match[0] == "From" ) {
						$tempFrom[$rawTime] = $temp;
					}
				}

			}
			ksort($tempTo);
			ksort($tempFrom);
			foreach( $tempTo as $time => $option ) {
				$toBeach .= $option;
			}
			foreach( $tempFrom as $time => $option ) {
				$fromBeach .= $option;
			}
			return <<<OUTPUT
			<p class="form-field">
				<label for="wc_trip_to_beach">To Beach <span class="required">*</span></label>
				<select name="wc_trip_to_beach" id="wc_trip_to_beach" data-required="true">
				{$toBeach}
				</select>
			</p>
			<strong>Earlier pickups 3:50-4:40pm stop in Brooklyn only.</strong>
			<p class="form-field">
				<label for="wc_trip_from_beach">From Beach <span class="required">*</span></label>
				<select name="wc_trip_from_beach" id="wc_trip_from_beach" data-required="true">
				{$fromBeach}
				</select>
			</p>
OUTPUT;
		}
		public function get_pickup_route( $pickup_id ) {
			$pickups = get_post_meta( $this->id, "_wc_trip_pickups", true);
			if ( isset($pickups[$pickup_id]) ) {
				return $pickups[$pickup_id];
			} else {
				return false;
			}
		}
		public function pickup_stock_check( $type ) {//WORK THIS INTO TRIP.PHP VIEW
			$secondary_package = get_post_meta( $this->id, "_wc_trip_secondary_packages", true);
			foreach( $secondary_package as $key => $array ) {
				if ( $type === $array["description"] ) {
					if ( intval($array["stock"]) > 0 ) {
						return true;
					} else {
						return false;
					}
				}
			}
			// Either array is not set or type doesn't match a stock type
			return false;
		}
    public function output_packages( $type ) {
        $packages = get_post_meta( $this->id, "_wc_trip_" . $type . "_packages", true );
        $stock = get_post_meta( $this->id, "_wc_trip_" . $type . "_package_stock", true );
        $stock = ( $stock == "yes" ? true : false);
        $label = get_post_meta( $this->id, "_wc_trip_" . $type . "_package_label", true );
        if ( "" === strval($label) ) {
            $label = $type;
        }
        $htmlOutput = "";
        foreach( $packages as $key => $values ) {
            $disabled = $outOfStock = $costLabel = $dataCost = "";

            if ( $stock && "" !== strval($values['stock']) && 0 === intval($values['stock'])) {
                $disabled = "disabled";
                $outOfStock = "(Out of Stock) ";
            }

            if ( "" !== strval($values['cost']) && floatval($values['cost']) > 0 ) {
                $dataCost = "data-cost='" . floatval($values['cost']) . "'" ;
                $costLabel = " $" . floatval($values['cost']);
            } else if ( "" !== floatval($values['cost']) && floatval($values['cost']) < 0 ) {
                $dataCost = "data-cost='" . floatval($values['cost']) . "'";
                $costLabel = " " . substr_replace(floatval($values['cost']), "$", 1, 0);
            }

            $htmlOutput .= '<option value="' . $values['description'] . '" ' . $dataCost . ' ' . $disabled . '>' .$outOfStock . $values['description'] . $costLabel . '</option>';


        }
        if ( count($packages) > 0 ) {
            return array("label" => $label, "html" => $htmlOutput);
        } else {
            return false;
        }
    }

}
