<?php

/**
 * @class FLRichTextModule
 */
class FLPricingTableModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Pricing Table', 'fl-builder' ),
			'description'     => __( 'A simple pricing table generator.', 'fl-builder' ),
			'category'        => __( 'Layout', 'fl-builder' ),
			'partial_refresh' => true,
			'icon'            => 'editor-table.svg',
		));
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {

		// Handle pricing column settings.
		for ( $i = 0; $i < count( $settings->pricing_columns ); $i++ ) {

			if ( ! is_object( $settings->pricing_columns[ $i ] ) ) {
				continue;
			}

			// Handle old link fields.
			if ( isset( $settings->pricing_columns[ $i ]->btn_link_target ) ) {
				$settings->pricing_columns[ $i ]->button_url_target = $settings->pricing_columns[ $i ]->btn_link_target;
				unset( $settings->pricing_columns[ $i ]->btn_link_target );
			}
			if ( isset( $settings->pricing_columns[ $i ]->btn_link_nofollow ) ) {
				$settings->pricing_columns[ $i ]->button_url_nofollow = $settings->pricing_columns[ $i ]->btn_link_nofollow;
				unset( $settings->pricing_columns[ $i ]->btn_link_nofollow );
			}

			// Handle old button module settings.
			$helper->filter_child_module_settings( 'button', $settings->pricing_columns[ $i ], array(
				'btn_3d'                 => 'three_d',
				'btn_style'              => 'style',
				'btn_padding'            => 'padding',
				'btn_padding_top'        => 'padding_top',
				'btn_padding_bottom'     => 'padding_bottom',
				'btn_padding_left'       => 'padding_left',
				'btn_padding_right'      => 'padding_right',
				'btn_mobile_align'       => 'mobile_align',
				'btn_align_responsive'   => 'align_responsive',
				'btn_font_size'          => 'font_size',
				'btn_font_size_unit'     => 'font_size_unit',
				'btn_typography'         => 'typography',
				'btn_bg_color'           => 'bg_color',
				'btn_bg_hover_color'     => 'bg_hover_color',
				'btn_bg_opacity'         => 'bg_opacity',
				'btn_bg_hover_opacity'   => 'bg_hover_opacity',
				'btn_border'             => 'border',
				'btn_border_hover_color' => 'border_hover_color',
				'btn_border_radius'      => 'border_radius',
				'btn_border_size'        => 'border_size',
			) );
		}

		return $settings;
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @param object $pricing_column
	 * @return array
	 */
	public function get_button_settings( $pricing_column ) {
		$settings = array(
			'link'          => $pricing_column->button_url,
			'link_nofollow' => $pricing_column->button_url_nofollow,
			'link_target'   => $pricing_column->button_url_target,
			'text'          => $pricing_column->button_text,
		);

		foreach ( $pricing_column as $key => $value ) {
			if ( strstr( $key, 'btn_' ) ) {
				$key              = str_replace( 'btn_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	/**
	 * @method render_button
	 */
	public function render_button( $column ) {
		$pricing_column = $this->settings->pricing_columns[ $column ];
		FLBuilder::render_module_html( 'button', $this->get_button_settings( $pricing_column ) );
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLPricingTableModule', array(
	'columns' => array(
		'title'    => __( 'Pricing Boxes', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'pricing_columns' => array(
						'type'         => 'form',
						'label'        => __( 'Pricing Box', 'fl-builder' ),
						'form'         => 'pricing_column_form',
						'preview_text' => 'title',
						'multiple'     => true,
					),
				),
			),
		),
	),
	'style'   => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'highlight'     => array(
						'type'    => 'select',
						'label'   => __( 'Highlight', 'fl-builder' ),
						'default' => 'price',
						'options' => array(
							'price' => __( 'Price', 'fl-builder' ),
							'title' => __( 'Title', 'fl-builder' ),
							'none'  => __( 'None', 'fl-builder' ),
						),
					),
					'border_radius' => array(
						'type'    => 'select',
						'label'   => __( 'Border Style', 'fl-builder' ),
						'default' => 'rounded',
						'options' => array(
							'rounded'  => __( 'Rounded', 'fl-builder' ),
							'straight' => __( 'Straight', 'fl-builder' ),
						),
					),
					'border_size'   => array(
						'type'    => 'select',
						'label'   => __( 'Border Size', 'fl-builder' ),
						'default' => 'wide',
						'options' => array(
							'large'  => _x( 'Large', 'Border size.', 'fl-builder' ),
							'medium' => _x( 'Medium', 'Border size.', 'fl-builder' ),
							'small'  => _x( 'Small', 'Border size.', 'fl-builder' ),
						),
					),
					'spacing'       => array(
						'type'    => 'select',
						'label'   => __( 'Spacing', 'fl-builder' ),
						'default' => 'wide',
						'options' => array(
							'large'  => __( 'Large', 'fl-builder' ),
							'medium' => __( 'Medium', 'fl-builder' ),
							'none'   => __( 'None', 'fl-builder' ),
						),
					),
					'min_height'    => array(
						'type'    => 'unit',
						'label'   => __( 'Features Min Height', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px' ),
						'slider'  => array(
							'max'  => 1000,
							'step' => 10,
						),
						'preview' => array(
							'type'      => 'css',
							'selector'  => '.fl-pricing-table-features',
							'property'  => 'min-height',
							'unit'      => 'px',
							'important' => true,
						),
						'help'    => __( 'Use this to normalize the height of your boxes when they have different numbers of features.', 'fl-builder' ),
					),
				),
			),
		),
	),
));

FLBuilder::register_settings_form('pricing_column_form', array(
	'title' => __( 'Add Pricing Box', 'fl-builder' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'General', 'fl-builder' ),
			'sections' => array(
				'title'     => array(
					'title'  => __( 'Title', 'fl-builder' ),
					'fields' => array(
						'title'      => array(
							'type'  => 'text',
							'label' => __( 'Title', 'fl-builder' ),
						),
						'title_size' => array(
							'type'    => 'unit',
							'label'   => __( 'Title Size', 'fl-builder' ),
							'default' => '24',
							'units'   => array( 'px' ),
							'slider'  => true,
						),
					),
				),
				'price-box' => array(
					'title'  => __( 'Price Box', 'fl-builder' ),
					'fields' => array(
						'price'      => array(
							'type'  => 'text',
							'label' => __( 'Price', 'fl-builder' ),
						),
						'duration'   => array(
							'type'        => 'text',
							'label'       => __( 'Duration', 'fl-builder' ),
							'placeholder' => __( 'per Year', 'fl-builder' ),
						),
						'price_size' => array(
							'type'    => 'unit',
							'label'   => __( 'Price Size', 'fl-builder' ),
							'default' => '31',
							'units'   => array( 'px' ),
							'slider'  => true,
						),
					),
				),
				'features'  => array(
					'title'  => _x( 'Features', 'Price features displayed in pricing box.', 'fl-builder' ),
					'fields' => array(
						'features' => array(
							'type'        => 'text',
							'label'       => __( 'Feature', 'fl-builder' ),
							'placeholder' => __( 'One feature per line. HTML is okay.', 'fl-builder' ),
							'multiple'    => true,
						),
					),
				),
			),
		),
		'button'  => array(
			'title'    => __( 'Button', 'fl-builder' ),
			'sections' => array(
				'default'    => array(
					'title'  => '',
					'fields' => array(
						'button_text' => array(
							'type'    => 'text',
							'label'   => __( 'Button Text', 'fl-builder' ),
							'default' => __( 'Get Started', 'fl-builder' ),
						),
						'button_url'  => array(
							'type'          => 'link',
							'label'         => __( 'Button URL', 'fl-builder' ),
							'show_target'   => true,
							'show_nofollow' => true,
							'connections'   => array( 'url' ),
						),
					),
				),
				'btn_icon'   => array(
					'title'  => __( 'Button Icon', 'fl-builder' ),
					'fields' => array(
						'btn_icon'           => array(
							'type'        => 'icon',
							'label'       => __( 'Button Icon', 'fl-builder' ),
							'show_remove' => true,
							'show'        => array(
								'fields' => array( 'btn_icon_position', 'btn_icon_animation' ),
							),
						),
						'btn_icon_position'  => array(
							'type'    => 'select',
							'label'   => __( 'Button Icon Position', 'fl-builder' ),
							'default' => 'before',
							'options' => array(
								'before' => __( 'Before Text', 'fl-builder' ),
								'after'  => __( 'After Text', 'fl-builder' ),
							),
						),
						'btn_icon_animation' => array(
							'type'    => 'select',
							'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
							'default' => 'disable',
							'options' => array(
								'disable' => __( 'Always Visible', 'fl-builder' ),
								'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
							),
						),
					),
				),
				'btn_style'  => array(
					'title'  => __( 'Button Style', 'fl-builder' ),
					'fields' => array(
						'btn_width'   => array(
							'type'    => 'select',
							'label'   => __( 'Button Width', 'fl-builder' ),
							'default' => 'full',
							'options' => array(
								'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
								'full' => __( 'Full Width', 'fl-builder' ),
							),
							'toggle'  => array(
								'auto' => array(
									'fields' => array( 'btn_align' ),
								),
							),
						),
						'btn_align'   => array(
							'type'       => 'align',
							'label'      => __( 'Button Align', 'fl-builder' ),
							'default'    => 'center',
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => '.fl-button-wrap',
								'property' => 'text-align',
							),
						),
						'btn_padding' => array(
							'type'       => 'dimension',
							'label'      => __( 'Button Padding', 'fl-builder' ),
							'responsive' => true,
							'slider'     => true,
							'units'      => array( 'px' ),
							'preview'    => array(
								'type'     => 'css',
								'selector' => 'a.fl-button',
								'property' => 'padding',
							),
						),
					),
				),
				'btn_text'   => array(
					'title'  => __( 'Button Text', 'fl-builder' ),
					'fields' => array(
						'btn_text_color'       => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Text Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => 'a.fl-button, a.fl-button *',
								'property'  => 'color',
								'important' => true,
							),
						),
						'btn_text_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Text Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type'      => 'css',
								'selector'  => 'a.fl-button:hover, a.fl-button:hover *, a.fl-button:focus, a.fl-button:focus *',
								'property'  => 'color',
								'important' => true,
							),
						),
						'btn_typography'       => array(
							'type'       => 'typography',
							'label'      => __( 'Button Typography', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'     => 'css',
								'selector' => 'a.fl-button',
							),
						),
					),
				),
				'btn_colors' => array(
					'title'  => __( 'Button Background', 'fl-builder' ),
					'fields' => array(
						'btn_bg_color'          => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Background Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'btn_bg_hover_color'    => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
						'btn_style'             => array(
							'type'    => 'select',
							'label'   => __( 'Button Background Style', 'fl-builder' ),
							'default' => 'flat',
							'options' => array(
								'flat'     => __( 'Flat', 'fl-builder' ),
								'gradient' => __( 'Gradient', 'fl-builder' ),
							),
						),
						'btn_button_transition' => array(
							'type'    => 'select',
							'label'   => __( 'Button Background Animation', 'fl-builder' ),
							'default' => 'disable',
							'options' => array(
								'disable' => __( 'Disabled', 'fl-builder' ),
								'enable'  => __( 'Enabled', 'fl-builder' ),
							),
							'preview' => array(
								'type' => 'none',
							),
						),
					),
				),
				'btn_border' => array(
					'title'  => __( 'Button Border', 'fl-builder' ),
					'fields' => array(
						'btn_border'             => array(
							'type'       => 'border',
							'label'      => __( 'Button Border', 'fl-builder' ),
							'responsive' => true,
							'preview'    => array(
								'type'      => 'css',
								'selector'  => 'a.fl-button',
								'important' => true,
							),
						),
						'btn_border_hover_color' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
							'default'     => '',
							'show_reset'  => true,
							'show_alpha'  => true,
							'preview'     => array(
								'type' => 'none',
							),
						),
					),
				),
			),
		),
		'style'   => array(
			'title'    => __( 'Style', 'fl-builder' ),
			'sections' => array(
				'style' => array(
					'title'  => 'Style',
					'fields' => array(
						'background'        => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Box Border', 'fl-builder' ),
							'default'     => 'F2F2F2',
							'show_alpha'  => true,
						),
						'foreground'        => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'label'       => __( 'Box Foreground', 'fl-builder' ),
							'default'     => 'ffffff',
							'show_alpha'  => true,
						),
						'column_background' => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'default'     => '66686b',
							'label'       => __( 'Accent Color', 'fl-builder' ),
							'show_alpha'  => true,
						),
						'column_color'      => array(
							'type'        => 'color',
							'connections' => array( 'color' ),
							'default'     => 'ffffff',
							'label'       => __( 'Accent Text Color', 'fl-builder' ),
							'show_alpha'  => true,
						),
						'margin'            => array(
							'type'    => 'unit',
							'label'   => __( 'Box Top Margin', 'fl-builder' ),
							'default' => '0',
							'units'   => array( 'px' ),
							'slider'  => true,
						),
					),
				),
			),
		),
	),
));
