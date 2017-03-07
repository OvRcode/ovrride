(function($){

    $(document).ready(function(){
        var sm_shortcode = new Vue({
            el: '#wpwrap',
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