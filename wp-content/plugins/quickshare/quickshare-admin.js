var mouseOffX = 0;
var mouseOffY = 0;

jQuery(document).ready(function(){
	// prepare the color pickers
	// plugin does not support WP<3.5
	jQuery('.color-field').wpColorPicker();
	
	// 3.5+ media/image upload (no support for older versions)
	// File uploader
	var file_frame;
	var formfield;
	jQuery('.img_upload_button').on('click', function( event ){
		formfield = jQuery(this).prev().attr('id');
	
		event.preventDefault();
	 
		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}
		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: 'Select an Image',
		  button: {
			text: 'Use This Image',
		  },
		  library: {
			type: 'image'
		  },
		  multiple: false  // only allow the one file to be selected
		});
		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  // We set multiple to false so only get one image from the uploader
		  attachment = file_frame.state().get('selection').first().toJSON();
		  jQuery('#'+formfield).attr('value',attachment.url);
		  jQuery('#'+formfield+'_preview').attr('src',attachment.url);
		});
		// Finally, open the modal
		file_frame.open();
		
	});

	checkInputs(0,0); // initial setup
	jQuery('input').bind('change', function(){ document.getElementById('quickshare-design-preview').className = getUlClass(); checkInputs(200,150); });
	var t = setInterval(quicksharepreview_update,43);//30 fps refresh rate, needed for color pickers, which don't update the input values

	jQuery('#displayeverywhere').bind('change', function () {
		jQuery('.display-option').toggle(200);
	});
	jQuery('#responsive-help').click(function() {
		jQuery('#responsive-description').toggle(200);
	});
	
	jQuery('#nav-design').click(function(){switchTabs('design')});
	jQuery('#nav-config').click(function(){switchTabs('config')});
	
	
	// make the admin preview position draggable
	jQuery('#quickshare-preview').mousedown(function(e){
		boxOff = jQuery('#quickshare-preview').offset();
		mouseOffX = e.pageX - boxOff.left;
		mouseOffY = e.pageY - boxOff.top;
		jQuery(document).mousemove(function(e){
			qs_track(e);
		});
	});

	jQuery(document).mouseup(function(){
		jQuery(document).unbind('mousemove'); 
	});
	
});
function toType( type ){
	jQuery('#settingsform').removeClass('icons genericons text');
	jQuery('#settingsform').addClass(type);
	checkInputs(0,0);
}
function updatebr(){
	var br = jQuery('#brinput').val();
	jQuery('#br-current').html(br);
}
function quicksharepreview_update(){
	document.getElementById('dynamic-custom-options-css').innerHTML = getDynamicCSS();
}
function getUlClass(){
	var tclass = '';
	var type = jQuery("#display_type_holder input[type='radio']:checked").val();
	if(type == 'icons'){
		tclass = 'quickshare-icons';
	}
	else if(type =='genericons'){
		tclass = 'quickshare-genericons';
		if(jQuery('#monochrome_genericons').is(':checked')) {
			tclass += ' monochrome';
			if(jQuery('#monochrome_hover').is(':checked'))
				tclass += '-color';
		}
	}
	else {
		tclass = 'quickshare-text';
		if(jQuery('#text_icons').is(':checked'))
			tclass += ' qs-genericons';
	}
	//effects 
	if(jQuery('#effect-spin').is(':checked'))
		tclass += ' quickshare-effect-spin';
	if(jQuery('#effect-round').is(':checked'))
		tclass += ' quickshare-effect-round';
	if(jQuery('#effect-glow').is(':checked'))
		tclass += ' quickshare-effect-glow';
	if(jQuery('#effect-expand').is(':checked'))
		tclass += ' quickshare-effect-expand';
	if(jQuery('#effect-contract').is(':checked'))
		tclass += ' quickshare-effect-contract';
	//size
	if(jQuery('#display_size_holder input[type="radio"]:checked').val()!='')
		tclass += ' quickshare-' + jQuery('#display_size_holder input[type="radio"]:checked').val();
	
	return tclass;
}
function getDynamicCSS() {
	var displaytype = jQuery('input:radio[name="cxnh_quickshare_options[displaytype]"]:checked').val();
	var css = '.quickshare-text span, ';
	if(jQuery('#text_icons_color input').is(':checked'))
		css += '.quickshare-text span:before, ';
	css += '.quickshare-text span:hover, .quickshare-genericons.monochrome span:before,.quickshare-genericons.monochrome-color span:before { ';
	if(!jQuery('#inherit_colors').is(':checked'))
		css += 'color: '+jQuery('#maincolor input').val()+'; ';
	if(!jQuery('#bgtrans').is(':checked')&&displaytype=='text')
		css += 'background-color: '+jQuery('#bgcolor input').val()+'; ';
	css += '} .quickshare-text span:hover, ';
	if(jQuery('#text_icons_color input').is(':checked'))
		css += '.quickshare-text span:hover:before,';
	css += '.quickshare-genericons.monochrome span:hover:before { ';
	if(!jQuery('#inherit_colors').is(':checked'))
		css += 'color: ' + jQuery('#hovercolor').val() +';';
	css += '} .quickshare-icons span, .quickshare-genericons span:before, .quickshare-text span { ';
	css += 'border-radius: '+jQuery('#brinput').val()+'px; }';
	css += jQuery('#customcss').val();
	return css;
}
function switchTabs( tabto ){
	jQuery('table.form-table').hide(150);
	jQuery('#quickshare_'+tabto).show(300);
	jQuery('.nav-tab-wrapper .nav-tab-active').removeClass( 'nav-tab-active' );
	jQuery('#nav-'+tabto).addClass( 'nav-tab-active' );
	jQuery('#nav-input').val(tabto);
}
function checkInputs(st,ht){
	//check showing/hiding of each input
	if(jQuery('input:radio[name="cxnh_quickshare_options[size]"]:checked').val() == 'small')
		jQuery('#responsive-small').hide(ht);
	else
		jQuery('#responsive-small').show(st);
	
	//Icons
	if(jQuery('input:radio[name="cxnh_quickshare_options[displaytype]"]:checked').val() == 'icons') {
		// colors always hidden, nothing else changes
		jQuery('#main-color').hide(ht);
		jQuery('#hover-color').hide(ht);
	}
	
	//Genericons
	else if(jQuery('input:radio[name="cxnh_quickshare_options[displaytype]"]:checked').val() == 'genericons') {
		if(jQuery('#monochrome_genericons').is(':checked')) {
			jQuery('#main-color').show(st);
			jQuery('#hover-color').show(st);
			if(jQuery('#inherit_colors').is(':checked')) {
				jQuery('#maincolor').hide(ht);
				jQuery('#hovercolorwrap').hide(ht);
			}
			else {
				jQuery('#maincolor').show(st);
				if(!jQuery('#monochrome_hover').is(':checked'))
					jQuery('#hovercolorwrap').show(st);
			}
			if(jQuery('#monochrome_hover').is(':checked')) {
				jQuery('#hovercolorwrap').hide(ht);
			}
		}
		else {
			jQuery('#main-color').hide(ht);
			jQuery('#hover-color').hide(ht);
		}
	}
	
	//Text
	else if(jQuery('input:radio[name="cxnh_quickshare_options[displaytype]"]:checked').val() == 'text') {
		jQuery('#main-color').show(st); // always displayed for text
		jQuery('#hovercolorwrap').show(st); // only tr is changed for text
		if(jQuery('#text_icons').is(':checked') && !jQuery('#inherit_colors').is(':checked')) {
			jQuery('#text_icons_color').show(st);
		}
		else {
			jQuery('#text_icons_color').hide(ht);
		}
		if(jQuery('#inherit_colors').is(':checked')) {
			jQuery('#maincolor').hide(ht);
			jQuery('#hover-color').hide(ht);
			jQuery('#text_icons_color').hide(ht); // not available because results in stupidly ridculous css (probably impossible actually, without uninheritance-type stuff)
		}
		else {
			jQuery('#maincolor').show(st);
			jQuery('#hover-color').show(st);
			if(jQuery('#text_icons').is(':checked'))
				jQuery('#text_icons_color').show(st);
		}
		if(jQuery('#bgtrans').is(':checked')) {
			jQuery('#bgcolor').hide(ht);
		}
		else {
			jQuery('#bgcolor').show(st);
		}
	}
}

function qs_track(e){
	jQuery('#quickshare-preview').css({'left': e.pageX-mouseOffX, 'top': e.pageY-mouseOffY});
}