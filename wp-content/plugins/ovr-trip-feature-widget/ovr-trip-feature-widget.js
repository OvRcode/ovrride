jQuery(document).ready(function ($) {
  $(".ovr_trip_feature").on("click", function(){
    window.location.href = $(this).data('link');
  });
});
