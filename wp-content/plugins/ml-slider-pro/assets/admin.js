window.jQuery(function($) {
	window.metaslider.app.EventManager.$on("metaslider/app-loaded", function (e) {

	var $ = window.jQuery;
	
	// Enable the correct options for this slider type
    var checkSlideCompatibility = function(slider) {
        // slides - set red background on incompatible slides
        jQuery("#compatibilityWarning").remove();

        if (jQuery('.metaslider .slide:not(.' + slider + ')').length) {
            var sup_types = 'Image and Post Feed';
            if(slider === 'responsive') {
                sup_types = 'Image, Post Feed, Vimeo, YouTube, External URL and Layer Slide';
            }
            var message = ucFirst(slider) + " Slider is only compatible with " + sup_types + " slides.";
            var warningDiv = jQuery("<div id='compatibilityWarning' class='updated ms-slideshow-warning'><p><b>Warning:</b> " + message + "</p></div>");
            jQuery(".metaslider .left").prepend(warningDiv);
        };
    };

    var ucFirst = function(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    // handle slide libary switching
    $(".metaslider").on('click', '.select-slider', function() {
        checkSlideCompatibility(jQuery(this).attr('rel'));
    });

    checkSlideCompatibility(jQuery('.metaslider .select-slider:checked').attr('rel'));

    function loadCodeMirror(textarea) {

		$(textarea).each(function() {
			$(this).hide().siblings('.CodeMirror').remove();

			CodeMirror.fromTextArea(this, {
				tabMode: 'indent',
				mode: 'xml',
				lineNumbers: true,
				lineWrapping: true,
				theme: 'monokai',
				onChange: function(cm) {
					cm.save();
				}
			});
		})
	}
	
	loadCodeMirror($('.metaslider-ui .wysiwyg'));
	window.metaslider.app.EventManager.$on("metaslider/slides-created", function() { loadCodeMirror($('.metaslider-ui .wysiwyg')); });

    $(".metaslider").on('change', '.external input.extimgurl', function() {
        var val = $(this).val();
        $(this).parents('.slide').find('.thumb').css('background-image', 'url(' + val + ')');
    });

    /**
     * Hide slide
     */
    // Stop propagation
    $(".metaslider").on('click', 'button.hide-slide input[type=checkbox]', function(e){
        e.stopPropagation();
    });
    // Button click handler
    $(".metaslider").on('click', 'button.hide-slide', function(e) {
        e.stopPropagation();
        $(this).find('input[type=checkbox]').trigger('click');
        $(this).closest('tr.slide').toggleClass('slide-is-hidden', $(this).find('input[type=checkbox]').is(':checked'));
        checkDelayCompatibility();
    });

    $(".metaslider").on('change', 'select[name="template_tags"]', function(e) {
        e.preventDefault();

        var tag = $(this).val();

        var codeMirror = $(this).closest('.tab').find('.CodeMirror')[0].CodeMirror;

        if (codeMirror.getSelection() == "") {
            codeMirror.replaceRange(tag, codeMirror.getCursor());
        } else {
            codeMirror.replaceSelection(tag);
        }

        codeMirror.focus();
    });

	var updateDatepicker = function() {
		$('.metaslider .datepicker').datepicker({
			dateFormat:'yy-mm-dd',
		}).on('focus', function(e){
			if ($(this).datepicker('widget').offset().top > $(this).offset().top) {
				$(this).datepicker('widget').addClass('bottom');
				$(this).datepicker('widget').removeClass('top');
			} else {
				$(this).datepicker('widget').addClass('top');
				$(this).datepicker('widget').removeClass('bottom');
			}
		});
	}
	updateDatepicker();
	window.metaslider.app.EventManager.$on('metaslider/slides-created', function() { updateDatepicker(); });

    /**
     * Set Hiden slide class on page load
     */
    $(".metaslider button.hide-slide input[type=checkbox]").each(function(i) {
        $(this).closest('tr.slide').toggleClass('slide-is-hidden', $(this).is(':checked'));
    });

    /**
     * Check if custom delay is enabled in "Advanced" tab
     * 
     * @param {obj} el The input checkbox
     * 
     * @return void
     */
    var checkDelayCompatibility = function () {
        jQuery("#compatibilityWarningDelay").remove();
        
        if (jQuery('.ms-switch-button input[name*="[delay]"]:checked').length > 0
            && jQuery('tr.slide.post_feed').length > 0
        ) {
            var message = "Post Feed slide isn't compatible with custom delay. Disable custom delay through 'Advanced' tab of each slide.";
            var warningDiv = jQuery("<div id='compatibilityWarningDelay' class='updated ms-slideshow-warning'><p><b>Warning:</b> " + message + "</p></div>");
            jQuery(".metaslider .left").prepend(warningDiv);
        };
    }

    // When custom delay checkboxes changes
    $(document).on('change', '.ms-switch-button input[name*="[delay]"]', function() {
        checkDelayCompatibility();
    });

    // Check all the custom delay checkboxes on load
    $('.ms-switch-button input[name*="[delay]"]').each( function() {
        checkDelayCompatibility();
    });
});
});
