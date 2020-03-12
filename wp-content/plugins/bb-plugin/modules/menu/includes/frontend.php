<?php

$menu_classes = 'fl-menu';

if ( $settings->collapse && 'accordion' == $settings->menu_layout ) {
	$menu_classes .= ' fl-menu-accordion-collapse';
}
if ( $settings->mobile_breakpoint && 'expanded' != $settings->mobile_toggle ) {
	$menu_classes .= ' fl-menu-responsive-toggle-' . $settings->mobile_breakpoint;
}
if ( $module->is_responsive_menu_flyout() ) {
	$menu_classes .= ' fl-menu-responsive-' . $settings->mobile_full_width;
	$menu_classes .= ' fl-flyout-' . $settings->flyout_position;
}
?>
<div class="<?php echo $menu_classes; ?>">
	<?php $module->render_toggle_button(); ?>
	<div class="fl-clear"></div>
	<?php

	if ( ! empty( $settings->menu ) ) {

		if ( isset( $settings->menu_layout ) ) {
			if ( in_array( $settings->menu_layout, array( 'vertical', 'horizontal' ) ) && isset( $settings->submenu_hover_toggle ) ) {
				$toggle = ' fl-toggle-' . $settings->submenu_hover_toggle;
			} elseif ( 'accordion' == $settings->menu_layout && isset( $settings->submenu_click_toggle ) ) {
				$toggle = ' fl-toggle-' . $settings->submenu_click_toggle;
			} else {
				$toggle = ' fl-toggle-arrows';
			}
		} else {
			$toggle = ' fl-toggle-arrows';
		}

		$layout = isset( $settings->menu_layout ) ? 'fl-menu-' . $settings->menu_layout : 'fl-menu-horizontal';

		printf( '<nav aria-label="%s"%s>', esc_attr( $module->get_menu_label() ), FLBuilder::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/SiteNavigationElement"', false ) );

		$defaults = array(
			'menu'         => $settings->menu,
			'container'    => false,
			'menu_class'   => 'menu ' . $layout . $toggle,
			'walker'       => new FL_Menu_Module_Walker(),
			'item_spacing' => 'discard',
		);
		add_filter( 'wp_nav_menu_objects', 'FLMenuModule::sort_nav_objects', 10, 2 );
		wp_nav_menu( $defaults );
		remove_filter( 'wp_nav_menu_objects', 'FLMenuModule::sort_nav_objects' );

		echo '</nav>';
	}
	?>
</div>
