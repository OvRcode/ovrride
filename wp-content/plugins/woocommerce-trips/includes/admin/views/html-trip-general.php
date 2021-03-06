<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="options_group show_if_trip">
		<p class="form-field">
			<label for="_wc_trip_sku">SKU</label>
			<input type="text" value="<?php echo $sku;?>" name="_wc_trip_sku" id="_wc_trip_sku" >
		</p>
		<p class="form-field">
			<label for="_wc_trip_stock_management">Manage product stock:</label>
			<input type="radio" value="yes" name="_wc_trip_stock_management" id="_wc_trip_stock_management" <?php echo $stock_management_yes; ?>> Yes </input>
			<input type="radio" value="no" name="_wc_trip_stock_management" id="_wc_trip_stock_management" <?php echo $stock_management_no; ?>> No </input>
		</p>
    <p class="form-field">
       <label for="_wc_trip_stock_status">Stock Status</label>
       <select name="_wc_trip_stock_status" id="_wc_trip_stock_status">
           <option value="outofstock" <?php echo ( $stock_status == "outofstock" ? 'selected' : ''); ?>>Out of Stock</option>
           <option value="instock" <?php echo ( $stock_status == "instock" ? 'selected' : ''); ?>>In Stock</option>
       </select>
    </p>
    <p class="form-field">
       <label for="_wc_trip_stock">Stock</label>
       <input type="number" name="_wc_trip_stock" id="_wc_trip_stock" min="0" class="trip_stock" value="<?php echo $stock; ?>">
       </input>
    </p>
    <p class="form-field">
        <label for="_wc_trip_base_price">Base Price ($)</label>
        <input type="text" name="_wc_trip_base_price" id="_wc_trip_base_price"
            placeholder="0.00" value="<?php echo $base_price; ?>">
        </input>
    </p>
    <p class="form-field">
        <label for="_wc_trip_destination">Destination</label>
        <select name="_wc_trip_destination" id="_wc_trip_destination">
            <option value="" default>Select a destination</option>
            <?php
            foreach( $destinations as $destination ) {
                $destination_selected = ($destination->post_title == $saved_destination ? "selected" : "");
                echo sprintf('<option value="%s" %s>%s</option>',$destination->post_title, $destination_selected, $destination->post_title);
            }
            ?>
        </select>
    </p>
    <p class="form-field">
        <label for="_wc_trip_type">Trip type</label>
        <select name="_wc_trip_type">
            <option value="" default>Select trip type</option>
            <?php
            $trip_types = array("bus"                   => "Bus",
																"beach_bus"							=> "Beach Bus",
                                "domestic_flight"       => "Flight: Domestic",
                                "international_flight"  => "Flight: International"
                                );
            foreach( $trip_types as $value => $label ) {
                $trip_selected = ($value == $trip_type ? "selected" : "");

                echo sprintf('<option value="%s" %s>%s</option>', $value, $trip_selected, $label);
            }
            ?>
        </select>
    </p>
		<p class="form-field">
			<label for="_wc_trip_age_check">Age check</label>
			<select id="_wc_trip_age_check" name="_wc_trip_age_check">
				<option value="18" default<?php echo ($age_check == "18" ? ' selected': '');?>>18</option>
				<option value="18+"<?php echo ($age_check == "18+" ? ' selected': '');?>>18+</option>
				<option value="21"<?php echo ($age_check == "21" ? ' selected': '');?>>21</option>
				<option value="21+"<?php echo ($age_check == "21+" ? ' selected': '');?>>21+</option>
			</select>
		</p>
    <p class="form-field">
        <label for="_wc_trip_start_date">Start date</label>
        <input type="text" name="_wc_trip_start_date" id="_wc_trip_start_date" value="<?php echo $start_date; ?>"></input>
    </p>
    <p class="form-field">
       <label for="_wc_trip_end_date">End date</label>
       <input type="text" name="_wc_trip_end_date" id="_wc_trip_end_date" value="<?php echo $end_date; ?>"></input>
    </p>
</div>
