(function($) {
	"use strict";
	jQuery(document).ready(function($) {

		$(document).on("show_variation", ".single_variation_wrap", function(event, variation) {
			console.log(variation);
		    if (undefined !== variation.woope_text && variation.woope_text && $('.woope-variable-notice').length) {
		    	$('.entry-summary').find('.woope-variable-notice').text(variation.woope_text);
		    } else {
		    	$('.entry-summary').find('.woope-variable-notice').text("");
		    }
		});
	});
})(jQuery);