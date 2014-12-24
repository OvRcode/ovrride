$(function() {
    // Turn off tap event when taphold is triggered;
    $.event.special.tap.emitTapOnTaphold = false; 
    
    /*$('#AMPM').change(function(){
        if ( $(this).val() == 'PM' ) {
            $('.listButton.bg-none').addClass('hidden');
        } else if ( $(this).val() == 'AM' ) {
            $('.listButton.bg-none').removeClass('hidden');
        }
    });*/
    $("#AMPM").on("click", function(){
        if ( $(this).val() == "AM") {
            $('.listButton.bg-none').addClass('hidden');
            var html = '<i class="fa fa-sun-o fa-lg"></i>&nbsp;\
                            <i class="fa fa-toggle-on fa-lg"></i>&nbsp;\
            <i class="fa fa-moon-o fa-lg"></i>';
            $(this).val("PM");
            $(this).html(html);
        } else {
            $('.listButton.bg-none').removeClass('hidden');
            $(this).val("AM");
            var html = '<i class="fa fa-sun-o fa-lg"></i>&nbsp;\
                            <i class="fa fa-toggle-off fa-lg"></i>&nbsp;\
            <i class="fa fa-moon-o fa-lg"></i>';
            $(this).html(html);
        }
    });
    setupPage();
    checkData();
    setupListeners();
});
function setupPage(){
    if ( window.settings.isSet('tripName') ) {
        $('#tripName').text(window.settings.get('tripName'));
    }
    if ( settings.isSet('bus') ) {
        $('#bus').text(settings.get('bus'));
    }
    var keys = initialHTML.keys();
    jQuery.each(keys, function(key,value){
        $('#content').append(initialHTML.get(value));
    });
    // Hide expanded area of reservation
    $("div.expanded").hide();
}
function noShow(element) {
    var NoShow = element.attr('id')+":NoShow";
    tripData.set(NoShow, 1);
    element.addClass('bg-noshow');
    element.find("span.icon").html('<i class="fa fa-exclamation-triangle fa-lg"></i>');
}
function resetGuest(element){
    var ID = element.attr('id');
    var clearVars = [ ID + ":AM", ID + ":PM", ID + ":Waiver", ID + ":Product", ID + ":NoShow", ID + ":Bus" ];
    element.removeClass();
    element.addClass("row listButton bg-none");
    element.find("span.icon").html('');
    jQuery.each(clearVars, function(key,value){
        tripData.remove(value);
    });
}
function changeStatus(element){
    if ( $('#AMPM').val() == 'PM' ) {
        // Customer checked in at end of day
        element.removeClass('bg-productrec');
        element.addClass('bg-pm');
        element.find("span.icon").html('<i class="fa fa-moon-o fa-lg"></i>');
        var PM = element.attr('id')+":PM";
        tripData.set(PM, 1);
    }
    
    if ( element.hasClass('bg-none') && ! element.hasClass('bg-danger')) {
        // Customer Checked in
        var AM = element.attr('id') + ":AM";
        var Bus = element.attr('id')+":Bus";
        tripData.set(AM, 1 );
        tripData.set(Bus, settings.get('bus'));
        element.removeClass('bg-none');
        element.addClass('bg-am');
        element.find("span.icon").html('<i class="fa fa-sun-o fa-lg"></i>');
        element.find('.flexPackage').removeClass('visible-md visible-lg');
        element.find('.flexPickup').addClass('visible-md visible-lg');
    } else if ( element.hasClass('bg-am') ) {
        // Waiver Received from Customer
        var Waiver = element.attr('id')+":Waiver";
        tripData.set(Waiver, 1);
        element.removeClass('bg-am');
        element.addClass('bg-waiver');
        element.find("span.icon").html('<i class="fa fa-file-word-o fa-lg"></i>');
    } else if ( element.hasClass('bg-waiver') ) {
        // Customer received product
        var Product = element.attr('id')+":Product";
        tripData.set(Product, 1);
        element.removeClass('bg-waiver');
        element.addClass('bg-productrec');
        element.find('span.icon').html('<i class="fa fa-ticket fa-lg"></i>');
        element.find('.flexPackage').addClass('visible-md visible-lg');
        element.find('.flexPickup').removeClass('visible-md visible-lg');
    }

}
function toggleExpanded(element){
    if ( element.children('div.expanded').is(':visible') ){
        element.children('div.expanded').hide(600);
        element.children("div.row.primary").children().not(".noClick").on("tap", function(){
        changeStatus($(this).parents().eq(1));
        });
    } else {
        element.children('div.expanded').show(600);
        element.children("div.row.primary").children().not(".noClick").unbind("tap");
    }
}
function setupListeners(){
    jQuery.each(orders.keys(), function(key, value){
        var split = value.split(":");
        var selectorID = "#" + split[0] + "\\:" + split[1];
        // Click events for noshow/reset buttons
        $(selectorID + "\\:Reset").click(function(){
            resetGuest($(this).parents().eq(3));
        });
        $(selectorID + "\\:NoShow").click(function(){
            noShow($(this).parents().eq(3));
        });
        
       // Expand list entry by pressing and holding on entry (works on mobile and desktop)
        $( selectorID ).on("taphold", function(){
            toggleExpanded( $(this) );
        });
        
        /* Click events for listButton
            on small screens noClick class is ignored because links are not shown on button in regular list mode */
        /*if ($(window).width() < 970) {
            $("#" + split[0] + "\\:" + split[1]).on("tap", function(){
                changeStatus($(this));
            });
        } else {*/
            $("#" + split[0] + "\\:" + split[1] + " div.row.primary").children().not(".noClick").on("tap", function(){
                changeStatus($(this).parents().eq(1));
            });
            //}
        
    });
}
function setState(element, state){
    if ( state == 'AM' ){
        element.addClass('bg-am');
        element.find("span.icon").html('<i class="fa fa-sun-o fa-lg"></i>');
        element.removeClass('bg-none');
    } else if ( state == 'Waiver' ) {
        element.addClass('bg-waiver');
        element.find("span.icon").html('<i class="fa fa-file-word-o fa-lg"></i>');
        element.removeClass('bg-none');
    } else if ( state == 'Product' ) {
        element.addClass('bg-productrec');
        element.find('span.icon').html('<i class="fa fa-ticket fa-lg"></i>');
        element.removeClass('bg-none');
    } else if ( state == 'PM' ) {
        element.addClass('bg-pm');
        element.find("span.icon").html('<i class="fa fa-moon-o fa-lg"></i>');
        element.removeClass('bg-none');
    }
}
function checkData(){
    var localData = {};
    var states = { AM: 1, BUS: 0, Waiver: 2, Product: 3, PM: 4};
    if ( ! jQuery.isEmptyObject(tripData) ) {
        jQuery.each(tripData.keys(), function(key, value){
            var split = value.split(":");
            var ID = split[0] + ":" + split[1];

            if ( jQuery.isEmptyObject(localData[ID])) {
                localData[ID] = split[2];
            } else if ( states[localData[ID]] < states[split[2]] ) {
                localData[ID] = split[2];
            }
        });
        jQuery.each(localData, function(key, value){
            var selector = key.split(":");
            setState($("#" + selector[0] + "\\:" + selector[1]), value);
        });
    }
}