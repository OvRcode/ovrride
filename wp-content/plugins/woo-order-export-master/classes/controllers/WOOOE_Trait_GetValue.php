<?php
if(!defined('ABSPATH')){
    exit;
}

/*
 * Define trait to get properties.
 * Since this would be repetitive in case of class fields.
 */
if( !trait_exists('WOOOE_Trait_GetValue', false) ){

    trait WOOOE_Trait_GetValue{

        //Creates instance and stores in property.
        static function instance($order_id){
            if( empty(self::$instance[$order_id]) ){
                self::$instance[$order_id] = new self($order_id);
            }
            
            return self::$instance[$order_id];
        }
        
        /*
         * Sets values for properties.
         */
        function set_value(){

            foreach($this->properties as $property){

                if(method_exists($this, $property)){
                    $this->$property = $this->$property();
                }elseif( in_array($property, $this->properties) ){
                    $this->$property = apply_filters('woooe_call_fallback', '', $property, $this);
                }
            }
        }

        /*
         * Gets property value of an object
         */
        function get_value($property){

            $value = null;

            if(property_exists($this, $property)){
                $value = $this->$property;
            }else{
                $value = get_post_meta($this->order_id, $property, true);
            }

            return $value;
        }
    }
}
