<?php
class ovr_wide_ad_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_wide_ad',

    // Widget name will appear in UI
    'OvR wide Ad',

    // Widget description
    array( 'description' => 'wide tile widget to display an ad' )
    );
  }
  public function form($instance) {
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script('ovr_wide_ad_widget_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-wide-ad-widget-admin.min.js', array('jquery') );

    $adID       = $this->get_field_id('ad');
    $adLinkID   = $this->get_field_id('link');
    $adName     = $this->get_field_name('ad');
    $adLinkName = $this->get_field_name('link');
    $ad         = $instance['ad'];
    $adLink     = $instance['link'];

    echo <<<WIDGETADMIN
      <p>
        <label for="{$adID}">Ad Image: </label>
        <input class="widefat" id="{$adID}" name="{$adName}" type="text" value="{$ad}" />
        <button class="wide_ad_upload_image_button button button-primary">Upload Ad Image</button>
      </p>
      <p>
        <label for="{$adLinkID}">Ad Link: </label>
        <input class="widefat" id="$adLinkID" name="{$adLinkName}" type="text" value="{$adLink}" />
      </p>
WIDGETADMIN;
  }
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['ad'] = ( ! empty( $new_instance['ad'] ) ) ? strip_tags( $new_instance['ad'] ) : '';
    $instance['link'] = esc_url(( ! empty( $new_instance['link'] ) ) ? strip_tags( $new_instance['link'] ) : '');

    return $instance;
  }
  public function widget($args, $instance) {
    wp_enqueue_style('ovr_wide_ad_widget_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-wide-ad-widget.min.css');
    echo <<<WIDGET
      <div class="ovr_wide_ad_widget">
        <div class="ovr_wide_ad_inner">
          <a href="{$instance['link']}" target="_blank">
            <img class="ovr_wide_ad_img" src="{$instance['ad']}">
          </a>
        </div>
      </div>
WIDGET;
  }
}
