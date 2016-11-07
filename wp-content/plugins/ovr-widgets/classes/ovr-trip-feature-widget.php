<?php
/*
* Plugin Name: OvRride Trip Feature Widget
* Description:  Widget to display a trip product in a small tile
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/

class ovr_trip_feature_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_trip_feature',

    // Widget name will appear in UI
    'OvR Trip Feature',

    // Widget description
    array( 'description' => 'Small tile widget to display a single trip' )
    );
  }
  public function form($instance) {
    // Widget Admin Form
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script('ovr_trip_feature_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-trip-feature-admin.min.js', array('jquery') );

    $options              = $this->get_trips_options($instance['trip']);
    $primaryImage         = esc_url(! empty( $instance['primaryImage'] ) ? $instance['primaryImage'] : '');
    $secondaryImage       = esc_url(! empty( $instance['secondaryImage'] ) ? $instance['secondaryImage'] : '');
    $tripID               = $this->get_field_id('trip');
    $primaryImageID       = $this->get_field_id( 'primaryImage' );
    $secondaryImageID     = $this->get_field_id( 'secondaryImage' );
    $tripFieldName        = $this->get_field_name('trip');
    $primaryImageName     = $this->get_field_name('primaryImage');
    $secondaryImageName   = $this->get_field_name('secondaryImage');
    $tripLabel            = 'Selected trip: ';
    $primaryImageLabel    = "Primary Image: 225x150px ";
    $secondaryImageLabel  = "Secondary Image: 150x50px ";

    echo <<<ADMINFORM
    <p>
      <label for="{$tripID}">{$tripLabel}</label>
      <select id="{$tripID}" name="{$tripFieldName}" style="width:100%">
        {$options}
      </select>
    </p>
    <p>
      <label for="{$primaryImageID}">{$primaryImageLabel}</label>
      <input class="widefat" id="{$primaryImageID}" name="{$primaryImageName}" type="text" value="{$primaryImage}" />
      <button class="trip_upload_image_button button button-primary">Upload Primary Image</button>
    </p>
    <p>
      <label for="{$secondaryImageID}">{$secondaryImageLabel}</label>
      <input class="widefat" id="{$secondaryImageID}" name="{$secondaryImageName}" type="text" value="{$secondaryImage}" />
      <button class="trip_upload_image_button button button-primary">Upload Secondary Image</button>
    </p>
ADMINFORM;
  }
  public function update( $new_instance, $old_instance ) {
    global $wpdb;

    $instance = '';
    $instance['trip'] = ( ! empty( $new_instance['trip'] ) ) ? strip_tags( $new_instance['trip'] ) : '';
    $instance['primaryImage'] = ( ! empty( $new_instance['primaryImage'] ) ) ? $new_instance['primaryImage'] : '';
    $instance['secondaryImage'] = ( ! empty( $new_instance['secondaryImage'] ) ) ? $new_instance['secondaryImage'] : '';
    if ( "" !== $instance['trip'] ) {
      $instance['title'] = get_the_title($instance['trip']);

      // Remove Dates from title
      $instance['title'] = preg_replace('/[JFMASOND][aepuco][nbrylgptvc][.eyt].*/','', $instance['title']);
      // Remove any leftover day abbreviations in titile
      $instance['title'] = preg_replace('/[STMWF][uhaoer][neutdi][rs.][.$]/','',trim($instance['title']));

      $instance['date'] = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['trip']}' AND `meta_key`='_wc_trip_start_date'");

      $instance['link'] = get_the_permalink($instance['trip']);
    } else {
      $instance['title'] = '';
      $instance['date'] = '';
      $instance['link'] = '';
    }

    return $instance;
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
  public function widget( $args, $instance ) {
    wp_enqueue_style('ovr_trip_feature_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-trip-feature-widget.min.css');
    wp_enqueue_script('ovr_trip_feature_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-trip-feature-widget.min.js', array('jquery'), false, true );

    echo <<<FRONTEND
      <div class="ovr_trip_feature" data-link="{$instance['link']}">
        <div class="ovr_trip_feature_inner">
          <div class="ovr_trip_feature_content">
            <img class="ovr_trip_feature_primary_image" src="{$instance['primaryImage']}">
            <h5 class="ovr_trip_feature_header">destination</h5>
            <span class="ovr_trip_feature_title" maxlength="25">
              <a href="{$instance['link']}">{$instance['title']}</a>
            </span>
            <p class="ovr_trip_feature_date">{$instance['date']}</p>
            <img class="ovr_trip_feature_secondary_image" src={$instance['secondaryImage']}>
          </div>
        </div>
      </div>
FRONTEND;
  }
}
