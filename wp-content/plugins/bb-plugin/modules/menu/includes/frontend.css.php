<?php

$toggle_spacing = $settings->link_spacing_right > 10 ? $settings->link_spacing_right : 10;
$toggle_padding = ! empty( $settings->link_spacing_right ) ? $settings->link_spacing_right : 0;
$toggle_width   = ( $toggle_padding + 14 );
$toggle_height  = ceil( ( ( $toggle_padding * 2 ) + 14 ) * 0.65 );

/**
 * Overall menu styling
 */
FLBuilderCSS::typography_field_rule( array(
	'settings'     => $settings,
	'setting_name' => 'typography',
	'selector'     => ".fl-node-$id .fl-menu .menu, .fl-node-$id .fl-menu .menu > li",
) );

?>
.fl-node-<?php echo $id; ?> .fl-menu .menu {
	<?php

	if ( ! empty( $settings->menu_bg_color ) ) {
		echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->menu_bg_color ) . ';';
	}

	?>
}
<?php

/**
 * Overall menu alignment (horizontal only)
 */
if ( 'horizontal' === $settings->menu_layout ) {

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_align',
		'selector'     => ".fl-node-$id .fl-menu",
		'prop'         => 'text-align',
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_align',
		'selector'     => ".fl-node-$id .fl-menu .menu",
		'prop'         => 'float',
		'ignore'       => array( 'center' ),
	) );

	FLBuilderCSS::responsive_rule( array(
		'settings'     => $settings,
		'setting_name' => 'menu_align',
		'selector'     => ".fl-node-$id .fl-menu .menu",
		'props'        => array(
			'float'          => 'none',
			'display'        => 'inline-block',
			'vertical-align' => 'top',
		),
		'ignore'       => array( 'left', 'right' ),
	) );
}


/**
 * Links
 */
?>
.fl-node-<?php echo $id; ?> .menu a{
	padding-left: <?php echo ! empty( $settings->link_spacing_left ) ? $settings->link_spacing_left . $settings->link_spacing_unit : '0'; ?>;
	padding-right: <?php echo ! empty( $settings->link_spacing_right ) ? $settings->link_spacing_right . $settings->link_spacing_unit : '0'; ?>;
	padding-top: <?php echo ! empty( $settings->link_spacing_top ) ? $settings->link_spacing_top . $settings->link_spacing_unit : '0'; ?>;
	padding-bottom: <?php echo ! empty( $settings->link_spacing_bottom ) ? $settings->link_spacing_bottom . $settings->link_spacing_unit : '0'; ?>;
}

<?php if ( ! empty( $settings->link_color ) ) : ?>
.fl-builder-content .fl-node-<?php echo $id; ?> .menu > li > a,
.fl-builder-content .fl-node-<?php echo $id; ?> .menu > li > .fl-has-submenu-container > a,
.fl-builder-content .fl-node-<?php echo $id; ?> .sub-menu > li > a,
.fl-builder-content .fl-node-<?php echo $id; ?> .sub-menu > li > .fl-has-submenu-container > a{
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
	<?php if ( ! empty( $settings->link_bg_color ) ) : ?>
		background-color: #<?php echo $settings->link_bg_color; ?>;
	<?php endif; ?>
}

	<?php if ( isset( $settings->link_color ) ) : ?>

		<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none .fl-menu-toggle:before {
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
		}
	<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-menu-toggle:after{
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
		}
		<?php endif; ?>
	<?php endif; ?>

	<?php
endif;

/**
 * Links - hover / active
 */
if ( ! empty( $settings->link_hover_bg_color ) || $settings->link_hover_color ) :
	?>
.fl-node-<?php echo $id; ?> .menu > li > a:hover,
.fl-node-<?php echo $id; ?> .menu > li > a:focus,
.fl-node-<?php echo $id; ?> .menu > li > .fl-has-submenu-container:hover > a,
.fl-node-<?php echo $id; ?> .menu > li > .fl-has-submenu-container.focus > a,
.fl-node-<?php echo $id; ?> .sub-menu > li > a:hover,
.fl-node-<?php echo $id; ?> .sub-menu > li > a:focus,
.fl-node-<?php echo $id; ?> .sub-menu > li > .fl-has-submenu-container:hover > a,
.fl-node-<?php echo $id; ?> .sub-menu > li > .fl-has-submenu-container.focus > a,
.fl-node-<?php echo $id; ?> .menu > li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .menu > li.current-menu-item > .fl-has-submenu-container > a,
.fl-node-<?php echo $id; ?> .sub-menu > li.current-menu-item > a,
.fl-node-<?php echo $id; ?> .sub-menu > li.current-menu-item > .fl-has-submenu-container > a{
	<?php
	if ( ! empty( $settings->link_hover_bg_color ) ) {
		echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_bg_color ) . ';';
	}
	if ( ! empty( $settings->link_hover_color ) ) {
		echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_color ) . ';';
	}
	?>
}
<?php endif ?>

<?php if ( ! empty( $settings->link_hover_color ) ) : ?>
		<?php if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows .fl-has-submenu-container:hover > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows .fl-has-submenu-container.focus > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-arrows li.current-menu-item >.fl-has-submenu-container > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none .fl-has-submenu-container:hover > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none .fl-has-submenu-container.focus > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-none li.current-menu-item >.fl-has-submenu-container > .fl-menu-toggle:before{
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_hover_color ); ?>;
		}
	<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container:hover > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container.focus > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus li.current-menu-item > .fl-has-submenu-container > .fl-menu-toggle:before,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container:hover > .fl-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus .fl-has-submenu-container.focus > .fl-menu-toggle:after,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-toggle-plus li.current-menu-item > .fl-has-submenu-container > .fl-menu-toggle:after{
			border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_hover_color ); ?>;
		}
	<?php endif; ?>

	<?php
endif;

/**
 * Overall submenu styling
 */
if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) :
	?>
	.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
		display: none;
	}
	<?php
endif;

if ( ! empty( $settings->submenu_bg_color ) || 'yes' == $settings->drop_shadow ) :
	?>
.fl-node-<?php echo $id; ?> .fl-menu .sub-menu {
	<?php

	if ( ! empty( $settings->submenu_bg_color ) ) {
		echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->submenu_bg_color ) . ';';
	}
	if ( 'yes' == $settings->drop_shadow ) {
		echo '-webkit-box-shadow: 0 1px 20px rgba(0,0,0,0.1);';
		echo '-ms-box-shadow: 0 1px 20px rgba(0,0,0,0.1);';
		echo 'box-shadow: 0 1px 20px rgba(0,0,0,0.1);';
	}

	?>
}
	<?php
endif;

/**
 * Toggle - Arrows / None
 */
if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'arrows' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:before{
		content: '';
		position: absolute;
		right: 50%;
		top: 50%;
		z-index: 1;
		display: block;
		width: 9px;
		height: 9px;
		margin: -5px -5px 0 0;
		border-right: 2px solid;
		border-bottom: 2px solid;
		-webkit-transform-origin: right bottom;
			-ms-transform-origin: right bottom;
				transform-origin: right bottom;
		-webkit-transform: translateX( -5px ) rotate( 45deg );
			-ms-transform: translateX( -5px ) rotate( 45deg );
				transform: translateX( -5px ) rotate( 45deg );
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.fl-active > .fl-has-submenu-container .fl-menu-toggle{
		-webkit-transform: rotate( -180deg );
			-ms-transform: rotate( -180deg );
				transform: rotate( -180deg );
	}
	<?php

	/**
	 * Toggle - Plus
	 */
elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:before,
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:after{
		content: '';
		position: absolute;
		z-index: 1;
		display: block;
		border-color: #333;
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:before{
		left: 50%;
		top: 50%;
		width: 12px;
		border-top: 3px solid;
		-webkit-transform: translate( -50%, -50% );
			-ms-transform: translate( -50%, -50% );
				transform: translate( -50%, -50% );
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle:after{
		left: 50%;
		top: 50%;
		border-left: 3px solid;
		height: 12px;
		-webkit-transform: translate( -50%, -50% );
			-ms-transform: translate( -50%, -50% );
				transform: translate( -50%, -50% );
	}
	.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.fl-active > .fl-has-submenu-container .fl-menu-toggle:after{
		display: none;
	}
	<?php
endif;

/**
 * Submenu toggle
 */
if ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && in_array( $settings->submenu_hover_toggle, array( 'arrows', 'none' ) ) ) || ( 'accordion' == $settings->menu_layout && 'arrows' == $settings->submenu_click_toggle ) ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-arrows .fl-has-submenu-container a{
		padding-right: <?php echo $toggle_width; ?>px;
	}
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-arrows .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-none .fl-menu-toggle{
		width: <?php echo $toggle_height; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
	.fl-node-<?php echo $id; ?> .fl-menu-horizontal.fl-toggle-arrows .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-horizontal.fl-toggle-none .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-vertical.fl-toggle-arrows .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-vertical.fl-toggle-none .fl-menu-toggle{
		width: <?php echo $toggle_width; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
<?php elseif ( ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) && 'plus' == $settings->submenu_hover_toggle ) || ( 'accordion' == $settings->menu_layout && 'plus' == $settings->submenu_click_toggle ) ) : ?>
	.fl-node-<?php echo $id; ?> .fl-menu-<?php echo $settings->menu_layout; ?>.fl-toggle-plus .fl-has-submenu-container a{
		padding-right: <?php echo $toggle_width; ?>px;
	}

	.fl-node-<?php echo $id; ?> .fl-menu-accordion.fl-toggle-plus .fl-menu-toggle{
		width: <?php echo $toggle_height; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
	.fl-node-<?php echo $id; ?> .fl-menu-horizontal.fl-toggle-plus .fl-menu-toggle,
	.fl-node-<?php echo $id; ?> .fl-menu-vertical.fl-toggle-plus .fl-menu-toggle{
		width: <?php echo $toggle_width; ?>px;
		height: <?php echo $toggle_height; ?>px;
		margin: -<?php echo $toggle_height / 2; ?>px 0 0;
	}
	<?php
endif;

/**
 * Separators
 */
?>
.fl-node-<?php echo $id; ?> .fl-menu li{
	border-top: 1px solid transparent;
}
.fl-node-<?php echo $id; ?> .fl-menu li:first-child{
	border-top: none;
}
<?php if ( isset( $settings->show_separator ) && 'yes' == $settings->show_separator && ! empty( $settings->separator_color ) ) : ?>
	.fl-node-<?php echo $id; ?> .menu.fl-menu-<?php echo $settings->menu_layout; ?> li,
	.fl-node-<?php echo $id; ?> .menu.fl-menu-horizontal li li{
		border-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->separator_color ); ?>;
	}
	<?php
endif;

/**
 * Responsive Layout
 */
if ( 'always' != $module->get_media_breakpoint() ) :
	?>
	@media ( max-width: <?php echo $module->get_media_breakpoint(); ?>px ) {
<?php endif; ?>

	<?php if ( $module->is_responsive_menu_flyout() ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout {
			<?php if ( ! empty( $settings->mobile_menu_bg ) ) : ?>
				background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->mobile_menu_bg ); ?>;
			<?php else : ?>
				background-color: #fff;
			<?php endif; ?>

			<?php if ( 'right' == $settings->flyout_position ) : ?>
				right: -267px;
			<?php elseif ( 'left' == $settings->flyout_position ) : ?>
				left: -267px;
			<?php endif; ?>

			overflow-y: auto;
			padding: 0 5px;
			position: fixed;
			top: 0;
			transition-property: left, right;
			transition-duration: .2s;
			-moz-box-shadow: 0 0 4px #4e3c3c;
			-webkit-box-shadow: 0 0 4px #4e3c3c;
			box-shadow: 0 0 4px #4e3c3c;
			z-index: 999999;
			width: 250px;
		}
		.fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout ul {
			margin: 0 auto;
		}
		.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-mobile-flyout .menu {
			display: block !important;
			float: none;
		}
		.admin-bar .fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout {
			top: 32px;
		}

		<?php if ( 'flyout-push-opacity' == $settings->mobile_full_width ) : ?>
		.fl-menu-mobile-opacity {
			display: none;
			position: fixed;
			width: 100%;
			height: 100%;
			top: 0;
			left: 0;
			right: 0;
			bottom: 0;
			background-color: rgba(0,0,0,0.4);
			z-index: 100;
			cursor: pointer;
		}
		<?php endif; ?>

		.fl-menu-mobile-close {
			display: block;
		}
		.fl-flyout-right .fl-menu-mobile-close {
			float: left;
		}
		.fl-flyout-left .fl-menu-mobile-close {
			float: right;
		}

	<?php endif; ?>

	<?php if ( ( isset( $settings->mobile_full_width ) && 'no' != $settings->mobile_full_width ) && ( isset( $settings->mobile_toggle ) && 'expanded' != $settings->mobile_toggle ) ) : ?>

		<?php if ( 'yes' == $settings->mobile_full_width ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .menu {
				position: absolute;
				left: <?php echo empty( $settings->margin_left ) ? $global_settings->module_margins : $settings->margin_left; ?>px;
				right: <?php echo empty( $settings->margin_right ) ? $global_settings->module_margins : $settings->margin_right; ?>px;
				z-index: 1500;
			}
		<?php endif; ?>

		<?php if ( ! empty( $settings->mobile_menu_bg ) ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .menu {
				background-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->mobile_menu_bg ); ?>;
			}
		<?php endif; ?>

	<?php endif; ?>

	<?php if ( 'expanded' != $settings->mobile_toggle ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu ul.menu {
			display: none;
		}
	<?php endif; ?>

	.fl-menu-horizontal {
		text-align: left;
	}

	.fl-node-<?php echo $id; ?> .fl-menu .sub-menu {
		background-color: transparent;
		-webkit-box-shadow: none;
		-ms-box-shadow: none;
		box-shadow: none;
	}

	.fl-node-<?php echo $id; ?> .mega-menu.fl-active .hide-heading > .sub-menu,
	.fl-node-<?php echo $id; ?> .mega-menu-disabled.fl-active .hide-heading > .sub-menu {
		display: block !important;
	}

<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
	} <?php // close media max-width ?>

	<?php if ( $module->is_responsive_menu_flyout() ) : ?>
		@media ( max-width: 782px ) {
			.admin-bar .fl-node-<?php echo $id; ?> .fl-menu-mobile-flyout {
				top: 46px;
			}
		}
	<?php endif; ?>
<?php endif; ?>

<?php if ( 'always' != $module->get_media_breakpoint() ) : ?>
@media ( min-width: <?php echo ( $module->get_media_breakpoint() ) + 1; ?>px ) {

	<?php // if menu is horizontal ?>
	<?php if ( 'horizontal' == $settings->menu_layout ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu .menu > li{ display: inline-block; }

		.fl-node-<?php echo $id; ?> .menu li{
			border-left: 1px solid transparent;
			border-top: none;
		}

		.fl-node-<?php echo $id; ?> .menu li:first-child{
			border: none;
		}
		.fl-node-<?php echo $id; ?> .menu li li{
			border-top: 1px solid transparent;
			border-left: none;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
			position: absolute;
			top: 100%;
			left: 0;
			z-index: 10;
			visibility: hidden;
			opacity: 0;
			text-align:left;
		}

		.fl-node-<?php echo $id; ?> .fl-has-submenu .fl-has-submenu .sub-menu{
			top: 0;
			left: 100%;
		}

		<?php // if menu is vertical ?>
	<?php elseif ( 'vertical' == $settings->menu_layout ) : ?>

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .sub-menu{
			position: absolute;
			top: 0;
			left: 100%;
			z-index: 10;
			visibility: hidden;
			opacity: 0;
		}

	<?php endif; ?>

	<?php // if menu is horizontal or vertical ?>
	<?php if ( in_array( $settings->menu_layout, array( 'horizontal', 'vertical' ) ) ) : ?>

		.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu:hover > .sub-menu,
		.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.focus > .sub-menu{
			display: block;
			visibility: visible;
			opacity: 1;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu.fl-menu-submenu-right .sub-menu{
			left: inherit;
			right: 0;
		}

		.fl-node-<?php echo $id; ?> .menu .fl-has-submenu .fl-has-submenu.fl-menu-submenu-right .sub-menu{
			top: 0;
			left: inherit;
			right: 100%;
		}

		.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu.fl-active > .fl-has-submenu-container .fl-menu-toggle{
			-webkit-transform: none;
				-ms-transform: none;
					transform: none;
		}

		<?php //change selector depending on layout ?>
		<?php if ( 'arrows' == $settings->submenu_hover_toggle ) : ?>
			<?php if ( 'horizontal' == $settings->menu_layout ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu .fl-has-submenu .fl-menu-toggle:before{
			<?php elseif ( 'vertical' == $settings->menu_layout ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .fl-has-submenu .fl-menu-toggle:before{
			<?php endif; ?>
				-webkit-transform: translateY( -5px ) rotate( -45deg );
					-ms-transform: translateY( -5px ) rotate( -45deg );
						transform: translateY( -5px ) rotate( -45deg );
			}
		<?php endif; ?>

		<?php if ( 'none' == $settings->submenu_hover_toggle ) : ?>
			.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-toggle{
				display: none;
			}
		<?php endif; ?>

		.fl-node-<?php echo $id; ?> ul.sub-menu {
			<?php if ( '' !== $settings->submenu_spacing_top ) : ?>
			padding-top: <?php echo $settings->submenu_spacing_top . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_spacing_right ) : ?>
			padding-right: <?php echo $settings->submenu_spacing_right . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_spacing_bottom ) : ?>
			padding-bottom: <?php echo $settings->submenu_spacing_bottom . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_spacing_left ) : ?>
			padding-left: <?php echo $settings->submenu_spacing_left . $settings->submenu_spacing_unit; ?>;
			<?php endif; ?>
		}

		.fl-node-<?php echo $id; ?> ul.sub-menu a {
			<?php if ( '' !== $settings->submenu_link_spacing_top ) : ?>
			padding-top: <?php echo $settings->submenu_link_spacing_top . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_link_spacing_right ) : ?>
			padding-right: <?php echo $settings->submenu_link_spacing_right . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_link_spacing_bottom ) : ?>
			padding-bottom: <?php echo $settings->submenu_link_spacing_bottom . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
			<?php if ( '' !== $settings->submenu_link_spacing_left ) : ?>
			padding-left: <?php echo $settings->submenu_link_spacing_left . $settings->submenu_link_spacing_unit; ?>;
			<?php endif; ?>
		}

	<?php endif; ?>

	<?php if ( 'expanded' != $settings->mobile_toggle ) : ?>
		.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle{
			display: none;
		}
	<?php endif; ?>
}
	<?php
endif;

/**
 * Mobile toggle button
 */
if ( isset( $settings->mobile_toggle ) && 'expanded' != $settings->mobile_toggle ) :
	?>
	<?php if ( 'horizontal' == $settings->menu_layout && ! empty( $settings->menu_align ) ) : ?>
		<?php
		FLBuilderCSS::responsive_rule( array(
			'settings'     => $settings,
			'setting_name' => 'menu_align',
			'selector'     => ".fl-node-$id .fl-menu-mobile-toggle",
			'prop'         => 'float',
			'ignore'       => array( 'center' ),
		) );

		FLBuilderCSS::responsive_rule( array(
			'settings'     => $settings,
			'setting_name' => 'menu_align',
			'selector'     => ".fl-node-$id .fl-menu-mobile-toggle",
			'props'        => array(
				'float' => 'none',
			),
			'ignore'       => array( 'left', 'right' ),
		) );

		?>
	<?php endif; ?>

	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle{
		<?php
		if ( isset( $settings->typography['font_size'] ) ) {
			echo 'font-size: ' . $settings->typography['font_size']['length'] . $settings->typography['font_size']['unit'] . ';';
		}
		if ( isset( $settings->typography['text_transform'] ) ) {
			echo 'text-transform: ' . $settings->typography['text_transform'] . ';';
		}
		if ( ! empty( $settings->link_color ) ) {
			echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->link_color ) . ';';
		}
		if ( ! empty( $settings->menu_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->menu_bg_color ) . ';';
		}

		?>
		padding-left: <?php echo ! empty( $settings->link_spacing_left ) ? $settings->link_spacing_left . $settings->link_spacing_unit : '0'; ?>;
		padding-right: <?php echo ! empty( $settings->link_spacing_right ) ? $settings->link_spacing_right . $settings->link_spacing_unit : '0'; ?>;
		padding-top: <?php echo ! empty( $settings->link_spacing_top ) ? $settings->link_spacing_top . $settings->link_spacing_unit : '0'; ?>;
		padding-bottom: <?php echo ! empty( $settings->link_spacing_bottom ) ? $settings->link_spacing_bottom . $settings->link_spacing_unit : '0'; ?>;
		border-color: rgba( 0,0,0,0.1 );
	}
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle rect{
		<?php
		if ( ! empty( $settings->link_color ) ) {
			echo 'fill: ' . FLBuilderColor::hex_or_rgb( $settings->link_color ) . ';';
		}
		?>
	}
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle:hover,
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle.fl-active{
		<?php
		if ( ! empty( $settings->link_hover_color ) ) {
			echo 'color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_color ) . ';';
		}
		if ( ! empty( $settings->link_hover_bg_color ) ) {
			echo 'background-color: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_bg_color ) . ';';
		}
		?>
	}

	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle:hover rect,
	.fl-node-<?php echo $id; ?> .fl-menu-mobile-toggle.fl-active rect{
		<?php
		if ( ! empty( $settings->link_hover_color ) ) {
			echo 'fill: ' . FLBuilderColor::hex_or_rgb( $settings->link_hover_color ) . ';';
		}
		?>
	}
	<?php
endif;

if ( isset( $settings->mobile_button_label ) && 'no' == $settings->mobile_button_label ) :
	?>
	.fl-node-<?php echo $id; ?> .fl-menu .fl-menu-mobile-toggle.hamburger .fl-menu-mobile-toggle-label{
		display: none;
	}
	<?php
endif;

/**
 * Mega menus
 */
?>
.fl-node-<?php echo $id; ?> ul.fl-menu-horizontal li.mega-menu > ul.sub-menu > li > .fl-has-submenu-container a:hover {
	color: <?php echo FLBuilderColor::hex_or_rgb( $settings->link_color ); ?>;
}
