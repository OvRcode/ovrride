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