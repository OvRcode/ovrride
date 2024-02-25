/**
 * MetaSlider
 */

(function ($) {
    $(function () {

        var getVideotUrl = function (url) {
            var p = /^(https?:\/\/)[^/]+\/[^?#]+\.(webm|mov|mp4)(\?.*)?$/;
            return p.test(url) ? url : false;
        }

        $('body').on('click', '.media-button', function (e) {
            e.preventDefault();

            var video_url = getVideotUrl($('.external_video_url').val());

            if (video_url) {
                var APP = window.parent.metaslider.app.MetaSlider;
                // APP comes from the free version which holds some generic translations
                APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
                    APP.__('Preparing %s slide...', 'ml-slider'),
                    '1'), true);

                var data = {
                    action: 'create_' + metaslider_external_video_slide_type.identifier + '_slide',
                    slider_id: window.parent.metaslider_slider_id,
                    video_url: video_url,
                    nonce: metaslider_external_video_slide_type.nonce
                };

                jQuery.post(ajaxurl, data, function (response) {
                    window.parent.metaslider.after_adding_slide_success(response.data);
                });
            }
        });

        $('.external_video_url').each(function () {
            var elem    = $(this);
            var nextMsg = $('.embed-link-settings');
            var spinner = $('.spinner');
            var button  = $('.media-button');

            // Save current value of element
            elem.data('oldVal', elem.val());
            
            // Look for changes in the value
            elem.bind("propertychange keyup input paste", function (event) {
                // If value has changed...
                if (elem.data('oldVal') != elem.val()) {
                    nextMsg.hide();
                    spinner.show().delay(1000).fadeOut('fast');
                    button.attr('disabled', 'disabled');
                    
                    // Updated stored value
                    elem.data('oldVal', elem.val());

                    if (getVideotUrl(elem.val())) {
                        nextMsg.show();
                        button.removeAttr('disabled');
                    }
                }
            });
        });
    });
}(jQuery));