<?php

/**
 * This file should be used to render each module instance.
 * You have access to two variables in this file: 
 * 
 * $module An instance of your module class.
 * $settings The module's settings.
 *
 * Example: 
 */

if ( !function_exists('add_loginout_link') ) {

    function add_loginout_link($iconArray, $showPrice, $showAccount, $settings) {

        if ( class_exists( 'WooCommerce' ) ) {
        
        
            if ($settings->show_if_not_logged_in == 'yes') {
        
                if ( is_user_logged_in() ) {
        
                    if ($showPrice === "yes") {
                        echo'<a class="cart-link" href="'. wc_get_cart_url() .'"><i class="' . $iconArray[0] . '"></i> '. sprintf ( _n( "%d item", "%d items", WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ) .' - '. WC()->cart->get_cart_total() .'</a>';
                    } else {
                        echo'<a class="cart-link" href="'. wc_get_cart_url() .'"><i class="' . $iconArray[0] . '"></i> '. sprintf ( _n( "%d item", "%d items", WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ) .'</a>';
                    }
        
                    if ($showAccount === "yes") {
                        echo '<a href="'. get_permalink( get_option("woocommerce_myaccount_page_id")) .'"> | <i class="' . $iconArray[1] . '"></i> My Account</a>';
                        echo '<a href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'"> | Log Out</a>';
                    } else {
                        echo '<a href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'"> | <i class="' . $iconArray[1] . '"></i> Log Out</a>';
                    }
                } else {
        
                    if ($showPrice === "yes") {
                        echo'<a class="cart-link" href="'. wc_get_cart_url() .'"><i class="' . $iconArray[0] . '"></i> '. sprintf ( _n( "%d item", "%d items", WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ) .' - '. WC()->cart->get_cart_total() .'</a>';
                    } else {
                        echo'<a class="cart-link" href="'. wc_get_cart_url() .'"><i class="' . $iconArray[0] . '"></i> '. sprintf ( _n( "%d item", "%d items", WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ) .'</a>';
                    }
        
                    echo '<a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '"> | Log In</a>';
        
                }
                    
                    
            
                
        
                    
               
        
            } else {
                // if they dont want the cart shown when user is logged out
                if (is_user_logged_in()) {
                    if ($showPrice === "yes") {
                        echo'<a class="cart-link" href="'. wc_get_cart_url() .'"><i class="' . $iconArray[0] . '"></i> '. sprintf ( _n( "%d item", "%d items", WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ) .' - '. WC()->cart->get_cart_total() .'</a>';
                    } else {
                        echo'<a class="cart-link" href="'. wc_get_cart_url() .'"><i class="' . $iconArray[0] . '"></i> '. sprintf ( _n( "%d item", "%d items", WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ) .'</a>';
                    }
            
                    if ($showAccount === "yes") {
                        echo '<a href="'. get_permalink( get_option("woocommerce_myaccount_page_id")) .'"> | <i class="' . $iconArray[1] . '"></i> My Account</a>';
                        echo '<a href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'"> | Log Out</a>';
                    } else {
                        echo '<a href="'. wp_logout_url( get_permalink( woocommerce_get_page_id( 'myaccount' ) ) ) .'"> | <i class="' . $iconArray[1] . '"></i> Log Out</a>';
                    }
                    
                } 
                elseif (!is_user_logged_in()) {
                    echo '<a href="' . get_permalink( wc_get_page_id( 'myaccount' ) ) . '">Log In</a>';
                }
            }
        }
        
        
        }

}



?>

<div class="fl-loginout-link">
<?php
$iconArray = array($settings->cart_icon, $settings->account_icon);
add_loginout_link($iconArray, $settings->show_price, $settings->show_account, $settings);
?>
</div>