<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'buddypress/bp-loader.php' ) && !function_exists( 'bp_is_active' ) ){
	return;
}

class ACUI_Buddypress{
	var $fields;
	var $profile_groups;
	var $plugin_path;

	function __construct(){
		$this->plugin_path = is_plugin_active( 'buddyboss-platform/bp-loader.php' ) ? WP_PLUGIN_DIR . "/buddyboss-platform/" : WP_PLUGIN_DIR . "/buddypress/";

		if( !class_exists( 'BP_XProfile_Group' ) )
			require_once( $this->plugin_path . "bp-xprofile/classes/class-bp-xprofile-group.php" );
		
		$this->profile_groups = $this->get_profile_groups();
		$this->fields = $this->get_fields();
	}
	
	function hooks(){
		add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
		add_action( 'acui_tab_import_before_import_button', array( $this, 'show_compatibility' ) );
		add_action( 'acui_documentation_after_plugins_activated', array( $this, 'documentation' ) );
		add_filter( 'acui_export_columns', array( $this, 'export_columns' ), 10, 1 );
		add_filter( 'acui_export_data', array( $this, 'export_data' ), 10, 3 );
		add_action( 'post_acui_import_single_user', array( $this, 'import' ), 10, 6 );	
		add_action( 'post_acui_import_single_user', array( $this, 'import_avatar' ), 10, 3 );	
	}

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, array( 'bp_group', 'bp_group_role', 'bp_avatar' ), $this->fields );
	}

	function get_profile_groups(){
		return BP_XProfile_Group::get( array( 'fetch_fields' => true ) );
	}

	public function get_fields(){
		$buddypress_fields = array();
		
		if ( !empty( $this->profile_groups ) ) {
			 foreach ( $this->profile_groups as $profile_group ) {
				if ( !empty( $profile_group->fields ) ) {				
					foreach ( $profile_group->fields as $field ) {
						$buddypress_fields[] = $field->name;
					}
				}
			}
		}

		return $buddypress_fields;
	}

	function get_field_type( $field_name ){
		if ( !empty( $this->profile_groups ) ) {
			 foreach ( $this->profile_groups as $profile_group ) {
				if ( !empty( $profile_group->fields ) ) {				
					foreach ( $profile_group->fields as $field ) {
						if( $field_name == $field->name )
							return $field->type;
					}
				}
			}
		}
	}

	function get_type_import_help( $type ){
		switch( $type ){
			case 'datebox':
				$help = __( sprintf( 'Format should be like this: %s-01-01 00:00:00', date( 'Y' ) ), 'import-users-from-csv-with-meta' );
				break;
				
			case 'checkbox':
				$help = __( 'If you use more than one value, please use ## to separate each item', 'import-users-from-csv-with-meta' );
				break;
		}

		return empty( $help ) ? '' : " <em>($help)</em>";
	}

	function get_groups( $user_id ){
		if( !class_exists( "BP_Groups_Member" ) )
			require_once( $this->plugin_path . "bp-groups/classes/class-bp-groups-member.php" );

		$groups = BP_Groups_Member::get_group_ids( $user_id );
		return implode( ",", $groups['groups'] );
	}

	function get_member_type( $user_id ){
		$member_types = bp_get_member_type( $user_id, false );
		return ( is_array( $member_types ) ) ? implode( ",", $member_types ) : $member_types;
	}

	function show_compatibility(){
		?>
		<h2><?php _e( 'BuddyPress & BuddyBoss compatibility', 'import-users-from-csv-with-meta'); ?></h2>
	
		<table class="form-table">
			<tbody>
			<tr class="form-field form-required">
				<th scope="row"><label><?php _e( 'BuddyPress/BuddyBoss users', 'import-users-from-csv-with-meta' ); ?></label></th>
				<td><?php _e( 'You can insert any profile from BuddyPress using his name as header. Plugin will check, before import, which fields are defined in BuddyPress and will assign it in the update. You can use this fields:', 'import-users-from-csv-with-meta' ); ?>
				<ul style="list-style:disc outside none;margin-left:2em;">
					<?php foreach ( $this->get_fields() as $buddypress_field ): 
						$type = $this->get_field_type( $buddypress_field ); 
					?>
					<li><?php echo $buddypress_field; ?> - <?php echo $type . $this->get_type_import_help( $type ); ?></li>
					<?php endforeach; ?>
				</ul>
				</td>					
			</tr>
			</tbody>
		</table>
		<?php
	}
	
	function documentation(){
		?>
		<tr valign="top">
			<th scope="row"><?php _e( 'BuddyPress/BuddyBoss avatar', 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( 'You can import users avatars using a column called <strong>bp_avatar</strong>, in this field you can place:', 'import-users-from-csv-with-meta' ); ?>
			<ul style="list-style:disc outside none;margin-left:2em;">
					<li>An integer which identify the ID of an attachment uploaded to your media library</li>
					<li>A string that contain a path or an URL to the image</li>
				</ul>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( "BuddyPress or BuddyBoss is activated", 'import-users-from-csv-with-meta' ); ?></th>
			<td><?php _e( "You can use the <strong>profile fields</strong> you have created and also you can set one or more groups for each user. For example:", 'import-users-from-csv-with-meta' ); ?>
				<ul style="list-style:disc outside none; margin-left:2em;">
					<li><?php _e( "If you want to assign an user to a group you have to create a column 'bp_group' and a column 'bp_group_role'", 'import-users-from-csv-with-meta' ); ?></li>
					<li><?php _e( "Then in each cell you have to fill with the BuddyPress <strong>group slug</strong>", 'import-users-from-csv-with-meta' ); ?></li>
                    <li><?php _e( "And the role assigned in this group:", 'import-users-from-csv-with-meta' ); ?>  <em>Administrator, Moderator or Member</em></li>
					<li><?php _e( "You can also use group ids if you know it using a column 'bp_group_id' instead of 'bp_group'", 'import-users-from-csv-with-meta' ); ?></li>
					<li><?php _e( "You can do it with multiple groups at the same time using commas to separate different groups, in bp_group column, i.e.: <em>group_1, group_2, group_3</em>", 'import-users-from-csv-with-meta' ); ?></li>
					<li><?php _e( "But you will have to assign a role for each group:", 'import-users-from-csv-with-meta' ); ?> <em>Moderator,Moderator,Member,Member</em></li>
                    <li><?php _e( "If you choose to update roles and group role is empty, user will be removed from the group", 'import-users-from-csv-with-meta' ); ?></li>
					<li><?php _e( "If you get some error of this kind:", 'import-users-from-csv-with-meta' ); ?> <code>Fatal error: Class 'BP_XProfile_Group'</code> <?php _e( "please enable Buddypress Extended Profile then import the csv file. You can then disable this afterwards", 'import-users-from-csv-with-meta' ); ?></li>
				</ul>
			</td>
		</tr>
		<?php
	}

	function export_columns( $row ){
		foreach ( $this->fields as $key ) {
			$row[ $key ] = $key;
		}

		$row['bp_group_id'] = 'bp_group_id';
		$row['bp_member_type'] = 'bp_member_type';

		return $row;
	}

	function export_data( $row, $user, $args ){
		$fields_to_export = ( count( $args['filtered_columns'] ) == 0 ) ? $this->fields : array_intersect( $this->fields, $args['filtered_columns'] );
		
        foreach( $fields_to_export as $key ) {
			$row[ $key ] = xprofile_get_field_data( $key, $user, 'comma' );
		}

		if( count( $args['filtered_columns'] ) == 0 || in_array( 'bp_group_id', $args['filtered_columns'] ) )
			$row['bp_group_id'] = $this->get_groups( $user );

		if( count( $args['filtered_columns'] ) == 0 || in_array( 'bp_member_type', $args['filtered_columns'] ) )
			$row['bp_member_type'] = $this->get_member_type( $user );

		return $row;
	}

	function import( $headers, $row, $user_id, $role, $positions, $form_data ){
        $update_roles_existing_users = isset( $form_data["update_roles_existing_users"] ) ? sanitize_text_field( $form_data["update_roles_existing_users"] ) : '';

		foreach( $this->fields as $field ){
			$pos = array_search( $field, $headers );

			if( $pos === FALSE )
				continue;

			switch( $this->get_field_type( $field ) ){
				case 'datebox':
					$date = $row[$pos];
					switch( true ){
						case is_numeric( $date ):
							$UNIX_DATE = ($date - 25569) * 86400;
							$datebox = gmdate("Y-m-d H:i:s", $UNIX_DATE);break;
						case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-]([4567890]{1}\d{1})/',$date,$match):
							$match[3]='19'.$match[3];
						case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](20[4567890]{1}\d{1})/',$date,$match):
						case preg_match('/(\d{1,2})[\/-](\d{1,2})[\/-](19[4567890]{1}\d{1})/',$date,$match):
							$datebox= ($match[3].'-'.$match[2].'-'.$match[1]);
							break;

						default:
							$datebox = $date;
					}

					$datebox = strtotime( $datebox );
					xprofile_set_field_data( $field, $user_id, date( 'Y-m-d H:i:s', $datebox ) );
					unset( $datebox );
					break;
				
				case 'checkbox':
					xprofile_set_field_data( $field, $user_id, explode( '##', $row[ $pos ] ) );
					break;

				default:
					xprofile_set_field_data( $field, $user_id, $row[$pos] );
			}	
		}

		$pos_bp_group = array_search( 'bp_group', $headers );
        $pos_bp_group_id = array_search( 'bp_group_id', $headers );
		$pos_bp_group_role = array_search( 'bp_group_role', $headers );
		
        if( $pos_bp_group !== FALSE ){
			$groups = explode( ',', $row[ $pos_bp_group ] );
			$groups_role = explode( ',', $row[ $pos_bp_group_role ] );

            for( $j = 0; $j < count( $groups ); $j++ ){
				$group_id = BP_Groups_Group::group_exists( $groups[ $j ] );

				if( !empty( $group_id ) ){
					$this->add_user_group( $user_id, $group_id, $groups_role[ $j ], $update_roles_existing_users );
				}
			}
		}

        if( $pos_bp_group_id !== FALSE ){
			$groups_id = explode( ',', $row[ $pos_bp_group_id ] );
			$groups_role = explode( ',', $row[ $pos_bp_group_role ] );

            for( $j = 0; $j < count( $groups_id ); $j++ ){
				$group_id = intval( $groups_id[ $j ] );

				if( !empty( $group_id ) ){
					$this->add_user_group( $user_id, $group_id, $groups_role[ $j ], $update_roles_existing_users );
				}
			}
		}
			
		$pos_member_type = array_search( 'bp_member_type', $headers );
		if( $pos_member_type !== FALSE ){
			bp_set_member_type( $user_id, $row[$pos_member_type] );
		}
	}

    function add_user_group( $user_id, $group_id, $group_role, $update_roles_existing_users ){
        if( $update_roles_existing_users == 'yes' || $update_roles_existing_users == 'yes_no_override' ){
            $member = new BP_Groups_Member( $user_id, $group_id );
            $member->remove();
        }
        
        if( ( $update_roles_existing_users == 'yes' || $update_roles_existing_users == 'yes_no_override' ) && empty( $group_role ) )
            return;

        groups_join_group( $group_id, $user_id );

        if( $group_role == 'Moderator' ){
            groups_promote_member( $user_id, $group_id, 'mod' );
        }
        elseif( $group_role == 'Administrator' ){
            groups_promote_member( $user_id, $group_id, 'admin' );
        }
    }

	function import_avatar( $headers, $row, $user_id ){
		$pos = array_search( 'bp_avatar', $headers );

		if( $pos === FALSE )
			return;

		$this->import_avatar_raw( $user_id, $row[ $pos ] );
	}

	function import_avatar_raw( $user_id, $source ){
		$avatar_dir = bp_core_avatar_upload_path() . '/avatars';

		if ( ! file_exists( $avatar_dir ) ) {
			if ( ! wp_mkdir_p( $avatar_dir ) ) {
				return false;
			}
		}

		$avatar_folder_dir = apply_filters( 'bp_core_avatar_folder_dir', $avatar_dir . '/' . $user_id, $user_id, 'user', 'avatars' );

		if ( ! is_dir( $avatar_folder_dir ) ) {
			if ( ! wp_mkdir_p( $avatar_folder_dir ) ) {
				return false;
			}
		}

		$original_file = $avatar_folder_dir . '/import-export-users-customers-bp-avatar-' . $user_id . '.png';
		$data = ( (string)(int)$source == $source ) ? file_get_contents( get_attached_file( $source ) ) : file_get_contents( $source );
		
		if ( file_put_contents( $original_file, $data ) ) {
			$avatar_to_crop = str_replace( bp_core_avatar_upload_path(), '', $original_file );

			$crop_args = array(
				'item_id'       => $user_id,
				'original_file' => $avatar_to_crop,
				'crop_x'        => 0,
				'crop_y'        => 0,
			);

			return bp_core_avatar_handle_crop( $crop_args );
		} else {
			return false;
		}
	}
}
$acui_buddypress = new ACUI_Buddypress();
$acui_buddypress->hooks();