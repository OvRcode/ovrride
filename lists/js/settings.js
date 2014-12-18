function tripDropdown(){
    $.get("/api/dropdown/trip", function(data){
            $('#trip').append(data); 
    })
    .done(function(data){
        $('#trip').chained("#destination");
        window.dd.set('trip',data);
    })
    .fail(function(){
        alert('Trip dropdown failed to load, please refresh page');
    });
}
function saveOptions(){
    window.settings.set('destination', $('#destination').val());
    var trip = $('#trip');
    window.settings.set('tripNum', $('#trip').val());
    window.settings.set('tripName', $('#trip option:selected').text());
    window.settings.set('bus', $('#bus').val());
    // All Checkboxes on settings page w/selectors
    var checkBoxes = {
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
    // Array to be filled with active checkbox values then concatenated with , seperator
    window.activeBoxes = []
    jQuery.each(checkBoxes, function(index, value){
        if ( value.prop('checked') ){
            window.activeBoxes[window.activeBoxes.length] = value.val();
        }
    });
    window.settings.set('status', window.activeBoxes.join());
}
/* Menu JS */
$("#menu-toggle").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
$("#btn-hide").click(function(e) {
    e.preventDefault();
    $("#wrapper").toggleClass("toggled");
});
$('#btn-settings').click(function(){
   window.location.href='index.html';
})
$('#btn-list').click(function(){
   window.location.href='list.html';
})
/* End Menu JS */
/* Setup Namespace storage */
window.dropDown = $.initNamespaceStorage('dropdown');
window.dd = dropDown.localStorage; 
window.options = $.initNamespaceStorage('settings');
window.settings = options.localStorage; 
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
