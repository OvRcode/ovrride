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
    include 'includes/config.php';
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
     
    //conditional SQL for checkboxes on form
    $sql_conditional = "";
    $checkboxes = array("processing","pending","cancelled","failed","on-hold","completed","refunded");
    foreach($checkboxes as $field){
      if(isset($_POST[$field])){
        if($sql_conditional == "")
          $sql_conditional .= "`wp_terms`.`name` = '$field'";
        else
          $sql_conditional .= " OR `wp_terms`.`name` = '$field'";
      }
    }

    $sql = "SELECT `wp_posts`.`ID`, `wp_woocommerce_order_items`.`order_item_id`
        FROM `wp_posts`
        INNER JOIN `wp_woocommerce_order_items` ON `wp_posts`.`id` = `wp_woocommerce_order_items`.`order_id`
        INNER JOIN `wp_woocommerce_order_itemmeta` ON `wp_woocommerce_order_items`.`order_item_id` = `wp_woocommerce_order_itemmeta`.`order_item_id`
        INNER JOIN `wp_term_relationships` ON `wp_posts`.`id` = `wp_term_relationships`.`object_id`
        INNER JOIN `wp_terms` on `wp_term_relationships`.`term_taxonomy_id` = `wp_terms`.`term_id` 
        WHERE `wp_posts`.`post_type` =  'shop_order'
        AND `wp_woocommerce_order_items`.`order_item_type` =  'line_item'
        AND `wp_woocommerce_order_itemmeta`.`meta_key` =  '_product_id'
        AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '$trip'
        AND ($sql_conditional)";
    if($sql_conditional === "")
      $sql = substr($sql, 0, -6);
    
    $result = db_query($sql);
    $orders = array();
    while($row = $result->fetch_assoc()){
        $orders[] = array($row['ID'],$row['order_item_id']);
    }

    $result->free();

    if(count($orders) == 0){
        return FALSE;
    }
    else{
        return $orders;
    }
}
function get_order_data($order_array,$trip){
    global $db_connect;

    $order = $order_array[0];
    $order_item_id = $order_array[1];

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
        elseif ($row['meta_key'] == 'Package')
          $meta_data[$row['meta_key']][] = preg_replace('/\(\$\S*\)/', '', $row['meta_value']);
        else
            $meta_data[$row['meta_key']][] = $row['meta_value'];
    }
    $result2->free();
    
    # split names
    foreach($meta_data['Name'] as $index => $name){
        $name = split_name($name,$meta_data['_product_id']);
        $meta_data['First'][] = $name['First'];
        $meta_data['Last'][] = $name['Last'];
    }


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
    
    # add order number to data
    $meta_data['Order'] = $order;
    return $meta_data;
}
function split_name($name,$order_id){
  global $db_connect;

  $form_id = get_gravity_id($order_id);
  # select name fields from gravity form table and match
  # had to cast field_number to match against a float value, i hate floats
  # TODO: figure out a way to automate the field_numbers...currently these have been pulled from looking at forms
  $sql ="SELECT field_number, value, lead_id
          FROM wp_rg_lead_detail
          WHERE ( CAST( field_number AS CHAR ) <=> 2.3
            OR CAST( field_number AS CHAR ) <=> 2.6
            OR CAST( field_number AS CHAR ) <=> 9.3
            OR CAST( field_number AS CHAR ) <=> 9.6
            OR CAST( field_number AS CHAR ) <=> 8.3
            OR CAST( field_number AS CHAR ) <=> 8.6
            OR CAST( field_number AS CHAR ) <=> 7.3
            OR CAST( field_number AS CHAR ) <=> 7.6
            OR CAST( field_number AS CHAR ) <=> 6.3
            OR CAST( field_number AS CHAR ) <=> 6.6
            OR CAST( field_number AS CHAR ) <=> 5.3
            OR CAST( field_number AS CHAR ) <=> 5.6 )
          AND form_id = '$form_id'
          ORDER BY lead_id ASC , field_number ASC ";
    $result = db_query($sql);
    $names = array();
    while($row = $result->fetch_assoc()){
        $field_number = $row['field_number'];
        $decimal = explode('.',$field_number);
        $decimal = end($decimal);
        if($decimal == 3)
            $names[$row['lead_id']]['First'][] = $row['value'];
        elseif($decimal == 6)
            $names[$row['lead_id']]['Last'][] = $row['value'];
    }
    # now that we have complete names loop through array and match against provided name
    foreach ($names as $lead => $array){
        foreach($array['First'] as $index => $first){
            $complete = trim($first) . " " . trim($array['Last'][$index]);
            if(strcmp(strtolower($name), strtolower($complete)) == 0){
              return array("First" => $first, "Last" => $array['Last'][$index]);
            }     
        }
    }
}
function get_gravity_id($order_id){
    global $db_connect;

    $sql = "select meta_value from wp_postmeta where meta_key = '_gravity_form_data' and post_id = '$order_id' ";
    $result = db_query($sql);
    $row = $result->fetch_assoc();
    # meta_value returns a ; delimited field
    $row = explode(';', $row['meta_value']);
    # break up field by :, last fragment has form id
    $form_id = explode(':',$row[1]);
    $form_id = end($form_id);
    $form_id = str_replace('"','',$form_id);
    return $form_id;
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
function table_header($data){
    $html = "<table border=1>\n";
    $html .= "<thead><tr>\n";
    $html .= "<td>AM</td><td>First</td><td>Last</td>";
    if(isset($data['Pickup Location']))
      $html .= "<td>Pickup</td>";
    $html .= "<td>Phone</td><td>Package</td><td>Order</td><td>Waiver</td><td>Product REC.</td><td>PM Checkin</td><td>Bus Only</td>";
    $html .= "<td>All Area Lift</td><td>Beg. Lift</td><td>BRD Rental</td><td>Ski Rental</td><td>LTS</td><td>LTR</td><td>Prog. Lesson</td>\n";
    $html .= "</tr></thead>\n";
    $html .= "<tbody>";
    return $html;
}
function table_row($data){
    $html = "";
    foreach($data['First'] as $index => $first){
        $html .= "<tr><td></td><td>".$first."</td><td>".$data['Last'][$index]."</td>";
        if(isset($data['Pickup Location'][$index]))
            $html .= "<td>".$data['Pickup Location'][$index]."</td>";
        $html .= "<td>".$data['Phone']."</td><td>".$data['Package'][$index]."</td><td>".$data['Order']."</td>";
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