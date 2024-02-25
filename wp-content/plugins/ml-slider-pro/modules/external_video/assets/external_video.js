window.jQuery(function ($) {

    $(document).on('change', '.metaslider .external_video_slide input.video_url', function() {
        var el      = $(this);
        var url     = el.val();
        var thumb   = el.parents('.slide').find('.thumb');

        var APP = window.parent.metaslider.app.MetaSlider;

        // Visual indicators a request is in progress
        el.prop('disabled', true);
        thumb.css('opacity', 0.5);

        $.ajax({
            url: metaslider.ajaxurl,
            data: {
                action: 'update_external_video_slide',
                slide_id: el.data('slideId'),
                slider_id: window.parent.metaslider_slider_id,
                video_url: url,
                nonce: metaslider_external_video.update_slide_nonce
            },
            type: 'POST',
            error: function (error) {
                console.error(error.status,error.statusText);
                
                el.prop('disabled', false);
                thumb.css('opacity', 1);

                el.css('border-color','#b32d2e');
                APP && APP.notifyError('metaslider/slide-update-failed', 
                    APP && __("This isn't a supported video format. Please use MP4, WebM, or MOV videos.", "ml-slider-pro"),
                    true
                );
            },
            success: function (response) {
                console.log(response);

                var embed  = '<video loop="loop" muted="muted"'
                embed      += ' onmouseover="this.play()"';
                embed      += ' onmouseout="this.pause()"';
                embed      += ' style="object-fit: cover; height: 100%; width: 100%;">';
                embed      += ' <source src="'  + response.data.video_url + '"'
                embed      += ' type="' + response.data.mime_type + '"></video>';

                thumb.html(embed);

                el.css('border-color','');
                APP && APP.notifySuccess('metaslider/slide-updated', APP.__('Video updated successfully', 'ml-slider-pro'), true)
            },
            complete: function () {
                console.log('complete');
                el.prop('disabled', false);
                thumb.css('opacity', 1);
            }
        });
    });
});