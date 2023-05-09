/**
 * Meta Slider
 */
(function ($) {
	$(function () {
		function vimeoVidId( url ) {
		  // look for a string with 'vimeo', then whatever, then a 
		  // forward slash and a group of digits.
		  var match = /vimeo.*\/(\d+)/i.exec( url );

		  // if the match isn't null (i.e. it matched)
		  if ( match ) {
		    // the grouped/matched digits from the regex
		    return match[1];
		  }

		  return false;
		}

		var showSpinner = function() {
			$('.spinner').show().delay(1000).fadeOut('fast');
		}

		$('.vimeo_url').each(function() {
			var elem = $(this);

			// Save current value of element
			elem.data('oldVal', elem.val());
			// Look for changes in the value
			elem.bind("propertychange keyup input paste", function(event){
				// If value has changed...
				if (elem.data('oldVal') != elem.val()) {
					showSpinner();
					// Updated stored value
					elem.data('oldVal', elem.val());

					var vimeo_id = vimeoVidId(elem.val());

					if (vimeo_id) {
						$('.embed-link-settings').html("<iframe src='//player.vimeo.com/video/" + vimeo_id + "?title=0&portait=0&byline=0' frameborder='0'></iframe>");
						$('.media-button').removeAttr('disabled');
					}
				}
			});
		});

		$('.media-button').live('click', function(e) {
			e.preventDefault();
			var vimeo_id = vimeoVidId($('.vimeo_url').val());

			if (vimeo_id) {				
				var data = {
					action: 'create_vimeo_slide',
					video_id: vimeo_id,
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