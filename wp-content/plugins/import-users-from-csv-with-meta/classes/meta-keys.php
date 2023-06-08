<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class ACUI_MetaKeys{
	public static function admin_gui(){
		$meta_keys_obj = new ACUI_MetaKeys_Table();
	?>
	<style type="text/css">
		.tablenav.top{
			display: none;
		}
	</style>
	<h3><?php _e( 'Meta keys used in your database for users', 'import-users-from-csv-with-meta' ); ?></h3>
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
			<div id="post-body-content">
				<div class="meta-box-sortables ui-sortable">
					<form method="post">
						<?php
						$meta_keys_obj->prepare_items();
						$meta_keys_obj->display(); ?>
					</form>
				</div>
			</div>
		</div>
		<br class="clear">
	</div>
		<?php
	}
}

class ACUI_MetaKeys_Table extends WP_List_Table {
	public function __construct() {
		parent::__construct( [
			'singular' => 'Meta key',
			'plural'   => 'Meta keys',
			'ajax'     => false,
		] );
	}

	function get_columns() {
		$columns = [
			'meta_key' => 'Meta key',
			'type' => 'Type',
			'example' => 'Example'
		];

		return $columns;
	}

	static function get_meta_keys() {
		global $wpdb;

	    $meta_keys = array();

	    $select = "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta ORDER BY $wpdb->usermeta.meta_key";
	    $usermeta = $wpdb->get_results( $select, ARRAY_A );
	  
	  	foreach ($usermeta as $key => $value) {
	  		$meta_key = array();
	  		$meta_key['meta_key'] = $value["meta_key"];
	  		$meta_key['type'] = "";
	  		$meta_key['example'] = "";

	  		$meta_keys[] = $meta_key;
	  	}

	    return $meta_keys;
	}
	
	static function record_count() {
		global $wpdb;

	    $select = "SELECT distinct $wpdb->usermeta.meta_key FROM $wpdb->usermeta";
	    $usermeta = $wpdb->get_results( $select );

	    return count( $usermeta );
	}

	function no_items() {
		_e( 'No meta keys availale.', 'sp' );
	}

	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'meta_key':
				return $item['meta_key'];

			case 'type':
				return $this->get_type( $item['meta_key'] );

			case 'example':
				return $this->get_example( $item['meta_key'] );
		}
	}

	function get_example( $meta_key ){
		global $wpdb;
	    $select = $wpdb->prepare( "SELECT $wpdb->usermeta.meta_value FROM $wpdb->usermeta WHERE meta_key = %s AND meta_value IS NOT NULL AND meta_value <> '' LIMIT 1", $meta_key );
	    $usermeta = $wpdb->get_results( $select, ARRAY_A);

		if( count( $usermeta ) == 0 )
			return '';

	    $usermeta = reset( $usermeta );

	    return $usermeta['meta_value'];
	}

	function get_type( $meta_key ){
		$example = $this->get_example( $meta_key );
		if( empty( $example ) )
			return '';

		return is_serialized( $example ) ? 'Serialized' : 'Non serialized';
	}

	function column_name( $item ) {
		return '<strong>' . $item['name'] . '</strong>';
	}

	function prepare_items() {
		$columns = $this->get_columns();
		$this->_column_headers = array( $columns, array(), array() );

		$this->items = self::get_meta_keys();
	}
}