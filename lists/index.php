<?php
/**
 * OvR Lists - The main template file for OvR Lists
 *
 *
 * @package OvR Lists
 * @since Version 0.0.1
 */

# Report all PHP errors
# For Development use only
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors','On');

# Start Session Validation
session_start();

# Include Functions
require_once("includes/lists.php");

# Session Validation - Is User logged in?
# else redirect to login page
if (!(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] != '')) {
  header ("Location: login/index.php");
}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>OvR Trip Lists</title>

    <!-- Mobile view properties & enable iOS Web App-->
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    
    <!-- favicon and apple-touch-icon -->
    <link rel="apple-touch-icon" href="touch-icon-iphone.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/images/touch-icon-ipad.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="assets/images/touch-icon-iphone-retina.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="assets/images/touch-icon-ipad-retina.png" />
    <link rel="icon" type="image/png" href="http://ovrride.com/favicon.ico">

    <!-- Include compiled and minified stylesheets -->
    <link rel="stylesheet" href="assets/stylesheets/all.css">
    <!-- Include tablesorter styles -->
    <link rel="stylesheet" href="assets/tablesorter/css/theme.bootstrap.css">
  </head>
  <body>
    <h1>OvR Trip Lists</h1>
    <br>

    <form action="index.php" method="post" name="trip_list" id="trip_list">
      <section class="trip-select">
      <label>Select a Trip:</label>
      <br>
      <select id="trip" name="trip" id="trip">
      <?php echo $list->select_options; ?>
      </select>
      </section>
      <br>

      <section class="order-status-select">
      <label>Order Status: </label>
      <a onclick="javascript:checkAll('trip_list', true);" href="javascript:void();">Check All</a> &#47;
      <a onclick="javascript:checkAll('trip_list', false);" href="javascript:void();">Uncheck All</a>
      <br>
      <label class="checkbox-inline">
        <input type="checkbox" name="processing" value="processing" <?php if(isset($_POST['processing']) || !isset($_POST['trip'])) echo 'checked';?>>Processing</input>
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" name="pending" value="pending" <?php if(isset($_POST['pending']) || !isset($_POST['trip'])) echo 'checked'; ?>>Pending</input>
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" name="cancelled" value="cancelled" <?php if(isset($_POST['cancelled'])) echo 'checked'; ?>>Cancelled</input>
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" name="failed" value="failed" <?php if(isset($_POST['failed'])) echo 'checked'; ?>>Failed</input>
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" name="on-hold" value="on-hold" <?php if(isset($_POST['on-hold'])) echo 'checked'; ?>>On-hold</input>
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" name="completed" value="completed" <?php if(isset($_POST['completed'])) echo 'checked'; ?>>Completed</input>
      </label>
      <label class="checkbox-inline">
        <input type="checkbox" name="refunded" value="refunded" <?php if(isset($_POST['refunded'])) echo 'checked'; ?>>Refunded</input>
      </label>
      <br>
      <input type="submit" class="btn btn-primary generate-list" value="Generate List" /> 
      <button type="button" onclick="javascript:formReset();" class="btn btn-primary generate-list">Clear Form</button>
      </section>
      <br>

      <?php # Output of the Trip List Table ?>
      <?php if(isset($_POST['trip']) && $_POST['trip'] != "none"){ 
          print $list->html_table; ?>
      <form>
        <button type="submit" class="btn btn-primary generate-list" id="csv_list" name="csv_list" value="csv_list">Generate List CSV</button> 
        <button type="submit" class="btn btn-primary generate-list" id="csv_email" name="csv_email" value="csv_email">Generate Email CSV</button>
        <button type="button" class="btn btn-success generate-list" id="save_form" name="save_form" onclick="javascript:tableToForm();">Save</button>
      </form>
      </form>
      <?php } ?>

      <!-- TODO: Temporary location for Logout and Register User. These need to go into the navbar -->
      <br>
       <a href="login/logout.php"><button type="button" class="btn btn-danger">Logout</button></a>
       <a href="login/register.php"><button type="button" class="btn btn-danger">Create New User</button></a>
      <br>

      <footer>
        <div class="container">
          <div class="row">
            <div class="col-md-4 text-left">
              <h5><span>&copy; Copyright <?php echo date('Y'); ?> - <a href="/">OvR ride LLC.</a></span></h5>
            </div>
          <div class="col-md-4 text-center footer-center">
            <h5>For OvR Staff Use Only</h5>
          </div>
          <div class="col-md-4 text-right">
            <h5>Version <?php echo $lists_version ?></h5>
          </div>
        </div>
      </div>
      </footer>
      <!-- Include concatenated and minified javascripts -->
      <script src="assets/javascripts/all.min.js"></script>
  </body>
</html>