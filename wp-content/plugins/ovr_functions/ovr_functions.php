<?php
/**
 * Plugin Name: OvRride Custom Functions
 * Plugin URI: https://github.com/AJAlabs/aja_functions
 * Description: Custom WordPress functions.php for OvRride.
 * Author: AJ Acevedo
 * Author URI: http://ajacevedo.com
 * Version: 0.2.0
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
 
 if ( 'processing' == $order_status && ( 'on-hold' == $order->status || 'pending' == $order->status || 'failed' == $order->status ) ) {
 
 return 'completed';
 
 }
 
 return $order_status;
}

// Add no-show status
function register_no_show_order_status() {
    register_post_status( 'wc-no-show', array(
        'label'                     => 'No Show',
        'public'                    => true,
        'exclude_from_search'       => false,
        'show_in_admin_all_list'    => true,
        'show_in_admin_status_list' => true,
        'label_count'               => _n_noop( 'No Show <span class="count">(%s)</span>', 'No Show <span class="count">(%s)</span>' )
    ) );
}
add_action( 'init', 'register_no_show_order_status' );
// Add to list of WC Order statuses
function add_no_show_to_order_statuses( $order_statuses ) {
  
    $new_order_statuses = array();
  
    // add new order status after processing
    foreach ( $order_statuses as $key => $status ) {
  
        $new_order_statuses[ $key ] = $status;
  
        if ( 'wc-processing' === $key ) {
            $new_order_statuses['wc-no-show'] = 'No Show';
        }
    }
  
    return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'add_no_show_to_order_statuses' );

// Adds Google Analytics to the footer
add_action('wp_footer', 'add_google_analytics');
  function add_google_analytics() { ?>
<!-- Google Analytics: -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-11964448-1']);
  _gaq.push(['_setDomainName', 'ovrride.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

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


/* Place custom code above this line. */
?>
