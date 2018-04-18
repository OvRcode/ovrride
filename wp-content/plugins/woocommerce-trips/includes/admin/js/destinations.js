(function($){
  $("#addReport").on("click", function(e){
    e.preventDefault();
    // get number of existing reports on page and add postfix
    reportNumber = generatePostFix( $(".reportSetting").length );
    output = "<div class='reportSetting' style='display:inline-block;width:100%;'>\
    <div style='float: left; padding-right:5px;'><i class='fa fa-2x fa-times reportDelete' ></i></div>\
    <div style='float:left;'?\
    <label>" + reportNumber + " Report Days before trip (0-7):</label><input type='number' name='_report_day[]' min='0' max='7'>\
    <br/>\
    <label>" + reportNumber + " Report Time to send report (24hr EST): </label><input type='number' name='_report_hour[]' min='0' max='24'>:\
    <input type='number' name='_report_minute[]' min='0' max='59'>\
    <br/>\
    </div>\
    </div>";
    $(".reportSettings").append(output);
    $(".reportDelete").unbind("click");
    $(".reportDelete").on("click", event,removeSetting);
  });

  $("#addEmail").on("click", function(e){
    e.preventDefault();
    emailNumber = generatePostFix( $(".emailSetting").length );
    output = "<div class='emailSetting' style='display:inline-block; width:100%;'>\
    <div style='float:left; margin-right:5px;'>\
      <i class='fa fa-2x fa-times emailDelete'></i>\
    </div>\
    <div style='float:left;'>\
      <label>Report " + emailNumber + " Email: </label><input type='text' size='36' name='_report_email[]' />\
      <br />\
    </div>\
  </div>";
  $(".reportEmails").append(output);
  $(".emailDelete").unbind("click").on("click", event, removeEmail);
  });

  $(".reportDelete").on("click", event,removeSetting);
  $(".emailDelete").on("click", event, removeEmail);
  function removeSetting( event ) {
    $(event.currentTarget).closest(".reportSetting").remove();
  }
  function removeEmail( event ) {
    $(event.currentTarget).closest(".emailSetting").remove();
  }
  function generatePostFix( index ) {
    // takes count of settings and returns number with apropriate postfix
    index += 1;
    switch( parseInt( String(index).substring(-1) ) ) {
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
    return index + postFix;
  }
})( jQuery );
