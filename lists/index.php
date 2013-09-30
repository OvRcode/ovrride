<?php
# Include Functions
include 'include/functions.php';

# Include Configurations
include 'include/config.php';
?>
<html>
<head><title>OvR Test</title></head>
<body>
	<form action="index.php" method="post">
		<label>Select trip</label><br /><select id="trip" name="trip">
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