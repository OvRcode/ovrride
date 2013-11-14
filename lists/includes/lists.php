<?php
/**
 * OvR Lists - Functions and Definitions
 *
 * @package OvR Lists
 * @since Version 0.0.2
 */

# OvR Lists Version Number
$lists_version = "0.3.2";

# Form
if(isset($_SESSION['saved_table']) && $_SESSION['saved_table'])
    unset($_SESSION['saved_table']);
else
    $_SESSION['post_data'] = $_POST;

if(isset($_SESSION['post_data']['trip']))
    $list = new Trip_List($_SESSION['post_data']['trip']);
else
    $list = new Trip_List("None");

if(isset($_SESSION['post_data']['trip']) && isset($_SESSION['post_data']['csv_list'])){
    if($_SESSION['post_data']['csv_list'] == "csv_list")
        $list->csv("trip_list");
}
    

if(isset($_SESSION['post_data']['trip']) && isset($_SESSION['post_data']['csv_email'])){
    if($_SESSION['post_data']['csv_email'] == "csv_email")
        $list->csv("email_list");
}

class Trip_List{
    var $db_connect;
    var $trip;
    var $select_options;
    var $orders;
    var $order_data;
    var $has_pickup;
    var $html_table;
    var $html_checkboxes;
    function __construct($selected_trip){
        # Connect to database
        require_once("config.php");
        $this->db_connect = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);
        if($this->db_connect->connect_errno > 0){
            die('Unable to connect to database [' . $this->db_connect->connect_error . ']');
        }
        $this->trip = $selected_trip;
        $this->trip_options();
        if($selected_trip != "none"){
            $this->find_orders();
            if(count($this->orders) > 0){
                $this->get_order_data();
                #$this->get_saved_data();
                #$this->generate_table();
              }
              else{ $this->html_table = "There are no orders for the selected Trip and Order Status."; }
          }

    }
    function csv($type) {
        $sql = "select `post_title` from `wp_posts` where `ID` = '$this->trip' and `post_status` = 'publish' and `post_type` = 'product' order by `post_title`";
        $result = $this->db_query($sql);
        $name = $result->fetch_assoc();
        $filename = $name['post_title'];
        if($type == "email_list")
        $filename .= "_EMAIL";

        header("Content-type: text/csv");  
        header("Cache-Control: no-store, no-cache");  
        header("Content-Disposition: attachment; filename={$filename}.csv");
        $f = fopen('php://output', 'w');
        # start CSV with column labels
        if($type == "trip_list"){
            if($this->has_pickup)
                $labels = array("AM","First","Last","Pickup","Phone","Package","Order","Waiver","Product REC.","PM Checkin","Bus Only","All Area Lift","Beg. Lift","BRD Rental","Ski Rental","LTS","LTR","Prog. Lesson");
            else
                $labels = array("AM","First","Last","Phone","Package","Order","Waiver","Product REC.","PM Checkin","Bus Only","All Area Lift","Beg. Lift","BRD Rental","Ski Rental","LTS","LTR","Prog. Lesson");
        }
        elseif($type == "email_list"){
          $labels = array("Email", "First","Last");
        }
        fputcsv($f,$labels,',');
        
        foreach($this->order_data as $order => $info){
          foreach($info['First'] as $index => $first){
            if($type == "trip_list"){
                if($this->has_pickup)
                    $array = array("",$first, $info['Last'][$index], $info['Pickup Location'][$index], $info['Phone'], $info['Package'][$index], $order);
                else
                    $array = array("",$first, $info['Last'][$index], $info['Phone'], $info['Package'][$index], $order);
            }
            elseif($type == "email_list"){
              $array = array($info['Email'][$index], $first, $info['Last'][$index]);
            }
                
            fputcsv($f,$array,',');
          }
        }

        fclose($f);
        exit();
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
        $this->select_options = '<option value="none"';
        if($this->trip == "none")
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
          if(isset($_SESSION['post_data'][$field])){
            if($sql_conditional == "")
              $sql_conditional .= "`wp_terms`.`name` = '$field'";
            else
              $sql_conditional .= " OR `wp_terms`.`name` = '$field'";
          }
        }

        $sql = "SELECT `wp_posts`.`ID`, `wp_woocommerce_order_items`.`order_item_id`, `wp_terms`.`name`
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
            $this->orders[$row['ID']][$row['order_item_id']] = $row['name'];
        }

        $result->free();
    }
    function get_order_data(){
        foreach($this->orders as $order => $data){
            # Get phone number
            $sql = "SELECT  `meta_value` AS  `Phone`
                    FROM wp_postmeta
                    WHERE meta_key =  '_billing_phone'
                    AND post_id =  '$order'";
            $result = $this->db_query($sql);
            $row = $result->fetch_assoc();
            $phone = $row['Phone'];
            $result->free();

            foreach($data as $order_item_id => $status){
                $this->order_data[$order][$order_item_id]['Phone'] = $this->reformat_phone($phone);
                $sql = "select `meta_key`, `meta_value` from `wp_woocommerce_order_itemmeta` 
                        where 
                        ( meta_key = 'Name' 
                        or meta_key = 'Email' 
                        or meta_key = 'Package' 
                        or meta_key = 'Pickup Location' ) 
                        and order_item_id = '$order_item_id'";
                $result = $this->db_query($sql);
              
                while($row = $result->fetch_assoc()){
                    if($row['meta_key'] == 'Package')
                        $this->order_data[$order][$order_item_id]['Package'] = $this->remove_package_price($row['meta_value']);
                    elseif($row['meta_key'] == 'Pickup Location')
                        $this->order_data[$order][$order_item_id]['Pickup Location'] = $this->strip_time($row['meta_value']);
                    else
                        $this->order_data[$order][$order_item_id][$row['meta_key']] = trim($row['meta_value']);
                }
                $result->free();
                $this->get_checkbox_states($order,$order_item_id);
                
                # Is there a pickup location for this trip?
                if(isset($this->order_data[$order][$order_item_id]['Pickup Location']))
                    $this->has_pickup = TRUE;
                elseif($this->has_pickup == "")
                    $this->has_pickup = FALSE;
            }
        }
    }
    private function remove_package_price($package){
        return preg_replace('/\(\$\S*\)/', "", $package);
    }
    private function get_checkbox_states($order,$order_item_id){
        # Attempts to lookup set checkboxes from form in ovr_lists_checkboxes table
        $ID = $order+":"+$order_item_id;
        $sql="select `AM`,`PM`,`Waiver`,`Product`,`Bus`,`All_Area`,`Beg`,`BRD`,`SKI`,`LTS`,`LTR`,`Prog_Lesson` from `ovr_lists_checkboxes` where `ID` = '$ID'";
        $result = $this->db_query($sql);
        if($row = $result->fetch_assoc())
            foreach($row as $key => $value)
                $this->order_data[$order][$order_item_id][$key] = $value;
    }
    private function get_saved_data(){
      $sql = "select * from `ovr_lists_table` where `trip` = '$this->trip'";
      $result = $this->db_query($sql);
      while($row = $result->fetch_assoc()){
        $order = $row['order'];
        $item_id = $row['item_id'];
        $order_prefix = substr($order, 0,2);
        if($order_prefix != "WO"){
            $id = $key = array_search($row['item_id'], $this->order_data[$order]['item_id']);
            $this->order_data[$order]['First'][$id] = $row['First'];
            $this->order_data[$order]['Last'][$id] = $row['Last'];
            $this->order_data[$order]['Package'][$id] = $row['Package'];
            $this->order_data[$order]['Pickup'][$id] = $row['Pickup'];
            $this->order_data[$order]['Phone'] = $row['Phone'];
        }else{
            $this->order_data[$order]['item_id'][] = $row['item_id'];
            $this->order_data[$row['order']]['First'][] = $row['First'];
            $this->order_data[$row['order']]['Last'][] = $row['Last'];
            $this->order_data[$order]['Package'][] = $row['Package'];
            $this->order_data[$order]['Pickup Location'][] = $row['Pickup'];
            $this->order_data[$order]['Phone'] = $row['Phone'];
        }
        $this->html_checkboxes[$order][$item_id]['AM'] = $row['AM'];
        $this->html_checkboxes[$order][$item_id]['PM'] = $row['PM'];
        $this->html_checkboxes[$order][$item_id]['Waiver'] = $row['Waiver'];
        $this->html_checkboxes[$order][$item_id]['Product'] = $row['Product'];
        $this->html_checkboxes[$order][$item_id]['Bus'] = $row['Bus'];
        $this->html_checkboxes[$order][$item_id]['All_Area'] = $row['All_Area'];
        $this->html_checkboxes[$order][$item_id]['Beg'] = $row['Beg'];
        $this->html_checkboxes[$order][$item_id]['BRD'] = $row['BRD'];
        $this->html_checkboxes[$order][$item_id]['SKI'] = $row['SKI'];
        $this->html_checkboxes[$order][$item_id]['LTS'] = $row['LTS'];
        $this->html_checkboxes[$order][$item_id]['LTR'] = $row['LTR'];
        $this->html_checkboxes[$order][$item_id]['Prog_Lesson'] = $row['Prog_Lesson'];
      }
    }
    private function generate_table(){
      $total_guests = 0;

      $head = "<div class='table-responsive'>\n
               <table id='Listable' class='tablesorter table table-bordered table-striped table-condensed'>\n
                 <thead>
                   <tr class='tablesorter-headerRow'>\n
                   <td>AM</td>
                   <td>PM</td>
                   <td>First</td>
                   <td>Last</td>";
                   
      if($this->has_pickup)
        $head .= "<td>Pickup</td>";
        
      $head .= "<td>Phone</td>
                <td>Package</td>
                <td>Order</td>
                <td>Waiver</td>
                <td>Product REC.</td>
                <td>Bus Only</td>";
                
      $head .= "<td>All Area Lift</td>
                <td>Beg. Lift</td>
                <td>BRD Rental</td>
                <td>Ski Rental</td>
                <td>LTS</td>
                <td>LTR</td>
                <td>Prog. Lesson</td>\n";
                
      $head .= "</tr>
                </thead>\n";
                
      $body = "<tbody>\n";

      foreach($this->order_data as $order => $info){
          foreach($info['First'] as $index => $first){
            $total_guests += 1;
            $id = $order .":".$info['item_id'][$index];
            $body .= <<< EOT
              <tr>
                <td><input type='checkbox' name='{$id}:AM' {$this->is_checkbox_set($order,$info['item_id'][$index],"AM")}></td>
                <td><input type='checkbox' name='{$id}:PM' {$this->is_checkbox_set($order,$info['item_id'][$index],"PM")}></td>
                <td>{$first}</td>
                <td>{$info['Last'][$index]}</td>
EOT;
            if($this->has_pickup)
                $body .= "<td>".$info['Pickup Location'][$index]."</td>";
            $body .= <<< EOT2
                <td>{$info['Phone']}</td>
                <td>{$info['Package'][$index]}</td>
                <td class='no-edit'>{$order}<input type='hidden' name='{$id}:item_id' value='{$info['item_id'][$index]}'></td>
                <td><input type='checkbox' name='{$id}:Waiver' {$this->is_checkbox_set($order,$info['item_id'][$index],"Waiver")}></td>
                <td><input type='checkbox' name='{$id}:Product' {$this->is_checkbox_set($order,$info['item_id'][$index],"Product")}></td>
                <td><input type='checkbox' name='{$id}:Bus' {$this->is_checkbox_set($order,$info['item_id'][$index],"Bus")}></td>
                <td><input type='checkbox' name='{$id}:All_Area' {$this->is_checkbox_set($order,$info['item_id'][$index],"All_Area")}></td>
                <td><input type='checkbox' name='{$id}:Beg' {$this->is_checkbox_set($order,$info['item_id'][$index],"Beg")}></td>
                <td><input type='checkbox' name='{$id}:BRD' {$this->is_checkbox_set($order,$info['item_id'][$index],"BRD")}></td>
                <td><input type='checkbox' name='{$id}:SKI' {$this->is_checkbox_set($order,$info['item_id'][$index],"SKI")}></td>
                <td><input type='checkbox' name='{$id}:LTS' {$this->is_checkbox_set($order,$info['item_id'][$index],"LTS")}></td>
                <td><input type='checkbox' name='{$id}:LTR' {$this->is_checkbox_set($order,$info['item_id'][$index],"LTR")}></td>
                <td><input type='checkbox' name='{$id}:Prog_Lesson' {$this->is_checkbox_set($order,$info['item_id'][$index],"Prog_Lesson")}></td>
              </tr>
EOT2;
          }
      }
      $body .= "</tbody>\n";
      $foot = "<tfoot>\n<tr>
                 <td colspan=2 >Total Guests: </td>
                 <td id='total_guests'>$total_guests</td>
                 <td><button type='button' class='btn btn-primary' id='add'><span class='glyphicon glyphicon-plus'></span></button></td>
                 </tr>
               </tfoot>
               </table>
               </div><!-- /.table-responsive -->";
      $this->html_table = $head . $body . $foot;
    }
    private function is_checkbox_set($order,$item_id,$value){
      if(isset($this->html_checkboxes[$order][$item_id][$value]) && $this->html_checkboxes[$order][$item_id][$value] == TRUE )
        return " checked ";
      else
        return " ";
    }
    private function split_name($name){
        $parts = explode(" ", $name);
        $last = array_pop($parts);
        $first = implode(" ", $parts);
        
        return array("First" => $first, "Last" => $last); 
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
    private function strip_time($pickup){
      # Remove dash and time from pickup
      preg_match("/(.*).-.*/", $pickup, $matched);
      return $matched[1];
    }
}
?>