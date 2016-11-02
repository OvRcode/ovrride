<?php
/*
* Plugin Name: OvRride Jumbotron Widget
* Description: Widget to display a large feature story
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/

class ovr_jumbotron_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_jumbotron',

    // Widget name will appear in UI
    __('OvR Jumbotron', 'ovr_jumbotron'),

    // Widget description
    array( 'description' => __( 'Widget to display a large feature story', 'ovr_jumbotron_domain' ), )
    );
  }
  public function form($instance) {
    // Widget Admin Form
    // Get list of recent posts
    $args = array(
      'numberposts' => 20,
      'offset' => 0,
      'category' => 0,
      'orderby' => 'post_date',
      'order' => 'DESC',
      'include' => '',
      'exclude' => '',
      'meta_key' => '',
      'meta_value' =>'',
      'post_type' => 'post',
      'post_status' => 'publish',
      'suppress_filters' => true
    );
    $recent_posts = wp_get_recent_posts( $args, ARRAY_A);
    $options = "<option>Select a post for top story</option>";
    # Make sure post value set in instance array is set, could possibly not be in the last 20 posts
    $instanceCheck = false;
    foreach($recent_posts as $index => $post_data ) {
      if ( $instance['post'] == $post_data['ID'] ) {
        $instanceCheck = true; // Only change if we found the value
        $selected = "selected";
      } else {
        $selected = "";
      }
      $options .= "<option value='{$post_data['ID']}' {$selected}>{$post_data['post_title']}</option>";
    }
    unset($recent_posts);
    if ( !$instanceCheck ) {
      $options .= "<option value='{$instance['post']}' selected>". get_the_title($instance['post']) ."</option>";
    }
    // Setup field id and name
    $postID = $this->get_field_id( 'post' );
    $postLabel = 'Selected post:';
    $postFieldName = $this->get_field_name('post');
    echo <<<ADMINFORM
    <p>
    <label for="{$postID}">{$postLabel}</label>
    <select id="{$postID}" name="{$postFieldName}" style="width:100%;">
      {$options}
    </select>
    </p>
ADMINFORM;
  }
  public function update( $new_instance, $old_instance ) {
    $instance = array();
    $instance['post'] = ( ! empty( $new_instance['post'] ) ) ? strip_tags( $new_instance['post'] ) : '';
    return $instance;
  }
  public function widget( $args, $instance ) {
    echo $instance['post'];
  }
}
function ovr_jumbotron_load_widget() {
	register_widget( 'ovr_jumbotron_widget' );
}
add_action( 'widgets_init', 'ovr_jumbotron_load_widget' );
