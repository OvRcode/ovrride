<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'wpum-groups/wpum-groups.php' ) ){
	return;
}

class ACUI_WPUM_Groups{
	function __construct(){
    }

    function hooks(){
		add_action( 'post_acui_import_single_user', array( $this, 'assign_group' ), 10, 7 );
	}

	function assign_group( $headers, $data, $user_id, $role, $positions, $form_data, $is_frontend ){
		if( !$is_frontend )
			return;

		$group_ids = wpumgrp_get_user_group_ids( get_current_user_id() );
		if( empty( $group_ids ) )
			return;
		
		foreach( $group_ids as $group_id ){
			wpumgp_join_group( $group_id, $user_id );
			wpumgr_approve_group_member( $group_id, $user_id );
		}
	}
}

$acui_wpum_groups = new ACUI_WPUM_Groups();
$acui_wpum_groups->hooks();