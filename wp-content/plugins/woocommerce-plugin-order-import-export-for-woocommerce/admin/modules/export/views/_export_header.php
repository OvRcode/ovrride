<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt-iew-settings-header">
	<h3>
		<?php _e('Export'); ?><?php if($this->step!='post_type'){ ?> <span class="wt_iew_step_head_post_type_name"></span><?php } ?>: <?php echo $this->step_title; ?>
	</h3>
	<span class="wt_iew_step_info" title="<?php echo $this->step_summary; ?>">
		<?php 
		echo $this->step_summary;
		?>
	</span>
</div>