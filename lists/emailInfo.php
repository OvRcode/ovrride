<?php
// TODO: Disabled due to mandrill account closure, need to re-write for sparkpost 
//require('../Mandrill.php');
/*
For command line use only
This tool sorts through riders on a trip and sends an email with a summary of
guests with lessons and rentals

1st argument is the destination
2nd argument is the recipient of the email report

This script will be scheduled with a cron job
*/
/*
class lessonEmail {
  var $dbConnect;
  var $saturday;
  var $trips;
  var $packages;
  var $totalGuests;
  var $guests;
  var $tripId;
  var $tripDate;
  var $title;
  var $txtEmail;
  var $htmlEmail;
  var $recipient;

  function __construct() {
    global $argv;

    $this->dbConnect = new mysqli(getenv('MYSQL_HOST'), getenv('MYSQL_USER'), getenv('MYSQL_PASS'), getenv('MYSQL_DB'));
    if($this->dbConnect->connect_errno > 0){
        die('Unable to connect to database [' . $this->dbConnect->connect_error . ']');
    }
    else{
      $this->dbConnect->query("SET NAMES utf8");
      $this->dbConnect->query("SET CHARACTER SET utf8");
      $this->dbConnect->query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
    }

    $this->saturday = date("m/d/Y", strtotime("next saturday"));
    $this->destination = ucfirst( strtolower( $argv[1] ) );
    $this->recipient = $argv[2];
    $this->packages = [
                        "LTR" => "/beginner.*lift.*bus.*lesson.*board/i",
                        "LTS" => "/beginner.*lift.*bus.*lesson.*ski/i",
                        "ProgLesson" => "/prog.* lesson/i",
                        "Ski" => "/ski rental/i",
                        "Brd" => "/board rental/i"
                      ];
    $this->getTrip();
    $this->getRiders();
    $this->filterRiders();
    $this->composeEmail();
    $this->sendEmail();

  }
  function getTrip() {
    $sql = <<<DESTSQL
    SELECT ID, post_title
    FROM `wp_posts`
    JOIN `wp_postmeta`
    ON `wp_posts`.`ID` = `wp_postmeta`.`post_id`
    WHERE `meta_key` = '_wc_trip_destination'
    AND `meta_value` = '{$this->destination}'
    AND `post_status` = 'publish'
DESTSQL;
    $result = $this->dbQuery($sql);

    while ( $row = $result->fetch_assoc() ) {
      $dateSql = "SELECT `meta_value`
      FROM `wp_postmeta`
      WHERE `meta_key` = '_wc_trip_start_date'
      AND `post_id` = '{$row['ID']}'";

      $dateResult = $this->dbQuery($dateSql);
      $dateRow = $dateResult->fetch_assoc();
      if ( (string)$this->saturday == $dateRow['meta_value'] ) {
        $this->title = $row['post_title'];
        $this->tripDate = $dateRow['meta_value'];
        $this->tripId = $row['ID'];
      }

    }


  }
  function getRiders() {
    $sql = <<<TRIPSQL
    SELECT `wp_woocommerce_order_items`.`order_item_id`
            FROM `wp_posts`
            INNER JOIN `wp_woocommerce_order_items` ON `wp_posts`.`id` = `wp_woocommerce_order_items`.`order_id`
            INNER JOIN `wp_woocommerce_order_itemmeta` ON `wp_woocommerce_order_items`.`order_item_id` = `wp_woocommerce_order_itemmeta`.`order_item_id`
            WHERE `wp_posts`.`post_type` =  'shop_order'
            AND `wp_posts`.`post_status` = 'wc-completed'
            AND `wp_woocommerce_order_items`.`order_item_type` =  'line_item'
            AND `wp_woocommerce_order_itemmeta`.`meta_key` =  '_product_id'
            AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '{$this->tripId}'
TRIPSQL;

    $result = $this->dbQuery($sql);
    $this->totalGuests = $result->num_rows;

    while( $row = $result->fetch_assoc() ) {
      $metaSql = <<<METASQL
        SELECT `meta_key`, `meta_value`
        FROM `wp_woocommerce_order_itemmeta`
        WHERE `order_item_id` = '{$row['order_item_id']}'
        AND ( `meta_key` = 'First'
        OR `meta_key` = 'Last'
        OR `meta_key` = 'Email'
        OR `meta_key` = 'Phone'
        OR `meta_key` = 'Package' )
METASQL;
      $metaResult = $this->dbQuery($metaSql);
      while( $metaRow = $metaResult->fetch_assoc() ) {
        $this->guests[$row['order_item_id']][$metaRow['meta_key']] = $metaRow['meta_value'];
      }
    }
  }
  function filterRiders() {
    $filteredGuests = array();
    foreach( $this->guests as $id => $info) {
      foreach( $this->packages as $label => $regex ) {
        if ( preg_match( $regex, $info['Package'])) {
          $temp = $info;
          unset($temp['Package']);
          $filteredGuests[$label][$id] = $temp;
        }
      }
    }
    $this->guests = $filteredGuests;
  }
  function composeEmail() {
    $txtEmail = $this->title . "\n";
    $htmlEmail = "<p>" . $this->title . "</p>";
    $txtEmail .= "Total guests: " . $this->totalGuests."\n\n";
    $htmlEmail .= "<p>Total guests: " . $this->totalGuests . "</p><br />";

    foreach( $this->packages as $label => $regex) {
      $txtEmail .= $this->translateLabel($label);
      $htmlEmail .= "<p>" . $this->translateLabel($label);
      if ( ! isset( $this->guests[$label] ) ) {
        $txtEmail .= ": 0\n";
        $htmlEmail .= ": 0</p>";
      } else {
        $txtEmail .= ": ".count($this->guests[$label]) . "\n";
        $htmlEmail .= ": ".count($this->guests[$label]) . "</p>";
        foreach( $this->guests[$label] as $order_item_id => $info ) {
          $txtEmail .= "\t" . $info['First'] . " " . $info['Last'];
          $htmlEmail .= "<p>&nbsp;&nbsp;&nbsp;&nbsp;" . $info['First'] . "&nbsp;" . $info['Last'];
          $txtEmail .= "\tPhone: " . $info['Phone'] . "\tEmail: " . $info['Email'] . "\n\n";
          $htmlEmail .= "&nbsp;&nbsp;&nbsp;&nbsp;Phone:&nbsp;" . $info['Phone'] . "&nbsp;&nbsp;&nbsp;&nbsp;Email:&nbsp;" . $info['Email'] . "</p><br />";
        }
      }
    }
    $this->txtEmail = $txtEmail;
    $this->htmlEmail = $htmlEmail;
  }
  function sendEmail() {
    $subject = "OvRride: " . $this->title;
    $mandrill = new Mandrill();
    $message = array(
        'html' => $this->htmlEmail,
        'text' => $this->txtEmail,
        'subject' => $subject,
        'from_email' => 'info@ovride.com',
        'from_name' => 'OvRride',
        'to' => array(
            array(
                'email' => $this->recipient
            ),
            array(
              'email' => 'info@ovrride.com'
            )
        ),
        'headers' => array('Reply-To' => 'info@ovrride.com'),
        'important' => true,
        'track_opens' => true,
        'track_clicks' => true
      );
    $async = false;
    $ip_pool = 'Main Pool';
    $mandrill->messages->send($message, $async, $ip_pool);

  }
  private function translateLabel($label) {
    switch($label) {
      case "LTR":
        return "Learn To Ride";
      break;
      case "LTS":
        return "Learn To Ski";
      break;
      case "ProgLesson":
        return "Progressive Lesson";
      break;
      case "Ski":
        return "Ski Rental";
      break;
      case "Brd":
        return "Board Rental";
      break;
    }
  }

  private function dbQuery($sql) {
      if ( !$result = $this->dbConnect->query($sql)) {
          die('There was an error running the query [' . $this->dbConnect->error . ']');
      } else {
        return $result;
      }
  }

}

new lessonEmail();
 ?>
*/
