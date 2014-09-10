jQuery(document).ready(function($) {

	/** Cart Handling */
	$supports_html5_storage = ( 'sessionStorage' in window && window['sessionStorage'] !== null );

	$fragment_refresh = {
		url: woocommerce_params.ajax_url,
		type: 'POST',
		data: { action: 'woocommerce_get_refreshed_fragments' },
		success: function( data ) {
			if ( data && data.fragments ) {

				$.each( data.fragments, function( key, value ) {
					$(key).replaceWith(value);
				});

				if ( $supports_html5_storage ) {
					sessionStorage.setItem( "wc_fragments", JSON.stringify( data.fragments ) );
					sessionStorage.setItem( "wc_cart_hash", data.cart_hash );
				}

				$('body').trigger( 'wc_fragments_refreshed' );
			}
		}
	};

	if ( $supports_html5_storage ) {

		$('body').bind( 'added_to_cart', function( event, fragments, cart_hash ) {
			sessionStorage.setItem( "wc_fragments", JSON.stringify( fragments ) );
			sessionStorage.setItem( "wc_cart_hash", cart_hash );
		});

		try {
			var wc_fragments = $.parseJSON( sessionStorage.getItem( "wc_fragments" ) );
			var cart_hash    = sessionStorage.getItem( "wc_cart_hash" );
			var cookie_hash  = $.cookie( "woocommerce_cart_hash" );

			if ( cart_hash == null || cart_hash == undefined || cart_hash == '' )
				cart_hash = '';

			if ( cookie_hash == null || cookie_hash == undefined || cookie_hash == '' )
				cookie_hash = '';

			if ( wc_fragments && wc_fragments['div.widget_shopping_cart_content'] && cart_hash == cookie_hash ) {

				$.each( wc_fragments, function( key, value ) {
					$(key).replaceWith(value);
				});

				$('body').trigger( 'wc_fragments_loaded' );

			} else {
				throw "No fragment";
			}

		} catch(err) {
			$.ajax( $fragment_refresh );
		}

	} else {
		$.ajax( $fragment_refresh );
	}

	/* Cart hiding */
	if ( $.cookie( "woocommerce_items_in_cart" ) > 0 )
		$('.hide_cart_widget_if_empty').closest('.widget_shopping_cart').show();
	else
		$('.hide_cart_widget_if_empty').closest('.widget_shopping_cart').hide();

	$('body').bind( 'adding_to_cart', function() {
		$('.hide_cart_widget_if_empty').closest('.widget_shopping_cart').show();
	} );

});