<?php
if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WOOOE_Fetch_Customer', false)){

    class WOOOE_Fetch_Customer extends WOOOE_Fetch_Order{

        use WOOOE_Trait_GetValue;

        /*
         * Customer object
         */
        public $customer;

        /*
         * Holds instance of class
         */
        static $instance = array();

        /*
         * Properties array.
         */
        public $properties;

        function __construct($order_id) {

            parent::__construct($order_id);
            $this->customer = new WC_Customer($this->order->get_user_id());
            $this->properties = apply_filters('woooe_customer_properties', array('customer_name','customer_email') );
            $this->set_value();
        }

        /*
         * Get customer name
         */
        function customer_name(){

            $name = '';

            $fname = $this->customer->get_first_name();
            $lname = $this->customer->get_last_name();

            $name = trim($fname.' '.$lname);

            return $name;
        }
        
        /*
         * Get customer email
         */
        function customer_email(){
            return $this->order->get_billing_email();
        }

    }
}