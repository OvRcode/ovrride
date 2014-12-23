$(function() {
    window.dropDown = $.initNamespaceStorage('dropdown');
    window.dd = dropDown.localStorage; 
    window.options = $.initNamespaceStorage('settings');
    window.settings = options.localStorage; 
    window.orderData = $.initNamespaceStorage('orders');
    window.orders = orderData.localStorage;
    window.outputHTML = $.initNamespaceStorage('initialHTML');
    window.initialHTML = outputHTML.localStorage;  
    window.data =$.initNamespaceStorage('data');
    window.tripData = data.localStorage; 
    window.notespace = $.initNamespaceStorage('notes');
    window.notes = notespace.localStorage;
    // Menu JS
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    $("#btn-hide").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    
    $( '#btn-settings' ).on("tap", function(){ window.location.href='index.html' });
    $( '#btn-list' ).on("tap", function(){ window.location.href='list.html' });
    $( '#btn-summary' ).on("tap", function(){ window.location.href = 'summary.html' });
    $( '#btn-notes' ).on("tap", function(){ window.location.href = 'notes.html' });
    $( '#btn-message' ).on("tap", function(){ window.location.href = 'message.html' });
    $( '#btn-export' ).on("tap", function(){ window.location.href = 'export.html' });
    $( '#btn-logout' ).on("tap", function(){ /*TODO: implement login/logout */ });
});

function getNotes(){
    notes.removeAll();
    var trip = settings.get('tripNum');
    $.get("/api/notes/"+trip, function(data){
        var parsed = jQuery.parseJSON(data);
        jQuery.each(parsed, function(key,value){
            notes.set(key, value);
        });
    });
}