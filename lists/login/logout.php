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
session_destroy();
header ("Location: index.php");

?>