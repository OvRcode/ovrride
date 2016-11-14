(function($){
  $(window).on("resize", function(){
    $(".footer-square").height($(".footer-square").width());
  }).resize();

})( jQuery );
