<?php
class ovr_blog_feature_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_blog_feature',

    // Widget name will appear in UI
    'OvR Blog Feature',

    // Widget description
    array( 'description' => 'Widget to display a story from blog in a small tile' )
    );
  }
  public function form($instance) {
    // Widget Admin Form
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script('ovr_blog_feature_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-blog-feature-admin.min.js', array('jquery') );
    // Get list of recent posts
    $options = $this->get_recent_posts_options($instance['post']);
    // Setup field variables
    $postID = $this->get_field_id( 'post' );
    $postLabel = 'Selected post:';
    $postFieldName = $this->get_field_name('post');
    $primaryImage = esc_url(! empty( $instance['primaryImage'] ) ? $instance['primaryImage'] : '');
    $primaryImageID = $this->get_field_id( 'primaryImage' );
    $primaryImageName = $this->get_field_name('primaryImage');
    $primaryImageLabel = "Primary Image: 247x164px ";
    $secondaryImage = esc_url(! empty( $instance['secondaryImage'] ) ? $instance['secondaryImage'] : '');
    $secondaryImageID = $this->get_field_id( 'secondaryImage' );
    $secondaryImageName = $this->get_field_name('secondaryImage');
    $secondaryImageLabel = "Secondary Image: 150x50px ";
    echo <<<ADMINFORM
      <p>
        <label for="{$postID}">{$postLabel}</label>
        <select id="{$postID}" name="{$postFieldName}" style="width:100%;">
          {$options}
        </select>
      </p>
      <p>
        <label for="{$primaryImageID}">{$primaryImageLabel}</label>
        <input class="widefat" id="{$primaryImageID}" name="{$primaryImageName}" type="text" value="{$primaryImage}" />
        <button class="upload_image_button button button-primary">Upload Primary Image</button>
      </p>
      <p>
        <label for="{$secondaryImageID}">{$secondaryImageLabel}</label>
        <input class="widefat" id="{$secondaryImageID}" name="{$secondaryImageName}" type="text" value="{$secondaryImage}" />
        <button class="upload_image_button button button-primary">Upload Secondary Image</button>
      </p>
ADMINFORM;
  }
  public function update( $new_instance, $old_instance ) {
    $instance = '';
    $instance['post'] = ( ! empty( $new_instance['post'] ) ) ? strip_tags( $new_instance['post'] ) : '';
    $instance['primaryImage'] = ( ! empty( $new_instance['primaryImage'] ) ) ? $new_instance['primaryImage'] : '';
    $instance['secondaryImage'] = ( ! empty( $new_instance['secondaryImage'] ) ) ? $new_instance['secondaryImage'] : '';
    if ( "" !== $instance['post'] ) {
      $instance['title'] = get_the_title($instance['post']);
      $instance['date'] = get_the_time('M j, Y', $instance['post']);
      $instance['link'] = get_the_permalink($instance['post']);
    } else {
      $instance['title'] = '';
      $instance['date'] = '';
      $instance['link'] = '';
    }

    return $instance;
  }
  public function widget( $args, $instance ) {
    wp_enqueue_style('ovr_blog_feature_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-blog-feature-widget.min.css');
    wp_enqueue_script('ovr_blog_feature_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-blog-feature-widget.min.js', array('jquery'), false, true );

    echo <<<FRONTEND
      <div class="ovr_blog_feature" data-link="{$instance['link']}">
        <div class="ovr_blog_feature_inner">
          <div class="ovr_blog_feature_content">
            <img class="ovr_blog_feature_primary_image" src="{$instance['primaryImage']}">
            <h5 class="ovr_blog_feature_header">on the blog</h5>
            <span class="ovr_blog_feature_title" maxlength="25">
              <a href="{$instance['link']}">{$instance['title']}</a>
            </span>
            <p class="ovr_blog_feature_date">{$instance['date']}</p>
            <img class="ovr_blog_feature_secondary_image" src={$instance['secondaryImage']}>
          </div>
        </div>
      </div>
FRONTEND;
  }
  private function get_recent_posts_options($post) {
    // Get recent posts, verify post passed to function is in the list
    // Add post to list if it is not then return options string for select element
    $args = array(
      'numberposts' => 50,
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
    $posts = wp_get_recent_posts( $args, ARRAY_A);
    $options = "<option>Select a post for top story</option>";
    $instanceCheck = false;
    foreach($posts as $index => $post_data ) {
      if ( $post == $post_data['ID'] ) {
        $instanceCheck = true; // Only change if we found the value
        $selected = "selected";
      } else {
        $selected = "";
      }
      $options .= "<option value='{$post_data['ID']}' {$selected}>{$post_data['post_title']}</option>";
    }
    unset($recent_posts);
    if ( !$instanceCheck && "" !== $post) {
      $options .= "<option value='{$post}' selected>". get_the_title($post) ."</option>";
    }
    return $options;
  }
}
