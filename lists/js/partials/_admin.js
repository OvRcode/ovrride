/*jshint multistr: true */
$(function(){
    // Enable popovers
    $("[data-toggle=popover]").popover();
    
    // Add listeners when popover is shown
    $('[data-toggle="popover"]').on('shown.bs.popover', function(){
        checkDestPopover();
        $("#destName").on("keyup", function(){
            checkDestPopover();
        });
        $("#addDestBtn").on("click",function(){ saveDestination(); });
    });
    // unbind popover listeners when closed
    $('[data-toggle="popover"]').on('hide.bs.modal', function(){
        $("#destName").unbind("keyup");
        $("#addDestBtn").unbind("click");
    });
    $("#exportList").on("click", function(){
      window.location.href = "api/csv/list/" + settings.get('tripNum') + "/" + settings.get("status");
    });
    $("#exportEmail").on("click", function(){
      window.location.href = "api/csv/email/" + settings.get('tripNum') + "/" + settings.get("status");
    });
    getDestinations();
    if ( jQuery.browser.mobile ) {
      $("#exportList").hide();
      $("#exportEmail").hide();
    }
});
function saveDestination(){
    $.post("api/dropdown/destination/save", { destination: $("#destName").val() })
      .done(function(){
        location.reload();
      });
    
}
function checkDestPopover(){
    var destName = $("#destName").val();
    if (  destName === "" ) {
        $("#addDestBtn").addClass('disabled');
    } else if ( $("#addDestBtn").hasClass('disabled') ) {
        $("#addDestBtn").removeClass('disabled');
    }
}
function getDestinations(){
    $.getJSON( "api/dropdown/destination/all", function(data){
        window.destOutput = "<table id='destTable'>\
                <thead><tr><th></th><th>Enabled</th><th>Disabled</th><th>Remove</th><th>Contact</th>\
                <th>Contact Phone</th><th>Rep</th><th>Rep Phone</th></tr></thead>\
                <tbody>";
        jQuery.each(data, function(key,value){
            var row = "<tr class='" + key +"'><td>" + key + " </td>";
            if ( value.enabled == "Y" ){
                row = row.concat('<td><input type="radio" value="Y" name="' + key + '" checked></td>');
            } else {
                row = row.concat('<td><input type="radio" value="Y" name="' + key + '"></td>');
            }
            if ( value.enabled == "N" ){
                row = row.concat('<td><input type="radio" value="N" name="' + key + '" checked></td>');
            } else {
                row = row.concat('<td><input type="radio" value="N" name="' + key + '"></td>');
            }
            row = row.concat('<td><input type="radio" value="Delete" name="' + key + '"></td>');
            row = row.concat('<td><input type="text" size="15" class="contact" value="' + value.contact + '" placeholder="Contact"></td>');
            row = row.concat('<td><input type="text" size="13" class="contactPhone" value="' + value.contactPhone + '" placeholder="Contact Phone"></td>');
            row = row.concat('<td><input type="text" size="15" class="rep" value="' + value.rep + '" placeholder="Rep"></td>');
            row = row.concat('<td><input type="text" size="13" class="repPhone" value ="' + value.repPhone + '" placeholder="Rep Phone"></td></tr>');
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
        $("#destinations").append(destOutput);
        $("#saveDest").on("click", function(){ 
            updateDestinations();
        });
    });
}
function updateDestinations(){
    var destinations = $("#destTable tbody tr");
    window.destinationData = {};
    jQuery.each(destinations, function(key, value){
      var destination = $(this).attr('class'); 
      destinationData[destination] = {};
      destinationData[destination].enabled = $("tr[class='" + destination + "'] input:radio:checked").val();
      destinationData[destination].contact = $(this).find("input.contact").val();
      destinationData[destination].contactPhone = $(this).find("input.contactPhone").val();
      destinationData[destination].rep = $(this).find("input.rep").val();
      destinationData[destination].repPhone = $(this).find("input.repPhone").val();
    });
    $.post("api/dropdown/destination/update", {data: destinationData})
      .done(function(){
        location.reload();
      });
}