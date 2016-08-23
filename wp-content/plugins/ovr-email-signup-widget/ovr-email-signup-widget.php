<?php
/*
* Plugin Name: OvRride Email Signup Widget
* Description: Constant Contact signup widget
* Author: Mike Barnard
* Author URI: http://github.com/barnardm
* Version: 0.1.0
* License: MIT License
*/
// FROM: http://www.wpbeginner.com/wp-tutorials/how-to-create-a-custom-wordpress-widget/
// Creating the widget
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

// Creating widget front-end
// This is where the action happens
public function widget( $args, $instance ) {
$title = apply_filters( 'widget_title', $instance['title'] );
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
wp_enqueue_style( 'ovr_email_signup_widget_style', plugin_dir_url( __FILE__ ) . 'ovr-email-signup-widget.css');
wp_enqueue_script( 'constant_contact_signup_errors', plugin_dir_url( __FILE__ ) . 'constant_contact_errors.js');
wp_enqueue_script( 'constant_contact_signup_form', 'https://static.ctctcdn.com/h/contacts-embedded-signup-assets/1.0.2/js/signup-form.js', array('constant_contact_signup_errors'));
// This is where you run the code and display the output
echo <<<FORM
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
           <p data-id="Email Address:p" ><label data-id="Email Address:label" data-name="email" class="ctct-form-required">Email Address</label> <input data-id="Email Address:input" type="text" name="email" value="" maxlength="80"></p>
           <button type="submit" class="Button ctct-button Button--block Button-secondary" data-enabled="enabled">Sign Up</button>
       </form>
   </div>
</div>
<!--End CTCT Sign-Up Form-->
FORM;
echo $args['after_widget'];
}

// Widget Backend
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'ovr_email_signup_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php
}

// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
} // Class  ends here

// Register and load the widget
function ovr_email_signup_load_widget() {
	register_widget( 'ovr_email_signup_widget' );
}
add_action( 'widgets_init', 'ovr_email_signup_load_widget' );
?>
