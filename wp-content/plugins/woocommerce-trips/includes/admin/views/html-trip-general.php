<?php
$destinations = array("MT Snow","Killington");
?>
<div class="options_group show_if_trip">
    <p class="form-field">
        <label for="_wc_trip_base_price">Base Price ($)</label>
        <input type="text" name="_wc_trip_base_price" id="_wc_trip_base_price" placeholder="0.00"></input>
    </p>
    <p class="form-field">
        <label for="_wc_trip_destination">Destination</label>
        <select name="_wc_trip_destination" id="_wc_trip_destination">
            <option value="">Select a destination</option>
            <?php
            foreach( $destinations as $destination ) {
                echo sprintf('<option value="%s">%s</option>',$num, $destination);
            }
            ?>
        </select>
    </p>
    <p class="form-field">
        <label for="_wc_trip_type">Trip type</label>
        <select name="_wc_trip_type">
            <option value="" default>Select transit type</option>
            <option value="bus">Bus</option>
            <option value="domestic_flight">Flight: Domestic</option>
            <option value="international_flight">Flight: International</option>
        </select>
    </p>
    <p class="form-field">
        <label for="_wc_trip_start_date">Start date</label>
        <input type="text" name="_wc_trip_start_date" id="_wc_trip_start_date"></input>
    </p>
    <p class="form-field">
       <label for="_wc_trip_end_date">End date</label>
       <input type="text" name="_wc_trip_end_date" id="_wc_trip_end_date"></input> 
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