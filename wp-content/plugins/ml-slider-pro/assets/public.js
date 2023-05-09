(function ( $ ) {

    $.fn.metaslider_scale_layers = function(options) {

        var settings = $.extend({
            downscale_only: false,
            orig_width: 0
        }, options );

        var orig_width = settings.orig_width;
        var new_width  = this.width();

        jQuery('.msHtmlOverlay', this).each(function() {

            if (parseFloat(new_width) >= parseFloat(orig_width) && settings.downscale_only) {
                var multiplier = 1;
                var percentage = 100;
            } else {
                var multiplier = parseFloat(new_width) / parseFloat(orig_width);
                var percentage = multiplier * 100;
            }

            jQuery('.layer', jQuery(this)).each(function() {

                var layer_width  = parseFloat(jQuery(this).attr('data-width'));
                var layer_height = parseFloat(jQuery(this).attr('data-height'));
                var layer_top    = parseFloat(jQuery(this).attr('data-top'));
                var layer_left   = parseFloat(jQuery(this).attr('data-left'));
                var content_padding = parseFloat($('.content', $(this)).attr('data-padding'));

                jQuery(this).css('width',       Math.round(layer_width  * multiplier) + 'px');
                jQuery(this).css('height',      Math.round(layer_height * multiplier) + 'px');
                jQuery(this).css('top',         Math.round(layer_top    * multiplier) + 'px');
                jQuery(this).css('left',        Math.round(layer_left   * multiplier) + 'px');
                jQuery(this).css('font-size',   Math.round(percentage) + '%');
                jQuery(this).css('line-height', Math.round(percentage) + '%');
                
                jQuery('.content', jQuery(this)).css('padding', Math.round(content_padding * multiplier) + 'px');

            });
        });
    }

}( jQuery ));