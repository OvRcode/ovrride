jQuery(document).ready(function($){
  // show/hide flight times tab based on trip type selection
  showHideTabs();
  $("select[name=_wc_trip_type]").on("change", showHideTabs);

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
      } else if ( $(this).prop("id") == "package_add") {
        $("table.woocommerce_trip_packages").append( $(this).data( 'row' ) );
      } else if ( "to_beach_add" == $(this).prop("id") ) {
        $("table.woocommerce_trip_to_beach").append( $(this).data( 'row' ) );
      } else if ( "from_beach_add" == $(this).prop("id") ) {
        $("table.woocommerce_trip_from_beach").append( $(this).data( 'row' ) );
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
	$('body').on('click', 'td.deleteButton', function(){
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
  function showHideTabs() {
    var tabStatus = {
      bus: {
        trips_pickup_location: true,
        trips_primary_packages_tab: true,
        trips_secondary_packages_tab: true,
        trips_tertiary_packages_tab: true,
        trips_includes_tab: true,
        trips_rates_tab: true,
        trips_routes_tab: false,
        trips_partners_tab: false,
        trips_flight_times_tab: false,
        trips_pics_tab: true,
        trips_package_tab: false,
        trips_to_beach_tab: false,
        trips_from_beach_tab: false
      },
      beach_bus: {
        trips_pickup_location: false,
        trips_primary_packages_tab: false,
        trips_secondary_packages_tab: false,
        trips_tertiary_packages_tab: false,
        trips_includes_tab: true,
        trips_rates_tab: true,
        trips_routes_tab: true,
        trips_partners_tab: true,
        trips_flight_times_tab: false,
        trips_pics_tab: true,
        trips_package_tab: true,
        trips_to_beach_tab: true,
        trips_from_beach_tab: true
      },
      international_flight: {
        trips_pickup_location: false,
        trips_primary_packages_tab: true,
        trips_secondary_packages_tab: true,
        trips_tertiary_packages_tab: true,
        trips_includes_tab: true,
        trips_rates_tab: true,
        trips_routes_tab: false,
        trips_partners_tab: false,
        trips_flight_times_tab: true,
        trips_pics_tab: true,
        trips_package_tab: false,
        trips_to_beach_tab: false,
        trips_from_beach_tab: false
      },
      domestic_flight: {
        trips_pickup_location: false,
        trips_primary_packages_tab: true,
        trips_secondary_packages_tab: true,
        trips_tertiary_packages_tab: true,
        trips_includes_tab: true,
        trips_rates_tab: true,
        trips_routes_tab: false,
        trips_partners_tab: false,
        trips_flight_times_tab: true,
        trips_pics_tab: true,
        trips_package_tab: false,
        trips_to_beach_tab: false,
        trips_from_beach_tab: false
      }
    };
    // Loops through tabs for selected trip sub-type and shows or hides tabs
    $.each(tabStatus[$("select[name=_wc_trip_type] :selected").val()],
    function(index, value){
        if ( value ) {
          $("." + index).show();

        } else {
          $("." + index).hide();
        }
    });
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
      cssElement.show();
    } else {
      cssElement.hide();
    }
}
function pickup_row_indexes() {
  jQuery('.woocommerce_trip_pickup_locations .woocommerce_trip_pickup_location').each(function(index, el){
    jQuery('.pickup_location_menu_order', el).val( parseInt( jQuery(el).index('.woocommerce_trip_pickup_locations .woocommerce_trip_pickup_location') ) );
  });
}
