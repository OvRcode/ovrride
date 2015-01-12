<?php
require 'flight/Flight.php';
require 'twilio-php/Services/Twilio.php';

class Lists {
    var $dbConnect;
    var $destinations;
    var $orders;
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
        
    }
    function destinationDropdown(){
        $sql = "SELECT `destination` FROM `ovr_lists_destinations` WHERE `enabled` = 'Y'";
        $result = $this->dbQuery($sql);
        $output = "";
        $this->destinations = array();
        while($row = $result->fetch_assoc()){
            $output .= "<option value='".$row['destination']."'>".$row['destination']."</option>\n";
            if ( $row['destination'] == "Stratton" ) {
                $this->destinations[$row['destination']] = '/Stratturday\s(.*)/i';
            } else if ( $row['destination'] == "Jackson Hole" ) {
                $this->destinations[$row['destination']] = '/\bJackson\sHole\s\b(.*)/i';
            } else {
                $this->destinations[$row['destination']] = '/' . $row['destination'] . '(.*)/i';
            }
        }
        return $output;
    }
    function tripDropdown(){
        $options = "";
        # Find trips for the trip drop down
        $sql = "SELECT DISTINCT `id`, `post_title`
                FROM `wp_posts`
                INNER JOIN `wp_postmeta` ON `wp_posts`.`id` = `wp_postmeta`.`post_id`
                WHERE  (`post_status` =  'publish' OR (`post_status` = 'draft' AND `wp_postmeta`.`meta_value` = 'visible'))
                AND `post_type` =  'product'
                AND `post_title` NOT LIKE  '%High Five%'
                AND `post_title` NOT LIKE  '%Gift%'
                AND `post_title` NOT LIKE '%Beanie%'
                AND `post_title` NOT LIKE '%East Coast Fold Hat%'
                AND `post_title` NOT LIKE '%Good Wood%'
                AND `post_title` NOT LIKE '%East Coast Snapback%'
                ORDER BY `post_title`";
        $result = $this->dbQuery($sql);
        while($row = $result->fetch_assoc()){
            foreach($this->destinations as $destination => $regex){
                if ( preg_match( $regex, $row['post_title'], $match) ){
                    $HTMLClass = $destination;
                    $label = $match[1];
                    break;
                }
            }
            if ( isset($HTMLClass) ){
                $options .= "<option value='" . $row['id'] . "' class='" . $HTMLClass . "'>" . $label . "</option>\n";
                unset($HTMLClass);
            }
        }
        echo $options;
    }
    function csv($type,$trip,$status){
        $orders = $this->tripData("All",$trip,$status);
        $output = "";
        if ( $type == "list" ){
            $header = "First, Last, Phone, Pickup, Package, Order, AM, Waiver, Product Rec, PM\n";
            $output .= $header;
            foreach ( $orders as $ID => $data ) {
                $order = preg_split("/:/",$ID);
                $order = $order[0];
                $row = "\"" . $data['Data']['First'] . "\",\"" . $data['Data']['Last'] . "\",\"" . $data['Data']['Phone'] . "\"";
                if ( isset($data['Data']['Pickup']) ){
                    $row .= ",\"" . $data['Data']['Pickup'] . "\"";
                } else{
                    $row .= ",\"X\"";
                }
                $row .= ",\"" . $data['Data']['Package'] . "\",\"" . $order . "\"";
                
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
            $header ="Email, First, Last, Package, Pickup\n";
            $output .= $header;
            foreach( $orders as $ID => $data ) {
                $row = "";
                if ( isset($data['Data']['Email']) ) {
                    $row .= "\"" . $data['Data']['Email'] . "\"";
                } else {
                    $row .= "\"none\"";
                }
                $row .= ",\"" . $data['Data']['First'] . "\",\"" . $data['Data']['Last'] . "\",\"" . $data['Data']['Package'] . "\",\"" . $data['Data']['Pickup'] . "\"\n";
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
                        if (isset($row['Pickup']) && $row['Pickup'] != "No Pickup") {
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
    function getNotes($tripId){
        $notes = array();
        $sql = "SELECT * FROM `ovr_lists_notes` WHERE `Trip` ='" . $tripId . "'";
        $result = $this->dbQuery($sql);
        while( $row = $result->fetch_assoc() ){
            $notes[$row['Time']]="Bus " . $row['Bus'] . ": " .$row['Note'];
        }
        return $notes;
    }
    function addNote($bus, $tripId, $note){
        $sql = "INSERT INTO `ovr_lists_notes` (Bus, Trip, Note) VALUES('" . $bus . "','" . $tripId . "', '" . urldecode($note) . "')";
        if ( $this->dbQuery($sql) )
            return "success";
        else
            return "fail";
    }
    function getAllDestinations(){
        // returns array of destinations with status
        $sql = "SELECT * FROM `ovr_lists_destinations`";
        $result = $this->dbQuery($sql);
        $destination = [];
        while( $row = $result->fetch_assoc()){
            $destination[$row['destination']] = $row['enabled'];
        }
        return $destination;
    }
    function updateDestinations($destination, $enabled){
        if ( $enabled == "Delete" ) {
            $sql = "DELETE FROM `ovr_lists_destinations` WHERE `destination` = '" . $destination . "'";
        } else {
            $sql = "INSERT INTO `ovr_lists_destinations` (destination, enabled) 
                    VALUES('" . $destination . "', '" . $enabled ."')
                    ON DUPLICATE KEY UPDATE
                    enabled=VALUES(enabled)";
        }
        $this->dbQuery($sql);
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
        $AccountSid = getenv('TWILIO_SID');
        $AuthToken = getenv('TWILIO_AUTH');
 
        $client = new Services_Twilio($AccountSid, $AuthToken);
        $postData = $_POST['message'];
        $recipients = $postData['Recipients'];
        $message = $postData['Message'];
        foreach( $recipients as $phoneNum ) {
            $message = $client->account->messages->create(array(
                 "From" => "+16467629375",
                 "To" => $phoneNum,
                 "Body" => $message
             ));
 
             // Display a confirmation message on the screen
             echo "Sent message {$message->sid}";
        }
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
        if ( $data !== "" ) {
            $this->orders[$orderData['num'].":".$orderData['item_num']]['State'] = $data;
        }
        if ( $data == "" ){
            $htmlClass = "bg-none";
            $icon = "";
            $pickupVisible = "";
            $packageVisible = "visible-md visible-lg";
        } else if ( $data == "AM" ) {
            $htmlClass = "bg-am";
            $icon = "<i class='fa fa-sun-o fa-lg'></i>";
            $pickupVisible = "visible-md visible-lg";
            $packageVisible = "";
        } else if ( $data == "Waiver" ) {
            $htmlClass = "bg-waiver";
            $icon = "<i class='fa fa-file-word-o fa-lg'></i>";
            $pickupVisible = "visible-md visible-lg";
            $packageVisible = "";
        } else if ( $data == "Product" ) {
            $htmlClass = "bg-productrec";
            $icon = "<i class='fa fa-ticket fa-lg'></i>";
            $pickupVisible = "";
            $packageVisible = "visible-md visible-lg";
        } else if ( $data == "PM" ) {
            $htmlClass = "bg-pm";
            $icon = "<i class='fa fa-moon-o fa-lg'></i>";
            $pickupVisible = "";
            $packageVisible = "visible-md visible-lg";
        } else if ( $data == "NoShow" ) {
            $htmlClass = "bg-noshow";
            $icon = "<i class='fa fa-exclamation-triangle fa-lg'></i>";
            $pickupVisible = "";
            $packageVisible = "visible-md visible-lg";
        }
        $output = <<<AAA
            <div class="row listButton {$htmlClass}" id="{$orderData['num']}:{$orderData['item_num']}">
              <div class="row primary">
                  <div class="buttonCell name col-xs-7 col-md-4">
                  <span class="icon">{$icon}</span>
                      <span class="first">&nbsp;{$orderData['First']}</span>
                      <span class="last">{$orderData['Last']}</span>
                  </div>
                <div class="noClick buttonCell col-md-2 visible-md visible-lg">
AAA;
        if ( substr($orderData['num'],0,2) == "WO" ) {
            $output .= 'Order: <span class="orderNum">' . $orderData['num'] . '</span></a>';
        } else {
            $output .= 'Order:<a href="https://ovrride.com/wp-admin/post.php?post=' . $orderData['num'] . '&action=edit">';
            $output .= '<span class="orderNum">' . $orderData['num'] . '</span></a>';
        }
        $output .= "</div>";

        if ( isset($orderData['Pickup']) ) {
            $output .= '<div class="buttonCell col-xs-5 col-md-3 flexPickup ' . $pickupVisible . '">'.$orderData['Pickup'].'</div>';
        }
        $output .=<<<BBB
                <div class="buttonCell col-xs-5 col-md-3 flexPackage {$packageVisible}"> {$orderData['Package']}</div>
              </div>
              <div class="expanded">
              <div class="row">
                  <div class="buttonCell col-xs-5 col-md-6">
                      <strong>Package:</strong> {$orderData['Package']}
                  </div>
BBB;
        if ( isset($orderData['Pickup']) ) {
            $output .= '<div class="buttonCell col-xs-12 col-md-6">';
            $output .= '<strong>Pickup:</strong> ' . $orderData['Pickup'] . '</div>';
        }
        $output .=<<<CCC
              </div>
              <div class="row">
                <div class="buttonCell col-xs-12 col-md-6">
                     <strong>Order:</strong> 
                     <a href="https://ovrride.com/wp-admin/post.php?post={$orderData['num']}&action=edit">
                         {$orderData['num']}
                     </a>
                </div>
                <div class="buttonCell col-xs-12 col-md-6">
                    <strong>Phone:</strong> <a href="tel:{$orderData['Phone']}"><span class="phone">{$orderData['Phone']}</span></a>
                </div>
              </div>
CCC;
        if ( isSet($orderData['Email']) ){
            $output.=<<<DDD
                <div class="row">
                  <div class="buttonCell col-xs-12 col-md-6">
                    <strong>Email:</strong> <a href="mailto:{$orderData['Email']}"><span class="email">{$orderData['Email']}</span></a>
                  </div>
                </div>
DDD;
        }
        $output .=<<<EEE
              <div class="row">
                <br />
                <div class="buttonCell col-xs-4">
                    <button class="btn btn-info" id="{$orderData['num']}:{$orderData['item_num']}:Reset">
                        Reset
                    </button>
                </div>
                <div class="buttonCell col-xs-4">
                    <button class="btn btn-warning" id="{$orderData['num']}:{$orderData['item_num']}:NoShow">
                        No Show
                    </button>
                </div>
EEE;
        if ( substr($orderData['num'],0,2) == "WO" ) {
            $output .=<<<FFF
                <div class="buttonCell col-xs-4">
                    <button class="btn btn-danger" id="{$orderData['num']}:{$orderData['item_num']}:Delete">
                        Remove Order
                    </button>
                </div>
FFF;
        }
        $output .= "</div></div></div>";

        $ID = $orderData['num'].":".$orderData['item_num'];
        $this->orders[$ID]['HTML'] = $output;
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

session_regenerate_id();
session_start();

# Start Session with a 1 day persistent session lifetime
$cookieLifetime = 60 * 60 * 24 * 1;
setcookie(session_name(),session_id(),time()+$cookieLifetime);

# Session Validation - Is User logged in?
# else redirect to login page
if (!(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] != ''))
  header ("Location: /login/index.php");
else {
    Flight::register('Lists', 'Lists');
    Flight::route('/save/data', function(){
        $list = Flight::Lists();
        $list->saveData();
    });
    Flight::route('/save/walkon', function(){
        $list = Flight::Lists();
        $list->saveWalkOn();
    });
    Flight::route('/walkon/delete/@tripId', function($tripId){
        $list = Flight::Lists();
        $list->deleteOrder($tripId);
    });
    Flight::route('/dropdown/destination', function(){
        $list = Flight::Lists();
        echo $list->destinationDropdown();
    });
    Flight::route('/dropdown/trip', function(){
        $list = Flight::Lists();
        $list->destinationDropdown();
        $list->tripDropdown();
    });
    Flight::route('/dropdown/destination/all', function(){
        $list = Flight::Lists();
        echo json_encode($list->getAllDestinations());
    });
    Flight::route('POST /dropdown/destination/update', function(){
        $list = Flight::Lists();
        $list->updateDestinations($_POST['destination'], $_POST['enabled']);
    });
    Flight::route('/trip/@tripId/@bus/@status', function($tripId, $bus,$status){ 
        $list = Flight::Lists();
        echo json_encode($list->tripData($bus, $tripId, $status));
    });
    Flight::route('/notes/@tripId', function($tripId){
        $list = Flight::Lists();
        echo json_encode($list->getNotes($tripId));
    });
    Flight::route('/notes/add/@bus/@tripId/@note', function($bus,$tripId, $note){
        $list = Flight::Lists();
        echo $list->addNote($bus, $tripId, $note);
    });
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
    });
    Flight::route('/message', function(){
        $lists = Flight::Lists();
        $lists->sendMessage();
        $note = "Message send to: " . $_POST['message']['Group'] .  " Text:" . $_POST['message']['Message'];
        $lists->addNote($_POST['message']['Bus'], $_POST['message']['Trip'], $note );
    });
    Flight::start();
}
?>