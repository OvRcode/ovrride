( function( $ ) {

	FLBuilder.registerModuleHelper( 'icon', {

		init: function() {
			var form  = $( '.fl-builder-settings' ),
				icon = form.find( 'input[name=icon]' ),
				size = form.find( '#fl-field-size input[type=number]' ),
				text = form.find( '[data-name="text"] textarea.wp-editor-area' ),
				editorId = text.attr( 'id' );

			icon.on( 'change', this._previewIcon );
			size.on( 'input', this._previewSize );
			text.on( 'keyup', this._previewText );

			if ( 'undefined' !== typeof tinyMCE ) {
				var editor = tinyMCE.get( editorId );
				editor.on( 'change', this._previewText );
				editor.on( 'keyup', this._previewText );
			}
		},

		_previewIcon: function() {
			var ele = FLBuilder.preview.elements.node.find( '.fl-icon i' ),
				form  = $( '.fl-builder-settings' ),
				icon = form.find( 'input[name=icon]' );

			ele.attr( 'class', icon.val() );
		},

		_previewSize: function() {
			var preview = FLBuilder.preview,
				iconSelector = preview._getPreviewSelector( preview.classes.node, '.fl-icon i' ),
				beforeSelector = preview._getPreviewSelector( preview.classes.node, '.fl-icon i::before' ),
				textSelector = preview._getPreviewSelector( preview.classes.node, '.fl-icon-text' ),
				form = $( '.fl-builder-settings' ),
				field = form.find( '#fl-field-size .fl-field-responsive-setting:visible' ),
				size = field.find( 'input[type=number]' ).val(),
				unit = field.find( 'select' ).val(),
				bgColor = form.find( 'input[name=bg_color]' ).val(),
				value = '' === size ? '' : size + unit + ' !important',
				height = '' === size ? '' : ( size * 1.75 ) + unit + ' !important';

			preview.updateCSSRule( iconSelector, 'font-size', value, true );
			preview.updateCSSRule( beforeSelector, 'font-size', value, true );
			preview.updateCSSRule( textSelector, 'height', height, true );

			if ( '' === bgColor ) {
				preview.updateCSSRule( iconSelector, {
					'line-height': '1',
					'height': 'auto !important',
					'width': 'auto !important',
				}, undefined, true );
			} else {
				preview.updateCSSRule( iconSelector, {
					'line-height': height,
					'height': height,
					'width': height,
				}, undefined, true );
			}
		},

		_previewText: function() {
			var ele = FLBuilder.preview.elements.node.find( '.fl-icon-text' ),
				form = $( '.fl-builder-settings' ),
				text = form.find( '[data-name="text"] textarea.wp-editor-area' ),
				editorId = text.attr( 'id' ),
				editor = 'undefined' !== typeof tinyMCE ? tinyMCE.get( editorId ) : null,
				value = '';

			if ( editor && 'none' === text.css( 'display' ) ) {
				value = editor.getContent();
			} else {
				value = text.val();
			}

			if ( '' === value ) {
				ele.addClass( 'fl-icon-text-empty' );
			} else {
				ele.removeClass( 'fl-icon-text-empty' );
			}
		},
	});

} )( jQuery );
