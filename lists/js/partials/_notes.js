$(function(){
  if ( jQuery.browser.mobile && navigator.userAgent.match(/iPad/i) === null ){
    $("span.mobileButtons").removeClass('hidden');
  }
    //add check for online/offline when offline is implemented
    refreshNotes();
    $("#saveNote").click(function(){ saveNote(); });
    $("#refreshNotes").click(function(){ refreshNotes(); });
    $("#bus").click(function(){ toggleBus(); });
});
function toggleBus(){
    var bus = settings.get('bus');
    var selector = "div#notesContent :not(p:contains('Bus " + bus +"'))";
    var buttonValue = $("#bus").val();
    if ( buttonValue == "show" ) {
        $(selector).hide();
        $("#bus").val('hide');
        $("#bus").html('<i class="fa fa-bus"></i>&nbsp;Show All Buses');
    } else {
        $(selector).show();
        $("#bus").val("show");
        $("#bus").html('<i class="fa fa-bus"></i>&nbsp;This Bus Only');
    }
}
function saveNote(){
    var note = $("#newNote").val();
    var bus = settings.get('bus');
    var trip = settings.get('tripNum');
    var timestamp = timeStamp();
    
    if ( window.navigator.onLine ){
      onlineNoteSave(note,bus,trip);
    } else {
      notes.set(timestamp, "Bus " + bus + ": " + note);
      unsavedNotes.set(timestamp,1);
    }
    /*jshint -W030 */ 
    $("#notesContent").append("<p>" + timestamp + ": Bus " + settings.get('bus') + ": " + $("#newNote").val()).after() + "</p>";
    $("#newNote").val('');
}
function refreshNotes(){
    getNotes();
    setTimeout(outputNotes, 300);
}
function timeStamp(){
    var date    = new Date();
    var year    = date.getFullYear();
    var month   = date.getMonth().pad();
    var day     = date.getDay().pad();
    var hours   = date.getHours().pad();
    var minutes = date.getMinutes().pad();
    var seconds = date.getSeconds().pad();

    return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
}
function outputNotes(){
    $("#notesContent").empty();
    if (! jQuery.isEmptyObject(notes.keys()) ){
        var output = "";
        var allNotes = notes.keys();
        jQuery.each(allNotes, function(key, value){
            var note = notes.get(value);
            output = output.concat("<p>" + value + ": " + note + "</p>");
        });
        $("#notesContent").append(output);
    }   
}