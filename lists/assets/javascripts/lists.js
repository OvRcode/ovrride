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
  $('table').tablesorter({
    sortForce : [[2,0] ],
    sortList : [[2,0] ], // This is supposed to sort the 3rd column 'First Name'. But it's not working
    widgets : [ 'zebra', 'columns' ]
  });
});
