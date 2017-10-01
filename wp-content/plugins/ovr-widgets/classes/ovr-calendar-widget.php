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
  }
  public function form( $instance ) {

  }
  public function generate_calendar_ajax() {

    if ( ! wp_verify_nonce( $_REQUEST['ovr_calendar_shift'], 'ovr_calendar' ) ) {
      error_log("Failed Nonce:");
      error_log($_REQUEST['ovr_calendar_shift']);
      die('OvR Calendar Ajax nonce failed');
    }


    // Create php date object with correct timezone for calendar generation
    $date = new DateTime($_POST['calendarDate'], new DateTimeZone('EST'));

    wp_send_json( array("html" => $this->generate_calendar($date), "month_year" => $date->format('F Y') ) );
  }
  public function generate_calendar( $date ) {
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
    $raw_trips = $wpdb->get_results("SELECT `wp_posts`.`ID`, `wp_posts`.`post_title`, `wp_posts`.`post_status`, STR_TO_DATE(`wp_postmeta`.`meta_value`, '%M %d, %Y') as `Date`, `wp_posts`.`guid`
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

    $search_date = $year . "-" . $month . "-";
    $trips = array();
    //error_log(serialize($raw_trips));
    foreach($raw_trips as $index => $current_trip) {
      $ID = $current_trip['ID'];
      $trip_date = $current_trip['Date'];
      $stripped_title = preg_replace("/(.*[^:]):*\s[ADFJMNOS][aceopu][bcglnprtvy].\s[0-9\-]{1,5}[snrtdh]{1,2}/", "$1", $current_trip['post_title']);
      $stripped_title = preg_replace("/[-\s]{1,2}[0-9][0-9][tsr][hnd]/", "", $stripped_title); // edge case for weird date formatting
      $stripped_title = preg_replace("/[MTWFS][ouehra][neduitn][\.]/", "", $stripped_title);// edge case for weird day of week
      $stripped_title = preg_replace("/Thur\.-[0-9]{0,1}[0-9][tsr][htd]/", "", $stripped_title);//ugh, we need to fix titles
      if ( 'publish' === $current_trip['post_status'] ) {
        $current_trip_link = '<a href=\'' . $current_trip['guid'] . '\'>';
      } else {
        $current_trip_link = '<a class=\'calendar_past_trip\'>';
      }
      $current_trip_link .= $stripped_title .'</a>';
      $trip_destination = get_post_meta( $ID, '_wc_trip_destination', true);

      $trip_type = $wpdb->get_results("SELECT `meta_value` as 'type' FROM `wp_postmeta`
      JOIN wp_posts ON wp_postmeta.post_id = wp_posts.ID
      WHERE `post_title` = '{$trip_destination}'
      AND `post_type` = 'destinations'
      AND `meta_key` = '_type'");
      if ( !isset($trip_type[0]->trip) || '' == $trip_type[0]->trip ) {
        $trip_type[0]->trip = 'winter';
      }
      $trips[$trip_date][] = array("link" => $current_trip_link, "type" => $trip_type[0]->type);
      $end = $wpdb->get_var("select STR_TO_DATE(`meta_value`, '%M %d, %Y') as `End` FROM wp_postmeta where post_id='{$ID}' and meta_key='_wc_trip_end_date'");
      // Exit current loop iteration if trip_date is trip end date
      if ( $trip_date === $end ) {
        continue;
      }

      $end_month = substr($end, 5, 2);
      $trip_month = substr($trip_date, 5, 2);

      if ( $trip_month < $end_month ) {
        $end = $year . "-" . $month . "-". $lastDay;
      }
      error_log("End Month: $end_month");
      // Add check to see if end is in this month, if it isn't then make the $end be the last day of the month
      $trip_date++;
      // Loop until we find end of trip and add trip data to array on those days
      for($i=$trip_date; $i <= $end; $i++) {
        $trips[$i][] = array("link" => $current_trip_link, "type" => $trip_type[0]->type);
      }
    }

    // loop through month and assemble
    $end_week_offset = $date->format('w');
    $date->modify('first day of this month');
    $start_week_offset = $date->format('w');
    $days = '';

    // All calendars will have space for 6 weeks
    for($i = 1; $i <= 42; $i++ ) {
      $adjustedDay = $i - $start_week_offset;
      // Popover datafield
      $data = '';
      if ( $i <= $start_week_offset || $adjustedDay > $lastDay) {
        // Pad beginning and end of month with empty squares
        $add = '&nbsp;';
      } else if ( $i > $start_week_offset ) {
        // Add number to day
        $add = $i - $start_week_offset;
        $icon = false;
        $calendarDate = $date->format('Y-m-') . str_pad($adjustedDay, 2 , "0", STR_PAD_LEFT);
        // If the current calendar date exists in the trips array add the trip info

        if ( isset($trips[$calendarDate])) {
          $same_type = TRUE;
          unset($previous_type);
          foreach( $trips[$calendarDate] as $trip_date => $array ) {
            $data .= $array['link'] . "<br />";
            if ( !isset($previous_type) ) {
              $previous_type = $array['type'];
            }
            if ( $array['type'] !== $previous_type && $same_type) {
              $same_type = FALSE;
            }
          }

          if ( ! $same_type ) {
            $icon = "fa-circle";
            if ( strpos($data, 'href') !== FALSE) {
              $icon .= " winter";
            } else {
              $icon .= " past";
            }
          } else if ( "winter" === $previous_type || "summer_snow" === $previous_type ) {
            $icon = "fa-snowflake-o";
            if ( strpos($data, 'href') !== FALSE) {
              $icon .= " winter";
            } else {
              $icon .= " past";
            }
          } else if ( "summer" === $previous_type ) {
            $icon = "fa-sun-o";
            if ( strpos($data, 'href') !== FALSE) {
              $icon .= " summer";
            } else {
              $icon .= " past";
            }
          }

          $data = 'data-placement="auto-bottom" data-content="' . htmlentities($data) .'"';
          $add .= '<i class="fa icon ' . $icon . ' "';
          $add .= $data . ' aria-hidden="true"></i>';
        }

      }
      // Should the current date be highlighted on this calendar?
      if ( $activate && $adjustedDay == $day) {
        $days .= '<li class="active">';
      } else {
        $days .= '<li>';
      }
      $days .= $add . '</li>';
    }

    return $days;
  }
  public function widget( $args, $instance ) {

    $days = $this->generate_calendar(new DateTime('now'));
    $date = new DateTime('now');
    $month_year = $date->format('F Y');
    wp_enqueue_style('jquery.webui-popover-style', plugin_dir_url( dirname(__FILE__) ) . 'css/jquery.webui-popover.min.css');
    wp_enqueue_script( 'jquery.webui-popover-js', plugin_dir_url( dirname(__FILE__) ) . 'js/jquery.webui-popover.min.js', array('jquery'), false, true);
    wp_enqueue_script( 'jquery_spin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/jquery.spin.js', array('jquery','spin_js'), false, true);
    wp_enqueue_script( 'spin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/spin.min.js');
    wp_enqueue_script( 'ovr_calendar_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-calendar-widget.min.js', array('jquery.webui-popover-js', 'jquery_spin_js'), "1.1.0", true);
    wp_enqueue_style('ovr_calendar_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-calendar-widget.min.css', FALSE, "1.2");

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
            <ul class="weekdays">
              <li>S</li>
              <li>M</li>
              <li>T</li>
              <li>W</li>
              <li>T</li>
              <li>F</li>
              <li>S</li>
            </ul>

            <ul class="days">
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
