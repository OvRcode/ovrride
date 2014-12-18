<?php
class Lists {
    var $dbConnect;
    var $destinations;
    
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
    private function dbQuery($sql){
        if ( !$result = $this->dbConnect->query($sql)) {
            die('There was an error running the query [' . $this->dbConnect->error . ']');
        } else {
          return $result;
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
        # Find trips for the Select a Trip drop down
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
                    echo $row['ID']." ". $row['order_item_id'] . " " . $row['post_status'] . "<br >";
                    # NEST PHONE AND META DATA QUERIES!!
                }
            }
        }
    }
    // TODO: Need to finish tripData function, working on settings page right now
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
    $list->tripData($tripId, $status);
});
Flight::start();
?>