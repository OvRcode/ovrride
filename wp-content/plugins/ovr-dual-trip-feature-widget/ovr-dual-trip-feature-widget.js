jQuery(document).ready(function ($) {
  $('.ovr_dual_trip_feature_trip_one, .ovr_dual_trip_feature_trip_two').on("click", function(){
    window.location.href = $(this).data('link');
  });

});
