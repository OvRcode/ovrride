<?php
if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WOOOE_Data_Handler', false)){

    class WOOOE_Data_Handler {

        /*
         * Chunk size - number of records to fetch at single run.
         */
        static $chunk_size =10;

        /*
         * Holds the current query
         */
        static $query;

        /*
         * Starts the export process.
         * Creates file, loads data, writes file etc.
         */
        static function export_data(){

            $file_handler = WOOOE_File_Handler::prepare_file();
            $field_loader = new WOOOE_Fields_Loader(self::fields_to_export());
        }

        /*
         * Get chunk size
         */
        static function get_chunk_size(){
            return apply_filters('woooe_chunk_size', self::$chunk_size);
        }

        /*
         * Gets the current instance of query.
         */
        static function get_current_query(){

            /*
             * For cron requests, refresh report args
             */
            $args = self::get_report_args();
            self::$query = new WP_Query($args);
            
            return self::$query;
        }

        /*
         * Returns the fields which needs to be exported.
         */
        static function fields_to_export($exportable_fields = false){

            global $woooe;

            $fields = array();
            $reorder_settings = get_option('woooe_reorder_rename', array());

            foreach( $woooe->get_settings('general') as $value ){

                if( isset($value['export_field']) && 'yes' == $value['export_field'] ){
                    if( !$exportable_fields && 'yes' == woocommerce_settings_get_option($value['id'], 'no') ){
                        array_push($fields, $value);
                    }elseif ($exportable_fields) {
                        array_push($fields, $value);
                    }
                }
            }

            if( !empty($fields) && !empty($reorder_settings) && !$exportable_fields ){
                
                $temp_fields = $fields; //Temp holding variable
                $fields = array(); //Reset fields variable

                $plucked_list = wp_list_pluck($temp_fields, 'id');
                
                foreach($reorder_settings as $key=>$value){
                    $index = array_search($key, $plucked_list);
                    $val = $temp_fields[$index];
                    $val['name'] = stripslashes($value);
                    array_push($fields, $val);
                }
            }
            
            //Throw exceptions if no fields are selected to export.
            if( empty($fields) && !$exportable_fields){
                throw new Exception(__('No fields to export.', 'woooe'));
            }

            return $fields;
        }

        /*
         * Returns the order statuses to export.
         */
        static function get_statuses(){
            
            global $woooe;
            $statuses = array_keys(wc_get_order_statuses());
            
            $selected_statuses = array();
            
            foreach($statuses as $staus){
                
                if( 'yes' == woocommerce_settings_get_option('wooe_order_status_'.$staus, 'no') ){
                    array_push($selected_statuses, $staus);
                }
            }
            
            return !empty($selected_statuses) ? $selected_statuses : $statuses;
        }

        /*
         * Get arguments related to getting reports for orders.
         */
        static function get_report_args(){

            //Arguments for fetching.
            $args = array(
                        'post_type'=>'shop_order',
                        'posts_per_page'=> self::get_chunk_size(),
                        'post_status'=> self::get_statuses(),
                        'offset'=> (self::get_request_params('offset') * self::get_chunk_size())
                    );

            //Date query for orders.
            $args['date_query'] = array(
                                    'after' => self::get_request_params('startDate'),
                                    'before' => self::get_request_params('endDate'),
                                    'inclusive' => true,
                                );

            return apply_filters('woooe_report_args', $args);
        }

        /*
         * Get request parameters
         */
        static function get_request_params($return = null){

            //Requested return value should be scalar
            if(!is_scalar($return)){
                return array();
            }

            $startDate  = empty($_POST['startDate']) ? '' : $_POST['startDate'];
            $endDate    = empty($_POST['endDate']) ? '' : $_POST['endDate'];
            $offset     = empty($_POST['offset']) ? 0 : $_POST['offset'];
            $total_records  = empty($_POST['total_records']) ? 0 : $_POST['total_records'];
            $chunk_size = empty($_POST['chunk_size']) ? self::get_chunk_size() : $_POST['chunk_size'];
            $timestamp  = empty($_POST['timestamp']) ? time() : $_POST['timestamp'];

            $return_data = compact('startDate', 'endDate', 'offset', 'chunk_size', 'total_records', 'timestamp');

            if(!empty($return) && isset($return_data[$return])){
                return $return_data[$return];
            }
            
            return $return_data;
        }

        /*
         * Validates report data
         */
        static function validate(){

            $startDate  = self::get_request_params('startDate');
            $endDate    = self::get_request_params('endDate');

            if( empty($startDate) || empty($endDate) ){
                throw new Exception( __('Enter start date and End date', 'woooe') );
            }

            return true;
        }


    }
}