<?php

class SM_Ajax_Action {

    public static function init() {
        add_action( 'wp_ajax_sm_dismiss_feature_notice', array( 'SM_Ajax_Action', 'dissmiss_feature_notice' ) );
        add_action( 'wp_ajax_sm_dissmiss_modification_notice', array( __CLASS__, 'dissmiss_modification_notice' ) );
        add_action( 'wp_ajax_sm_dissmiss_news_notice', array( __CLASS__, 'dissmiss_news_notice' ) );
    }

    public static function dissmiss_feature_notice() {
        if( isset( $_POST['feature_notice_dissmiss'] ) && $_POST['feature_notice_dissmiss'] == 1 ) {
            update_option( 'sm_dismiss_feature_notice', $_POST['feature_notice_dissmiss'] );
        }
    }

    public static function dissmiss_modification_notice() {
        $notices = sm_get_notice('sm_admin_notices' );
        $notices['modification_notice']['is_dismissed'] = true;
        if ( update_option( 'sm_admin_notices', $notices ) ) {
            echo wp_send_json_success();
        }
    }

    public static function dissmiss_news_notice() {
        $notices = sm_get_notice('sm_admin_notices' );
        $notices['news_notice']['is_dismissed'] = true;
        if( isset( $_POST['last_news_date'] ) ) {
            $notices['news_notice']['last_news_date'] = $_POST['last_news_date'];
        } else {
            $notices['news_notice']['last_news_date'] = 0;
        }

        if ( update_option( 'sm_admin_notices', $notices ) ) {
            wp_send_json_success();
        }
        exit;
    }
}

SM_Ajax_Action::init();