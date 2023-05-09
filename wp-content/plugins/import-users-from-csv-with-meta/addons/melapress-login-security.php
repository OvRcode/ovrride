<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'melapress-login-security/melapress-login-security.php' ) ){
	return;
}

class ACUI_MelapressLoginSecurity{
	function __construct(){
    }
    
    function hooks(){
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_action( 'post_acui_import_single_user', array( $this, 'import' ), PHP_INT_MAX, 10  );
		add_action( 'acui_after_import_users', array( $this, 'maybe_reactivate_force_reset_password_login' ), 10, 4 );
	}

	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( "MelaPress Login Security is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td>
				<?php _e( "This security plugin allows you to force a password reset for users at the first login. If you have this plugin option enabled, users registered through an import will behave in the same way as manually registered users, i.e. they will be prompted to reset their password on first login using the same method as if they had been added manually", 'import-users-from-csv-with-meta' ); ?>.
			</td>
		</tr>
		<?php
	}

	function import( $headers, $data, $user_id, $role, $positions, $form_data, $is_frontend, $is_cron, $password_changed, $created ){
		if( !$created )
			return;

		$ppm_wp_history = new PPM_WP_History();
		$userdata = get_userdata( $user_id );
		$password = $userdata->user_pass;

		$password_event = array(
			'password'  => $password,
			'timestamp' => current_time( 'timestamp' ),
			'by'        => 'user',
			'pest'      => 'sss',
		);

		PPM_WP_History::_push( $user_id, $password_event );

		update_user_meta( $user_id, 'ppmwp_last_activity', current_time( 'timestamp' ) );
		
		if( !$ppm_wp_history->ppm_get_first_login_policy( $user_id ) )
			return;
		
		$ppm_wp_history->ppm_apply_forced_reset_usermeta( $user_id );
		$userdata = get_user_by( 'id', $user_id );
		$key = get_password_reset_key( $userdata );
		if ( !is_wp_error( $key ) ) {
			update_user_meta( $user_id, PPM_WP_META_USER_RESET_PW_ON_LOGIN, $key );
		}

		global $wp_hasher;
		wp_update_user( array( 'ID' => $user_id, 'user_activation_key' => time() . ':' . $wp_hasher->HashPassword( $key ) ) );
	}

	function maybe_reactivate_force_reset_password_login( $users_created, $users_updated, $users_deleted, $users_ignored ){	
		$ppm_wp_history = new PPM_WP_History();
		global $wp_hasher;

		foreach( $users_created as $user_id ){
			$userdata = get_userdata( $user_id );
			if( empty( $userdata ) )
				continue;

			$password = $userdata->user_pass;

			$password_event = array(
				'password'  => $password,
				'timestamp' => current_time( 'timestamp' ),
				'by'        => 'user',
				'pest'      => 'sss',
			);

			PPM_WP_History::_push( $user_id, $password_event );

			update_user_meta( $user_id, 'ppmwp_last_activity', current_time( 'timestamp' ) );
			
			if( !$ppm_wp_history->ppm_get_first_login_policy( $user_id ) )
				return;
			
			$ppm_wp_history->ppm_apply_forced_reset_usermeta( $user_id );
			$userdata = get_user_by( 'id', $user_id );
			$key = get_password_reset_key( $userdata );
			if ( !is_wp_error( $key ) ) {
				update_user_meta( $user_id, PPM_WP_META_USER_RESET_PW_ON_LOGIN, $key );
			}

			wp_update_user( array( 'ID' => $user_id, 'user_activation_key' => time() . ':' . $wp_hasher->HashPassword( $key ) ) );
		}

		delete_transient( 'acui_last_import_results' );
	}
}

$acui_melapress_login_security = new ACUI_MelapressLoginSecurity();
$acui_melapress_login_security->hooks();