<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'wp-user-manager/wp-user-manager.php' ) ){
	return;
}

class ACUI_WP_User_Manager{
	function __construct(){
		add_filter( 'acui_force_reset_password_edit_profile_url', array( $this, 'force_reset_password_edit_profile_url' ) );
        add_filter( 'acui_force_reset_password_redirect_condition', array( $this, 'force_reset_password_redirect_condition' ), 10 , 1 );
        add_action( 'wpum_account_page_content', array( $this, 'force_reset_password_notice' ), 1 );
        add_action( 'wpum_after_user_password_recovery', array( $this, 'force_reset_save_account_details' ) );
        add_action( 'wpum_account_page_content', array( $this, 'maybe_force_reset_save_account_details' ), 0 );
	}

	function force_reset_password_edit_profile_url(){
        global $wpdb;
        $query = "SELECT ID FROM ".$wpdb->posts." WHERE post_content LIKE '%[wpum_account]%' AND post_status = 'publish'";
        $results = $wpdb->get_results( $query );
        $result = $results[0];
        
        return get_permalink( $result->ID ) . "/password";
    }

    function force_reset_password_redirect_condition( $condition ){
        global $post;
        return ( $post instanceof WP_Post ) ? has_shortcode( $post->post_content, 'wpum_account' ) : $condition;
    }

    function force_reset_password_notice(){
        if ( get_user_meta( get_current_user_id(), 'acui_force_reset_password', true ) ) {
            echo apply_filters( 'acui_force_reset_password_message', __( '<span class="acui_force_reset_password_message">Please change your password</span>', 'import-users-from-csv-with-meta' ) );
        }
    }

    function force_reset_save_account_details( $user_id ){
        delete_user_meta( $user_id, 'acui_force_reset_password' );
    }

    function maybe_force_reset_save_account_details(){
        if( !isset( $_GET ) || empty( $_GET ) )
            return;

        if( isset( $_GET['password-updated'] ) && $_GET['password-updated'] == 'success' && isset( $_GET['tab'] ) && $_GET['tab'] == 'password' )
            delete_user_meta( get_current_user_id(), 'acui_force_reset_password' );
    }
}
new ACUI_WP_User_Manager();