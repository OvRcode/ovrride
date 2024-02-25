<?php
$byline_checked = isset( $this->slide_settings['byline'] ) && filter_var(
    $this->slide_settings['byline'],
    FILTER_VALIDATE_BOOLEAN
) ? true : false;
$portrait_checked = isset( $this->slide_settings['portrait'] ) && filter_var(
    $this->slide_settings['portrait'],
    FILTER_VALIDATE_BOOLEAN
) ? true : false;
$title_checked = isset( $this->slide_settings['title'] ) && filter_var(
    $this->slide_settings['title'],
    FILTER_VALIDATE_BOOLEAN
) ? true : false;
$autoPlay_checked = isset( $this->slide_settings['autoPlay'] ) && filter_var(
    $this->slide_settings['autoPlay'],
    FILTER_VALIDATE_BOOLEAN
) ? true : false;
$mute_checked = isset( $this->slide_settings['mute'] ) && filter_var(
    $this->slide_settings['mute'],
    FILTER_VALIDATE_BOOLEAN
) ? true : false;
$loop_checked = isset( $this->slide_settings['loop'] ) && filter_var(
    $this->slide_settings['loop'],
    FILTER_VALIDATE_BOOLEAN
) ? true : false;
$controls_checked = ! isset( $this->slide_settings['controls'] ) 
    || $this->slide_settings['controls'] == 'on' 
    ? true : false;
$video_url = get_post_meta( $this->slide->ID, 'ml-slider_vimeo_url', true );
?>
<div class="thumb-col-settings">
    <?php echo $this->get_admin_slide_thumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    <div>
        <input type="text" class="url metaslider-pro-vimeo_url" name="attachment[<?php 
        echo esc_attr( $this->slide->ID ); ?>][vimeo_url]" value="<?php 
        echo esc_attr( $video_url ); ?>" data-slide-id="<?php 
        echo esc_attr( $this->slide->ID ); ?>">
        <ul class="ms-split-li">
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][mute]',
                        (bool) $mute_checked
                    );
                    ?> <span>
                        <?php esc_html_e( 'Mute video', 'ml-slider-pro' ); ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][controls]',
                        (bool) $controls_checked
                    );
                    ?> <span>
                        <?php esc_html_e( 'Show controls', 'ml-slider-pro' ); ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][autoPlay]',
                        (bool) $autoPlay_checked
                    );
                    ?> <span>
                        <?php 
                        esc_html_e( 'Auto play', 'ml-slider-pro' ); 
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->info_tooltip( __( 'May require video to be muted', 'ml-slider-pro' ) );
                        ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][title]',
                        (bool) $title_checked
                    );
                    ?> <span>
                        <?php 
                        esc_html_e( 'Show title', 'ml-slider-pro' ); 
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->info_tooltip( __( 'Show the title if available', 'ml-slider-pro' ) );
                        ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][byline]',
                        (bool) $byline_checked
                    );
                    ?> <span>
                        <?php 
                        esc_html_e( 'Show user byline', 'ml-slider-pro' ); 
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->info_tooltip( __( 'Show the user byline if available', 'ml-slider-pro' ) );
                        ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][portrait]',
                        (bool) $portrait_checked
                    );
                    ?>  <span>
                        <?php 
                        esc_html_e( 'Show user portrait', 'ml-slider-pro' ); 
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->info_tooltip( __( 'Show the user portrait if available', 'ml-slider-pro' ) );
                        ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][loop]',
                        (bool) $loop_checked
                    );
                    ?> <span>
                        <?php esc_html_e( 'Loop video', 'ml-slider-pro' ); ?>
                    </span>
                </label>
            </li>
        </ul>
    </div>
</div>
<?php
