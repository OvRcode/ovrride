/*jshint multistr: true */
$(function(){
  bounceToIndex();
  if ( jQuery.browser.mobile && navigator.userAgent.match(/iPad/i) === null ){
    $("div.mobileButtons").removeClass('hidden');
  }
  if ( settings.isSet('tripName') ) {
      $('#tripName').text(settings.get('tripName'));
  }
  if ( settings.isSet('destination') ) {
    $("#destName").text(settings.get('destination'));
  }
    parseData();
    if ( ! jQuery.isEmptyObject(pickups) ) {
        outputPickups();
    }
    if ( tripData.keys() !== '' ) {
        outputPackages();
    }
    outputCrew();
    getContactInfo();
});
function addPackage(packageName){
    if ( typeof packages[packageName] == 'undefined' ) {
      packages[packageName] = 0;
    }
    packages[packageName]++;
}
function getContactInfo(){
  $("span.contact").text(settings.get("contact"));
  var contactPhone = settings.get("contactPhone");
  $("span.contactPhone").html('<a href="tel:' + contactPhone + '">' + contactPhone + '</a>');
  $("span.rep").text(settings.get("rep"));
  var repPhone = settings.get("repPhone");
  $("span.repPhone").html('<a href="tel:' + repPhone + '">' + repPhone + '</a>');
}
function outputPackages(){
    var output = "<h3>Package Item Totals</h3>\
                  <table id='packagesTable' class='summary'>\
                      <thead>\
                          <tr>\
                            <th>Item</th>\
                            <th>Total</th>\
                            <tbody>";
    window.packageAdded = false;
    jQuery.each(packages, function(key, value){
        packageAdded = true;
        var row = "<tr>\
                    <th>" + key + "</th><td>" + value + "</td>\
                  </tr>";
            output = output.concat(row);
    });
    output = output.concat("</tbody></table>");
    if ( packageAdded ){
      $("div.packageTotals").append(output);
    } else {
      $("div.packageTotals").append("<h3>Package Item Totals</h3> <p>Empty</p>");
    }
}
function outputPickups(){
    var output = "<h3>Riders by location</h3>\
                  <table id='pickupTable' class='summary'>\
                    <thead>\
                        <tr>\
                            <th>Location</th><th>Expected</th><th>AM</th><th>PM</th>\
                        </tr>\
                    </thead>\
                    <tbody>";
    window.expectedTotals = 0;
    window.amTotals = 0;
    window.pmTotals = 0;
    jQuery.each(pickups, function(pickup, object){
        var row = "<tr>\
                       <th>" + pickup + "</th><td>" + object.Expected + "</td><td>" + object.AM + "</td><td>" + object.PM + "</td>\
                  </tr>";
        output = output.concat(row);
        window.expectedTotals = expectedTotals + object.Expected;
        window.amTotals = amTotals + object.AM;
        window.pmTotals = pmTotals + object.PM;
    });
    output = output.concat("<tr><th>Totals:</th><td>" + expectedTotals + "</td><td>" + amTotals + "<td>" + pmTotals + "</tr>");
    output = output.concat("</tbody></table>");
    $("div.pickupTotals").append(output);
}
function outputCrew() {
  var output = "<h3>Crew Count</h3>\
                <table id='crewTable' class='summary'>\
                  <thead>\
                    <tr> \
                      <th style='text-transform:capitalize;'>Crew</th><th>Count</th>\
                    </tr>\
                  </thead>\
                  <tbody>";
  if ( typeof window.crew.leader !== "undefined" ) {
    output = output.concat("<tr><td>Leader</td><td>" + window.crew.leader + "</td></tr>");
    delete crew.leader;
  }
  if ( typeof window.crew.second !== "undefined" ) {
    output = output.concat("<tr><td>Second</td><td>" + window.crew.second + "</td></tr>");
    delete crew.second;
  }
  $.each( crew, function(crewName, count){
    output = output.concat("<tr><td>" + crewName + "</td><td>" + count + "</td></tr>");
  });
  output = output.concat("</tbody></table>");
  $("div.crewTotals").append(output);
}
function parseData(){
    window.packages = {};
    window.pickups  = {};
    window.crew     = {};
    jQuery.each(orders.keys(), function(key, value){
        var currentOrder = orders.get(value);
        if ( typeof currentOrder.Crew !== "undefined"){
          if ( "ovr1" == currentOrder.Crew ) {
            crew.leader = currentOrder.First + " " + currentOrder.Last;
          } else if ( "ovr2" == currentOrder.Crew ) {
            crew.second = currentOrder.First + " " + currentOrder.Last;
          } else {
            if ( typeof crew[currentOrder.Crew] === "undefined" ) {
              crew[currentOrder.Crew] = 0;
            }
            crew[currentOrder.Crew] += 1;
          }
        }
        // Check for pickup and save data
        if ( typeof currentOrder.Pickup !== 'undefined' ) {
               var pickup = currentOrder.Pickup.trim();
               if ( "" === pickup ) {
                 pickup = "Leaders";
               }
               if ( typeof pickups[pickup] == 'undefined' ) {
                   // Define pickup location object if not set
                   pickups[pickup] = {};
                   pickups[pickup].Expected = 0;
                   pickups[pickup].AM = 0;
                   pickups[pickup].PM = 0;
               }
               // Add to pickup location
               pickups[pickup].Expected++;
               if ( tripData.isSet(value + ":AM") ||
               ( ! tripData.isSet(value + ":AM") && tripData.isSet(value + ":Waiver") ) ||
               ( ! tripData.isSet(value + ":AM") && tripData.isSet(value + ":Product") ) ) {
                   pickups[pickup].AM++;
               }
               if ( tripData.isSet(value + ":PM" ) ) {
                   pickups[pickup].PM++;
                   if ( ! tripData.isSet(value + ":AM") && ! tripData.isSet(value + ":Waiver") && ! tripData.isSet(value + ":Product")) {
                     pickups[pickup].AM++;
                   }
               }
        }
        if ( tripData.isSet(value + ":AM") ||
               ( ! tripData.isSet(value + ":AM") && tripData.isSet(value + ":Waiver") ) ||
               ( ! tripData.isSet(value + ":AM") && tripData.isSet(value + ":Product") ) ||
               ( ! tripData.isSet(value + ":AM") && ! tripData.isSet(value + ":Waiver") && tripData.isSet(value + ":PM"))) {
            parsePackages(currentOrder.Package.trim());
          }
    });
}
function parsePackages(custPackage){
    var bus           = new RegExp(/bus only/i);
    var begLiftLesson = new RegExp(/beginner lift.*lesson$/i);
    var allArea       = new RegExp(/all area/i);
    var weekendLift   = new RegExp(/^lift/i);
    var weekendLift2  = new RegExp(/Balance \(lift/i);
    var ltr           = new RegExp(/beginner.*lift.*bus.*lesson.*board/i);
    var lts           = new RegExp(/beginner.*lift.*bus.*lesson.*ski/i);
    var progLesson    = new RegExp(/prog.* lesson/i);
    var ski           = new RegExp(/ski rental/i);
    var brd           = new RegExp(/board rental/i);
    var lunch         = new RegExp(/.*lunch.*/i);
    // Beach / Waterpark Specific packages
    var allMountainCoaster = new RegExp(/mountain coaster/i);
    var waterPark          = new RegExp(/all area waterpark/i);
    var oneWay             = new RegExp(/one way bus/i);
    var roundTrip          = new RegExp(/round trip bus/i);
    var beachDay           = new RegExp(/day at the beach package/i);
    var beachSurf          = new RegExp(/surf lesson/i);

    // Check summer packages then bus/Lift options
    if ( beachSurf.test(custPackage) ) {
        addPackage("Surf Lesson");
    }
    else if ( beachDay.test(custPackage) ) {
        addPackage("Day at the beach");
    }
    else if ( oneWay.test(custPackage) ) {
        addPackage("One way bus");
    }
    else if ( roundTrip.test(custPackage) ) {
        addPackage("Round Trip Bus");
    }
    else if ( waterPark.test(custPackage) ) {
        addPackage("All Area Waterpark");
    }
    else if ( allMountainCoaster.test(custPackage) ) {
        addPackage("Waterpark & Mountain Coaster");
    }
    else if ( bus.test(custPackage) ) {
        addPackage("Bus Only");
    } else if ( begLiftLesson.test(custPackage) && ! ltr.test(custPackage) && ! lts.test(custPackage) ) {
        addPackage("Beginner Lift and Lesson");
    } else if ( allArea.test(custPackage) ) {
        addPackage("All Area Lift");
    } else if ( weekendLift.test(custPackage) || weekendLift2.test(custPackage) ) {
        addPackage("Weekend Lift");
    }
    // Check rentals and lessons
    if ( ltr.test(custPackage) ) {
        addPackage("Learn to Ride");
    } else if ( lts.test(custPackage) ) {
        addPackage("Learn to Ski");
    } else if ( progLesson.test(custPackage) ) {
        addPackage("Progressive Lesson");
    }

    if ( ski.test(custPackage) && !lts.test(custPackage) ) {
        addPackage("Ski Rental");
    } else if ( brd.test(custPackage) && !ltr.test(custPackage) ) {
        addPackage("Board Rental");
    }
  // REI Lunch Vouchers
    if ( lunch.test(custPackage) ) {
        addPackage("Lunch Voucher");
    }
}
