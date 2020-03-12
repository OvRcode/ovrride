(function($){

	FLBuilder.registerModuleHelper( 'photo', {

		init: function() {
			var form   		= $( '.fl-builder-settings' ),
				source 		= form.find( 'select[name=photo_source]' ),
				attachment 	= form.find( 'select[name=photo_src]' ),
				url 		= form.find( 'input[name=photo_url]' ),
				showCaption = form.find( 'select[name=show_caption]' ),
				caption 	= form.find( 'input[name=caption]' );

			this._sourceChanged();

			source.on( 'change', this._sourceChanged );
			source.on( 'change', this._previewImage );
			source.on( 'change', this._previewCaption );
			attachment.on( 'change', this._previewImage );
			url.on( 'keyup', this._previewImage );
			showCaption.on( 'change', this._previewCaption );
			caption.on( 'keyup', this._previewCaption );
		},

		_sourceChanged: function() {
			var form     = $( '.fl-builder-settings' ),
				source 	 = form.find( 'select[name=photo_source]' ).val(),
				linkType = form.find( 'select[name=link_type]' );

			linkType.find( 'option[value=page]' ).remove();

			if( source === 'library' ) {
				linkType.append( '<option value="page">' + FLBuilderStrings.photoPage + '</option>' );
			}
		},

		_previewImage: function( e ) {
			var preview		= FLBuilder.preview,
				node		= preview.elements.node,
				img			= null,
				form        = $( '.fl-builder-settings' ),
				source 		= form.find( 'select[name=photo_source]' ).val(),
				attachment 	= form.find( 'select[name=photo_src]' ),
				url 		= form.find( 'input[name=photo_url]' ),
				crop 		= form.find( 'select[name=crop]' ).val();

			if ( '' === crop ) {
				img = node.find( '.fl-photo-img' );
				img.show();
				img.removeAttr( 'height' );
				img.removeAttr( 'width' );
				img.removeAttr( 'srcset' );
				img.removeAttr( 'sizes' );
				if ( 'library' === source ) {
					img.attr( 'src', attachment.val() );
				} else {
					img.attr( 'src', url.val() );
				}
			} else {
				preview.delayPreview( e );
			}
		},

		_previewCaption: function( e ) {
			var attachments = FLBuilderSettingsConfig.attachments,
				preview		= FLBuilder.preview,
				node		= preview.elements.node,
				form    	= $( '.fl-builder-settings' ),
				source 		= form.find( 'select[name=photo_source]' ).val(),
				id 			= form.find( 'input[name=photo]' ).val(),
				show		= form.find( 'select[name=show_caption]' ).val(),
				content		= node.find( '.fl-photo-content' ),
				container   = node.find( '.fl-photo-caption-below' ),
				caption 	= '';

			if ( '0' === show || 'hover' === show ) {
				node.find( '.fl-photo-caption' ).remove();
				return;
			}

			if ( 0 === container.length ) {
				content.append( '<div class="fl-photo-caption fl-photo-caption-below"></div>' );
				container = node.find( '.fl-photo-caption-below' );
			}

			if ( 'library' === source && attachments[ id ] && attachments[ id ].caption ) {
				caption = attachments[ id ].caption;
			} else if ( 'url' === source ) {
				caption = form.find( 'input[name=caption]' ).val();
			}

			container.html( caption );
		}
	} );

} )( jQuery );
