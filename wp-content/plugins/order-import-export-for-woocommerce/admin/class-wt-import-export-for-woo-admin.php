<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.webtoffee.com/
 * @since      1.0.0
 *
 * @package    Wt_Import_Export_For_Woo
 * @subpackage Wt_Import_Export_For_Woo/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wt_Import_Export_For_Woo
 * @subpackage Wt_Import_Export_For_Woo/admin
 * @author     Webtoffee <info@webtoffee.com>
 */
if(!class_exists('Wt_Import_Export_For_Woo_Admin_Basic')){
class Wt_Import_Export_For_Woo_Admin_Basic {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/*
	 * module list, Module folder and main file must be same as that of module name
	 * Please check the `register_modules` method for more details
	 */
	public static $modules=array(	
		'history',
		'export',
		'import',                
	);

	public static $existing_modules=array();

	public static $addon_modules=array();
        

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
            if(Wt_Import_Export_For_Woo_Basic_Common_Helper::wt_is_screen_allowed()){
                wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wt-import-export-for-woo-admin.css', array(), $this->version, 'all' );
            }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
            if(Wt_Import_Export_For_Woo_Basic_Common_Helper::wt_is_screen_allowed()){
		/* enqueue scripts */
		if(!function_exists('is_plugin_active'))
		{
			include_once(ABSPATH.'wp-admin/includes/plugin.php');
		}
		if(is_plugin_active('woocommerce/woocommerce.php'))
		{
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wt-import-export-for-woo-admin.js', array( 'jquery', 'jquery-tiptip'), $this->version, false );
		}else
		{
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wt-import-export-for-woo-admin.js', array( 'jquery'), $this->version, false );
			wp_enqueue_script(WT_IEW_PLUGIN_ID_BASIC.'-tiptip', WT_O_IEW_PLUGIN_URL.'admin/js/tiptip.js', array('jquery'), WT_O_IEW_VERSION, false);
		}

		$params=array(
			'nonces' => array(
		        'main' => wp_create_nonce(WT_IEW_PLUGIN_ID_BASIC),
		     ),
			'ajax_url' => admin_url('admin-ajax.php'),
			'plugin_id' =>WT_IEW_PLUGIN_ID_BASIC,
			'msgs'=>array(
				'settings_success'=>__('Settings updated.'),
				'all_fields_mandatory'=>__('All fields are mandatory'),
				'settings_error'=>__('Unable to update Settings.'),
                                'template_del_error'=>__('Unable to delete template'),
                                'template_del_loader'=>__('Deleting template...'),                            
				'value_empty'=>__('Value is empty.'),
				'error'=>sprintf(__('An unknown error has occurred! Refer to our %stroubleshooting guide%s for assistance.'), '<a href="'.WT_IEW_DEBUG_BASIC_TROUBLESHOOT.'" target="_blank">', '</a>'),
				'success'=>__('Success.'),
				'loading'=>__('Loading...'),
				'sure'=>__('Are you sure?'),
				'use_expression'=>__('Use expression as value.'),
				'cancel'=>__('Cancel'),
			),
                        'pro_plugins' => array(
                            'order' => array(
                                'url' => "https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('Order, Coupon, Subscription Export Import for WooCommerce'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/order-ie.svg'
                            ),
                            'coupon' => array(
                                'url' => "https://www.webtoffee.com/product/order-import-export-plugin-for-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Order_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('Order, Coupon, Subscription Export Import for WooCommerce'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/order-ie.svg'                                
                            ),
                            'product' => array(
                                'url' => "https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('Product Import Export Plugin For WooCommerce'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/product-ie.svg'                                
                            ),
                            'product_review' => array(
                                'url' => "https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('Product Import Export Plugin For WooCommerce'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/product-ie.svg'
                            ),
                            'product_categories' => array(
                                'url' => "https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('Product Import Export Plugin For WooCommerce'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/product-ie.svg'
                            ),
                            'product_tags' => array(
                                'url' => "https://www.webtoffee.com/product/product-import-export-woocommerce/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=Product_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('Product Import Export Plugin For WooCommerce'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/product-ie.svg'
                            ),
                            'user' => array(
                                'url' => "https://www.webtoffee.com/product/wordpress-users-woocommerce-customers-import-export/?utm_source=free_plugin_revamp&utm_medium=basic_revamp&utm_campaign=User_Import_Export&utm_content=" . WT_O_IEW_VERSION,
                                'name' => __('WordPress Users & WooCommerce Customers Import Export'),
                                'icon_url' => WT_O_IEW_PLUGIN_URL.'assets/images/gopro/user-ie.svg'
                            )
                    )
                );
		wp_localize_script($this->plugin_name, 'wt_iew_basic_params', $params);
            }

	}

	/**
	 * Registers menu options
	 * Hooked into admin_menu
	 *
	 * @since    1.0.0
	 */
	public function admin_menu()
	{
		$menus=array(
			'general-settings'=>array(
				'menu',
				__('General Settings'),
				__('General Settings'),
				apply_filters('wt_import_export_allowed_capability', 'import'),
				WT_IEW_PLUGIN_ID_BASIC,
				array($this,'admin_settings_page'),
				'dashicons-controls-repeat',
				56
			)
		);
		$menus=apply_filters('wt_iew_admin_menu_basic',$menus);

		$menu_order=array("export","export-sub","import","history","history_log");
		$this->wt_menu_order_changer($menus,$menu_order);                                            

		$main_menu = reset($menus); //main menu must be first one

		$parent_menu_key=$main_menu ? $main_menu[4] : WT_IEW_PLUGIN_ID_BASIC;

                
		/* adding general settings menu */
		$menus['general-settings-sub']=array(
			'submenu',
			$parent_menu_key,
			__('General Settings'),
			__('General Settings'), 
			apply_filters('wt_import_export_allowed_capability', 'import'),
			WT_IEW_PLUGIN_ID_BASIC,
			array($this, 'admin_settings_page')
		);
		if(count($menus)>0)
		{
			foreach($menus as $menu)
			{
				if($menu[0]=='submenu')
				{
					/* currently we are only allowing one parent menu */
					add_submenu_page($parent_menu_key,$menu[2],$menu[3],$menu[4],$menu[5],$menu[6]);
				}else
				{
					add_menu_page($menu[1],$menu[2],$menu[3],$menu[4],$menu[5],$menu[6],$menu[7]);	
				}
			}
		}
		if(function_exists('remove_submenu_page')){
			//remove_submenu_page(WT_PIEW_POST_TYPE, WT_PIEW_POST_TYPE);
		}
	}
	
	public function wt_menu_order_changer( &$arr, $index_arr ) {
			$arr_t = array();
			foreach ( $index_arr as $i => $v ) {
				foreach ( $arr as $k => $b ) {
					if ( $k == $v )
						$arr_t[ $k ] = $b;
				}
			}
			$arr = $arr_t;
	}

		public function admin_settings_page()
	{	
		include(plugin_dir_path( __FILE__ ).'partials/wt-import-export-for-woo-admin-display.php');
	}

	/**
	* 	Save admin settings and module settings ajax hook
	*/
	public function save_settings()
	{
		$out=array(
			'status'=>false,
			'msg'=>__('Error'),
		);

		if(Wt_Iew_Sh::check_write_access(WT_IEW_PLUGIN_ID_BASIC)) 
    	{
    		$advanced_settings=Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings();
    		$advanced_fields=Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings_fields();
    		$validation_rule=Wt_Import_Export_For_Woo_Basic_Common_Helper::extract_validation_rules($advanced_fields);
    		$new_advanced_settings=array();
    		foreach($advanced_fields as $key => $value) 
	        {
	            $form_field_name = isset($value['field_name']) ? $value['field_name'] : '';
				$field_name=(substr($form_field_name,0,8)!=='wt_iew_' ? 'wt_iew_' : '').$form_field_name;
	            $validation_key=str_replace('wt_iew_', '', $field_name);
	            if(isset($_POST[$field_name]))
	            {      	
	            	$new_advanced_settings[$field_name]=Wt_Iew_Sh::sanitize_data($_POST[$field_name], $validation_key, $validation_rule);
	            }
	        }
	        Wt_Import_Export_For_Woo_Basic_Common_Helper::set_advanced_settings($new_advanced_settings);
	        $out['status']=true;
	        $out['msg']=__('Settings Updated');
	        do_action('wt_iew_after_advanced_setting_update_basic', $new_advanced_settings);        
    	}
		echo json_encode($out);
		exit();
	}

        /**
	* 	Delete pre-saved temaplates entry from DB - ajax hook
	*/
        public function delete_template() {
            $out = array(
                'status' => false,
                'msg' => __('Error'),
            );

            if (Wt_Iew_Sh::check_write_access(WT_IEW_PLUGIN_ID_BASIC)) {
                if (isset($_POST['template_id'])) {

                    global $wpdb;
                    $template_id = absint($_POST['template_id']);
                    $tb = $wpdb->prefix . Wt_Import_Export_For_Woo_Basic::$template_tb;
                    $where = "=%d";
                    $where_data = array($template_id);
                    $wpdb->query($wpdb->prepare("DELETE FROM $tb WHERE id" . $where, $where_data));
                    $out['status'] = true;
                    $out['msg'] = __('Template deleted successfully');
                    $out['template_id'] = $template_id;
                }
            }
            wp_send_json($out);

        }        
        
	/**
	 Registers modules: admin	 
	 */
	public function admin_modules()
	{ 
		$wt_iew_admin_modules=get_option('wt_iew_admin_modules');
		if($wt_iew_admin_modules===false)
		{
			$wt_iew_admin_modules=array();
		}
		foreach (self::$modules as $module) //loop through module list and include its file
		{
			$is_active=1;
			if(isset($wt_iew_admin_modules[$module]))
			{
				$is_active=$wt_iew_admin_modules[$module]; //checking module status
			}else
			{
				$wt_iew_admin_modules[$module]=1; //default status is active
			}
			$module_file=plugin_dir_path( __FILE__ )."modules/$module/$module.php";
			if(file_exists($module_file) && $is_active==1)
			{
				self::$existing_modules[]=$module; //this is for module_exits checking
				require_once $module_file;
			}else
			{
				$wt_iew_admin_modules[$module]=0;	
			}
		}
		$out=array();
		foreach($wt_iew_admin_modules as $k=>$m)
		{
			if(in_array($k, self::$modules))
			{
				$out[$k]=$m;
			}
		}
		update_option('wt_iew_admin_modules',$out);


		/**
		*	Add on modules 
		*/
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );                 
		foreach (self::$addon_modules as $module) //loop through module list and include its file
		{                   
			$plugin_file="wt-import-export-for-woo-$module/wt-import-export-for-woo-$module.php";
			if(is_plugin_active($plugin_file))
			{
				$module_file=WP_PLUGIN_DIR."/wt-import-export-for-woo-$module/$module/$module.php";
				if(file_exists($module_file))
				{
					self::$existing_modules[]=$module;
					require_once $module_file;
				}				
			}
		}
                
                
                $addon_modules_basic = array(
                    'order'=>'order-import-export-for-woocommerce',
                    'coupon'=>'order-import-export-for-woocommerce',  
                    'product'=>'product-import-export-for-woo',
                    'product_review'=>'product-import-export-for-woo',
                    'product_categories'=>'product-import-export-for-woo',
                    'product_tags'=>'product-import-export-for-woo',
                    'user'=>'users-customers-import-export-for-wp-woocommerce',                                                          
                );
                foreach ($addon_modules_basic as $module_key => $module_path)
                {
                        if(is_plugin_active("{$module_path}/{$module_path}.php"))
                        {
                                $module_file=WP_CONTENT_DIR."/plugins/{$module_path}/admin/modules/$module_key/$module_key.php";
                                if(file_exists($module_file))
                            {
                            self::$existing_modules[]=$module_key;
                            require_once $module_file;
                            }
                        }		
                }

	}

	public static function module_exists($module)
	{
		return in_array($module, self::$existing_modules);
	}

	/**
	 * Envelope settings tab content with tab div.
	 * relative path is not acceptable in view file
	 */
	public static function envelope_settings_tabcontent($target_id,$view_file="",$html="",$variables=array(),$need_submit_btn=0)
	{
		extract($variables);
	?>
		<div class="wt-iew-tab-content" data-id="<?php echo $target_id;?>">
			<?php
			if($view_file!="" && file_exists($view_file))
			{
				include_once $view_file;
			}else
			{
				echo $html;
			}
			?>
			<?php 
			if($need_submit_btn==1)
			{
				include WT_O_IEW_PLUGIN_PATH."admin/views/admin-settings-save-button.php";
			}
			?>
		</div>
	<?php
	}

	/**
	*	Plugin page action links
	*/
	public function plugin_action_links($links)
	{
		$links[] = '<a href="'.admin_url('admin.php?page='.WT_IEW_PLUGIN_ID_BASIC).'">'.__('Settings').'</a>';
		$links[] = '<a href="https://www.webtoffee.com/" target="_blank">'.__('Documentation').'</a>';
		$links[] = '<a href="https://www.webtoffee.com/support/" target="_blank">'.__('Support').'</a>';
		return $links;
	}
}
}