<?php
function trip_options($selected){
	//connect to db
	$db = new mysqli('localhost', '***REMOVED***', '***REMOVED***', '***REMOVED***');

	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	//find trips
	$sql = "select `id`, `post_title` from `wp_posts` where `post_status` = 'publish' and `post_type` = 'product' order by `post_title`";
	if(!$result = $db->query($sql)){
  	die('There was an error running the query [' . $db->error . ']');
	}
	
	//construct options for a select field
	$options = "<option value=''";
	if($selected == "")
		$options .= " selected ";
	$options .= "> Select trip </option>\n";
	while($row = $result->fetch_assoc()){
		$options .= "<option value='".$row['id']."'";
		if($selected == $row['id'])
			$options .= " selected ";
		$options .= ">".$row['post_title']."</option>\n";
	}
	//clean up
	$result->free();
	$db->close();
	
	return $options;
}
function find_orders_by_trip($trip){
	//connect to db
	$db = new mysqli('localhost', '***REMOVED***', '***REMOVED***', '***REMOVED***');

	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
	
	$sql = "SELECT `wp_posts`.`ID`
		FROM `wp_posts`
		INNER JOIN `wp_woocommerce_order_items` ON `wp_posts`.`id` = `wp_woocommerce_order_items`.`order_id`
		INNER JOIN `wp_woocommerce_order_itemmeta` ON `wp_woocommerce_order_items`.`order_item_id` = `wp_woocommerce_order_itemmeta`.`order_item_id`
		WHERE `wp_posts`.`post_type` =  'shop_order'
		AND `wp_woocommerce_order_items`.`order_item_type` =  'line_item'
		AND `wp_woocommerce_order_itemmeta`.`meta_key` =  '_product_id'
		AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '$trip'";		
	
	if(!$result = $db->query($sql)){
		die('There was an error running the query [' . $db->error . ']');
	}
	$orders = array();
	while($row = $result->fetch_assoc()){
		$orders[] = $row['ID'];
	}
	$result->free();
	$db->close();
	if(count($orders) == 0){
		return FALSE;
	}
	else{
		return $orders;
	}
}
function get_order_data($order,$trip){
	//connect to db
	$db = new mysqli('localhost', '***REMOVED***', '***REMOVED***', '***REMOVED***');

	if($db->connect_errno > 0){
	    die('Unable to connect to database [' . $db->connect_error . ']');
	}
					
	//get line items from order
	$sql = "select order_item_id from wp_woocommerce_order_items where order_item_type = 'line_item' and order_id = '$order'";
	if(!$result = $db->query($sql)){
  	die('There was an error running the query [' . $db->error . ']');
	}
	$row = $result->fetch_assoc();
	$result->free();
	$order_item_id = $row['order_item_id'];
	
	//pull order item meta data
	$sql2 = "select `meta_key`, `meta_value` from `wp_woocommerce_order_itemmeta` 
			where 
			( meta_key ='How many riders are coming?' or meta_key = 'Name' or meta_key = 'Email' or meta_key = 'Package' or meta_key = 'Pickup Location' ) 
			and order_item_id = '$order_item_id'";
	if(!$result2 = $db->query($sql2)){
	  	die('There was an error running the query [' . $db->error . ']');
		}
	while($row = $result2->fetch_assoc()){
		if($row['meta_key'] == 'How many riders are coming?')
			$meta_data[$row['meta_key']] = $row['meta_value'];
		else
			$meta_data[$row['meta_key']][] = $row['meta_value'];
		}
	$result2->free();
	
	//get phone #
	$sql3 = "SELECT  `meta_value` AS  `Phone` 
					FROM wp_postmeta
					WHERE meta_key =  '_billing_phone'
					AND post_id =  '$order'";
	if(!$result3 = $db->query($sql3)){
		die('There was an error running the query [' . $db->error . ']');
	}		
	$row = $result3->fetch_assoc();
	$result3->free();
	$meta_data['Phone'] = $row['Phone'];
	//fix phone formatting
	$meta_data['Phone'] = reformat_phone($meta_data['Phone']);
		
	return $meta_data;
}
function reformat_phone($phone){
	//strip all formatting
	$phone = str_replace('-','',$phone);
	$phone = str_replace('(','',$phone);
	$phone = str_replace(')','',$phone);
	$phone = str_replace(' ','',$phone);
	$phone = str_replace('.','',$phone);
	//add formatting to raw phone #
	$phone = substr_replace($phone,'(',0,0);
	$phone = substr_replace($phone,') ',4,0);
	$phone = substr_replace($phone,'-',9,0);
	
	return $phone;
}
function table_header(){
	$html = "<table border=1>\n";
	$html .= "<thead><tr>\n";
	$html .= "<td>AM</td><td>Name</td><td>Pickup</td><td>Phone</td><td>Package</td><td>Waiver</td><td>Product REC.</td><td>PM Checkin</td><td>Bus Only</td>";
	$html .= "<td>All Area Lift</td><td>Beg. Lift</td><td>BRD Rental</td><td>Ski Rental</td><td>LTS</td><td>LTR</td><td>Prog. Lesson</td>\n";
	$html .= "</tr></thead>\n";
	$html .= "<tbody>";
	return $html;
}
function table_row($data){
	foreach($data['Name'] as $index => $name){
		$html .= "<tr><td></td><td>".$name."</td><td>".$data['Pickup Location'][$index]."</td><td>".$data['Phone']."</td><td>".$data['Package'][$index]."</td>";
		$html .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>\n";
	}
	return $html;
}
function table_close(){
	$html = "</tbody>\n";
	$html .= "</table>";
	return $html;
}
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