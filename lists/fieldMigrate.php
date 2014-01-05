#!/usr/bin/php

<?php
# Switch the hashbang above for the one below if you are running this locally
# you may need to customize this if you are using a different php locally

#!/Applications/MAMP/bin/php/php5.4.19/bin/php

# This script is designed to migrate data from the original ovr_lists_checkboxes.sql to the new ovr_lists_fields.sql format
# This script runs from the commandline and is executable, just type ./fieldMigrate.php
function db_query($sql){
    global $db;
        if(!$result = $db->query($sql))
            die('There was an error running the query [' . $db->error . ']');
        else
          return $result;
    }
    
// Connect to mySQL DB
define("DB_HOST", "localhost");
define("DB_USER", "***REMOVED***");
define("DB_PASS", "***REMOVED***");
define("DB_NAME", "***REMOVED***");
$db = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
if($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}
else{
    $db->query("SET NAMES utf8");
    $db->query("SET CHARACTER SET utf8");
    $db->query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
}

// Get checkbox data
$sql = "SELECT * 
        FROM `ovr_lists_checkboxes`
        WHERE `AM` != '0' 
        OR `PM` != '0'
        OR `Waiver` != '0'
        OR `Product` != '0'
        OR `Bus` != '0'
        OR `All_Area` != '0'
        OR `Beg` != '0'
        OR `BRD` != '0'
        OR `SKI` != '0'
        OR `LTS` != '0'
        OR `LTR` != '0'
        OR `Prog_Lesson` != '0'";
$result = db_query($sql);
while($row = $result->fetch_assoc()){
    $baseId = $row['ID'];
    if ($baseId != ":undefined") {
        foreach ( $row as $key => $value ) {
            // only insert checked boxes, on duplicate key value will be dropped
            $id = $baseId.":".$key;
            if ( $value == 1){
                $sql2 = "INSERT IGNORE INTO `ovr_lists_fields` (`ID`,`value`) VALUES('$id','$value')";
                if(db_query($sql2)){
                    print $baseId . " Success\n";
                } else {
                    print $baseId . " FAIL!, do something about this!\n";
                }
            }
        }
    }
}
?>