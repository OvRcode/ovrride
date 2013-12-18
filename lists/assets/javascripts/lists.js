/**
*  OvR Lists - Custom JavaScript
*
*/

// Order Status: Check All / Uncheck All
function checkAll(trip_list, checktoggle) {
  var checkboxes = [];
  checkboxes = document.getElementsByClassName('order_status_checkbox');

  for (var i=0; i<checkboxes.length; i++) {
    if (checkboxes[i].type == 'checkbox') {
      checkboxes[i].checked = checktoggle;
    }
  }
}

function formReset(){
  var tbl = document.getElementById("Listable");
  while (tbl.firstChild) {
    tbl.removeChild(tbl.firstChild);
  }
}

// webSQL Functions
function autoSaveManualOrder(id,field,value){
  var db = window.db;
  var trip = $('#trip').val();
  var replaceFields = {};
  replaceFields.First = "COALESCE((SELECT `First` FROM ovr_lists_manual_orders WHERE `ID` = '" + id + "'),'')";
  replaceFields.Last = "COALESCE((SELECT `Last` FROM ovr_lists_manual_orders WHERE `ID` = '" + id + "'),'')";
  replaceFields.Pickup = "COALESCE((SELECT `Pickup` FROM ovr_lists_manual_orders WHERE `ID` = '" + id +"'),'')";
  replaceFields.Phone = "COALESCE((SELECT `Phone` FROM ovr_lists_manual_orders WHERE `ID` = '" + id + "'),'')";
  replaceFields.Package = "COALESCE((SELECT `Package` FROM ovr_lists_manual_orders WHERE `ID` = '" + id + "'),'')";

  var sql = "INSERT OR REPLACE INTO ovr_lists_manual_orders (`ID`, `First`,`Last`, `Pickup`, `Phone`, `Package`, `Trip`) VALUES(?, ";
  switch(field){
    case 'First':
      sql += "'" + value +"'," + replaceFields.Last + ", " + replaceFields.Pickup + ", " + replaceFields.Phone + ", " + replaceFields.Package + ", ?)";
      break;
    case 'Last':
      sql += replaceFields.First +",'" + value + "', " + replaceFields.Pickup + ", " + replaceFields.Phone + ", " + replaceFields.Package + ", ?)";
      break;
    case 'Pickup':
      sql += replaceFields.First +"," + replaceFields.Last + ", '" + value + "', " + replaceFields.Phone + ", " + replaceFields.Package + ", ?)";
      break;
    case 'Phone':
      sql += replaceFields.First +"," + replaceFields.Last + ", " + replaceFields.Pickup + ", '" + value + "', " + replaceFields.Package + ", ?)";
      break;
    case 'Package':
      sql += replaceFields.First +"," + replaceFields.Last + ", " + replaceFields.Pickup + ", " + replaceFields.Phone + ", '" + value + "', ?)";
      break;
    default:
      sql += replaceFields.First +"," + replaceFields.Last + ", " + replaceFields.Pickup + ", " + replaceFields.Phone + ", " + replaceFields.Package + ", ?)";
      break;
  }
  db.transaction(function(tx){
    tx.executeSql(sql, [id, trip], function(tx,result){},
                  function(tx, error){
                    console.log(error.message);
                  }); 
  });
}
function saveCheckbox(id,value){
  var db = window.db;
  var time = (new Date()).valueOf();
  db.transaction(function(tx) {
    tx.executeSql('INSERT OR REPLACE INTO `ovr_lists_fields` (`ID`, `value`, `timeStamp`) VALUES(?,?,?)',
      [id, value, time],
      function(tx,result){},
      function(tx, error){
        console.log('error inserting or replacing on ovr_lists_fields: ' + error.message);
      }
    );
  });
}
// NEED TO RE THINK THIS
function saveWebOrder(id,webOrder){
  var db = window.db;
  var time = (new Date()).valueOf();
  db.transaction(function(tx) {
    tx.executeSql('INSERT OR REPLACE INTO `ovr_lists_orders`' +
                  ' (`ID`, `First`, `Last`, `Pickup`, `Phone`,`Package`, `Trip`,`timeStamp`)' +
                  ' VALUES(?,?,?,?,?,?,?,?)',
      [id, webOrder.First, webOrder.Last, webOrder.Pickup, webOrder.Phone, webOrder.Package, webOrder.Trip, time],
      function(tx,result){},
      function(tx, error){
        console.log('error inserting or replacing on ovr_lists_orders: ' + error.message);
      }
    );
  });
}
$("#save").click(function(){
  setupProgressBar();
  $('#saveBar').css('width', '10%');
  
  if(window.navigator.onLine){
    $('#saveBar').css('width', '20%');
    window.tableData = {};
    selectOrderCheckboxes();
  } else {
    $('#mainBody').append('<div id="success" class="alert alert-warning alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                            'Changes made have been saved locally, try again when you\'re online</div>');
    $('#saveBar').css('width', '100%');
    setTimeout(function(){
      $('#saveProgress').remove();
    },2000);
  }
});
function selectOrderCheckboxes(){
  $('#saveBar').css('width', '30%');
  var db = window.db;
  var tableData = window.tableData;
  var trip = $('#trip').val();
  db.transaction(function(tx){
    tx.executeSql('SELECT `ovr_lists_fields`.`ID`, `ovr_lists_fields`.`value`,`ovr_lists_fields`.`timeStamp`' +
                  'FROM `ovr_lists_fields`' +
                  'INNER JOIN `ovr_lists_orders` on `ovr_lists_fields`.`ID` LIKE `ovr_lists_orders`.`ID` || "%"' +
                  'WHERE `ovr_lists_orders`.`trip` = ?', 
                  [trip],
                  function(tx,results){
                    var len=results.rows.length;
                    var i;
                    for(i = 0; i < len; i++) {
                      var label = results.rows.item(i).ID;
                      label = label.split(':');
                      var order = label[0];
                      var orderItem = label[1];
                      var field = label[2];
                      var value = results.rows.item(i).value == 'true' ? 1:0;
                      if ( typeof tableData[order] === "undefined") {
                      tableData[order] = {};
                      }
                      if ( typeof tableData[order][orderItem] === "undefined" ) {
                      tableData[order][orderItem] = {};
                      }
                      tableData[order][orderItem][field] = new Array(value, results.rows.item(i).timeStamp);
                    }
                    $('#saveBar').css('width', '40%');
                    selectManualOrders();
                  });
  });
}
function selectManualOrders(){
  $('#saveBar').css('width', '50%');
  var db = window.db;
  var tableData = window.tableData;
  var trip = $('#trip').val();
  db.transaction(function(tx){
    tx.executeSql('SELECT * FROM `ovr_lists_manual_orders` WHERE `trip` = ?',
                  [trip],
                  function(tx,results){
                  var len = results.rows.length;
                  var i;
                  for(i = 0; i < len; i++) {
                    var id = results.rows.item(i).ID;
                    id = id.split(':');
                    var order = id[0];
                    var orderItem = id[1];
                    if ( typeof tableData[order] === 'undefined') {
                      tableData[order] = {};
                    }
                    if ( typeof tableData[order][orderItem] === 'undefined') {
                      tableData[order][orderItem] = {};
                    }
                    tableData[order][orderItem].First = results.rows.item(i).First;
                    tableData[order][orderItem].Last = results.rows.item(i).Last;
                    tableData[order][orderItem].Pickup = results.rows.item(i).Pickup;
                    tableData[order][orderItem].Phone = results.rows.item(i).Phone;
                    tableData[order][orderItem].Package = results.rows.item(i).Package;
                    tableData[order][orderItem].Trip = results.rows.item(i).Trip;
                  }
                  selectManualCheckboxes();
      });
  });
}
function selectManualCheckboxes(){
  $('#saveBar').css('width', '60%');
  var db = window.db;
  var tableData = window.tableData;
  var trip = $('#trip').val();
  db.transaction(function(tx){
    tx.executeSql('SELECT `ovr_lists_fields`.* ' +
                  'FROM `ovr_lists_fields`, `ovr_lists_manual_orders`' +
                  'WHERE `ovr_lists_fields`.`ID` LIKE `ovr_lists_manual_orders`.`ID`||"%" ' +
                  'AND `ovr_lists_manual_orders`.`trip` = ?',
                  [trip],
                  function(tx,results){
                    var len = results.rows.length;
                    var i;
                    console.log('Results:' + len);
                    for (i = 0; i < len; i++) {
                      var id = results.rows.item(i).ID;
                      id = id.split(':');
                      var order = id[0];
                      var orderItem = id[1];
                      var field = id[2];
                      var value = results.rows.item(i).value == 'true' ? 1:0;
                      if ( typeof tableData[order] === 'undefined') {
                        tableData[order] = {};
                      }
                      if ( typeof tableData[order][orderItem] === 'undefined') {
                        tableData[order][orderItem] = {};
                      }
                      tableData[order][orderItem][field] = new Array(value, results.rows.item(i).timeStamp);
                    }
                    postData();
                  },
                  function(tx,error) {
                    console.log("query error:" + error.message);
                  });
  });
}
function postData(){
  $('#saveBar').css('width', '80%');
  //console.log(window.tableData);
  console.log(window.tableData);
  var jqxhr = $.post( "save.php", window.tableData,function() {})
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
      });
    })
    .fail(function() {
      console.log("POST request error");
      $('#mainBody').append('<div id="success" class="alert alert-danger alert-dismissable">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>' +
                            'Something went wrong, try saving again</div>');
    });
}
function truncateTables(){
  // This is just to clear data without resetting browser
  var db = window.db;
  db.transaction(function (tx) {  
    tx.executeSql('DELETE FROM `ovr_lists_fields`');
  });
  db.transaction(function (tx) {
    tx.executeSql('DELETE FROM `ovr_lists_manual_orders`');
  });
  db.transaction(function (tx) {
    tx.executeSql('DELETE FROM `ovr_lists_orders`');
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
// save checkboxes and manual entries  on change to websql
$.fn.autoSave = function(){
  /* Function will be called each time a manual row is added
     unbind events first to avoid duplicate event listeners */
  $('#Listable').unbind('click');
  $('#Listable .manual').unbind('focusout');
  $('#Listable').on('click','.center-me' ,function(){
    saveCheckbox($(this).children('input').attr('name'),$(this).children('input').is(':checked'));
  }); 
  $('#Listable .manual').on('focusout','.unsaved', function(){
    autoSaveManualOrder($(this).children('input').val(), $(this).attr('headers'), $(this).text());
  });
};
// End webSQL Functions
$(function(){
  // Connect to webSQL DB and create tables
  (function(){
    var db = openDatabase('lists.ovrride.com', '0.2', 'OvR Ride Lists local DB', 2 * 1024 * 1024);
    window.db = db;
    db.transaction(function(tx) {
      tx.executeSql('CREATE TABLE IF NOT EXISTS' +
                    '`ovr_lists_fields` (`ID` UNIQUE, `value` INTEGER, `timeStamp` INTEGER)',
                    [],
                    function(tx, result) {
                      console.log("ovr_lists_fields setup success"); },
                    function(tx, error) {
                      console.log("ovr_lists_fields setup error: " + error.message); }
      );
    });
    db.transaction(function(tx) {
      tx.executeSql('CREATE TABLE IF NOT EXISTS' +
                    '`ovr_lists_manual_orders` (`ID` UNIQUE, `First`, `Last`,' +
                    ' `Pickup`, `Phone`, `Package`, `Trip`)',
                    [],
                    function(tx, result){
                      console.log('ovr_lists_manual_orders setup success'); },
                    function(tx, error){
                      console.log('ovr_lists_manual_orders setup error:' + error.message); }
      );
      tx.executeSql('CREATE TABLE IF NOT EXISTS' +
                    '`ovr_lists_orders` (`ID` UNIQUE, `First`, `Last`,' +
                    ' `Pickup`, `Phone`, `Package`, `Trip`, `timeStamp` INTEGER)',
                    [],
                    function(tx, result){
                      console.log('ovr_lists_manual_orders setup success'); },
                    function(tx, error){
                      console.log('ovr_lists_manual_orders setup error:' + error.message); }
      );
    });
  })();
  $('#status').click(function(e){
    e.preventDefault();
  });
  setInterval(function () {
    var status = $('#status');
    if (window.navigator.onLine) {
      if (status.hasClass('glyphicon-cloud-download')) {
        status.removeClass('glyphicon-cloud-download').addClass('glyphicon-cloud-upload').css('color','').text(' online');
      } else if (!status.hasClass('glyphicon-cloud-upload')) {
        status.addClass('glyphicon-cloud-upload').css('color','').text(' online');
      } 
    } else if (!window.navigator.onLine) {
      if (status.hasClass('glyphicon-cloud-upload')){
        status.removeClass('glyphicon-cloud-upload').addClass('glyphicon-cloud-download').css('color', 'red').text(' offline');
      } else if (!status.hasClass('glyphicon-cloud-download')){
        status.addClass('glyphicon-cloud-download').css('color','red').text(' offline');
      }
    }
  }, 250);
  $(window.navigator.onLine).on('offline',function(event){
    $('#status').removeClass('glyphicon-cloud-upload').addClass('glyphicon-cloud-download').css('color','red');
  });
  // Create a table if data exists
  $.fn.buildTable = function(){
    var hasPickup   = $("#hasPickup").val();

    var orders = $('.order');
    var orderData   = {};
    var tableHeader = '';
    var tableBody   = '';
    var tableFooter = '';
    var riders      = 0;
    var byLocation  = {};

    if (orders.length === 0 && $('#trip').val() != "none") {
      $(this).append('<div class="container"><p>There are no orders for the selected Trip and Order Status.</p></div>');
    } 
    else if (orders.length > 0){
      orders.each(function(){
        var temp = jQuery.parseJSON($(this).val());
        orderData[temp[0]] = temp[1];
      });
      
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
      
      $.each(orderData, function(orderNumber, values){
        var prefix = orderNumber.substring(0,2);
        $.each(values, function(orderItemNumber, fields){
          var id = orderNumber+":"+orderItemNumber+":";
          var row = {};
          $.each(fields, function(field, value){
            if (field == 'First' || field == 'Last' || field == 'Pickup' || field == 'Phone' || field == 'Package' || field == 'Order') {
              row[field] = '<td';
              switch (field) {
                case 'First':
                  row[field] += ' headers="First"';
                  break;
                case 'Last':
                  row[field] += ' headers="Last"';
                  break;
                case 'Pickup':
                  row[field] += ' headers="Pickup"';
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
              row[field] +='>'+value+'</td>';
            } 
            else {
              row[field] = '<td class="center-me"><input type="checkbox" name="' + id + field + '" ' + value +'></td>';
            }
          });
          // Had to manually assemble cells in correct order, couldn't get AM/Pm on left side of table with a loop
          //    this is proably a result of moving data from PHP to JSON and back to an array 
          tableBody += '<tr>'+row.AM + row.PM + row.First + row.Last;
          if (hasPickup == 1) {
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
          riders++;
          if (hasPickup == 1) {
            var locationName = row.Pickup.replace(/<(?:.|\n)*?>/gm, '');
            if(typeof byLocation[locationName] === undefined || typeof byLocation[locationName] === 'undefined'){
              byLocation[locationName] = 0;
            }
            byLocation[locationName] += 1;
          }
        });
      });
      tableBody += '</tbody>\n';
      tableFooter += '<tfoot>\n<tr class="totals-row">' +
                     '<td>Total Guests: </td>\n' +
                     '<td id="total_guests">' + riders + '</td>' +
                     '<td><button type="button" class="btn btn-primary" id="add">' +
                     '<span class="glyphicon glyphicon-plus"></span></button>' +
                     '<button type="button" class="btn btn-danger pull-right" id="remove">' +
                     '<span class="glyphicon glyphicon-minus"></span></button></td>';
      if (hasPickup == 1) {
        tableFooter += '<td>Guests by Pickup: </td>';
        $.each(byLocation, function(location, value){
          tableFooter += '<td>' + location + ': ' + value + '</td>';
        });
      }
      tableFooter += '</tfoot></table>';
      var output = tableHeader + tableBody + tableFooter;
      $(this).append(output);
    }
    if (orders.length > 0) {
      orders.remove();
      $('#hasPickup').remove();
    }
  };
  $("#listTable").buildTable();

  // Chained drop downs
  $("#trip").chained("#destination");
  //custom column counter
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
  // tablesorter configuration
  // http://mottie.github.io/tablesorter/docs/#Configuration
  var rows = $("#Listable").colCount();
  // check for pickup column, 18 columns with 17 without
  if(rows == 18){
    $('#Listable').tablesorter({
      sortList: [[4,0],[3,0]],
      headers: {
            0: { sorter: 'checkbox' },
            1: { sorter: 'checkbox' },
            8: { sorter: 'checkbox' },
            9: { sorter: 'checkbox' },
            10: { sorter: 'checkbox' },
            11: { sorter: 'checkbox' },
            12: { sorter: 'checkbox' },
            13: { sorter: 'checkbox' },
            14: { sorter: 'checkbox' },
            15: { sorter: 'checkbox' },
            16: { sorter: 'checkbox' },
            17: { sorter: 'checkbox' }
          },
      widgets : [ 'editable', 'columns','stickyHeaders','filter' ],
      widgetOptions: {
        editable_columns       : "2-6",  // point to the columns to make editable (zero-based index)
        editable_enterToAccept : true,     // press enter to accept content, or click outside if false
        editable_autoResort    : false,    // auto resort after the content has changed.
        editable_noEdit        : 'no-edit', // class name of cell that is no editable
        stickyHeaders_offset: 50,
        filter_childRows : false,
        filter_columnFilters : true,
        filter_hideFilters : true,
        filter_ignoreCase : true,
        filter_reset : '.reset',
        filter_searchDelay : 100,
        filter_functions : {
          4 : true,
          6 : true 
        }
      }
    });
  }
  else if (rows == 17){
    $('#Listable').tablesorter({
      sortList: [[4,0],[3,0]],
      headers: {
            0: { sorter: 'checkbox' },
            1: { sorter: 'checkbox' },
            8: { sorter: 'checkbox' },
            9: { sorter: 'checkbox' },
            10: { sorter: 'checkbox' },
            11: { sorter: 'checkbox' },
            12: { sorter: 'checkbox' },
            13: { sorter: 'checkbox' },
            14: { sorter: 'checkbox' },
            15: { sorter: 'checkbox' },
            16: { sorter: 'checkbox' },
            17: { sorter: 'checkbox' }
          },
      widgets : [ 'editable', 'columns','stickyHeaders','filter'],
      widgetOptions: {
        editable_columns       : "2-5",  // point to the columns to make editable (zero-based index)
        editable_enterToAccept : true,     // press enter to accept content, or click outside if false
        editable_autoResort    : false,    // auto resort after the content has changed.
        editable_noEdit        : 'no-edit', // class name of cell that is no editable
        stickyHeaders_offset: 50,
        filter_childRows : false,
        filter_columnFilters : true,
        filter_hideFilters : true,
        filter_ignoreCase : true,
        filter_reset : '.reset',
        filter_searchDelay : 100,
        filter_functions : {
          5 : true
        }
      }
    }); 
  }
  $().autoSave();
  $('#add').click(function(){
    // Find total cell and increment
    $('#total_guests').text(function(i,txt){ return parseInt(txt,10) + 1;});
    //Generate Walk On order #
    var itemNum = Math.floor(Math.random()*90000);
    var order = 'WO'+ Math.floor(Math.random()*90000);
    var id = order+":"+itemNum;
    var row = '<tr class="manual"><td class="center-me"><input type="checkbox" name="' + id + ':AM"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':PM"></td>' +
    '<td contenteditable="true" headers="First" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
    '<td contenteditable="true" headers="Last" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
    '<td contenteditable="true" headers="Pickup" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
    '<td contenteditable="true" headers="Phone" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
    '<td contenteditable="true" headers="Package" class="unsaved"><input type="hidden" value="' + id + '" /></td>' +
    '<td headers="Order" class="no-edit unsaved">' + order + '</td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':Waiver"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':Product"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':Bus"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':All_Area"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':Beg"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':BRD"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':SKI"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':LTS"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':LTR"></td>' +
    '<td class="center-me"><input type="checkbox" name="' + id + ':Prog_Lesson"></td></tr>',
    $row = $(row),
    // resort table using the current sort; set to false to prevent resort, otherwise 
    // any other value in resort will automatically trigger the table resort. 
    resort = true;
    $('#Listable').find('tbody').append($row).trigger('addRows', [$row, resort]);
    $().autoSave();
    return false;
   });
   
   // remove dynamically added rows from table
   $('#remove').click(function(){
       if($('#Listable tbody tr:last').hasClass('manual')){
       $('#Listable tbody tr:last').remove();
       $('#total_guests').text(function(i,txt){ return parseInt(txt,10) - 1; });
       $('#Listable').trigger("update");}
   });
  
});
