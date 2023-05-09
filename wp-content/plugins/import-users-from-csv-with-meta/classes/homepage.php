<?php
if ( ! defined( 'ABSPATH' ) ) 
    exit;

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
        if( $hook != 'tools_page_acui' || ( isset( $_GET['tab'] ) && $_GET['tab'] != 'homepage' ) )
            return;

        wp_enqueue_style( 'select2-css', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css' );
        wp_enqueue_script( 'select2-js', '//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js' );
    }

	static function admin_gui(){
		$settings = new ACUI_Settings( 'import_backend' );
		$settings->maybe_migrate_old_options( 'import_backend' );

		if( is_int( $settings->get( 'delete_users_assign_posts' ) ) ){
			$delete_users_assign_posts_user = get_user_by( 'id', $settings->get( 'delete_users_assign_posts' ) );
			$delete_users_assign_posts_options = array( $settings->get( 'delete_users_assign_posts' ) => $delete_users_assign_posts_user->display_name );
			$delete_users_assign_posts_option_selected = $settings->get( 'delete_users_assign_posts' );
		}
		else{
			$delete_users_assign_posts_options = array( 0 => __( 'No user selected', 'import-users-from-csv-with-meta' ) );
			$delete_users_assign_posts_option_selected = 0;
		}
		
?>
	<div class="wrap acui">	

		<?php do_action( 'acui_homepage_start' ); ?>

		<div id='message' class='updated'><?php sprintf( _e( 'File must contain at least <strong>2 columns: username and email</strong>. These should be the first two columns and it should be placed <strong>in this order: username and email</strong>. Both data are required unless you use <a href="%">this addon to allow empty emails</a>. If there are more columns, this plugin will manage it automatically.', 'import-users-from-csv-with-meta' ), 'https://import-wp.com/allow-no-email-addon/' ); ?></div>
		<div id='message-password' class='error'><?php _e( 'Please, read carefully how <strong>passwords are managed</strong> and also take note about capitalization, this plugin is <strong>case sensitive</strong>.', 'import-users-from-csv-with-meta' ); ?></div>

		<div>
			<h2><?php _e( 'Import users and customers from CSV','import-users-from-csv-with-meta' ); ?></h2>
		</div>

		<div style="clear:both;"></div>

		<div id="acui_form_wrapper" class="main_bar">
			<form method="POST" id="acui_form" enctype="multipart/form-data" action="" accept-charset="utf-8">
			<h2 id="acui_file_header"><?php _e( 'File', 'import-users-from-csv-with-meta'); ?></h2>
			<table  id="acui_file_wrapper" class="form-table">
				<tbody>

				<?php do_action( 'acui_homepage_before_file_rows' ); ?>

				<tr class="form-field form-required">
					<th scope="row"><label for="uploadfile"><?php _e( 'CSV file <span class="description">(required)</span></label>', 'import-users-from-csv-with-meta' ); ?></th>
					<td>
						<div id="upload_file">
							<input type="file" name="uploadfile" id="uploadfile" size="35" class="uploadfile" />
							<?php _e( '<em>or you can choose directly a file from your host,', 'import-users-from-csv-with-meta' ) ?> <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'import-users-from-csv-with-meta' ) ?></a>.</em>
						</div>
						<div id="introduce_path" style="display:none;">
							<input placeholder="<?php _e( 'You have to introduce the path to file, i.e.:' ,'import-users-from-csv-with-meta' ); ?><?php $upload_dir = wp_upload_dir(); echo $upload_dir["path"]; ?>/test.csv" type="text" name="path_to_file" id="path_to_file" value="<?php echo $settings->get( 'path_to_file' ); ?>" style="width:70%;" />
							<em><?php _e( 'or you can upload it directly from your PC', 'import-users-from-csv-with-meta' ); ?>, <a href="#" class="toggle_upload_path"><?php _e( 'click here', 'import-users-from-csv-with-meta' ); ?></a>.</em>
						</div>
					</td>
				</tr>

				<?php do_action( 'acui_homepage_after_file_rows' ); ?>

				</tbody>
			</table>
				
			<h2 id="acui_roles_header"><?php _e( 'Roles', 'import-users-from-csv-with-meta'); ?></h2>
			<table id="acui_roles_wrapper" class="form-table">
				<tbody>

				<?php do_action( 'acui_homepage_before_roles_rows' ); ?>

				<tr class="form-field">
					<th scope="row"><label for="role"><?php _e( 'Default role', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
					<?php 
                        foreach ( ACUI_Helper::get_editable_roles() as $key => $value )
                           ACUIHTML()->checkbox( array( 'label' => translate_user_role( $value ), 'name' => 'role[]', 'compare_value' => $settings->get( 'role' ), 'current' => $key, 'array' => true, 'class' => 'roles' ) );
                    ?>
					<p class="description"><?php _e( 'You can also import roles from a CSV column. Please read documentation tab to see how it can be done. If you choose more than one role, the roles would be assigned correctly but you should use some plugin like <a href="https://wordpress.org/plugins/user-role-editor/">User Role Editor</a> to manage them.', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<?php do_action( 'acui_homepage_after_roles_rows' ); ?>

				</tbody>
			</table>

			<h2 id="acui_options_header"><?php _e( 'Options', 'import-users-from-csv-with-meta'); ?></h2>
			<table id="acui_options_wrapper" class="form-table">
				<tbody>

				<?php do_action( 'acui_homepage_before_options_rows' ); ?>

				<tr id="acui_empty_cell_wrapper" class="form-field form-required">
					<th scope="row"><label for="empty_cell_action"><?php _e( 'What should the plugin do with empty cells?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'leave' => __( 'Leave the old value for this metadata', 'import-users-from-csv-with-meta' ), 'delete' => __( 'Delete the metadata', 'import-users-from-csv-with-meta' ) ),
                            'name' => 'empty_cell_action',
                            'show_option_all' => false,
                            'show_option_none' => false,
							'selected' => $settings->get( 'empty_cell_action' ),
                        )); ?>
					</td>
				</tr>

				<tr id="acui_send_email_wrapper" class="form-field">
					<th scope="row"><label for="user_login"><?php _e( 'Send mail', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<p id="sends_email_wrapper">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'sends_email', 'label' => __( 'Do you wish to send a mail from this plugin with credentials and other data? <a href="' . admin_url( 'tools.php?page=acui&tab=mail-options' ) . '">(email template found here)</a>', 'import-users-from-csv-with-meta' ), 'current' => 'yes', 'compare_value' => $settings->get( 'sends_email' ) ) ); ?>
						</p>
						<p id="send_email_updated_wrapper">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'send_email_updated', 'label' => __( 'Do you wish to send this mail also to users that are being updated? (not only to the one which are being created)', 'import-users-from-csv-with-meta' ), 'current' => 'yes', 'compare_value' => $settings->get( 'send_email_updated' ) ) ); ?>
						</p>
					</td>
				</tr>

                <tr class="form-field form-required">
					<th scope="row"><label for=""><?php _e( 'Force users to reset their passwords?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->checkbox( array( 'name' => 'force_user_reset_password', 'label' => __( 'If a password is set to an user and you activate this option, the user will be forced to reset their password in their first login', 'import-users-from-csv-with-meta' ), 'current' => 'yes', 'compare_value' => $settings->get( 'force_user_reset_password' ) ) ); ?>
						<p class="description"><?php echo sprintf( __( 'Please, <a href="%s">read the documentation</a> before activating this option', 'import-users-from-csv-with-meta' ), admin_url( 'tools.php?page=acui&tab=doc#force_user_reset_password' ) ); ?></p>
					</td>
				</tr>

				<?php do_action( 'acui_homepage_after_options_rows' ); ?>

				</tbody>
			</table>

			<h2 id="acui_update_users_header"><?php _e( 'Update users', 'import-users-from-csv-with-meta'); ?></h2>

			<table id="acui_update_users_wrapper" class="form-table">
				<tbody>

				<?php do_action( 'acui_homepage_before_update_users_rows' ); ?>

				<tr id="acui_update_existing_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_existing_users"><?php _e( 'Update existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'import-users-from-csv-with-meta' ), 'yes' => __( 'Yes', 'import-users-from-csv-with-meta' ), ),
                            'name' => 'update_existing_users',
                            'show_option_all' => false,
                            'show_option_none' => false,
							'selected' => $settings->get( 'update_existing_users' ),
                        )); ?>
					</td>
				</tr>

				<tr id="acui_update_emails_existing_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_emails_existing_users"><?php _e( 'Update emails?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'import-users-from-csv-with-meta' ), 'create' => __( 'No, but create a new user with a prefix in the username', 'import-users-from-csv-with-meta' ), 'yes' => __( 'Yes', 'import-users-from-csv-with-meta' ) ),
                            'name' => 'update_emails_existing_users',
                            'show_option_all' => false,
                            'show_option_none' => false,
							'selected' => $settings->get( 'update_emails_existing_users' ),
                        )); ?>
						<p class="description"><?php _e( 'What the plugin should do if the plugin find an user, identified by their username, with a different email', 'import-users-from-csv-with-meta' ); ?></p>
					</td>
				</tr>

				<tr id="acui_update_roles_existing_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_roles_existing_users"><?php _e( 'Update roles for existing users?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'no' => __( 'No', 'import-users-from-csv-with-meta' ), 'yes' => __( 'Yes, update and override existing roles', 'import-users-from-csv-with-meta' ), 'yes_no_override' => __( 'Yes, add new roles and not override existing ones', 'import-users-from-csv-with-meta' ) ),
                            'name' => 'update_roles_existing_users',
                            'show_option_all' => false,
                            'show_option_none' => false,
							'selected' => $settings->get( 'update_roles_existing_users' ),
                        )); ?>
					</td>
				</tr>

				<tr id="acui_update_allow_update_passwords_wrapper" class="form-field form-required">
					<th scope="row"><label for="update_allow_update_passwords"><?php _e( 'Never update passwords?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
                        <?php ACUIHTML()->select( array(
                            'options' => array( 'no' => __( 'Never update passwords when updating a user', 'import-users-from-csv-with-meta' ), 'yes_no_override' => __( 'Yes, add new roles and not override existing ones', 'import-users-from-csv-with-meta' ), 'yes' => __( 'Update passwords as it is described in documentation', 'import-users-from-csv-with-meta' ) ),
                            'name' => 'update_allow_update_passwords',
                            'show_option_all' => false,
                            'show_option_none' => false,
							'selected' => $settings->get( 'update_allow_update_passwords' ),
                        )); ?>
					</td>
				</tr>

				<?php do_action( 'acui_homepage_after_update_users_rows' ); ?>

				</tbody>
			</table>

			<h2 id="acui_users_not_present_header"><?php _e( 'Users not present in CSV file', 'import-users-from-csv-with-meta'); ?></h2>

			<table id="acui_users_not_present_wrapper" class="form-table">
				<tbody>

				<?php do_action( 'acui_homepage_before_users_not_present_rows' ); ?>
				
				<tr id="acui_delete_users_wrapper" class="form-field form-required">
					<th scope="row"><label for="delete_users_not_present"><?php _e( 'Delete users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'delete_users_not_present', 'current' => 'yes', 'compare_value' => $settings->get( 'delete_users_not_present' ) ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php ACUIHTML()->select( array(
								'options' => $delete_users_assign_posts_options,
                                'name' => 'delete_users_assign_posts',
                                'show_option_all' => false,
                                'show_option_none' => __( 'Delete posts of deleted users without assigning to any user or type to search a user', 'import-users-from-csv-with-meta' ),
								'selected' => $delete_users_assign_posts_option_selected,
                            )); ?>
							<p class="description"><?php _e( 'Administrators will not be deleted anyway. After delete users, we can choose if we want to assign their posts to another user. If you do not choose some user, content will be deleted.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>

				<tr id="acui_not_present_wrapper" class="form-field form-required">
					<th scope="row"><label for="change_role_not_present"><?php _e( 'Change role of users that are not present in the CSV?', 'import-users-from-csv-with-meta' ); ?></label></th>
					<td>
						<div style="float:left; margin-top: 10px;">
                            <?php ACUIHTML()->checkbox( array( 'name' => 'change_role_not_present', 'current' => 'yes', 'compare_value' => $settings->get( 'change_role_not_present' ) ) ); ?>
						</div>
						<div style="margin-left:25px;">
                            <?php ACUIHTML()->select( array(
                                'options' => ACUI_Helper::get_editable_roles(),
                                'name' => 'change_role_not_present_role',
                                'show_option_all' => false,
                                'show_option_none' => false,
								'selected' => $settings->get( 'change_role_not_present_role' ),
                            )); ?>
							<p class="description"><?php _e( 'After import users which is not present in the CSV and can be changed to a different role.', 'import-users-from-csv-with-meta' ); ?></p>
						</div>
					</td>
				</tr>

				<?php do_action( 'acui_homepage_after_users_not_present_rows' ); ?>

				</tbody>
			</table>

			<?php do_action( 'acui_tab_import_before_import_button' ); ?>
				
			<?php wp_nonce_field( 'codection-security', 'security' ); ?>

			<input class="button-primary" type="submit" name="uploadfile" id="uploadfile_btn" value="<?php _e( 'Start importing', 'import-users-from-csv-with-meta' ); ?>"/>
			<input class="button-primary" type="submit" name="save_options" id="save_options" value="<?php _e( 'Save options without importing', 'import-users-from-csv-with-meta' ); ?>"/>
			</form>
		</div>

		<div class="sidebar">
			<div class="sidebar_section premium_addons">
		    	<a class="premium-addons" color="primary" type="button" name="premium-addons" data-tag="premium-addons" href="https://www.import-wp.com/" role="button" target="_blank">
		    		<div><span><?php _e( 'Premium Addons', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

			<div class="sidebar_section premium_addons">
		    	<a class="premium-addons" color="primary" type="button" name="premium-addons" data-tag="premium-addons" href="https://import-wp.com/recurring-export-addon/" role="button" target="_blank">
		    		<div><span><?php _e( 'Automatic Exports', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

			<div class="sidebar_section premium_addons">
		    	<a class="premium-addons" color="primary" type="button" name="premium-addons" data-tag="premium-addons" href="https://import-wp.com/allow-no-email-addon/" role="button" target="_blank">
		    		<div><span><?php _e( 'Allow No Email', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

			<div class="sidebar_section become_patreon">
		    	<a class="patreon" color="primary" type="button" name="become-a-patron" data-tag="become-patron-button" href="https://www.patreon.com/carazo" role="button" target="_blank">
		    		<div><span><?php _e( 'Become a patron', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

			<div class="sidebar_section buy_me_a_coffee">
		    	<a class="ko-fi" color="primary" type="button" name="buy-me-a-coffee" data-tag="buy-me-a-button" href="https://ko-fi.com/codection" role="button" target="_blank">
		    		<div><span><?php _e( 'Buy me a coffee', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

			<div class="sidebar_section vote_us">
		    	<a class="vote-us" color="primary" type="button" name="vote-us" data-tag="vote_us" href="https://wordpress.org/support/plugin/import-users-from-csv-with-meta/reviews/" role="button" target="_blank">
		    		<div><span><?php _e( 'If you like it', 'import-users-from-csv-with-meta'); ?> <?php _e( 'Please vote and support us', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>

			<div class="sidebar_section donate">
		    	<a class="donate-button" color="primary" type="button" name="donate-button" data-tag="donate" href="https://paypal.me/imalrod" role="button" target="_blank">
		    		<div><span><?php _e( 'If you want to help us to continue developing it and give the best support you can donate', 'import-users-from-csv-with-meta'); ?></span></div>
		    	</a>
		    </div>
			
			<div class="sidebar_section">
				<h3><?php _e( 'Having issues?', 'import-users-from-csv-with-meta'); ?></h3>
				<ul>
					<li><label><?php _e( 'You can create a ticket', 'import-users-from-csv-with-meta'); ?></label> <a target="_blank" href="http://wordpress.org/support/plugin/import-users-from-csv-with-meta"><label><?php _e( 'WordPress support forum', 'import-users-from-csv-with-meta'); ?></label></a></li>
					<li><label><?php _e( 'You can ask for premium support', 'import-users-from-csv-with-meta'); ?></label> <a target="_blank" href="mailto:contacto@codection.com"><label>contacto@codection.com</label></a></li>
				</ul>
			</div>
		</div>

	</div>
	<script type="text/javascript">
	jQuery( document ).ready( function( $ ){
		check_delete_users_checked();

        $( '#uploadfile_btn' ).click( function(){
            if( $( '#uploadfile' ).val() == "" && $( '#upload_file' ).is( ':visible' ) ) {
                alert("<?php _e( 'Please choose a file', 'import-users-from-csv-with-meta' ); ?>");
                return false;
            }

            if( $( '#path_to_file' ).val() == "" && $( '#introduce_path' ).is( ':visible' ) ) {
                alert("<?php _e( 'Please enter a path to the file', 'import-users-from-csv-with-meta' ); ?>");
                return false;
            }
        } );

		$( '.acui-checkbox.roles[value="no_role"]' ).click( function(){
			var checked = $( this ).is(':checked');
			if( checked ) {
				if( !confirm( '<?php _e( 'Are you sure you want to disables roles from this users?', 'import-users-from-csv-with-meta' ); ?>' ) ){         
					$( this ).removeAttr( 'checked' );
					return;
				}
				else{
					$( '.acui-checkbox.roles' ).not( '.acui-checkbox.roles[value="no_role"]' ).each( function(){
						$( this ).removeAttr( 'checked' );
					} )
				}
			}
		} );

		$( '.acui-checkbox.roles' ).click( function(){
			if( $( this ).val() != 'no_role' && $( this ).val() != '' )
				$( '.acui-checkbox.roles[value="no_role"]' ).removeAttr( 'checked' );
		} );

		$( '#delete_users_not_present' ).on( 'click', function() {
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
                    if( params.term.trim().length < 3 )
                        throw false;
  
                    var query = {
                        search: params.term,
                        _wpnonce: '<?php echo wp_create_nonce( 'codection-security' ); ?>',
                        action: 'acui_delete_users_assign_posts_data',
                    }

                    return query;
                }
            },	
        });

		function check_delete_users_checked(){
			if( $( '#delete_users_not_present' ).is( ':checked' ) ){
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
