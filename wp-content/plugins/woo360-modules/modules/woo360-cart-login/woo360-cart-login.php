<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class FLBasicExampleModule
 */
class Woo360CartLoginModule extends FLBuilderModule {

    /** 
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */  
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('WooCommerce Cart/Login Link', 'fl-builder'),
            'description'   => __('Used to show Login Link or Combo of Cart Total/Logout Link', 'fl-builder'),
            'category'		=> __('Woo360 Modules', 'fl-builder'),
            'group'         => __('Woo360 Modules', 'fl-builder'),
            'category'        => __( 'Woo Commerce', 'fl-builder' ),
            'dir'           => WOO360_MODULES_DIR . 'modules/woo360-cart-login/',
            'url'           => WOO360_MODULES_URL . 'modules/woo360-cart-login/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
            'partial_refresh' => true,
        ));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('Woo360CartLoginModule', array(
    'woo360-tab-1'      => array(
        'title'         => __( 'Settings', 'fl-builder' ),
        'sections'      => array(
            'woo360-section-1'  => array(
                'title'            => __( 'Main Settings', 'fl-builder' ),
                'fields'        => array(
                    
                    'show_if_not_logged_in' => array(
                        'type'          => 'select',
                        'label'         => __( 'Show cart if not logged in', 'fl-builder' ),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'      => __( 'Yes', 'fl-builder' ),
                            'no'      => __( 'No', 'fl-builder' )
                        ),
                    ),

                    'text_color' => array(
                        'type'          => 'color',
                        'label'         => __( 'Text Color', 'fl-builder' ),
                        'default'       => '333333',
                        'show_reset'    => true,
                        'show_alpha'    => true
                    ),

                    'font_family'       => array(
                        'type'          => 'font',
                        'label'         => __('Font Family', 'uabb'),
                        'default'       => array(
                            'family'        => 'Default',
                            'weight'        => 'Default'
                        ),
                    ),

                    'hover_color'        => array( 
                        'type'       => 'color',
                        'label'      => __('Hover Color', 'uabb'),
                        'default'    => '',
                        'show_reset' => true,
                        'preview'   => array(
                            'type'      => 'css',
                            'selector'  => '.uabb-ultb3-title',
                            'property'  => 'color',
                        ),
                    ),

                    'font_size' => array(
                        'type'        => 'unit',
                        'label'       => 'Font Size',
                        'description' => 'px',
                        'default' => '14',
                    ),

                    'cart_icon' => array(
                        'type'          => 'icon',
                        'label'         => __( 'Cart Icon', 'fl-builder' ),
                        'show_remove'   => true
                    ),

                    'account_icon' => array(
                        'type'          => 'icon',
                        'label'         => __( 'Account Icon', 'fl-builder' ),
                        'show_remove'   => true
                    ),

                    'icon_size' => array(
                        'type'        => 'unit',
                        'label'       => 'Icon Size',
                        'description' => 'px',
                        'default' => '14',
                    ),
                    
                    'text_alignment' => array(
                        'type'          => 'select',
                        'label'         => __( 'Text Alignment', 'fl-builder' ),
                        'default'       => 'right',
                        'options'       => array(
                            'right'      => __( 'Right', 'fl-builder' ),
                            'center'      => __( 'Center', 'fl-builder' ),
                            'left'      => __( 'Left', 'fl-builder' )
                        ),
                    ),

                    'show_price' => array(
                        'type'          => 'select',
                        'label'         => __( 'Show Price', 'fl-builder' ),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'      => __( 'Yes', 'fl-builder' ),
                            'no'      => __( 'No', 'fl-builder' )
                        ),
                    ),

                    'show_account' => array(
                        'type'          => 'select',
                        'label'         => __( 'Show Account Link', 'fl-builder' ),
                        'default'       => 'yes',
                        'options'       => array(
                            'yes'      => __( 'Yes', 'fl-builder' ),
                            'no'      => __( 'No', 'fl-builder' )
                        ),
                    ),
                )
            )
        )
    ),
));

// Function to get the woo com login and cart info to be used in this module


