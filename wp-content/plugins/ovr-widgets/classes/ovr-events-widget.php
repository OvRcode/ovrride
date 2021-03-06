<?php
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
    add_action( "save_post", array( $this, "refresh") );
  }
public function refresh( $post_id ) {
  switch( get_post_type($post_id) ){
    case "shop_order":
    case "product":
      $widgets = maybe_unserialize(get_option($this->widget_options['classname']));
      $this->buildHTML($widgets[$this->number]);
      return;
    break;
    default:
      return;
  }
}
public function form($instance) {
  // Widget Admin Form
  $eventsID = $this->get_field_id( 'events' );
  $menuOrderID = $this->get_field_id( 'menu_order' );
  $seatCountID = $this->get_field_id( 'seat_count' );
  $eventsLabel = 'Number of events to list(1-30):';
  $menuOrderLabel = 'Override date dorting with menu order field on products:';
  $seatCountLabel = 'Number of seats left to initiate countdown (1-52):';
  $eventsFieldName = $this->get_field_name('events');
  $menuOrderFieldName = $this->get_field_name('menu_order');
  $seatCountFieldName = $this->get_field_name('seat_count');
  if ( isset($instance['events']) ) {
    $events = esc_attr($instance['events']);
  } else {
    $events = 10;
  }
  if ( isset($instance['menu_order']) ) {
    $menuOrder = "checked";
  } else {
    $menuOrder = "";
  }
  if ( isset($instance['seat_count']) ) {
    $seatCount = esc_attr($instance['seat_count']);
  } else {
    $seatCount = 30;
  }
  echo <<<ADMINFORM
  <p>
  <label for="{$eventsID}">{$eventsLabel}</label>
  <input id="{$eventsID}" name="{$eventsFieldName}" type="number" min="1" max="30" value="{$events}">
  </p>
  <p>
  <label for="{$menuOrderID}">{$menuOrderLabel}</label>
  <input id="{$menuOrderID}" name="{$menuOrderFieldName}" type="checkbox" value="true" {$menuOrder}>
  </p>
  <p>
  <label for="{$seatCountID}">{$seatCountLabel}</label>
  <input id="{$seatCount}" name="{$seatCountFieldName}" type="number" min="1" max="52" value="{$seatCount}">
  </p>
ADMINFORM;
}
public function update( $new_instance, $old_instance ) {
  $instance = array();
  $instance['events'] = ( ! empty( $new_instance['events'] ) ) ? strip_tags( $new_instance['events'] ) : '';
  $instance['menu_order'] = ( ! empty( $new_instance['menu_order'] ) ) ? strip_tags( $new_instance['menu_order'] ) : '';
  $instance['seat_count'] = ( ! empty( $new_instance['seat_count'] ) ) ? strip_tags( $new_instance['seat_count'] ) : '';
  update_option("ovr_events_widget_html", $this->buildHTML($instance) );
  return $instance;
}
public function widget( $args, $instance ) {
  wp_enqueue_style( 'ovr_event_widget_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-events-widget.min.css');
  wp_enqueue_script( 'ovr_event_widget_script', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-events-widget.min.js', array('jquery'), "1.0", true);
  $widget = get_option("ovr_event_widget_html");
  if( !$widget ) {
    $widget = $this->buildHTML($instance);
  }
  echo $widget;
}

function buildHTML($instance) {
  $html = "<div class='events'>";
  $html .= "<i class='leftArrow'></i>";
  $html .= "<div class='eventScroll'>";
  $trip = $this->returnTrips($instance['events'], $instance['menu_order']);
  foreach($trip as $id => $data ) {
    if ( $data->stock_management == "no" ) {
      $label ="<span class='available'>AVAILABLE</span>";
    } else if ( strcmp($data->stock_status, "outofstock") === 0 ) {
      $label = "<span class='soldOut'>SOLD OUT</span>";
    } else if ( $data->stock <= intval($instance['seat_count']) ) {
      $label = "<span class='spots'>" . $data->stock . " Spots Remaining</span>";
    } else {
      $label = "<span class='available'>AVAILABLE</span>";
    }
    $html .= <<<WIDGETHTML
    <div class="event">
      <a href="{$data->link}">{$data->post_title}</a><br />
      {$data->dateLabel}<br />
      {$label}
    </div>
WIDGETHTML;
  }
  $html .= "</div><i class='rightArrow'></i></div>";
  update_option( "ovr_events_widget_html", $html);
  return $html;
}
function returnTrips($numberOfTrips, $menu_order){
  global $wpdb;
  // Get All Trip Products
  $trip = $wpdb->get_results("SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`, `wp_posts`.`menu_order`
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
    $product = new WC_Product($id);
    unset($trip[$id]->ID);
    if ( "visible" !== $product->get_catalog_visibility() ) {
      unset($trip[$id]);
      continue;
    }
    $trip[$id]->stock_management = ( $product->get_manage_stock( 'view' ) ? 'yes' : 'no');
    $trip[$id]->stock = $product->get_stock_quantity( 'view' );
    $trip[$id]->stock_status = $product->get_stock_status( 'view' );
    $trip[$id]->date = $product->get_meta( '_wc_trip_start_date', true, 'view' );
    $trip[$id]->end_date = $product->get_meta( '_wc_trip_end_date', true, 'view' );
    $trip[$id]->link = get_permalink($id);

    if ( strtotime($trip[$id]->end_date) > strtotime($trip[$id]->date) ) {
      $trip[$id]->dateLabel = date('F jS - ', strtotime($trip[$id]->date)) . date('jS, Y', strtotime($trip[$id]->end_date));
    } else {
      $trip[$id]->dateLabel = date('F jS, Y', strtotime($trip[$id]->date));
    }
    preg_match("/.*:\s(Lady\sShred\sSession\s[1-9])/", $trip[$id]->post_title, $ladiesCheck);
    if ( $ladiesCheck ) {
      $trip[$id]->post_title = $ladiesCheck[1];
    } else {
      // Remove Dates from title
      $trip[$id]->post_title = preg_replace('/[JFMASOND][aepuco][nbrylgptvc][.eyt][^h].*/','',$trip[$id]->post_title);

      // Remove any leftover day abbreviations in titile
      $trip[$id]->post_title = preg_replace('/[STMWF][uhaoer][neutdi][rs.][.$]/','',trim($trip[$id]->post_title));

      // Remove any leftover brackets
      $trip[$id]->post_title =  str_replace( array("(",")"), "", $trip[$id]->post_title);
    }
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
