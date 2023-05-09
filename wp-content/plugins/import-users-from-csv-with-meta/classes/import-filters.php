<?php
if ( ! defined( 'ABSPATH' ) ) exit; 

class ACUI_Import_Filters{
    function __construct(){
    }
    
    function hooks(){
        add_filter( 'pre_acui_import_single_user_username', array( $this, 'pre_import_single_user_username' ) );
    }

    function pre_import_single_user_username( $username ){
        $acui_helper = new ACUI_Helper();
        return empty( $username ) ? $acui_helper->get_random_unique_username( 'user_' ) : $username;
    }
}

$acui_import_filters = new ACUI_Import_Filters();
$acui_import_filters->hooks();