/**
*  OvR Lists - Custom JavaScript
*
*/

// Order Status: Check All / Uncheck All
function checkAll(formname, checktoggle) {
  var checkboxes = [];
  checkboxes = document.formname.getElementsByTagName('input');

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
    var id = table.rows[rowCounter].cells[7].innerText + ":" + table.rows[rowCounter].cells[7].children[0].value;
    form += "<input type='hidden' name='"+id+":trip' value='"+trip+"'>";
    for(var cellCounter = 0, cellLength = table.rows[rowCounter].cells.length; cellCounter < cellLength; cellCounter++){
      
      if(labels[cellCounter] == "First" || labels[cellCounter] == "Last" || labels[cellCounter] == "Pickup" || labels[cellCounter] == "Phone" || labels[cellCounter] == "Package"){
        form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].innerText+"'>";
      }
      else if(labels[cellCounter] == "Order"){
        form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].innerText+"'>";
        form += "<input type='hidden' name='"+id+":item_id' value='"+table.rows[rowCounter].cells[7].children[0].value+"'>";
      }
      else{
        form += "<input type='hidden' name='"+id+":"+labels[cellCounter]+"' value='"+table.rows[rowCounter].cells[cellCounter].children[0].checked+"'>";
      }
    }
  }
  form += "</form>";
  $("body").append(form);
  document.getElementById("js_save").submit();
  
}
// tablesorter configuration
// http://mottie.github.io/tablesorter/docs/#Configuration
$(function(){
  $('#Listable').tablesorter({
    sortList: [[4,0],[3,0]],
    widgets : [ 'editable','zebra', 'columns' ],
    widgetOptions: {
      editable_columns       : [2,3,4,5,6],  // point to the columns to make editable (zero-based index)
      editable_enterToAccept : true,     // press enter to accept content, or click outside if false
      editable_autoResort    : false,    // auto resort after the content has changed.
      editable_noEdit        : 'no-edit' // class name of cell that is no editable
    }
  });
  
  $('#add').click(function(){
    // Find total cell and increment
    var cell = document.getElementById('total_guests');
    total = Number(cell.innerHTML) + 1;
    cell.innerHTML = total;
    
    //Generate Walk On order #
    var rand = Math.floor(Math.random()*90000);
    var order = 'WO'+ rand;
    var row = '<tr><td><input type="checkbox" name="AM"></td><td><input type="checkbox" name="PM"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td class="no-edit">'+order+'<input type="hidden" name="item_id" value="'+order+'"></td><td><input type="checkbox" name="Waiver"></td><td><input type="checkbox" name="Product"></td><td><input type="checkbox" name="Bus"></td><td><input type="checkbox" name="All Area"></td><td><input type="checkbox" name="Beg"></td><td><input type="checkbox" name="BRD"></td><td><input type="checkbox" name="SKI"></td><td><input type="checkbox" name="LTS"></td><td><input type="checkbox" name="LTR"></td><td><input type="checkbox" name="Prog Lesson"></td></tr>',
    $row = $(row),
    // resort table using the current sort; set to false to prevent resort, otherwise 
    // any other value in resort will automatically trigger the table resort. 
    resort = true;
    $('#Listable')
      .find('tbody').append($row)
      .trigger('addRows', [$row, resort]);
    return false;
   });
});
