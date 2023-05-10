<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_multisite() )
	return;

class ACUI_Multisite{
	var $sites;

	function __construct(){
		$this->sites = get_sites();

		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'assign' ), 10, 4 );
		add_filter( 'acui_email_apply_wildcards', array( $this, 'email_apply_wildcards' ), 10, 2 );
		add_action( 'acui_email_wildcards_list_elements', array( $this, 'email_wildcards_list_elements' ) );
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

	function email_apply_wildcards( $string, $args ){
		foreach( $this->sites as $subsite ) {
			$subsite_id = get_object_vars( $subsite )["blog_id"];
			$passwordreseturl_subsite = get_site_url( $subsite_id, 'wp-login.php?action=rp&key=' . $args['key'] . '&login=' . rawurlencode( $args['user_login'] ), 'login' );

			$string = str_replace( "**passwordreseturl_" . $subsite_id . "**", $passwordreseturl_subsite, $string );
		}

		
		return $string;
	}

	function email_wildcards_list_elements(){
		?>
		<?php _e( 'Multisite activated', 'import-users-from-csv-with-meta' ); ?>
		<ul style="list-style-type:disc; margin-left:2em;">
			<li>**passwordreseturl** = <?php _e( "It will work as follows: if there is only one site in the network, the URL will be that site's URL; if there is more than one site, it will lead to the login of the main site in the network of sites.", 'import-users-from-csv-with-meta' ); ?></li>
			<li>**passwordreseturllink** = <?php _e( "same behaviour as above", 'import-users-from-csv-with-meta' ); ?></li>
			<?php foreach( $this->sites as $subsite ): ?>
			<li>**passwordreseturl_<?php echo get_object_vars( $subsite )["blog_id"]; ?>**: <?php _e( 'password reset url for the sub-site', 'import-users-from-csv-with-meta' ); ?> <?php echo get_object_vars( $subsite )["path"]; ?></li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
}
new ACUI_Multisite();