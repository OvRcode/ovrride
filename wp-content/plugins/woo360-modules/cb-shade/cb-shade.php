<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class CBCustomShadeModule
 */
class CBCustomShadeModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('CTA-Shade', 'cb-custom-modules'),
            'description'   => __('Throw some custom shade.', 'cb-custom-modules'),
            'group'		    => __('Woo360 Modules', 'cb-custom-modules'),
            'category'		=> __('Media', 'cb-custom-modules'),
            'icon'          => 'format-image.svg',
            'dir'           => CB_CUSTOM_MODULE_DIR . 'cb-shade/',
            'url'           => CB_CUSTOM_MODULE_URL . 'cb-shade/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
						'partial_refresh' => true,
        ));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('CBCustomShadeModule', array(
    'content'       => array( // Tab
        'title'         => __('Content', 'cb-custom-modules'), // Tab title
        'sections'      => array( // Tab Sections
            'content'       => array( // Section
                'title'         => __('Content', 'cb-custom-modules'), // Section Title
                'fields'        => array( // Section Fields
					'cb_shade_link_field' => array(
							'type'          => 'link',
							'label'         => __('Link (optional)', 'cb-custom-modules'),
							'help'          => __( 'Set the entire module to be a link. You can also input links in the editor below.', 'cb-custom-modules' ),
							'connections'   => array( 'url' )
					),
                    'cb_shade_vertical_align' => array(
                        'type'          => 'select',
                        'label'         => __( 'Vertical Alignment', 'cb-custom-modules' ),
                        'default'       => 'center',
                        'options'       => array(
                            'flex-start'      => __( 'Top', 'cb-custom-modules' ),
                            'center'      => __( 'Center', 'cb-custom-modules' ),
                            'flex-end'      => __( 'Bottom', 'cb-custom-modules' )
                        ),
						'help'          => __( 'Vertical alignment is not currently supported in Internet Explorer.', 'cb-custom-modules' )
                    ),
                    'cb_shade_editor_field' => array(
							'type'          => 'editor',
							'media_buttons' => true,
							'rows'          => 10,
							'connections'   => array( 'string' )
					),
                )
            )
        )
    ),
      'design'       => array( // Tab
        'title'         => __('Design', 'cb-custom-modules'), // Tab title
        'sections'      => array( // Tab Sections
            'design'       => array( // Section
                'title'         => __('Design', 'cb-custom-modules'), // Section Title
                'fields'        => array( // Section Fields
                    'cb_shade_photo_field' => array(
							'type'          => 'photo',
							'label'         => __('Background Photo', 'cb-custom-modules'),
							'show_remove'	=> false,
							'connections'   => array( 'photo' )
					),
                    'cb_shade_bg_align' => array(
                        'type'          => 'select',
                        'label'         => __( 'Background Alignment', 'cb-custom-modules' ),
                        'default'       => 'center center',
                        'options'       => array(
                            'top left'      => __( 'Left Top', 'cb-custom-modules' ),
                            'center left'      => __( 'Left Center', 'cb-custom-modules' ),
                            'bottom left'      => __( 'Left Bottom', 'cb-custom-modules' ),
                            'top center'      => __( 'Center Top', 'cb-custom-modules' ),
                            'center center'      => __( 'Center', 'cb-custom-modules' ),
                            'bottom center'      => __( 'Center Bottom', 'cb-custom-modules' ),
                            'top right'      => __( 'Right Top', 'cb-custom-modules' ),
                            'center right'      => __( 'Right Center', 'cb-custom-modules' ),
                            'bottom right'      => __( 'Right Bottom', 'cb-custom-modules' ),
                        ),
                    ),
                    'cb_shade_bg_size' => array(
                        'type'          => 'select',
                        'label'         => __( 'Background Size', 'cb-custom-modules' ),
                        'default'       => 'cover',
                        'options'       => array(
                            'contain'      => __( 'Fit', 'cb-custom-modules' ),
                            'cover'      => __( 'Fill', 'cb-custom-modules' ),
                        ),
                    ),
                    'cb_shade_color_field' => array(
                        'type'          => 'color',
                        'label'         => __( 'Background/Overlay Color', 'cb-custom-modules' ),
                        'default'       => '000000',
                        'show_reset'    => true
                    ),
                    'overlay_opacity' => array(
                        'type'          => 'text',
                        'label'         => __( 'Overlay Opacity', 'cb-custom-modules' ),
                        'default'       => '.3',
                        'maxlength'     => '4',
                        'size'          => '6',
                        'description'   => __( '%', 'cb-custom-modules' )
                    ),
                    'hover_overlay_opacity' => array(
                        'type'          => 'text',
                        'label'         => __( 'Hover Overlay Opacity', 'cb-custom-modules' ),
                        'default'       => '.5',
                        'maxlength'     => '4',
                        'size'          => '6',
                        'description'   => __( '%', 'cb-custom-modules' )
                    ),
                    
                    'hover_overlay_radius' => array(
                        'type'          => 'text',
                        'label'         => __( 'Hover Overlay Border Radius', 'cb-custom-modules' ),
                        'default'       => '0',
                        'maxlength'     => '2',
                        'size'          => '4',
                        'description'   => __( 'px', 'cb-custom-modules' )
                    ),

                    'hover_text_padding' => array(
                        'type'          => 'text',
                        'label'         => __( 'Hover Overlay Text Padding', 'cb-custom-modules' ),
                        'default'       => '15',
                        'maxlength'     => '3',
                        'size'          => '4',
                        'description'   => __( 'px', 'cb-custom-modules' )
                    ),
                    
                    'cb_shade_min_height' => array(
							'type'          => 'text',
							'label'         => __( 'Custom Minimum Height', 'cb-custom-modules' ),
							'default'       => '',
							'maxlength'     => '4',
							'size'          => '6',
							'placeholder'   => __( '420', 'cb-custom-modules' ),
							'class'         => 'my-css-class',
							'description'   => __( 'px', 'cb-custom-modules' ),
							'help'          => __( 'Set a custom minimum height in pixels. For smaller screens, the module height may increase to accomodate content.', 'cb-custom-modules' )
					),
                    'cb_shade_secret' => array(
                        'type'          => 'select',
                        'label'         => __( 'Secret Content', 'cb-custom-modules' ),
                        'default'       => 'off',
                        'options'       => array(
                            'off'      => __( 'Off', 'cb-custom-modules' ),
                            'on'      => __( 'On', 'cb-custom-modules' )
                        ),
						'help'          => __( 'Content inside of the Module ONLY appears on hover.', 'cb-custom-modules' )
                    ),
                )
            ),
        ),
    ),
));
