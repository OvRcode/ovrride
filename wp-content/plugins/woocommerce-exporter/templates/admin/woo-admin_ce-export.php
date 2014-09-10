<script type="text/javascript">
	function showProgress() {
		window.scrollTo(0,0);
		document.getElementById('progress').style.display = 'block';
		document.getElementById('content').style.display = 'none';
		document.getElementById('support-donate_rate').style.display = 'none';
	}
</script>

<div id="content">

	<h2 class="nav-tab-wrapper">
		<a data-tab-id="overview" class="nav-tab<?php woo_ce_admin_active_tab( 'overview' ); ?>" href="<?php echo add_query_arg( 'page', 'woo_ce', 'admin.php' ); ?>"><?php _e( 'Overview', 'woo_ce' ); ?></a>
		<a data-tab-id="export" class="nav-tab<?php woo_ce_admin_active_tab( 'export' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'export' ), 'admin.php' ); ?>"><?php _e( 'Export', 'woo_ce' ); ?></a>
		<a data-tab-id="archive" class="nav-tab<?php woo_ce_admin_active_tab( 'archive' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'archive' ), 'admin.php' ); ?>"><?php _e( 'Archives', 'woo_ce' ); ?></a>
		<a data-tab-id="tools" class="nav-tab<?php woo_ce_admin_active_tab( 'tools' ); ?>" href="<?php echo add_query_arg( array( 'page' => 'woo_ce', 'tab' => 'tools' ), 'admin.php' ); ?>"><?php _e( 'Tools', 'woo_ce' ); ?></a>
	</h2>
	<?php woo_ce_tab_template( $tab ); ?>

</div>
<!-- #content -->

<div id="progress" style="display:none;">
	<p><?php _e( 'Chosen WooCommerce details are being exported, this process can take awhile. Time for a beer?', 'woo_ce' ); ?></p>
	<img src="<?php echo plugins_url( '/templates/admin/images/progress.gif', $woo_ce['relpath'] ); ?>" alt="" />
	<p><?php _e( 'When the download is complete, return to <a href="' . $url . '">WooCommerce Exporter</a>.', 'woo_ce' ); ?>
</div>
<!-- #progress -->