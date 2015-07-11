<?php
// TODO: Add code to pull destinations when they are setup
$destinations       = array("MT Snow","Killington");
$stock              = get_post_meta( $post_id, '_wc_trip_stock', true );
$base_price         = get_post_meta( $post_id, '_wc_trip_base_price', true );
$saved_destination  = get_post_meta( $post_id, '_wc_trip_destination', true );
$trip_type          = get_post_meta( $post_id, '_wc_trip_type', true );
$start_date         = get_post_meta( $post_id, '_wc_trip_start_date', true );
$end_date           = get_post_meta( $post_id, '_wc_trip_end_date', true );
?>
<div class="options_group show_if_trip">
    <p class="form-field">
       <label for="_wc_trip_stock">Stock</label>
       <!-- TODO: Move style to external CSS -->
       <input type="number" name="_wc_trip_stock" id="_wc_trip_stock" min="0" style="width: 6em;" value="<?php echo $stock; ?>">
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
                $destination_selected = ($destination == $saved_destination ? "selected" : "");
                echo sprintf('<option value="%s" %s>%s</option>',$destination, $destination_selected, $destination);
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
        <label for="_wc_trip_start_date">Start date</label>
        <input type="text" name="_wc_trip_start_date" id="_wc_trip_start_date" value="<?php echo $start_date; ?>"></input>
    </p>
    <p class="form-field">
       <label for="_wc_trip_end_date">End date</label>
       <input type="text" name="_wc_trip_end_date" id="_wc_trip_end_date" value="<?php echo $end_date; ?>"></input> 
    </p>
</div>
<script>
// TODO: Factor this into external JS
jQuery(function(){
    jQuery( "#_wc_trip_start_date, #_wc_trip_end_date" ).datepicker({
        changeMonth: true,
        changeYear: true
    });
    jQuery("#_wc_trip_base_price").change(function() {
        var valid = /^\d{0,4}(\.\d{0,2})?$/.test(this.value),
        val = this.value;
    
    if(!valid){
        alert("Please enter a valid price");
    }
    });
    jQuery( "#_wc_trip_end_date").change(function() {
        var start = jQuery("#_wc_trip_start_date");
        var end = jQuery(this);
        if ( end.val() < start.val() ) {
            end.val("");
            end.focus();
            alert("Please set an end date that is greater than or equal to the start date");
        }
    });
});
</script>