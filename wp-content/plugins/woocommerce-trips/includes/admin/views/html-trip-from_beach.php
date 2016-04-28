<div id="trips_from_beach" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="to-beach">
        <div class="toolbar">
            <h3>From Beach</h3>
            <br />
        </div>

        <div class="woocommerce_trip_from_beach wc-metaboxes">
            <table class="woocommerce_trip_from_beach">
                <thead>
                        <th class="sorting">&nbsp;</th>
                        <th class="description">Description</th>
                        <th class="cost">Cost ($)</th>
                        <th class="tertiary_package_stock">Stock</th>
                        <th class="delete_column">&nbsp;</th>
                </thead>
                <tbody id="from_beach_rows">
            <?php
                $tertiary_package = get_post_meta($post_id, "_wc_trip_tertiary_packages", true);

                if (is_array($tertiary_package) || is_object($tertiary_package)) {
                    foreach ( $tertiary_package as $key => $values ) {
                        echo <<< PRIMARYROW
                        <tr>
                            <td class='sorter sorting'>&nbsp;</td>
                            <td class="package_description">
                                <input type='text' name='wc_trips_from_beach_description[]' value='{$values['description']}' />
                            </td>
                            <td class="cost">
                                <input type='text' name='wc_trips_from_beach_cost[]' value='{$values['cost']}'>
                                </input>
                            </td>
                            <td class='tertiary_package_stock stock'>
                                <input type='number' name='wc_trips_from_beach_stock[]' value='{$values['stock']}'>
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
            <button type="button" class="button button-secondary add_package" id="from_beach_add" data-row="<tr>
                <td class='sorter sorting'>&nbsp;</td>
                <td class='package_description'><input type='text' name='wc_trips_from_beach_description[]'></input></td>
                <td class='cost'><input type='text' name='wc_trips_from_beach_cost[]'></input></td>
                <td class='from_beach_stock stock'><input type='number' name='wc_trips_from_beach_stock[]'></input></td>
                <td class='deleteButton'>&nbsp;</td>
            </tr>">Add from beach</button>
        </p>
    </div>
</div>
