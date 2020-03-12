<?php

$global_settings = FLBuilderModel::get_global_settings();

FLBuilder::register_settings_form('module_advanced', array(
	'title'    => __( 'Advanced', 'fl-builder' ),
	'sections' => array(
		'margins'       => array(
			'title'  => __( 'Spacing', 'fl-builder' ),
			'fields' => array(
				'margin' => array(
					'type'       => 'dimension',
					'label'      => __( 'Margins', 'fl-builder' ),
					'slider'     => true,
					'units'      => array(
						'px',
						'%',
					),
					'preview'    => array(
						'type'     => 'css',
						'selector' => '.fl-module-content',
						'property' => 'margin',
					),
					'responsive' => array(
						'default_unit' => array(
							'default'    => $global_settings->module_margins_unit,
							'medium'     => $global_settings->module_margins_medium_unit,
							'responsive' => $global_settings->module_margins_responsive_unit,
						),
						'placeholder'  => array(
							'default'    => empty( $global_settings->module_margins ) ? '0' : $global_settings->module_margins,
							'medium'     => empty( $global_settings->module_margins_medium ) ? '0' : $global_settings->module_margins_medium,
							'responsive' => empty( $global_settings->module_margins_responsive ) ? '0' : $global_settings->module_margins_responsive,
						),
					),
				),
			),
		),
		'visibility'    => array(
			'title'  => __( 'Visibility', 'fl-builder' ),
			'fields' => array(
				'responsive_display'         => array(
					'type'    => 'select',
					'label'   => __( 'Breakpoint', 'fl-builder' ),
					'options' => array(
						''               => __( 'Always', 'fl-builder' ),
						'desktop'        => __( 'Large Devices Only', 'fl-builder' ),
						'desktop-medium' => __( 'Large &amp; Medium Devices Only', 'fl-builder' ),
						'medium'         => __( 'Medium Devices Only', 'fl-builder' ),
						'medium-mobile'  => __( 'Medium &amp; Small Devices Only', 'fl-builder' ),
						'mobile'         => __( 'Small Devices Only', 'fl-builder' ),
					),
					'preview' => array(
						'type' => 'none',
					),
				),
				'visibility_display'         => array(
					'type'    => 'select',
					'label'   => __( 'Display', 'fl-builder' ),
					'options' => array(
						''           => __( 'Always', 'fl-builder' ),
						'logged_out' => __( 'Logged Out User', 'fl-builder' ),
						'logged_in'  => __( 'Logged In User', 'fl-builder' ),
						'0'          => __( 'Never', 'fl-builder' ),
					),
					'toggle'  => array(
						'logged_in' => array(
							'fields' => array( 'visibility_user_capability' ),
						),
					),
					'preview' => array(
						'type' => 'none',
					),
				),
				'visibility_user_capability' => array(
					'type'        => 'text',
					'label'       => __( 'User Capability', 'fl-builder' ),
					/* translators: %s: wporg docs link */
					'description' => sprintf( __( 'Optional. Set the <a%s>capability</a> required for users to view this module.', 'fl-builder' ), ' href="http://codex.wordpress.org/Roles_and_Capabilities#Capability_vs._Role_Table" target="_blank"' ),
					'preview'     => array(
						'type' => 'none',
					),
				),
			),
		),
		'animation'     => array(
			'title'  => __( 'Animation', 'fl-builder' ),
			'fields' => array(
				'animation' => array(
					'type'    => 'animation',
					'label'   => __( 'Animation', 'fl-builder' ),
					'preview' => array(
						'type'     => 'animation',
						'selector' => '{node}',
					),
				),
			),
		),
		'css_selectors' => array(
			'title'  => __( 'HTML Element', 'fl-builder' ),
			'fields' => array(
				'container_element' => array(
					'type'    => 'select',
					'label'   => __( 'Container Element', 'fl-builder' ),
					'default' => 'div',
					/**
					 * Filter to add/remove container types.
					 * @see fl_builder_node_container_element_options
					 */
					'options' => apply_filters( 'fl_builder_node_container_element_options', array(
						'div'     => '&lt;div&gt;',
						'section' => '&lt;section&gt;',
						'article' => '&lt;article&gt;',
						'aside'   => '&lt;aside&gt;',
						'header'  => '&lt;header&gt;',
						'footer'  => '&lt;footer&gt;',
					) ),
					'help'    => __( 'Optional. Choose an appropriate HTML5 content sectioning element to use for this module to improve accessibility and machine-readability.', 'fl-builder' ),
					'preview' => array(
						'type' => 'none',
					),
				),
				'id'                => array(
					'type'    => 'text',
					'label'   => __( 'ID', 'fl-builder' ),
					'help'    => __( "A unique ID that will be applied to this module's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. No spaces.", 'fl-builder' ),
					'preview' => array(
						'type' => 'none',
					),
				),
				'class'             => array(
					'type'    => 'text',
					'label'   => __( 'Class', 'fl-builder' ),
					'help'    => __( "A class that will be applied to this module's HTML. Must start with a letter and only contain dashes, underscores, letters or numbers. Separate multiple classes with spaces.", 'fl-builder' ),
					'preview' => array(
						'type' => 'none',
					),
				),
			),
		),
	),
));
