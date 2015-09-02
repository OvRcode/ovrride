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
    
/*    public function is_in_stock() {
        if ( true === $this->managing_stock() ) {
            if ( 0 == $this->get_stock_quantity() ) {
                return false;
            } else {
                error_log("Package Stock:" .  $this->check_package_stock());
                if ( $this->check_package_stock() ) {
                    return $this->stock_status === 'instock';
                } else {
                    return false;
                }
            }
        } else {
            return $this->stock_status === 'instock';
        }
    }
    
    public function check_package_stock() {
        
        foreach( array("primary", "secondary", "tertiary") as $package_type ) {
            if ( 'yes' == $this->{'wc_trip_' . $package_type . '_package_stock'} ) {
                $packageStock = 0;
                foreach( $this->{'wc_trip_' . $package_type . '_packages'} as $index => $array ) {
                    if ( '' === $array['stock'] || intval($array['stock']) > 0 ) {
                        $packageStock++;
                    }
                }
                if ( 0 == $packageStock ) {
                    return false;
                }
            } else {
                return true;
            }
        }
            return true;
    }*/
    public function is_purchasable() {
        return true;
    }
    
    public function is_sold_individually() {
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
            
            if ( "" !== $values['cost'] && $values['cost'] != "0") {
                $dataCost = "data-cost='" . $values['cost'] . "'" ;
                $costLabel = " +" . $values['cost'];
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