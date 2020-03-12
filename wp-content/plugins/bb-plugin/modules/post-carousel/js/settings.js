(function($){

	FLBuilder.registerModuleHelper('post-carousel', {

		init: function()
		{
			var form   = $('.fl-builder-settings'),
				layout = form.find('select[name=layout]');

			layout.on('change', this._fixfeatured);
		},

		_fixfeatured: function()
		{
			var form   = $('.fl-builder-settings'),
				image  = form.find('select[name=show_image]'),
				layout = form.find('select[name=layout]')

				if( 'gallery' === layout.val() ) {
					image.val('1')
					image.hide()
					form.find('label[for=show_image]').hide()
					$('#fl-field-image_size').show()
					$('#fl-field-crop').show()
				} else {
					form.find('label[for=show_image]').show()
					form.find('select[name=show_image]').show()
					$('#fl-field-image_size').show()
					$('#fl-field-crop').show()
				}
		}
	});

})(jQuery);
