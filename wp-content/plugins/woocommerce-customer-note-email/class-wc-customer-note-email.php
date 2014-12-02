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
    $this->subject = 'CUSTOMER NOTE';
    
    // template for email, using existing emails as stand-in
    $this->template_html = 'emails/admin-new-order.php';
    $this->template_plain = 'email/plain/admin-new-order.php';
    
    // Trigger on completed orders
    add_action( 'woocommerce_order_status_processing_to_completed_notification', array( $this, 'trigger' ) );
    
    // Call parent constructor, just in case any defaults were missed
    parent::__construct();
    
    // recipient, hard coding for testing/development 
    // TODO: switch to admin_email when ready to go live
    $this->recipient = "mikeb@ovrride.com";
    #$this->recipient = get_option( 'admin_email' );
  }
} // end \WC_Customer_Note_Email class