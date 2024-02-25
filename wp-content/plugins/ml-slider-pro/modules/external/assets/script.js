/**
 * MetaSlider
 */

(function ($) {
    $(function () {
        $('body').on('click', '.media-button', function (e) {
            e.preventDefault();

            var APP = window.parent.metaslider.app.MetaSlider;
            // APP comes from the free version which holds some generic translations
            APP && APP.notifyInfo('metaslider/creating-slides', APP.sprintf(
                APP.__('Preparing %s slide...', 'ml-slider'),
                '1'), true);

            var data = {
                action: 'create_' + metaslider_custom_slide_type.identifier + '_slide',
                slider_id: window.parent.metaslider_slider_id,
                nonce: metaslider_custom_slide_type.nonce
            };

            jQuery.post(ajaxurl, data, function (response) {
                window.parent.metaslider.after_adding_slide_success(response.data);
            });
        });
    });
}(jQuery));
