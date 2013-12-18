<?php
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

# Add to db
foreach($table_data as $id => $data){
    $prefix = substr($id, 0,2);
    if($prefix == "WO"){
        # This order has been manually entered into the list, need to save or update: Name and order info
        $sql_manual = <<< EOT
            INSERT INTO `ovr_lists_manual_orders` (`ID`, `First`, `Last`, `Pickup`, `Phone`, `Package`, `Trip`)
            VALUES ('$id','$data[First]','$data[Last]','$data[Pickup]','$data[Phone]','$data[Package]','$data[Trip]')
            ON DUPLICATE KEY UPDATE
            `First` = VALUES(`First`),
            `Last` = VALUES(`Last`),
            `Pickup` = VALUES(`Pickup`),
            `Phone` = VALUES(`Phone`),
            `Package` = VALUES(`Package`),
            `Trip` = VALUES(`Trip`)
EOT;
        if(!$result = $db_connect->query($sql_manual)){
            die('There was an error running the query [' . $db_connect->error . ']');
        }     
    }
    $sql = <<< EOT2
        INSERT INTO `ovr_lists_checkboxes` (`ID`,`AM`,`PM`,`Waiver`,`Product`,`Bus`,`All_Area`,`Beg`,`BRD`,`SKI`,`LTS`,`LTR`,`Prog_Lesson`) VALUES('$id','$data[AM]','$data[PM]','$data[Waiver]','$data[Product]','$data[Bus]','$data[All_Area]','$data[Beg]','$data[BRD]','$data[SKI]','$data[LTS]','$data[LTR]','$data[Prog_Lesson]')
        ON DUPLICATE KEY UPDATE
        `AM` = VALUES(`AM`),
        `PM` = VALUES(`PM`),
        `Waiver` = VALUES(`Waiver`),
        `Product` = VALUES(`Product`),
        `Bus` = VALUES(`Bus`),
        `All_Area` = VALUES(`All_Area`),
        `Beg` = VALUES(`Beg`),
        `BRD` = VALUES(`BRD`),
        `SKI` = VALUES(`SKI`),
        `LTS` = VALUES(`LTS`),
        `LTR` = VALUES(`LTR`),
        `Prog_Lesson` = VALUES(`Prog_Lesson`)
EOT2;


$db = dbConnect();

foreach ($_POST as $key => $value) {
  $input[$key] = objectToArray($value);
}

foreach ($input as $order => $orderInfo) {
    foreach ($orderInfo as $orderItem => $field) {
        foreach ($field as $fieldName => $value) {
            $prefix = substr($order,0,2);

            if ($fieldName == "First" || $fieldName == "Last" || $fieldName == "Pickup" 
              || $fieldName == "Phone" || $fieldName == "Package" || $fieldName == "Trip") {
                  $id = $order . ":" . $orderItem;
                  $sql = "INSERT INTO `ovr_lists_manual_orders` (`ID`, `" . $fieldName . "`)" .
                                      "VALUES ('$id','$value')" .
                                      "ON DUPLICATE KEY UPDATE" .
                                      "`" . $fieldName . "` = VALUES(`" . $fieldName . "`)";
                  $result = dbQuery($db,$sql);

              } else {
                  $id = $order.":".$orderItem.":".$fieldName;
                  $sql = "SELECT `ID`,`value`,UNIX_TIMESTAMP(`timeStamp`) AS `timeStamp` FROM `ovr_lists_fields` WHERE `ID` = '$id'";
                  $result = dbQuery($db,$sql);
                  $row = $result->fetch_assoc();
                  error_log("DEBUG HERE:");
                  if (($result->num_rows == 0 || !$result->num_rows) || (isset($row['timeStamp']) && $row['timeStamp'] < $value[1])) {
                      $sql = "INSERT INTO `ovr_lists_fields` (`ID`, `value`) ".
                                  "VALUES('$id','{$value[0]}') ".
                                  "ON DUPLICATE KEY UPDATE ".
                                  "`value` = VALUES(`value`)";
                      error_log($sql);
                      $result = dbQuery($db,$sql);
                  } 
              }
        }
    }
}

?>
