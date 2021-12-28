<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_export_main">
	<p><?php echo $step_info['description']; ?></p>
	<div class="wt_iew_warn wt_iew_post_type_wrn" style="display:none;">
		<?php _e('Please select a post type');?>
	</div>
	<table class="form-table wt-iew-form-table">
		<tr>
			<th><label><?php _e('Select a post type to export'); ?></label></th>
			<td>
				<select name="wt_iew_export_post_type">
					<option value="">-- <?php _e('Select post type'); ?> --</option>
					<?php
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