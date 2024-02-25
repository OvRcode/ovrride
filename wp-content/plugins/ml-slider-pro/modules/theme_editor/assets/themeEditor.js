jQuery(document).ready(function () {

    jQuery('#remove_custom_prev_arrow').on('click', function (event) {
        event.preventDefault();
        jQuery("#custom_prev_arrow").html("");
        jQuery("#custom_prev_arrow_input").val("0");
        jQuery("#open_media_manager_prev").show();
        jQuery("#remove_custom_prev_arrow").hide();
    });

    jQuery('#open_media_manager_prev').on('click', function (event) {
        event.preventDefault();

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            multiple: false,
            frame: 'post',
            library: {type: 'image'}
        });

        // When an image is selected, run a callback.
        file_frame.on('insert', function () {

            var selection = file_frame.state().get('selection');
            var slide_ids = [];

            selection.map(function (attachment) {
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

    jQuery('#remove_custom_next_arrow').on('click', function (event) {
        event.preventDefault();
        jQuery("#custom_next_arrow").html("");
        jQuery("#custom_next_arrow_input").val("0");
        jQuery("#open_media_manager_next").show();
        jQuery("#remove_custom_next_arrow").hide();
    });

    jQuery('#open_media_manager_next').on('click', function (event) {
        event.preventDefault();

        // Create the media frame.
        file_frame = wp.media.frames.file_frame = wp.media({
            multiple: false,
            frame: 'post',
            library: {type: 'image'}
        });

        // When an image is selected, run a callback.
        file_frame.on('insert', function () {

            var selection = file_frame.state().get('selection');
            var slide_ids = [];

            selection.map(function (attachment) {
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
    jQuery('.confirm').on('click', function () {
        return confirm("Are you sure?");
    });
    /**
     * Applying bullet styling to preview
     */
    var applyBulletStylingToPreview = function () {
        var bullets = jQuery(".flex-control-nav a, .nivo-controlNav a, .rslides_tabs li a, .cs-buttons a");
        var activeBullets = jQuery(".flex-control-nav li a.flex-active, .nivo-controlNav a.active, .rslides_tabs li.rslides_here a, .cs-buttons a.cs-active");

        if (jQuery('#enable_custom_navigation').is(':checked')) {
            var start = jQuery("#colourpicker-fill-start").data('new-color') || jQuery("#colourpicker-fill-start").val();
            var end = jQuery("#colourpicker-fill-end").data('new-color') || jQuery("#colourpicker-fill-end").val();
            var borderColour = jQuery("#colourpicker-border-colour").data('new-color') || jQuery("#colourpicker-border-colour").val();

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

            var activeStart = jQuery("#colourpicker-active-fill-start").data('new-color') || jQuery("#colourpicker-active-fill-start").val();
            var activeEnd = jQuery("#colourpicker-active-fill-end").data('new-color') || jQuery("#colourpicker-active-fill-end").val();
            var activeBorderColour = jQuery("#colourpicker-active-border-colour").data('new-color') || jQuery("#colourpicker-active-border-colour").val();

            activeBullets
                .css('background', '-webkit-gradient(linear, 0% 0%, 0% 100%, from(' + activeStart + '), to(' + activeEnd + '))')
                .css('background', '-webkit-linear-gradient(top, ' + activeStart + ', ' + activeEnd + ')')
                .css('background', '-moz-linear-gradient(top, ' + activeStart + ', ' + activeEnd + ')')
                .css('background', '-ms-linear-gradient(top, ' + activeStart + ', ' + activeEnd + ')')
                .css('background', '-o-linear-gradient(top, ' + activeStart + ', ' + activeEnd + ')')
                .css('border-color', activeBorderColour);
        } else {
            bullets.css('cssText', '');
            activeBullets.css('cssText', '');

            // Remove CSS from <head>
            var selector = 'style:contains(".flexslider .flex-control-paging li a"),';
            selector    += 'style:contains(".flexslider .flex-control-paging li a:hover"),';
            selector    += 'style:contains(".flexslider .flex-control-paging li"),';
            selector    += 'style:contains(".flexslider .flex-control-paging li a.flex-active"),';
            selector    += 'style:contains(".flexslider .flex-control-paging")';
            jQuery(selector).remove();
        }
    }

    /**
     * Applying bullet positionaing to preview
     */
    var applyBulletPositioningToPreview = function () {
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
            bulletContainers.css('cssText', '');
        }
    }

    /**
     * Define font-family, font-weight and font-style properties
     * 
     * @since 2.26
     * 
     * @param string family     e.g. 'Open Sans'
     * @param string variation  e.g. '400', '400italic'
     * 
     * @return string
     */
    var applyFontStyles = function (family, variation) {
        var style = '';

        // font-family
        if(typeof family !== 'undefined' && family.length > 0) {
            style += `font-family: "${family}" !important;`;
        } else {
            style += `font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;`;
        }

        // font-weight and font-style
        if(typeof variation !== 'undefined' && variation.length > 0) {
            if (variation.indexOf('italic') !== -1 && variation.length > 6) {
                style += `font-weight:${variation.split('italic', 1)[0]} !important; font-style:italic !important;`;
            } else {
                style += `font-weight:${variation} !important;`;
            }
        } else {
            style += `font-weight:normal !important; font-style:400 !important;`;
        }

        return style;
    }

    /**
     * Applying caption styling to preview
     */
    var applyCaptionStylingToPreview = function () {
        var captions = jQuery('.caption-wrap, .nivo-caption, .cs-title');

        if (jQuery('#enable_custom_caption').is(':checked')) {
            var position = jQuery('#caption_position option:selected').val();
            var caption_width = jQuery('#theme_caption_width').val();
            var font_family = jQuery('#theme_caption_font_family option:selected').val();
            var font_variation = jQuery('#theme_caption_font_variation option:selected').val();
            
            var style = "opacity: 1; " +
                "background: " + jQuery('#colourpicker-caption-background-colour').val() + "; " +
                "color: " + jQuery('#colourpicker-caption-text-colour').val() + "; " +
                "font-size: " + jQuery('#theme_caption_text_size').val() + "px; " +
                "line-height: " + jQuery('#theme_caption_text_line_height').val() + "px; " +
                "z-index: 1000; " +
                "text-align: " + jQuery('#caption_align option:selected').val() + "; " +
                "margin-top: " + jQuery('#theme_caption_vertical_margin').val() + "px; " +
                "margin-bottom: " + jQuery('#theme_caption_vertical_margin').val() + "px; " +
                "margin-left: " + jQuery('#theme_caption_horizontal_margin').val() + "px; " +
                "margin-right: " + jQuery('#theme_caption_horizontal_margin').val() + "px; " +
                "padding-top: " + jQuery('#theme_caption_vertical_padding').val() + "px; " +
                "padding-bottom: " + jQuery('#theme_caption_vertical_padding').val() + "px; " +
                "padding-left: " + jQuery('#theme_caption_horizontal_padding').val() + "px; " +
                "padding-right: " + jQuery('#theme_caption_horizontal_padding').val() + "px; " +
                "border-radius: " + jQuery('#theme_caption_border_radius').val() + "px;";

            style += applyFontStyles(font_family, font_variation);

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

            // Remove CSS from <head>
            jQuery('style:contains(".flexslider .caption-wrap")').remove();
        }
    }

    /**
     * Applying arrow styling to preview
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

            // Remove CSS from <head>
            var selector = 'style:contains(".flexslider .flex-direction-nav .flex-prev"),';
            selector    += 'style:contains(".flexslider .flex-direction-nav .flex-next"),';
            selector    += 'style:contains(".flexslider:hover .flex-direction-nav .flex-prev"),';
            selector    += 'style:contains(".flexslider:hover .flex-direction-nav .flex-next")';

            jQuery(selector).remove();
        }
    }//end applyArrowStylingToPreview()


    /**
     * Clean old CSS and apply new CSS
     * 
     * @param {string} newCss           String with all the new CSS properties
     * @param {bool} showBorderRadius   Display border radius setting?
     */
    var applyNewShapeCss = function (newCss, showBorderRadius) {
        var selector = jQuery('.metaslider ul.slides > li');
        var border_radius = jQuery('#theme_outer_border_radius');

        // Remove old CSS properties
        selector.css({
            'border-radius': 'none',
            'background-color': 'transparent',
            'overflow': 'visible',
            'clip-path': 'none'
        });

        // Add new CSS
        selector.css(newCss);

        // Show/hide Border radius setting
        if (showBorderRadius) {
            border_radius.closest('tr').show();
        } else {
            border_radius.closest('tr').hide();
        }
    }

    /**
     * Applying slide show styling to preview
     */
    function applySlideshowStylingToPreview() {
        var shape   = jQuery('#shape');
        var border_radius = jQuery('#theme_outer_border_radius');
        
        switch (shape.val()) {
            case 'oval':
                applyNewShapeCss(
                    {
                        'border-radius': '50%',
                        'background-color': '#000',
                        'overflow': 'hidden'
                    },
                    false
                );
                break;

            case 'circle':
                applyNewShapeCss(
                    {
                        'clip-path': 'circle(50% at 50% 50%)',
                        'background-color': '#000'
                    },
                    false
                );
                break;
            case 'triangle':
                applyNewShapeCss(
                    {
                        'clip-path': 'polygon(50% 0%, 0% 100%, 100% 100%)',
                        'background-color': '#000'
                    },
                    false
                );
                break;

            case 'pentagon':
                applyNewShapeCss(
                    {
                        'clip-path': 'polygon(50% 0%, 100% 38%, 82% 100%, 18% 100%, 0% 38%)',
                        'background-color': '#000'
                    },
                    false
                );
                break;

            case 'star':
                applyNewShapeCss(
                    {
                        'clip-path': 'polygon(50% 0%, 61.8% 35.4%, 100% 38.2%, 69.1% 61.8%, 80.3% 100%, 50% 80%, 19.7% 100%, 30.9% 61.8%, 0% 38.2%, 38.2% 35.4%)',
                        'background-color': '#000'
                    },
                    false
                );
                break;
            
            default:
                applyNewShapeCss(
                    {
                        'border-radius': border_radius.val() + 'px',
                        'background-color': '#000',
                        'overflow': 'hidden'
                    },
                    true
                );
                break;
        }

        jQuery('#theme_outer_border_radius').on('change', function () {
            jQuery(".metaslider .nivoSlider, .metaslider .nivoSlider img, .metaslider .rslides, .metaslider .rslides img")
                .css('border-radius', jQuery(this).val() + 'px');
        });
            
        jQuery(".metaslider").removeClass(function (index, css) {
            return (css.match(/\beffect\S+/g) || []).join(' ');
        });

        var effect = jQuery('#shadow option:selected').val();
        jQuery('.theme_editor_right .metaslider').addClass(effect);

    }//end applySlideshowStylingToPreview()

    var captionsTimeOut;
    var applyAllStylingToPreview = function () {
        applyBulletStylingToPreview();
        applyBulletPositioningToPreview();
        applyArrowStylingToPreview();
        applySlideshowStylingToPreview();

        // To avoid selected family variation values to be unsync, we delay 0.5 seconds
        clearTimeout(captionsTimeOut);
        captionsTimeOut = setTimeout( function() {
            applyCaptionStylingToPreview();
        }, 500);
    }

    jQuery( '.colorpicker' ).wpColorPicker({
        change: function(event, ui) {
            var input = jQuery(this).parents('.wp-picker-container').find('input.colorpicker');
            var btn = jQuery(this).parents('.wp-picker-container').find('button.wp-color-result');

            btn.css('background-color',ui.color.toCSS('rgba'));
            console.log(ui.color.toCSS('rgba'),btn);

            
            input.data('new-color',ui.color.toCSS('rgba'));
            input.attr('value',ui.color.toCSS('rgba'));

            btn.trigger('change');
        }
    });

    jQuery('.metaslider').on('click', '.flex-control-nav, .nivo-controlNav, .rslides_tabs, .cs-buttons, .nivo-prevNav, .flex-prev, .cs-prev, .nivo-nextNav, .flex-next, .cs-next', function () {
        applyBulletStylingToPreview();
        applySlideshowStylingToPreview();
    });

    jQuery('input, select, button.wp-color-result').change(function () {
        applyAllStylingToPreview();
    });

    // Sync input[type="number"] and input[type="range"] for the same theme setting
    jQuery('.ms-range-dual input').on('change', function () {
        var el = jQuery(this);
        var val = el.val() || 100;

        if (el.prop('type') === 'number') {
            // When input[type="number"] changes, sync input[type="range"]
            el.siblings('input[type="range"]').val(val);
        } else {
            /* When input[type="range"] changes, sync input[type="number"] 
             * and trigger change event for input[type="number"] to preview changes */
            el.siblings('input[type="number"]').val(val);
            el.siblings('input[type="number"]').trigger('change');
        }
    });

    /* Show/hide settings depending if the checkbox toggle linked 
     * to it is enabled/disabled */
    jQuery('.ms-switch-button input').on('change', function () {
        toggleSettings(jQuery(this));
    });
    
    /* Make sure switch state is in sync with its settings 
     * despite page is refreshed without saving changes */
    var syncSwitchWithSettings = function () {
        var editor_switches = jQuery('.metaslider_themeEditor').find('.ms-switch-button input');
    
        editor_switches.each(function () {
            toggleSettings(jQuery(this));
        });
    }

    /* Check if one switch (checkbox) is checked or unchecked 
     * and show/hide its settings according to it
     *
     * @param obj el    Checkbox element
     *
     * @return void
     */
    var toggleSettings = function (el) {
        var ref = el.data('ref') || null;
        var show = el.data('show') || null;

        if (!ref || !show) return;

        var table = jQuery('.metaslider_themeEditor').find('#'+ref);

        if (el.prop('checked') && show === 'checked') {
            // Show content with data-ref id when data-show equals to 'checked'
            table.show();
        } else if (!el.prop('checked') && show === 'unchecked') {
            // Show content with data-ref id when data-show equals to 'unchecked'
            table.show();
        } else {
            table.hide();
        }
    }

    /* Append Google Fonts options to a <select> field
     * and load font files
     *
     * @since 2.26
     * 
     * @param string selector The <select> selector. e.g. '#theme_caption_font_family'
     * @return void
     */
    var googleFontsField = function (id) {
        
        if (typeof metaslider_google_fonts === 'undefined') {
            console.error("Google Fonts data can't be loaded");
            return;
        }

        /* Execute only if we're in single theme editor screen
         * `admin.php?page=metaslider-theme-editor&theme_slug=${slug}` */
        if (!jQuery(id).length) {
            console.log("Theme editor listing page");
            return;
        }
        
        // Font family <select> tags
        var selectFont = jQuery(id);
        var currentFont = selectFont.data('selected').length ? selectFont.data('selected') : '';
        
        // External host
        var extHostWrapper = selectFont.data('ext-host-wrapper').length ? selectFont.data('ext-host-wrapper') : '';
        
        // Font variation <select> and  wrapper (e.g. <tr>) tags
        var variationWrapper = selectFont.data('variation-wrapper').length ? selectFont.data('variation-wrapper') : '';
        var selectVariation = jQuery(variationWrapper).find('select');

        /* Create and append <link> to head to load the font family
         *
         * @param string family     e.g. 'Open Sans'
         * @param string variation  e.g. 400, '400italic'
         */
        var createLinkTag = function (family, variation) {
            var fontId = buildFontId(family, variation);

            if (typeof family !== 'undefined'
                && family.length > 0
                && !document.getElementById(fontId)
            ) {
                var link_tag = document.createElement('link');
                link_tag.tagName.media = 'all';
                link_tag.rel = 'stylesheet';
                link_tag.id = fontId;
                link_tag.href = buildFontUrl(family, variation);
                document.head.appendChild(link_tag);
            }
        }

        /* Create a unique id dor the <link> tag
         *
         * @param string family     e.g. 'Open Sans'
         * @param string variation  e.g. 400, '400italic'
         */
        var buildFontId = function (family, variation) {
            var uniqueId = 'metaslider_google_font_' + family.replace(/\s/g, '_').toLowerCase() 
                + (variation.length ? '_' + variation : '');

            return uniqueId;
        } 

        /* Build the Google Font URL
         *
         * @param string family     e.g. 'Open Sans'
         * @param string variation  e.g. 400, '400italic'
         */
        var buildFontUrl = function (family, variation) {
            var google_base_url = 'https://fonts.googleapis.com/css2?family=';

            if(variation.length > 0) {
                var font_variation;
                // Extract font-weight and font-style from variation. e.g. 400italic -> 400
                if(variation.indexOf('italic') !== -1) {
                    let v = variation.split('italic', 1);
                    if(v[0] !== 'undefined' && v[0].length > 0) {
                        google_font_variation = ':ital,wght@1,' + v[0];
                    } else {
                        google_font_variation = ':ital@1';
                    }
                } else {
                    google_font_variation = ':wght@' + variation;
                }
                return google_base_url + family.replace(/\s/g, '+') + google_font_variation;
            }

            return `${google_base_url}${family.replace(/\s/g, '+')}`;
        } 

        /* Load saved font files
         *
         * @param string family             e.g. 'Open Sans'
         * @param string overrideVariation  Optional value to override variation. e.g. '400', '400italic'
         * 
         * @return void
         */
        var fontSync = function (family, overrideVariation = null) {
            
            // Fill variation options for the font family, then load the font
            setFontVariations(family).then((variation) => {
                
                // Load the font
                if (overrideVariation !== null) {
                    createLinkTag(family,overrideVariation);

                    // Update data-selected attribute with the new variation
                    selectVariation.attr('data-selected', overrideVariation);
                    
                    // Font variation, remove selected attribute to previous selected <option>
                    var prevOption = selectVariation.find('option:selected');
                    prevOption.removeAttr('selected');

                    // Font variation, <option> tag with selected attribute
                    var variationOption = selectVariation.find(`option[value="${overrideVariation}"]`);

                    // Add selected attribute to <option> tag
                    variationOption.attr('selected', 'selected');
                } else {
                    createLinkTag(family,variation);
                }
                console.log(`${family} font loaded`);
            });
        } 

        /* Dynamic load of variation options based on selected font family
         *
         * @param string family     e.g. 'Open Sans'
         * 
         * @return object
         */
        var setFontVariations = function (family) {

            return new Promise((resolve, reject) => {
                if (family !== '') {
                    var data = [];
                    var obj = metaslider_google_fonts.fonts.find(value => value.f === family);

                    // Clear select
                    selectVariation.find('option:selected').removeAttr('selected');
                    selectVariation.empty().val('');

                    // Append default option
                    selectVariation.append(`<option value="">${metaslider_google_fonts.default}</option>`);

                    // Append the font variations and save to return data
                    jQuery.each(obj.v, function (index, item) {
                        var label = item.toString().length > 6 && item.indexOf('italic') !== -1 
                                    ? `${item.split('italic', 1)[0]} + italic` 
                                    : item;
                        selectVariation.append(`<option value="${item}">${label}</option>`);
                    });

                    // Make sure variation is a string
                    var currentVariation = selectVariation.data('selected').toString().length 
                        ? selectVariation.data('selected').toString() : '';

                    // Font variation, <option> tag with selected attribute
                    var variationOption = selectVariation.find(`option[value="${selectVariation.data('selected')}"]`);

                    // Add selected attribute to <option> tag
                    variationOption.attr('selected', 'selected');

                    // Show font variation and external host
                    jQuery(variationWrapper).show();
                    jQuery(extHostWrapper).show();

                    // Return active variation e.g. '400', '400italic'
                    resolve(currentVariation);
                } else {
                    // Clear select
                    selectVariation.empty();

                    // Hide font variation and external host
                    jQuery(variationWrapper).hide();
                    jQuery(extHostWrapper).hide();

                    console.log('No variations found for this font');
                }
            });
        }

        // Append all the font options
        jQuery.each(metaslider_google_fonts.fonts, function (index, item) {
            selectFont.append(`<option value="${item.f}">${item.f}</option>`);
        });

        /* Set selected attribute to the saved <option> tags 
         * and load the stylesheet - executes on page loads */
        if (currentFont !== '') {
            // Font family, <option> tag with selected attribute
            var fontOption = selectFont.find(`option[value="${currentFont}"]`);

            // Add selected attribute to <option> tags in Font family
            fontOption.attr('selected', 'selected');
            
            // Load the font
            fontSync(currentFont);
        }

        // Load the Google font when Font family changes through <select>
        selectFont.on('change', function () {
            var newFont = jQuery(this).find('option:selected').val();
            /* Let's choose default variation instead of previous font's variation 
             * to avoid errors in case the old font's variation is not available in the new font */
            fontSync(newFont, '');
        });

        // Load the Google font when Font variation changes through <select>
        selectVariation.on('change', function () {
            var newFont = selectFont.find('option:selected').val();
            var newVariation = jQuery(this).find('option:selected').val();
            
            // Update data-select attribute
            selectVariation.attr('data-selected', newVariation);

            // Remove previous selected attribute from <option>
            selectVariation.find('option[selected="selected"]').removeAttr('selected');

            // Add selected attribute to new variation <option>
            selectVariation.find(`option[value="${newVariation}"]`).attr('selected', 'selected');

            createLinkTag(newFont, newVariation);
        });
    }

    syncSwitchWithSettings();
    googleFontsField('#theme_caption_font_family');
    
    // Sticky preview on scroll
    new jQuery.Zebra_Pin(jQuery('.theme_editor_preview'), {
        top_spacing: 90
    });

    jQuery('.tipsy-tooltip-top').tipsy({live: false, delayIn: 500, html: true, gravity: 's'})
});
