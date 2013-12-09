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

function tableToForm(){
  // Reads through generated table and saves to a php form which is submitted to save values to a mysql table
  var table = document.getElementById('Listable');
  // Start on row 1 (SKIP HEADER ROW), rowLength - 1 (SKIP FOOTER ROW)
  var labels = new Array("AM","PM","First","Last","Pickup","Phone","Package","Order","Waiver","Product","Bus","All_Area","Beg","BRD","SKI","LTS","LTR","Prog_Lesson");
  var form = "<form name='js_save' id='js_save' method='post' action='save.php'>";
  var trip = document.getElementById("trip").value;
  for(var rowCounter = 1, rowLength = table.rows.length; rowCounter < rowLength - 1; rowCounter++ ){
    var id = table.rows[rowCounter].cells[0].children[0].name;
    id = id.split(":");
    id = id[0]+":"+id[1];
    var prefix = id.substring(0,2);
    for(var cellCounter = 0, cellLength = table.rows[rowCounter].cells.length; cellCounter < cellLength; cellCounter++){
      if(prefix == "WO"){
        if(labels[cellCounter] == "First" || labels[cellCounter] == "Last" || labels[cellCounter] == "Pickup" || labels[cellCounter] == "Phone" || labels[cellCounter] == "Package"){
          form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].innerText+"'>";
        }
        else if(labels[cellCounter] == "Order"){
          form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].innerText+"'>";
          var select = document.getElementById("trip");
          form += "<input type='hidden' name='"+id+":Trip' value='"+select.options[select.selectedIndex].value+"'>";
        }
        else{
          form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].children[0].checked+"'>";
        }
      }
      else{
        // Only capture checkboxes for woocommerce orders
        if(labels[cellCounter] != "First" && labels[cellCounter] != "Last" && labels[cellCounter] != "Pickup" && labels[cellCounter] != "Phone" && labels[cellCounter] != "Package" && labels[cellCounter] != "Order"){
          form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].children[0].checked+"'>";
        }
      }
    }
  }
  form += "</form>";
  $("body").append(form);
  document.getElementById("js_save").submit();
}

// Chained drop downs
$("#trip").chained("#destination");
// Dynamically add
$('#add').click(function(){
  // Find total cell and increment
  var cell = document.getElementById('total_guests');
  total = Number(cell.innerHTML) + 1;
  cell.innerHTML = total;

  //Generate Walk On order #
  var itemNum = Math.floor(Math.random()*90000);
  var order = 'WO'+ Math.floor(Math.random()*90000);
  var id = order+":"+itemNum;
  var row = '<tr><td><input type="checkbox" name="'+id+':AM"></td><td><input type="checkbox" name="'+id+':PM"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td class="no-edit">'+order+'</td><td><input type="checkbox" name="'+id+':Waiver"></td><td><input type="checkbox" name="'+id+':Product"></td><td><input type="checkbox" name="'+id+':Bus"></td><td><input type="checkbox" name="'+id+':All_Area"></td><td><input type="checkbox" name="'+id+':Beg"></td><td><input type="checkbox" name="'+id+':BRD"></td><td><input type="checkbox" name="'+id+':SKI"></td><td><input type="checkbox" name="'+id+':LTS"></td><td><input type="checkbox" name="'+id+':LTR"></td><td><input type="checkbox" name="'+id+':Prog_Lesson"></td></tr>',
  $row = $(row),
  // resort table using the current sort; set to false to prevent resort, otherwise 
  // any other value in resort will automatically trigger the table resort. 
  resort = true;
  $('#Listable')
    .find('tbody').append($row)
    .trigger('addRows', [$row, resort]);
  return false;
 });

 $('#remove').click(function(){
   // TODO: limit this to cells added by the add function above, maybe check first cell name for WO order?
   $('#Listable tbody tr:last').remove();
   var total_cell = document.getElementById('total_guests');
   total = Number(total_cell.innerHTML) - 1;
   total_cell.innerHTML = total;
   $('#Listable').trigger("update");
 });
$(function(){
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
      widgets : [ 'editable','zebra', 'columns','stickyHeaders','filter'],
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
      widgets : [ 'editable','zebra', 'columns','stickyHeaders','filter'],
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
});
