<?php
/*
* Plugin Name: OvRride Medium Trip Feature Widget
* Description:  Medium tile widget to display a trip in a medium tile.
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/
class ovr_medium_trip_feature_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_medium_trip_feature',

    // Widget name will appear in UI
    'OvR Medium Trip Feature',

    // Widget description
    array( 'description' => 'Medium tile widget to display a trip in a medium tile' )
    );
  }
  public function form($instance) {
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script('ovr_medium_trip_feature_admin_js', plugin_dir_url( __FILE__ ) . 'ovr-medium-trip-feature-admin.js', array('jquery') );
    $widgetTitleID = $this->get_field_id('widgetTitle');
    $widgetTitleName = $this->get_field_name('widgetTitle');
    $widgetTitle = $instance['widgetTitle'];
    $widgetExcerptID = $this->get_field_id('widgetExcerpt');
    $widgetExcerptName = $this->get_field_name('widgetExcerpt');
    $widgetExcerpt = $instance['widgetExcerpt'];
    $widgetImageID = $this->get_field_id('widgetImage');
    $widgetImageName = $this->get_field_name('widgetImage');
    $widgetImage = $instance['widgetImage'];
    $tripID = $this->get_field_id('trip');
    $tripName = $this->get_field_name('trip');
    $tripOptions = $this->get_trips_options($instance['trip']);
    $tripImageID = $this->get_field_id('tripImage');
    $tripImageName = $this->get_field_name('tripImage');
    $tripImage = $instance['tripImage'];

    echo <<<ADMINFORM
    <p>
      <label for="{$widgetTitleID}">Widget title: 30 characters</label>
      <input maxlength="30" id="{$widgetTitleID}" name="{$widgetTitleName}" type="text" value="{$widgetTitle}">
    </p>
    <p>
      <label for="{$widgetExcerptID}">Widget excerpt: 126 characters</label>
      <textarea style="width:100%" maxlength="126" id="{$widgetExcerptID}" name="{$widgetExcerptName}">{$widgetExcerpt}</textarea>
    </p>
    <p>
      <label for="{$widgetImage}">Widget Main Image: 460x200px</label>
      <input class="widefat" id="{$widgetImageID}" name="{$widgetImageName}" type="text" value="{$widgetImage}" />
      <button class="medium_trip_upload_image_button button button-primary">Upload Widget Image</button>
    </p>
    <p>
      <label for="{$tripID}">Select Trip: </label>
      <select id="{$tripID}" name="{$tripName}" style="width:100%">
        {$tripOptions}
      </select>
    </p>
    <p>
      <label for="{$tripImageID}">Trip Image: 150x50px </label>
      <input class="widefat" id="{$tripImageID}" name="{$tripImageName}" type="text" value="{$tripImage}" />
      <button class="medium_trip_upload_image_button button button-primary">Upload Trip One Image</button>
    </p>
ADMINFORM;
  }
  public function update( $new_instance, $old_instance ) {
    global $wpdb;

    $instance = '';
    $instance['widgetTitle'] = ( ! empty( $new_instance['widgetTitle'] ) ) ? strip_tags( $new_instance['widgetTitle'] ) : '';
    $instance['widgetExcerpt'] = ( ! empty( $new_instance['widgetExcerpt'] ) ) ? strip_tags( $new_instance['widgetExcerpt'] ) : '';
    $instance['widgetImage'] = ( ! empty( $new_instance['widgetImage'] ) ) ? strip_tags( $new_instance['widgetImage'] ) : '';
    $instance['trip'] = ( ! empty( $new_instance['trip'] ) ) ? strip_tags( $new_instance['trip'] ) : '';
    $instance['tripImage'] = ( ! empty( $new_instance['tripImage'] ) ) ? strip_tags( $new_instance['tripImage'] ) : '';


    if ( "" !== $instance['trip'] ) {
      $instance['tripTitle'] = get_the_title($instance['trip']);

      // Remove Dates and Days of the week from title
      $tempTitle = preg_replace('/[JMSNFAODTW][aueopchr][nrylpvbgtceudi][\srst.][\ss.].*/','', $instance['tripTitle']);
      $tempTitle = ( "" !== $tempTitle ? $tempTitle : $instance['tripTitle']);

      $instance['tripTitle'] = $tempTitle;

      $instance['tripDate'] = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['trip']}' AND `meta_key`='_wc_trip_start_date'");
      $endDate = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['trip']}' AND `meta_key`='_wc_trip_end_date'");
      if ( strtotime($instance['tripDate']) != strtotime($endDate) ) {
        $instance['tripDate'] = date('m/d/y', strtotime($instance['tripDate'])) . " - " . date('m/d/y', strtotime($endDate));
      }
      $instance['tripLink'] = get_the_permalink($instance['trip']);
    }

    return $instance;
  }
  public function widget( $args, $instance ) {
    wp_enqueue_style('ovr-medium-trip-feature-widget', plugin_dir_url( __FILE__ ) . 'ovr-medium-trip-feature-widget.css');
    echo <<<FRONTEND
    <div class="ovr_medium_trip_feature" data-link="{$instance['link']}">
      <div class="ovr_medium_trip_feature_inner">
        <div class="ovr_medium_trip_feature_content">
          <h4 class="ovr_medium_trip_feature_title">{$instance['widgetTitle']}</h4>
          <img src="{$instance['widgetImage']}">
          <p>
            {$instance['widgetExcerpt']}
          </p>
          <div class="ovr_medium_trip_feature_trip">
            <span class="ovr_medium_trip_feature_trip_title">{$instance['tripTitle']}</span>
            <span class="ovr_medium_trip_feature_trip_date">{$instance['tripDate']}</span>
            <img src="{$instance['tripImage']}">
          </div>
        </div>
      </div>
    </div>
FRONTEND;
  }
  private function get_trips_options( $selectedTrip ) {
    global $wpdb;
    // Get All Trip Products
    $trips = $wpdb->get_results("SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`
    FROM `wp_posts`
    JOIN `wp_term_relationships` ON `wp_posts`.`ID` = `wp_term_relationships`.`object_id`
    JOIN `wp_term_taxonomy` ON `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id`
    JOIN `wp_terms` ON `wp_term_taxonomy`.`term_id` = `wp_terms`.`term_id`
    WHERE `wp_posts`.`post_status` = 'publish'
    AND `wp_posts`.`post_type`='product'
    AND `wp_term_taxonomy`.`taxonomy` = 'product_type'
    AND `wp_terms`.`name` = 'trip'
    ", OBJECT_K);
    // Attach trip date
    foreach( $trips as $id => $data) {
      $meta_query = $wpdb->prepare(
        "SELECT `post_id`,
        MAX(CASE WHEN `wp_postmeta`.`meta_key` = '_wc_trip_start_date' THEN `wp_postmeta`.`meta_value` END) as 'date'
        FROM `wp_postmeta`
        WHERE `post_id` = '%d'", intval($id)
      );
      $meta = $wpdb->get_results($meta_query, OBJECT_K);
      $trips[$id]->date = $meta[$id]->date;
    }
    // Sort trips by trip date
    usort($trips, function($a, $b){
      $aTime = strtotime($a->date);
      $bTime = strtotime($b->date);
      if( $aTime > $bTime) {
        return 1;
      } else if ( $aTime < $bTime ) {
        return -1;
      } else if ( $aTime === $bTime) {
        // Secondary Sort by title
        $titleCompare = strcmp($a->post_title,$b->post_title);
        if ( $titleCompare > 0) {
          return 1;
        } else if ( $titleCompare < 0 ) {
          return -1;
        } else {
          return 0;
        }
      } else {
        return 0;
      }
    });
    $options = "<option>Select a trip</option>";
    foreach( $trips as $id => $data ) {
      if ( $data->ID == $selectedTrip ) {
        $selected = "selected ";
      } else {
        $selected = "";
      }
      $options .= "<option value='{$data->ID}' {$selected}>{$data->post_title}</option>";
    }
    return $options;
  }
}
function ovr_medium_trip_feature_load_widget() {
  register_widget( 'ovr_medium_trip_feature_widget' );
}
add_action( 'widgets_init', 'ovr_medium_trip_feature_load_widget' );
