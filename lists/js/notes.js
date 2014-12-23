function outputNotes(){
    if (! jQuery.isEmptyObject(notes.keys()) ){
        var output = "<h3>Notes</h3>";
        var allNotes = notes.keys();
        jQuery.each(allNotes, function(key, value){
            var note = notes.get(value);
            output = output.concat("<p>" + value + ": " + note + "</p>");
        });
        $("#content").append(output);
    }
    
}