(function ($) {
    $(function () {
        $('.asap-tab').click(function(){
           var attr_id = $(this).attr('id');
           var id = attr_id.replace('asap-tab-','');
           $('.asap-tab').removeClass('asap-active-tab');
           $(this).addClass('asap-active-tab'); 
           $('.asap-section').hide();
           $('#asap-section-'+id).show();
        });
        
        
        
        $('#asap-fb-authorize-ref').click(function(){
           $('input[name="asap_fb_authorize"]').click(); 
        });
        
        
          });//document.ready close
}(jQuery));