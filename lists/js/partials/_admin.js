$(function(){
    $("#exportList").on("click", function(){
      window.location.href = "api/csv/list/" + settings.get('tripNum') + "/" + settings.get("status");
    });
    $("#exportEmail").on("click", function(){
      window.location.href = "api/csv/email/" + settings.get('tripNum') + "/" + settings.get("status");
    });
    if ( jQuery.browser.mobile ) {
      $("#exportList").hide();
      $("#exportEmail").hide();
    }
});
