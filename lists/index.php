<?php
/**
 * OvR Lists - The main template file for OvR Lists
 *
 *
 * @package OvR Lists
 * @since Version 0.0.1
 */

# Include Functions

include 'include/lists.php';

# Report all PHP errors on page
# For Development use only
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors','On');

if(isset($_POST['trip']))
  $list = new Trip_List($_POST['trip']);
else
  $list = new Trip_List("");
?>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <title>OvR Trip Lists</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Include compiled and minified stylesheets -->
    <link rel="stylesheet" href="assets/stylesheets/all.css">
  </head>
  <body>
    <h1>OvR Trip Lists</h1>
    <br>
    <form action="index.php" method="post" name="trip_list">
      <label>Select a Trip:</label>
      <br>
      <select id="trip" name="trip">
      <?php echo $list->select_options; ?>
      </select>
      <br>
      <label>Order Status: </label>
      <a onclick="javascript:checkAll('trip_list', true);" href="javascript:void();">Check All</a> /
      <a onclick="javascript:checkAll('trip_list', false);" href="javascript:void();">Uncheck All</a><br />
      <input type="checkbox" name="processing" value="processing" <?php if(isset($_POST['processing']) || !isset($_POST['trip'])) echo 'checked';?>>Processing</input>
      <input type="checkbox" name="pending" value="pending" <?php if(isset($_POST['pending']) || !isset($_POST['trip'])) echo 'checked'; ?>>Pending</input>
      <input type="checkbox" name="cancelled" value="cancelled" <?php if(isset($_POST['cancelled'])) echo 'checked'; ?>>Cancelled</input>
      <input type="checkbox" name="failed" value="failed" <?php if(isset($_POST['failed'])) echo 'checked'; ?>>Failed</input>
      <input type="checkbox" name="on-hold" value="on-hold" <?php if(isset($_POST['on-hold'])) echo 'checked'; ?>>On-hold</input>
      <input type="checkbox" name="completed" value="completed" <?php if(isset($_POST['completed'])) echo 'checked'; ?>>Completed</input>
      <input type="checkbox" name="refunded" value="refunded" <?php if(isset($_POST['refunded'])) echo 'checked'; ?>>Refunded</input>
      <br>
      <input type="submit" value="Generate List" />
      </form>
      <br>

      <?php if(isset($_POST['trip']) && $_POST['trip'] != "") print $list->html_table; ?>
      <!-- Include concatenated and minified javascripts -->
      <script src="assets/javascripts/all.min.js"></script>

  </body>
</html>