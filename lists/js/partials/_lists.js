/*jshint multistr: true */
$(function() {
    $("#saveList").on("tap", function(){
        saveData();
    });
    // Turn off tap event when taphold is triggered;
    $.event.special.tap.emitTapOnTaphold = false; 
    
    // Initialize Bootstrap popover function
    $("[data-toggle=popover]").popover();
    
    // Zoom to popover when shown
    $('[data-toggle="popover"]').on('shown.bs.popover', function(){
        $("#walkonPackage").append(dd.get('packages'));
        if ( !settings.isSet('Pickup') ){
            $("#pickupDiv").remove();
        } else {
            $("#pickup").change(function(){ addWalkonButton(); });
        }
        // WalkOn Order listeners
        $("#first").change(function(){ addWalkonButton(); });
        $("#last").change(function(){ addWalkonButton(); });
        $("#phone").change(function(){ addWalkonButton(); });
        $("#walkonPackage").change(function(){ addWalkonButton(); });
        $("#saveWalkOn").on("click", function(){
            saveWalkOn();
        });
        $("#sidebar-wrapper").animate({
            scrollTop: $("#walkon").offset().top
        },1000);
    });
    
    // Unbind change listeners when popover is hidden
    $('[data-toggle="popover"]').on('hide.bs.modal', function(){
        $("#first").unbind("change");
        $("#last").unbind("change");
        $("#phone").unbind("change");
        $("#walkonPackage").unbind("change");
        $("#otherPackage").unbind("change");
        $("#saveWalkon").unbind("click");
    });
    // Show/Hide Records with AM/PM Toggle button
    var html;
    $("#AMPM").on("click", function(){
        if ( $(this).val() == "AM") {
            $('.listButton.bg-none').addClass('hidden');
            html = '<i class="fa fa-sun-o fa-lg"></i>&nbsp;\
                            <i class="fa fa-toggle-on fa-lg"></i>&nbsp;\
            <i class="fa fa-moon-o fa-lg"></i>';
            $(this).val("PM");
            $(this).html(html);
            $(this).addClass('btn-black').removeClass('btn-default');
        } else {
            $('.listButton.bg-none').removeClass('hidden');
            $(this).val("AM");
            html = '<i class="fa fa-sun-o fa-lg"></i>&nbsp;\
                            <i class="fa fa-toggle-off fa-lg"></i>&nbsp;\
            <i class="fa fa-moon-o fa-lg"></i>';
            $(this).html(html);
            $(this).addClass('btn-default').removeClass('btn-black');
        }
    });
    
    // List sorting dropdown
    $("#sortBy").on("change", function(){
        sortList($(this).val());
    });
    
    // Search Type listener
    $("#searchType li a").on("click", function(){
        var value = $(this).text();
        var placeholder = "";
        var target = $("#searchButton");
        if ( value == "Clear Search" ) {
            value = "Search By:";
            placeholder = "Choose Search field first";
            $("#searchField").val('');
        } else {
            placeholder = "Search here";
        }
        target.val(value).text(value);
        $("#searchField").prop('placeholder', placeholder);
        
        // show any hidden entries
        $(".listButton").show();
    });
    $("#searchField").on("keyup", function(){
        var button = $("#searchButton").val();
        if ( button !== "" && button !== "Search By:"){
            searchList(button, $(this).val());
        } else {
            alert("Choose search type first");
        }
    });
    setupPage();
    checkData();
    setupAllListeners();
    packageList();
});
function saveData(){
    // get state data from localstorage
    var packageWeight = {AM: 1, Waiver: 2, Product: 3, PM: 4, NoShow: 5,Delete: 6};
    var allDataKeys = tripData.keys();
    var orderLocalData = {};
    jQuery.each(allDataKeys, function(key,value){
        var split = value.split(":");
        var ID = split[0] + ":" + split[1];
        var valueName = split[2];
        // Check for delete walkon
        if ( value.substring(0,2) == "WO" && valueName == "Delete" && tripData.get(value) == "Delete" ) {
            $.post("api/walkon/delete/"+ID);
        }
        // Check if object is setup
        if ( jQuery.isEmptyObject(orderLocalData[ID]) ) {
            orderLocalData[ID] = { Trip: settings.get('tripNum'), Bus: settings.get('bus'), Data:""};
        }
        if ( orderLocalData[ID].Data === "" || packageWeight[valueName] > packageWeight[orderLocalData[ID].Data]) {
            orderLocalData[ID].Data = valueName;
        }
    });
    
    // get walkon data if not previously saved
    if ( newWalkon.keys().length > 0 ){
        var walkonData = {};
        jQuery.each(newWalkon.keys(), function(key,value){
             walkonData[value]= orders.get(value);
            walkonData[value].Bus = settings.get('bus');
            walkonData[value].Trip = settings.get('tripNum');
        });
        $.post("api/save/walkon", {walkon: walkonData}, function(){
            newWalkon.removeAll();
        });
    }
    $.post("api/save/data", {data: orderLocalData}, function(){
        tripData.removeAll();
        getTripData();
    });
}
function searchList(searchType, text){
    //TODO: Look at options for case insensitivity 
    var match;
    var search;
    // Skip blank searches
    if ( text !== ""){
        if ( searchType == "Name" ){
            search = $(".listButton div.name:contains('" + text + "')");
        } else if ( searchType == "Email" ) {
            search = $(".listButton span.email:contains('" + text + "')");
        } else if ( searchType == "Phone" ) {
            search = $(".listButton span.phone:contains('" + text + "')");
        } else if ( searchType == "Order" ) {
            search = $(".listButton span.orderNum:contains('" + text + "')");
        } else if ( searchType == "Package" ){
            search = $(".listButton div.flexPackage:contains('" + text + "')");
        }
        showSearchResults(search);
        // END OF SEARCH TYPES
    } else {
        $(".listButton").show();
    }
}
function showSearchResults(targets){
    $(".listButton").hide();
    targets.parents('.listButton').show();
}
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
    tripData.set(ID + ":Delete", 1);
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
        var Delete = element.attr('id') + ":Delete";
        var Bus = element.attr('id')+":Bus";
        tripData.set(AM, 1 );
        tripData.remove(Delete);
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
        if ( settings.get('bus') !== "All" ){
          element.children("div.row.primary").children().not(".noClick").on("tap", function(){
            changeStatus($(this).parents().eq(1));
          });
        }
    } else {
        element.children('div.expanded').show(600);
        element.children("div.row.primary").children().not(".noClick").unbind("tap");
    }
}
function setupAllListeners(){
    jQuery.each(orders.keys(), function(key, value){
        setupListener(value);
    });
}
function setupListener(ID){
    var split = ID.split(":");
    var selectorID = "#" + split[0] + "\\:" + split[1];
    if ( ID.substring(0,2) == "WO" ){
        $(selectorID + "\\:Delete").click(function(){
            tripData.set(split[0]+":"+split[1]+":Delete", "Delete");
            $(this).parents('.listButton').remove();
        });
    }
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
    if ( settings.get('bus') != "All" ){
        $("#" + split[0] + "\\:" + split[1] + " div.row.primary").children().not(".noClick").on("tap", function(){
            changeStatus($(this).parents().eq(1));
        });
    }
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
function packageList(){
    window.packageList = [];
    var output = "<option value='none' selected>Select Package</option>";
    // Identify unique package values
    jQuery.each(orders.keys(), function(key,value){
        var currentOrder = orders.get(value);
        if ( packageList.indexOf(currentOrder.Package) == -1 ) packageList.push(currentOrder.Package);
    });
    packageList.push("Other");
    // Output entries for select
    jQuery.each(packageList, function(key,value){
        output = output.concat("<option value='" + value + "'>" + value + "</option>");
    });
    dd.set('packages',output);
}
function addWalkonButton(){
    var walkonPackage = $("#walkonPackage").val();
    if ( walkonPackage == "Other" && $("#otherPackage").val() === undefined ) {
        var html = "<div id='otherDiv'><input id='otherPackage' type='text' class='input-sm' placeholder='Input Package'></input><br /><br /></div>";
        $(html).insertBefore("#saveWalkOn");
        $("#otherPackage").change(function(){ addWalkonButton(); });
    } else if ( walkonPackage !== "Other" && $("#otherPackage").val() !== undefined ) {
        $("#otherPackage").unbind("change");
        $("#otherDiv").remove();
    }
    var first         = $("#first").val();
    var last          = $("#last").val();
    var phone         = $("#phone").val();
    var otherPackage  = $("#otherPackage").val();
    var pickup;
    if ( settings.isSet('Pickup') ) {
        pickup = $("#pickup").val();
    } else {
        pickup = "none";
    }

    if ( first !== "" && last !== "" && phone !== "" && pickup !== "" && 
    ((walkonPackage == "Other" && otherPackage !== "" && otherPackage !== undefined ) || 
    ( walkonPackage !== "Other" && walkonPackage !== "none"))) {
        $("#saveWalkOn").removeClass('disabled');
    } else if ( ! $("#saveWalkon").hasClass('disabled') ) {
        $("#saveWalkOn").addClass('disabled');
    }
    
}
function saveWalkOn(){
    // Saves to local storage
    var walkonPackage = $("#walkonPackage");
    if ( walkonPackage.val() == "Other" ) {
        walkonPackage = $("#otherPackage").val();
    } else {
        walkonPackage = walkonPackage.val();
    }
    var orderNum = Math.floor((Math.random() * 99999) + 1);
    orderNum = "WO" + String(orderNum.pad(4));
    var orderItem = Math.floor((Math.random() * 99999) + 1);
    orderItem = orderItem.pad(4);
    var ID = orderNum + ":" + orderItem;
    var walkOn = {First: $("#first").val(),
                  Last: $("#last").val(),
                  Phone: $("#phone").val(),
                  Package: walkonPackage};
    if( settings.isSet('Pickup') ) {
        walkOn.Pickup = $("#pickup").val();
    }
    listHTML(ID, walkOn);
    orders.set(ID,walkOn);
    newWalkon.set(ID,"unsaved");
    $("#addWalkOn").popover('toggle');
}
function listHTML(ID, order){
    var split = ID.split(":");
    var output = "<div class='row listButton bg-none' id='" + ID + "'>\
                      <div class='row primary'>\
                          <div class='buttonCell name col-xs-7 col-md-4'>\
                              <span class='icon'></span>\
                              <span class='first'>&nbsp;" + order.First + "</span>\
                              <span class='last'>" + order.Last + "</span>\
                          </div>\
                          <div class='noClick buttonCell col-md-2 visible-md visible-lg'>\
                            Order: <span class='orderNum'>" + split[0] + "</span></div>";
    if( settings.isSet('Pickup') ) {
        var pickupHTML = '<div class="buttonCell col-xs-5 col-md-3 flexPickup">' + order.Pickup + '</div>';
        output = output.concat(pickupHTML);
    }
    var packageHTML = "<div class='buttonCell col-xs-5 col-md-3 flexPackage visible-md visible-lg'>\
                        " + order.Package + "</div>\
                    </div>\
                    <div class='expanded'>\
                        <div class='row'>\
                            <div class='buttonCell col-xs-5 col-md-6'>\
                            <strong>Package:</strong> " + order.Package + "</div>";
    output = output.concat(packageHTML);
    if ( settings.isSet('Pickup') ) {
        var packageHTML2 = "<div class='buttonCell col-xs-12 col-md-6'>\
                            <strong>Pickup:</strong> " + order.Pickup + "</div>";
        output = output.concat(packageHTML2);
    }
    var expandedRemainder = "</div>\
                <div class='row'>\
                    <div class='buttonCell col-xs-12 col-md-6'>\
                        <strong>Order:</strong>" + split[0] + " </div>\
                    <div class='buttonCell col-xs-12 col-md-6'>\
                    <strong>Phone:</strong> <a href='tel:" + order.Phone + "'><span class='phone'>" + order.Phone + "</span></a> \
                </div>\
              </div>\
              <div class='row'>\
                <br />\
                <div class='buttonCell col-xs-4'>\
                    <button class='btn btn-info' id='" + ID +":Reset'>\
                        Reset\
                    </button>\
                </div>\
                <div class='buttonCell col-xs-4'>\
                    <button class='btn btn-warning' id='" + ID + ":NoShow'>\
                        No Show\
                    </button>\
                </div>";
    if ( ID.substring(0,2) == "WO" ) {
        expandedRemainder = expandedRemainder.concat('<div class="buttonCell col-xs-4"><button class="btn btn-danger" id="' + ID + ':Delete">Remove Order</button></div>');
    }
    expandedRemainder = expandedRemainder.concat("</div></div></div>");
    output = output.concat(expandedRemainder);
    $("#content").append(output);
    initialHTML.set(ID, output);
    setupListener(ID);
    // Hide expanded area of reservation
    $("div.expanded").hide();
}
function sortList(value){
    switch(value){
        case "Faz":
            // First Name Ascending
            $(".listButton").tsort('span.first',{order: 'asc'});
            break;
        case "Fza":
            // First Name Descending
            $(".listButton").tsort('span.first',{order:'desc'});
            break;
        case "Laz":
            // Last Name Ascending
            $(".listButton").tsort('span.last',{order: 'asc'});
            break;
        case "Lza":
            // Last Name Descending
            $(".listButton").tsort('span.first',{order: 'desc'});
            break;
        case "Paz":
            // Package Ascending
            $(".listButton").tsort('div.flexPackage',{order:'asc'});
            break;
        case "Pza":
            // Package Descending
            $(".listButton").tsort('div.flexPackage',{order:'desc'});
            break;
        case "Piaz":
            // Pickup Ascending
            $(".listButton").tsort('div.flexPickup',{order:'asc'});
            break;
        case "Piza":
            // Pickup Descending
            $(".listButton").tsort('div.flexPickup',{order:'desc'});
            break;
        case "Oza":
            // Order Descending
            $(".listButton").tsort('span.orderNum', {order:'desc'});
            break;
        default:
            // Order Ascending
            $(".listButton").tsort('span.orderNum', {order:'asc'});
            break;
    }
}