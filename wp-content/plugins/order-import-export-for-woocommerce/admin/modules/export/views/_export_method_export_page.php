<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_export_main">
	<p><?php //echo $step_info['description']; ?></p>
	
    <div class="wt_iew_warn wt_iew_method_export_wrn" style="display:none;">
		<?php _e('Please select an export method');?>
	</div>

    <div class="wt_iew_warn wt_iew_export_template_wrn" style="display:none;">
        <?php _e('Please select an export template.');?>
    </div>
    
	<table class="form-table wt-iew-form-table">
		<tr>
			<th><label><?php _e('Select an export method');?></label></th>
			<td colspan="2" style="width:75%;">
                <div class="wt_iew_radio_block">
                    <?php
                    foreach($this->export_obj->export_methods as $key => $value) 
                    {
                        ?>
                        <p>
                            <input type="radio" value="<?php echo $key;?>" id="wt_iew_export_<?php echo $key;?>_export" name="wt_iew_export_method_export" <?php echo ($this->export_method==$key ? 'checked="checked"' : '');?>><b><label for="wt_iew_export_<?php echo $key;?>_export"><?php echo $value['title']; ?></label></b> <br />
                            <span><label for="wt_iew_export_<?php echo $key;?>_export"><?php echo $value['description']; ?></label></span>
                        </p>
                        <?php
                    }
                    ?>
                </div>

			</td>
		</tr>

<!--        <tr class="wt-iew-export-method-options wt-iew-export-method-options-quick" style="display:none;">
            <th style="width:150px; text-align:left; vertical-align:top;"><label><?php _e('Include fields from the respective groups');?></label></th>
            <td colspan="2" style="width:75%;">
                <?php
                foreach($this->mapping_enabled_fields as $mapping_enabled_field_key=>$mapping_enabled_field)
                {
                    $mapping_enabled_field=(!is_array($mapping_enabled_field) ? array($mapping_enabled_field, 0) : $mapping_enabled_field);
                    
                    if($this->rerun_id>0) /* check this is a rerun request */
                    {
                        if(in_array($mapping_enabled_field_key, $form_data_mapping_enabled))
                        {
                            $mapping_enabled_field[1]=1; //mark it as checked
                        }else
                        {
                            $mapping_enabled_field[1]=0; //mark it as unchecked
                        }
                    }
                    ?>
                    <div class="wt_iew_checkbox" style="padding-left:0px;">
                        <input type="checkbox" id="wt_iew_<?php echo $mapping_enabled_field_key;?>" name="wt_iew_include_these_fields[]" value="<?php echo $mapping_enabled_field_key;?>" <?php echo ($mapping_enabled_field[1]==1 ? 'checked="checked"' : '');?> /> 
                        <label for="wt_iew_<?php echo $mapping_enabled_field_key;?>"><?php echo $mapping_enabled_field[0];?></label>
                    </div>  
                    <?php
                }
                ?>
                <span class="wt-iew_form_help"><?php _e('Enabling any of these ensures that all the fields from the respective groups are included in your export.');?></span>
            </td>
        </tr>-->


		<tr class="wt-iew-export-method-options wt-iew-export-method-options-template" style="display:none;">
    		<th><label><?php _e('Export template');?></label></th>
    		<td>
    			<select class="wt-iew-export-template-sele">
    				<option value="0">-- <?php _e('Select a template'); ?> --</option>
    				<?php
    				foreach($this->mapping_templates as $mapping_template)
    				{
    				?>
    					<option value="<?php echo $mapping_template['id'];?>" <?php echo ($form_data_export_template==$mapping_template['id'] ? ' selected="selected"' : ''); ?>>
    						<?php echo $mapping_template['name'];?>
    					</option>
    				<?php
    				}
    				?>
    			</select>
    		</td>
    		<td>
    		</td>
    	</tr>
	</table>
</div>