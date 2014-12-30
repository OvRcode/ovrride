$(function(){
    // Enable popovers
    $("[data-toggle=popover]").popover();
    $('[data-toggle="popover"]').on('shown.bs.popover', function(){
        checkDestPopover();
        $("#destName").on("keyup", function(){
            checkDestPopover();
        });
        $("#addDestBtn").on("click",function(){ saveDestination() });
    });
    $('[data-toggle="popover"]').on('hide.bs.modal', function(){
        $("#destName").unbind("keyup");
        $("#addDestBtn").unbind("click");
    });
    getDestinations();
});
function saveDestination(){
    $.post("api/dropdown/destination/update", { destination: $("#destName").val(), enabled: "Y"});
    location.reload();
}
function checkDestPopover(){
    var destName = $("#destName").val();
    if (  destName == "" ) {
        $("#addDestBtn").addClass('disabled');
    } else if ( $("#addDestBtn").hasClass('disabled') ) {
        $("#addDestBtn").removeClass('disabled');
    }
}
function getDestinations(){
    $.getJSON( "api/dropdown/destination/all", function(data){
        window.destOutput = "<table id='destTable'>\
                <thead><tr><th></th><th>Enabled</th><th>Disabled</th><th>Remove</th></tr></thead>\
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
            row = row.concat('<td><input type="radio" value="Delete" name="' + key + '"></td>');
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
    location.reload();
}