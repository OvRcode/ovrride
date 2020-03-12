<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class FLBasicExampleModule
 */
class Woo360GravityFormModule extends FLBuilderModule
{
    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name' => __('Gravity Form', 'fl-builder'),
            'description' => __('Used to show a specific Gravity Form', 'fl-builder'),
            'group' => __('Woo360 Modules', 'fl-builder'),
            'category' => __('Actions', 'fl-builder'),
            'dir' => WOO360_MODULES_DIR . 'modules/woo360-gravity-form/',
            'url' => WOO360_MODULES_URL . 'modules/woo360-gravity-form/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled' => true, // Defaults to true and can be omitted.
            'partial_refresh' => true,
        ));
    }
}

$formsFormatted = array();
if ( class_exists( 'GFAPI' ) ) {
$forms = (GFAPI::get_forms());
foreach ($forms as $form) {
    $title = $form['title'];
    $id = $form['id'];
    $formsFormatted[$id] = $title;
}
}



/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('Woo360GravityFormModule', array(
    'woo360-tab-1' => array(
        'title' => __('Settings', 'fl-builder'),
        'sections' => array(
            'woo360-section-1' => array(
                'title' => __('From', 'fl-builder'),
                'fields' => array(
                    'form' => array(
                        'type' => 'select',
                        'label' => __('Select A Form', 'fl-builder'),
                        'default' => '1',
                        'options' => $formsFormatted,
                    ),
                    'button_block' => array(
                        'type' => 'select',
                        'label' => __('Full Width Button', 'fl-builder'),
                        'default' => 'no',
                        'options' => array(
                            'yes' => "Yes",
                            'no'  => "No"
                        ),
                    ),
                    
                    'button_background' => array(
                        'type'          => 'color',
                        'label'         => __( 'Button Background Color', 'fl-builder' ),
                        'default'       => '333333',
                        'show_reset'    => true
                    ),

                    'button_text' => array(
                        'type'          => 'color',
                        'label'         => __( 'Button Text Color', 'fl-builder' ),
                        'default'       => 'ffffff',
                        'show_reset'    => true
                    ),

                    'button_radius' => array(
                        'type'        => 'unit',
                        'label'       => 'Button Border Radius',
                        'description' => 'px',
                    ),

                    'transition' => array(
                        'type'        => 'text',
                        'label'       => 'Button Hover Transition',
                        'default'       => '.3',
                        'maxlength'     => '2',
                        'size'        => '3',
                        'description' => 'sec'
                    ),

                    'hover_button_background' => array(
                        'type'          => 'color',
                        'label'         => __( 'Hover Button Background Color', 'fl-builder' ),
                        'default'       => '333333',
                        'show_reset'    => true
                    ),

                    'hover_button_text' => array(
                        'type'          => 'color',
                        'label'         => __( 'Hover Button Text Color', 'fl-builder' ),
                        'default'       => 'ffffff',
                        'show_reset'    => true
                    ),

                    'vert_button_padding' => array(
                        'type'        => 'unit',
                        'label'       => 'Vertical Button Padding',
                        'description' => 'px',
                    ),

                    'horiz_button_padding' => array(
                        'type'        => 'unit',
                        'label'       => 'Horizontal Button Padding',
                        'description' => 'px',
                    ),
                ),
            ),
        ),
    ),
));

// Function to get the woo com login and cart info to be used in this module

function add_gravity_form($gf_formId)
{
    echo '[gravityform id="' . $gf_formId . '" title="false" description="false"]';

}

// allow gravity forms to hide labels
add_filter( 'gform_enable_field_label_visibility_settings', '__return_true' );
