<p><?php esc_html_e(
    'Posts must be tagged to at least one of the selected categories to display in the feed.',
    'ml-slider-pro'
); ?></p>
<?php echo $this->get_tag_options(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
