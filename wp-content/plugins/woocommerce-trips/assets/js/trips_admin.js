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
  
  // Package stock checkboxes
  showHideStock("primary");
  $("#_wc_trip_primary_package_stock").change(function(){
      showHideStock("primary");
  });
  $("#_wc_trip_secondary_package_stock").change(function(){
      showHideStock("secondary");
  });
  
  // Add package buttons
  $( '.add_package' ).click(function(){
      if ( $(this).prop("id") == "primary_package_add" ) {
        $("table.woocommerce_trip_primary_packages").append( $(this).data( 'row' ) );
        showHideStock("primary");
      } 
      else if ( $(this).prop("id") == "secondary_package_add" ) {
        $("table.woocommerce_trip_secondary_packages").append( $(this).data( 'row' ) );
        showHideStock("secondary");
      }
      
      if ( $(".sorter:visible").size() > 1 ) {
        $(".sorter:visible").css("visibility","visible");
      }
  });
  
  // Remove package rows
	$('body').on('click', 'td.delete', function(){
		$(this).closest('tr').remove();
    if ( $(".sorter:visible").size() <= 1 ) {
      $(".sorter:visible").css("visibility","hidden");
    }
		return false;
	});
  
  // Sorting for packages
  $( "#primary_package_rows" ).sortable({
    items: 'tr',
		cursor:'move',
		axis:'y',
		handle: '.sorter',
		scrollSensitivity:40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start:function(event,ui){
			ui.item.css('background-color','#f6f6f6');
		},
		stop:function(event,ui){
			ui.item.removeAttr('style');
		}
	});
  // Hide sort button if there is only one row
  if ( $(".sorter:visible").size() <= 1 ) {
    $(".sorter:visible").css("visibility", "hidden");
  }
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
        cssElement.css("visibility", "visible");
        jQuery(".sorting").css("width", "2%");
        jQuery(".description").css("width", "54%");
        jQuery(".cost").css("width", "22%");
        jQuery(".primary_package_stock, .secondary_package_stock").css("width", "20%");
        jQuery(".delete_column").css("width", "2%");
    } else {
        cssElement.css("visibility", "collapse");
        jQuery(".sorting").css("width", "2%");
        jQuery(".description").css("width", "59%");
        jQuery(".cost").css("width", "27%");
        jQuery(".primary_package_stock, .secondary_package_stock").css("width", "0");
        jQuery(".delete_column").css("width", "2%");
    }
}