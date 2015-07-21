jQuery(document).ready(function($){
  // General Tab date validator
  $( "#_wc_trip_start_date, #_wc_trip_end_date" ).datepicker({
      changeMonth: true,
      changeYear: true
  });
  $("#_wc_trip_base_price").change(function() {
      var valid = /^\d{0,4}(\.\d{0,2})?$/.test(this.value),
      val = this.value;
  
  if(!valid){
      alert("Please enter a valid price");
  }
  });
  $( "#_wc_trip_end_date").change(function() {
      var start = $("#_wc_trip_start_date");
      var end = $(this);
      if ( end.val() < start.val() ) {
          end.val("");
          end.focus();
          alert("Please set an end date that is greater than or equal to the start date");
      }
  });
  
  // Primary package add button
  showHideStock("primary");
  $("#_wc_trip_primary_package_stock").change(function(){
      showHideStock("primary");
  });
  $("#_wc_trip_secondary_package_stock").change(function(){
      showHideStock("secondary");
  });
  jQuery( '.add_package' ).click(function(){
      if ( $(this).prop("id") == "primary_package_add" ) {
        $("table.woocommerce_trip_primary_packages").append( $(this).data( 'row' ) );
        showHideStock("primary");
      } else if ( $(this).prop("id") == "secondary_package_add" ) {
        $("table.woocommerce_trip_secondary_packages").append( $(this).data( 'row' ) );
        showHideStock("secondary");
      }
  });
});

function showHideStock( StockType ) {
    var selector = "";
    var cssElement = "";
    switch( StockType ) {
        case "primary":
            selector = jQuery("#_wc_trip_primary_package_stock");
            cssElement = jQuery(".primary_package_stock");
            break;
        case "secondary":
            selector = jQuery("#_wc_trip_secondary_package_stock");
            cssElement = jQuery(".secondary_package_stock");
            break;
    }
    if ( selector.prop("checked") ) {
        cssElement.css("display", "block");
    } else {
        cssElement.css("display", "none");
    }
}