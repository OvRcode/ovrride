<?php
$output = '';
if( $wp_plugins && $vl_plugins ) {
	$output .= '<div class="table table_content">';

	if( isset( $wp_plugins_update ) ) {
		$message = __( 'A new version of a Visser Labs Plugin for WooCommerce is available for download.', 'woo_vm' );
		$output .= '<p class="message">' . $message . '</p>';
	}

	$output .= '<table class="woo_vm_version_table">';
	$output .= '<tr><th style="text-align:left;">' . __( 'Plugin', 'woo_vm' ) . '</th><th style="text-align:left;">' . __( 'Version', 'woo_vm' ) . '</th><th style="text-align:left;">' . __( 'Status', 'woo_vm' ) . '</th></tr>';

	foreach( $vl_plugins as $vl_plugin ) {
		if( $vl_plugin['version'] ) {

			if( isset( $vl_plugin['installed'] ) ) {
				$output .= '<tr>';
				$output .= '<td><a href="' . $vl_plugin['url'] . '#toc-news" target="_blank">' . str_replace( ' for WooCommerce', '', $vl_plugin['name'] ) . '</a></td>';
				if( isset( $vl_plugin['version_existing'] ) ) {
					$output .= '<td class="version">' . $vl_plugin['version_existing'] . ' to <span>' . $vl_plugin['version'] . '</span></td>';
					if( $vl_plugin['url'] && current_user_can( 'update_plugins' ) )
						$output .= '<td class="status"><a href="update-core.php"><span class="red" title="Plugin update available for ' . $wp_plugin['Name'] . '.">' . __( 'Update', 'woo_vm' ) . '</span></a></td>';
					else
						$output .= '<td class="status"><span class="red" title="Plugin update available for ' . $wp_plugin['Name'] . '.">' . __( 'Update', 'woo_vm' ) . '</span></td>';
				} elseif( isset( $vl_plugin['version_beta'] ) ) {
					$output .= '<td class="version">' . $vl_plugin['version_beta'] . '</td>';
					$output .= '<td class="status"><span class="yellow" title="' . str_replace( ' for WooCommerce', '', $vl_plugin['name'] ) . ' is from the future.">' . __( 'Beta', 'woo_vm' ) . '</span></td>';
				} else {
					$output .= '<td class="version">' . $vl_plugin['version'] . '</td>';
					$output .= '<td class="status"><span class="green" title="' . str_replace( ' for WooCommerce', '', $vl_plugin['name'] ) . ' is up to date.">' . __( 'OK', 'woo_vm' ) . '</span></td>';
				}
				$output .= '</tr>';
			}
		}
		unset( $vl_plugin );
	}

	$output .= '</table>';

	$message = __( 'Looking for more WooCommerce Plugins?', 'woo_vm' );
	$output .= '<p class="link"><a href="http://www.visser.com.au/woocommerce/" target="_blank">' . $message . '</a></p>';

	$output .= '</div>';

} else {

	$message = __( 'Connection failed. Please check your network settings.', 'woo_wm' );
	$output .= '<p>' . $message . '</p>';

}
echo $output;
?>