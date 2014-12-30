$(function(){
  
    /* Start Drop Down population */
    $.get("api/dropdown/destination", function(data, dd){
            $('#destination').append(data); 
        })
        .done(function(data){
            window.dd.set('destination', data);
            // Download trips data if destinations load
            tripDropdown();
        })
        .fail(function(){
            alert('Destination data failed to load, please refresh page');
    }); 
    // All Checkboxes on settings page w/selectors
    window.checkBoxes = {
        "balance":    $("#balance"),
        "cancelled":  $("#cancelled"),
        "completed":  $("#completed"),
        "failed":     $("#failed"),
        "finalized":  $("#finalized"),
        "no-show":    $("#no-show"),
        "on-hold":    $("#on-hold"),
        "processing": $("#processing"),
        "pending":    $("#pending"),
        "refunded":   $("#refunded"),
        "walk-on":    $("#walk-on")
    };
    $('#default').click(function(){resetStatuses("Default")});
    $('#clear').click(function(){resetStatuses("All")});
    $('#settings_save').click(function(){
        saveOptions();
        getTripData();
    });
}); 

function tripDropdown(){
    $.get("api/dropdown/trip", function(data){
            $('#trip').append(data); 
    })
    .done(function(data){
        $('#trip').chained("#destination");
        window.dd.set('trip',data);
        checkSettings();
    })
    .fail(function(){
        alert('Trip dropdown failed to load, please refresh page');
    });
}
function checkSettings(){
    if ( settings.isSet('destination') ) {
        $('#destination').val(settings.get('destination')).trigger('change');
        if ( settings.isSet('tripNum') ) {
            $('#trip').val(settings.get('tripNum'));
        }
    }
    if ( settings.isSet('status') ) {
        var status = settings.get('status');
        var statusList = status.split(",");
        jQuery.each(statusList, function(index,value){
            $("input:checkbox[value="+value+"]").prop('checked','checked');
        });
    }
    if ( settings.isSet('bus') ) {
        $('#bus').val(settings.get('bus'));
    }
}
function saveOptions(){
    settings.removeAll();
    window.settings.set('destination', $('#destination').val());
    var trip = $('#trip');
    window.settings.set('tripNum', $('#trip').val());
    window.settings.set('tripName', $('#trip option:selected').text());
    window.settings.set('bus', $('#bus').val());
    
    // Array to be filled with active checkbox values then concatenated with , seperator
    window.activeBoxes = []
    jQuery.each(window.checkBoxes, function(index, value){
        if ( value.prop('checked') ){
            window.activeBoxes[window.activeBoxes.length] = value.val();
        }
    });
    window.settings.set('status', window.activeBoxes.join());
}
function resetStatuses(type){
    jQuery.each(window.checkBoxes, function(index,value){
        if ( type == 'All' || (type == 'Default' && (index != 'completed' && index != 'processing' && index != 'walk-on'))){
            value.prop('checked','');
        } else if ( type == 'Default'){
            value.prop('checked', 'checked');
        }
    });
}
function getTripData(){
    var trip = settings.get('tripNum');
    var statuses = settings.get('status');
    //Start with a clean slate
    window.orders.removeAll();
    window.initialHTML.removeAll();
    window.tripData.removeAll();
    $.get("api/trip/"+trip+"/"+statuses, function(data){
        var apiData = jQuery.parseJSON(data);
        jQuery.each(apiData, function(id,dataObject){
            jQuery.each(dataObject, function(key, value){
                if ( key == 'Data' ){
                    orders.set(id,value);
                    if ( 'Pickup' in value )
                        settings.set('Pickup', 1);
                } else {
                    initialHTML.set(id,value);
                }
            });
        });
    })
    .done(function(){
        window.location.href= "list.html";
    });
}