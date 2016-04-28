<div id="trips_packages" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="primary-packages">
        <div class="toolbar">
            <h3>Packages</h3>
            <br />
        </div>

        <div class="woocommerce_trip_primary_packages wc-metaboxes">
            <input type="hidden" name="_wc_trip_package_stock" id="_wc_trip_package_stock" value="no" />
            <input type="hidden" name="_wc_trip_package_label" id="_wc_trip_package_label" value="Package" />
            <table class="woocommerce_trip_packages">
                <thead>
                        <th class="sorting">&nbsp;</th>
                        <th class="description">Description</th>
                        <th class="cost">Cost ($)</th>
                        <th class="delete_column">&nbsp;</th>
                </thead>
                <tbody id="package_rows">
            <?php
                $primary_package = get_post_meta($post_id, "_wc_trip_primary_packages", true);

                if (is_array($primary_package) || is_object($primary_package)) {
                    foreach ( $primary_package as $key => $values ) {
                        echo <<< PRIMARYROW
                        <tr>
                            <td class='sorter sorting'>&nbsp;</td>
                            <td class="package_description">
                                <input type='text' name='wc_trips_package_description[]' value='{$values['description']}' />
                            </td>
                            <td class="cost">
                                <input type='text' name='wc_trips_package_cost[]' value='{$values['cost']}'>
                                </input>
                            </td>
                            <td class='primary_package_stock stock'>
                                <input type='number' name='wc_trips_package_stock[]' value='{$values['stock']}'>
                                </input>
                            </td>
                            <td class='deleteButton'>&nbsp;</td>
                        </tr>
PRIMARYROW;
                    }
                } else {
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
            <button type="button" class="button button-primary add_package" id="package_add" data-row="<tr>
                <td class='sorter sorting'>&nbsp;</td>
                <td class='package_description'><input type='text' name='wc_trips_package_description[]'></input></td>
                <td class='cost'><input type='text' name='wc_trips_package_cost[]'></input></td>
                <td class='deleteButton'>&nbsp;</td>
            </tr>">Add package</button>
        </p>
    </div>
</div>
