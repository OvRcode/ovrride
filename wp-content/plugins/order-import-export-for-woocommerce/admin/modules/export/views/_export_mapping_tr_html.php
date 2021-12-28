<?php
if (!defined('ABSPATH')) {
    exit;
}
$checked=is_array($val) ? $val[1] : 0;
$val=(is_array($val) ? $val[0] : $val);
?>
<tr id="columns_<?php echo $key;?>">
	<td>
	<div class="wt_iew_sort_handle"><span class="dashicons dashicons-move"></span></div>
	<input type="checkbox" name="columns_key[]" class="columns_key wt_iew_mapping_checkbox_sub" value="<?php echo $key;?>" <?php echo ($checked==1 ? 'checked' : ''); ?>></td>
	<td>
		<label class="wt_iew_mapping_column_label"><?php echo $label;?></label>
	</td>
	<td>
		<input type="text" name="columns_val[]" class="columns_val" value="<?php echo $val;?>">
	</td>
</tr>