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
  public function widget( $args, $instance ) {
    echo "Jumbotron!";
  }
}
function ovr_jumbotron_load_widget() {
	register_widget( 'ovr_jumbotron_widget' );
}
add_action( 'widgets_init', 'ovr_jumbotron_load_widget' );
