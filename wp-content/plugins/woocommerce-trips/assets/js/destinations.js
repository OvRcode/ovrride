jQuery(document).ready( function( $ ) {
$('#upload_trail_map_button').click(function() {
  console.log("clicked");
        formfield = $('#upload_trail_map').attr('name');
        tb_show( '', 'media-upload.php?type=image&amp;TB_iframe=true' );
        return false;
    });

    window.send_to_editor = function(html) {

        imgurl = $('img',html).attr('src');
        $('#upload_trail_map').val(imgurl);
        tb_remove();
    };
});