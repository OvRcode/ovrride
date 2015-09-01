jQuery(document).ready(function($){
  
  // disabled add to cart button on load
  enableDisableCart();
  
  // validate fields when values change
  $("#wc-trips-form select").on("change", function(){
    enableDisableCart();
  });
  $("#wc-trips-form input").on("keyup", function(){
    enableDisableCart();
  });
  $("select[name$=_package]").on("change", function(){
    var name = $(this).attr('name');
    var cost = $(this).find(":selected").data('cost');
    $("input[name='" + name + "_cost']").val( cost );
    
  });
  $("input[name=wc_trip_age_check]").on("change", function(){
    enableDisableCart();
    var fields = [$("label[for=wc_trip_dob]"), $("#wc_trip_dob_month"), $("#wc_trip_dob_day"), $("#wc_trip_dob_year")];
    if ( "no" == $(this).val() ) {
      $.each(fields, function(k,v){
        v.attr('data-required', 'true').show();
        v.siblings('label').show();
      });
      $(".DOB").after('<br class="postDOB" /><br class="postDOB" /><br class="postDOB" />');
    } else {
      $.each(fields, function(k,v){
        v.removeData('required').removeAttr('data-required').hide();
        v.siblings('label').hide();
      });
      $(".postDOB").remove();
    }
    enableDisableCart();
  });
  // TODO: Auto format Birthday
  // Enables/disables cart button based on fields being filled out
  function enableDisableCart() {
    var fieldsOK = true;
    var fields = $("input[name^=wc_trip_], select[name^=wc_trip_]");
    $.each(fields, function(key, value){
      if ( "radio" == $(value).attr("type") ) {
        var name = $(value).attr("name");
        var radio = $("input[name=" + name + "]");
        if ( true === radio.data("required") && !radio.is(":checked") ) {
          fieldsOK = false;
          return false;
        }
      }
      
      if ( "radio" !== $(value).attr("type") && "" === $(value).val() && true === $(value).data("required")) {
        fieldsOK = false;
        return false;
      }
    });
    if ( fieldsOK ) {
      $(".single_add_to_cart_button").prop("disabled", "");
    } else {
      $(".single_add_to_cart_button").prop("disabled", "disabled");
    }
  }
});