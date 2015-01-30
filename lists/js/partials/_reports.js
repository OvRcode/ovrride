$(function(){
  if ( jQuery.browser.mobile && navigator.userAgent.match(/iPad/i) === null ){
    $("span.mobileButtons").removeClass('hidden');
  }
    //add check for online/offline when offline is implemented
    refreshReports();
    $("#saveReport").click(function(){ saveReport(); });
    $("#refreshReports").click(function(){ refreshReports(); });
    $("#bus").click(function(){ toggleBus(); });
});
function outputReports(){
    $("#reportsContent").empty();
    if (! jQuery.isEmptyObject(reports.keys()) ){
        var output = "";
        var allReports = reports.keys();
        jQuery.each(allReports, function(key, value){
            var report = reports.get(value);
            output = output.concat("<p>" + value + ": " + report + "</p>");
        });
        $("#reportsContent").append(output);
    }   
}
function refreshReports(){
    getReports();
    setTimeout(outputReports, 300);
}
function saveReport(){
    var report = $("#newReport").val().replace(/\n/g,"<br>");
    var bus = settings.get('bus');
    var trip = settings.get('tripNum');
    var timestamp = timeStamp();
    
    if ( window.navigator.onLine ){
      onlineReportSave(report,bus,trip);
    } else {
      reports.set(timestamp, "Bus " + bus + ": " + report);
      unsavedReports.set(timestamp,1);
    }
    /*jshint -W030 */ 
    $("#reportsContent").append("<p>" + timestamp + ": Bus " + settings.get('bus') + ": " + report).after() + "</p>";
    $("#newReport").val('');
}
function timeStamp(){
    var date    = new Date();
    var year    = date.getFullYear();
    var month   = date.getMonth().pad();
    var day     = date.getDay().pad();
    var hours   = date.getHours().pad();
    var minutes = date.getMinutes().pad();
    var seconds = date.getSeconds().pad();

    return year + "-" + month + "-" + day + " " + hours + ":" + minutes + ":" + seconds;
}
function toggleBus(){
    var bus = settings.get('bus');
    var selector = "div#reportsContent :not(p:contains('Bus " + bus +"'))";
    var buttonValue = $("#bus").val();
    if ( buttonValue == "show" ) {
        $(selector).hide();
        $("#bus").val('hide');
        $("#bus").html('<i class="fa fa-bus"></i>&nbsp;Show All Buses');
    } else {
        $(selector).show();
        $("#bus").val("show");
        $("#bus").html('<i class="fa fa-bus"></i>&nbsp;This Bus Only');
    }
}