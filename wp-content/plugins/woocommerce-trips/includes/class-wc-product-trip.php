<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Trip extends WC_Product {

    public function __construct( $product ) {
        $this->product_type = 'trip';
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
		public function packages_stock(){
			$primary_stock 		= $this->get_meta( "_wc_trip_primary_package_stock", true, "view" );
			$secondary_stock 	= $this->get_meta( "_wc_trip_secondary_package_stock", true, "view" );
			$tertiary_stock 	= $this->get_meta( "_wc_trip_tertiary_package_stock", true, "view" );
			if ( "yes" == $primary_stock ) {
				$return['primary'] = $this->get_meta("_wc_trip_primary_packages", true, "view");
			}
			if ( "yes" == $secondary_stock ) {
					$return['secondary'] = $this->get_meta("_wc_trip_secondary_packages", true, "view");
			}
			if ( "yes" == $tertiary_stock ) {
					$return['tertiary'] = $his->get_meta("_wc_trip_tertiary_packages", true, "view");
			}
			return $return;
		}
		public function get_packages_stock( $type ) {
			return $this->get_meta( "_wc_trip_" . $type . "_package_stock" );
		}
		public function get_package_stock( $type, $description) {
			$packages = $this->get_meta( "_wc_trip_" . $type . "_packages" );

			if ( "yes" == $this->get_meta( "_wc_trip_" . $type . "_package_stock") ) {
				foreach( $packages as $key => $values ) {
					if ( $values['description'] == $description ) {
						return $values['stock'];
					}
				}
			}
		}
    public function reduce_package_stock( $type, $description ) {
				$packages = $this->get_meta("_wc_trip_" . $type . "_packages");

				if ( "yes" == $this->get_meta("_wc_trip_" . $type . "_package_stock") ) {
            $foundKey = "";
            foreach( $packages as $key => $values ) {
                if ( $description == $values['description'] ) {
                    $foundKey = $key;
                    break;
                }
            }

            if ( "" !== strval($foundKey) ) {
                if ( "" !== strval($packages[$foundKey]['stock']) ) {
									$packages[$foundKey]['stock']--;
									$this->update_meta_data( "_wc_trip_" . $type . "_packages", $packages);
									$this->save_meta_data();
									return true;
                }
            }
						return false;
        }
				return true;
    }

    public function check_package_stock( $type, $description ) {
				$packages = $this->get_meta("_wc_trip_".$type."_packages", true, 'view' );
				if ( "oneWay" == $description) {
					return true;
				}

        if ( "" == $this->get_meta("_wc_trip_".$type."_package_stock", true,'view') ) {
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
        $package_types = array("_wc_trip_primary_package", "_wc_trip_secondary_package", "_wc_trip_tertiary_package");
        foreach( $package_types as $package ) {
					$stock = $this->get_meta($package . "_stock", true, 'view' );
            if ( "yes" == strval($stock) ) {
								$outOfStockCount = 0;
								$packages = $this->get_meta($package."s", true, 'view' );
                foreach( $packages as $key => $values ) {
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
			$toBeach= "<option value=''>Select To Beach pickup location</option>";
			$fromBeach="<option value=''>Select From Beach pickup location</option>";
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
			<div class="clearfix toFromBeach">
				<div class="toBeach">
					<label for="wc_trip_to_beach"><strong>To Beach</strong> <span class="required">*</span></label>
					<select name="wc_trip_to_beach" id="wc_trip_to_beach" data-required="true">
					{$toBeach}
					</select>
				</div>
				<div class="fromBeach">
					<label for="wc_trip_from_beach"><strong>From Beach</strong> <span class="required">*</span></label>
					<select name="wc_trip_from_beach" id="wc_trip_from_beach" data-required="true">
					{$fromBeach}
					</select>
				</div>
			</div>
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
					if ( $array["stock"] > 0 ) {
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
        $packages 	= get_post_meta( $this->id, "_wc_trip_{$type}_packages", true );
        $stock 			= get_post_meta( $this->id, "_wc_trip_{$type}_package_stock", true );
        $stock 			= ( $stock == "yes" ? true : false);
        $label 			= get_post_meta( $this->id, "_wc_trip_{$type}_package_label", true );
				$optional 	= get_post_meta( $this->id, "_wc_trip_{$type}_package_optional", true);
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
						if ( ! isset( $values['default'])) {
							$values['default'] = "";
							$default = "";
						} else if  ( "yes" == $values['default'] ) {
							$default = "selected";
						} else {
							$default = "";
						}
            //$htmlOutput .= '<option value="' . $values['description'] . '" ' . $dataCost . ' ' . $disabled . '>' .$outOfStock . $values['description'] . $costLabel . '</option>';
						$htmlOutput .= "<option value='{$values['description']}' {$dataCost} {$disabled} {$default}> {$outOfStock} {$values['description']} {$costLabel} </option>";


        }
        if ( count($packages) > 0 ) {
					if ( "checked" == $optional ) {
						$optional = "yes";
					} else {
						$optional = "no";
					}
            return array("label" => $label, "html" => $htmlOutput, "optional" => $optional);
        } else {
            return false;
        }
    }

}
