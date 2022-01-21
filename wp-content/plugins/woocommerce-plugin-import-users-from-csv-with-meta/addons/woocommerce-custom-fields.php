<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'woocommerce-custom-fields/woocommerce-custom-fields.php' ) ){
	return;
}

class ACUI_WCF{
	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_filter( 'acui_not_meta_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'import' ), 10, 3 );    
	}

	function get_fields(){
		$customer_fields = get_posts( array(
			'post_type' => 'wccf_user_field',
			'posts_per_page' => -1,
		) );

		$result = array();

		foreach ( $customer_fields as $custom_field ) {
			$result[ $custom_field->ID ] = get_post_meta( $custom_field->ID, 'key', true );
		}

		return $result;
	}

  	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "WooCommerce Custom Fields is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td>
				<?php _e( "You can import those fields, look at fields you can import using the column names shown below.", 'import-users-from-csv-with-meta' ); ?>.
				<ul style="list-style:square inside none; margin-left:2em;">
					<?php foreach ( $this->get_fields() as $field => $field_id): ?>
					<li><?php echo $field; ?></li>
					<?php endforeach; ?>
				</ul>
			</td>
		</tr>
		<?php
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, array_keys( $this->get_fields() ) );
	}

	function import( $headers, $row, $user_id ){
		$context = 'user_field';
		$columns = array();
		$data = array();

		foreach ( $this->get_fields() as $key => $value ) {
			$pos = array_search( $value, $headers );

			if( $pos !== FALSE ){
				$columns[ $value ] = $pos;
				$data[ $value ] = $row[ $columns[ $value ] ];
			}
		}

		$values = array();
		foreach ( $this->get_fields() as $key => $value ) {
			$values[ $key ] = array( 'value' => $data[ $value ], 'data' => array(), 'files' => array() );
		}

		$item = RightPress_Help::wc_get_customer( $user_id );

		if( empty( $values ) || !is_array( $values ) ){
			return;
		}

		foreach( $values as $field_id => $field_value ) {
			$field = WCCF_Field_Controller::get( $field_id, 'wccf_' . $context );

			if ( !$field ) {
				continue;
			}

			$field->store_value( $item, $field_value );
		}

		$item->save();
	}
}

new ACUI_WCF();