<?php
if (!defined('ABSPATH')) {
    exit;
}
foreach($form_fields as $key=>$value)
{
	/* setting default value form fields list array */
	$vl=(isset($value['value']) ? $value['value'] : '');
	$vl=($vl=='' && isset($value['default_value']) ? $value['default_value'] : $vl);

	$type=(isset($value['type']) ? $value['type'] : 'text');
	$css_class=(isset($value['css_class']) ? $value['css_class'] : '');
	$html_id=(isset($value['html_id']) ? ' id="'.$value['html_id'].'" ' : '');
	$tr_id=(isset($value['tr_id']) ? ' id="'.$value['tr_id'].'" ' : '');
	$tr_class=(isset($value['tr_class']) ? $value['tr_class'] : '');
	$td_class1=(isset($value['td_class1']) ? $value['td_class1'] : '');
	$td_class2=(isset($value['td_class2']) ? $value['td_class2'] : '');
	$td_class3=(isset($value['td_class3']) ? $value['td_class3'] : '');

	$tr_html=(isset($value['tr_html']) ? $value['tr_html'] : '');
	$field_html=(isset($value['field_html']) ? $value['field_html'] : '');
	$help_text=(isset($value['help_text']) ? $value['help_text'] : '');
	$placeholder=(isset($value['placeholder']) ? $value['placeholder'] : '');
	$attr_arr=(isset($value['attr']) ? $value['attr'] : array());
	$after_form_field_html=(isset($value['after_form_field_html']) ? $value['after_form_field_html'] : ''); /* after form field `td` */
	$after_form_field=(isset($value['after_form_field']) ? $value['after_form_field'] : ''); /* after form field */
	$before_form_field=(isset($value['before_form_field']) ? $value['before_form_field'] : '');
	$merge_left=(isset($value['merge_left']) ? $value['merge_left'] : false); /* merge field td with left td */
	$merge_right=(isset($value['merge_right']) ? $value['merge_right'] : false); /* merge field td with right td */
	$colspan=1;
	if($merge_left)
	{
		$colspan++;
	}
	if($merge_right)
	{
		$colspan++;
	}
	$colspan_attr='';
	if($colspan>1)
	{
		$colspan_attr=' colspan="'.$colspan.'"';
	}

	/** 
	*	conditional help texts 
	*	!!Important: Using OR mixed with AND then add OR conditions first.
	*/
	$conditional_help_html='';
	if(isset($value['help_text_conditional']) && is_array($value['help_text_conditional']))
	{		
		foreach ($value['help_text_conditional'] as $help_text_config)
		{
			if(is_array($help_text_config))
			{
				$condition_attr='';
				if(is_array($help_text_config['condition']))
				{
					$previous_type=''; /* this for avoiding fields without glue */
					foreach ($help_text_config['condition'] as $condition)
					{
						if(is_array($condition))
						{
							if($previous_type!='field')
							{
								$condition_attr.='['.$condition['field'].'='.$condition['value'].']';
								$previous_type='field';
							}
						}else
						{
							if(is_string($condition))
							{
								$condition=strtoupper($condition);
								if(($condition=='AND' || $condition=='OR') && $previous_type!='glue')
								{
									$condition_attr.='['.$condition.']';
									$previous_type='glue';
								}
							}
						}
					}
				}			
				$conditional_help_html.='<span class="wt-iew_form_help wt-iew_conditional_help_text" data-iew-help-condition="'.esc_attr($condition_attr).'">'.$help_text_config['help_text'].'</span>';
			}	
		}
	}

	$form_toggler_p_class="";
	$form_toggler_register="";
	$form_toggler_child="";
	if(isset($value['form_toggler']))
	{
		if($value['form_toggler']['type']=='parent')
		{
			$form_toggler_p_class="wt_iew_form_toggler";
			$form_toggler_register=' wf_frm_tgl-target="'.$value['form_toggler']['target'].'"';
		}
		elseif($value['form_toggler']['type']=='child')
		{
			$form_toggler_child=' wf_frm_tgl-id="'.$value['form_toggler']['id'].'" wf_frm_tgl-val="'.$value['form_toggler']['val'].'" '.(isset($value['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$value['form_toggler']['chk'].'"' : '').(isset($value['form_toggler']['depth']) ? ' wf_frm_tgl-lvl="'.$value['form_toggler']['depth'].'"' : '');	
		}else
		{
			$form_toggler_child=' wf_frm_tgl-id="'.$value['form_toggler']['id'].'" wf_frm_tgl-val="'.$value['form_toggler']['val'].'" '.(isset($value['form_toggler']['chk']) ? 'wf_frm_tgl-chk="'.$value['form_toggler']['chk'].'"' : '').(isset($value['form_toggler']['depth']) ? ' wf_frm_tgl-lvl="'.$value['form_toggler']['depth'].'"' : '');	
			$form_toggler_p_class="wt_iew_form_toggler";
			$form_toggler_register=' wf_frm_tgl-target="'.$value['form_toggler']['target'].'"';				
		}		
	}
	
	$field_group_attr=(isset($value['field_group']) ? ' data-field-group="'.$value['field_group'].'" ' : '');
	$tr_class.=(isset($value['field_group']) ? ' wt_iew_field_group_children ' : ''); //add an extra class to tr when field grouping enabled

	if($type=='image_export')
	{
		$css_class.=' wt_iew_separate_image_export';
	}

	$attr='';
	$has_class_attr=0;
	$css_attr_value='';
	foreach ($attr_arr as $attr_key => $attr_value)
	{
		if($attr_key=='class')
		{
			$attr_value.=" ".$form_toggler_p_class." ".$css_class;
			$form_toggler_p_class='';
			$css_class='';
			$has_class_attr=1;
			$css_attr_value=$attr_value;
		}
		$attr.=$attr_key.'="'.$attr_value.'" ';
	}

	$css_class.=($form_toggler_p_class!="" ? ' '.$form_toggler_p_class : '');
	$css_attr=($css_class!="" ? ' class="'.$css_class.'"' : '');


	if($tr_html=="")
	{
		$form_field_name = isset($value['field_name']) ? $value['field_name'] : '';
		$form_data_key=(substr($form_field_name,0,8)!=='wt_iew_' ? 'wt_iew_' : '').$form_field_name;

		/* checking field value exist in form data array */
		if(isset($form_data[$form_data_key]))
		{
			$vl=$form_data[$form_data_key];
		}
		if($type=='field_group_head') //heading for field group
		{
			$visibility=(isset($value['show_on_default']) ? $value['show_on_default'] : 0);
		?>
		<tr <?php echo $tr_id.$field_group_attr;?> class="<?php echo $tr_class;?>">
			<td colspan="3" class="wt_iew_field_group">
				<div class="wt_iew_field_group_hd">
					<?php echo isset($value['head']) ? $value['head'] : ''; ?>
					<div class="wt_iew_field_group_toggle_btn" data-id="<?php echo isset($value['group_id']) ? $value['group_id'] : ''; ?>" data-visibility="<?php echo $visibility; ?>"><span class="dashicons dashicons-arrow-<?php echo ($visibility==1 ? 'down' : 'right'); ?>"></span></div>
				</div>
				<div class="wt_iew_field_group_content">
					<table></table>
				</div>
			</td>
		</tr>
		<?php
		}else
		{		
		?>		
		<tr <?php echo $tr_id.$field_group_attr;?> class="<?php echo $tr_class;?>"  <?php echo $form_toggler_child; ?>>
			<?php
			if(!$merge_left) /* if merge left not enabled */
			{
			?>
				<th class="<?php echo $td_class1;?>">
					<label><?php echo isset($value['label']) ? $value['label'] : ''; ?></label>
				</th>
			<?php
			}
			?>
			<td <?php echo $colspan_attr;?> class="<?php echo $td_class2;?>">
				
				<?php
				if($field_html=='')
				{
					echo $before_form_field;
					if($type=='text' || $type=='number' || $type=='password')
					{
						?>
						<input placeholder="<?php echo $placeholder;?>" type="<?php echo $type; ?>" <?php echo $html_id;?> <?php echo $css_attr;?> name="<?php echo $form_data_key;?>" value="<?php echo $vl;?>" <?php echo $attr;?> >
						<?php
					}
					if($type=='textarea')
					{
						?>
						<textarea placeholder="<?php echo $placeholder;?>" <?php echo $html_id;?> <?php echo $css_attr;?> name="<?php echo $form_data_key;?>" <?php echo $attr;?> ><?php echo $vl;?></textarea>
						<?php
					}
					elseif($type=='multi_select')
					{
						$sele_vals=(isset($value['sele_vals']) && is_array($value['sele_vals']) ? $value['sele_vals'] : array());
						$vl=(is_array($vl) ? $vl : array($vl));
						$vl=array_filter($vl);
						?>
						<select <?php echo $html_id;?> <?php echo $css_attr;?> data-placeholder="<?php echo $placeholder;?>" name="<?php echo $form_data_key;?>" multiple="multiple" <?php echo $attr;?> >
						<?php
						foreach($sele_vals as $sele_val=>$sele_lbl) 
						{
						?>
	                      <option value="<?php echo $sele_val;?>" <?php echo (in_array($sele_val,$vl) ? 'selected' : ''); ?>><?php echo $sele_lbl;?></option>
	                    <?php
	                    }

	                    /* in the case of ajax product search selectbox */
	                    if($has_class_attr==1)
	                    {
	                    	$css_class_arr=explode(" ", $css_attr_value);
	                    }else
	                    {
	                    	$css_class_arr=explode(" ", $css_class);
	                    }
	                    if(in_array('wc-product-search', $css_class_arr))
	                    {
	                    	foreach($vl as $single_vl)
	                    	{
	                    		$single_vl=(int) $single_vl;
	                    		if($single_vl>0)
	                    		{
	                    			$product=wc_get_product($single_vl);
	                    			?>
	                  				<option value="<?php echo $single_vl;?>" selected><?php echo $product->get_title();?></option>
	                    			<?php
	                    		}
	                    	}
	                    }
                            if(in_array('wc-customer-search', $css_class_arr)){
	                    	foreach($vl as $single_vl)
	                    	{
	                    		$single_vl=(int) $single_vl;
	                    		if($single_vl>0)
	                    		{
	                    			$user=get_user_by( 'id', $single_vl);
                                                  if(!is_object($user))
                                                       continue;
	                    			?>
	                  				<option value="<?php echo $single_vl;?>" selected><?php echo sprintf(
                                               /* translators: $1: user name, $2 user id, $3: user email */
                                                esc_html__( '%1$s (#%2$s &ndash; %3$s)'),
                                                $user->first_name . ' ' . $user->last_name,
                                                $user->ID,
                                                $user->user_email
                                                );?></option>
	                    			<?php
	                    		}
	                    	}
	                    }
	                    ?>	
						</select>
						<?php
					}
					elseif($type=='select')
					{
						$sele_vals=(isset($value['sele_vals']) && is_array($value['sele_vals']) ? $value['sele_vals'] : array());
						$vl=(is_array($vl) ? $vl : array($vl));
						?>
						<select <?php echo $html_id;?> <?php echo $css_attr;?> data-placeholder="<?php echo $placeholder;?>" name="<?php echo $form_data_key;?>" <?php echo $attr;?> <?php echo $form_toggler_register;?> >
						<?php
						foreach($sele_vals as $sele_val=>$sele_lbl) 
						{
							$sele_lbl_txt=(is_array($sele_lbl) ? ( isset($sele_lbl['value']) ? $sele_lbl['value'] : (isset($sele_lbl[0]) ? $sele_lbl[0] : '' ) ) : $sele_lbl);

							/* check any extra data to append */
							$sele_extra_attr='';
							if(is_array($sele_lbl))
							{
								foreach($sele_lbl as $sele_lblk=>$sele_lblv)
								{
									if($sele_lblk=='value')
									{
										continue;
									}
									$sele_extra_attr.=' data-'.$sele_lblk.'="'.$sele_lblv.'"';
								}	
							}
						?>
	                      <option value="<?php echo $sele_val;?>" <?php echo (in_array($sele_val,$vl) ? 'selected' : ''); ?> <?php echo $sele_extra_attr; ?> ><?php echo $sele_lbl_txt;?></option>
	                    <?php
	                    } 
	                    ?>	
						</select>
						<?php
					}
					elseif($type=='radio') //radio button
					{
						?>
						<div class="wt_form_radio_block">
							<?php
							$radio_fields=isset($value['radio_fields']) ? $value['radio_fields'] : array();
							foreach ($radio_fields as $rad_vl=>$rad_label) 
							{
							?>
							<input <?php echo $css_attr;?> type="radio" id="<?php echo $form_data_key.'_'.$rad_vl;?>" name="<?php echo $form_data_key;?>" value="<?php echo $rad_vl;?>" <?php echo ($vl==$rad_vl) ? ' checked="checked"' : ''; ?> <?php echo $attr;?> <?php echo $form_toggler_register;?> /> <?php echo $rad_label; ?>
							&nbsp;&nbsp;
							<?php
							}
							?>
						</div>
						<?php					
					}
					elseif($type=='image_export') //image export radio button
					{
						?>
						<div class="wt_form_radio_block">
							<?php
							$radio_fields=array(
				                'Yes' => __('Yes'),
				                'No' => __('No')
				            );
							foreach ($radio_fields as $rad_vl=>$rad_label) 
							{
							?>
							<input <?php echo $css_attr;?> type="radio" id="<?php echo $form_data_key.'_'.$rad_vl;?>" name="<?php echo $form_data_key;?>" value="<?php echo $rad_vl;?>" <?php echo ($vl==$rad_vl) ? ' checked="checked"' : ''; ?> <?php echo $attr;?> <?php echo $form_toggler_register;?> /> <?php echo $rad_label; ?>
							&nbsp;&nbsp;
							<?php
							}
							?>
						</div>
						<?php
					}
					elseif($type=='checkbox') //checkbox
					{
						?>
						<div class="wt_form_checkbox_block">
							<?php
							$checkbox_fields=isset($value['checkbox_fields']) ? $value['checkbox_fields'] : array();
							foreach ($checkbox_fields as $chk_vl=>$chk_label) 
							{
							?>
								<input <?php echo $css_attr;?> type="checkbox" id="<?php echo $form_data_key.'_'.$chk_vl;?>" name="<?php echo $form_data_key;?>" value="<?php echo $chk_vl;?>" <?php echo ($vl==$chk_vl) ? ' checked="checked"' : ''; ?> <?php echo $attr;?> <?php echo $form_toggler_register;?> /> <?php echo $chk_label; ?>
								&nbsp;&nbsp;
							<?php
							}?>
						</div>
						<?php					
					}elseif($type=='uploader')
					{
						$field_id=(isset($value['html_id']) ? $value['html_id'] : $form_data_key);
						?>
						<div class="wt_iew_file_attacher_dv">
				            <input <?php echo ($html_id!="" ? $html_id : 'id="'.$field_id.'"');?> placeholder="<?php echo $placeholder;?>" <?php echo $css_attr;?> type="text" name="<?php echo $form_data_key; ?>" value="<?php echo $vl; ?>" <?php echo $attr;?>/>						
							<input type="button" name="upload_file" data-wt_iew_file_attacher_title="<?php _e('Choose a file.'); ?>"  data-wt_iew_file_attacher_button_text="<?php _e('Select'); ?>" class="wf_button button button-primary wt_iew_file_attacher" wt_iew_file_attacher_target="#<?php echo $field_id; ?>" value="<?php _e('Upload'); ?>" />
						</div>
						<?php
					}elseif($type=='dropzone') /* dropzone file uploader */
					{
						$dropzone_id=(isset($value['dropzone']) ? $value['dropzone'] : $form_data_key.'_dropzone');
						$field_id=(isset($value['html_id']) ? $value['html_id'] : $form_data_key);
						?>
						<input <?php echo $css_attr;?> type="hidden" name="<?php echo $form_data_key; ?>" value="<?php echo $vl; ?>" <?php echo $attr;?> <?php echo ($html_id!="" ? $html_id : 'id="'.$field_id.'"');?>/>
						
						<div id="<?php echo $dropzone_id;?>" class="wt_iew_dropzone" wt_iew_dropzone_target="#<?php echo $field_id; ?>">
							<div class="dz-message">
								<?php _e('Drop files here or click to upload');?>
								<br /><br /><div class="wt_iew_dz_file_success"></div> <br />
								<div class="wt_iew_dz_file_name"></div> <br />
								<div class="wt_iew_dz_remove_link"></div> <br />
							</div>
						</div>
						<?php
					}
					echo $after_form_field;
				}else
				{
					echo $field_html;
				}
				if($help_text!="")
				{
					?>
					<span class="wt-iew_form_help"><?php echo $help_text;?></span>
					<?php
				}
				echo $conditional_help_html;
				?>
			</td>
			<?php
			if(!$merge_right) /* if merge right not enabled */
			{
			?>
				<td class="<?php echo $td_class3;?>">
					<?php echo $after_form_field_html; ?>
				</td>
			<?php
			}
			?>
		</tr>
	<?php
		}
	}else
	{
		echo $tr_html;
	}
}
?>