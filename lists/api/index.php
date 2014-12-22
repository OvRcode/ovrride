<?php
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
    function tripData($tripId, $status){
        $statuses = explode(',',$status);
        foreach($statuses as $single){
            if ( $single == "walk-on" ) {
                # Walkon logic here
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
                        } else if ( $detailRow['meta_key'] == 'Transit To Rockaway' || $detailRow['meta_key'] == 'Transit From Rockaway') {
                            $orderData[$detailRow['meta_key']] = ucwords(strtolower($detailRow['meta_value']));
                        } else if ( $detailRow['meta_key'] == 'Name' ) {
                            $names = $this->splitName($detailRow['meta_value']);
                            $orderData['First'] = stripcslashes(ucwords(strtolower($names['First'])));
                            $orderData['Last']  = stripcslashes(ucwords(strtolower($names['Last'])));
                        } else {
                            $orderData[$detailRow['meta_key']] = trim($detailRow['meta_value']);
                        }
                    }
                    // Assemble output HTML HERE
                    $this->listHTML($orderData);
                    $this->customerData($orderData);
                }
            }
        }
        return $this->orders;
    }
    private function customerData($orderData){
        $orderNum = array_shift($orderData);
        $orderItemNum = array_shift($orderData);
        $this->orders[$orderNum.":".$orderItemNum]['Data'] = $orderData;
    }
    private function listHTML($orderData){
        $output = <<<AAA
            <div class="row listButton bg-none" id="{$orderData['num']}:{$orderData['item_num']}">
              <div class="row primary">
                <div class="buttonCell col-xs-4 col-md-2">{$orderData['First']}</div>
                <div class="buttonCell col-xs-4 col-md-2">{$orderData['Last']}</div>
                <div class="noClick buttonCell col-md-2 visible-md visible-lg">
                    Order:<a href="https://ovrride.com/wp-admin/post.php?post={$orderData['num']}&action=edit">       
                            {$orderData['num']}</a>
                </div>
AAA;
        if ( isset($orderData['Pickup']) ) {
            $output .= '<div class="buttonCell col-xs-4 col-md-3 flexPickup">'.$orderData['Pickup'].'</div>';
        }
        $output .=<<<BBB
                <div class="buttonCell col-xs-4 col-md-3 flexPackage visible-md visible-lg"> {$orderData['Package']}</div>
              </div>
              <div class="expanded">
              <div class="row">
                  <div class="buttonCell col-xs-12 col-md-6">
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
                    <strong>Phone:</strong> <a href="tel:{$orderData['Phone']}">{$orderData['Phone']}</a>
                </div>
              </div>
              <div class="row">
                <div class="buttonCell col-xs-12 col-md-6">
                  <strong>Email:</strong> <a href="mailto:{$orderData['Email']}">{$orderData['Email']}</a>
                </div>
              </div>
              <div class="row">
                <br />
                <div class="buttonCell col-xs-6">
                    <button class="btn btn-warning" id="{$orderData['num']}:{$orderData['item_num']}:Reset">
                        Reset
                    </button>
                </div>
                <div class="buttonCell col-xs-6">
                    <button class="btn btn-danger" id="{$orderData['num']}:{$orderData['item_num']}:NoShow">
                        No Show
                    </button>
                </div>
              </div>
              </div>
            </div>
CCC;
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
    // TODO: Need to finish tripData function, needs walk-on data and save
    // TODO: Offline functionality
}
require 'flight/Flight.php';


Flight::register('Lists', 'Lists');

Flight::route('/dropdown/destination', function(){
    $list = Flight::Lists();
    echo $list->destinationDropdown();
});
Flight::route('/dropdown/trip', function(){
    $list = Flight::Lists();
    $list->destinationDropdown();
    $list->tripDropdown();
});
Flight::route('/trip/@tripId/@status', function($tripId,$status){ 
    $list = Flight::Lists();
    echo json_encode($list->tripData($tripId, $status));
});
Flight::start();
?>