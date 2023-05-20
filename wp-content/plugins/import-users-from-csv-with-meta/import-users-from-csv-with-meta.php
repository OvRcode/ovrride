<?php
/*
Plugin Name:	Import and export users and customers
Plugin URI:		https://www.codection.com
Description:	Using this plugin you will be able to import and export users or customers choosing many options and interacting with lots of other plugins
Version:		1.22.4
Author:			codection
Author URI: 	https://codection.com
License:     	GPL2
License URI: 	https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: import-users-from-csv-with-meta
Domain Path: /languages
*/
if ( ! defined( 'ABSPATH' ) ) 
	exit;

define( 'ACUI_VERSION', '1.22.4' );

class ImportExportUsersCustomers{
	var $file;

	function __construct(){
	}

	function on_init(){
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		if( is_plugin_active( 'buddypress/bp-loader.php' ) || function_exists( 'bp_is_active' ) ){
			if ( defined( 'BP_VERSION' ) )
				$this->loader();
			else
				add_action( 'bp_init', array( $this, 'loader' ) );
		}
		else{
			$this->loader();
		}

		load_plugin_textdomain( 'import-users-from-csv-with-meta', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	function loader(){
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_filter( 'plugin_action_links', array( $this, 'action_links' ), 10, 2 );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'wp_check_filetype_and_ext' ), PHP_INT_MAX, 4 );
	
		if( is_plugin_active( 'buddypress/bp-loader.php' ) && file_exists( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' ) ){
			require_once( plugin_dir_path( __DIR__ ) . 'buddypress/bp-xprofile/classes/class-bp-xprofile-group.php' );	
		}
	
		// classes
		foreach ( glob( plugin_dir_path( __FILE__ ) . "classes/*.php" ) as $file ) {
			include_once( $file );
		}
	
		// includes
		foreach ( glob( plugin_dir_path( __FILE__ ) . "include/*.php" ) as $file ) {
			include_once( $file );
		}
	
		// addons
		foreach ( glob( plugin_dir_path( __FILE__ ) . "addons/*.php" ) as $file ) {
			include_once( $file );
		}
	}
	
	static function activate(){
		include_once( 'classes/options.php' );
		$acui_default_options_list = ACUI_Options::get_default_list();
			
		foreach ( $acui_default_options_list as $key => $value) {
			add_option( $key, $value, '', false );		
		}
	}

	static function deactivate(){
		wp_clear_scheduled_hook( 'acui_cron' );
	}

	function menu() {
		$acui_import = new ACUI_Import();
		add_submenu_page( 'tools.php', __( 'Import and export users and customers', 'import-users-from-csv-with-meta' ), __( 'Import and export users and customers', 'import-users-from-csv-with-meta' ), apply_filters( 'acui_capability', 'create_users' ), 'acui', array( $acui_import, 'show' ) );
	}
	
	function admin_enqueue_scripts( $hook ) {
		if( 'tools_page_acui' != $hook )
			return;
		
		wp_enqueue_style( 'acui_css', plugins_url( 'assets/style.css', __FILE__ ), false, ACUI_VERSION );
		wp_enqueue_style( 'datatable', '//cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css' );
		wp_enqueue_script( 'datatable', '//cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js' );

        if( isset( $_GET['tab'] ) && $_GET['tab'] == 'export' ){
            ACUI_Exporter::enqueue();
        }
	}

	function action_links( $links, $file ) {
		if ($file == 'import-users-from-csv-with-meta/import-users-from-csv-with-meta.php') {
			$links[] = sprintf( __( '<a href="%s">Export</a>', 'import-users-from-csv-with-meta' ), get_admin_url( null, 'tools.php?page=acui&tab=export' ) );
			$links[] = sprintf( __( '<a href="%s">Import</a>', 'import-users-from-csv-with-meta' ), get_admin_url( null, 'tools.php?page=acui&tab=homepage' ) );
			return array_reverse( $links );		
		}
		
		return $links; 
	}

	function plugin_row_meta( $links, $file ){
		if ( strpos( $file, basename( __FILE__ ) ) !== false ) {
			$new_links = array(
						'<a href="https://ko-fi.com/codection" target="_blank">' . __( 'Invite us for a coffee', 'import-users-from-csv-with-meta' ) . '</a>',
						'<a href="mailto:contacto@codection.com" target="_blank">' . __( 'Premium support', 'import-users-from-csv-with-meta' ) . '</a>',
						'<a href="https://codection.com/" target="_blank">' . __( 'RedSys and Ceca Gateways', 'import-users-from-csv-with-meta' ) . '</a>',
						'<a href="https://import-wp.com/" target="_blank" style="color:#d54e21;font-weight:bold">' . __( 'Premium addons and plugins', 'import-users-from-csv-with-meta' ) . '</a>',
					);
			
			$links = array_merge( $links, $new_links );
		}
		
		return $links;
	}

	function wp_check_filetype_and_ext( $values, $file, $filename, $mimes ) {
		if ( extension_loaded( 'fileinfo' ) ) {
			// with the php-extension, a CSV file is issues type text/plain so we fix that back to 
			// text/csv by trusting the file extension.
			$finfo     = finfo_open( FILEINFO_MIME_TYPE );
			$real_mime = finfo_file( $finfo, $file );
			finfo_close( $finfo );
			if ( $real_mime === 'text/plain' && preg_match( '/\.(csv)$/i', $filename ) ) {
				$values['ext']  = 'csv';
				$values['type'] = 'text/csv';
			}
		} else {
			// without the php-extension, we probably don't have the issue at all, but just to be sure...
			if ( preg_match( '/\.(csv)$/i', $filename ) ) {
				$values['ext']  = 'csv';
				$values['type'] = 'text/csv';
			}
		}
		return $values;
	}	
}

function acui_start(){
	$import_export_users_customers = new ImportExportUsersCustomers();
	add_action( 'init', array( $import_export_users_customers, 'on_init' ) );
}
add_action( 'plugins_loaded', 'acui_start', 8);

register_activation_hook( __FILE__, array( 'ImportExportUsersCustomers', 'activate' ) ); 
register_deactivation_hook( __FILE__, array( 'ImportExportUsersCustomers', 'deactivate' ) );