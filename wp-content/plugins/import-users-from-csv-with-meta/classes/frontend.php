<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Frontend{
	function __construct(){
	}

    function hooks(){
        add_action( 'acui_frontend_save_settings', array( $this, 'save_settings' ), 10, 1 );
		add_action( 'acui_post_frontend_import', array( $this, 'email_admin' ) );
		add_shortcode( 'import-users-from-csv-with-meta', array( $this, 'shortcode_import' ) );
        add_shortcode( 'export-users', array( $this, 'shortcode_export' ) );
    }
	
	static function admin_gui(){
		$send_mail_frontend = get_option( "acui_frontend_send_mail" );
		$send_mail_updated_frontend = get_option( "acui_frontend_send_mail_updated" );
		$send_mail_admin_frontend = get_option( "acui_frontend_mail_admin" );
        $send_mail_admin_adress_list_frontend = get_option( "acui_frontend_send_mail_admin_address_list" );
		$delete_users_frontend = get_option( "acui_frontend_delete_users" );
		$delete_users_assign_posts_frontend = get_option( "acui_frontend_delete_users_assign_posts" );
		$change_role_not_present_frontend = get_option( "acui_frontend_change_role_not_present" );
		$change_role_not_present_role_frontend = get_option( "acui_frontend_change_role_not_present_role" );
		$role = get_option( "acui_frontend_role" );
		$update_existing_users = get_option( "acui_frontend_update_existing_users" );
		$update_roles_existing_users = get_option( "acui_frontend_update_roles_existing_users" );
		$activate_users_wp_members = get_option( "acui_frontend_activate_users_wp_members" );

		if( empty( $send_mail_frontend ) )
			$send_mail_frontend = false;

		if( empty( $send_mail_updated_frontend ) )
			$send_mail_updated_frontend = false;

		if( empty( $send_mail_admin_frontend ) )
			$send_mail_admin_frontend = false;
		
		if( empty( $update_existing_users ) )
			$update_existing_users = 'no';

		if( empty( $update_roles_existing_users ) )
			$update_roles_existing_users = 'no';
		?>
		<h3><?php _e( "Execute an import of users in the frontend", 'import-users-from-csv-with-meta' ); ?> <em><a href="#export_frontend">(<?php _e( "you can also do an export in the frontend", 'import-users-from-csv-with-meta' ); ?>)</a></em></h3>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
			<table class="form-table">
				<tbody>

				<tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Use this shortcode in any page or post', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<pre>[import-users-from-csv-with-meta]</pre>
						<input class="button-primary" type="button" id="copy_to_clipboard" value="<?php _e( 'Copy to clipboard', 'import-users-from-csv-with-meta'); ?>"/>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use role as attribute to choose directly in the shortcode the role to use during the import. Remind that you must use the role slug, for example:', 'import-users-from-csv-with-meta' ); ?> <pre>[import-users-from-csv-with-meta role="editor"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute delete-only-specified-role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use this attribute to make delete only users of the specified role that are not present in the CSV, for example:', 'import-users-from-csv-with-meta' ); ?> <pre>[import-users-from-csv-with-meta role="editor" delete-only-specified-role="true"]</pre> <?php _e( 'will only delete (if the deletion is active) the users not present in the CSV with are editors', 'import-users-from-csv-with-meta' ); ?>
					</td>
				</tr>
                </tbody>
            </table>

            <h2 id="acui_roles_header"><?php _e( 'Roles', 'import-users-from-csv-with-meta'); ?></h2>
            <table class="form-table">
                <tbody>
				<tr class="form-field form-required">
					<th scope="row"><label for="role"><?php _e( 'Default role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<?php ACUIHTML()->select( array(
                            'options' => ACUI_Helper::get_editable_roles(),
                            'name' => 'role-frontend',
                            'selected' => $role,
                            'show_option_all' => false,
                            'show_option_none' => __( 'Disable role assignment in frontend import', 'import-users-from-csv-with-meta' ),
                        )); ?>
						<p class="description"><?php _e( 'Which role would be used to import users?', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>
                </tbody>
            </table>

            <h2 id="acui_options_header"><?php _e( 'Options', 'import-users-from-csv-with-meta'); ?></h2>
            <table class="form-table">
                <tbody>

                <tr id="acui_send_email_wrapper" class="form-field">
					<th scope="row"><label for="user_login"><?php _e( 'Send mail', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<p id="sends_email_wrapper">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'send-mail-frontend', 'label' => __( 'Do you wish to send a mail with credentials and other data?', 'import-users-from-csv-with-meta' ), 'compare_value' => $send_mail_frontend ) ); ?>
						</p>
						<p id="send_email_updated_wrapper">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'send-mail-updated-frontend', 'label' => __( 'Do you wish to send this mail also to users that are being updated? (not only to the one which are being created)', 'import-users-from-csv-with-meta' ), 'compare_value' => $send_mail_updated_frontend ) ); ?>
						</p>
					</td>
				</tr>

                <tr class="form-field form-required">
					<th scope="row"><label for=""><?php _e( 'Force users to reset their passwords?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->checkbox( array( 'name' => 'force_user_reset_password', 'compare_value' => get_option( 'acui_frontend_force_user_reset_password' ) ) ); ?>
                        <p class="description"><?php _e( 'If a password is set to an user and you activate this option, the user will be forced to reset their password in their first login', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="send_mail_admin_frontend"><?php _e( 'Send notification to admin when the frontend importer is used?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <div style="float:left; margin-top: 10px;">
                        <?php ACUIHTML()->checkbox( array( 'name' => 'send_mail_admin_frontend', 'compare_value' => $send_mail_admin_frontend ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php ACUIHTML()->text( array( 'name' => 'send_mail_admin_frontend_address_list', 'value' => $send_mail_admin_adress_list_frontend, 'class' => '', 'placeholder' => __( 'Include a list of emails where notification will be sent, use commas to separate addresses', 'import-users-from-csv-with-meta' ) ) ); ?>
							<p class="description"><?php _e( 'If list is empty, the admin email will be used', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

			<h2><?php _e( 'Update users', 'import-users-from-csv-with-meta'); ?></h2>

			<table class="form-table">
				<tbody>
				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Update existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'import-users-from-csv-with-meta' ), 'yes' => __( 'Yes', 'import-users-from-csv-with-meta' ) ),
                            'name' => 'update_existing_users',
                            'selected' => $update_existing_users,
                            'show_option_all' => false,
                            'show_option_none' => false,
                        )); ?>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label><?php _e( 'Update roles for existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'import-users-from-csv-with-meta' ), 'yes_no_override' => __( 'Yes, add new roles and not override existing ones', 'import-users-from-csv-with-meta' ), 'yes' => __( 'Yes', 'import-users-from-csv-with-meta' ) ),
                            'name' => 'update_roles_existing_users',
                            'selected' => $update_roles_existing_users,
                            'show_option_all' => false,
                            'show_option_none' => false,
                        )); ?>
					</td>
				</tr>
				</tbody>
			</table>

			<h2><?php _e( 'Users not present in CSV file', 'import-users-from-csv-with-meta'); ?></h2>
			<table class="form-table">
				<tbody>

				<tr class="form-field form-required">
					<th scope="row"><label for="delete_users_frontend"><?php _e( 'Delete users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <div style="float:left; margin-top: 10px;">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'delete_users_frontend', 'compare_value' => $delete_users_frontend ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php ACUIHTML()->select( array(
                                'options' => ACUI_Helper::get_list_users_with_display_name(),
                                'name' => 'delete-users-assign-posts-frontend',
                                'selected' => $delete_users_assign_posts_frontend,
                                'show_option_all' => false,
                                'show_option_none' => __( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' ),
                            )); ?>
							<p class="description"><?php _e( 'After delete users, we can choose if we want to assign their posts to another user. Please do not delete them or posts will be deleted.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="change_role_not_present_frontend"><?php _e( 'Change role of users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'change_role_not_present_frontend', 'compare_value' => $change_role_not_present_frontend ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php ACUIHTML()->select( array(
                                'options' => ACUI_Helper::get_editable_roles(),
                                'name' => 'change_role_not_present_role_frontend',
                                'selected' => $change_role_not_present_role_frontend,
                                'show_option_all' => false,
                                'show_option_none' => false,
                            )); ?>
							<p class="description"><?php _e( 'After import users which is not present in the CSV and can be changed to a different role.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

			<?php wp_nonce_field( 'codection-security', 'security' ); ?>
			<input class="button-primary" type="submit" value="<?php _e( 'Save frontend import options', 'import-users-from-csv-with-meta'); ?>"/>
		</form>

        <h3 id="export_frontend"><?php _e( "Execute an export of users in the frontend", 'import-users-from-csv-with-meta' ); ?></h3>
        <table class="form-table">
			<tbody>

				<tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Use this shortcode in any page or post', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<pre>[export-users]</pre>
						<input class="button-primary" type="button" id="copy_to_clipboard_export" value="<?php _e( 'Copy to clipboard', 'import-users-from-csv-with-meta'); ?>"/>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use role as attribute to choose directly in the shortcode the role to use during the export. Remind that you must use the role slug, for example:', 'import-users-from-csv-with-meta' ); ?> <pre>[export-users role="editor"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute from', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use from attribute to filter users created from a specified date. Date format has to be: Y-m-d, for example:', 'import-users-from-csv-with-meta' ); ?> <pre>[export-users from="<?php echo date( 'Y-m-d' ); ?>"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute to', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use from attribute to filter users created before a specified date. Date format has to be: Y-m-d, for example:', 'import-users-from-csv-with-meta' ); ?> <pre>[export-users to="<?php echo date( 'Y-m-d' ); ?>"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute delimiter', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use delimiter attribute to set which delimiter is going to be used, allowed values are:', 'import-users-from-csv-with-meta' ); ?> COMMA, COLON, SEMICOLON, TAB <pre>[export-users delimiter="SEMICOLON"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute order-alphabetically', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use order-alphabetically attribute to order alphabetically the fields, for example', 'import-users-from-csv-with-meta' ); ?> <pre>[export-users order-alphabetically]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute columns', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use columns attribute to set which columns must be exported and in which order. Use a list of fields separated by commas, for example', 'import-users-from-csv-with-meta' ); ?> <pre>[export-users columns="user_email,first_name,last_name"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute orderby', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'You can use orderby attribute to set the order in which users would be exported. You can use some of the next fields or a meta_key:', 'import-users-from-csv-with-meta' ); ?>
                        <ul style="list-style:disc outside none;margin-left:2em;">
                            <li><strong>ID</strong>: <?php _e( 'Order by user id', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>display_name</strong>: <?php _e( 'Order by user display name', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>name</strong> or <strong>user_name</strong>: <?php _e( 'Order by user name', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>login</strong> or <strong>user_login</strong>: <?php _e( 'Order by user login', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>nicename</strong> or <strong>user_nicename</strong>: <?php _e( 'Order by user nicename', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>email</strong> or <strong>user_email</strong>: <?php _e( 'Order by user email', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>url</strong> or <strong>user_url</strong>: <?php _e( 'Order by user url', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>registered</strong> or <strong>user_registered</strong>: <?php _e( 'Order by user registered date', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong>post_count</strong>: <?php _e( 'Order by user post count', 'import-users-from-csv-with-meta' ); ?></li>
                            <li><strong><?php _e( 'Any meta_key', 'import-users-from-csv-with-meta' ); ?></strong>: <?php _e( 'Order by user meta value', 'import-users-from-csv-with-meta' ); ?></li>
                        </ul>
                        <?php _e( 'For example', 'import-users-from-csv-with-meta' ); ?> <pre style="display: inline-block;">[export-users orderby="user_email"]</pre>
					</td>
				</tr>

                <tr class="form-field">
					<th scope="row"><label for=""><?php _e( 'Attribute order', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td><?php _e( 'If you use orderby attrbute you can also use order attribute that designates the ascending or descending order of the "orderby" parameter, values can be "asc" or "desc", for example', 'import-users-from-csv-with-meta' ); ?> <pre>[export-users orderby="display_name" order="asc"]</pre>
					</td>
				</tr>

            </tbody>
        </table>                            

		<script>
		jQuery( document ).ready( function( $ ){
			check_delete_users_checked();
            check_send_mail_admin_frontend();

			$( '#delete_users_frontend' ).on( 'click', function() {
				check_delete_users_checked();
			});

            $( '#send_mail_admin_frontend' ).on( 'click', function() {
                check_send_mail_admin_frontend();
            });

			$( '#copy_to_clipboard' ).click( function(){
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val( '[import-users-from-csv-with-meta]' ).select();
				document.execCommand("copy");
				$temp.remove();
			} );

            $( '#copy_to_clipboard_export' ).click( function(){
				var $temp = $("<input>");
				$("body").append($temp);
				$temp.val( '[export-users]' ).select();
				document.execCommand("copy");
				$temp.remove();
			} );

			function check_delete_users_checked(){
				if( $('#delete_users_frontend').is(':checked') ){
					$( '#change_role_not_present_role_frontend' ).prop( 'disabled', true );
					$( '#change_role_not_present_frontend' ).prop( 'disabled', true );				
				} else {
					$( '#change_role_not_present_role_frontend' ).prop( 'disabled', false );
					$( '#change_role_not_present_frontend' ).prop( 'disabled', false );
				}
			}

            function check_send_mail_admin_frontend(){
				if( $('#send_mail_admin_frontend').is(':checked') ){
					$( '#send_mail_admin_frontend_address_list' ).prop( 'disabled', false );
				} else {
					$( '#send_mail_admin_frontend_address_list' ).prop( 'disabled', true );
				}
			}            
		});
		</script>
		<?php
	}

	function save_settings( $form_data ){
		if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'codection-security' ) ) {
			wp_die( __( 'Nonce check failed', 'import-users-from-csv-with-meta' ) ); 
		}

		ACUI_Options::save_options( $form_data, false, true );
		?>
		<div class="updated">
	       <p><?php _e( 'Settings updated correctly', 'import-users-from-csv-with-meta' ) ?></p>
	    </div>
	    <?php
	}

	function email_admin(){
        $send_mail_admin_frontend = get_option( "acui_frontend_mail_admin" );
        if( $send_mail_admin_frontend == false )
            return;

        $send_mail_admin_adress_list_frontend = get_option( "acui_frontend_send_mail_admin_address_list" );
        if( empty( $send_mail_admin_adress_list_frontend ) )
            $send_mail_admin_adress_list_frontend = get_option( 'admin_email' );

		$current_user = wp_get_current_user();
		$current_user_name = ( empty( $current_user ) ) ? 'User not logged in' : $current_user->user_login;

		$body_mail = sprintf( __("User with username: %s has executed an import using the shortcode in the frontend.", 'import-users-from-csv-with-meta'), $current_user_name );

		wp_mail( $send_mail_admin_adress_list_frontend, '[Import and export users and customers] Frontend import has been executed', $body_mail, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

    function shortcode_import( $atts ) {
		$atts = shortcode_atts( array( 'role' => '', 'delete-only-specified-role' => false ), $atts );

		ob_start();
		
		if( !current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
			wp_die( __( 'Only users who are able to create users can manage this form.', 'import-users-from-csv-with-meta' ) );

		if ( $_FILES && !empty( $_POST ) ):
			if ( !wp_verify_nonce( $_POST['security'], 'codection-security' ) ){
				wp_die( __( 'Nonce check failed', 'import-users-from-csv-with-meta' ) );
			}

            if( $_FILES['uploadfile']['error'] != 0 || $_FILES['uploadfile']['size'] == 0 ){
                _e( 'You must choose a file', 'import-users-from-csv-with-meta' );
            }
            else{
                do_action( 'acui_pre_frontend_import' );

                $file = array_keys( $_FILES );
                $csv_file_id = $this->upload_file( $file[0] );

                // start
                $form_data = array();
                $form_data["path_to_file"] = get_attached_file( $csv_file_id );

                // emails
                $form_data["sends_email"] = get_option( "acui_frontend_send_mail" );
                $form_data["send_email_updated"] = get_option( "acui_frontend_send_mail_updated" );
                $form_data["force_user_reset_password"] = get_option( "acui_frontend_force_user_reset_password" );

                // roles
                $form_data["role"] = empty( $atts["role"] ) ? get_option( "acui_frontend_role") : $atts["role"];

                // update
                $form_data["update_existing_users"] = empty( get_option( "acui_frontend_update_existing_users" ) ) ? 'no' : get_option( "acui_frontend_update_existing_users" );
                $form_data["update_roles_existing_users"] = empty( get_option( "acui_frontend_update_roles_existing_users" ) ) ? 'no' : get_option( "acui_frontend_update_roles_existing_users" );

                // delete
                $form_data["delete_users_not_present"] = ( get_option( "acui_frontend_delete_users" ) ) ? 'yes' : 'no';
                $form_data["delete_users_assign_posts"] = get_option( "acui_frontend_delete_users_assign_posts" );
                $form_data["delete_users_only_specified_role"] = empty( $form_data[ "role" ] ) ? false : $atts['delete-only-specified-role'];

                // others
                $form_data["empty_cell_action"] = "leave";
                $form_data["activate_users_wp_members"] = empty( get_option( "acui_frontend_activate_users_wp_members" ) ) ? 'no_activate' : get_option( "acui_frontend_activate_users_wp_members" );
                $form_data["security"] = wp_create_nonce( "codection-security" );

                $form_data = apply_filters( 'acui_frontend_import_form_data', $form_data );
                
                $acui_import = new ACUI_Import();
                $acui_import->fileupload_process( $form_data, false, true );

                wp_delete_attachment( $csv_file_id, true );

                do_action( 'acui_post_frontend_import' );
            }
		else:
		?>

        <?php do_action( 'acui_frontend_import_before_form' ); ?>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" class="acui_frontend_form">
            <?php do_action( 'acui_frontend_import_before_input_file' ); ?>

			<label><?php _e( 'CSV file <span class="description">(required)</span>', 'import-users-from-csv-with-meta' ); ?></label></th>
			<input class="acui_frontend_file_button" type="button" onclick="document.getElementById('uploadfile').click();" value="<?php echo apply_filters( 'acui_import_shortcode_file_button_text', __( 'Choose file', 'import-users-from-csv-with-meta' ) ); ?>">
            <input class="acui_frontend_file" type="file" name="uploadfile" id="uploadfile" class="uploadfile" style="display:none;" onchange="document.getElementById('acui_frontend_selected_file').innerHTML=this.value.replace(/C:\\fakepath\\/i, '');"/>
			<label id="acui_frontend_selected_file"><?php _e( 'No file selected', 'import-users-from-csv-with-meta' ) ?></label>

            <?php do_action( 'acui_frontend_import_after_input_file' ); ?>

			<input class="acui_frontend_submit" type="submit" value="<?php echo apply_filters( 'acui_import_shortcode_button_text', __( 'Upload and process', 'import-users-from-csv-with-meta' ) ); ?>" />

            <?php do_action( 'acui_frontend_import_after_submit' ); ?>

			<?php wp_nonce_field( 'codection-security', 'security' ); ?>
		</form>

        <?php do_action( 'acui_frontend_import_after_form' ); ?>

		<?php endif; ?>
		
		<?php
		return ob_get_clean();
	}

	function upload_file( $file_handler ) {
	    if ( $_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK ) {
	        __return_false();
	    }
	    require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
	    require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
	    require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
	    $attach_id = media_handle_upload( $file_handler, 0 );
	    return $attach_id;
	}

    function shortcode_export( $atts ) {
        $atts = shortcode_atts( array( 'role' => '', 'from' => '', 'to' => '', 'delimiter' => '', 'order-alphabetically' => '', 'columns' => '', 'orderby' => '', 'order' => '' ), $atts );

		ob_start();
		
		if( !current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can export them.', 'import-users-from-csv-with-meta' ) );

        ACUI_Exporter::enqueue();
        ACUI_Exporter::styles();
		?>
        
		<form method="POST" class="acui_frontend_form" id="acui_exporter">
            <input type="hidden" name="acui_frontend_export" value="1"/>
        
            <?php foreach( $atts as $key => $value ): ?>
            <input type="hidden" name="<?php echo $key; ?>" value="<?php echo $value; ?>"/>
            <?php endforeach; ?>
            
            <input class="acui_frontend_submit" type="submit" value="<?php apply_filters( 'acui_export_shortcode_button_text', _e( 'Export', 'import-users-from-csv-with-meta' ) ); ?>"/>

			<?php wp_nonce_field( 'codection-security', 'security' ); ?>

            <div class="user-exporter-progress-wrapper">
                <progress class="user-exporter-progress" value="0" max="100"></progress>
                <span class="user-exporter-progress-value">0%</span>
            </div>
		</form>
		<?php
		return ob_get_clean();
	}
}

$acui_frontend = new ACUI_Frontend();
$acui_frontend->hooks();