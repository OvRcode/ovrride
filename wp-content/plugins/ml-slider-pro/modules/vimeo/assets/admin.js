var { __ } = wp.i18n; // For Pro translations only

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

        $(document).on('change', 'tr.slide.vimeo .metaslider-pro-vimeo_url', function (e) {
            var APP = window.parent.metaslider.app.MetaSlider;
            var $field = $(e.target);

            APP.notifyInfo(
                'metaslider/updating-vimeo-video',
                __('Updating Vimeo video...', 'ml-slider-pro'),
                true
            );

            var data = {
                action: 'update_vimeo_thumbnail',
                slide_id: $field.data('slide-id'),
                video_id: vimeoVidId($field.val()),
                nonce: metaslider_vimeo.nonce
            };

            // Check if YouTube URL is valid
            if(!data.video_id) {
                APP && APP.notifyError('metaslider/slide-create-failed', 
                    APP.__("Please make sure to enter a valid Vimeo video URL", "ml-slider-pro"),
                    true
                );
                return;
            }

            $.post(ajaxurl, data, function(response) {
                if (! response.success) {
                    APP && APP.notifyError('metaslider/vimeo-video-not-updated', 
                        APP.__("There was an error updating the Vimeo video", "ml-slider-pro"),
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

                APP && APP.notifySuccess('metaslider/vimeo-video-updated', 
                    APP.__("Vimeo video updated successfully!", "ml-slider-pro"), 
                    true
                );
            });
        });
    });
});
