<?php

if (!defined('WPINC')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_Basic_Order_Import')){
class Wt_Import_Export_For_Woo_Basic_Order_Import {

    public $post_type = 'shop_order';
    public $parent_module = null;
    public $parsed_data = array();
    public $import_columns = array();
    public $merge;
    public $skip_new;
    public $merge_empty_cells;
    public $delete_existing;
    public $ord_link_using_sku;
    public $create_user;
    public $status_mail;
    public $new_order_status;    
    public $allow_unknown_products = true;
    public $item_data = array();
    public $is_order_exist = false;
    public $found_action = 'skip';
    public $id_conflict = 'skip';

    // Results
    var $import_results = array();

    public function __construct($parent_object) {

        $this->parent_module = $parent_object;
    }

    /* WC object based import  */

    public function prepare_data_to_import($import_data, $form_data, $batch_offset, $is_last_batch) {
        
        $this->found_action = !empty($form_data['advanced_form_data']['wt_iew_found_action']) ? $form_data['advanced_form_data']['wt_iew_found_action'] : 'skip'; 
        $this->id_conflict = !empty($form_data['advanced_form_data']['wt_iew_id_conflict']) ? $form_data['advanced_form_data']['wt_iew_id_conflict'] : 'skip'; 
        $this->merge_empty_cells = !empty($form_data['advanced_form_data']['wt_iew_merge_empty_cells']) ? 1 : 0; 
        $this->skip_new = !empty($form_data['advanced_form_data']['wt_iew_skip_new']) ? 1 : 0;        
        
        $this->delete_existing = !empty($form_data['advanced_form_data']['wt_iew_delete_existing']) ? 1 : 0;

        $this->ord_link_using_sku = !empty($form_data['advanced_form_data']['wt_iew_ord_link_using_sku']) ? 1 : 0;
        $this->create_user = !empty($form_data['advanced_form_data']['wt_iew_create_user']) ? 1 : 0;
        $this->notify_customer = !empty($form_data['advanced_form_data']['wt_iew_notify_customer']) ? 1 : 0;        
        $this->status_mail = !empty($form_data['advanced_form_data']['wt_iew_status_mail']) ? 1 : 0;
                
        Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Preparing for import.");

        $success = 0;
        $failed = 0;
        $msg = 'Order imported successfully.';
                
        foreach ($import_data as $key => $data) { 
            $row = $batch_offset+$key+1;
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing item.");
            $parsed_data = $this->parse_data($data);              
            if (!is_wp_error($parsed_data)){
                Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing item.");
                $result = $this->process_item($parsed_data);
                if(!is_wp_error($result)){
                    if($this->is_order_exist){
                        $msg = 'Order updated successfully.';
                    }
                    $this->import_results[$row] = array('row'=>$row, 'message'=>$msg, 'status'=>true, 'post_id'=>$result['id']); 
                    Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - ".$msg);                    
                    $success++;                     
                }else{
                   $this->import_results[$row] = array('row'=>$row, 'message'=>$result->get_error_message(), 'status'=>false, 'post_id'=>'');
                    Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Processing failed. Reason: ".$result->get_error_message());                   
                   $failed++;
                }                
            }else{
               $this->import_results[$row] = array('row'=>$row, 'message'=>$parsed_data->get_error_message(), 'status'=>false, 'post_id'=>'');
               Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Row :$row - Parsing failed. Reason: ".$parsed_data->get_error_message());               
               $failed++;                
            }            
        }
        
        if($is_last_batch && $this->delete_existing){
            $this->delete_existing();                        
        } 
        
        $this->clean_after_import();
                
        $import_response=array(
                'total_success'=>$success,
                'total_failed'=>$failed,
                'log_data'=>$this->import_results,
            );

        return $import_response;
    }
    
    public function clean_after_import() {
        global $wpdb;
        $posts = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_status = '%s' AND post_type = '%s' ", 'importing' ,$this->post_type)); 
        if($posts){
            array_map('wp_delete_post',$posts);
        }
    }
    
    public function delete_existing() {
    
        $posts = new WP_Query([
            'post_type' => $this->post_type,
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_status' => array_keys($this->wc_get_order_statuses_neat()),
            'meta_query' => [
                [
                    'key' => '_wt_delete_existing',
                    'compare' => 'NOT EXISTS',
                ]
            ]
        ]);
               
        foreach ($posts->posts as $post) {
            $this->import_results['detele_results'][$post] = wp_trash_post($post);
        }
        
        
        $posts = new WP_Query([
            'post_type' => $this->post_type,
            'fields' => 'ids',
            'posts_per_page' => -1,
            'post_status' => array_keys($this->wc_get_order_statuses_neat()),
            'meta_query' => [
                [
                    'key' => '_wt_delete_existing',
                    'compare' => 'EXISTS',
                ]
            ]
        ]);        
        foreach ($posts->posts as $post) {
            delete_post_meta($post,'_wt_delete_existing');
        }
                               
    }

    /**
     * Parse the data.
     *
     *
     * @param array $data value.
     *
     * @return array
     */
    public function parse_data($data) {

        try {
            $data = apply_filters('wt_woocommerce_order_importer_pre_parse_data', $data);

            $mapping_fields = $data['mapping_fields'];

            $this->item_data = array(); // resetting WC default data before parsing new item to avoid merging last parsed item wp_parse_args
            
            if(isset($mapping_fields['order_id']) && !empty($mapping_fields['order_id'])){
                $this->item_data['order_id'] = $this->wt_order_existance_check($mapping_fields['order_id']);  // to determine wether merge or import
            }

            
            if(!$this->merge){
                $default_data = $this->get_default_data();                
                $this->item_data  = wp_parse_args( $this->item_data, $default_data );
            }
            
                
            if($this->merge && !$this->merge_empty_cells){
                $this->item_data = array();
                $this->item_data['order_id'] = $this->order_id; // $this->order_id set from wt_order_existance_check
            }

            foreach ($mapping_fields as $column => $value) {
                if($this->merge && !$this->merge_empty_cells && $value == ''){
                    continue;
                }

                $column = strtolower($column);
                                 
                if ('order_number' == $column) {
                    $this->item_data['order_number'] = $this->wt_parse_order_number_field($value);
                    continue;
                }

                if ('parent_id' == $column || 'post_parent' == $column) {
                    $this->item_data['parent_id'] = $this->wt_parse_int_field($value);
                    continue;
                }

                if ('_payment_method_title' == $column || 'payment_method_title' == $column) {
                    $this->item_data['payment_method_title'] = $value;
                    continue;
                }
                if ('transaction_id' == $column ) {
                    $this->item_data['transaction_id'] = $value;
                    continue;
                }
                if ('customer_ip_address' == $column ) {
                    $this->item_data['customer_ip_address'] = $value;
                    continue;
                }
                if ('customer_user_agent' == $column) {
                    $this->item_data['customer_user_agent'] = $value;
                    continue;
                }
                if ( 'date_created' == $column || 'post_date' == $column || 'order_date' == $column) {
                    $date = $this->wt_parse_date_field($value,$column);
                    $this->item_data['date_created'] = date('Y-m-d H:i:s', $date);
                    continue;
                }  
                if(('_paid_date' == $column || 'paid_date' == $column) && $value != ''){

                    $date = $this->wt_parse_date_field($value,$column);
                    $this->item_data['date_paid'] = date('Y-m-d H:i:s', $date);
                    continue;
                }

                if ('post_modified' == $column || 'date_modified' == $column || 'date_completed' == $column || '_completed_date' == $column ) {
                    $date = $this->wt_parse_date_field($value,$column);
                    $this->item_data['date_modified'] = date('Y-m-d H:i:s', $date);
                    $this->item_data['date_completed'] = date('Y-m-d H:i:s', $date);
                    continue;
                }

                if ('status' == $column || 'post_status' == $column) {
                    $this->item_data['status'] = $this->wt_parse_status_field($value);                
                    continue;
                }

                if ('shipping_tax_total' == $column ) {
                    $this->item_data['shipping_tax_total'] = wc_format_decimal($value);
                    $this->item_data['shipping_tax'] = wc_format_decimal($value);
                    continue;
                }
                if ('fee_total' == $column ) {
                    $this->item_data['fee_total'] = wc_format_decimal($value);
                    continue;
                }
                if ('fee_tax_total' == $column ) {
                    $this->item_data['fee_tax_total'] = wc_format_decimal($value);
                    continue;
                }
                if ('tax_total' == $column ) {
                    $this->item_data['tax_total'] = wc_format_decimal($value);
                    continue;
                }
                if ('cart_discount' == $column ) {
                    $this->item_data['cart_discount'] = wc_format_decimal($value);
                    $this->item_data['cart_tax'] = wc_format_decimal($value);
                    continue;
                }
                if ('order_discount' == $column ) {
                    $this->item_data['order_discount'] = wc_format_decimal($value);
                    continue;
                }
                if ('discount_total' == $column ) {
                    $this->item_data['discount_total'] = wc_format_decimal($value);
                    $this->item_data['discount_tax'] = wc_format_decimal($value);
                    continue;
                }            
                if ('order_total' == $column ) {
                    $this->item_data['order_total'] = wc_format_decimal($value);
                    $this->item_data['total'] = wc_format_decimal($value);
                    $this->item_data['total_tax'] = wc_format_decimal($value);
                    continue;
                }
                if ('order_currency' == $column ) {
                    $this->item_data['currency'] = ($value) ? $value : get_woocommerce_currency();
                    continue;
                }
                if ('payment_method' == $column) {
                    $this->item_data['payment_method'] = $this->wt_parse_payment_method_field($value);
                    continue;
                }                        
                if ('shipping_method' == $column ) {
                    $this->item_data['shipping_method'] = $this->wt_parse_shipping_method_field($value);
                    continue;
                }
                if ('order_shipping' == $column || 'shipping_total' == $column) {
                    if ('shipping_total' == $column ) {
                        $this->item_data['shipping_total'] = wc_format_decimal($value);
                    }
                    $this->item_data['order_shipping'] = $this->wt_parse_order_shipping_field($value,$column,$mapping_fields); // special case need to rewrite this concept
                    continue;
                }
                if ('customer_user' == $column || 'customer_email' == $column || 'customer_id' == $column ) {                    
                    $this->wt_parse_customer_id_field($value,$column,$mapping_fields);
                    continue;
                }
                if ('billing_first_name' == $column ) {
                    $this->item_data['billing']['first_name'] = ($value);
                    continue;
                }
                if ('billing_last_name' == $column ) {
                     $this->item_data['billing']['last_name'] = ($value);
                    continue;
                }
                if ('billing_company' == $column ) {
                     $this->item_data['billing']['company'] = ($value);
                    continue;
                }
                if ('billing_email' == $column ) {
                     $this->item_data['billing']['email'] = $this->wt_parse_email_field($value);
                    continue;
                }
                if ('billing_phone' == $column ) {
                     $this->item_data['billing']['phone'] = trim($value,'\'');
                    continue;
                }
                if ('billing_address_1' == $column ) {
                     $this->item_data['billing']['address_1'] = ($value);
                    continue;
                }
                if ('billing_address_2' == $column ) {
                     $this->item_data['billing']['address_2'] = ($value);
                    continue;
                }
                if ('billing_postcode' == $column ) {
                     $this->item_data['billing']['postcode'] = ($value);
                    continue;
                }
                if ('billing_city' == $column ) {
                     $this->item_data['billing']['city'] = ($value);
                    continue;
                }
                if ('billing_state' == $column ) {
                     $this->item_data['billing']['state'] = ($value);
                    continue;
                }
                if ('billing_country' == $column ) {
                     $this->item_data['billing']['country'] = ($value);
                    continue;
                }
                if ('shipping_first_name' == $column ) {
                     $this->item_data['shipping']['first_name'] = ($value);
                    continue;
                }
                if ('shipping_last_name' == $column ) {
                     $this->item_data['shipping']['last_name'] = ($value);
                    continue;
                }
                if ('shipping_company' == $column ) {
                     $this->item_data['shipping']['company'] = ($value);
                    continue;
                }
                if ('shipping_phone' == $column ) {
                     $this->item_data['shipping']['phone'] = trim($value,'\'');
                    continue;
                }                
                if ('shipping_address_1' == $column) {
                    $this->item_data['shipping']['address_1'] = ($value);
                    continue;
                }
                if ('shipping_address_2' == $column ) {
                    $this->item_data['shipping']['address_2'] = ($value);
                    continue;
                }
                if ('shipping_postcode' == $column ) {
                    $this->item_data['shipping']['postcode'] = ($value);
                    continue;
                }
                if ('shipping_city' == $column ) {
                    $this->item_data['shipping']['city'] = ($value);
                    continue;
                }
                if ('shipping_state' == $column ) {
                    $this->item_data['shipping']['state'] = ($value);
                    continue;
                }
                if ('shipping_country' == $column ) {
                    $this->item_data['shipping']['country'] = ($value);
                    continue;
                }
                if ('customer_note' == $column || 'post_excerpt' == $column ) {
                    $this->item_data['customer_note'] = ($value);
                    continue;
                }

                if ('shipping_items' == $column ) {
                    $this->item_data['shipping_items'] = $this->wt_parse_shipping_items_field($value);
                    continue;
                }
                if ('fee_items' == $column) {
                    $this->item_data['fee_items'] = $this->wt_parse_fee_items_field($value);
                    continue;
                }
                if ('tax_items' == $column) {
                    $this->item_data['tax_items'] = $this->wt_parse_tax_items_field($value);
                    continue;
                }
                if ('coupon_items' == $column) {
                    $this->item_data['coupon_items'] = $this->wt_parse_coupon_items_field($value);
                    continue;
                }
                if ('refund_items' == $column) {
                    $this->item_data['refund_items'] = $this->wt_parse_refund_items_field($value);
                    continue;
                }
                if ('order_notes' == $column) {
                    $this->item_data['order_notes'] = $this->wt_parse_order_notes_field($value);
                    continue;
                }
                if ('download_permissions' == $column) {
                    $this->item_data['meta_data'][] = array('key' => '_download_permissions_granted', 'value' => $value);
                    $this->item_data['meta_data'][] = array('key' => '_download_permissions', 'value' => $value);
                    continue;
                }
                if ('wt_import_key' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'_wt_import_key','value'=>$value);
                    continue;
                }
                
                if ('meta:wf_invoice_number' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'wf_invoice_number','value'=>$value);
                    continue;
                }
                if ('meta:_wf_invoice_date' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'_wf_invoice_date','value'=> strtotime($value));
                    continue;
                }  
                if ('meta:ywot_tracking_code' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'ywot_tracking_code', 'value'=> $value);
                    continue;
                }
                if ('meta:ywot_tracking_postcode' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'ywot_tracking_postcode', 'value'=> $value);
                    continue;
                }  
                if ('meta:ywot_carrier_id' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'ywot_carrier_id', 'value'=> $value);
                    continue;
                }  
                if ('meta:ywot_pick_up_date' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'ywot_pick_up_date', 'value'=> $value);
                    continue;
                }  
                if ('meta:ywot_picked_up' == $column ) {
                    $this->item_data['meta_data'][] = array('key'=>'ywot_picked_up', 'value'=> $value);
                    continue;
                }                  

                if(strstr($column, 'line_item_')){
                    $this->item_data['order_items'][] = $this->wt_parse_line_item_field($value,$column);
                    continue;                
                }

            }  

            if(empty($this->item_data['order_id'])){                                 
                $this->item_data['order_id'] = $this->wt_parse_id_field($mapping_fields,$this->item_data);
            } 
            
            if (empty($this->item_data['status'])) {
                    if ($this->item_data['order_id'] > 0) {
                        $ord = wc_get_order($this->item_data['order_id']);
                        $this->item_data['status'] = preg_replace('/^wc-/', '', $ord->get_status());
                    }
            }

            return $this->item_data;
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }

    }
    
    public function wt_order_existance_check($id){        
        global $wpdb;  
        $order_id = 0;
        $this->merge = false;
        $this->is_order_exist = false;
                
        $id = absint($id);
        $id_found_with_id = '';
        if($id){                              
            $id_found_with_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_status IN ( 'wc-pending', 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-failed' , 'wc-refunded', 'wc-cancelled')  AND ID = %d;", $id)); // WPCS: db call ok, cache ok.
            if($id_found_with_id){
               if($this->post_type == get_post_type($id_found_with_id)){
                   $this->is_order_exist = true;
                   $order_id = $id_found_with_id;
               }
            }            
        } 
                
        if($this->is_order_exist){
            if('skip' == $this->found_action){
                if($id && $id_found_with_id ){
                    throw new Exception(sprintf('Order with same ID already exists. ID: %d',$id ));
                }                 
            }elseif('update' == $this->found_action){
                $this->merge = true; 
                $this->order_id = $order_id;
                return $order_id;
            }                            
        }  
        
        if($this->skip_new){
            throw new Exception('Skipping new item' );
        } 
                        
        if ($id && is_string(get_post_status($id)) && (get_post_type($id) !== $this->post_type ) && !$this->is_order_exist && 'skip' == $this->id_conflict) {
            throw new Exception(sprintf('Importing Order(ID) conflicts with an existing post. ID: %d',$id ));               
        }
        
    }

    /**
     * Explode CSV cell values using commas by default, and handling escaped
     * separators.
     *
     * @since  3.2.0
     * @param  string $value     Value to explode.
     * @param  string $separator Separator separating each value. Defaults to comma.
     * @return array
     */
    protected function wt_explode_values($value, $separator = ',') {
        $value = str_replace('\\,', '::separator::', $value);
        $values = explode($separator, $value);
        $values = array_map(array($this, 'wt_explode_values_formatter'), $values);

        return $values;
    }

    /**
     * Remove formatting and trim each value.
     *
     * @since  3.2.0
     * @param  string $value Value to format.
     * @return string
     */
    protected function wt_explode_values_formatter($value) {
        return trim(str_replace('::separator::', ',', $value));
    }
    
    public function wt_parse_order_number_field($value) {
        $order_number_formatted = $this->order_id;
        $order_number = (!empty($value) ? $value : ( is_numeric($order_number_formatted) ? $order_number_formatted : 0 ) );
        
        if ($order_number_formatted) {
            // verify that this order number isn't already in use
            $query_args = array(
                'numberposts' => 1,
                'meta_key' => apply_filters('woocommerce_order_number_formatted_meta_name', '_order_number_formatted'),
                'meta_value' => $order_number_formatted,
                'post_type' => $this->post_type,
                'post_status' => array_keys(wc_get_order_statuses()),
                'fields' => 'ids',
            );

            $order_id = 0;
            $orders = get_posts($query_args);
            if (!empty($orders)) {
                list( $order_id ) = get_posts($query_args);
            }

            $order_id = apply_filters('woocommerce_find_order_by_order_number', $order_id, $order_number_formatted);

            if ($order_id) {
                // skip if order ID already exist.                 
                throw new Exception(sprintf('Skipped. %s already exists.', ucfirst($this->parent_module->module_base)) );
            }
        }
        if ($order_number_formatted)
            $this->item_data['order_number_formatted'] = $order_number_formatted;      
            
        if (!is_null($order_number))
            return $order_number;  // optional order number, for convenience
        
    }
    
    public function wt_parse_date_field($value, $column) { 
        
        $date = $value;
        
        if($value == ''){
            $date = date('Y-m-d h:i:s');
        }
                
        if(false === ( $date = strtotime($date) )) {
            // invalid date format
            throw new Exception(sprintf('Skipped. Invalid date format %s in column %s.', $value,$column) ); 
        }
        return $date;        
    }
    
    public function wt_parse_customer_id_field($value,$column,$data) {  
        if(isset($this->item_data['customer_id']) && !empty($this->item_data['customer_id'])){
            return $this->item_data['customer_id'];
        }
        if (isset($value) && $value) {
            // attempt to find the customer user
            
            $found_customer = null;
            switch ($column) {
                case 'customer_id':  
                case 'customer_user':
                    $customer = get_user_by('id', $value);
                    if($customer){
                        $this->item_data['customer_id'] = $value;
                    }                                  
                    break;

                case 'customer_email':                     
                    // check by email
                    if(is_email($value)){
                        $found_customer = email_exists($value);
                        if($found_customer){  
                            $this->item_data['customer_id'] = $found_customer;
                            break;                            
                        }else{
                            if ($this->create_user && is_email($value)) {                                
                                $customer_email = $value;                                
                                $username = (!empty($data['_customer_username']) ) ? $data['_customer_username'] : '';
                                // Not in test mode, create a user account for this email
                                if (empty($username)) {
                                    $maybe_username = explode('@', $customer_email);
                                    $maybe_username = sanitize_user($maybe_username[0]);
                                    $counter = 1;
                                    $username = $maybe_username;
                                    while (username_exists($username)) {
                                        $username = $maybe_username . $counter;
                                        $counter++;
                                    }
                                }
                                if (!empty($data['_customer_password'])) {
                                    $password = $data['_customer_password'];
                                } else {
                                    $password = wp_generate_password(12, true);
                                }
                                $found_customer = wp_create_user($username, $password, $customer_email);
                                if (!is_wp_error($found_customer)) {
                                    $user_meta_fields = array(
                                        'billing_first_name', // Billing Address Info
                                        'billing_last_name',
                                        'billing_company',
                                        'billing_address_1',
                                        'billing_address_2',
                                        'billing_city',
                                        'billing_state',
                                        'billing_postcode',
                                        'billing_country',
                                        'billing_email',
                                        'billing_phone',
                                        'shipping_first_name', // Shipping Address Info
                                        'shipping_last_name',
                                        'shipping_company',
                                        'shipping_address_1',
                                        'shipping_address_2',
                                        'shipping_city',
                                        'shipping_state',
                                        'shipping_postcode',
                                        'shipping_country',
                                    );
     
                                    
                                    // update user meta data
                                    foreach ($user_meta_fields as $key) {
                                        switch ($key) {
                                            case 'billing_email':
                                                // user billing email if set in csv otherwise use the user's account email
                                                $meta_value = (!empty($data[$key])) ? $data[$key] : $customer_email;
                                                $key = substr($key, 1);
                                                update_user_meta($found_customer, $key, $meta_value);
                                                break;

                                            case 'billing_first_name':
                                                $meta_value = (!empty($data[$key])) ? $data[$key] : $username;
                                                $key = substr($key, 1);
                                                update_user_meta($found_customer, $key, $meta_value);
                                                update_user_meta($found_customer, 'first_name', $meta_value);
                                                break;

                                            case 'billing_last_name':
                                                $meta_value = (!empty($data[$key])) ? $data[$key] : '';
                                                $key = substr($key, 1);
                                                update_user_meta($found_customer, $key, $meta_value);
                                                update_user_meta($found_customer, 'last_name', $meta_value);
                                                break;

                                            case 'shipping_first_name':
                                            case 'shipping_last_name':
                                            case 'shipping_address_1':
                                            case 'shipping_address_2':
                                            case 'shipping_city':
                                            case 'shipping_postcode':
                                            case 'shipping_state':
                                            case 'shipping_country':
                                                // Set the shipping address fields to match the billing fields if not specified in CSV
                                                $meta_value = (!empty($data[$key])) ? $data[$key] : '';

                                                if (empty($meta_value)) {
                                                    $n_key = str_replace('shipping', 'billing', $key);
                                                    $meta_value = (!empty($data[$n_key])) ? $data[$n_key] : '';
                                                }
                                                $key = substr($key, 1);
                                                update_user_meta($found_customer, $key, $meta_value);
                                                break;

                                            default:
                                                $meta_value = (!empty($data[$key])) ? $data[$key] : '';
                                                $key = substr($key, 1);
                                                update_user_meta($found_customer, $key, $meta_value);
                                        }
                                    }
                                    $wp_user_object = new WP_User($found_customer);
                                    $wp_user_object->set_role('customer');
                                    // send user registration email if admin as chosen to do so
                                    if ($this->notify_customer && function_exists('wp_new_user_notification')) {
                                        $previous_option = get_option('woocommerce_registration_generate_password');
                                        // force the option value so that the password will appear in the email
                                        update_option('woocommerce_registration_generate_password', 'yes');
                                        do_action('woocommerce_created_customer', $found_customer, array('user_pass' => $password), true);
                                        update_option('woocommerce_registration_generate_password', $previous_option);
                                    }
                                    
                                    $this->item_data['customer_id'] = $found_customer;
                                    break;
                                }
                            }
                        }
                    }                    
            }           
        } 
        
    }

    public function wt_parse_product_ids_field($value) {
        return $value;
    }


    public function wt_parse_email_field($value) {
        return is_email($value) ? $value : '';
    }
    
    
    
    public function wt_parse_order_shipping_field($value,$column,$item) {
                        
        $available_methods = WC()->shipping()->load_shipping_methods();
        
        $order_shipping = $value;
        
        $order_shipping_methods = array();
        $_shipping_methods = array();

        // pre WC 2.1 format of a single shipping method, left for backwards compatibility of import files
        if (isset($item['shipping_method']) && $item['shipping_method']) {
            // collect the shipping method id/cost
            $_shipping_methods[] = array(
                $item['shipping_method'],
                isset($item['shipping_cost']) ? $item['shipping_cost'] : null
            );
        }

        // collect any additional shipping methods
        $i = null;
        if (isset($item['shipping_method_1'])) {
            $i = 1;
        } elseif (isset($item['shipping_method_2'])) {
            $i = 2;
        }

        if (!is_null($i)) {
            while (!empty($item['shipping_method_' . $i])) {

                $_shipping_methods[] = array(
                    $item['shipping_method_' . $i],
                    isset($item['shipping_cost_' . $i]) ? $item['shipping_cost_' . $i] : null
                );
                $i++;
            }
        }
        
        // if the order shipping total wasn't set, calculate it
        if (!isset($order_shipping)) {

            $order_shipping = 0;
            foreach ($_shipping_methods as $_shipping_method) {
                $order_shipping += $_shipping_method[1];
            }
            $postmeta[] = array('key' => '_order_shipping' . $column, 'value' => number_format((float) $order_shipping, 2, '.', ''));
        } elseif (isset($order_shipping) && 1 == count($_shipping_methods) && is_null($_shipping_methods[0][1])) {
            // special case: if there was a total order shipping but no cost for the single shipping method, use the total shipping for the order shipping line item
            $_shipping_methods[0][1] = $order_shipping;
        }

        foreach ($_shipping_methods as $_shipping_method) {

            // look up shipping method by id or title
            $shipping_method = isset($available_methods[$_shipping_method[0]]) ? $_shipping_method[0] : null;

            if (!$shipping_method) {
                // try by title
                foreach ($available_methods as $method) {
                    if (0 === strcasecmp($method->title, $_shipping_method[0])) {
                        $shipping_method = $method->id;
                        break;  // go with the first one we find
                    }
                }
            }

            if ($shipping_method) {
                // known shipping method found
                $order_shipping_methods[] = array('cost' => $_shipping_method[1], 'title' => $available_methods[$shipping_method]->title);
            } elseif ($_shipping_method[0]) {
                // Standard format, shipping method but no title
                $order_shipping_methods[] = array('cost' => $_shipping_method[1], 'title' => '');
            }
        }
        
        return $order_shipping_methods;
        
    }
    
    public function wt_parse_shipping_method_field($value){
        
        
        $order_shipping_methods = array();
        $_shipping_methods = array();
        
        $available_methods = WC()->shipping()->load_shipping_methods();
        // look up shipping method by id or title
        $shipping_method_obj = isset($available_methods[$value]) ? $available_methods[$value] : $value;

        if (!$shipping_method_obj) {
            // try by title
            foreach ($available_methods as $method) {
                if (0 === strcasecmp($method->title, $value)) {
                    $shipping_method = $method->id;
                    break;  // go with the first one we find
                }
            }
            $shipping_method_obj = isset($available_methods[$shipping_method]) ? $available_methods[$shipping_method] : $shipping_method;
        }
        return $shipping_method_obj;
    }
    
    public function wt_parse_payment_method_field($value) {
        $available_gateways = WC()->payment_gateways->payment_gateways();        
        // look up shipping method by id or title
        $payment_method_obj = isset($available_gateways[$value]) ? $available_gateways[$value] : $value;
        if (!$payment_method_obj) {
            // try by title
            foreach ($available_gateways as $method) {
                if (0 === strcasecmp($method->title, $value)) {
                    $payment_method = $method->id;
                    break;  // go with the first one we find
                }
            }
			if(isset($payment_method)){
				$payment_method_obj = isset($available_gateways[$payment_method]) ? $available_gateways[$payment_method] : $payment_method;
			}
        }
        return $payment_method_obj;                
    }
    
    public function wt_parse_shipping_items_field($value) {
        $shipping_items = array();
        if('' !== $value){
            $shipping_line_items = explode('|', $value);
            $items = array_shift($shipping_line_items);
            $items = substr($items, strpos($items, ":") + 1);
            $method_id = array_shift($shipping_line_items);
            $method_id = substr($method_id, strpos($method_id, ":") + 1);
            $taxes = array_shift($shipping_line_items);
            $taxes = substr($taxes, strpos($taxes, ":") + 1);

            $shipping_items = array(
                'Items' => $items,
                'method_id' => $method_id,
                'taxes' => maybe_unserialize($taxes)
            );
        }
        
        return $shipping_items;
        
    }
        
    public function wt_parse_fee_items_field($value) {
        $fee_items = array();
        if( '' !== $value){
            $fee_line_items = explode('||', $value);            
            foreach ($fee_line_items as $fee_line_item) {
                $fee_item_meta = explode('|', $fee_line_item);
                $name = array_shift($fee_item_meta);
                $name = substr($name, strpos($name, ":") + 1);
                $total = array_shift($fee_item_meta);
                $total = substr($total, strpos($total, ":") + 1);
                $tax = array_shift($fee_item_meta);
                $tax = substr($tax, strpos($tax, ":") + 1);
                $tax_data = array_shift($fee_item_meta);
                $tax_data = substr($tax_data, strpos($tax_data, ":") + 1);

                $fee_items[] = array(
                    'name' => $name,
                    'total' => $total,
                    'tax' => $tax,
                    'tax_data' => $tax_data
                );                        
            }        
        }        
        return $fee_items;
    }
        
    public function wt_parse_tax_items_field($value) {
        global $wpdb;
        $tax_rates = array();

        foreach ($wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_tax_rates") as $_row) {
            $tax_rates[$_row->tax_rate_id] = $_row;
        }
        
        $tax_items = array();

        // standard tax item format which supports multiple tax items in numbered columns containing a pipe-delimated, colon-labeled format
//        if (isset($item['tax_items']) && !empty($item['tax_items'])) {
            // one or more order tax items
            // get the first tax item
            $tax_item = explode(';', $value);
//            $tax_amount_sum = $shipping_tax_amount_sum = 0;
            foreach ($tax_item as $tax) {

                $tax_item_data = array();

                // turn "label: Tax | tax_amount: 10" into an associative array
                foreach (explode('|', $tax) as $piece) {
                    list( $name, $value ) = array_pad(explode(':', $piece), 2, null);
					if(isset($name)){
                    $tax_item_data[trim($name)] = trim($value);
					}
                }
                
                // default rate id to 0 if not set
                if (!isset($tax_item_data['rate_id'])) {
                    $tax_item_data['rate_id'] = 0;
                }

                if (!isset($tax_item_data['rate_percent'])) {
                    $tax_item_data['rate_percent'] = '';
                }

                // have a tax amount or shipping tax amount
                if (isset($tax_item_data['total']) || isset($tax_item_data['shipping_tax_amount'])) {
                    // try and look up rate id by label if needed
                    if (isset($tax_item_data['label']) && $tax_item_data['label'] && !$tax_item_data['rate_id']) {
                        foreach ($tax_rates as $tax_rate) {

                            if (0 === strcasecmp($tax_rate->tax_rate_name, $tax_item_data['label'])) {
                                // found the tax by label
                                $tax_item_data['rate_id'] = $tax_rate->tax_rate_id;
                                break;
                            }
                        }
                    }

                    // check for a rate being specified which does not exist, and clear it out (technically an error?)
                    if ($tax_item_data['rate_id'] && !isset($tax_rates[$tax_item_data['rate_id']])) {
                        $tax_item_data['rate_id'] = 0;
                    }

                    // default label of 'Tax' if not provided
                    if (!isset($tax_item_data['label']) || !$tax_item_data['label']) {
                        $tax_item_data['label'] = 'Tax';
                    }

                    // default tax amounts to 0 if not set
                    if (!isset($tax_item_data['total'])) {
                        $tax_item_data['total'] = 0;
                    }
                    if (!isset($tax_item_data['shipping_tax_amount'])) {
                        $tax_item_data['shipping_tax_amount'] = 0;
                    }

                    // handle compound flag by using the defined tax rate value (if any)
                    if (!isset($tax_item_data['tax_rate_compound'])) {
                        $tax_item_data['tax_rate_compound'] = '';
                        if ($tax_item_data['rate_id']) {
                            $tax_item_data['tax_rate_compound'] = $tax_rates[$tax_item_data['rate_id']]->tax_rate_compound;
                        }
                    }
                    
                    $tax_items[] = array(
                        'title' => $tax_item_data['code'],
                        'rate_id' => $tax_item_data['rate_id'],
                        'label' => $tax_item_data['label'],
                        'compound' => $tax_item_data['tax_rate_compound'],
                        'tax_amount' => $tax_item_data['total'],
                        'shipping_tax_amount' => $tax_item_data['shipping_tax_amount'],
                        'rate_percent' => $tax_item_data['rate_percent'],
                    );
                }
            }
//        }
        return $tax_items;
    }
        
    public function wt_parse_coupon_items_field($value) {
        
        
        // standard format
        $coupon_item = array();

        if(isset($value) && !empty($value)){
            $coupon_item = explode(';', $value);
        }
        
        return $coupon_item;        
        
    }

    public function wt_parse_refund_items_field($value) {
 
        //added since refund not importing 
        $refund_item = array();
        if(isset($value) && !empty($value)){
            $refund_item = explode(';', $value);
        }
        return $refund_item;
    }
    
    public function wt_parse_order_notes_field($value) {
         
        $order_notes = array();
        if (!empty($value)) {
            $order_notes = explode("||", $value);
        }
        return $order_notes;
    }
 
    public function wt_parse_line_item_field($value,$column) {
            if(empty($value)){
                return array();
            }

            global $wpdb;
            $order_items = array();            
            $variation = FALSE;
            //$_item_meta = preg_split("~\\\\.(*SKIP)(*FAIL)|\|~s", $item['line_item_' . $i]);
            $_item_meta = array();
            if ($value && empty($_item_meta)) {
                $_item_meta = explode(apply_filters('wt_change_item_separator','|'), $value);
            }

            // get any additional item meta
            $item_meta = array();
            foreach ($_item_meta as $pair) {

                // replace any escaped pipes
                $pair = str_replace('\|', '|', $pair);

                // find the first ':' and split into name-value
                $split = strpos($pair, ':');
                $name = substr($pair, 0, $split);
                $value = substr($pair, $split + 1);
                switch ($name) {
                    case 'name':
                        $unknown_product_name = $value;
                        break;
                    case 'product_id':
                        $product_identifier_by_id = $value;
                        break;
                    case 'sku':
                        $product_identifier_by_sku = $value;
                        break;
                    case 'quantity':
                        $qty = $value;
                        break;
                    case 'total':
                        $total = $value;
                        break;
                    case 'sub_total':
                        $sub_total = $value;
                        break;
                    case 'tax':
                        $tax = $value;
                        break;
                    case 'tax_data':
                        $tax_data = $value;
                        break;
                    default :
                        $item_meta[$name] = $value;
                }

            }

            if($this->ord_link_using_sku || (empty($product_identifier_by_id))){
                $product_sku = !empty($product_identifier_by_sku) ? $product_identifier_by_sku : '';
                if ($product_sku){
                    $product_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value=%s LIMIT 1", $product_sku));
                    if(!empty($product_id)){
                        if(get_post_type($product_id) == 'product_variation'){
                            $variation = TRUE;
                            $variation_id = $product_id;
                            $product_id = wp_get_post_parent_id($variation_id);
                            $item_meta['_variation_id'] = $variation_id;
                        }
                    }
                } else {
                    $product_id = '';
                }
            } else {
                if (!empty($product_identifier_by_id)) {
                    // product by product_id
                    $product_id = $product_identifier_by_id;

                    // not a product
                    if (!in_array(get_post_type($product_id), array('product', 'product_variation'))) {
                        $product_id = '';
                    }
                } else {
                    $product_id = '';
                }
            }

            if (!$this->allow_unknown_products && !$product_id) {
                // unknown product
//                $this->hf_order_log_data_change('hf-order-csv-import', sprintf(__('> > Skipped. Unknown order item: %s.'), $product_identifier));
                return ;

            }


            $order_items = array(
                'product_id'    => !empty($product_id) ? $product_id : 0,
                'qty'           => !empty($qty) ? $qty : 0,
                'total'         => !empty($total) ? $total : 0,
                'sub_total'     => !empty($sub_total) ? $sub_total : 0,
                'tax'           => !empty($tax) ? $tax : 0,
                'meta'          => $item_meta,
                'product_name'  => !empty($unknown_product_name) ? $unknown_product_name : ''
            );
            if(!empty($tax_data)){
                $order_items['tax_data'] = $tax_data;
            }
                
            return $order_items;             
    }



    /**
     * Parse relative field and return product ID.
     *
     * Handles `id:xx` and SKUs.
     *
     * If mapping to an id: and the product ID does not exist, this link is not
     * valid.
     *
     * If mapping to a SKU and the product ID does not exist, a temporary object
     * will be created so it can be updated later.
     *
     * @param string $value Field value.
     *
     * @return int|string
     */
    public function wt_parse_relative_field($value) {
        global $wpdb;

        if (empty($value)) {
            return '';
        }

        // IDs are prefixed with id:.
        if (preg_match('/^id:(\d+)$/', $value, $matches)) {
            $id = intval($matches[1]);

            // If original_id is found, use that instead of the given ID since a new placeholder must have been created already.
            $original_id = $wpdb->get_var($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_original_id' AND meta_value = %s;", $id)); // WPCS: db call ok, cache ok.

            if ($original_id) {
                return absint($original_id);
            }

            // See if the given ID maps to a valid product allready.
            $existing_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type IN ( 'product', 'product_variation' ) AND ID = %d;", $id)); // WPCS: db call ok, cache ok.

            if ($existing_id) {
                return absint($existing_id);
            }

            // If we're not updating existing posts, we may need a placeholder product to map to.
            if (!$this->params['update_existing']) {
                $product = wc_get_product_object('simple');
                $product->set_name('Import placeholder for ' . $id);
                $product->set_status('importing');
                $product->add_meta_data('_original_id', $id, true);
                $id = $product->save();
            }

            return $id;
        }

        $id = wc_get_product_id_by_sku($value);

        if ($id) {
            return $id;
        }

        try {
            $product = wc_get_product_object('simple');
            $product->set_name('Import placeholder for ' . $value);
            $product->set_status('importing');
            $product->set_sku($value);
            $id = $product->save();

            if ($id && !is_wp_error($id)) {
                return $id;
            }
        } catch (Exception $e) {
            return '';
        }

        return '';
    }

    /**
     * Parse the ID field.
     *
     * If we're not doing an update, create a placeholder product so mapping works
     * for rows following this one.
     *
     * @param string $value Field value.
     *
     * @return int
     */
    public function wt_parse_id_field($data, $parsed_data) {
         
		if(!isset($data['order_id'])){
			return 0;
		}
		
        $id = $this->wt_order_existance_check($data['order_id']);         
        if($id){
            return $id;
        }
        
        if(class_exists('HF_Subscription')){
            remove_all_actions('save_post');
        }
                
        $date = !empty($parsed_data['date_created']) ? $parsed_data['date_created'] : date('Y-m-d H:i:s', time());
        $postdata = array( // if not specifiying id (id is empty) or if not found by given id  
            'post_date'     => $date,
            'post_date_gmt' => $date,
            'post_type'     => $this->post_type,
            'post_status'   => 'wc-pending',
            'ping_status'   => 'closed',
            'post_author'   => 1,
            'post_title'    => sprintf( 'Order &ndash; %s', strftime( '%b %d, %Y @ %I:%M %p', strtotime($date)) ),
            'post_password' => wc_generate_order_key(),
            'post_parent'   => !empty($parsed_data['parent_id']) ? $parsed_data['parent_id'] : 0,
            'post_excerpt'  => !empty($parsed_data['customer_note']) ? $parsed_data['customer_note'] : '',
        );                  
        if(isset($data['order_id']) && !empty($data['order_id'])){
            $postdata['import_id'] = $data['order_id'];
        }                   
        $post_id = wp_insert_post( $postdata, true );                
        if($post_id && !is_wp_error($post_id)){
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', sprintf('Importing as new '. ($this->parent_module->module_base).' ID:%d',$post_id ));
            return $post_id;
        }else{
            throw new Exception($post_id->get_error_message());
        }
                          
    }
    
    
    /**
     * Parse relative comma-delineated field and return product ID.
     *
     * @param string $value Field value.
     *
     * @return array
     */
    public function wt_parse_relative_comma_field($value) {
        if (empty($value)) {
            return array();
        }

        return array_filter(array_map(array($this, 'wt_parse_relative_field'), $this->wt_explode_values($value)));
    }

    /**
     * Parse a comma-delineated field from a CSV.
     *
     * @param string $value Field value.
     *
     * @return array
     */
    public function parse_comma_field($value) {
        if (empty($value) && '0' !== $value) {
            return array();
        }

        $value = $this->unescape_data($value);
        return array_map('wc_clean', $this->wt_explode_values($value));
    }

    /**
     * Parse a field that is generally '1' or '0' but can be something else.
     *
     * @param string $value Field value.
     *
     * @return bool|string
     */
    public function wt_parse_bool_field($value) {
        if ('0' === $value) {
            return false;
        }

        if ('1' === $value) {
            return true;
        }

        // Don't return explicit true or false for empty fields or values like 'notify'.
        return wc_clean($value);
    }


    /**
     * Parse an int value field
     *
     * @param int $value field value.
     *
     * @return int
     */
    public function wt_parse_int_field($value) {
        // Remove the ' prepended to fields that start with - if needed.

        return intval($value);
    }

    /**
     * Parse the published field. 1 is published, 0 is private, -1 is draft.
     * Alternatively, 'true' can be used for published and 'false' for draft.
     *
     * @param string $value Field value.
     *
     * @return float|string
     */
    public function wt_parse_status_field($value) {
        
        if (!empty($value)) {
			
            $shop_order_status = $this->wc_get_order_statuses_neat();

            $found_status = false;

            foreach ($shop_order_status as $status_slug => $status_name) {
                if (0 == strcasecmp($status_slug, $value))
                    $found_status = true;
            }

            if ($found_status) {
                return $value;
            }else{
                throw new Exception(sprintf('Skipped. Unknown order status (%s).', $value));    
            }
        }

    }
    
    private function wc_get_order_statuses_neat() {
        $order_statuses = array();
        foreach (wc_get_order_statuses() as $slug => $name) {
            $order_statuses[preg_replace('/^wc-/', '', $slug)] = $name;
        }
        return $order_statuses;
    }

    public function get_default_data() {

        return array(
            // Abstract order props.
            'parent_id' => 0,
            'status' => '',
            'currency' => '',
            'version' => '',
            'prices_include_tax' => false,
            'date_created' => null,
            'date_modified' => null,
            'discount_total' => 0,
            'discount_tax' => 0,
            'shipping_total' => 0,
            'shipping_tax' => 0,
            'cart_tax' => 0,
            'total' => 0,
            'total_tax' => 0,
            // Order props.
            'customer_id' => null,
            'order_key' => '',
            'billing' => array(
                'first_name' => '',
                'last_name' => '',
                'company' => '',
                'address_1' => '',
                'address_2' => '',
                'city' => '',
                'state' => '',
                'postcode' => '',
                'country' => '',
                'email' => '',
                'phone' => '',
            ),
            'shipping' => array(
                'first_name' => '',
                'last_name' => '',
                'company' => '',
                'address_1' => '',
                'address_2' => '',
                'city' => '',
                'state' => '',
                'postcode' => '',
                'country' => '',
            ),
            'payment_method' => '',
            'payment_method_title' => '',
            'transaction_id' => '',
            'customer_ip_address' => '',
            'customer_user_agent' => '',
            'created_via' => '',
            'customer_note' => '',
            'date_completed' => null,
            'date_paid' => null,
            'cart_hash' => '',
        );
    }

    public function process_item($data) { 

        try {                        
            global $wpdb;
            do_action('wt_woocommerce_order_import_before_process_item', $data);
            $data = apply_filters('wt_woocommerce_order_import_process_item', $data); 
             
            $post_id = $data['order_id'];

            $status = !empty($data['status']) ? $data['status'] : 'wc-pending';
            
                       
            // wc_create_order();  woocommerce/includes/wc-core-function.php:83 -> $order = new WC_Order(); woocommerce/includes/class-wc-order.php:16 -> $order->save(); 
            // woocommerce/includes/class-wc-order.php:218 -> parent::save();  woocommerce/includes/abstracts/abstract_wc_order.php:168 -> $this->data_store->create( $this );  woocommerce/includes/data-store/abstract-wc-order-data-store-cpt.php:58

            add_action( 'woocommerce_email', array($this, 'wt_iew_order_import_unhook_woocommerce_email') );  // disabled all order related email sending. Need to implimet a way to send status change email based on $this->status_mail flag

            
            remove_all_actions('woocommerce_order_status_refunded_notification');
            remove_all_actions('woocommerce_order_partially_refunded_notification');
            remove_action('woocommerce_order_status_refunded', array('WC_Emails', 'send_transactional_email'));
            remove_action('woocommerce_order_partially_refunded', array('WC_Emails', 'send_transactional_email'));
            remove_action('woocommerce_order_fully_refunded', array('WC_Emails', 'send_transactional_email'));

            
            $order = wc_create_order($data);            
            if (is_wp_error($order)) {
                return $order;
            }
            
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->parent_module->module_base, 'import', "Found order object. ID:".$order->get_id());            
            $default_args = array(
		'status',
		'customer_id',
		'customer_note',
		'parent',
		'created_via',
		'cart_hash',
		'order_id',
                'shipping_items',
                'fee_items',
                'tax_items',
                'coupon_items',
                'refund_items',
                'order_items',
                'meta_data',
            );
            
            $order->set_props(array_diff_key($data, array_flip($default_args)));

            if ($order->get_id()) {
                $order_id = $order->get_id();
            }

            if(isset($data['billing']))
            $order->set_address($data['billing'], 'billing');
            if(isset($data['shipping']))
            $order->set_address($data['shipping'], 'shipping');  
            
            $order->set_prices_include_tax( 'yes' === get_option( 'woocommerce_prices_include_tax' ) );
            
            $order->add_meta_data( '_order_key', apply_filters('woocommerce_generate_order_key', uniqid('order_')) );

            
            // handle order items
            $order_items = array();
            $order_item_meta = null;
            if ($this->merge && $this->is_order_exist && !empty($data['order_items'])) {
                $wpdb->query($wpdb->prepare("DELETE items,itemmeta FROM {$wpdb->prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}woocommerce_order_items items ON itemmeta.order_item_id = items.order_item_id WHERE items.order_id = %d and items.order_item_type = 'line_item'", $order_id));
            }
            if ($this->merge && $this->is_order_exist && !empty($data['order_shipping'])) {
                $wpdb->query($wpdb->prepare("DELETE items,itemmeta FROM {$wpdb->prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}woocommerce_order_items items ON itemmeta.order_item_id = items.order_item_id WHERE items.order_id = %d and items.order_item_type = 'shipping'", $order_id));
            }
            
            $_order_item_meta = array();
            if(!empty($data['order_items'])){
                foreach ($data['order_items'] as $item) {
                    if(empty($item))
                        continue; // special case need to rewrite this concept.  empty array returning from wt_parse_line_item_field
                    $product = null;
                    $variation_item_meta = array();
                    $product_title = __('Unknown Product');
                    if ($item['product_id']) {
                        $product = wc_get_product($item['product_id']);
                        if($product){
                            $product_title = ($product->get_title()!='') ? $product->get_title() :__('Unknown Product') ;
                        }
                        // handle variations
                        if ($product && ( $product->is_type('variable') || $product->is_type('variation') || $product->is_type('subscription_variation') ) && method_exists($product, 'get_variation_id')) {
                            foreach ($product->get_variation_attributes() as $key => $value) {
                                $variation_item_meta[] = array('meta_name' => esc_attr(substr($key, 10)), 'meta_value' => $value);  // remove the leading 'attribute_' from the name to get 'pa_color' for instance
                            }                        
                        }
                    }
                    // order item
                    $order_items[] = array(
                        'order_item_name' => !empty($item['product_name']) ? $item['product_name'] : ($product_title),
                        'order_item_type' => 'line_item',
                    );
                    $var_id = 0;
                    if ($product) {
                        if (WC()->version < '2.7.0') {
                            $var_id = ($product->product_type === 'variation') ? $product->variation_id : 0;
                        } else {
                            $var_id = $product->is_type('variation') ? $product->get_id() : 0;
                        }
                    }
                    // standard order item meta
                    $_order_item_meta = array(
                        '_qty' => (int) $item['qty'],
                        '_tax_class' => '', // Tax class (adjusted by filters)
                        '_product_id' => $item['product_id'],
                        '_variation_id' => $var_id,
                        '_line_subtotal' => number_format((float) $item['sub_total'], 2, '.', ''), // Line subtotal (before discounts)
                        '_line_subtotal_tax' => number_format((float) $item['tax'], 2, '.', ''), // Line tax (before discounts)
                        '_line_total' => number_format((float) $item['total'], 2, '.', ''), // Line total (after discounts)
                        '_line_tax' => number_format((float) $item['tax'], 2, '.', ''), // Line Tax (after discounts)
                    );
                    if(!empty($item['tax_data'])){
                        $_order_item_meta['_line_tax_data'] = $item['tax_data'];
                    }
                    // add any product variation meta
                    foreach ($variation_item_meta as $meta) {
                        $_order_item_meta[$meta['meta_name']] = $meta['meta_value'];
                    }
                    // include any arbitrary order item meta
                    $_order_item_meta = array_merge($_order_item_meta, $item['meta']);
                    $order_item_meta[] = $_order_item_meta;
                }

                foreach ($order_items as $key => $order_item) {
                    $order_item_id = wc_add_order_item($order_id, $order_item);
                    if ($order_item_id) {
                        foreach ($order_item_meta[$key] as $meta_key => $meta_value) {
                            wc_add_order_item_meta($order_item_id, $meta_key, maybe_unserialize($meta_value));
                        }
                    }
                } 
            }

            $shipping_tax = isset($data['shipping_tax_total'])?$data['shipping_tax_total']:0;
            // create the shipping order items
            if (!empty($data['order_shipping'])) {
                foreach ($data['order_shipping'] as $order_shipping) {                    
                    if(empty($order_shipping)) 
                        continue; // special case need to rewrite this concept.  empty array returning from wt_parse_order_shipping_field
                    
                    $shipping_order_item = array(
                        'order_item_name' => ($order_shipping['title']) ? $order_shipping['title'] : $data['shipping_method'],
                        'order_item_type' => 'shipping',
                    );
                    $shipping_order_item_id = wc_add_order_item($order_id, $shipping_order_item);
                    if ($shipping_order_item_id) {
                        wc_add_order_item_meta($shipping_order_item_id, 'cost', $order_shipping['cost']);
                        wc_add_order_item_meta($shipping_order_item_id, 'total_tax', $shipping_tax);
                    }
                }
            }

            if (!empty($data['shipping_items'])) {
                foreach ($data['shipping_items'] as $key => $value) {
                    if ($shipping_order_item_id) {
                        wc_add_order_item_meta($shipping_order_item_id, $key, $value);
                    } else {
                        $shipping_order_item_id = wc_add_order_item($order_id, $shipping_order_item);
                        wc_add_order_item_meta($shipping_order_item_id, $key, $value);
                    }
                }
            }

            // create the fee order items
            if (!empty($data['fee_items'])) {
                if ($this->merge && $this->is_order_exist) {
                    $fee_str = 'fee';
                    $wpdb->query($wpdb->prepare("DELETE items,itemmeta FROM {$wpdb->prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}woocommerce_order_items items WHERE itemmeta.order_item_id = items.order_item_id and items.order_id = %d and items.order_item_type = %s", $order_id, $fee_str));
                }
                foreach ($data['fee_items'] as $key => $fee_item) {
                    $fee_order_item = array(
                        'order_item_name' => $fee_item['name'],
                        'order_item_type' => "fee"
                    );
                    $fee_order_item_id = wc_add_order_item($order_id, $fee_order_item);
                    if ($fee_order_item_id) {
                        wc_add_order_item_meta($fee_order_item_id, '_line_tax', $fee_item['tax']);
                        wc_add_order_item_meta($fee_order_item_id, '_line_total', $fee_item['total']);
                        wc_add_order_item_meta($fee_order_item_id, '_fee_amount', $fee_item['total']);
                        wc_add_order_item_meta($fee_order_item_id, '_line_tax_data', $fee_item['tax_data']);
                    }
                }
            }
            // create the tax order items
            if (!empty($data['tax_items'])) {
                if ($this->merge && $this->is_order_exist) {
                    $tax_str = 'tax';
                    $wpdb->query($wpdb->prepare("DELETE items,itemmeta FROM {$wpdb->prefix}woocommerce_order_itemmeta itemmeta INNER JOIN {$wpdb->prefix}woocommerce_order_items items WHERE itemmeta.order_item_id = items.order_item_id and items.order_id = %d and items.order_item_type = %s", $order_id, $tax_str));
                }
                foreach ($data['tax_items'] as $tax_item) {
                    $tax_order_item = array(
                        'order_item_name' => $tax_item['title'],
                        'order_item_type' => "tax",
                    );
                    $tax_order_item_id = wc_add_order_item($order_id, $tax_order_item);
                    if ($tax_order_item_id) {
                        wc_add_order_item_meta($tax_order_item_id, 'rate_id', $tax_item['rate_id']);
                        wc_add_order_item_meta($tax_order_item_id, 'label', $tax_item['label']);
                        wc_add_order_item_meta($tax_order_item_id, 'compound', $tax_item['compound']);
                        wc_add_order_item_meta($tax_order_item_id, 'tax_amount', $tax_item['tax_amount']);
                        wc_add_order_item_meta($tax_order_item_id, 'shipping_tax_amount', $tax_item['shipping_tax_amount']);
                        wc_add_order_item_meta($tax_order_item_id, 'rate_percent', $tax_item['rate_percent']);
                    }
                }
            }
            
            //importing coupon items
            if (!empty($data['coupon_items'])) {

                if (is_woocommerce_prior_to_basic('2.7')) {

                    if ($this->merge && $this->is_order_exist) {
                        $applied_coupons = $order->get_coupon_codes();
                        if (!empty($applied_coupons)) {
                            $order->remove_order_items('coupon');
                        }
                    }

                    $coupon_item = array();
                    foreach ($data['coupon_items'] as $coupon) {

                        $_citem_meta = explode('|', $coupon);
                        $coupon_code = array_shift($_citem_meta);
                        $coupon_code = substr($coupon_code, strpos($coupon_code, ":") + 1);

                        $discount_amount = array_shift($_citem_meta);
                        $discount_amount = substr($discount_amount, strpos($discount_amount, ":") + 1);

                        $mypost = get_page_by_title($coupon_code, '', 'shop_coupon');
                        $id = (isset($mypost->ID) ? $mypost->ID : '');
  
                        if ($id && $this->merge && $this->is_order_exist) {
                            $order->add_coupon($coupon_code, $discount_amount);
                        } else {
                            $coupon_item['order_item_name'] = $coupon_code;
                            $coupon_item['order_item_type'] = 'coupon';
                            $order_item_id = wc_add_order_item($order_id, $coupon_item);
                            wc_add_order_item_meta($order_item_id, 'discount_amount', $discount_amount);
                        }
                    }
                } else {

                    if ($this->merge && $this->is_order_exist) {
                        $applied_coupons = $order->get_used_coupons();
                        if (!empty($applied_coupons)) {
                            foreach ($applied_coupons as $coupon) {
                                $order->remove_coupon($coupon);
                            }
                        }
                    }
                    $coupon_item = array();
                    foreach ($data['coupon_items'] as $coupon) {
                        $_citem_meta = explode('|', $coupon);
                        $coupon_code = array_shift($_citem_meta);
                        $coupon_code = substr($coupon_code, strpos($coupon_code, ":") + 1);
                        $discount_amount = array_shift($_citem_meta);
                        $discount_amount = substr($discount_amount, strpos($discount_amount, ":") + 1);

                        $id = wc_get_coupon_id_by_code($coupon_code);

                        if ($id && $this->merge && $this->is_order_exist) {
                            $order->apply_coupon($coupon_code);
                        } else {
                            $coupon_item['order_item_name'] = $coupon_code;
                            $coupon_item['order_item_type'] = 'coupon';
                            $order_item_id = wc_add_order_item($order_id, $coupon_item);
                            wc_add_order_item_meta($order_item_id, 'discount_amount', $discount_amount);
                        }
                    }
                }
            }

            // importing refund items
            if (!empty($data['refund_items'])) {
                if ($this->merge && $this->is_order_exist) {
                    $refund = 'shop_order_refund';
                    $wpdb->query($wpdb->prepare("DELETE po,pm FROM $wpdb->posts AS po INNER JOIN $wpdb->postmeta AS pm ON po.ID = pm.post_id WHERE post_parent = %d and post_type = %s", $order_id, $refund));
                }
                foreach ($data['refund_items'] as $refund) {
                    $single_refund = explode('|', $refund);
                    $amount = array_shift($single_refund);
                    $amount = substr($amount, strpos($amount, ":") + 1);
                    $reason = array_shift($single_refund);
                    $reason = substr($reason, strpos($reason, ":") + 1);
                    $date = array_shift($single_refund);
                    $date = substr($date, strpos($date, ":") + 1);

                    $args = array(
                        'amount' => $amount,
                        'reason' => $reason,
                        'date_created' => $date,
                        'order_id' => $order_id,
                    );
					$input_currency = isset($data['currency']) ? $data['currency'] : $order->get_currency();
                    remove_all_actions('woocommerce_order_status_refunded_notification');
                    remove_all_actions('woocommerce_order_partially_refunded_notification');
                    remove_action('woocommerce_order_status_refunded', array('WC_Emails', 'send_transactional_email'));
                    remove_action('woocommerce_order_partially_refunded', array('WC_Emails', 'send_transactional_email'));
                    remove_action('woocommerce_order_fully_refunded', array('WC_Emails', 'send_transactional_email'));
                    $this->wt_create_refund($input_currency, $args);
                }
            }
            
            // add order notes
            if(!empty($data['order_notes'])){
                add_filter('woocommerce_email_enabled_customer_note', '__return_false');
                    $wpdb->query($wpdb->prepare("DELETE comments,meta FROM {$wpdb->prefix}comments comments LEFT JOIN {$wpdb->prefix}commentmeta meta ON comments.comment_ID = meta.comment_id WHERE comments.comment_post_ID = %d",$order_id));
                foreach ($data['order_notes'] as $order_note) {
                    $note = explode('|', $order_note);
                    $con = array_shift($note);
                    $con = substr($con, strpos($con, ":") + 1);
                    $date = array_shift($note);
                    $date = substr($date, strpos($date, ":") + 1);
                    $cus = array_shift($note);
                    $cus = substr($cus, strpos($cus, ":") + 1);
                    $system = array_shift($note);
                    $added_by = substr($system, strpos($system, ":") + 1);
                    if($added_by == 'system'){
                        $added_by_user = FALSE;
                    }else{
                        $added_by_user = TRUE;
                    }
                    if($cus == '1'){
                        $comment_id = $order->add_order_note($con,1,1);
                    } else {
                        $comment_id = $order->add_order_note($con,0,$added_by_user);
                    }
                    wp_update_comment(array('comment_ID' => $comment_id,'comment_date' => $date));
                }
            }

                        
            $this->set_meta_data($order, $data);  
            
            if(isset($data['order_number']))
            update_post_meta($order_id, '_order_number', $data['order_number']);           
            // was an original order number provided?
            if (!empty($data['order_number_formatted'])) {                
                //Provide custom order number functionality , also allow 3rd party plugins to provide their own custom order number facilities
                do_action('woocommerce_set_order_number', $order, $data['order_number'], $data['order_number_formatted']);
                $order->add_order_note(sprintf(__("Original order #%s", 'wf_order_import_export'), $data['order_number_formatted']));                
            }
      
            if($this->status_mail == true){   
                $order->update_status('wc-' . preg_replace('/^wc-/', '', $status)); 
            } else {    
                $update_post = array(
                    'ID' => $order_id,
                    'post_status' => 'wc-' . preg_replace('/^wc-/', '', $status),
                );
                wp_update_post($update_post);
            }
            
               
            if($this->delete_existing){
                update_post_meta($order_id, '_wt_delete_existing', 1);
            }
                        
            
            $order = apply_filters('wt_woocommerce_import_pre_insert_order_object', $order, $data);  
            
            
            
            $order->save();
                                    
            do_action('wt_woocommerce_order_import_inserted_object', $order, $data);
            
            $result = array(
                'id' => $order->get_id(),
                'updated' => $this->merge,
            );            
            return $result;
        } catch (Exception $e) {
            return new WP_Error('woocommerce_product_importer_error', $e->getMessage(), array('status' => $e->getCode()));
        }
    }
    
	
	
	
	
	function wt_create_refund( $input_currency, $args = array() ) {
		
                    
            
	$default_args = array(
		'amount'         => 0,
		'reason'         => null,
		'order_id'       => 0,
		'refund_id'      => 0,
		'line_items'     => array(),
		'refund_payment' => false,
		'restock_items'  => false,
	);

	try {
		$args  = wp_parse_args( $args, $default_args );
		$order = wc_get_order( $args['order_id'] );

		if ( ! $order ) {
			throw new Exception( __( 'Invalid order ID.', 'woocommerce' ) );
		}

		$remaining_refund_amount = $order->get_remaining_refund_amount();
		$remaining_refund_items  = $order->get_remaining_refund_items();
		$refund_item_count       = 0;
		$refund                  = new WC_Order_Refund( $args['refund_id'] );

		$refund->set_currency( $input_currency );
		$refund->set_amount( $args['amount'] );
		$refund->set_parent_id( absint( $args['order_id'] ) );
		$refund->set_refunded_by( get_current_user_id() ? get_current_user_id() : 1 );
		$refund->set_prices_include_tax( $order->get_prices_include_tax() );

		if ( ! is_null( $args['reason'] ) ) {
			$refund->set_reason( $args['reason'] );
		}

		
		// Negative line items.
		if ( count( $args['line_items'] ) > 0 ) {
			$items = $order->get_items( array( 'line_item', 'fee', 'shipping' ) );

			foreach ( $items as $item_id => $item ) {
				if ( ! isset( $args['line_items'][ $item_id ] ) ) {
					continue;
				}

				$qty          = isset( $args['line_items'][ $item_id ]['qty'] ) ? $args['line_items'][ $item_id ]['qty'] : 0;
				$refund_total = $args['line_items'][ $item_id ]['refund_total'];
				$refund_tax   = isset( $args['line_items'][ $item_id ]['refund_tax'] ) ? array_filter( (array) $args['line_items'][ $item_id ]['refund_tax'] ) : array();

				if ( empty( $qty ) && empty( $refund_total ) && empty( $args['line_items'][ $item_id ]['refund_tax'] ) ) {
					continue;
				}

				$class         = get_class( $item );
				$refunded_item = new $class( $item );
				$refunded_item->set_id( 0 );
				$refunded_item->add_meta_data( '_refunded_item_id', $item_id, true );
				$refunded_item->set_total( wc_format_refund_total( $refund_total ) );
				$refunded_item->set_taxes(
					array(
						'total'    => array_map( 'wc_format_refund_total', $refund_tax ),
						'subtotal' => array_map( 'wc_format_refund_total', $refund_tax ),
					)
				);

				if ( is_callable( array( $refunded_item, 'set_subtotal' ) ) ) {
					$refunded_item->set_subtotal( wc_format_refund_total( $refund_total ) );
				}

				if ( is_callable( array( $refunded_item, 'set_quantity' ) ) ) {
					$refunded_item->set_quantity( $qty * -1 );
				}

				$refund->add_item( $refunded_item );
				$refund_item_count += $qty;
			}
		}

		$refund->update_taxes();
		$refund->calculate_totals( false );
		$refund->set_total( $args['amount'] * -1 );

		// this should remain after update_taxes(), as this will save the order, and write the current date to the db
		// so we must wait until the order is persisted to set the date.
		if ( isset( $args['date_created'] ) ) {
			$refund->set_date_created( $args['date_created'] );
		}

		/**
		 * Action hook to adjust refund before save.
		 *
		 * @since 3.0.0
		 */
		do_action( 'woocommerce_create_refund', $refund, $args );

                add_action( 'woocommerce_email', array($this, 'wt_iew_order_import_unhook_woocommerce_email') );    
                
		if ( $refund->save() ) {
			if ( $args['refund_payment'] ) {
				$result = wc_refund_payment( $order, $refund->get_amount(), $refund->get_reason() );

				if ( is_wp_error( $result ) ) {
					$refund->delete();
					return $result;
				}

				$refund->set_refunded_payment( true );
				$refund->save();
			}

			if ( $args['restock_items'] ) {
				wc_restock_refunded_items( $order, $args['line_items'] );
			}

			// Trigger notification emails.
			if ( ( $remaining_refund_amount - $args['amount'] ) > 0 || ( $order->has_free_item() && ( $remaining_refund_items - $refund_item_count ) > 0 ) ) {
				//do_action( 'woocommerce_order_partially_refunded', $order->get_id(), $refund->get_id() );
			} else {
				//do_action( 'woocommerce_order_fully_refunded', $order->get_id(), $refund->get_id() );

				$parent_status = apply_filters( 'woocommerce_order_fully_refunded_status', 'refunded', $order->get_id(), $refund->get_id() );

				if ( $parent_status ) {
					$order->update_status( $parent_status );
				}
			}
		}

		do_action( 'woocommerce_refund_created', $refund->get_id(), $args );
		do_action( 'woocommerce_order_refunded', $order->get_id(), $refund->get_id() );

	} catch ( Exception $e ) {
		if ( isset( $refund ) && is_a( $refund, 'WC_Order_Refund' ) ) {
			wp_delete_post( $refund->get_id(), true );
		}
		return new WP_Error( 'error', $e->getMessage() );
	}

	return $refund;
}
	
    function set_meta_data(&$object, $data) {
        if (isset($data['meta_data'])) {
            $order_id = $object->get_id();
            $add_download_permissions = false;
            foreach ($data['meta_data'] as $meta) {                                                
                if (( 'Download Permissions Granted' == $meta['key'] || '_download_permissions_granted' == $meta['key'] ) && $meta['value']) {
                    $add_download_permissions = true;
                }
                
                if ('wf_invoice_number' ==  $meta['key']) {
                    update_post_meta($order_id, 'wf_invoice_number',$meta['value']);
                    continue;
                }
                if ('_wf_invoice_date' == $meta['key'] ) {
                    update_post_meta($order_id, '_wf_invoice_date',$meta['value']);
                    continue;
                }     
                
                if('_wt_import_key' == $meta['key']){
                    $object->update_meta_data('_wt_import_key', apply_filters('wt_importing_order_reference_key', $meta['value'], $data)); // for future reference, this holds the order number which in the csv.
                    continue;
                }
                
                if ( is_serialized( $meta['value'] ) ) { // Don't attempt to unserialize data that wasn't serialized going in.
                    $meta['value'] = maybe_unserialize(maybe_unserialize($meta['value']));
                }
                
                $object->update_meta_data($meta['key'], $meta['value']);
            }

            // Grant downloadalbe product permissions
            if ($add_download_permissions) {
                $force = apply_filters('wt_force_update_downloadalbe_product_permissions', true);
                wc_downloadable_product_permissions($order_id, $force);

            }
            
        }

    }

    function wt_iew_order_import_unhook_woocommerce_email( $email_class ) {

            // New order emails
            remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_failed_to_pending_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_cancelled_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_cancelled_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            
            // Processing  emails
            remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_on-hold_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );         
            remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );         
            remove_action( 'woocommerce_order_status_cancelled_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );         
            
            // On-hold emails
            remove_action( 'woocommerce_order_status_cancelled_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_cancelled_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_On_Hold_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_On_Hold_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_On_Hold_Order'], 'trigger' ) );                        
            remove_action( 'woocommerce_order_status_processing_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_On_Hold_Order'], 'trigger' ) );
                       
            // Cancelled emails
            remove_action( 'woocommerce_order_status_on-hold_to_cancelled_notification', array( $email_class->emails['WC_Email_Cancelled_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_processing_to_cancelled_notification', array( $email_class->emails['WC_Email_Cancelled_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_failed_to_cancelled_notification', array( $email_class->emails['WC_Email_Cancelled_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_completed_to_cancelled_notification', array( $email_class->emails['WC_Email_Cancelled_Order'], 'trigger' ) );
            
            
            // Completed  emails
            remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_processing_to_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_refunded_to_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_cancelled_to_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
            
            // Refund mails
            remove_action( 'woocommerce_order_status_completed_to_refunded_notification', array( $email_class->emails['WC_Email_Customer_Refunded_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_processing_to_refunded_notification', array( $email_class->emails['WC_Email_Customer_Refunded_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_refunded', array( $email_class->emails['WC_Email_Customer_Refunded_Order'], 'trigger' ) );
            
            // Failed emails
            remove_action( 'woocommerce_order_status_on-hold_to_failed_notification', array( $email_class->emails['WC_Email_Failed_Order'], 'trigger' ) );
            remove_action( 'woocommerce_order_status_pending_to_failed_notification', array( $email_class->emails['WC_Email_Failed_Order'], 'trigger' ) );
        
    }
    
    function wt_iew_order_import_hook_woocommerce_email( $email_class ) {                  
        
            // New order emails
            add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            add_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            add_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
            add_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

            // Processing order emails
            add_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
            add_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

            // Completed order emails
            add_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
        
    }
}
}

