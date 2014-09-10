jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.smart_coupons_shortcode_plugin', {
        init : function(ed, url) {
                var assetsUrl = url.substr(0, url.lastIndexOf("/js"));
                // Register command for when button is clicked
                ed.addCommand('smart_coupons_insert_shortcode', function() {
                    
                    ed.windowManager.open({
                        id : 'sc_coupons_attributes',
                        width : "auto",
                        height : "auto",
                        wpDialog : true
                    }, {
                        plugin_url : url // Plugin absolute URL
                    });
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('sc_shortcode_button', {title : 'Insert Smart Coupons shortcode', cmd : 'smart_coupons_insert_shortcode', image: assetsUrl + '/images/coupon_shortcode.jpg' });
        },   
    });

    // Register our TinyMCE plugin
    // first parameter is the button ID1
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('sc_shortcode_button', tinymce.plugins.smart_coupons_shortcode_plugin);
});