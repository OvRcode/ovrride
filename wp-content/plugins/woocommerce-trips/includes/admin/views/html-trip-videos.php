<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$content = get_post_meta( $post_id, '_wc_trip_videos', true);
$content = ( $content ?: "");
$editorSettings = array('textarea_rows' => 12);
?>
<div id="trips_videos" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group">
        <h1>Videos</h1>
        <p>Content for videos tab on product page</p>
        <?php wp_editor( $content , "_wc_trip_videos", $editorSettings); ?>
    </div>
</div>
