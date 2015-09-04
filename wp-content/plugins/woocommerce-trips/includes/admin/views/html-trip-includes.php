<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$content = get_post_meta( $post_id, '_wc_trip_includes', true);
$content = ( $content ?: "");
$editorSettings = array('textarea_rows' => 12);
?>
<div id="trips_includes" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group">
        <h1>Includes</h1>
        <p>Content for includes tab on product page</p>
        <?php 
        remove_filter('the_content', 'do_shortcode',11 );
        wp_editor( $content , "_wc_trip_includes", $editorSettings);
        ?>
    </div>
</div>