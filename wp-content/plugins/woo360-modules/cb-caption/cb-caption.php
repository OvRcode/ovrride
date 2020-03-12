<?php

/**
 * This is an example module with only the basic
 * setup necessary to get it working.
 *
 * @class CBCustomCaptionModule
 */
class CBCustomCaptionModule extends FLBuilderModule {

    /**
     * Constructor function for the module. You must pass the
     * name, description, dir and url in an array to the parent class.
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct(array(
            'name'          => __('CTA-Legend', 'cb-custom-modules'),
            'description'   => __('Photo Module with a secret caption.', 'cb-custom-modules'),
            'group'		=> __('Woo360 Modules', 'cb-custom-modules'),
            'category'		=> __('Media', 'cb-custom-modules'),
            'icon'        => 'format-image.svg',
            'dir'           => CB_CUSTOM_MODULE_DIR . 'cb-caption/',
            'url'           => CB_CUSTOM_MODULE_URL . 'cb-caption/',
            'editor_export' => true, // Defaults to true and can be omitted.
            'enabled'       => true, // Defaults to true and can be omitted.
						'partial_refresh' => true,
        ));
    }
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('CBCustomCaptionModule', array(
    'content'       => array( // Tab
        'title'         => __('Content', 'cb-custom-modules'), // Tab title
        'sections'      => array( // Tab Sections
            'content'       => array( // Section
                'title'         => __('Content', 'cb-custom-modules'), // Section Title
                'fields'        => array( // Section Fields
					'cb_caption_link_field' => array(
							'type'          => 'link',
							'label'         => __('Link (optional)', 'cb-custom-modules'),
							'help'          => __( 'Set the photo to be a link. You can also input links in the editor below.', 'cb-custom-modules' ),
							'connections'   => array( 'url' )
					),
                    'cb_caption_vertical_align' => array(
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
                    'cb_caption_editor_field' => array(
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
                    'cb_caption_photo' => array(
							'type'          => 'photo',
							'label'         => __('Photo', 'cb-custom-modules'),
							'show_remove'	=> false,
							'connections'   => array( 'photo' )
					),
                    'cb_caption_photo_align' => array(
                        'type'          => 'select',
                        'label'         => __( 'Photo Alignment', 'cb-custom-modules' ),
                        'default'       => 'left',
                        'options'       => array(
                            'left'      => __( 'Left', 'cb-custom-modules' ),
                            'center'      => __( 'Center', 'cb-custom-modules' ),
                            'right'      => __( 'Right', 'cb-custom-modules' ),
                        ),
                    ),

                    'text_color_field' => array(
                        'type'          => 'color',
                        'label'         => __( 'Text Color', 'cb-custom-modules' ),
                        'default'       => 'ffffff',
                        'show_reset'    => true
                    ),

                    'cb_caption_color_field' => array(
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
                    'text_hover_transform' => array(
                        'type'          => 'text',
                        'label'         => __( 'Hover Text Transform (moves text up on hover)', 'cb-custom-modules' ),
                        'default'       => '25',
                        'maxlength'     => '4',
                        'size'          => '6',
                        'description'   => __( 'px', 'cb-custom-modules' )
                    ),
                    'box_hover_transform' => array(
                        'type'          => 'text',
                        'label'         => __( 'Hover Box Transform (moves box up on hover)', 'cb-custom-modules' ),
                        'default'       => '15',
                        'maxlength'     => '4',
                        'size'          => '6',
                        'description'   => __( 'px', 'cb-custom-modules' )
                    ),
                )
            ),
        ),
    ),
));
