$(function(){
    getDestinations();
});
function getDestinations(){
    $.getJSON( "api/dropdown/destination/all", function(data){
        window.destOutput = "<table id='destTable'>\
                <thead><tr><th></th><th>Enabled</th><th>Disabled</th></tr></thead>\
                <tbody>";
        jQuery.each(data, function(key,value){
            //console.log("Key: " + key + " Value: " + value);
            var row = "<tr class='" + key +"'><td>" + key + " </td>";
            if ( value == "Y" ){
                row = row.concat('<td><input type="radio" value="Y" name="' + key + '" checked></td>');
            } else {
                row = row.concat('<td><input type="radio" value="Y" name="' + key + '"></td>');
            }
            if ( value == "N" ){
                row = row.concat('<td><input type="radio" value="N" name="' + key + '" checked></td>');
            } else {
                row = row.concat('<td><input type="radio" value="N" name="' + key + '"></td>');
            }
            destOutput = destOutput.concat(row);
        });
        var foot = "</tbody>\
                    <tfoot>\
                        <tr>\
                            <td><button class='btn btn-success' id='saveDest'><i class='fa fa-floppy-o'></i> Save Changes</button></td>\
                        </tr>\
                    </tfoot>\
                    </table>";
                    destOutput = destOutput.concat(foot);
        $("#content").append(destOutput);
        $("#saveDest").on("click", function(){ 
            console.log("clicked");
            updateDestinations() 
        });
    });
}
function updateDestinations(){
    var destinations = $("#destTable tbody tr");
    jQuery.each(destinations, function(key, value){
        var thisClass = $(this).attr('class');
        var radioValue = $("tr[class='" + thisClass + "'] input:radio:checked").val();
        $.post("api/dropdown/destination/update", { destination: thisClass, enabled: radioValue});
    });
}