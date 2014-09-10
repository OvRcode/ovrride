<table class="widefat" style="font-family:monospace;">
	<thead>

		<tr>
			<th colspan="2">Export Details</th>
		</tr>

	</thead>
	<tbody>

		<tr>
			<th style="width:20%;"><?php _e( 'Dataset', 'woo_ce' ); ?></th>
			<td><?php echo woo_ce_export_type_label( $dataset ); ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Filepath', 'woo_ce' ); ?></th>
			<td><?php echo $filepath; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Total columns', 'woo_ce' ); ?></th>
			<td><?php echo $columns; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Total rows', 'woo_ce' ); ?></th>
			<td><?php echo $rows; ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Process time', 'woo_ce' ); ?></th>
			<td><?php echo woo_ce_display_time_elapsed( $start_time, $end_time ); ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Idle memory usage (start)', 'woo_ce' ); ?></th>
			<td><?php woo_ce_display_memory( $idle_memory_start ); ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Memory usage prior to loading dataset', 'woo_ce' ); ?></th>
			<td><?php woo_ce_display_memory( $data_memory_start ); ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Memory usage after loading dataset', 'woo_ce' ); ?></th>
			<td><?php woo_ce_display_memory( $data_memory_end ); ?></td>
		</tr>
		<tr>
			<th><?php _e( 'Memory usage at render time', 'woo_ce' ); ?></th>
			<td>-</td>
		</tr>
		<tr>
			<th><?php _e( 'Idle memory usage (end)', 'woo_ce' ); ?></th>
			<td><?php woo_ce_display_memory( $idle_memory_end ); ?></td>
		</tr>

	</tbody>
</table>
<br />