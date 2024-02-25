var { __ } = wp.i18n; // For Pro translations only

jQuery(function ($) {
    window.metaslider.app.EventManager.$on("metaslider/app-loaded", function (e) {
        /**
         * JavaScript function to match (and return) the video Id
         * of any valid Youtube Url, given as input string.
         *
         * @author: Stephan Schmitz <eyecatchup@gmail.com>
         * @url:    http://stackoverflow.com/a/10315969/624466
         */
        var ytVidId = function (url) {
            var p = /^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|shorts\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/;
            return (url.match(p)) ? RegExp.$1 : false;
        }

        $(document).on('change', 'tr.slide.youtube .metaslider-pro-youtube_url', function (e) {
            var APP = window.parent.metaslider.app.MetaSlider;
            var $field = $(e.target);
            var yt_url = $field.val();

            APP.notifyInfo(
                'metaslider/updating-youtube-video',
                __('Updating YouTube video...', 'ml-slider-pro'),
                true
            );

            var data = {
                action: 'update_youtube_thumbnail',
                slide_id: $field.data('slide-id'),
                video_id: ytVidId(yt_url),
                video_url: yt_url,
                video_type: yt_url.indexOf("shorts") >= 0 ? 'shorts' : 'video',
                nonce: metaslider_youtube.nonce
            };

            // Check if YouTube URL is valid
            if(!data.video_id) {
                APP && APP.notifyError('metaslider/slide-create-failed', 
                    APP.__("Please make sure to enter a valid YouTube video URL", "ml-slider-pro"),
                    true
                );
                return;
            }

            $.post(ajaxurl, data, function(response) {
                if (! response.success) {
                    APP && APP.notifyError('metaslider/youtube-video-not-updated', 
                        APP.__("There was an error updating the YouTube video", "ml-slider-pro"),
                        true
                    );
                    return;
                }

                /**
                 * Updates the image on success
                 */
                var new_image = $('#slide-' + response.data.slide_id + ' .thumb').find('img');
                new_image.attr(
                    'srcset',
                    `${response.data.thumbnail_url_large} 1024w, ${response.data.thumbnail_url_medium} 768w, ${response.data.thumbnail_url_small} 240w`
                );
                new_image.attr('src', response.data.thumbnail_url_small);

                APP && APP.notifySuccess('metaslider/youtube-video-updated', 
                    APP.__("YouTube video updated successfully!", "ml-slider-pro"), 
                    true
                );
            });
        });
    });
});
