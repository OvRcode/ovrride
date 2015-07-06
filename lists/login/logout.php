<?php
/**
 * OvR Lists - Logout
 *
 * @package OvR Lists
 * @since Version 0.2.0
 */

# Simple Logout page to destroy the session
# and redirect to the login page
session_start();
    session_unset();
    session_destroy();
    session_write_close();
    setcookie(session_name(),'',0,'/');
    session_regenerate_id(true);
    header("HTTP/1.0 401 Unauthorized");
    header ("Location: login.php");

?>