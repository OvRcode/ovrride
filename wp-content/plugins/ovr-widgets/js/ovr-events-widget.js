jQuery(function($) {
  // Make upcoming event divs links using their built in link for source
  $("div.event").on("click",function(){
    window.location.href=$(this).children("a:first-child").attr("href");
  });
  // Click functions to drive upcoming events scroll action
  $("i.rightArrow").on("click", function(){
    $(".event:first-of-type").appendTo(".eventScroll");
  });
  $("i.leftArrow").on("click", function(){
    $(".event:last-of-type").prependTo(".eventScroll");
  });
});
