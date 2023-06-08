<div class='row'><label><?php _e("External Image URL", "ml-slider-pro") ?></label></div>
<div class='row'><input class='url extimgurl' type='text' name='attachment[<?php echo esc_attr($this->slide->ID); ?>][extimgurl]' placeholder='Source Image URL' value='<?php echo esc_attr($extimgurl); ?>' /></div>
<div class='row'><label><?php echo esc_html("Link URL", "ml-slider-pro") ?></label></div>
<input class='url' type='text' name='attachment[<?php echo esc_attr($this->slide->ID); ?>][url]' placeholder='Link to URL' value='<?php echo esc_attr($url); ?>' />
<div class='new_window'>
	<label>New Window<input type='checkbox' name='attachment[<?php echo esc_attr($this->slide->ID); ?>][new_window]' <?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> /></label>
</div>
