( function( $ ) {

	/**
	 * Helper for handling responsive preview in an iframe.
	 *
	 * @since 2.0.6
	 * @class FLBuilderResponsivePreview
	 */
	FLBuilderResponsivePreview = {

		/**
		 * Enters responsive preview mode.
		 *
		 * @since 2.0.6
		 * @method enter
		 */
		enter: function() {
			this.render();
		},

		/**
		 * Exits responsive preview mode.
		 *
		 * @since 2.0.6
		 * @method exit
		 */
		exit: function() {
			this.destroy();
		},

		/**
		 * Switch to a different device preview size.
		 *
		 * @since 2.0.6
		 * @param {String} mode
		 * @method switchTo
		 */
		switchTo: function( mode ) {
			var settings = FLBuilderConfig.global,
				frame	 = $( '#fl-builder-preview-frame' ),
				width 	 = '100%';

			if ( 'responsive' == mode ) {
				width = settings.responsive_breakpoint >= 360 ? 360 : settings.responsive_breakpoint;
				frame.width( width );
			} else if ( 'medium' == mode ) {
				width = settings.medium_breakpoint >= 769 ? 769 : settings.medium_breakpoint;
				frame.width( width );
			}

			frame.width( width );
		},

		/**
		 * Renders the iframe for previewing the layout.
		 *
		 * @since 2.0.6
		 * @method render
		 */
		render: function() {
			var body	= $( 'body' ),
				src 	= FLBuilderConfig.previewUrl,
				last	= $( '#fl-builder-preview-mask, #fl-builder-preview-frame' ),
				mask	= $( '<div id="fl-builder-preview-mask"></div>' ),
				frame 	= $( '<iframe id="fl-builder-preview-frame" src="' + src + '"></iframe>' );

			last.remove();
			body.append( mask );
			body.append( frame );
			body.css( 'overflow', 'hidden' );
		},

		/**
		 * Removes the iframe for previewing the layout.
		 *
		 * @since 2.0.6
		 * @method destroy
		 */
		destroy: function() {
			$( '#fl-builder-preview-mask, #fl-builder-preview-frame' ).remove();
			$( 'body' ).css( 'overflow', 'visible' );
		},
	}
} )( jQuery );
