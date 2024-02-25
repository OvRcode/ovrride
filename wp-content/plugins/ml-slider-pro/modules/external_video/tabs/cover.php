<?php
$cover_id   = $this->get_attachment_id();
$thumb      = $this->get_intermediate_image_src( 240 );
?>
<button class="update-cover-image" 
    data-button-text="<?php esc_attr_e( 'Update cover image', 'ml-slider-pro' ) ?>" 
    data-slide-id="<?php esc_attr_e( $this->slide->ID ) ?>" 
    data-slide-type="external_video" 
    data-attachment-id="<?php esc_attr_e( $cover_id ) ?>" 
    title="<?php esc_attr_e( 'Update cover image', 'ml-slider-pro' ) ?>"<?php 
    if( ! empty( $thumb ) ) { 
        ?> style="background-image: url(<?php echo esc_url( $thumb ) ?>);"<?php 
    } 
    ?>
>
    <?php 
    // Hide text if cover image is set
    if( empty( $thumb ) ) {
        esc_html_e( 'Set cover image', 'ml-slider-pro' );
    }
    ?>
</button>