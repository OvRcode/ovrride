/*jshint multistr: true */
$(function() {
    bounceToIndex();
    $("#menuSave").on("click", function(){
      $(this).removeClass("btn-success");
      setTimeout(function(){
        saveData();
        $("#menuSave").addClass("btn-success");
      }, 1000);
    });
    if ( jQuery.browser.mobile ) {
      $("#top").addClass("iosFix");
    }

    // Initialize Bootstrap popover function
    $("[data-toggle=popover]").popover();

    // Zoom to popover when shown
    $('[data-toggle="popover"]').on('shown.bs.popover', function(){
        $(".walkonPackages").append(dd.get('packages'));
        $(".walkonPickups").append(dd.get('walkonPickups'));

        // WalkOn Order listeners
        $("#first, #last, #phone, #walkonPrimaryPackage, #walkonSecondaryPackage, #walkonTertiaryPackage, #walkonPickup").on("change", function(){
          addWalkonButton();
        });

        $("#saveWalkOn").on("click", function(){
            saveWalkOn();
        });
        $("#sidebar-wrapper").animate({
            scrollTop: $("#walkon").offset().top
        },1000);
    });

    // Unbind change listeners when popover is hidden
    $('[data-toggle="popover"]').on('hide.bs.modal', function(){
        $("#first").unbind("change");
        $("#last").unbind("change");
        $("#phone").unbind("change");
        $("#walkonPackage").unbind("change");
        $("#saveWalkon").unbind("click");
    });

    if ( settings.get('bus') !== "All" ){
        // Show/Hide Records with AM/PM Toggle button
        $("#AMPM").on("click", function(){
            if ( $(this).val() == "AM") {
                $('.listButton.bg-none').addClass('hidden');
                $('.listButton.bg-noshow').addClass('hidden');
                html = '<i class="fa fa-sun-o fa-lg"></i>&nbsp;\
                            <i class="fa fa-toggle-on fa-lg"></i>&nbsp;\
                      <i class="fa fa-moon-o fa-lg"></i>';
                $(this).val("PM");
                $(this).html(html);
                $(this).addClass('btn-black').removeClass('btn-default');
            } else {
                $('.listButton.bg-none').removeClass('hidden');
                $('.listButton.bg-noshow').removeClass('hidden');
                $(this).val("AM");
                html = '<i class="fa fa-sun-o fa-lg"></i>&nbsp;\
                            <i class="fa fa-toggle-off fa-lg"></i>&nbsp;\
                        <i class="fa fa-moon-o fa-lg"></i>';
                $(this).html(html);
                $(this).addClass('btn-default').removeClass('btn-black');
            }
            pageTotal();
        });
    } else {
      $("#AMPM").hide();
    }

    // List sorting dropdown
    $("#sortBy").on("change", function(){
        sortList($(this).val());
    });

    // Search Type listener
    $("#searchType li a").on("click", function(){
        var value = $(this).text();
        var placeholder = "";
        var target = $("#searchButton");
        if ( value == "Clear Search" ) {
            value = "Search By:";
            placeholder = "Choose Search field first";
            $("#searchField").val('');
        } else {
            placeholder = "Search here";
        }
        target.val(value).text(value);
        $("#searchField").prop('placeholder', placeholder);

        // show any hidden entries
        $(".listButton").show();
    });
    $("#searchField").on("keyup", function(){
        var button = $("#searchButton").val();
        if ( button !== "" && button !== "Search By:"){
            searchList(button, $(this).val());
        } else {
            alert("Choose search type first");
        }
    });

    // Show extra buttons for mobile
    if ( jQuery.browser.mobile && navigator.userAgent.match(/iPad/i) === null ){
      $("div.mobileButtons").removeClass('hidden');
      $("button.secondaryWalkOn").on("click", function(){
        if ( ! $("#wrapper").hasClass("toggled") ){
          $("#wrapper").addClass("toggled");
        }
        // Slight delay for opening drawer
        setTimeout(function(){ $("#addWalkOn").trigger("click"); }, 300);
      });
    }
    setupPage();
    checkData();
    setupAllListeners();
    packageList();
    pageTotal();
    function addWalkonButton(){
        var fields = [$("#first"), $("#last"), $("#phone"),
                      $("#walkonPickup"), $("#walkonCrew"),
                      $("#walkonPrimaryPackage"), $("#walkonSecondaryPackage"),
                      $("#walkonTertiaryPackage")
                    ];

        $.each(fields, function( key,value){
          // Check that field exists before checking value
          if ( 0 === value.length) {
            return;// Skip this iteration if field doesn't exist
          }
          if ( "" === value.val() ) {
            $("#saveWalkOn").addClass('disabled');
            return false;
          } else {
            $("#saveWalkOn").removeClass('disabled');
            return true;
          }
        });
    }
    function changeStatus( element) {
      // updates status of selected guest. changes css and sets local data
      var icon = $(element).find("span.icon i");
      var button = $(element);
      var flexPickup = $(element).find(".flexPickup");
      var flexPackage = $(element).find(".flexPackage");
      var id = $(element).attr("ID");
      tripData.remove( id + ":Delete");
      if ( "PM" == $("#AMPM").val() ) {
        if ( ! button.hasClass("bg-pm") ) {
          button.removeClass("bg-none bg-am bg-waiver bg-productrec").addClass('bg-pm');
          icon.removeClass("fa-square-o fa-sun-o fa-file-word-o fa-ticket").addClass("fa-moon-o");
          tripData.set(id + ":PM", 1);
        }
      }

      if ( button.hasClass("bg-none") ) {
        button.removeClass("bg-none").addClass("bg-am");
        icon.removeClass("fa-square-o").addClass("fa-sun-o");
        flexPickup.addClass('visible-md visible-lg');
        flexPackage.removeClass('visible-md visible-lg');
        tripData.set(id + ':AM', 1);
      } else if ( button.hasClass("bg-am") ) {
        button.removeClass("bg-am").addClass("bg-waiver");
        icon.removeClass("fa-sun-o").addClass("fa-file-word-o");
        tripData.set(id + ':Waiver', 1);
      } else if ( button.hasClass("bg-waiver") ) {
        button.removeClass("bg-waiver").addClass("bg-productrec");
        icon.removeClass("fa-file-word-o").addClass("fa-ticket");
        flexPackage.addClass('visible-md visible-lg');
        flexPickup.removeClass('visible-md visible-lg');
        tripData.set(id + ':Product', 1);
      }
    }
    function checkData(){
        var localData = {};
        var states = { AM: 1, BUS: 0, Waiver: 2, Product: 3, PM: 4};
        if ( ! jQuery.isEmptyObject(tripData) ) {
            jQuery.each(tripData.keys(), function(key, value){
                var split = value.split(":");
                var ID = split[0] + ":" + split[1];

                if ( jQuery.isEmptyObject(localData[ID])) {
                    localData[ID] = split[2];
                } else if ( states[localData[ID]] < states[split[2]] ) {
                    localData[ID] = split[2];
                }
            });
            jQuery.each(localData, function(key, value){
                var selector = key.split(":");
                setState($("#" + selector[0] + "\\:" + selector[1]), value);
            });
        }
    }
    function getPackages(){
      var output = "<option value='none'>Packages</option>";
      jQuery.each( packages.keys() , function(key, value){
        var tempPackage = packages.get(value);

        if ( key > 0) {
          output = output.concat("<option value='none' disabled>"+value+"</option>");
        }
        jQuery.each(tempPackage, function(index, packageInfo){
          output = output.concat("<option value='" + packageInfo.description + "'>" + packageInfo.description + "</option>");
        });
      });
      $("select.packageList").append(output);
    }
    function getPickups(){
      var walkonSelect = "<select class='input-sm' id='walkonPickup'><option value=''>Select Pickup</option>";
      var pickupOptions = "";
      if ( "Rockaway Beach" == settings.get("destination") ) {
        $.each(settings.get('pickups'), function(route, pickups){
          pickupOptions = pickupOptions.concat("<option value='' disabled>" + route + "</option>");
          $.each(pickups, function(id, pickup){
            var time = pickup.time.split(":");
            if ( time[0] <= 12 ) {
              time = time[0] + ":" + time[1] + " Am";
            } else {
              time = (time[0] - 12) + ":" + time[1] + " Pm";
            }
            var pickupString = pickup.name + " - " + time;
            pickupOptions = pickupOptions.concat("<option value='" + pickupString + "'>" + pickupString + "</option>");
          });
        });
      } else {
        $.each(settings.get('pickups'), function(key,value){
          var row = "<option value='" + value + "'>" + value + "</option>";
          pickupOptions = pickupOptions.concat(row);
        });
      }
      walkonSelect = walkonSelect.concat(pickupOptions);
      walkonSelect = walkonSelect.concat("</select>");
      dd.set("walkonPickups", walkonSelect);
      pickupOptions = "<option value='none'>Pickups</option>" + pickupOptions;
      $("select.pickupList").append(pickupOptions);
      dd.set("pickups", pickupOptions);
    }
    function listHTML(ID, order){
        var split = ID.split(":");
        var underAge = "";
        if ( order['Is this guest at least 21 years of age?'] == "No" ) {
          underAge = "<span class='underAge'><i class='fa fa-child fa-3x'></i></span>";
        }
        var crewHTML = '';
        var crewLabel = '';
        if ( typeof(order.Crew) !== 'undefined' && "none" !== order.Crew) {
          crewHTML = "<span class='crew'>";
          if ( "ovr" === order.Crew ) {
            crewHTML = crewHTML.concat("<img src='images/ovr.png' />");
          } else if ( "patagonia" === order.Crew ) {
            crewHTML = crewHTML.concat("<img src='images/patagonia.png' />");
          } else if ( "burton" === order.Crew ) {
            crewHTML = crewHTML.concat("<img src='images/burton.png' />");
          }
          crewHTML = crewHTML.concat("</span>");
        }
        var output = "<div class='row listButton bg-none ' id='" + ID + "'>\
                          <div class='row primary'>\
                              <div class='buttonCell col-xs-3 col-md-1'>\
                                <span class='icon'><i class='fa fa-square-o fa-3x'></i></span>\
                              </div>\
                              <div class='buttonCell name col-xs-9 col-md-3'>\
                                  " + crewHTML + "\
                                  " + underAge + "\
                                  <span class='first'>&nbsp;" + order.First + "</span>\
                                  <span class='last'>" + order.Last + "</span>\
                              </div>\
                              <div class='buttonCell col-md-2 visible-md visible-lg'>\
                                Order: <span class='orderNum'>" + split[0] + "</span></div>";
        if( settings.isSet('Pickup') ) {
            var pickupHTML = '<div class="buttonCell col-xs-9 col-md-2 flexPickup">' + order.Pickup + '</div>';
            output = output.concat(pickupHTML);
        }
        var packageLabels = packages.keys();
        var combinedPackages = '';
        $.each(packageLabels, function(index, label){
          if ( label in order ) {
            combinedPackages = combinedPackages.concat( order[label] + "<br />" );
          }
        });
        // Remove trailing line break from string
        combinedPackages = combinedPackages.replace(/<br \/>$/,"");

        var packageHTML = "<div class='buttonCell col-xs-9 col-md-3 flexPackage visible-md visible-lg'>\
                            " + combinedPackages + "</div>\
                            <div class='buttonCell col-xs-3 col-md-offset-0 col-md-1 expand'>\
                              <i class='fa fa-bars fa-3x'></i>\
                            </div>\
                        </div>\
                        <div class='expanded'>\
                            <div class='row'>\
                                <div class='buttonCell col-xs-5 col-md-6'>";
        $.each(packageLabels, function(index, label){
          if ( label in order) {
            packageHTML = packageHTML.concat("<strong>"+label+":</strong> " + order[label] + "<br />");
          }
        });
        packageHTML = packageHTML.replace(/<br \/>$/, "");
        packageHTML = packageHTML.concat("</div>");
        output = output.concat(packageHTML);
        if ( settings.isSet('Pickup') ) {
            var packageHTML2 = "<div class='buttonCell col-xs-12 col-md-6'>\
                                <strong>Pickup:</strong> " + order.Pickup + "</div>";
            output = output.concat(packageHTML2);
        }
        var expandedRemainder = "</div>\
                    <div class='row'>\
                        <div class='buttonCell col-xs-12 col-md-6'>\
                            <strong>Order:</strong>" + split[0] + " </div>\
                        <div class='buttonCell col-xs-12 col-md-6'>\
                        <strong>Phone:</strong> <a href='tel:" + order.Phone + "'><span class='phone'>" + order.Phone + "</span></a> \
                    </div>\
                  </div>\
                  <div class='row'>\
                    <br />\
                    <div class='buttonCell col-xs-4'>\
                        <button class='btn btn-info' id='" + ID +":Reset'>\
                            Reset\
                        </button>\
                    </div>\
                    <div class='buttonCell col-xs-4'>\
                        <button class='btn btn-warning' id='" + ID + ":NoShow'>\
                            No Show\
                        </button>\
                    </div>";
        if ( ID.substring(0,2) == "WO" ) {
            expandedRemainder = expandedRemainder.concat('<div class="buttonCell col-xs-4"><button class="btn btn-danger removeOrder" id="' + ID + ':Delete">Remove Order</button></div>');
        }
        expandedRemainder = expandedRemainder.concat("</div></div></div>");
        output = output.concat(expandedRemainder);
        var newOrder = $("#content").append(output);
        newOrder.find("span.icon").on("click", function(){
          changeStatus( $(this).parents("div.row.listButton"));
        });
        newOrder.find("div.expand").on("click", function(){
          toggleExpanded( $(this) );
        });
        newOrder.find("button.reset").on("click", function(){
          resetGuest( $(this).parents('div.listButton').attr('id') );
        });
        newOrder.find("button.noShow").on("click", function(){
          noShow( $(this).parents('div.row.listButton').attr('id') );
        });
        newOrder.find("button.removeOrder").click(function(){
            var rootElement = $(this).parents("div.row.listButton");
            var id = rootElement.attr('id');
            tripData.set(rootElement.attr('id')+":Delete", "Delete");
            rootElement.remove();
        });
        initialHTML.set(ID, output);
        // Hide expanded area of reservation
        $("div.expanded").hide();
    }
    function noShow(id) {
      tripData.set(id+":NoShow", 1);
      var element = $("#" + id.replace(":","\\\:"));
      element.removeClass('bg-none bg-am bg-waiver bg-productrec bg-pm').addClass('bg-noshow');
      element.find("span.icon i").removeClass('fa-square-o fa-sun-o fa-file-word-o fa-ticket fa-moon-o').addClass('fa-times-circle-o');
    }
    function packageList(){
        var output = "";
        jQuery.each(packages.keys(), function(index,label){
          if ( '' === label ) {
            return;
          }
          output = output.concat("<select class='input-sm' id='");
          if ( 0 === index ) {
            output = output.concat("walkonPrimaryPackage");
          } else if ( 1 === index ) {
            output = output.concat("walkonSecondaryPackage");
          } else if ( 2 === index ) {
            output = output.concat("walkonTertiaryPackage");
          }
          output = output.concat("'>");
          output = output.concat("<option value='' selected>Select " + label + "</option>");
          jQuery.each(packages.get(label), function(key, value){
            var outputCost = '';
            if ( parseFloat(value.cost).toFixed(2) > 0.00 ) {
              outputCost = " $" + value.cost;
            }
            output = output.concat("<option value='" + value.description + "'>" + value.description + outputCost + "</option>");
          });
          output = output.concat("</select><br /><br />");
        });

        dd.set('packages',output);
    }
    function pageTotal(){
      var totalGuests = $("div.listButton:visible").length;
      $("span.listTotal").text(totalGuests + " Guests");
    }
    function resetGuest(id){
      var clearVars = [ id + ":AM", id + ":PM", id + ":Waiver", id + ":Product", id + ":NoShow", id + ":Bus" ];
      var selector = $("#"+id.replace(":","\\\:"));
      selector.removeClass("bg-am bg-pm bg-waiver bg-productrec bg-pm bg-none bg-noshow").addClass("bg-none");
      selector.find("span.icon i").removeClass("fa-square-o fa-sun-o fa-file-word-o fa-ticket fa-moon-o").addClass("fa-square-o");

      jQuery.each(clearVars, function(key,value){
        tripData.remove(value);
      });
      tripData.set(id + ":Delete", "delete");
    }
    function saveData(){
        // get state data from localstorage
        var packageWeight = {AM: 1, Waiver: 2, Product: 3, PM: 4, NoShow: 5,Delete: 6};
        var allDataKeys = tripData.keys();
        var orderLocalData = {};
        jQuery.each(allDataKeys, function(key,value){
            var split = value.split(":");
            var ID = split[0] + ":" + split[1];
            var valueName = split[2];
            // Check for delete walkon
            if ( value.substring(0,2) == "WO" && valueName == "Delete" && tripData.get(value) == "Delete" ) {
                $.post("api/walkon/delete/"+ID);
            }
            // Check if object is setup
            if ( jQuery.isEmptyObject(orderLocalData[ID]) ) {
                orderLocalData[ID] = { Trip: settings.get('tripNum'), Bus: settings.get('bus'), Data:""};
            }
            if ( orderLocalData[ID].Data === "" || packageWeight[valueName] > packageWeight[orderLocalData[ID].Data]) {
                orderLocalData[ID].Data = valueName;
            }
        });

        // get walkon data if not previously saved
        if ( newWalkon.keys().length > 0 ){
            var walkonData = {};
            jQuery.each(newWalkon.keys(), function(key,value){
                 walkonData[value]= orders.get(value);
                walkonData[value].Bus = settings.get('bus');
                walkonData[value].Trip = settings.get('tripNum');
            });
            $.post("api/save/walkon", {walkon: walkonData}, function(){
                newWalkon.removeAll();
            });
        }
        if ( ! tripData.isEmpty() ){
          $.post("api/save/data", {data: orderLocalData}, function(){
            tripData.removeAll();
            getTripData();
            alert("Trip data has been saved!");
          })
          .fail(function(){
            alert("Trip data failed to save!");
          });
        }
    }
    function saveWalkOn(){
        // Saves to local storage
        var orderNum = Math.floor((Math.random() * 99999) + 1);
        orderNum = "WO" + String(orderNum.pad(4));
        var orderItem = Math.floor((Math.random() * 99999) + 1);
        orderItem = orderItem.pad(4);
        var ID = orderNum + ":" + orderItem;
        var walkOn = {First: $("#first").val(),
                      Last: $("#last").val(),
                      Phone: $("#phone").val(),
                      Package: $("#walkonPackage").val(),
                      Crew: $("#walkonCrew").val(),};
        var packageKeys = packages.keys();
        if ( $("#walkonPrimaryPackage").length > 0 ) {
          walkOn[packageKeys[0]] = $("#walkonPrimaryPackage").val();
        }
        if ( $("#walkonSecondaryPackage").length > 0 ) {
          walkOn[packageKeys[1]] = $("#walkonSecondaryPackage").val();
        }
        if ( $("#walkonTertiaryPackage").length > 0 ) {
          walkOn[packageKeys[2]] = $("#walkonTertiaryPackage").val();
        }
        if ( $("#walkonPickup").length > 0) {
            walkOn.Pickup = $("#walkonPickup").val();
        }
        listHTML(ID, walkOn);
        orders.set(ID,walkOn);
        newWalkon.set(ID,"unsaved");
        $("#addWalkOn").popover('toggle');
    }
    function searchList(searchType, text){
        //TODO: Look at options for case insensitivity
        var match;
        var search;
        // Skip blank searches
        if ( text !== ""){
            if ( searchType == "Name" ){
                search = $(".listButton div.name:contains('" + text + "')");
            } else if ( searchType == "Email" ) {
                search = $(".listButton span.email:contains('" + text + "')");
            } else if ( searchType == "Phone" ) {
                search = $(".listButton span.phone:contains('" + text + "')");
            } else if ( searchType == "Order" ) {
                search = $(".listButton span.orderNum:contains('" + text + "')");
            } else if ( searchType == "Package" ){
                search = $(".listButton div.flexPackage:contains('" + text + "')");
            }
            showSearchResults(search);
            // END OF SEARCH TYPES
        } else {
            $(".listButton").show();
        }
    }
    function setState(element, state){
      element.removeClass("bg-am bg-waiver bg-productrec bg-pm");
      element.find("span.icon i").removeClass("fa-square-o fa-sun-o fa-file-word-o fa-ticket fa-moon-o");
      var bg = "";
      var icon = "";
      if ( state == 'AM' ){
        bg = "bg-am";
        icon = "fa-sun-o";
      } else if ( state == 'Waiver' ) {
        bg = "bg-waiver";
        icon = "fa-file-word-o";
      } else if ( state == 'Product' ) {
        bg = "bg-productrec";
        icon = "fa-ticket";
      } else if ( state == 'PM' ) {
        bg = "bg-pm";
        icon = "fa-moon-o";
        element.find('.flexPackage').addClass('visible-md visible-lg');
        element.find('.flexPickup').removeClass('visible-md visible-lg');
        } else if ( state == 'NoShow' ){
          bg = "bg-noshow";
          icon = "fa-times-circle-o";
        } else {
          bg = "bg-none";
          icon = "fa-square-o";
        }

        element.addClass(bg);
        element.find("span.icon i").addClass(icon);
    }
    function setupAllListeners() {
      $("span.icon").on("click", function(){
        changeStatus( $(this).parents("div.row.listButton"));
      });
      $("div.expand").on("click", function(){
        toggleExpanded( $(this) );
      });
      $("button.reset").on("click", function(){
        resetGuest( $(this).parents('div.listButton').attr('id') );
      });
      $("button.noShow").on("click", function(){
        noShow( $(this).parents('div.row.listButton').attr('id') );
      });
      $("button.removeOrder").click(function(){
          var rootElement = $(this).parents('div.row.listButton');
          tripData.set(rootElement.attr('id')+":Delete", "Delete");
          rootElement.remove();
      });
    }
    function setupListener(ID){
        var split = ID.split(":");
        var selectorID = "#" + split[0] + "\\:" + split[1];
        if ( ID.substring(0,2) == "WO" ){
            $(selectorID + "\\:Delete").click(function(){
                tripData.set(split[0]+":"+split[1]+":Delete", "Delete");
                $(this).parents('.listButton').remove();
            });
        }
        // Click events for noshow/reset buttons
        $(selectorID + "\\:Reset").click(function(){
            resetGuest($(this).parents().eq(3));
        });
        $(selectorID + "\\:NoShow").click(function(){
            noShow($(this).parents().eq(3));
        });

       // Expand list entry by pressing and holding on entry (works on mobile and desktop)
       // TODO: update for new expand button
        //$( selectorID ).on("doubletap", function(){
        //    toggleExpanded( $(this) );
        //});
        if ( settings.get('bus') != "All" ){
          $("#" + split[0] + "\\:" + split[1] + " div.row.primary").on("click", function(){
                changeStatus($(this).parent());
            });
        }
    }
    function setupPage(){
        $(".expanded").hide();

        if ( settings.isSet('tripName') ) {
            $('#tripName').text(settings.get('tripName'));
        }
        if ( settings.isSet('bus') ) {
            $('#bus').text(settings.get('bus'));
        }
        if ( settings.isSet('destination') ) {
          $("#destName").text(settings.get('destination'));
        }
        var keys = initialHTML.keys();
        jQuery.each(keys, function(key,value){
            $('#content').append(initialHTML.get(value));
        });
        // Hide pickup select
        if ( settings.get('pickups').length === 0 ) {
          $("select.pickupList").hide();
        } else {
          getPickups();
          $("select.pickupList").on("change", function(){
            $("select.packageList").val('none');
            $("div.listButton").show();
            var value = $(this).val();
            if ( value !== "none"){
              var selector = "div.listButton:not(:contains('" + value + "'))";
              $(selector).hide();
            }
            pageTotal();
          });
        }
        // Setup package list
        getPackages();
        $("select.packageList").on("change", function(){
          $("select.pickupList").val('none');
          $("div.listButton").show();
          var value = $(this).val();
          if ( value !== "none"){
            var selector = "div.listButton:not(:contains('" + value + "'))";
            $(selector).hide();
          }
          pageTotal();
        });
        // Hide expanded area of reservation
        $("div.expanded").hide();
        sortList();
    }
    function showSearchResults(targets){
        $(".listButton").hide();
        targets.parents('.listButton').show();
    }
    function sortList(value){
        switch(value){
            case "Faz":
                // First Name Ascending
                $(".listButton").tsort('span.first',{order: 'asc'});
                break;
            case "Fza":
                // First Name Descending
                $(".listButton").tsort('span.first',{order:'desc'});
                break;
            case "Laz":
                // Last Name Ascending
                $(".listButton").tsort('span.last',{order: 'asc'});
                break;
            case "Lza":
                // Last Name Descending
                $(".listButton").tsort('span.first',{order: 'desc'});
                break;
            case "Paz":
                // Package Ascending
                $(".listButton").tsort('div.flexPackage',{order:'asc'});
                break;
            case "Pza":
                // Package Descending
                $(".listButton").tsort('div.flexPackage',{order:'desc'});
                break;
            case "Piaz":
                // Pickup Ascending
                $(".listButton").tsort('div.flexPickup',{order:'asc'});
                break;
            case "Piza":
                // Pickup Descending
                $(".listButton").tsort('div.flexPickup',{order:'desc'});
                break;
            case "Oza":
                // Order Descending
                $(".listButton").tsort('span.orderNum', {order:'desc'});
                break;
            default:
                // Sort by Pickup, then last name
                $(".listButton").tsort('div.flexPickup','span.last');
                break;
        }
    }
    function toggleExpanded( element ) {
      var button = $(element).parents("div.row.listButton");
      var drawer = button.find("div.expanded");

      if ( $(".expanded").not(drawer).is(':visible') ) {
        $(".expanded").hide().not(drawer);
      }

      if ( drawer.is(':visible') ) {
        drawer.hide();
      } else {
        drawer.show();
      }
    }
});
