<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'woocommerce-membership/woocommerce-membership.php' ) ){
	return;
}

add_filter( 'acui_restricted_fields', 'acui_wmr_restricted_fields', 10, 1 );
add_action( 'acui_documentation_after_plugins_activated', 'acui_wmr_documentation_after_plugins_activated' );
add_action( 'post_acui_import_single_user', 'acui_wmr_post_import_single_user', 10, 3 );

function acui_wmr_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, array( 'plan_id' ) );
}

function acui_wmr_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "WooCommerce Membership by RightPress is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td>
			<ol>
				<li><strong><?php _e( "Add users to membership plans", 'import-users-from-csv-with-meta' ); ?></strong>: <?php _e( "In this case you will only have to use <strong>plan_id</strong> column in order to associate a user to their membership plan", 'import-users-from-csv-with-meta' ); ?>.</li>
			</ol>
		</td>
	</tr>
	<?php
}

function acui_wmr_post_import_single_user( $headers, $row, $user_id ){
	$pos = array_search( 'plan_id', $headers );

	if( $pos === FALSE )
		return;

	$plan_id = absint( $row[ $pos ] );
	$resultado = WooCommerce_Membership_Plan::add_member( $plan_id, $user_id );
}