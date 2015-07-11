<div id="trips_primary_packages" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
	<div class="options_group" id="primary-packages-options">
       <?php woocommerce_wp_checkbox( array( 'id' => '_wc_trip_primary_package_stock', 'label' => 'Enable package stock', 'description' => 'Enable this option to manage stock on some or all primary packages', 'desc_tip' => true, 'value' => get_post_meta( $post_id, '_wc_trip_primary_package_stock', true ) ) ); ?>
    </div>
    <div class="options_group" id="primary-packages">

        <div class="toolbar">
            <h3>Primary Packages</h3>
            <a href="#" class="close_all">Close All</a><a href="#" class="expand_all">Expand All</a>
            <br />
        </div>

        <div class="woocommerce_trip_primary_packages wc-metaboxes">

            <?php
                global $post;

                $primary_packages = get_posts( array(
                    'post_type'      => 'trips_primary_package',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'asc',
                    'post_parent'    => $post->ID
                ) );

                if ( sizeof( $primary_packages ) == 0 ) {
                    echo <<< PPMessage
                        <div id="message" class="inline woocommerce-message" style="margin: 1em 0;">
                            <div class="squeezer">
                                &nbsp;<h4>Package options for trip</h4>
                            </div>
                        </div>
PPMessage;
                }

                /*if ( $primary_packages ) {
                    $count = 0;

                    foreach ( $person_types as $person_type ) {
                        // sort out what to do with post here
                    }
                }*/
            ?>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_package">Add primary package</button>
        </p>
    </div>
</div>