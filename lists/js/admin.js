$(function(){
    //window.administration = $.initNamespaceStorage('admin');
    //window.admin = administration.localStorage;

    getDestinations();
});
function getDestinations(){
    $.getJSON( "api/dropdown/destination/all", function(data){
        window.destOutput = "<table id='destTable'>\
                <thead><tr><th></th><th>Enabled</th><th>Disabled</th></tr></thead>\
                <tbody>";
        jQuery.each(data, function(key,value){
            //console.log("Key: " + key + " Value: " + value);
            var row = "<tr><td>" + key + " </td>";
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
    });
}