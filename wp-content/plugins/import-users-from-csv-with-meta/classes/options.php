<?php 

class ACUI_Options{
	static $prefix = 'acui_';

    static function get_default_list(){
		return array(
			self::$prefix . 'columns' => array(),
            // emails
			self::$prefix . 'mail_subject' => __('Welcome to', 'import-users-from-csv-with-meta') . ' ' . get_bloginfo("name"),
			self::$prefix . 'mail_body' => __('Welcome,', 'import-users-from-csv-with-meta') . '<br/>' . __('Your data to login in this site is:', 'import-users-from-csv-with-meta') . '<br/><ul><li>' . __('URL to login', 'import-users-from-csv-with-meta') . ': **loginurl**</li><li>' . __( 'Username', 'import-users-from-csv-with-meta') . '= **username**</li><li>Password = **password**</li></ul>',
			self::$prefix . 'mail_template_id' => 0,
			self::$prefix . 'mail_attachment_id' => 0,
			self::$prefix . 'enable_email_templates' => false,
			self::$prefix . 'mail_disable_wp_editor' => false,
			// cron																	
			self::$prefix . 'cron_activated' => false,
			self::$prefix . 'cron_send_mail' => false,
			self::$prefix . 'cron_send_mail_updated' => false,
			self::$prefix . 'cron_delete_users' => false,
			self::$prefix . 'cron_delete_users_assign_posts' => 0,
			self::$prefix . 'cron_change_role_not_present' => false,
			self::$prefix . 'cron_change_role_not_present_role' => 0,
			self::$prefix . 'cron_path_to_file' => '',
			self::$prefix . 'cron_path_to_move' => '',
			self::$prefix . 'cron_path_to_move_auto_rename' => false,
			self::$prefix . 'cron_period' => '',
			self::$prefix . 'cron_role' => '',
			self::$prefix . 'cron_update_roles_existing_users' => '',
			self::$prefix . 'cron_log' => '',
			self::$prefix . 'cron_allow_multiple_accounts' => 'not_allowed',
			// frontend
			self::$prefix . 'frontend_send_mail'=> false,
			self::$prefix . 'frontend_send_mail_updated' => false,
			self::$prefix . 'frontend_mail_admin' => false,
            self::$prefix . 'frontend_force_user_reset_password' => false,
            self::$prefix . 'frontend_send_mail_admin_address_list' => '',
			self::$prefix . 'frontend_delete_users' => false,
			self::$prefix . 'frontend_delete_users_assign_posts' => 0,
			self::$prefix . 'frontend_change_role_not_present' => false,
			self::$prefix . 'frontend_change_role_not_present_role' => 0,
			self::$prefix . 'frontend_role' => '',
			self::$prefix . 'frontend_update_existing_users' => false,
			self::$prefix . 'frontend_update_roles_existing_users' => false,
			// emials
			self::$prefix . 'manually_send_mail' => false,
			self::$prefix . 'manually_send_mail_updated' => false,
			self::$prefix . 'automatic_wordpress_email' => false,
			self::$prefix . 'automatic_created_edited_wordpress_email' => false,
			// profile fields
			self::$prefix . 'show_profile_fields' => false
		);
	}

	static function get_default( $key ){
		$defaults = self::get_default_list();
		return ( isset( $defaults[$key] ) ) ? $defaults[$key] : ''; 
	}

	static function prepare_key( $key, $class = '' ){
		return ( empty( $class ) ) ? self::$prefix . $key : self::$prefix . $class . $key;
	}

	static function get( $key, $class = '' ){
		$key = self::prepare_key( $key, $class );
		$value = get_option( $key );
		
		return ( !empty( $value ) ) ? $value : self::get_default( $key );	
	}

	static function update( $key, $value, $class = '' ){
		$key = self::prepare_key( $key, $class );
		return update_option( $key, $value );
	}

    static function save_options( $form_data, $is_cron = false, $is_frontend = false ){
		if( $is_frontend ){
            $form_data["send_mail_admin_frontend_address_list"] = isset( $form_data["send_mail_admin_frontend_address_list"] ) ? $form_data["send_mail_admin_frontend_address_list"] : '';

            update_option( "acui_frontend_send_mail", isset( $form_data["send-mail-frontend"] ) && $form_data["send-mail-frontend"] == "1" );
            update_option( "acui_frontend_send_mail_updated", isset( $form_data["send-mail-updated-frontend"] ) && $form_data["send-mail-updated-frontend"] == "1" );
            update_option( "acui_frontend_mail_admin", isset( $form_data["send_mail_admin_frontend"] ) && $form_data["send_mail_admin_frontend"] == "1" );
            update_option( "acui_frontend_force_user_reset_password", isset( $form_data["force_user_reset_password"] ) && $form_data["force_user_reset_password"] == "1" );
            update_option( "acui_frontend_send_mail_admin_address_list", sanitize_text_field( $form_data["send_mail_admin_frontend_address_list"] ) );
            update_option( "acui_frontend_delete_users", isset( $form_data["delete_users_frontend"] ) && $form_data["delete_users_frontend"] == "1" );
            update_option( "acui_frontend_delete_users_assign_posts", sanitize_text_field( $form_data["delete-users-assign-posts-frontend"] ) );
            update_option( "acui_frontend_change_role_not_present", isset( $form_data["change_role_not_present_frontend"] ) && $form_data["change_role_not_present_frontend"] == "1" );
            update_option( "acui_frontend_change_role_not_present_role", sanitize_text_field( $form_data["change_role_not_present_role_frontend"] ) );
            update_option( "acui_frontend_activate_users_wp_members", isset( $form_data["activate-users-wp-members-frontend"] ) ? sanitize_text_field( $form_data["activate-users-wp-members-frontend"] ) : 'no_activate' );

            update_option( "acui_frontend_role", sanitize_text_field( $form_data["role-frontend"] ) );
            update_option( "acui_frontend_update_existing_users", sanitize_text_field( $form_data["update_existing_users"] ) );
            update_option( "acui_frontend_update_roles_existing_users", sanitize_text_field( $form_data["update_roles_existing_users"] ) );
        }
    }
}