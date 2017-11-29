;(function( $ ) {
    tinymce.PluginManager.add('pushortcodes', function( editor )
    {
        var shortcodeValues = [];
        jQuery.each(shortcodes_button, function(i)
        {
            shortcodeValues.push({text: shortcodes_button[i], value:i});
        });

        editor.addButton('pushortcodes', {
            //type: 'listbox',
            text: 'Shortcodes',
            onclick : function(e){
                $.post(
                    ajaxurl,
                    {
                        action : 'show_shortcodes',
                        shortcode_array :  shortcode_array
                    },
                    function(data){
                        $('#wpwrap').append(data);
                    }
                )
            },
            values: shortcodeValues
        });
    });

    var selector = '';

    $(document).on( 'click', '.modal-content .close', function(){
        $(this).parent().parent().remove();
    }).on( 'click', '.sm_shortcode_list li',function(){
        selector = $(this);
        get_shortcode_attr( $(this).data('id'), selector.text(), $('#sm-modal') );
        //$('#sm-modal').remove();

    }).on( 'click', '.shortcode_atts_ok', function(){
        var atts_string = '';

        for( v in sm_attrs.$data.shortcode_atts ) {
            atts_string = atts_string + ' ' + sm_attrs.$data.shortcode_atts[v].name + '="' + sm_attrs.$data.shortcode_atts[v].value + '"';
        }
        tinyMCE.activeEditor.selection.setContent('['+selector.text().trim()+' ' + atts_string + '][/'+selector.text().trim()+']' );

        $('#sm-modal,#sm-modal-atts').remove();
    }).on( 'click', '.shortcode_atts_cancel', function(){
        $('#sm-modal-atts').remove();
    });

    var get_shortcode_attr = function( id, tag, shortcode_modal ) {

        $.post(
            ajaxurl,
            {
                action : 'sm_get_shortcode_atts',
                shortcode_id : id,
                tag : tag
            },
            function( data ) {
                if( data.trim() != '' ) {
                    $('#sm-modal').append(data);
                } else {
                    tinyMCE.activeEditor.selection.setContent('['+selector.text().trim()+'][/'+selector.text().trim()+']' );
                    shortcode_modal.remove();
                }

            }
        )
    }
}(jQuery));
