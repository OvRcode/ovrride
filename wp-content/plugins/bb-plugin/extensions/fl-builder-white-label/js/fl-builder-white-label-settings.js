(function($){

	/**
	 * Helper class for dealing with the builder's white label 
	 * settings on the admin settings page.
	 *
	 * @class FLBuilderWhiteLabelSettings
	 * @since 1.8
	 */
	FLBuilderWhiteLabelSettings = {
	
		/**
		 * Initializes the builder's admin settings page.
		 *
		 * @since 1.0
		 * @method init
		 */ 
		init: function()
		{
			FLBuilderWhiteLabelSettings._bind();
			FLBuilderWhiteLabelSettings._initHelpButtonSettings();
		},
		
		/**
		 * Binds events for the builder's admin settings page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$('input[name=fl-help-button-enabled]').on('click', FLBuilderWhiteLabelSettings._initHelpButtonSettings);
			$('input[name=fl-help-video-enabled]').on('click', FLBuilderWhiteLabelSettings._initHelpButtonSettings);
			$('input[name=fl-knowledge-base-enabled]').on('click', FLBuilderWhiteLabelSettings._initHelpButtonSettings);
			$('input[name=fl-forums-enabled]').on('click', FLBuilderWhiteLabelSettings._initHelpButtonSettings);
		},
		
		/**
		 * Initializes the the help button settings.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _initHelpButtonSettings
		 */
		_initHelpButtonSettings: function()
		{
			if ( 0 === $( '#fl-help-button-form' ).length ) {
				return;
			}
			
			var enabled  = $( 'input[name=fl-help-button-enabled]' )[ 0 ].checked,
				tour     = $('input[name=fl-help-tour-enabled]')[ 0 ].checked,
				video    = $('input[name=fl-help-video-enabled]')[ 0 ].checked,
				kb       = $('input[name=fl-knowledge-base-enabled]')[ 0 ].checked,
				forums   = $('input[name=fl-forums-enabled]')[ 0 ].checked;
			
			$( '.fl-help-button-settings' ).toggle( enabled );
			$( '.fl-help-video-embed' ).toggle( video );
			$( '.fl-knowledge-base-url' ).toggle( kb );
			$( '.fl-forums-url' ).toggle( forums );
		}
	};

	$( FLBuilderWhiteLabelSettings.init );

})(jQuery);