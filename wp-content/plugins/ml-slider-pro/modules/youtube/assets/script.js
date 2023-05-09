/**
 * Meta Slider
 */
(function ($) {
	$(function () {
		/**
		 * JavaScript function to match (and return) the video Id 
		 * of any valid Youtube Url, given as input string.
		 * @author: Stephan Schmitz <eyecatchup@gmail.com>
		 * @url: http://stackoverflow.com/a/10315969/624466
		 */
		var ytVidId = function(url) {
		    var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
		    return (url.match(p)) ? RegExp.$1 : false;
		}

		var showSpinner = function() {
			$('.spinner').show().delay(1000).fadeOut('fast');
		}

		$('.youtube_url').each(function() {
			var elem = $(this);

			// Save current value of element
			elem.data('oldVal', elem.val());
			// Look for changes in the value
			elem.bind("propertychange keyup input paste", function(event){
				// If value has changed...
				if (elem.data('oldVal') != elem.val()) {
					showSpinner();
					$('.media-button').attr('disabled', 'disabled');
					// Updated stored value
					elem.data('oldVal', elem.val());

					var yt_id = ytVidId(elem.val());

					if (yt_id) {
						$('.embed-link-settings').html("<iframe src='//www.youtube.com/embed/" + yt_id + "?HD=1;rel=0;showinfo=0' frameborder='0'></iframe>");
						$('.media-button').removeAttr('disabled');
					}
				}
			});
		});

		$('.media-button').live('click', function(e) {
			e.preventDefault();

			var yt_id = ytVidId($('.youtube_url').val());

			if (yt_id) {
				var data = {
					action: 'create_youtube_slide',
					video_id: yt_id,
					slider_id: window.parent.metaslider_slider_id
				};

				jQuery.post(ajaxurl, data, function(response) {
					window.parent.jQuery(".metaslider .left table").append(response);
					window.parent.jQuery(".media-modal-close").click();
				});
			}
		});
	});
}(jQuery));