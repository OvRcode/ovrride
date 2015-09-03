<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$content = get_post_meta( $post_id, '_wc_trip_rates', true);
$content = ( $content ?: "");
$editorSettings = array('textarea_rows' => 12);
?>
<div id="trips_rates" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group">
        <h1>Rates</h1>
        <p>Content for rates tab on product page</p>
        <?php wp_editor( $content , "_wc_trip_rates", $editorSettings);?>
    </div>
</div>