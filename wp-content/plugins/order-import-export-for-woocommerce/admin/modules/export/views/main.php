<?php
/**
 * Main view file of export section
 *
 * @link            
 *
 * @package  Wt_Import_Export_For_Woo
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php
do_action('wt_iew_exporter_before_head');
$wf_admin_view_path=plugin_dir_path(WT_O_IEW_PLUGIN_FILENAME).'admin/views/';
?>
<style type="text/css">
.wt_iew_export_step{ display:none; }
.wt_iew_export_step_loader{ width:100%; height:400px; text-align:center; line-height:400px; font-size:14px; }
.wt_iew_export_step_main{ float:left; box-sizing:border-box; padding:15px; padding-bottom:0px; width:95%; margin:30px 2.5%; background:#fff; box-shadow:0px 2px 2px #ccc; border:solid 1px #efefef; }
.wt_iew_export_main{ padding:20px 0px; }
.wt_iew_file_ext_info_td{ vertical-align:top !important; }
.wt_iew_file_ext_info{ display:inline-block; margin-top:3px; }
</style>
<?php
Wt_Iew_IE_Basic_Helper::debug_panel($this->module_base);
?>
<?php include WT_O_IEW_PLUGIN_PATH."/admin/views/_save_template_popup.php"; ?>
<h2 class="wt_iew_page_hd"><?php _e('Export'); ?><span class="wt_iew_post_type_name"></span></h2>
<span class="wt-webtoffee-icon" style="float: <?php echo (!is_rtl()) ? 'right' : 'left'; ?>; padding-<?php echo (!is_rtl()) ? 'right' : 'left'; ?>:30px; margin-top: -25px;">
    <?php _e('Developed by'); ?> <a target="_blank" href="https://www.webtoffee.com">
        <img src="<?php echo WT_O_IEW_PLUGIN_URL.'/assets/images/webtoffee-logo_small.png';?>" style="max-width:100px;">
    </a>
</span>

<?php
	if($requested_rerun_id>0 && $this->rerun_id==0)
	{
		?>
		<div class="wt_iew_warn wt_iew_rerun_warn">
			<?php _e('Unable to handle Re-Run request.');?>
		</div>
		<?php
	}
?>

<div class="wt_iew_loader_info_box"></div>
<div class="wt_iew_overlayed_loader"></div>
<div class="wt_iew_export_step_main" style = "width:68%">
	<?php
	foreach($this->steps as $stepk=>$stepv)
	{
		?>
		<div class="wt_iew_export_step wt_iew_export_step_<?php echo $stepk;?>" data-loaded="0"></div>
		<?php
	}
	?>
</div>
<?php 
include $wf_admin_view_path."market.php";
/*
<script type="text/javascript">
/* external modules can hook 
function wt_iew_exporter_validate(action, action_type, is_previous_step)
{
	var is_continue=true;
	<?php
	do_action('wt_iew_exporter_validate');
	?>
	return is_continue;
}
</script> */ ?>