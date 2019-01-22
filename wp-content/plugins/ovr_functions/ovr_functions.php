<?php
/**
 * Plugin Name: OvRride Custom Functions
 * Plugin URI: https://github.com/AJAlabs/aja_functions
 * Description: Custom WordPress functions.php for OvRride.
 * Author: AJ Acevedo, Mike Barnard
 * Author URI: http://ajacevedo.com
 * Version: 0.6.2
 * License: MIT License
 */

/* Place custom code below this line. */

// Remove WordPress version meta generator from head
remove_action('wp_head', 'wp_generator');


// Remove Windows Live Writer meta from head
remove_action('wp_head', 'wlwmanifest_link');

///////////////////
//  WooCommerce  //
///////////////////

// Unhook (remove) the WooCommerce sidebar from archive pages
add_action('wp', create_function("", "if (is_archive(array('product'))) remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);") );

//Unhook (remove) the WooCommerce sidebar on individual product pages
add_action('wp', create_function("", "if (is_singular(array('product'))) remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);") );

//Unhook (remove) the WooCommerce sidebar on all pages
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10);

// Change "OUT OF STOCK" to "SOLD OUT"
add_filter('woocommerce_get_availability', 'availability_filter_func');
  function availability_filter_func($availability) {
      $availability['availability'] = str_ireplace('Out of stock', 'SOLD OUT',
      $availability['availability']);
  return $availability;
  }

// Change order status to completed when payment is complete
// found instructions
// http://www.rcorreia.com/woocommerce/woocommerce-automatically-set-order-status-payment-received/

add_filter( 'woocommerce_payment_complete_order_status', 'ovr_update_order_status', 10, 2 );

function ovr_update_order_status( $order_status, $order_id ) {

 $order = new WC_Order( $order_id );
 $original_status = $order->get_status('view');
 if ( 'processing' == $order_status && ( 'on-hold' == $original_status || 'pending' == $original_status || 'failed' == $original_status ) ) {

 return 'completed';
  //$order->set_status( 'completed', '', false);
 }

 return $order_status;
}

// Add custom order statuses
function register_custom_order_statuses() {
  // Add No Show status
  register_post_status( 'wc-no-show', array(
      'label'                     => 'No Show',
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'No Show <span class="count">(%s)</span>', 'No Show <span class="count">(%s)</span>' )
  ) );

  // Add Balance Due status
  register_post_status( 'wc-balance-due', array(
      'label'                     => 'Balance Due',
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Balance Due <span class="count">(%s)</span>', 'Balance Due<span class="count">(%s)</span>' )
  ) );
  // Add Completed Modified status
  register_post_status( 'wc-modified', array(
      'label'                     => 'Completed, Modified',
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Completed, Modified <span class="count">(%s)</span>', 'Completed, Modified<span class="count">(%s)</span>' )
  ) );
  // Add Comped status
  register_post_status( 'wc-comped', array(
      'label'                     => 'Comped',
      'public'                    => true,
      'exclude_from_search'       => false,
      'show_in_admin_all_list'    => true,
      'show_in_admin_status_list' => true,
      'label_count'               => _n_noop( 'Comped <span class="count">(%s)</span>', 'Comped<span class="count">(%s)</span>' )
  ) );
}
add_action( 'init', 'register_custom_order_statuses' );

function add_custom_statuses_to_order_statuses( $order_statuses ) {
  $new_order_statuses = array();

  // add new order status after processing
  foreach ( $order_statuses as $key => $status ) {

      $new_order_statuses[ $key ] = $status;

      if ( 'wc-processing' === $key ) {
          $new_order_statuses['wc-no-show'] = 'No Show';
          $new_order_statuses['wc-balance-due'] = 'Balance Due';
      } else if ( 'wc-completed' === $key ) {
          $new_order_statuses['wc-modified'] = 'Completed, Modified';
          $new_order_statuses['wc-comped'] = 'Comped';
      }
  }

  return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_custom_statuses_to_order_statuses' );
//Remove Reviews tab from woocommerce
function woocommerce_remove_reviews($tabs){
    unset( $tabs['reviews'] );
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'woocommerce_remove_reviews', 98);

// Remove Dynamic content gallery columns from product admin page
add_filter( 'manage_edit-product_columns', 'products_column_header' );
function products_column_header( $defaults ) {
    unset( $defaults['dfcg_image_col'] );
    unset( $defaults['dfcg_desc_col'] );
    return $defaults;
}
// Adds Google Analytics to the footer
add_action('wp_footer', 'add_google_analytics');
  function add_google_analytics() { ?>
<!-- Google Analytics: -->
<script type="text/javascript">
  // Only load Analytics in production
  if (document.location.hostname.search("ovrride.com") !== -1) {
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', 'UA-11964448-1']);
    _gaq.push(['_setDomainName', 'ovrride.com']);
    _gaq.push(['_trackPageview']);

    (function() {
      var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
      ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
  }

</script>

<?php }


// Replace Howdy with Whats up, in the Admin Toolbar
function replace_howdy( $wp_admin_bar ) {
    $my_account=$wp_admin_bar->get_node('my-account');
    $newtitle = str_replace( 'Howdy,', 'What up,', $my_account->title );
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => $newtitle,
    ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );


/**
* Adds a custom User Role 'OvR Staff'.
* With the capability to read_private_posts and read_private_pages.
* This role allows staff members to ready the Private SOP pages and Field Guides.
**/
add_role('staff', 'OvR Staff', array(
  'read' => true, // Can read posts and pages
  'read_private_posts' => true,
  'read_private_pages' => true,
  'edit_private_pagess' => false,
  'delete_private_pages' => false,
));


// Adds the Manning avatar to Settings > Discussion
if ( !function_exists('fb_addgravatar') ) {
  function fb_addgravatar( $avatar_defaults ) {
    $manning_avatar = get_bloginfo('template_directory') . '/images/default_avatar.png';
    $avatar_defaults[$manning_avatar] = 'Manning';

    return $avatar_defaults;
  }

  add_filter( 'avatar_defaults', 'fb_addgravatar' );
}

///////////////////
//   SHORTCODE   //
///////////////////
// Shortcode to display the current year, dynamically in a Post.

// Use: [year]
function year_current() {
    $year = date('Y');
    return $year;
}

add_shortcode('year', 'year_current');


// Ensures that a shortcode block is not wrapped in <p> ... </p> when on a standalone line
add_filter( 'widget_text', 'shortcode_unautop');

// Enable shortcode in the text widgets
add_filter('widget_text', 'do_shortcode');

function ovr_get_coupon_url($code) {
    global $wpdb;
    $ID = $wpdb->get_var( $wpdb->prepare("SELECT `ID` from {$wpdb->prefix}posts WHERE post_title =%s", $code) );
    $url = home_url("wp-admin/post.php?post=".$ID."&action=edit", "https");
    return $url;
}

/* sort woocommerce categories by SKU */
add_filter('woocommerce_get_catalog_ordering_args', 'am_woocommerce_catalog_orderby');
function am_woocommerce_catalog_orderby( $args ) {
  $args['orderby'] = 'meta_value';
  $args['order'] = 'asc';
  $args['meta_key'] = '_sku';
    return $args;
}

function clear_cart_on_logout() {
    if( function_exists('WC') ){
        WC()->cart->empty_cart();
    }
}
add_action('wp_logout', 'clear_cart_on_logout');




/*--------------------------------------------------------
 * Redirect WeWork Orders to cart after adding + Add WeWork Query Param
 ---------------------------------------------------------*/

if(!function_exists('wework_add_to_cart_redirect')) :
  function wework_add_to_cart_redirect( $url ) {

    if ( ! isset( $_REQUEST['add-to-cart'] ) || ! is_numeric( $_REQUEST['add-to-cart'] ) ) {
      return $url;
    }

    $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_REQUEST['add-to-cart'] ) );

    // Only redirect products that have the 'wework' category
    if ( has_term( 'wework', 'product_cat', $product_id ) ) {
      $url = get_permalink( 38 );
      $url .= '?wework=1';
    }

    return $url;
  }
  add_filter( 'woocommerce_add_to_cart_redirect', 'wework_add_to_cart_redirect' );
endif;


/*--------------------------------------------------------
 * Add WeWork Query Param to Checkout Confirmation Page 
 ---------------------------------------------------------*/

add_filter('woocommerce_get_return_url','wework_override_return_url',10,2);

function wework_override_return_url( $return_url, $order ){

  $modified_url = $return_url . '&wework=1';
  
  if( isset($order) && !empty($order) ) {
    foreach($order->get_items() as $key => $item) {
      if ( has_term( 'wework', 'product_cat', $item['product_id'] ) ) {
        return $modified_url;      
      }
    }    
  }
  return $return_url;
}


if(!function_exists('_log')){
  function _log( $message ) {
    if( WP_DEBUG === true )
      ( is_array( $message ) || is_object( $message ) ) ? error_log( print_r( $message, true ) ) : error_log( $message );
  }
}
// _log('Testing the error message logging');
// _log(array('it' => 'works'));



/*-------------------------------------------
 * CUSTOM WEWORK SUBJECT
 --------------------------------------------*/
 
//telling WooCommerce to run our custom function on the email subject
add_filter('woocommerce_email_subject_customer_completed_order', 'uiwc_change_email_subject', 1, 2);
 
function uiwc_change_email_subject( $subject, $order ) {
  $wework = false;

  // getting the order products
  $items = $order->get_items();

  // let's loop through each of them
  foreach ( $items as $item ) {      
    // checking if the ordered product is a VIP product
    if ( has_term( 'wework', 'product_cat', $item['product_id'] ) ) {
      $wework = true;
    }
  }
  
  // creating our new subject line
  if($wework ){
    $subject = sprintf( 'WeWork Ski Trip 2019 - Order # %s', $order->get_id() );   
    add_filter( 'woocommerce_email_from_name', 'wework_from_name', 1, 2);
    add_filter( 'woocommerce_email_from_address', 'wework_from_email', 1, 2);
  }
 
  // sending our custom subject back to WooCommerce
  return $subject;
}


function wework_from_name($name, $order){
  $name = 'WeWork Ski Trip 2019'; 
  return $name;
}

function wework_from_email($email, $order){
  $email = 'skitrip@wework.com'; 
  return $email;
}



/*---------------------------------------------------------------
  DIFFERENT MESSAGES FOR DIFFERENT PRODUCTS
-----------------------------------------------------------------*/
 
//hook our function to the new order email
// add_action('woocommerce_email_order_details', 'uiwc_email_order_details_products', 1, 4);
 
function uiwc_email_order_details_products($order, $admin, $plain, $email) {
  $status = $order->get_status();
  
  // checking if it's the order status we want
  if ( $status == "completed" ) {
 
    // the IDs of our wework products
    $prod_arr = array( 136 );  
    
    // getting the order products
    $items = $order->get_items();
    
    // starting the bought products variable
    $bought = false;
    
    // let's loop through each of them
    foreach ( $items as $item ) {      
      // checking if the ordered product is a VIP product
      if ( in_array( $item['product_id'], $prod_arr ) ) {
        $bought = true;
      }
    }
    
    if ( $bought ) {      
      //using WP's function for localization
      echo __( '<strong>Premium offer:</strong> Your ordered products puts you in our VIP list. 
        You can <a href="#">sign up for it here</a>.', 'uiwc' );
    }
  }  
}




/* Place custom code above this line. */
?>
