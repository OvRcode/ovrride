window.GFToken = null;

( function( $ ) {
	
	GFToken = function( args ) {
		
		for ( var prop in args ) {
			if ( args.hasOwnProperty( prop ) )
				this[prop] = args[prop];
		}
		
		this.form = $( '#gform_' + this.formId );

		this.init = function() {
		
			var GFTokenObj = this;

			/* Initialize spinner. */
			if ( ! this.isAjax )
				gformInitSpinner( this.formId );

			/* If multipage form, run on gform_page_loaded. */
			if ( this.hasPages ) {

				$( document ).bind( 'gform_page_loaded', function( event, form_id, current_page ) {
				
					if ( form_id != GFTokenObj.formId)
						return;
					
					if ( current_page != GFTokenObj.pageCount)
						GFTokenObj.saveEntryData();
			
				} );

			}
			
			this.form.submit( function() {
				GFTokenObj.onSubmit();
			} );
			
		};

		this.onSubmit = function() {

			if ( this.form.data('gftokensubmitting') ) {
				return;
			} else {
				event.preventDefault();
				this.form.data( 'gftokensubmitting', true );
			}

			this.saveEntryData();
			this.processTokens();

		}
		
		this.processTokens = function() {
			
			/* Setup object to store token information. */
			var tokens = {};
			
			/* Process feeds. */
			for ( var feed_id in this.feeds ) {
				
				/* Create new feed object so we can store the billing information. */
				var active_feed = this.feeds[feed_id],
					feed = {
						'billing_fields': {},
						'id': active_feed.id,
						'name': active_feed.name
					},
					token = {
						'feed_id': active_feed.id,
						'response': ''
					};
				
				/* Add billing information to feed object. */
				for ( var billing_field in active_feed.billing_fields ) {
					
					field_id = active_feed.billing_fields[ billing_field ];
					feed.billing_fields[ billing_field ] = this.entry_data[ field_id ];
					
				}
				
				/* Get credit card token response. */
				token.response = window[ this.callback ].createToken( feed );
				
				/* Add token response to tokens array. */
				tokens[ active_feed.id ] = token;
				
			}
			
			/* Add tokens to form. */
			this.form.find( this.responseField ).val( $.toJSON( tokens ) );
			
			/* Submit the form. */
			this.form.submit();
			
		}

		this.saveEntryData = function() {
			
			var GFPaymentObj = this,
				input_prefix = 'input_' + this.formId + '_';
				
			if ( ! this.entry_data )
				this.entry_data = {};
			
			this.form.find( 'input[id^="' + input_prefix + '"], select[id^="' + input_prefix + '"], textarea[id^="' + input_prefix + '"]' ).each( function() {
				
				var input_id = $( this ).attr( 'id' ).replace( input_prefix, '' ).replace( '_', '.' ); 
				
				if ( $.inArray( input_id, GFPaymentObj.fields ) >= 0 )				
					GFPaymentObj.entry_data[ input_id ] = $( this ).val();
				
			} );
		
		}

		this.init();
		
	}
	
} )( jQuery );