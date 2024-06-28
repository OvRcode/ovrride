<?php
if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WOOOE_Fetch_Meta', false)){
    
    class WOOOE_Fetch_Meta extends WOOOE_Fetch_Order {
        
        use WOOOE_Trait_GetValue;
        
        /*
         * Holds instance of class
         */
        static $instance = array();
        
        function __construct($order_id) {

            parent::__construct($order_id);
            $this->properties = apply_filters('woooe_meta_properties', array() );
            $this->set_value();
        }


    }
}