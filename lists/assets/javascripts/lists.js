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
function checkPackages(text, order, orderItem, value){
  var bus         = new RegExp(/bus only/i);
  var begLift     = new RegExp(/beginner lift/i);
  var lesson      = new RegExp(/lesson/i);
  var progSki     = new RegExp(/prog.* lesson.*ski rental/i);
  var progBrd     = new RegExp(/prog.* lesson.*board rental/i);
  var progLesson  = new RegExp(/prog.* lesson/i);
  var ski         = new RegExp(/ski rental/i);
  var brd         = new RegExp(/board rental/i);
  var allArea     = new RegExp(/all area/i);
  var weekendLift = new RegExp(/lift/i);
  
  if ( bus.test(text) ) {
    console.log('RegExp: Matched bus only');
    var busId = $('#' + order + "\\:" + orderItem + "\\:Bus");
    if ( busId.children('span').text() == value ){
      busId.children('button').click();
    } else { console.log('Bus: wrong value');}
  }
  else {
    // Selectors for button values
    var begId     = "#" + order + "\\:" + orderItem + "\\:Beg";
    var allAreaId = "#" + order + "\\:" + orderItem + "\\:All_Area";
    var progId    = "#" + order + "\\:" + orderItem + "\\:Prog_Lesson";
    var skiId     = "#" + order + "\\:" + orderItem + "\\:SKI";
    var brdId     = "#" + order + "\\:" + orderItem + "\\:BRD";
    var ltrId     = "#" + order + "\\:" + orderItem + "\\:LTR";
    var ltsId     = "#" + order + "\\:" + orderItem + "\\:LTS";

    // All area, beginner, weekend lift ticket
    if ( lesson.test(text) && brd.test(text) && begLift.test(text)) {
      if ( $(begId).children('span').text() == value ){
        // Counter act value increment for LTS/LTR package items
        if ( value == "true" ) {
          $("#Beg").text(parseInt($("#Beg").text(), 10) + 1);
        } else {
          $("#Beg").text(parseInt($("#Beg").text(), 10) - 1);
        }
        $(begId).children('button').click();
      }
      if ( $(ltrId).children('span').text() == value ) {
        $(ltrId).children('button').click();
      }
      if ( $(brdId).children('span').text() == value ) {
        if ( value == "true" ) {
          $("#BRD").text(parseInt($("#BRD").text(), 10) + 1);
        } else {
          $("#BRD").text(parseInt($("#BRD").text(), 10) - 1);
        }
        $(brdId).children('button').click();
      }
    }
    else if ( lesson.test(text) && ski.test(text) && begLift.test(text) ) {
      if ( $(begId).children('span').text() == value ){
        // Counter act value incriment for LTS/LTR package items
        if ( value == "true" ) {
          $("#Beg").text(parseInt($("#Beg").text(), 10) + 1);
        } else {
          $("#Beg").text(parseInt($("#Beg").text(), 10) - 1);
        }
        $(begId).children('button').click();
      }
      if ( $(ltsId).children('span').text() == value ) {
        $(ltsId).children('button').click();
      }
      if ( $(skiId).children('span').text() == value ){
        if ( value == "true" ) {
          $("#SKI").text(parseInt($("#SKI").text(), 10) + 1);
        } else {
          $("#SKI").text(parseInt($("#SKI").text(), 10) - 1);
        }
        $(skiId).children('button').click();
      }
    }
    else if ( begLift.test(text) ) {
      if ( $(begId).children('span').text() == value ){
        $(begId).children('button').click();
      }
    } else if ( allArea.test(text) || weekendLift.test(text) ) {
      if ( $(allAreaId).children('span').text() == value ) {
        $(allAreaId).children('button').click();
      }
    }

    // Lessons + rental combos
    if ( progSki.test(text)) {
      if ( $(progId).children('span').text() == value ) {
        $(progId).children('button').click();
      }
      if ( $(skiId).children('span').text() == value ) {
        $(skiId).children('button').click();
      }
    }
    else if ( progBrd.test(text) ) {
      if ( $(brdId).children('span').text() == value ) {
        $(brdId).children('button').click();
      }
      if ( $(progId).children('span').text() == value ) {
        $(progId).children('button').click();
      }
    }
    else if ( progLesson.test(text) ) {
      if ( $(progId).children('span').text() == value ) {
        progId.children('button').click();
      }
    }
    else if ( ski.test(text) ) {
      if ( $(skiId).children('span').text() == value ) {
        $(skiId).children('button').click();
      }
    }
    else if ( brd.test(text) ) {
      if ( $(brdId).children('span').text() == value ) {
        $(brdId).children('button').click();
      }
    }
    else if ( lesson.test(text) ) {
      if ( value == "false" ) {
        var skiRegex = new RegExp(/ski/i);
        var brdRegex = new RegExp(/brd/i);
        var input = prompt('SKI or BRD Lesson?\n(enter ski or brd)');
        if ( skiRegex.test(input) ) {
          $(ltsId).children('button').click();
        } else if ( brdRegex.test(input) ) {
          $(ltrId).children('button').click();
        }
      } else {
        if ( $(ltsId).children('span').text() == value ) {
          $(ltsId).children('button').click();
        }
        if ( $(ltrId).children('span').text() == value ) {
          $(ltrId).children('button').click();
        }
      }
      
    }
  }
}
function generateOnOff(){
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

  console.log('SaveData: ');
  console.log(window.storage.get('orderData'));
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
      0: { sorter: 'text' },
      1: { sorter: 'checkbox' },
      2: { sorter: 'text' },
      3: { sorter: 'text' },
      4: { sorter: 'text' },
      5: { sorter: 'digit' },
      6: { sorter: 'text' },
      7: { sorter: 'digit' },
      8: { sorter: 'text' },
      9: { sorter: 'text' },
      10: { sorter: 'text' },
      11: { sorter: 'text' },
      12: { sorter: 'text' },
      13: { sorter: 'text' },
      14: { sorter: 'text' },
      15: { sorter: 'text' },
      16: { sorter: 'text' },
      17: { sorter: 'text' },
      18: { sorter: 'text' }
    };
    var filterOptions = {
        4 : true,
        6 : true 
      };
    var widgetOptions = {
      editable_columns       : '2-6',     // point to the columns to make editable (zero-based index)
      editable_enterToAccept : true,      // press enter to accept content, or click outside if false
      editable_autoResort    : false,     // auto resort after the content has changed.
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
    if (rows == 17) {
      delete headerOptions[18];
      headerOptions[4].sorter = 'digit';
      headerOptions[5].sorter = 'text';
      headerOptions[6].sorter = 'digit';
      headerOptions[7].sorter = 'text';
      widgetOptions.editable_columns = '2-5';
      delete filterOptions[4];
      delete filterOptions[6];
      filterOptions[5] = true;
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
   $('thead:nth-child(1) td', this).each(function () {
       if ($(this).attr('colspan')) {
           colCount += +$(this).attr('colspan');
       } else {
           colCount++;
       }
   });
   return colCount;
};
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
  var fieldTotals = {"Bus" : 0, "All_Area" : 0, "Beg" : 0, "BRD" : 0, "SKI" : 0, "LTS" : 0, "LTR" : 0, "Prog_Lesson" : 0};
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
          if (field == 'First' || field == 'Last' || field == 'Pickup' || field == 'Phone' || field == 'Package' || field == 'Order') {
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
            }
            if (prefix != 'WO') {
              row[field] += ' class="no-edit"';
            } else {
              row[field] += ' class="saved"';
            }
            row[field] +='>' + value + '</td>';
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
              if ( typeof fieldTotals[field] === 'undefined' && (field != 'Waiver' && field != 'Order') ) {
                fieldTotals[field] = 0;
              }
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

        tableBody += row.Phone + row.Package;

        if (prefix == 'WO') {
          tableBody += '<td>' + orderNumber + '</td>';
        } else {
          tableBody += '<td><a href="https://ovrride.com/wp-admin/post.php?post=' + orderNumber +'&action=edit" target="_blank">' + orderNumber+ '</a></td>';
        }

        tableBody += row.Waiver + row.Product + row.Bus + row.All_Area;
        tableBody += row.Beg + row.BRD + row.SKI + row.LTS + row.LTR + row.Prog_Lesson + '</tr>';
        //set itemTable values
        if ( findButtonValueString(row.Bus) == "true" ) { fieldTotals.Bus++; }
        if ( findButtonValueString(row.All_Area) == "true" ) { fieldTotals.All_Area++; }
        if ( findButtonValueString(row.Beg) == "true" ) { fieldTotals.Beg++; }
        if ( findButtonValueString(row.BRD) == "true" ) { fieldTotals.BRD++; }
        if ( findButtonValueString(row.SKI) == "true" ) { fieldTotals.SKI++; }
        if ( findButtonValueString(row.LTS) == "true" ) { fieldTotals.LTS++; }
        if ( findButtonValueString(row.LTR) == "true" ) { fieldTotals.LTR++; }
        if ( findButtonValueString(row.Prog_Lesson) == "true" ) { fieldTotals.Prog_Lesson++; }
        if (row.Pickup) {
          var locationName = row.Pickup.replace(/<(?:.|\n)*?>/gm, '');
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
    tableHeader += '<td data-placeholder="Choose a Location">Pickup</td>';
  }

  tableHeader += '<td>Phone</td>' +
                '<td data-placeholder="Choose a Package">Package</td>' +
                '<td>Order</td>' +
                '<td class="filter-false">Waiver</td>' +
                '<td class="filter-false">Product REC.</td>' +
                '<td class="filter-false">Bus Only</td>' +
                '<td class="filter-false">All Area Lift</td>' +
                '<td class="filter-false">Beg. Lift</td>' +
                '<td class="filter-false">BRD Rental</td>' +
                '<td class="filter-false">Ski Rental</td>' +
                '<td class="filter-false">LTS</td>' +
                '<td class="filter-false">LTR</td>' +
                '<td class="filter-false">Prog. Lesson</td>\n' +
                '</tr>' +
                '</thead>\n';

  tableBody += '</tbody>\n';
  tableFooter += '<tfoot>\n<tr>' +
                 '<td><button type="button" class="btn btn-primary" id="add">' +
                 '<span class="glyphicon glyphicon-plus"></span></button></td>' +
                 '<td><button type="button" class="btn btn-danger" id="remove">' +
                 '<span class="glyphicon glyphicon-minus"></span></button></td>' +
                 '</tfoot></table>';
  if (byLocation) {
    locationTable = '<div id="locationContainer" class="col col-md-4 col-md-offset-2"><h4>Riders by pickup location:</h4>';
    locationTable += '<table id="locationTable" class="table table-bordered table-striped table-condensed">\n';
    locationTable += '<thead><tr><td>Location</td><td>Expected</td><td>AM</td><td>PM</td></tr></thead>\n';
    locationTable += '<tbody>\n';
    $.each(byLocation, function(location, value){
      locationTable += '<tr><td>' + location + '</td><td>' + value.Expected + '</td><td>' + value.AM+ '</td><td>' + value.PM + '</td></tr>\n';
    });
    locationTable += '</tbody></table></div>';
    $('#totals').append(locationTable);
  }
  var adjustedBeg = Math.max(fieldTotals.Beg - ( fieldTotals.LTS + fieldTotals.LTR ), 0);
  var adjustedBRD = Math.max(fieldTotals.BRD - fieldTotals.LTR, 0);
  var adjustedSKI = Math.max(fieldTotals.SKI - fieldTotals.LTS, 0);
  itemTable =  '<div id="itemContainer" class="col col-md-4"><h4>Package item totals:</h4>\n';
  itemTable += '<table id="itemTable" class="table table-bordered table-striped table-condensed">\n';
  itemTable += '<thead><tr><td>Package Item</td><td>Count</td></tr></thead>\n';
  itemTable += '<tbody><tr><td>Bus Only</td><td id="Bus">' + fieldTotals.Bus + '</td></tr>\n';
  itemTable += '<tr><td>All Area Lift</td><td id="All_Area">' + fieldTotals.All_Area + '</td></tr>\n';
  itemTable += '<tr><td>Beginner Lift</td><td id="Beg">' + adjustedBeg + '</td></tr>\n';
  itemTable += '<tr><td>Board Rental</td><td id="BRD">' + adjustedBRD + '</td></tr>\n';
  itemTable += '<tr><td>Ski Rental</td><td id="SKI">' + adjustedSKI + '</td></tr>\n';
  itemTable += '<tr><td>Learn To Ski</td><td id="LTS">' + fieldTotals.LTS + '</td></tr>\n';
  itemTable += '<tr><td>Learn To Ride</td><td id="LTR">' + fieldTotals.LTR + '</td></tr>\n';
  itemTable += '<tr><td>Progressive Lesson</td><td id="Prog_Lesson">' + fieldTotals.Prog_Lesson + '</td></tr>\n';
  itemTable += '</tbody></table></div>';
  $('#totals').append(itemTable);
  var output = tableHeader + tableBody + tableFooter;
  $(this).append(output);
  $('#footer').css('position','static');
  $('#loader').css('display','none');
  // click event to add row to the table for a manual order
  $('#add').click(function(){addOrder();});

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
    var packageValue;
    var fieldValue;
    var listLocation;
    var locationRow;

    var time = (((new Date()).valueOf()).toString()).substr(0,10);

    if ( button.hasClass('btn-success')) {
      button.removeClass('btn-success').addClass('btn-danger').val('false');
      iconSpan.removeClass('glyphicon-ok-sign').addClass('glyphicon-minus-sign');
      hiddenSpan.text('false');
      orderData[order][item][field] = "0";
      packageValue = "true";
      $("#" + field).text(parseInt($("#" + field).text(), 10) - 1);
      if ( field == 'AM' ) {
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
      packageValue = "false";
      $("#" + field).text(parseInt($("#" + field).text(), 10) + 1);
      if ( field == 'AM'){
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
      checkPackages(packageText, order, item, packageValue);
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
function addOrder(){
  // switch to last page
  $('.last.btn.btn-default').trigger('click');

  // Find total cell and increment
  $('#total_guests').text( function(i,txt) { return parseInt(txt,10) + 1;} );

  //Generate Walk On order #
  var itemNum = Math.floor( Math.random() * 90000 );
  var order = 'WO' + Math.floor( Math.random() * 90000 );
  var id = order + ":" + itemNum;

  var row = '<tr class="manual">' +
  '<td class="center-me" id ="' + id + ':AM"><span class="value">false</span>' +
  '<button name ="' + id + ':AM" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':PM"><span class="value">false</span>' +
  '<button name ="' + id + ':PM" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td contenteditable="true" id ="' + id +':First" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td contenteditable="true" id ="' + id +':Last" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td contenteditable="true" id ="' + id +':Pickup" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td contenteditable="true" id ="' + id +':Phone" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td contenteditable="true" id ="' + id +':Package" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
  '<td headers="Order" class="no-edit unsaved">' + order + '</td>' +
  '<td class="center-me" id ="' + id + ':Waiver"><span class="value">false</span>' +
  '<button name ="' + id + ':Waiver" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':Product"><span class="value">false</span>' +
  '<button name ="' + id + ':Product" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':Bus"><span class="value">false</span>' +
  '<button name ="' + id + ':Bus" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':All_Area"><span class="value">false</span>' +
  '<button name ="' + id + ':All_Area" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':Beg"><span class="value">false</span>' +
  '<button name ="' + id + ':Beg" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':BRD"><span class="value">false</span>' +
  '<button name ="' + id + ':BRD" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':SKI"><span class="value">false</span>' +
  '<button name ="' + id + ':SKI" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':LTS"><span class="value">false</span>' +
  '<button name ="' + id + ':LTS" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':LTR"><span class="value">false</span>' +
  '<button name ="' + id + ':LTR" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td>' +
  '<td class="center-me" id ="' + id + ':Prog_Lesson"><span class="value">false</span>' +
  '<button name ="' + id + ':Prog_Lesson" class="btn-xs btn-default btn-danger" value="false">' +
  '<span class="glyphicon glyphicon-minus-sign"></span></button></td></tr>',
  $row = $(row),
  resort = false;
  $('#Listable').find('tbody').append($row).trigger('addRows', [$row, resort]);
  addButtonListener("#" + order + "\\:" + itemNum + "\\:AM");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:PM");
  addManualListener("#" + order + "\\:" + itemNum + "\\:First");
  addManualListener("#" + order + "\\:" + itemNum + "\\:Last");
  addManualListener("#" + order + "\\:" + itemNum + "\\:Pickup");
  addManualListener("#" + order + "\\:" + itemNum + "\\:Phone");
  addManualListener("#" + order + "\\:" + itemNum + "\\:Package");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Waiver");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Product");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Bus");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:All_Area");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Beg");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:BRD");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:SKI");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:LTS");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:LTR");
  addButtonListener("#" + order + "\\:" + itemNum + "\\:Prog_Lesson");
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
          text += (fields.AM == 1 ? "X":"O") + ',' + (fields.PM == 1 ? "X":"O") + ',"' + fields.First + '","' + fields.Last + '","' + fields.Pickup + '","' + fields.Phone + '",';
          text += '"' + fields.Package + '",' + order + ',' +  (fields.Waiver == 1 ? "X":"O") + ',' + (fields.Product == 1 ? "X":"O") + ',';
          text += (fields.Bus == 1 ? "X":"O") + ',' + (fields.All_Area == 1 ? "X":"O") + ',' + (fields.Beg == 1 ? "X":"O") + ',';
          text += (fields.BRD == 1 ? "X":"O") + ',' + (fields.SKI == 1 ? "X":"O") + ',' + (fields.LTS == 1 ? "X":"O") + ',';
          text += (fields.LTR == 1 ? "X":"O") + ',' + (fields.Prog_Lesson == 1 ? "X":"O") + '\n';
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