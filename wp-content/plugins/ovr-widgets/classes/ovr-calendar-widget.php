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
    add_action( 'wp_ajax_myajax_ovr_calendar', array( $this, "generate_calendar_ajax") );
  }
  public function form( $instance ) {

  }
  public function generate_calendar_ajax() {
    $nonce = $_POST['calendarNonce'];
    error_log("NONCE: " . $nonce);
    error_log(wp_verify_nonce( $nonce, 'ovr-calendar-nonce' ));


    $date = new DateTime($_POST['calendarDate']);

    wp_send_json( array("html" => $this->generate_calendar($date), "month_year" => $date->format('F Y') ) );
  }
  public function generate_calendar( $date ) {
    global $wpdb;
    $date->setTimezone(new DateTimeZone('America/New_York'));
    $currentDay = new DateTime('now');
    if ( $date == $currentDay ) {
      $activate = true;
    } else {
      $activate = false;
    }
    $month = $date->format('m');
    $day = $date->format('d');
    $year = $date->format('Y');
    $trips = $wpdb->get_results("SELECT `wp_posts`.`post_title`, STR_TO_DATE(`wp_postmeta`.`meta_value`, '%M %d, %Y') as `Date`, `wp_posts`.`guid`
    FROM `wp_posts`
    JOIN `wp_postmeta` ON `wp_posts`.`ID` = `wp_postmeta`.`post_id`
    JOIN `wp_term_relationships` ON `wp_posts`.`ID` = `wp_term_relationships`.`object_id`
    JOIN `wp_term_taxonomy` ON `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id`
    JOIN `wp_terms` ON `wp_term_taxonomy`.`term_id` = `wp_terms`.`term_id`
    WHERE `wp_posts`.`post_status` = 'publish'
    AND `wp_posts`.`post_type`='product'
    AND `wp_term_taxonomy`.`taxonomy` = 'product_type'
    AND `wp_terms`.`name` = 'trip'
    AND `wp_postmeta`.`meta_key` = '_wc_trip_start_date'
    ORDER BY `Date`;", OBJECT_K);
    $search_date = $year . "-" . $month . "-";
    // Narrow down trips to current month
    $trips_this_month = array();
    foreach($trips as $title => $info ) {
      if ( strpos($info->Date, $search_date) !== FALSE) {
        $temp = $info;
        $temp->post_title = preg_replace("/(.*)\s[ADFJMNOS][aceopu][bcglnprtvy].\s[0-9\-]{1,4}[snrtdh]{1,2}/", "$1", $temp->post_title);
        $trips_this_month[$info->Date][] = $temp;
      }
    }

    // loop through month and assemble
    $date->modify('last day of this month');
    $lastDay = $date->format('d');
    $end_week_offset = $date->format('w');
    $date->modify('first day of this month');
    $start_week_offset = $date->format('w');
    $days = '';
    for($i=1; $i <= $start_week_offset; $i++) {
      $days .= '<li>&nbsp;</li>';
    }

    for($i=1; $i <= $lastDay; $i++){
      if ( $day == $i && $activate) {
        $days .= '<li class="active">';
      } else {
        $days .= '<li>';
      }
      $days .= $i;
      $temp_date = $search_date . $i;
      if ( isset($trips_this_month[$temp_date] ) ) {
        $data = 'data-placement="auto-bottom" data-content="';

        foreach( $trips_this_month[$temp_date] as $date => $info ) {
          $data .= '<a href=\''.$info->guid.'\'>'.$info->post_title.'</a><br />';
        }
        $data .= '"';
        $days .= '<i class="fa fa-snowflake-o icon winter" ' . $data . ' aria-hidden="true"></i>';
      }
      $days .="</li>";
    }

    for($i=1; $i <= (7 - ($end_week_offset + 1) ); $i++) {
      $days .= '<li>&nbsp;</li>';
    }
    if ( $start_week_offset <= 4 || ( $start_week_offset == 5 && $lastDay <= 30 )
    || ( $start_week_offset == 6 && $lastDay <= 29) || ( $start_week_offset <= 1 && $lastDay == 28)) {
      // Pad a full week
      $days .= '<li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li><li>&nbsp;</li>';
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
    wp_enqueue_script( 'ovr_calendar_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-calendar-widget.js', array('jquery.webui-popover-js', 'jquery_spin_js'), false, true);
    wp_enqueue_style('ovr_calendar_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-calendar-widget.css');
    wp_localize_script( 'ovr_calendar_js', 'OvRCalVars', array(
      'ajaxurl'          => admin_url( 'admin-ajax.php' ),
      'calendarNonce' => wp_create_nonce( 'ovr-calendar-nonce' ),
      )
    );
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
                  <a href="/calendar"><h4 class="month_year">{$month_year}</h4><a/>
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
}
