(function($){
  $(".ovr_calendar .icon").webuiPopover();
  $(".next, .prev").on("click", function(){
    $(".ovr_calendar").spin();

    window.date = new Date( $(".month_year").text() );

    if ( $(this).hasClass("next") ) {
      window.date.setMonth( window.date.getMonth() + 1 );
    } else if ( $(this).hasClass("prev") ) {
      window.date.setMonth( window.date.getMonth() - 1 );
    } else {
      return;
    }
    jQuery.post(
      OvRCalVars.ajaxurl,
      {
        action : 'ovr_calendar',
        calendarDate : window.date,
        calendarNonce : OvRCalVars.calendarNonce
      },
      function( response ) {
        $(".ovr_calendar").spin(false);
        $(".month_year").text(response.month_year);
        $(".ovr_calendar .days").html(response.html);
        $(".ovr_calendar .icon").webuiPopover();
      }
    );
  });

})( jQuery );
