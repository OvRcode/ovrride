/**
*  OvR Lists - Custom JavaScript
*
*/

// Order Status: Check All / Uncheck All
function checkAll(formname, checktoggle) {
  var checkboxes = [];
  checkboxes = document[formname].getElementsByTagName('input');

  for (var i=0; i<checkboxes.length; i++) {
    if (checkboxes[i].type == 'checkbox') {
      checkboxes[i].checked = checktoggle;
    }
  }
}

// Tell tablesorter to sort the table
$(function(){
  $("#Listable").tablesorter();
});