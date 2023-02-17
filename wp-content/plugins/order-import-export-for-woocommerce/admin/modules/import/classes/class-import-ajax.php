<?php
/**
 * Ajax section of the Import module
 *
 * @link        
 *
 * @package  Wt_Import_Export_For_Woo
 */
if (!defined('ABSPATH')) {
    exit;
}

if(!class_exists('Wt_Import_Export_For_Woo_Basic_Import_Ajax')){
class Wt_Import_Export_For_Woo_Basic_Import_Ajax
{
	public $step='';
	public $steps=array();
	public $step_btns=array();
	public $import_method='';
	public $to_import='';

	protected $step_title='';
	protected $step_keys=array();
	protected $current_step_index=0;
	protected $current_step_number=1;
	protected $last_page=false;
	protected $total_steps=0;
	protected $step_summary='';
	protected $step_description='';
	protected $mapping_enabled_fields=array();
	protected $mapping_templates=array();
	protected $selected_template=0;
	protected $selected_template_form_data=array();
	protected $import_obj=null;
	protected $field_group_prefixes=array();
	protected $rerun_id=0;

	public function __construct($import_obj, $to_import, $steps, $import_method, $selected_template, $rerun_id)
	{
		$this->import_obj=$import_obj;
		$this->to_import=$to_import;
		$this->steps=$steps;
		$this->import_method=$import_method;
		$this->selected_template=$selected_template;
		$this->rerun_id=$rerun_id;

		/**
		*  This array is to group the fields in the input file that are not in the default list.
		*/
		$this->field_group_prefixes=array(
			'taxonomies'=>array('tax'),
			'meta'=>array('meta'),
			'attributes'=>array('attribute', 'attribute_data', 'attribute_default', 'meta:attribute'),
			'hidden_meta'=>array('meta:_'),
		);
	}

	/** 
	*	Ajax main function to retrive steps HTML 
	*/
	public function get_steps($out)
	{
		//sleep(3);
		$steps=(is_array($_POST['steps']) ? $_POST['steps'] : array($_POST['steps']));
		$steps=Wt_Iew_Sh::sanitize_item($steps, 'text_arr');
		$page_html=array();

		if($this->selected_template>0) /* taking selected tamplate formdata */
		{
			$this->get_template_form_data($this->selected_template);

		}elseif($this->rerun_id>0)
		{
			$this->selected_template_form_data=$this->import_obj->form_data;
		}

		foreach($steps as $step)
		{
			$method_name=$step.'_page';
			if(method_exists($this, $method_name))
			{
				$page_html[$step]=$this->{$method_name}();
				
				if($step=='method_import' && ($this->selected_template>0 || $this->rerun_id>0))
				{
					$out['template_data']=$this->selected_template_form_data;
				}
			}
		}
		$out['status']=1;
		$out['page_html']=$page_html;
		return $out;
	}

	/**
	*	Delete uploaded import file
	*
	*/
	public function delete_import_file($out)
	{
		$file_url=(isset($_POST['file_url']) ? esc_url_raw($_POST['file_url']) : '');
                $mapping_profile=(isset($_POST['mapping_profile']) ? ($_POST['mapping_profile']) : '');
		$out['file_url']=$file_url;
		if($file_url!="" )
		{
                    if(!$mapping_profile){
                      $this->import_obj->delete_import_file($file_url);
                    }
			$out['status']=1;
			$out['msg']='';
		}
		return $out;
	}

	/**
	*	Upload import file (Drag and drop  upload)
	*
	*/
	public function upload_import_file($out)
	{
		if(isset($_FILES['wt_iew_import_file']))
		{

			$is_file_type_allowed=false;
			if(!in_array($_FILES['wt_iew_import_file']['type'], $this->import_obj->allowed_import_file_type_mime)) /* Not allowed file type. [Bug fix for Windows OS]Then verify it again with file extension */
			{
				$ext=pathinfo($_FILES['wt_iew_import_file']['name'], PATHINFO_EXTENSION);
				if(isset($this->import_obj->allowed_import_file_type_mime[$ext])) /* extension exists. */
				{
					$is_file_type_allowed=true;
				}
			}else
			{
				$is_file_type_allowed=true;
			}

			if($is_file_type_allowed) /* Allowed file type */
			{

				@set_time_limit(3600); // 1 hour 

				$max_bytes=($this->import_obj->max_import_file_size*1000000); //convert to bytes
				if($max_bytes>=$_FILES['wt_iew_import_file']['size'])
				{
					$file_name='local_file_'.time().'_'.sanitize_file_name($_FILES['wt_iew_import_file']['name']); //sanitize the file name, add a timestamp prefix to avoid conflict
					$file_path=$this->import_obj->get_file_path($file_name);
                                        // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
					if(@move_uploaded_file($_FILES['wt_iew_import_file']['tmp_name'], $file_path))
					{
						$out['msg']='';
						$out['status']=1;
						$out['url']=$this->import_obj->get_file_url($file_name);

						/**
						*	Check old file exists, and delete it
						*/
						$file_url=(isset($_POST['file_url']) ? esc_url_raw($_POST['file_url']) : '');
                                                $map_profile_id=(isset($_POST['map_profile_id']) ? ($_POST['map_profile_id']) : '');
						if($file_url!="" && !$map_profile_id)
						{
							$this->import_obj->delete_import_file($file_url);
						}
					}else
					{
						$out['msg']=__('Unable to upload file. Please check write permission of your `wp-content` folder.');
					}
				}else
				{
					$out['msg']=sprintf(__('File size exceeds the limit. %dMB max'), $this->import_obj->max_import_file_size);
				}
			}else
			{
				$out['msg']=sprintf(__('Invalid file type. Only %s are allowed.'), implode(", ", array_values($this->import_obj->allowed_import_file_type)));
			}
		}

		return $out;
	}

	/**
	* 	Ajax hook to download the input file as temp file and validate its extension.
	*/
	public function validate_file($out)
	{
		/* process form data */
		$form_data=(isset($_POST['form_data']) ? Wt_Import_Export_For_Woo_Basic_Common_Helper::process_formdata(maybe_unserialize(($_POST['form_data']))) : array());
		$response=$this->import_obj->download_remote_file($form_data);

		if($response['response']) /* temp file created. Then delete old temp file if exists */
		{
			$temp_import_file=(isset($_POST['temp_import_file']) ? sanitize_file_name($_POST['temp_import_file']) : '');
			if($temp_import_file!="")
			{
				$file_path=$this->import_obj->get_file_path($temp_import_file);
				if(file_exists($file_path))
				{
					@unlink($file_path);
				}
			}
		}
			
		$out['status']=($response['response'] ? 1 : 0);
		$out['msg']=($response['msg']!="" ? $response['msg'] : $out['msg']);
		$out['file_name']=(isset($response['file_name']) ? $response['file_name'] : '');

		return $out;
	}

	/**
	* 	Ajax function to retrive meta step data
	*/
	public function get_meta_mapping_fields($out)
	{
		if($this->selected_template>0) /* taking selected tamplate formdata */
		{
			$this->get_template_form_data($this->selected_template);

		}elseif($this->rerun_id>0)
		{
			$this->selected_template_form_data=$this->import_obj->form_data;
		}

		/* This is the sample data from input file */
		$file_heading_meta_fields=(isset($_POST['file_head_meta']) ? json_decode(stripslashes($_POST['file_head_meta']), true) : array());	
		
		//taking current page form data
		$meta_step_form_data=(isset($this->selected_template_form_data['meta_step_form_data']) ? $this->selected_template_form_data['meta_step_form_data'] : array());

		/* formdata/template data of fields in mapping page */
		$form_data_meta_mapping_fields=isset($meta_step_form_data['mapping_fields']) ? $meta_step_form_data['mapping_fields'] : array();


		$meta_mapping_screen_fields=$this->_get_meta_mapping_screen_fields($form_data_meta_mapping_fields);
		
		$draggable_tooltip=__("Drag to rearrange the columns");
		$module_url=plugin_dir_url(dirname(__FILE__));


		/* preparing meta fields. */
		$prepared_meta_fields=array();
		if($meta_mapping_screen_fields && is_array($meta_mapping_screen_fields))
		{
			/* loop through mapping fields */
			foreach($meta_mapping_screen_fields as $meta_mapping_screen_field_key=>$meta_mapping_screen_field_val)
			{
				/* decalaring an empty array*/
				$temp_arr=array();

				/* current field group(tax, meta) formdata */
				$current_meta_step_form_data=(isset($form_data_meta_mapping_fields[$meta_mapping_screen_field_key]) ? $form_data_meta_mapping_fields[$meta_mapping_screen_field_key] : array());

				/* default field list from post type module */
				$mapping_fields=((isset($meta_mapping_screen_field_val['fields']) && is_array($meta_mapping_screen_field_val['fields'])) ? $meta_mapping_screen_field_val['fields'] : array());

				/* loop through form data */
				foreach($current_meta_step_form_data as $key=>$val_arr) /* looping the template form data */
				{
					$val=$val_arr[0]; /* normal column val */
					$checked=$val_arr[1]; /* import this column? */
					
					if(isset($mapping_fields[$key])) /* found in default field list */
					{
						$label=(isset($mapping_fields[$key]['title']) ? $mapping_fields[$key]['title'] : '');
						$description=(isset($mapping_fields[$key]['description']) ? $mapping_fields[$key]['title'] : '');
						$type=(isset($mapping_fields[$key]['type']) ? $mapping_fields[$key]['type'] : '');
						unset($mapping_fields[$key]); //remove the field from default list
						
						if(isset($file_heading_meta_fields[$key])) /* also found in file heading list */
						{
							unset($file_heading_meta_fields[$key]); //remove the field from file heading list
						}
						$temp_arr[$key]=array('label'=>$label, 'description'=>$description, 'val'=>$val, 'checked'=>$checked, 'type'=>$type);
					}
					elseif(isset($file_heading_meta_fields[$key])) /* found in file heading list */
					{
						$label=$key;
						$description=$this->prepare_field_description($key);
						$type='';
						unset($file_heading_meta_fields[$key]); //remove the field from file heading list
						$temp_arr[$key]=array('label'=>$label, 'description'=>$description, 'val'=>$val, 'checked'=>$checked, 'type'=>$type);	
					}
				}

				/* loop through mapping fields */
				if(count($mapping_fields)>0)
				{
					foreach($mapping_fields as $key=>$val_arr)
					{
						$label=(isset($val_arr['title']) ? $val_arr['title'] : '');
						$description=(isset($val_arr['description']) ? $val_arr['description'] : '');
						$type=(isset($val_arr['type']) ? $val_arr['type'] : '');
						$val='';
						$checked=0; /* import this column? */
						if(isset($file_heading_meta_fields[$key]))
						{
							$checked=1; /* import this column? */
							$val='{'.$key.'}';
							unset($file_heading_meta_fields[$key]); //remove the field from file heading list
						}
						$temp_arr[$key]=array('label'=>$label, 'description'=>$description, 'val'=>$val, 'checked'=>$checked, 'type'=>$type);
					}
				}
				
				if(count($file_heading_meta_fields)>0)
				{
					$current_field_group_prefix_arr=(isset($this->field_group_prefixes[$meta_mapping_screen_field_key]) ? $this->field_group_prefixes[$meta_mapping_screen_field_key] : array());
					foreach($file_heading_meta_fields as $key=>$sample_val)
					{
						$is_include=$this->_is_include_meta_in_this_group($current_field_group_prefix_arr, $key);						
						if($is_include==1)
						{
							$label=Wt_Iew_Sh::sanitize_item($key);
							$description=$this->prepare_field_description($key);
							$type='';
							$val='{'.$key.'}';
							$checked=1; /* import this column? */
							unset($file_heading_meta_fields[$key]); //remove the field from file heading list
							$temp_arr[$key]=array('label'=>$label, 'description'=>$description, 'val'=>$val, 'checked'=>$checked, 'type'=>$type);
						}
					}
				}

				/* adding value to main array */
				$prepared_meta_fields[$meta_mapping_screen_field_key]=array('fields'=>$temp_arr, 'checked'=>(isset($meta_mapping_screen_field_val['checked']) && $meta_mapping_screen_field_val['checked']==1 ? 1 : 0) );
			}

			/* if any columns that not in the above list */
			if(count($file_heading_meta_fields)>0)
			{
				//do something
			}
		}

		/* prepare HTML for meta mapping step */
		$meta_html=array();

		/* loop through prepared meta fields */
		foreach($prepared_meta_fields as $meta_mapping_screen_field_key=>$meta_mapping_screen_field_val)
		{	
			ob_start();							
			include dirname(plugin_dir_path(__FILE__)).'/views/_import_meta_step_page.php';
			$meta_html[$meta_mapping_screen_field_key]=ob_get_clean();
		}

		$out['status']=1;
		$out['meta_html']=$meta_html;
		return $out;
	}

	public function save_template($out)
	{
		return $this->do_save_template('save', $out);
	}

	public function save_template_as($out)
	{
		return $this->do_save_template('save_as', $out);
	}

	public function update_template($out)
	{
		return $this->do_save_template('update', $out);
	}

	/**
	*	Download the input file and create history entry. 
	*	This is the primary step before Import
	*	On XML import the file will be converted to CSV (Batch processing)
	*/
	public function download($out)
	{
		$this->import_obj->temp_import_file=(isset($_POST['temp_import_file']) ? sanitize_file_name($_POST['temp_import_file']) : '');

		$offset=(isset($_POST['offset']) ? floatval($_POST['offset']) : 0);
		$import_id=(isset($_POST['import_id']) ? intval($_POST['import_id']) : 0);
		$import_method=(isset($_POST['import_method']) ? sanitize_text_field($_POST['import_method']) : $this->import_obj->default_import_method);

		if($offset==0)
		{
			/* process form data */
			$form_data=(isset($_POST['form_data']) ? Wt_Import_Export_For_Woo_Basic_Common_Helper::process_formdata(maybe_unserialize(($_POST['form_data']))) : array());

			//sanitize form data
			$form_data=Wt_Iew_IE_Basic_Helper::sanitize_formdata($form_data, $this->import_obj);
		}else
		{
			/* no need to process the formdata steps other than first */ 
			$form_data=array();
		}

		$out=$this->import_obj->process_download($form_data, 'download', $this->to_import, $import_id, $offset);
		if($out['response']===true)
		{			
			$import_id=$out['import_id'];

			/**
			* 	Prepare default mapping data for quick import
			*	After preparing update the Formdata in history table
			*/
			if($import_method=='quick' && $import_id>0 && $out['finished']==3)
			{
				$this->_prepare_for_quick($import_id);
			}


			$out['status']=1;
		}else
		{
			$out['status']=0;
		}
		return $out;
	}

	/**
	* Process the import
	*
	* @return array 
	*/
	public function import($out)
	{
		$offset=(isset($_POST['offset']) ? floatval($_POST['offset']) : 0);
		$import_id=(isset($_POST['import_id']) ? intval($_POST['import_id']) : 0);

		/* no need to send formdata. It will take from history table by `process_action` method */
		$form_data=array();

		/* do the export process */
		$out=$this->import_obj->process_action($form_data, 'import', $this->to_import, '', $import_id, $offset);
		if($out['response']===true)
		{			
			$out['status']=1;
		}else
		{
			$out['status']=0;
		}
		return $out;
	}

	/**
	* Save/Update template (Ajax sub function)
	* @param boolean $is_update is update existing template or save as new
	* @return array response status, name, id
	*/
	public function do_save_template($step, $out)
	{
		$is_update=($step=='update' ? true : false);

		/* take template name from post data, if not then create from time stamp */
		$template_name=(isset($_POST['template_name']) ? sanitize_text_field($_POST['template_name']) : date('d-M-Y h:i:s A'));
		
		$template_name = stripslashes($template_name);
		$out['name']= $template_name;
		$out['id']=0;
		$out['status']=1;

		if($this->to_import!='')
		{
			global $wpdb;

			/* checking: just saved and again click the button so shift the action as update */
			if($step=='save' && $this->selected_template>0) 
			{
				$is_update=true;
			}

			/* checking template with same name exists */
			$template_data=$this->get_mapping_template_by_name($template_name);
			if($template_data)
			{
				$is_throw_warn=false;
				if($is_update)
				{
					if($template_data['id']!=$this->selected_template)
					{
						$is_throw_warn=true;
					}	
				}else
				{
					$is_throw_warn=true;
				}

				if($is_throw_warn)
				{
					$out['status']=0;
					if($step=='save_as')
					{
						$out['msg']=__('Please enter a different name');
					}else
					{
						$out['msg']=__('Template with same name already exists');	
					}
					return $out;
				}		
			}			

			$tb=$wpdb->prefix.Wt_Import_Export_For_Woo_Basic::$template_tb;
			
			/* process form data */
			$form_data=(isset($_POST['form_data']) ? Wt_Import_Export_For_Woo_Basic_Common_Helper::process_formdata(maybe_unserialize(($_POST['form_data']))) : array());

			//sanitize form data
			$form_data=Wt_Iew_IE_Basic_Helper::sanitize_formdata($form_data, $this->import_obj);

			/* upadte the template */
			if($is_update)
			{ 
							
				$update_data=array(
					'data'=>maybe_serialize($form_data),
					'name'=>$template_name, //may be a rename
				);
				$update_data_type=array(
					'%s',
					'%s'
				);
				$update_where=array(
					'id'=>$this->selected_template
				);
				$update_where_type=array(
					'%d'
				);
				if($wpdb->update($tb, $update_data, $update_where, $update_data_type, $update_where_type)!==false)
				{
					$out['id']=$this->selected_template;
					$out['msg']=__('Template updated successfully');
					$out['name']=$template_name;
					return $out;
				}
			}else
			{
				$insert_data=array(
					'template_type'=>'import',
					'item_type'=>$this->to_import,
					'name'=>$template_name,
					'data'=>maybe_serialize($form_data),
				);
				$insert_data_type=array(
					'%s','%s','%s','%s'
				);
				if($wpdb->insert($tb, $insert_data, $insert_data_type)) //success
				{
					$out['id']=$wpdb->insert_id;
					$out['msg']=__('Template saved successfully');
					return $out;
				}
			}
		}
		$out['status']=0;
		return $out;
	}

	/*
	 * Get step information
	 * @param string $step
	 */

	public function get_step_info( $step ) {
		return isset( $this->steps[ $step ] ) ? $this->steps[ $step ] : array( 'title' => ' ', 'description' => ' ' );
	}
	/**
	*  Step 1 (Ajax sub function)
	*  Built in steps, post type choosing page
	*/
	public function post_type_page()
	{
		$post_types=apply_filters('wt_iew_importer_post_types_basic', array());
		$post_types=(!is_array($post_types) ? array() : $post_types);
		$this->step='post_type';
		
		$this->prepare_step_summary();
		$this->prepare_footer_button_list();

		ob_start();		
		$this->prepare_step_header_html();
		include_once dirname(plugin_dir_path(__FILE__)).'/views/_import_post_type_page.php';
		$this->prepare_step_footer_html();
		return ob_get_clean();
	}

	/**
	*  Step 2 (Ajax sub function)
	* Built in steps, import method choosing page
	*/
	public function method_import_page()
	{
		$this->step='method_import';
		if($this->to_import!="")
		{
			/* setting a default import method */
			$this->import_method=($this->import_method=='' ? $this->import_obj->default_import_method : $this->import_method);
			$this->import_obj->import_method=$this->import_method;
			$this->steps=$this->import_obj->get_steps();

			$this->prepare_step_summary();
			$this->prepare_footer_button_list();

			//taking current page form data
			$method_import_form_data=(isset($this->selected_template_form_data['method_import_form_data']) ? $this->selected_template_form_data['method_import_form_data'] : array());

			$method_import_screen_fields=$this->import_obj->get_method_import_screen_fields($method_import_form_data);
			

			$form_data_import_template=$this->selected_template;
			if($this->rerun_id>0)
			{
				if(isset($method_import_form_data['selected_template']))
				{
					/* do not set this value to `$this->selected_template` */
					$form_data_import_template=$method_import_form_data['selected_template'];
				}
			}

			/* meta field list for quick import */
			$this->get_mapping_templates();

			ob_start();		
			$this->prepare_step_header_html();
			include_once dirname(plugin_dir_path(__FILE__)).'/views/_import_method_import_page.php';
			$this->prepare_step_footer_html();
			return ob_get_clean();
		}else
		{
			return '';
		}
	}

	/**
	*  Step 3 (Ajax sub function)
	* Built in steps, Import mapping page
	*/
	public function mapping_page()
	{
		$this->step='mapping';
		if($this->to_import!="")
		{
			$this->prepare_step_summary();
			$this->prepare_footer_button_list();

			
			$temp_import_file=(isset($_POST['temp_import_file']) ? sanitize_file_name($_POST['temp_import_file']) : '');
			$file_path=$this->import_obj->get_file_path($temp_import_file);
			if($temp_import_file!="" && file_exists($file_path))
			{
				$ext_arr=explode('.', $temp_import_file);
				$ext= strtolower(end($ext_arr));
				if(isset($this->import_obj->allowed_import_file_type[$ext])) /* file type is in allowed list */ 
				{
//					if($ext=='xml')
//					{
//						include_once WT_O_IEW_PLUGIN_PATH.'admin/classes/class-xmlreader.php';
//						$reader=new Wt_Import_Export_For_Woo_Basic_Xmlreader();
//					}else  /* csv */
//					{
						include_once WT_O_IEW_PLUGIN_PATH.'admin/classes/class-csvreader.php';
						$delimiter=(isset($_POST['delimiter']) ? ($_POST['delimiter']) : ','); //no sanitization 
						$reader=new Wt_Import_Export_For_Woo_Basic_Csvreader($delimiter);
//					}
                                        
					/* take first two rows in csv and in xml takes column keys and a sample data */
					$sample_data=$reader->get_sample_data($file_path, true);                                        

					$file_heading_data=$this->process_file_heading_data($sample_data);
					$file_heading_default_fields=$file_heading_data['default'];
					$file_heading_meta_fields=$file_heading_data['meta'];

					
					//taking current page form data
					$mapping_form_data=(isset($this->selected_template_form_data['mapping_form_data']) ? $this->selected_template_form_data['mapping_form_data'] : array());	
	
					/* formdata/template data of fields in mapping page */
					$form_data_mapping_fields=isset($mapping_form_data['mapping_fields']) ? $mapping_form_data['mapping_fields'] : array();
			
					/**
					*	default mapping page fields 
					*	Format: 'field_key'=>array('title'=>'', 'description'=>'')
					*/
					$mapping_fields=array();
					$mapping_fields=apply_filters('wt_iew_importer_alter_mapping_fields_basic', $mapping_fields, $this->to_import, $form_data_mapping_fields);

					/* meta fields list */
					$this->get_mapping_enabled_fields();

					/* mapping enabled meta fields */
					$form_data_mapping_enabled_fields=(isset($mapping_form_data['mapping_enabled_fields']) ? $mapping_form_data['mapping_enabled_fields'] : array());				
				}
			}

			ob_start();			
			$this->prepare_step_header_html();
			include_once dirname(plugin_dir_path(__FILE__)).'/views/_import_mapping_page.php';
			$this->prepare_step_footer_html();
			return ob_get_clean();

		}else
		{
			return '';	
		}
	}

	/**
	*  Step 4 (Ajax sub function)
	* Built in steps, Advanced settings page
	*/
	public function advanced_page()
	{
		$this->step='advanced';
		if($this->to_import!="")
		{
			$this->prepare_step_summary();
			$this->prepare_footer_button_list();

			//taking current page form data
			$advanced_form_data=(isset($this->selected_template_form_data['advanced_form_data']) ? $this->selected_template_form_data['advanced_form_data'] : array());

			$advanced_screen_fields=$this->import_obj->get_advanced_screen_fields($advanced_form_data);

			ob_start();		
			$this->prepare_step_header_html();
			include_once dirname(plugin_dir_path(__FILE__)).'/views/_import_advanced_page.php';
			$this->prepare_step_footer_html();
			return ob_get_clean();

		}else
		{
			return '';	
		}
	}

	/**
	* Prepare description for mapping step fields 
	*/
	protected function prepare_field_description($key)
	{
		$out='';
		if(strpos($key, 'tax:')!==false)  /* taxonomy */
		{
			$column=trim(str_replace('tax:', '', $key));
			if(substr($column, 0, 3)!== 'pa_')
			{
				$out=__('Product taxonomies');
			}else
			{
				$out=__('New taxonomy: ').$column;
			}
		}
		elseif(strpos($key, 'meta:')!==false)  /* meta */
		{
			$column=trim(str_replace('meta:', '', $key));
			$out=__('Custom Field: ').$column;
		}
		elseif(strpos($key, 'attribute:')!==false)  /* attribute */
		{
			$column=trim(str_replace('attribute:', '', $key));
			if(substr($column, 0, 3)== 'pa_')
			{
				$out=__('Taxonomy attributes');
			}else
			{
				$out=__('New attribute: ').$column;
			}
		}
		elseif(strpos($key, 'attribute_data:')!==false)  /* attribute data */
		{
			$column=trim(str_replace('attribute_data:', '', $key));
			$out=__('Attribute data: ').$column;
		}
		elseif(strpos($key, 'attribute_default:')!==false)  /* attribute default */
		{
			$column=trim(str_replace('attribute_default:', '', $key));
			$out=__('Attribute default value: ').$column;
		}
		return $out;
	}

	/**
	* split default mapping fields and meta mapping fields
	*/
	protected function process_file_heading_data($arr)
	{
		$default=array();
		$meta=array();
		foreach($arr as $key=>$v)
		{
		    if(is_array($v))
		    {
		       $meta=array_merge($meta, $v);
		    }else
		    {
		        $default[$key]=$v;
		    }
		}
		return array('default'=>$default, 'meta'=>$meta);
	}

	/**
	* Get template form data
	*/
	protected function get_template_form_data($id)
	{
		$template_data=$this->get_mapping_template_by_id($id);
		if($template_data)
		{
			$decoded_form_data=Wt_Import_Export_For_Woo_Basic_Common_Helper::process_formdata(maybe_unserialize($template_data['data']));
			$this->selected_template_form_data=(!is_array($decoded_form_data) ? array() : $decoded_form_data);
		}
	}


	/**
	* Taking mapping template by Name
	*/
	protected function get_mapping_template_by_name($name)
	{
		global $wpdb;
		$tb=$wpdb->prefix.Wt_Import_Export_For_Woo_Basic::$template_tb;
		$qry=$wpdb->prepare("SELECT * FROM $tb WHERE template_type=%s AND item_type=%s AND name=%s",array('import', $this->to_import, $name));
		return $wpdb->get_row($qry, ARRAY_A);
	}


	/**
	* Taking mapping template by ID
	*/
	protected function get_mapping_template_by_id($id)
	{
		global $wpdb;
		$tb=$wpdb->prefix.Wt_Import_Export_For_Woo_Basic::$template_tb;
		$qry=$wpdb->prepare("SELECT * FROM $tb WHERE template_type=%s AND item_type=%s AND id=%d",array('import', $this->to_import, $id));
		return $wpdb->get_row($qry, ARRAY_A);
	}

	/**
	* Taking all mapping templates
	*/
	protected function get_mapping_templates()
	{
		if($this->to_import=='')
		{
			return;
		}		
		global $wpdb;
		$tb=$wpdb->prefix.Wt_Import_Export_For_Woo_Basic::$template_tb;
		$val=$wpdb->get_results("SELECT * FROM $tb WHERE template_type='import' AND item_type='".$this->to_import."' ORDER BY id DESC", ARRAY_A);	

		//add a filter here for modules to alter the data
		$this->mapping_templates=($val ? $val : array());
	}


	/**
	* Get meta field list for mapping page
	*
	*/
	protected function get_mapping_enabled_fields()
	{
		$mapping_enabled_fields=array(
//			'hidden_meta'=>array(__('Hidden meta'),0),
//			'meta'=>array(__('Meta'),1),
		);
		$this->mapping_enabled_fields=apply_filters('wt_iew_importer_alter_mapping_enabled_fields_basic', $mapping_enabled_fields, $this->to_import, array());
	}

	protected function prepare_step_summary()
	{
		$step_info= $this->get_step_info($this->step);
		$this->step_title=$step_info['title'];
		$this->step_keys=array_keys($this->steps);
		$this->current_step_index=array_search($this->step, $this->step_keys);
		$this->current_step_number=$this->current_step_index+1;
		$this->last_page=(!isset($this->step_keys[$this->current_step_index+1]) ? true : false);
		$this->total_steps=count($this->step_keys);
		$this->step_summary=__(sprintf("Step %d of %d", $this->current_step_number, $this->total_steps));
		$this->step_description=$step_info['description'];
	}

	protected function prepare_step_header_html()
	{	
		include dirname(plugin_dir_path(__FILE__)).'/views/_import_header.php';
	}

	protected function prepare_step_footer_html()
	{		
		include dirname(plugin_dir_path(__FILE__)).'/views/_import_footer.php';
	}

	protected function prepare_footer_button_list()
	{
		$out=array();
		$step_keys=$this->step_keys;
		$current_index=$this->current_step_index;
		$last_page=$this->last_page;
		if($current_index!==false) /* step exists */
		{
			if($current_index>0) //add back button
			{
				$out['back']=array(
					'type'=>'button',
					'action_type'=>'step',
					'key'=>$step_keys[$current_index-1],
					'text'=>'<span class="dashicons dashicons-arrow-left-alt2" style="line-height:27px;"></span> '.__('Back'),
				);
			}
			
			if(isset($step_keys[$current_index+1])) /* not last step */
			{
				$next_number=$current_index+2;
				$next_key=$step_keys[$current_index+1];
				$next_title=$this->steps[$next_key]['title'];
				$out['next']=array(
					'type'=>'button',
					'action_type'=>'step',
					'key'=>$next_key,
					'text'=>__('Step').' '.$next_number.': '.$next_title.' <span class="dashicons dashicons-arrow-right-alt2" style="line-height:27px;"></span>',
				);

				if($this->import_method=='quick' || $this->import_method=='template') //Quick Or Template method
				{
					$out['or']=array(
						'type'=>'text',
						'text'=>__('Or'),
					);
				}

			}else
			{
				$last_page=true;
			}

			if($this->import_method=='quick' || $this->import_method=='template' || $last_page) //template method, or last page, or quick import
			{
				if($last_page && $this->import_method!='quick') //last page and not quick import
				{
					if($this->import_method=='template')
					{
						$out['save']=array(
							'key'=>'save',
							'icon'=>'',
							'type'=>'dropdown_button',
							'text'=>__('Save template'), 
							'items'=>array(
								'update'=>array(
									'key'=>'update_template',
									'text'=>__('Save'),  //no prompt
								),
								'save'=>array(
									'key'=>'save_template_as',
									'text'=>__('Save As'), //prompt for name
								)
							)
						);
					}else
					{
						$out['save']=array(
							'key'=>'save_template',
							'icon'=>'',
							'type'=>'button',
							'text'=>__('Save template'), //prompt for name
						);
					}
				}
				$out['download']=array(
					'key'=>'download', /* first step of import must be download the input file */
					'class'=>'iew_import_btn',
					'icon'=>'',
					'type'=>'button',
					'text'=>__('Import'),
				);
			}
		}		
		$this->step_btns=apply_filters('wt_iew_importer_alter_footer_btns_basic', $out, $this->step, $this->steps);
	}

	/**
	* 	Prepare default mapping data for quick import
	*	After preparing update the Formdata in history table
	*/
	protected function _prepare_for_quick($import_id)
	{
		//take history data by import_id
		$import_data=Wt_Import_Export_For_Woo_Basic_History::get_history_entry_by_id($import_id);

		//processing form data
		$form_data=(isset($import_data['data']) ? maybe_unserialize($import_data['data']) : array());


		$ext_arr=explode('.', $this->import_obj->temp_import_file);
//		if(strtolower(end($ext_arr))=='xml') /* no chance for XML. anyway we are not skipping the future possibilities */
//		{
//			include_once WT_O_IEW_PLUGIN_PATH.'admin/classes/class-xmlreader.php';
//			$reader=new Wt_Import_Export_For_Woo_Basic_Xmlreader();
//
//		}else  /* csv */
//		{
			include_once WT_O_IEW_PLUGIN_PATH.'admin/classes/class-csvreader.php';
			$delimiter=(isset($form_data['method_import_form_data']['wt_iew_delimiter']) ? ($form_data['method_import_form_data']['wt_iew_delimiter']) : ',');
			$reader=new Wt_Import_Export_For_Woo_Basic_Csvreader($delimiter);
//		}

		$file_path=$this->import_obj->get_file_path($this->import_obj->temp_import_file);
		
		/* take first two rows in csv and in xml takes column keys and a sample data */
		$sample_data=$reader->get_sample_data($file_path, true);

		$file_heading_data=$this->process_file_heading_data($sample_data);
		$file_heading_default_fields=$file_heading_data['default'];
		$file_heading_meta_fields=$file_heading_data['meta'];
		

		/**
		*	Default mapping fields 
		*	Format: 'field_key'=>array('title'=>'', 'description'=>'')
		*/
		$mapping_fields=array();
		$mapping_fields=apply_filters('wt_iew_importer_alter_mapping_fields_basic', $mapping_fields, $this->to_import, array());

                $array_keys_file_heading_default_fields = array_keys($file_heading_default_fields);
		$mapping_form_data=array('mapping_fields'=>array(), 'mapping_selected_fields'=>array());
		$allowed_field_types=array('start_with', 'end_with', 'contain');

		foreach($mapping_fields as $key=>$val_arr)
		{
			$val='';
			$checked=0; /* import this column? */
			$type=(isset($val_arr['type']) ? $val_arr['type'] : '');
//			if(isset($file_heading_default_fields[$key]))
                        if($case_key = preg_grep("/^$key$/i", $array_keys_file_heading_default_fields))   //preg_grep used escape from case sensitive check.    
			{
				$checked=1; /* import this column? */
//				$val='{'.$key.'}';
                                $val='{'.array_shift($case_key).'}';  //  preg_grep give an array with actual index and value
				unset($file_heading_default_fields[$key]); //remove the field from file heading list
			}
			elseif(isset($file_heading_meta_fields[$key])) /* some meta items will show inside default field list, Eg: yoast */
			{
				$checked=1; /* import this column? */
				$val='{'.$key.'}';
				unset($file_heading_meta_fields[$key]); //remove the field from file heading list
			}else
			{
				$field_type=(isset($val_arr['field_type']) ? $val_arr['field_type'] : '');
				if($field_type!="" && in_array($field_type, $allowed_field_types)) // it may be a different field type 
				{
					foreach ($file_heading_default_fields as $def_key => $def_val) 
					{
						$matched=false;
						if($field_type=='start_with' && strpos($def_key, $key)===0)
						{
							$matched=true;
						}
						elseif($field_type=='ends_with' && strrpos($def_key, $key)===(strlen($def_key) - strlen($key)))
						{
							$matched=true;
						}
						elseif($field_type=='contains' && strpos($def_key, $key)!==false)
						{
							$matched=true;
						}
						if($matched)
						{
							$val='{'.$def_key.'}';
							unset($file_heading_default_fields[$def_key]); //remove the field from file heading list						
							$mapping_form_data['mapping_selected_fields'][$def_key]=$val;
							$mapping_form_data['mapping_fields'][$def_key]=array($val, 1, $type); //value, enabled, type
						}
					}
				}else /* unmatched keys */
				{
					$checked=0; 
					$val='';
				}
			}
			if($checked==1)
			{ 			
				$mapping_form_data['mapping_selected_fields'][$key]=$val;
				$mapping_form_data['mapping_fields'][$key]=array($val, 1, $type); //value, enabled, type
			}			
		}	

		/**
		*	Meta mapping fields
		*
		*/
		$form_data_meta_mapping_fields = array(); // recheck the need of this variable in the below context.
		$meta_mapping_screen_fields=$this->_get_meta_mapping_screen_fields($form_data_meta_mapping_fields);

		/* preparing meta fields. */
		$meta_mapping_form_data=array('mapping_fields'=>array(), 'mapping_selected_fields'=>array());
		if($meta_mapping_screen_fields && is_array($meta_mapping_screen_fields))
		{
			/* loop through mapping fields */
			foreach($meta_mapping_screen_fields as $meta_mapping_screen_field_key=>$meta_mapping_screen_field_val)
			{
				/* decalaring an empty array*/
				$temp_arr=array();
				$temp_fields_arr=array(); /* this is to store mapping field other details */

				/* default field list from post type module */
				$mapping_fields=((isset($meta_mapping_screen_field_val['fields']) && is_array($meta_mapping_screen_field_val['fields'])) ? $meta_mapping_screen_field_val['fields'] : array());

				/* loop through mapping fields */
				if(count($mapping_fields)>0)
				{
					foreach($mapping_fields as $key=>$val_arr)
					{
						$val='';
						$checked=0; /* import this column? */
						$type=(isset($val_arr['type']) ? $val_arr['type'] : '');

						if(isset($file_heading_meta_fields[$key]))
						{
							$checked=1; /* import this column? */
							$val='{'.$key.'}';
							unset($file_heading_meta_fields[$key]); //remove the field from file heading list
						}
						if($checked==1)
						{
							$temp_arr[$key]=$val;
							$temp_fields_arr[$key]=array($val, 1, $type);
						}						
					}
				}
				if(count($file_heading_meta_fields)>0)
				{
					$current_field_group_prefix_arr=(isset($this->field_group_prefixes[$meta_mapping_screen_field_key]) ? $this->field_group_prefixes[$meta_mapping_screen_field_key] : array());
					foreach($file_heading_meta_fields as $key=>$sample_val)
					{
						$is_include=$this->_is_include_meta_in_this_group($current_field_group_prefix_arr, $key);						
						if($is_include==1)
						{
							$val='{'.$key.'}';
							$checked=1; /* import this column? */
							unset($file_heading_meta_fields[$key]); //remove the field from file heading list
							$temp_arr[$key]=$val;
							$temp_fields_arr[$key]=array($val, 1, '');
						}
					}
				}

				/* adding value to main array */
				$meta_mapping_form_data['mapping_selected_fields'][$meta_mapping_screen_field_key]=$temp_arr;
				$meta_mapping_form_data['mapping_fields'][$meta_mapping_screen_field_key]=$temp_fields_arr;
				$mapping_fields=$temp_arr=$temp_fields_arr=null;
				unset($temp_arr, $temp_fields_arr, $mapping_fields);
			}

			/* if any columns that not in the above list */
			if(count($file_heading_meta_fields)>0)
			{
				//do something
			}
		}

		/**
		*	 update form data with prepared mapping form data 
		*/
		$form_data['mapping_form_data']=$mapping_form_data;
		$form_data['meta_step_form_data']=$meta_mapping_form_data;
				

		$update_data=array(
			'data'=>maybe_serialize($form_data), //formadata
		);
		$update_data_type=array(
			'%s',
		);
		Wt_Import_Export_For_Woo_Basic_History::update_history_entry($import_id, $update_data, $update_data_type);

		$mapping_form_data=$meta_mapping_form_data=$form_data=null;
		unset($mapping_form_data, $meta_mapping_form_data, $form_data);
	}

	protected function _get_meta_mapping_screen_fields($form_data_meta_mapping_fields)
	{
		$this->get_mapping_enabled_fields();
		$meta_mapping_screen_fields=array();
		foreach($this->mapping_enabled_fields as $field_key=>$field_vl)
		{
			$field_vl=(!is_array($field_vl) ? array($field_vl, 0) : $field_vl);
			$meta_mapping_screen_fields[$field_key]=array(
				'title'=>'',
				'checked'=>$field_vl[1],
				'fields'=>array(),
			);
		}

		/**
		*	default mapping page fields 
		*	Format: 'field_key'=>array('title'=>'', 'description'=>'')
		*/
		return apply_filters('wt_iew_importer_alter_meta_mapping_fields_basic', $meta_mapping_screen_fields, $this->to_import, $form_data_meta_mapping_fields);
	}

	protected function _is_include_meta_in_this_group($current_field_group_prefix_arr, $key)
	{
		$is_include=0;
		foreach ($current_field_group_prefix_arr as $_prefix) 
		{	
			if(strpos($key, $_prefix)===0) /* find as first occurrence */
			{ 
				if($_prefix=='meta') /* avoid conflict with hidden meta */
				{
					$name=str_replace('meta:', '', $key);
					if(substr($name, 0, 1)!='_') /* not hidden meta */
					{
						if(strpos($name, 'attribute')!==0) /* its not meta attribute */
						{
							$is_include=1;
							break;
						}
					}
				}else
				{
					$is_include=1;
					break;
				}							
			}
		}
		return $is_include;
	}
}
}