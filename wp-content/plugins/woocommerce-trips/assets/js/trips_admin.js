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
  showHideStock("secondary");
  showHideStock("tertiary");
  $("#_wc_trip_primary_package_stock").change(function(){
      showHideStock("primary");
  });
  $("#_wc_trip_secondary_package_stock").change(function(){
      showHideStock("secondary");
  });
  $("#_wc_trip_tertiary_package_stock").change(function(){
      showHideStock("tertiary");
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
      else if ( $(this).prop("id") == "tertiary_package_add" ) {
        $("table.woocommerce_trip_tertiary_packages").append( $(this).data( 'row' ) );
        showHideStock("tertiary");
      }
      
      if ( $(".sorter:visible").size() > 1 ) {
        $(".sorter:visible").css("visibility","visible");
      }
  });
  // Add a pickup location
  $( 'button.add_pickup').on('click', function(){
    $(".woocommerce_trip_pickup_locations").block( { message: null, overlayCSS: { background: '#ffffff url(' + wc_trips_admin_js_params.plugin_url + '/assets/images/select2-spinner.gif) no-repeat center', opacity: 0.6} } );
    var pickupCount = $( ".woocommerce_trip_pickup_location" ).size();
    var new_pickup_id = $( ".add_pickup_location_id" ).val();
    var new_pickup_name = '';
    
    if ( ! new_pickup_id ) {
      new_pickup_name = prompt( 'New Pickup Location Name: ' );
      
      if ( ! new_pickup_name ) {
        return false;
      }
    }
    
    var post_data = {
      action: 'woocommerce_add_pickup_location',
      post_id: wc_trips_admin_js_params.post,
      pickupCount: pickupCount,
      new_pickup_id: new_pickup_id,
      new_pickup_name: new_pickup_name,
      nonce: wc_trips_admin_js_params.nonce_add_pickup_location
    };
    
    $.post( wc_trips_admin_js_params.ajax_url, post_data, function(response){
      if ( response.error ) {
        alert(response.error);
        $(".woocommerce_trip_pickup_locations").unblock();
      } else {
        $( ".woocommerce_trip_pickup_locations" ).append( response.html ).unblock();
        $( ".woocommerce_trip_pickup_locations" ).sortable( pickup_sortable_options );
        if ( new_pickup_id ) {
          $('.add_pickup_location_id option[value=' + new_pickup_id + ']').remove();
        }
      }
    });
    
    return false;
  });
  // Remove a pickup location
  $( 'button.remove_trip_pickup_location' ).on( 'click', function(e){
    e.preventDefault();
    
    var checkRemove = confirm( "Are you sure you want to remove this location?" );
    var entry = $(this).parent().parent();
    if ( checkRemove ) {
      $( entry ).block( { message: null, overlayCSS: { background: '#ffffff url(' + wc_trips_admin_js_params.plugin_url + '/assets/images/select2-spinner.gif) no-repeat center', opacity: 0.6} } );
      var appendHTML = '<option value="' + $(this).attr("rel") + '">' + $(this).siblings('strong').text() + '</option>';
      var removeData = {
        action: 'woocommerce_remove_pickup_location',
        post_id: wc_trips_admin_js_params.post,
        location_id: $(this).attr('rel'),
        nonce: wc_trips_admin_js_params.nonce_remove_pickup_location
      };
      
      $.post( wc_trips_admin_js_params.ajax_url, removeData, function(response){
        if ( response.removed ) {
          $( ".add_pickup_location_id" ).append( appendHTML );
          $( entry ).fadeOut( '400', function(){
          $( entry ).remove();
          });
        } else {
          $( entry ).unblock();
          if ( response.error ) {
            alert(response.error);
          }
        }
      });
    }
    return false;
  });
  // Remove package rows
	$('body').on('click', 'td.delete', function(){
		$(this).closest('tr').remove();
    if ( $(".sorter:visible").size() <= 1 ) {
      $(".sorter:visible").css("visibility","hidden");
    }
		return false;
	});
  // Sorting for pickups
	var pickup_sortable_options = {
		items: '.woocommerce_trip_pickup_location',
		cursor: 'move',
		axis: 'y',
		handle: 'h3',
		scrollSensitivity: 40,
		forcePlaceholderSize: true,
		helper: 'clone',
		opacity: 0.65,
		placeholder: 'wc-metabox-sortable-placeholder',
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function ( event, ui ) {
			ui.item.removeAttr( 'style' );
			pickup_row_indexes();
		}
	};

	$( '.woocommerce_trip_pickup_locations' ).sortable( pickup_sortable_options );
  // Sorting for packages
  $( "#primary_package_rows, #secondary_package_rows, #tertiary_package_rows" ).sortable({
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
        case "tertiary":
            selector = jQuery("#_wc_trip_tertiary_package_stock");
            cssElement = jQuery(".tertiary_package_stock");
    }
    if ( selector.prop("checked") ) {
        cssElement.css("visibility", "visible");
        jQuery(".sorting").css("width", "2%");
        jQuery(".description").css("width", "54%");
        jQuery(".cost").css("width", "22%");
        jQuery(".primary_package_stock, .secondary_package_stock, .tertiary_package_stock").css("width", "20%");
        jQuery(".delete_column").css("width", "2%");
    } else {
        cssElement.css("visibility", "collapse");
        jQuery(".sorting").css("width", "2%");
        jQuery(".description").css("width", "59%");
        jQuery(".cost").css("width", "27%");
        jQuery(".primary_package_stock, .secondary_package_stock, .tertiary_package_stock").css("width", "0");
        jQuery(".delete_column").css("width", "2%");
    }
}
function pickup_row_indexes() {
  jQuery('.woocommerce_trip_pickup_locations .woocommerce_trip_pickup_location').each(function(index, el){
    jQuery('.pickup_location_menu_order', el).val( parseInt( jQuery(el).index('.woocommerce_trip_pickup_locations .woocommerce_trip_pickup_location') ) );
  });
}