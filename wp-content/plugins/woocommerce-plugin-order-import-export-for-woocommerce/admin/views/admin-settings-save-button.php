<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
$settings_button_title=isset($settings_button_title) ? $settings_button_title : 'Update Settings';
$before_button_text=isset($before_button_text) ? $before_button_text : '';
$after_button_text=isset($after_button_text) ? $after_button_text : '';
?>
<div style="clear: both;"></div>
<div class="wt-iew-plugin-toolbar bottom">
    <div class="left">
    </div>
    <div class="right">
    	<?php echo $before_button_text; ?>
        <input type="submit" name="wt_iew_update_admin_settings_form" value="<?php _e($settings_button_title); ?>" class="button button-primary" style="float:right;"/>
        <?php echo $after_button_text; ?>
        <span class="spinner" style="margin-top:11px"></span>
    </div>
</div>