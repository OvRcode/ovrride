<?php


function sm_get_all_shortcode_packages() {
    return apply_filters('smps_package_dir', array(
        'simple-light' => 'Simple Light'
    ));
}

function sm_save_shortcode_packages( $data ) {
    update_option( 'sm_shortcode_package_list', $data );
}

function sm_get_shortcode_packages() {
    $sm_shortcode_packages = get_option( 'sm_shortcode_package_list' );
    return !is_array( $sm_shortcode_packages ) ? array() : $sm_shortcode_packages;
}

if( !function_exists( 'sm_is_pro' ) ) {
    function sm_is_pro() {
        if( is_file( SHORTCODE_MAKER_ROOT.'/pro/loader.php' ) ) {
            return true;
        } else {
            return false;
        }
    }
}
if( !function_exists( 'sm_get_notice' ) ) {
    function sm_get_notice ( $notice_name =  'sm_admin_notices'  ) {
        $notice = get_option( $notice_name );
        if( !is_array( $notice ) ) $notice = array();
        return $notice;
    }
}
if( !function_exists( 'pri' ) ) {
    function pri($data) {
        echo '<pre>';print_r($data);echo '</pre>';
    }
}

if( !function_exists( 'sm_get_package_settings' ) ) {

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
}


if( !function_exists( 'sm_get_package_classname' ) ) {
    function sm_get_package_classname( $slug = '') {
        return $classname = 'Smps_'.ucwords(str_replace('-','_',$slug),'_');
    }
}



if( !function_exists( 'sm_extract_shortcode_atts' ) ) {

    function sm_extract_shortcode_atts ( $content, $ignore_html = false ) {

        global $shortcode_tags;

        if ( false === strpos( $content, '[' ) ) {
            return $content;
        }

        if (empty($shortcode_tags) || !is_array($shortcode_tags))
            return $content;

        // Find all registered tag names in $content.
        preg_match_all( '@\[([^<>&/\[\]\x00-\x20=]++)@', $content, $matches );
        $tagnames = array_intersect( array_keys( $shortcode_tags ), $matches[1] );

        if ( empty( $tagnames ) ) {
            return $content;
        }

        $content = do_shortcodes_in_html_tags( $content, $ignore_html, $tagnames );

        $pattern = get_shortcode_regex( $tagnames );
        $atts = preg_replace_callback( "/$pattern/", 'sm_get_all_atts', $content);

        // Always restore square braces so we don't break things like <!--[if IE ]>
        //$content = unescape_invalid_shortcodes( $content );

        $atts = json_decode($atts,true);
        return $atts;
    }
}



if( !function_exists( 'sm_get_all_atts' ) ) {

    function sm_get_all_atts( $m ) {
        global $shortcode_tags;

        // allow [[foo]] syntax for escaping a tag
        if ( $m[1] == '[' && $m[6] == ']' ) {
            return substr($m[0], 1, -1);
        }

        $tag = $m[2];
        $attr = shortcode_parse_atts( $m[3] );
        return json_encode($attr);
    }
}
