<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wt_iew_import_main">
	<p><?php echo $this->step_description;?></p>
	<form class="wt_iew_import_advanced_form">
		<table class="form-table wt-iew-form-table">
			<?php
			Wt_Import_Export_For_Woo_Basic_Common_Helper::field_generator($advanced_screen_fields, $advanced_form_data);
			?>
		</table>
	</form>
</div>
<script type="text/javascript">
/* custom action: other than import, save, update. Eg: schedule */
function wt_iew_custom_action_basic(ajx_dta, action, id)
{
	ajx_dta['item_type']=ajx_dta['to_import'];
	<?php
	do_action('wt_iew_custom_action_basic');
	?>
}
</script>