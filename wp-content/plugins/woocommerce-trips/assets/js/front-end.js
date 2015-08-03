jQuery(document).ready(function($){
  window.fields = ["first", "last", "email", "phone"];
  if ( $("label[for='wc_trip_primary_package']").length > 0 ) {
    window.fields.push("primary_package");
  }
  if ( $("label[for='wc_trip_secondary_package']").length > 0 ) {
    window.fields.push("secondary_package");
  }
  if ( $("label[for='wc_trip_tertiary_package']").length > 0 ) {
    window.fields.push("tertiary_package");
  }
  switch( $("#wc_trip_type").val() ) {
    case "domestic_flight":
      window.fields.push("dob");
      break;
    case "international_flight":
      window.fields.push("passport_num");
      window.fields.push("passport_country");
      break;
  }
  $("#wc-trips-form input select").on("change", function(){

  });
}