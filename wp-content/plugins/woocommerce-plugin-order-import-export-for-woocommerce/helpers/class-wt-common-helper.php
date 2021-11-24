<?php
if(!class_exists('Wt_Import_Export_For_Woo_Basic_Common_Helper')){
class Wt_Import_Export_For_Woo_Basic_Common_Helper
{
	
    public static $min_version_msg='';

    /**
    * Get File name by url
    * @param string $file_url URL of the file.
    * @return string the base name of the given URL (File name).
    */
   public static function wt_wc_get_filename_from_url( $file_url ) {
       $parts = parse_url( $file_url );
       if ( isset( $parts['path'] ) ) {
           return basename( $parts['path'] );
       }
   }

   /**
    * Get info like language code, parent product ID etc by product id.
    * @param int Product ID.
    * @return array/false.
    */
   public static function wt_get_wpml_original_post_language_info($element_id){
       $get_language_args = array('element_id' => $element_id, 'element_type' => 'post_product');
       $original_post_language_info = apply_filters('wpml_element_language_details', null, $get_language_args);
       return $original_post_language_info;
   }

   public static function wt_get_product_id_by_sku($sku){
       global $wpdb;
       $post_exists_sku = $wpdb->get_var($wpdb->prepare("
                   SELECT $wpdb->posts.ID
                   FROM $wpdb->posts
                   LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id )
                   WHERE $wpdb->posts.post_status IN ( 'publish', 'private', 'draft', 'pending', 'future' )
                   AND $wpdb->postmeta.meta_key = '_sku' AND $wpdb->postmeta.meta_value = '%s'
                   ", $sku));   
       if ($post_exists_sku) {
           return $post_exists_sku;
       }
       return false;

   }

   /**
    * To strip the specific string from the array key as well as value.
    * @param array $array.
    * @param string $data.
    * @return array.
    */
   public static function wt_array_walk($array , $data) {
       $new_array =array();
       foreach ($array as $key => $value) {
           $new_array[str_replace($data, '', $key)] = str_replace($data, '', $value);
       }
       return $new_array;
   }

   /**
   *  Check the minimum base version required for post type modules
   *
   */
    public static function check_base_version($post_type, $post_type_title, $min_version)
    {
        $warn_icon='<span class="dashicons dashicons-warning"></span>&nbsp;';
        if(!version_compare(WT_O_IEW_VERSION, $min_version, '>=')) /* not matching the min version */
        {
            self::$min_version_msg.=$warn_icon.sprintf(__("The %s requires a minimum version of %s %s. Please upgrade the %s accordingly."), "<b>$post_type_title</b>", "<b>".WT_O_IEW_PLUGIN_NAME."</b>", "<b>v$min_version</b>", "<b>".WT_O_IEW_PLUGIN_NAME."</b>").'<br />';
            add_action('admin_notices', array(__CLASS__, 'no_minimum_base_version') );
            return false;
        }
        return true;
    }

    /**
    *
    *   No minimum version error message
    */
    public static function no_minimum_base_version()
    {
        ?>
        <div class="notice notice-warning">
            <p>
                <?php 
                echo self::$min_version_msg;
                ?>
            </p>
        </div>
        <?php
    }

    /**
    *   Decode the post data as normal array from json encoded from data.
    *   If step key is specified, then it will return the data corresponds to the form key
    *   @param array $form_data
    *   @param string $key
    */
    public static function process_formdata($form_data, $key='')
    {
        if($key!="") /* if key is given then take its data */
        {
            if(isset($form_data[$key]))
            {
                if(is_array($form_data[$key]))
                {
                    $form_data_vl=$form_data[$key];
                }else
                {
                    $form_data_vl=json_decode(stripslashes($form_data[$key]),true);
                } 
            }else
            {
                $form_data_vl=array();
            }
        }else
        {
            $form_data_vl=array();
            foreach($form_data as $form_datak=>$form_datav)
            {
                $form_data_vl[$form_datak]=self::process_formdata($form_data, $form_datak);
            }
        }
        return (is_array($form_data_vl) ? $form_data_vl : array());
    }

    /**
    *   Form field generator
    */
    public static function field_generator($form_fields, $form_data)
    {
        include plugin_dir_path( dirname( __FILE__ ) ).'admin/partials/_form_field_generator.php';
    }


    /**
    *   Save advanced settings
    *   @param  array   $settings   array of setting values
    */
    public static function set_advanced_settings($settings)
    {
        update_option('wt_iew_advanced_settings', $settings);
    }

    /**
    *
    *   Extract validation rule from form field array
    *   @param  array   $fields   form field array
    */
    public static function extract_validation_rules($fields)
    {
        $out=array_map(function ($r) { return (isset($r['validation_rule']) ? $r['validation_rule'] : ''); }, $fields);
        return array_filter($out);
    }

    /**
    *   Get advanced settings.
    *   @param      string  $key    key for specific setting (optional)
    *   @return     mixed   if key provided then the value of key otherwise array of values
    */
    public static function get_advanced_settings($key="")
    {
        $advanced_settings=get_option('wt_iew_advanced_settings');       
        $advanced_settings=($advanced_settings ? $advanced_settings : array());
        if($key!="")
        {
            $key=(substr($key,0,8)!=='wt_iew_' ? 'wt_iew_' : '').$key;
            if(isset($advanced_settings[$key]))
            {
                return $advanced_settings[$key];
            }else
            {
                $default_settings=self::get_advanced_settings_default();
                return (isset($default_settings[$key]) ? $default_settings[$key] : '');
            }
        }else
        {
            $default_settings=self::get_advanced_settings_default();            
            $advanced_settings=wp_parse_args($advanced_settings, $default_settings);
            return $advanced_settings; 
        }
    }

    /**
    *   Get default value of advanced settings
    *   @return     array   array of default values
    *
    */
    public static function get_advanced_settings_default()
    {
        $fields=self::get_advanced_settings_fields();      
        foreach ($fields as $key => $value)
        {
            if(isset($value['value']))
            {
                $key=(substr($key,0,8)!=='wt_iew_' ? 'wt_iew_' : '').$key;
                $out[$key]=$value['value'];
            }
        }
        return $out;
    }

    /**
    *   Get advanced fields
    *   @return     array   array of fields
    *
    */
    public static function get_advanced_settings_fields()
    {
        $fields=array();
        return apply_filters('wt_iew_advanced_setting_fields_basic', $fields);
    }
    
    
    public static function wt_allowed_screens(){
        $screens=array('wt_import_export_for_woo_basic','wt_import_export_for_woo_basic_export','wt_import_export_for_woo_basic_import','wt_import_export_for_woo_basic_history','wt_import_export_for_woo_basic_history_log');
        return apply_filters('wt_iew_allowed_screens_basic', $screens);

    }
    public static function wt_get_current_page(){        
        if (isset($_GET['page'])) {
            return $_GET['page'];
        }
        return '';
    }
    
    public static function wt_is_screen_allowed(){
        if(in_array(self::wt_get_current_page(), self::wt_allowed_screens())){
            return true;
        }else{
            return false;
        }
    }
    
}
}

if(!function_exists('is_woocommerce_prior_to_basic')){
    function is_woocommerce_prior_to_basic($version) {

        $woocommerce_is_pre_version = (!defined('WC_VERSION') || version_compare(WC_VERSION, $version, '<')) ? true : false;
        return $woocommerce_is_pre_version;

        if (WC()->version < $version) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

if(!function_exists('wt_let_to_num_basic')){
function wt_let_to_num_basic( $size ) {
	$l   = substr( $size, -1 );
	$ret = (int) substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
			// No break.
		case 'T':
			$ret *= 1024;
			// No break.
		case 'G':
			$ret *= 1024;
			// No break.
		case 'M':
			$ret *= 1024;
			// No break.
		case 'K':
			$ret *= 1024;
			// No break.
	}
	return $ret;
}
}

if(!function_exists('wt_removeBomUtf8_basic')){
function wt_removeBomUtf8_basic($s) {
    if (substr($s, 0, 3) == chr(hexdec('EF')) . chr(hexdec('BB')) . chr(hexdec('BF'))) {
        return substr($s, 3);
    } else {
        return $s;
    }
}
}

