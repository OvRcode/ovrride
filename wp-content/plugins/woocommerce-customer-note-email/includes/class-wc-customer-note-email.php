<?php

if (! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

/**
 * A customer Customer Note WooCommerce Email Class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Customer_Note_Email extends WC_Email {
  /**
  * Set Email Defaults
  *
  * @since 0.1
  */
  public function __construct() {
    
    // set ID
    $this->id = 'wc_customer_note_order';
    
    // this is the title in WooCommerce Email Settings
    $this->title = 'Customer Note Order';
    
    // this is the description in WooCommerce email settings
    $this->description = 'Customer Note Emails are sent to admin email when a customer adds a note to an order';
    
    // these are the default heading and subject lines, can be overridden using settings
    $this->heading = 'Customer Order Note';
    $this->subject = 'Customer Note on Order:';
    
    // template for email, using existing emails as stand-in
    $this->template_html = 'emails/admin-customer-note.php';
    $this->template_plain = 'email/plain/admin-new-order.php';
    
    // Trigger on completed orders
    #add_action( 'woocommerce_order_status_pending_to_completed_notification', array( $this, 'trigger' ) );
    add_action( 'woocommerce_order_status_pending_to_completed', array( $this, 'trigger' ) );
    
    // Call parent constructor, just in case any defaults were missed
    parent::__construct();
    
    // this sets the recipient to the settings defined below in init_form_fields()
    $this->recipient = $this->get_option( 'recipient' );
    
    // if none was entered, just use the WP admin email as a fallback
    if ( ! $this->recipient )
      $this->recipient = get_option( 'admin_email' );
  }
  
  /**
   * Determine if the email should be sent and setup email merge variables
   *
   * @since 0.1
   * @param int $order_id
   */
  public function trigger( $order_id ) {
    
    // bail if no order ID is present
    if ( ! $order_id )
      return;
    
    // setup order object
    $this->object = new WC_Order( $order_id );
    // bail if order has no customer note or if the email is not enabled
    if ( ! $this->is_enabled() || empty($this->object->customer_note) )
      return;
    
    // Add order # to subject
    $this->subject .= $this->object->get_order_number();
    // Send note email
    $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
  }
  
  /**
   * get_content_html function.
   *
   * @since 0.1
   * @return string
   */
  public function get_content_html() {
    ob_start();
    woocommerce_get_template( $this->template_html, array(
      'order'         =>  $this->object,
      'email_heading' =>  $this->get_heading()
    ) );
    return ob_get_clean();
  }
  
  /**
   * get_content_plain function.
   *
   * @since 0.1
   * @return string
   */
  public function get_content_plain() {
    ob_start();
    woocommerce_get_template( $this->template_plain, array(
      'order'          => $this->object,
      'email_heading'  => $this->get_heading()      
    ) );
    return ob_get_clean();
  }
  
  /**
   * Initialize Settings Form Fields
   *
   * @since 0.1
   */
  public function init_form_fields() {
 
      $this->form_fields = array(
          'enabled'    => array(
              'title'   => 'Enable/Disable',
              'type'    => 'checkbox',
              'label'   => 'Enable this email notification',
              'default' => 'yes'
          ),
          'recipient'  => array(
              'title'       => 'Recipient(s)',
              'type'        => 'text',
              'description' => sprintf( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', esc_attr( get_option( 'admin_email' ) ) ),
              'placeholder' => '',
              'default'     => ''
          ),
          'subject'    => array(
              'title'       => 'Subject',
              'type'        => 'text',
              'description' => sprintf( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', $this->subject ),
              'placeholder' => '',
              'default'     => ''
          ),
          'heading'    => array(
              'title'       => 'Email Heading',
              'type'        => 'text',
              'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.' ), $this->heading ),
              'placeholder' => '',
              'default'     => ''
          ),
          'email_type' => array(
              'title'       => 'Email type',
              'type'        => 'select',
              'description' => 'Choose which format of email to send.',
              'default'     => 'html',
              'class'       => 'email_type',
              'options'     => array(
                  'plain'     => 'Plain text',
                  'html'      => 'HTML', 'woocommerce',
                  'multipart' => 'Multipart', 'woocommerce',
              )
          )
      );
  }
} // end \WC_Customer_Note_Email class
?>