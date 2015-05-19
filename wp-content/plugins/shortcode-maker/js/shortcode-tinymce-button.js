(function() {
    tinymce.PluginManager.add('pushortcodes', function( editor )
    {
        var shortcodeValues = [];
        jQuery.each(shortcodes_button, function(i)
        {
            shortcodeValues.push({text: shortcodes_button[i], value:i});
        });

        editor.addButton('pushortcodes', {
            type: 'listbox',
            text: 'Shortcodes',
            onselect: function(e) {
                var v = e.control._text;

                tinyMCE.activeEditor.selection.setContent( '[' + v + '][/' + v + ']' );
            },
            values: shortcodeValues
        });
    });
})();
