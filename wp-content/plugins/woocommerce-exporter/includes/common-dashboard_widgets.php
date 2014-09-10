<?php
/*

Filename: common-dashboard_widgets.php
Description: common-dashboard_widgets.php loads commonly access Dashboard widgets across the Visser Labs suite.
Version: 1.1

*/

/* Start of: WooCommerce News - by Visser Labs */

if( !function_exists( 'woo_vl_dashboard_setup' ) ) {

	function woo_vl_dashboard_setup() {

		wp_add_dashboard_widget( 'woo_vl_news_widget', __( 'WooCommerce Plugin News - by Visser Labs', 'woo_vl' ), 'woo_vl_news_widget' );

	}
	add_action( 'wp_dashboard_setup', 'woo_vl_dashboard_setup' );

	function woo_vl_news_widget() {

		include_once( ABSPATH . WPINC . '/feed.php' );

		$rss = fetch_feed( 'http://www.visser.com.au/blog/category/woocommerce/feed/' );
		$output = '<div class="rss-widget">';
		if( !is_wp_error( $rss ) ) {
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
			$output .= '<ul>';
			foreach ( $rss_items as $item ) :
				$output .= '<li>';
				$output .= '<a href="' . $item->get_permalink() . '" title="' . 'Posted ' . $item->get_date( 'j F Y | g:i a' ) . '" class="rsswidget">' . $item->get_title() . '</a>';
				$output .= '<span class="rss-date">' . $item->get_date( 'j F, Y' ) . '</span>';
				$output .= '<div class="rssSummary">' . $item->get_description() . '</div>';
				$output .= '</li>';
			endforeach;
			$output .= '</ul>';
		} else {
			$message = __( 'Connection failed. Please check your network settings.', 'woo_vl' );
			$output .= '<p>' . $message . '</p>';
		}
		$output .= '</div>';

		echo $output;

	}

}

/* End of: WooCommerce News - by Visser Labs */

/* Start of: WooCommerce Plugins - by Visser Labs */

if( !function_exists( 'woo_vm_dashboard_setup' ) ) {

	function woo_vm_dashboard_setup() {

		global $woo_ce;

		$plugin_slug = $woo_ce['dirname'];

		if( current_user_can( 'manage_options' ) ) {
			wp_add_dashboard_widget( 'woo_vm_status_widget', __( 'WooCommerce Plugins - by Visser Labs', 'woo_vm' ), 'woo_vm_status_widget' );
			if( file_exists( STYLESHEETPATH . '/woo-admin_dashboard_vm-plugins.css' ) )
				wp_enqueue_style( 'woo_vm_styles', get_bloginfo( 'stylesheet_directory' ) . '/woo-admin_dashboard_vm-plugins.css', false );
			else
				wp_enqueue_style( 'woo_vm_styles', plugins_url( $plugin_slug . '/templates/admin/woo-admin_dashboard_vm-plugins.css' ) );
		}

	}
	add_action( 'wp_dashboard_setup', 'woo_vm_dashboard_setup' );

	function woo_vm_status_widget() {

		global $woo_ce;

		$plugin_path = $woo_ce['abspath'];

		$check = wp_remote_fopen( 'http://www.visser.com.au/?woo_vm_data' );
		$vl_plugins = array();
		if( $check ) {
			$raw_plugins = explode( '<br />', $check );
			foreach( $raw_plugins as $raw_plugin ) {
				$raw_plugin = explode( '@', $raw_plugin );
				$vl_plugins[] = array(
					'name' => $raw_plugin[1],
					'version' => $raw_plugin[3],
					'url' => $raw_plugin[5]
				);
			}
		}

		$wp_plugins = get_plugins();
		foreach( $wp_plugins as $wp_plugin ) {
			if( $wp_plugin['Author'] == 'Visser Labs' ) {
				if( $vl_plugins ) {
					$size = count( $vl_plugins );
					for( $i = 0; $i < $size; $i++ ) {
						if( $vl_plugins[$i]['name'] == $wp_plugin['Name'] ) {
							$vl_plugins[$i]['name'] = str_replace( 'WooCommerce - ', '', $vl_plugins[$i]['name'] );
							$vl_plugins[$i]['installed'] = true;
							if( ( version_compare( strval( $vl_plugins[$i]['version'] ), strval( $wp_plugin['Version'] ), '>' ) == 1 ) ) {
								$wp_plugins_update = true;
								$vl_plugins[$i]['version_existing'] = $wp_plugin['Version'];
							}
							if( strval( $wp_plugin['Version'] ) > strval( $vl_plugins[$i]['version'] ) )
								$vl_plugins[$i]['version_beta'] = $wp_plugin['Version'];
						}
					}
				}
			}
		}

		include_once( $plugin_path . '/templates/admin/woo-admin_dashboard_vm-plugins.php' );

	}

}

/* End of: WooCommerce Plugins - by Visser Labs */
?>