<?php
$webm   = get_post_meta( $this->slide->ID, 'ml-slider_webm', true );
$mp4    = get_post_meta( $this->slide->ID, 'ml-slider_mp4', true );
?>
<div class="row mb-2">
    <label>
        <?php esc_html_e( 'MP4 Source', 'ml-slider-pro' ); ?>
    </label>
</div>
<div class="row">
    <input type="text" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][mp4]" placeholder="<?php 
    esc_attr_e( 'URL', 'ml-slider-pro' ); ?>" value="<?php 
    echo esc_attr( $mp4 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
</div>
<div class="row mb-2">
    <label>
        <?php esc_html_e( 'WebM Source', 'ml-slider-pro' ); ?>
    </label>
</div>
<div class="row">
    <input type="text" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][webm]" placeholder="<?php 
    esc_attr_e( 'URL', 'ml-slider-pro' ); ?>" value="<?php 
    echo esc_attr( $webm ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" />
</div>
