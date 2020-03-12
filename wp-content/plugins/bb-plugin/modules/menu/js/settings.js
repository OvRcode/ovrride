( function( $ ) {

	FLBuilder.addHook( 'didRenderLayoutJSComplete', function() {
		FLBuilder._moduleHelpers.menu._previewSubmenu();
	} );

	FLBuilder.addHook( 'didHideAllLightboxes', function() {
		FLBuilder._moduleHelpers.menu._closeSubmenuPreview();
	} );

	FLBuilder.addHook( 'didShowLightbox', function() {
		FLBuilder._moduleHelpers.menu._closeSubmenuPreview();
	} );

	FLBuilder.registerModuleHelper( 'menu', {

		init: function() {
			var form = $( '.fl-builder-menu-settings:visible' ),
				submenuBg = form.find( 'input[name=submenu_bg_color]' ),
				submenuShadow = form.find( 'select[name=drop_shadow]' ),
				submenuSpacing = form.find( '#fl-field-submenu_spacing input' ),
				submenuLinkSpacing = form.find( '#fl-field-submenu_link_spacing input' );

			submenuBg.on( 'change', this._previewSubmenu );
			submenuShadow.on( 'change', this._previewSubmenu );
			submenuSpacing.on( 'input', this._previewSubmenu );
			submenuLinkSpacing.on( 'input', this._previewSubmenu );
		},

		_previewSubmenu: function() {
			var form = $( '.fl-builder-menu-settings:visible' );
			var preview = FLBuilder.preview;

			if ( ! form.length || ! preview || ! preview.elements.node ) {
				return;
			}

			var node = preview.elements.node;

			if ( node.hasClass( 'fl-submenu-preview' ) ) {
				return;
			}

			var parent = node.find( '.fl-menu-horizontal .fl-has-submenu, .fl-menu-vertical .fl-has-submenu' ).eq( 0 );

			node.addClass( 'fl-submenu-preview' )
			parent.find( '.fl-has-submenu-container a' ).focus();
			parent.addClass( 'focus' );
		},

		_closeSubmenuPreview: function() {
			$( '.fl-submenu-preview' ).removeClass( 'fl-submenu-preview' );
			$( '.fl-module-menu .fl-has-submenu.focus' ).removeClass( 'focus' );
		},
	} );

} )( jQuery );
