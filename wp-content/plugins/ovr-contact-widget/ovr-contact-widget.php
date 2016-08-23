<?php
/*
* Plugin Name: OvRride Contact Form Widget
* Description: Widget to display contact info and a form
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/
// FROM: http://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
// Creating the widget
class ovr_contact_widget extends WP_Widget {
  function __construct() {
  parent::__construct(
  // Base ID of your widget
  'ovr_contact',

  // Widget name will appear in UI
  __('OvR Contact Form', 'ovr_contact'),

  // Widget description
  array( 'description' => __( 'Comment form for contact page', 'ovr_contact_domain' ), )
  );
  // Setup Ajax action for form
  add_action('wp_ajax_ovr_contact_form_submit', array($this, 'ovr_contact_form_submit'));
  add_action('wp_ajax_nopriv_ovr_contact_form_submit', array($this,'ovr_contact_form_submit'));

  $this->fields = array(
    "sp_key"        =>  array("text" => "Please set Sparkpost Key", "label" => "Sparkpost Key"),
    "recipient"     =>  array("text" => "Recipient for contact emails", "label" => "Recipient"),
    "recipientName" =>  array("text" => "Recipient Name to show on emails", "label" => "Recipient Name"),
    "phoneNumber"   =>  array("text" => "Phone number to display on widget", "label" => "Phone Number")
  );
  }

  // Creating widget front-end
  // This is where the action happens
  public function widget( $args, $instance ) {
  // before and after widget arguments are defined by themes
  echo $args['before_widget'];
  echo $args['before_title'];
  echo $args['after_title'];
  error_log("PHONE::::".$instance['phone']);
  $phoneNumber = ( isset($instance['phoneNumber']) ? $instance['phoneNumber'] : FALSE );
  $recipient = ( isset($instance['recipient']) ? $instance['recipient'] : FALSE );
  $recipientName = ( isset($instance['recipientName']) ? $instance['recipientName'] : FALSE );

  $nonce = wp_create_nonce("ovr_contact_form_submit");
  wp_enqueue_style( 'ovr_contact_widget_style', plugin_dir_url( __FILE__ ) . 'ovr-contact-widget.css');
  wp_enqueue_script( 'jquery_validate', plugin_dir_url( __FILE__ ) . 'jquery.validate.min.js', array('jquery'));
  wp_enqueue_script( 'ovr_contact_form_js', plugin_dir_url( __FILE__ ) . 'ovr-contact-form.js', array('jquery_validate','jquery_spin_js'));
  wp_enqueue_script( 'spin_js', plugin_dir_url( __FILE__ ) . 'spin.min.js');
  wp_enqueue_script( 'jquery_spin_js', plugin_dir_url( __FILE__ ) . 'jquery.spin.js', array('jquery','spin_js'));
  wp_localize_script('ovr_contact_form_js', 'ovr_contact_vars', array( 'ajax_url' => admin_url( 'admin-ajax.php'), 'nonce' => $nonce));
  // This is where you run the code and display the output
  echo "<h1>Contact Us</h1>";
  if ( $phoneNumber ) {
    echo "<strong>Phone: </strong><a href='tel:{$phoneNumber}'>{$phoneNumber}</a><br /><br />";
  }
  if ( $recipient ) {
    echo "<strong>Email: </strong><a href='mailto:{$recipient}'>{$recipient}</a><br /><br />";
  }
  echo <<<FORM
  <h2>Comment Form</h2>
  <br />
  <div class="formContainer">
    <span class="required">*</span> Required Fields<br /><br />
    <form action="" id="ovr_contact_form" method="POST">
      <label><strong>Name&nbsp;<span class="required">*</span></strong>
      <input type="text" name="ovr_contact_first" id="ovr_contact_first" placeholder="First" required/>
      <input type="text" name="ovr_contact_last" id="ovr_contact_last" placeholder="Last" required/></label>
      <br />
      <span class="email"><label for="ovr_contact_email"><strong>Email&nbsp;</strong><span class="required">*</span>&nbsp;
      <input type="text" name="ovr_contact_email" id="ovr_contact_email" required/></label></span>
      <span class="phone"><label for="ovr_contact_phone"><strong>Phone</strong>&nbsp;
      <input type="text" name="ovr_contact_phone" id="ovr_contact_phone" /></label></span>
      <br />
      <label for="ovr_contact_comment"><strong>Comments&nbsp;</strong><span class="required">*</span><br />
      <textarea name="ovr_contact_comment" id="ovr_contact_comment" required></textarea></label>
      <input type="submit" value="Submit" />
    </div>
FORM;
  echo $args['after_widget'];
  }

  // Widget Backend
  public function form( $instance ) {
  foreach( $this->fields as $id => $values ) {
    $fieldId = $this->get_field_id($id);
    $fieldName = $this->get_field_name($id);
    $fieldLabel = _e( ucwords($values['label']).":" );
    // Either set escaped value of field or default text
    $fieldValue = ( isset($instance[$id]) ? esc_attr($instance[$id]) : '' );
    $fieldPlaceholder =  __( $values["text"], 'ovr_contact_domain');
    echo <<<ADMINFIELD
    <p>
      <label for="{$fieldId}">{$fieldLabel}</label>
      <input class="widefat" id="{$fieldId}" name="{$fieldName}" type="text" placeholder="{$fieldPlaceholder}" value="{$fieldValue}" />
    </p>
ADMINFIELD;
  }

  }

  // Updating widget replacing old instances with new
  public function update( $new_instance, $old_instance ) {
  $instance = array();
  foreach( $this->fields as $id => $values ) {
      $instance[$id] = ( ! empty( $new_instance[$id] ) ) ? strip_tags( $new_instance[$id] ) : '';
  }
  return $instance;
  }

  function ovr_contact_form_submit() {
    wp_verify_nonce($_POST['security'], $_POST['action']);

    $settings = get_option( $this->option_name ); //Get All widget options
    $settings = $settings[$this->number]; // Only keep options for this widget

    $jsonOut                                                = (object)[];
    $jsonOut->recipients                                    = [];
    $jsonOut->recipients[0]                                 = (object)[];
    $jsonOut->recipients[0]->address                        = (object)[];
    $jsonOut->recipients[0]->address->email                 = $settings['recipient'];
    $jsonOut->recipients[0]->address->name                  = $settings['recipientName'];
    $jsonOut->recipients[0]->{substitution_data}            = (object)[];
    $jsonOut->recipients[0]->{substitution_data}->from      = "info@ovrride.com";
    $jsonOut->recipients[0]->{substitution_data}->reply_to  = sanitize_text_field($_POST['from']);// From = customer email from form
    $jsonOut->recipients[0]->{substitution_data}->subject   = "OvR Comment Form Message";
    $jsonOut->recipients[0]->{substitution_data}->name      = sanitize_text_field( $_POST['name'] );
    $jsonOut->recipients[0]->{substitution_data}->phone     = sanitize_text_field( $_POST['phone'] );
    $jsonOut->recipients[0]->{substitution_data}->content   = sanitize_text_field( $_POST['comment'] );
    $jsonOut->content                                       = (object)[];
    $jsonOut->content->{template_id}                        = "ovr-wordpress-contact-form";

    $jsonOut = json_encode( $jsonOut );


    $ch   = curl_init('https://api.sparkpost.com/api/v1/transmissions/');
    $auth = "Authorization: Basic " . base64_encode($settings['sp_key'].":");

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonOut);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $auth, 'Content-Length: '.strlen($jsonOut)));
    curl_setopt($ch, CURLOPT_HEADER, 1);

    $result = curl_exec($ch);

    if ( property_exists($result, 'errors') ) {
      error_log("COMMENT FORM ERROR: " . $result->errors->message . ", " . $result->errors->description . ", " . $result->errors->code);
      curl_close($ch);
      return http_response_code(404);
    } else {
      curl_close($ch);
      return http_response_code(200);
    }
  }
}
function ovr_contact_load_widget() {
	register_widget( 'ovr_contact_widget' );
}
add_action( 'widgets_init', 'ovr_contact_load_widget' );
