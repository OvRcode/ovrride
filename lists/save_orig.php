<?php
function objectToArray($d) {
  if (is_object($d)) {
      // Gets the properties of the given object
      // with get_object_vars function
      $d = get_object_vars($d);
    }
    
    if (is_array($d)) {
      /*
      * Return array converted to object
      * Using __FUNCTION__ (Magic constant)
      * for recursive call
      */
      return array_map(__FUNCTION__, $d);
    }
    else {
      // Return array
      return $d;
    }
  }
require_once('includes/config.php');
session_start();
$db_connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if($db_connect->connect_errno > 0){
        die('Unable to connect to database [' . $db_connect->connect_error . ']');
    }
$db_connect->query("SET NAMES utf8");
$db_connect->query("SET CHARACTER SET utf8");

foreach ($_POST as $key => $value) {
  $input[$key] = objectToArray($value);
}

foreach ($input as $order => $orderInfo) {
  foreach ($orderInfo as $orderItem => $field) {
    foreach ($field as $fieldName => $value) {
      $prefix = substr($order,0,2);
      if ($fieldName == "First" 
        || $fieldName == "Last" 
        || $fieldName == "Pickup" 
        || $fieldName == "Phone" 
        || $fieldName == "Package"
        || $fieldName == "Trip") {
          $id = $order . ":" . $orderItem;
          $sql = "INSERT INTO `ovr_lists_manual_orders` (`ID`, `" . $fieldName . "`)" .
                              "VALUES ('$id','$value')" .
                              "ON DUPLICATE KEY UPDATE" .
                              "`" . $fieldName . "` = VALUES(`" . $fieldName . "`)";
          if (!$result = $db_connect->query($sql)) {
            die('There was an error running the query [' . $db_connect->error . ']');
            }
        } else {
          $id = $order.":".$orderItem.":".$fieldName;
          error_log($id);
          $result = $db_connect->query("SELECT `ID`,`value`,UNIX_TIMESTAMP(`timeStamp`) FROM `ovr_lists_fields` WHERE `ID` = '$id'");
          $row = $result->fetch_assoc();
          if ($result->num_rows == 0 ) {
            # data is older than input timestamp or doesnt exist in DB
            $sql ="INSERT INTO `ovr_lists_fields` (`ID`, `value`) ".
                                  "VALUES('$id','{$value[0]}') ".
                                  "ON DUPLICATE KEY UPDATE ".
                                  "`value` = VALUES(`value`)";
            if ($result = $db_connect->query($sql)) {
              die('There was an error running the query [' . $db_connect->error . ']');
              }
          }
        }
        }
      }
    }
?>