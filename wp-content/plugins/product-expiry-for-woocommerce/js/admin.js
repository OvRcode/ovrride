jQuery(document).ready(function($) {
	$('.spinner').css('visibility', 'visible');
	$('.spinner').hide();
	$('.success-text').hide();
	$('.woope-form').submit(function(event) {
		event.preventDefault();
		var formData = $(this).serialize();
		$('.spinner').show();
		$('.success-text').hide();
		$.post(ajaxurl, formData, function(resp) {
			$('.spinner').hide();
			$('.success-text').show();
		});
	});
});