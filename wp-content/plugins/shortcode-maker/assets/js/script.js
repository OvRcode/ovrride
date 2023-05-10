(function($){
    $(document).ready(function(){
        $(document).on('click','.sm_modification_notice button.notice-dismiss',function () {
            $.post(
                ajaxurl,
                {
                    action: 'sm_dissmiss_modification_notice',
                    dismiss: true
                },
                function (data) {
                }
            )
        });

        var sm_shortcode = new Vue({
            el: '.attr_field',
            data : {
                shortcode_atts : shortcode_atts
            },
            methods : {
                add_attr_box : function(){
                    this.shortcode_atts.push({
                        name : 'Attribute_name',
                        value : 'Attribute_value'
                    });
                },
                remove_attr : function( key ) {
                    this.shortcode_atts.splice(key,1);
                }
            }
        });
    });
}(jQuery))