<?php

if (!defined('WPINC')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_Basic_Order_Export')){
class Wt_Import_Export_For_Woo_Basic_Order_Export {

    public $parent_module = null;
    private $line_items_max_count = 0;
    private $export_to_separate_columns = false;
    private $export_to_separate_rows = false;     
    private $line_item_meta;
    private $is_wt_invoice_active = false;
    private $is_yith_tracking_active = false;

    public function __construct($parent_object) {

        $this->parent_module = $parent_object;                
    }

    public function prepare_header() {

        $export_columns = $this->parent_module->get_selected_column_names();

        $this->line_item_meta = self::get_all_line_item_metakeys();
        if (is_plugin_active('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php')):
            $this->is_wt_invoice_active = true;
        endif;
        if (is_plugin_active('yith-woocommerce-order-tracking-premium/init.php')):
            $this->is_yith_tracking_active = true;
        endif;        
		
        $max_line_items = $this->line_items_max_count;

        for ($i = 1; $i <= $max_line_items; $i++) {
            $export_columns["line_item_{$i}"] = "line_item_{$i}";
        }      

        if ($this->export_to_separate_columns) {
            for ($i = 1; $i <= $max_line_items; $i++) {
                foreach ($this->line_item_meta as $meta_value) {
                    $new_val = str_replace("_", " ", $meta_value);
                    $export_columns["line_item_{$i}_name"] = "Product Item {$i} Name";
                    $export_columns["line_item_{$i}_product_id"] = "Product Item {$i} id";
                    $export_columns["line_item_{$i}_sku"] = "Product Item {$i} SKU";
                    $export_columns["line_item_{$i}_quantity"] = "Product Item {$i} Quantity";
                    $export_columns["line_item_{$i}_total"] = "Product Item {$i} Total";
                    $export_columns["line_item_{$i}_subtotal"] = "Product Item {$i} Subtotal";
                    if (in_array($meta_value, array("_product_id", "_qty", "_variation_id", "_line_total", "_line_subtotal", "_tax_class", "_line_tax", "_line_tax_data", "_line_subtotal_tax"))) {
                        continue;
                    } else {
                        $export_columns["line_item_{$i}_$meta_value"] = "Product Item {$i} $new_val";
                    }
                }
            }
        }
        if ($this->export_to_separate_rows) {
            $export_columns = $this->wt_line_item_separate_row_csv_header($export_columns);
        }
        return apply_filters('hf_alter_csv_header', $export_columns);
    }

    
        public function wt_line_item_separate_row_csv_header($export_columns) {


        foreach ($export_columns as $s_key => $value) {
            if (strstr($s_key, 'line_item_')) {
                unset($export_columns[$s_key]);
            }
        }

        $export_columns["line_item_product_id"] = "item_product_id";
        $export_columns["line_item_name"] = "item_name";
        $export_columns["line_item_sku"] = "item_sku";
        $export_columns["line_item_quantity"] = "item_quantity";
        $export_columns["line_item_subtotal"] = "item_subtotal";
        $export_columns["line_item_subtotal_tax"] = "item_subtotal_tax";
        $export_columns["line_item_total"] = "item_total";
        $export_columns["line_item_total_tax"] = "item_total_tax";
        $export_columns["item_refunded"] = "item_refunded";
        $export_columns["item_refunded_qty"] = "item_refunded_qty";
        $export_columns["item_meta"] = "item_meta";
        return $export_columns;
    }
    
    public function wt_line_item_separate_row_csv_data($order_export_data, $order_data_filter_args) {

        $order_id = $order_export_data['order_id'];
        $order = wc_get_order($order_id);
        $row = array();
        if ($order) {
            foreach ($order->get_items() as $item_key => $item) {
                foreach ($order_export_data as $key => $value) {
                    if (strpos($key, 'line_item_') !== false) {
                        continue;
                    } else {
                        $data1[$key] = $value;
                    }
                }
                $item_data = $item->get_data();
                $product = $item->get_product();

                $data1["line_item_product_id"] = !empty($item_data['product_id']) ? $item_data['product_id'] : '';
                $data1["line_item_name"] = !empty($item_data['name']) ? $item_data['name'] : '';
                $data1["line_item_sku"] = !empty($product) ? $product->get_sku() : '';
                $data1["line_item_quantity"] = !empty($item_data['quantity']) ? $item_data['quantity'] : '';
                $data1["line_item_subtotal"] = !empty($item_data['subtotal']) ? $item_data['subtotal'] : 0;
                $data1["line_item_subtotal_tax"] = !empty($item_data['subtotal_tax']) ? $item_data['subtotal_tax'] : 0;
                $data1["line_item_total"] = !empty($item_data['total']) ? $item_data['total'] : 0;
                $data1["line_item_total_tax"] = !empty($item_data['total_tax']) ? $item_data['total_tax'] : 0;

                $data1["item_refunded"] = !empty($order->get_total_refunded_for_item($item_key)) ? $order->get_total_refunded_for_item($item_key) : '';
                $data1["item_refunded_qty"] = !empty($order->get_qty_refunded_for_item($item_key)) ? absint($order->get_qty_refunded_for_item($item_key)) : '';
                $data1["item_meta"] = !empty($item_data['meta_data']) ? json_encode($item_data['meta_data']) : '';


                $row[] = $data1;

            }
           return $row;
        }
   
    }
        
    public function wt_ier_alter_order_data_before_export_for_separate_row($data_array) {
        $new_data_array = array();
        foreach ($data_array as $key => $avalue) {
            if (is_array($avalue)) {
                if (count($avalue) == 1) {
                    $new_data_array[] = $avalue[0];
                } elseif (count($avalue) > 1) {
                    foreach ($avalue as $arrkey => $arrvalue) {
                        $new_data_array[] = $arrvalue;
                    }
                }
            }
        }
        return $new_data_array;
    }
    
    /**
     * Prepare data that will be exported.
     */
    public function prepare_data_to_export($form_data, $batch_offset) {
        
        $export_order_statuses = !empty($form_data['filter_form_data']['wt_iew_order_status']) ? $form_data['filter_form_data']['wt_iew_order_status'] : 'any';
        $products = !empty($form_data['filter_form_data']['wt_iew_products']) ? $form_data['filter_form_data']['wt_iew_products'] : '';
        $email = !empty($form_data['filter_form_data']['wt_iew_email']) ? $form_data['filter_form_data']['wt_iew_email'] : array(); // user email fields return user ids
        $start_date = !empty($form_data['filter_form_data']['wt_iew_date_from']) ? $form_data['filter_form_data']['wt_iew_date_from'] . ' 00:00:00' : date('Y-m-d 00:00:00', 0);
        $end_date = !empty($form_data['filter_form_data']['wt_iew_date_to']) ? $form_data['filter_form_data']['wt_iew_date_to'] . ' 23:59:59.99' : date('Y-m-d 23:59:59.99', current_time('timestamp'));        
        $coupons = !empty($form_data['filter_form_data']['wt_iew_coupons']) ? array_filter(explode(',', strtolower($form_data['filter_form_data']['wt_iew_coupons'])),'trim') : array();
        $orders = !empty($form_data['filter_form_data']['wt_iew_orders']) ? array_filter(explode(',', strtolower($form_data['filter_form_data']['wt_iew_orders'])),'trim') : array();

        $export_limit = !empty($form_data['filter_form_data']['wt_iew_limit']) ? intval($form_data['filter_form_data']['wt_iew_limit']) : 999999999; //user limit
        $current_offset = !empty($form_data['filter_form_data']['wt_iew_offset']) ? intval($form_data['filter_form_data']['wt_iew_offset']) : 0; //user offset
        $export_offset = $current_offset;
        $batch_count = !empty($form_data['advanced_form_data']['wt_iew_batch_count']) ? $form_data['advanced_form_data']['wt_iew_batch_count'] : Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings('default_export_batch');

        $exclude_already_exported = (!empty($form_data['advanced_form_data']['wt_iew_exclude_already_exported']) && $form_data['advanced_form_data']['wt_iew_exclude_already_exported'] == 'Yes') ? true : false;
                       
        $this->export_to_separate_columns = (!empty($form_data['advanced_form_data']['wt_iew_export_to_separate']) && $form_data['advanced_form_data']['wt_iew_export_to_separate'] == 'column') ? true : false;                       
        $this->export_to_separate_rows = (!empty($form_data['advanced_form_data']['wt_iew_export_to_separate']) && $form_data['advanced_form_data']['wt_iew_export_to_separate'] == 'row') ? true : false;               

        
        $real_offset = ($current_offset + $batch_offset);

        if($batch_count<=$export_limit)
        {
            if(($batch_offset+$batch_count)>$export_limit) //last offset
            {
                $limit=$export_limit-$batch_offset;
            }else
            {
                $limit=$batch_count;
            }
        }else
        {
            $limit=$export_limit;
        }

        $data_array = array();
        if ($batch_offset < $export_limit)
        {
                        
            /**
            *   taking total records
            */
            $order_ids =0;
            $total_records=0;
            if($batch_offset==0) //first batch
            {
                if (!empty($email) && empty($products) && empty($coupons)) {

                    $args = array(
                        'customer_id' => $email,
                        'paginate' => true,
                        'return' => 'ids',
                        'limit' => $export_limit, //user given limit,
                        'offset' => $current_offset, //user given offset,
                    );                                        

                    if ($exclude_already_exported) {
                        $args['wt_meta_query'][] = (array(
                            'key' => 'wf_order_exported_status',
                            'value' => FALSE,
                            'compare' => 'NOT EXISTS',
                        ));
                    }
                    
                    $ord_email = wc_get_orders($args);

                    $order_ids = $ord_email->orders;
                } elseif (!empty($products) && empty($coupons) && empty($email)) {
                    $order_ids = self::hf_get_orders_of_products($products, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);
                } elseif (!empty($coupons) && empty($products) && empty($email)) {
                    $order_ids = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);
                } elseif (!empty($coupons) && !empty($products) && empty($email)) {
                    $ord_prods = self::hf_get_orders_of_products($products, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);
                    $ord_coups = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);
                    $order_ids = array_intersect($ord_prods, $ord_coups);
                } elseif (!empty($coupons) && empty($products) && !empty($email)) {
                    $ord_coups = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);
                    $args = array(
                        'customer_id' => $email,
                    );

                    if ($exclude_already_exported) {
                        $args['wt_meta_query'][] = (array(
                            'key' => 'wf_order_exported_status',
                            'value' => FALSE,
                            'compare' => 'NOT EXISTS',
                        ));
                    }

                    $ord_email = wc_get_orders($args);
                    foreach ($ord_email as $id) {
                        $order_id[] = $id->get_id();
                    }
                    $order_ids = array_intersect($order_id, $ord_coups);
                } elseif (empty($coupons) && !empty($products) && !empty($email)) {
                    $ord_prods = self::hf_get_orders_of_products($products, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);

                    $args = array(
                        'customer_id' => $email,
                    );

                    $ord_email = wc_get_orders($args);
                    foreach ($ord_email as $id) {
                        $order_id[] = $id->get_id();
                    }

                    $order_ids = array_intersect($ord_prods, $order_id);
                } elseif (!empty($coupons) && !empty($products) && !empty($email)) {
                    $ord_prods = self::hf_get_orders_of_products($products, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);
                    $ord_coups = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $export_limit, $current_offset, $end_date, $start_date, $exclude_already_exported);

                    $args = array(
                        'customer_id' => $email,
                    );

                    if ($exclude_already_exported) {
                        $args['wt_meta_query'][] = (array(
                            'key' => 'wf_order_exported_status',
                            'value' => FALSE,
                            'compare' => 'NOT EXISTS',
                        ));
                    }

                    $ord_email = wc_get_orders($args);
                    foreach ($ord_email as $id) {
                        $order_id[] = $id->get_id();
                    }
                    $order_ids = array_intersect($ord_prods, $ord_coups, $order_id);
                } else {
                    $query_args = array(
                        'fields' => 'ids',
                        'post_type' => 'shop_order',
                        'order' => 'DESC',
                        'orderby' => 'ID',
                        'post_status' => $export_order_statuses,
                        'date_query' => array(
                            array(
                                'before' => $end_date,
                                'after' => $start_date,
                                'inclusive' => true,
                            ),
                        ),
                    );

                    if ($exclude_already_exported) {
                        $query_args['meta_query'][] = (array(
                            'key' => 'wf_order_exported_status',
                            'value' => FALSE,
                            'compare' => 'NOT EXISTS',
                        ));
                    }
                    if(!empty($orders)){
                        $query_args['post__in'] = $orders;
                    }
                    $query_args = apply_filters('wt_orderimpexpcsv_export_query_args', $query_args);
                    $query_args['offset'] = $current_offset; //user given offset
                    $query_args['posts_per_page'] = $export_limit; //user given limit

                    $query = new WP_Query($query_args);

                    $order_ids = $query->posts;
                }
                
                $total_records = count($order_ids); 
                
                $this->line_items_max_count = $this->get_max_line_items($order_ids);
                add_option('wt_order_line_items_max_count',$this->line_items_max_count);
            }
                
            if(empty($this->line_items_max_count)){
                $this->line_items_max_count = get_option('wt_order_line_items_max_count');
            }
                                
            $order_ids =0;
            if (!empty($email) && empty($products) && empty($coupons)) {

                $args = array(
                    'customer_id' => $email,
                    'paginate' => true,
                    'return' => 'ids',
                    'order' => 'DESC',
                    'orderby' => 'ID',
                    'limit' => $limit,
                    'offset' => $real_offset,
                );

                if ($exclude_already_exported) {
                    $args['wt_meta_query'][] = (array(
                        'key' => 'wf_order_exported_status',
                        'value' => FALSE,
                        'compare' => 'NOT EXISTS',
                    ));
                }

                $ord_email = wc_get_orders($args);

                $order_ids = $ord_email->orders;
            } elseif (!empty($products) && empty($coupons) && empty($email)) {
                $order_ids = self::hf_get_orders_of_products($products, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);
            } elseif (!empty($coupons) && empty($products) && empty($email)) {
                $order_ids = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);
            } elseif (!empty($coupons) && !empty($products) && empty($email)) {
                $ord_prods = self::hf_get_orders_of_products($products, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);
                $ord_coups = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);
                $order_ids = array_intersect($ord_prods, $ord_coups);
            } elseif (!empty($coupons) && empty($products) && !empty($email)) {
                $ord_coups = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);

                $args = array(
                    'customer_id' => $email,
                );

                if ($exclude_already_exported) {
                    $args['wt_meta_query'][] = (array(
                        'key' => 'wf_order_exported_status',
                        'value' => FALSE,
                        'compare' => 'NOT EXISTS',
                    ));
                }

                $ord_email = wc_get_orders($args);
                foreach ($ord_email as $id) {
                    $order_id[] = $id->get_id();
                }
                $order_ids = array_intersect($order_id, $ord_coups);
            } elseif (empty($coupons) && !empty($products) && !empty($email)) {
                $ord_prods = self::hf_get_orders_of_products($products, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);

                $args = array(
                    'customer_id' => $email,
                );

                $ord_email = wc_get_orders($args);
                foreach ($ord_email as $id) {
                    $order_id[] = $id->get_id();
                }

                $order_ids = array_intersect($ord_prods, $order_id);
            } elseif (!empty($coupons) && !empty($products) && !empty($email)) {
                $ord_prods = self::hf_get_orders_of_products($products, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);
                $ord_coups = self::hf_get_orders_of_coupons($coupons, $export_order_statuses, $limit, $real_offset, $end_date, $start_date, $exclude_already_exported);

                $args = array(
                    'customer_id' => $email,
                );

                if ($exclude_already_exported) {
                    $args['wt_meta_query'][] = (array(
                        'key' => 'wf_order_exported_status',
                        'value' => FALSE,
                        'compare' => 'NOT EXISTS',
                    ));
                }

                $ord_email = wc_get_orders($args);
                foreach ($ord_email as $id) {
                    $order_id[] = $id->get_id();
                }
                $order_ids = array_intersect($ord_prods, $ord_coups, $order_id);
            } else {
                $query_args = array(
                    'fields' => 'ids',
                    'post_type' => 'shop_order',
                    'order' => 'ASC',
                    'orderby' => 'ID',
                    'post_status' => $export_order_statuses,
                    'date_query' => array(
                        array(
                            'before' => $end_date,
                            'after' => $start_date,
                            'inclusive' => true,
                        ),
                    ),
                );

                if ($exclude_already_exported) {
                    $query_args['meta_query'][] = (array(
                        'key' => 'wf_order_exported_status',
                        'value' => FALSE,
                        'compare' => 'NOT EXISTS',
                    ));
                }
                if(!empty($orders)){
                        $query_args['post__in'] = $orders;
                }
                $query_args = apply_filters('wt_orderimpexpcsv_export_query_args', $query_args);                
                $query_args['offset'] = $real_offset;
                $query_args['posts_per_page'] = $limit;

                $query = new WP_Query($query_args);

                $order_ids = $query->posts;
            }
            
            $order_ids = apply_filters('wt_orderimpexpcsv_alter_order_ids', $order_ids);
            
            foreach ($order_ids as $order_id) {
                $data_array[] = $this->generate_row_data($order_id);
                // updating records with expoted status 
                update_post_meta($order_id, 'wf_order_exported_status', TRUE);
            }

            if($this->export_to_separate_rows){
                $data_array = $this->wt_ier_alter_order_data_before_export_for_separate_row($data_array);
            }
            
            $data_array = apply_filters('wt_ier_alter_order_data_before_export', $data_array);  
              
            
            $return['total'] = $total_records;
            $return['data'] = $data_array;
            return $return;
        }
        
       
    }

    public function generate_row_data($order_id) {

        $csv_columns = $this->prepare_header();
        
        $row = array();
        // Get an instance of the WC_Order object
        $order = wc_get_order($order_id);
        $line_items = $shipping_items = $fee_items = $tax_items = $coupon_items = $refund_items = array();

        // get line items
        foreach ($order->get_items() as $item_id => $item) {
            /* WC_Abstract_Legacy_Order::get_product_from_item() deprecated since version 4.4.0*/
            $product = (WC()->version < '4.4.0') ? $order->get_product_from_item($item) : $item->get_product();  
            if (!is_object($product)) {
                $product = new WC_Product(0);
            }
            $item_meta = self::get_order_line_item_meta($item_id);
            $prod_type = (WC()->version < '3.0.0') ? $product->product_type : $product->get_type();
            $line_item = array(
                'name' => html_entity_decode(!empty($item['name']) ? $item['name'] : $product->get_title(), ENT_NOQUOTES, 'UTF-8'),
                'product_id' => (WC()->version < '2.7.0') ? $product->id : (($prod_type == 'variable' || $prod_type == 'variation' || $prod_type == 'subscription_variation') ? $product->get_parent_id() : $product->get_id()),
                'sku' => $product->get_sku(),
                'quantity' => $item['qty'],
                'total' => wc_format_decimal($order->get_line_total($item), 2),
                'sub_total' => wc_format_decimal($order->get_line_subtotal($item), 2),
            );

            //add line item tax
            $line_tax_data = isset($item['line_tax_data']) ? $item['line_tax_data'] : array();
            $tax_data = maybe_unserialize($line_tax_data);
            $tax_detail = isset($tax_data['total']) ? wc_format_decimal(wc_round_tax_total(array_sum((array) $tax_data['total'])), 2) : '';
            if ($tax_detail != '0.00' && !empty($tax_detail)) {
                $line_item['tax'] = $tax_detail;
                $line_tax_ser = maybe_serialize($line_tax_data);
                $line_item['tax_data'] = $line_tax_ser;
            }

            foreach ($item_meta as $key => $value) {
                switch ($key) {
                    case '_qty':
                    case '_variation_id':
                    case '_product_id':
                    case '_line_total':
                    case '_line_subtotal':
                    case '_tax_class':
                    case '_line_tax':
                    case '_line_tax_data':
                    case '_line_subtotal_tax':
                        break;

                    default:
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);
                        $line_item[$key] = $value;
                        break;
                }
            }

            $refunded = wc_format_decimal($order->get_total_refunded_for_item($item_id), 2);
            if ($refunded != '0.00') {
                $line_item['refunded'] = $refunded;
            }

            if ($prod_type === 'variable' || $prod_type === 'variation' || $prod_type === 'subscription_variation') {
                $line_item['_variation_id'] = (WC()->version > '2.7') ? $product->get_id() : $product->variation_id;
            }
            $line_items[] = $line_item;
        }
        

        //shipping items is just product x qty under shipping method
        $line_items_shipping = $order->get_items('shipping');

        foreach ($line_items_shipping as $item_id => $item) {
            $item_meta = self::get_order_line_item_meta($item_id);
            foreach ($item_meta as $key => $value) {
                switch ($key) {
                    case 'Items':	
                    case 'method_id':
                    case 'taxes':
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);
                        $meta[$key] = $value;
                        break;
                }
            }
            foreach (array('Items','method_id', 'taxes') as $value) {
                if (!isset($meta[$value])) {
                    $meta[$value] = '';
                }
            }
            $shipping_items[] = trim(implode('|', array('items:' . $meta['Items'],'method_id:' . $meta['method_id'], 'taxes:' . $meta['taxes'])));
        }

        //get fee and total
        $fee_total = 0;
        $fee_tax_total = 0;

        foreach ($order->get_fees() as $fee_id => $fee) {
            $fee_items[] = implode('|', array(
                'name:' . html_entity_decode($fee['name'], ENT_NOQUOTES, 'UTF-8'),
                'total:' . wc_format_decimal($fee['line_total'], 2),
                'tax:' . wc_format_decimal($fee['line_tax'], 2),
                'tax_data:' . maybe_serialize($fee['line_tax_data'])
            ));
            $fee_total += $fee['line_total'];
            $fee_tax_total += $fee['line_tax'];
        }

        // get tax items
        foreach ($order->get_tax_totals() as $tax_code => $tax) {
            $rate_percent = wc_get_order_item_meta( $tax->id, 'rate_percent', true ) ? wc_get_order_item_meta( $tax->id, 'rate_percent', true ):'';
            $tax_items[] = implode('|', array(
                'rate_id:' . $tax->rate_id,
                'code:' . $tax_code,
                'total:' . wc_format_decimal($tax->amount, 2),
                'label:' . $tax->label,
                'tax_rate_compound:' . $tax->is_compound,
                'rate_percent:'.$rate_percent,
            ));
        }

        // add coupons
		if ( (WC()->version < '4.4.0' ) ) {
			foreach ( $order->get_items('coupon') as $_ => $coupon_item ) {
				$discount_amount = !empty( $coupon_item[ 'discount_amount' ] ) ? $coupon_item[ 'discount_amount' ] : 0;
				$coupon_items[]	 = implode( '|', array(
					'code:' . $coupon_item[ 'name' ],
					'amount:' . wc_format_decimal( $discount_amount, 2 ),
				) );
			}
		} else {
			foreach ( $order->get_coupon_codes() as $_ => $coupon_code ) {
				$coupon_obj = new WC_Coupon($coupon_code);
				$discount_amount = !empty( $coupon_obj->get_amount() ) ? $coupon_obj->get_amount() : 0;
				$coupon_items[]	 = implode( '|', array(
					'code:' . $coupon_code,
					'amount:' . wc_format_decimal( $discount_amount, 2 ),
				) );
			}
		}

        foreach ($order->get_refunds() as $refunded_items) {

            if ((WC()->version < '2.7.0')) {
                $refund_items[] = implode('|', array(
                    'amount:' . $refunded_items->get_refund_amount(),
                    'reason:' . $refunded_items->reason,
                    'date:' . date('Y-m-d H:i:s', strtotime($refunded_items->date_created)),
                ));
            } else {
                $refund_items[] = implode('|', array(
                    'amount:' . $refunded_items->get_amount(),
                    'reason:' . $refunded_items->get_reason(),
                    'date:' . date('Y-m-d H:i:s', strtotime($refunded_items->get_date_created())),
                ));
            }
        }

        if (version_compare(WC_VERSION, '2.7', '<')) {
            
            $paid_date = get_post_meta($order->id, '_date_paid');
            $order_data = array(
                'order_id' => $order->id,
                'order_number' => $order->get_order_number(),
                'order_date' => date('Y-m-d H:i:s', strtotime(get_post($order->id)->post_date)),
                'paid_date' => isset($paid_date) ? date('Y-m-d H:i:s', $paid_date) : '',
                'status' => $order->get_status(),
                'shipping_total' => $order->get_total_shipping(),
                'shipping_tax_total' => wc_format_decimal($order->get_shipping_tax(), 2),
                'fee_total' => wc_format_decimal($fee_total, 2),
                'fee_tax_total' => wc_format_decimal($fee_tax_total, 2),
                'tax_total' => wc_format_decimal($order->get_total_tax(), 2),
                'cart_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_cart_discount(), 2),
                'order_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_order_discount(), 2),
                'discount_total' => wc_format_decimal($order->get_discount_total(), 2),
                'order_total' => wc_format_decimal($order->get_total(), 2),
                'order_currency' => $order->get_order_currency(),
                'payment_method' => $order->payment_method,
                'payment_method_title' => $order->payment_method_title,
                'transaction_id' => $order->transaction_id,
                'customer_ip_address' => $order->customer_ip_address,
                'customer_user_agent' => $order->customer_user_agent, 
                'shipping_method' => $order->get_shipping_method(),
                'customer_id' => $order->get_user_id(),
                'customer_user' => $order->get_user_id(),
                'customer_email' => ($a = get_userdata($order->get_user_id())) ? $a->user_email : '',
                'billing_first_name' => $order->billing_first_name,
                'billing_last_name' => $order->billing_last_name,
                'billing_company' => $order->billing_company,
                'billing_email' => $order->billing_email,
                'billing_phone' => $order->billing_phone,
                'billing_address_1' => $order->billing_address_1,
                'billing_address_2' => $order->billing_address_2,
                'billing_postcode' => $order->billing_postcode,
                'billing_city' => $order->billing_city,
                'billing_state' => $order->billing_state,
                'billing_country' => $order->billing_country,
                'shipping_first_name' => $order->shipping_first_name,
                'shipping_last_name' => $order->shipping_last_name,
                'shipping_company' => $order->shipping_company,
                'shipping_phone' => isset($order->shipping_phone) ? $order->shipping_phone : '',                
                'shipping_address_1' => $order->shipping_address_1,
                'shipping_address_2' => $order->shipping_address_2,
                'shipping_postcode' => $order->shipping_postcode,
                'shipping_city' => $order->shipping_city,
                'shipping_state' => $order->shipping_state,
                'shipping_country' => $order->shipping_country,
                'customer_note' => $order->customer_note,
                'wt_import_key' => $order->get_order_number(),
                'shipping_items' => self::format_data(implode(';', $shipping_items)),
                'fee_items' => implode('||', $fee_items),
                'tax_items' => implode(';', $tax_items),
                'coupon_items' => implode(';', $coupon_items),
                'refund_items' => implode(';', $refund_items),
                'order_notes' => implode('||', self::get_order_notes($order)),
                'download_permissions' => $order->download_permissions_granted ? $order->download_permissions_granted : 0,
            );
        } else {
            $paid_date = $order->get_date_paid();
            $order_data = array(
                'order_id' => $order->get_id(),
                'order_number' => $order->get_order_number(),
                'order_date' => date('Y-m-d H:i:s', strtotime(get_post($order->get_id())->post_date)),
                'paid_date' => $paid_date, //isset($paid_date) ? date('Y-m-d H:i:s', strtotime($paid_date)) : '',
                'status' => $order->get_status(),
                'shipping_total' => $order->get_total_shipping(),
                'shipping_tax_total' => wc_format_decimal($order->get_shipping_tax(), 2),
                'fee_total' => wc_format_decimal($fee_total, 2),
                'fee_tax_total' => wc_format_decimal($fee_tax_total, 2),
                'tax_total' => wc_format_decimal($order->get_total_tax(), 2),
                'cart_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_cart_discount(), 2),
                'order_discount' => (defined('WC_VERSION') && (WC_VERSION >= 2.3)) ? wc_format_decimal($order->get_total_discount(), 2) : wc_format_decimal($order->get_order_discount(), 2),
                'discount_total' => wc_format_decimal($order->get_total_discount(), 2),
                'order_total' => wc_format_decimal($order->get_total(), 2),
                'order_currency' => $order->get_currency(),
                'payment_method' => $order->get_payment_method(),
                'payment_method_title' => $order->get_payment_method_title(),
                'transaction_id' => $order->get_transaction_id(),
                'customer_ip_address' => $order->get_customer_ip_address(),
                'customer_user_agent' => $order->get_customer_user_agent(), 
                'shipping_method' => $order->get_shipping_method(),
                'customer_id' => $order->get_user_id(),
                'customer_user' => $order->get_user_id(),
                'customer_email' => ($a = get_userdata($order->get_user_id())) ? $a->user_email : '',
                'billing_first_name' => $order->get_billing_first_name(),
                'billing_last_name' => $order->get_billing_last_name(),
                'billing_company' => $order->get_billing_company(),
                'billing_email' => $order->get_billing_email(),
                'billing_phone' => $order->get_billing_phone(),
                'billing_address_1' => $order->get_billing_address_1(),
                'billing_address_2' => $order->get_billing_address_2(),
                'billing_postcode' => $order->get_billing_postcode(),
                'billing_city' => $order->get_billing_city(),
                'billing_state' => $order->get_billing_state(),
                'billing_country' => $order->get_billing_country(),
                'shipping_first_name' => $order->get_shipping_first_name(),
                'shipping_last_name' => $order->get_shipping_last_name(),
                'shipping_company' => $order->get_shipping_company(),
                'shipping_phone' =>  (version_compare(WC_VERSION, '5.6', '<')) ? '' : $order->get_shipping_phone(), 
                'shipping_address_1' => $order->get_shipping_address_1(),
                'shipping_address_2' => $order->get_shipping_address_2(),
                'shipping_postcode' => $order->get_shipping_postcode(),
                'shipping_city' => $order->get_shipping_city(),
                'shipping_state' => $order->get_shipping_state(),
                'shipping_country' => $order->get_shipping_country(),
                'customer_note' => $order->get_customer_note(),
                'wt_import_key' => $order->get_order_number(),
                'shipping_items' => self::format_data(implode(';', $shipping_items)),
                'fee_items' => implode('||', $fee_items),
                'tax_items' => implode(';', $tax_items),
                'coupon_items' => implode(';', $coupon_items),
                'refund_items' => implode(';', $refund_items),
                'order_notes' => implode('||', (defined('WC_VERSION') && (WC_VERSION >= 3.2)) ? self::get_order_notes_new($order) : self::get_order_notes($order)),
                'download_permissions' => $order->is_download_permitted() ? $order->is_download_permitted() : 0,                
            );
        }
        
        if ($this->is_wt_invoice_active):
            $invoice_date = get_post_meta($order_data['order_id'], '_wf_invoice_date', true);
            $invoice_number = get_post_meta($order_data['order_id'], 'wf_invoice_number', true);
            $order_data['meta:wf_invoice_number'] = empty($invoice_number) ? '' : $invoice_number;
            $order_data['meta:_wf_invoice_date'] = empty($invoice_date) ? '' : date_i18n(get_option( 'date_format' ), $invoice_date);
        endif;
        if ($this->is_yith_tracking_active):
            $ywot_tracking_code = get_post_meta($order_data['order_id'], 'ywot_tracking_code', true);
            $ywot_tracking_postcode = get_post_meta($order_data['order_id'], 'ywot_tracking_postcode', true);
            $ywot_carrier_id = get_post_meta($order_data['order_id'], 'ywot_carrier_id', true);
            $ywot_pick_up_date = get_post_meta($order_data['order_id'], 'ywot_pick_up_date', true);
            $ywot_picked_up = get_post_meta($order_data['order_id'], 'ywot_picked_up', true);            
            $order_data['meta:ywot_tracking_code'] = empty($ywot_tracking_code) ? '' : $ywot_tracking_code;
            $order_data['meta:ywot_tracking_postcode'] = empty($ywot_tracking_postcode) ? '' : $ywot_tracking_postcode;
            $order_data['meta:ywot_carrier_id'] = empty($ywot_carrier_id) ? '' : $ywot_carrier_id;
            $order_data['meta:ywot_pick_up_date'] = empty($ywot_pick_up_date) ? '' : $ywot_pick_up_date;
            $order_data['meta:ywot_picked_up'] = empty($ywot_picked_up) ? '' : $ywot_picked_up;            
        endif;        

        $order_export_data = array();
        foreach ($csv_columns as $key => $value) {
            if (!$order_data || array_key_exists($key, $order_data)) {
                $order_export_data[$key] = $order_data[$key];
            } 
        }

        $li = 1;
        foreach ($line_items as $line_item) {
            foreach ($line_item as $name => $value) {
                $line_item[$name] = $name . ':' . $value;
            }
            $line_item = implode(apply_filters('wt_change_item_separator', '|'), $line_item);
            $order_export_data["line_item_{$li}"] = $line_item;
            $li++;
        }
         
        $max_line_items = $this->line_items_max_count;
        for ($i = 1; $i <= $max_line_items; $i++) {
            $order_export_data["line_item_{$i}"] = !empty($order_export_data["line_item_{$i}"]) ? self::format_data($order_export_data["line_item_{$i}"]) : '';
        }

        if ($this->export_to_separate_columns) {
            $line_item_values = self::get_all_metakeys_and_values($order);
            $this->line_item_meta = self::get_all_line_item_metakeys();
            $max_line_items = $this->line_items_max_count;                                                                   
            for ($i = 1; $i <= $max_line_items; $i++) {
                $line_item_array = explode('|', $order_export_data["line_item_{$i}"]);                                               
                foreach ($this->line_item_meta as $meta_val) {
                    $order_export_data["line_item_{$i}_name"] = !empty($line_item_array[0]) ? substr($line_item_array[0], strpos($line_item_array[0], ':') + 1) : '';
                    $order_export_data["line_item_{$i}_product_id"] = !empty($line_item_array[1]) ? substr($line_item_array[1], strpos($line_item_array[1], ':') + 1) : '';
                    $order_export_data["line_item_{$i}_sku"] = !empty($line_item_array[2]) ? substr($line_item_array[2], strpos($line_item_array[2], ':') + 1) : '';
                    $order_export_data["line_item_{$i}_quantity"] = !empty($line_item_array[3]) ? substr($line_item_array[3], strpos($line_item_array[3], ':') + 1) : '';
                    $order_export_data["line_item_{$i}_total"] = !empty($line_item_array[4]) ? substr($line_item_array[4], strpos($line_item_array[4], ':') + 1) : '';
                    $order_export_data["line_item_{$i}_subtotal"] = !empty($line_item_array[5]) ? substr($line_item_array[5], strpos($line_item_array[5], ':') + 1) : '';
                    if (in_array($meta_val, array("_product_id", "_qty", "_variation_id", "_line_total", "_line_subtotal", "_tax_class", "_line_tax", "_line_tax_data", "_line_subtotal_tax"))) {
                        continue;
                    } else {
                        $order_export_data["line_item_{$i}_$meta_val"] = !empty($line_item_values[$i][$meta_val]) ? $line_item_values[$i][$meta_val] : '';
                    }
                }
            }
        }      
        $order_data_filter_args = array('max_line_items' => $max_line_items);
        
        if ($this->export_to_separate_rows) {
            $order_export_data = $this->wt_line_item_separate_row_csv_data($order_export_data, $order_data_filter_args);
        } 
        
        return apply_filters('hf_alter_csv_order_data', $order_export_data, $order_data_filter_args);
    }

    public static function hf_get_orders_of_products($products, $export_order_statuses, $export_limit, $export_offset, $end_date, $start_date, $exclude_already_exported, $retun_count = false) {
        global $wpdb;
        $query .= "SELECT DISTINCT po.ID FROM {$wpdb->posts} AS po
            LEFT JOIN {$wpdb->postmeta} AS pm ON pm.post_id = po.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON oi.order_id = po.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om ON om.order_item_id = oi.order_item_id
            WHERE po.post_type = 'shop_order'
            AND oi.order_item_type = 'line_item'
            AND om.meta_key IN ('_product_id','_variation_id')
            AND om.meta_value IN ('" . implode("','", $products) . "')
            AND (po.post_date BETWEEN '$start_date' AND '$end_date')";
        if ($export_order_statuses != 'any') {
            $query .= " AND po.post_status IN ( '" . implode("','", $export_order_statuses) . "' )";
        }

        if ($exclude_already_exported) {
            $query .= " AND pm.meta_key = 'wf_order_exported_status' AND pm.meta_value=1";
        }

        if ($retun_count == FALSE) {
            $query .= " LIMIT " . intval($export_limit) . ' ' . (!empty($export_offset) ? 'OFFSET ' . intval($export_offset) : '');
        }

        $order_ids = $wpdb->get_col($query);

        if ($retun_count == TRUE) {
            return count($order_ids);
        }
        return $order_ids;
    }

    public static function hf_get_orders_of_coupons($coupons, $export_order_statuses, $export_limit, $export_offset, $end_date, $start_date, $exclude_already_exported, $retun_count = false) {
        global $wpdb;
        $query = "SELECT DISTINCT po.ID FROM {$wpdb->posts} AS po
            LEFT JOIN {$wpdb->postmeta} AS pm ON pm.post_id = po.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON oi.order_id = po.ID
            LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS om ON om.order_item_id = oi.order_item_id
            WHERE po.post_type = 'shop_order'
            AND oi.order_item_type = 'coupon'
            AND oi.order_item_name IN ('" . implode("','", $coupons) . "')
            AND (po.post_date BETWEEN '$start_date' AND '$end_date')";
        if ($export_order_statuses != 'any') {
            $query .= " AND po.post_status IN ( '" . implode("','", $export_order_statuses) . "' )";
        }
        if ($export_order_statuses == 'any') {
            $defualt_exclude_status = get_post_stati(array('exclude_from_search' => true));
            $stati = array_values(get_post_stati());
            foreach ($stati as $key => $status) {
                if (in_array($status, $defualt_exclude_status, true)) {
                    unset($stati[$key]);
                }
            }
            $query .= " AND po.post_status IN ( '" . implode("','", $stati) . "' )";
        }
        if ($exclude_already_exported) {
            $query .= " AND pm.meta_key = 'wf_order_exported_status' AND pm.meta_value=1";
        }
        if ($retun_count == FALSE) {
            $query .= " LIMIT " . intval($export_limit) . ' ' . (!empty($export_offset) ? 'OFFSET ' . intval($export_offset) : '');
        }
        $order_ids = $wpdb->get_col($query);
        if ($retun_count == TRUE) {
            return count($order_ids);
        }
        return $order_ids;
    }

    public static function get_all_line_item_metakeys() {
        global $wpdb;
        $filter_meta = apply_filters('wt_order_export_select_line_item_meta', array());
        $filter_meta = !empty($filter_meta) ? implode("','", $filter_meta) : '';
        $query = "SELECT DISTINCT om.meta_key
            FROM {$wpdb->prefix}woocommerce_order_itemmeta AS om 
            INNER JOIN {$wpdb->prefix}woocommerce_order_items AS oi ON om.order_item_id = oi.order_item_id
            WHERE oi.order_item_type = 'line_item'";
        if (!empty($filter_meta)) {
            $query .= " AND om.meta_key IN ('" . $filter_meta . "')";
        }
        $meta_keys = $wpdb->get_col($query);
        return $meta_keys;
    }

    public static function get_order_line_item_meta($item_id) {
        global $wpdb;
        $filtered_meta = apply_filters('wt_order_export_select_line_item_meta', array());
        $filtered_meta = !empty($filtered_meta) ? implode("','", $filtered_meta) : '';
        $query = "SELECT meta_key,meta_value
            FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE order_item_id = '$item_id'";
        if (!empty($filtered_meta)) {
            $query .= " AND meta_key IN ('" . $filtered_meta . "')";
        }
        $meta_keys = $wpdb->get_results($query, OBJECT_K);
        return $meta_keys;
    }

    public static function get_order_notes($order) {
        $callback = array('WC_Comments', 'exclude_order_comments');
        $args = array(
            'post_id' => (WC()->version < '2.7.0') ? $order->id : $order->get_id(),
            'approve' => 'approve',
            'type' => 'order_note'
        );
        remove_filter('comments_clauses', $callback);
        $notes = get_comments($args);
        add_filter('comments_clauses', $callback);
        $notes = array_reverse($notes);
        $order_notes = array();
        foreach ($notes as $note) {
            $date = $note->comment_date;
            $customer_note = 0;
            if (get_comment_meta($note->comment_ID, 'is_customer_note', '1')) {
                $customer_note = 1;
            }
            $order_notes[] = implode('|', array(
                'content:' . str_replace(array("\r", "\n"), ' ', $note->comment_content),
                'date:' . (!empty($date) ? $date : current_time('mysql')),
                'customer:' . $customer_note,
                'added_by:' . $note->added_by
            ));
        }
        return $order_notes;
    }

    public static function get_order_notes_new($order) {
        $notes = wc_get_order_notes(array('order_id' => $order->get_id(), 'order_by' => 'date_created', 'order' => 'ASC'));
        $order_notes = array();
        foreach ($notes as $note) {
            $order_notes[] = implode('|', array(
                'content:' . str_replace(array("\r", "\n"), ' ', $note->content),
                'date:' . $note->date_created->date('Y-m-d H:i:s'),
                'customer:' . $note->customer_note,
                'added_by:' . $note->added_by
            ));
        }
        return $order_notes;
    }

    public static function get_all_metakeys_and_values($order = null) {
        $in = 1;
        $line_item_values = array();
        foreach ($order->get_items() as $item_id => $item) {
            //$item_meta = function_exists('wc_get_order_item_meta') ? wc_get_order_item_meta($item_id, '', false) : $order->get_item_meta($item_id);
            $item_meta = self::get_order_line_item_meta($item_id);
            foreach ($item_meta as $key => $value) {
                switch ($key) {
                    case '_qty':
                    case '_product_id':
                    case '_line_total':
                    case '_line_subtotal':
                    case '_tax_class':
                    case '_line_tax':
                    case '_line_tax_data':
                    case '_line_subtotal_tax':
                        break;

                    default:
                        if (is_object($value))
                            $value = $value->meta_value;
                        if (is_array($value))
                            $value = implode(',', $value);
                        $line_item_value[$key] = $value;
                        break;
                }
            }
            $line_item_values[$in] = !empty($line_item_value) ? $line_item_value : '';
            $in++;
        }
        return $line_item_values;
    }

    /**
     * Format the data if required
     * @param  string $meta_value
     * @param  string $meta name of meta key
     * @return string
     */
    public static function format_export_meta($meta_value, $meta) {
        switch ($meta) {
            case '_sale_price_dates_from' :
            case '_sale_price_dates_to' :
                return $meta_value ? date('Y-m-d', $meta_value) : '';
                break;
            case '_upsell_ids' :
            case '_crosssell_ids' :
                return implode('|', array_filter((array) json_decode($meta_value)));
                break;
            default :
                return $meta_value;
                break;
        }
    }

    public static function format_data($data) {
        if (!is_array($data))
            ;
        $data = (string) urldecode($data);
//        $enc = mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true);        
        $use_mb = function_exists('mb_detect_encoding');
        $enc = '';
        if ($use_mb) {
            $enc = mb_detect_encoding($data, 'UTF-8, ISO-8859-1', true);
        }
        $data = ( $enc == 'UTF-8' ) ? $data : utf8_encode($data);

        return $data;
    }

    public static function highest_line_item_count($line_item_keys) {
   
        $all_items  = array_count_values(array_column($line_item_keys, 'order_id'));
        return max($all_items);
        
    }
    
    /**
     * Wrap a column in quotes for the CSV
     * @param  string data to wrap
     * @return string wrapped data
     */
    public static function wrap_column($data) {
        return '"' . str_replace('"', '""', $data) . '"';
    }
    
    public static function get_max_line_items($order_ids) {
        
        global $wpdb;
        $query_line_items = "select p.order_id, p.order_item_type from {$wpdb->prefix}woocommerce_order_items as p where order_item_type ='line_item' and p.order_item_id = p.order_item_id";
        $line_item_keys = $wpdb->get_results($query_line_items, ARRAY_A);                
        $max_line_items = self::highest_line_item_count($line_item_keys);
        return $max_line_items;
        /*
        $max_line_items = 0;
        foreach ($order_ids as $order_id) {
            $order = wc_get_order($order_id);
            $line_items_count = count($order->get_items());
            if ($line_items_count >= $max_line_items) {
                $max_line_items = $line_items_count;
            }
        }
        return $max_line_items;
         * 
         */
    }

}
}


/*
* https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query#adding-custom-parameter-support
* It is possible to add support for custom query variables in wc_get_orders and WC_Order_Query. To do this you need to filter the generated query.
*/
add_filter('woocommerce_order_data_store_cpt_get_orders_query', function ($query, $query_vars) {
   if (!empty($query_vars['wt_meta_query'])) {

       foreach ($query_vars['wt_meta_query'] as $meta_querys) {

           foreach ($meta_querys as $key => $value) {
               $meta_query[$key] = $value;
           }
           if (!empty($meta_query)) {
               $query['meta_query'][] = $meta_query;
           }
       }
   }
   return $query;
}, 10, 2);
