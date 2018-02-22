(function($){
  $("#addReport").on("click", function(e){
    e.preventDefault();
    // get number of existing reports on page
    reportNumber = $(".reportSetting").length + 1;
    // add postfix to number
    switch( parseInt( String(reportNumber).substring(-1) ) ) {
      case 1:
        postFix = "st";
        break;
      case 2:
        postFix = "nd";
        break;
      case 3:
        postFix = "rd";
        break;
      default:
        postFix = "th";
    }
    reportNumber += postFix;
    output = "<div class='reportSetting'>\
    <i class='fa fa-2x fa-times reportDelete' ></i><br />\
    <label>" + reportNumber + " Report Days before trip (0-7):</label><input type='number' name='_report_day[]' min='0' max='7'>\
    <br/>\
    <label>" + reportNumber + " Report Time to send report (24hr EST): </label><input type='number' name='_report_hour[]' min='0' max='24'>:\
    <input type='number' name='_report_minute[]' min='0' max='59'>\
    <br/>\
    </div>";
    $(".reportSettings").append(output);
    $(".reportDelete").unbind("click");
    $(".reportDelete").on("click", function(){
      $(this).parent("div").remove();
    });
  });
  $(".reportDelete").on("click", function(){
    $(this).parent("div").remove();
  });
})( jQuery );
