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
  checkAll('trip_list', false);
  var select_element = document.getElementById("trip");
  select_element.selectedIndex=0;
  checkboxes = document.trip_list.getElementsByTagName('input');
  checkboxes[0].checked = true;
  checkboxes[1].checked = true;
  document.trip_list.submit();
}
// Tell tablesorter to sort the table
$(function(){
  $("#Listable").tablesorter();
});