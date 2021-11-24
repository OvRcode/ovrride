(function( $ ) {
	'use strict';

	$(function() {
	 	
	 	$(".wt-iew-tips").tipTip({'attribute': 'data-wt-iew-tip'});


	 	/* tab view */
	 	var wf_tab_view=
		{
			Set:function(prnt_selector)
			{ 
				var wf_prnt_obj=$(prnt_selector);
				var wf_nav_tab=wf_prnt_obj.find('.wt-iew-tab-head .nav-tab');
			 	if(wf_nav_tab.length>0)
			 	{ 
				 	wf_nav_tab.click(function(){
				 		var wf_tab_hash=$(this).attr('href');
				 		wf_nav_tab.removeClass('nav-tab-active');
				 		$(this).addClass('nav-tab-active');
				 		wf_tab_hash=wf_tab_hash.charAt(0)=='#' ? wf_tab_hash.substring(1) : wf_tab_hash;
				 		var wf_tab_elm=$('div[data-id="'+wf_tab_hash+'"]');
				 		wf_prnt_obj.find('.wt-iew-tab-content').hide();
				 		if(wf_tab_elm.length>0 && wf_tab_elm.is(':hidden'))
				 		{	 			
				 			wf_tab_elm.fadeIn();
				 		}
				 	});
				 	$(window).on('hashchange', function (e) {
					    var location_hash=window.location.hash;
					 	if(location_hash!="")
					 	{
					    	wf_tab_view.showTab(location_hash);
					    }
					}).trigger('hashchange');

				 	var location_hash=window.location.hash;
				 	if(location_hash!="")
				 	{
				 		wf_tab_view.showTab(location_hash);
				 	}else
				 	{
				 		wf_nav_tab.eq(0).click();
				 	}		 	
				}
				this.subTab(wf_prnt_obj);
			},
			showTab:function(location_hash)
			{
				var wf_tab_hash=location_hash.charAt(0)=='#' ? location_hash.substring(1) : location_hash;
		 		if(wf_tab_hash!="")
		 		{
		 			var wf_tab_elm=$('div[data-id="'+wf_tab_hash+'"]');
			 		if(wf_tab_elm.length>0 && wf_tab_elm.is(':hidden'))
			 		{	 			
			 			$('a[href="#'+wf_tab_hash+'"]').click();
			 		}
		 		}
			},
			subTab:function(wf_prnt_obj)
			{
				wf_prnt_obj.find('.wt_iew_sub_tab li').click(function(){
					var trgt=$(this).attr('data-target');
					var prnt=$(this).parent('.wt_iew_sub_tab');
					var ctnr=prnt.siblings('.wt_iew_sub_tab_container');
					prnt.find('li a').css({'color':'#0073aa','cursor':'pointer'});
					$(this).find('a').css({'color':'#000','cursor':'default'});
					ctnr.find('.wt_iew_sub_tab_content').hide();
					ctnr.find('.wt_iew_sub_tab_content[data-id="'+trgt+'"]').fadeIn();
				});
				wf_prnt_obj.find('.wt_iew_sub_tab').each(function(){
					var elm=$(this).children('li').eq(0);
					elm.click();
				});
			}
		}
		wf_tab_view.Set('#'+wt_iew_basic_params.plugin_id); /* set plugin main div as parent object to avoid conflict with other WT plugins */
		/* tab view */

	});

})( jQuery );

var wt_iew_settings_form_basic=
{
	Set:function(prnt_selector)
	{ 
		var wf_prnt_obj=jQuery(prnt_selector);
		wf_prnt_obj.find('.wt_iew_settings_form_basic').submit(function(e){
			e.preventDefault();
			var data=jQuery(this).serialize();

			var submit_btn=jQuery(this).find('input[type="submit"]');
			var spinner=submit_btn.siblings('.spinner');
			spinner.css({'visibility':'visible'});
			submit_btn.css({'opacity':'.5','cursor':'default'}).prop('disabled',true);

			jQuery.ajax({
				url:wt_iew_basic_params.ajax_url,
				type:'POST',
				dataType:'json',
				data:data+'&action=wt_iew_save_settings_basic',
				success:function(data)
				{
					spinner.css({'visibility':'hidden'});
					submit_btn.css({'opacity':'1','cursor':'pointer'}).prop('disabled',false);
					if(data.status==true)
					{
						wt_iew_notify_msg.success(data.msg);
					}else
					{
						wt_iew_notify_msg.error(data.msg);
					}
				},
				error:function () 
				{
					spinner.css({'visibility':'hidden'});
					submit_btn.css({'opacity':'1','cursor':'pointer'}).prop('disabled',false);
					wt_iew_notify_msg.error(wt_iew_basic_params.msgs.settings_error);
				}
			});
		});
	}
}

var wt_saved_templates = {
    Set: function()
	{ 
                jQuery('.wt_ier_delete_template').unbind('click').click(function (e) {
                    e.preventDefault();
                    if(confirm(wt_iew_basic_params.msgs.sure)){                    
                    
                    var template_id = jQuery(this).attr('data-id');

                    var data = {
                        _wpnonce: wt_iew_basic_params.nonces.main,
                        action: 'wt_iew_delete_template',
                        template_id: template_id,
                    };
                    jQuery('tr[data-row-id='+data.template_id+']').html('<td colspan="5">'+wt_iew_basic_params.msgs.template_del_loader+'</td>');
                    jQuery.ajax({
                        url: wt_iew_basic_params.ajax_url,
                        type: 'POST',
                        dataType: 'json',
                        data: data,
                        success: function (data)
                        {
                            if (data.status == true)
                            {
                                wt_iew_notify_msg.success(data.msg, true);
                                jQuery('tr[data-row-id='+data.template_id+']').remove();
                            } else
                            {
                                wt_iew_notify_msg.error(data.msg, true);
                            }
                        },
                        error: function ()
                        {
                           wt_iew_notify_msg.error(wt_iew_basic_params.msgs.template_del_error);
                        }
                    });
                }
                });
            }
}

var wt_drp_menu=
{
	Set:function()
	{
		jQuery(document).on('click', '.wt_iew_drp_menu', function(){
			var trgt=jQuery(this).attr('data-target');
			var drp_menu=jQuery('.wt_iew_dropdown[data-id="'+trgt+'"]');
			if(drp_menu.is(':visible'))
			{
				drp_menu.hide();
			}else
			{
				var pos=jQuery(this).position();
				var t=pos.top+(jQuery(this).height()/2)+5;
				var l=pos.left; //-drp_menu.outerWidth()+jQuery(this).outerWidth();
				var w=jQuery(this).outerWidth();
				drp_menu.css({'display':'block','left':l,'top':t,'opacity':0,'width':w}).stop(true, true).animate({'top':t+5, 'opacity':1});
			}
		});

		jQuery(document).on('click', 'body, body *', function(e){
	    	var drp_menu=jQuery('.wt_iew_dropdown');
	    	if(drp_menu.is(':visible'))
	    	{
	    		if(jQuery(e.target).hasClass('wt_iew_dropdown')===false && jQuery(e.target).hasClass('wt_iew_drp_menu')===false && jQuery(e.target).hasClass('dashicons')===false)
		    	{
		    		drp_menu.hide();
		    	}
	    	}
	    });
	}
}

var wt_iew_notify_msg=
{
	error:function(message, auto_close)
	{
                var auto_close=(auto_close!== undefined ? auto_close : false);
		var er_elm=jQuery('<div class="wt_notify_msg" style="background:#f8d7da; border:solid 1px #f5c6cb; color:  #721c24">'+message+'</div>');				
		this.setNotify(er_elm, auto_close);
	},
	success:function(message, auto_close)
	{
                var auto_close=(auto_close!== undefined ? auto_close : false);
		var suss_elm=jQuery('<div class="wt_notify_msg" style="background:#d4edda; border:solid 1px #c3e6cb; color: #155724;">'+message+'</div>');				
		this.setNotify(suss_elm, auto_close);
	},
	setNotify:function(elm, auto_close)
	{
		jQuery('body').append(elm);
		jQuery('.wt_notify_msg').click(function(){
			jQuery(this).remove();
		});
		elm.stop(true,true).animate({'opacity':1,'top':'50px'},1000);
		if(auto_close)
		{
			setTimeout(function(){
				wt_iew_notify_msg.fadeOut(elm);
			},5000);
		}else
		{  
			jQuery('body').click(function(){
				wt_iew_notify_msg.fadeOut(elm);
			});
		}
	},
	fadeOut:function(elm)
	{
		elm.animate({'opacity':0,'top':'100px'},1000,function(){
			elm.remove();
		});
	}
}

wt_iew_popup={
	Set:function()
	{
		this.regPopupOpen();
		this.regPopupClose();
		jQuery('body').prepend('<div class="wt_iew_overlay"></div>');
	},
	regPopupOpen:function()
	{
		jQuery('[data-wt_iew_popup]').click(function(){
			var elm_class=jQuery(this).attr('data-wt_iew_popup');
			var elm=jQuery('.'+elm_class);
			if(elm.length>0)
			{
				wt_iew_popup.showPopup(elm);
			}
		});
	},
	showPopup:function(popup_elm)
	{
		var pw=popup_elm.outerWidth();
		var wh=jQuery(window).height();
		var ph=wh-150;
		popup_elm.css({'margin-left':((pw/2)*-1),'display':'block','top':'20px'}).animate({'top':'50px'});
		popup_elm.find('.wt_iew_popup_body').css({'max-height':ph+'px','overflow':'auto'});
		jQuery('.wt_iew_overlay').show();
	},
	hidePopup:function()
	{
		jQuery('.wt_iew_popup_close').click();
	},
	regPopupClose:function(popup_elm)
	{
		jQuery(document).keyup(function(e){
			if(e.keyCode==27)
			{
				wt_iew_popup.hidePopup();
			}
		});
		jQuery('.wt_iew_popup_close, .wt_iew_popup_cancel').unbind('click').click(function(){
			jQuery('.wt_iew_overlay, .wt_iew_popup').hide();
		});
	}
}

var wt_field_group=
{
	Set:function()
	{
		//jQuery('.wt_iew_field_group_children').hide();
		jQuery('.wt_iew_field_group_hd .wt_iew_field_group_toggle_btn').each(function(){
			var group_id = jQuery(this).attr('data-id');
			var group_content_dv = jQuery(this).parents('tr').find('.wt_iew_field_group_content');
			var visibility = jQuery(this).attr('data-visibility');
			jQuery('.wt_iew_field_group_children[data-field-group="'+group_id+'"]').appendTo(group_content_dv.find('table'));
			if(visibility==1)
			{
				group_content_dv.show();
			}
		});
		jQuery('.wt_iew_field_group_hd').click(function(){

			var toggle_btn=jQuery(this).find('.wt_iew_field_group_toggle_btn');
			var visibility=toggle_btn.attr('data-visibility');
			var group_content_dv=toggle_btn.parents('tr').find('.wt_iew_field_group_content');
			if(visibility==1)
			{
				toggle_btn.attr('data-visibility',0);
				toggle_btn.find('.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
				group_content_dv.hide();
			}else
			{
				toggle_btn.attr('data-visibility',1);
				toggle_btn.find('.dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
				group_content_dv.show();
			}
		});
	}
}
var wt_iew_form_toggler=
{
	Set:function()
	{
		this.runToggler();
		jQuery('select.wt_iew_form_toggler').change(function(){
			wt_iew_form_toggler.toggle(jQuery(this));
		});
		jQuery('input[type="radio"].wt_iew_form_toggler').click(function(){
			if(jQuery(this).is(':checked'))
			{
				wt_iew_form_toggler.toggle(jQuery(this));
			}
		});
		jQuery('input[type="checkbox"].wt_iew_form_toggler').click(function(){
			wt_iew_form_toggler.toggle(jQuery(this),1);
		});
	},
	runToggler:function(prnt)
	{  
		prnt=prnt ? prnt : jQuery('body');
		prnt.find('select.wt_iew_form_toggler').each(function(){
			wt_iew_form_toggler.toggle(jQuery(this));
		});
		prnt.find('input[type="radio"].wt_iew_form_toggler, input[type="checkbox"].wt_iew_form_toggler').each(function(){
			if(jQuery(this).is(':checked'))
			{
				wt_iew_form_toggler.toggle(jQuery(this));
			}
		});
		prnt.find('input[type="checkbox"].wt_iew_form_toggler').each(function(){
			wt_iew_form_toggler.toggle(jQuery(this),1);
		});
	},
	toggle:function(elm, checkbox)
	{
		var vl=elm.val();
		var trgt=elm.attr('wf_frm_tgl-target');
		jQuery('[wf_frm_tgl-id="'+trgt+'"]').hide();
		if(!elm.is(':visible'))
		{
			return false;
		}
		if(elm.css('display')!='none') /* if parent is visible. `:visible` method. it will not work on JS tabview */
		{
			var elms=this.getElms(elm,trgt,vl,checkbox);
			elms.show().find('th label').css({'margin-left':'0px'})
			elms.each(function(){
				var lvl=jQuery(this).attr('wf_frm_tgl-lvl');
				var mrgin=25;
				if (typeof lvl!== typeof undefined && lvl!== false) {
				    mrgin=lvl*mrgin;
				}
				jQuery(this).find('th label').animate({'margin-left':mrgin+'px'},1000);
			});
		}

		/* in case of greater than 1 level */
		jQuery('[wf_frm_tgl-id="'+trgt+'"]').each(function(){
			wt_iew_form_toggler.runToggler(jQuery(this));
		});
	},
	getElms:function(elm,trgt,vl,checkbox)
	{
		
		return jQuery('[wf_frm_tgl-id="'+trgt+'"]').filter(function(){
				if(jQuery(this).attr('wf_frm_tgl-val')==vl)
				{
					if(checkbox)
					{
						if(elm.is(':checked'))
						{
							if(jQuery(this).attr('wf_frm_tgl-chk')=='true')
							{
								return true;
							}else
							{
								return false;
							}
						}else
						{
							if(jQuery(this).attr('wf_frm_tgl-chk')=='false')
							{
								return true;
							}else
							{
								return false;
							}
						}
					}else
					{
						return true;
					}
				}else
				{
					return false;
				}
			});
	}
}

var wt_iew_file_attacher={

	Set:function()
	{
		var file_frame;
		jQuery(".wt_iew_file_attacher").click(function(event){
			event.preventDefault();
			if(jQuery(this).data('file_frame'))
			{
				
			}else
			{
				/* reset user preference. This will open file uploader by default. */
				wp.media.controller.Library.prototype.defaults.contentUserSetting=false;


				/* Create the media frame. */
				var file_frame = wp.media.frames.file_frame = wp.media({
					title: jQuery( this ).data( 'invoice_uploader_title' ),
					button: {
						text: jQuery( this ).data( 'invoice_uploader_button_text' ),
					},
					/* Set to true to allow multiple files to be selected */
					multiple: false
				});
				jQuery(this).data('file_frame',file_frame);
				var wf_file_target=jQuery(this).attr('wt_iew_file_attacher_target');
				var wt_file_attacher_choosed=jQuery(this).parent('.wt_iew_file_attacher_dv').find('.wt_iew_file_attacher_choosed');
				var elm=jQuery(this);

				/* When an image is selected, run a callback. */
				jQuery(this).data('file_frame').on( 'select', function() {
					/* We set multiple to false so only get one image from the uploader */
					var attachment =file_frame.state().get('selection').first().toJSON();
					/* Send the value of attachment.url back to shipment label printing settings form */
					jQuery(wf_file_target).val(attachment.url);
					if(wt_file_attacher_choosed.length>0)
					{
						wt_file_attacher_choosed.css({'visibility':'visible'}).html(attachment.filename);
					}
				});
				/* Finally, open the modal	*/			
			}
			jQuery(this).data('file_frame').open();
		});
		function wf_update_file_choosed(wf_file_target, wt_file_attacher_choosed)
		{
			if(jQuery(wf_file_target).val()=="")
			{ 
				wt_file_attacher_choosed.css({'visibility':'hidden'});
			}else
			{
				wt_file_attacher_choosed.css({'visibility':'visible'});
			}
		}
		jQuery(".wt_iew_file_attacher").each(function(){
			var wf_file_target=jQuery(this).attr('wt_iew_file_attacher_target');
			var wt_file_attacher_choosed=jQuery(this).parent('.wt_iew_file_attacher_dv').find('.wt_iew_file_attacher_choosed');
			if(wt_file_attacher_choosed.length>0)
			{ 
				wf_update_file_choosed(wf_file_target, wt_file_attacher_choosed);
				jQuery(wf_file_target).change(function(){
					wf_update_file_choosed(wf_file_target, wt_file_attacher_choosed);
				});
			}
		});
	}
}

var wt_iew_popover=
{
	action_module:false,
	Set:function()
	{
		jQuery('[data-wt_iew_popover="1"]').unbind('click').click(function(){
			
			/* check popover is visible now */
			if(jQuery('.wt_iew_popover').length>0 && jQuery('.wt_iew_popover').is(':visible'))
			{
				wt_iew_popover.remove_active_row();
			}

			var cr_elm=jQuery(this);
			if(cr_elm.attr('data-popup-opened')==1)
			{
				jQuery('[data-wt_iew_popover="1"]').attr('data-popup-opened',0);
				wt_iew_popover.closePop();
				return false;
			}else
			{
				jQuery('[data-wt_iew_popover="1"]').attr('data-popup-opened',0);
				cr_elm.attr('data-popup-opened',1);
			}
			if(jQuery('.wt_iew_popover').length==0)
			{
				var template='<div class="wt_iew_popover"><h3 class="wt_iew_popover-title">'
				+'<span class="wt_iew_popover-title-text"></span><span class="popover_close_icon_button popover_close">X</span></h3>'
				+'<div class="wt_iew_popover-content"></div><div class="wt_iew_popover-footer">'
				+'<button name="wt_iew_popover_do_action" type="button" class="button button-primary">'+wt_iew_basic_params.msgs.use_expression+'</button>'
				+'<button name="popover_close" type="button" class="button button-secondary popover_close">'+wt_iew_basic_params.msgs.cancel+'</button>'
				+'<span class="spinner" style="margin-top:5px"></span>'
				+'</div></div>';
				jQuery('body').append(template);
				wt_iew_popover.regclosePop();
			}
			
			var ttle=jQuery.trim(cr_elm.attr('data-title'));
			var pp_elm=jQuery('.wt_iew_popover');
			var pp_html='';
			var pp_html_cntr=cr_elm.attr('data-content-container');
			if(typeof pp_html_cntr!==typeof undefined && pp_html_cntr!==false)
			{
				pp_html=jQuery(pp_html_cntr).html();
				ttle=(ttle=="" ? jQuery(pp_html_cntr).attr('data-title') : ttle);
				wt_iew_popover.action_module=jQuery(pp_html_cntr).attr('data-module');
			}else
			{
				pp_html=cr_elm.attr('data-content');
			}
			pp_elm.css({'display':'block'}).find('.wt_iew_popover-content').html(pp_html);
			pp_elm.find('.wt_iew_popover-footer').show();
			var cr_elm_w=cr_elm.width();
			var cr_elm_h=cr_elm.height();
			var pp_elm_w=pp_elm.width();
			var pp_elm_h=pp_elm.height();
			var cr_elm_pos=cr_elm.offset();
			var cr_elm_pos_t=cr_elm_pos.top-((pp_elm_h-cr_elm_h)/4);
			var cr_elm_pos_l=cr_elm_pos.left+cr_elm_w;

			cr_elm_pos_t=cr_elm_pos_t+10; /* 10 px buffer for input span element padding */

			pp_elm.find('.wt_iew_popover-title-text').html(ttle);
			var target_elm_label=cr_elm.parents('tr').find('.wt_iew_mapping_column_label').html();
			jQuery('.wt_iew_target_column').html(target_elm_label);
			jQuery('.wt_iew_popover-content').find('.wt_iew_mapping_field_editor_expression').val(cr_elm.siblings('.columns_val').val());

			wt_iew_popover.set_active_row(cr_elm);
			pp_elm.css({'display':'block','opacity':0, 'top':cr_elm_pos_t,'left':cr_elm_pos_l}).stop(true,true).animate({'left':cr_elm_pos_l+20,'opacity':1}, 500, function(){
				jQuery('.wt_iew_mapping_field_editor_expression').focus();
			});
			
			jQuery('[name="wt_iew_popover_do_action"]').data('click-elm', cr_elm);
			wt_iew_popover.do_action();
			if(wt_iew_popover.action_module=='import')
			{
				wt_iew_basic_import.mapping_field_editor();
				wt_iew_basic_import.mapping_field_editor_output_preview();
			}

		});
	},
	do_action:function()
	{	
		jQuery('[name="wt_iew_popover_do_action"]').unbind('click').click(function(){
			var click_elm=jQuery(this).data('click-elm');
			var vl=jQuery.trim(jQuery('.wt_iew_popover-content').find('.wt_iew_mapping_field_editor_expression').val());

			var html_vl=vl;
			if(wt_iew_popover.action_module=='import')
			{
				var html_vl=wt_iew_basic_import.mapping_field_editor_validate_column_val(vl);
			}
			click_elm.html(html_vl);
			click_elm.siblings('.columns_val').val(vl);
			wt_iew_popover.closePop();

			if(wt_iew_popover.action_module=='import')
			{
				if(vl=="")
				{
					click_elm.parents('tr').find('.wt_iew_mapping_checkbox_sub').prop('checked', false);
				}else
				{
					click_elm.parents('tr').find('.wt_iew_mapping_checkbox_sub').prop('checked', true);
				}
				wt_iew_basic_import.mapping_fields_selected_count(click_elm.parents('table'));
			}		
		});
	},
	regclosePop:function()
	{
		jQuery('.meta_mapping_box_toggle').click(function(){
			wt_iew_popover.closePop();
		});
		jQuery('.popover_close').unbind('click').click(function(){
			wt_iew_popover.closePop();
		});
	},
	set_active_row:function(cr_elm)
	{
		cr_elm.parents('tr').find('td').css({'background':'#f6f6f6'});
	},
	remove_active_row:function()
	{
		var click_elm=jQuery('[name="wt_iew_popover_do_action"]').data('click-elm');
		click_elm.parents('tr').find('td').css({'background':'#fff'});
	},
	closePop:function()
	{
		var pp_elm=jQuery('.wt_iew_popover');
		if(pp_elm.length>0)
		{
			var pp_lft=pp_elm.offset().left-50;
			jQuery('[data-wt_iew_popover="1"]').attr('data-popup-opened',0);
			pp_elm.stop(true,true).animate({'opacity':0, 'left':pp_lft},300,function(){
				jQuery(this).css({'display':'none'});
			});
			this.remove_active_row();
		}
	}
};

var wt_iew_custom_and_preset=
{
	toggler:function(preset_elm, custom_elm, custom_val) /* Toggle between custom and preset value */
	{
		this.do_toggle(preset_elm, custom_elm, custom_val);
		preset_elm.unbind('change').change(function(){
			wt_iew_custom_and_preset.do_toggle(preset_elm, custom_elm, custom_val);
		});
	},
	do_toggle:function(preset_elm, custom_elm, custom_val)
	{
		if(preset_elm.val()==custom_val)
		{
			custom_elm.prop('readonly', false).css({'background':'#ffffff'}).focus().val('');
		}else
		{
			custom_elm.prop('readonly', true).css({'background':'#efefef'}).val(preset_elm.find('option:selected').attr('data-val'));
		}
	},
	delimiter_toggler:function() /* function for delimiter toggle */
	{
		this.toggler(jQuery('.wt_iew_delimiter_preset'), jQuery('.wt_iew_custom_delimiter'), 'other');
	},
	date_format_toggler:function() /* function for date format toggle */
	{
		this.toggler(jQuery('.wt_iew_date_format_preset'), jQuery('.wt_iew_custom_date_format'), 'other');
	}
}
var wt_iew_conditional_help_text=
{
	Set:function(prnt)
	{
		prnt=prnt ? prnt : jQuery('body');
		const regex = /\[(.*?)\]/gm;
		let m;
		prnt.find('.wt-iew_conditional_help_text').each(function()
		{
			var help_text_elm=jQuery(this);
			var this_condition=jQuery(this).attr('data-iew-help-condition');
			if(this_condition!='')
			{
				var condition_conf=new Array();
				var field_arr=new Array();
				while ((m = regex.exec(this_condition)) !== null)
				{
					/* This is necessary to avoid infinite loops with zero-width matches */
				    if(m.index === regex.lastIndex)
				    {
				        regex.lastIndex++;
				    }
				    condition_conf.push(m[1]);
				    condition_arr=m[1].split('=');
				    if(condition_arr.length>1) /* field value pair */
				    {
				    	field_arr.push(condition_arr[0]);
				    }
				}
				if(field_arr.length>0)
				{					
					var callback_fn=function()
					{
						var is_hide=true;
						var previous_type='';
						for(var c_i=0; c_i<condition_conf.length; c_i++)
						{
							var cr_conf=condition_conf[c_i]; /* conf */
							var conf_arr=cr_conf.split('=');
							if(conf_arr.length>1) /* field value pair */
							{
								if(previous_type!='field')
								{
									previous_type='field';
									var elm=jQuery('[name="'+conf_arr[0]+'"]');
									var vl='';
									if(elm.prop('nodeName').toLowerCase()=='input' && elm.attr('type')=='radio')
									{
										vl=jQuery('[name="'+conf_arr[0]+'"]:checked').val();
									}
									else if(elm.prop('nodeName').toLowerCase()=='input' && elm.attr('type')=='checkbox')
									{
										if(elm.is(':checked'))
										{
											vl=elm.val();
										}
									}else
									{
										vl=elm.val();
									}
									is_hide=(vl==conf_arr[1] ? false : true);
								}
							}else /* glue */
							{
								if(previous_type!='glue')
								{
									previous_type='glue';
									if(conf_arr[0]=='OR')
									{
										if(is_hide===false) /* one previous condition is okay, then stop the loop */
										{
											break;
										}

									}else if(conf_arr[0]=='AND')
									{
										if(is_hide===true && c_i>0) /* one previous condition is not okay,  then stop the loop */
										{
											break;
										} 
									}
								}
							}
						}
						if(is_hide)
						{
							help_text_elm.hide();
						}else
						{
							help_text_elm.css({'display':'inline-block'});
						}
					}
					callback_fn();
					for(var f_i=0; f_i<field_arr.length; f_i++)
					{
						var elm=jQuery('[name="'+field_arr[f_i]+'"]');
						if(elm.prop('nodeName')=='radio' || elm.prop('nodeName')=='checkbox')
						{
							elm.on('click', callback_fn);
						}else
						{
							elm.on('change', callback_fn);
						}
					}
				}
			}
		});
	}
}


jQuery(document).ready(function(){
	wt_iew_popup.Set();
	wt_iew_settings_form_basic.Set('#'+wt_iew_basic_params.plugin_id);
	wt_drp_menu.Set();
	wt_iew_file_attacher.Set();
	wt_iew_form_toggler.Set();
	wt_field_group.Set();
        wt_saved_templates.Set();        
});
