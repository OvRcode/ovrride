jQuery(document).ready(function($){
  var vars = window.location.search.replace("?","");
  if ( vars.indexOf("bb=1") >= 0 ){
  $(".social-media-icons").remove();
  $('.site-title').find('img').attr('src','http://rockawaybeachbus.com/img/rbb_logo.png');
  $('#maincontentcontainer').css({"background": "url('https://rockawaybeachbus.com/img/bkgsm.png') #FFFFFF"});
  $("#masthead a").attr('href', 'http://rockawaybeachbus.com/');
  $("#headercontainer").css({"background": "#002549"});
  $('.images').remove();
  $('.wc_trip_add').css({'background':'#a4366d'});
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
      if ( $(field).data("required")){
        var label = $(field).siblings('label').text().replace(" *","");
        if ( "text" == $(field).attr("type") && "" === $(field).val()){
          $(field).addClass("errorField");
          errors[label] = label + " is blank";
        } else if ( $(field).is("select") && "" === $(field).val()) {
          $(field).addClass("errorField");
          errors[label] = "Select an option for " + label;
        } else if ( "radio" == $(field).attr("type") ) {
          //  label = $(field).parents('label').text().replace(" *","");
          var fieldName = $(field).attr("name");
          var radio = $("input[name=" + fieldName + "]");
          if ( true === radio.data("required") && !radio.is(":checked") ) {
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
      var fields = [$("label[for=wc_trip_dob]"), $("#wc_trip_dob_month"), $("#wc_trip_dob_day"), $("#wc_trip_dob_year")];
      if ( "no" == $(this).val() ) {
        $.each(fields, function(k,v){
          v.attr('data-required', 'true').show();
          v.siblings('label').show();
        });
        $(".DOB").after('<br class="postDOB" /><br class="postDOB" /><br class="postDOB" />');
      } else {
        $.each(fields, function(k,v){
          v.removeData('required').removeAttr('data-required').hide();
          v.siblings('label').hide();
        });
        $(".postDOB").remove();
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
      // Switch to bus routes tab, if it's on the page
      if ( $(".bus_routes_tab").is(":visible") ){
        $('.active').removeClass('active');
        $('.bus_routes_tab').addClass('active');
        $('.wc-tab').hide();
        $('#tab-bus_routes').show();
      }

      // Re-enable all dropdowns
      $("#wc_trip_primary_package, #wc_trip_secondary_package, #wc_trip_tertiary_package").prop('disabled', false);
      $("#oneWay").remove();

      if (/to beach/i.test($(this).val() ) ) {
        $("#wc_trip_tertiary_package").append("<option id='oneWay' value='oneWay'>One Way To Beach</option>");
        $("#wc_trip_tertiary_package").val('oneWay');
        $("#wc_trip_tertiary_package").prop('disabled', true);
      } else if(/from beach/i.test($(this).val() ) ) {
        $("#wc_trip_secondary_package").append("<option id='oneWay' value='oneWay'>One Way From Beach</option>");
        $("#wc_trip_secondary_package").val('oneWay');
        $("#wc_trip_secondary_package").prop('disabled', true);
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
