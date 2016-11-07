<?php
class ovr_dual_trip_feature_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_dual_trip_feature',

    // Widget name will appear in UI
    'OvR Dual Trip Feature',

    // Widget description
    array( 'description' => 'Medium tile widget to display two trips' )
    );
  }
  public function form($instance) {
    wp_enqueue_script('media-upload');
    wp_enqueue_media();
    wp_enqueue_script('ovr_dual_trip_feature_admin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-dual-trip-feature-admin.min.js', array('jquery') );
    $widgetTitleID = $this->get_field_id('widgetTitle');
    $widgetTitleName = $this->get_field_name('widgetTitle');
    $widgetTitle = $instance['widgetTitle'];
    $widgetExcerptID = $this->get_field_id('widgetExcerpt');
    $widgetExcerptName = $this->get_field_name('widgetExcerpt');
    $widgetExcerpt = $instance['widgetExcerpt'];
    $widgetImageID = $this->get_field_id('widgetImage');
    $widgetImageName = $this->get_field_name('widgetImage');
    $widgetImage = $instance['widgetImage'];
    $tripOneID = $this->get_field_id('tripOne');
    $tripOneName = $this->get_field_name('tripOne');
    $tripOneOptions = $this->get_trips_options($instance['tripOne']);
    $tripOneImageID = $this->get_field_id('tripOneImage');
    $tripOneImageName = $this->get_field_name('tripOneImage');
    $tripOneImage = $instance['tripOneImage'];
    $tripTwoID = $this->get_field_id('tripTwo');
    $tripTwoName = $this->get_field_name('tripTwo');
    $tripTwoOptions = $this->get_trips_options($instance['tripTwo']);
    $tripTwoImageID = $this->get_field_id('tripTwoImage');
    $tripTwoImageName = $this->get_field_name('tripTwoImage');
    $tripTwoImage = $instance['tripTwoImage'];
    echo <<<ADMINFORM
    <p>
      <label for="{$widgetTitleID}">Widget title: 25 characters</label>
      <input maxlength="25" id="{$widgetTitleID}" name="{$widgetTitleName}" type="text" value="{$widgetTitle}">
    </p>
    <p>
      <label for="{$widgetExcerptID}">Widget excerpt: 126 characters</label>
      <textarea style="width:100%" maxlength="126" id="{$widgetExcerptID}" name="{$widgetExcerptName}">{$widgetExcerpt}</textarea>
    </p>
    <p>
      <label for="{$widgetImage}">Widget Main Image: 588x235px</label>
      <input class="widefat" id="{$widgetImageID}" name="{$widgetImageName}" type="text" value="{$widgetImage}" />
      <button class="dual_trip_upload_image_button button button-primary">Upload Widget Image</button>
    </p>
    <p>
      Trip One:<hr />
      <label for="{$tripOneID}">Trip: </label>
      <select id="{$tripOneID}" name="{$tripOneName}" style="width:100%">
        {$tripOneOptions}
      </select>
    </p>
    <p>
      <label for="{$tripOneImageID}">Trip One Image: 150x50px </label>
      <input class="widefat" id="{$tripOneImageID}" name="{$tripOneImageName}" type="text" value="{$tripOneImage}" />
      <button class="dual_trip_upload_image_button button button-primary">Upload Trip One Image</button>
    </p>

    <p>
      Trip Two:<hr />
      <label for="{$tripTwoID}">Trip: </label>
      <select id="{$tripTwoID}" name="{$tripTwoName}" style="width:100%">
        {$tripTwoOptions}
      </select>
    </p>
    <p>
      <label for="{$tripTwoImageID}">Trip Two Image: 150x50px</label>
      <input class="widefat" id="{$tripTwoImageID}" name="{$tripTwoImageName}" type="text" value="{$tripTwoImage}" />
      <button class="dual_trip_upload_image_button button button-primary">Upload Trip Two Image</button>
    </p>
ADMINFORM;
  }
  public function update( $new_instance, $old_instance ) {
    global $wpdb;

    $instance = '';
    $instance['widgetTitle'] = ( ! empty( $new_instance['widgetTitle'] ) ) ? strip_tags( $new_instance['widgetTitle'] ) : '';
    $instance['widgetExcerpt'] = ( ! empty( $new_instance['widgetExcerpt'] ) ) ? strip_tags( $new_instance['widgetExcerpt'] ) : '';
    $instance['widgetImage'] = ( ! empty( $new_instance['widgetImage'] ) ) ? strip_tags( $new_instance['widgetImage'] ) : '';
    $instance['tripOne'] = ( ! empty( $new_instance['tripOne'] ) ) ? strip_tags( $new_instance['tripOne'] ) : '';
    $instance['tripOneImage'] = ( ! empty( $new_instance['tripOneImage'] ) ) ? strip_tags( $new_instance['tripOneImage'] ) : '';
    $instance['tripTwo'] = ( ! empty( $new_instance['tripTwo'] ) ) ? strip_tags( $new_instance['tripTwo'] ) : '';
    $instance['tripTwoImage'] = ( ! empty( $new_instance['tripTwoImage'] ) ) ? strip_tags( $new_instance['tripTwoImage'] ) : '';


    if ( "" !== $instance['tripOne'] ) {
      $instance['tripOneTitle'] = get_the_title($instance['tripOne']);

      // Remove Dates and Days of the week from title
      $tempTitle = preg_replace('/[JMSNFAODTW][aueopchr][nrylpvbgtceudi][\srst.][\ss.].*/','', $instance['tripOneTitle']);
      $tempTitle = ( "" !== $tempTitle ? $tempTitle : $instance['tripOneTitle']);

      $instance['tripOneTitle'] = $tempTitle;

      $instance['tripOneDate'] = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['tripOne']}' AND `meta_key`='_wc_trip_start_date'");
      $endDate = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['tripOne']}' AND `meta_key`='_wc_trip_end_date'");
      if ( strtotime($instance['tripOneDate']) != strtotime($endDate) ) {
        $instance['tripOneDate'] = date('m/d/y', strtotime($instance['tripOneDate'])) . " - " . date('m/d/y', strtotime($endDate));
      }
      $instance['tripOneLink'] = get_the_permalink($instance['tripOne']);
    }
    if ( "" !== $instance['tripTwo'] ) {
      $instance['tripTwoTitle'] = get_the_title($instance['tripTwo']);

      // Remove Dates and Days of the week from title
      $tempTitle = preg_replace('/[JMSNFAODTW][aueopchr][nrylpvbgtceudi][\srst.][\ss.].*/','', $instance['tripTwoTitle']);
      $tempTitle = ( "" !== $tempTitle ? $tempTitle : $instance['tripTwoTitle']);

      $instance['tripTwoTitle'] = $tempTitle;

      $instance['tripTwoDate'] = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['tripTwo']}' AND `meta_key`='_wc_trip_start_date'");
      $endDate = $wpdb->get_var("SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id`='{$instance['tripTwo']}' AND `meta_key`='_wc_trip_end_date'");
      if ( strtotime($instance['tripTwoDate']) != strtotime($endDate) ) {
        $instance['tripTwoDate'] = date('m/d/y', strtotime($instance['tripTwoDate'])) . " - " . date('m/d/y', strtotime($endDate));
      }
      $instance['tripTwoLink'] = get_the_permalink($instance['tripTwo']);
    }

    return $instance;
  }
  public function widget( $args, $instance ) {
    wp_enqueue_style('ovr-dual-trip-feature-widget', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-dual-trip-feature-widget.min.css');
    wp_enqueue_script('ovr_dual_trip_feature_widget_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-dual-trip-feature-widget.min.js', array('jquery') );
    echo <<<FRONTEND
    <div class="ovr_dual_trip_feature" data-link="{$instance['link']}">
      <div class="ovr_dual_trip_feature_inner">
        <div class="ovr_dual_trip_feature_content">
          <h4 class="ovr_dual_trip_feature_title">{$instance['widgetTitle']}</h4>
          <img src="{$instance['widgetImage']}">
          <p>
            {$instance['widgetExcerpt']}
          </p>
          <div class="ovr_dual_trip_feature_trip_box">
            <div class="ovr_dual_trip_feature_trip_one" data-link="{$instance['tripOneLink']}">
              <a href="{$instance['tripOneLink']}"><span class="ovr_dual_trip_feature_trip_one_title">{$instance['tripOneTitle']}</span></a>
              <span class="ovr_dual_trip_feature_trip_one_date">{$instance['tripOneDate']}</span>
              <img src="{$instance['tripOneImage']}">
              </div>
            <div class="ovr_dual_trip_feature_trip_two" data-link="{$instance['tripTwoLink']}">
              <a href="{$instance['tripTwoLink']}"><span class="ovr_dual_trip_feature_trip_two_title">{$instance['tripTwoTitle']}</span></a>
              <span class="ovr_dual_trip_feature_trip_two_date">{$instance['tripTwoDate']}</span>
              <img src="{$instance['tripTwoImage']}">
            </div>
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
