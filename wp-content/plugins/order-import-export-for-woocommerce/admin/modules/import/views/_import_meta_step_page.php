<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<table class="wt-iew-mapping-tb wt-iew-importer-meta-mapping-tb" data-field-type="<?php echo $meta_mapping_screen_field_key; ?>">
	<thead>
		<tr>
    		<th>
    			<?php 
    			$is_checked=$meta_mapping_screen_field_val['checked'];
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
		
		foreach($meta_mapping_screen_field_val['fields'] as $key=>$val_arr)
		{
			extract($val_arr);
			include "_import_mapping_tr_html.php";
			$tr_count++;
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