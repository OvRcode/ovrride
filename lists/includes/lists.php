<?php
/**
 * OvR Lists - Functions and Definitions
 *
 * @package OvR Lists
 * @since Version 0.0.2
 */

$lists_version = "0.1.0";

class Trip_List{
    var $db_connect;
    var $trip;
    var $select_options;
    var $orders;
    var $order_data;
    var $has_pickup;
    var $html_table;
    function __construct($selected_trip){
        # Connect to database
        include 'config.php';
        $this->db_connect = new mysqli($host,$user,$pass,$db); 
        if($this->db_connect->connect_errno > 0){
            die('Unable to connect to database [' . $this->db_connect->connect_error . ']');
        }
        $this->trip = $selected_trip;
        $this->trip_options();
        if($selected_trip != ""){
            $this->find_orders();
            if(count($this->orders) > 0){
                $this->get_order_data();
                $this->generate_table();
              }
              else{ $this->html_table = "There are no orders for the selected Trip and Order Status."; }
          }

    }
    private function db_query($sql){
        if(!$result = $this->db_connect->query($sql))
            die('There was an error running the query [' . $this->db_connect->error . ']');
        else
          return $result;
    }
    private function trip_options(){
        # Find trips
        $sql = "select `id`, `post_title` from `wp_posts` where `post_status` = 'publish' and `post_type` = 'product' order by `post_title`";
        $result = $this->db_query($sql);

        # Construct options for a select field
        $this->select_options = "<option value=''";
        if($this->trip == "")
            $this->select_options .= " selected ";
        $this->select_options .= "> Select trip </option>\n";
        while($row = $result->fetch_assoc()){
            $this->select_options .= "<option value='".$row['id']."'";
            if($this->trip == $row['id'])
                $this->select_options .= " selected ";
            $this->select_options .= ">".$row['post_title']."</option>\n";
        }
        # Clean up
        $result->free();
    }
    private function find_orders(){
        # Conditional SQL for checkboxes on form
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
            AND `wp_woocommerce_order_itemmeta`.`meta_value` =  '$this->trip'
            AND ($sql_conditional)";
        if($sql_conditional === "")
          $sql = substr($sql, 0, -6);

        $result = $this->db_query($sql);
        $this->orders = array();
        while($row = $result->fetch_assoc()){
            $this->orders[] = array("id" => $row['ID'], "order_item_id" => $row['order_item_id']);
        }

        $result->free();
    }
    private function get_order_data(){
        foreach($this->orders as $key => $value){
          $order = $value["id"];
          $order_item_id = $value["order_item_id"];
          
          $sql = "select `meta_key`, `meta_value` from `wp_woocommerce_order_itemmeta` 
                    where 
                    ( meta_key = '_product_id' or meta_key ='How many riders are coming?' or meta_key = 'Name' or meta_key = 'Email' 
                    or meta_key = 'Package' or meta_key = 'Pickup Location' ) 
                    and order_item_id = '$order_item_id'";
          $result = $this->db_query($sql);
          while($row = $result->fetch_assoc()){
              if($row['meta_key'] == 'How many riders are coming?' || $row['meta_key'] == '_product_id')
                $this->order_data[$order][$row['meta_key']] = $row['meta_value'];
              elseif($row['meta_key'] == 'Package')
                $this->order_data[$order][$row['meta_key']][] = preg_replace('/\(\$\S*\)/', "", $row['meta_value']);
              else
                $this->order_data[$order][$row['meta_key']][] = $row['meta_value'];
          }
          $result->free();

          foreach($this->order_data[$order]['Name'] as $index => $name){
              $name = $this->split_name($name,$this->order_data[$order]['_product_id']);
              $this->order_data[$order]['First'][] = $name['First'];
              $this->order_data[$order]['Last'][] = $name['Last'];

          }

          # Get phone number
          $sql2 = "SELECT  `meta_value` AS  `Phone`
                    FROM wp_postmeta
                    WHERE meta_key =  '_billing_phone'
                    AND post_id =  '$order'";
                    
          $result2 = $this->db_query($sql2);
          $row = $result2->fetch_assoc();
          $result2->free();
          $this->order_data[$order]['Phone'] = $row['Phone'];

          # Fix phone formatting
          $this->order_data[$order]['Phone'] = $this->reformat_phone($this->order_data[$order]['Phone']);

          # Is there a pickup location for this trip?
          if(isset($this->order_data[$order]['Pickup Location'][0]))
              $this->has_pickup = TRUE;
          elseif($this->has_pickup == "")
              $this->has_pickup = FALSE;
      }
    }
    private function generate_table(){
      $total_guests = 0;
      $head = "<table class='table table-bordered table-striped table-condensed'>\n<thead><tr>\n<td>AM</td><td>First</td><td>Last</td>";
      if($this->has_pickup)
        $head .= "<td>Pickup</td>";
      $head .= "<td>Phone</td><td>Package</td><td>Order</td><td>Waiver</td><td>Product REC.</td><td>PM Checkin</td><td>Bus Only</td>";
      $head .= "<td>All Area Lift</td><td>Beg. Lift</td><td>BRD Rental</td><td>Ski Rental</td><td>LTS</td><td>LTR</td><td>Prog. Lesson</td>\n";
      $head .= "</tr></thead>\n";
      $body = "<tbody>\n";

      foreach($this->order_data as $order => $info){
          foreach($info['First'] as $index => $first){
            $total_guests += 1;
            $body .= "<tr><td></td><td>".$first."</td><td>".$info['Last'][$index]."</td>";
            if($this->has_pickup)
              $body .= "<td>".$info['Pickup Location'][$index]."</td>";
            $body .= "<td>".$info['Phone']."</td><td>".$info['Package'][$index]."</td><td>".$order."</td>";
            $body .= "<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>\n";
          }
      }
      $body .= "</tbody>\n";
      $foot = "<tfoot>\n<tr><td colspan=2 >Total Guests: </td><td>$total_guests</td></tr></tfoot></table>";
      $this->html_table = $head . $body . $foot;
    }
    private function get_gravity_id($order_id){
        $sql = "select meta_value from wp_postmeta where meta_key = '_gravity_form_data' and post_id = '$order_id' ";
        $result = $this->db_query($sql);
        $row = $result->fetch_assoc();
        # meta_value returns a ; delimited field
        $row = explode(';', $row['meta_value']);
        # break up field by :, last fragment has form id
        $form_id = explode(':',$row[1]);
        $form_id = end($form_id);
        $form_id = str_replace('"','',$form_id);
        return $form_id;
    }
    private function split_name($name,$order_id){
      $form_id = $this->get_gravity_id($order_id);
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
        $result = $this->db_query($sql);
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
        # Now that we have complete names, loop through array and match against provided name
        foreach ($names as $lead => $array){
            foreach($array['First'] as $index => $first){
                $complete = trim($first) . " " . trim($array['Last'][$index]);
                if(strcmp(strtolower($name), strtolower($complete)) == 0){
                  return array("First" => $first, "Last" => $array['Last'][$index]);
                }
            }
        }
    }
    private function reformat_phone($phone){
        # Strip all formatting
        $phone = str_replace('-','',$phone);
        $phone = str_replace('(','',$phone);
        $phone = str_replace(')','',$phone);
        $phone = str_replace(' ','',$phone);
        $phone = str_replace('.','',$phone);

        # Add formatting to raw phone num
        $phone = substr_replace($phone,'(',0,0);
        $phone = substr_replace($phone,') ',4,0);
        $phone = substr_replace($phone,'-',9,0);

        return $phone;
    }
}
?>