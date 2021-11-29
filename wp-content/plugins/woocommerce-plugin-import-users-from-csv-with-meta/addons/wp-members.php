<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'wp-members/wp-members.php' ) ){
	return;
}

add_action( 'acui_tab_import_before_import_button', 'acui_wp_members_tab_import_before_import_button' );
function acui_wp_members_tab_import_before_import_button(){
	?>
	<h2><?php _e( 'WP Members compatibility', 'import-users-from-csv-with-meta'); ?></h2>

	<table class="form-table">
		<tbody>
		<tr class="form-field form-required">
			<th scope="row"><label>Activate user when they are being imported?</label></th>
			<td>
				<select name="activate_users_wp_members">
					<option value="no_activate"><?php _e( 'Do not activate users', 'import-users-from-csv-with-meta' ); ?></option>
					<option value="activate"><?php _e( 'Activate users when they are being imported', 'import-users-from-csv-with-meta' ); ?></option>
				</select>

				<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/wp-members/"><?php _e( 'WP Members', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta' ); ?>)</strong>.</p>
			</td>
			
		</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'acui_tab_frontend_before_save_button', 'acui_wp_members_tab_frontend_before_save_button' );
function acui_wp_members_tab_frontend_before_save_button(){
	?>
	<h2><?php _e( 'WP Members compatibility', 'import-users-from-csv-with-meta'); ?></h2>
	<table class="form-table">
		<tbody>

		<tr class="form-field form-required">
			<th scope="row"><label>Activate user when they are being imported?</label></th>
			<td>
				<select name="activate-users-wp-members-frontend">
					<option value="no_activate" <?php selected( $activate_users_wp_members,'no_activate', true ); ?>><?php _e( 'Do not activate users', 'import-users-from-csv-with-meta' ); ?></option>
					<option value="activate"  <?php selected( $activate_users_wp_members,'activate', true ); ?>><?php _e( 'Activate users when they are being imported', 'import-users-from-csv-with-meta' ); ?></option>
				</select>

				<p class="description"><strong>(<?php _e( 'Only for', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/plugins/wp-members/"><?php _e( 'WP Members', 'import-users-from-csv-with-meta' ); ?></a> <?php _e( 'users', 'import-users-from-csv-with-meta' ); ?>)</strong>.</p>
			</td>
		</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'post_acui_import_single_user', 'acui_wp_members_post_acui_import_single_user', 10, 6 );
function acui_wp_members_post_acui_import_single_user( $headers, $data, $user_id, $role, $positions, $form_data ){
	$activate_users_wp_members = ( !isset( $form_data["activate_users_wp_members"] ) || empty( $form_data["activate_users_wp_members"] ) ) ? "no_activate" : sanitize_text_field( $form_data["activate_users_wp_members"] );
	if( $activate_users_wp_members == "activate" ){
		update_user_meta( $user_id, "active", true );
	}
}