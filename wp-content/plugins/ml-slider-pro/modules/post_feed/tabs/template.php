<?php
$custom_template = isset( $this->slide_settings['custom_template'] ) && strlen(
    $this->slide_settings['custom_template']
) ? $this->slide_settings['custom_template'] : $this->backwards_compatible_caption();
?>
<div class="thumb-col-settings">
    <div class="metaslider-ui-inner metaslider-slide-thumb">
        <div class="thumb post_feed"></div>
    </div>
    <div>
        <div style="position: relative; margin-bottom: 10px;">
            <?php echo $this->get_template_tags(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <div class="row last">
            <textarea class="wysiwyg" id="editor<?php 
            echo esc_attr( $this->slide->ID ); ?>" name="attachment[<?php 
            echo esc_attr( $this->slide->ID ); ?>][settings][custom_template]"><?php 
            echo $custom_template; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></textarea>
        </div>
    </div>
</div>