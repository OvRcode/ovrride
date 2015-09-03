<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$content = get_post_meta( $post_id, '_wc_trip_includes', true);
$content = ( $content ?: "Trip Includes product tab content");
$editorSettings = array('textarea_rows' => 12);
?>
<div id="trips_includes" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group">
        <h1>Includes</h1>
        <?php wp_editor( $content , "_wc_trip_includes", $editorSettings);?>
    </div>
</div>