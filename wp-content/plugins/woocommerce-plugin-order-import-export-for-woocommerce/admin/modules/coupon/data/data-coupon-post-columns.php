<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return apply_filters('coupon_csv_coupon_post_columns', array(
    'ID'                    => 'ID',
    'post_title'            => 'Coupon code',
    'post_excerpt'          => 'Description',
    'post_status'           => 'Status',
    'post_date'             => 'Post date',
    'post_author'           => 'Post author',

    // // Meta
    'discount_type'         => 'Discount type',
    'coupon_amount'         => 'Coupon amount',
    'individual_use'        => 'Individual use only',
    'product_ids'           => 'Product IDs',
    'product_SKUs'          => 'Product SKUs',
    'exclude_product_ids'   => 'Exclude product IIDs',
    'exclude_product_SKUs'  => 'Exclude product SKUs',
    'usage_count'           => 'No of times used',
    'usage_limit'           => 'Usage limit per coupon',
    'usage_limit_per_user'  => 'Usage limit per user',
    'limit_usage_to_x_items' => 'Limit usage to X items',
    'date_expires'          => 'Expiry date',
    'free_shipping'         => 'Allow free shipping',
    'exclude_sale_items'    => 'Exclude sale items',
    'product_categories'    => 'Product categories',
    'exclude_product_categories' => 'Exclude categories',
    'minimum_amount'        => 'Minimum amount',
    'maximum_amount'        => 'Maximum amount',
    'customer_email'        => 'Allowed emails',
) );
