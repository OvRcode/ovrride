(function ($) {
    $(function () {
        var layerEditor = {

            // show the spinner while slides are being added
            checkSaveRequest: function() {
                if (jQuery.active < 1) {
                    $('.spinner').hide();
                }

                setTimeout(layerEditor.checkSaveRequest, 1000); 
            },

            checkForSelectedLayer: function() {
                if ($(".metaslider-active").length == 0) {
                    alert(metasliderpro.noLayerSelected);
                }
            },

            createNewLayer: function() {
                $('.metaslider-active').removeClass('metaslider-active');
                var rand_id = Math.floor((Math.random()*999999999)+1);

                var content = $("<div class='content' id='layer_content_" + rand_id + "'>" + metasliderpro.newLayer + "</div>")
                                    .css({
                                        'color' : 'white',
                                        'padding' : '5px'
                                    })
                                    .attr('data-padding', '5');


                var content_wrap = $("<div class='ms_content_wrap' />")
                                        .css({
                                            'height': '100%',
                                            'background' : 'rgba(142, 142, 142, 0.50)'
                                        })
                                        .html(content);

                var animation_out = $("<div class='animation_out animated disabled'/>")
                                        .attr('data-animation', 'disabled')
                                        .attr('data-animation-delay', '0')
                                        .css({
                                            'height' : '100%',
                                            'width' : '100%'
                                        })
                                        .html(content_wrap);

                var animation_in = $("<div class='animation_in animated disabled' />")
                                        .attr('data-animation', 'disabled')
                                        .attr('data-animation-delay', '0')
                                        .css({
                                            'width': '100%',
                                            'height': '100%'
                                        })
                                        .html(animation_out);

                var layer = $("<div class='layer metaslider-active' />")
                                .css({
                                        'width': '120px',
                                        'height': '40px',
                                        'top': '5px',
                                        'left': '5px',
                                        'position': 'absolute'
                                })
                                .attr('data-width', '120')
                                .attr('data-height', '40')
                                .html(animation_in);

                $("#htmlOverlay").append(layer);

                layerEditor.makeLayerEditable(layer);
            },

            updateLayerAnimationDelay: function() {
                var anim_in_delay_ms = parseFloat($('#animation_in_delay').val()) * 1000;
                var anim_out_delay_ms = parseFloat($('#animation_out_delay').val()) * 1000;
                var total_delay = anim_out_delay_ms + anim_in_delay_ms;

                $(".metaslider-active .animation_in")
                    .attr('data-animation-delay', $('#animation_in_delay').val(), 10)
                    .attr('style',
                        'animation-delay: '+ anim_in_delay_ms +'ms; '+
                        '-moz-animation-delay: '+ anim_in_delay_ms +'ms; '+
                        '-webkit-animation-delay: '+ anim_in_delay_ms +'ms;'
                    );

                $(".metaslider-active .animation_out")
                    .attr('data-animation-delay', $('#animation_out_delay').val(), 10)
                    .attr('style',
                        'animation-delay: '+ total_delay +'ms; '+
                        '-moz-animation-delay: '+ total_delay +'ms; '+
                        '-webkit-animation-delay: '+ total_delay +'ms;'
                    );
            },

            save: function(editor_id) {
                $('.spinner').show();
                $('#' + editor_id).val(layerEditor.get_html());
                $('#ms-save').click();
            },

            save_on_close: function(editor_id) {
                var decoded = $('#' + editor_id).val();
                var new_decoded = layerEditor.get_html();

                if (decoded != new_decoded) {
                    if (confirm(metasliderpro.saveChanges)) {
                        layerEditor.save(editor_id);
                    }
               }
            },

            render: function(image_url, editor_id, width, height) {
                var layer_color_bg = $("<input type='text' id='layerBgColor' />");
                var layer_editor = $("<div class='layer_editor' />");
                var layer_config = $("<div />");

                // load HTML from CodeMirror
                var html = $($('#' + editor_id).val());

                var add_layer_button = $("<button class='button primary addLayer'>")
                                                .html(metasliderpro.newLayer)
                                                .bind('click', function() {
                                                    layerEditor.createNewLayer();
                                                });

                var spinner = $("<span class='spinner'></span>");

                var save_button = $("<button class='button secondary save'>")
                                        .html(metasliderpro.save)
                                        .bind('click', function() {
                                            layerEditor.save(editor_id);
                                        });

                var container = $("<div />")
                                    .attr('class', 'container')
                                    .css('position', 'relative')
                                    .css('overflow', 'auto');

                var html_overlay = $("<div />")
                                        .attr('id', 'htmlOverlay')
                                        .css({
                                            'position': 'absolute',
                                            'top': '0',
                                            'left': '0',
                                            'width': width + 'px',
                                            'height': height + 'px'
                                        })
                                        .html(html);

                var img = $("<img class='slide' />")
                                .attr('src', image_url)
                                .attr('width', width)
                                .attr('height', height)
                                .css('display', 'block');

                var toolbar_wrapper = $("<div class='toolbar_wrapper' />");

                var wysiwyg_buttons = $("<div id='wysiwyg_buttons' />");

                var animation_toolbar = $("<div id='animation_toolbar'><b>" + metasliderpro.animation + ":</b> </div>");

                var style_toolbar = $("<div id='style_toolbar'></div>");

                var layer_padding = $("<input id='layer_padding' type='number' step='1' min='0' max='99' value='0' size='3' />" + metasliderpro.px + "&nbsp;")
                                        .on('change', function() {
                                            $('.metaslider-active .content').attr('data-padding', $(this).val());
                                            $('.metaslider-active .content').css('padding', $(this).val() + 'px');
                                        });

                var snap_to_grid = $("<input type='checkbox' />")
                                        .attr('checked', 'checked')
                                        .change(function() {
                                            if ($(this).is(':checked')) {
                                                $(".layer").each(function() {
                                                    $(this).resizable({
                                                        grid: 5
                                                    })
                                                    .draggable({
                                                        grid: [5,5]
                                                    });
                                                });
                                            } else {
                                                $(".layer").each(function() {
                                                   $(this).resizable({
                                                        grid: false
                                                    })
                                                    .draggable({
                                                        grid: false
                                                    });
                                                });
                                            }
                                        });

                var snap_to_grid_label = $("<label id='snapToGrid'>" + metasliderpro.snapToGrid + "</label>")
                                                .css('float', 'right')
                                                .append(snap_to_grid);

                var in_animations = [' ',
                    'flash','bounce','shake','tada',
                    'swing','wobble','wiggle','pulse','flip','flipInX',
                    'flipInY','fadeIn','fadeInUp','fadeInDown','fadeInLeft',
                    'fadeInRight','fadeInUpBig','fadeInDownBig','fadeInLeftBig',
                    'fadeInRightBig','bounceIn','bounceInDown','bounceInUp',
                    'bounceInLeft','bounceInRight','rotateIn','rotateInDownLeft',
                    'rotateInDownRight','rotateInUpLeft','rotateInUpRight',
                    'lightSpeedIn','hinge','rollIn'
                ];

                var out_animations = [' ',
                    'flash','bounce','shake',
                    'tada','swing','wobble','wiggle','pulse',
                    'flip','rollOut','lightSpeedOut','rotateOut',
                    'rotateOutDownLeft','rotateOutDownRight','rotateOutUpLeft',
                    'rotateOutUpRight','bounceOut','bounceOutDown','bounceOutUp',
                    'bounceOutLeft','bounceOutRight','flipOutY','flipOutX',
                    'fadeOut','fadeOutUp','fadeOutDown','fadeOutLeft',
                    'fadeOutRight','fadeOutUpBig','fadeOutDownBig','fadeOutLeftBig',
                    'fadeOutRightBig','hinge'
                ];

                var animation_in_delay = $("<input id='animation_in_delay' type='number' max='99' min='0' step='0.1' size='3' value='0' />")
                                                .change(function() {
                                                    layerEditor.checkForSelectedLayer();
                                                    layerEditor.updateLayerAnimationDelay();
                                                });

                var animation_out_delay = $("<input id='animation_out_delay' type='number' max='99' min='0' step='0.1' size='3' value='0' />")
                                                .change(function() {
                                                    layerEditor.checkForSelectedLayer();
                                                    layerEditor.updateLayerAnimationDelay();
                                                });

                var animation_in_options = $("<select id='animation_in' />")
                                                .change(function() {
                                                    layerEditor.checkForSelectedLayer();
                                                    var selected = $('#animation_in :selected').val();
                                                    var animation_in = $(".metaslider-active .animation_in");
                                                    animation_in.removeClass(animation_in.attr('data-animation'));
                                                    animation_in.addClass(selected);
                                                    animation_in.attr('data-animation', selected);
                                                });

                var animation_out_options = $("<select id='animation_out' />")
                                                .change(function() {
                                                    layerEditor.checkForSelectedLayer();
                                                    var selected = $('#animation_out :selected').val();
                                                    var animation_out = $(".metaslider-active .animation_out");
                                                    animation_out.removeClass(animation_out.attr('data-animation'));
                                                    animation_out.addClass(selected);
                                                    animation_out.attr('data-animation', selected);
                                                });

                for (i=0;i<in_animations.length;i++){
                   $('<option/>').val(in_animations[i]).html(in_animations[i]).appendTo(animation_in_options);
                }

                for (i=0;i<out_animations.length;i++){
                   $('<option/>').val(out_animations[i]).html(out_animations[i]).appendTo(animation_out_options);
                }

                html_overlay.after(img);

                animation_toolbar
                    .append(metasliderpro.wait)
                    .append(animation_in_delay)
                    .append(metasliderpro.secondsAnd)
                    .append(animation_in_options)
                    .append(metasliderpro.thenWait)
                    .append(animation_out_delay)
                    .append(metasliderpro.secondsAnd)
                    .append(animation_out_options);

                style_toolbar
                    .append("<b>" + metasliderpro.padding + ":</b>")
                    .append(layer_padding)
                    .append("&nbsp;&nbsp;<b>" + metasliderpro.background + ":</b>")
                    .append(layer_color_bg)
                    .append(snap_to_grid_label);

                toolbar_wrapper
                    .append(wysiwyg_buttons)
                    .append(animation_toolbar)
                    .append(style_toolbar);

                layer_color_bg
                    .spectrum({
                        preferredFormat: "rgb",
                        showInput: true,
                        showAlpha: true,
                        change: function(color) {
                            $('.metaslider-active .content_wrap, .metaslider-active .ms_content_wrap').css('background-color', color.toRgbString());
                        }
                    });

                container
                    .append(html_overlay)
                    .append(img);

                return layer_editor
                        .append(add_layer_button)
                        .append(save_button)
                        .append(spinner)
                        .append(toolbar_wrapper)
                        .append(container);
            },

            get_html: function() {

                var html = "";
                var layers = $('#htmlOverlay').clone();

                $('.layer', layers).each(function() {
                    var layer = $(this);
                    var content = $('.content', layer);
                    var id = content.attr('id');

                    // getData returns formatted HTML
                    content.html(CKEDITOR.instances[id].getData());

                    // remove drag handle
                    $('.drag_handle', layer).remove();
                    $('.delete_button', layer).remove();

                    // clean up
                    content.removeAttr('tabindex');
                    content.removeAttr('spellcheck');
                    content.removeAttr('contenteditable');
                    content.removeAttr('title');
                    content.removeAttr('aria-describedby');
                    content.removeAttr('role');
                    content.removeAttr('aria-label');
                    content.removeClass('cke_editable_inline cke_contents_ltr cke_show_borders cke_editable cke_focus');

                    layer.removeClass('ui-resizable');
                    layer.removeClass('ui-draggable');
                    layer.removeClass('metaslider-active');
                    layer.css('cursor', '');
                    layer.css('background-position', '');
                    layer.css('background-repeat', '');
                    $('.ui-resizable-handle', layer).remove();
                });

                return layers.html();
            },

            init: function() {
                CKEDITOR.disableAutoInline = true;
                CKEDITOR.config.allowedContent = true;
                CKEDITOR.config.language = 'en';

                layerEditor.checkSaveRequest();

                $('.layer').each(function() {
                    layerEditor.makeLayerEditable($(this));
                });

            },

            makeLayerEditable: function(layer) {
                var delete_button = $("<div class='delete_button'>X</div>").click(function() {
                    if (confirm(metasliderpro.areYouSure)) {
                        layer.remove();
                    }
                });

                layer
                    .prepend(delete_button)
                    .append("<div class='drag_handle'>X</div>")
                    .resizable({
                        handles: "se",
                        containment: "#htmlOverlay",
                        grid: 5,
                        stop: function(event, ui) {
                            var width = ui.size.width;
                            $(this).attr('data-width', width);
                            var height = ui.size.height;
                            $(this).attr('data-height', height);
                        }
                    })
                    .draggable({
                        containment: "#htmlOverlay",
                        handle: ".drag_handle",
                        grid: [5,5],
                        stop: function(event, ui) {
                            var top = ui.position.top;
                            $(this).attr('data-top', top);
                            var left = ui.position.left;
                            $(this).attr('data-left', left);
                        }
                    });

                $('.content', layer)
                    .attr('contenteditable', 'true')
                    .focus(function() {
                        $('.metaslider-active').removeClass('metaslider-active');
                        $(this).parent().parent().parent().parent().addClass('metaslider-active');
                        $("#animation_out_delay").val($('.animation_out', layer).attr('data-animation-delay'));
                        $("#animation_in_delay").val($('.animation_in', layer).attr('data-animation-delay'));
                        $("#animation_out").val($('.animation_out', layer).attr('data-animation'));
                        $("#animation_in").val($('.animation_in', layer).attr('data-animation'));
                        $("#layer_padding").val($('.content', layer).attr('data-padding'));
                        $("#layerBgColor").spectrum("set", $('.content_wrap, .ms_content_wrap', layer).css('background-color'));
                    });

                CKEDITOR.inline($('.content', layer).attr('id'));
            }
        };

        // launch the layer editor
        $('.openLayerEditor').live('click', function(e) {
            e.preventDefault();
            $('#colorbox').removeAttr('tabindex');

            var button = $(this);

            if (button.attr('data-height') == "") {
                return alert(metasliderpro.setHeight);
            }

            if (button.attr('data-width') == "") {
                return alert(metasliderpro.setWidth);
            }
            
            var width = parseInt(button.attr('data-width'), 10) < 850 ? 850 : parseInt(button.attr('data-width'), 10);

            var colorbox = jQuery.colorbox({
                html: layerEditor.render(button.attr('data-thumb'), button.attr('data-editor_id'), button.attr('data-width'), button.attr('data-height')),
                transition: "elastic",
                innerHeight: parseInt(button.attr('data-height'), 10) + 152 + 'px',
                innerWidth: width + 'px',
                scrolling: true,
                className: 'layerEditorCbox',
                onCleanup: function() {
                    layerEditor.save_on_close(button.attr('data-editor_id'));
                },
                onComplete: function() {
                    layerEditor.init();

                    // fix for RTL languages when layer editor is > 100% wide
                    if (jQuery('html').attr('dir') == 'rtl' && jQuery('#colorbox').css('left') == '0px') {
                        jQuery('#colorbox').css('left', 'auto');
                    }
                }
            });
        });
    });
}(jQuery));