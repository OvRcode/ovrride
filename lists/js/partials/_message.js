$(function(){
  $("#messageText").on("keyup", function(){
    var text = $(this).val().length + " /160 Characters";
    $("span.charCount").text(text);
  }); 
});
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
  console.log(phoneData);
}