jQuery(document).ready(function($){
  // Set price on page
  var base_price = Number($("#base_price").val());
  base_price = base_price.toFixed(2);
  $("#trip_price").text("$" + base_price);
  
  // disabled add to cart button on load
  enableDisableCart();
  
  // validate fields when values change
  $("#wc-trips-form select").on("change", function(){
    enableDisableCart();
  });
  
  $("#wc-trips-form input").on("keyup", function(){
    enableDisableCart();
  });
  $("input[name=wc_trip_email]").verimail({
    messageElement: "p#emailValidation"
  });
  $("input[name=wc_trip_email]").on("change", function(){
    var status = $("input[name=wc_trip_email]").getVerimailStatus();
    if ( "success" !== status ) {
      $("#emailValidation").show();
      $(this).css({background: '#CC0000', color: '#FFFFFF'}).focus();
    } else {
      $("#emailValidation").hide();
      $(this).css({background: '#FFFFFF', color: '#000000'});
    }
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
  
  $("input[name^=wc_trip_dob_]").on("keyup", function(){
    var month = $("input[name=wc_trip_dob_month]").val();
    var day = $("input[name=wc_trip_dob_day]").val();
    var year = $("input[name=wc_trip_dob_year]").val();
    $("input[name=wc_trip_dob]").val(month + "/" + day + "/" + year);
  });
  
  $("#wc_trip_primary_package, #wc_trip_secondary_package, #wc_trip_tertiary_package").on("change", function(){
    var base      = Number($("#base_price").val()) || 0;
    var primary   = Number( $("#wc_trip_primary_package :selected").data('cost') ) || 0;
    var secondary = Number( $("#wc_trip_secondary_package :selected").data('cost') ) || 0;
    var tertiary  = Number( $("#wc_trip_tertiary_package :selected").data('cost') ) || 0;
    var total = base + primary + secondary + tertiary;
    
    $("#trip_price").text( "$" + total.toFixed(2) );
  });
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