<?php
require_once('db.php');
require_once('PasswordHashClass.php');

# Make sure params are in place or die!
if (!isset($_GET['user']) || !isset($_GET['key'])){
    die("Missing some parameters here");
}

$db = new PDO('mysql:host='. DB_HOST .';dbname='. DB_NAME .';charset=utf8', DB_USER, DB_PASS);

# Retrieve user data
$userCheck = $db->prepare("SELECT * FROM ovr_lists_login WHERE user_id = :id");
$userCheck->bindParam(':id', $_GET['user'], PDO::PARAM_INT);
$userCheck->execute();

if ( $userCheck->rowCount() == 1 ) {
    $user = $userCheck->fetch(PDO::FETCH_ASSOC);
    $activation_hash_string = $user['user_id'] . $user['user_name'] . $user['user_email'] . $user['user_password_hash'];
    $generatedActivation = urlencode(hash_hmac('sha256', $activation_hash_string, $user['user_password_hash']));

    if ( PasswordHash::slow_equals($generatedActivation, $_GET['key']) == 1 ){
        $activateUser = $db->prepare("UPDATE ovr_lists_login SET activated = '1' WHERE user_id = :id");
        $activateUser->bindParam(':id',$_GET['user'], PDO::PARAM_INT);
        if( $activateUser->execute() ){
            echo "<h1>Activation successful</h1>";
        } else {
            echo "<h1 style='color:red'>Activation failed</h1>";
        }
    }
}
?>