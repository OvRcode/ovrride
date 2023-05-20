<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

if( !is_plugin_active( 'wp-private-content-plus/wp-private-content-plus.php' ) ){
	return;
}

class ACUI_WP_Private_Content_Plus{
	function __construct(){
	}

    function bootstrap(){
        add_filter( 'acui_restricted_fields', array( $this, 'restricted_fields' ), 10, 1 );
        add_filter( 'acui_export_columns', array( $this, 'export_columns' ), 10, 1 );
		add_filter( 'acui_export_data', array( $this, 'export_data' ), 10, 2 );
        add_action( 'post_acui_import_single_user', array( $this, 'import' ), 10, 3 );        
    }

	function restricted_fields( $acui_restricted_fields ){
		return array_merge( $acui_restricted_fields, array( 'wp_private_content_plus_groups' ) );
	}

    function export_columns( $row ){
		$row['wp_private_content_plus_groups'] = 'wp_private_content_plus_groups';
		return $row;
	}

	function export_data( $row, $user_id ){
        global $wppcp;
        $user_groups = $wppcp->groups->get_user_groups_by_id( $user_id );
        $user_groups_slugs = array();

        foreach( $user_groups as $user_group ){
            $user_group_object = get_post( $user_group );

            if( is_a( $user_group_object, 'WP_Post')  )
                $user_groups_slugs[] = $user_group_object->post_name;
        }
        
		$row[] = implode( ',', $user_groups_slugs );
		return $row;
	}

    function import( $headers, $row, $user_id ){
        global $wpdb;

        $pos_wp_private_content_plus_groups = array_search( 'wp_private_content_plus_groups', $headers );
		if( $pos_wp_private_content_plus_groups === FALSE )
            return;
        
        $wp_private_content_plus_groups = explode( ',', $row[ $pos_wp_private_content_plus_groups ] );
        if( !is_array( $wp_private_content_plus_groups ) )
            return;

        foreach( $wp_private_content_plus_groups as $group ){
            $user_id = (int) $user_id;
            $group_object = get_page_by_path( $group, OBJECT, 'wppcp_group' );

            if( empty( $group_object ) )
                continue;
                    
            $wpdb->get_results( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wppcp_group_users where group_id=%d and user_id=%d", $group_object->ID , $user_id) );
            $wpdb->get_results( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}wppcp_group_users( group_id, user_id, updated_at) values(%d,%d,'%s')", $group_object->ID , $user_id, date("Y-m-d H:i:s") ) );
        }
	}
}

$acui_wp_private_content_plus = new ACUI_WP_Private_Content_Plus();
$acui_wp_private_content_plus->bootstrap();