<?php
/*
* Plugin Name: OvRride Featured Video Widget
* Description:  Widget to display featured video in medium tile
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/
class ovr_featured_video_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_featured_video',

    // Widget name will appear in UI
    'OvR Featured Video',

    // Widget description
    array( 'description' => 'Display featured video in medium tile' )
    );
  }
  public function form($instance) {
    $title = $instance['title'];
    $titleID      = $this->get_field_id('title');
    $titleName    = $this->get_field_name('title');
    $videoURLID   = $this->get_field_id('videoURL');
    $videoURLName = $this->get_field_name('videoURL');
    switch($instance["type"]) {
      case "vimeo":
        $youtubeClass = "youtube_inactive";
        $vimeoClass   = "vimeo";
      break;
      case "youtube":
        $youtubeClass = "youtube";
        $vimeoClass   = "vimeo_inactive";
      break;
      default:
        $youtubeClass = "youtube_inactive";
        $vimeoClass   = "vimeo_inactive";
      }

    wp_enqueue_style('ovr-featured-video-admin', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-featured-video-admin.min.css');
    echo <<<ADMINFORM
    <p>
      <label for="{$titleID}">Video title: 30 characters</label>
      <input class="ovr-featured-video-title" id="{$titleID}" name="{$titleName}" type="text" value="{$title}" maxlength="30">
    </p>
    <p>
      <label for="{$videoURLID}">Video URL: </label>
      <input id="{$videoURLID}" name="$videoURLName" type="text" value="{$instance['videoURL']}" style="width:100%;">
    </p>
    <div class="ovr-featured-video-icon {$youtubeClass}"></div>
    <div class="ovr-featured-video-icon {$vimeoClass}"></div>
    <br /><br /><br />
ADMINFORM;
  }
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['videoURL']= ( ! empty( $new_instance['videoURL'] ) ) ? strip_tags( $new_instance['videoURL'] ) : '';
    $instance['title']= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

    if ( preg_match("/vimeo.com/", $instance['videoURL']) ) {
      $instance['type'] = "vimeo";

      preg_match("/vimeo.com\/(.*)/", $$instance['videoURL'], $output_array);

      $url = "https://player.vimeo.com/video/{$output_array[1]}";

    } else if ( preg_match("/youtube.com/", $instance['videoURL']) ) {
      $instance['type'] = "youtube";

      preg_match("/youtube.com\/watch\?v=(.*)/", $instance['videoURL'], $output_array);

      $url = "https://www.youtube.com/embed/{$output_array[1]}";

    }
    $instance['iframe'] ="<iframe src='{$url}' frameborder='0' allowfullscreen></iframe>";

    return $instance;
  }
  public function widget( $args, $instance ) {
    wp_enqueue_style('ovr-featured-video-widget', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-featured-video-widget.min.css');
    wp_enqueue_script('ovr-featured-video-widget-js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-featured-video-widget.min.js', array('jquery') );
    echo <<<FRONTEND
      <div class="ovr_featured_video">
        <div class="ovr_featured_video_inner">
          <h4>Video of the Week:<br />{$instance['title']}</h4>
          <div class="ovr_featured_video_box">
            {$instance['iframe']}
          </div>
        </div>
      </div>
FRONTEND;
  }
}
