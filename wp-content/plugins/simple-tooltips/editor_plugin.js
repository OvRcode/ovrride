(function() {
	var button_name = 'simple_tooltips'; //set button name
	
	tinymce.create('tinymce.plugins.'+button_name, {
		init : function(ed, url) {
			ed.addButton(button_name, {
				title : 'Add a Tooltip', //set button label
				image : url+'/icon.png', //set icon filename (20 X 20px). put icon in same folder
				onclick : function() {
					//idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
					//var vidId = prompt("YouTube Video", "Enter the id or url for your video");
					//var m = idPattern.exec(vidId);
					//if (m != null && m != 'undefined')
						ed.execCommand('mceInsertContent', false, '[simple_tooltip content=\'This is the content for the tooltip bubble\']This triggers the tooltip[/simple_tooltip]');
				}
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname : button_name,
				author : 'Justin Saad',
				authorurl : 'http://clevelandwebdeveloper.com/',
				infourl : 'http://clevelandwebdeveloper.com/',
				version : "1.0"
			};
		}
	});
	tinymce.PluginManager.add(button_name, tinymce.plugins[button_name]);
})();