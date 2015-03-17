/**
* SRP INIT JS
* Package: special-recent-posts-free
* Version: 2.0.4
* Author: Luca Grandicelli <lgrandicelli@gmail.com>
* Copyright (C) 2011-2014 Luca Grandicelli
* The SRP jQuery init file.
*/

/**
 * The jQuery DOM Ready Event.
 */
(function($) {

	/**
	 * srpInitAdminSettingsTabs()
	 *
	 * This function handles the switching admin tabs.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @param {jQuery Object} tab The menu tab.
	 * @version 2.0.4
	 */
	function srpInitAdminSettingsTabs( tab ) {
		
		$('.srp-tabs-menu a').on( 'click', function( e ) {

			// Preventing default behaviour.
	        e.preventDefault();

	        // Adding the 'current' class to the parent().
	        $(this).parent().addClass( 'current' );

	        // Removing old 'current' classes.
	        $(this).parent().siblings().removeClass( 'current' );

	        // Fetching current tab link href attribute.
	        var tab = $(this).attr( 'href' );

	        // Hide all others panel but the current one.
	        $( '.srp-tab-content' ).not( tab ).css( 'display', 'none' );

	        // Fade in the current content panel.
	        $( tab ).fadeIn();

	        // Returning false.
			return false;

	    });
	}

	/**
	 * srpInitAccordion()
	 *
	 * This function handles the widget accordion animations
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @version 2.0.4
	 */
	function srpInitAccordion() {

		// Main logic for accordion headers links.
		$( '.srp-wdg-accordion dt a' ).on( 'click', function( e ) {

			// Preventing default behaviour.
			e.preventDefault();

			// Preventing double click on the same accordion tab.
			if( $(this).hasClass( 'active' ) ) return;

			// Removing highlight from all headers links.
			$( '.srp-wdg-accordion-item' ).removeClass( 'active' );
			
			// Highlighting current header link.
			$(this).addClass( 'active' );
			
			// Hide previously open accordion tab.
			$( 'dl.srp-wdg-accordion dd' ).slideUp();

			// Show current accordion tab.
			$(this).parent().next().slideDown();
			
			// Returning false.
			return false;
		});
	}

	/**
	 * srpOnWidgetUpdate()
	 *
	 * This function is called when the widget is updated.
	 * Page is not reloaded but the widget re-builded, so we need to re call
	 * the initialization functions.
	 *
	 * @author Luca Grandicelli <lgrandicelli@gmail.com>
	 * @copyright (C) 2011-2014 Luca Grandicelli
	 * @package special-recent-posts-free
	 * @param {event} event The on widgets update event.
	 * @param {Object} widget The current widget instance.
	 * @version 2.0.4
	 */
	function srpOnWidgetUpdate( event, widget ) {

		// Reinitializing accordion.
		srpInitAccordion();
	}
	
	// The widget update WP event.
	$( document ).on( 'widget-added widget-updated', srpOnWidgetUpdate );

	// Initializing admin settings tabs.
	srpInitAdminSettingsTabs();

	// Initializing accordion.
	srpInitAccordion();

})(jQuery);