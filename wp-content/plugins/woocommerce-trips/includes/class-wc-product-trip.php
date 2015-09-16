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
                return "space available";
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
                    $packages[$foundKey]['stock']--;
                    update_post_meta( $this->id, '_wc_trip_'.$type."_packages", $packages);
                }
            }
        }
    }
    
    public function check_package_stock( $type, $description ) {
        $packages = $this->{"wc_trip_" . $type . "_packages"};
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
        $all_stock = false;
        foreach( $package_types as $package ) {
            if ( "" == strval($this->{$package . "_stock"}) ) {
                $all_stock = true;
            } else if ( "yes" == strval($this->{$package . "_stock"}) ) {
                foreach( $this->{$package . "s"} as $key => $values ) {
                    if ( "" === strval($values['stock']) || intval($values['stock']) > 0 ) {
                        $all_stock = true;
                    } else {
                        $all_stock = false;
                        continue(2);
                    }
                }
            }
        }
        if ( $all_stock ) {
            return true;
        } else {
            return false;
        }
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