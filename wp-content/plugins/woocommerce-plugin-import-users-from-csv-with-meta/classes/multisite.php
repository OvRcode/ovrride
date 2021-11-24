<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_multisite() )
	return;

class ACUI_Multisite{
	function __construct(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'assign' ), 10, 4 );
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, array( 'blogs' ) );
	}

	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "Multisite is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( "Plugin can assing users to blogs after importing them roles. This is how it works:", 'import-users-from-csv-with-meta' ); ?>
				<ul style="list-style:disc outside none; margin-left:2em;">
					<li><?php _e( "You have to <strong>create a column called 'blogs'</strong>: if cell is empty, it won't assign users to any blog; if cell has a value, it will be used. You have to fill it with blog_id", 'import-users-from-csv-with-meta' ); ?></li>
					<li><?php _e( "Multiple blogs can be assigned creating <strong>a list of blog ids</strong> using commas to separate values.", 'import-users-from-csv-with-meta' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
	}

	function assign( $headers, $row, $user_id, $role ){
		$pos = array_search( 'blogs', $headers );

		if( $pos === FALSE )
			return;

		if( empty( $role ) )
			$role = 'subscriber';

		if( is_array( $role ) )
			$role = reset( $role );

		$user_blogs_csv = explode( ',', $row[ $pos ] );
		$user_blogs_csv = array_filter( $user_blogs_csv, function( $value ){ return $value !== ''; } );

		foreach ( $user_blogs_csv as $blog_id ) {
			add_user_to_blog( $blog_id, $user_id, $role );
		}
	}
}
new ACUI_Multisite();