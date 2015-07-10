<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Product_Trip extends WC_Product {
    public function __construct( $product ) {
        $this->product_type = 'trip';
        parent::__construct( $product );
    }
    
}