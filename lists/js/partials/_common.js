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
    window.reportspace = $.initNamespaceStorage('reports');
    window.reports = reportspace.localStorage;
    window.walkonspace = $.initNamespaceStorage('newWalkon');
    window.newWalkon = walkonspace.localStorage;
    window.reportSaveSpace = $.initNamespaceStorage('unsavedReports');
    window.unsavedReports = reportSaveSpace.localStorage;
    window.messageSpace = $.initNamespaceStorage('messages');
    window.messages = messageSpace.localStorage;
    window.packageSpace = $.initNamespaceStorage('packages');
    window.packages = packageSpace.localStorage;

    // Menu JS
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    $("#btn-hide").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
    $( '#brand' ).on("click", function(){ window.location.href='index.php'; });
    $( '#btn-settings' ).on("click", function(){ window.location.href='index.php'; });
    $( 'button.btn-list' ).on("click", function(){ window.location.href='list.php'; });
    $( 'button.btn-summary' ).on("click", function(){ window.location.href = 'summary.php'; });
    $( 'button.btn-reports' ).on("click", function(){ window.location.href = 'reports.php'; });
    $( '#btn-message' ).on("click", function(){ window.location.href = 'message.php'; });
    $( '#btn-admin' ).on("click", function(){ window.location.href = 'admin.php'; });
    $( '#btn-logout' ).on("click", function(){ window.location.href= 'login/logout.php'; });
    if ( jQuery.browser.mobile ) {
      $(".navbar-static-top").addClass("iosFix");
      $(".sidebar-nav").addClass("iosFix");
    }
    // Monitor onLine status and flip navbar indicator
    setInterval(function () {
        var statusIcon = $("#status");
        if (window.navigator.onLine) {
            toggleMenuButtons("online");
            if ( statusIcon.hasClass('btn-danger') ) {
                statusIcon.removeClass('btn-danger')
                    .addClass('btn-black')
                    .html('<i class="fa fa-signal"></i> Online');
            }
        } else if (!window.navigator.onLine) {
            toggleMenuButtons("offline");

            if ( statusIcon.hasClass('btn-black') ) {
                statusIcon.removeClass('btn-black')
                    .addClass('btn-danger')
                    .html('<i class="fa fa-plane"></i> Offline');
            }
        }
    }, 250);


    // Notify user before reloading for update
    $(window.applicationCache).on("updateready", function(){
      var r = confirm("Update downloaded need to reload page. Press cancel if you need to save changes, OK to reload");
      if (r === true) {
        window.location.reload();
      }
    });
});
function getContactData(){
  var deferred = $.Deferred();
  var destination = settings.get('destination');
  $.getJSON("api/contact/destination/" + encodeURIComponent(destination), function(data){
    settings.set('contact', data.contact);
    settings.set('contactPhone', data.contactPhone);
    settings.set('rep', data.rep);
    settings.set('repPhone', data.repPhone);
  }).done(function(){
    deferred.resolve();
  });

  return deferred.promise();
}
function downloadReports(){
  var deferred = new $.Deferred();
  reports.removeAll();
  $.get("api/reports/" + settings.get('tripNum'), function(data){
    $.each( data, function(key,value){
        reports.set(key, {bus: value.Bus, report: value.Report});
    });
  }, "json")
  .success(function(){
    deferred.resolve();
  });

  return deferred.promise();
}
function getTripInfo() {
  var trip = settings.get('tripNum');
  var deferred = $.Deferred();
  $.get("api/trip/" + trip, function(data){
    settings.set('pickups', data.pickups);
    if ( ! $.isEmptyObject(data._wc_trip_primary_packages)) {
      packages.set(data._wc_trip_primary_packages.label, data._wc_trip_primary_packages.packages);
    }
    /*$.each(tempPackages, function( index, value){
      packages.set(value.label,value.packages);
    });*/
    deferred.resolve();
  }, "json");
  return deferred.promise();
}
function getTripData(){
    var trip = settings.get('tripNum');
    var statuses = settings.get('status');
    var bus = settings.get('bus');
    var destination = settings.get('destination');
    var deferred = $.Deferred();
    //Start with a clean slate
    orders.removeAll();
    initialHTML.removeAll();
    tripData.removeAll();
    newWalkon.removeAll();
    messages.removeAll();
    unsavedReports.removeAll();

    $.get("api/trip/" + trip + "/" + bus + "/" + statuses, function(data){
        var apiData = jQuery.parseJSON(data);
        if ( apiData ){
          jQuery.each(apiData, function(id,dataObject){
              jQuery.each(dataObject, function(key, value){
                  if ( key == 'Data' ){
                      orders.set(id,value);
                      if ( 'Pickup' in value ) {
                          settings.set('Pickup', 1);
                      } else if ( 'Transit To Rockaway' in value ) {
                          settings.set('Rockaway', 1);
                      }
                    } else if ( key == 'HTML' ){
                      initialHTML.set(id,value);
                    } else if ( key == 'State' && value !== null) {
                      tripData.set(id+":"+value, 1);
                    }
                  });
                });
        }
    })
    .done(function(){
      deferred.resolve();
    });

    return deferred.promise();
}
function toggleMenuButtons(onlineOffline){
    var buttons = ["#btn-settings","#saveList","#btn-message","#btn-admin","#btn-logout","#refreshReports"];
    if ( onlineOffline == "offline" ) {
        jQuery.each(buttons, function(key,value){
            if ( ! $(value).hasClass('disabled') ){
                $(value).addClass('disabled');
            }
        });
    } else {
        var currentPage = location.pathname.split('/').slice(-1)[0];
        currentPage = currentPage.split(".");
        currentPage = currentPage[0];
        jQuery.each(buttons, function(key,value){
            if ( $(value).hasClass('disabled') ) {
                $(value).removeClass('disabled');
                if ( (value == "#btn-settings" && (currentPage == "index" || currentPage === "")) ||
                     (value == "#btn-message" && currentPage == "message") ||
                     (value == "#btn-admin" && currentPage == "admin")){
                         $(value).addClass('disabled');
                }
            }
        });
    }
}
function bounceToIndex(){
  var bounce = false;
  switch ( window.location.pathname ){
    case "/":
      bounce = false;
      break;
    case "/index.php":
      bounce = false;
      break;
    case "/list.php":
      bounce = true;
      break;
    case "/summary.php":
      bounce = true;
      break;
    case "/reports.php":
      bounce = true;
      break;
    case "/message.php":
      bounce = true;
      break;
    case "/login/index.php":
      bounce = false;
      break;
    case "/login/logout.php":
      bounce = false;
      break;
    default:
      bounce = false;
      break;
  }
  if ( bounce && settings.isEmpty() ) {
    alert("Please select a trip first");
    window.location.href="index.php";
  }
}

// Number padding for timestamp generation
Number.prototype.pad = function(size) {
  var s = String(this);
  while (s.length < (size || 2)) {s = "0" + s;}
  return s;
};

function htmlEncode(value){
  return $('<div/>').text(value).html();
}
