<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Force_Reset_Password{
    function __construct(){
    }

    function hooks(){
        add_action( 'post_acui_import_single_user', array( $this, 'new_user' ), 10, 9 );
		add_action( 'personal_options_update', array( $this, 'updated' ) );
		add_action( 'template_redirect', array( $this, 'redirect' ) );
		add_action( 'current_screen', array( $this, 'redirect' ) );
		add_action( 'admin_notices', array( $this, 'notice' ) );
		add_action( 'wp_ajax_acui_force_reset_password_delete_metas', array( $this, 'ajax_force_reset_password_delete_metas' ) );
    }

	function new_user( $headers, $data, $user_id, $role, $positions, $form_data, $is_frontend, $is_cron, $password_changed ){
        if( isset( $form_data["force_user_reset_password"] ) && in_array( $form_data["force_user_reset_password"], array( 'yes', 1 ) ) && $password_changed )
		    update_user_meta( $user_id, 'acui_force_reset_password', 1 );
	}

	function updated( $user_id ){
		$pass1 = $pass2 = '';

		if ( isset( $_POST['pass1'] ) )
			$pass1 = $_POST['pass1'];

		if ( isset( $_POST['pass2'] ) )
			$pass2 = $_POST['pass2'];

		if ( $pass1 != $pass2 || empty( $pass1 ) || empty( $pass2 ) || false !== strpos( stripslashes( $pass1 ), "\\" ) )
			return;

		delete_user_meta( $user_id, 'acui_force_reset_password' );
	}

	function redirect() {
        if( is_admin() ) {
			$screen = get_current_screen();

			if ( in_array( $screen->base, array( 'profile', 'plugins' ) ) )
				return;
		}

		if( !is_user_logged_in() )
			return;

        if( apply_filters( 'acui_force_reset_password_redirect_condition', false ) )
            return;

		if( get_user_meta( get_current_user_id(), 'acui_force_reset_password', true ) ) {
			wp_redirect( apply_filters( 'acui_force_reset_password_edit_profile_url', admin_url( 'profile.php' ) ) );
			die();
		}
	}

	function notice(){
		if ( get_user_meta( get_current_user_id(), 'acui_force_reset_password', true ) ) {
			printf( '<div class="error"><p>%s</p></div>', apply_filters( 'acui_force_reset_password_message', __( 'Please change your password', 'import-users-from-csv-with-meta' ) ) );
		}
	}

	function ajax_force_reset_password_delete_metas(){
		global $wpdb;
		$rows = $wpdb->delete( $wpdb->usermeta, array( 'meta_key' => 'acui_force_reset_password' ) );
		$result = ( $rows === false ) ? 'ERROR' : $rows;
		
		echo $result;
		wp_die();
	}
}

$acui_force_reset_password = new ACUI_Force_Reset_Password();
$acui_force_reset_password->hooks();