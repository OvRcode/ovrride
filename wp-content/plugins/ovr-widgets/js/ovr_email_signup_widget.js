(function($){
  $("#newsletter").on("click", newsletter);
  $(".email_signup i").on("click", function(){
    $("#newsletter").parent("div").css("background", "rgb(221,221,221)");
    $("#newsletter_back").toggle();
    $("#newsletter").toggle();
    $("#newsletter").on("click", newsletter);
  });

  function newsletter(){
    $(this).parent("div").css("background", "rgb(150,150,150)");
    $(this).toggle();
    $("#newsletter_back").toggle();
    $(this).unbind("click");
  }
})( jQuery );
