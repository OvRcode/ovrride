var { __ } = wp.i18n; // For Pro translations only

// Toolbar for Video
wp.media.view.Toolbar.Localvideo = wp.media.view.Toolbar.extend({
    initialize: function () {
        _.defaults(this.options, {
            event: 'local_video_event',
            close: false,
            items: {
                local_video_event: {
                    text: metasliderpro.addToSlider, //wp.media.view.l10n.customButton, // added via 'media_view_strings' filter,
                    style: 'primary',
                    priority: 80,
                    requires: false,
                    click: this.localvideoAction
                }
            }
        });

        wp.media.view.Toolbar.prototype.initialize.apply(this, arguments);

        // Enable/disable button if video is selected
        var button = this.$('.media-button-local_video_event');
        if (this.controller.state().get('selection').length === 0) {
            button.prop('disabled', true);
        } else {
            button.prop('disabled', false);
        }

    },

    // called each time the model changes
    refresh: function () {
        // call the parent refresh
        wp.media.view.Toolbar.prototype.refresh.apply(this, arguments);
    },

    // triggered when the button is clicked
    localvideoAction: function () {
        var selection = this.controller.state().get('selection');

        selection.map(function (attachment) {
			attachment = attachment.toJSON();
			var APP = window.parent.metaslider.app.MetaSlider;
			// APP comes from the free version which holds some generic translations
			APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
				APP.__('Preparing %s slide...', 'ml-slider'),
			'1'), true);

            var data = {
                action: 'create_local_video_slide',
                video_id: attachment.id,
                slider_id: window.parent.metaslider_slider_id,
                nonce: metaslider_local_video.nonce
            };

            jQuery.post(ajaxurl, data, function(response) {
                window.parent.metaslider.after_adding_slide_success(response.data);
            }).fail(function(error) { 
                console.error(error.status,error.statusText);
                APP && APP.notifyError('metaslider/slide-create-failed', 
                    APP && __("This isn't a supported video format. Please use MP4, WebM, or MOV videos.", "ml-slider-pro"),
                    true
                );
            });
        });
    }
});

// supersede the default MediaFrame.Post view for Video
var oldMediaFrameLV = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrameLV.extend({

    initialize: function () {
        oldMediaFrameLV.prototype.initialize.apply(this, arguments);
        
        this.states.add([
            // Main states.
            new wp.media.controller.Library({
                id: 'insert-local-video',
                title: wp.media.view.l10n.insertLocalVideo,
                priority: 999,
                toolbar: 'add-local-video-slide',
                filterable: 'video',
                multiple: false,
                editable: true,
                allowLocalEdits: true,
                displaySettings: true,
                displayUserSettings: true,
                library: wp.media.query(_.defaults({
                    type: 'video' // Override type to only show videos
                }, this.options.library))
            }),
        ]);

        this.on('toolbar:create:add-local-video-slide', this.createLocalvideoToolbar, this);
        this.on('toolbar:render:add-local-video-slide', this.renderLocalvideoToolbar, this);

        // Enable "Add to slideshow" button when video is selected or uploaded
        this.on('selection:toggle', this.videoSelection, this);
        this.on('library:selection:add', this.videoSelection, this);
        this.on('open', this.videoSelection, this);
    },

    createLocalvideoToolbar: function (toolbar) {
        toolbar.view = new wp.media.view.Toolbar.Localvideo({
            controller: this
        });
    },

    videoSelection: function () {
        if(typeof this.content.view._state !== 'undefined' 
            && this.content.view._state === 'insert-local-video') {

            var selectedVideos = this.state().get('selection').length;
            var button = this.$('.media-button-local_video_event');

            if (selectedVideos === 1) {
                button.prop('disabled', false);
            } else {
                button.prop('disabled', true);
            }
        }
    },
});

window.jQuery(function ($) {
    const APP = window.metaslider.app ? window.metaslider.app.MetaSlider : null;
    
    if(! APP || ! window.metaslider.add_image_apis || ! window.metaslider.remove_image_apis) {
        console.error('At least one global var is null! Probably MetaSlider free plugin needs to be updated.');
        return;
    }

    // Remove Unsplash tab when creating a new Local video slide
    window.create_slides.on('open activate uploader:ready', function() {
        if ($('#menu-item-insert-local-video.media-menu-item.active').length) {
            window.metaslider.remove_image_apis();
        }
    });

    /**
     * Reset selection to avoid errors when opening for video selection change
     */
    window.create_slides.on('escape', function() {
        window.create_slides.state()?.get('selection')?.reset();
    });

    // Changing the video
    $('.metaslider').on('click', '.update-video', function (event) {
        event.preventDefault();
        updateMedia(this, 'video');
    });

    // Changing the cover image
    $('.metaslider').on('click', '.update-cover-image', function (event) {
        event.preventDefault();
        updateMedia(this, 'image');
    });

    // Changing the text track
    $('.metaslider').on('click', '.update-text-track', function (event) {
        event.preventDefault();
        updateMedia(this, 'text');
    });

    // Removing text track
    $('.metaslider').on('click', '.remove-text-track', function (event) {
        event.preventDefault();
        
        var $this = $(this);

        var data = {
            action: 'remove_slide_track',
            slide_id: $this.data('slideId'),
            track_id: $this.data('attachment-id'),
            _wpnonce: metaslider_local_video.update_slide_nonce
        };

        $.ajax({
            url: metaslider.ajaxurl,
            data: data,
            type: 'POST',
            error: function (error) {
                console.error(error.status,error.statusText);
                
                APP && APP.notifyError('metaslider/slide-update-failed', 
                    APP && __("There was an error removing the text track", "ml-slider-pro"),
                    true
                );
            },
            success: function (response) {

                // Updates the text track url
                $('#slide-' + $this.data('slideId') + ' input.track_url').val('');
                    
                // set attachment ID as empty
                $('#slide-' + $this.data('slideId') + ' .update-text-track').data('attachment-id', '');
                $('#slide-' + $this.data('slideId') + ' .remove-text-track').data('attachment-id', '');

                if (response.data.video_url) {
                    $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                }

                APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Text track removed successfully', 'ml-slider-pro'), true)
            }
        });
    });

    /**
     * Handles changing a video or image
     * 
     * @param {object} elmnt      Button that triggers this function. e.g. this
     * @param {string} media_type 'video', 'image' or 'text'
     * 
     * @return void
     */
    const updateMedia = function(elmnt, media_type) {
        var $this = $(elmnt);
        var current_id = $this.data('attachment-id');

        /**
         * Opens up a media window showing media
         */
        update_slide_frame = wp.media.frames.file_frame = wp.media({
            title: MetaSlider_Helpers.capitalize(
                metaslider_local_video[`update_${media_type}_text`]
            ),
            library: {
                type: media_type
            },
            button: {
                text: MetaSlider_Helpers.capitalize($this.attr('data-button-text'))
            }
        });

        /**
         * Selects current media
         */
        update_slide_frame.on('open', function () {
            if (current_id) {
                var selection = update_slide_frame.state().get('selection');
                selection.reset([wp.media.attachment(current_id)]);
            }
            
            // Add various image APIs
            if (media_type === 'image') {
                window.metaslider.add_image_apis($this.data('slideType'), $this.data('slideId'));
            }
        });

        /**
         * Reset selection to avoid errors on second open for video selection change
         */
        update_slide_frame.on('escape', function() {
            update_slide_frame.state().get('selection').reset();
        });

        /**
         * Open media modal
         */
        update_slide_frame.open();

        /**
         * Handles changing a media in DB and UI
         */
        update_slide_frame.on('select', function () {
            var selection = update_slide_frame.state().get('selection');
            selection.map(function (attachment) {
                attachment = attachment.toJSON();
                new_media_id = attachment.id;
                selected_item = attachment;
            });

            APP && APP.notifyInfo('metaslider/updating-slide', APP.__('Updating slide...', 'ml-slider'), true)

            // Remove the events for image APIs
            if(media_type === 'image') {
                window.metaslider.remove_image_apis();
            }

            /**
             * Updates the meta information on the slide
             */
            var data = {
                action: 'update_slide_' + media_type,
                slide_id: $this.data('slideId'),
                slider_id: window.parent.metaslider_slider_id
            };

            if( media_type === 'video' ) {
                data._wpnonce = metaslider_local_video.update_slide_nonce,
                data.video_id = new_media_id;
            } else if( media_type === 'image' ) {
                // Image - We use the nonce and action from MetaSlider Free from wp_ajax_update_slide_image
                data._wpnonce = metaslider.update_slide_image_nonce,
                data.image_id = new_media_id;
            } else if( media_type === 'text' ) {
                // Text track
                data.action = 'update_slide_track',
                data._wpnonce = metaslider_local_video.update_slide_nonce,
                data.track_id = new_media_id;
            } else {
                console.error('Invalid media type', media_type);
            }

            $.ajax({
                url: metaslider.ajaxurl,
                data: data,
                type: 'POST',
                error: function (error) {
                    console.error(error.status,error.statusText);
                    
                    if( media_type === 'video' ) {
                        APP && APP.notifyError('metaslider/slide-update-failed', 
                            APP && __("This isn't a supported video format. Please use MP4, WebM, or MOV videos.", "ml-slider-pro"),
                            true
                        );
                    } else if( media_type === 'image' ) {
                        // Cover image
                        APP && APP.notifyError('metaslider/slide-update-failed', 
                            APP && __("This isn't a supported image format. Please use JPG, PNG, or GIF images.", "ml-slider-pro"),
                            true
                        );
                    } else if( media_type === 'text' ) {
                        // Text track
                        APP && APP.notifyError('metaslider/slide-update-failed', 
                            APP && __("This isn't a supported text track format. Please use TXT or VTT files.", "ml-slider-pro"),
                            true
                        );
                    } else {
                        // Nothing to do here
                    }
                },
                success: function (response) {
                    if( media_type === 'video' ) {
                        /**
                         * Updates the video embed
                         */
                        var $embed  = '<video loop="loop" muted="muted"'
                        $embed      += ' onmouseover="this.play()"';
                        $embed      += ' onmouseout="this.pause()"';
                        $embed      += ' style="object-fit: cover; height: 100%; width: 100%;">';
                        $embed      += ' <source src="'  + response.data.video_url + '"'
                        $embed      += ' type="' + response.data.video_type + '"></video>';
                        
                        $('#slide-' + $this.data('slideId') + ' .thumb').html($embed);
                        $('#slide-' + $this.data('slideId') + ' input.video_url').val(response.data.video_url);
                        
                        // set new attachment ID
                        var $edited_slide_elms = $('#slide-' + $this.data('slideId') + ', #slide-' + $this.data('slideId') + ' .update-video');
                        $edited_slide_elms.data('attachment-id', selected_item.id);

                        if (response.data.video_url) {
                            $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                        }

                        APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video updated successfully', 'ml-slider-pro'), true)
                    } else if( media_type === 'image' ) {

                        /**
                         * Updates the cover image preview
                         */
                        var $cover_preview = $('#slide-' + $this.data('slideId') + ' .update-cover-image');
                        $cover_preview.css('background-image', 'url(' + response.data.thumbnail_url_small + ')');
                        $cover_preview.html('');
                        $cover_preview.data('attachment-id', selected_item.id);

                        if (response.data.thumbnail_url_small) {
                            $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                        }

                        APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Cover image updated successfully', 'ml-slider-pro'), true)
                    } else if( media_type === 'text' ) {

                        /**
                         * Updates the text track url
                         */
                        $('#slide-' + $this.data('slideId') + ' input.track_url').val(response.data.track_url);
                        
                        // set new attachment ID
                        $('#slide-' + $this.data('slideId') + ' .update-text-track').data('attachment-id', selected_item.id);
                        $('#slide-' + $this.data('slideId') + ' .remove-text-track').data('attachment-id', selected_item.id);

                        if (response.data.track_url) {
                            $('#slide-' + $this.data('slideId')).trigger('metaslider/attachment/updated', response.data);
                        }

                        APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Text track updated successfully', 'ml-slider-pro'), true)
                    } else {
                        // Nothing to do here
                    }

                    // TODO: run a function in SlideViewer.vue to replace this
                    $(".metaslider table#metaslider-slides-list").trigger('resizeSlides');
                }
            });
        });

        update_slide_frame.on('close', function () {
            if(media_type === 'image') {
                window.metaslider.remove_image_apis();
            }
        });
    }
});