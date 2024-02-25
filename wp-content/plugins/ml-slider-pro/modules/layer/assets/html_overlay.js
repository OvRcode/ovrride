// @codingStandardsIgnoreStart
// for debug : trace every event
/*
    var originalTrigger = wp.media.view.MediaFrame.Post.prototype.trigger;
    wp.media.view.MediaFrame.Post.prototype.trigger = function(){
    console.log('Event Triggered:', arguments);
    originalTrigger.apply(this, Array.prototype.slice.call(arguments));
    }*/
// custom toolbar : contains the buttons at the bottom
// @codingStandardsIgnoreEnd
wp.media.view.Toolbar.Custom = wp.media.view.Toolbar.extend({
    initialize: function () {
        _.defaults(this.options, {
            event: 'custom_event',
            close: false,
            items: {
                custom_event: {
                    text: metasliderpro.addToSlider, //wp.media.view.l10n.customButton, // added via 'media_view_strings' filter,
                    style: 'primary',
                    priority: 80,
                    requires: false,
                    click: this.customAction
                }
            }
        });

        wp.media.view.Toolbar.prototype.initialize.apply(this, arguments);

        // Enable/disable button if image is selected
        var button = this.$('.media-button-custom_event');
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
    customAction: function () {
        var selection = this.controller.state().get('selection');

        selection.map(function (attachment) {
			attachment = attachment.toJSON();
			var APP = window.parent.metaslider.app.MetaSlider;
			// APP comes from the free version which holds some generic translations
			APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
				APP.__('Preparing %s slide...', 'ml-slider'),
			'1'), true);

            var data = {
                action: 'create_html_overlay_slide',
                slide_id: attachment.id,
                slider_id: window.parent.metaslider_slider_id,
                nonce: metaslider_custom_slide_type.nonce
            };

            jQuery.post(ajaxurl, data, function(response) {
                window.parent.metaslider.after_adding_slide_success(response.data);
            }).fail(function(error) { 
                console.error(error.status,error.statusText);
                APP && APP.notifyError('metaslider/slide-create-failed', 
                    APP && __("This isn't a supported format. Please use JPG, PNG and GIF images.", "ml-slider-pro"),
                    true
                );
            });
        });
    }
});

// custom content : this view contains the main panel UI
wp.media.view.Custom = wp.media.View.extend({
    className: 'media-custom',

    // bind view events
    events: {
        'input': 'custom_update',
        'keyup': 'custom_update',
        'change': 'custom_update'
    },

    initialize: function () {

        // create an input
        this.input = this.make('input', {
            type: 'text',
            value: this.model.get('custom_data')
        });

        // insert it in the view
        this.$el.append(this.input);

        // re-render the view when the model changes
        this.model.on('change:custom_data', this.render, this);
    },

    render: function () {
        this.input.value = this.model.get('custom_data');
        return this;
    },

    custom_update: function (event) {
        this.model.set('custom_data', event.target.value);
    }
});


// supersede the default MediaFrame.Post view
var oldMediaFrame = wp.media.view.MediaFrame.Post;
wp.media.view.MediaFrame.Post = oldMediaFrame.extend({

    initialize: function () {
        oldMediaFrame.prototype.initialize.apply(this, arguments);

        this.states.add([

            // Main states.
            new wp.media.controller.Library({
                id: 'insert-html',
                title: wp.media.view.l10n.insertHtmlOverlay,
                priority: 999,
                toolbar: 'add-html-overlay-slide',
                filterable: 'image',
                multiple: false,
                editable: true,
                allowLocalEdits: true,
                displaySettings: true,
                displayUserSettings: true,
                library: wp.media.query(_.defaults({
                    type: 'image' // Override type to only show images
                }, this.options.library))
            }),
        ]);

        this.on('toolbar:create:add-html-overlay-slide', this.createCustomToolbar, this);
        this.on('toolbar:render:add-html-overlay-slide', this.renderCustomToolbar, this);

        // Enable "Add to slideshow" button when image is selected or uploaded
        this.on('selection:toggle', this.imageSelection, this);
        this.on('library:selection:add', this.imageSelection, this);
        this.on('open', this.imageSelection, this);
    },

    createCustomToolbar: function (toolbar) {
        toolbar.view = new wp.media.view.Toolbar.Custom({
            controller: this
        });
    },

    imageSelection: function () {
        if(typeof this.content.view._state !== 'undefined' 
            && this.content.view._state === 'insert-html') {

            var selectedImages = this.state().get('selection').length;
            var button = this.$('.media-button-custom_event');

            if (selectedImages === 1) {
                button.prop('disabled', false);
            } else {
                button.prop('disabled', true);
            }
        }
    },
});
