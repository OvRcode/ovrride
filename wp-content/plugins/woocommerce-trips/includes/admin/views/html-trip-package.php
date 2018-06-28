<div id="trips_<?php echo $type;?>_packages" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group" id="<?php echo $type; ?>-packages-options">
       <?php woocommerce_wp_checkbox( array( 'id' => "_wc_trip_{$type}_package_stock", 'label' => 'Enable package stock', 'description' => "Enable this option to manage stock on some or all $type packages", 'desc_tip' => true, 'value' => get_post_meta( $post_id, "_wc_trip_{$type}_package_stock", true ) ) ); ?>
       <p>Stock on packages is limited by the stock of the product. Leaving a stock field blank will not impose an addition restriction on the package</p>
    </div>
    <div class="options_group" id="<?php echo $type; ?>-packages-optional">
       <?php
        $optional = get_post_meta( $post_id, "_wc_trip_{$type}_package_optional", true);
       ?>
        Optional Package <input type="checkbox" id="_wc_trip_<?php echo $type; ?>_package_optional" name="_wc_trip_<?php echo $type; ?>_package_optional" value="yes" <?php echo $optional; ?>>
       <p>Optional Packages will have a selectable default and require no change from the guest when booking.</p>
    </div>
    <div class="options_group" id="<?php echo $type; ?>-packages">
        <?php woocommerce_wp_text_input( array( 'id' => "_wc_trip_{$type}_package_label", 'label' => ucwords($type) . " Package label", 'description' => 'Label to be shown on product page', 'desc_tip' => false, 'value' => get_post_meta( $post_id, "_wc_trip_{$type}_package_label", true ) ) );?>
        <div class="toolbar">
            <h3><?php echo ucwords($type);?> Packages</h3>
            <br />
        </div>

        <div class="woocommerce_trip_<?php echo $type; ?>_packages wc-metaboxes">
            <table class="woocommerce_trip_<?php echo $type; ?>_packages">
                <thead>
                        <th class="sorting">&nbsp;</th>
                        <th class="description">Description</th>
                        <th class="cost">Cost ($)</th>
                        <th class="<?php echo $type; ?>_package_stock">Stock</th>
                        <th class="<?php echo $type; ?>_default">Default</th>
                        <th class="delete_column">&nbsp;</th>
                </thead>
                <tbody id="<?php echo $type; ?>_package_rows">
            <?php
                $package = get_post_meta($post_id, "_wc_trip_".$type."_packages", true);

                if (is_array($package) || is_object($package)) {
                  $package_count = 0;
                    foreach ( $package as $key => $values ) {
                      if ( "yes" == $values["default"] ) {
                        $default = "checked";
                      } else {
                        $default = "";
                      }
                        echo <<< PRIMARYROW
                        <tr>
                            <td class='sorter sorting'>&nbsp;</td>
                            <td class="package_description">
                                <input type='text' name='wc_trips_{$type}_package_description[]' value='{$values['description']}' />
                            </td>
                            <td class="cost">
                                <input type='text' name='wc_trips_{$type}_package_cost[]' value='{$values['cost']}'>
                                </input>
                            </td>
                            <td class='{$type}_package_stock stock'>
                                <input type='number' name='wc_trips_{$type}_package_stock[]' value='{$values['stock']}'>
                                </input>
                            </td>
                            <td class='{$type}_default'>
                                <input type='radio' name='wc_trips_{$type}_package_default'  value='{$package_count}' {$default}>
                            </td>
                            <td class='deleteButton'>&nbsp;</td>
                        </tr>
PRIMARYROW;
                      $package_count++;
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
            <button type="button" class="button button-<?php echo $type; ?> add_package" id="<?php echo $type; ?>_package_add" data-row="<tr>
                <td class='sorter sorting'>&nbsp;</td>
                <td class='package_description'><input type='text' name='wc_trips_<?php echo $type; ?>_package_description[]'></input></td>
                <td class='cost'><input type='text' name='wc_trips_<?php echo $type; ?>_package_cost[]'></input></td>
                <td class='<?php echo $type; ?>_package_stock stock'><input type='number' name='wc_trips_<?php echo $type; ?>_package_stock[]'></input></td>
                <td class='deleteButton'>&nbsp;</td>
            </tr>">Add <?php echo $type; ?> package</button>
        </p>
    </div>
</div>
