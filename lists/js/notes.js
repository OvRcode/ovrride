// Number padding for timestamp generation
Number.prototype.pad = function(size) {
      var s = String(this);
      while (s.length < (size || 2)) {s = "0" + s;}
      return s;
    }
$(function(){
    //add check for online/offline when offline is implemented
    //getNotes();
});
function timestamp(){
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