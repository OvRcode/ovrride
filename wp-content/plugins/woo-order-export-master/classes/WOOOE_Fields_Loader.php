<?php
//Prevent direct access.
if( !defined('ABSPATH') ){
    exit;
}

if(!class_exists('WOOOE_Fields_Loader', false)){

    class WOOOE_Fields_Loader {

        /*
         * Fields to export
         */
        public $fields;

        /*
         * values of corresponding fields.
         */
        public $data = array();
        
        /*
         * Static tracker
         */
        static $track;

        /*
         * Constructor. Accepts fields to export.
         */
        function __construct($fields) {

            if(!is_array($fields)){
                $fields = array();
            }

            $this->fields = $fields;
            $this->load_classes();
            $this->write_data();
        }

        /*
         * Loads data into classes and instantiate them
         */
        function load_classes(){

            $current_query = WOOOE_Data_Handler::get_current_query();

            if( $current_query->have_posts() ){

                while( $current_query->have_posts() ){

                    $current_query->the_post();
                    
                    $p = WOOOE_Fetch_Product::instance(get_the_ID());
                    
                    /*
                     * If your fields are using classes.
                     * Load the classes by instantiating them,
                     * classes must use singleton pattern and must use trait WOOOE_Trait_GetValue.
                     */
                    do_action('load_woooe_classes', get_the_ID());
                    
                    self::$track = get_the_ID();
                    $this->data[get_the_ID()] = $this->load_data();
                }
                wp_reset_postdata();
                //Reset tracker
                self::$track = null;
            }
        }
        
        /*
         * Loads data for each order
         */
        function load_data(){
            
            if( self::$track ){
                
                $row = array();
                
                foreach($this->fields as $field){

                    //Check if it has class
                    if( isset($field['classname']) ){
                        
                        $field_name = str_replace('woooe_field_', '', $field['id']);
                        $class = $field['classname'];
                        $instance = $class::instance(self::$track);
                        $row[$field['id']] = $instance->get_value($field_name);
                    }

                    //Check if field has function
                    if( isset($field['function']) && is_callable($field['function']) ){
                        $row[$field['id']] = $field['function']();
                    }
                }
                
                return $row;
            }
        }
        
        /*
         * Gets records according to export fashion
         */
        function get_records($order_id, $data){
            
            $records = array();
            $export_style = woocommerce_settings_get_option('woooe_field_export_style', 'inline');

            $product_instance = WOOOE_Fetch_Product::instance($order_id);
            $product_names  =   $product_instance->product_name();
            
            switch($export_style){
                
                case 'inline':
                    $getData = array_map(function($element){

                        return is_array($element) ? implode(' | ', $element) : $element;
                        
                    }, $data);
                    array_push($records, $getData);
                break;

                case 'separate':
                    foreach($product_names as $key=>$product_name){

                        $getData = array_map(function($element, $key){

                            return is_array($element) ? ( isset($element[$key]) ? $element[$key] : '' ) : $element;

                        }, $data, array_fill(0, count($data), $key));

                        array_push($records, $getData);
                    }
                break;
            }
            
            return $records;
        }

        /*
         * Send the request to write data in file.
         */
        function write_data(){

            if( !is_array($this->data) ){
                return;
            }
            
            foreach( $this->data as $order_id=>$data ){

                $records = $this->get_records($order_id, $data);

                foreach($records as $record){
                    WOOOE_File_Handler::add_row($record);
                }
            }
        }
    }
}
