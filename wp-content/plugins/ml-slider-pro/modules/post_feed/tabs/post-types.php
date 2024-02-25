<p><?php esc_html_e(
    'Select the Post types to include in the feed.',
    'ml-slider-pro'
); ?></p>
<?php echo $this->get_post_type_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>