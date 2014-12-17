<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

require 'flight/Flight.php';
Flight::route('/', function(){
    echo 'hello world!';
});
Flight::route('/test/', function(){
    echo 'WTF?';
});
Flight::start();
?>