<?php

/**
 * @class FLCalloutModule
 */
class Woo360FeaturedProductCTAModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Featured Product Category', 'fl-builder' ),
			'description'   	=> __( 'Find a specific WooCommerce product category and display it like a CTA', 'fl-builder' ),
            'group' 			=> __('Woo360 Modules', 'fl-builder'),
			'category'      	=> __( 'Woo Commerce', 'fl-builder' ),
            'dir'          		=> WOO360_MODULES_DIR . 'modules/woo360-featured-product/',
            'url'          		=> WOO360_MODULES_URL . 'modules/woo360-featured-product/',
            'editor_export'		=> true, // Defaults to true and can be omitted.
            'enabled'      		=> true, // Defaults to true and can be omitted.
			'partial_refresh'	=> true,
		));
	}
}

/**
 *	Get All WooCommerce Product Categories
 */
$product_cats = array();
$args = array(
	'taxonomy'		=> 'product_cat',
	'orderby'		=> 'name',
	'show_count'	=> 1,
	'pad_counts'	=> 0,
	'hierarchical'	=> 1,
	'title_li'		=> '',
	'hide_empty'	=> 1
);

if (count(get_categories($args)) > 0) {
	$cat_unformatted = get_categories($args);
	foreach ($cat_unformatted as $cat) {
		$title = $cat->name;
		$id = $cat->term_id;
		$product_cats[$id] = $title;
	}
}
$cat_default = $cat_unformatted[0]->term_id;


/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('Woo360FeaturedProductCTAModule', array(
    'woo360-tab-1' => array(
        'title' => __('Settings', 'fl-builder'),
        'sections' => array(
            'woo360-section-1' => array(
                'fields' => array(
                    'category' => array(
                        'type' => 'select',
                        'label' => __('Select A Product Category', 'fl-builder'),
                        'options' => $product_cats,
                        'default' => $cat_default,
                    ),
                    'image' => array(
                        'type' => 'photo',
                        'label' => __('CTA Image', 'fl-builder'),
                        'show_remove'	=> 'false',
                        'help' => __('leave blank to use the category thumbnail','fl-builder'),
                    ),
                    'heading' => array(
                        'type' => 'text',
                        'label' => __('CTA Title', 'fl-builder'),
                        'help' => __('leave blank to use the category title', 'fl-builder'),
                    ),
                    'subheading' => array(
                        'type' => 'text',
                        'label' => __('CTA Subtitle', 'fl-builder'),
                        'help' => __('leave blank to use the category description', 'fl-builder'),
                    ),
                ),
            ),
        ),
    ),
    'woo360-tab-2' => array(
        'title' => __('Base Styling', 'fl-builder'),
        'sections' => array(
            'woo360-section-1' => array(
                'title' => __('Block', 'fl-builder'),
                'fields' => array(
					'title_background' => array(
					    'type'          => 'color',
					    'label'         => __( 'Title Background Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => true,
					    'help'			=> __('leave blank for no background color'),
					),
					'block_padding' => array(
						'type'        => 'dimension',
						'label'       => 'Block Margins',
						'description' => 'px',
					),
                ),
            ),
            'woo360-section-2' => array(
                'title' => __('Heading', 'fl-builder'),
                'fields' => array(
                    'heading_type' => array(
                        'type' => 'select',
                        'label' => __('Choose Title Style', 'fl-builder'),
                        'options' => array(
                        	'h1' => 'Heading 1',
                        	'h2' => 'Heading 2',
                        	'h3' => 'Heading 3',
                        	'h4' => 'Heading 4',
                        	'h5' => 'Heading 5',
                        	'h6' => 'Heading 6',
                        	'p' => 'Paragraph',
                        ),
                        'default' => 'h1',
                    ),
                    'heading_position' => array(
                        'type' => 'select',
                        'label' => __('Choose Title Position', 'fl-builder'),
                        'options' => array(
                        	'above' => 'Above Image',
                        	'top' => 'Overlaid on Image: Top',
                        	'middle' => 'Overlaid on Image: Middle',
                        	'bottom' => 'Overlaid on Image: Bottom',
                        	'below' => 'Below Image',
                        ),
                        'toggle' => array(
                        	'top' => array (
                        		'fields' => array('heading_on_hover'),
                        	),
                        	'middle' => array (
                        		'fields' => array('heading_on_hover'),
                        	),
                        	'bottom' => array (
                        		'fields' => array('heading_on_hover'),
                        	),
                        ),
                        'default' => 'below',
                    ),
                    'heading_on_hover' => array(
                        'type' => 'select',
                        'label' => __('Show Title on Hover?', 'fl-builder'),
                        'options' => array(
                        	'block' => 'Yes',
                        	'none' => 'No',
                        ),
                        'default' => 'none',
                    ),
                    'heading_text_align' => array(
                        'type' => 'select',
                        'label' => __('Text Alignment', 'fl-builder'),
                        'options' => array(
                        	'left' => 'Left',
                        	'center' => 'Center',
                        	'right' => 'Right',
                        ),
                        'default' => 'center',
                    ),
					'heading_margins' => array(
						'type'        => 'dimension',
						'label'       => 'Heading Margins',
						'description' => 'px',
					),
					'heading_padding' => array(
						'type'        => 'dimension',
						'label'       => 'Heading Padding',
						'description' => 'px',
					),
					'heading_color' => array(
					    'type'          => 'color',
					    'label'         => __( 'Title Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => false,
					    'help'			=> __('leave blank for base title color'),
					),
                ),
            ),
            'woo360-section-3' => array(
                'title' => __('Subheading', 'fl-builder'),
                'fields' => array(
                    'subheading_type' => array(
                        'type' => 'select',
                        'label' => __('Choose Subtitle Style', 'fl-builder'),
                        'options' => array(
                        	'h1' => 'Heading 1',
                        	'h2' => 'Heading 2',
                        	'h3' => 'Heading 3',
                        	'h4' => 'Heading 4',
                        	'h5' => 'Heading 5',
                        	'h6' => 'Heading 6',
                        	'p' => 'Paragraph',
                        ),
                        'default' => 'p',
                    ),
                    'subheading_position' => array(
                        'type' => 'select',
                        'label' => __('Choose subtitle Position', 'fl-builder'),
                        'options' => array(
                        	'under' => 'Underneath Heading',
                        	'below' => 'Below Image',
                        	'hover' => 'Show on CTA Hover',
                        	'hide' => 'Hide Subtitle Completely',
                        ),
                        'toggle' => array(
                        	'under'		=> array(
                        		'fields' => array('subheading'),
                        	),
                        	'below'		=> array(
                        		'fields' => array('subheading'),
                        	),
                        	'hover'		=> array(
                        		'fields' => array('subheading'),
                        	),
                        ),
                        'default' => 'under',
                    ),
                    'subheading_text_align' => array(
                        'type' => 'select',
                        'label' => __('Text Alignment', 'fl-builder'),
                        'options' => array(
                        	'left' => 'Left',
                        	'center' => 'Center',
                        	'right' => 'Right',
                        ),
                        'default' => 'center',
                    ),
					'subheading_margins' => array(
						'type'        => 'dimension',
						'label'       => 'Subheading Margins',
						'description' => 'px',
					),
					'subheading_padding' => array(
						'type'        => 'dimension',
						'label'       => 'Subheading Padding',
						'description' => 'px',
					),
					'subheading_color' => array(
					    'type'          => 'color',
					    'label'         => __( 'Subtitle Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => false,
					    'help'			=> __('leave blank for base subtitle color'),
					),
                ),
            ),
        ),
    ),
    'woo360-tab-3' => array(
        'title' => __('Hover Styling', 'fl-builder'),
        'sections' => array(
            'woo360-section-1' => array(
                'title' => __('Block', 'fl-builder'),
                'fields' => array(
                    'test_hover' => array(
                        'type' => 'select',
                        'label' => __('Test Hover Effects (force hover)', 'fl-builder'),
                        'options' => array(
                        	'' => 'Stop Test',
                        	'hover' => 'Do Test',
                        ),
                        'default' => '',
                    ),
					'title_hover_background' => array(
					    'type'          => 'color',
					    'label'         => __( 'Title Background Hover Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => true,
					    'help'			=> __('leave blank for base title background color, if set in the "Styling" tab'),
					),
					'overlay_hover_background' => array(
					    'type'          => 'color',
					    'label'         => __( 'Overlay Hover Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => true,
					    'help'			=> __('leave blank for no overlay color'),
					),
                ),
            ),
            'woo360-section-2' => array(
                'title' => __('Heading', 'fl-builder'),
                'fields' => array(
					'heading_hover_color' => array(
					    'type'          => 'color',
					    'label'         => __( 'Title Hover Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => false,
					    'help'			=> __('leave blank for base title hover color'),
					),
                ),
            ),
            'woo360-section-3' => array(
                'title' => __('Subheading', 'fl-builder'),
                'fields' => array(
					'subheading_hover_color' => array(
					    'type'          => 'color',
					    'label'         => __( 'Subtitle Hover Color', 'fl-builder' ),
					    'show_reset'    => true,
					    'show_alpha'    => false,
					    'help'			=> __('leave blank for base subtitle hover color'),
					),
                ),
            ),
        ),
    ),
));
