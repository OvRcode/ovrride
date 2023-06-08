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

        $('tr.slide.youtube .metaslider-pro-youtube_url').on('change', function (e) {
            var APP = window.parent.metaslider.app.MetaSlider;
            var $field = $(e.target);

            APP.notifyInfo(
                'metaslider/updating-youtube-video',
                APP.__('Updating YouTube video...', 'ml-slider-pro'),
                true
            );

            var data = {
                action: 'update_youtube_thumbnail',
                slide_id: $field.data('slide-id'),
                video_id: ytVidId($field.val()),
                nonce: metaslider_youtube.nonce
            };

            $.post(ajaxurl, data, function(response) {
                if (! response.success) {
                    APP && APP.notifyError('metaslider/youtube-video-not-updated', null, true);
                    return;
                }

                $('#slide-' + response.data.slide_id + ' .metaslider-slide-thumb .thumb').css('background-image', 'url("' + response.data.thumbnail + '")');
                APP && APP.notifySuccess('metaslider/youtube-video-updated', null, true);
            });
        });
    });
});
