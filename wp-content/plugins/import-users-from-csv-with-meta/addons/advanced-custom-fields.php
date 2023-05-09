<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'advanced-custom-fields-pro/acf.php' ) && !is_plugin_active( 'advanced-custom-fields/acf.php' ) ){
	return;
}

class ACUI_ACF{
	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_filter( 'acui_not_meta_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'import' ), 10, 3 );
		add_filter( 'acui_export_columns', array( $this, 'export_columns' ), 10, 1 );
		add_filter( 'acui_export_data', array( $this, 'export_data' ), 10, 2 );
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, $this->get_user_fields_keys() );
	}

	function documentation(){
		$fields = $this->get_user_fields();
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "Advaced Custom Fields is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td>
				<?php _e( "You can import those fields, look at every group which fields you can import using the column names shown below.", 'import-users-from-csv-with-meta' ); ?>.
				<ul style="list-style:disc outside none; margin-left:2em;">
				<?php foreach ( $this->get_user_fields() as $group => $fields ): ?>
					<li><?php _e( "Group name", 'import-users-from-csv-with-meta' ); ?>: <em><?php echo $group; ?></em></li>
					<ul style="list-style:square inside none; margin-left:2em;">
						<?php foreach ( $fields as $field ): ?>
						<li><?php echo $field['label']; ?> <em>(type: <?php echo $field['type']; ?>)</em> - Column name in the CSV: <strong><?php echo $field['name']; ?></strong></li>
						<?php endforeach; ?>
					</ul>
				<?php endforeach; ?>
				</ul>
				<p class="description">In fields of type relationships, you can use post slug or post ID in the list of posts related.</p>
			</td>
		</tr>
		<?php
	}

	function import( $headers, $row, $user_id ){
		$fields_positions = array();
		$types = $this->get_user_fields_types();

		foreach ( $types as $key => $type ) {
			$pos = array_search( $key, $headers );

			if( $pos === FALSE )
				continue;

			$fields_positions[ $pos ] = $key;
		}

		foreach ( $fields_positions as $pos => $key ) {
			if( $types[ $key ][ 'type' ] == 'relationship' ){
				$data = explode( ',', $row[ $pos ] );

				foreach ( $data as $it => $value ) {
					$data[ $it ] = is_numeric( $value ) ? $value : ACUI_Helper::get_post_id_by_slug( $value );
				}
			}
			elseif( $types[ $key ][ 'multiple' ] ){
				$data = explode( ',', $row[ $pos ] );
				array_filter( $data, function( $value ){ return $value !== ''; } );
			}
			else{
				$data = $row[ $pos ];
			}
			
			update_field( $key, $data, "user_" . $user_id );
		}
	}

	function export_columns( $row ){
		foreach( $this->get_fields_with_url() as $field ){
			$row[ $field . "_url" ] = $field . "_url";
		}

		return $row;
	}

	function export_data( $row, $user ){
        foreach( $this->get_fields_with_url() as $field ){
			$row[] = wp_get_attachment_url( get_user_meta( $user, $field, true ) );
		}

		return $row;
	}

	function get_user_fields(){
		$fields = array();	
		$field_groups = acf_get_field_groups( array( 'user_id' => 'new', 'user_form' => '#your-profile' ) );
		
		if( empty( $field_groups ) ) 
			return array();
		
		//acf_form_data( array( 'post_id'	=> "user_new", 'nonce' => 'user' ) );
		
		foreach( $field_groups as $field_group ) {
			$fields[ $field_group['title'] ] = acf_get_fields( $field_group );
		}

		return $fields;
	}

	function get_user_fields_keys(){
		$fields = $this->get_user_fields();
		$fields_keys = array();

		if( empty( $fields ) )
			return array();

		foreach ( $fields as $group => $fields_of_group ){
			foreach ( $fields_of_group as $field ){
				$fields_keys[] = $field['name'];
			}
		}

		return $fields_keys;
	}

	function get_user_fields_types(){
		$fields = $this->get_user_fields();
		$fields_keys = array();
		$types_multiple = array( 'select', 'checkbox', 'radio', 'button_group' );

		if( empty( $fields ) )
			return array();

		foreach ( $fields as $group => $fields_of_group ){
			foreach ( $fields_of_group as $field ){
				$fields_keys[ $field['name'] ] = [
					'type' => $field['type'],
					'multiple' => !empty( $field['multiple'] ) || ( !isset( $field['multiple'] ) && in_array( $field['type'], $types_multiple ) ),
				];
			}
		}

		return $fields_keys;
	}

	function get_fields_with_url(){
		$fields = array();

		foreach( $this->get_user_fields_types() as $key => $field ){
			if( in_array( $field['type'], array( 'image', 'file', 'image_aspect_ratio_crop' ) ) )
				$fields[] = $key;
		}

		return $fields;
	}
}

new ACUI_ACF();