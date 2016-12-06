<?php
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
    "recipient"     =>  array("text" => "Recipient for contact emails", "label" => "Recipient"),
    "recipientName" =>  array("text" => "Recipient Name to show on emails", "label" => "Recipient Name"),
    "phoneNumber"   =>  array("text" => "Phone number to display on widget", "label" => "Phone Number")
  );
  }


  public function widget( $args, $instance ) {

    echo $args['before_widget'];
    echo $args['before_title'];
    echo $args['after_title'];

    $phoneNumber    = ( isset($instance['phoneNumber']) ? $instance['phoneNumber'] : FALSE );
    $recipient      = ( isset($instance['recipient']) ? $instance['recipient'] : FALSE );
    $recipientName  = ( isset($instance['recipientName']) ? $instance['recipientName'] : FALSE );

    $nonced_url     = wp_nonce_url( admin_url( 'admin-ajax.php'), 'ovr_contact_form_submit', 'ovr_contact_form_submit_nonce' );
    wp_enqueue_style( 'ovr_contact_widget_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-contact-widget.css');
    wp_enqueue_script( 'jquery_validate', plugin_dir_url( dirname(__FILE__) ) . 'js/jquery.validate.min.js', array('jquery'));
    wp_enqueue_script( 'ovr_contact_form_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr-contact-form.js', array('jquery_validate','jquery_spin_js'));
    wp_enqueue_script( 'spin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/spin.min.js');
    wp_enqueue_script( 'jquery_spin_js', plugin_dir_url( dirname(__FILE__) ) . 'js/jquery.spin.js', array('jquery','spin_js'));
    wp_localize_script('ovr_contact_form_js', 'ovr_contact_vars', array( 'ajax_url' => $nonced_url));

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

  public function update( $new_instance, $old_instance ) {
    $instance = array();
    foreach( $this->fields as $id => $values ) {
      $instance[$id] = ( ! empty( $new_instance[$id] ) ) ? strip_tags( $new_instance[$id] ) : '';
    }

    return $instance;
  }

  function ovr_contact_form_submit() {
    if ( ! wp_verify_nonce( $_GET['ovr_contact_form_submit_nonce'], 'ovr_contact_form_submit' ) ) {
      die('OvR Contact Form Ajax nonce failed');
    }

    $settings = get_option( $this->option_name ); //Get All widget options
    $settings = $settings[$this->number]; // Only keep options for this widget
    $to = $settings['recipientName'] . " <" . $settings['recipient'] .">";
    $subject = "OvR Comment Form Message From " . sanitize_text_field($_POST['from']);
    $message = "Message from: " . sanitize_text_field($_POST['name']) . "\n";
    if ( isset($_POST['phone']) && "" !== $_POST['phone'] ) {
      $message .= "Phone: " . sanitize_text_field($_POST['phone']) . "\n";
    }
    $message .= "Message: " . sanitize_text_field($_POST['comment']) . "\n";
    $headers[] = "From: no-reply@ovrride.com";
    $headers[] = "Reply-To: " . sanitize_text_field($_POST['from']);
    if ( wp_mail( $to, $subject, $message, $headers) ) {
      return http_response_code(200);
    } else {
      return http_response_code(404);
    }
  }
}
