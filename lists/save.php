<?php
// This function is only needed for testing JSON in string format
function objectToArray($d) {
    if (is_object($d)) {
        # Gets the properties of the given object
        # with get_object_vars function
        $d = get_object_vars($d);
    }

    if (is_array($d)) {
        return array_map(__FUNCTION__, $d);
    } else {
      # Return array
      return $d;
    }
}
function dbConnect(){
    require_once('includes/config.php');
    $db = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    $db->query("SET NAMES utf8");
    $db->query("SET CHARACTER SET utf8");
    $db->query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
    return $db;
}

function dbQuery($db,$sql){
    if (!$result = $db->query($sql)) {
        die('There was an error running the query [' . $db->error . ']');
    } else {
        return $result;
    }
}

$db = dbConnect();
$input = $_POST['data'];

// Loop through orders
foreach ($input as $order => $orderInfo) {
    foreach ($orderInfo as $orderItem => $field) {
        foreach ($field as $fieldName => $value) {          
            if ($fieldName == "First" || $fieldName == "Last" || $fieldName == "Pickup"
              || $fieldName == "Phone" || $fieldName == "Package" || $fieldName == "Trip") {
                  $id = $order . ":" . $orderItem;
                  $sql = "INSERT INTO `ovr_lists_manual_orders` (`ID`, `" . $fieldName . "`)" .
                                      "VALUES ('$id','$value')" .
                                      "ON DUPLICATE KEY UPDATE" .
                                      "`" . $fieldName . "` = VALUES(`" . $fieldName . "`)";
                  $result = dbQuery($db,$sql);

              } else if ($fieldName != 'Status'){
                  $id = $order.":".$orderItem.":".$fieldName;
                  $sql = "SELECT `ID`,`value`,UNIX_TIMESTAMP(`timeStamp`) AS `timeStamp` FROM `ovr_lists_fields` WHERE `ID` = '$id'";
                  $result = dbQuery($db,$sql);
                  $row = $result->fetch_assoc();
                  $intValue = (int)$value[0];
                    if (!isset($row['timeStamp']) || $row['timeStamp'] < intval($value[1])) {
                      $sql = "INSERT INTO `ovr_lists_fields` (`ID`, `value`) ".
                                  "VALUES('$id',{$intValue}) ".
                                  "ON DUPLICATE KEY UPDATE ".
                                  "`value` = VALUES(`value`)";
                      $result = dbQuery($db,$sql);
                  }
              }
        }
    }
}
?>