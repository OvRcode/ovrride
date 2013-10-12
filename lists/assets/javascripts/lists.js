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
// Tell tablesorter to sort the table
$(function(){
  $("#Listable").tablesorter();
});