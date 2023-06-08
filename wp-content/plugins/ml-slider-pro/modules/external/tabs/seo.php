<div class='row'><label><?php echo esc_html__("Image Title Text", "ml-slider-pro"); ?></label></div>
<div class='row'><input class='url' type='text' name='attachment[<?php echo esc_attr($this->slide->ID); ?>][title]' placeholder='Title Text' value='<?php echo esc_attr($title); ?>' /></div>
<div class='row'><label><?php echo esc_html__("Image Alt Text", "ml-slider-pro"); ?></label></div>
<div class='row'><input class='url' type='text' name='attachment[<?php echo esc_attr($this->slide->ID); ?>][alt]' placeholder='Alt Text' value='<?php echo esc_attr($alt); ?>' /></div>
