<?php
$light_theme_selected   = isset( $this->slide_settings['theme'] ) 
                        && $this->slide_settings['theme'] == 'light' ? 'selected' : '';
$white_color_selected   = isset( $this->slide_settings['color'] ) 
                        && $this->slide_settings['color'] == 'white' ? 'selected' : '';        
?>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Theme', 'ml-slider-pro' ); ?>
    </label>
    <select name="attachment[<?php echo esc_attr( $this->slide->ID ); ?>][settings][theme]">
        <option value="dark">
            <?php esc_html_e( 'Dark', 'ml-slider-pro' ); ?>
        </option>
        <option value="light" <?php 
        echo $light_theme_selected; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
            <?php esc_html_e( 'Light', 'ml-slider-pro' ); ?>
        </option>
    </select>
</div>
<div class="row has-right-field">
    <label>
        <?php esc_html_e( 'Color', 'ml-slider-pro' ); ?>
    </label>
    <select name="attachment[<?php echo esc_attr( $this->slide->ID ); ?>][settings][color]">
        <option value="red">
            <?php esc_html_e( 'Red', 'ml-slider-pro' ); ?>
        </option>
        <option value="white" <?php 
        echo $white_color_selected; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
            <?php esc_html_e( 'White', 'ml-slider-pro' ); ?>
        </option>
    </select>
</div>
<?php
