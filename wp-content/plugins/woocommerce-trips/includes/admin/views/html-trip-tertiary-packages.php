<div id="trips_tertiary_packages" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="tertiary-packages-options">
       <?php woocommerce_wp_checkbox( array( 'id' => '_wc_trip_tertiary_package_stock', 'label' => 'Enable package stock', 'description' => 'Enable this option to manage stock on some or all tertiary packages', 'desc_tip' => true, 'value' => get_post_meta( $post_id, '_wc_trip_tertiary_package_stock', true ) ) ); ?>
       <p>Stock on packages is limited by the stock of the product. Leaving a stock field blank will not impose an addition restriction on the package</p>
    </div>
    <div class="options_group" id="tertiary-packages">
        <?php woocommerce_wp_text_input( array( 'id' => '_wc_trip_tertiary_package_label', 'label' => 'Tertiary Package label', 'description' => 'Label to be shown on product page', 'desc_tip' => false, 'value' => get_post_meta( $post_id, '_wc_trip_tertiary_package_label', true ) ) );?>
        <div class="toolbar">
            <h3>Tertiary Packages</h3>
            <br />
        </div>

        <div class="woocommerce_trip_tertiary_packages wc-metaboxes">
            <table class="woocommerce_trip_tertiary_packages">
                <thead>
                    <tr>
                        <th class="sorting">&nbsp;</th>
                        <th class="description">Description</th>
                        <th class="cost">Cost</th>
                        <th class="tertiary_package_stock">Stock</th>
                        <th class="delete_column">&nbsp;</th>
                    </tr>
                </thead>
                <tbody id="tertiary_package_rows">
            <?php
                $tertiary_package = get_post_meta($post_id, "_wc_trip_tertiary_packages", true);
                if (is_array($tertiary_package) || is_object($tertiary_package)) {
                    foreach ( $tertiary_package as $key => $values ) {
                        echo <<< PRIMARYROW
                        <tr>
                            <td class='sorter'>&nbsp;</td>
                            <td>
                                <input type='text' name='wc_trips_tertiary_package_description[]' value='{$values['description']}'>
                                </input>
                            </td>
                            <td>
                                <input type='text' name='wc_trips_tertiary_package_cost[]' value='{$values['cost']}'>
                                </input>
                            </td>
                            <td class='tertiary_package_stock'>
                                <input type='number' name='wc_trips_tertiary_package_stock[]' value='{$values['stock']}'>
                                </input>
                            </td>
                            <td class='delete'>&nbsp;</td>
                        </tr>
PRIMARYROW;
                    }
                }
                if ( sizeof( $tertiary_package ) == 0 ) {
                    echo <<< PPMessage
                        <div id="message" class="inline woocommerce-message" style="margin: 1em 0;">
                            <div class="squeezer">
                                &nbsp;<h4>Tertiary package options for trip</h4>
                            </div>
                        </div>
PPMessage;
                }
            ?>
            </tbody>
        </table>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_package" id="tertiary_package_add" data-row="<tr>
                <td class='sorter'>&nbsp;</td>
                <td><input type='text' name='wc_trips_tertiary_package_description[]'></input></td>
                <td><input type='text' name='wc_trips_tertiary_package_cost[]'></input></td>
                <td class='tertiary_package_stock'><input type='number' name='wc_trips_tertiary_package_stock[]'></input></td>
                <td class='delete'>&nbsp;</td>
            </tr>">Add tertiary package</button>
        </p>
    </div>
</div>