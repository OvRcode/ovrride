<?php
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
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script('ovr_jumbotron_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-jumbotron-admin.min.js' , array('jquery'));
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
    if ( !$instanceCheck && "" !== $instance['post']) {
      $options .= "<option value='{$instance['post']}' selected>". get_the_title($instance['post']) ."</option>";
    }

    // Setup field ids and names
    $postID = $this->get_field_id( 'post' );
    $postLabel = 'Selected post:';
    $postFieldName = $this->get_field_name('post');
    $image = esc_url(! empty( $instance['image'] ) ? $instance['image'] : '');
    $imageID = $this->get_field_id( 'image' );
    $imageName = $this->get_field_name('image');
    $imageLabel = "Image: ";
    echo <<<ADMINFORM
    <p>
      <label for="{$imageID}">{$imageLabel}</label>
      <input class="widefat" id="{$imageID}" name="{$imageName}" type="text" value="{$image}" />
      <button class="upload_image_button button button-primary">Upload Image</button>
    </p>
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
    $instance['image'] = ( ! empty( $new_instance['image'] ) ) ? $new_instance['image'] : '';
    if ( "" !== $instance['post'] ) {
      $instance['title'] = get_the_title($instance['post']);
      $instance['excerpt'] = get_the_excerpt($instance['post']);
      $instance['link'] = get_the_permalink($instance['post']);
    } else {
      $instance['title'] = '';
      $instance['excerpt'] = '';
      $instance['link'] = '';
    }
    return $instance;
  }
  public function widget( $args, $instance ) {
    wp_enqueue_style( 'ovr_jumbotron_widget_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-jumbotron-widget.min.css' );

    echo <<<FRONTEND
    <div class="ovr_jumbotron">
      <div class="jumbotron_inner">
        <div class="jumbotron_content">
          <img src="{$instance['image']}">
          <h1>{$instance['title']}</h1>
          <p>
            {$instance['excerpt']}<a href="{$instance['link']}">...Read More</a>
          </p>
        </div>

      </div>
    </div>
FRONTEND;
  }
}
