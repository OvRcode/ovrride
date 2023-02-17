<?php
/**
 * Export section of the plugin
 *
 * @link            
 *
 * @package  Wt_Import_Export_For_Woo
 */
if (!defined('ABSPATH')) {
    exit;
}
if(!class_exists('Wt_Import_Export_For_Woo_Basic_Export')){
class Wt_Import_Export_For_Woo_Basic_Export
{
	public $module_id='';
	public static $module_id_static='';
	public $module_base='export';

	public static $export_dir=WP_CONTENT_DIR.'/webtoffee_export';
	public static $export_dir_name='/webtoffee_export';
	public $steps=array();
	public $allowed_export_file_type=array();
	
	private $to_export='';
	private $to_export_id='';
	private $rerun_id=0;
	public $export_method='';
	public $export_methods=array();
	public $selected_template=0;
	public $default_batch_count=0; /* configure this value in `advanced_setting_fields` method */
	public $selected_template_data=array();
	public $default_export_method='';  /* configure this value in `advanced_setting_fields` method */
	public $form_data=array();

	public function __construct()
	{
		$this->module_id=Wt_Import_Export_For_Woo_Basic::get_module_id($this->module_base);
		self::$module_id_static=$this->module_id;

		/* allowed file types */
		$this->allowed_export_file_type=array(
			'csv'=>__('CSV'),
			'xml'=>__('XML')
		);

		/* default step list */
		$this->steps=array
		(
			'post_type'=>array(
				'title'=>__('Select a post type'),
				'description'=>__('Export and download the respective post type into a CSV. This file can also be used to import data related to the specific post type back into your WordPress/WooCommerce site. As a first step you need to choose the post type to start the export.'),
			),
			'method_export'=>array(
				'title'=>__('Select an export method'),
				'description'=>__('Choose from the options below to continue with your export: quick export from DB, based on a pre-saved template or a new export with advanced options.'),
			),
			'filter'=>array(
				'title'=>__('Filter data'),
				'description'=>__('Filter data that needs to be exported as per the below criteria.'),
			), 
			'mapping'=>array(
				'title'=>__('Map and reorder export columns'),
				'description'=>__('The default export column names can be edited from the screen below, if required. If you have chosen a pre-saved template you can see the preferred names and choices that were last saved. You may also drag the columns accordingly to reorder them within the output file.'),
			),
			'advanced'=>array(
				'title'=>__('Advanced options/Batch export'),
				'description'=>__('You can save the template file for future exports or proceed with the export.'),
			),
		);


		$this->validation_rule=array(
			'post_type'=>array(), /* no validation rule. So default sanitization text */
			'method_export'=>array(
				'mapping_enabled_fields' => array('type'=>'text_arr') //in case of quick export
			)
		);

		$this->step_need_validation_filter=array('filter', 'advanced');


		$this->export_methods=array(
			'quick'=>array('title'=>__('Quick export'), 'description'=> __('Exports all the basic fields.')),
			'template'=>array('title'=>__('Pre-saved template'), 'description'=> __('Exports data as per the specifications(filters,selective column,mapping etc) from the previously saved file.')),
			'new'=>array('title'=>__('Advanced export'), 'description'=> __('Exports data after a detailed process of data filtering/column selection/advanced options that may be required for your export. You can also save this selection for future use.')),
		);

		/* advanced plugin settings */
		add_filter('wt_iew_advanced_setting_fields_basic', array($this, 'advanced_setting_fields'), 11);

		/* setting default values, this method must be below of advanced setting filter */
		$this->get_defaults();

		/* main ajax hook. The callback function will decide which is to execute. */
		add_action('wp_ajax_iew_export_ajax_basic', array($this, 'ajax_main'), 11);

		/* Admin menu for export */
		add_filter('wt_iew_admin_menu_basic', array($this, 'add_admin_pages'), 10, 1);

		/* Download export file via nonce URL */
		add_action('admin_init', array($this, 'download_file'), 11);
	}

	public function get_defaults()
	{	
		$this->default_export_method= Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings('default_export_method');
		$this->default_batch_count=Wt_Import_Export_For_Woo_Basic_Common_Helper::get_advanced_settings('default_export_batch');
	}

	/**
	*	Fields for advanced settings
	*
	*/
	public function advanced_setting_fields($fields)
	{
		$export_methods=array_map(function($vl){ return $vl['title']; }, $this->export_methods);
		$fields['default_export_method']=array(
			'label'=>__("Default Export method"),
			'type'=>'select',
			'sele_vals'=>$export_methods,
                        'value' =>'quick',
			'field_name'=>'default_export_method',
			'field_group'=>'advanced_field',
			'help_text'=>__('Select the default method of export.'),
		);
		$fields['default_export_batch']=array(
			'label'=>__("Default Export batch count"),
			'type'=>'number',
                        'value' =>30,
			'field_name'=>'default_export_batch',
			'help_text'=>__('Provide the default count for the records to be exported in a batch.'),
			'validation_rule'=>array('type'=>'absint'),
		);
		return $fields;
	}

	/**
	* Adding admin menus
	*/
	public function add_admin_pages($menus)
	{		
		$menu_temp=array(
			$this->module_base=>array(
				'menu',
				__('Export'),
				__('WebToffee Import Export (Basic)'),
				apply_filters('wt_import_export_allowed_capability', 'import'),
				$this->module_id,
				array($this,'admin_settings_page'),
				'dashicons-controls-repeat',
				56
			),
			$this->module_base.'-sub'=>array(
				'submenu',
				$this->module_id,
				__('Export'),
				__('Export'), 
				apply_filters('wt_import_export_allowed_capability', 'import'),
				$this->module_id,
				array($this, 'admin_settings_page')
			),
		);
		unset($menus['general-settings']);
		$menus=array_merge($menu_temp, $menus);
		return $menus;
	}

	/**
	* 	Export page
	*/
	public function admin_settings_page()
	{
		/**
		*	Check it is a rerun call
		*/
		$requested_rerun_id=(isset($_GET['wt_iew_rerun']) ? absint($_GET['wt_iew_rerun']) : 0);
		$this->_process_rerun($requested_rerun_id);

		$this->enqueue_assets();
		include plugin_dir_path(__FILE__).'views/main.php';
	}

	/**
	* 	Main ajax hook to handle all export related requests
	*/
	public function ajax_main()
	{       

		include_once plugin_dir_path(__FILE__).'classes/class-export-ajax.php';
		if(Wt_Iew_Sh::check_write_access(WT_IEW_PLUGIN_ID_BASIC))
		{
			/**
			*	Check it is a rerun call
			*/
			if(!$this->_process_rerun((isset($_POST['rerun_id']) ? absint($_POST['rerun_id']) : 0)))
			{	
				$this->export_method=(isset($_POST['export_method']) ? Wt_Iew_Sh::sanitize_item($_POST['export_method'], 'text') : '');
				$this->to_export=(isset($_POST['to_export']) ? Wt_Iew_Sh::sanitize_item($_POST['to_export'], 'text') : '');
				$this->selected_template=(isset($_POST['selected_template']) ? Wt_Iew_Sh::sanitize_item($_POST['selected_template'], 'int') : 0);
			}		
			
			$this->get_steps();

			$ajax_obj=new Wt_Import_Export_For_Woo_Basic_Export_Ajax($this, $this->to_export, $this->steps, $this->export_method, $this->selected_template, $this->rerun_id);
			
			$export_action=Wt_Iew_Sh::sanitize_item($_POST['export_action'], 'text');
			$data_type=Wt_Iew_Sh::sanitize_item($_POST['data_type'], 'text');
			
			$allowed_ajax_actions=array('get_steps', 'get_meta_mapping_fields', 'save_template', 'save_template_as', 'update_template', 'upload', 'export', 'export_image');

			$out=array(
				'status'=>0,
				'msg'=>__('Error'),
			);

			if(method_exists($ajax_obj, $export_action) && in_array($export_action, $allowed_ajax_actions))
			{
				$out=$ajax_obj->{$export_action}($out);
			}

			if($data_type=='json')
			{
				echo json_encode($out);
			}
		}
		exit();
	}

	public function get_filter_screen_fields($filter_form_data) {
            $filter_screen_fields = array(
                'limit' => array(
                    'label' => __("Limit"),
                    'value' => '',
                    'type' => 'number',
                    'field_name' => 'limit',
                    'placeholder' => 'Unlimited',
                    'help_text' => __('The actual number of records you want to export. e.g. A limit of 500 with an offset 10 will export records from 11th to 510th position.'),
                    'attr' => array('step' => 1, 'min' => 0),
                    'validation_rule' => array('type' => 'absint')
                ),
                'offset' => array(
                    'label' => __("Offset"),
                    'value' => '',
                    'field_name' => 'offset',
                    'placeholder' => __('0'),
                    'help_text' => __('Specify the number of records that should be skipped from the beginning. e.g. An offset of 10 skips the first 10 records.'),
                    'type' => 'number',
                    'attr' => array('step' => 1, 'min' => 0),
                    'validation_rule' => array('type' => 'absint')
                ),
            );
            $filter_screen_fields = apply_filters('wt_iew_exporter_alter_filter_fields_basic', $filter_screen_fields, $this->to_export, $filter_form_data);
            return $filter_screen_fields;
        }

        public function get_advanced_screen_fields($advanced_form_data)
	{
		$file_into_arr=array('local'=>__('Local'));

		/* taking available remote adapters */
		$remote_adapter_names=array();
		$remote_adapter_names=apply_filters('wt_iew_exporter_remote_adapter_names_basic', $remote_adapter_names);
		if($remote_adapter_names && is_array($remote_adapter_names))
		{
			foreach($remote_adapter_names as $remote_adapter_key => $remote_adapter_vl)
			{
				$file_into_arr[$remote_adapter_key]=$remote_adapter_vl;
			}
		}

		
		$advanced_screen_fields=array(

			'batch_count'=>array(
				'label'=>__("Export in batches of"),
				'type'=>'text',
                                'merge_right'=>true,
				'value'=>$this->default_batch_count,
				'field_name'=>'batch_count',
				'help_text'=>sprintf(__('The number of records that the server will process for every iteration within the configured timeout interval. If the export fails due to timeout you can lower this number accordingly and try again. Defaulted to %d records.'), 30),
				'validation_rule'=>array('type'=>'absint'),
			)
		);

		/* taking advanced fields from post type modules */
		$advanced_screen_fields=apply_filters('wt_iew_exporter_alter_advanced_fields_basic', $advanced_screen_fields, $this->to_export, $advanced_form_data);
		return $advanced_screen_fields;
	}

	/**
	* Get steps
	*
	*/
	public function get_steps()
	{
		if($this->export_method=='quick') /* if quick export then remove some steps */
		{
			$out=array(
				'post_type'=>$this->steps['post_type'],
				'method_export'=>$this->steps['method_export'],
				'advanced'=>$this->steps['advanced'],
			);
			$this->steps=$out;
		}
		$this->steps=apply_filters('wt_iew_exporter_steps_basic', $this->steps, $this->to_export);
		return $this->steps;
	}
	

	/**
	*	Validating and Processing rerun action
	*/
	protected function _process_rerun($rerun_id)
	{
		if($rerun_id>0)
		{
			/* check the history module is available */
			$history_module_obj=Wt_Import_Export_For_Woo_Basic::load_modules('history');
			if(!is_null($history_module_obj))
			{
				/* check the history entry is for export and also has form_data */
				$history_data=$history_module_obj->get_history_entry_by_id($rerun_id);
				if($history_data && $history_data['template_type']==$this->module_base)
				{
					$form_data=maybe_unserialize($history_data['data']);
					if($form_data && is_array($form_data))
					{
						$this->to_export=(isset($form_data['post_type_form_data']) && isset($form_data['post_type_form_data']['item_type']) ? $form_data['post_type_form_data']['item_type'] : '');
						if($this->to_export!="")
						{
							$this->export_method=(isset($form_data['method_export_form_data']) && isset($form_data['method_export_form_data']['method_export']) && $form_data['method_export_form_data']['method_export']!="" ?  $form_data['method_export_form_data']['method_export'] : $this->default_export_method);
							$this->rerun_id=$rerun_id;
							$this->form_data=$form_data;
							//process steps based on the export method in the history entry
							$this->get_steps();

							return true;
						}
					}
				}
			}
		}
		return false;
	}

	protected function enqueue_assets()
	{
            if(Wt_Import_Export_For_Woo_Basic_Common_Helper::wt_is_screen_allowed()){
		wp_enqueue_script($this->module_id, plugin_dir_url(__FILE__).'assets/js/main.js', array('jquery', 'jquery-ui-sortable', 'jquery-ui-datepicker'), WT_O_IEW_VERSION);
		wp_enqueue_style('jquery-ui-datepicker');
		wp_enqueue_style(WT_IEW_PLUGIN_ID_BASIC.'-jquery-ui', WT_O_IEW_PLUGIN_URL.'admin/css/jquery-ui.css', array(), WT_O_IEW_VERSION, 'all');
		$params=array(
			'item_type'=>'',
			'steps'=>$this->steps,
			'rerun_id'=>$this->rerun_id,
			'to_export'=>$this->to_export,
			'export_method'=>$this->export_method,
			'msgs'=>array(
				'choosed_template'=>__('Choosed template: '),
				'choose_export_method'=>__('Please select an export method.'),
				'choose_template'=>__('Please select an export template.'),
				'step'=>__('Step'),
				'choose_ftp_profile'=>__('Please select an FTP profile.'),
			),
		);
		wp_localize_script($this->module_id, 'wt_iew_export_basic_params', $params);

		$this->add_select2_lib(); //adding select2 JS, It checks the availibility of woocommerce
            }
	}

	/**
	* 
	* Enqueue select2 library, if woocommerce available use that
	*/
	protected function add_select2_lib()
	{
		/* enqueue scripts */
		if(!function_exists('is_plugin_active'))
		{
			include_once(ABSPATH.'wp-admin/includes/plugin.php');
		}
		if(is_plugin_active('woocommerce/woocommerce.php'))
		{ 
			wp_enqueue_script('wc-enhanced-select');
			wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url().'/assets/css/admin.css');
		}else
		{
			wp_enqueue_style(WT_IEW_PLUGIN_ID_BASIC.'-select2', WT_O_IEW_PLUGIN_URL. 'admin/css/select2.css', array(), WT_O_IEW_VERSION, 'all' );
			wp_enqueue_script(WT_IEW_PLUGIN_ID_BASIC.'-select2', WT_O_IEW_PLUGIN_URL.'admin/js/select2.js', array('jquery'), WT_O_IEW_VERSION, false );
		}
	}


	/**
	* Upload data to the user choosed remote method (Eg: FTP)
	* @param   string   $step       the action to perform, here 'upload'
	*
	* @return array 
	*/
	public function process_upload($step, $export_id, $to_export)
	{
		$out=array(
			'response'=>false,
			'export_id'=>0,
			'history_id'=>0, //same as that of export id
			'finished'=>0,
			'file_url'=>'',
			'msg'=>'',
		);

		if($export_id==0) //it may be an error
		{
			return $out;
		}

		//take history data by export_id
		$export_data=Wt_Import_Export_For_Woo_Basic_History::get_history_entry_by_id($export_id);
		if(is_null($export_data)) //no record found so it may be an error
		{
			return $out;
		}

		$form_data=maybe_unserialize($export_data['data']);

		//taking file name
		$file_name=(isset($export_data['file_name']) ? $export_data['file_name'] : '');
		
		$file_path=$this->get_file_path($file_name);
		if($file_path===false)
		{
			$update_data=array(
				'status'=>Wt_Import_Export_For_Woo_Basic_History::$status_arr['failed'],
				'status_text'=>'File not found.' //no need to add translation function
			);
			$update_data_type=array(
				'%d',
				'%s',
			);
			Wt_Import_Export_For_Woo_Basic_History::update_history_entry($export_id, $update_data, $update_data_type);

            return $out;
		}

		/* updating output parameters */
		$out['export_id']=$export_id;
		$out['history_id']=$export_id;
		$out['file_url']='';

		//check where to copy the files
		$file_into='local';
		if(isset($form_data['advanced_form_data']))
		{
			$file_into=(isset($form_data['advanced_form_data']['wt_iew_file_into']) ? $form_data['advanced_form_data']['wt_iew_file_into'] : 'local');
		}

		if('local' != $file_into) /* file not save to local. Initiate the choosed remote profile */
		{
			$remote_adapter=Wt_Import_Export_For_Woo_Basic::get_remote_adapters('export', $file_into);
			if(is_null($remote_adapter)) /* adapter object not found */
			{
				$msg=sprintf('Unable to initailize %s', $file_into);
				Wt_Import_Export_For_Woo_Basic_History::record_failure($export_id, $msg);
				$out['msg']=__($msg);
				return $out;
			}

			/* upload the file */
			$upload_out_format = array('response'=>true, 'msg'=>'');

			$advanced_form_data=(isset($form_data['advanced_form_data']) ? $form_data['advanced_form_data'] : array());

			$upload_data = $remote_adapter->upload($file_path, $file_name, $advanced_form_data, $upload_out_format);
			$out['response'] = (isset($upload_data['response']) ? $upload_data['response'] : false);
			$out['msg'] = (isset($upload_data['msg']) ? $upload_data['msg'] : __('Error'));

			//unlink the local file
			@unlink($file_path);
		}else
		{
			$out['response']=true;
			$out['file_url']=html_entity_decode($this->get_file_url($file_name));
		}

		$out['finished']=1;  //if any error then also its finished, but with errors
		if($out['response'] === true) //success
		{
			$out['msg']=__('Finished');
			
			/* updating finished status */
			$update_data=array(
				'status'=>1  //success
			);
			$update_data_type=array(
				'%d'
			);
			Wt_Import_Export_For_Woo_Basic_History::update_history_entry($export_id, $update_data, $update_data_type);

		}else //failed
		{
			//no need to add translation function in message
			Wt_Import_Export_For_Woo_Basic_History::record_failure($export_id, 'Failed while uploading');
		}
		return $out;
	}


	/**
	* 	Do the export process
	*/
	public function process_action($form_data, $step, $to_process, $file_name='', $export_id=0, $offset=0)
	{
		$out=array(
			'response'=>false,
			'new_offset'=>0,
			'export_id'=>0,
			'history_id'=>0, //same as that of export id
			'total_records'=>0,
			'finished'=>0,
			'file_url'=>'',
			'msg'=>'',
		);

		/* prepare form_data, If this was not first batch */
		if($export_id>0)
		{
			//take history data by export_id
			$export_data=Wt_Import_Export_For_Woo_Basic_History::get_history_entry_by_id($export_id);
			if(is_null($export_data)) //no record found so it may be an error
			{
				return $out;
			}

			//processing form data
			$form_data=(isset($export_data['data']) ? maybe_unserialize($export_data['data']) : array());
		}
		$this->to_export=$to_process;
		$default_batch_count=$this->_get_default_batch_count($form_data);
		$batch_count=$default_batch_count;		
		$file_as='csv';
		$csv_delimiter=',';
		$total_records=0;
		if(isset($form_data['advanced_form_data']))
		{
			$batch_count=(isset($form_data['advanced_form_data']['wt_iew_batch_count']) ? $form_data['advanced_form_data']['wt_iew_batch_count'] : $batch_count);
			$file_as=(isset($form_data['advanced_form_data']['wt_iew_file_as']) ? $form_data['advanced_form_data']['wt_iew_file_as'] : 'csv');
			$csv_delimiter=(isset($form_data['advanced_form_data']['wt_iew_delimiter']) ? $form_data['advanced_form_data']['wt_iew_delimiter'] : ',');
			$csv_delimiter=($csv_delimiter=="" ? ',' : $csv_delimiter);
		}		
		$file_as=(isset($this->allowed_export_file_type[$file_as]) ? $file_as : 'csv');

		
		$generated_file_name=$this->to_export.'_export_'.date('Y-m-d-h-i-s').'.'.$file_as;

		if($export_id==0) //first batch then create a history entry
		{
			$file_name=($file_name=="" ? $generated_file_name : sanitize_file_name($file_name.'.'.$file_as));
			$export_id=Wt_Import_Export_For_Woo_Basic_History::create_history_entry($file_name, $form_data, $this->to_export, $step);
			$offset=0;
		}else
		{
			//taking file name from export data
			$file_name=(isset($export_data['file_name']) ? $export_data['file_name'] : $generated_file_name);		
			$total_records=(isset($export_data['total']) ? $export_data['total'] : 0);
		}

		/* setting history_id in Log section */
		Wt_Import_Export_For_Woo_Basic_Log::$history_id=$export_id;


		$file_path=$this->get_file_path($file_name);
		if($file_path===false)
		{
			$msg='Unable to create backup directory. Please grant write permission for `wp-content` folder.';		
			
			//no need to add translation function in message
			Wt_Import_Export_For_Woo_Basic_History::record_failure($export_id, $msg);

            $out['msg']=__($msg);
            return $out;
		}

		/* giving full data */
		$form_data=apply_filters('wt_iew_export_full_form_data_basic', $form_data, $to_process, $step, $this->selected_template_data);

		/* hook to get data from corresponding module. Eg: product, order */
		$export_data=array(
			'total'=>100,
			'head_data'=>array("abc"=>"hd1", "bcd"=>"hd2", "cde"=>"hd3", "def"=>"hd4"),
			'body_data'=>array(
				array("abc"=>"Abc1", "bcd"=>"Bcd1", "cde"=>"Cde1", "def"=>"Def1"),
  				array("abc"=>"Abc2", "bcd"=>"Bcd2", "cde"=>"Cde2", "def"=>"Def2")
			),
		); 		

		/* in scheduled export. The export method will not available so we need to take it from form_data */
		$form_data_export_method=(isset($form_data['method_export_form_data']) && isset($form_data['method_export_form_data']['method_export']) ?  $form_data['method_export_form_data']['method_export'] : $this->default_export_method);
		$this->export_method=($this->export_method=="" ? $form_data_export_method : $this->export_method);

		$export_data=apply_filters('wt_iew_exporter_do_export_basic', $export_data, $to_process, $step, $form_data, $this->selected_template_data, $this->export_method, $offset);
		if($offset==0)
		{
			$total_records=intval(isset($export_data['total']) ? $export_data['total'] : 0);
		}
		$this->_update_history_after_export($export_id, $offset, $total_records, $export_data);

		/* checking action is finshed */
		$is_last_offset=false;
		$new_offset=$offset+$batch_count; //increase the offset
		if($new_offset>=$total_records) //finished
		{
			$is_last_offset=true;
		}


		/* no data from corresponding module */
		if(!$export_data) //error !!!
		{
			//return $out;
		}else
		{

				include_once WT_O_IEW_PLUGIN_PATH.'admin/classes/class-csvwriter.php';
				$writer=new Wt_Import_Export_For_Woo_Basic_Csvwriter($file_path, $offset, $csv_delimiter);

                                                
                        /**
			*	Alter export data before writing to file.
			*	@param 	array 	$export_data  		data to export
			*	@param 	int 	$offset  			current offset
			*	@param 	boolean $is_last_offset 	is current offset is last one
			*	@param 	string 	$file_as 			file type to write Eg: XML, CSV
			*	@param 	string 	$to_export 			Post type
			*	@param 	string 	$csv_delimiter 		CSV delimiter. In case of CSV export
			*	@return array 	$export_data 		Altered export data
			*/
			$export_data=apply_filters('wt_iew_alter_export_data_basic', $export_data, $offset, $is_last_offset, $file_as, $this->to_export, $csv_delimiter);                        
                        
			$writer->write_to_file($export_data, $offset, $is_last_offset, $this->to_export);
		}
		
		/* updating output parameters */
		$out['total_records']=$total_records;
		$out['export_id']=$export_id;
		$out['history_id']=$export_id;
		$out['file_url']='';
		$out['response']=true;

		/* updating action is finshed */	
		if($is_last_offset) //finished
		{
			//check where to copy the files
			$file_into='local';
			if(isset($form_data['advanced_form_data']))
			{
				$file_into=(isset($form_data['advanced_form_data']['wt_iew_file_into']) ? $form_data['advanced_form_data']['wt_iew_file_into'] : 'local');
			}
			if('local' != $file_into) /* file not save to local. Initiate the choosed remote profile */
			{
				$out['finished']=2; //file created, next upload it

				$out['msg']=sprintf(__('Uploading to %s'), $file_into);
			}else
			{
				$out['file_url']=html_entity_decode($this->get_file_url($file_name));
				$out['finished']=1; //finished

				$msg = __('Export file processing completed');
                                $msg.='<span class="wt_iew_popup_close" style="line-height:10px;width:auto" onclick="wt_iew_basic_export.hide_export_info_box();">X</span>';
                                
                                $msg.='<span class="wt_iew_info_box_finished_text" style="font-size: 10px; display:block">';
                                if(Wt_Import_Export_For_Woo_Admin_Basic::module_exists('history'))
                                {
                                        $history_module_id= Wt_Import_Export_For_Woo_Basic::get_module_id('history');
                                        $history_page_url=admin_url('admin.php?page='.$history_module_id);
                                        $msg.=__('You can manage exports from History section.');
                                }
                                
                                $msg.='<a class="button button-secondary" style="margin-top:10px;" onclick="wt_iew_basic_export.hide_export_info_box();" target="_blank" href="'.$out['file_url'].'" >'.__('Download file').'</a></span>';

				$out['msg']=$msg;
				
				/* updating finished status */
				$update_data=array(
					'status'=>Wt_Import_Export_For_Woo_Basic_History::$status_arr['finished'],
					'status_text'=>'Finished' //translation function not needed
				);
				$update_data_type=array(
					'%d',
					'%s',
				);
				Wt_Import_Export_For_Woo_Basic_History::update_history_entry($export_id,$update_data,$update_data_type);
			}

		}else
		{
			$out['new_offset']=$new_offset;
			$out['msg']=sprintf(__('Exporting...(%d out of %d)'), $new_offset, $total_records);
		}
		return $out;
	}


	public static function get_file_path($file_name)
	{
		if(!is_dir(self::$export_dir))
        {
            if(!mkdir(self::$export_dir, 0700))
            {
            	return false;
            }else
            {
            	$files_to_create=array('.htaccess' => 'deny from all', 'index.php'=>'<?php // Silence is golden');
		        foreach($files_to_create as $file=>$file_content)
		        {
		        	if(!file_exists(self::$export_dir.'/'.$file))
			        {
			            $fh=@fopen(self::$export_dir.'/'.$file, "w");
			            if(is_resource($fh))
			            {
			                fwrite($fh, $file_content);
			                fclose($fh);
			            }
			        }
		        } 
            }
        }
        return self::$export_dir.'/'.$file_name;
	}

	/**
	*  	Download file via a nonce URL
	*/
	public function download_file()
	{
		if(isset($_GET['wt_iew_export_download']))
		{ 
			if(Wt_Iew_Sh::check_write_access(WT_IEW_PLUGIN_ID_BASIC)) /* check nonce and role */
			{
				$file_name=(isset($_GET['file']) ? sanitize_file_name($_GET['file']) : '');
				if($file_name!="")
				{
					$file_arr=explode(".", $file_name);
					$file_ext=end($file_arr);
					if(isset($this->allowed_export_file_type[$file_ext]) || $file_ext=='zip') /* Only allowed files. Zip file in image export */
					{
						$file_path=self::$export_dir.'/'.$file_name;
						if(file_exists($file_path) && is_file($file_path)) /* check existence of file */
						{	
							header('Pragma: public');
						    header('Expires: 0');
						    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
						    header('Cache-Control: private', false);
						    header('Content-Transfer-Encoding: binary');
						    header('Content-Disposition: attachment; filename="'.$file_name.'";');
						    header('Content-Description: File Transfer');
						    header('Content-Type: application/octet-stream');
						    //header('Content-Length: '.filesize($file_path));

						    $chunk_size=1024 * 1024;
						    $handle=@fopen($file_path, 'rb');
						    while(!feof($handle))
						    {
						        $buffer = fread($handle, $chunk_size);
						        echo $buffer;
						        ob_flush();
						        flush();
						    }
						    fclose($handle);
						    exit();

						}
					}
				}	
			}
		}
	}

	private function _update_history_after_export($export_id, $offset, $total_records, $export_data)
	{
		/* we need to update total record count on first batch */
		if($offset==0)
		{
			$update_data=array(
				'total'=>$total_records
			);			
		}else
		{
			/* updating completed offset */
			$update_data=array(
				'offset'=>$offset
			);
		}
		$update_data_type=array(
			'%d'
		);
		Wt_Import_Export_For_Woo_Basic_History::update_history_entry($export_id, $update_data, $update_data_type);
	}

	private function _get_default_batch_count($form_data)
	{
		$default_batch_count=absint(apply_filters('wt_iew_exporter_alter_default_batch_count_basic', $this->default_batch_count, $this->to_export, $form_data));
		$form_data=null;
		unset($form_data);
		return ($default_batch_count==0 ? $this->default_batch_count : $default_batch_count);
	}

	/**
	*	Generating downloadable URL for a file
	*/
	private function get_file_url($file_name)
	{
		return wp_nonce_url(admin_url('admin.php?wt_iew_export_download=true&file='.$file_name), WT_IEW_PLUGIN_ID_BASIC);
	}
}
}
Wt_Import_Export_For_Woo_Basic::$loaded_modules['export']=new Wt_Import_Export_For_Woo_Basic_Export();