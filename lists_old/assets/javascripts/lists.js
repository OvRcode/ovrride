/**
*  OvR Lists - Custom JavaScript
*
*/
// iOS click lag workaround
(function( $ ) {
  $.fn.noClickDelay = function() {
    var $wrapper = this;
    var $target = this;
    var moved = false;
    $wrapper.bind('touchstart mousedown',function(e) {
      e.preventDefault();
      moved = false;
      $target = $(e.target);
      if ($target.nodeType == 3) {
        $target = $($target.parent());
      }
      $target.addClass('pressed');
      $wrapper.bind('touchmove mousemove',function(e) {
      moved = true;
      $target.removeClass('pressed');
    });
    $wrapper.bind('touchend mouseup',function(e) {
    $wrapper.unbind('mousemove touchmove');
    $wrapper.unbind('mouseup touchend');
    if(!moved && $target.length) {
      $target.removeClass('pressed');
      $target.trigger('click');
      $target.focus();
    }
  });
});
};
})( jQuery );
// end iOS click lag workaround
// Order Status: Check All / Uncheck All
function checkAll(status) {
  // reset checked attr status either way
  $('.order_status_checkbox').removeAttr('checked');

  if (status == 'check'){
    $('.order_status_checkbox').prop('checked', true);
  }
}
function formReset(){
  var clearForm = confirm("You are about to clear the form and locally saved data. Ok?");
  if ( clearForm ) {
    $("#Listable").trigger("destroy");
    $('#Listable').remove();
    $('#footer').css('position','absolute');
    $('#locationContainer').remove();
    $('#itemContainer').remove();
    $('.pager').css('visibility','hidden');
    $('#destination').val('none');
    $('#destination').trigger('change');
    checkAll('uncheck');
    $('input[name=pending]').click();
    $('input[name=processing]').click();
    $('input[name=walk-on]').click();
    $('input[name=completed]').click();
    window.storage.set('unsaved', false);
    window.storage.remove('orderData');
    window.storage.remove('destination');
    window.storage.remove('trip');
    window.storage.remove('tablesorter-pager');
  }
}
function generateOnOff(){
  window.fieldTotals = {};
  // switch generate list button between online and offline mode
  $('#locationContainer').remove();
  $('#itemContainer').remove();
  $('#Listable').remove();
  $('#footer').css('position','absolute');
  $('.pager').css("visiblity", "hidden");
  if (window.navigator.onLine){
    $('#loader').css('display','inline');
    $('#trip').getData();
  } else {
    $('#loader').css('display','inline');
    setTimeout(function(){
      setupDropDowns();
      $('#save').css('visibility','hidden');
      $('#csv_list').css('visibility','hidden');
      $('#csv_email').css('visibility','hidden');
      console.log('Offline Loading data:');
      window.orderData = window.storage.get('orderData');

      $('#listTable').buildTable();
    },200);
  }
}
function postData(){
  // send data to backend mySQL database
  $('#saveBar').css('width', '80%');
  var jqxhr = $.post( "save.php", {'data' : window.storage.get('orderData') } ,function() {})
    .done(function() {
      $('#saveBar').css('width', '100%');
      setTimeout(function(){
        $('#saveProgress').remove();
        $('#mainBody').append('<div id="success" class="alert alert-success alert-dismissable">' +
                              '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                              'Successful Save</div>');
        setTimeout(function(){ 
          $('#success').remove();
        }, 5000);
        window.storage.set('unsaved', false);
      });
    })
    .fail(function() {
      console.log("POST request error");
      $('#mainBody').append('<div id="success" class="alert alert-danger alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                            'Something went wrong, try saving again</div>');
                            $('.close').on('click',function(){
                              $('#saveProgress').remove();
                            });
    });
}
function setupProgressBar(){
  $('#mainBody').append('<div id="saveProgress"><h3>Save Progress:</h3>' +
    '<div class="progress progress-striped active">' +
    '<div id="saveBar" class="progress-bar"  role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100" style="width: 1%">' +
      '<span class="sr-only">1% Complete</span>' +
    '</div>' +
  '</div>' +
    '</div>');
}
$('#save').click(function(){
  setupProgressBar();
  $('#saveBar').css('width', '10%');

  if(window.navigator.onLine){
    $('#saveBar').css('width', '50%');
    postData();
  } else {
    $('#mainBody').append('<div id="success" class="alert alert-warning alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                            'Changes made have already been saved locally, try again when you\'re online</div>');
    $('#saveBar').css('width', '100%');
    setTimeout(function(){
      $('#saveProgress').remove();
    },2000);
  }
});
$.fn.getData = function(){
  var jqxhr = $.post("/pull.php", {'requestType':'orders','trip' : $(this).val()})
  .done(function(data){
    window.orderData = data;
    window.storage.set('orderData',orderData);
    $("#listTable").buildTable();
  })
  .fail(function(error){
    console.log('Error getting data:' + error.message);
  });
};
function setLocalTrip(){
  window.storage.set('trip', $('#trip').val());
  window.storage.set('destination', $('#destination').val());
}
function setTrip(){
  $('#destination').val(window.storage.get('destination'));
  // Need to trigger change for chained plugin to show trips
  $('#destination').trigger('change');
  $('#trip').val(window.storage.get('trip'));

}
function setupTablesorter(rows) {
    var pagerOptions = {
    container: $('.pager'),
    output: '{startRow} - {endRow} / {filteredRows} ({totalRows})' 
    };
    var headerOptions = {
      0:  { sorter: 'text' },
      1:  { sorter: 'checkbox' },
      2:  { sorter: 'text' },
      3:  { sorter: 'text' },
      4:  { sorter: 'text' },
      5:  { sorter: 'digit' },
      6:  { sorter: 'text' },
      7:  { sorter: 'digit' },
      8:  {sorter: 'text'}, 
      9:  { sorter: 'text' },
      10: { sorter: false }
    };
    var filterOptions = {
        4 : true,
        6 : true 
      };
    var widgetOptions = {
      editable_columns       : '2-6',  // point to the columns to make editable (zero-based index)
      editable_enterToAccept : true,     // press enter to accept content, or click outside if false
      editable_autoResort    : false,    // auto resort after the content has changed.
      editable_noEdit        : 'no-edit', // class name of cell that is no editable
      stickyHeaders_offset: 50,
      filter_childRows : true,
      filter_columnFilters : true,
      filter_hideFilters : true,
      filter_ignoreCase : true,
      filter_reset : '.reset',
      filter_searchDelay : 0,
      filter_functions : filterOptions
    };
    // Modify options for tables with no pickup column 
    if (rows == 10) {
      headerOptions[4].sorter = 'digit';
      headerOptions[5].sorter = 'text';
      headerOptions[6].sorter = 'digit';
      headerOptions[7].sorter = 'text';
      widgetOptions.editable_columns = '2-5';
      delete filterOptions[4];
      delete filterOptions[6];
      filterOptions[5] = true;
    } else if ( rows == 12 ) {
      headerOptions[4].sorter = 'digit';
      headerOptions[5].sorter = 'text';
      headerOptions[6].sorter = 'text';
      headerOptions[7].sorter = 'text';
      headerOptions[8].sorter = 'digit';
      headerOptions[9].sorter = 'text';
      headerOptions[10].sorter = 'text';
      delete filterOptions[4];
      filterOptions[5] = true;
      filterOptions[6] = true;
      filterOptions[7] = true;
    }

    var tablesorterOptions = {
      delayInit: false,
      widthFixed: true,
      ignoreCase: true,
      removeRows: true,
      sortList: [[4,0],[3,0]],
      headers: headerOptions,
      widgets : [ 'editable', 'columns','filter' ],
      widgetOptions: widgetOptions,
    };
    if (rows > 0) {
      $('#Listable').tablesorter(tablesorterOptions).tablesorterPager(pagerOptions);
      $('.pager').css('visibility','visible');
    }
}
function setupDropDowns(){
  if (window.navigator.onLine) {
    var dropDown = {};
    dropDown.destinations = {};
    dropDown.trips = {};
    console.log("dropdown setup started");
    var jqxhr = $.post('/pull.php', {'requestType':'dropdowns'})
    .done(function(data){
      var destinations = '';
      var trips = '';
      $.each(data.destinations, function(key,value){
        destinations += '<option class="' + value + '" value="' + value + '">'+ value + '</option>';
        dropDown.destinations[value] = value;
      });
      $('#destination', '#mainBody').append(destinations);
      $.each(data.trip, function(classType,value){
        $.each(value, function(tripId, tripLabel){
          trips += '<option class="' + classType + '" value="' + tripId + '">' + tripLabel + '</option>';
          if ( typeof dropDown.trips[classType] == 'undefined' ) {
            dropDown.trips[classType] = {};
          }
          dropDown.trips[classType][tripId] = tripLabel;
        });
      });
      window.storage.set('dropDown',dropDown);
      $('#trip', '#mainBody').append(trips);
      $("#trip").chained("#destination");
    });
  } else {
    var localDropdown = window.storage.get('dropDown');
    var localTrips = '';
    var localDestinations = '';
    $.each(localDropdown.destinations, function(destination){
      localDestinations += '<option class="' + destination + '" value="' + destination + '">' + destination + '</option>\n';
    });
    $('#destination').append(localDestinations);
    $.each(localDropdown.trips, function(tripClass, data){
      $.each(data, function(tripId, tripLabel){
          localTrips += '<option class="' + tripClass + '" value="' + tripId + '">'+tripLabel+'</option>\n';
      });

    });
    $('#trip').append(localTrips);
    setTimeout(function(){
      $("#trip").chained("#destination");
    },200);
  }

}
$.fn.colCount = function() {
   var colCount = 0;
   $('thead tr:nth-child(1) td', this).each(function () {
       if ($(this).attr('colspan')) {
           colCount += +$(this).attr('colspan');
       } else {
           colCount++;
       }
   });
   return colCount;
};
function define(objectName,propertyName) {
  if ( typeof objectName[propertyName] == 'undefined' ) {
    objectName[propertyName] = 0;
  }
}
function addOrSubtract(objectName, propertyName, addSubtract) {
  if ( addSubtract == 'add' ) {
    objectName[propertyName]++;
  } else if ( addSubtract == 'subtract' ) {
    objectName[propertyName] = Math.max(0,--objectName[propertyName]);
  }
}
function parsePackage(packageText, addSubtract, outputType) {
  var bus           = new RegExp(/bus only/i);
  var begLiftLesson = new RegExp(/beginner lift.*lesson$/i);
  var allArea       = new RegExp(/all area/i);
  var weekendLift   = new RegExp(/^lift/i);
  var weekendLift2  = new RegExp(/Balance \(lift/i);
  var ltr           = new RegExp(/beginner lift area.*bus.*lesson.*board/i);
  var lts           = new RegExp(/beginner lift area.*bus.*lesson.*ski/i);
  var progLesson    = new RegExp(/prog.* lesson/i);
  var ski           = new RegExp(/ski rental/i);
  var brd           = new RegExp(/board rental/i);
  var lunch         = new RegExp(/.*lunch.*/i);
  // Beach / Waterpark Specific packages
  var allMountainCoaster = new RegExp(/mountain coaster/i);
  var waterPark          = new RegExp(/all area waterpark/i);
  var oneWay             = new RegExp(/one way bus/i);
  var roundTrip          = new RegExp(/round trip bus/i);
  var beachDay           = new RegExp(/day at the beach package/i);
  var beachSurf          = new RegExp(/surf lesson/i);
  var output;
  if ( outputType == 'List' ) {
    output = window.fieldTotals;
  } else if ( outputType == 'CSV' ) {
    output = window.csvValue;
  }
  if ( typeof output == 'undefined' ) {
    output = {};
  }
  // Check summer packages then bus/Lift options
  if ( beachSurf.test(packageText) ) {
    define(output,"Surf Lesson");
    addOrSubtract(output, "Surf Lesson", addSubtract);
  }
  else if ( beachDay.test(packageText) ) {
    define(output, "Day at the beach");
    addOrSubtract(output, "Day at the beach", addSubtract);
  }
  else if ( oneWay.test(packageText) ) {
    define(output, "One way bus");
    addOrSubtract(output, "One way bus", addSubtract);
  }
  else if ( roundTrip.test(packageText) ) {
    define(output, "Round Trip Bus");
    addOrSubtract(output, "Round Trip Bus", addSubtract);
  }
  else if ( waterPark.test(packageText) ) {
    define(output, "All Area Waterpark");
    addOrSubtract(output, "All Area Waterpark", addSubtract);
  }
  else if ( allMountainCoaster.test(packageText) ) {
    define(output, "Waterpark & Mountain Coaster");
    addOrSubtract(output, "Waterpark & Mountain Coaster", addSubtract);
  }
  else if ( bus.test(packageText) ) {
    define(output, "Bus Only");
    addOrSubtract(output, "Bus Only", addSubtract);
  } else if ( begLiftLesson.test(packageText) ) {
    define(output, "Beginner Lift and Lesson");
    addOrSubtract(output, "Beginner Lift and Lesson", addSubtract);
  } else if ( allArea.test(packageText) ) {
    define(output, "All Area Lift");
    addOrSubtract(output, "All Area Lift", addSubtract);
  } else if ( weekendLift.test(packageText) || weekendLift2.test(packageText) ) {
    define(output, "Weekend Lift");
    addOrSubtract(output, "Weekend Lift", addSubtract);
  }
  // Check rentals and lessons
  if ( ltr.test(packageText) ) {
    define(output, "Learn to Ride");
    addOrSubtract(output, "Learn to Ride", addSubtract);
  } else if ( lts.test(packageText) ) {
    define(output,"Learn to Ski");
    addOrSubtract(output, "Learn to Ski", addSubtract);
  } else if ( progLesson.test(packageText) ) {
    define(output, "Progressive Lesson");
    addOrSubtract(output, "Progressive Lesson", addSubtract);
  }
  
  if ( ski.test(packageText) && !lts.test(packageText) ) {
    define(output, "Ski Rental");
    addOrSubtract(output, "Ski Rental", addSubtract);
  } else if ( brd.test(packageText) && !ltr.test(packageText) ) {
    define(output, "Board Rental");
    addOrSubtract(output, "Board Rental", addSubtract);
  }
  // REI Lunch Vouchers
  if ( lunch.test(packageText) ) {
    define(output, "Lunch Voucher");
    addOrSubtract(output, "Lunch Voucher", addSubtract);
  }
}
$.fn.buildTable = function(){
  var hasPickup = '';
  var orderData   = window.orderData;
  var tableHeader = '';
  var tableBody   = '';
  var tableFooter = '';
  var localtionTable = '';
  var itemTable = '';
  var byLocation  = {};
  var statusBoxes = {};
  var manualEvents = [];
  $('.order_status_checkbox').each(function(){
    statusBoxes[$(this).attr('name')] = $(this).is(':checked');
  });
  $('#Listable').remove();
  $('#footer').css('position','absolute');
  if (window.navigator.onLine) {
    // ONLINE

    if (jQuery.isEmptyObject(orderData)) {
      $(this).append('<div class="container" id="Listable"><p>There are no orders for the selected Trip and Order Status.</p></div>');
      $('#loader').css('display','none');
      throw new Error('Aborted table creation, no data here');
    }
  }

  var events = [];
  $.each(orderData, function(orderNumber, values){
    var prefix = orderNumber.substring(0,2);
    $.each(values, function(orderItemNumber, fields){
      var id = orderNumber+":"+orderItemNumber;
      var row = {};

      if (statusBoxes[fields.Status] === true) {
        $.each(fields, function(field, value){
          if ( field == 'Pickup' && value == "No Pickup" ) {
            return true;//skip this loop;
          }
          if ( field == 'First' || field == 'Last' || field == 'Pickup' || field == 'Phone' || 
             field == 'Package' || field == 'Order' || field == 'Transit To Rockaway' || 
             field == 'Transit From Rockaway') {
            row[field] = '<td id="' + id + ':' + field +'"';
            switch (field) {
              case 'First':
                row[field] += ' headers="First"';
                break;
              case 'Last':
                row[field] += ' headers="Last"';
                break;
              case 'Pickup':
                row[field] += ' headers="Pickup"';
                hasPickup = true;
                break;
              case 'Phone':
                row[field] += ' headers="Phone"';
                break;
              case 'Package':
                row[field] += ' headers="Package"';
                break;
              case 'Order':
                row[field] += 'headers="Order"';
                break;
              case 'Transit To Rockaway':
                row[field] += 'headers="To Rockaway"';
                break;
              case 'Transit From Rockaway':
                row[field] += 'headers="From Rockaway"';
                break;
            }
            if (prefix != 'WO') {
              row[field] += ' class="no-edit"';
            } else {
              row[field] += ' class="saved"';
            }
            // Add phone links, were not being auto detected on mobile platforms
            if ( field == 'Phone' ) {
              row[field] += '><a href="tel://'+ value + '">' + value + '</a></td>';
              if ( prefix == 'WO' ) {
                manualEvents.push("#"+orderNumber+"\\:"+orderItemNumber);
              }
            } else {
              row[field] +='>' + value + '</td>';
            }
          } else if (field != 'Email'){
            var btnClass;
            var spanClass;
            if (value == 1) {
              btnClass = 'btn-success';
              spanClass = 'glyphicon-ok-sign';
            } else {
              btnClass= 'btn-danger';
              spanClass = 'glyphicon-minus-sign';
            }
            value = (value == 1 ? true : false);
            if ( value == 1 ) {
              value = true;
            } else {
              value = false;
            }
            row[field] = '<td class="center-me" id ="' + id + ':' + field + '"><span class="value">' + value + '</span>' +
                          '<button name ="' + id + ':' + field + '" class="btn-xs btn-default ' + btnClass + '" value="' + value + '">' +
                          '<span class="glyphicon ' + spanClass + '"></span></button></td>';

            events.push("#"+orderNumber+"\\:"+orderItemNumber+"\\:"+field);
          }
        });
        /* Had to manually assemble cells in correct order, couldn't get AM/Pm on left side of table with a loop
          this is proably a result of moving data from PHP to JSON and back to an array */
        tableBody += '<tr>'+row.AM + row.PM + row.First + row.Last;
        if (row.Pickup) {
          tableBody += row.Pickup;
        }

        tableBody += row.Phone + row.Package ;
        if ( row["Transit To Rockaway"] ) {
          tableBody += row["Transit To Rockaway"];
        }
        if ( row["Transit From Rockaway"] ) {
          tableBody += row["Transit From Rockaway"];
        }
        if (prefix == 'WO') {
          tableBody += '<td>' + orderNumber + '</td>';
        } else {
          tableBody += '<td><a href="https://ovrride.com/wp-admin/post.php?post=' + orderNumber +'&action=edit" target="_blank">' + orderNumber+ '</a></td>';
        }

        tableBody += row.Waiver + row.Product;
        if ( prefix == 'WO') {
          tableBody += '<td class="center-me" id="' + id +'"><button class="btn-xs btn-warning" >' +
            '<span class="glyphicon glyphicon-remove"></span></button></td>';
        } else {
          tableBody += '<td></td></tr>';
        }
        //set itemTable values
        if ( findButtonValueString(row.AM) == "true" ) {
          parsePackage(row.Package,'add', 'List');
        } else {
          parsePackage(row.Package, 'setup', 'List');
        }

        if (row.Pickup || row["Transit To Rockaway"]) {
          var locationName;
          if ( row.Pickup )
            locationName = row.Pickup.replace(/<(?:.|\n)*?>/gm, '');
          else
            locationName = row["Transit To Rockaway"].replace(/<(?:.|\n)*?>/gm, '');
          if ( typeof byLocation[locationName] === 'undefined' ) {
            byLocation[locationName] = {};
            byLocation[locationName].Expected = 0;
            byLocation[locationName].AM = 0;
            byLocation[locationName].PM = 0;
          }
          byLocation[locationName].Expected++;
          if ( findButtonValueString(row.AM) == "true" ) {
            byLocation[locationName].AM++;
          }
          if ( findButtonValueString(row.PM) == "true") {
            byLocation[locationName].PM++;
          }
        }
      }
    });
  });
  // Assemble table from data
  tableHeader += '<table id="Listable" class="tablesorter table table-bordered table-striped table-condensed">\n' +
                    '<thead>' +
                    '<tr class="tablesorter-headerRow">\n' +
                    '<td class="filter-false">AM</td>' +
                    '<td class="filter-false">PM</td>' +
                    '<td>First</td>' +
                    '<td>Last</td>';

  if (hasPickup == 1) {
    tableHeader += '<td data-placeholder="Location">Pickup</td>';
  }

  tableHeader += '<td>Phone</td><td data-placeholder="Package">Package</td>';
  if ( $("#destination").val() == "Rockaway Beach" ) {
    tableHeader += '<td data-placeholder="To Rockaway">To Rockaway</td>' +
                   '<td data-placeholder="From Rockaway">From Rockaway</td>';
  }
  tableHeader += '<td>Order</td>' +
                '<td class="filter-false">Waiver</td>' +
                '<td class="filter-false">Product REC.</td>' +
                '<td class="filter-false"></td></tr>' +
                '</thead>\n';

  tableBody += '</tbody>\n';
  tableFooter += '<tfoot>\n<tr>' +
                 '<td class="center-me"><button type="button" class="btn btn-primary" id="add">' +
                 '<span class="glyphicon glyphicon-plus"></span></button></td>' +
                 '<td class="center-me"><button type="button" class="btn btn-danger" id="remove">' +
                 '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                 '</tfoot></table>';
  if (!jQuery.isEmptyObject(byLocation)) {
    locationTable = '<div id="locationContainer" class="col col-md-4 col-md-offset-2"><h4>Riders by pickup location:</h4>';
    locationTable += '<table id="locationTable" class="table table-bordered table-striped table-condensed">\n';
    locationTable += '<thead><tr><td>Location</td><td>Expected</td><td>AM</td><td>PM</td></tr></thead>\n';
    locationTable += '<tbody>\n';
    $.each(byLocation, function(location, value){
      locationTable += '<tr><td>' + location + '</td><td>' + value.Expected + '</td><td>' + value.AM+ '</td><td>' + value.PM + '</td></tr>\n';
    });
    locationTable += '</tbody></table></div>';
    $('#totals').append(locationTable);

    itemTable =  '<div id="itemContainer" class="col col-md-4"><h4>Package item totals:</h4>\n';
    itemTable += '<table id="itemTable" class="table table-bordered table-striped table-condensed">\n';
    itemTable += '<thead><tr><td>Package Item</td><td>Count</td></tr></thead>\n<tbody>\n';
    $.each(window.fieldTotals, function(name, quantity){
      itemTable += '<tr><td>' + name + '</td><td id="' + name + '">' + quantity + '</td></tr>\n';
    });
    itemTable += '</tbody></table></div>';
    $('#totals').append(itemTable);
  }
  var output = tableHeader + tableBody + tableFooter;
  $(this).append(output);
  $('#footer').css('position','static');
  $('#loader').css('display','none');
  // click event to add row to the table for a manual order
  $('#add').click(function(){addOrder();});
  //Update view all option in pager for # of rows
  updateViewAll();

  // click event to remove unsaved manual orders from table
   $('#remove').click(function(){removeOrder();});

  $('#save').css('visibility','visible');
  if (window.navigator.onLine){
    $('#csv_list').css('visibility','visible');
    $('#csv_email').css('visibility','visible');
  }

  setupTablesorter($("#Listable").colCount());

  // update table when sorting (speeds up click lag on iOS/mobile devices )
  $('#Listable thead tr td').on("click", function(){ $('#Listable').trigger('update'); });

  setLocalTrip();

  $.each(events, function(key,value){
    addButtonListener(value);
  });
  $.each(manualEvents, function(key,value){
    $(value).on('click', function(elem){
      removeManualOrder(elem);
    });
  });
};
function findButtonValueString(searchString){
  var reg = new RegExp(/"value">([a-zA-Z]*)/);
  var result = reg.exec(searchString);
  return result[1];
}
function addButtonListener(value){
  $(value,'#Listable').on('click',function(){
    var button = $(this).children('button');
    var iconSpan = button.children('.glyphicon');
    var hiddenSpan = $(this).children('span');
    var tdId = $(this).attr('id');
    tdId = tdId.split(':');
    var order = tdId[0];
    var item = tdId[1];
    var field = tdId[2];
    var packageText;
    var action;
    var fieldValue;
    var listLocation;
    var locationRow;

    var time = (((new Date()).valueOf()).toString()).substr(0,10);

    if ( button.hasClass('btn-success')) {
      button.removeClass('btn-success').addClass('btn-danger').val('false');
      iconSpan.removeClass('glyphicon-ok-sign').addClass('glyphicon-minus-sign');
      hiddenSpan.text('false');
      orderData[order][item][field] = "0";
      action = 'subtract';
      $("#" + field).text(parseInt($("#" + field).text(), 10) - 1);
      if ( field == 'AM' ) {
        if ( $("#destination").val() == "Rockaway Beach" )
          listLocation = $("[id='"+ order +":"+ item +":Transit To Rockaway']").text();
        else
          listLocation = $("#" + order + "\\:" + item + "\\:Pickup").text();

        locationRow = $('tr','#locationTable').find('td:contains(' + listLocation + ')');
        locationRow.next().next().text(parseInt(locationRow.next().next().text(), 10) - 1);
      } else if ( field == 'PM' ) {
        listLocation = $("#" + order + "\\:" + item + "\\:Pickup").text();
        locationRow = $('tr','#locationTable').find('td:contains(' + listLocation + ')');
        locationRow.next().next().next().text(parseInt(locationRow.next().next().next().text(), 10) - 1);
      }
    } else if ( button.hasClass('btn-danger')) {
      button.removeClass('btn-danger').addClass('btn-success').val('true');
      iconSpan.removeClass('glyphicon-minus-sign').addClass('glyphicon-ok-sign');
      hiddenSpan.text('true');
      orderData[order][item][field] = "1";
      action = 'add';
      $("#" + field).text(parseInt($("#" + field).text(), 10) + 1);
      if ( field == 'AM'){
        if ( $("#destination").val() == "Rockaway Beach" )
          listLocation = $("[id='"+ order +":"+ item +":Transit To Rockaway']").text();
        else
          listLocation = $("#" + order + "\\:" + item + "\\:Pickup").text();

        locationRow = $('tr','#locationTable').find('td:contains(' + listLocation + ')');
        locationRow.next().next().text(parseInt(locationRow.next().next().text(), 10) + 1);
      } else if ( field == 'PM' ) {
        listLocation = $("#" + order + "\\:" + item + "\\:Pickup").text();
        locationRow = $('tr','#locationTable').find('td:contains(' + listLocation + ')');
        locationRow.next().next().next().text(parseInt(locationRow.next().next().next().text(), 10) + 1);
      }
    }

    if ( typeof orderData[order][item].timeStamp === undefined ) {
      console.log('Setting timeStamp field');
    }

    orderData[order][item].timeStamp = time;

    window.storage.set('orderData',window.orderData);
    window.storage.set('unsaved',true);

    if (field == 'AM') {
      packageText = $("#" + order + "\\:" + item + "\\:Package").text();
      parsePackage(packageText, action, 'List');
      updateItemTable();
    }
  });
}
function addManualListener(value){
  $(value).on('blur', function(){
    var id = $(this).attr('id');
    id = id.split(':');
    var order = id[0];
    var itemNum = id[1];
    var field = id[2];
    var time = (((new Date()).valueOf()).toString()).substr(0,10);
    var text = $(this).text();

    if ( text === '' || text === ' ' || text == 'Cannot be blank!') {
      $(this).text('Cannot be blank!').css('color','red');
    } else {
      $(this).css('color','');
      // Setup nested objects if they do not exist
      if ( typeof window.orderData[order] === "undefined" ) {
        window.orderData[order] = {};
      }
      if (typeof window.orderData[order][itemNum] === "undefined" ) {
        window.orderData[order][itemNum] = {};
      }
      if (typeof window.orderData[order][itemNum].Trip === "undefined" ) {
        window.orderData[order][itemNum].Trip = $('#trip').val();
      }
      window.orderData[order][itemNum][field] = text;
      window.orderData[order][itemNum].timeStamp = time;
      window.storage.set('orderData',window.orderData);
      window.storage.set('unsaved',true);
    }
  });
  $(value).on('focusin', function(){
    var text = $(this).text();
    if ( text == 'Cannot be blank!' || text === ' ') {
      $(this).text('');
    }
  });
}
function updateItemTable(){
  $.each(window.fieldTotals, function(name,quantity){
    var selector = $("[id='" + name + "']");
    if ( selector.length > 0 ) {
      selector.text(quantity);
    } else {
      var html = '<tr><td>' + name + '</td><td id="' + name + '">' + quantity + '</td></tr>\n';
      $('#itemTable').append(html);
    }
  });
}
function removeManualOrder(elem){
  var id = elem.currentTarget.id;
  var order = id.split(':');
  var selector = $("#"+order[0]+"\\:"+order[1]);
  var removeOrder = confirm("Are you sure you want to delete this order?");
  if ( removeOrder === true ) {
    // Remove from local storage
    delete window.orderData[order];
    window.storage.set('orderData',window.orderData);

    // Remove item count if AM box was checked
    if ( selector.parent('tr').children('td:first').text() == "true" ) {
      var packageText = selector.parent('tr').children('td:nth-child(7)').text();
      parsePackage(packageText, 'subtract', 'List');
    }

    if ( !selector.parent('tr').children('td').hasClass('unsaved') ) {
      // This order has been saved to the serverSide DB
      if ( window.navigator.onLine ) {
        // Delete data from server
        $.post("delete.php", {"order" : order[0] + ":" + order[1]});
      } else {
        // alert user to delete when online
        var html = '<div id="offLineDelete" class="alert alert-danger alert-dismissable">' +
          '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
          '<strong>WARNING!</strong> This order can only be removed when online, please try again when online</div>';
        $('#mainBody').append(html);
        $('#offLineDelete').focus();
        setTimeout(function(){
          $('#offLineDelete').remove();
        }, 10000);
      }
    }
    selector.parent('tr').remove();
  }
}
function addOrder(){
  // switch to last page
  $('.last.btn.btn-default').trigger('click');
  var hasPickup = ($('#Listable').colCount() == 11 ? true : false);
  var rockaway = ($("#destination").val() == 'Rockaway Beach' ? true : false);
  //Generate Walk On order #
  var itemNum = Math.floor( Math.random() * 90000 );
  var order = 'WO' + Math.floor( Math.random() * 90000 );
  var id = order + ":" + itemNum;
  var row = '<tr style="display:table-row" class="manual">' +
  '<td class="center-me" id ="' + id + ':AM"><span class="value">false</span>' +
  '<button name ="' + id + ':AM" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':PM"><span class="value">false</span>' +
  '<button name ="' + id + ':PM" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td contenteditable="true" id ="' + id +':First" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td contenteditable="true" id ="' + id +':Last" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  ( hasPickup ? '<td contenteditable="true" id ="' + id +':Pickup" class="unsaved"><input type="hidden" value="' + id + '" /></td>' : '') +
  '<td contenteditable="true" id ="' + id +':Phone" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td contenteditable="true" id ="' + id +':Package" class="unsaved">' +
  ( rockaway ? '<td contenteditable="true" id ="' + id + ':Transit To Rockaway" class="unsaved"><input type="hidden" value="' + id + '" /></td>' : '') +
  ( rockaway ? '<td contenteditable="true" id ="' + id + ':Transit From Rockaway" class="unsaved"><input type="hidden" value="' + id + '" /></td>' : '') +
  '<input type="hidden" value="' + id + '" /></td>' +
  '<td headers="Order" class="no-edit unsaved">' + order + '</td>' +
  '<td class="center-me" id ="' + id + ':Waiver"><span class="value">false</span>' +
  '<button name ="' + id + ':Waiver" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':Product"><span class="value">false</span>' +
  '<button name ="' + id + ':Product" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id="' + id +'"><button class="btn-xs btn-warning">' +
  '<span class="glyphicon glyphicon-remove"></span></button></td>' +
  '</tr>',
  $row = $(row),
  resort = false;
  $('#Listable').find('tbody').append($row).trigger('addRows', [$row, resort]);
  $("#"+order+"\\:"+itemNum).on('click', function(elem){
    removeManualOrder(elem);
  });
  addButtonListener("#" + order + "\\:" + itemNum + "\\:AM");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:PM");
  addManualListener("#" + order + "\\:" + itemNum + "\\:First");
  addManualListener("#" + order + "\\:" + itemNum + "\\:Last");
  if ( rockaway ) {
    addManualListener("[id='" + order +":" + itemNum + ":Transit To Rockaway']");
    addManualListener("[id='" + order +":" + itemNum + ":Transit From Rockaway']");
  } else{
    addManualListener("#" + order + "\\:" + itemNum + "\\:Pickup");
  }
  addManualListener("#" + order + "\\:" + itemNum + "\\:Phone");
  addManualListener("#" + order + "\\:" + itemNum + "\\:Package");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Waiver");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Product");
  updateViewAll();
  $('#viewAll').trigger('change');
  $("#" + order + "\\:" + itemNum + "\\:First").focus();

}
function removeOrder(){
    if($('#Listable tbody tr:last').hasClass('manual')){
      var label = $('#Listable tbody tr:last td').attr('id');
      var id = label[0] + ':' + label[1];

      // Delete from object and local storage
      delete window.orderData[id];
      window.storage.set('orderData',window.orderData);

      $('#Listable tbody tr:last').remove();
      $('#total_guests').text(function(i,txt){ return parseInt(txt,10) - 1; });
      $('#Listable').trigger("update"); 
      updateViewAll();
    }
}
function exportCsv(mode){
  var text = '';
  if ( mode == 'Email' ) {
    text += 'Email, First, Last, Package, Pickup\n';
  } else if ( mode == 'Export' ) {
    text += 'AM, PM, First, Last, Pickup, Phone, Package, Order, Waiver, Product REC.,';
    text += ' Bus Only, All Area Lift, Beg. Lift, BRD Rental, Ski Rental, LTS, LTR, Prog. Lesson\n';
  }
  var statusBoxes = {};
  $('.order_status_checkbox').each(function(){
    statusBoxes[$(this).attr('name')] = $(this).is(':checked');
  });
  $.each(window.orderData, function(order,data){
    $.each(data, function(orderItem, fields){
      if ( statusBoxes[fields.Status] === true ) {
        if ( mode == 'Export' ) {
          window.csvValue = {};
          if ( fields.AM == 1 ) {
            parsePackage(fields.Package, 'add', 'CSV');
          } else {
            parsePackage(fields.Package, 'setup', 'CSV');
          }
          text += (fields.AM == 1 ? "X":"") + ',' + (fields.PM == 1 ? "X":"") + ',"' + fields.First + '","' + fields.Last + 
                  '","' + fields.Pickup + '","' + fields.Phone + '","' + fields.Package + '",' + order + ',' +  
                  (fields.Waiver == 1 ? "X":"") + ',' + (fields.Product == 1 ? "X":"") + ',' + (csvValue["Bus Only"] == 1 ? "X":"") + 
                  ',' + (csvValue["All Area Lift"] == 1 ? "X":"") + ',' + (csvValue["Beginner Lift and Lesson"] == 1 ? "X":"") + 
                  ',' + (csvValue["Board Rental"] == 1 ? "X":"") + ',' + (csvValue["Ski Rental"] == 1 ? "X":"") + 
                  ',' + (csvValue["Learn to Ski"] == 1 ? "X":"") + ',' + (csvValue["Learn to Ride"] == 1 ? "X":"") + 
                  ',' + (csvValue["Progressive Lesson"] == 1 ? "X":"") + '\n';  
        } else if ( mode == 'Email' ) { 
          text += '"' + (typeof fields.Email === 'undefined' ? "No Email" : fields.Email) + '","' + fields.First + '","' + fields.Last + '","' + fields.Package + '","' + fields.Pickup + '"\n';
        }
      }
    });
  });
  // for maximum compatibility csv results are bounced through a php script to get consistent download results
  var name = $('#destination').val() + ' ' + $('#trip option:selected').text() + ' ' + mode + '.csv';
  $('body').append('<form id="csv" method="post" action="csv.php">' +
                  '<input type="hidden" name="csvName" /><input type="hidden" name="csvData" /></form>');
  $('#csv input[name=csvName]').val(name);
  $('#csv input[name=csvData]').val(text);
  $('#csv').submit();
  $('#csv').remove();
  /* TODO: change this back to the following lines when safari and firefox support HTML5 link download spec
  var encodedUri = encodeURI(text);
  var link = document.createElement("a");
  var name = $('#destination').val() + ' ' + $('#trip option:selected').text() + ' ' + mode + '.csv';
  link.setAttribute("href", "data:text/csv;charset=utf-8," + encodedUri);
  link.setAttribute("download",name);
  link.click();*/

}
function reloadData() {
  if ( window.storage.get('unsaved') ) {
    window.orderData = window.storage.get('orderData');
    console.log('found unsaved data');
    $('#loader').css('display','inline');
    if (window.navigator.onLine){
      $('#mainBody').append('<div id="#unsavedOnline" class="alert alert-warning alert-dismissable">'+
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                            'Found some unsaved data, save when you get a chance</div>');
    } else {
      $('#loader').css('display','inline');
      $('#save').css('visibility','hidden');
      $('#csv_list').css('visibility','hidden');
      $('#csv_email').css('visibility','hidden');
      $('#mainBody').append('<div id="#unsavedOffline" class="alert alert-success alert-dismissable">'+
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>'+
                            'Reloaded data offline</div>');  
      setTimeout(function(){
        $('#unsavedOffline').remove();
      },10000);
    }
    $('#listTable').buildTable();
  }
}
function updateViewAll(){
  var rows = $('#Listable tbody tr').length;
  $('#viewAll').val(rows);
}
$(function(){
  // Setup local storage
  window.storage = $.localStorage;
  var iOS = ( navigator.userAgent.match(/(iPad|iPhone|iPod)/g) ? true : false );
  if ( iOS ) {
    $('#csv_email').css('display', 'none');
    $('#csv_list').css('display', 'none');
  }
  setupDropDowns();
  if (!window.navigator.onLine) {
    setTrip();
  }
  // remove 300ms click input for checkboxes on iOS
  $('#listTable tbody tr td input').noClickDelay();

  // disable link on onLine/offLine status
  $('.status').not('.iphone').click(function(e){
    e.preventDefault();
  });

  // save when back online
  window.addEventListener('online',  function(){
    if ( $('#Listable').length > 0) {
      $('#mainBody').append('<div id="backOnline" class="alert alert-info alert-dismissable">' +
        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
        'Back online, save starting...</div>');
        setTimeout(function(){
          $('#backOnline').remove();
          $('#save').trigger('click');
        },2000);
    }
  });

  // Monitor onLine status and flip navbar indicator
  setInterval(function () {
    var status = $('.status').not('.iphone');
    var statusSmall = $('.status.iphone');
    if (window.navigator.onLine) {
      if ($('#Listable').length > 0) {
      $('#csv_list').css('visibility','visible');
      $('#csv_email').css('visibility','visible');
      }
      $('#save').removeClass('btn-warning');
      if (status.hasClass('glyphicon-cloud-download')) {
        status.removeClass('glyphicon-cloud-download').addClass('glyphicon-cloud-upload').css('color','').text(' online');
        statusSmall.removeClass('glyphicon-cloud-download').addClass('glyphicon-cloud-upload').css('color','');
      } else if (!status.hasClass('glyphicon-cloud-upload')) {
        status.addClass('glyphicon-cloud-upload').css('color','').text(' online');
        statusSmall.addClass('glyphicon-cloud-upload').css('color','');
      }
    } else if (!window.navigator.onLine) {
      $('#csv_list').css('visibility','hidden');
      $('#csv_email').css('visibility','hidden');
      $('#save').addClass('btn-warning');
      if (status.hasClass('glyphicon-cloud-upload')){
        status.removeClass('glyphicon-cloud-upload').addClass('glyphicon-cloud-download').css('color', 'red').text(' offline');
        statusSmall.removeClass('glyphicon-cloud-upload').addClass('glyphicon-cloud-download').css('color', 'red');
      } else if (!status.hasClass('glyphicon-cloud-download')){
        status.addClass('glyphicon-cloud-download').css('color','red').text(' offline');
        statusSmall.addClass('glyphicon-cloud-download').css('color','red');
      }
    }
  }, 250);
  $('#logo').on('click', function(event){
    event.preventDefault();
    window.location = $(this).attr("href");
  });
  $('#logout').on('click', function(event){
    event.preventDefault();
    window.location = $(this).attr("href");
  });
  reloadData();
});
