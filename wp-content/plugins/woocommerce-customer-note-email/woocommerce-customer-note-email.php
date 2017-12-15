<?php
  /**
   * Plugin Name: WooCommerce Customer Note Email
   * TODO:ADD URI
   * Description: Plugin to add an extra email when a customer adds a note to an order
   * Author: Mike Barnard
   * Version: 0.2
   *
   * License: GNU General Public License v3.0
   * License URI: http://www.gnu.org/licenses/gpl-3.0.html
   *
   */

  if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

  /**
   * Add a custom email to the list of WooCommerce Emails
   *
   * @since 0.1
   * @param array $email_classes availble email classes
   * @return array filtered available email classes
   *
   */
  function add_customer_note_woocommerce_email( $email_classes ){
    //include custom email class
    require_once( 'includes/class-wc-customer-note-email.php' );

    // add the email class to the WooCommerce email class list
    $email_classes['WC_Customer_Note_Email'] = new WC_Customer_Note_Email();

    return $email_classes;
  }
  add_filter( 'woocommerce_email_classes', 'add_customer_note_woocommerce_email' );
