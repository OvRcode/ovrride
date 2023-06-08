/**
 * MetaSlider
 */

(function ($) {
    $(function () {
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

        var showSpinner = function () {
            $('.spinner').show().delay(1000).fadeOut('fast');
        }

        $('.youtube_url').each(function () {
            var elem = $(this);
            // Save current value of element
            elem.data('oldVal', elem.val());
            // Look for changes in the value
            elem.bind("propertychange keyup input paste", function (event) {
                // If value has changed...
                if (elem.data('oldVal') != elem.val()) {
                    showSpinner();
                    $('.media-button').attr('disabled', 'disabled');
                    // Updated stored value
                    elem.data('oldVal', elem.val());

                    var yt_id = ytVidId(elem.val());

                    if (yt_id) {
                        $('.embed-link-settings').html("<iframe src='//www.youtube.com/embed/" + yt_id + "?HD=1;rel=0;showinfo=0' frameborder='0'></iframe>");
                        $('.media-button').removeAttr('disabled');
                    }
                }
            });
        });

        $('body').on('click', '.media-button', function (e) {
            e.preventDefault();

            var yt_id = ytVidId($('.youtube_url').val());
            var yt_type = 'video';
            var yt_url = $('.youtube_url').val();

            if (yt_id) {
                var APP = window.parent.metaslider.app.MetaSlider;
                // APP comes from the free version which holds some generic translations
                APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
                    APP.__('Preparing %s slide...', 'ml-slider'),
                    '1'), true);
                
                if (yt_url.indexOf("shorts") >= 0) {
                    yt_type = 'shorts';
                }

                var data = {
                    action: 'create_youtube_slide',
                    video_id: yt_id,
                    video_type: yt_type,
                    video_url: yt_url,
                    slider_id: window.parent.metaslider_slider_id,
                    nonce: metaslider_custom_slide_type.nonce
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
