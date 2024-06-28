<?php
if ( ! defined( 'WPINC' ) ) {
    die;
}
?>
<style type="text/css">
.wt_iew_history_page{ padding:15px; }
.history_list_tb td, .history_list_tb th{ text-align:center; }
.history_list_tb tr th:first-child{ text-align:left; }
.wt_iew_delete_history, .wt_iew_delete_log{ cursor:pointer; }
.wt_iew_history_settings{  float:left; width:100%; padding:15px; background:#fff; border:solid 1px #ccd0d4; box-sizing:border-box; margin-bottom:15px; }
.wt_iew_history_settings_hd{ float:left; width:100%; font-weight:bold; font-size:13px; }
.wt_iew_history_settings_form_group_box{ float:left; width:100%; box-sizing:border-box; padding:10px; padding-bottom:0px; height:auto; font-size:12px; }
.wt_iew_history_settings_form_group{ float:left; width:auto; margin-right:3%; min-width:200px;}
.wt_iew_history_settings_form_group label{ font-size:12px; font-weight:bold; }
.wt_iew_history_settings_form_group select, .wt_iew_history_settings_form_group input[type="text"]{ height:20px; }
.wt_iew_history_no_records{float:left; width:100%; margin-bottom:55px; margin-top:20px; text-align:center; background:#fff; padding:15px 0px; border:solid 1px #ccd0d4;}
.wt_iew_bulk_action_box{ float:left; width:auto; margin:10px 0px; }
select.wt_iew_bulk_action{ float:left; width:auto; height:20px; margin-right:10px; }
.wt_iew_view_log_btn{ cursor:pointer; }
.wt_iew_view_log{  }
.wt_iew_log_loader{ width:100%; height:200px; text-align:center; line-height:150px; font-size:14px; font-style:italic; }
.wt_iew_log_container{ padding:25px; }
.wt_iew_raw_log{ text-align:left; font-size:14px; }
.log_view_tb th, .log_view_tb td{ text-align:center; }
.log_list_tb .log_file_name_col{ text-align:left; }
</style>
<div class="wt_iew_view_log wt_iew_popup">
	<div class="wt_iew_popup_hd">
		<span style="line-height:40px;" class="dashicons dashicons-media-text"></span>
		<span class="wt_iew_popup_hd_label"><?php _e('View log');?></span>
		<div class="wt_iew_popup_close">X</div>
	</div>
	<div class="wt_iew_log_container">
		
	</div>
</div>
<?php
if(isset($_GET['page']) && $_GET['page']==$this->module_id.'_log')
{
	include plugin_dir_path(__FILE__)."/_log_list.php";
}else
{
	include plugin_dir_path(__FILE__)."/_history_list.php";	
}
?>