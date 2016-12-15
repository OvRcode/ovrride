jQuery(document).ready(function ($) {
  $('.ovr_blog_feature').on("click", function(){
    window.location.href = $(this).data('link');
  });
  
});
