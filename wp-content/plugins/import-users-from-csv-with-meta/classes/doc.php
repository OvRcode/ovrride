<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Doc{
	public static function message(){
	?>
	<h3><?php _e( 'Documentation', 'import-users-from-csv-with-meta' ); ?></h3>
		<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><?php _e( 'Columns position', 'import-users-from-csv-with-meta' ); ?></th>
				<td><small><em><?php _e( '(Documents should look like the one presented into screenshot. Remember you should fill the first two columns with the next values)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<ol>
						<li><?php _e( 'Username: you can leave it empty and the username will be generated randomly', 'import-users-from-csv-with-meta' ); ?> </li>
						<li><?php _e( 'Email:', 'import-users-from-csv-with-meta' ); ?> <?php echo apply_filters( 'acui_documentation_email_message', sprintf( __( 'required, although you can use <a href="%s">this addon to allow users to be imported without an associated email address</a>.','import-users-from-csv-with-meta' ), 'https://import-wp.com/allow-no-email-addon/' ) ); ?></li>
					</ol>						
					<small><em><?php _e( '(The next columns are totally customizable and you can use whatever you want. All rows must contains same columns)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<small><em><?php _e( '(User profile will be adapted to the kind of data you have selected)', 'import-users-from-csv-with-meta' ); ?></em></small>
					<small><em><?php _e( '(If you want to disable the extra profile information, please deactivate this plugin after make the import)', 'import-users-from-csv-with-meta' ); ?></em></small>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'id (column id)', 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( 'You can use a column called id in order to make inserts or updates of an user using the ID used by WordPress in the wp_users table. We have two different cases:', 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "If id <strong>doesn't exist in your users table</strong>: WordPress core does not allow us insert it, so it will throw an error of kind: invalid_user_id", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If id <strong>exists</strong>: plugin check if username is the same, if yes, it will update the data, if not, it ignores the cell to avoid problems", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Passwords (column password and user_pass)", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "A string that contains user passwords. We have different options for this case:", 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
                        <li><?php _e( "If you <strong>create a column password</strong>: if cell is empty, password won't be updated; if cell has a value, it will be used.", 'import-users-from-csv-with-meta' ); ?></li>
                        <li><?php _e( "If you <strong>create a column called user_pass</strong>: this will be a hashed password that will be inserted directly in database, this is the best option to move users with their passwords using export tool", 'import-users-from-csv-with-meta' ); ?></li>
                        <li><?php _e( "If you <strong>don't create a column for passwords (nor user_pass nor password)</strong>: passwords will be generated automatically.", 'import-users-from-csv-with-meta' ); ?></li>
                        <li><?php _e( "You should not use both columns in the same import", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Roles (column role)", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can import roles from the CSV. This is how it works:", 'import-users-from-csv-with-meta' ); ?>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "If you <strong>don't create a column for roles</strong>: roles would be chosen from the 'Default role' field in import screen.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If you <strong>create a column called 'role'</strong>: if cell is empty, roles would be chosen from 'Default role' field in import screen; if cell has a value, it will be used as role, if this role doesn't exist the default one would be used", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "Multiple roles can be imported creating <strong>a list of roles</strong> using commas to separate values.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><?php _e( "If you choose <strong>no role</strong> checkbox or write <strong>no_role</strong> in the column role, the users created or updated won't have any role assigned.", 'import-users-from-csv-with-meta' ); ?></li>
					</ul>
					<em><?php _e( "Notice: If the default new role is administrator in WordPress settings, role will not be set during a CSV file import with this plugin. Check it if all users are being imported as administrators and you have set another role in this plugin.", 'import-users-from-csv-with-meta' ); ?></em>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Serialized data", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can now import serialized data. You have to use the serialized string directly in the CSV cell in order the plugin will be able to understand it as an serialized data instead as any other string.", 'import-users-from-csv-with-meta' ); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( "Lists", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can import lists an array. Use this separator:", 'import-users-from-csv-with-meta'); ?> <strong>::</strong> <?php _e("two colons, inside the cell in order to split the string in a list of items.", 'import-users-from-csv-with-meta' ); ?>
				</td>
			</tr>
            <tr valign="top">
				<th scope="row"><?php _e( "Arrays with string keys", 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "Plugin can also import arrays with string keys. Use this separator:", 'import-users-from-csv-with-meta'); ?> <strong>::</strong> <?php _e("two colons, inside the cell in order to split the string in a list of items. Every item should be splitted using => to separate the key from the value. For example: ", 'import-users-from-csv-with-meta' ); ?>key1=>value1::key2=>value2::key3=>value3
				</td>
			</tr>
			<tr valign="top" id="force_user_reset_password">
				<th scope="row"><?php _e( "Force users to reset their passwords", 'import-users-from-csv-with-meta' ); ?></th>
				<td>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( "This option will force users to go to their edit password screen after being created with this plugin. As this is an option that deals with profile screens and password edit actions that can change, <strong>you should be careful if you use a plugin to modify any of these functions</strong>.", 'import-users-from-csv-with-meta'); ?></li>
						<li><?php _e( "We <strong>support the standard WordPress method and WooCommerce</strong> but if you use other plugins that modify these views or actions you may have problems with infinite redirection loops with users who have this option checked.", 'import-users-from-csv-with-meta'); ?></li>
						<li><?php _e( "If you have this redirection problem, you can delete the metadata that forces you to change the password and causes the redirection loop problem for all users using the following button:", 'import-users-from-csv-with-meta'); ?></li>
					</ul>

					<input type="button" id="delete_all_metadata_forcing_password_change" class="button button-primary" value="<?php _e( 'Delete all metadata forcing password change for all users', 'import-users-from-csv-with-meta' ); ?>">
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e( 'WordPress default profile data', 'import-users-from-csv-with-meta' ); ?></th>
				<td><?php _e( "You can use those labels if you want to set data adapted to the WordPress default user columns (the ones who use the function", 'import-users-from-csv-with-meta' ); ?> <a href="http://codex.wordpress.org/Function_Reference/wp_update_user">wp_update_user</a>)
					<ol>
						<li><strong>user_nicename</strong>: <?php _e( "A string that contains a URL-friendly name for the user. The default is the user's username.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>user_url</strong>: <?php _e( "A string containing the user's URL for the user's web site.", 'import-users-from-csv-with-meta' ); ?>	</li>
						<li><strong>display_name</strong>: <?php _e( "A string that will be shown on the site. Defaults to user's username. It is likely that you will want to change this, for both appearance and security through obscurity (that is if you don't use and delete the default admin user).", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>nickname</strong>: <?php _e( "The user's nickname, defaults to the user's username.", 'import-users-from-csv-with-meta' ); ?>	</li>
						<li><strong>first_name</strong>: <?php _e( "The user's first name.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>last_name</strong>: <?php _e("The user's last name.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>description</strong>: <?php _e("A string containing content about the user.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>jabber</strong>: <?php _e("User's Jabber account.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>aim</strong>: <?php _e("User's AOL IM account.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>yim</strong>: <?php _e("User's Yahoo IM account.", 'import-users-from-csv-with-meta' ); ?></li>
						<li><strong>user_registered</strong>: <?php _e( "Using the WordPress format for this kind of data Y-m-d H:i:s.", "import-users-from-csv-with-meta "); ?></li>
					</ol>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Cron', 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( 'Cron tab allows you to make periodical imports using the WordPress cron scheduler.','import-users-from-csv-with-meta'); ?></td>
			</tr>

			<?php do_action( 'acui_documentation_after_plugins_activated' ); ?>
			
			<tr valign="top">
				<th scope="row"><?php _e( "Any question about it", 'import-users-from-csv-with-meta' ); ?></th>
				<td>
					<ul style="list-style:disc outside none; margin-left:2em;">
						<li><?php _e( 'Free support (in WordPress forums):', 'import-users-from-csv-with-meta' ); ?> <a href="https://wordpress.org/support/plugin/import-users-from-csv-with-meta">https://wordpress.org/support/plugin/import-users-from-csv-with-meta</a>.</li>
						<li><?php _e( 'Premium support (with a quote):', 'import-users-from-csv-with-meta' ); ?> <a href="mailto:contacto@codection.com">contacto@codection.com</a>.</li>
					</ul>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Hooks', 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( 'If you are a developer you can extend or use this plugin with all the hooks we provide, you have <a href="https://codection.com/import-users-csv-meta/listado-de-hooks-de-import-and-exports-users-and-customers/">a list of them here</a>','import-users-from-csv-with-meta'); ?></td>
			</tr>

			<tr valign="top">
				<th scope="row"><?php _e( 'Example', 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( 'Download this', 'import-users-from-csv-with-meta' ); ?> <a href="<?php echo esc_url( plugins_url( 'test.csv', dirname( __FILE__ ) ) ); ?>">.csv <?php _e('file','import-users-from-csv-with-meta'); ?></a> <?php _e( 'to test', 'import-users-from-csv-with-meta' ); ?></td>
			</tr>
		</tbody>
		</table>
		<br/>
		<div style="width:775px;margin:0 auto"><img src="<?php echo esc_url( plugins_url( 'csv_example.png', dirname( __FILE__ ) ) ); ?>"/></div>

		<script>
		jQuery( document ).ready( function( $ ){
			$( '#delete_all_metadata_forcing_password_change' ).click( function(){
				var r = confirm( '<?php _e( 'Are you sure?', 'import-users-from-csv-with-meta' ); ?>' );

				if( !r )
					return;

				var data = {
					'action': 'acui_force_reset_password_delete_metas',
					'security': '<?php echo wp_create_nonce( "codection-security" ); ?>'
				};

				$.post( ajaxurl, data, function( response ) {
					if( response == "ERROR" )
						alert( "<?php _e( 'Problems executing task: ', 'import-users-from-csv-with-meta' ); ?>" + response );
					else{
						alert( "<?php _e( 'Task successfully executed: ', 'import-users-from-csv-with-meta' ); ?> " + response + " <?php _e( ' rows deleted', 'import-users-from-csv-with-meta' ); ?>" );
					}
				});
			} );
		} )
		</script>
	<?php
	}
}