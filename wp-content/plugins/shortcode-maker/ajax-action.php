<?php

class SM_Ajax_Action {

    public static function init() {
        add_action( 'wp_ajax_sm_dismiss_feature_notice', array( 'SM_Ajax_Action', 'dissmiss_feature_notice' ) );
    }

    public static function dissmiss_feature_notice() {
        if( isset( $_POST['feature_notice_dissmiss'] ) && $_POST['feature_notice_dissmiss'] == 1 ) {
            update_option( 'sm_dismiss_feature_notice', $_POST['feature_notice_dissmiss'] );
        }
    }
}

SM_Ajax_Action::init();