<?php
require_once('includes/lists_new.php');

$list = new TripList();

$list->tripData($_POST['trip']);
$list->getManualOrders($_POST['trip']);

header('Content-Type: application/json');
echo json_encode($list->orderData);
?>