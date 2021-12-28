<?php

/**
 * Coupon section of the plugin
 *
 * @link           
 *
 * @package  Wt_Import_Export_For_Woo 
 */
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_Basic_Coupon')){
class Wt_Import_Export_For_Woo_Basic_Coupon {

    public $module_id = '';
    public static $module_id_static = '';
    public $module_base = 'coupon';
    public $module_name = 'Coupon Import Export for WooCommerce';
    public $min_base_version= '1.0.0'; /* Minimum `Import export plugin` required to run this add on plugin */

    private $importer = null;
    private $exporter = null;
    private $all_meta_keys = array();
    private $found_meta = array();
    private $found_hidden_meta = array();
	private $selected_column_names = null;

    public function __construct()
    {
        /**
        *   Checking the minimum required version of `Import export plugin` plugin available
        */
        if(!Wt_Import_Export_For_Woo_Basic_Common_Helper::check_base_version($this->module_base, $this->module_name, $this->min_base_version))
        {
            return;
        }
        if(!function_exists('is_plugin_active'))
        {
            include_once(ABSPATH.'wp-admin/includes/plugin.php');
        }
        if(!is_plugin_active('woocommerce/woocommerce.php'))
        {
            return;
        }

        $this->module_id = Wt_Import_Export_For_Woo_basic::get_module_id($this->module_base);
        self::$module_id_static = $this->module_id;
       
        add_filter('wt_iew_exporter_post_types_basic', array($this, 'wt_iew_exporter_post_types_basic'), 10, 1);
        add_filter('wt_iew_importer_post_types_basic', array($this, 'wt_iew_exporter_post_types_basic'), 10, 1);
        
        add_filter('wt_iew_exporter_alter_mapping_fields_basic', array($this, 'exporter_alter_mapping_fields'), 10, 3);        
        add_filter('wt_iew_importer_alter_mapping_fields_basic', array($this, 'get_importer_post_columns'), 10, 3);  
        
		add_filter('wt_iew_exporter_alter_filter_fields_basic', array($this, 'exporter_alter_filter_fields'), 10, 3);
		
        add_filter('wt_iew_importer_alter_advanced_fields_basic', array($this, 'importer_alter_advanced_fields'), 10, 3);

        add_filter('wt_iew_exporter_alter_meta_mapping_fields_basic', array($this, 'exporter_alter_meta_mapping_fields'), 10, 3);
        add_filter('wt_iew_importer_alter_meta_mapping_fields_basic', array($this, 'importer_alter_meta_mapping_fields'), 10, 3);

        add_filter('wt_iew_exporter_alter_mapping_enabled_fields_basic', array($this, 'exporter_alter_mapping_enabled_fields'), 10, 3);
        add_filter('wt_iew_importer_alter_mapping_enabled_fields_basic', array($this, 'exporter_alter_mapping_enabled_fields'), 10, 3);

        add_filter('wt_iew_exporter_do_export_basic', array($this, 'exporter_do_export'), 10, 7);
        add_filter('wt_iew_importer_do_import_basic', array($this, 'importer_do_import'), 10, 8);

        add_filter('wt_iew_importer_steps_basic', array($this, 'importer_steps'), 10, 2);
    }

    /**
    *   Altering advanced step description
    */
    public function importer_steps($steps, $base)
    {
        if($this->module_base==$base)
        {
            $steps['advanced']['description']=__('Use advanced options from below to decide updates to existing coupons, batch import count. You can also save the template file for future imports.');
        }
        return $steps;
    }
    
    public function importer_do_import($import_data, $base, $step, $form_data, $selected_template_data, $method_import, $batch_offset, $is_last_batch) {        
        if ($this->module_base != $base) {
            return $import_data;
        }
        
        if(0 == $batch_offset){                        
            $memory = size_format(wt_let_to_num_basic(ini_get('memory_limit')));
            $wp_memory = size_format(wt_let_to_num_basic(WP_MEMORY_LIMIT));                      
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->module_base, 'import', '---[ New import started at '.date('Y-m-d H:i:s').' ] PHP Memory: ' . $memory . ', WP Memory: ' . $wp_memory);
        }
        
        include plugin_dir_path(__FILE__) . 'import/import.php';
        $import = new Wt_Import_Export_For_Woo_Basic_Coupon_Import($this);
        
        $response = $import->prepare_data_to_import($import_data,$form_data,$batch_offset,$is_last_batch);
        
        if($is_last_batch){
            Wt_Import_Export_For_Woo_Basic_Logwriter::write_log($this->module_base, 'import', '---[ Import ended at '.date('Y-m-d H:i:s').']---');
        }
                
        return $response;
    }
    
    
    public function exporter_do_export($export_data, $base, $step, $form_data, $selected_template_data, $method_export, $batch_offset) {
        if ($this->module_base != $base) {
            return $export_data;
        }
                
        switch ($method_export) {
            case 'quick':
                $this->set_export_columns_for_quick_export($form_data);  
                break;

            case 'template':            
            case 'new':
                $this->set_selected_column_names($form_data);
                break;
            
            default:
                break;
        }
        
        include plugin_dir_path(__FILE__) . 'export/export.php';
        $export = new Wt_Import_Export_For_Woo_Basic_Coupon_Export($this);

        $header_row = $export->prepare_header();

        $data_row = $export->prepare_data_to_export($form_data, $batch_offset);

        $export_data = array(
            'head_data' => $header_row,
            'body_data' => $data_row['data'],
            'total' => $data_row['total'],
        );
        
        return $export_data;
    }
    
    /*
     * Setting default export columns for quick export
     */

    public function set_export_columns_for_quick_export($form_data) {

        $post_columns = self::get_coupon_post_columns();

        $this->selected_column_names = array_combine(array_keys($post_columns), array_keys($post_columns));

        if (isset($form_data['method_export_form_data']['mapping_enabled_fields']) && !empty($form_data['method_export_form_data']['mapping_enabled_fields'])) {
            foreach ($form_data['method_export_form_data']['mapping_enabled_fields'] as $value) {
                $additional_quick_export_fields[$value] = array('fields' => array());
            }

            $export_additional_columns = $this->exporter_alter_meta_mapping_fields($additional_quick_export_fields, $this->module_base, array());
            foreach ($export_additional_columns as $value) {
                $this->selected_column_names = array_merge($this->selected_column_names, $value['fields']);
            }
        }
    }

    
    /**
     * Adding current post type to export list
     *
     */
    public function wt_iew_exporter_post_types_basic($arr) {
        $arr['coupon'] = __('Coupon');
        return $arr;
    }

    public static function get_coupon_types() {
        $coupon_types   = wc_get_coupon_types();
        return apply_filters('wt_iew_export_coupon_types',  $coupon_types);
        
    }

    public static function get_coupon_statuses() {
        $statuses = array('publish', 'private', 'draft', 'pending', 'future'); 
        return apply_filters('wt_iew_export_coupon_statuses', array_combine($statuses, $statuses));
    }

    public static function get_coupon_sort_columns() {                
        $sort_columns = array('ID', 'post_parent', 'post_title', 'post_date', 'post_modified', 'post_author', 'menu_order', 'comment_count');
        return apply_filters('wt_iew_export_coupon_sort_columns', array_combine($sort_columns, $sort_columns));
    }

    public static function get_coupon_post_columns() {
        return include plugin_dir_path(__FILE__) . 'data/data-coupon-post-columns.php';
    }
    
    public function get_importer_post_columns($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
        $colunm = include plugin_dir_path(__FILE__) . 'data/data/data-wf-reserved-fields-pair.php';
//        $colunm = array_map(function($vl){ return array('title'=>$vl, 'description'=>$vl); }, $arr); 
        return $colunm;
    }
    
    public function exporter_alter_mapping_enabled_fields($mapping_enabled_fields, $base, $form_data_mapping_enabled_fields) {
        if ($base == $this->module_base) {
            $mapping_enabled_fields = array();             
        }
        return $mapping_enabled_fields;
    }

    
    public function exporter_alter_meta_mapping_fields($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }

        foreach ($fields as $key => $value) {
            switch ($key) {              
                default:
                    break;
            }
        }

        return $fields;
    }
    
    public function importer_alter_meta_mapping_fields($fields, $base, $step_page_form_data) {
        if ($base != $this->module_base) {
            return $fields;
        }
        $fields=$this->exporter_alter_meta_mapping_fields($fields, $base, $step_page_form_data);
        $out=array();
        foreach ($fields as $key => $value) 
        {
            $value['fields']=array_map(function($vl){ return array('title'=>$vl, 'description'=>$vl); }, $value['fields']);
            $out[$key]=$value;
        }
        return $out;
    }
    
    public function wt_get_found_meta() {

        if (!empty($this->found_meta)) {
            return $this->found_meta;
        }

        // Loop products and load meta data
        $found_meta = array();
        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.

        $all_meta_keys = $this->wt_get_all_meta_keys();


        $csv_columns = self::get_coupon_post_columns();


        foreach ($all_meta_keys as $meta) {

            if (!$meta || (substr((string) $meta, 0, 1) == '_') || in_array($meta, array_keys($csv_columns)) || in_array('meta:' . $meta, array_keys($csv_columns)))
                continue;

            $found_meta[] = $meta;
        }
        
        $found_meta = array_diff($found_meta, array_keys($csv_columns));
        $this->found_meta = $found_meta;
        return $this->found_meta;
    }

    public function wt_get_found_hidden_meta() {

        if (!empty($this->found_hidden_meta)) {
            return $this->found_hidden_meta;
        }

        // Loop products and load meta data
        $found_hidden_meta = array();
        // Some of the values may not be usable (e.g. arrays of arrays) but the worse
        // that can happen is we get an empty column.

        $all_meta_keys = $this->wt_get_all_meta_keys();
        $csv_columns = self::get_coupon_post_columns();
        foreach ($all_meta_keys as $meta) {

            if (!$meta || (substr((string) $meta, 0, 1) != '_') || in_array($meta, array_keys($csv_columns)) || in_array('meta:' . $meta, array_keys($csv_columns)))
                continue;

            $found_hidden_meta[] = $meta;
        }

        $found_hidden_meta = array_diff($found_hidden_meta, array_keys($csv_columns));

        $this->found_hidden_meta = $found_hidden_meta;
        return $this->found_hidden_meta;
    }


    public function wt_get_all_meta_keys() {

        if (!empty($this->all_meta_keys)) {
            return $this->all_meta_keys;
        }

        $all_meta_keys = self::get_all_metakeys('shop_coupon');

        $this->all_meta_keys = $all_meta_keys;
        return $this->all_meta_keys;
    }

        
    public static function get_all_metakeys($post_type = 'shop_coupon') {
        global $wpdb;

        $meta = $wpdb->get_col($wpdb->prepare(
                        "SELECT DISTINCT pm.meta_key
            FROM {$wpdb->postmeta} AS pm
            LEFT JOIN {$wpdb->posts} AS p ON p.ID = pm.post_id
            WHERE p.post_type = %s
            AND p.post_status IN ( 'publish', 'pending', 'private', 'draft' )", $post_type
                ));

        sort($meta);

        return $meta;
    }
    
    
    public function set_selected_column_names($full_form_data) {        
        if (is_null($this->selected_column_names)) {
            if (isset($full_form_data['mapping_form_data']['mapping_selected_fields']) && !empty($full_form_data['mapping_form_data']['mapping_selected_fields'])) {
                $this->selected_column_names = $full_form_data['mapping_form_data']['mapping_selected_fields'];
            }
            if (isset($full_form_data['meta_step_form_data']['mapping_selected_fields']) && !empty($full_form_data['meta_step_form_data']['mapping_selected_fields'])) {
                $export_additional_columns = $full_form_data['meta_step_form_data']['mapping_selected_fields'];
                foreach ($export_additional_columns as $value) {
                    $this->selected_column_names = array_merge($this->selected_column_names, $value);
                }
            }
        }

        return $full_form_data;
    }
    
    public function get_selected_column_names() {
            
        return $this->selected_column_names;
    }    

    public function exporter_alter_mapping_fields($fields, $base, $mapping_form_data) {
        if ($base == $this->module_base) {
            $fields = self::get_coupon_post_columns();
        }
        return $fields;
    }
    
    public function importer_alter_advanced_fields($fields, $base, $advanced_form_data) {
        if ($this->module_base != $base) {
            return $fields;
        }
        $out = array();         
        $out['found_action_merge'] = array(
            'label' => __("If the coupon exists in the store"),
            'type' => 'radio',
            'radio_fields' => array(
                'skip' => __('Skip'),
                'update' => __('Update'),                
            ),
            'value' => 'skip',
            'field_name' => 'found_action',
            'help_text_conditional'=>array(
                array(
                    'help_text'=> __('Retains the coupon in the store as is and skips the matching coupon from the input file.'),
                    'condition'=>array(
                        array('field'=>'wt_iew_found_action', 'value'=>'skip')
                    )
                ),
                array(
                    'help_text'=> __('Update coupon as per data from the input file'),
                    'condition'=>array(
                        array('field'=>'wt_iew_found_action', 'value'=>'update')
                    )
                )
            ),
            'form_toggler'=>array(
                'type'=>'parent',
                'target'=>'wt_iew_found_action'
            )
        );       
        
        
        foreach ($fields as $fieldk => $fieldv) {
            $out[$fieldk] = $fieldv;
        }
        return $out;
    }

    /**
     *  Customize the items in filter export page
     */
    public function exporter_alter_filter_fields($fields, $base, $filter_form_data) {

        if ($base == $this->module_base)
        {
            /* altering help text of default fields */
            $fields['limit']['label']=__('Total number of coupons to export'); 
            $fields['limit']['help_text']=__('Exports specified number of coupons. e.g. Entering 500 with a skip count of 10 will export coupons from 11th to 510th position.');
            $fields['offset']['label']=__('Skip first <i>n</i> coupons');
            $fields['offset']['help_text']=__('Skips specified number of coupons from the beginning. e.g. Enter 10 to skip first 10 coupons from export.');

            $fields['statuses'] = array(
                'label' => __('Coupon status'),
                'placeholder' => __('Any status'),
                'field_name' => 'statuses',
                'sele_vals' => self::get_coupon_statuses(),
                'help_text' => __('Export coupons by their status. You can specify more than one status if required.'),
                'type' => 'multi_select',
                'css_class' => 'wc-enhanced-select',
                'validation_rule' => array('type'=>'text_arr')
            );
            $fields['types'] = array(
                'label' => __('Coupon type'),
                'placeholder' => __('Any type'),
                'field_name' => 'types',
                'sele_vals' => self::get_coupon_types(),
                'help_text' => __('Select the coupon type e.g, fixed cart, recurring etc to export only coupon of a specific type.'),
                'type' => 'multi_select',
                'css_class' => 'wc-enhanced-select',
                'validation_rule' => array('type'=>'text_arr')
            );

            
            $fields['coupon_amount_from'] = array(
                'label'=>__("Coupon amount: from"),
                'placeholder' => __('From amount'),
                'type'=>'number',
                'value' =>'',
                'attr' =>array(
                        'min'=>0,
                    ),
                'field_name'=>'coupon_amount_from',
                'help_text'=>__('Export coupons by their discount amount. Specify the minimum discount amount for which the coupon was levied.'),
                'validation_rule'=>array('type'=>'floatval'),
            
            );
            
            
            $fields['coupon_amount_to'] = array(
                'label'=>__("Coupon amount: to"),
                'placeholder' => __('To amount'),
                'type'=>'number',
                'value' =>'',
                'attr' =>array(
                        'min'=>0,
                    ),
                'field_name'=>'coupon_amount_to',
                'help_text'=>__('Export coupons by their discount amount. Specify the maximum discount amount for which the coupon was levied.'),
                'validation_rule'=>array('type'=>'floatval'),
            
            );
  
            
            
            $fields['coupon_exp_date_from'] = array(
                'label' => __('Coupon expiry date: from'),
                'placeholder' => __('From date'),
                'field_name' => 'coupon_exp_date_from',
                'sele_vals' => '',
                'help_text' => __('Date on which the coupon will expire. Export coupons with expiry date equal to or greater than the specified date.'),
                'type' => 'text',
                'css_class' => 'wt_iew_datepicker',                
            );
            
            $fields['coupon_exp_date_to'] = array(
                'label' => __('Coupon expiry date: to'),
                'placeholder' => __('To date'),
                'field_name' => 'coupon_exp_date_to',
                'sele_vals' => '',
                'help_text' => __('Date on which the coupon will expire. Export coupons with expiry date equal to or less than the specified date.'),
                'type' => 'text',
                'css_class' => 'wt_iew_datepicker',                
            );

            $fields['sort_columns'] = array(
                'label' => __('Sort columns'),
                'placeholder' => __('ID'),
                'field_name' => 'sort_columns',
                'sele_vals' => self::get_coupon_sort_columns(),
                'help_text' => __('Sort the exported data based on the selected columns in order specified. Defaulted to ascending order.'),
                'type' => 'multi_select',
                'css_class' => 'wc-enhanced-select',
                'validation_rule' => array('type'=>'text_arr')
            );

            $fields['order_by'] = array(
                'label' => __('Sort by'),
                'placeholder' => __('ASC'),
                'field_name' => 'order_by',
                'sele_vals' => array('ASC' => 'Ascending', 'DESC' => 'Descending'),
                'help_text' => __('Defaulted to Ascending. Applicable to above selected columns in the order specified.'),
                'type' => 'select',
            );
        }
        return $fields;
    }
    public function get_item_by_id($id) {
        $post['edit_url']=get_edit_post_link($id);
        $post['title'] = get_the_title($id);
        return $post; 
    }
}
}
new Wt_Import_Export_For_Woo_Basic_Coupon();
