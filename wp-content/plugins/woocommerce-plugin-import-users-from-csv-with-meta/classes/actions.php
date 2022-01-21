<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Actions{
	var $prefix;
	var $registered;
	
	function __construct(){
		$this->prefix = '#action_';
		$this->registered = array( 'assign_post' );
	}
	
	function hooks(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ) );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );

		foreach( $this->registered as $registered_action ){
			add_action( 'acui_action_' . $registered_action, array( $this, $registered_action ), 10, 6 );
		}

		add_action( 'post_acui_import_single_user', array( $this, 'run' ), 10, 8 );
	}

	function check_prefix( $header ){
		return ( substr( $header, 0, strlen( $this->prefix ) ) === $this->prefix );
	}

	function remove_prefix( $header ){
		return substr( $header, strlen( $this->prefix ) );
	}

	function restricted_fields( $fields ){
		$actions = array();
		foreach( $this->registered as $registered_action )
			$actions[] = $this->prefix . $registered_action;

		return array_merge( $fields, $actions );
	}

	function run( $headers, $data, $user_id, $role, $positions, $form_data, $is_frontend, $is_cron ){
		$actions = $this->get_actions_from_headers( $headers );

		if( empty( $actions ) )
			return;
		
		foreach( $actions as $pos => $action ){
			do_action( 'acui_action_' . $action, $user_id, $role, $data[ $pos ], $form_data, $is_frontend, $is_cron );
		}
	}

	function get_actions_from_headers( $headers ){
		$actions = array();

		foreach( $headers as $pos => $header ){
			if( $this->is_action( $header ) )
				$actions[ $pos ] = $this->remove_prefix( $header );
		}
		
		return $actions;
	}

	function is_action( $header ){
		if( !$this->check_prefix( $header ) )
			return false;

		return in_array( $this->remove_prefix( $header ), $this->registered );
	}

	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( 'Actions', 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( 'You can do some actions while you are importing. You have to name the column as indicated below and follow the instructions:', 'import-users-from-csv-with-meta' ); ?>
			<ul style="list-style:disc outside none;margin-left:2em;">
					<li><strong>#action_assign_post</strong>: <?php _e( 'Within each cell, you must indicate the post_id that will be assigned to this user. You can use a list separating each ID by commas. The post can be of any post type.', 'import-users-from-csv-with-meta' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
	}

	function assign_post( $user_id, $role, $data_cell, $form_data, $is_frontend, $is_cron ){
		$post_ids = explode( ',', $data_cell );

		foreach( $post_ids as $post_id ){
			wp_update_post( array(
				'ID' => intval( $post_id ),
				'post_author' => $user_id,
			) );
		}
	}
}

$acui_actions = new ACUI_Actions();
$acui_actions->hooks();