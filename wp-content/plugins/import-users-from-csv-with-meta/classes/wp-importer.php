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
		$title = function_exists( 'is_woocommerce' ) ? __( 'Users and customers (in CSV format)', 'import-users-from-csv-with-meta' ) : __( 'Users (in CSV format)', 'import-users-from-csv-with-meta' );?>
		<p><label><input type="radio" name="content" value="users" aria-describedby="Users"> <?php echo $title; ?></label></p>
		<script>
		jQuery( document ).ready( function( $ ){
			$( '#export-filters' ).submit( function( e ){
				if( $('input[type="radio"][name="content"][value="users"').is(':checked') ){
					document.location.href='<?php echo admin_url( 'tools.php?page=acui&tab=export' ); ?>';
					return false;
				}

				return true;
			} );

			$( 'input[type="radio"][name="content"]' ).change( function(){
				if( $( this ).val() == 'users' ){
					$( '#submit' ).val( '<?php _e( 'Choose options...', 'import-users-from-csv-with-meta' ); ?>' );
				}
				else{
					$( '#submit' ).val( '<?php _e( 'Download Export File' ) ?>' );
				}
			});
		} )	
		</script>
		<?php
	}
}
new ACUI_WP_Importer_GUI();