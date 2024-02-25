window.jQuery(function ($) {

    // Display delay settings only when "Custom delay for this slide" is enabled
    $(document).on('change', '.delay .ms-switch-button input[name*="[delay]"]', function() {
        var el  = $(this);
        var id  = slideIdAttr(el);

        if (!id) return;

        var content = $(`#${id} .delay_wrapper_settings`);

        if(el.prop('checked')) {
            content.show();
        } else {
            content.hide();
        }
    });

    /**
     * Get slide id attribute. e.g. `slide-${id}`
     * 
     * @param {string} el The input[name*="[delay] element that enables/disabled schedule
     * 
     * @return string
     */
    var slideIdAttr = function (el) {
        return el.parents('tr.slide').attr('id') || null;
    }
});
