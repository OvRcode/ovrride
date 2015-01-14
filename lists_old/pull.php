<?php
require_once('includes/lists.php');

$list = new TripList();
switch ($_POST['requestType']){
    case 'orders':
        $list->tripData($_POST['trip']);
        $list->getManualOrders($_POST['trip']);

        header('Content-Type: application/json');
        echo json_encode($list->orderData);
    break;
    case 'dropdowns':
        $list->tripOptions();
        header('Content-Type: application/json');
        echo json_encode($list->options);
    break;
}

?>