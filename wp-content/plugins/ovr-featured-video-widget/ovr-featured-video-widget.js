jQuery(function($) {
  $(window).resize(resizeVideo).resize();
  function resizeVideo(){
    var video = $(".ovr_featured_video_box iframe");
    var container = $(".ovr_featured_video_box");
    var title = $(".ovr_featured_video_inner h4");
    var newWidth = container.width() * 0.98;
    var newHeight = (container.height() - 20) * 0.90;
    video.removeAttr('height').removeAttr('width');
    video.height(newHeight).width(newWidth);
  }
});
