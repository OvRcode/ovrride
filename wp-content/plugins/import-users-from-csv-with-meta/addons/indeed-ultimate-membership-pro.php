<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'indeed-membership-pro/indeed-membership-pro.php' ) ){
	return;
}

class ACUI_IndeedMemberShipPro{
	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'import_single_user' ), 10, 3 );
	}

	function fields(){
		return array( "level" );
	}
	
	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, $this->fields() );
	}
	
	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "Indeed Ultimate Membership Pro is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td>
				<?php _e( "You can use the columns in the CSV in order to import data from Indeed Ultimate Membership Pro.", 'import-users-from-csv-with-meta' ); ?>.
				<ul style="list-style:disc outside none; margin-left:2em;">
				<li>level: you have to use the level id, you can find it in "Levels" tab</li>
				</ul>
			</td>
		</tr>
		<?php
	}
	
	function import_single_user( $headers, $row, $user_id ){
		global $wpdb;
	
		$keys = $this->fields();
		$columns = array();
	
		foreach ( $keys as $key ) {
			$pos = array_search( $key, $headers );
	
			if( $pos !== FALSE ){
				$columns[ $key ] = $pos;
				$$key = $row[ $columns[ $key ] ];
			}
		}
	
		if( !empty( $level ) )
			$level = ihc_do_complete_level_assign_from_ap( $user_id, $level );
	}
}

new ACUI_IndeedMemberShipPro();