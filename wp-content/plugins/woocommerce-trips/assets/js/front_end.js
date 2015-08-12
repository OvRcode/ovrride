jQuery(document).ready(function($){
  // check for present fields on load
  fieldSetup($);
  // disabled add to cart button on load
  enableDisableCart($);
  
  // validate fields when values change
  $("#wc-trips-form input, select").on("change", function(){
    enableDisableCart($);
  });
  
  // TODO: Auto format Birthday
});
// Sets up array of fields present on page
function fieldSetup($) {
  var potentialFields = ["first", "last", "email", "phone","primary_package", 
  "secondary_package", "tertiary_package", "passport_num", "passport_country", "dob"];
  if ( ! $.isArray(window.fields) ) {
    window.fields = [];
  }
  $.each( potentialFields, function( key, value ) {
    if ( $("label[for='wc_trip_" + value + "']").length > 0 ) {
      window.fields.push(value);
    }
  });
}
// Checks that a field has a non blank value
// TODO: Field validation
function checkField( $, label) {
  if ( "" !== $("input[name='wc_trip_" + label + "']").val() ) {
    return true;
  } else {
    return false;
  }
}
// Enables/disables cart button based on fields being filled out
function enableDisableCart($) {
  var fieldsOK = true;
  $.each(window.fields, function(key, value){
    var testField = checkField($, value);
    if ( ! testField ) {
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