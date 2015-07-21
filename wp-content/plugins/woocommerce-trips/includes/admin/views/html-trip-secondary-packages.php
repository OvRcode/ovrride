<div id="trips_secondary_packages" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
	<div class="options_group" id="secondary-packages-options">
       <?php woocommerce_wp_checkbox( array( 'id' => '_wc_trip_secondary_package_stock', 'label' => 'Enable package stock', 'description' => 'Enable this option to manage stock on some or all secondary packages', 'desc_tip' => true, 'value' => get_post_meta( $post_id, '_wc_trip_secondary_package_stock', true ) ) ); ?>
       <p>Stock on packages is limited by the stock of the product. Leaving a stock field blank will not impose an addition restriction on the package</p>
    </div>
    <div class="options_group" id="secondary-packages">

        <div class="toolbar">
            <h3>Primary Packages</h3>
            <a href="#" class="close_all">Close All</a><a href="#" class="expand_all">Expand All</a>
            <br />
        </div>

        <div class="woocommerce_trip_secondary_packages wc-metaboxes">
            <table class="woocommerce_trip_secondary_packages">
                <thead>
                    <tr>
                        <th class="sort" width="1%">&nbsp;</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th class="secondary_package_stock">Stock</th>
                    </tr>
                </thead>
                <tbody>
            <?php
                global $post;

                $secondary_packages = get_posts( array(
                    'post_type'      => 'trips_secondary_package',
                    'post_status'    => 'publish',
                    'posts_per_page' => -1,
                    'orderby'        => 'menu_order',
                    'order'          => 'asc',
                    'post_parent'    => $post->ID
                ) );

                if ( sizeof( $secondary_packages ) == 0 ) {
                    echo <<< PPMessage
                        <div id="message" class="inline woocommerce-message" style="margin: 1em 0;">
                            <div class="squeezer">
                                &nbsp;<h4>Secondary package options for trip</h4>
                            </div>
                        </div>
PPMessage;
                }

                /*if ( $secondary_packages ) {
                    $count = 0;

                    foreach ( $person_types as $person_type ) {
                        // sort out what to do with post here
                    }
                }*/
            ?>
            </tbody>
            </table>
        </div>
    
        <p class="toolbar">
            <button type="button" id="secondary_package_add" class="button button-primary add_package" data-row="<tr>
                <td class='sort'>&nbsp;</td>
                <td><input type='text' name='_wc_trips_secondary_package_description' id='_wc_trips_secondary_package_description'></input></td>
                <td><input type='text' name='_wc_trips_secondary_package_cost' id='_wc_trips_secondary_package_cost'></input></td>
                <td class='secondary_package_stock'><input type='number' name='_wc_trips_secondary_package_stock' id='_wc_trips_secondary_package_stock'></input></td>
            </tr>">Add secondary package</button>
        </p>
    </div>
</div>