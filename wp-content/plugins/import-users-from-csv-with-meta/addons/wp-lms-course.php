<?php

/* this addon was originally developed by @egraznov https://wordpress.org/support/topic/lifterlms-addon/ */

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'lifterlms/lifterlms.php' ) ){
	return;
}

add_filter( 'acui_restricted_fields', 'acui_wlms_restricted_fields', 10, 1 );
add_action( 'acui_documentation_after_plugins_activated', 'acui_wlms_documentation_after_plugins_activated' );
add_action( 'post_acui_import_single_user', 'acui_wlms_post_import_single_user', 10, 3 );

function acui_wlms_restricted_fields( $acui_restricted_fields ){
	return array_merge( $acui_restricted_fields, array( 'lms_courses' ) );
}

function acui_wlms_documentation_after_plugins_activated(){
	?>
	<tr valign="top">
		<th scope="row"><?php _e( "LifterLMS is activated", 'import-users-from-csv-with-meta' ); ?></th>
		<td>
			<?php _e( "You can import users and assign them to LMS Course using next format", 'import-users-from-csv-with-meta' ); ?>.
			<ul style="list-style:disc outside none; margin-left:2em;">
				<li><?php _e( "lms_courses as the column title", 'import-users-from-csv-with-meta' ); ?></li>
				<li><?php _e( "The value of each cell will be the NUMBER of the course to enroll (do not use slugs)", 'import-users-from-csv-with-meta' ); ?></li>
				<li><?php _e( "If you want to import multiple values, you can use a list using / to separate items", 'import-users-from-csv-with-meta' ); ?></li>
			</ul>
		</td>
	</tr>
	<?php
}

function acui_wlms_post_import_single_user( $headers, $row, $user_id ){
	$pos = array_search( 'lms_courses', $headers );

	if( $pos === FALSE )
		return;

	$lms_courses = explode( '/', $row[ $pos ] );
	$lms_courses = array_filter( $lms_courses, function( $value ){ return $value !== ''; } );

	foreach ($lms_courses as $course) {
		if ( is_int( (int)$course ) ) {
			$trigger = 'admin_import_' . $user_id;
			$enrolled = llms_enroll_student( $user_id, (int)$course, $trigger );
		}    
	}
}