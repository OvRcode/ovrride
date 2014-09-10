/*
| --------------------------------------------------------
| File        : srp-init.js
| Project     : Special Recent Posts FREE Edition plugin for Wordpress
| Version     : 1.9.9
| Description : Custom js init file.
| Author      : Luca Grandicelli
| Author URL  : http://www.lucagrandicelli.com
| Plugin URL  : http://www.specialrecentposts.com
| Copyright (C) 2011-2012  Luca Grandicelli
| --------------------------------------------------------
*/

/*
| ------------------------------------------------------
| This function handles the switching admin tabs.
| ------------------------------------------------------
*/
function srpTabsSwitcher(tab) {
	
	// Switching mode.
	switch(tab) {
	
		case 1:
			
			// Adding active class to tab links.
			$jsrp('a.srp_tab_1').addClass('active');
			$jsrp('a.srp_tab_2').removeClass('active');
			
			// Switching Tab.
			$jsrp('div#srp_tab2').hide();
			$jsrp('div#srp_tab1').show();
		break;
		
		case 2:
		
			// Adding active class to tab links.
			$jsrp('a.srp_tab_2').addClass('active');
			$jsrp('a.srp_tab_1').removeClass('active');
			
			// Switching Tab.
			$jsrp('div#srp_tab1').hide();
			$jsrp('div#srp_tab2').show();
		break;
	}
}

/*
| ------------------------------------------------------
| This function handles the widget accordion animation
| ------------------------------------------------------
*/
function initAccordion() {

	// Main logic for accordion headers links.
	$jsrp('dl.srp-wdg-accordion dt a').live({

		click: function() {
		
			// Removing highlight from all headers links.
			$jsrp('dl.srp-wdg-accordion dt a').removeClass("accordion-active-link");
			
			// Highlighting current header link.
			$jsrp(this).addClass("accordion-active-link");
			
			// Normal Behaviour. Accordion Logic.
			$jsrp('dl.srp-wdg-accordion dd').slideUp();
			$jsrp(this).parent().next().slideDown();
			
			// Return false.
			return false;
		}
	});
	
	// Main logic for textareas highlighting.
	$jsrp('dl.srp-wdg-accordion textarea').live({

		click: function() {
		
			// Setting focus on clicked textarea.
			this.focus();
			
			// Highlighting inner text.
			this.select();
			
			// Return false.
			return false;
		}
	});
}

/*
| ------------------------------------------------------
| JQUERY DOMREADY
| ------------------------------------------------------
*/

// Setting up jQuery no-conflict.
var $jsrp = jQuery.noConflict();

// DOM READY START.
$jsrp(document).ready(function() {

	// Initialize Accordion.
	initAccordion();
});