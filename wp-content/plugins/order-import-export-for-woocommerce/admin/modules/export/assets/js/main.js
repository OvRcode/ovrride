var wt_iew_basic_export=(function( $ ) {
	//'use strict';
	var wt_iew_basic_export=
	{
		ajax_data:{},
		selected_template:0,
		selected_template_name:'',
		to_export:'order',
		to_export_title:'',
		export_method:'',
		current_step:'',
		loaded_status_arr:{'loaded':1, 'loading':2, 'not_loaded':0},
		page_overlay:false,
		step_keys:[],
		form_data:{},
		only_enabled_data:false,
		on_rerun:false,
		rerun_id:0,
		Set:function()
		{
			this.step_keys=Object.keys(wt_iew_export_basic_params.steps);
			if(this.on_rerun)
			{
				this.rerun();
			}else
			{
				var first_step=this.step_keys[0];
				this.load_steps([first_step], first_step);
				this.show_step_page(first_step, false); /* just to show a loading */
			}	
		},
		rerun:function()
		{
			var last_step=this.step_keys[this.step_keys.length-1];
			this.load_steps(this.step_keys, last_step);
			this.show_step_page(last_step, false); /* just to show a loading */
		},
		load_steps:function(steps, step_to_show)
		{
			this.prepare_ajax_data('get_steps', 'json');
			this.ajax_data.steps=steps;
			if(this.on_rerun)
			{
				this.ajax_data.rerun_id=this.rerun_id;
			}
			this.set_step_loading_status(steps, 'loading');
			this.set_ajax_page_loader(steps, 'loading');
			$.ajax({
				type: 'POST',
            	url:wt_iew_basic_params.ajax_url,
            	data:this.ajax_data,
            	dataType:'json',
            	success:function(data)
				{
					if(data.status==1)
					{
						wt_iew_basic_export.set_step_page(data);
						wt_iew_basic_export.set_step_loading_status(steps, 'loaded');
						if(step_to_show)
						{
							wt_iew_basic_export.show_step_page(step_to_show, true);
						}
						if(wt_iew_basic_export.on_rerun)
						{
							wt_iew_basic_export.load_meta_mapping_fields();
							wt_iew_basic_export.on_rerun=false;
							wt_iew_basic_export.rerun_id=0;
						}
					}else
					{
//						wt_iew_basic_import.set_step_loading_status(steps, 'not_loaded');
                                                wt_iew_basic_export.set_step_loading_status(steps, 'not_loaded');
						wt_iew_basic_export.set_ajax_page_loader(steps, 'error');
					}
					wt_iew_basic_export.remove_ajax_page_loader();
				},
				error:function()
				{
//					wt_iew_basic_import.set_step_loading_status(steps, 'not_loaded');
                                        wt_iew_basic_export.set_step_loading_status(steps, 'not_loaded');
					wt_iew_basic_export.remove_ajax_page_loader();
					wt_iew_basic_export.set_ajax_page_loader(steps, 'error');
				}
			});
		},
		load_meta_mapping_fields:function()
		{
			if($('.meta_mapping_box_con').length>0)
			{
				if($('.meta_mapping_box_con[data-loaded="0"]').length==0)
				{
					return false;
				}
			}else
			{
				return false;
			}

			this.prepare_ajax_data('get_meta_mapping_fields', 'json');
			$('.meta_mapping_box_con[data-loaded="0"]').html('<div class="wt_iew_export_step_loader">'+wt_iew_basic_params.msgs.loading+'</div>');
			$.ajax({
				type: 'POST',
            	url:wt_iew_basic_params.ajax_url,
            	data:this.ajax_data,
            	dataType:'json',
            	success:function(data)
				{
					if(data.status==1)
					{
						$.each(data.meta_html, function(meta_id, meta_content){
							$('.meta_mapping_box_con[data-key="'+meta_id+'"]').html(meta_content).attr('data-loaded', 1);					  	
						});
						wt_iew_basic_export.enable_sortable();
						wt_iew_basic_export.reg_mapping_field_bulk_action();
					}else
					{
						$('.meta_mapping_box_con[data-loaded="0"]').html('<div class="wt_iew_export_step_loader">'+wt_iew_basic_params.msgs.error+'</div>');
					}
				},
				error:function()
				{
					$('.meta_mapping_box_con[data-loaded="0"]').html('<div class="wt_iew_export_step_loader">'+wt_iew_basic_params.msgs.loading+'</div>');
				}
			});
		},
		console_formdata:function()
		{
			console.log(this.form_data);
		},
		refresh_step:function(no_overlay)
		{
			if(!no_overlay){
				this.page_overlay=true; 
			}
			this.load_steps([this.current_step], this.current_step);
		},
		load_pending_steps:function(no_overlay)
		{
			if(!no_overlay){
				this.page_overlay=true; 
			}
			var rest_steps=this.step_keys.slice(0);
			rest_steps.shift(); /* remove first step. no need to load it agian */
			this.load_steps(rest_steps, this.current_step);
		},
		get_page_dom_object:function(step)
		{
			return $('.wt_iew_export_step_'+step);
		},
		remove_ajax_page_loader:function()
		{
			$('.wt_iew_loader_info_box').hide();
			$('.wt_iew_overlayed_loader').hide();
			$('.spinner').css({'visibility':'hidden'});	
			this.page_overlay=false;
		},
		set_ajax_page_loader:function(steps, msg_type)
		{
			if(this.page_overlay)
			{
				var h=parseInt($('.wt_iew_export_step_main').outerHeight());
				var w=parseInt($('.wt_iew_export_step_main').outerWidth());
				$('.wt_iew_overlayed_loader').show().css({'height':h,'width':w,'margin-top':'30px','margin-left':'30px'});
				$('.spinner').css({'visibility':'visible'});
			}else
			{
				var msg='';
				if(msg_type=='loading')
				{
					msg=wt_iew_basic_params.msgs.loading;
				}else if(msg_type=='error')
				{
					msg=wt_iew_basic_params.msgs.error;
				}
				$.each(steps, function(step_ind, step){
					wt_iew_basic_export.get_page_dom_object(step).html('<div class="wt_iew_export_step_loader">'+msg+'</div>');
				});
			}		
		},
		hide_export_info_box:function()
		{
			$('.wt_iew_loader_info_box').hide();
		},
		set_export_progress_info:function(msg)
		{
			$('.wt_iew_loader_info_box').show().html(msg);
		},
		nonstep_actions:function(action)
		{
			if(this.export_method=='template' && this.selected_template==0)
			{	
				$('.wt_iew_warn').hide();
				$('.wt_iew_export_template_wrn').show();
				return false;
			}

			this.prepare_ajax_data(action, 'json');

			if(action=='save_template' || action=='save_template_as' || action=='update_template')
			{
				$('.wt_iew_template_name_wrn').hide();
				var pop_elm=$('.wt_iew_template_name');
				var popup_label=pop_elm.attr('data-save-label');
				if(action=='save_template_as')
				{
					var popup_label=pop_elm.attr('data-saveas-label');
				}
				pop_elm.find('.wt_iew_popup_hd_label, .wt_iew_template_create_btn').text(popup_label);
				wt_iew_popup.showPopup(pop_elm);
				$('[name="wt_iew_template_name_field"]').val(this.selected_template_name).focus();

				$('.wt_iew_template_create_btn').unbind('click').click(function(){
					var name=$.trim($('.wt_iew_template_name_field').val());
					if(name=='')
					{
						$('.wt_iew_template_name_wrn').show();
						$('.wt_iew_template_name_field').focus();
					}else
					{
						$('.wt_iew_template_name_wrn').hide();
						wt_iew_popup.hidePopup();

						wt_iew_basic_export.prepare_form_data();
						wt_iew_basic_export.ajax_data['template_name']=name;
						wt_iew_basic_export.ajax_data['form_data']=wt_iew_basic_export.form_data;
						wt_iew_basic_export.do_nonstep_action(action);
					}
				});
			}else if(action=='export' || action=='upload' || action=='export_image')
			{
				if(action=='export' || action=='export_image')
				{
					this.ajax_data['offset']=0;
					this.prepare_form_data();
					this.ajax_data['form_data']=this.form_data;
				}
				wt_iew_basic_export.do_nonstep_action(action);
			}else
			{
				/* custom action section for other modules */
				this.prepare_form_data();
				this.ajax_data['form_data']=this.form_data;
				wt_iew_custom_action_basic(this.ajax_data, action, this.selected_template);
			}
		},
		do_nonstep_action:function(action)
		{
			this.page_overlay=true;
			this.set_ajax_page_loader();
			$.ajax({
				type: 'POST',
				url:wt_iew_basic_params.ajax_url,
				data:this.ajax_data,
				dataType:'json',
				success:function(data)
				{
					wt_iew_basic_export.remove_ajax_page_loader();
					if(data.status==1)
					{
						if(action=='save_template' || action=='save_template_as' || action=='update_template')
						{
							wt_iew_basic_export.selected_template=data.id;
							wt_iew_basic_export.selected_template_name=data.name;
							wt_iew_notify_msg.success(data.msg);
							//wt_iew_notify_msg.success(wt_iew_basic_params.msgs.success);
							
						}else if(action=='export' || action=='upload' || action=='export_image')
						{
							if(data.finished==1)
							{
								wt_iew_basic_export.set_export_progress_info(data.msg);
								wt_iew_notify_msg.success(wt_iew_basic_params.msgs.success);
			
							}
							else if(data.finished==2) /* Remote export */
							{
								wt_iew_basic_export.set_export_progress_info(data.msg);
								wt_iew_basic_export.ajax_data['export_id']=data.export_id;
								wt_iew_basic_export.ajax_data['total_records']=data.total_records;
								wt_iew_basic_export.ajax_data['export_action']='upload';
								wt_iew_basic_export.ajax_data['form_data']={};
								wt_iew_basic_export.do_nonstep_action('upload');
							}
							else
							{
								wt_iew_basic_export.set_export_progress_info(data.msg);
								wt_iew_basic_export.ajax_data['offset']=data.new_offset;
								wt_iew_basic_export.ajax_data['export_id']=data.export_id;
								wt_iew_basic_export.ajax_data['total_records']=data.total_records;
								wt_iew_basic_export.do_nonstep_action(action);
							}
						}else
						{

						}
					}else
					{
						if(data.msg!="")
						{
							wt_iew_notify_msg.error(data.msg);
						}else
						{
							wt_iew_notify_msg.error(wt_iew_basic_params.msgs.error);
						}
					}
				},
				error:function()
				{
					wt_iew_basic_export.remove_ajax_page_loader();
					wt_iew_notify_msg.error(wt_iew_basic_params.msgs.error);
				}
			});
		},
		reg_button_actions:function()
		{
			$('.wt_iew_export_action_btn').unbind('click').click(function(e){
				e.preventDefault();
				var action=$(this).attr('data-action');
				var action_type=$(this).attr('data-action-type');
				var is_previous_step=wt_iew_basic_export.is_previous_step(action);
//				if(!wt_iew_exporter_validate(action, action_type, is_previous_step))
//				{
//					return false;
//				}

				if(action_type=='step')
				{
					wt_iew_basic_export.change_step(action);
				}else
				{
					wt_iew_basic_export.nonstep_actions(action);
				}	
			});
		},
		change_step:function(step_to_go)
		{
			/* validation section */
			if(this.current_step=='post_type')
			{
				if(this.to_export=='')
				{
					$('.wt_iew_post_type_wrn').show();
					return false;
				}
			}else if(this.current_step=='method_export') /* method export page */ 
			{
				if(this.export_method=='template' && this.selected_template==0 && !this.is_previous_step(step_to_go))
				{	
					$('.wt_iew_warn').hide();
					$('.wt_iew_export_template_wrn').show();
					return false;
				}
			}

			/* setting fromdata */
			this.prepare_form_data();

			/* step changing section */
			this.show_step_page(step_to_go, true);
		},
		reset_form_data:function()
		{
			this.form_data={};
			this.selected_template=0;
			this.selected_template_name='';
			this.export_method='';
		},
		prepare_form_data:function()
		{
			if(this.current_step=='post_type')
			{	
				this.form_data['post_type_form_data']=JSON.stringify({'item_type':wt_iew_basic_export.to_export});
			}
			else if(this.current_step=='method_export')
			{			
				var mapping_enabled_fields=new Array();
				$('.wt_iew_export_step_method_export [name="wt_iew_include_these_fields[]"]').each(function(){
					if($(this).is(':checked'))
					{
						mapping_enabled_fields.push($(this).val());
					}
				});
				this.form_data['method_export_form_data']=JSON.stringify({'method_export':wt_iew_basic_export.export_method, 'mapping_enabled_fields':mapping_enabled_fields, 'selected_template':this.selected_template});
			}
			else if(this.current_step=='filter')
			{
				if($('.wt_iew_export_filter_form').length>0)
				{
					var form_data=$('.wt_iew_export_filter_form').serializeArray();
					var filter_form_data={};
					$.each(form_data, function(){
						if(filter_form_data[this.name])
						{
							if(!filter_form_data[this.name].push)
							{
								filter_form_data[this.name] = [filter_form_data[this.name]];
							}
							filter_form_data[this.name].push(this.value || '');
						}else
						{
							if(wt_iew_basic_export.is_multi_select(this.name))
							{
								filter_form_data[this.name] = [(this.value || '')];
							}else
							{
								filter_form_data[this.name] = this.value || '';
							}
						}
					});
					this.form_data['filter_form_data']=JSON.stringify(filter_form_data);
				}
			}
			else if(this.current_step=='mapping')
			{
				
				/**
				* Default mapping fields  //===============================================
				*/

				var mapping_form_data={};
				var mapping_fields={};
				var mapping_selected_fields={}; /* this value is only for backend processing */

				$('.wt-iew-exporter-default-mapping-tb tbody tr').each(function(){
					
					var columns_key=$(this).find('.columns_key').val();
					var columns_val=$(this).find('.columns_val').val();
					
					if(wt_iew_basic_export.only_enabled_data===false) /* get whole keys instead of enabled/disabled */
					{
						var enabled=($(this).find('.columns_key').is(':checked') ? 1 : 0);
						mapping_fields[columns_key]=[columns_val,enabled];
						
						if(enabled==1)
						{
							mapping_selected_fields[columns_key]=columns_val;
						}

					}else
					{
						if($(this).find('.columns_key').is(':checked'))
						{
							mapping_fields[columns_key]=columns_val;
							mapping_selected_fields[columns_key]=columns_val;
						}
					}
				});

				var mapping_enabled_fields=new Array();
				/*
				$('.wt_iew_export_step_mapping [name="wt_iew_include_these_fields[]"]').each(function(){
					
					if($(this).is(':checked'))
					{
						mapping_enabled_fields.push($(this).val());
					}

				});
				*/

				mapping_form_data={'mapping_fields':mapping_fields,'mapping_enabled_fields':mapping_enabled_fields, 'mapping_selected_fields':mapping_selected_fields};
				this.form_data['mapping_form_data']=JSON.stringify(mapping_form_data);
				
				

				/**
				* meta mapping fields  //===============================================
				*/

				var meta_step_form_data={};
				var mapping_fields={};
				var mapping_selected_fields={}; /* this value is only for backend processing */

				$('.wt-iew-exporter-meta-mapping-tb').each(function(){
					var mapping_key=$(this).attr('data-field-type');
					mapping_fields[mapping_key]={};
					mapping_selected_fields[mapping_key]={};
					
					$(this).find('tbody tr').each(function(){						
						if($(this).find('.columns_key').length>0 && $(this).find('.columns_val').length>0)
						{
							var columns_key=$(this).find('.columns_key').val();
							var columns_val=$(this).find('.columns_val').val();
							
							if(wt_iew_basic_export.only_enabled_data===false) /* get whole keys instead of enabled/disabled */
							{
								var enabled=($(this).find('.columns_key').is(':checked') ? 1 : 0);
								mapping_fields[mapping_key][columns_key]=[columns_val,enabled];
								
								if(enabled==1)
								{
									mapping_selected_fields[mapping_key][columns_key]=columns_val;
								}

							}else
							{
								if($(this).find('.columns_key').is(':checked'))
								{
									mapping_fields[mapping_key][columns_key]=columns_val;
									mapping_selected_fields[mapping_key][columns_key]=columns_val;
								}
							}
						}
					});
				});

				meta_step_form_data={'mapping_fields':mapping_fields, 'mapping_selected_fields':mapping_selected_fields};
				this.form_data['meta_step_form_data']=JSON.stringify(meta_step_form_data);

			}
			else
			{
				var current_form_data={};
				if($('.wt_iew_export_'+this.current_step+'_form').length>0) /* may be user hit the back button */
				{
					var form_data=$('.wt_iew_export_'+this.current_step+'_form').serializeArray();
					$.each(form_data, function(){
						
						if(current_form_data[this.name])
						{
							if(!current_form_data[this.name].push)
							{
								current_form_data[this.name] = [current_form_data[this.name]];
							}
							current_form_data[this.name].push(this.value || '');
						}else
						{
							current_form_data[this.name] = this.value || '';
						}

					});

					this.form_data[this.current_step+'_form_data']=JSON.stringify(current_form_data);
				}
			}

			/* we are resetting formdata on second step. If user not going to first step then post type formdata will be empty. */
			if(this.current_step!='post_type')
			{	
				this.form_data['post_type_form_data']=JSON.stringify({'item_type':wt_iew_basic_export.to_export});
			}

		},
		is_multi_select:function(name)
		{
			var elm=$('[name="'+name+'"]');
			if(elm.prop("tagName").toLowerCase()=='select' && this.has_attr(elm,'multiple'))
			{
				return true;
			}else
			{
				return false;
			}
		},
		has_attr:function(elm,attr_name)
		{
			var attr = elm.attr(attr_name);
			if(typeof attr!==typeof undefined  &&  attr!==false)
			{
				return true;
			}else
			{
				return false;
			}
		},
		is_previous_step:function(step_key)
		{
			if(wt_iew_export_basic_params.steps.hasOwnProperty(step_key)) 
			{				
				if(this.step_keys.indexOf(step_key)<this.step_keys.indexOf(this.current_step))
				{
					return true;
				}
			}
			return false;
		},
		is_step_loaded:function(step)
		{
			if(this.get_page_dom_object(step).length==0){ return true; } /* block infinite loop, if element is not available */ 
			return (this.get_page_dom_object(step).attr('data-loaded')==this.loaded_status_arr['loaded']);
		},
		set_step_loading_status:function(steps, status)
		{
			$.each(steps, function(step_ind, step){
			  	wt_iew_basic_export.get_page_dom_object(step).attr('data-loaded', wt_iew_basic_export.loaded_status_arr[status]);
			});
		},
		show_step_page:function(step, force_check_loaded)
		{
			$('.wt_iew_export_step').hide();	
			this.get_page_dom_object(step).show();
			this.current_step=step;
			if(force_check_loaded)
			{	
				if(this.is_step_loaded(step))
				{
					this.current_step_actions();
				}else
				{
					this.refresh_step(true);
				}
			}else
			{
				this.current_step_actions();
			}
			wt_iew_form_toggler.runToggler();
		},
		current_step_actions:function() /* current page actions after page is visible */
		{
			if(this.current_step=='filter')
			{
				this.load_meta_mapping_fields();
			}else if(this.current_step=='advanced')
			{
				wt_field_group.Set();
			}
		},
		show_post_type_name:function()
		{
			if(this.to_export!="" && this.to_export_title=='')
			{
				$('[name="wt_iew_export_post_type"]').val(this.to_export);
				this.to_export_title=$('[name="wt_iew_export_post_type"] option:selected').text();
			}
			$('.wt_iew_step_head_post_type_name').html(this.to_export_title);
                        $('.wt-ierpro-blue-btn').attr("href", wt_iew_basic_params.pro_plugins[this.to_export]['url']);
                        $('.wt-ier-product-name').html(wt_iew_basic_params.pro_plugins[this.to_export]['name']);
                        $('.wt-ierpro-name>img').attr("src", wt_iew_basic_params.pro_plugins[this.to_export]['icon_url']);
                        $('.wt-ier-gopro-cta').hide();
                        $('.wt-ier-'+this.to_export).show();
		},
		page_actions:function(step)
		{
			if(step=='post_type') /* post type page */
			{
				$('[name="wt_iew_export_post_type"]').unbind('change').change(function(){					
					wt_iew_basic_export.to_export=$(this).val();
					wt_iew_basic_export.to_export_title='';
					wt_iew_basic_export.reset_form_data();
					$('.wt_iew_post_type_name').html('');
					if(wt_iew_basic_export.to_export=='')
					{
						$('.wt_iew_post_type_wrn').show();
					}else
					{
						$('.wt_iew_post_type_wrn').hide();
						var post_type_name=$('[name="wt_iew_export_post_type"] option:selected').text();
						/* $('.wt_iew_post_type_name').html(': '+post_type_name); */
						wt_iew_basic_export.to_export_title=post_type_name;
						wt_iew_basic_export.load_pending_steps(true);	
					}
				});
			}
			else if(step=='method_export') /* method export page */ 
			{
				this.export_method=$('[name="wt_iew_export_method_export"]:checked').val();
				this.toggle_export_method_options();

				$('[name="wt_iew_export_method_export"]').unbind('click').click(function(){
					var vl=$(this).val();
					if(wt_iew_basic_export.export_method==vl)
					{
						return false;
					}
					wt_iew_basic_export.reset_form_data();
					wt_iew_basic_export.export_method=vl;
					if(wt_iew_basic_export.export_method=='template')
					{
						if($('.wt-iew-export-template-sele').val()==0)
						{
							wt_iew_basic_export.refresh_step();
						}else
						{
							wt_iew_basic_export.selected_template=$('.wt-iew-export-template-sele').val();
							wt_iew_basic_export.selected_template_name=$.trim($('.wt-iew-export-template-sele option:selected').text());
							wt_iew_basic_export.load_pending_steps();
						}		
					}else
					{ 
						wt_iew_basic_export.load_pending_steps();
					}					
				});                                

				$('.wt-iew-export-template-sele').unbind('change').change(function(){
					wt_iew_basic_export.selected_template=$(this).val();
					$('.wt_iew_warn').hide();				
					if(wt_iew_basic_export.selected_template==0)
					{
						$('.wt_iew_export_template_wrn').show();
					}else
					{
						wt_iew_basic_export.selected_template_name=$.trim($('.wt-iew-export-template-sele option:selected').text());
						wt_iew_basic_export.load_pending_steps();
					}
				});
                                wt_iew_basic_export.warn_on_refresh();
			}
			else if(step=='filter') /* filter page */ 
			{
				$('.wc-enhanced-select').select2();
				$( document.body ).trigger( 'wc-enhanced-select-init' );
                                wt_iew_basic_export.warn_on_refresh();
			}
			else if(step=='mapping') /* mapping page */ 
			{
				this.enable_sortable();
				this.mapping_box_accordian();
				this.reg_mapping_field_bulk_action();
                                wt_iew_basic_export.warn_on_refresh();
			}
			else if(step=='advanced')
			{	
				/* callback for external adapters */
				if($('select[name="wt_iew_file_into"]').length>0) /* multiple adapter exists so select box */
				{
					var file_into=$('[name="wt_iew_file_into"]').val();
					$('[name="wt_iew_file_into"]').unbind('change').on('change',function(){
						var file_into=$(this).val();
						wt_iew_set_file_into_fields(file_into);
					});
				}else  /* radio button */
				{
					var file_into=$('[name="wt_iew_file_into"]:checked').val();
					$('[name="wt_iew_file_into"]').on('click',function(){
						var file_into=$('[name="wt_iew_file_into"]:checked').val();
						wt_iew_set_file_into_fields(file_into);
					});
				}
				wt_iew_set_file_into_fields(file_into);

				/* separate image export option */
				if($('.wt_iew_separate_image_export').length>0) /* separate image export option available */
				{
					if($('.wt_iew_separate_image_export:checked').val()=='Yes')
					{
						$('.iew_export_image_btn').show();
					}else
					{
						$('.iew_export_image_btn').hide();
					}
				}else
				{
					$('.iew_export_image_btn').hide();
				}
				$('.wt_iew_separate_image_export').unbind('click').click(function(){
					if($(this).val()=='Yes')
					{
						$('.iew_export_image_btn').show();
					}else
					{
						$('.iew_export_image_btn').hide();
					}
				});

				/* CSV delimiter form toggler. Custom and preset delimiter */
				wt_iew_custom_and_preset.delimiter_toggler();

				/* file extension info box */
				$('.wt_iew_file_ext_info').html('.'+$('[name="wt_iew_file_as"]').val());
				$('[name="wt_iew_file_as"]').unbind('change').change(function(){
					$('.wt_iew_file_ext_info').html('.'+$(this).val());
				});
                                wt_iew_basic_export.warn_on_refresh();
			}

			/* common events */
			if($('.wt_iew_datepicker').length>0)
			{
				$('.wt_iew_datepicker').datepicker({dateFormat: 'yy-mm-dd'});
			}
			wt_field_group.Set();
			wt_iew_form_toggler.Set();
			wt_iew_conditional_help_text.Set(this.get_page_dom_object(step));

		},
		enable_sortable:function()
		{
			$('.meta_mapping_box_con[data-sortable="0"]').each(function(){
				var tb=$(this).find(".wt-iew-mapping-tb tbody");
				if(tb.length>0)
				{
					tb.sortable({
						handle: ".wt_iew_sort_handle",
						placeholder: "wt-iew-sortable-placeholder",
						forcePlaceholderSize: true,
						revert:true
					});
					$(this).attr('data-sortable', 1);
				}
			});			
		},
		toggle_export_method_options:function()
		{
			$('.wt-iew-export-method-options').hide();
			$('.wt-iew-export-method-options-'+this.export_method).show();
		},
		mapping_box_accordian:function()
		{
			$('.meta_mapping_box_hd').unbind('click').click(function()
			{
				var c_dv=$(this).parents('.meta_mapping_box').find('.meta_mapping_box_con');
				if(c_dv.is(':visible'))
				{
					c_dv.hide();
					$(this).find('.dashicons').removeClass('dashicons-arrow-down').addClass('dashicons-arrow-right');
				}else
				{
					c_dv.show();
					c_dv.find(".wt-iew-mapping-tb tbody tr td").each(function(){
						$(this).css({'width':$(this).width()});
					});
					$(this).find('.dashicons').removeClass('dashicons-arrow-right').addClass('dashicons-arrow-down');
				}
			});
		},
		mapping_fields_selected_count:function(tb_elm)
		{
			tb_elm.parents('.meta_mapping_box').find('.meta_mapping_box_selected_count_box_num').text(tb_elm.find('.wt_iew_mapping_checkbox_sub:checked').length);
		},
		reg_mapping_field_bulk_action:function()
		{
			$('.wt_iew_mapping_checkbox_main').each(function()
			{
				var tb=$(this).parents('.wt-iew-mapping-tb');
				if(tb.find('.wt_iew_mapping_checkbox_sub:checked').length==tb.find('.wt_iew_mapping_checkbox_sub').length)
				{
					tb.find('.wt_iew_mapping_checkbox_main').prop('checked',true);
				}else
				{
					tb.find('.wt_iew_mapping_checkbox_main').prop('checked',false);
				}
				wt_iew_basic_export.mapping_fields_selected_count(tb);
			});

			$('.wt_iew_mapping_checkbox_main').unbind('click').click(function()
			{
				var tb=$(this).parents('.wt-iew-mapping-tb');
				if($(this).is(':checked'))
				{
					tb.find('.wt_iew_mapping_checkbox_sub').prop('checked',true);
				}else
				{
					tb.find('.wt_iew_mapping_checkbox_sub').prop('checked',false);
				}
				wt_iew_basic_export.mapping_fields_selected_count(tb);
			});
			$('.wt_iew_mapping_checkbox_sub').unbind('click').click(function()
			{
				var tb=$(this).parents('.wt-iew-mapping-tb');
				if($(this).is(':checked') && tb.find('.wt_iew_mapping_checkbox_sub:checked').length==tb.find('.wt_iew_mapping_checkbox_sub').length)
				{
					tb.find('.wt_iew_mapping_checkbox_main').prop('checked',true);
				}else
				{
					tb.find('.wt_iew_mapping_checkbox_main').prop('checked',false);
				}
				wt_iew_basic_export.mapping_fields_selected_count(tb);
			});
		},
		set_step_page:function(data)
		{
			page_html=data.page_html;
			$.each(page_html, function(step_id, page_content){
			  	wt_iew_basic_export.get_page_dom_object(step_id).html(page_content);
			  	wt_iew_basic_export.page_actions(step_id);
			  	
			  	if(step_id=='method_export' && (wt_iew_basic_export.selected_template>0 || wt_iew_basic_export.on_rerun))
			  	{
			  		wt_iew_basic_export.form_data=data.template_data;
			  		
			  		if(wt_iew_basic_export.on_rerun)
			  		{
			  			if($('.wt-iew-export-template-sele').val()==0)
						{
							wt_iew_basic_export.selected_template=0;
							wt_iew_basic_export.selected_template_name='';
						}else
						{
							wt_iew_basic_export.selected_template=$('.wt-iew-export-template-sele').val();
							wt_iew_basic_export.selected_template_name=$.trim($('.wt-iew-export-template-sele option:selected').text());
						}
			  		}
			  	}
			  	wt_iew_basic_export.show_post_type_name();
			});
			this.reg_button_actions();
		},
		prepare_ajax_data:function(action, data_type)
		{
			this.ajax_data = {
	            '_wpnonce': wt_iew_basic_params.nonces.main,
	            'action': "iew_export_ajax_basic",
	            'export_action': action,
	            'selected_template': this.selected_template,
	            'to_export': this.to_export,
	            'data_type': data_type,
	            'export_method': this.export_method,
	        };
		},
                warn_on_refresh: function () {
                    window.onbeforeunload = function (event)
                    {
                        return confirm("Changes that you made may not be saved.");
                    };
                }
	}
	return wt_iew_basic_export;
	
})( jQuery );

jQuery(function() {
	
	if(wt_iew_export_basic_params.rerun_id>0)
	{
		wt_iew_basic_export.to_export=wt_iew_export_basic_params.to_export;
		wt_iew_basic_export.export_method=wt_iew_export_basic_params.export_method;
		wt_iew_basic_export.rerun_id=wt_iew_export_basic_params.rerun_id;
		wt_iew_basic_export.on_rerun=true;
	}
	wt_iew_basic_export.Set();	
});