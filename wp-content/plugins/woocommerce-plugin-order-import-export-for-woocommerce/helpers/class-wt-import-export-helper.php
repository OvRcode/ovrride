<?php

/**
 * Import Export Helper Library
 *
 * Includes helper functions for import, export, history modules
 * @author     WebToffee <info@webtoffee.com>
 */

if(!class_exists('Wt_Iew_IE_Basic_Helper'))
{
	/**
	 * 
	 */
	class Wt_Iew_IE_Basic_Helper
	{
		
		public static function _get_csv_delimiters()
		{
			return array(
				'comma'=>array('value'=>__('Comma'), 'val'=>","),
				'semicolon'=>array('value'=>__('Semicolon'), 'val'=>";"),
				'tab'=>array('value'=>__('Tab'), 'val'=>"\t"),
				'space'=>array('value'=>__('Space'), 'val'=>" "),
				'other'=>array('value'=>__('Other'), 'val'=>""),
			);
		}
		public static function _get_local_file_path($file_url)
		{
			$file_path = untrailingslashit(ABSPATH).str_replace(site_url(), '', $file_url);
                     
			if(file_exists($file_path))
			{
				return $file_path;
			}else
			{
                            /* Retrying if the directory structure is different from wordpress default file structure */
                            $url_parms = explode('/', $file_url);

                            $file_name =  end($url_parms);

                            $file_dir_name = prev($url_parms);

                            $file_path = WP_CONTENT_DIR.'/'.$file_dir_name.'/'.$file_name;
                            
                            if(file_exists($file_path))
                            {
                                    return $file_path;
                            }else
                            {
                            
				return false;
                            }
			}
		}
		public static function get_validation_rules($step, $form_data, $module_obj)
		{
			$method_name='get_'.$step.'_screen_fields';
			$out=array();
			if(method_exists($module_obj, $method_name))
			{
				$fields=$module_obj->{$method_name}($form_data);
				$out=Wt_Import_Export_For_Woo_Basic_Common_Helper::extract_validation_rules($fields);
			}
			$form_data=$module_obj=null;
			unset($form_data, $module_obj);
			return $out;
		}
		public static function sanitize_formdata($form_data, $module_obj)
		{
			$out=array();
			foreach ($module_obj->steps as $step=>$step_data)
			{
				if($step=='mapping') //custom rule needed for mapping fieds
				{

					/* general mapping fields section */
					if(isset($form_data['mapping_form_data']) && is_array($form_data['mapping_form_data']))
					{
						$mapping_form_data=$form_data['mapping_form_data'];
						
						/* mapping fields. This is an internal purpose array */
						if(isset($mapping_form_data['mapping_fields']) && is_array($mapping_form_data['mapping_fields']))
						{
							foreach ($mapping_form_data['mapping_fields'] as $key => $value)
							{
								$new_key=sanitize_text_field($key);
								$value=array(sanitize_text_field($value[0]), absint($value[1]));
								unset($mapping_form_data['mapping_fields'][$key]);
								$mapping_form_data['mapping_fields'][$new_key]=$value;
							}
						}

						/*mapping enabled meta items */
						if(isset($mapping_form_data['mapping_enabled_fields']) && is_array($mapping_form_data['mapping_enabled_fields']))
						{
							$mapping_form_data['mapping_enabled_fields']=Wt_Iew_Sh::sanitize_item($mapping_form_data['mapping_enabled_fields'], 'text_arr');
						}

						/* mapping fields. Selected fields only */
						if(isset($mapping_form_data['mapping_selected_fields']) && is_array($mapping_form_data['mapping_selected_fields']))
						{
							foreach ($mapping_form_data['mapping_selected_fields'] as $key => $value)
							{
								$new_key=sanitize_text_field($key);
								unset($mapping_form_data['mapping_selected_fields'][$key]);
								$mapping_form_data['mapping_selected_fields'][$new_key]=sanitize_text_field($value);
							}
						}

						$out['mapping_form_data']=$mapping_form_data;
					}


					/* meta mapping fields section */
					if(isset($form_data['meta_step_form_data']) && is_array($form_data['meta_step_form_data']))
					{	
						$meta_step_form_data=$form_data['meta_step_form_data'];
						/* mapping fields. This is an internal purpose array */
						if(isset($meta_step_form_data['mapping_fields']) && is_array($meta_step_form_data['mapping_fields']))
						{
							foreach ($meta_step_form_data['mapping_fields'] as $meta_key => $meta_value)
							{
								foreach ($meta_value as $key => $value)
								{
									$new_key=sanitize_text_field($key);
									$value=array(sanitize_text_field($value[0]), absint($value[1]));
									unset($meta_value[$key]);
									$meta_value[$new_key]=$value;
								}
								$meta_step_form_data['mapping_fields'][$meta_key]=$meta_value;
							}
						}


						/* mapping fields. Selected fields only */
						if(isset($meta_step_form_data['mapping_selected_fields']) && is_array($meta_step_form_data['mapping_selected_fields']))
						{
							foreach ($meta_step_form_data['mapping_selected_fields'] as $meta_key => $meta_value)
							{
								foreach ($meta_value as $key => $value)
								{
									$new_key=sanitize_text_field($key);
									unset($meta_value[$key]);
									$meta_value[$new_key]=sanitize_text_field($value);
								}
								$meta_step_form_data['mapping_selected_fields'][$meta_key]=$meta_value;
							}
						}

						$out['meta_step_form_data']=$meta_step_form_data;
					}
				}else
				{
					$current_form_data_key=$step.'_form_data';
					$current_form_data=(isset($form_data[$current_form_data_key]) ? $form_data[$current_form_data_key] : array());
					if(in_array($step, $module_obj->step_need_validation_filter))
					{
						$validation_rule=self::get_validation_rules($step, $current_form_data, $module_obj); 
						
						foreach($current_form_data as $key => $value) 
						{
							$no_prefix_key=str_replace('wt_iew_', '', $key);
							$current_form_data[$key]=Wt_Iew_Sh::sanitize_data($value, $no_prefix_key, $validation_rule);
						}
					}else
					{	
						$validation_rule=(isset($module_obj->validation_rule[$step]) ? $module_obj->validation_rule[$step] : array());				
						foreach($current_form_data as $key => $value) 
						{
							$current_form_data[$key]=Wt_Iew_Sh::sanitize_data($value, $key, $validation_rule);
						}					
					}
					$out[$current_form_data_key]=$current_form_data;
				}				
			}
			$form_data=$current_form_data=$mapping_form_data=$meta_step_form_data=$module_obj=null;
			unset($form_data, $current_form_data, $mapping_form_data, $meta_step_form_data, $module_obj);
			return $out;
		}
		public static function debug_panel($module_base)
		{
			if($module_base=='import' || $module_base=='export')
			{
				$debug_panel_btns=array(
					'refresh_step'=>array(
						'title'=>__('Refresh the step'),
						'icon'=>'dashicons dashicons-update',
						'onclick'=>'wt_iew_basic_'.$module_base.'.refresh_step();',
					),
					'console_form_data'=>array(
						'title'=>__('Console form data'),
						'icon'=>'dashicons dashicons-code-standards',
						'onclick'=>'wt_iew_basic_'.$module_base.'.console_formdata();',
					),
				);
			}

			$debug_panel_btns=apply_filters('wt_iew_debug_panel_buttons_basic', $debug_panel_btns, $module_base);
			if(defined('WT_IEW_DEBUG_BASIC') && WT_IEW_DEBUG_BASIC && is_array($debug_panel_btns) && count($debug_panel_btns)>0)
			{
				?>
				<div class="wt_iew_debug_panel" title="<?php _e('For debugging process');?>">
					<div class="wt_iew_debug_panel_hd"><?php _e('Debug panel');?></div>
					<div class="wt_iew_debug_panel_con">
						<?php
						foreach ($debug_panel_btns as $btn) 
						{
							?>
							<a onclick="<?php echo $btn['onclick'];?>" title="<?php echo $btn['title'];?>">
								<span class="<?php echo $btn['icon'];?>"></span>
							</a>
							<?php
						}
						?>
					</div>
				</div>
				<?php
			}
		}
	}

}