<?php
/**
 * Represents the main wrapper for master slider admin page.
 *
 * @package   MasterSlider
 * @author    averta [averta.net]
 * @license   LICENSE.txt
 * @link      http://masterslider.com
 * @copyright Copyright © 2014 averta
 */

 $msp_nonce = wp_create_nonce( 'msp_panel' );
 $action  = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
 $slider_id = isset( $_REQUEST['slider_id'] ) ? $_REQUEST['slider_id'] : '';
 $do    = isset( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';


function msp_thankyou_footer() {

    $text = sprintf(
        __('If you like %sMaster Slider%s and want to support us, please rate us %s ★★★★★ %s, that is a huge help!', 'master-slider' ),
        '<a href="http://masterslider.com/?msl" title="Version ' . MSWP_AVERTA_VERSION . '" target="_blank">',
        '</a>',
        '<a href="https://wordpress.org/support/view/plugin-reviews/master-slider/?filter=5#postform" target="_blank">',
        '</a>'
    );

    return '<span id="footer-thankyou">' . $text . '</span>';
}
add_filter( 'admin_footer_text',  'msp_thankyou_footer' );


?>

<div id="msp-main-wrapper" class="wrap" data-nonce="<?php echo $msp_nonce; ?>" >

<?php

  // process slider data and generate required thumbnails for slider panel
  // if( ! empty( $slider_id ) && isset( $_REQUEST['fr'] ) )
  //  msp_get_ms_slider_shortcode_by_slider_id( $slider_id );

  // If the requested page is edit page
  if( in_array( $action, array( 'add', 'edit' ) ) ){

    include( 'slider-panel/index.php' );

  // If the requested page is preview page
  } elseif( 'preview' == $action ) {

    include( 'slider-dashboard/preview.php' );

  // Otherwise display sliders list
  } else {

    include( 'slider-dashboard/list-sliders.php' );

  }

?>

</div>
