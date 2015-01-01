<?php
    // Setup DB Connection and check that it works
    // TWEAK VALUES WHEN RUNNING ON SERVER
    $dbConnect = new mysqli('localhost', 'root', 'iloverandompasswordsbutthiswilldo', 'ovrride');
    if($dbConnect->connect_errno > 0){
        die('Unable to connect to database [' . $dbConnect->connect_error . ']');
    }
    else{
        $dbConnect->query("SET NAMES utf8");
        $dbConnect->query("SET CHARACTER SET utf8");
        $dbConnect->query("SET COLLATION_CONNECTION = 'utf8_unicode_ci'");
    }
    $sql = "SELECT `ID`, `value` FROM `ovr_lists_fields`";
    $result = $dbConnect->query($sql);
    $weightedPackages = ['AM'=>1,'Waiver'=>2,'Product'=>3,'PM'=>4, 
                        'LTR'=>0,'LTS'=>0,'Prog_Lesson'=>0,'Beg'=>0,
                        'All_Area'=>0,'SKI'=>0,'BRD'=>0,'Bus'=>0];
    $data=[];
    while($row = $result->fetch_assoc()){
        echo $row['ID']. " ".$row['value']."\n";
        $split = split(":",$row['ID']);
        $ID = $split[0].":".$split[1];
        $field = $split[2];
        if ( ($row['value'] != "0" || $row['value'] != 0) && 
            $field != "LTR" && $field != "LTS" && $field != "Prog_Lesson" &&
            $field != "Beg" && $field != "All_Area" && $field != "SKI" && $field != "BRD" &&
            $field != "Bus"){

            if ( ! isset($data[$ID]) ) {
                $data[$ID] = $field;
            } else if ( $weightedPackages[$field] > $weightedPackages[$data[$ID]] ) {
                $data[$ID] = $field;
            }
        }
    }
    foreach($data as $ID => $value){

        if ( substr($ID,0,2) == "WO" ) {
            $manualSQL = "SELECT `Trip` FROM `ovr_lists_manual_orders` WHERE `ID` = '" . $ID . "'";
            $result = $dbConnect->query($manualSQL);
            $trip = $result->fetch_assoc();
            $trip = $trip['Trip'];
            echo $trip."\n";        
        } else {
            $split = split(":",$ID);
            $normalSQL = "SELECT `meta_value` FROM `wp_woocommerce_order_itemmeta` 
                        WHERE `meta_key` = '_product_id' AND order_item_id = '" . $split[1] . "'";
            $result = $dbConnect->query($normalSQL);
            $trip = $result->fetch_assoc();
            $trip = $trip['meta_value'];
        }
        $sql = "INSERT INTO `ovr_lists_data` (ID,Trip,Bus,Data) 
                VALUES('" . $ID . "', '" . $trip . "', '1', '" . $value . "')";
        $dbConnect->query($sql);
        
    }
    
?>