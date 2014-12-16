<?php
/**
 * OvR Lists - Functions and Definitions
 *
 * @package OvR Lists
 * @since Version 0.0.2
 */

class TripList{
    var $dbConnect;
    var $destinations;
    var $orders;
    var $orderData;
    var $options;
    var $checkboxes;

    function __construct(){
        # Connect to database
        require_once("config.php");
        $this->dbConnect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if($this->dbConnect->connect_errno > 0){
            die('Unable to connect to database [' . $this->dbConnect->connect_error . ']');
        }
        else{
          $this->dbConnect->query("SET NAMES utf8");
          $this->dbConnect->query("SET CHARACTER SET utf8");
          $this->dbConnect->query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
        }
        $this->destinations = array("Asbury Park","Austria","Breckenridge","Camelbeach","Camelback MT","Hunter MT","Jackson Hole",
                                    "Japan","Jay Peak","Killington","Lake Tahoe","MT Snow","Northern Argentina",
                                    "Northern Chile","Okemo","Paintball","Rockaway Beach",
                                    "Skydiving","Snowbird","Southern Argentina","Southern Chile","Stowe",
                                    "Stratton","Sugarbush","Tap New York","Tough Mudder","Whistler","Whitewater Weekend","Windham");

        $this->checkboxes = array("AM","PM","Waiver","Product");
    }
    function dbQuery($sql){
        if ( !$result = $this->dbConnect->query($sql)) {
            die('There was an error running the query [' . $this->dbConnect->error . ']');
        } else {
          return $result;
        }
    }
    function tripOptions(){
        $options = array();
        # Find trips for the Select a Trip drop down
        $sql = "SELECT DISTINCT `id`, `post_title`
                FROM `wp_posts`
                INNER JOIN `wp_postmeta` ON `wp_posts`.`id` = `wp_postmeta`.`post_id`
                WHERE  (`post_status` =  'publish' OR `post_status` = 'private')
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
             foreach($this->destinations as $value){
               if ($value == "Stratton") {
                   $regex = '/Stratturday\s(.*)/i';
               } else if ($value == "Jackson Hole") {
                   $regex = '/\bJackson\sHole\s\b(.*)/i';
               } else {
                 $regex = '/'.$value.'(.*)/i';
                }

               if(preg_match($regex,$row['post_title'],$match)){
                 $HTMLclass = $value;
                 $label = $match[1];
               }

             }
             $this->options['destinations'] = $this->destinations;
             if(isset($HTMLclass))
               $this->options['trip'][$HTMLclass][$row['id']] = $label;
         }
    }
    function tripData($tripId){
        // Select ALL orders for trip regardless of status, statuses will be sorted in JavaScript
        $sql = "SELECT `wp_posts`.`ID`, `wp_woocommerce_order_items`.`order_item_id`, `wp_posts`.`post_status`
            FROM `wp_posts`
            INNER JOIN `wp_woocommerce_order_items` ON `wp_posts`.`id` = `wp_woocommerce_order_items`.`order_id`
            INNER JOIN `wp_woocommerce_order_itemmeta` ON `wp_woocommerce_order_items`.`order_item_id` = `wp_woocommerce_order_itemmeta`.`order_item_id`
            WHERE `wp_posts`.`post_type` =  'shop_order'
            AND `wp_woocommerce_order_items`.`order_item_type` =  'line_item'
            AND `wp_woocommerce_order_itemmeta`.`meta_key` =  '_product_id'
            AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '$tripId'";
        $result = $this->dbQuery($sql);
        $this->orders = array();
        while($row = $result->fetch_assoc()){
            $this->orders[$row['ID']][$row['order_item_id']] = substr($row['post_status'],3);
        }
        $result->free();

        foreach ( $this->orders as $order => $data ) {
            # Get phone number
            $sql = "SELECT  `meta_value` AS  `Phone`
                    FROM wp_postmeta
                    WHERE meta_key =  '_billing_phone'
                    AND post_id =  '$order'";
            $result = $this->dbQuery($sql);
            $row = $result->fetch_assoc();
            $phone = $this->reformatPhone($row['Phone']);

            foreach ( $data as $orderItem => $status ) {
                $this->orderData[$order][$orderItem]['Phone'] = $phone;
                $this->orderData[$order][$orderItem]['Status'] = $status;
                $sql = "SELECT `meta_key`, `meta_value` 
                        FROM `wp_woocommerce_order_itemmeta`
                        WHERE ( `meta_key` = 'Name'
                        OR `meta_key` = 'Email'
                        OR `meta_key` = 'Package'
                        OR `meta_key` = 'Pickup'
                        OR `meta_key` = 'Pickup Location'
                        OR `meta_key` = 'Transit To Rockaway'
                        OR `meta_key` = 'Transit From Rockaway')
                        AND `order_item_id` = '$orderItem'";
                $result = $this->dbQuery($sql);
                while ($row = $result->fetch_assoc()) {
                    if ($row['meta_key'] == 'Package') {
                        $this->orderData[$order][$orderItem]['Package'] = ucwords(strtolower($this->removePackagePrice($row['meta_value'])));
                    } elseif($row['meta_key'] == 'Pickup' || $row['meta_key'] == 'Pickup Location' ||
                    $row['meta_key'] == 'Transit To Rockaway'|| $row['meta_key'] == 'Transit From Rockaway') {
                      if ($row['meta_key'] == 'Pickup' || $row['meta_key'] == 'Pickup Location')
                        $this->orderData[$order][$orderItem]['Pickup'] = ucwords(strtolower($this->stripTime($row['meta_value'])));
                      else
                        $this->orderData[$order][$orderItem][$row['meta_key']] = ucwords(strtolower($row['meta_value']));
                    }
                    elseif($row['meta_key'] == 'Name'){
                        $names = $this->splitName($row['meta_value']);
                        $this->orderData[$order][$orderItem]['First'] = stripcslashes(ucwords(strtolower($names['First'])));
                        $this->orderData[$order][$orderItem]['Last'] = stripcslashes(ucwords(strtolower($names['Last'])));
                    } else {
                        $this->orderData[$order][$orderItem][$row['meta_key']] = trim($row['meta_value']);
                    }
                }
                $this->getCheckboxStates($order,$orderItem);
            }
        }
    }
    function getManualOrders($tripId){
        $sql = "SELECT  `ID` ,  `First` ,  `Last` ,  `Pickup` ,  `Phone` ,  `Package`, `Transit To Rockaway`, `Transit From Rockaway`
                FROM  `ovr_lists_manual_orders` 
                WHERE  `Trip` =  '$tripId'";
        $result = $this->dbQuery($sql);
        while($row = $result->fetch_assoc()){

            $explodedId = explode(":",$row['ID']);
            $order = $explodedId[0];
            $orderItem = $explodedId[1];
            $this->orderData[$order][$orderItem]['First'] = stripcslashes(ucwords(strtolower(trim($row['First']))));
            $this->orderData[$order][$orderItem]['Last'] = stripcslashes(ucwords(strtolower(trim($row['Last']))));
            if ( isset($row['Pickup']) )
              $this->orderData[$order][$orderItem]['Pickup'] = stripcslashes(ucwords(strtolower(trim($row['Pickup']))));
            if ( isset($row['Transit To Rockaway']) )
              $this->orderData[$order][$orderItem]['Transit To Rockaway'] = stripcslashes(ucwords(strtolower(trim($row['Transit To Rockaway']))));
            if ( isset($row['Transit From Rockaway']) )
              $this->orderData[$order][$orderItem]['Transit From Rockaway'] = stripcslashes(ucwords(strtolower(trim($row['Transit From Rockaway']))));
            $this->orderData[$order][$orderItem]['Phone'] = $this->reformatPhone($row['Phone']);
            $this->orderData[$order][$orderItem]['Package'] = stripcslashes(ucwords(strtolower(trim($row['Package']))));
            $this->orderData[$order][$orderItem]['Status'] = 'walk-on';
            $this->getCheckboxStates($order,$orderItem);
        }
    }
    function getCheckboxStates($order,$orderItem){
        # Attempts to lookup set checkboxes from form in ovr_lists_checkboxes table
        $id = $order.":".$orderItem;
        $sql = "SELECT `ID`, `value` FROM `ovr_lists_fields` WHERE `ID` LIKE CONCAT('$id','%')";
        $result = $this->dbQuery($sql);
        while ($row = $result->fetch_assoc()) {
          $label = explode(':',$row['ID']);
          if($label[2] != 'Transit To Rockaway' && $label[2] != 'Transit From Rockawa'){
            $this->orderData[$order][$orderItem][$label[2]] = $row['value'];
          }
        }
        foreach ($this->checkboxes as $field) {
         
          if (!isset($this->orderData[$order][$orderItem][$field])){
            $this->orderData[$order][$orderItem][$field] = "";
          }
        }
    }
    function reformatPhone($phone){

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
    function splitName($name){
        $parts = explode(" ", $name);
        $last = array_pop($parts);
        $first = implode(" ", $parts);

        return array("First" => $first, "Last" => $last); 
    }
    function stripTime($pickup){
      # Remove dash and time from pickup
      preg_match("/(.*).-.*/", $pickup, $matched);
      if ( isset($matched[1]) ) {
          return $matched[1];
      } else {
          return $pickup;
      }
    }
    function removePackagePrice($package){
        return preg_replace('/\(\$\S*\)/', "", $package);
    }
}
?>
