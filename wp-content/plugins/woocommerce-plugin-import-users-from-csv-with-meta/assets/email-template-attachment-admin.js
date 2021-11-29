 jQuery(document).ready(function($){
	'use strict';
	var attachmentFrame;

	$( '#acui_email_option_upload_button,#acui_email_template_upload_button' ).click(function(e) {
		var btn = e.target;

		if ( !btn  ) return;

		e.preventDefault();

		attachmentFrame = wp.media.frames.attachmentFrame = wp.media({
			title: email_template_attachment_admin.title,
			button: { text:  email_template_attachment_admin.button },
		});

		attachmentFrame.on('select', function() {
			var media_attachment = attachmentFrame.state().get('selection').first().toJSON();

			$( '#email_template_attachment_file' ).val( media_attachment.url );
			$( '#email_template_attachment_id' ).val( media_attachment.id );
		});

		attachmentFrame.open();
	});

	$( '#enable_email_templates' ).change( function(){
		var enable = $( this ).is( ':checked' );
		var data = {
			'action': 'acui_refresh_enable_email_templates',
			'enable': enable,
			'security': email_template_attachment_admin.security,
		};

		$.post( ajaxurl, data, function( response ) {
			location.reload();
		});
	} );

	$( '#load_email_template' ).click( function(){
		if( $( '#email_template_selected' ).val() == '' )
			return;

		var data = {
			'action': 'acui_email_template_selected',
			'email_template_selected': $( '#email_template_selected' ).val(),
			'security': email_template_attachment_admin.security,
		};

		$.post( ajaxurl, data, function( response ) {
			var response = JSON.parse( response );
			$( '#title' ).val( response.title );
			
			if( typeof( tinyMCE ) === "undefined" )
				$( 'body_mail' ).val( response.content );
			else
				tinyMCE.get( 'body_mail' ).setContent( response.content );
			
			$( '#email_template_attachment_id' ).val( response.attachment_id );
			if( response.attachment_url != '' ){
				$( '#email_template_attachment_file' ).val( response.attachment_url );
			}
			$( '#template_id' ).val( response.id );
			$( '#save_mail_template_options' ).click();
		});
	} );
});