<?php
$title_ = get_post_meta( $this->slide->ID, 'ml-slider_title', true );
$alt    = get_post_meta( $this->slide->ID, 'ml-slider_alt', true );
?>
<div class="row mb-2">
    <label>
        <?php esc_html_e( 'Image Title Text', 'ml-slider-pro' ); ?>
    </label>
</div>
<div class="row">
    <input type="text" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][title]" placeholder="<?php
    esc_attr_e( 'Title Text', 'ml-slider-pro' ); ?>" value="<?php 
    echo esc_attr( $title_ ); ?>" />
</div>
<div class="row mb-2">
    <label>
        <?php esc_html_e( 'Image Alt Text', 'ml-slider-pro' ); ?>
    </label>
</div>
<div class="row">
    <input type="text" name="attachment[<?php 
    echo esc_attr( $this->slide->ID ); ?>][alt]" placeholder="<?php 
    esc_attr_e( 'Alt Text', 'ml-slider-pro' ); ?>" value="<?php 
    echo esc_attr( $alt ); ?>" />
</div>
