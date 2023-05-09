<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Homepage{
	function __construct(){
	}

    function hooks(){
        add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ), 10, 1 );
		add_action( 'acui_homepage_start', array( $this, 'maybe_remove_old_csv' ) );
        add_action( 'wp_ajax_acui_delete_attachment', array( $this, 'delete_attachment' ) );
		add_action( 'wp_ajax_acui_bulk_delete_attachment', array( $this, 'bulk_delete_attachment' ) );
		add_action( 'wp_ajax_acui_delete_users_assign_posts_data', array( $this, 'delete_users_assign_posts_data' ) );
    }

    function load_scripts( $hook ){
        if( $hook != 'tools_page_acui' || isset( $_GET['tab'] ) )
            return;
        
        wp_enqueue_style( 'select2-css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' );
    }

	static function admin_gui(){
		$last_roles_used = empty( get_option( 'acui_last_roles_used' ) ) ? array( 'subscriber' ) : get_option( 'acui_last_roles_used' );
?>
	<div class="wrap acui">	

		<?php do_action( 'acui_homepage_start' ); ?>

		<div id='message' class='updated'><?php _e( 'File must contain at least <strong>2 columns: username and email</strong>. These should be the first two columns and it should be placed <strong>in this order: username and email</strong>. If there are more columns, this plugin will manage it automatically.', 'import-users-from-csv-with-meta' ); ?></div>
		<div id='message-password' class='error'><?php _e( 'Please, read carefully how <strong>passwords are managed</strong> and also take note about capitalization, this plugin is <strong>case sensitive</strong>.', 'import-users-from-csv-with-meta' ); ?></div>

		<div>
			<h2><?php _e( 'Import users and customers from CSV','import-users-from-csv-with-meta' ); ?></h2>
		</div>

		<div style="clear:both;"></div>

		<div id="acui_form_wrapper" class="main_bar">
			<form method="POST" enctype="multipart/form-data" action="" accept-charset="utf-8" onsubmit="return check();">
			<h2 id="acui_file_header"><?php _e( 'File', 'import-users-from-csv-with-meta'); ?></h2>
			<table  id="acui_file_wrapper" class="form-table">
				<tbody>

				<tr class="form-field form-required">
					<th scope="row"><label for="uploadfile"><?php _e( 'CSV file <span class="description">(required)</span></label>', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<div id="upload_file">
							<input type="file" name="uploadfile" id="uploadfile" size="35" class="uploadfile" />
							<?php _e( '<em>or you can choose directly a file from your host,', 'import-users-from-csv-with-meta' ) ?> <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'import-users-from-csv-with-meta' ) ?></a>.</em>
						</div>
						<div id="introduce_path" style="display:none;">
							<input placeholder="<?php _e( 'You have to introduce the path to file, i.e.:' ,'import-users-from-csv-with-meta' ); ?><?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv" type="text" name="path_to_file" id="path_to_file" value="<?php echo ACUI_Options::get( 'path_to_file' ); ?>" style="width:70%;" />
							<em><?php _e( 'or you can upload it directly from your PC', 'import-users-from-csv-with-meta' ); ?>, <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'import-users-from-csv-with-meta' ); ?></a>.</em>
						</div>
					</td>
				</tr>
				</tbody>
			</table>
				
			<h2 id="acui_roles_header"><?php _e( 'Roles', 'import-users-from-csv-with-meta'); ?></h2>
			<table  id="acui_roles_wrapper" class="form-table">
				<tbody>
				<tr class="form-field">
					<th scope="row"><label for="role"><?php _e( 'Default role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
					<?php 
						foreach ( ACUI_Helper::get_editable_roles() as $key => $value ){
							if( in_array( $key, $last_roles_used ) )
								echo "<label id='$key' style='margin-right:5px;'><input name='role[]' type='checkbox' checked='checked' value='$key'/>$value</label>";
							else
								echo "<label id='$key' style='margin-right:5px;'><input name='role[]' type='checkbox' value='$key'/>$value</label>";
						}
					?>

					<p class="description"><?php _e( 'You can also import roles from a CSV column. Please read documentation tab to see how it can be done. If you choose more than one role, the roles would be assigned correctly but you should use some plugin like <a href="https://wordpress.org/plugins/user-role-editor/">User Role Editor</a> to manage them.', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>

			<h2 id="acui_options_header"><?php _e( 'Options', 'import-users-from-csv-with-meta'); ?></h2>
			<table  id="acui_options_wrapper" class="form-table">
				<tbody>
				<tr  id="acui_empty_cell_wrapper" class="form-field form-required">
					<th scope="row"><label for="empty_cell_action"><?php _e( 'What should the plugin do with empty cells?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="empty_cell_action">
							<option value="leave"><?php _e( 'Leave the old value for this metadata', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="delete"><?php _e( 'Delete the metadata', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>

				<tr id="acui_send_email_wrapper" class="form-field">
					<th scope="row"><label for="user_login"><?php _e( 'Send mail', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<p id="sends_email_wrapper">
							<?php _e( 'Do you wish to send a mail with credentials and other data?', 'import-users-from-csv-with-meta' ); ?> 
							<input type="checkbox" name="sends_email" value="yes" <?php if( get_option( 'acui_manually_send_mail' ) ): ?> checked="checked" <?php endif; ?>>
						</p>
						<p id="send_email_updated_wrapper">
							<?php _e( 'Do you wish to send this mail also to users that are being updated? (not only to the one which are being created)', 'import-users-from-csv-with-meta' ); ?>
							<input type="checkbox" name="send_email_updated" value="yes" <?php if( get_option( 'acui_manually_send_mail_updated' ) ): ?> checked="checked" <?php endif; ?>>
						</p>
					</td>
				</tr>

                <tr class="form-field form-required">
					<th scope="row"><label for=""><?php _e( 'Force users to reset their passwords?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<input type="checkbox" name="force_user_reset_password" value="yes" <?php checked( get_option( 'acui_manually_force_user_reset_password' ) ); ?>/>
                        <p class="description"><?php _e( 'If a password is set to an user and you activate this option, the user will be forced to reset their password in their first login', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>
				</tbody>
			</table>

			<h2 id="acui_update_users_header"><?php _e( 'Update users', 'import-users-from-csv-with-meta'); ?></h2>

			<table id="acui_update_users_wrapper" class="form-table">
				<tbody>
				<tr id="acui_update_existing_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_existing_users"><?php _e( 'Update existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="update_existing_users">
							<option value="yes"><?php _e( 'Yes', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="no"><?php _e( 'No', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>

				<tr id="acui_update_emails_existing_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_emails_existing_users"><?php _e( 'Update emails?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="update_emails_existing_users">
							<option value="yes"><?php _e( 'Yes', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="create"><?php _e( 'No, but create a new user with a prefix in the username', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="no"><?php _e( 'No, skip this user', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
						<p class="description"><?php _e( 'What the plugin should do if the plugin find an user, identified by their username, with a different email', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<tr id="acui_update_roles_existing_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_roles_existing_users"><?php _e( 'Update roles for existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="update_roles_existing_users">
							<option value="no"><?php _e( 'No', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="yes"><?php _e( 'Yes, update and override existing roles', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="yes_no_override"><?php _e( 'Yes, add new roles and not override existing ones', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>

				<tr id="acui_update_allow_update_passwords_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_allow_update_passwords"><?php _e( 'Never update passwords?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<select name="update_allow_update_passwords">
							<option value="yes"><?php _e( 'Update passwords as it is described in documentation', 'import-users-from-csv-with-meta' ); ?></option>
							<option value="no"><?php _e( 'Never update passwords when updating a user', 'import-users-from-csv-with-meta' ); ?></option>
						</select>
					</td>
				</tr>
				</tbody>
			</table>

			<h2 id="acui_users_not_present_header"><?php _e( 'Users not present in CSV file', 'import-users-from-csv-with-meta'); ?></h2>

			<table id="acui_users_not_present_wrapper" class="form-table">
				<tbody>
				
				<tr id="acui_delete_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="delete_users"><?php _e( 'Delete users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
							<input type="checkbox" name="delete_users" id="delete_users" value="yes"/>
						</div>
						<div style="margin-left:25px;">
							<select id="delete_users_assign_posts" name="delete_users_assign_posts">
                                <option value=""><?php _e( 'Delete posts of deleted users without assigning to any user or type to search a user', 'import-users-from-csv-with-meta' ) ?></option>
							</select>
							<p class="description"><?php _e( 'Administrators will not be deleted anyway. After delete users, we can choose if we want to assign their posts to another user. If you do not choose some user, content will be deleted.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>

				<tr id="acui_not_present_wrapper" class="form-field form-required">
					<th scope="row"><label for="change_role_not_present"><?php _e( 'Change role of users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
							<input type="checkbox" name="change_role_not_present" id="change_role_not_present" value="yes"/>
						</div>
						<div style="margin-left:25px;">
							<select id="change_role_not_present_role" name="change_role_not_present_role">
								<?php foreach ( ACUI_Helper::get_editable_roles() as $key => $value ): ?>
									<option value='<?php echo $key; ?>'><?php echo $value; ?></option>
								<?php endforeach; ?>
							</select>
							<p class="description"><?php _e( 'After import users which is not present in the CSV and can be changed to a different role.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>

				</tbody>
			</table>

			<?php do_action( 'acui_tab_import_before_import_button' ); ?>
				
			<?php wp_nonce_field( 'codection-security', 'security' ); ?>

			<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php _e( 'Start importing', 'import-users-from-csv-with-meta' ); ?>"/>
			</form>
		</div>

		<div class="sidebar">
			<div class="sidebar_section become_patreon">
		    	<a class="patreon" color="primary" type="button" name="become-a-patron" data-tag="become-patron-button" href="https://www.patreon.com/carazo" role="button">
		    		<div><span><?php _e( 'Become a patron', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>
			
			<div class="sidebar_section" id="vote_us">
				<h3><?php _e( 'Rate Us', 'import-users-from-csv-with-meta'); ?></h3>
				<ul>
					<li><label><?php _e( 'If you like it', 'import-users-from-csv-with-meta'); ?>, <a href="https://wordpress.org/support/plugin/import-users-from-csv-with-meta/reviews/"><?php _e( 'Please vote and support us', 'import-users-from-csv-with-meta'); ?></a>.</label></li>
				</ul>
			</div>
			<div class="sidebar_section">
				<h3>Having Issues?</h3>
				<ul>
					<li><label>You can create a ticket</label> <a target="_blank" href="http://wordpress.org/support/plugin/import-users-from-csv-with-meta"><label>WordPress support forum</label></a></li>
					<li><label>You can ask for premium support</label> <a target="_blank" href="mailto:contacto@codection.com"><label>contacto@codection.com</label></a></li>
				</ul>
			</div>
			<div class="sidebar_section">
				<h3>Donate</h3>
				<ul>
					<li><label>If you appreciate our work and you want to help us to continue developing it and giving the best support</label> <a target="_blank" href="https://paypal.me/imalrod"><label>donate</label></a></li>
				</ul>
			</div>
		</div>

	</div>
	<script type="text/javascript">
	function check(){
		if(document.getElementById( 'uploadfile' ).value == "" && jQuery( '#upload_file' ).is( ':visible' ) ) {
		   alert("<?php _e( 'Please choose a file', 'import-users-from-csv-with-meta' ); ?>");
		   return false;
		}

		if( jQuery( '#path_to_file' ).val() == "" && jQuery( '#introduce_path' ).is( ':visible' ) ) {
		   alert("<?php _e( 'Please enter a path to the file', 'import-users-from-csv-with-meta' ); ?>");
		   return false;
		}
	}

	jQuery( document ).ready( function( $ ){
		check_delete_users_checked();

		$( '#delete_users' ).on( 'click', function() {
			check_delete_users_checked();
		});

		$( '.delete_attachment' ).click( function(){
			var answer = confirm( "<?php _e( 'Are you sure to delete this file?', 'import-users-from-csv-with-meta' ); ?>" );
			if( answer ){
				var data = {
					'action': 'acui_delete_attachment',
					'attach_id': $( this ).attr( "attach_id" ),
					'security': '<?php echo wp_create_nonce( "codection-security" ); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					if( response != 1 )
						alert( response );
					else{
						alert( "<?php _e( 'File successfully deleted', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			}
		});

		$( '#bulk_delete_attachment' ).click( function(){
			var answer = confirm( "<?php _e( 'Are you sure to delete ALL CSV files uploaded? There can be CSV files from other plugins.', 'import-users-from-csv-with-meta' ); ?>" );
			if( answer ){
				var data = {
					'action': 'acui_bulk_delete_attachment',
					'security': '<?php echo wp_create_nonce( "codection-security" ); ?>'
				};

				$.post(ajaxurl, data, function(response) {
					if( response != 1 )
						alert( "<?php _e( 'There were problems deleting the files, please check files permissions', 'import-users-from-csv-with-meta' ); ?>" );
					else{
						alert( "<?php _e( 'Files successfully deleted', 'import-users-from-csv-with-meta' ); ?>" );
						document.location.reload();
					}
				});
			}
		});

		$( '.toggle_upload_path' ).click( function( e ){
			e.preventDefault();
			$( '#upload_file,#introduce_path' ).toggle();
		} );

		$( '#vote_us' ).click( function(){
			var win=window.open( 'http://wordpress.org/support/view/plugin-reviews/import-users-from-csv-with-meta?free-counter?rate=5#postform', '_blank');
			win.focus();
		} );

        $( '#change_role_not_present_role' ).select2();

        $( '#delete_users_assign_posts' ).select2({
            ajax: {
                url: '<?php echo admin_url( 'admin-ajax.php' ) ?>',
                cache: true,
                dataType: 'json',
                minimumInputLength: 3,
                allowClear: true,
                placeholder: { id: '', title: '<?php _e( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' )  ?>' },
                data: function( params ) {
                    if (params.term.trim().length < 3)
                        throw false;
  
                    var query = {
                        search: params.term,
                        _wpnonce: '<?php echo wp_create_nonce( 'codection-security' ); ?>',
                        action: 'acui_delete_users_assign_posts_data',
                    }

                    return query;
                }
            }
        });

		function check_delete_users_checked(){
			if( $( '#delete_users' ).is( ':checked' ) ){
                $( '#delete_users_assign_posts' ).prop( 'disabled', false );
				$( '#change_role_not_present' ).prop( 'disabled', true );
				$( '#change_role_not_present_role' ).prop( 'disabled', true );				
			} else {
                $( '#delete_users_assign_posts' ).prop( 'disabled', true );
				$( '#change_role_not_present' ).prop( 'disabled', false );
				$( '#change_role_not_present_role' ).prop( 'disabled', false );
			}
		}
	} );
	</script>
	<?php 
	}

	function maybe_remove_old_csv(){
		$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
		$old_csv_files = new WP_Query( $args_old_csv );

		if( $old_csv_files->found_posts > 0 ): ?>
		<div class="postbox">
		    <div title="<?php _e( 'Click to open/close', 'import-users-from-csv-with-meta' ); ?>" class="handlediv">
		      <br>
		    </div>

		    <h3 class="hndle"><span>&nbsp;&nbsp;&nbsp;<?php _e( 'Old CSV files uploaded', 'import-users-from-csv-with-meta' ); ?></span></h3>

		    <div class="inside" style="display: block;">
		    	<p><?php _e( 'For security reasons you should delete this files, probably they would be visible in the Internet if a bot or someone discover the URL. You can delete each file or maybe you want delete all CSV files you have uploaded:', 'import-users-from-csv-with-meta' ); ?></p>
		    	<input type="button" value="<?php _e( 'Delete all CSV files uploaded', 'import-users-from-csv-with-meta' ); ?>" id="bulk_delete_attachment" style="float:right;" />
		    	<ul>
		    		<?php while($old_csv_files->have_posts()) : 
		    			$old_csv_files->the_post(); 

		    			if( get_the_date() == "" )
		    				$date = "undefined";
		    			else
		    				$date = get_the_date();
		    		?>
		    		<li><a href="<?php echo wp_get_attachment_url( get_the_ID() ); ?>"><?php the_title(); ?></a> <?php _e( 'uploaded on', 'import-users-from-csv-with-meta' ) . ' ' . $date; ?> <input type="button" value="<?php _e( 'Delete', 'import-users-from-csv-with-meta' ); ?>" class="delete_attachment" attach_id="<?php the_ID(); ?>" /></li>
		    		<?php endwhile; ?>
		    		<?php wp_reset_postdata(); ?>
		    	</ul>
		        <div style="clear:both;"></div>
		    </div>
		</div>
		<?php endif;
	}

    function delete_attachment() {
		check_ajax_referer( 'codection-security', 'security' );
	
		if( ! current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can delete CSV attachments.', 'import-users-from-csv-with-meta' ) );
	
		$attach_id = absint( $_POST['attach_id'] );
		$mime_type  = (string) get_post_mime_type( $attach_id );
	
		if( $mime_type != 'text/csv' )
			_e('This plugin only can delete the type of file it manages, CSV files.', 'import-users-from-csv-with-meta' );
	
		$result = wp_delete_attachment( $attach_id, true );
	
		if( $result === false )
			_e( 'There were problems deleting the file, please check file permissions', 'import-users-from-csv-with-meta' );
		else
			echo 1;
	
		wp_die();
	}

	function bulk_delete_attachment(){
		check_ajax_referer( 'codection-security', 'security' );
	
		if( ! current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
        wp_die( __( 'Only users who are able to create users can bulk delete CSV attachments.', 'import-users-from-csv-with-meta' ) );
	
		$args_old_csv = array( 'post_type'=> 'attachment', 'post_mime_type' => 'text/csv', 'post_status' => 'inherit', 'posts_per_page' => -1 );
		$old_csv_files = new WP_Query( $args_old_csv );
		$result = 1;
	
		while($old_csv_files->have_posts()) : 
			$old_csv_files->the_post();
	
			$mime_type  = (string) get_post_mime_type( get_the_ID() );
			if( $mime_type != 'text/csv' )
				wp_die( __('This plugin only can delete the type of file it manages, CSV files.', 'import-users-from-csv-with-meta' ) );
	
			if( wp_delete_attachment( get_the_ID(), true ) === false )
				$result = 0;
		endwhile;
		
		wp_reset_postdata();
	
		echo $result;
	
		wp_die();
	}

    function delete_users_assign_posts_data(){
        check_ajax_referer( 'codection-security', 'security' );
	
		if( ! current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) )
            wp_die( __( 'Only users who are able to create users can manage this option.', 'import-users-from-csv-with-meta' ) );

        $results = array( array( 'id' => '', 'value' => __( 'Delete posts of deleted users without assigning to any user', 'import-users-from-csv-with-meta' ) ) );
        $search = sanitize_text_field( $_GET['search'] );

        if( strlen( $search ) >= 3 ){
            $blogusers = get_users( array( 'fields' => array( 'ID', 'display_name' ), 'search' => '*' . $search . '*' ) );
            
            foreach ( $blogusers as $bloguser ) {
                $results[] = array( 'id' => $bloguser->ID, 'text' => $bloguser->display_name );
            }
        }
        
        echo json_encode( array( 'results' => $results, 'more' => 'false' ) );
        
        wp_die();
    }
}

$acui_homepage = new ACUI_Homepage();
$acui_homepage->hooks();