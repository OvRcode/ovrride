<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_import_main">
	<p><?php echo $this->step_description;?></p>
	<div class="wt_iew_warn wt_iew_post_type_wrn" style="display:none;">
		<?php _e('Please select a post type');?>
	</div>
	<table class="form-table wt-iew-form-table">
		<tr>
			<th><label><?php _e('Select a post type to import'); ?></label></th>
			<td>
				<select name="wt_iew_import_post_type">
					<option value="">-- <?php _e('Select post type'); ?> --</option>
					<?php
					$item_type = isset($item_type) ? $item_type : '';
					foreach($post_types as $key=>$value)
					{
						?>
						<option value="<?php echo $key;?>" <?php echo ($item_type==$key ? 'selected' : '');?>><?php echo $value;?></option>
						<?php
					}
					?>
				</select>
			</td>
			<td></td>
		</tr>
	</table>
</div>