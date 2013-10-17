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
    var row = '<tr><td></td><td></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td contenteditable="true"></td><td class="no-edit">'+order+'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>',
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
