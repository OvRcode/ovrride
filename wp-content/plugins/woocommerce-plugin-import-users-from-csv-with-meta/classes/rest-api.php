<?php

if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_REST_API{
	function __construct(){
		add_action( 'rest_api_init', array( $this, 'init' ) );
        add_filter( 'acui_rest_api_permission_callback', function(){ return true; } );
	}

	function init() {
		register_rest_route( 'import-users-from-csv-with-meta/v1', '/execute-cron/', array( 
			'methods' => 'GET',  
			'callback' => array( $this, 'fire_cron' ),
			'permission_callback' => function () {
				return apply_filters( 'acui_rest_api_permission_callback', current_user_can( apply_filters( 'acui_capability', 'create_users' ) ) );
			}
		) );
	}

	function fire_cron(){
		do_action( 'acui_cron_process' );
		return "OK";
	}
}

new ACUI_REST_API();