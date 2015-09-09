<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce_trip_pickup_location wc-metabox closed">
    <h3>
        <button type="button" class="remove_trip_pickup_location button" rel="<?php echo esc_attr( absint( $location_id ) );?>">Remove</button>
        <a href="<?php echo admin_url( "post.php?post=" . $location_id . "&action=edit" );?>" target="_blank" class="edit_pickup_location"> Edit Pickup Location &rarr;</a>
        
        <strong><?php echo $location->post_title . $location_time ?></strong>
        
        <input type="hidden" name="pickup_location_id[<?php echo $count; ?>]" value="<?php echo esc_attr( $location_id); ?>" />
        <input type="hidden" class="pickup_location_menu_order" name="pickup_location_menu_order[<?php echo $count; ?>]" value="<?php echo $count; ?>" />
    </h3>
</div>