<?php
require_once('includes/lists.php');
$list = new TripList();

$order = $_POST['order'];

$sqlFields = "DELETE FROM `ovr_lists_fields` WHERE `ID` LIKE '$order'";
$sqlOrders = "DELETE FROM `ovr_lists_manual_orders` WHERE `ID` = '$order'";

$list->dbQuery($sqlFields);
$list->dbQuery($sqlOrders);
?>