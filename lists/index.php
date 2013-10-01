<?php
/**
 * OvR Lists - The main template file for OvR Lists
 *
 *
 * @package OvR Lists
 * @since Version 0.0.1
 */

# Include Functions
include 'include/functions.php';

# Report all PHP errors on page
# For Development use only
error_reporting(E_ALL|E_STRICT);
ini_set('display_errors','On');

?>
<!DOCTYPE html>
  <head>
    <meta charset="utf-8">
    <title>OvR Trip Lists</title>
  </head>
  <body>
    <form action="index.php" method="post">
      <label>Select trip</label>
      <br>
      <select id="trip" name="trip">
      <?php echo trip_options($_POST['trip']); ?>
      </select>
      <input type="submit" value="Generate List" />
      </form>

      <?php 
        if(isset($_POST['trip']) && $_POST['trip'] != ""){
            if($orders=find_orders_by_trip($_POST['trip'])){
                print table_header();
                foreach($orders as $order){
                    $data = get_order_data($order,$_POST['trip']);
                    print table_row($data);
                    #print_r(get_order_data($order,$_POST['trip']));
                }
                print table_close();
            }
            else{
                print "No orders found for this trip";
            }
        }
      ?>
  </body>
</html>