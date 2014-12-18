function tripDropdown(){
    $.get("/api/dropdown/trip", function(data){
            $('#trip').append(data); 
    })
    .done(function(){
        $('#trip').chained("#destination");
    })
    .fail(function(){
        alert('Trip dropdown failed to load, please refresh page');
    });
}
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
$.get("api/dropdown/destination", function(data){
    $('#destination').append(data);  
    })
    .done(function(){
        tripDropdown();
    })
    .fail(function(){
        alert('Destination data failed to load, please refresh page');
    });