<?php
/**
 * OvR Lists - Functions and Definitions
 *
 * @package OvR Lists
 * @since Version 0.0.2
 */

# Trip List Exporter Functions
function db_connect(){
    # Include Configurations
    include 'include/config.php';
    global $db_connect;
    $db_connect = new mysqli($host,$user,$pass,$db); 
    if($db_connect->connect_errno > 0){
      die('Unable to connect to database [' . $db_connect->connect_error . ']');
    }
}
function db_query($sql){
    global $db_connect;
    if(!$result = $db_connect->query($sql))
        die('There was an error running the query [' . $db_connect->error . ']');
    else
      return $result;
}
function trip_options($selected){
  global $db_connect;
    
    # find trips
    $sql = "select `id`, `post_title` from `wp_posts` where `post_status` = 'publish' and `post_type` = 'product' order by `post_title`";
    $result = db_query($sql);
    
    # Construct options for a select field
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
    # clean up
    $result->free();

    return $options;
}
function find_orders_by_trip($trip){
    global $db_connect;
    
    $sql = "SELECT `wp_posts`.`ID`
        FROM `wp_posts`
        INNER JOIN `wp_woocommerce_order_items` ON `wp_posts`.`id` = `wp_woocommerce_order_items`.`order_id`
        INNER JOIN `wp_woocommerce_order_itemmeta` ON `wp_woocommerce_order_items`.`order_item_id` = `wp_woocommerce_order_itemmeta`.`order_item_id`
        WHERE `wp_posts`.`post_type` =  'shop_order'
        AND `wp_woocommerce_order_items`.`order_item_type` =  'line_item'
        AND `wp_woocommerce_order_itemmeta`.`meta_key` =  '_product_id'
        AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '$trip'";
    
    $result = db_query($sql);
    $orders = array();
    while($row = $result->fetch_assoc()){
        $orders[] = $row['ID'];
    }

    $result->free();

    if(count($orders) == 0){
        return FALSE;
    }
    else{
        return $orders;
    }
}

function get_order_data($order,$trip){
    global $db_connect;

    # get line items from order
    $sql = "select order_item_id from wp_woocommerce_order_items where order_item_type = 'line_item' and order_id = '$order'";
    $result = db_query($sql);
    $row = $result->fetch_assoc();
    $result->free();
    $order_item_id = $row['order_item_id'];

    # pull order item meta data
    $sql2 = "select `meta_key`, `meta_value` from `wp_woocommerce_order_itemmeta` 
        where 
    ( meta_key = '_product_id' or meta_key ='How many riders are coming?' or meta_key = 'Name' or meta_key = 'Email' or meta_key = 'Package' or meta_key = 'Pickup Location' ) 
        and order_item_id = '$order_item_id'";
    $result2 = db_query($sql2);

    while($row = $result2->fetch_assoc()){
        if($row['meta_key'] == 'How many riders are coming?')
            $meta_data[$row['meta_key']] = $row['meta_value'];
        elseif ($row['meta_key'] == '_product_id')
            $meta_data[$row['meta_key']] = $row['meta_value'];
        else
            $meta_data[$row['meta_key']][] = $row['meta_value'];
    }

    $result2->free();

    # get phone num
    $sql3 = "SELECT  `meta_value` AS  `Phone`
            FROM wp_postmeta
            WHERE meta_key =  '_billing_phone'
            AND post_id =  '$order'";

    $result3 = db_query($sql3);
    $row = $result3->fetch_assoc();
    $result3->free();
    $meta_data['Phone'] = $row['Phone'];

    # fix phone formatting
    $meta_data['Phone'] = reformat_phone($meta_data['Phone']);

    return $meta_data;
}

function reformat_phone($phone){
    # strip all formatting
    $phone = str_replace('-','',$phone);
    $phone = str_replace('(','',$phone);
    $phone = str_replace(')','',$phone);
    $phone = str_replace(' ','',$phone);
    $phone = str_replace('.','',$phone);
    
    # add formatting to raw phone num
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
    $html = "";
    foreach($data['Name'] as $index => $name){
        $html .= "<tr><td></td><td>".$name."</td>";
        if(isset($data['Pickup Location'][$index]))
            $html .= "<td>".$data['Pickup Location'][$index]."</td>";
        else
            $html .= "<td></td>";
        $html .= "<td>".$data['Phone']."</td><td>".$data['Package'][$index]."</td>";
        $html .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>\n";
    }
    return $html;
}
function table_close(){
    $html = "</tbody>\n";
    $html .= "</table>";
    return $html;
}
db_connect();
?>