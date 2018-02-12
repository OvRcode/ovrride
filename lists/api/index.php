<?php
require 'flight/Flight.php';
require 'twilio-php/Services/Twilio.php';

class Lists {
    var $dbConnect;
    var $destinations;
    var $trips;
    var $orders;
    var $pickup;
    var $tripInfo;
    function __construct(){
        // Setup DB Connection and check that it works
        $this->dbConnect = new mysqli(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PASS'), getenv('MYSQL_DB'));
        if($this->dbConnect->connect_errno > 0){
            die('Unable to connect to database [' . $this->dbConnect->connect_error . ']');
        }
        else{
          $this->dbConnect->query("SET NAMES utf8");
          $this->dbConnect->query("SET CHARACTER SET utf8");
          $this->dbConnect->query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
        }
        // Pre-populate data for trip and destination dropdowns

        // Pull full list of active destinations
        $this->destinations = array();
        $sql = "SELECT `post_title` FROM `wp_posts` WHERE post_type='destinations' AND `post_status` = 'publish'";
        $result = $this->dbQuery($sql);
        while ( $row = $result->fetch_assoc() ) {
          $this->destinations[] = $row['post_title'];
        }

        // Find Trip type products and their destination
        $sql = "SELECT `wp_posts`.`ID`,`wp_posts`.`post_title` as 'Trip', `wp_postmeta`.`meta_value` as 'Destination'
        FROM `wp_posts`
        INNER JOIN `wp_postmeta` on `wp_posts`.`ID` = `wp_postmeta`.`post_id`
        INNER JOIN `wp_term_relationships` ON `wp_posts`.`ID` = `wp_term_relationships`.`object_id`
        INNER JOIN `wp_term_taxonomy` ON `wp_term_relationships`.`term_taxonomy_id` = `wp_term_taxonomy`.`term_taxonomy_id`
        INNER JOIN `wp_terms` ON `wp_term_taxonomy`.`term_id` = `wp_terms`.`term_id`
        WHERE `wp_term_taxonomy`.`taxonomy` = 'product_type'
        AND `wp_terms`.`name` = 'trip'
        AND `wp_postmeta`.`meta_key` = '_wc_trip_destination'";

        $result = $this->dbQuery($sql);
        while ( $row = $result->fetch_assoc() ) {
          // Only save info for active destinations
          if ( in_array($row['Destination'], $this->destinations) ) {
            $this->trips[$row['Destination']][$row['ID']]['title'] = $row['Trip'];
          }
        }

    }
    function destinationDropdown(){
        $output = "";
        asort($this->destinations);
        foreach ( $this->destinations as $index => $destination) {
          $output .= "<option value='{$destination}'>{$destination}</option>\n";
        }

        return $output;
    }
    function tripDropdown(){
        $options = "";
        $options_trips = array();
        foreach( $this->destinations as $index => $destination) {
          if ( isset( $this->trips[$destination] ) ) {
            foreach( $this->trips[$destination] as $id => $info) {
                $sql = "SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = '{$id}' AND `meta_key` = '_wc_trip_start_date' LIMIT 1";
                $result = $this->dbQuery($sql);
                $result = $result->fetch_assoc();
                $date = strtotime($result['meta_value']);
                $year = date('Y', $date);
                $month = date('m', $date);
                $day = date('d', $date);
                $options_trips[$year][$month][$day][$id] = array("title" => $info['title'], "destination" => $destination);
              }
          }
        }
        // reverse sort years
        krsort($options_trips);
        foreach($options_trips as $year => $month_info) {
          // sort months numerically
          ksort($options_trips[$year]);
          foreach($month_info as $month => $day_info) {
            // sort days numerically
            ksort($options_trips[$year][$month]);
            foreach($day_info as $day => $trip_info) {
              // sort trips inside each day alphabetically
              asort($options_trips[$year][$month][$day]);
            }
          }
        }
        $divider_class = "";
        foreach($this->destinations as $index => $destination) {
          $divider_class .= " $destination ";
        }
        foreach($options_trips as $year => $month_info) {
          $options .= "<option class='$divider_class' disabled>$year</option>";
          foreach($month_info as $month => $day_info){
            foreach($day_info as $day => $trip_info) {
              foreach($trip_info as $id => $trip) {
                $trip_date = "$year-$month-$day";
                $options .= "<option value='$id' class='{$trip['destination']}' data-date='$trip_date'>{$trip['title']}</option>\n";
              }
            }
          }
        }
        echo $options;
    }
    function csv($type,$trip,$status, $tripName ){
      $fileName = date("m-d-Y") . " - " . $tripName . " - " . $type . ".csv";
      //Gets CSV data from POST and returns file download
      header("Content-type: text/csv");
      header("Content-Disposition: attachment; filename=".$fileName);
      header("Pragma: no-cache");
      header("Expires: 0");
      // Setup trip/order data
      $this->getTripInfo($trip);
      $orders = $this->tripData("All",$trip,$status);

      $output = fopen("php://output", "w");

      $headers = array("First", "Last", "Phone","Crew","Pickup","Package","Order","AM","Waiver","Product Recieved", "PM");

      if ( $type === "list") {
        if ( !$this->pickup ) {
          unset($headers[4]);
        }
        fputcsv($output, $headers);

        foreach( $orders as $ID => $data ) {
          $order = array();
          $order[0] = ( isset( $data['Data']['First'] ) ? $data['Data']['First'] : '' );
          $order[1] = ( isset( $data['Data']['Last'] ) ? $data['Data']['Last'] : '' );
          $order[2] = ( isset( $data['Data']['Phone'] ) ? $data['Data']['Phone'] : '' );
          if ( isset($data['Data']['Crew']) ) {
            switch( $data['Data']['Crew'] ) {
              case 'ovr1':
                $order[3] = "Trip Leader";
                break;
              case 'ovr2':
                $order[3] = "Second";
                break;
              default:
                $order[3] = $data['Data']['Crew'];
            }
          } else {
            $order[3] = "";
          }

          $orderNum = preg_split("/:/",$ID);

          if ( $this->pickup ) {
            $order[4] = ( isset( $data['Data']['Pickup'] ) ? $data['Data']['Pickup'] : '' );
            $order[5] = ( isset( $data['Data']['Package'] ) ? $data['Data']['Package'] : '' );
            $order[6] = $orderNum[0];
          } else {
            $order[4] = ( isset( $data['Data']['Package'] ) ? $data['Data']['Package'] : '' );
            $order[5] = $orderNum[0];
          }
          $index = count($order);
          switch( $data['State'] ) {
            case "AM":
              $order[$index + 1] = "X";
              $order[$index + 2] = "";
              $order[$index + 3] = "";
              $order[$index + 4] = "";
              break;
            case "Waiver":
              $order[$index + 1] = "X";
              $order[$index + 2] = "X";
              $order[$index + 3] = "";
              $order[$index + 4] = "";
              break;
            case "Product":
              $order[$index + 1] = "X";
              $order[$index + 2] = "X";
              $order[$index + 3] = "X";
              $order[$index + 4] = "";
              break;
            case "PM":
              $order[$index + 1] = "X";
              $order[$index + 2] = "X";
              $order[$index + 3] = "X";
              $order[$index + 4] = "X";
              break;
            case "NoShow":
              $order[$index + 1] = "NS";
              $order[$index + 2] = "NS";
              $order[$index + 3] = "NS";
              $order[$index + 4] = "NS";
              break;
            default:
            $order[$index + 1] = "";
            $order[$index + 2] = "";
            $order[$index + 3] = "";
            $order[$index + 4] = "";
            break;
          }
        fputcsv($output, $order);
        }
        fclose($output);
      } else if ( "email" == $type ) {
        array_unshift($headers, "Email");
        unset($headers[3],$headers[4],$headers[7], $headers[8], $headers[9], $headers[10], $headers[11]);
        if ( !$this->pickup ) {
          unset($headers[5]);
        }
        fputcsv($output, $headers);

        foreach( $orders as $ID => $data ) {
          $order = array();
          $order[0] = ( isset($data['Data']['Email']) ? $data['Data']['Email'] : '');
          if ( $order[0] === '' ) {
            continue;
          }
          $order[1] = (isset($data['Data']['First']) ? $data['Data']['First'] : '');
          $order[2] = (isset($data['Data']['Last']) ? $data['Data']['Last'] : '');
          if ( $this->pickup ) {
            $order[3] = (isset($data['Data']['Pickup']) ? $data['Data']['Pickup'] : '');
            $order[4] = (isset($data['Data']['Package']) ? $data['Data']['Package'] : '');
          } else {
            $order[3] = (isset($data['Data']['Package']) ? $data['Data']['Package'] : '');
          }
          fputcsv($output, $order);
        }
        fclose($output);
      }
      /*

            foreach( $orders as $ID => $data ) {
                $first = (isset($data['Data']['First']) ? $data['Data']['First'] : '');
                $last = (isset($data['Data']['Last']) ? $data['Data']['Last'] : '');
                $package = (isset($data['Data']['Package']) ? $data['Data']['Package'] : '');
                $pickup = (isset($data['Data']['Pickup']) ? $data['Data']['Pickup'] : '');
                $email = ( isset($data['Data']['Email']) ? $data['Data']['Email'] : 'none');
                if ( 'none' === $email ) continue;
                $row = "";
                $row .= "\"" . $email . "\"";
                $row .= ",\"" . $first . "\",\"" . $last . "\",\"" . $package;
                if ( $this->pickup ) {
                  $row .= "\",\"" . $pickup;
                }
                $row .= "\"\n";
                $output .= $row;
            }
        }

        if ( $output !== "" ){
            return $output;
        }*/
    }
    function getTripName($trip){
        $sql = "select post_title from wp_posts where ID = '" . $trip . "'";
        $result = $this->dbQuery($sql);
        $name = $result->fetch_assoc();
        return $name['post_title'];
    }
    function getTripInfo($trip){
      // Pull Metadata for Product
      $sql = "SELECT `meta_key`,`meta_value`
              FROM `wp_postmeta`
              WHERE (`meta_key` = '_wc_trip_type'
                OR `meta_key` = '_wc_trip_primary_package_label'
                OR `meta_key` = '_wc_trip_primary_packages'
                OR `meta_key` = '_wc_trip_secondary_packages'
                OR `meta_key` = '_wc_trip_secondary_package_label'
                OR `meta_key` = '_wc_trip_tertiary_package_label'
                OR `meta_key` = '_wc_trip_tertiary_packages'
                OR `meta_key` = '_wc_trip_pickups')
              AND `post_id` = '{$trip}'";
      $result = $this->dbQuery($sql);
      $this->tripInfo = array();
      while ( $row = $result->fetch_assoc() ) {
        switch ( $row['meta_key'] ) {
          case '_wc_trip_primary_packages':
          case '_wc_trip_secondary_packages':
          case '_wc_trip_tertiary_packages':
            $this->tripInfo[$row['meta_key']]['packages'] = unserialize($row['meta_value']);
            break;
          case '_wc_trip_primary_package_label':
            $this->tripInfo['_wc_trip_primary_packages']['label'] = $row['meta_value'];
            break;
          case '_wc_trip_secondary_package_label':
            $this->tripInfo['_wc_trip_secondary_packages']['label'] = $row['meta_value'];
            break;
          case '_wc_trip_tertiary_package_label':
            $this->tripInfo['_wc_trip_tertiary_packages']['label'] = $row['meta_value'];
            break;
          case '_wc_trip_type':
            $this->tripInfo['type'] = $row['meta_value'];
            break;
          case '_wc_trip_pickups':
            $this->tripInfo['pickups'] = unserialize($row['meta_value']);
        }
      }
      // Pull titles for pickups
      if ( 'bus' == $this->tripInfo['type'] ) {
      $pickupNames = array();
        if ( isset($this->tripInfo['pickups'] ) && is_array($this->tripInfo['pickups'])) {
          $this->pickup = TRUE;
          foreach( $this->tripInfo['pickups'] as $id => $route) {
            $sql = "SELECT `post_title` as `name` FROM `wp_posts` WHERE `ID` = '{$id}' LIMIT 1";
            $result = $this->dbQuery($sql);
            $name = $result->fetch_assoc();
            $pickupNames[] = $name['name'];
          }
          unset($this->tripInfo['pickups']);
          $this->tripInfo['pickups'] = $pickupNames;
        }
      } elseif ( 'beach_bus' == $this->tripInfo['type'] ) {
        $this->pickup = TRUE;
        foreach( $this->tripInfo['pickups'] as $id => $route ) {
          $sql = "SELECT `post_title` as `name` FROM `wp_posts` WHERE `ID` = '{$id}' LIMIT 1";
          $result = $this->dbQuery($sql);
          $name = $result->fetch_assoc();
          $beach_bus_pickups[$route][$id]['name'] = $name['name'];
          $sql = "SELECT `meta_value` as `time` FROM `wp_postmeta` WHERE post_id ='{$id}' AND `meta_key` = '_pickup_location_time' LIMIT 1";
          $result = $this->dbQuery($sql);
          $time = $result->fetch_assoc();
          $beach_bus_pickups[$route][$id]['time'] = $time['time'];
        }
        $this->tripInfo['pickups'] = $beach_bus_pickups;
      }

      return $this->tripInfo;
    }
    function tripData($bus, $tripId, $status){
        /* Get saved trip data and sort into array based on bus # */
        $busSql = "select ID,Bus from ovr_lists_data where Trip='" . $tripId . "'";
        $busResult = $this->dbQuery($busSql);
        $busData = [];
        $busData[$bus] = [];
        $busData["Other"] = [];
        if ("beach_bus" !== $this->tripInfo['type']){
        while( $busRow = $busResult->fetch_assoc() ) {
            if ( $busRow['Bus'] !== 0 ){
                if ( $busRow['Bus'] == $bus ){
                    $busData[$busRow['Bus']][] = $busRow['ID'];
                } else {
                    $busData["Other"][] = $busRow['ID'];
                }
            }
        }
        }
        $statuses = explode(',',$status);
        foreach($statuses as $single){
            if ( $single == "walk-on" ) {
                $sql = "SELECT * FROM `ovr_lists_manual_orders` WHERE `Trip` = '" . $tripId . "'";
                $result = $this->dbQuery($sql);
                while($row = $result->fetch_assoc()){
                        $walkOnOrder = [];
                        $walkOnOrder['Bus'] = (isset($row['Bus']) ? $row['Bus'] : "");
                        // Make sure walkons are either unclaimed or on the selected bus
                        if ( $walkOnOrder['Bus'] !== "" && $walkOnOrder['Bus'] !== $bus ) {
                          continue;
                        }
                        $split = preg_split("/:/", $row['ID']);
                        $walkOnOrder['num']      = $split[0];
                        $walkOnOrder['item_num'] = $split[1];
                        $walkOnOrder['First'] = $row['First'];
                        $walkOnOrder['Last'] = $row['Last'];
                        if (isset($row['Pickup']) && $row['Pickup'] !== "NO PICKUP" && $row['Pickup'] !== "No Pickup") {
                            $walkOnOrder['Pickup'] = $row['Pickup'];
                        }
                        $walkOnOrder['Phone'] = $row['Phone'];
                        $walkOnOrder['Package'] = $row['Package'];
                        $walkOnOrder['Crew'] = $row['Crew'];

                          $this->listHTML($walkOnOrder);
                          $this->customerData($walkOnOrder);
                }
            } else {
                $sql = "SELECT `wp_posts`.`ID`, `wp_woocommerce_order_items`.`order_item_id`, `wp_posts`.`post_status`
                        FROM `wp_posts`
                        INNER JOIN `wp_woocommerce_order_items` ON `wp_posts`.`id` = `wp_woocommerce_order_items`.`order_id`
                        INNER JOIN `wp_woocommerce_order_itemmeta` ON `wp_woocommerce_order_items`.`order_item_id` = `wp_woocommerce_order_itemmeta`.`order_item_id`
                        WHERE `wp_posts`.`post_type` =  'shop_order'
                        AND `wp_posts`.`post_status` = '$single'
                        AND `wp_woocommerce_order_items`.`order_item_type` =  'line_item'
                        AND `wp_woocommerce_order_itemmeta`.`meta_key` =  '_product_id'
                        AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '$tripId'";
                $result = $this->dbQuery($sql);
                while($row = $result->fetch_assoc()){
                    $searchID = $row['ID'] . ":" . $row['order_item_id'];
                    if ( $bus == "All" || array_search($searchID, $busData[$bus]) !== FALSE ||
                        array_search($searchID, $busData['Other']) === FALSE) {
                        $orderData = [];
                        $orderData['num'] = $row['ID'];
                        $orderData['item_num'] = $row['order_item_id'];
                        $order = $row['ID'];
                        $orderItem = $row['order_item_id'];
                        // Assemble meta keys for query
                        $fields = "`meta_key` = 'First'
                        OR `meta_key` = 'Last'
                        OR `meta_key` = 'Email'
                        OR `meta_key` = 'Phone'";
                        switch ( $this->tripInfo['type'] ) {
                          case 'bus':
                              $fields .= " OR `meta_key` = 'Pickup Location'
                              OR `meta_key` = 'Is this guest at least 18 years of age?'";
                              break;
                          case 'beach_bus':
                              $fields .= " OR `meta_key` = 'To Beach'
                              OR `meta_key` = '_to_beach_route' OR `meta_key` = '_to_beach_id'
                              OR `meta_key` = 'From Beach' OR `meta_key` = '_from_beach_route'
                              OR `meta_key` = '_from_beach_id' OR `meta_key` = 'Is this guest at least 18 years of age?'";
                              break;
                          case 'domestic_flight':
                            $fields .= " OR `meta_key` = 'Date of Birth'";
                          case 'international_flight':
                            $fields .= " OR `meta_key` = 'Passport Number'
                                        OR `meta_key` = 'Passport Country'";
                            break;
                        }


                        if ( count($this->tripInfo['_wc_trip_primary_packages']['packages']) > 0) {
                          $fields .= " OR `meta_key` = '{$this->tripInfo['_wc_trip_primary_packages']['label']}'";
                        }
                        if ( count($this->tripInfo['_wc_trip_secondary_packages']['packages']) > 0) {
                          $fields .= " OR `meta_key` = '{$this->tripInfo['_wc_trip_secondary_packages']['label']}'";
                        }
                        if ( count($this->tripInfo['_wc_trip_tertiary_packages']['packages']) > 0) {
                          $fields .= " OR `meta_key` = '{$this->tripInfo['_wc_trip_tertiary_packages']['label']}'";
                        }

                        $detailSql = "SELECT `meta_key`, `meta_value`
                            FROM `wp_woocommerce_order_itemmeta`
                            WHERE ( " . $fields . ")
                            AND `order_item_id` = '$orderItem'";

                        $detailResult = $this->dbQuery($detailSql);
                        while( $detailRow = $detailResult->fetch_assoc() ) {
                          switch ( $detailRow['meta_key'] ) {
                            case 'Pickup Location':
                              $orderData['Pickup'] = $this->stripTime($detailRow['meta_value']);
                              break;
                            case 'Phone':
                              $orderData['Phone'] = $this->reformatPhone($detailRow['meta_value']);
                            default:
                              $orderData[$detailRow['meta_key']] = ucwords(strtolower($detailRow['meta_value'] ) );
                          }
                        }

                        $this->listHTML($orderData);
                        $this->customerData($orderData);
                    }
                }
            }
        }

        // Only filter beach bus orders, buses are not numeric for bb
        if ( !is_numeric($bus) && "All" !== $bus){
          $remove=array();
          foreach( $this->orders as $id => $info ) {
            if ( isset($info['Data']['Bus']) && strpos($info['Data']['Bus'], $bus) !== FALSE ) {
              continue;
            } else if ( isset($info['Data']['Bus']) && strpos($info['Data']['Bus'], $bus) === FALSE) {
              $remove[] = $id;
            } else if ( ! in_array( ucwords(strtolower($bus)), $info['Data'] )) {
              $remove[] = $id;
            }
          }

          foreach($remove as $id ) {
            unset($this->orders[$id]);
          }
        }

        return $this->orders;
    }
    function getReports($tripId){
        $reports = array();
        $sql = "SELECT * FROM `ovr_lists_reports` WHERE `Trip` ='" . $tripId . "'";
        $result = $this->dbQuery($sql);
        while( $row = $result->fetch_assoc() ){
            $reports[$row['Time']]['Bus'] = $row['Bus'];
            $reports[$row['Time']]['Report'] = $row['Report'];
        }
        return $reports;
    }
    function addReport($bus, $tripId, $report, $time){
        $sql = "INSERT INTO `ovr_lists_reports` (Bus, Trip, Report, Time) VALUES('{$bus}','{$tripId}', '{$report}','{$time}')";
        if ( $this->dbQuery($sql) )
            return http_response_code(200);
        else
            return http_response_code(404);
    }
    function saveData(){
        foreach( $_POST['data'] as $ID => $field ) {
            if ( $field['Data'] == "Delete" ) {
                $sql = "DELETE FROM `ovr_lists_data` WHERE `ID` ='" . $ID . "'";
            } else {
            $sql = "INSERT INTO `ovr_lists_data` (ID, Trip, Bus, Data)
                    VALUES( '" .$ID . "', '" . $field['Trip'] ."', '" . $field['Bus'] . "', '" . $field['Data'] . "')
                    ON DUPLICATE KEY UPDATE
                    Bus=VALUES(Bus), Data=VALUES(Data)";
            }
            $this->dbQuery($sql);
            // Skip this for the beachbus
            if ( isset($field['Bus']) && is_numeric($field['Bus']) && substr($ID,0,2) == "WO") {
                $sql = "UPDATE `ovr_lists_manual_orders` SET `Bus` = '" . $field['Bus'] . "' WHERE ID ='" . $ID . "'";
                $this->dbQuery($sql);
            }
        }
    }
    function saveWalkOn(){
        foreach( $_POST['walkon'] as $ID => $fields ) {

          if ( isset($fields['RBB']) && $fields['RBB'] ) {
              if ( !isset($fields['Secondary Package']) ) {
                $fields['Secondary Package'] = null;
              }
              if ( !isset($fields['Tertiary Package']) ) {
                $fields['Tertiary Package'] = null;
              }
              if ( isset($fields['RBB']) && $fields['RBB'] ) {
                $sql = "INSERT INTO `ovr_lists_manual_orders` (`ID`, `First`,
                  `Last`, `Crew`, `Transit To Rockaway`, `Transit From Rockaway`, `Phone`, `Package`, `Secondary Package`, `Tertiary Package`, `Trip`, `Bus`)
                  VALUES ('{$ID}', '{$fields['First']}', '{$fields['Last']}', '{$fields['Crew']}', '{$fields['To Beach']}', '{$fields['From Beach']}',
                  '{$fields['Phone']}', '{$fields['Package']}', '{$fields['Secondary Package']}', '{$fields['Tertiary Package']}',
                  '{$fields['Trip']}', '{$fields['Bus']}')
                  ON DUPLICATE KEY UPDATE
                  First=VALUES(First), Last=VALUES(Last), Crew=VALUES(Crew), `Transit To Rockaway`=VALUES(`Transit To Rockaway`),
                  `Transit From Rockaway`=VALUES(`Transit From Rockaway`), Phone=VALUES(Phone), Package=VALUES(Package), `Secondary Package`=VALUES(`Secondary Package`),
                  `Tertiary Package`=VALUES(`Tertiary Package`), Trip=VALUES(Trip)";
              } else {
                $sql = "INSERT INTO `ovr_lists_manual_orders` (`ID`, `First`,
                  `Last`, `Crew`, `Transit To Rockaway`, `Transit From Rockaway`, `Phone`, `Package`, `Secondary Package`, `Tertiary Package`, `Trip`, `Bus`)
                  VALUES ('{$ID}', '{$fields['First']}', '{$fields['Last']}', '{$fields['Crew']}', '{$fields['To Beach']}', '{$fields['From Beach']}',
                  '{$fields['Phone']}', '{$fields['Package']}', '{$fields['Secondary Package']}', '{$fields['Tertiary Package']}',
                  '{$fields['Trip']}', '{$fields['Bus']}')
                  ON DUPLICATE KEY UPDATE
                  First=VALUES(First), Last=VALUES(Last), Crew=VALUES(Crew), `Transit To Rockaway`=VALUES(`Transit To Rockaway`),
                  `Transit From Rockaway`=VALUES(`Transit From Rockaway`), Phone=VALUES(Phone), Package=VALUES(Package), `Secondary Package`=VALUES(`Secondary Package`),
                  `Tertiary Package`=VALUES(`Tertiary Package`), Trip=VALUES(Trip), Bus=VALUES(Bus)";
                }

              $this->dbQuery($sql);
          } else {
              if ( ! isset($fields['Pickup']) ){
                  $fields['Pickup'] = "";
              }
              $sql = "INSERT INTO `ovr_lists_manual_orders` (ID, First, Last, Pickup, Phone, Package, Trip, Bus, Crew)
                      VALUES('" . $ID . "', '" . $fields['First'] . "', '" . $fields['Last']. "', '" . $fields['Pickup']. "',
                      '" . $fields['Phone']. "', '" . $fields['Package'] . "', '" . $fields['Trip']. "', '" . $fields['Bus'] . "',
                      '" . $fields['Crew'] . "')
                      ON DUPLICATE KEY UPDATE
                      First=VALUES(First), Last=VALUES(Last), Pickup=VALUES(Pickup), Phone=VALUES(Phone), Package=VALUES(Package),
                      Trip=VALUES(Trip), Bus=VALUES(Bus), Crew=VALUES(Crew)";
              $this->dbQuery($sql);
          }
        }
    }
    function deleteOrder($tripId){
        $sqlManual = "DELETE FROM `ovr_lists_manual_orders` WHERE `ID`='" . $tripId . "'";
        $sqlData = "DELETE FROM `ovr_lists_data` WHERE `ID`='" . $tripId . "'";
        $this->dbQuery($sqlManual);
        $this->dbQuery($sqlData);
    }
    function sendMessage(){
        $accountSid = getenv('TWILIO_SID');
        $authToken = getenv('TWILIO_AUTH');

        $client = new Services_Twilio($accountSid, $authToken);
        $postData = $_POST['message'];
        $recipients = $postData['Recipients'];
        $body = "Message from OvR Ride: " . $postData['Message'];
        // Loop through each recipient
        foreach( $recipients as $phoneNum ) {
            $message = $client->account->messages->create(array(
                 "From" => "+16467629375",
                 "To" => $phoneNum,
                 "Body" => $body
             ));

             echo "Sent message {$message->sid}";
        }
    }
    function getContactInfo($destination){
        $sql = "SELECT `meta_key`, `meta_value`
                FROM `wp_postmeta`
                JOIN `wp_posts` ON `wp_postmeta`.`post_id` = `wp_posts`.`ID`
                WHERE `post_type` = 'destinations'
                AND `post_status` = 'publish'
                AND `post_title` = '{$destination}'
                AND (`meta_key` = '_contact'
                OR `meta_key` = '_contact_phone'
                OR `meta_key` = '_rep'
                OR `meta_key` = '_rep_phone')";
        $result = $this->dbQuery($sql);
        $contactInfo = array('contact' => '', 'contactPhone' => '',
                            'rep' => '', 'repPhone' => '');
        while ( $row = $result->fetch_assoc() ) {
          if ( '_contact' == $row['meta_key'] ) {
            $contactInfo['contact'] = $row['meta_value'];
          } else if ( '_contact_phone' == $row['meta_key'] ) {
            $contactInfo['contactPhone'] = $row['meta_value'];
          } else if ( '_rep' == $row['meta_key'] ) {
            $contactInfo['rep'] = $row['meta_value'];
          } else if ( '_rep_phone' == $row['meta_key'] ) {
            $contactInfo['repPhone'] = $row['meta_value'];
          }
        }
        return $contactInfo;
    }
    private function customerData($orderData){
        $orderNum = array_shift($orderData);
        $orderItemNum = array_shift($orderData);
        $this->orders[$orderNum.":".$orderItemNum]['Data'] = $orderData;
    }
    private function listHTML($orderData){
        $sql = "SELECT `Data` FROM `ovr_lists_data` WHERE ID='".$orderData['num'].":".$orderData['item_num']."'";
        $result = $this->dbQuery($sql);
        $row = $result->fetch_assoc();
        $data = $row['Data'];
        $id = $orderData['num'] . ":" . $orderData['item_num'];

        if ( $data !== "" ) {
            $this->orders[$orderData['num'].":".$orderData['item_num']]['State'] = $data;
        }
        if ( $data == "" ){
            $statusClass = " bg-none";
            $statusIcon = "far fa-square";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        } else if ( $data == "AM" ) {
            $statusClass = " bg-am";
            $statusIcon = "far fa-sun";
            $pickupVisible = " visible-md visible-lg";
            $packageVisible = "";
        } else if ( $data == "Waiver" ) {
            $statusClass = " bg-waiver";
            $statusIcon = "far fa-file-alt";
            $pickupVisible = " visible-md visible-lg";
            $packageVisible = "";
        } else if ( $data == "Product" ) {
            $statusClass = " bg-productrec";
            $statusIcon = "fas fa-ticket-alt";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        } else if ( $data == "PM" ) {
            $statusClass = " bg-pm";
            $statusIcon = "far fa-moon";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        } else if ( $data == "NoShow" ) {
            $statusClass = " bg-noshow";
            $statusIcon = "far fa-times-circle";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        }
        if ( ! isset($orderData['First']) ) {
            $first = "";
        } else {
            $first = $orderData['First'];
        }

        if ( ! isset($orderData['Last']) ) {
            $last = "";
        } else {
          $last = $orderData['Last'];
        }

        if ( ! isset($orderData['Package']) ) {
            $package = "";
        } else {
            $package = $orderData['Package'];
        }
        if ( isset($orderData['To Beach']) ) {
          $toBeach = $orderData['To Beach'];
        }
        if ( isset($orderData['From Beach']) ) {
          $fromBeach = $orderData['From Beach'];
        }
        if ( ! isset($orderData['Phone']) ) {
            $phone = "";
        } else {
            $phone = $orderData['Phone'];
        }

        if ( ! isset( $orderData['Email']) ) {
            $email = "";
        } else {
            $email = $orderData['Email'];
        }

        if ( isset($orderData['Is this guest at least 18 years of age?']) && $orderData['Is this guest at least 18 years of age?'] == "No" ) {
            $underAge = TRUE;
        } else {
            $underAge = FALSE;
        }

        $orderNum = $orderData['num'];

        if ( substr($orderNum,0,2) == "WO" ) {
            $walkOn = TRUE;
            $orderLink = "#";
            $woSQL = "SELECT `Transit To Rockaway`, `Transit From Rockaway` FROM ovr_lists_manual_orders WHERE `ID` = '{$id}'";
            $woResult = $this->dbQuery($woSQL);
            $woRow = $woResult->fetch_assoc();
            if ( isset($woRow['Transit To Rockaway']) ) {
              $toBeach = $woRow['Transit To Rockaway'];
            }
            if ( isset($woRow['Transit From Rockaway']) ) {
              $fromBeach = $woRow['Transit From Rockaway'];
            }
        } else {
            $walkOn = FALSE;
            $orderLink = "https://ovrride.com/wp-admin/post.php?post={$orderNum}&action=edit";
        }

        if ( isset($orderData['Pickup']) ) {
            $pickup = TRUE;
            $pickupName = $orderData['Pickup'];
        } else {
            $pickup = FALSE;
        }
        if ( isset($orderData['Crew']) ){
          switch( $orderData['Crew']) {
            case 'burton':
              $crew = "<img src='images/burton.png' />";
              break;
            case 'patagonia':
              $crew = "<img src='images/patagonia.png' />";
              break;
            case 'ovr':
              $crew = "<img src='images/ovr.png' />";
              break;
            case 'ovr1':
              $crew = "<img src='images/ovr.png' /><i class='far fa-hand-point-up fa-2x' aria-hidden='true'></i>";
              break;
            case 'ovr2':
              $crew = "<img src='images/ovr.png' /><i class='far fa-hand-peace fa-2x' aria-hidden='true'></i>";
              break;
            case 'arcteryx':
              $crew = "<img src='images/arcteryx.png' />";
              break;
            default:
              if ( isset($crew) ) {
                unset($crew);
              }
          }
        }
        // TODO: Setup conditions for rockaway trips
        // TODO: implement mustache.php to use same template on client/server
        // Clear buffer before include to make sure output is clean
        ob_get_clean();
        ob_start();
        include("templates/listButton.php");
        $this->orders[$id]['HTML'] = ob_get_clean();
    }
    private function dbQuery($sql){
        if ( !$result = $this->dbConnect->query($sql)) {
            die('There was an error running the query [' . $this->dbConnect->error . ']');
        } else {
          return $result;
        }
    }
    private function reformatPhone($phone){

        # Strip all formatting
        $phone = str_replace('-','',$phone);
        $phone = str_replace('(','',$phone);
        $phone = str_replace(')','',$phone);
        $phone = str_replace(' ','',$phone);
        $phone = str_replace('.','',$phone);
        if(strlen($phone) == 11)
            $phone = substr($phone,1,10);
        # Add formatting to raw phone num
        $phone = substr_replace($phone,'(',0,0);
        $phone = substr_replace($phone,') ',4,0);
        $phone = substr_replace($phone,'-',9,0);

        return $phone;
    }
    private function removePackagePrice($package){
        return preg_replace('/\(\$\S*\)/', "", $package);
    }
    private function stripTime($pickup){
      # Remove dash and time from pickup
      preg_match("/(.*).-.*/", $pickup, $matched);
      if ( isset($matched[1]) ) {
          return $matched[1];
      } else {
          return $pickup;
      }
    }
    private function splitName($name){
        $parts = explode(" ", $name);
        $last = array_pop($parts);
        $first = implode(" ", $parts);

        return array("First" => $first, "Last" => $last);
    }
}

Flight::register('Lists', 'Lists');
Flight::route('/save/data', function(){
        $list = Flight::Lists();
        $list->saveData();
    }
);
Flight::route('/save/walkon', function(){
        $list = Flight::Lists();
        $list->saveWalkOn();
    }
);
Flight::route('/walkon/delete/@tripId', function($tripId){
        $list = Flight::Lists();
        $list->deleteOrder($tripId);
    }
);
Flight::route('/dropdown/destination', function(){
        $list = Flight::Lists();
        echo $list->destinationDropdown();
    }
);
Flight::route('/dropdown/trip', function(){
        $list = Flight::Lists();
        $list->tripDropdown();
    }
);
Flight::route('/contact/destination/@destination', function($destination){
    $list = Flight::Lists();
    echo json_encode($list->getContactInfo($destination));
});
Flight::route('GET /trip/@tripId/@bus/@status', function($tripId, $bus,$status){
        $list = Flight::Lists();
        $list->getTripInfo($tripId);
        $data = $list->tripData($bus, $tripId, $status);
        echo json_encode($data);
    }
);
Flight::route('GET /trip/@tripId', function( $tripId ){
  $list = Flight::Lists();
  echo json_encode($list->getTripInfo($tripId));
});
Flight::route('GET /reports/@tripId', function($tripId){
  // Returns JSON Array of reports
  $list = Flight::Lists();
  $reports = $list->getReports($tripId);
  if ( $reports ) {
    header('Content-Type: application/json');
    echo json_encode($reports);
  } else {
    http_response_code(404);
  }
});

Flight::route('POST /report/add', function(){
        $list = Flight::Lists();

        $reportAdd = $list->addReport($_POST['bus'], $_POST['tripId'], $_POST['report'], $_POST['time']);
        if ( $reportAdd ) {
          http_response_code(200);
        } else {
          http_response_code(404);
        }
    }
);
Flight::route('/csv/@type/@trip/@status', function($type,$trip,$status){
        $list = Flight::Lists();
        $tripName = $list->getTripName($trip);
        $list->csv($type,$trip,$status, $tripName);
    }
);
Flight::route('/message', function(){
        $lists = Flight::Lists();
        $lists->sendMessage();
        $report = "Message send to: " . $_POST['message']['Group'] .  " Text:" . $_POST['message']['Message'];
        $lists->addReport($_POST['message']['Bus'], $_POST['message']['Trip'], $report, $_POST['message']['Time'] );
    }
);
Flight::start();
?>
