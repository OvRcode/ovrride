(function($){

	FLBuilder.registerModuleHelper('content-slider', {

		submit: function() {
			var form   = $('.fl-builder-settings'),
			transition = parseInt( form.find('input[name=speed]').val() ),
			delay      = parseInt( form.find('input[name=delay]').val() )

			if ( transition > delay ) {
				FLBuilder.alert( FLBuilderStrings.contentSliderTransitionWarn )
				return false;
			}
			return true;
		}
	})

	FLBuilder.registerModuleHelper('content_slider_slide', {

		init: function()
		{
			var form          = $('.fl-form-field-settings'),
				bgLayout      = form.find('select[name=bg_layout]'),
				contentLayout = form.find('select[name=content_layout]');

			bgLayout.on('change', this._toggleMobileTab);
			bgLayout.on('change', this._toggleTextAndCtaTabs);
			contentLayout.on('change', this._toggleMobileTab);
			contentLayout.on('change', this._toggleTextAndCtaTabs);
			contentLayout.trigger('change');
		},

		submit: function()
		{
			var form          = $('.fl-builder-settings'),
				bgLayout      = form.find('select[name=bg_layout]').val(),
				contentLayout = form.find('select[name=content_layout]').val();

			if(bgLayout == 'none' && contentLayout == 'none') {
				FLBuilder.alert(FLBuilderStrings.contentSliderSelectLayout);
				return false;
			}

			return true;
		},

		_toggleTextAndCtaTabs: function()
		{
			var form          = $('.fl-builder-settings'),
				bgLayout      = form.find('select[name=bg_layout]').val(),
				contentLayout = form.find('select[name=content_layout]').val(),
				show          = true;

			if(bgLayout == 'video' || contentLayout == 'none') {
				show = false;
			}

			if(show) {
				$('[data-form-id=content_slider_slide] a[href*=fl-builder-settings-tab-style]').show();
				$('a[href*=fl-builder-settings-tab-cta]').show();
			}
			else {
				$('[data-form-id=content_slider_slide] a[href*=fl-builder-settings-tab-style]').hide();
				$('a[href*=fl-builder-settings-tab-cta]').hide();
			}
		},

		_toggleMobileTab: function()
		{
			var form          = $('.fl-builder-settings'),
				bgLayout      = form.find('select[name=bg_layout]').val(),
				contentLayout = form.find('select[name=content_layout]').val(),
				show          = true,
				showPhoto     = true,
				showText      = true;

			// Hide or show tab.
			if(bgLayout == 'video' || (bgLayout != 'photo' && contentLayout == 'none')) {
				show = false;
			}

			if(show) {
				$('a[href*=fl-builder-settings-tab-mobile]').show();
			}
			else {
				$('a[href*=fl-builder-settings-tab-mobile]').hide();
			}

			// Hide or show text.
			if(contentLayout == 'none') {
				showText = false;
			}

			if(showText) {
				$('#fl-builder-settings-section-r_text_style').show();
			}
			else {
				$('#fl-builder-settings-section-r_text_style').hide();
			}

			// Hide or show photos.
			if(bgLayout != 'photo' && contentLayout != 'photo') {
				showPhoto = false;
			}

			if(showPhoto) {
				$('#fl-builder-settings-section-r_photo').show();
			}
			else {
				$('#fl-builder-settings-section-r_photo').hide();
			}
		},
	});

})(jQuery);
