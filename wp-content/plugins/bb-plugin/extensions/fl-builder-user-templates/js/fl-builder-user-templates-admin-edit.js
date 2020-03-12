( function( $ ) {
	
	/**
	 * Handles logic for the user templates admin edit interface.
	 *
	 * @class FLBuilderUserTemplatesAdminEdit
	 * @since 1.10
	 */
	FLBuilderUserTemplatesAdminEdit = {
		
		/**
		 * Initializes the user templates admin edit interface.
		 *
		 * @since 1.10
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			this._setupPageTitle();
		},

		/**
		 * Adds to correct title to the edit screen and changes the 
		 * Add New button URL to point to our custom Add New page.
		 *
		 * @since 1.10
		 * @access private
		 * @method _setupPageTitle
		 */
		_setupPageTitle: function()
		{
			var button = $( '.page-title-action' ),
				url    = FLBuilderConfig.addNewURL + '&fl-builder-template-type=' + FLBuilderConfig.userTemplateType,
				h1     = $( '.wp-heading-inline' );
				
			h1.html( FLBuilderConfig.pageTitle + ' ' ).append( button );
			button.attr( 'href', url ).show();
		},
	};
	
	// Initialize
	$( function() { FLBuilderUserTemplatesAdminEdit._init(); } );

} )( jQuery );