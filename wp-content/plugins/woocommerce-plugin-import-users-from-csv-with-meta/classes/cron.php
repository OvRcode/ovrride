<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Cron{
	function __construct(){
		add_action( 'acui_cron_save_settings', array( $this, 'save_settings' ), 10, 1 );
		add_action( 'acui_cron_process', array( $this, 'process' ), 10 );
		add_action( 'wp_ajax_acui_fire_cron', array( $this, 'ajax_fire_cron' ) );
	}

	function save_settings( $form_data ){
		if ( !isset( $form_data['security'] ) || !wp_verify_nonce( $form_data['security'], 'codection-security' ) ) {
			wp_die( __( 'Nonce check failed', 'import-users-from-csv-with-meta' ) ); 
		}

		$next_timestamp = wp_next_scheduled( 'acui_cron_process' );
		$period = sanitize_text_field( $form_data[ "period" ] );

		if( isset( $form_data["cron-activated"] ) && $form_data["cron-activated"] == "yes" ){
			update_option( "acui_cron_activated", true );

			$old_period = get_option( "acui_cron_period" );

			if( $old_period != $period ){
				wp_unschedule_event( $next_timestamp, 'acui_cron_process');
				wp_schedule_event( time(), $period, 'acui_cron_process' );
			}
			elseif( !$next_timestamp ) {
				wp_schedule_event( time(), $period, 'acui_cron_process' );
			}
		}
		else{
			update_option( "acui_cron_activated", false );
			wp_unschedule_event( $next_timestamp, 'acui_cron_process');
		}
		
		update_option( "acui_cron_send_mail", isset( $form_data["send-mail-cron"] ) && $form_data["send-mail-cron"] == "yes" );
		update_option( "acui_cron_send_mail_updated", isset( $form_data["send-mail-updated"] ) && $form_data["send-mail-updated"] == "yes" );
		update_option( "acui_cron_delete_users", isset( $form_data["cron-delete-users"] ) && $form_data["cron-delete-users"] == "yes" );
		update_option( "acui_cron_delete_users_assign_posts", sanitize_text_field( $form_data["cron-delete-users-assign-posts"] ) );
		update_option( "acui_move_file_cron", isset( $form_data["move-file-cron"] ) && $form_data["move-file-cron"] == "yes" );
		update_option( "acui_cron_path_to_move_auto_rename", isset( $form_data["path_to_move_auto_rename"] ) && $form_data["path_to_move_auto_rename"] == "yes" );
		update_option( "acui_cron_allow_multiple_accounts", ( isset( $form_data["allow_multiple_accounts"] ) && $form_data["allow_multiple_accounts"] == "yes" ) ? "allowed" : "not_allowed" );
		update_option( "acui_cron_path_to_file", sanitize_text_field( $form_data["path_to_file"] ) );
		update_option( "acui_cron_path_to_move", sanitize_text_field( $form_data["path_to_move"] ) );
		update_option( "acui_cron_period", sanitize_text_field( $form_data["period"] ) );
		update_option( "acui_cron_role", sanitize_text_field( $form_data["role"] ) );
		update_option( "acui_cron_update_roles_existing_users", isset( $form_data["update-roles-existing-users"] ) && $form_data["update-roles-existing-users"] == "yes" );
		update_option( "acui_cron_change_role_not_present", isset( $form_data["cron-change-role-not-present"] ) && $form_data["cron-change-role-not-present"] == "yes" );
		update_option( "acui_cron_change_role_not_present_role", sanitize_text_field( $form_data["cron-change-role-not-present-role"] ) );
		?>
		<div class="updated">
	       <p><?php _e( 'Settings updated correctly', 'import-users-from-csv-with-meta' ) ?></p>
	    </div>
	    <?php
	}

	function process(){
		$message = __('Import cron task starts at', 'import-users-from-csv-with-meta' ) . ' ' . date("Y-m-d H:i:s") . '<br/>';

		$form_data = array();
		$form_data[ "path_to_file" ] = get_option( "acui_cron_path_to_file");
		$form_data[ "role" ] = get_option( "acui_cron_role");
		$form_data[ "update_roles_existing_users" ] = ( get_option( "acui_cron_update_roles_existing_users" ) ) ? 'yes' : 'no';
		$form_data[ "empty_cell_action" ] = "leave";
		$form_data[ "allow_update_emails" ] = "disallow";
		$form_data[ "security" ] = wp_create_nonce( "codection-security" );

		ob_start();
		$acui_import = new ACUI_Import();
		$acui_import->fileupload_process( $form_data, true );
		$message .= "<br/>" . ob_get_contents() . "<br/>";
		ob_end_clean();

		$move_file_cron = get_option( "acui_move_file_cron");
		
		if( $move_file_cron ){
			$path_to_file = get_option( "acui_cron_path_to_file");
			$path_to_move = get_option( "acui_cron_path_to_move");

			rename( $path_to_file, $path_to_move );

			$this->auto_rename(); // optionally rename with date and time included
		}
		$message .= __( '--Finished at', 'import-users-from-csv-with-meta' ) . ' ' . date("Y-m-d H:i:s") . '<br/><br/>';

		update_option( "acui_cron_log", $message );
	}

	function auto_rename() {
		if( get_option( "acui_cron_path_to_move_auto_rename" ) != true )
			return;

		$movefile  = get_option( "acui_cron_path_to_move");
		
		if ( $movefile && file_exists( $movefile ) ) {
			$parts = pathinfo( $movefile );
			$filename = $parts['filename'];
			
			if ( $filename ){
				$date = date( 'YmdHis' ); 
				$newfile = $parts['dirname'] . '/' . $filename .'_' . $date . '.' . $parts['extension'];
				rename( $movefile , $newfile );
			} 
		}
	}

	public static function admin_gui(){
		$cron_activated = get_option( "acui_cron_activated");
		$send_mail_cron = get_option( "acui_cron_send_mail");
		$send_mail_updated = get_option( "acui_cron_send_mail_updated");
		$cron_delete_users = get_option( "acui_cron_delete_users");
		$cron_delete_users_assign_posts = get_option( "acui_cron_delete_users_assign_posts");
		$cron_change_role_not_present = get_option( "acui_cron_change_role_not_present" );
		$cron_change_role_not_present_role = get_option( "acui_cron_change_role_not_present_role" );
		$path_to_file = get_option( "acui_cron_path_to_file");
		$period = get_option( "acui_cron_period");
		$role = get_option( "acui_cron_role");
		$update_roles_existing_users = get_option( "acui_cron_update_roles_existing_users");
		$move_file_cron = get_option( "acui_move_file_cron");
		$path_to_move = get_option( "acui_cron_path_to_move");
		$path_to_move_auto_rename = get_option( "acui_cron_path_to_move_auto_rename");
		$log = get_option( "acui_cron_log");
		$allow_multiple_accounts = get_option("acui_cron_allow_multiple_accounts");
		$loaded_periods = wp_get_schedules();

		$rest_api_execute_cron_url = home_url() . '/wp-json/import-users-from-csv-with-meta/v1/execute-cron/';

		if( empty( $cron_activated ) )
			$cron_activated = false;

		if( empty( $send_mail_cron ) )
			$send_mail_cron = false;

		if( empty( $send_mail_updated ) )
			$send_mail_updated = false;

		if( empty( $cron_delete_users ) )
			$cron_delete_users = false;

		if( empty( $update_roles_existing_users) )
			$update_roles_existing_users = false;

		if( empty( $cron_delete_users_assign_posts ) )
			$cron_delete_users_assign_posts = '';

		if( empty( $path_to_file ) )
			$path_to_file = dirname( __FILE__ ) . '/test.csv';

		if( empty( $period ) )
			$period = 'hourly';

		if( empty( $move_file_cron ) )
			$move_file_cron = false;

		if( empty( $path_to_move ) )
			$path_to_move = dirname( __FILE__ ) . '/move.csv';

		if( empty( $path_to_move_auto_rename ) )
			$path_to_move_auto_rename = false;

		if( empty( $log ) )
			$log = "No tasks done yet.";
		
		if( empty( $allow_multiple_accounts ) )
			$allow_multiple_accounts = "not_allowed";
		?>
		<h2><?php _e( "Execute an import of users periodically", 'import-users-from-csv-with-meta' ); ?></h2>

		<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8">
			<table class="form-table">
				<tbody>

				<tr class="form-field form-required">
					<th scope="row"><label for="cron-activated"><?php _e( 'Activate periodical import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="cron-activated" value="yes" <?php if( $cron_activated == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>

				<tr class="form-field">
					<th scope="row"><label for="path_to_file"><?php _e( "Path of file that are going to be imported", 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input placeholder="<?php _e('Insert complete path to the file', 'import-users-from-csv-with-meta' ) ?>" type="text" name="path_to_file" id="path_to_file" value="<?php echo $path_to_file; ?>" style="width:70%;" />
						<p class="description"><?php _e( 'You have to introduce the path to file, i.e.:', 'import-users-from-csv-with-meta' ); ?> <?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv</p>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="period"><?php _e( 'Period', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>	
						<select id="period" name="period">
							<?php foreach( $loaded_periods as $key => $value ): ?>
							<option <?php if( $period == $key ) echo "selected='selected'"; ?> value="<?php echo $key; ?>"><?php echo $value['display']; ?></option>
							<?php endforeach; ?>
						</select>
						<p class="description"><?php _e( 'How often the event should reoccur?', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="send-mail-cron"><?php _e( 'Send mail when using periodical import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="send-mail-cron" value="yes" <?php if( $send_mail_cron == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="send-mail-updated"><?php _e( 'Send mail also to users that are being updated?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="send-mail-updated" value="yes" <?php if( $send_mail_updated == true ) echo "checked='checked'"; ?>/>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="role"><?php _e( 'Role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select id="role" name="role">
							<?php 
								if( $role == '' )
									echo "<option selected='selected' value=''>" . __( 'Disable role assignment in cron import', 'import-users-from-csv-with-meta' )  . "</option>";
								else
									echo "<option value=''>" . __( 'Disable role assignment in cron import', 'import-users-from-csv-with-meta' )  . "</option>";

								$list_roles = ACUI_Helper::get_editable_roles();						
								foreach ($list_roles as $key => $value) {
									if($key == $role)
										echo "<option selected='selected' value='$key'>$value</option>";
									else
										echo "<option value='$key'>$value</option>";
								}
							?>
						</select>
						<p class="description"><?php _e( 'Which role would be used to import users?', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="update-roles-existing-users"><?php _e( 'Update roles for existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="update-roles-existing-users" value="yes" <?php if( $update_roles_existing_users ) echo "checked='checked'"; ?>/>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="move-file-cron"><?php _e( 'Move file after import?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left;">
							<input type="checkbox" name="move-file-cron" value="yes" <?php if( $move_file_cron == true ) echo "checked='checked'"; ?>/>
						</div>

						<div class="move-file-cron-cell" style="margin-left:25px;">
							<input placeholder="<?php _e( 'Insert complete path to the file', 'import-users-from-csv-with-meta'); ?>" type="text" name="path_to_move" id="path_to_move" value="<?php echo $path_to_move; ?>" style="width:70%;" />
							<p class="description"><?php _e( 'You have to introduce the path to file, i.e.:', 'import-users-from-csv-with-meta'); ?> <?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/move.csv</p>
						</div>
					</td>
				</tr>

				<tr class="form-field form-required move-file-cron-cell">
					<th scope="row"><label for="move-file-cron"><?php _e( 'Auto rename after move?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left;">
							<input type="checkbox" name="path_to_move_auto_rename" value="yes" <?php if( $path_to_move_auto_rename == true ) echo "checked='checked'"; ?>/>
						</div>

						<div style="margin-left:25px;">
							<p class="description"><?php _e( 'Your file will be renamed after moved, so you will not lost any version of it. The way to rename will be append the time stamp using this date format: YmdHis.', 'import-users-from-csv-with-meta'); ?></p>
						</div>
					</td>
				</tr>

				</tbody>
			</table>

			<h2><?php _e( 'Users not present in CSV file', 'import-users-from-csv-with-meta'); ?></h2>

			<table class="form-table">
				<tbody>
				
				<tr class="form-field form-required">
					<th scope="row"><label for="cron-delete-users"><?php _e( 'Delete users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
							<input type="checkbox" name="cron-delete-users" id="cron-delete-users" value="yes" <?php if( $cron_delete_users == true ) echo "checked='checked'"; ?>/>
						</div>
						<div style="margin-left:25px;">
							<select id="cron-delete-users-assign-posts" name="cron-delete-users-assign-posts">
								<?php
									if( $cron_delete_users_assign_posts == '' )
										echo "<option selected='selected' value=''>" . __( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' ) . "</option>";
									else
										echo "<option value=''>" . __( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' ) . "</option>";

									$blogusers = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
									
									foreach ( $blogusers as $bloguser ) {
										if( $bloguser->ID == $cron_delete_users_assign_posts )
											echo "<option selected='selected' value='{$bloguser->ID}'>{$bloguser->display_name}</option>";
										else
											echo "<option value='{$bloguser->ID}'>{$bloguser->display_name}</option>";
									}
								?>
							</select>
							<p class="description"><?php _e( 'Administrators will not be deleted anyway. After delete users, we can choose if we want to assign their posts to another user. If you do not choose some user, content will be deleted.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>

				<tr class="form-field form-required">
					<th scope="row"><label for="cron-change-role-not-present"><?php _e( 'Change role of users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
							<input type="checkbox" name="cron-change-role-not-present" id="cron-change-role-not-present" value="yes" <?php checked( $cron_change_role_not_present ); ?> />
						</div>
						<div style="margin-left:25px;">
							<select name="cron-change-role-not-present-role" id="cron-change-role-not-present-role">
								<?php
									$list_roles = ACUI_Helper::get_editable_roles();
									foreach ($list_roles as $key => $value):
								?>
									<option value='<?php echo $key; ?>' <?php selected( $cron_change_role_not_present_role, $key ); ?> ><?php echo $value; ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php _e( 'After import users which is not present in the CSV and can be changed to a different role.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>
				</tbody>
			</table>

			<h2><?php _e( 'Call cron process using REST-API', 'import-users-from-csv-with-meta'); ?></h2>

			<table class="form-table">
				<tbody>
				<tr class="form-field form-required">
					<th scope="row"><label for="log"><?php _e( 'GET endpoint to execute cron', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<?php _e( 'You can execute the cron process out of your site using the next REST-API endpoint:', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo $rest_api_execute_cron_url; ?>"><?php echo $rest_api_execute_cron_url; ?></a>.<br/>
						<p class="description"><?php _e( 'This endpoint does an administrative task, so in order to run it you must be authenticated with a user with privileges.', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>				
				</tbody>
			</table>

			<?php do_action( 'acui_tab_cron_before_log' ); ?>

			<h2><?php _e( 'Log', 'import-users-from-csv-with-meta'); ?></h2>

			<table class="form-table">
				<tbody>
				<tr class="form-field form-required">
					<th scope="row"><label for="log"><?php _e( 'Last actions of schedule task', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<pre><?php echo strip_tags( $log, '<br><div><p><strong><style><h2><h3><table><tbody><tr><td><th>' ); ?></pre>
					</td>
				</tr>
				
				</tbody>
			</table>
			<?php wp_nonce_field( 'codection-security', 'security' ); ?>
			<input class="button-primary" type="submit" value="<?php _e( 'Save schedule options', 'import-users-from-csv-with-meta'); ?>"/>
			<input id="cron-execute-cron-task-now" class="button-primary" type="button" value="<?php _e( 'Execute cron task now', 'import-users-from-csv-with-meta'); ?>"/>
		</form>

		<script>
		jQuery( document ).ready( function( $ ){
			check_delete_users_checked();

			$( '#cron-delete-users' ).on( 'click', function() {
				check_delete_users_checked();
			});

			$( '#cron-execute-cron-task-now' ).click( function(){
				$( this )
					.prop( 'disabled', true )
					.val( 'Loading...' );

				var data = {
					'action': 'acui_fire_cron',
					'security': '<?php echo wp_create_nonce( "codection-security" ); ?>'
				};

				$.post( ajaxurl, data, function( response ) {
					if( response != "OK" )
						alert( "<?php _e( 'Problems executing cron task: ', 'import-users-from-csv-with-meta' ); ?>" + response );
					else{
						alert( "<?php _e( 'Cron task successfully executed', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			} );

			function check_delete_users_checked(){
				if( $('#cron-delete-users').is(':checked') ){
					$( '#cron-change-role-not-present-role' ).prop( 'disabled', true );
					$( '#cron-change-role-not-present' ).prop( 'disabled', true );				
				} else {
					$( '#cron-change-role-not-present-role' ).prop( 'disabled', false );
					$( '#cron-change-role-not-present' ).prop( 'disabled', false );
				}
			}

			$( "[name='cron-delete-users']" ).change(function() {
		        if( $(this).is( ":checked" ) ) {
		            var returnVal = confirm("<?php _e( 'Are you sure to delete all users that are not present in the CSV? This action cannot be undone.', 'import-users-from-csv-with-meta' ); ?>");
		            $(this).attr("checked", returnVal);

		            if( returnVal )
		            	$( '#cron-delete-users-assign-posts' ).show();
		        }
		        else{
	       	        $( '#cron-delete-users-assign-posts' ).hide();     	        
		        }
		    });

		    $( "[name='move-file-cron']" ).change(function() {
		        if( $(this).is( ":checked" ) ){
		        	$( '.move-file-cron-cell' ).show();
		        }
		        else{
		        	$( '.move-file-cron-cell' ).hide();
		        }
		    });

		    <?php if( $cron_delete_users == '' ): ?>
		    $( '#cron-delete-users-assign-posts' ).hide();
		    <?php endif; ?>

		    <?php if( !$move_file_cron ): ?>
		    $( '.move-file-cron-cell' ).hide();
		    <?php endif; ?>
		});
		</script>
	<?php
	}

	function ajax_fire_cron(){
		check_ajax_referer( 'codection-security', 'security' );

		do_action( 'acui_cron_process' );
		echo "OK";
		wp_die();
	}
}

new ACUI_Cron();