<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Trip extends WC_Product {
    public function __construct( $product ) {
        $this->product_type = 'trip';
        parent::__construct( $product );
    }
    public function is_purchasable() {
        // insert purchaseable checks here
        return true;
    }
    public function output_packages( $type ) {
        $packages = get_post_meta( $this->id, "_wc_trip_" . $type . "_packages", true );
        error_log($type . ":" . count($packages));
        $stock = get_post_meta( $this->id, "_wc_trip_" . $type . "_package_stock", true );
        $stock = ( $stock == "yes" ? true : false);
        $label = get_post_meta( $this->id, "_wc_trip_" . $type . "_package_label", true );
        if ( count($packages) > 0 ) {
            return array("label" => $label, "stock" => $stock, "packages" => $packages);
        } else {
            return false;
        }
    }
    public function primary_packages() {
        //get_post_meta ( int $post_id, string $key = '', bool $single = false )
    }
}