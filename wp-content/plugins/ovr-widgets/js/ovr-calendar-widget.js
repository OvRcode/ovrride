(function($){

  $(".ovr_calendar .icon").webuiPopover();
  $(".next, .prev").on("click", function(){
    $(".ovr_calendar").fadeTo("slow", 0.6);
    $(".ovr_calendar").spin();

    window.date = new Date( $(".month_year").text() );

    if ( $(this).hasClass("next") ) {
      window.date.setMonth( window.date.getMonth() + 1 );
    } else if ( $(this).hasClass("prev") ) {
      window.date.setMonth( window.date.getMonth() - 1 );
    } else {
      return;
    }
    window.date = window.date.getFullYear() + "-" + (window.date.getMonth()+1) + "-" + window.date.getDate();
    $.post(
      ovr_calendar_vars.ajax_url ,
      {
        action : 'ovr_calendar',
        calendarDate : window.date,
      },
      function( response ) {
        $(".ovr_calendar").fadeTo("fast", 1);
        $(".ovr_calendar").spin(false);
        $(".month_year").text(response.month_year);
        $(".ovr_calendar .days").html(response.html);
        $(".ovr_calendar .icon").webuiPopover();
      }
    );
  });

})( jQuery );