<?php
class ovr_calendar_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_calendar',

    // Widget name will appear in UI
    'OvR Calendar',

    // Widget description
    array( 'description' => 'Calendar for small tile display or full page' )
  );
    add_action( 'wp_ajax_nopriv_ovr_calendar', array( $this, "generate_calendar_ajax") );
    add_action( 'wp_ajax_ovr_calendar', array( $this, "generate_calendar_ajax") );
    add_action( 'init', array( $this, 'register_archive') );
    add_action( 'save_post', array( $this, "product_refresh") );
    add_action( 'ovr_calendar_refresh', array( $this, "refresh") );

    // Make sure calendar refreshes every day
    if ( ! wp_next_scheduled( 'ovr_calendar_refresh' ) ) {
      $refreshTime = new DateTime('now', new DateTimeZone('America/New_York'));
      $refreshTime->modify('Tomorrow 12:01am');
      wp_schedule_single_event( $refreshTime->format('U'), 'ovr_calendar_refresh' );
    }
  }

  public function form( $instance ) {
    $checkboxID = $this->get_field_id('mini');
    $checkboxName = $this->get_field_name('mini');
    $checkbox = $instance['mini'];
    echo <<<CALENDARFORM
    <p>
    <label>Mini calendar:<input type="checkbox" name="{$checkboxName}" id="{$checkboxID}" {$checkbox} value="checked"></label>
    </p>
CALENDARFORM;
  }
  public function update( $new_instance, $old_instance) {
    $instance['mini'] = ( ! empty( $new_instance['mini'] ) ) ? strip_tags( $new_instance['mini'] ) : '';
    return $instance;
  }
  public function generate_calendar_ajax() {

    if ( ! wp_verify_nonce( $_REQUEST['ovr_calendar_shift'], 'ovr_calendar' ) ) {
      error_log("Failed Nonce:");
      error_log($_REQUEST['ovr_calendar_shift']);
      die('OvR Calendar Ajax nonce failed');
    }


    // Create php date object with correct timezone for calendar generation
    $date = new DateTime($_POST['calendarDate'], new DateTimeZone('EST'));

    wp_send_json( array("html" => $this->generate_calendar($date, false), "month_year" => $date->format('F Y') ) );
  }
  public function refresh() {
    $date = new DateTime('now');
    $this->generate_calendar( new DateTime('now'), true );
  }
  public function product_refresh( $post_id ) {
    if ( get_post_type($post_id) !== 'product' ) {
      return;
    } else {
      $date = new DateTime('now');
      $this->generate_calendar( new DateTime('now'), true );
      return;
    }
  }

  function wpdb_array_shift($data) {
    global $wpdb;

    $temp_array = array();
    foreach($data as $index => $array){
      // remove dates from titles
      $stripped_title = preg_replace("/(.*[^:]):*\s[ADFJMNOS][aceopu][bcglnprtvy].\s[0-9\-]{1,5}[snrtdh]{1,2}/", "$1", $array['post_title']);
      $stripped_title = preg_replace("/[-\s]{1,2}[0-9][0-9][tsr][hnd]/", "", $stripped_title); // edge case for weird date formatting
      $stripped_title = preg_replace("/[MTWFS][ouehra][neduitn][\.]/", "", $stripped_title);// edge case for weird day of week
      $stripped_title = preg_replace("/Thur\.-[0-9]{0,1}[0-9][tsr][htd]/", "", $stripped_title);//ugh, we need to fix titles
      $array['post_title'] = $stripped_title;
      $destination = get_post_meta( $array['ID'], '_wc_trip_destination', true);
      $type = $wpdb->get_results("SELECT `meta_value` as 'type' FROM `wp_postmeta`
        JOIN wp_posts ON wp_postmeta.post_id = wp_posts.ID
        WHERE `post_title` = '{$destination}'
        AND `post_type` = 'destinations'
        AND `meta_key` = '_type'", ARRAY_A);

      if ( count($type) == 0 ) {
        $array['season'] = "winter";
      } else {
        $array['season'] = $type[0]['type'];
      }
      $array['name'] = $array['post_title'];
      unset($array['post_title']);
      $array['url'] = $array['guid'];
      unset($array['guid']);
      //assign to new array and remove ID field
      $temp_array[$array['ID']] = $array;
      unset($temp_array[$array['ID']]['ID']);
    }

    return $temp_array;
  }
  function key_compare_func($key1, $key2){
    if ($key1 == $key2)
        return 0;
    else if ($key1 > $key2)
        return 1;
    else
        return -1;
  }
  public function generate_calendar( $date, $refresh ) {
    global $wpdb;
    $date->setTimezone(new DateTimeZone('America/New_York'));
    $currentDay = new DateTime('now');
    if ( $currentDay->format('m') == $date->format('m') ) {
      $date = new DateTime('now', new DateTimeZone('America/New_York'));
    }
    if ( $date == $currentDay) {
      $activate = true;
    } else {
      $activate = false;
    }
    $month = $date->format('m');
    $day = $date->format('d');
    $year = $date->format('Y');
    $sqlDate = $date->format('F %, Y');

    $date->modify('last day of this month');
    $lastDay = $date->format('d');
    // Find trips happening this month
    $start_trips = $wpdb->get_results("SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`, `wp_posts`.`post_status`, STR_TO_DATE(`wp_postmeta`.`meta_value`, '%M %d, %Y') as `Date`, `wp_posts`.`guid`
    FROM `wp_posts`
    JOIN `wp_postmeta` ON `wp_posts`.`ID` = `wp_postmeta`.`post_id`
    JOIN `wp_term_relationships` ON `wp_posts`.`ID` = `wp_term_relationships`.`object_id`
    JOIN `wp_term_taxonomy` ON `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id`
    JOIN `wp_terms` ON `wp_term_taxonomy`.`term_id` = `wp_terms`.`term_id`
    WHERE (`wp_posts`.`post_status` = 'publish'
      OR `wp_posts`.`post_status` = 'archive')
    AND `wp_posts`.`post_type`='product'
    AND `wp_term_taxonomy`.`taxonomy` = 'product_type'
    AND `wp_terms`.`name` = 'trip'
    AND `wp_postmeta`.`meta_key` = '_wc_trip_start_date'
    AND `wp_postmeta`.`meta_value` LIKE '{$sqlDate}'
    ORDER BY `Date`", ARRAY_A);

    $end_trips = $wpdb->get_results("SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`, `wp_posts`.`post_status`, STR_TO_DATE(`wp_postmeta`.`meta_value`, '%M %d, %Y') as `Date`, `wp_posts`.`guid`
    FROM `wp_posts`
    JOIN `wp_postmeta` ON `wp_posts`.`ID` = `wp_postmeta`.`post_id`
    JOIN `wp_term_relationships` ON `wp_posts`.`ID` = `wp_term_relationships`.`object_id`
    JOIN `wp_term_taxonomy` ON `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id`
    JOIN `wp_terms` ON `wp_term_taxonomy`.`term_id` = `wp_terms`.`term_id`
    WHERE (`wp_posts`.`post_status` = 'publish'
      OR `wp_posts`.`post_status` = 'archive')
    AND `wp_posts`.`post_type`='product'
    AND `wp_term_taxonomy`.`taxonomy` = 'product_type'
    AND `wp_terms`.`name` = 'trip'
    AND `wp_postmeta`.`meta_key` = '_wc_trip_end_date'
    AND `wp_postmeta`.`meta_value` LIKE '{$sqlDate}'
    ORDER BY `Date`", ARRAY_A);


    $start_trips = $this->wpdb_array_shift($start_trips);
    $end_trips = $this->wpdb_array_shift($end_trips);
    $trips = [];
    // Trips that end in this month
    foreach($end_trips as $id => $trip_data) {
      $product = new WC_Product($id);
      if ( "visible" !== $product->get_catalog_visibility() ) {
        continue;
      }
      if ( isset($start_trips[$id]) ) {
        if( $start_trips[$id]['Date'] == $end_trips[$id]['Date'] ) {
          unset($trip_data['Date']);
        }
        $trips[$start_trips[$id]['Date']][] = $trip_data;
        unset($start_trips[$id]);
      } else {
        $trips[1][] = $trip_data;
      }
    }
    // Trips that do not end in this month
    foreach($start_trips as $id => $trip_data) {
      $product = new WC_Product($id);
      if ( "visible" !== $product->get_catalog_visibility() ) {
        continue;
      }
      $start_date = $trip_data['Date'];
      $trip_data['Date'] = $year."-".$month."-".$lastDay;
      $trips[$start_date][] = $trip_data;
    }
    // Add custom events
    $custom_events = maybe_unserialize( get_option("ovr_custom_events", array() ) );
    foreach( $custom_events as $index => $event_data ) {
      // Only process active events
      if ( $event_data["active"] == 1 ) {
        // Setup DateTime Objects for event dates and month start/end
        $event_start_date = new DateTime($event_data["start"]);
        $event_end_date = new DateTime($event_data["end"]);
        $start_month_date = new DateTime($year."-".$month."-01");
        $end_month_date = new DateTime($year."-".$month."-".$lastDay);
        unset($event_data["active"]);
        if ( $event_data["start"] == $event_data["end"] &&
        $event_end_date->format('m') == $month) {
          unset($event_data["end"]);
          $event_start = $event_data["start"];
          unset($event_data["start"]);
          $trips[$event_start][] = $event_data;
        } else {
          if ( $event_end_date->format('m') == $month || $event_start_date->format('m') == $month ){
            if ( $event_start_date <= $start_month_date && $event_end_date->format('m') == $month){
              $event_start_date = $start_month_date->format('Y-m-d');
              if ( $event_start_date !== $event_data["end"] ) {
                $event_data["Date"] = $event_data["end"];
              }
            } else if ( $event_start_date <= $end_month_date && $event_start_date->format('m') == $month) {
              $event_end_date = $end_month_date->format('Y-m-d');
              $event_start_date = $event_start_date->format('Y-m-d');
              $event_data["Date"] = $event_end_date;
            }

            unset($event_data["start"]);
            unset($event_data["end"]);
            if ( "string" == gettype($event_start_date) ) {
              $trips[$event_start_date][] = $event_data;
            }
          } else if ( $event_start_date < $start_month_date && $event_end_date > $end_month_date ) {
            unset($event_data["start"]);
            unset($event_data["end"]);
            $event_data["Date"] = $end_month_date->format('Y-m-d');
            $trips[$start_month_date->format('Y-m-d')][] = $event_data;
          }

        }

      }
    }
    ksort($trips);

    // Expand trips to fill month
    $calendar = array();
    foreach( $trips as $start_of_trip => $trip_info ) {
      foreach($trip_info as $index => $trip ) {
        if ( isset($trip["Date"]) ) {
          $stop_date = $trip["Date"];
          unset($trip["Date"]);
          for($i=$start_of_trip++; $i <= $stop_date; $i++ ) {
            $calendar[$i][] = $trip;
          }
        } else {
          $calendar[$start_of_trip][] = $trip;
        }
      }
    }
    $calendar_check = $year."-".$month."-01";
    $calendar_check_end = $year ."-".$month."-".$lastDay;
    for($i=$calendar_check;$i<=$calendar_check_end;$i++) {
      if ( !isset($calendar[$i]) ) {
        $calendar[$i] = [];
      } else {
        usort($calendar[$i], function($a, $b) {
          return strcmp($a["name"],$b["name"]);
        });
      }
    }

    // Assemble calendar html
    // $date is already set to end of month
    $date->modify('first day of this month');
    $start_week_offset = $date->format('w');
    $days = '';
    $adjusted_end_of_month = 42 - $start_week_offset;
    // Calendar has room for six weeks
    for($i = -1 * abs($start_week_offset) + 1; $i <= $adjusted_end_of_month; $i++) {
      if ( $i <= 0 || $i > $lastDay) {
        $days .= "<li class='calendarInactive'>&nbsp;<span class='no-mini'><br />&nbsp;</span></li>";
      } else if ( $i > 0 && $i <= $lastDay ) {
        $calendar_key = $year ."-".$month."-".str_pad($i, 2, 0, STR_PAD_LEFT);
        $day_class = '';
        $day_content = '';
        if ( isset($calendar[$calendar_key]) && count($calendar[$calendar_key]) > 0 ) {
          foreach( $calendar[$calendar_key] as $index => $event) {
            if ( strpos( $day_class, "calendarEvent") === FALSE ) {
              $day_class .= "calendarEvent ";
            }
            if ( strpos( $day_class, $event["season"]) === FALSE ) {
                $day_class .= $event["season"] . " ";
            }
            $event["name"] = str_replace("\\","", htmlspecialchars($event["name"], ENT_QUOTES) );
            if ( $calendar_key < $currentDay->format('Y-m-d') ) {
                if ( strpos( $day_class, "past") === FALSE ) {
                  $day_class .= "past ";
                }
                $day_content .="<a>{$event["name"]}</a><br />";
            } else {
              $day_content .="<a href='{$event["url"]}'>{$event["name"]}</a><br />";
            }
          }
        }
        if ( $calendar_key == $currentDay->format('Y-m-d') ) {
          $day_class .= "active ";
        }
        $day_content = htmlentities( substr( $day_content, 0, -6 ) );
        $day_class = substr($day_class, 0, -1);
        if ( '' !== $day_content) {
          $icon = '<i class="fa fa-circle" aria-hidden="true"></i>';
        } else {
          $icon = "&nbsp;";
        }
        $days .=<<<DAYTEMPLATE
        <li class="{$day_class}" data-placement="auto-bottom" data-content="{$day_content}" aria-hidden="true">
        {$i}<span class="no-mini"><br />
        {$icon}</span>
        </li>
DAYTEMPLATE;
      }
    }
    if ( $refresh ) {
      update_option("ovr_calendar_days_data", $days);
    }
    return $days;
  }
  public function widget( $args, $instance ) {

    $days = get_option("ovr_calendar_days_data");
    if( !$days ) {
      $days = $this->generate_calendar(new DateTime('now'), true);
    }
    $date = new DateTime('now');
    $month_year = $date->format('F Y');
    wp_enqueue_style('jquery.webui-popover-style', plugin_dir_url( dirname(__FILE__) ) . 'css/jquery.webui-popover.min.css');
    wp_enqueue_script( 'jquery.webui-popover-js', plugin_dir_url( dirname(__FILE__) ) . 'js/jquery.webui-popover.min.js', array('jquery'), false, true);
    wp_enqueue_script( 'jquery_spin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/jquery.spin.js', array('jquery','spin_js'), false, true);
    wp_enqueue_script( 'spin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/spin.min.js');
    wp_enqueue_script( 'ovr_calendar_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-calendar-widget.min.js', array('jquery.webui-popover-js', 'jquery_spin_js', 'jquery'), "1.2.0", true);
    if ( $instance["mini"] == "checked" ) {
      wp_enqueue_style('ovr_calendar_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-calendar-widget-mini.min.css', FALSE, "1.5.1");
    } else {
      wp_enqueue_style('ovr_calendar_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-calendar-widget.min.css', FALSE, "1.5.1");
    }

    if ( is_ssl() ) {
        $nonced_url = wp_nonce_url( admin_url( 'admin-ajax.php', 'https'), 'ovr_calendar', 'ovr_calendar_shift' );
    } else {
      $nonced_url = wp_nonce_url( admin_url( 'admin-ajax.php', 'http'), 'ovr_calendar', 'ovr_calendar_shift' );
    }

    wp_localize_script('ovr_calendar_js', 'ovr_calendar_vars', array( 'ajax_url' => $nonced_url ) );

    echo <<<FRONTEND
    <div class="ovr_calendar_widget">
      <div class="ovr_calendar_widget_inner">
        <div class="ovr_calendar_widget_content">
          <div class="ovr_calendar">
            <div class="month">
              <ul>
                <li class="prev"><i class="fa fa-arrow-left fa-lg" aria-hidden="true"></i></li>
                <li class="next"><i class="fa fa-arrow-right fa-lg" aria-hidden="true"></i></li>
                <li>
                  <h4 class="month_year">{$month_year}</h4>
                </li>
              </ul>
            </div>
            <ul class="weekdays clearfix">
              <li>S</li>
              <li>M</li>
              <li>T</li>
              <li>W</li>
              <li>T</li>
              <li>F</li>
              <li>S</li>
            </ul>

            <ul class="days clearfix">
            {$days}
            </ul>
          </div>
        </div>
      </div>
    </div>
FRONTEND;
  }
  function register_archive() {
    register_post_status( 'archive', array(
            'label'                       => __( 'Archive', 'wp-statuses' ),
            'label_count'                 => _n_noop( 'Archived <span class="count">(%s)</span>', 'Archived <span class="count">(%s)</span>', 'wp-statuses' ),
            'public'                      => false,
            'show_in_admin_all_list'      => false,
            'show_in_admin_status_list'   => true,
            'post_type'                   => array( 'product' ),
            'show_in_metabox_dropdown'    => true,
            'show_in_inline_dropdown'     => true,
            'show_in_press_this_dropdown' => true,
            'labels'                      => array(
                'metabox_dropdown' => __( 'Archived',        'wp-statuses' ),
                'inline_dropdown'  => __( 'Archived',        'wp-statuses' ),
            ),
            'dashicon'                    => 'dashicons-archive',
        ) );
  }
}
