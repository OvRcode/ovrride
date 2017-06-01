jQuery(document).ready(function($){
  var vars = window.location.search.replace("?","");
  if ( vars.indexOf("bb=1") >= 0 ){
    $(".navbar-inverse").css({"background-color":"#002549"});
    // Transform logo and logo placement
    var title_logo = $("a[title='Home']");
    var title_li = title_logo.parent('li');
    title_logo.attr('href','http://rockawaybeachbus.com');
    //title_logo.css({'width':'','height':''});
    title_li.css({'background':'url(http://rockawaybeachbus.com/img/rbb_logo.png)'});
    title_li.css({'background-repeat':'no-repeat'});
    title_li.css({'background-size': 'contain'});
    title_logo.css({'width':'170px'});
    title_logo.css({'height':'140px'});
    title_li.css({'margin-top':'10px'});
    title_li.css({'left':'40%'});
    $("#main-no-collapse li:first-of-type a").css({"background":"none"});

    // Remove and Replace menu items
    $("a[title='Book A Trip']").remove();
    var main_collapse = $("ul#main-collapse");
    main_collapse.html('');
    main_collapse.append("<li><a title='home' href='http://rockawaybeachbus.com'>HOME</a></li>");
    main_collapse.append("<li><a title='calendar' href='http://rockawaybeachbus.com/calendar.php'>CALENDAR</a></li>");
    main_collapse.append("<li><a title='partners' href='http://rockawaybeachbus.com/partners.php'>PARTNERS</a></li>");
    main_collapse.append("<li><a title='faq' href='http://rockawaybeachbus.com/faq.php'>FAQ</a></li>");
    main_collapse.append("<li><a title='terms and conditions' href='http://rockawaybeachbus.com/terms.php'>TERMS AND CONDITIONS</a></li>");
    main_collapse.find('li a').css({'font-size':'1em'});

    // Move navbar down and to the right
    var collapse_div = main_collapse.parent('div');
    main_collapse.addClass('navbar-right');
    collapse_div.css({'position':'absolute'});
    collapse_div.css({'right':'10px'});
    collapse_div.css({'bottom':'-28px'});
    // Switch site background
    $("body").css({'background':'url(http://rockawaybeachbus.com/img/bkgsm.png) repeat'});
    $("div.main-content").css({'background':'none'});

    //Hide upcoming events list
    $("div.events").parent().parent('div.row').remove();
    //Hide product image
    $("div.images").remove();
    // Remove footer squares
    $("div.footer-square-container").remove();
    //Remove ovr footer sponsors
    $("div.ovr-sponsors").remove();
    // Remove footer links
    $("div.ovr-footer-links").remove();
    // Remove Woocommerce breadcrumb links
    $("nav.woocommerce-breadcrumb").remove();
    // Change stock and price color
    $("p.stock, span#trip_price").css({'color':'#A4366D'});
    // Change Add to cart button color
    $("button.wc_trip_add").css({'background':'#A4366D'});
    //Change header link colors
    $(".navbar-inverse .navbar-nav>li>a:link, .navbar-inverse .navbar-nav>li>a:visited").css({'color':'#FFF'});
	  // Had to setup JS events for :hover due to jquery hardcoding new link color onto links
    $(".navbar-inverse .navbar-nav>li>a").on('mouseenter',function() {
      $(this).css("color", "#ED665E");
    });
    $(".navbar-inverse .navbar-nav>li>a").on('mouseleave',function() {
      $(this).css("color", "#FFF");
    });
  }
  // Set price on page
  var base_price = Number($("#base_price").val());
  base_price = base_price.toFixed(2);
  $("#trip_price").text("$" + base_price);
  // Prevent html form submission before validation
  $(".single_add_to_cart_button.wc_trip_add").on("click", function(event){
    event.preventDefault();
    var errors = {};
    $.each($("input[name^=wc_trip_], select[name^=wc_trip_]"), function(key,field){
      if ( $(field).data("required") && $(field).data("required") === true){
        var label = $(field).siblings('label').text().replace(" *","");
        if ( "text" == $(field).attr("type") && "" === $(field).val()){
          $(field).addClass("errorField");
          errors[label] = label + " is blank";
        } else if ( $(field).is("select") && "" === $(field).val()) {
          $(field).addClass("errorField");
          errors[label] = "Select an option for " + label;
        } else if ( "radio" == $(field).attr("type") ) {
          var fieldName = $(field).attr("name");
          var radio = $("input[name=" + fieldName + "]");
          if ( !radio.is(":checked") ) {
            errors[label] = "Select an option for " + label;
            radio.addClass('errorField');
            radio.parents('p').addClass('errorField');
          }
        }
      }
    });
    if ( !jQuery.isEmptyObject(errors) ) {
      $("#errors").html('');
      $("#errors").append("<strong>Please correct the following errors to complete your reservation</strong><br />");
      $.each(errors, function(k,v){
        $("#errors").append(v + "<br />");
      });
      $("#errors").append("</p>");
      $("#errors").show();
      $('html, body').animate({ scrollTop: $("p.stock").offset().top }, 'slow');
      $(".errorField, p.errorField").on("change", function(){
        if ( "" !== $(this).val() ) {
          $(this).removeClass('errorField');
        }
        if ( "radio" == $(this).attr("type") ) {
          $(this).parents('p').removeClass('errorField');
        }
      });
    } else {
      $("#wc_trip_primary_package, #wc_trip_secondary_package, #wc_trip_tertiary_package").prop('disabled', false);
      $("#wc-trips-form").parents('form').submit();
    }
  });
  $("input[name=wc_trip_email]").verimail({
    messageElement: "p#emailValidation"
  });
  $("input[name=wc_trip_email]").on("change", function(){
    var status = $("input[name=wc_trip_email]").getVerimailStatus();
    if ( "success" !== status ) {
      $("#emailValidation").show();
      $(this).css({background: '#CC0000', color: '#FFFFFF'}).focus();
    } else {
      $("#emailValidation").hide();
      $(this).css({background: '#FFFFFF', color: '#000000'});
    }
  });

  if ( "domestic_flight" !== $("#wc_trip_type").val() && "international_flight" !== $("#wc_trip_type").val()){
    $("input[name=wc_trip_age_check]").on("change", function(){
      if ( "yes" == $(this).val() ) {
        $(".dob, .dobLabel").hide();
        $("#wc_trip_dob_year, #wc_trip_dob_day, #wc_trip_dob_month").data('required', false);
      } else {
        $(".dob, .dobLabel").show();
        $("#wc_trip_dob_year, #wc_trip_dob_day, #wc_trip_dob_month").data('required', true);
      }

    });
  } else {
    $(".DOB").show();
    $(".DOB").find("label").show();
    $("label[for=wc_trip_dob]").show();
    $("#wc_trip_dob_month").show();
    $("#wc_trip_dob_day").show();
    $("#wc_trip_dob_year").show();
  }

  $("input[name^=wc_trip_dob_]").on("keyup", function(){
    var month = $("input[name=wc_trip_dob_month]").val();
    var day = $("input[name=wc_trip_dob_day]").val();
    var year = $("input[name=wc_trip_dob_year]").val();
    $("#wc_trip_dob_field").val(month + "/" + day + "/" + year);
  });
  $("#wc_trip_primary_package").on("change", function(){
    if ( "beach_bus" === $("#wc_trip_type").val() ) {
      // Re-enable all dropdowns
      $("#wc_trip_to_beach,#wc_trip_from_beach").prop('disabled', false);
      $("#oneWay").remove();

      if (/to beach/i.test($(this).val() ) ) {
        $("#wc_trip_from_beach").append("<option id='oneWay' value='oneWay'>One Way To Beach</option>");
        $("#wc_trip_from_beach").val('oneWay');
        $("#wc_trip_from_beach").prop('disabled', true);
      } else if(/from beach/i.test($(this).val() ) ) {
        $("#wc_trip_to_beach").append("<option id='oneWay' value='oneWay'>One Way From Beach</option>");
        $("#wc_trip_to_beach").val('oneWay');
        $("#wc_trip_to_beach").prop('disabled', true);
      }

    }
  });
  $("#wc_trip_primary_package, #wc_trip_secondary_package, #wc_trip_tertiary_package, #wc_trip_pickup_location").on("change", function(){
    var base      = Number($("#base_price").val()) || 0;
    var primary   = Number( $("#wc_trip_primary_package :selected").data('cost') ) || 0;
    var secondary = Number( $("#wc_trip_secondary_package :selected").data('cost') ) || 0;
    var tertiary  = Number( $("#wc_trip_tertiary_package :selected").data('cost') ) || 0;
    var pickup    = Number( $("#wc_trip_pickup_location :selected").data('cost') ) || 0;
    var total = base + primary + secondary + tertiary + pickup;

    $("#trip_price").text( "$" + total.toFixed(2) );
  });
  function enableDisableCart() {
    var fieldsOK = true;
    var fields = $("input[name^=wc_trip_], select[name^=wc_trip_]");
    $.each(fields, function(key, value){
      if ( "radio" == $(value).attr("type") ) {
        var name = $(value).attr("name");
        var radio = $("input[name=" + name + "]");
        if ( true === radio.data("required") && !radio.is(":checked") ) {
          fieldsOK = false;
          return false;
        }
      }

      if ( "radio" !== $(value).attr("type") && "" === $(value).val() && true === $(value).data("required")) {
        fieldsOK = false;
        return false;
      }
    });
    if ( fieldsOK ) {
      $(".single_add_to_cart_button").prop("disabled", "");
    } else {
      $(".single_add_to_cart_button").prop("disabled", "disabled");
    }
  }
});
