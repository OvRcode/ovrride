<?php

/**
 * @class FLMenuModule
 */
class FLMenuModule extends FLBuilderModule {

	/**
	 * @property $fl_builder_page_id
	 */
	public static $fl_builder_page_id;

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Menu', 'fl-builder' ),
			'description'     => __( 'Renders a WordPress menu.', 'fl-builder' ),
			'category'        => __( 'Actions', 'fl-builder' ),
			'partial_refresh' => true,
			'editor_export'   => false,
			'icon'            => 'hamburger-menu.svg',
		));

		add_action( 'pre_get_posts', __CLASS__ . '::set_pre_get_posts_query', 10, 2 );
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		if ( ! FLBuilderModel::is_builder_active() && $this->is_responsive_menu_flyout() ) {
			$this->add_css( 'font-awesome-5' );
		}
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

		// We need to double the old opacity inputs because the bg color used
		// to be applied to the menu and the list items which layers the color.
		if ( isset( $settings->menu_bg_opacity ) && is_numeric( $settings->menu_bg_opacity ) ) {
			$settings->menu_bg_opacity        = $settings->menu_bg_opacity * 1.5;
			$settings->mobile_menu_bg_opacity = $settings->menu_bg_opacity;
		}

		// Handle old opacity inputs.
		$helper->handle_opacity_inputs( $settings, 'menu_bg_opacity', 'menu_bg_color' );
		$helper->handle_opacity_inputs( $settings, 'mobile_menu_bg_opacity', 'mobile_menu_bg' );
		$helper->handle_opacity_inputs( $settings, 'submenu_bg_opacity', 'submenu_bg_color' );
		$helper->handle_opacity_inputs( $settings, 'separator_opacity', 'separator_color' );

		// Remove old align default.
		if ( 'default' === $settings->menu_align ) {
			$settings->menu_align = '';
		}

		// Handle old horizontal_spacing.
		if ( isset( $settings->horizontal_spacing ) ) {
			$settings->link_spacing_left  = $settings->horizontal_spacing;
			$settings->link_spacing_right = $settings->horizontal_spacing;
			unset( $settings->horizontal_spacing );
		}

		// Handle old vertical_spacing.
		if ( isset( $settings->vertical_spacing ) ) {
			$settings->link_spacing_top    = $settings->vertical_spacing;
			$settings->link_spacing_bottom = $settings->vertical_spacing;
			unset( $settings->vertical_spacing );
		}

		// Make sure we have a typography array.
		if ( ! isset( $settings->typography ) || ! is_array( $settings->typography ) ) {
			$settings->typography            = array();
			$settings->typography_medium     = array();
			$settings->typography_responsive = array();
		}

		// Handle old font setting.
		if ( isset( $settings->font ) ) {
			$settings->typography['font_family'] = $settings->font['family'];
			$settings->typography['font_weight'] = $settings->font['weight'];
			unset( $settings->font );
		}

		// Handle old font size setting.
		if ( isset( $settings->text_size ) ) {
			$settings->typography['font_size'] = array(
				'length' => $settings->text_size,
				'unit'   => 'px',
			);
			unset( $settings->text_size );
		}

		// Handle old text transform setting.
		if ( isset( $settings->text_transform ) ) {
			$settings->typography['text_transform'] = $settings->text_transform;
			unset( $settings->text_transform );
		}

		// Handle old submenu spacing.
		if ( isset( $settings->submenu_spacing ) ) {
			$settings->submenu_spacing_top    = $settings->submenu_spacing;
			$settings->submenu_spacing_right  = $settings->submenu_spacing;
			$settings->submenu_spacing_bottom = $settings->submenu_spacing;
			$settings->submenu_spacing_left   = $settings->submenu_spacing;
			unset( $settings->submenu_spacing );
		}

		// Return the filtered settings.
		return $settings;
	}

	/**
	 * Get the WordPress menu options.
	 *
	 * @return array
	 */
	public static function _get_menus() {
		$get_menus = get_terms( 'nav_menu', array(
			'hide_empty' => true,
		) );
		$fields    = array(
			'type'   => 'select',
			'label'  => __( 'Menu', 'fl-builder' ),
			'helper' => __( 'Select a WordPress menu that you created in the admin under Appearance > Menus.', 'fl-builder' ),
		);

		if ( $get_menus ) {

			foreach ( $get_menus as $key => $menu ) {

				if ( 0 == $key ) {
					$fields['default'] = $menu->name;
				}

				$menus[ $menu->slug ] = $menu->name;
			}

			$fields['options'] = $menus;

		} else {
			$fields['options'] = array(
				'' => __( 'No Menus Found', 'fl-builder' ),
			);
		}

		return $fields;

	}

	public function get_menu_label() {
		return isset( $this->settings->mobile_title ) && '' !== $this->settings->mobile_title ? $this->settings->mobile_title : __( 'Menu', 'fl-builder' );
	}

	public function render_toggle_button() {

		$toggle = $this->settings->mobile_toggle;

		$menu_title = $this->get_menu_label();

		if ( isset( $toggle ) && 'expanded' != $toggle ) {

			if ( in_array( $toggle, array( 'hamburger', 'hamburger-label' ) ) ) {

				echo '<button class="fl-menu-mobile-toggle ' . $toggle . '" aria-label="' . esc_attr( $menu_title ) . '"><span class="svg-container">';
				include FL_BUILDER_DIR . 'img/svg/hamburger-menu.svg';
				echo '</span>';

				if ( 'hamburger-label' == $toggle ) {
					echo '<span class="fl-menu-mobile-toggle-label">' . esc_attr( $menu_title ) . '</span>';
				}

				echo '</button>';

			} elseif ( 'text' == $toggle ) {

				echo '<button class="fl-menu-mobile-toggle text"><span class="fl-menu-mobile-toggle-label" aria-label="' . esc_attr( $menu_title ) . '">' . esc_attr( $menu_title ) . '</span></button>';

			}
		}
	}

	public static function set_pre_get_posts_query( $query ) {
		if ( ! is_admin() && $query->is_main_query() ) {

			if ( $query->queried_object_id ) {

				self::$fl_builder_page_id = $query->queried_object_id;

				// Fix when menu module is rendered via hook
			} elseif ( isset( $query->query_vars['page_id'] ) && 0 != $query->query_vars['page_id'] ) {

				self::$fl_builder_page_id = $query->query_vars['page_id'];

			}
		}
	}

	public static function sort_nav_objects( $sorted_menu_items, $args ) {
		$menu_items   = array();
		$parent_items = array();
		foreach ( $sorted_menu_items as $key => $menu_item ) {
			$classes = (array) $menu_item->classes;

			// Setup classes for current menu item.
			if ( $menu_item->ID == self::$fl_builder_page_id || self::$fl_builder_page_id == $menu_item->object_id ) {
				$parent_items[ $menu_item->object_id ] = $menu_item->menu_item_parent;

				if ( ! in_array( 'current-menu-item', $classes ) ) {
					$classes[] = 'current-menu-item';

					if ( 'page' == $menu_item->object ) {
						$classes[] = 'current_page_item';
					}
				}
			}
			$menu_item->classes = $classes;
			$menu_items[ $key ] = $menu_item;
		}

		// Setup classes for parent's current item.
		foreach ( $menu_items as $key => $sorted_item ) {
			if ( in_array( $sorted_item->db_id, $parent_items ) && ! in_array( 'current-menu-parent', (array) $sorted_item->classes ) ) {
				$menu_items[ $key ]->classes[] = 'current-menu-ancestor';
				$menu_items[ $key ]->classes[] = 'current-menu-parent';
			}
		}

		return $menu_items;
	}

	public function get_media_breakpoint() {
		$global_settings   = FLBuilderModel::get_global_settings();
		$media_width       = $global_settings->responsive_breakpoint;
		$mobile_breakpoint = $this->settings->mobile_breakpoint;

		if ( isset( $mobile_breakpoint ) && 'expanded' != $this->settings->mobile_toggle ) {
			if ( 'medium-mobile' == $mobile_breakpoint ) {
				$media_width = $global_settings->medium_breakpoint;
			} elseif ( 'mobile' == $this->settings->mobile_breakpoint ) {
				$media_width = $global_settings->responsive_breakpoint;
			} elseif ( 'always' == $this->settings->mobile_breakpoint ) {
				$media_width = 'always';
			}
		}

		return $media_width;
	}

	/**
	 * Checks to see if responsive menu style is flyout.
	 *
	 * @since 2.2
	 * @return bool
	 */
	public function is_responsive_menu_flyout() {
		return strpos( $this->settings->mobile_full_width, 'flyout-' ) !== false;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLMenuModule', array(
	'general' => array( // Tab
		'title'    => __( 'General', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general' => array( // Section
				'title'  => '', // Section Title
				'fields' => array( // Section Fields
					'menu'                 => FLMenuModule::_get_menus(),
					'menu_layout'          => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'horizontal',
						'options' => array(
							'horizontal' => __( 'Horizontal', 'fl-builder' ),
							'vertical'   => __( 'Vertical', 'fl-builder' ),
							'accordion'  => __( 'Accordion', 'fl-builder' ),
							'expanded'   => __( 'Expanded', 'fl-builder' ),
						),
						'toggle'  => array(
							'horizontal' => array(
								'fields' => array( 'submenu_hover_toggle', 'menu_align' ),
							),
							'vertical'   => array(
								'fields' => array( 'submenu_hover_toggle' ),
							),
							'accordion'  => array(
								'fields' => array( 'submenu_click_toggle', 'collapse' ),
							),
						),
					),
					'submenu_hover_toggle' => array(
						'type'    => 'select',
						'label'   => __( 'Submenu Icon', 'fl-builder' ),
						'default' => 'none',
						'options' => array(
							'arrows' => __( 'Arrows', 'fl-builder' ),
							'plus'   => __( 'Plus sign', 'fl-builder' ),
							'none'   => __( 'None', 'fl-builder' ),
						),
					),
					'submenu_click_toggle' => array(
						'type'    => 'select',
						'label'   => __( 'Submenu Icon click', 'fl-builder' ),
						'default' => 'arrows',
						'options' => array(
							'arrows' => __( 'Arrows', 'fl-builder' ),
							'plus'   => __( 'Plus sign', 'fl-builder' ),
						),
					),
					'collapse'             => array(
						'type'    => 'select',
						'label'   => __( 'Collapse Inactive', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
						'help'    => __( 'Choosing yes will keep only one item open at a time. Choosing no will allow multiple items to be open at the same time.', 'fl-builder' ),
						'preview' => array(
							'type' => 'none',
						),
					),
					'mobile_title'         => array(
						'label'   => __( 'Menu Name', 'fl-builder' ),
						'type'    => 'text',
						'help'    => __( 'This is used as the menu aria attribute for accessibility and label for responsive menus.', 'fl-builder' ),
						'default' => __( 'Menu', 'fl-builder' ),
					),
				),
			),
			'mobile'  => array(
				'title'  => __( 'Responsive', 'fl-builder' ),
				'fields' => array(
					'mobile_toggle'     => array(
						'type'    => 'select',
						'label'   => __( 'Responsive Toggle', 'fl-builder' ),
						'default' => 'hamburger',
						'options' => array(
							'hamburger'       => __( 'Hamburger Icon', 'fl-builder' ),
							'hamburger-label' => __( 'Hamburger Icon + Label', 'fl-builder' ),
							'text'            => __( 'Menu Button', 'fl-builder' ),
							'expanded'        => __( 'None', 'fl-builder' ),
						),
						'toggle'  => array(
							'hamburger'       => array(
								'fields' => array( 'mobile_full_width', 'mobile_breakpoint' ),
							),
							'hamburger-label' => array(
								'fields' => array( 'mobile_full_width', 'mobile_breakpoint' ),
							),
							'text'            => array(
								'fields' => array( 'mobile_full_width', 'mobile_breakpoint' ),
							),
						),
					),
					'mobile_full_width' => array(
						'type'    => 'select',
						'label'   => __( 'Responsive Style', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'                  => __( 'Inline', 'fl-builder' ),
							'below'               => __( 'Below Row', 'fl-builder' ),
							'yes'                 => __( 'Overlay', 'fl-builder' ),
							'flyout-overlay'      => __( 'Flyout Overlay', 'fl-builder' ),
							'flyout-push'         => __( 'Flyout Push', 'fl-builder' ),
							'flyout-push-opacity' => __( 'Flyout Push with Opacity', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes'                 => array(
								'fields' => array( 'mobile_menu_bg' ),
							),
							'below'               => array(
								'fields' => array( 'mobile_menu_bg' ),
							),
							'flyout-overlay'      => array(
								'fields' => array( 'mobile_menu_bg', 'flyout_position' ),
							),
							'flyout-push'         => array(
								'fields' => array( 'mobile_menu_bg', 'flyout_position' ),
							),
							'flyout-push-opacity' => array(
								'fields' => array( 'mobile_menu_bg', 'flyout_position' ),
							),
						),
					),
					'flyout_position'   => array(
						'type'    => 'select',
						'label'   => __( 'Flyout Position', 'fl-builder' ),
						'default' => 'left',
						'options' => array(
							'left'  => __( 'Left', 'fl-builder' ),
							'right' => __( 'Right', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
					'mobile_breakpoint' => array(
						'type'    => 'select',
						'label'   => __( 'Responsive Breakpoint', 'fl-builder' ),
						'default' => 'mobile',
						'options' => array(
							'always'        => __( 'Always', 'fl-builder' ),
							'medium-mobile' => __( 'Medium &amp; Small Devices Only', 'fl-builder' ),
							'mobile'        => __( 'Small Devices Only', 'fl-builder' ),
						),
					),
				),
			),
		),
	),
	'style'   => array( // Tab
		'title'    => __( 'Style', 'fl-builder' ), // Tab title
		'sections' => array( // Tab Sections
			'general_style'   => array(
				'title'  => __( 'Menu', 'fl-builder' ),
				'fields' => array(
					'menu_align'     => array(
						'type'       => 'align',
						'label'      => __( 'Menu Alignment', 'fl-builder' ),
						'default'    => '',
						'responsive' => true,
					),
					'menu_bg_color'  => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Menu Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.menu',
							'property' => 'background-color',
						),
					),
					'mobile_menu_bg' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Menu Background Color (Mobile)', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
					),
				),
			),
			'text_style'      => array(
				'title'  => __( 'Links', 'fl-builder' ),
				'fields' => array(
					'link_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'  => 'css',
							'rules' => array(
								array(
									'selector' => '.fl-menu a, .menu > li > a, .menu > li > .fl-has-submenu-container > a, .sub-menu > li > a',
									'property' => 'color',
								),
								array(
									'selector' => '.menu .fl-menu-toggle:before, .menu .fl-menu-toggle:after',
									'property' => 'border-color',
								),
							),
						),
					),
					'link_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu a, .menu > li.current-menu-item > a, .menu > li.current-menu-item > .fl-has-submenu-container > a, .sub-menu > li.current-menu-item > a',
							'property' => 'color',
						),
					),
					'link_hover_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.menu > li.current-menu-item > a, .menu > li.current-menu-item > .fl-has-submenu-container > a, .sub-menu > li.current-menu-item > a, .sub-menu > li.current-menu-item > .fl-has-submenu-container > a',
							'property' => 'background-color',
						),
					),
					'link_spacing'        => array(
						'type'    => 'dimension',
						'label'   => __( 'Link Padding', 'fl-builder' ),
						'default' => '14',
						'units'   => array( 'px', 'em' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.menu a',
							'property' => 'padding',
						),
					),
					'typography'          => array(
						'type'       => 'typography',
						'label'      => __( 'Link Typography', 'fl-builder' ),
						'responsive' => array(
							'default'    => array(
								'default' => array(
									'font_size' => array(
										'length' => '16',
										'unit'   => 'px',
									),
								),
							),
							'medium'     => array(),
							'responsive' => array(),
						),
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-menu .menu, .fl-menu .menu > li',
							'important' => true,
						),
					),
				),
			),
			'separator_style' => array(
				'title'  => __( 'Separators', 'fl-builder' ),
				'fields' => array(
					'show_separator'  => array(
						'type'    => 'select',
						'label'   => __( 'Show Separators', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'separator_color', 'separator_opacity' ),
							),
						),
					),
					'separator_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Separator Color', 'fl-builder' ),
						'default'     => '000000',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.menu.fl-menu-horizontal li, .menu.fl-menu-horizontal li li, .menu.fl-menu-vertical li, .menu.fl-menu-accordion li, .menu.fl-menu-expanded li',
							'property' => 'border-color',
						),
					),
				),
			),
			'submenu_style'   => array(
				'title'  => __( 'Dropdowns', 'fl-builder' ),
				'fields' => array(
					'submenu_bg_color'     => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Dropdown Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'default'     => 'ffffff',
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-menu .sub-menu',
							'property' => 'background-color',
						),
					),
					'drop_shadow'          => array(
						'type'    => 'select',
						'label'   => __( 'Dropdown Shadow', 'fl-builder' ),
						'default' => 'yes',
						'options' => array(
							'no'  => __( 'No', 'fl-builder' ),
							'yes' => __( 'Yes', 'fl-builder' ),
						),
					),
					'submenu_spacing'      => array(
						'type'    => 'dimension',
						'label'   => __( 'Dropdown Padding', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px', 'em' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => 'ul.sub-menu',
							'property' => 'padding',
						),
					),
					'submenu_link_spacing' => array(
						'type'    => 'dimension',
						'label'   => __( 'Dropdown Link Padding', 'fl-builder' ),
						'default' => '',
						'units'   => array( 'px', 'em' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => 'ul.sub-menu a',
							'property' => 'padding',
						),
					),
				),
			),
		),
	),
));


class FL_Menu_Module_Walker extends Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$args   = (object) $args;

		$class_names = '';
		$value       = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$submenu = $args->has_children ? ' fl-has-submenu' : '';

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = ' class="' . esc_attr( $class_names ) . $submenu . '"';

		$item_id = apply_filters( 'fl_builder_menu_item_id', 'menu-item-' . $item->ID, $item, $depth );
		$output .= $indent . '<li id="' . $item_id . '"' . $value . $class_names . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$item_output  = $args->has_children ? '<div class="fl-has-submenu-container">' : '';
		$item_output .= $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= '</a>';

		if ( $args->has_children ) {
			$item_output .= '<span class="fl-menu-toggle"></span>';
		}

		$item_output .= $args->after;
		$item_output .= $args->has_children ? '</div>' : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {
		$id_field = $this->db_fields['id'];
		if ( is_object( $args[0] ) ) {
			$args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] );
		}
		return parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
	}
}
