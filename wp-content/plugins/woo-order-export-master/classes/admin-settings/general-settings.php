<?php

$filename_section = array(

	array(
		'name' => __( 'Enter export filename', 'woooe' ),
		'type' => 'title',
		'desc' => '',
		'id'   => 'woooe_filename'
	),

	array(
		'name' => __( 'File Name', 'woooe' ),
		'type' => 'text',
		'id'   => 'woooe_field_export_filename',
		'custom_attributes' => [
			'autocomplete' => 'off'
		]
	),

	array(
		'type' => 'sectionend',
		'id'   => 'woooe_filename'
	),

);

$fields = apply_filters( 'woooe_exportable_fields', array(

	array(
		'name'         => __( 'Order ID', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_id',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Number', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_number',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Date', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_date',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Total', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_total',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Currency', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_currency',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Prices Include Tax (yes/no)', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_price_include_tax',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Total Tax', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_total_tax',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Shipping Total', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_shipping_total',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Shipping Tax', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_shipping_tax',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

	array(
		'name'         => __( 'Customer Name', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_customer_name',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Customer',
		'class'        => 'customer-fields woooe-field'
	),

	array(
		'name'         => __( 'Customer\'s Billing Email', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_customer_email',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Customer',
		'class'        => 'customer-fields woooe-field'
	),

	array(
		'name'         => __( 'Product Name', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_product_name',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Product',
		'class'        => 'product-fields woooe-field'
	),

	array(
		'name'         => __( 'Order Status', 'woooe' ),
		'type'         => 'checkbox',
		'id'           => 'woooe_field_order_status',
		'export_field' => 'yes',
		'classname'    => 'WOOOE_Fetch_Order',
		'class'        => 'order-fields woooe-field'
	),

) );

$fields_section_start = array(

	array(
		'name' => __( 'Choose fields to export', 'woooe' ),
		'type' => 'title',
		'desc' => '',
		'id'   => 'woooe_title_sm'
	),

	array(
		'name'    => __( 'Select fields', 'woooe' ),
		'type'    => 'woooe_field_filter',
		'id'      => 'woooe_title_sm',
		'filters' => array(
			__( 'All Fields', 'woooe' ),
			__( 'Product Fields', 'woooe' ),
			__( 'Order Fields', 'woooe' ),
			__( 'Billing Fields', 'woooe' ),
			__( 'Shipping Fields', 'woooe' ),
			__( 'Customer Fields', 'woooe' ),
			__( 'Cart Fields', 'woooe' ),
		)
	),

);

$fields_section_end = array(

	array(
		'type' => 'sectionend',
		'id'   => 'woooe_title_sm'
	),

);

$fields_section = array_merge( $fields_section_start, $fields, $fields_section_end );

$export_duration = array(

	array(
		'name' => __( 'Select export duration & Export', 'woooe' ),
		'type' => 'title',
		'desc' => '',
		'id'   => 'woooe_export_duration'
	),

	array(
		'name'              => __( 'Start Date', 'woooe' ),
		'type'              => 'text',
		'class'             => 'woooe-datepicker',
		'custom_attributes' => array( 'autocomplete' => 'off' ),
		'id'                => 'woooe_field_start_date'
	),

	array(
		'name'              => __( 'End Date', 'woooe' ),
		'type'              => 'text',
		'class'             => 'woooe-datepicker',
		'custom_attributes' => array( 'autocomplete' => 'off' ),
		'id'                => 'woooe_field_end_date'
	),

	array(
		'name' => __( 'Export Now!', 'woooe' ),
		'type' => 'export_button',
		'id'   => 'woooe_field_export_now'
	),


	array(
		'type' => 'sectionend',
		'id'   => 'woooe_export_duration'
	),

);

return apply_filters( 'woooe_settings_fields_general', array_merge( $filename_section, $fields_section, $export_duration ) );