<?php

function sm_save_shortcode_packages( $data ) {
    update_option( 'sm_shortcode_packages', $data );
}

function sm_get_shortcode_packages() {
    $sm_shortcode_packages = get_option( 'sm_shortcode_packages' );
    return !is_array( $sm_shortcode_packages ) ? array() : $sm_shortcode_packages;
}

if( !function_exists( 'pri' ) ) {
    function pri($data) {
        echo '<pre>';print_r($data);echo '</pre>';
    }
}

function sm_get_package_settings( $classname = '', $slug = '' ) {
    if( $classname ) {
        if( !class_exists( $classname ) ) return;
        return $classname::settings();
    }

    if( $slug ) {
        $classname = 'Smps_'.ucwords(str_replace('-','_',$slug),'_');
        return $classname::settings();
    }
}

function sm_get_package_classname( $slug = '') {
    return $classname = 'Smps_'.ucwords(str_replace('-','_',$slug),'_');
}