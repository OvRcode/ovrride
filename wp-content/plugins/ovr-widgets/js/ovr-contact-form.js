(function($){
  console.log(ovr_contact_vars.nonce);
  $("#ovr_contact_form").validate({
    submitHandler: function(form){
      var data = {
        action : 'ovr_contact_form_submit',
        security : ovr_contact_vars.nonce,
        from : $('#ovr_contact_email').val(),
        name : $('#ovr_contact_first').val() + " " + $('#ovr_contact_last').val(),
        phone : $('#ovr_contact_phone').val(),
        comment : $('#ovr_contact_comment').val()
      }
      $(".formContainer").spin('large');
      var jqxhr = $.post( ovr_contact_vars.ajax_url, data, function(){
        $(".formContainer").html('<h4>Your email has been sent, thanks for reaching out.<br /> We\'ll be in touch soon.</h4>');
      })
        .fail(function(){
          $(".formContainer").html('<h4>You email has not been sent, looks like we had a technical SNAFU.<br /> Please send an email to <a href="mailto:info@ovrride.com">info@ovrride.com</h4>');
        })
        .always(function(){
          $(".formContainer").spin(false);
        });

    }
  });

})( jQuery );
