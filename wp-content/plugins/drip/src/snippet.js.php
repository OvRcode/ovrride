<?php
/**
 * The Drip JS snippet
 *
 * @package Drip_Woocommerce
 */

defined( 'ABSPATH' ) || die( 'Executing outside of the WordPress context.' );

$script_type = apply_filters('drip_set_snippet_script_type', 'text/javascript');
$script_additional_attributes = apply_filters('drip_set_snippet_script_additional_attributes', array());
?>

<!-- Drip Code -->
<script type="text/javascript">
	var _dcq = _dcq || [];
	var _dcs = _dcs || {};
	_dcs.account = '<?php echo esc_js( $account_id ); ?>';

	(function() {
		var dc = document.createElement('script');
		dc.type = '<?php echo esc_js( $script_type ); ?>'; 
		dc.async = true;
		dc.src = '//tag.getdrip.com/<?php echo esc_js( $account_id ); ?>.js';
		<?php
		if(is_array($script_additional_attributes)) {
			foreach($script_additional_attributes as $attname => $attval) {
				?>
				dc.setAttribute("<?php echo esc_js( $attname ); ?>", "<?php echo esc_js( $attval ); ?>");
				<?php
			}
		}
		?>
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(dc, s);
	})();
</script>
