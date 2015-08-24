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
  // TODO: Auto format Birthday
  // Enables/disables cart button based on fields being filled out
  function enableDisableCart() {
    var fieldsOK = true;
    var fields = $("input[name^=wc_trip_], select[name^=wc_trip_]");
    $.each(fields, function(key, value){
      if ( "" === $(value).val() && true === $(value).data("required")) {
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