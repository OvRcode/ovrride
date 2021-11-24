<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'mailpoet/mailpoet.php' ) ){
	return;
}

add_filter( 'acui_restricted_fields', 'acui_mp_restricted_fields', 10, 1 );
add_action( 'acui_documentation_after_plugins_activated', 'acui_mp_documentation_after_plugins_activated' );
add_action( 'post_acui_import_single_user', 'acui_mp_post_import_single_user', 10, 3 );

function acui_mp_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, array( 'mailpoet_list_ids' ) );
}

function acui_mp_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "MailPoet is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td>
			<ol>
				<li><strong><?php _e( "Subscribe users to MailPoet lists", 'import-users-from-csv-with-meta' ); ?></strong>: <?php _e( "In this case you will only have to use <strong>mailpoet_list_ids</strong> column in order to associate a user to their users list, you can use a ID or a list of IDs separated by commas", 'import-users-from-csv-with-meta' ); ?>.</li>
			</ol>
		</td>
	</tr>
	<?php
}

function acui_mp_post_import_single_user( $headers, $row, $user_id ){
	if ( !class_exists(\MailPoet\API\API::class) ) {
		return;
	}

	$mailpoet_api = \MailPoet\API\API::MP('v1');
	$pos = array_search( 'mailpoet_list_ids', $headers );

	if( $pos === FALSE )
		return;

	$mailpoet_list_ids = explode( ',', $row[ $pos ] );
	$mailpoet_list_ids = array_filter( $mailpoet_list_ids, function( $value ){ return $value !== ''; } );

	$user = get_userdata( $user_id );

	if( !$user )
		return;

	try {
    	$resultado = $mailpoet_api->subscribeToLists( $user->user_email, $mailpoet_list_ids );
	} catch (Exception $e) { }
}