<?php
require_once('includes/config.php');
session_start();
$db_connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
    if($db_connect->connect_errno > 0){
        die('Unable to connect to database [' . $db_connect->connect_error . ']');
    }
# Assemble data
$table_data = array();
foreach($_POST as $field => $value){
    $exploded = explode(':',$field);
    $id = $exploded[0].":".$exploded[1];
    $label = end($exploded);
    $table_data[$id][$label]=$value;
}
# Add to db
foreach($table_data as $id => $data){

  $sql = <<<AAA
  Insert into `ovr_lists_table` (`ID`,`trip`,`order`,`item_id`,`AM`,`PM`,`First`,`Last`,`Pickup`,`Phone`,`Package`,`Waiver`,`Product`,
    `Bus`,`All_Area`,`Beg`,`BRD`,`SKI`,`LTS`,`LTR`,`Prog_Lesson`)
  VALUES('$id','$data[trip]','$data[Order]','$data[item_id]',$data[AM],$data[PM],'$data[First]','$data[Last]','$data[Pickup]','$data[Phone]','$data[Package]',
    $data[Waiver],$data[Product],$data[Bus],$data[All_Area],$data[Beg],$data[BRD],$data[SKI],$data[LTS],$data[LTR],$data[Prog_Lesson])
  ON DUPLICATE KEY UPDATE
  `AM` = VALUES(`AM`),
  `PM` = VALUES(`PM`),
  `First` = VALUES(`First`),
  `Last` = VALUES(`Last`),
  `Pickup` = VALUES(`Pickup`),
  `Phone` = VALUES(`Phone`),
  `Package` = VALUES(`Package`),
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
AAA;

if(!$result = $db_connect->query($sql)){
    $_SESSION['saved_table'] = false;
    # print $sql."<br />";
    die('There was an error running the query [' . $db_connect->error . ']');
}
else{
    $_SESSION['saved_table'] = true;
}
}
header('Location: /');
?>