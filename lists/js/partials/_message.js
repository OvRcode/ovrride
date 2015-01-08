$(function(){
  $("#messageText").on("keyup", function(){
    var text = $(this).val().length + " /160 Characters";
    $("span.charCount").text(text);
  }); 
  getPhoneNumbers();
  if ( settings.get('Pickup') == "1" ) {
    populatePickups();
  } else {
    $("#Pickups").append('<option value="none" selected>Pickups Disabled</option>');
    $('input[name=messageType][value=Pickup]').prop('disabled','disabled');
    $("#Pickups").prop('disabled','disabled');
  }
});
function populatePickups(){
  var htmlOutput = "<option value='none' selected>Pickup Location</option>";
  var phoneData = messages.get('phoneData');
  var pickups = {};
  jQuery.each(phoneData, function(phone, data){
    if ( pickups[data.Pickup] === undefined ) {
      pickups[data.Pickup] = [];
      htmlOutput = htmlOutput.concat("<option value='" + data.Pickup + "'>"+data.Pickup+"</option>");
    }
    if ( pickups[data.Pickup].indexOf(phone) == -1 ) {
      pickups[data.Pickup].push(phone);
    }
  });
  $("#Pickups").append(htmlOutput);
  messages.set('pickups', pickups);
  $("#Pickups").on("change", function(){
    $("input[name=messageType][value=Pickup]").click();
  });
}
function getPhoneNumbers(){
  var phoneData = {};
  jQuery.each(orders.keys(), function(key,value){
    var orderData = orders.get(value);
    if ( jQuery.isEmptyObject(phoneData[orderData.Phone]) ) {
      phoneData[orderData.Phone] = {};
      phoneData[orderData.Phone].First = orderData.First;
      phoneData[orderData.Phone].Last = orderData.Last;
      if ( orderData.Pickup ) {
        phoneData[orderData.Phone].Pickup = orderData.Pickup;
      }
    }
  });
  messages.set('phoneData', phoneData);
}