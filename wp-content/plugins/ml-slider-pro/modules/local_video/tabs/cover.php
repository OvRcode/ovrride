<?php
$cover_id   = $this->get_attachment_id();
$thumb      = $this->get_intermediate_image_src( 240 );
?>

<button class="update-cover-image"
    data-button-text="<?php esc_attr_e( 'Update cover image', 'ml-slider' ); ?>"
    data-slide-id="<?php esc_attr_e( $this->slide->ID ); ?>"
    data-slide-type="local_video"
    data-attachment-id="<?php esc_attr_e( $cover_id ); ?>"
    <?php if ( ! empty( $thumb ) ) : ?>
        title="<?php esc_attr_e( 'Update cover image', 'ml-slider' ); ?>"
        style="background-image: url(<?php echo esc_url( $thumb ); ?>)"
    <?php endif; ?>
>
    <?php if ( empty( $thumb ) ) : ?>
        <?php esc_html_e( 'Set cover image', 'ml-slider-pro' ); ?>
    <?php endif; ?>
</button>
