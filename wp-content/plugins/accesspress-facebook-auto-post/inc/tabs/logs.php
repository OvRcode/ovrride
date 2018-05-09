<div class="asap-section" id="asap-section-logs" <?php if ($active_tab != 'logs') { ?>style="display: none;"<?php } ?>>
    <?php
    $table_name = $wpdb->prefix . "afap_logs";
    $logs = $wpdb->get_results("select * from $table_name order by log_id DESC limit 0,100", 'ARRAY_A');
    $afap_clear_log_nonce = wp_create_nonce('afap-clear-log-nonce');
    //$this->print_array($logs);
    ?>
    <div class="asap-clear-log"><a href="<?php echo admin_url("admin-post.php?action=afap_clear_log&_wpnonce=$afap_clear_log_nonce"); ?>" onclick="return confirm('<?php _e('Are you sure you want to clear all the logs', 'accesspress-facebook-auto-post'); ?>')"><input type="button" value="<?php _e('Clear Log', 'accesspress-facebook-auto-post') ?>"/></a></div>
    <table class="widefat stripped">
        <thead>
            <tr>
                <th><?php _e('Post ID', 'accesspress-facebook-auto-post'); ?></th>
                <th><?php _e('Status', 'accesspress-facebook-auto-post'); ?></th>
                <th><?php _e('Time', 'accesspress-facebook-auto-post'); ?></th>
                <th><?php _e('Log Details', 'accesspress-facebook-auto-post'); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th><?php _e('Post ID', 'accesspress-facebook-auto-post'); ?></th>
                <th><?php _e('Status', 'accesspress-facebook-auto-post'); ?></th>
                <th><?php _e('Time', 'accesspress-facebook-auto-post'); ?></th>
                <th><?php _e('Log Details', 'accesspress-facebook-auto-post'); ?></th>
            </tr>
        </tfoot>
        <tbody>
            <?php
            if (count($logs) > 0) {
                $log_count = 1;
                foreach ($logs as $log) {
                    $log_id = $log['log_id'];
                    $delete_nonce = wp_create_nonce('afap_delete_nonce');
                    $row_class = ($log_count % 2 == 0) ? 'asap-even-row' : 'asap-odd-row';
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td class="title column-title">
                            <a href="<?php echo admin_url("post.php?post={$log['post_id']}&action=edit"); ?>"><?php echo $log['post_id']; ?></a>
                            <div class="row-actions">
                                <span class="post-link"><a href="<?php echo admin_url("post.php?post={$log['post_id']}&action=edit"); ?>" target="_blank"><?php _e('Go to Post', 'accesspress-facebook-auto-post') ?></a></span>&nbsp;|&nbsp;
                                <span class="delete"><a href="<?php echo admin_url("admin-post.php?action=afap_delete_log&log_id=$log_id&_wpnonce=$delete_nonce"); ?>" onclick="return confirm('<?php _e('Are you sure you want to delete this log?', 'accesspress-facebook-auto-post'); ?>');">Delete</a></span>
                            </div>
                        </td>
                        <td>
                            <?php echo ($log['log_status'] == 1) ? __('Success', 'accesspress-facebook-auto-post') : __('Error', 'accesspress-facebook-auto-post'); ?> 
                        </td>
                        <td><?php echo $log['log_time']; ?></td>
                        <td><?php echo $log['log_details']; ?></td>
                    </tr>
                    <?php
                    $log_count++;
                }
            } else {
                ?>
                <tr colspan="3"><td><?php _e('No Logs found', 'accesspress-facebook-auto-post'); ?></td></tr>
                <?php
            }
            ?>

        </tbody>
    </table>
</div>