( function( $ ) {

	FLBuilder.registerModuleHelper( 'icon-group', {

		init: function() {
			var form  = $( '.fl-builder-settings' ),
				size = form.find( '#fl-field-size input[type=number]' ),
				spacing = form.find( 'input[name=spacing]' ),
				align = form.find( 'input[name=align]' );

			size.on( 'input', this._previewSize );
			spacing.on( 'input', this._previewSpacing );
			align.on( 'change', this._previewAlign );
		},

		_previewSize: function() {
			var preview = FLBuilder.preview,
				wrapSelector = preview._getPreviewSelector( preview.classes.node, '.fl-icon' ),
				iconSelector = preview._getPreviewSelector( preview.classes.node, '.fl-icon i' ),
				beforeSelector = preview._getPreviewSelector( preview.classes.node, '.fl-icon i::before' ),
				form = $( '.fl-builder-settings' ),
				field = form.find( '#fl-field-size .fl-field-responsive-setting:visible' ),
				size = field.find( 'input[type=number]' ).val(),
				unit = field.find( 'select' ).val(),
				icons = form.find( '#fl-field-icons .fl-form-field input' ),
				value = '' === size ? '' : size + unit + ' !important',
				height = '' === size ? '' : ( size * 1.75 ) + unit + ' !important',
				index = 0,
				settings = null,
				bgColor = '';

			preview.updateCSSRule( iconSelector, 'font-size', value, true );
			preview.updateCSSRule( beforeSelector, 'font-size', value, true );

			icons.each( function( i ) {
				index = i + 1;
				settings = JSON.parse( $( this ).val() )

				if ( '' === settings.bg_color ) {
					preview.updateCSSRule( wrapSelector + ':nth-child(' + ( index ) +') i', {
						'line-height': '1',
						'height': 'auto !important',
						'width': 'auto !important',
					}, undefined, true );
				} else {
					preview.updateCSSRule( wrapSelector + ':nth-child(' + ( index ) +') i', {
						'line-height': height,
						'height': height,
						'width': height,
					}, undefined, true );
				}
			} );
		},

		_previewSpacing: function() {
			var icons = FLBuilder.preview.elements.node.find( '.fl-icon' ),
				form = $( '.fl-builder-settings' ),
				spacing = form.find( 'input[name=spacing]' ).val(),
				align = form.find( 'input[name=align]' ).val(),
				margin = '';

			if ( 'left' === align ) {
				margin = '10px ' + spacing + 'px 10px 0';
			} else if ( 'center' === align ) {
				margin = '10px ' + spacing + 'px';
			} else if ( 'right' === align ) {
				margin = '10px 0 10px ' + spacing + 'px';
			}

			icons.css( 'margin', margin );
		},

		_previewAlign: function() {
			var node = FLBuilder.preview.elements.node.find( '.fl-icon-group' ),
				form = $( '.fl-builder-settings' ),
				spacing = form.find( 'input[name=spacing]' ),
				align = form.find( 'input[name=align]' ).val();

			node.removeClass( 'fl-icon-group-left' );
			node.removeClass( 'fl-icon-group-center' );
			node.removeClass( 'fl-icon-group-right' );
			node.addClass( 'fl-icon-group-' + align );

			spacing.trigger( 'input' );
		},
	});

} )( jQuery );
