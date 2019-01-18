// parseQuery: Originally from StackOverflow of: https://stackoverflow.com/a/13419367/2529423
function parseQuery(qstr) {
    var query = {};
    var a = (qstr[0] === '?' ? qstr.substr(1) : qstr).split('&');
    for (var i = 0; i < a.length; i++) {
        var b = a[i].split('=');
        query[decodeURIComponent(b[0])] = decodeURIComponent(b[1] || '');
    }
    return query;
}

// encodeQuery originally from StackOverflow: https://stackoverflow.com/a/1714899/2529423
function encodeQuery(obj) {
  var str = [];
  for(var p in obj)
    if (obj.hasOwnProperty(p)) {
      str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
    }
  return str.join("&");
}

function addQueryParameterToDomain(key, value, conditionDomain) {
    var links = document.querySelectorAll('a[href]'),
        linksLength = links.length,
        index,
        queryIndex,
        queryString,
        query,
        url,
        domain,
        colonSlashSlash;

    // Iterate through and add query paramter
    for(index = 0; index < linksLength; ++index) {
        url = links[index].href,
        queryIndex = url.indexOf('?'),
        colonSlashSlash = url.indexOf('://');

        domain = url.substring(colonSlashSlash + 3);
        domain = domain.substring(0, domain.indexOf('/'));

        if(domain !== conditionDomain) {
            continue;
        }

        if(queryIndex === -1) {
            url += '?' + key + '=' + value;
        } else {
            queryString = url.substring(queryIndex);
            url = url.substring(0, queryIndex);
            query = parseQuery(queryString);

            query[key] = value;

            url += '?' + encodeQuery(query);
        }

        links[index].href = url;
    }
}

// jQuery(function() {
    
//     addQueryParameterToDomain('lkid', '12345678', 'www.example.com');
// });

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
    //Modify Shop links on page
    //$("a.woocommerce-LoopProduct-link").attr("href", $(this).attr("href") + "?bb=1");
    function modifyShopLink(e) {
      e.preventDefault();
      var url = jQuery(this).attr("href").concat("?bb=1");
      window.location.href=url;
    }
    // Keep BB referal on links
    $("a.woocommerce-LoopProduct-link").click( jQuery(this), modifyShopLink );
    $("a.woocommerce-LoopProduct-link").siblings('a').click( jQuery(this), modifyShopLink );
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
    month = $("input[name=wc_trip_dob_month]").val();
    day = $("input[name=wc_trip_dob_day]").val();
    year = $("input[name=wc_trip_dob_year]").val();
    if ( month !== "" && day !== "" && year !== "" ) {
      $("#wc_trip_dob_field").val(month + "/" + day + "/" + year);
    }
    // Check for lessons and age
    if ( $("#wc_trip_dob_field").val() !== "" ) {
      lesson_age = parseInt( $("#wc_trip_lesson_restriction").val() );
      if ( lesson_age > 0 ) {
        today = new Date();
        birthDate = new Date( $("#wc_trip_dob_field").val() );
        age = today.getFullYear() - birthDate.getFullYear();
        monthCheck = today.getMonth() - birthDate.getMonth();
        lesson = new RegExp(/lesson/i);
        primary_package = $("#wc_trip_primary_package").val();
        secondary_package = $("#wc_trip_secondary_package").val();
        tertiary_package = $("#wc_trip_tertiary_package").val();

        if ( monthCheck < 0 || ( monthCheck == 0 && today.getDate() < birthDate.getDate() ) ) {
          age--;
        }

        if ( age < lesson_age && ( lesson.test(primary_package) || lesson.test(secondary_package) || lesson.test(tertiary_package) ) ) {
          errors[age] = "Sorry, we cannot accomodate lessons for guests " + age + " years of age. Please select a different package.";
        }
      }
    }
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
      ageLimit = $("#wc_trip_age_limit").val();
      if ( ageLimit.substr( ageLimit.length - 1 ) == "+" ) {
        ageStrict = true;
        ageLabel = ageLimit.substr(0, ageLimit.length - 1 );
      } else {
        ageStrict = false;
      }

      if ( "yes" == $(this).val() ) {
        $(".dob, .dobLabel").hide();
        $("#wc_trip_dob_year, #wc_trip_dob_day, #wc_trip_dob_month").data('required', false);
        if ( ageStrict ) {
          $(".wc_trip_add").prop("disabled",false);
        }

      } else if ( ageStrict && "no" == $(this).val() ) {
        $(".wc_trip_add").prop("disabled",true);
        alert( "Sorry, you need to be over " + ageLabel + " for this trip." );
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

  $("input[name^=wc_trip_dob_]").on( "keyup change", function(e){
    var month = $("input[name=wc_trip_dob_month]").val();
    var day = $("input[name=wc_trip_dob_day]").val();
    var year = $("input[name=wc_trip_dob_year]").val();
    $(".wc_trip_add").prop("disabled",false);
    if ( e.type == "change" && e.currentTarget.id !== "wc_trip_dob_year" ) {
      if ( month.length > 0 && month.length <= 2 && ( month <= 0 || month > 12 || isNaN(parseInt(month)) ) )  {
        $(".dobError .month").text("Please enter a valid month for date of birth");
        $("input[name=wc_trip_dob_month]").val("").focus();
        $(".wc_trip_add").prop("disabled",true);
        return;
      } else {
        $(".dobError .month").text("");
      }

      if ( day.length > 0 && day.length <= 2 && ( isNaN(parseInt(day)) || day <=0 || day > 31 ) ) {
        $(".dobError .day").text("Please enter a valid day for date of birth");
        $("input[name=wc_trip_dob_day]").val("").focus();
        $(".wc_trip_add").prop("disabled",true);
        return;
      } else {
        $(".dobError .day").text("");
      }
    } else {
      if ( year.length > 0 && year.length < 4) {
        $(".dobError .year").text("Please provide a 4 digit year for birthdate");
        $(".wc_trip_add").prop("disabled",true);
        return;
      } else {
        $(".dobError .year").text("");
        $(".wc_trip_add").prop("disabled",false);
      }

      if ( year.length > 0 && year.length <= 4 && ( isNaN(parseInt(year)) || year <=0 ) ) {
        $(".dobError .year").text("Please enter a valid year for date of birth");
        $("input[name=wc_trip_dob_year]").val("").focus();
        $(".wc_trip_add").prop("disabled",true);
        return;
      } else {
        $(".dobError .year").text("");
        $(".wc_trip_add").prop("disabled",false);
      }
    }

    if ( month == 2 ) {
      if ( ( year%4 == 0 && year%100 !== 0 ) || year%400 == 0 ) {
        //leap year
        if ( day > 29 ) {
          $(".dobError .day").text("Invalid day for February of a leap year, please fix day in birthday");
          $(".wc_trip_add").prop("disabled",true);
          return;
        } else {
          $(".dobError .day").text("");
        }
      } else {
        // normal year
        if ( day > 28 ) {
          $(".dobError .day").text("Invalid day for February, please fix day in birthday");
          $(".wc_trip_add").prop("disabled",true);
          return;
        } else {
          $(".dobError .day").text("");
        }
      }
    } else if ( month == 4 || month == 6 || month == 9 || month == 11 ) {
      if ( day > 30 ) {
        $(".dobError .day").text("Invalid day for month, please fix day in birthday");
        $(".wc_trip_add").prop("disabled",true);
        return;
      } else {
        $(".dobError .day").text("");
      }
    }
    // Check that DOB is under 18
    if ( month.legth !== 0 && day.length !== 0 && year.length == 4 ) {
      today = new Date();
      birthDate = new Date(month + "/" + day + "/" + year);
      age = today.getFullYear() - birthDate.getFullYear();
      monthCheck = today.getMonth() - birthDate.getMonth();
      if ( monthCheck < 0 || ( monthCheck == 0 && today.getDate() < birthDate.getDate() ) ) {
        age--;
      }
      if ( $("#wc_trip_age_limit").length > 0 ) {
        ageCheck = $("#wc_trip_age_limit").val();

        if ( parseInt(age) >= parseInt(ageCheck) ) {
          alert( "No need to enter a birthday, you're over " + ageCheck +"!");
          $("#wc_trip_dob_field").val("");
          $("input[name=wc_trip_dob_month]").val("");
          $("input[name=wc_trip_dob_day]").val("");
          $("input[name=wc_trip_dob_year]").val("");
          $('input:radio[name=wc_trip_age_check]:first').trigger("click");
        }
      }
    }
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
  $("#wc_trip_primary_package, #wc_trip_secondary_package, #wc_trip_tertiary_package, #wc_trip_pickup_location").on("change", addCosts);
  function addCosts(){
    var base      = Number($("#base_price").val()) || 0;
    var primary   = Number( $("#wc_trip_primary_package :selected").data('cost') ) || 0;
    var secondary = Number( $("#wc_trip_secondary_package :selected").data('cost') ) || 0;
    var tertiary  = Number( $("#wc_trip_tertiary_package :selected").data('cost') ) || 0;
    var pickup    = Number( $("#wc_trip_pickup_location :selected").data('cost') ) || 0;
    var total = base + primary + secondary + tertiary + pickup;

    $("#trip_price").text( "$" + total.toFixed(2) );
  }
  addCosts();
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
  

  /*---------------------------------------------------------------------------------------------------------------------
    WeWork Trip
  -----------------------------------------------------------------------------------------------------------------------*/

  if ( vars.indexOf("wework=1") >= 0 ){
      $(".navbar-inverse").css({
        "background":"url(https://lh3.googleusercontent.com/EyHauB03faA07sgKthv3zkmNTVUf5hWTaXgEi61gGEroaUFGrhb2OhQYgI28TILNPAnDKlK2YQ=w1191) no-repeat center center",
        "min-height": 298
      });
      
      // Transform logo and logo placement
      var title_logo = $("a[title='Home']");
      var title_li = title_logo.parent('li');
      title_logo.attr('href','https://weworkskitrip.squarespace.com/');

      //title_logo.css({'width':'','height':''});
      title_li.css({'background':'url(https://static1.squarespace.com/static/5bd9e871d274cb8403963a2a/t/5c33b664562fa726f24d77b9/1547686652380/?format=1500w)'});
      title_li.css({'background-repeat':'no-repeat'});
      title_li.css({'background-size': 'contain'});
      title_logo.css({'width':'170px'});
      title_logo.css({'height':'140px'});
      title_li.css({'margin-top':'10px'});
      title_li.css({'left':'40%'});

      $('.main-content, .mainBackground, #main-no-collapse li:first-of-type a').css({
        "background": "none"
      });


      // Remove and Replace menu items
      $("a[title='Book A Trip']").remove();
      var main_collapse = $("ul#main-collapse");
      main_collapse.html('');

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
      
      //Modify Shop links on page
      //$("a.woocommerce-LoopProduct-link").attr("href", $(this).attr("href") + "?bb=1");
      function modifyShopLink(e) {
        e.preventDefault();
        var url = jQuery(this).attr("href").concat("?wework=1");
        window.location.href=url;
      }

      $("a.button.wc-forward").click( jQuery(this), modifyShopLink );
      $("a.woocommerce-LoopProduct-link").click( jQuery(this), modifyShopLink );
      $("a.woocommerce-LoopProduct-link").siblings('a').click( jQuery(this), modifyShopLink );
      
      $('p.stock').text("Space Available");
      $('.dobCheck p').html("For this trip guests need to be at least 21 years of age. <br>We are unable to accomodate guests under 21 years of age.");
    
      addQueryParameterToDomain('wework=1');
    }  
});
