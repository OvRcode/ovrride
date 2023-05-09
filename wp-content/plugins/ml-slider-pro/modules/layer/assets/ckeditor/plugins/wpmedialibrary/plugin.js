CKEDITOR.plugins.add( 'wpmedialibrary',
{
	init: function( editor )
	{
		editor.addCommand( 'insertMediaLibrary',
		{
			exec : function( editor )
			{    
				console.log(jQuery(".metaslider-active").length);
                if (! jQuery(".metaslider-active").length ) {
                    alert(metasliderpro.noLayerSelected);
                    return;
                }
		        // Create the media frame.
		        file_frame = wp.media.frames.file_frame = wp.media({
		            multiple: false,
		            frame: 'post',
		            library: {
		            	type: 'image'
		            },
		            button: {
						text: metasliderpro.insertIntoLayer,
					}
		        });

		        // When an image is selected, run a callback.
		        file_frame.on('insert', function() {

		            var selection = file_frame.state().get('selection');

		            selection.map(function(attachment) {
		                attachment = attachment.toJSON();
		                editor.insertHtml( "<img style='max-height: 100%' src='" + attachment.url + "' alt='" + attachment.alt + "' />" );
		            });

		        });

		        file_frame.open();
			}
		});

		editor.ui.addButton( 'Media Library',
		{
			label: metasliderpro.insertFromMediaLibrary,
			command: 'insertMediaLibrary',
			icon: this.path + 'images/wpmedialibrary.png'
		} );
	}
} );