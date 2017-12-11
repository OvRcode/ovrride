(function($){
  $(".shop_buttons a").each(function(key, value){
    if ( window.location.pathname.search($(value).attr('href')) >= 0 ) {
    //if ( $(value).attr('href') == window.location.pathname ) {
      $(value).hide();
    }
  });
  function featureMainSize() {
    if ( $(window).width() > 991 ) {
      featureMain = $(".feature-main").height();
      $(".feature-right").height(featureMain);
    }
  }
  featureMainSize();
  
  $(window).on("resize", function(){
    featureMainSize();
    $(".footer-square").height($(".footer-square").width());
    //$(".footer-square").width($(".footer-square").width());
    if ( $(".footer-square").width() > 200) {
      var fontSize = $(".footer-square").width() - 100;
    } else {
      var fontSize = 100;
    }

    $(".footer-square-inner .icon").css("font-size", $(".footer-square-inner").height() + "px");

    // Find the widest word in footer squares and use that to calculate best font size
    var maxTextWidth = 0;
    $(".text").css("font-size", "initial");
    $(".text").each(function( index, value){
      if ( $(this).width() > maxTextWidth ) {
        maxTextWidth = $(this).width();
      }
    });
    var containerWidth = $(".footer-square-inner").width();
    var currentFontSize = $(".footer-square-inner .text").css("font-size").slice(0,-2);
    // Round down, full decimal on font size was causing overflow
    var newFontSize = Math.floor(containerWidth / (maxTextWidth/currentFontSize));
    // Vertical Center rollover words
    var top = (parseInt($(".footer-square-inner").height()) - (parseInt($(".text").height())/2))/2;
    $(".footer-square-inner .text").css({"font-size": newFontSize, "top": top});
  }).resize();
  $("#about").on("click", aboutToggle);
  $(".aboutOvR i").on("click", aboutToggle);
  function aboutToggle() {
    if ( $('.aboutOvR:visible').size() > 0 ) {
      $("#about").removeClass("aboutActive");
      $("#about .text").removeClass("aboutShow");
      $("#about .icon").removeClass("aboutHide");
    } else {
      $("#about").addClass("aboutActive");
      $("#about .text").addClass("aboutShow");
      $("#about .icon").addClass("aboutHide");
      $('html,body').delay(100).animate({
          scrollTop: $("#about").offset().top
        }, 1000);
    }

    $(".aboutOvR").toggle();
  }
})( jQuery );
