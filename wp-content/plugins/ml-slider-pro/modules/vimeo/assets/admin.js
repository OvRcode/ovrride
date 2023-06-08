jQuery(function ($) {
    window.metaslider.app.EventManager.$on("metaslider/app-loaded", function (e) {
        /**
         * Vimeo Video ID
         *
         * @param  {string} url URL
         * @return {string}
         */
        function vimeoVidId(url) {
            // look for a string with 'vimeo', then whatever, then a
            // forward slash and a group of digits.
            var match = /(vimeo(pro)?\.com)\/(?:[^\d]+)?(\d+)\??(.*)?$/.exec(url);
            // if the match isn't null (i.e. it matched)
            if (match) {
                // the grouped/matched digits from the regex
                return match[3];
            }

            return false;
        }

        $('tr.slide.vimeo .metaslider-pro-vimeo_url').on('change', function (e) {
            var APP = window.parent.metaslider.app.MetaSlider;
            var $field = $(e.target);

            APP.notifyInfo(
                'metaslider/updating-vimeo-video',
                APP.__('Updating Vimeo video...', 'ml-slider-pro'),
                true
            );

            var data = {
                action: 'update_vimeo_thumbnail',
                slide_id: $field.data('slide-id'),
                video_id: vimeoVidId($field.val()),
                nonce: metaslider_vimeo.nonce
            };

            $.post(ajaxurl, data, function(response) {
                if (! response.success) {
                    APP && APP.notifyError('metaslider/vimeo-video-not-updated', null, true);
                    return;
                }

                $('#slide-' + response.data.slide_id + ' .metaslider-slide-thumb .thumb').css('background-image', 'url("' + response.data.thumbnail + '")');
                APP && APP.notifySuccess('metaslider/vimeo-video-updated', null, true);
            });
        });
    });
});
