<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$content = get_post_meta( $post_id, "_wc_trip_$type", true);
$content = ( $content ?: "");
$editorSettings = array('textarea_rows' => 12);
$title = strtolower($type);
?>
<div id="trips_<?php echo $type;?>" class="woocommerce_options_panel panel wc-metaboxes-wrapper">
    <div class="options_group">
        <h1><?php echo ucfirst($title); ?></h1>
        <p>Content for <?php echo $title; ?> tab on product page</p>
        <?php wp_editor( $content , "_wc_trip_{$type}", $editorSettings); ?>
    </div>
</div>
