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
    public function get_price_html() {
        if ( ! $this->is_purchasable() ) {
            echo "<p class='stock out-of-stock'>Out of stock</p>";
        } else if ( $this->stock <= 10 ) {
            echo "<p class='stock low-in-stock'>Low stock,&nbsp;" . $this->stock . " left</p>";
        } else {
            echo "<p class='stock in-stock'>In Stock</p>";
        }
    }
    public function is_purchasable() {
        // insert purchaseable checks here
        if ( "outofstock" == $this->stock_status) {
            return false;
        } else {
            return true;
        }
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
            
            $htmlOutput .= '<option value="' . $values['description'] . '" ' . $data_cost . ' ' . $disabled . '>' .$outOfStock . $values['description'] . $costLabel . '</option>';

            
        }
        if ( count($packages) > 0 ) {
            return array("label" => $label, "html" => $htmlOutput);
        } else {
            return false;
        }
    }

}