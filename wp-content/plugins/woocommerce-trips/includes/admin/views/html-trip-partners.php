<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$content = get_post_meta( $post_id, '_wc_trip_partners', true);
$content = ( $content ?: "");
$editorSettings = array('textarea_rows' => 12);
?>
<div id="trips_partners" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group">
        <h1>Beach Bus Partners</h1>
        <p>Content for partners tab on product page</p>
        <?php wp_editor( $content , "_wc_trip_partners", $editorSettings); ?>
    </div>
</div>
