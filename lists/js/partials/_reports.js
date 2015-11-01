$(function(){
  bounceToIndex();
  setupBusDropdown();
  if ( jQuery.browser.mobile && navigator.userAgent.match(/iPad/i) === null ){
    $("span.mobileButtons").removeClass('hidden');
  }
  // Download then display reports;
  downloadReports().done(function(){ loadReports(); });

  $("#bus").on("change", function(){ toggleBus($(this).val()); });
  $("#saveReport").prop('disabled', true);

  // Enable/disable save button if field is blank
  $("#newReport").on('keyup', function(){
    if ( $(this).val().length > 0 ) {
      $("#saveReport").prop('disabled', false);
    } else {
      $("#saveReport").prop('disabled', true);
    }
  });

  $("#saveReport").on("click", function(){
    var report = $("#newReport").val();
    saveReport(report);
    syncReports().done(function(){
      downloadReports().done(function(){ loadReports(); });
    }).fail(function(){
      loadReports();
    });
  });
  $("#syncReports").on("click", function(){
    syncReports().done(function(){
      downloadReports().done(function(){ loadReports(); });
    }).fail(function(){
      alert("Failed to sync reports with the server");
    });
  });


  function saveReport(report) {
    // saves report data to local storage
    var time = moment().format("YYYY-MM-DD HH:mm:ss");
    var prep = htmlEncode(report);
    prep = prep.replace(/\n/g, "<br />");
    // save to local storage
    unsavedReports.set(time, {bus: settings.get('bus'), report: prep});
    $("#newReport").val('');
  }
  function syncReports() {
    var deferred = $.Deferred();
    $.each( unsavedReports.keys() , function(key, value){
      var report = unsavedReports.get(value);
      $.post("api/report/add", {bus: report.bus, tripId: settings.get('tripNum'), report: report.report, time: value})
      .done(function(){
        unsavedReports.remove(value);
        deferred.resolve();
      })
      .fail(function(){
        deferred.reject();
      });
    });

    return deferred.promise();
  }

});
function loadReports() {
  $("#reportsContent").html('');

  var onlineOutput = "";
  var onlineKeys = reports.keys();

  $.each(onlineKeys, function(key, value){
    var onlineReport = reports.get(value);
    onlineOutput = onlineOutput.concat("<p class='report' data-bus='" + onlineReport.bus + "' data-time='" + value + "'>Bus " + onlineReport.bus + " <i class='fa fa-globe fa-lg'></i> " + value + "<br />" + onlineReport.report + "</p>");
  });

  $("#reportsContent").append(onlineOutput);

  var offlineOutput = "";
  var offlineKeys = unsavedReports.keys();
  $.each(offlineKeys, function(key, value){
    var offlineReport = unsavedReports.get(value);
    offlineOutput = offlineOutput.concat("<p class='report' data-bus='" + offlineReport.bus + "' data-time='" + value + "'>Bus " +offlineReport.bus + " <i class='fa fa-thumb-tack fa-lg'></i> " + value + "<br />" + offlineReport.report + "</p>");
  });
  $("#reportsContent").append(offlineOutput);

    $("#bus").trigger("change");
    // Sort based on time data attribute
    $("p.report").tsort({attr: 'data-time'});

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

function toggleBus(value){
  $("p.report").show();
  if ( value !== "All" ){
    $("p.report:not([data-bus='" + value + "'])").hide();
  }
}
