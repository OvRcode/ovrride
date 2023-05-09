<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<div class="wt_iew_history_page">
	<h2 class="wp-heading-inline"><?php _e('Import/Export history');?></h2>

	<div style="margin-bottom:25px;">
		<?php _e('Lists the runs and the status corresponding to every import/export with options to re-run, view detailed log or delete entry.');?>
	</div>


	<div class="wt_iew_history_settings">
		<form action="admin.php">
			<input type="hidden" name="page" value="<?php echo $this->module_id;?>">
			<?php	
			if(array_filter(array_column($filter_by, 'values')))
			{
			?>
				<div class="wt_iew_history_settings_hd"><?php _e('Filter'); ?></div>
				<div class="wt_iew_history_settings_form_group_box">
					<?php
					foreach ($filter_by as $filter_by_key => $filter_by_value) 
					{
						if(count($filter_by_value['values'])>0)
						{					
						?>
							<div class="wt_iew_history_settings_form_group">
								<label><?php echo $filter_by_value['label']; ?></label>
								<select name="wt_iew_history[filter_by][<?php echo $filter_by_key;?>]" class="wt_iew_select">
									<option value=""><?php _e('All'); ?></option>
									<?php
									$val_labels=$filter_by_value['val_labels'];
									foreach($filter_by_value['values'] as $val)
									{
										?>
										<option value="<?php echo $val;?>" <?php echo ($filter_by_value['selected_val']==$val ? 'selected="selected"' : '');?>><?php echo (isset($val_labels[$val]) ? $val_labels[$val] : $val);?></option>
										<?php
									}
									?>
								</select>
							</div>
						<?php
						}
					}
					?>
				</div>
			<?php 
			}
			?>

			<div class="wt_iew_history_settings_form_group_box">
				<div class="wt_iew_history_settings_form_group">
					<label><?php _e('Sort by'); ?></label>
					<select name="wt_iew_history[order_by]" class="wt_iew_select">
						<?php
						foreach ($order_by as $key => $value) 
						{
							?>
							<option value="<?php echo $key;?>" <?php echo ($order_by_val==$key ? 'selected="selected"' : '');?>><?php echo $value['label'];?></option>
							<?php
						}
						?>
					</select>
				</div>
				<div class="wt_iew_history_settings_form_group">
					<label><?php _e('Max record/page'); ?></label>
					<input type="text" name="wt_iew_history[max_data]" value="<?php echo $this->max_records;?>" class="wt_iew_text" style="width:50px;">
				</div>
			</div>
			<div class="wt_iew_history_settings_form_group_box">
				<input type="hidden" name="offset" value="0">
				<?php
				if($list_by_cron) /* list by cron */
				{
					?>
					<input type="hidden" name="wt_iew_cron_id" value="<?php echo $cron_id;?>">
					<?php
				}
				?>
				<button class="button button-primary" type="submit" style="float:left;"><?php _e('Apply'); ?></button>
			</div>
		</form>
	</div>
	
	<div class="wt_iew_bulk_action_box">
		<select class="wt_iew_bulk_action wt_iew_select">
			<option value=""><?php _e('Bulk Actions'); ?></option>
			<option value="delete"><?php _e('Delete'); ?></option>
		</select>
		<button class="button button-primary wt_iew_bulk_action_btn" type="button" style="float:left;"><?php _e('Apply'); ?></button>
	</div>
	<?php
	echo self::gen_pagination_html($total_records, $this->max_records, $offset, 'admin.php', $pagination_url_params);
	?>
	<?php
	if(isset($history_list) && is_array($history_list) && count($history_list)>0)
	{
		?>
		<table class="wp-list-table widefat fixed striped history_list_tb">
		<thead>
			<tr>
				<th width="100">
					<input type="checkbox" name="" class="wt_iew_history_checkbox_main">
					<?php _e("No."); ?>
				</th>
				<th width="50"><?php _e("Id"); ?></th>
				<th><?php _e("Action type"); ?></th>
				<th><?php _e("Post type"); ?></th>
				<th><?php _e("Started at"); ?></th>
				<th>
					<?php _e("Status"); ?>
					<span class="dashicons dashicons-editor-help wt-iew-tips" 
						data-wt-iew-tip="
						<span class='wt_iew_tooltip_span'><?php echo sprintf(__('%sSuccess%s - Process completed successfully'), '<b>', '</b>');?></span><br />
						<span class='wt_iew_tooltip_span'><?php echo sprintf(__('%sFailed%s - Failed process triggered due to connection/permission or similar issues(unable to establish FTP/DB connection, write permission issues etc.)'), '<b>', '</b>');?> </span><br />
						<span class='wt_iew_tooltip_span'><?php echo sprintf(__('%sRunning/Incomplete%s - Process that are running currently or that may have been terminated unknowingly(e.g, closing a browser tab while in progress etc)'), '<b>', '</b>');?> </span>">			
					</span>
				</th>
				<th>
					<?php _e("Actions"); ?>
					<span class="dashicons dashicons-editor-help wt-iew-tips" 
						data-wt-iew-tip=" <span class='wt_iew_tooltip_span'><?php _e('Re-run will take the user to the respective screen depending on the corresponding action type and the user can initiate the process accordingly.');?></span>"></span>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php
		$i=$offset;
		foreach($history_list as $key =>$history_item)
		{
			$i++;
			?>
			<tr>
				<th>
					<input type="checkbox" value="<?php echo $history_item['id'];?>" name="history_id[]" class="wt_iew_history_checkbox_sub">
					<?php echo $i;?>						
				</td>
				<td><?php echo $history_item['id']; ?></td>
				<td><?php echo ucfirst($history_item['template_type']); ?></td>
				<td><?php echo ucfirst($history_item['item_type']); ?></td>
				<td><?php echo date_i18n('Y-m-d h:i:s A', $history_item['created_at']); ?></td>
				<td>
					<?php
					echo (isset(self::$status_label_arr[$history_item['status']]) ? self::$status_label_arr[$history_item['status']] : __('Unknown'));
					?>
				</td>
				<td>
					<a class="wt_iew_delete_history" data-href="<?php echo str_replace('_history_id_', $history_item['id'], $delete_url);?>"><?php _e('Delete'); ?></a>
					<?php
					$form_data=maybe_unserialize($history_item['data']);
					$action_type=$history_item['template_type'];
					if($form_data && is_array($form_data))
					{
						$to_process=(isset($form_data['post_type_form_data']) && isset($form_data['post_type_form_data']['item_type']) ? $form_data['post_type_form_data']['item_type'] : '');
						if($to_process!="")
						{
							if(Wt_Import_Export_For_Woo_Admin_Basic::module_exists($action_type))
							{
								$action_module_id=Wt_Import_Export_For_Woo_Basic::get_module_id($action_type);
								$url=admin_url('admin.php?page='.$action_module_id.'&wt_iew_rerun='.$history_item['id']);
								?>
								 | <a href="<?php echo $url;?>" target="_blank"><?php _e("Re-Run");?></a>
								<?php
							}
						}
					}
					if($action_type=='import' && Wt_Import_Export_For_Woo_Admin_Basic::module_exists($action_type))
					{
						$action_module_obj=Wt_Import_Export_For_Woo_Basic::load_modules($action_type);
						$log_file_name=$action_module_obj->get_log_file_name($history_item['id']);
						$log_file_path=$action_module_obj->get_file_path($log_file_name);
						if(file_exists($log_file_path))
						{
						?>
							| <a class="wt_iew_view_log_btn" data-history-id="<?php echo $history_item['id'];?>"><?php _e("View log");?></a>
						<?php
						}
					}
                                        if($action_type=='export' && Wt_Import_Export_For_Woo_Admin_Basic::module_exists($action_type))
					{
                                            $export_download_url=wp_nonce_url(admin_url('admin.php?wt_iew_export_download=true&file='.$history_item['file_name']), WT_IEW_PLUGIN_ID_BASIC);
						?>
                                                        | <a class="wt_iew_export_download_btn" target="_blank" href="<?php echo $export_download_url;?>"><?php _e('Download');?></a>
						<?php
					}                                        
					?>
				</td>
			</tr>
			<?php	
		}
		?>
		</tbody>
		</table>
		<?php
		echo self::gen_pagination_html($total_records, $this->max_records, $offset, 'admin.php', $pagination_url_params);
	}else
	{
		?>
		<h4 class="wt_iew_history_no_records"><?php _e("No records found."); ?></h4>
		<?php
	}
	?>
</div>