/**
 * MetaSlider
 */

(function ($) {
	$(function () {
		$('body').on('click', '.media-button', function(e) {
			e.preventDefault();

			var APP = window.parent.metaslider.app.MetaSlider;
			// APP comes from the free version which holds some generic translations
			APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
				APP.__('Preparing %s slide...','ml-slider'),
			'1'), true);

			var data = {
				action: 'create_post_feed_slide',
				slider_id: window.parent.metaslider_slider_id,
				nonce: metaslider_custom_slide_type.nonce
			};

			jQuery.post(ajaxurl, data, function(response) {
				window.parent.jQuery(".metaslider table#metaslider-slides-list").append(response);
				var APP = window.parent.metaslider.app.MetaSlider;
				APP && APP.notifySuccess('metaslider/slides-created', null, true);
				window.parent.jQuery(".media-modal-close").click();
			});
		});
	});
}(jQuery));
