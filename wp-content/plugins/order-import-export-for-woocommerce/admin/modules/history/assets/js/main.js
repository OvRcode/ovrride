var wt_iew_basic_history=(function( $ ) {
	//'use strict';
	var wt_iew_basic_history=
	{
		log_offset:0,
		Set:function()
		{
			this.reg_delete();
			this.reg_view_log();
			this.reg_bulk_action();
		},
		reg_view_log:function()
		{
			jQuery(document).on('click', ".wt_iew_view_log_btn", function(){					
				wt_iew_basic_history.show_log_popup();
				var history_id=$(this).attr('data-history-id');
				if(history_id>0)
				{
					wt_iew_basic_history.log_offset=0;
					wt_iew_basic_history.load_page(history_id);
				}else
				{
					var log_file=$(this).attr('data-log-file');
					if(log_file!="")
					{
						wt_iew_basic_history.view_raw_log(log_file);
					}
				}
			});
		},
		view_raw_log:function(log_file)
		{
			$('.wt_iew_log_container').html('<div class="wt_iew_log_loader">'+wt_iew_basic_params.msgs.loading+'</div>');
			$.ajax({
				url:wt_iew_basic_params.ajax_url,
				data:{'action':'iew_history_ajax_basic', _wpnonce:wt_iew_basic_params.nonces.main, 'history_action':'view_log', 'log_file':log_file, 'data_type':'json'},
				type:'post',
				dataType:"json",
				success:function(data)
				{
					if(data.status==1)
					{
						$('.wt_iew_log_container').html(data.html);
					}else
					{
						$('.wt_iew_log_loader').html(wt_iew_basic_params.msgs.error);
						wt_iew_notify_msg.error(wt_iew_basic_params.msgs.error);
					}
				},
				error:function()
				{
					$('.wt_iew_log_loader').html(wt_iew_basic_params.msgs.error);
					wt_iew_notify_msg.error(wt_iew_basic_params.msgs.error);
				}
			});
		},
		show_log_popup:function()
		{
			var pop_elm=$('.wt_iew_view_log');
			var ww=$(window).width();
			pop_w=(ww<1300 ? ww : 1300)-200;
			pop_w=(pop_w<200 ? 200 : pop_w);
			pop_elm.width(pop_w);

			wh=$(window).height();
			pop_h=(wh>=400 ? (wh-200) : wh);
			$('.wt_iew_log_container').css({'max-height':pop_h+'px','overflow':'auto'});
			wt_iew_popup.showPopup(pop_elm);
		},
		load_page:function(history_id)
		{
			var offset=wt_iew_basic_history.log_offset;
			if(offset==0)
			{
				$('.wt_iew_log_container').html('<div class="wt_iew_log_loader">'+wt_iew_basic_params.msgs.loading+'</div>');
			}else
			{
				$('.wt_iew_history_loadmore_btn').hide();
				$('.wt_iew_history_loadmore_loading').show();
			}
			$.ajax({
				url:wt_iew_basic_params.ajax_url,
				data:{'action':'iew_history_ajax_basic', _wpnonce:wt_iew_basic_params.nonces.main, 'history_action':'view_log', 'offset': offset, 'history_id':history_id, 'data_type':'json'},
				type:'post',
				dataType:"json",
				success:function(data)
				{
					$('.wt_iew_history_loadmore_btn').show();
					$('.wt_iew_history_loadmore_loading').hide();
					if(data.status==1)
					{
						wt_iew_basic_history.log_offset=data.offset;
						if(offset==0)
						{
							$('.wt_iew_log_container').html(data.html);
						}else
						{
							$('.log_view_tb_tbody').append(data.html);
						}
						if(data.finished)
						{
							$('.wt_iew_history_loadmore_btn').hide();
						}else
						{
							if(offset==0)
							{
								$('.wt_iew_history_loadmore_btn').unbind('click').click(function(){
									wt_iew_basic_history.load_page(history_id);
								});
							}
						}
					}else
					{
						$('.wt_iew_log_loader').html(wt_iew_basic_params.msgs.error);
						wt_iew_notify_msg.error(wt_iew_basic_params.msgs.error);
					}				
				},
				error:function()
				{
					$('.wt_iew_log_loader').html(wt_iew_basic_params.msgs.error);
					$('.wt_iew_history_loadmore_btn').show();
					$('.wt_iew_history_loadmore_loading').hide();
					wt_iew_notify_msg.error(wt_iew_basic_params.msgs.error);
				}
			});
		},
		reg_delete:function()
		{
			jQuery('.wt_iew_delete_history, .wt_iew_delete_log').click(function(){
				if(confirm(wt_iew_history_basic_params.msgs.sure))
				{
					window.location.href=jQuery(this).attr('data-href');
				}
			});
		},
		reg_bulk_action:function()
		{
			var checkbox_main=$('.wt_iew_history_checkbox_main');
			var checkbox_sub=$('.wt_iew_history_checkbox_sub');
			var tb=$('.history_list_tb');
			if(tb.find('.wt_iew_history_checkbox_sub:checked').length==tb.find('.wt_iew_history_checkbox_sub').length)
			{
				checkbox_main.prop('checked',true);
			}else
			{
				checkbox_main.prop('checked',false);
			}

			checkbox_main.unbind('click').click(function()
			{
				if($(this).is(':checked'))
				{
					checkbox_sub.prop('checked',true);
				}else
				{
					checkbox_sub.prop('checked',false);
				}
			});
			checkbox_sub.unbind('click').click(function()
			{
				if($(this).is(':checked') && $('.wt_iew_history_checkbox_sub:checked').length==checkbox_sub.length)
				{
					checkbox_main.prop('checked',true);
				}else
				{
					checkbox_main.prop('checked',false);
				}
			});

			$('.wt_iew_bulk_action_btn').click(function(){
				if($('.wt_iew_history_checkbox_sub:checked').length>0 && $('.wt_iew_bulk_action option:selected').val()!="")
				{
					var cr_action=$('.wt_iew_bulk_action option:selected').val();
					if(cr_action=='delete')
					{
						if(confirm(wt_iew_history_basic_params.msgs.sure))
						{
							var id_arr=new Array();
							$('.wt_iew_history_checkbox_sub:checked').each(function(){
								id_arr.push($(this).val());
							});
							var delete_url=wt_iew_history_basic_params.delete_url.replace('_history_id_', id_arr.join(','));
							window.location.href=delete_url;
						}
					}
				}
			});
		}
	}
	return wt_iew_basic_history;
	
})( jQuery );

jQuery(function() {			
	wt_iew_basic_history.Set();
});