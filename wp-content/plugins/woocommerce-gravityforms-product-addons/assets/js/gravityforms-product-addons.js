var ajax_price_req;
//See the gravity forms documentation for this function. 
function gform_product_total(formId, total) {
	return update_dynamic_price(total);
}

function update_dynamic_price(gform_total) {
	jQuery('div.product_totals').block({
		message: null,
		overlayCSS: {
			background: '#fff',
			opacity: 0.6
		}
	});

	var base = jQuery('#woocommerce_product_base_price').val();

	if (ajax_price_req) {
		ajax_price_req.abort();
	}

	var opts = "product_id=" + jQuery("#product_id").val() + "&variation_id=" + jQuery("input[name=variation_id]").val();
	opts += '&action=get_updated_price&gform_total=' + gform_total;

	ajax_price_req = jQuery.ajax({
		type: "POST",
		url: woocommerce_params.ajax_url,
		data: opts,
		dataType: 'json',
		success: function (response) {
			jQuery('.formattedBasePrice').html((response.formattedBasePrice));
			jQuery('.formattedVariationTotal').html(response.formattedVariationTotal);
			jQuery('.formattedTotalPrice').html(response.formattedTotalPrice);

			jQuery('div.product_totals').unblock();
		}
	});
	return gform_total;
}

jQuery(document).ready(function ($) {
	if (window.gravityforms_params) {

		console.log(gravityforms_params.previous_page);

		if (gravityforms_params.previous_page === 0 && ($('.woocommerce-message').length)) {
			window.location.hash = '';
		}
		;


		$("form.cart").attr('action', '');
		$('form.cart').attr('id', 'gform_' + gravityforms_params.form_id);

		$('body').delegate('form.cart', 'found_variation', function () {
			try {
				gf_apply_rules(gravityforms_params.form_id, ["0"]);
			} catch (err) {
			}
			gformCalculateTotalPrice(gravityforms_params.form_id);
		});



		$('button[type=submit]', 'form.cart').attr('id', 'gform_submit_button_' + gravityforms_params.form_id).addClass('button gform_button');


		if (gravityforms_params.next_page != 0) {

			$('button[type=submit]', 'form.cart').remove();
			$('div.quantity').remove();
			$('#wl-wrapper').hide();

		} else {

		}

		$('.gform_next_button', 'form.cart').attr('onclick', '');
		$('.gform_next_button', 'form.cart').click(function (event) {
			window.location.hash = '#_form_' + gravityforms_params.form_id;

			$("#gform_target_page_number_" + gravityforms_params.form_id).val(gravityforms_params.next_page);
			$("form.cart").trigger("submit", [true]);

		});

		$('.gform_previous_button', 'form.cart').click(function (event) {
			$("#gform_target_page_number_" + gravityforms_params.form_id).val(gravityforms_params.previous_page);
			$("form.cart").trigger("submit", [true]);
		});
	}
});