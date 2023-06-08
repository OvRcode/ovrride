/**
 * MetaSlider
 */

(function ($) {
    $(function () {
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
        }//end vimeoVidId()


        var showSpinner = function () {
            $('.spinner').show().delay(1000).fadeOut('fast');
        }

        $('.vimeo_url').each(function () {
            var elem = $(this);

            // Save current value of element
            elem.data('oldVal', elem.val());
            // Look for changes in the value
            elem.bind("propertychange keyup input paste", function (event) {
                // If value has changed...
                if (elem.data('oldVal') != elem.val()) {
                    showSpinner();
                    // Updated stored value
                    elem.data('oldVal', elem.val());

                    var vimeo_id = vimeoVidId(elem.val());

                    if (vimeo_id) {
                        $('.embed-link-settings').html("<iframe src='//player.vimeo.com/video/" + vimeo_id + "?title=0&portait=0&byline=0' frameborder='0'></iframe>");
                        $('.media-button').removeAttr('disabled');
                    }
                }
            });
        });

        $('body').on('click', '.media-button', function (e) {
            e.preventDefault();
            var vimeo_id = vimeoVidId($('.vimeo_url').val());

            if (vimeo_id) {
                var APP = window.parent.metaslider.app.MetaSlider;
                // APP comes from the free version which holds some generic translations
                APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
                    APP.__('Preparing %s slide...', 'ml-slider'),
                    '1'), true);

                var data = {
                    action: 'create_vimeo_slide',
                    video_id: vimeo_id,
                    slider_id: window.parent.metaslider_slider_id,
                    nonce: metaslider_custom_slide_type.nonce,
                };

                jQuery.post(ajaxurl, data, function (response) {
                    window.parent.jQuery(".metaslider table#metaslider-slides-list").append(response);
                    var APP = window.parent.metaslider.app.MetaSlider;
                    APP && APP.notifySuccess('metaslider/slides-created', null, true);
                    window.parent.jQuery(".media-modal-close").click();
                });
            }
        });
    });
}(jQuery));
