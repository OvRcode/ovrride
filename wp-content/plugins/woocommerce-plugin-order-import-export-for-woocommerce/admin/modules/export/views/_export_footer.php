<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt-iew-plugin-toolbar bottom">
	<div style="float:left; padding-top:10px;" class="wt_iew_export_template_name"> </div>
	<div style="float:right;">
		<div style="float:right;">
	        <?php
			$button_types = array_column(array_values($this->step_btns), 'type');
			$last_button_key = array_search('button', array_reverse($button_types, true));
			$count = 0;
			$button_standard_class = 'media-button';			
	        foreach($this->step_btns as $btnk=>$btnv)
	        { 	
	        	$css_class=(isset($btnv['class']) ? $btnv['class'] : '');
	        	$action_type=(isset($btnv['action_type']) ? $btnv['action_type'] : 'non-step');
				if($count == $last_button_key){
					$button_standard_class = 'button-primary';
				}				
	        	if($btnv['type']=='button')
	        	{
	        		?>
	        		<button class="button <?php echo $button_standard_class; ?> wt_iew_export_action_btn <?php echo $css_class; ?>" data-action-type="<?php echo $action_type; ?>" data-action="<?php echo $btnv['key'];?>" type="submit">
			        	<?php echo $btnv['text'];?>    		
			        </button>
	        		<?php

	        	}
	        	elseif($btnv['type']=='dropdown_button')
	        	{
	        		$btn_arr=(isset($btnv['items']) && is_array($btnv['items']) ? $btnv['items'] : array());
	        		?>
					<button type="button" class="button button-primary wt_iew_drp_menu <?php echo $css_class; ?>" data-target="wt_iew_<?php echo $btnk; ?>_drp">
						<?php echo $btnv['text'];?> <span class="dashicons dashicons-arrow-down" style="line-height: 28px;"></span>
					</button>
					<ul class="wt_iew_dropdown <?php echo $css_class; ?>" data-id="wt_iew_<?php echo $btnk; ?>_drp">
						<?php
						foreach($btn_arr as $btnkk => $btnvv)
						{
							$field_attr=(isset($btnvv['field_attr']) ? $btnvv['field_attr'] : '');
							$action_type=(isset($btnvv['action_type']) ? $btnvv['action_type'] : 'non-step');
							?>
							<li class="wt_iew_export_action_btn" data-action-type="<?php echo $action_type; ?>"  data-action="<?php echo $btnvv['key'];?>" <?php echo $field_attr;?> ><?php echo $btnvv['text'];?></li>
							<?php
						}
						?>
					</ul>
	        		<?php
	        	}
	        	elseif($btnv['type']=='hidden_button')
	        	{
	        		?>
	        		<button style="display:none;" class="button button-primary wt_iew_export_action_btn <?php echo $css_class; ?>" data-action-type="<?php echo $action_type; ?>" data-action="<?php echo $btnv['key'];?>" type="submit">
			        	<?php echo $btnv['text'];?>    		
			        </button>
	        		<?php

	        	}
	        	elseif($btnv['type']=='text')
	        	{
	        	?>
	        		<span style="line-height:40px; font-weight:bold;" class="<?php echo $css_class; ?>"><?php echo $btnv['text'];?></span>
	        	<?php
	        	}
				$count++;
	        }
	        ?>
		</div>
	</div>
	<span class="spinner" style="margin-top:11px;"></span>
</div>