$(function(){
  bounceToIndex();
  $("#messageText").on("keyup", function(){
    var text = $(this).val().length + " /119 Characters";
    $("span.charCount").text(text);
  }); 
  
  sortPhoneNumbers();
  populateGuests();
  toggleSend();
  $("#messageText").on("keyup", function(){
    if ( $(this).val().length === 0 && !$("#sendMessage").hasClass('disabled') ){
      toggleSend();
    } else if ( $(this).val().length > 0 && $("#sendMessage").hasClass('disabled') ) {
      toggleSend();
    }
  });
  $("#sendMessage").on("click", function(){
    sendMessage();
  });
  $("input[name=messageType][value=All]").on("click",function(){
    $("#Pickups").val("none");
    $("#Guests").val("none");
  });
  
  if ( settings.get('Pickup') == "1" ) {
    populatePickups();
  } else {
    $("#Pickups").append('<option value="none" selected>Pickups Disabled</option>');
    $('input[name=messageType][value=Pickup]').prop('disabled','disabled');
    $("#Pickups").prop('disabled','disabled');
  }
});
function sendMessage(){
  var recipients = [];
  var target = $("input[name=messageType]:checked").val();
  var messageData = {};
  messageData.Message = $("#messageText").val();
  if ( target == "All" ) {
    jQuery.each(messages.get('phoneData'), function(Name, data){
      recipients.push(data.Phone);
      messageData.Group = "All";
    });
  } else if ( target == "Pickup" ) {
    var pickup = $("#Pickups").val();
    jQuery.each(messages.get('phoneData'), function(Name, data){
      if ( data.Pickup == pickup) {
        recipients.push(data.Phone);
        messageData.Group = pickup;
      }
    });
  } else if ( target == "Single" ) {
    var phoneData = messages.get('phoneData');
    recipients.push(phoneData[$("#Guests").val()].Phone);
    messageData.Group = $("#Guests").val();
  }
  messageData.Recipients = recipients;
  messageData.Bus = settings.get('bus');
  messageData.Trip = settings.get('tripNum');
  alert("Sending messages");
  $.post("api/message", {message: messageData}).done(function(data){
    alert("Message Sent");
    $("#messageText").val("").trigger("keyup");
    $("#Guests").val("none").trigger("change");
    $("#Pickups").val("none").trigger("change");
  });
}
function toggleSend(){
  var button = $("#sendMessage");
  if ( button.hasClass('disabled') ) {
    button.removeClass('disabled');
  } else {
    button.addClass('disabled');
  }
}
function populatePickups(){
  var htmlOutput = "<option value='none' selected>Pickup Location</option>";
  var phoneData = messages.get('phoneData');
  var pickups = {};
  jQuery.each(phoneData, function(Name, data){
    if ( pickups[data.Pickup] === undefined ) {
      pickups[data.Pickup] = [];
      htmlOutput = htmlOutput.concat("<option value='" + data.Pickup + "'>"+data.Pickup+"</option>");
    }
    if ( pickups[data.Pickup].indexOf(data.Phone) == -1 ) {
      pickups[data.Pickup].push(data.Phone);
    }
  });
  $("#Pickups").append(htmlOutput);
  messages.set('pickups', pickups);
  $("#Pickups").on("change", function(){
    $("#Guests").val("none");
    $("input[name=messageType][value=Pickup]").click();
  });
}
function populateGuests(){
  var htmlOutput = "<option value='none' selected> Single Guest</option>";
  var phoneData = messages.get('phoneData');
  jQuery.each(phoneData, function(Name, data){
    htmlOutput = htmlOutput.concat("<option value='" + Name + "'>" + Name + "</option>");
  });
  $("#Guests").append(htmlOutput);
  $("#Guests").on("change", function(){
    $("#Pickups").val("none");
    $("input[name=messageType][value=Single]").click();
  });
}
function sortPhoneNumbers(){
  var phoneData = {};
  jQuery.each(orders.keys(), function(key, value){
    var orderData = orders.get(value);
    var Name = orderData.First + " " + orderData.Last;
    if ( jQuery.isEmptyObject(phoneData[Name]) ) {
      phoneData[Name] = {};
      phoneData[Name].Phone = orderData.Phone;
      if ( orderData.Pickup ) {
        phoneData[Name].Pickup = orderData.Pickup;
      }
    }
  });
  messages.set('phoneData', phoneData);
}