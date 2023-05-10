;(function ( $, window ) {
	var userExportForm = function( $form ) {
		this.$form = $form;
		this.xhr   = false;

		this.$form.find( '.user-exporter-progress' ).val( 0 );
		this.processStep = this.processStep.bind( this );
		$form.on( 'submit', { userExportForm: this }, this.onSubmit );
	};

	userExportForm.prototype.onSubmit = function( event ) {
		event.preventDefault();

        $( "html, body" ).animate({ scrollTop: 0 }, "slow" );

		var currentDate    = new Date(),
			day            = currentDate.getDate(),
			month          = currentDate.getMonth() + 1,
			year           = currentDate.getFullYear(),
			timestamp      = currentDate.getTime(),
			filename       = 'user-export-' + day + '-' + month + '-' + year + '-' + timestamp + '.csv';

		event.data.userExportForm.$form.addClass( 'user-exporter__exporting' );
		event.data.userExportForm.$form.find( '.user-exporter-progress' ).val( 0 );
        event.data.userExportForm.$form.find( '.user-exporter-progress-value' ).text( acui_export_js_object.starting_process + " - 0%" );
		event.data.userExportForm.processStep( 1, $( this ).serialize(), '', filename );
	};

	userExportForm.prototype.processStep = function( step, data, filename ) {
		var $this = this;
        var frontend = $( '[name="acui_frontend_export"]' ).val();
        var convert_timestamp, order_fields_alphabetically, double_encapsulate_serialized_values;

        if( frontend == 1 ){
            convert_timestamp = $this.$form.find( '[name="convert_timestamp"]' ).val();
            order_fields_alphabetically = $this.$form.find( '[name="order_fields_alphabetically"]' ).val();
            double_encapsulate_serialized_values = $this.$form.find( '[name="double_encapsulate_serialized_values"]' ).val();
        }
        else{
            convert_timestamp = $this.$form.find( '[name="convert_timestamp"]' ).is( ":checked");
            order_fields_alphabetically = $this.$form.find( '[name="order_fields_alphabetically"]' ).is( ":checked");
            double_encapsulate_serialized_values = $this.$form.find( '[name="double_encapsulate_serialized_values"]' ).is( ":checked");
        }

		$.ajax( {
			type: 'POST',
			url: acui_export_js_object.ajaxurl,
			data: {
				form: data,
				action: 'acui_export_users_csv',
				step: step,
				filename: filename,
				delimiter: $this.$form.find( '[name="delimiter"]' ).val(),
				role: $this.$form.find( '[name="role"]' ).val(),
				from: $this.$form.find( '[name="from"]' ).val(),
				to: $this.$form.find( '[name="to"]' ).val(),
				convert_timestamp: convert_timestamp,
				datetime_format: $this.$form.find( '[name="datetime_format"]' ).val(),
				order_fields_alphabetically: order_fields_alphabetically,
				double_encapsulate_serialized_values: double_encapsulate_serialized_values,
				columns: $this.$form.find( '[name="columns"]' ).val(),
				orderby: $this.$form.find( '[name="orderby"]' ).val(),
				order: $this.$form.find( '[name="order"]' ).val(),
				security: $this.$form.find( '#security' ).val(),
			},
			dataType: 'json',
			success: function( response ) {
                if ( response.success ) {
					if ( 'done' === response.data.step ) {
						$this.$form.find('.user-exporter-progress').val( response.data.percentage );
                        $this.$form.find('.user-exporter-progress-value').text( response.data.percentage + "%" );
						window.location = response.data.url;
						setTimeout( function() {
							$this.$form.removeClass( 'user-exporter__exporting' );
							$( '#acui_download_csv_wrapper > td > input' ).prop( 'disabled', false );

							if( response.data.results != '' ){
								$( '#acui_export_results' )
									.html( response.data.results )
									.show()
							}
						}, 2000 );
					} else {
						$this.$form.find( '.user-exporter-progress' ).val( response.data.percentage );
                        $this.$form.find( '.user-exporter-progress-value' ).text( acui_export_js_object.step + " " + response.data.step + " " + acui_export_js_object.of_approximately +  " " + response.data.total_steps + " " + acui_export_js_object.steps + " - " + response.data.percentage + "%" );
						$this.processStep( parseInt( response.data.step, 10 ), data, filename );
					}
				}
			}
		} ).fail( function( response ) {
			window.console.log( response );
            alert( acui_export_js_object.error_thrown );
		} );
	};

	$.fn.user_export_form = function() {
		new userExportForm( this );
		return this;
	};

	$( '#acui_exporter' ).user_export_form();
})( jQuery, window );