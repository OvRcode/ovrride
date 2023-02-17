<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<tr id="columns_<?php echo $key;?>">
	<td>
		<input type="checkbox" name="columns_key[]" class="columns_key wt_iew_mapping_checkbox_sub" value="<?php echo $key;?>" <?php echo ($checked==1 ? 'checked' : ''); ?>>
	</td>
	<td>
		<label class="wt_iew_mapping_column_label"><?php echo $label;?></label>
	</td>
	<td>
		<input type="hidden" name="columns_val[]" class="columns_val" value="<?php echo $val;?>" data-type="<?php echo $type;?>">
		<span data-wt_iew_popover="1" data-title="" data-content-container=".wt_iew_mapping_field_editor_container" class="wt_iew_mapping_field_val"><?php echo $val;?></span>		
	</td>
</tr>