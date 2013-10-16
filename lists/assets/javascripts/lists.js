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
    widgets : [ 'zebra', 'columns' ]
  });
  $('#add').click(function(){
    var rand = Math.floor(Math.random()*90000);
    var order = 'WO'+ rand;
     var row = '<tr><td></td><td></td><td></td><td></td><td></td><td></td><td>'+order+'</td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>',
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
