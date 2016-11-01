<?php
/*
* Plugin Name: OvRride Events Widget
* Description: Widget to display upcoming events
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/

class ovr_events_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_events',

    // Widget name will appear in UI
    __('OvR Upcoming Events', 'ovr_events'),

    // Widget description
    array( 'description' => __( 'Widget to display upcoming events', 'ovr_events_domain' ), )
    );
  }
public function form($instance) {
  // Widget Admin Form
  $eventsID = $this->get_field_id( 'events' );
  $menu_orderID = $this->get_field_id( 'menu_order' );
  $eventsLabel = 'Number of Events to list(1-30):';
  $menu_orderLabel = 'Override Date Sorting with menu order field on products:';
  $eventsFieldName = $this->get_field_name('events');
  $menu_orderFieldName = $this->get_field_name('menu_order');
  if ( isset($instance['events']) ) {
    $events = esc_attr($instance['events']);
  } else {
    $events = 10;
  }
  if ( isset($instance['menu_order']) ) {
    $menu_order = checked;
  } else {
    $menu_order = "";
  }
  echo <<<ADMINFORM
  <p>
  <label for="{$eventsID}">{$eventsLabel}</label>
  <input id="{$eventsID}" name="{$eventsFieldName}" type="number" min="1" max="30" value="{$events}" />
  </p>
  <p>
  <label for="{$menu_orderID}">{$menu_orderLabel}</label>
  <input id="{$menu_orderID}" name="{$menu_orderFieldName}" type="checkbox" value="true" {$menu_order}>
  </p>
ADMINFORM;
}
public function update( $new_instance, $old_instance ) {
  $instance = array();
  $instance['events'] = ( ! empty( $new_instance['events'] ) ) ? strip_tags( $new_instance['events'] ) : '';
  $instance['menu_order'] = ( ! empty( $new_instance['menu_order'] ) ) ? strip_tags( $new_instance['menu_order'] ) : '';
  return $instance;
}
public function widget( $args, $instance ) {
  $trip = $this->returnTrips($instance['events'], $instance['menu_order']);
  foreach($trip as $id => $data ) {
    echo $data->post_title ."<br />";
  }
}
function returnTrips($numberOfTrips, $menu_order){
  global $wpdb;
  // Get All Trip Products
  $trip = $wpdb->get_results("SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`, `wp_posts`.`guid`, `wp_posts`.`menu_order`
  FROM `wp_posts`
  JOIN `wp_term_relationships` ON `wp_posts`.`ID` = `wp_term_relationships`.`object_id`
  JOIN `wp_term_taxonomy` ON `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id`
  JOIN `wp_terms` ON `wp_term_taxonomy`.`term_id` = `wp_terms`.`term_id`
  WHERE `wp_posts`.`post_status` = 'publish'
  AND `wp_posts`.`post_type`='product'
  AND `wp_term_taxonomy`.`taxonomy` = 'product_type'
  AND `wp_terms`.`name` = 'trip'
  ", OBJECT_K);
  // Attach Meta data with dates and stock info
  foreach( $trip as $id => $data) {
    $meta_query = $wpdb->prepare(
      "SELECT `post_id`,
      MAX(CASE WHEN `wp_postmeta`.`meta_key` = '_stock' THEN `wp_postmeta`.`meta_value` END) as 'stock',
      MAX(CASE WHEN `wp_postmeta`.`meta_key` = '_stock_status' THEN `wp_postmeta`.`meta_value` END) as 'stock_status',
      MAX(CASE WHEN `wp_postmeta`.`meta_key` = '_wc_trip_start_date' THEN `wp_postmeta`.`meta_value` END) as 'date'
      FROM `wp_postmeta`
      WHERE `post_id` = '%d'", intval($id)
    );
    $meta = $wpdb->get_results($meta_query, OBJECT_K);
    unset($trip[$id]->ID);
    $trip[$id]->stock = $meta[$id]->stock;
    $trip[$id]->stock_status = $meta[$id]->stock_status;
    $trip[$id]->date = $meta[$id]->date;
  }
  // Sort trips by trip date
  usort($trip, function($a, $b){
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
  if ( (bool)$menu_order ){
  foreach($trip as $id => $data ) {
      if ( intval($data->menu_order) > 0 ){
        $out = array_splice($trip, $id, 1);
        array_splice($trip, (intval($data->menu_order) -1), 0, $out);
      }
    }
  }
  // Cut trips down to specified Number
  if ( count($trip) > $numberOfTrips ){
    $trip = array_splice($trip,0,$numberOfTrips, NULL);
  }
  return $trip;
}
}
function ovr_events_load_widget() {
	register_widget( 'ovr_events_widget' );
}
add_action( 'widgets_init', 'ovr_events_load_widget' );
