/**
 * Meta Slider
 */
(function ($) {
	$(function () {
		$('.media-button').live('click', function(e) {
			e.preventDefault();

         var data = {
            action: 'create_' + metaslider_custom_slide_type.identifier + '_slide',
            slider_id: window.parent.metaslider_slider_id
         };

         jQuery.post(ajaxurl, data, function(response) {
            window.parent.jQuery(".metaslider .left table").append(response);
            window.parent.jQuery(".media-modal-close").click();
         });
		});
	});
}(jQuery));