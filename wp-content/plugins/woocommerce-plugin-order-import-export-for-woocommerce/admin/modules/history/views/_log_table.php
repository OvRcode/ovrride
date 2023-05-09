<?php
/**
 * Log table view file
 *
 * @link       
 *
 * @package  Wt_Import_Export_For_Woo 
 */
if (!defined('ABSPATH')) {
    exit;
}

$summary = array(
    'type' => array(
        0 => array(
            'count' => 0,
            'description' => __('Item with same ID already exists.'),
            'help_link' => 'https://www.webtoffee.com/how-to-resolve-id-conflict-during-import-in-woocommerce/',
            'error_code' => 'already exists'
        ),
        1 => array(
            'count' => 0,
            'description' => __('Importing item conflicts with an existing post.'),
            'help_link' => 'https://www.webtoffee.com/how-to-resolve-id-conflict-during-import-in-woocommerce/',
            'error_code' => 'conflicts with an existing post'
        ),
        2 => array(
            'count' => 0,
            'description' => __('Invalid product type.'),
            'help_link' => 'https://www.webtoffee.com/setting-up-product-import-export-plugin-for-woocommerce/',
            'error_code' => 'Invalid product type'
        )
    )
);
if(isset($log_list) && is_array($log_list) && count($log_list)>0)
{
	if($offset==0)
	{
	?>
		<table class="wp-list-table widefat fixed striped log_view_tb" style="margin-bottom:25px;">
		<thead>
			<tr>
				<th style="width:100px;"><?php _e("Row No."); ?></th>
				<th><?php _e("Status"); ?></th>
				<th><?php _e("Message"); ?></th>
				<th><?php _e("Item"); ?></th>
			</tr>
		</thead>
		<tbody class="log_view_tb_tbody">
	<?php
	}
	foreach($log_list as $key =>$log_item)
	{   
                if(!$log_item['status']){
                    if(strpos($log_item['message'], 'already exists')!==false){
                        $summary['type'][0]['count'] = $summary['type'][0]['count']+1;                      
                    }
                    if(strpos($log_item['message'], 'conflicts with an existing post')!==false){
                        $summary['type'][1]['count'] = $summary['type'][1]['count']+1;                       
                    }
                    if(strpos($log_item['message'], 'Invalid product type')!==false){
                        $summary['type'][2]['count'] = $summary['type'][2]['count']+1;                       
                    }
                }
		?>
		<tr>
			<td><?php echo absint($log_item['row']); ?></td>
			<td><?php echo ($log_item['status'] ? __('Success') : __('Failed/Skipped') ); ?></td>
			<td><?php esc_html_e($log_item['message']); ?></td>
			<td>
			<?php 
				if($show_item_details)
				{
					$item_data=$item_type_module_obj->get_item_by_id($log_item['post_id']);					
					if($item_data && isset($item_data['title']))
					{
						if(isset($item_data['edit_url']))
						{
							echo '<a href="'.$item_data['edit_url'].'" target="_blank">'.$item_data['title'].'</a>';
						}else
						{
							echo $item_data['title'];
						}
					}else
					{
						echo $log_item['post_id'];
					}
				}else
				{
					echo $log_item['post_id'];	
				}
			?>
			</td>
		</tr>
		<?php	
	}?>
                <div style="background-color: #f6f7f7;padding: 10px;">
            <?php

            foreach ($summary['type'] as $summary_row) {
                $summary_row_count = $summary_row['count'];
                $summary_row_help_link = $summary_row['help_link'];
                if($summary_row_count):
                ?>
                    <p><?php echo $summary_row['description']."($summary_row_count)";?> - <?php _e('Please refer')?> <a href="<?php echo $summary_row_help_link; ?>" target="_blank"><?php _e('this article');?></a> <?php _e('for troubleshoot.');?></p> 
          <?php 
                endif;
          
            }
        ?>
        </div>  
        <?php    
	if($offset==0)
	{
	?>
		</tbody>
		</table>
		<h4 style="margin-top:0px;"> 
			<a class="wt_iew_history_loadmore_btn button button-primary"> <?php _e("Load more."); ?></a>
			<span class="wt_iew_history_loadmore_loading" style="display:none;"><?php _e("Loading...."); ?></span>
		</h4>
	<?php
	}
}else
{
	?>
	<h4 style="margin-bottom:55px;"><?php _e("No records found."); ?> </h4>
	<?php
}
?>