// DOM READY START.
(function($) {
	var editor = ace.edit("srp_custom_css");
	var textarea = $('textarea[name="srp_custom_css"]').hide();
	editor.getSession().setMode("ace/mode/css");
	editor.getSession().setUseWrapMode( true );
	editor.getSession().setValue(textarea.val());
	editor.getSession().on('change', function(){
	  textarea.val(editor.getSession().getValue());
	});
})(jQuery);