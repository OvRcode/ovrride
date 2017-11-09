jQuery(document).ready(function() {

	jQuery('#remove_custom_prev_arrow').on('click', function(event){
		event.preventDefault();
		jQuery("#custom_prev_arrow").html("");
		jQuery("#custom_prev_arrow_input").val("0");
		jQuery("#open_media_manager_prev").show();
		jQuery("#remove_custom_prev_arrow").hide();
	});

	jQuery('#open_media_manager_prev').on('click', function(event){
		event.preventDefault();

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			multiple: false,
			frame: 'post',
			library: {type: 'image'}
		});

		// When an image is selected, run a callback.
		file_frame.on('insert', function() {

			var selection = file_frame.state().get('selection');
			var slide_ids = [];

			selection.map(function(attachment) {
				attachment = attachment.toJSON();

				if (attachment.height > 100 || attachment.width > 100) {
					return alert('Image too large. Max dimensions: 100px x 100px.');
				}

				jQuery("#custom_prev_arrow").html("<img style='max-width: 100px' src= " + attachment.url + ">");
				jQuery("#custom_prev_arrow_input").val(attachment.id);
				jQuery("#open_media_manager_prev").hide();
				jQuery("#remove_custom_prev_arrow").show();
			});
		});

		file_frame.open();
	});

	jQuery('#remove_custom_next_arrow').on('click', function(event){
		event.preventDefault();
		jQuery("#custom_next_arrow").html("");
		jQuery("#custom_next_arrow_input").val("0");
		jQuery("#open_media_manager_next").show();
		jQuery("#remove_custom_next_arrow").hide();
	});

	jQuery('#open_media_manager_next').on('click', function(event){
		event.preventDefault();

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			multiple: false,
			frame: 'post',
			library: {type: 'image'}
		});

		// When an image is selected, run a callback.
		file_frame.on('insert', function() {

			var selection = file_frame.state().get('selection');
			var slide_ids = [];

			selection.map(function(attachment) {
				attachment = attachment.toJSON();

				if (attachment.height > 100 || attachment.width > 100) {
					return alert('Image too large. Max dimensions: 100px x 100px.');
				}

				jQuery("#custom_next_arrow").html("<img style='max-width: 100px' src= " + attachment.url + ">");
				jQuery("#custom_next_arrow_input").val(attachment.id);
				jQuery("#open_media_manager_next").hide();
				jQuery("#remove_custom_next_arrow").show();
			});
		});

		file_frame.open();
	});

    // show the confirm dialogue
    jQuery(".confirm").live('click', function() {
        return confirm("Are you sure?");
    });
	/**
	 *
	 */
	var applyBulletStylingToPreview = function() {
		var bullets = jQuery(".flex-control-nav a, .nivo-controlNav a, .rslides_tabs li a, .cs-buttons a");
		var activeBullets = jQuery(".flex-control-nav li a.flex-active, .nivo-controlNav a.active, .rslides_tabs li.rslides_here a, .cs-buttons a.cs-active");

		if (jQuery('#enable_custom_navigation').is(':checked')) {
			var start = jQuery("#colourpicker-fill-start").spectrum("get").toRgbString();
		    var end = jQuery("#colourpicker-fill-end").spectrum("get").toRgbString();
			var borderColour = jQuery("#colourpicker-border-colour").spectrum("get").toRgbString();

			bullets
				.css('padding', '0')
				.css('box-shadow', 'none')
				.css('text-indent', '-9999px')
			    .css('border-style', 'solid')
			    .css('border-color', borderColour)
			    .css('border-radius', jQuery('#theme_dot_border_radius').val() + 'px')
		        .css('border-width', jQuery('#theme_dot_border_width').val() + 'px')
			    .css('width', jQuery('#theme_dot_size').val() + 'px')
		        .css('height', jQuery('#theme_dot_size').val() + 'px')
		        .css('margin-left', jQuery('#theme_dot_spacing').val() + 'px')
		        .css('margin-right', jQuery('#theme_dot_spacing').val() + 'px')
			    .css('background', '-webkit-gradient(linear, 0% 0%, 0% 100%, from(' + start + '), to(' + end + '))')
			    .css('background', '-webkit-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('background', '-moz-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('background', '-ms-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('background', '-o-linear-gradient(top, ' + start + ', ' + end + ')');

			var start = jQuery("#colourpicker-active-fill-start").spectrum("get").toRgbString();
		    var end = jQuery("#colourpicker-active-fill-end").spectrum("get").toRgbString();
			var activeBorderColour = jQuery("#colourpicker-active-border-colour").spectrum("get").toRgbString();

			activeBullets
			    .css('background', '-webkit-gradient(linear, 0% 0%, 0% 100%, from(' + start + '), to(' + end + '))')
			    .css('background', '-webkit-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('background', '-moz-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('background', '-ms-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('background', '-o-linear-gradient(top, ' + start + ', ' + end + ')')
			    .css('border-color', activeBorderColour);
		} else {
			bullets.css('cssText','');
			activeBullets.css('cssText','');
		}
	}

	/**
	 *
	 */
	var applyBulletPositioningToPreview = function() {
		var bulletContainers = jQuery(".flex-control-nav, .nivo-controlNav, .rslides_tabs, .cs-buttons");

		if (jQuery('#enable_custom_navigation').is(':checked')) {
			jQuery('.metaslider').css('margin-bottom', '0px');


		    var style = "padding: 0; " +
					    "background: transparent; " +
					    "position: absolute; " +
					    "z-index: 99; " +
					    "margin-top: " + jQuery('#theme_nav_vertical_margin').val() + "px; " +
					    "margin-bottom: " + jQuery('#theme_nav_vertical_margin').val() + "px; " +
					    "margin-left: " + jQuery('#theme_nav_horizontal_margin').val() + "px; " +
					    "margin-right: " + jQuery('#theme_nav_horizontal_margin').val() + "px; ";

			var position = jQuery('#nav_position option:selected').val();

			// slider bottom margin - apply if the buttons are underneath
			var dotSize = parseInt(jQuery('#theme_dot_size').val());
			var dotMargin = parseInt(jQuery('#theme_nav_vertical_margin').val());
			var margin = dotSize + (dotMargin * 2);

			if (position == 'default') {
				bulletContainers
					.css('cssText', style +
									"top: auto !important;" +
									"bottom: auto !important;" +
									"left: auto !important;" +
									"right: auto !important;" +
									"width: 100% !important;" +
									"text-align: center !important");

				jQuery('.metaslider').css('margin-bottom', margin + 'px');
			}

			if (position == 'topLeft') {
				bulletContainers
					.css('cssText', style +
									"width: auto !important;" +
									"bottom: auto !important;" +
									"top: 0 !important;" +
									"right: auto !important;" +
									"left: 0 !important;");
			}

			if (position == 'topCenter') {
				bulletContainers
					.css('cssText', style +
									"width: 100% !important;" +
									"bottom: auto !important;" +
									"top: 0 !important;" +
									"right: auto !important;" +
									"left: 0 !important;");
			}

			if (position == 'topRight') {
				bulletContainers
					.css('cssText', style +
									"width: auto !important;" +
									"bottom: auto !important;" +
									"top: 0 !important;" +
									"right: 0 !important;" +
									"left: auto !important;");
			}

			if (position == 'bottomLeft') {
				bulletContainers
					.css('cssText', style +
									"width: auto !important;" +
									"bottom: 0 !important;" +
									"top: auto !important;" +
									"right: auto !important;" +
									"left: 0 !important;");
			}

			if (position == 'bottomCenter') {
				bulletContainers
					.css('cssText', style +
									"width: 100% !important;" +
									"bottom: 0 !important;" +
									"top: auto !important;" +
									"right: auto !important;" +
									"left: 0 !important;");
			}

			if (position == 'bottomRight') {
				bulletContainers
					.css('cssText', style +
									"width: auto !important;" +
									"bottom: 0 !important;" +
									"top: auto !important;" +
									"right: 0 !important;" +
									"left: auto !important;");
			}
		} else {
			bulletContainers.css('cssText','');
		}
	}


	/**
	 *
	 */
	var applyCaptionStylingToPreview = function() {
	    var captions = jQuery('.caption-wrap, .nivo-caption, .cs-title');

		if (jQuery('#enable_custom_caption').is(':checked')) {
			var position = jQuery('#caption_position option:selected').val();
			var caption_width = jQuery('#theme_caption_width').val();

		    var style = "opacity: 1; " +
					    "background: " + jQuery('#colourpicker-caption-background-colour').val() + "; " +
					    "color: " + jQuery('#colourpicker-caption-text-colour').val() + "; " +
					    "z-index: 1000; " +
					    "text-align: " + jQuery('#caption_align option:selected').val() + "; " +
					    "margin-top: " + jQuery('#theme_caption_vertical_margin').val() + "px; " +
					    "margin-bottom: " + jQuery('#theme_caption_vertical_margin').val() + "px; " +
					    "margin-left: " + jQuery('#theme_caption_horizontal_margin').val() + "px; " +
					    "margin-right: " + jQuery('#theme_caption_horizontal_margin').val() + "px; " +
					    "border-radius: " + jQuery('#theme_caption_border_radius').val() + "px;";

			if (position == 'underneath') {
				captions
					.css('cssText', style +
									"width: " + caption_width + "% !important;" +
									"bottom: auto !important;" +
									"top: auto !important;" +
									"right: auto !important;" +
									"left: auto !important;" +
									"clear: both !important;" +
									"position: relative !important;");
			}

			if (position == 'topLeft') {
				captions
					.css('cssText', style +
									"width: " + caption_width + "% !important;" +
									"bottom: auto !important;" +
									"top: 0 !important;" +
									"right: auto !important;" +
									"left: 0 !important;" +
									"clear: none !important;" +
									"position: absolute !important;");
			}

			if (position == 'topRight') {
				captions
					.css('cssText', style +
									"width: " + caption_width + "% !important;" +
									"bottom: auto !important;" +
									"top: 0 !important;" +
									"right: 0 !important;" +
									"left: auto !important;" +
									"clear: none !important;" +
									"position: absolute !important;");
			}

			if (position == 'bottomLeft') {
				captions
					.css('cssText', style +
									"width: " + caption_width + "% !important;" +
									"bottom: 0 !important;" +
									"top: auto !important;" +
									"right: auto !important;" +
									"left: 0 !important;" +
									"clear: none !important;" +
									"position: absolute !important;");
			}

			if (position == 'bottomRight') {
				captions
					.css('cssText', style +
									"width: " + caption_width + "% !important;" +
									"bottom: 0 !important;" +
									"top: auto !important;" +
									"right: 0 !important;" +
									"left: auto !important;" +
									"clear: none !important;" +
									"position: absolute !important;");
			}
		} else {
			captions.css('cssText', '');
		}
	}

	/**
	 *
	 */
	function applyArrowStylingToPreview() {
		var prev = jQuery('.nivo-prevNav, .flex-prev, .cs-prev, .rslides_nav.prev');
		var next = jQuery('.nivo-nextNav, .flex-next, .cs-next, .rslides_nav.next');

		if (jQuery('#enable_custom_arrows').is(':checked')) {

			if (jQuery('#custom_prev_arrow_input').val() > 0) {
		        var prev_offset = '';
		        var prev_height = jQuery('#custom_prev_arrow img').attr('height');
		        var prev_width = jQuery('#custom_prev_arrow img').attr('width');
		        var prev_url = jQuery('#custom_prev_arrow img').attr('src');
		    } else {
		        var prev_offset = '0 -' + jQuery('#arrow_style option:selected').data('offset') + 'px';
		        var prev_height = jQuery('#arrow_style option:selected').data('height');
		        var prev_width = jQuery('#arrow_style option:selected').data('width');
		        var prev_url = jQuery('#arrow_colour option:selected').data('url');
		    }

		    if (jQuery('#custom_prev_arrow_input').val() > 0) {
		        var next_offset = '';
		        var next_height = jQuery('#custom_next_arrow img').attr('height');
		        var next_width = jQuery('#custom_next_arrow img').attr('width');
		        var next_url = jQuery('#custom_next_arrow img').attr('src');
			} else {
		        var next_offset = '100% -' + jQuery('#arrow_style option:selected').data('offset') + 'px';
		        var next_height = jQuery('#arrow_style option:selected').data('height');
		        var next_width = jQuery('#arrow_style option:selected').data('width');
		        var next_url = jQuery('#arrow_colour option:selected').data('url');
		    }

		    var opacity = jQuery('#theme_arrow_opacity').val() / 100;

	        // prev arrows
	        prev
	            .css('margin-top', '-' + (prev_height / 2) + 'px')
	            .css('width', prev_width + 'px')
	        	.css('height', prev_height + 'px')
	        	.css('padding', '0')
	        	.css('text-indent', '-9999px')
	        	.css('top', '50%')
	        	.css('opacity', opacity)
		        .css('background', 'transparent url(' + prev_url + ') ' + prev_offset)
		        .css('left', jQuery('#theme_arrow_indent').val() + 'px');

		    // next arrows
	        next
	            .css('margin-top', '-' + (next_height / 2) + 'px')
	            .css('width', next_width + 'px')
	        	.css('height', next_height + 'px')
	        	.css('padding', '0')
	        	.css('text-indent', '-9999px')
	        	.css('top', '50%')
	        	.css('opacity', opacity)
		        .css('background', 'transparent url(' + next_url + ') ' + next_offset)
	        	.css('right', jQuery('#theme_arrow_indent').val() + 'px');

        } else {
        	prev.css('cssText', '');
        	next.css('cssText', '');
        }
	}

	/**
	 *
	 */
	function applySlideshowStylingToPreview() {

		jQuery('#theme_outer_border_radius').on('change', function() {
			jQuery(".metaslider .flexslider, .metaslider .flexslider img, .metaslider .nivoSlider, .metaslider .nivoSlider img, .metaslider .rslides, .metaslider .rslides img")
			   .css('border-radius', jQuery(this).val() + 'px');
		});

		jQuery(".metaslider").removeClass (function (index, css) {
		    return (css.match (/\beffect\S+/g) || []).join(' ');
		});

		var effect = jQuery('#shadow option:selected').val();
		jQuery('.theme_editor_right .metaslider').addClass(effect);

	}

	var applyAllStylingToPreview = function() {
		applyBulletStylingToPreview();
		applyBulletPositioningToPreview();
		applyArrowStylingToPreview();
		applySlideshowStylingToPreview();
		applyCaptionStylingToPreview();
	}

	/**
	 *
	 */
	jQuery('.colorpicker').spectrum({
		preferredFormat: "rgb",
		showInput: true,
		showAlpha: true
	});

	jQuery(".flex-control-nav, .nivo-controlNav, .rslides_tabs, .cs-buttons, .nivo-prevNav, .flex-prev, .cs-prev, .nivo-nextNav, .flex-next, .cs-next").live('click', function() {
		applyBulletStylingToPreview();
		applySlideshowStylingToPreview();
	});

	jQuery('input, select').change(function() {
		applyAllStylingToPreview();
	});

});
