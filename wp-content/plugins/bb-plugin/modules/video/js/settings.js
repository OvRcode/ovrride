( function( $ ) {

	FLBuilder.registerModuleHelper( 'video', {

		// rules: {
		// 	service: {
		// 		required: true
		// 	}
		// },

		init: function()
		{

		},

		submit: function()
		{

			var form      = $( '.fl-builder-settings' ),
				enabled     = form.find( 'select[name=schema_enabled]' ).val(),
				name        = form.find( 'input[name=name]' ).val(),
				description = form.find( 'input[name=description]' ).val();
				thumbnail   = form.find( 'input[name=thumbnail]' ).val();
				update      = form.find( 'input[name=up_date]' ).val();

			if( 'no' === enabled ) {
				return true;
			}

			if ( 0 === name.length ) {
				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );
				return false;
			}
			else if ( 0 === description.length ) {
				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );
				return false;
			}
			else if ( 0 === thumbnail.length ) {

				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );

				return false;
			}
			else if( 0 === update.length ) {
				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );
				return false;
			}

			return true;
		}
	});
})(jQuery);
