<div id="trips_primary_packages" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="primary-packages-options">
       <?php woocommerce_wp_checkbox( array( 'id' => '_wc_trip_primary_package_stock', 'label' => 'Enable package stock', 'description' => 'Enable this option to manage stock on some or all primary packages', 'desc_tip' => true, 'value' => get_post_meta( $post_id, '_wc_trip_primary_package_stock', true ) ) ); ?>
       <p>Stock on packages is limited by the stock of the product. Leaving a stock field blank will not impose an addition restriction on the package</p>
    </div>
    <div class="options_group" id="primary-packages">
        <?php woocommerce_wp_text_input( array( 'id' => '_wc_trip_primary_package_label', 'label' => 'Primary Package label', 'description' => 'Label to be shown on product page', 'desc_tip' => false, 'value' => get_post_meta( $post_id, '_wc_trip_primary_package_label', true ) ) );?>
        <div class="toolbar">
            <h3>Primary Packages</h3>
            <br />
        </div>

        <div class="woocommerce_trip_primary_packages wc-metaboxes">
            <table class="woocommerce_trip_primary_packages">
                <thead>
                    <tr>
                        <th class="sorting">&nbsp;</th>
                        <th class="description">Description</th>
                        <th class="cost">Cost</th>
                        <th class="primary_package_stock">Stock</th>
                        <th class="delete_column">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="primary_package_rows">
            <?php
                $primary_package = get_post_meta($post_id, "_wc_trip_primary_packages", true);
                foreach ( $primary_package as $key => $values ) {
                    echo <<< PRIMARYROW
                        <tr>
                            <td class='sorter'>&nbsp;</td>
                            <td>
                                <input type='text' name='wc_trips_primary_package_description[]' value='{$values['description']}'>
                                </input>
                            </td>
                            <td>
                                <input type='text' name='wc_trips_primary_package_cost[]' value='{$values['cost']}'>
                                </input>
                            </td>
                            <td class='primary_package_stock'>
                                <input type='number' name='wc_trips_primary_package_stock[]' value='{$values['stock']}'>
                                </input>
                            </td>
                            <td class='delete'>&nbsp;</td>
                        </tr>
PRIMARYROW;
                }
                if ( sizeof( $primary_package ) == 0 ) {
                    echo <<< PPMessage
                        <div id="message" class="inline woocommerce-message" style="margin: 1em 0;">
                            <div class="squeezer">
                                &nbsp;<h4>Package options for trip</h4>
                            </div>
                        </div>
PPMessage;
                }
            ?>
            </tbody>
        </table>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_package" id="primary_package_add" data-row="<tr>
                <td class='sorter'>&nbsp;</td>
                <td><input type='text' name='wc_trips_primary_package_description[]'></input></td>
                <td><input type='text' name='wc_trips_primary_package_cost[]'></input></td>
                <td class='primary_package_stock'><input type='number' name='wc_trips_primary_package_stock[]'></input></td>
                <td class='delete'>&nbsp;</td>
            </tr>">Add primary package</button>
        </p>
    </div>
</div>