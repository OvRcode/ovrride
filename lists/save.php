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
    $test = '{"15922":{"190":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"352":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"353":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"354":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"16849":{"494":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"495":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17387":{"704":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"705":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17547":{"849":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"850":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17548":{"851":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17559":{"855":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"856":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17643":{"862":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17659":{"887":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"888":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17660":{"889":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"890":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17665":{"894":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17672":{"901":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17728":{"954":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17784":{"987":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17789":{"996":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"997":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17792":{"1001":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17809":{"1020":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17821":{"1024":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0},"1025":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17852":{"1050":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17889":{"1101":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17891":{"1104":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17912":{"1122":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"17915":{"1125":{"AM":0,"PM":0,"Waiver":0,"Product":0,"Bus":0,"All_Area":0,"Beg":0,"BRD":0,"SKI":0,"LTS":0,"LTR":0,"Prog_Lesson":0}},"WO86330":{"36551":{"timestamp":1387027333864,"First":"A","Last":"B","Pickup":"C","Phone":"D","Package":"E","Trip":"377"}}}';
    echo $test."<br />]";
    $json = json_decode($test);
    $array = objectToArray($json);
    foreach ($array as $order => $orderInfo) {
      foreach ($orderInfo as $orderItem => $field) {
        foreach ($field as $fieldName => $value) {
          print $order.":".$orderItem.":".$fieldName.":".$value."<br />";
        }
      }
    }
?>
