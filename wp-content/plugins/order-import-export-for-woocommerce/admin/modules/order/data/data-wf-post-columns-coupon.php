<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

return apply_filters('coupon_csv_coupon_post_columns', array(
    'post_title'            => 'post_title',
    'ID'                    => 'ID',
    'post_excerpt'          => 'post_excerpt',
    'post_status'           => 'post_status',
    'post_date'             => 'post_date',
    'post_author'           => 'post_author',

    // // Meta
    'discount_type'         => 'discount_type',
    'coupon_amount'         => 'coupon_amount',
    'individual_use'        => 'individual_use',
    'product_ids'           => 'product_ids',
    'product_SKUs'          => 'product_SKUs',
    'exclude_product_ids'   => 'exclude_product_ids',
    'exclude_product_SKUs'  => 'exclude_product_SKUs',
    'usage_count'           => 'usage_count',
    'usage_limit'           => 'usage_limit',
    'usage_limit_per_user'  => 'usage_limit_per_user',
    'limit_usage_to_x_items' => 'limit_usage_to_x_items',
    'expiry_date'           => 'expiry_date',
    'date_expires'          => 'date_expires',
    'free_shipping'         => 'free_shipping',
    'exclude_sale_items'    => 'exclude_sale_items',
    'product_categories'    => 'product_categories',
    'exclude_product_categories' => 'exclude_product_categories',
    'minimum_amount'        => 'minimum_amount',
    'maximum_amount'        => 'maximum_amount',
    'customer_email'        => 'customer_email',
) );