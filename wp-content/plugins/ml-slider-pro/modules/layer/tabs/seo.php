<?php
$title_ = get_post_meta( $this->slide->ID, 'ml-slider_title', true );
$alt    = get_post_meta( $this->slide->ID, '_wp_attachment_image_alt', true );
?>
<div class="row mb-2">
    <label>
        <?php esc_html_e( 'Background Image Title Text', 'ml-slider-pro' ); ?>
    </label>
</div>
<div class="row">
    <input type="text" size="50" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][title]" value="<?php 
    echo esc_attr( $title_ ); ?>" />
</div>
<div class="row mb-2">
    <label>
        <?php esc_html_e( 'Background Image Alt Text', 'ml-slider-pro' ); ?>
    </label>
</div>
<div class="row">
    <input type="text" size="50" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][alt]" value="<?php 
    echo esc_attr( $alt ); ?>" />
</div>
