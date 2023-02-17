<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_export_main">
    <p><?php echo $step_info['description']; ?></p>
    <form class="wt_iew_export_filter_form">
	    <table class="form-table wt-iew-form-table wt-iew-export-filter-table">				
			<?php
			Wt_Import_Export_For_Woo_Basic_Common_Helper::field_generator($filter_screen_fields, $filter_form_data);
			?>
	    </table>
    </form>
</div>