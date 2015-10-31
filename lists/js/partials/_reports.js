$(function(){
  bounceToIndex();
  setupBusDropdown();
  if ( jQuery.browser.mobile && navigator.userAgent.match(/iPad/i) === null ){
    $("span.mobileButtons").removeClass('hidden');
  }
    //add check for online/offline when offline is implemented
    getReports();
//    $("#saveReport").click(function(){ saveReport(); });
//    $("#refreshReports").click(function(){ getReports(); });
    $("#bus").on("change", function(){ toggleBus($(this).val()); });
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
        $("#bus").trigger("change");
    }
}
function setupBusDropdown(){
  var bus = settings.get("bus");
  var html = "<option value='All'>All</option>";
  for(var i = 1; i <= 10; i++){
    html = html.concat("<option value='" + i + "'>Bus " + i + "</option>");
  }
  $("#bus").append(html);
  $("#bus").val(bus);
  setTimeout(function(){
    $("#bus").trigger("change");
  },200);

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
function toggleBus(value){
  $("div#reportsContent p").show();
  if ( value !== "All" ){
    var selector = "div#reportsContent :not(p:contains('Bus " + value + ":'))";
    $(selector).hide();
  }
}
