<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_WP_Importer_GUI{
	function __construct(){
		add_action( 'admin_init', array( $this, 'register' ) );
		add_action( 'export_filters', array( $this, 'exporter' ) );
	}

	function register(){
		register_importer( 'acui_importer', __( 'Import users or customers (CSV)', 'import-users-from-csv-with-meta' ), __( 'Import <strong>users or customers</strong> to your site via a csv file.', 'import-users-from-csv-with-meta' ), array( $this, 'importer' ) );
	}

	function importer(){
		echo "<script>document.location.href='" . admin_url( 'tools.php?page=acui' ) . "'</script>";
	}

	function exporter(){
		?>
		<p><?php printf( __( 'You can also export users and customers in CSV format, filtering by user created date, role, choosing the delimiter and some other options using <a href="%s">this exporter</a>.', 'import-users-from-csv-with-meta' ), admin_url( 'tools.php?page=acui&tab=export' ) ); ?></p>
		<?php
	}
}
new ACUI_WP_Importer_GUI();