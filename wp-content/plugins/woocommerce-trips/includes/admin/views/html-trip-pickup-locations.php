<div id="trips_pickup_locations" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="pickup-locations">

        <div class="toolbar">
            <h3>Pickup Locations</h3>
            <a href="#" class="close_all">Close All</a><a href="#" class="expand_all">Expand All</a>
            <br />
        </div>

        <div class="woocommerce_trip_pickup_locations wc-metaboxes">

            <?php
                global $post;

                $pickup_locations = get_posts( array(
                    'post_type'      => 'trips_pickup_locations',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'asc',
                    'post_parent'    => $post->ID
                ) );

                if ( sizeof( $pickup_locations ) == 0 ) {
                    echo <<< PPMessage
                        <div id="message" class="inline woocommerce-message" style="margin: 1em 0;">
                            <div class="squeezer">
                                &nbsp;<h4>Pickup Locations for trip</h4>
                            </div>
                        </div>
PPMessage;
                }

                /*if ( $pickup_locations ) {
                    // do something here
                }*/
            ?>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_pickup">Add pickup location</button>
        </p>
    </div>
</div>