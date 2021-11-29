<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<table class="wt-iew-mapping-tb wt-iew-exporter-meta-mapping-tb" data-field-type="<?php echo $meta_mapping_screen_field_key; ?>">
	<thead>
		<tr>
    		<th>
    			<?php 
    			$is_checked=(isset($meta_mapping_screen_field_val['checked']) && $meta_mapping_screen_field_val['checked']==1 ? 1 : 0);
    			$checked_attr=($is_checked==1 ? ' checked="checked"' : '');
    			?>
    			<input type="checkbox" name="" class="wt_iew_mapping_checkbox_main" <?php echo $checked_attr; ?>>
    		</th>
    		<th width="35%"><?php _e('Column');?></th>
    		<th><?php _e('Column name');?></th>
    	</tr>
	</thead>
	<tbody>
		<?php
		$tr_count=0; 
		
		if(isset($meta_mapping_screen_field_val['fields']) && is_array($meta_mapping_screen_field_val['fields']) && count($meta_mapping_screen_field_val['fields'])>0)
		{
			foreach($meta_mapping_screen_field_val['fields'] as $key=>$val)
			{
				$val=is_array($val) ? $val : array($val, 0);
				$label=$val[0];

				if(isset($current_meta_step_form_data[$key])) /* forma data/template data available */
				{
					$val=(is_array($current_meta_step_form_data[$key]) ? $current_meta_step_form_data[$key] : array($current_meta_step_form_data[$key], 1));
				}else
				{
					$val[1]=$is_checked; //parent is checked
				}

				include "_export_mapping_tr_html.php";
				$tr_count++;
			}
		}

		if($tr_count==0)
		{
			?>
			<tr>
				<td colspan="3" style="text-align:center;">
					<?php _e('No fields found.'); ?>
				</td>
			</tr>
			<?php
		}
		?>
	</tbody>
</table>   