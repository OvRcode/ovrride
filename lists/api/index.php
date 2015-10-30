<?php
require 'flight/Flight.php';
require 'twilio-php/Services/Twilio.php';

class Lists {
    var $dbConnect;
    var $destinations;
    var $trips;
    var $orders;
    var $pickup;
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
        foreach ( $this->destinations as $index => $destination) {
          $output .= "<option value='{$destination}'>{$destination}</option>\n";
        }

        return $output;
    }
    function tripDropdown(){
        $options = "";
        foreach( $this->destinations as $index => $destination) {
          if ( isset( $this->trips[$destination] ) ) {
            foreach( $this->trips[$destination] as $id => $info) {
                $sql = "SELECT `meta_value` FROM `wp_postmeta` WHERE `post_id` = '{$id}' AND `meta_key` = '_wc_trip_start_date' LIMIT 1";
                $result = $this->dbQuery($sql);
                $result = $result->fetch_assoc();
                $options .= "<option value='{$id}' class='{$destination}' data-date='{$result['meta_value']}'>{$info['title']}</option>\n";
              }
          }
        }
        echo $options;
    }
    function csv($type,$trip,$status){
        $orders = $this->tripData("All",$trip,$status);
        $output = "";
        if ( $type == "list" ){
            if ( $this->pickup ) {
                $header = "First, Last, Phone, Pickup, Package, Order, AM, Waiver, Product Rec, PM\n";
            } else {
                $header = "First, Last, Phone, Package, Order, AM, Waiver, Product Rec, PM\n";
            }
            $output .= $header;
            foreach ( $orders as $ID => $data ) {
                $first      = ( isset( $data['Data']['First'] ) ? $data['Data']['First'] : '' );
                $last       = ( isset( $data['Data']['Last'] ) ? $data['Data']['Last'] : '' );
                $phone      = ( isset( $data['Data']['Phone'] ) ? $data['Data']['Phone'] : '' );
                $pickup     = ( isset( $data['Data']['Pickup'] ) ? $data['Data']['Pickup'] : 'X' );
                $package    = ( isset( $data['Data']['Package'] ) ? $data['Data']['Package'] : '' );

                $order = preg_split("/:/",$ID);
                $order = $order[0];
                $row = "\"" . $first . "\",\"" . $last . "\",\"" . $phone . "\"";
                $row .= ",\"" . $pickup . "\"";
                $row .= ",\"" . $package . "\",\"" . $order . "\"";

                if ( $data['State'] == "AM" ) {
                    $state = ",\"X\",\"\",\"\",\"\"\n";
                } else if ( $data['State'] == "Waiver" ) {
                    $state = ",\"X\",\"X\",\"\",\"\"\n";
                } else if ( $data['State'] == "Product" ) {
                    $state = ",\"X\",\"X\",\"X\",\"\"\n";
                } else if ( $data['State'] == "PM" ) {
                    $state = ",\"X\",\"X\",\"X\",\"X\"\n";
                } else {
                    $state = ",\"\",\"\",\"\",\"\"\n";
                }
                $row .= $state;
                $output .= $row;
            }
        } else if ( $type = "email" ) {
            if ( $this->pickup ){
                $header ="Email, First, Last, Package, Pickup\n";
            } else {
                $header ="Email, First, Last, Package\n";
            }
            $output .= $header;
            foreach( $orders as $ID => $data ) {
                $first = (isset($data['Data']['First']) ? $data['Data']['First'] : '');
                $last = (isset($data['Data']['Last']) ? $data['Data']['Last'] : '');
                $package = (isset($data['Data']['Package']) ? $data['Data']['Package'] : '');
                $pickup = (isset($data['Data']['Pickup']) ? $data['Data']['Pickup'] : '');
                $email = ( isset($data['Data']['Email']) ? $data['Data']['Email'] : 'none');
                $row = "";
                $row .= "\"" . $email . ",\"";
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
        }
    }
    function getTripName($trip){
        $sql = "select post_title from wp_posts where ID = '" . $trip . "'";
        $result = $this->dbQuery($sql);
        $name = $result->fetch_assoc();
        return $name['post_title'];
    }
    function tripData($bus, $tripId, $status){
        $this->pickup = FALSE;
        /* Get saved trip data and sort into array based on bus # */
        $busSql = "select ID,Bus from ovr_lists_data where Trip='" . $tripId . "'";
        $busResult = $this->dbQuery($busSql);
        $busData = [];
        $busData[$bus] = [];
        $busData["Other"] = [];
        while( $busRow = $busResult->fetch_assoc() ) {
            if ( $busRow['Bus'] !== 0 ){
                if ( $busRow['Bus'] == $bus ){
                    $busData[$busRow['Bus']][] = $busRow['ID'];
                } else {
                    $busData["Other"][] = $busRow['ID'];
                }
            }
        }
        $statuses = explode(',',$status);
        foreach($statuses as $single){
            if ( $single == "walk-on" ) {
                $sql = "SELECT * FROM `ovr_lists_manual_orders` WHERE `Trip` = '" . $tripId . "'";
                $result = $this->dbQuery($sql);
                while($row = $result->fetch_assoc()){
                    if ( $bus == "All" || array_search($row['ID'], $busData[$bus]) !== FALSE ||
                        array_search($row['ID'], $busData['Other']) === FALSE) {
                        $walkOnOrder = [];
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
                        $walkOnOrder['Bus'] = (isset($row['Bus']) ? $row['Bus'] : "");
                        $this->listHTML($walkOnOrder);
                        $this->customerData($walkOnOrder);
                    }
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

                        # Get phone number
                        $phoneSql = "SELECT  `meta_value` AS  `Phone`
                            FROM wp_postmeta
                            WHERE meta_key =  '_billing_phone'
                            AND post_id =  '$order'";
                        $phoneResult = $this->dbQuery($phoneSql);
                        $phoneRow = $phoneResult->fetch_assoc();
                        $orderData['Phone'] = $this->reformatPhone($phoneRow['Phone']);
                        # Get meta details
                        $detailSql = "SELECT `meta_key`, `meta_value`
                            FROM `wp_woocommerce_order_itemmeta`
                            WHERE ( `meta_key` = 'Name'
                            OR `meta_key` = 'Email'
                            OR `meta_key` = 'Package'
                            OR `meta_key` = 'Pickup'
                            OR `meta_key` = 'Pickup Location'
                            OR `meta_key` = 'Is This Guest At Least 21 Years Of Age?'
                            OR `meta_key` = 'Transit To Rockaway'
                            OR `meta_key` = 'Transit From Rockaway')
                            AND `order_item_id` = '$orderItem'";
                        $detailResult = $this->dbQuery($detailSql);
                        while($detailRow = $detailResult->fetch_assoc()){
                            if ( $detailRow['meta_key'] == 'Package' ) {
                                $orderData['Package'] = ucwords(strtolower($this->removePackagePrice($detailRow['meta_value'])));
                            } else if ( $detailRow['meta_key'] == 'Pickup' || $detailRow['meta_key'] == 'Pickup Location') {
                                $orderData['Pickup'] = ucwords(strtolower($this->stripTime($detailRow['meta_value'])));
                            } else if ( $detailRow['meta_key'] == 'Transit To Rockaway' ||
                                        $detailRow['meta_key'] == 'Transit From Rockaway') {
                                $orderData[$detailRow['meta_key']] = ucwords(strtolower($detailRow['meta_value']));
                            } else if ( $detailRow['meta_key'] == 'Name' ) {
                                $names = $this->splitName($detailRow['meta_value']);
                                $orderData['First'] = stripcslashes(ucwords(strtolower($names['First'])));
                                $orderData['Last']  = stripcslashes(ucwords(strtolower($names['Last'])));
                            } else {
                                $orderData[$detailRow['meta_key']] = trim($detailRow['meta_value']);
                                if ( $detailRow['meta_key'] == 'Pickup' && $this->pickup === FALSE ) {
                                    $this->pickup = TRUE;
                                }
                            }
                        }

                        $this->listHTML($orderData);
                        $this->customerData($orderData);
                    }
                }
            }
        }
        return $this->orders;
    }
    function getReports($tripId){
        $reports = array();
        $sql = "SELECT * FROM `ovr_lists_reports` WHERE `Trip` ='" . $tripId . "'";
        $result = $this->dbQuery($sql);
        while( $row = $result->fetch_assoc() ){
            $reports[$row['Time']]="Bus " . $row['Bus'] . ": " .$row['Report'];
        }
        return $reports;
    }
    function addReport($bus, $tripId, $report){
        $sql = "INSERT INTO `ovr_lists_reports` (Bus, Trip, Report) VALUES('" . $bus . "','" . $tripId . "', '" . urldecode($report) . "')";
        if ( $this->dbQuery($sql) )
            return "success";
        else
            return "fail";
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
            if ( substr($ID,0,2) == "WO" ) {
                $sql = "UPDATE `ovr_lists_manual_orders` SET `Bus` = '" . $field['Bus'] . "' WHERE ID ='" . $ID . "'";
                $this->dbQuery($sql);
            }
        }
    }
    function saveWalkOn(){
        foreach( $_POST['walkon'] as $ID => $fields ) {
            if ( ! isset($fields['Pickup']) ){
                $fields['Pickup'] = "";
            }
            $sql = "INSERT INTO `ovr_lists_manual_orders` (ID, First, Last, Pickup, Phone, Package, Trip, Bus)
                    VALUES('" . $ID . "', '" . $fields['First'] . "', '" . $fields['Last']. "', '" . $fields['Pickup']. "',
                    '" . $fields['Phone']. "', '" . $fields['Package'] . "', '" . $fields['Trip']. "', '" . $fields['Bus'] . "')
                    ON DUPLICATE KEY UPDATE
                    First=VALUES(First), Last=VALUES(Last), Pickup=VALUES(Pickup), Phone=VALUES(Phone), Package=VALUES(Package),
                    Trip=VALUES(Trip), Bus=VALUES(Bus)";
            $this->dbQuery($sql);
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
        $sql = "SELECT `contact`, `contactPhone`, `rep`, `repPhone` FROM `ovr_lists_destinations` WHERE `destination` ='" . $destination . "'";
        $result = $this->dbQuery($sql);
        $row = $result->fetch_assoc();
        return array('contact'     => $row['contact'],
                    'contactPhone' => $row['contactPhone'],
                    'rep'          => $row['rep'],
                    'repPhone'     => $row['repPhone']
                    );
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
            $statusIcon = "fa-square-o";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        } else if ( $data == "AM" ) {
            $statusClass = " bg-am";
            $statusIcon = "fa-sun-o";
            $pickupVisible = " visible-md visible-lg";
            $packageVisible = "";
        } else if ( $data == "Waiver" ) {
            $statusClass = " bg-waiver";
            $statusIcon = "fa-file-word-o";
            $pickupVisible = " visible-md visible-lg";
            $packageVisible = "";
        } else if ( $data == "Product" ) {
            $statusClass = " bg-productrec";
            $statusIcon = "fa-ticket";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        } else if ( $data == "PM" ) {
            $statusClass = " bg-pm";
            $statusIcon = "fa-moon-o";
            $pickupVisible = "";
            $packageVisible = " visible-md visible-lg";
        } else if ( $data == "NoShow" ) {
            $statusClass = " bg-noshow";
            $statusIcon = "fa-times-circle-o";
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

        if ( isset($orderData['Is this guest at least 21 years of age?']) && $orderData['Is this guest at least 21 years of age?'] == "No" ) {
            $underAge = TRUE;
        } else {
            $underAge = FALSE;
        }

        $orderNum = $orderData['num'];

        if ( substr($orderNum,0,2) == "WO" ) {
            $walkOn = TRUE;
            $orderLink = "#";
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
        $list->destinationDropdown();
        $list->tripDropdown();
    }
);
Flight::route('/contact/destination/@destination', function($destination){
    $list = Flight::Lists();
    echo json_encode($list->getContactInfo($destination));
});
Flight::route('/trip/@tripId/@bus/@status', function($tripId, $bus,$status){
        $list = Flight::Lists();
        echo json_encode($list->tripData($bus, $tripId, $status));
    }
);
Flight::route('/reports/@tripId', function($tripId){
        $list = Flight::Lists();
        echo json_encode($list->getReports($tripId));
    }
);
Flight::route('POST /report/add', function(){
        $list = Flight::Lists();
        echo $list->addReport($_POST['bus'], $_POST['tripId'], $_POST['report']);
    }
);
Flight::route('/csv/@type/@trip/@status', function($type,$trip,$status){
        $list = Flight::Lists();
        $tripName = $list->getTripName($trip);
        $fileName = date("m-d-Y") . " - " . $tripName . " - " . $type . ".csv";
        //Gets CSV data from POST and returns file download
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=".$fileName);
        header("Pragma: no-cache");
        header("Expires: 0");
        echo $list->csv($type,$trip,$status);;
    }
);
Flight::route('/message', function(){
        $lists = Flight::Lists();
        $lists->sendMessage();
        $report = "Message send to: " . $_POST['message']['Group'] .  " Text:" . $_POST['message']['Message'];
        $lists->addReport($_POST['message']['Bus'], $_POST['message']['Trip'], $report );
    }
);
Flight::start();
?>
