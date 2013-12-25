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
# error_reporting(E_ALL|E_STRICT);
# ini_set('display_errors','On');

session_regenerate_id();
session_start();

# Start Session with a 1 day persistent session lifetime
$cookieLifetime = 60 * 60 * 24 * 1;
setcookie(session_name(),session_id(),time()+$cookieLifetime);

# Include Functions
require_once("includes/lists.php");

# Session Validation - Is User logged in?
# else redirect to login page
if (!(isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] != ''))
  header ("Location: login/index.php");

?>
<!DOCTYPE html>
<html lang="en" manifest="manifest.appcache">
  <head>
    <meta charset="utf-8">
    <title>OvR Trip Lists</title>

    <!-- Mobile view properties & enable iOS Web App-->
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0, maximum-scale=1.0"/>

    <!-- favicon and apple-touch-icon -->
    <link rel="apple-touch-icon" href="assets/images/touch-icon-iphone.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/images/touch-icon-ipad.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="assets/images/touch-icon-iphone-retina.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="assets/images/touch-icon-ipad-retina.png" />
    <link rel="icon" type="image/png" href="http://ovrride.com/favicon.ico">

    <!-- Include compiled and minified stylesheets -->
    <link rel="stylesheet" href="assets/stylesheets/all.css">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand logo-nav" onclick="formReset();" href="javascript:void();">OvR Trip Lists&nbsp;&nbsp;<span class="status iphone glyphicon"></span></a>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav nav-puller">  
            <li>
              <form action="index.php" method="post" name="trip_list" id="trip_list" class="navbar-form">
                <button type="button" class="btn btn-default" id="save" name="save" title="Save changes">
                  <span class="glyphicon glyphicon-floppy-disk"></span> SAVE
                </button>
                <button type="submit" class="btn btn-default" id="csv_list" name="csv_list" value="csv_list" title="Full List CSV">
                  <span class="glyphicon glyphicon-list-alt"></span> CSV
                </button> 
                <button type="submit" class="btn btn-default" id="csv_email" name="csv_email" value="csv_email" title="Email CSV">
                  <span class="glyphicon glyphicon-envelope"></span> CSV
                </button>
            </li>
            <li><a href="login/register.php">Create New User</a></li>
            <li><a href="login/logout.php">Logout</a></li>
            <li><a href=""><span class="status medLg glyphicon"></span></a></li>  
          </ul>
        </div><!--/.nav-collapse -->
      </div><!-- /.container -->
    </nav>

    <div class="container" id="mainBody">
      <div class="col-md-5">
      <section class="trip-select">
          <label>Select a Destination:</label>
            <select class="form-control input-sm" id="destination" name="destination">
              <?php echo $list->select_options['destinations']?>
            </select>
        </label>
        <label>Select a Trip:
          <select class="form-control input-sm" id="trip" name="trip" id="trip">
          <?php echo $list->select_options['trip']; ?>
          </select>
        </label>
      </section>
    </div>
      <br>

      <section class="order-status-select input-group">
          <label>Order Status: </label>
          <a onclick="javascript:checkAll('check');" href="javascript:void();"> Check All</a> &#47;
          <a onclick="javascript:checkAll('uncheck');" href="javascript:void();">Uncheck All</a>
          <br>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="processing" value="processing" <?php checkbox_helper("processing");?>>Processing</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="pending" value="pending" <?php checkbox_helper("pending");?>>Pending</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="walk-on" value="walk-on" <?php checkbox_helper("walk-on");?>>Walk On</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="cancelled" value="cancelled" <?php checkbox_helper("cancelled");?>>Cancelled</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="failed" value="failed" <?php checkbox_helper("failed");?>>Failed</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="on-hold" value="on-hold" <?php checkbox_helper("on-hold");?>>On-hold</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="completed" value="completed" <?php checkbox_helper("completed");?>>Completed</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="refunded" value="refunded" <?php checkbox_helper("refunded");?>>Refunded</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="balance-due" value="balance-due" <?php checkbox_helper("balance-due");?>>Balance Due</input>
          </label>
          <label class="checkbox order-checkbox">
            <input type="checkbox" class="order_status_checkbox" name="no-show" value="no-show" <?php checkbox_helper("no-show");?>>No Show</input>
          </label>
          <br>
          <input type="button" class="btn btn-primary generate-list" value="Generate List" onclick="generateOnOff();" />

          <button type="button" onclick="formReset();" class="btn btn-primary generate-list">Clear Form</button>

          <?php if(isset($_SESSION['post_data']['trip']) && $_SESSION['post_data']['trip'] != "none"){ ?>
            <button type="button" class="reset btn btn-warning generate-list">Reset Table Filters </button>
          <?php } ?>
      </section>
      <br>
      </form>
      </div><!-- /.container -->

      <!-- Lists table added here by Jquery -->
      <div id="listTable">
          
      </div>
      <div class="pager">

          
          <button type="button" class="first btn btn-default">
              <span class="glyphicon glyphicon-fast-backward"></span>
          </button>
          <button type="button" class="prev btn btn-default">
              <span class="glyphicon glyphicon-backward"></span>
          </button>
          <span class="pagedisplay"></span> <!-- this can be any element, including an input -->
          <button type="button" class="next btn btn-default">
              <span class="glyphicon glyphicon-forward"></span>
          </button>
          <button type="button" class="last btn btn-default">
              <span class="glyphicon glyphicon-fast-forward"></span>
          </button>
          <select class="pagesize">
              <option selected="selected" value="10">10</option>
              <option value="20">20</option>
              <option value="30">30</option>
              <option value="40">40</option>
              <option value="50">50</option>
      		</select>
      	</div>
      <footer>
        <div class="page-header"></div><!-- inserts the line separator -->
          <div class="container">
            <div class="row">
              <div class="col-md-4 text-center">
                <h5><span>&copy; Copyright <?php echo date('Y'); ?> - <a href="/">OvR ride LLC.</a></span></h5>
              </div>
              <div class="col-md-4 text-center footer-center">
                <h5>For OvR Staff Use Only</h5>
              </div>
              <div class="col-md-4 text-center">
                <h5>Version <?php echo $lists_version ?></h5>
              </div>
            </div>
          </div>
        </div>
      </footer>
        <?php 
        if(isset($list->order_data)){
            foreach($list->order_data as $order => $data){
                $array = array($order,$data);
                $json = htmlspecialchars(json_encode($array));
                if (json_last_error() > 0) {
                    error_log('Last JSON encode error: ' . json_last_error());
                } else {
                    printf('<input type="hidden" class="order" value="%s">',$json, ENT_QUOTES, 'UTF-8');
                }
            }
        }
        
        if(isset($list->has_pickup)){
            error_log('Pickup Flag:'.$list->has_pickup);
            print '<input type="hidden" id="hasPickup" value="'.$list->has_pickup.'" />';
        }
        ?>

      <!-- Include concatenated and minified javascripts -->
      <script src="assets/javascripts/all.min.js"></script>
  </body>
</html>
