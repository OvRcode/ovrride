<div id="trips_pickup_locations" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="pickup-locations">

        <div class="toolbar">
            <h2>Pickup Locations</h2>
        </div>

        <div class="woocommerce_trip_pickup_locations wc-metaboxes">
            <?php
                global $post;

                $all_pickup_locations = get_posts( array(
                    'post_type'      => 'pickup_locations',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'asc'
                ) );
                //$pickup_locations = array("49894", "49891", "49892", "49893");
                $pickup_location = get_post_meta( $post->ID, '_wc_trip_pickups', true);
                $count = 0;
                // MISSING DATA WHEN ADDED TO ADMIN PANEL
                foreach( $pickup_locations as $location_id) {
                    $location = get_post( $location_id );
                    if ( "No Bus" == $location->post_title) {
                        $location_time = "";
                    } else {
                        $location_time = " - " . date("g:i a", strtotime($location_time));
                    }
                    
                    include('html-trip-pickup-location.php');
                    
                    $count++;
                }
            ?>
        </div>

        <p class="toolbar">
            <a href="<?php echo admin_url("edit.php?post_type=pickup_locations")?>" target="_blank" class="pickup_manage">
                Manage Pickup Locations &rarr;
            </a>
            <button type="button" class="button button-primary add_pickup" data-row = "<td class='sort'>">Add pickup location</button>
            <select name="add_pickup_location_id" class="add_pickup_location_id">
                <option value="">New Pickup Location</option>
                <?php
                    if ( $all_pickup_locations ) {
                        foreach( $all_pickup_locations as $pickup ) {
                            $time = get_post_meta( $pickup->ID, '_pickup_location_time', true);
                            if ( "No Bus" == $pickup->post_title) {
                                $time = "";
                            } else {
                                $time = " - " . date("g:i a", strtotime($time));
                            }
                            echo '<option value="' .esc_attr($pickup->ID).'">'. esc_html( $pickup->post_title ) . $time . '</option>';
                        }
                    }
                ?>
            </select>
        </p>
    </div>
</div>