(function ($) {
    var Sketch = VueColor.Sketch;
    Vue.component('color-picker-sketch',Sketch);
    $(document).ready(function () {
        smps_app = new Vue({
            el : '.smps_app',
            data : {
                //
                show_shortcode_settings_panel : false,

                hide_packaged_shortcode_panel : hide_shortcode_panel,/*false*/
                target_class : '',
                target_item : '',
                settings_modal_label : '',

                //
                edit_target_item : '',
                edit_target_item_data : '',

                //
                visible_button_section : ''
            },
            methods : {
                get_settings_html : function ( settings_class, shortcode_name , item_label) {
                    smps_app.target_class = settings_class;
                    smps_app.target_item = shortcode_name;
                    smps_app.settings_modal_label = item_label;

                    smps_app.show_shortcode_settings_panel = true;
                    //smps_app.$emit( 'edit_mode', smps_app.edit_target_item, smps_app.edit_target_item_data );
                },
                dismiss_settings_panel : function () {
                    this.reset_all();
                    smps_app.show_shortcode_settings_panel = false;
                },
                make_section_visible : function ( section ) {
                    smps_app.visible_button_section = section;
                },
                reset_all : function () {
                    show_packaged_shortcode_panel =  false;
                    this.target_class = '';
                    this.target_item = '';
                    this.settings_modal_label = '';

                    //
                    this.edit_target_item = '';
                    this.edit_target_item_data = '';

                }
            }
        });

        window.onload = function () {
            tinyMCE.activeEditor.on('NodeChange',function () {
                var selection = tinyMCE.activeEditor.selection.getContent();

                if ( !selection ) {
                    smps_app.reset_all();return;
                }

                if( selection.indexOf('[') == 0 && selection.indexOf(']') == selection.length - 1 ) {
                    var splits = selection.split(' ');

                    if( splits[0].trim() == '[smps_shortcode' && splits[1].trim().indexOf('element=') != -1 ) {
                        var encoded_str = splits[2].trim().replace('data=','').replace(/"/g,'');

                        var decoded_data = '';
                        try {
                            decoded_data = JSON.parse(decodeURIComponent(raw_item_data))
                        }
                        catch(err) {
                            decoded_data = JSON.parse(atob(encoded_str))
                        }



                        if( typeof decoded_data == 'object' ) {
                            var item_name = splits[1].trim().replace('element=','').replace(/"/g,'');
                            smps_app.edit_target_item = item_name;
                            smps_app.edit_target_item_data = decoded_data;
                        }
                    }
                }
            });
        }

        $(document).on( 'click', '.sm_feature_notice .notice-dismiss', function () {
            $.post(
                ajaxurl,
                {
                    action : 'sm_dismiss_feature_notice',
                    feature_notice_dissmiss : 1
                },
                function (data) {

                }
            )
        }).on('click','.sm_doc_link',function () {

        });
    });
}(jQuery));