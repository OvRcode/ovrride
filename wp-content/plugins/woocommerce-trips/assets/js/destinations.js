jQuery(document).ready( function( $ ) {
var upload_image_button=false;
$('#upload_trail_map_button,#upload_trail_map_2_button,#upload_trail_map_3_button,#upload_trail_map_4_button').click(function() {
        upload_image_button =true;
        formfieldID=$(this).prev().attr('id');
     //formfield = jQuery("#"+formfieldID).attr('name');
     tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
        if(upload_image_button==true){

                var oldFunc = window.send_to_editor;
                window.send_to_editor = function(html) {

                imgurl = jQuery('img', html).attr('src');
                jQuery("#"+formfieldID).val(imgurl);
                 tb_remove();
                window.send_to_editor = oldFunc;
                }
        }
        upload_image_button=false;
    });

});
