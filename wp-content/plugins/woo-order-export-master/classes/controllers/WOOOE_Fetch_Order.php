<?php
if(!defined('ABSPATH')){
    exit;
}

if(!class_exists('WOOOE_Fetch_Order', false)){

    class WOOOE_Fetch_Order {

        use WOOOE_Trait_GetValue;

        /*
         * Order ID
         */
        public $order_id, $order, $order_number;
        
        static $instance = array();

        //Constructor
        function __construct($order_id) {

            $this->order_id = $order_id;
            $this->order = wc_get_order($order_id);
            $this->order_number = $this->order->get_order_number();
            $this->order_status = $this->order->get_status();
            $this->order_date = $this->order->get_date_created();
            $this->order_currency = $this->order->get_currency();
            $this->price_include_tax = $this->order->get_prices_include_tax('edit');
            $this->order_total = woooe_format_price($this->order->get_total(), $this->order->get_currency());
            $this->order_total_tax = woooe_format_price($this->order->get_total_tax(), $this->order->get_currency());
            $this->order_shipping_total = woooe_format_price($this->order->get_shipping_total(), $this->order->get_currency());
            $this->order_shipping_tax = woooe_format_price($this->order->get_shipping_tax(), $this->order->get_currency());
            $this->items = apply_filters('woooe_items_filtering', $this->order->get_items('line_item'));
            $this->shipping_items = $this->order->get_items( apply_filters( 'woocommerce_admin_order_item_types', 'shipping' ) );
        }
    }
}