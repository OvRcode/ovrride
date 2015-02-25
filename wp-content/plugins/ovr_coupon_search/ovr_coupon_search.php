<?php
/**
 * Plugin Name: OvRride Coupon Search
 * Description: Search woocommerce orders by coupon code applied.
 * Author: Mike Barnard
 * Version: 0.1.0
 * License: MIT License
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'coupon_search_setup_menu');

function coupon_search_setup_menu() {
    add_menu_page( 'Coupon Search Plugin Page', 'Coupon Search', 'manage_options', 'coupon-search-plugin', 'coupon_search_init' );
}

function coupon_search_init() {
    global $wpdb, $blog_id;
    echo "<h1>OvR Coupon Search</h1>";
    
    $wpdb_obj = clone $wpdb;
    $wpdb->blogid = $blog_id;
    $wpdb->set_prefix($wpdb->base_prefix);
    
    $query = "SELECT DISTINCT `post_title` FROM {$wpdb->prefix}posts WHERE 
            `post_type` = 'shop_coupon' AND `post_status` = 'publish' ORDER BY `post_title`";

    $results = $wpdb->get_col($query);
    $htmlForm = "<form method='post'>";
    if ( isset($_POST['couponSearch']) ) { 
        $selected_coupon = $_POST['couponSearch'];
        $htmlForm .= "<select name='couponSearch' id='coupon'><option value='none'>Select Coupon</option>";
    } else {
        $selected_coupon = "none";
        $htmlForm .= "<select name='couponSearch' id='coupon'><option value='none' selected>Select Coupon</option>";
    }
    foreach ($results as $result) {
        if ( $result == $selected_coupon ) {
            $htmlForm .= '<option value="' . $result . '" selected>' . $result . '</option>';
        } else {
            $htmlForm .= '<option value="' . $result . '">' . $result . '</option>';
        }
    }
    $htmlForm .= "</select><input type='submit' value='Search'></form>";
    echo $htmlForm;
    
    if ( isset($_POST['couponSearch']) && $_POST['couponSearch'] !== "none" ) {
        $query = "SELECT `order_id` FROM {$wpdb->prefix}woocommerce_order_items WHERE `order_item_name` = '" . $_POST['couponSearch'] . "'";

        $results = $wpdb->get_col($query);
        echo $wpdb->num_rows . " Orders Found<br />";
        foreach ( $results as $result ) {
            echo '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $result . '&action=edit" target="_new">' . $result . '</a><br />';
            
        }
    }
}