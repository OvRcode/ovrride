$(document).ready(function (){

    if ( typeof(BDWP_CaptchaImageRenderCheck) == "undefined" ) {

    	var BDWP_CaptchaImageRenderCheck = function () {
    		this.urlCaptchaImage	= $('#BDUrlCaptchaImage').val();
    		this.pluginFolder		= $('#BDPluginFolder').val();
    		this.loading       = '<div class="updated" style="border: none"><p><img src="' + this.pluginFolder +'public/images/loading.gif"> ' + $('#BDMsgLoadingRenderCheck').val() + '</p></div>';
    	}

    	BDWP_CaptchaImageRenderCheck.prototype.CaptchaImageRenderCheck = function () {
    		var progressUrl = this.pluginFolder + 'handlers/captcha_provider_installation_handler.php';
    		$('#lblMessageStatus').html(this.loading);

	        var request = jQuery.ajax({
	            type  : 'GET',
	            url   : this.urlCaptchaImage,
	            async : true,
	            cache : false
	        });

	        request.done(function (data, textStatus, xhr) {
	            var contentType = xhr.getResponseHeader ("Content-Type");
	            var contentLength = xhr.getResponseHeader ("Content-Length");
	            var statusCode = xhr.status;

	            if (contentType && contentLength && statusCode && !BDWP_CaptchaImageRenderCheck.prototype.IsCaptchaImage.call(this, statusCode, contentLength, contentType)) {
	                BDWP_CaptchaImageRenderCheck.prototype.DisableLoginForm.call(this, progressUrl);
	            } else {
	                BDWP_CaptchaImageRenderCheck.prototype.SessionAndQueryStringsWokingCheck.call(this, progressUrl);
	            }
	        });
	        
	        request.fail(function (xhr, textStatus) {
				BDWP_CaptchaImageRenderCheck.prototype.DisableLoginForm.call(this, progressUrl);
	        });
    	}

    	BDWP_CaptchaImageRenderCheck.prototype.DisableLoginForm = function (progressUrl) {
	        var request = jQuery.ajax({
	            type  : 'POST',
	            url   : progressUrl,
	            data  : {
	            	bdwpProgress : 'disable_login_form',
	            	bdwpOptions : $('#BDOptions').val()
	            },
	            async : true,
	            cache : false
	        });
	        
	        request.done(function (data) {
	        	if (data != '') {
		            var jsonData = jQuery.parseJSON(data);
		            if (jsonData.status == 'LOGIN_DISABLED') {
		                var msg = '<div class="error"><p><strong>' + $('#BDMsgImageRenderError').val() + '</strong></p></div>';
		                $('#lblMessageStatus').html(msg);
		                $('input[name="botdetect_options[on_login]"]').prop('checked', false);
		            }
		        } else {
		        	 $('#lblMessageStatus').html('');
		        }
	        });
			
			request.fail(function (xhr, textStatus) { $('#lblMessageStatus').html('') });
    	}

    	BDWP_CaptchaImageRenderCheck.prototype.SessionAndQueryStringsWokingCheck = function (progressUrl) {
    		var request = jQuery.ajax({
	            type  : 'POST',
	            url   : progressUrl,
	            data  : { 
	            	bdwpProgress : 'session_and_query_string_check',
	            	bdwpOptions : $('#BDOptions').val()
	            },
	            async : true,
	            cache : false
	        });

	        request.done(function (data) {
	        	if (data != '') {
	            	var jsonData = jQuery.parseJSON(data);
	            	var msg = '';

		            if (jsonData.status == 'ERROR_OPTIONS_QUERY_STRING_IS_ENABLED') {
		                msg = $('#BDMsgDisableSuspiciousQueryStrings').val();
		                
		            } else if (jsonData.status == 'ERROR_SESSION_IS_DISABLED') {
		            	msg = $('#BDMsgSessionIsDisabled').val();
		            }

		            if (msg == '') {
		            	$('#lblMessageStatus').html('');
		            } else {
		            	$('input[name="botdetect_options[on_login]"]').prop('checked', false);
	                	$('#lblMessageStatus').html('<div class="error"><p><strong>' + msg +'</strong></p></div>');
	                }
	        	} else {
	        		 $('#lblMessageStatus').html('');
	        	}
	        });

	        request.fail(function (xhr, textStatus) {  $('#lblMessageStatus').html('') });
    	}

    	BDWP_CaptchaImageRenderCheck.prototype.IsCaptchaImage = function (statusCode, contentLength, contentType) {
    		var typeImages = ['image/jpeg', 'image/gif', 'image/png'];
        	return (statusCode == 200 && jQuery.inArray(contentType, typeImages) != -1 && contentLength > 1000)? true : false;
    	}

    	var bdwpCaptchaImagesRender = new BDWP_CaptchaImageRenderCheck();
    	bdwpCaptchaImagesRender.CaptchaImageRenderCheck();
    }
});