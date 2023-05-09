<?php

// Reserved column names
return apply_filters('woocommerce_csv_product_import_reserved_fields_pair',array(
        'ID' => array('title'=>'ID','description'=>'Coupon ID'),
        'post_title' => array('title'=>'Coupon code','description'=>'Name of the coupon '), 
        'post_excerpt' => array('title'=>'Description','description'=>'Short description about the Coupon'),
        'post_status' => array('title'=>'Status','description'=>'Coupon Status ( published , draft ...)'),                
        'post_date' => array('title'=>'Post date','description'=>'Coupon posted date', 'type' => 'date'),
        'discount_type' => array('title'=>'Discount type','description'=>'fixed_cart OR percent OR fixed_product OR percent_product'),
        'coupon_amount' => array('title'=>'Coupon amount','description'=>'Numeric values'),
        'individual_use' => array('title'=>'Individual use only','description'=>'yes or no'),
        'product_ids' => array('title'=>'Products','description'=>'With comma(,) Separator'),
        'exclude_product_ids' => array('title'=>'Exclude products','description'=>'With comma(,) Separator'),
        'usage_count' => array('title'=>'No of times used','description'=>'Numeric Values'),
        'usage_limit' => array('title'=>'Usage limit per coupon','description'=>'Numeric Values'),
        'usage_limit_per_user' => array('title'=>'Usage limit per user','description'=>'Numeric Values'),
        'limit_usage_to_x_items' => array('title'=>'Limit usage to X items','description'=>'Maximum Number Of Individual Items This Coupon Can Apply'),
        'date_expires' => array('title'=>'Expiry date','description'=>'YYYY-MM-DD', 'type' => 'date'),
        'free_shipping' => array('title'=>'Allow free shipping','description'=>'yes or no'),
        'exclude_sale_items' => array('title'=>'Exclude sale items','description'=>'yes or no'),
        'product_categories' => array('title'=>'Product categories','description'=>'with comma(,) Separator'),
        'exclude_product_categories' => array('title'=>'Exclude categories','description'=>'With comma(,) Separator'),
        'minimum_amount' => array('title'=>'Minimum amount','description'=>'Numeric'),
        'maximum_amount' => array('title'=>'Maximum amount','description'=>'Numeric'),
        'customer_email' => array('title'=>'Allowed emails','description'=>'With comma(,) Separator'),
));