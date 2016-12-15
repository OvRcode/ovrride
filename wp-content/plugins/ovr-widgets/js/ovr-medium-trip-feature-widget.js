jQuery(function($) {
  $('.ovr_medium_trip_feature_trip').on("click", function(){
    window.location.href = $(this).data('link');
  });
});
