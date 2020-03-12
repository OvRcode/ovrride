(function($){

	FLBuilder.registerModuleHelper('callout', {

		init: function()
		{
			var form        = $('.fl-builder-settings'),
				imageType   = form.find('select[name=image_type]'),
				ctaType     = form.find('select[name=cta_type]'),
				titleSize   = form.find('select[name=title_size]'),
				align       = form.find('select[name=align]');

			// Preview events.
			align.on('change', this._previewAlign);

			// Button background color change
			$( 'input[name=btn_bg_color]' ).on( 'change', this._bgColorChange );
			this._bgColorChange();
		},

		_previewAlign: function()
		{
			var form   = $('.fl-builder-settings'),
				align  = form.find('select[name=align]').val(),
				wrap   = FLBuilder.preview.elements.node.find('.fl-callout');

			wrap.removeClass('fl-callout-left');
			wrap.removeClass('fl-callout-center');
			wrap.removeClass('fl-callout-right');
			wrap.addClass('fl-callout-' + align);
		},

		_bgColorChange: function()
		{
			var bgColor = $( 'input[name=btn_bg_color]' ),
				style   = $( '#fl-builder-settings-section-btn_style' );

			if ( '' == bgColor.val() ) {
				style.hide();
			}
			else {
				style.show();
			}
		}
	});

})(jQuery);
