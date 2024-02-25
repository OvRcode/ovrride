<?php
$nl2br_checked  = ! isset( $this->slide_settings['nl2br'] ) 
                || $this->slide_settings['nl2br'] == 'on' 
                ? true : false;
$posts_with_no_image_checked = isset( $this->slide_settings['posts_with_no_image'] ) 
                && $this->slide_settings['posts_with_no_image'] == 'on' 
                ? true : false;
?>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Slide Link', 'ml-slider-pro' ); ?>
    </label>
    <?php echo $this->get_link_to_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Order By', 'ml-slider-pro' ); ?>
    </label>
    <?php 
    echo $this->get_order_by_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $this->get_order_direction_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    ?>
</div>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Post Limit', 'ml-slider-pro' ); ?>
    </label>
    <?php echo $this->get_limit_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</div>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Preserve New Lines', 'ml-slider-pro' ); ?>
    </label>
    <?php 
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $this->switch_button(
        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][nl2br]',
        (bool) $nl2br_checked
    )
    ?>
</div>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Include posts with no featured image', 'ml-slider-pro' ); ?>
    </label>
    <?php 
    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    echo $this->switch_button(
        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][posts_with_no_image]',
        (bool) $posts_with_no_image_checked
    )
    ?>
</div>
