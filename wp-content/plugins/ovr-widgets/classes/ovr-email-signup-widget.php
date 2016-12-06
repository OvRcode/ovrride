<?php
class ovr_email_signup_widget extends WP_Widget {

  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_email_signup',

    // Widget name will appear in UI
    __('OvR Email Signup Form', 'ovr_email_signup'),

    // Widget description
    array( 'description' => __( 'Signup form for constant contact', 'ovr_email_signup_domain' ), )
    );
  }

  public function widget( $args, $instance ) {
    wp_enqueue_style( 'ovr_email_signup_widget_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-email-signup-widget.css');
    wp_enqueue_script( 'constant_contact_signup_errors', plugin_dir_url( dirname(__FILE__) ) . 'js/constant_contact_errors.js');
    wp_enqueue_script( 'constant_contact_signup_form', 'https://static.ctctcdn.com/h/contacts-embedded-signup-assets/1.0.2/js/signup-form.js', array('constant_contact_signup_errors'));
    wp_enqueue_script( 'ovr_email_signup_widget_js', plugin_dir_url( dirname(__FILE__) ) . 'js/ovr_email_signup_widget.js', array('jquery'), false, true);
    echo <<<FORM
    <div class="email_signup">
    <i class="fa fa-times fa-lg" aria-hidden="true"></i>
      <!--Begin CTCT Sign-Up Form-->
      <div class="ctct-embed-signup">
        <div>
          <span id="success_message" style="display:none;">
           <div style="text-align:center;">Thanks for signing up!</div>
          </span>
          <form data-id="embedded_signup:form" class="ctct-custom-form Form" name="embedded_signup" method="POST" action="https://visitor2.constantcontact.com/api/signup">
            <p>Sign Up For Our Newsletter</p>
            <!-- The following code must be included to ensure your sign-up form works properly. -->
            <input data-id="ca:input" type="hidden" name="ca" value="1fab471a-74d2-480b-8b74-65d3ac1758fb">
            <input data-id="list:input" type="hidden" name="list" value="4">
            <input data-id="source:input" type="hidden" name="source" value="EFD">
            <input data-id="required:input" type="hidden" name="required" value="list,email">
            <input data-id="url:input" type="hidden" name="url" value="">
            <p data-id="Email Address:p" ><label data-id="Email Address:label" data-name="email" class="ctct-form-required">Email Address</label> <input id="email" data-id="Email Address:input" type="text" name="email" value="" maxlength="80"></p>
            <button type="submit" class="Button ctct-button Button--block Button-secondary" data-enabled="enabled">Sign Up</button>
          </form>
        </div>
      </div>
    <!--End CTCT Sign-Up Form-->
    </div>
FORM;
    echo $args['after_widget'];
  }

  public function form( $instance ) {

  }


  public function update( $new_instance, $old_instance ) {

    return array();
  }
}
