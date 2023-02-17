<?php

if (!defined('ABSPATH')) {
    exit;
}

$base_columns = array(
    'order_id' => 'ID',
    'order_number' => 'Order number',
    'order_date' => 'Order date',
    'paid_date' => 'Paid date',
    'status' => 'Status',
    'shipping_total' => 'Shipping total',
    'shipping_tax_total' => 'Shipping tax total',
    'fee_total' => 'Fee total',
    'fee_tax_total' => 'Fee tax total',
    'tax_total' => 'Tax total',
    'cart_discount' => 'Cart discount',
    'order_discount' => 'Order discount',
    'discount_total' => 'Discount total',
    'order_total' => 'Order total',
    //'refunded_total' => 'refunded_total',
    'order_currency' => 'Order currency',
    'payment_method' => 'Payment method',
    'payment_method_title' => 'Payment method title',
    'transaction_id' => 'Transaction ID',
    'customer_ip_address' => 'Customer IP address',
    'customer_user_agent' => 'Customer user agent',
    'shipping_method' => 'Shipping method',
    'customer_id' => 'Customer ID',
    'customer_user' => 'Customer user',
    'customer_email' => 'Customer email',
    'billing_first_name' => 'Billing first name',
    'billing_last_name' => 'Billing last name',
    'billing_company' => 'Billing company',
    'billing_email' => 'Billing email',
    'billing_phone' => 'Billing phone',
    'billing_address_1' => 'Billing address 1',
    'billing_address_2' => 'Billing address 2',
    'billing_postcode' => 'Billing postcode',
    'billing_city' => 'Billing city',
    'billing_state' => 'Billing state',
    'billing_country' => 'Billing country',
    'shipping_first_name' => 'Shipping first name',
    'shipping_last_name' => 'Shipping last name',
    'shipping_company' => 'Shipping company',
    'shipping_phone' => 'Shipping phone',    
    'shipping_address_1' => 'Shipping address 1',
    'shipping_address_2' => 'Shipping address 2',
    'shipping_postcode' => 'Shipping postcode',
    'shipping_city' => 'Shipping city',
    'shipping_state' => 'Shipping state',
    'shipping_country' => 'Shipping country',
    'customer_note' => 'Customer note',
    'wt_import_key' => 'wt_import_key',
    'shipping_items' => 'Shipping items',
    'fee_items' => 'Fee items',
    'tax_items' => 'Tax items',
    'coupon_items' => 'Coupon items',
    'refund_items' => 'Refund items',
    'order_notes' => 'Order notes',
    'download_permissions' => 'Download permissions'
);

if (!function_exists('is_plugin_active'))
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

if (is_plugin_active('print-invoices-packing-slip-labels-for-woocommerce/print-invoices-packing-slip-labels-for-woocommerce.php')):
    $base_columns['meta:wf_invoice_number'] = 'WT Invoice number';
    $base_columns['meta:_wf_invoice_date'] = 'WT Invoice date';
endif;

if (is_plugin_active('yith-woocommerce-order-tracking-premium/init.php')):
    $base_columns['meta:ywot_tracking_code'] = 'Tracking code';
    $base_columns['meta:ywot_tracking_postcode'] = 'Tracking postcode';
    $base_columns['meta:ywot_carrier_id'] = 'Carrier name';
    $base_columns['meta:ywot_pick_up_date'] = 'Pickup date';
    $base_columns['meta:ywot_picked_up'] = 'Order picked up?';
endif;

return apply_filters('hf_csv_order_post_columns', $base_columns);
