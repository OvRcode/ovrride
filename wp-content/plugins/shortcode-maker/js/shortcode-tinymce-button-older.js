(function() {
    tinymce.create('tinymce.plugins.pushortcodes', {

        init : function(ed, url) {
            var t = this;
            t.editor = ed;
        },

        //Creates the dropdown
        createControl : function(n, cm)
        {
            if(n=='pushortcodes')
            {
                var button = cm.createListBox('shortcode_dropdown',
                {
                     title : 'Shortcodes',
                     onselect : function(code) {
                        if(tinyMCE.activeEditor.selection.getContent() == '')
                        {
                            tinyMCE.activeEditor.selection.setContent( '['+code+']' );
                        }
                        else
                        {
                            tinyMCE.activeEditor.execCommand('mceReplaceContent', false, '['+code+']{$selection}[/'+code+']');
                        }
                        return false;
                     }
                });

                //Add the options to the dropdown
                for(var count=0; count<shortcodes_button.length; count++)
                {
                    button.add('['+shortcodes_button[count]+']', shortcodes_button[count]);
                }

                return button;
            }
            return null;
        }
    });

    tinymce.PluginManager.add('pushortcodes', tinymce.plugins.pushortcodes);

})();