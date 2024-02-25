<?php
$showControls_checked   = ! isset( $this->slide_settings['showControls'] ) 
                        || $this->slide_settings['showControls'] == 'on' ? true : false;
$show_related_checked   = isset( $this->slide_settings['showRelated'] ) 
                        && $this->slide_settings['showRelated'] == 'on' ? true : false;
$auto_play_checked      = isset( $this->slide_settings['autoPlay'] ) 
                        && $this->slide_settings['autoPlay'] == 'on' ? true : false;
$mute_checked           = isset( $this->slide_settings['mute'] ) 
                        && $this->slide_settings['mute'] == 'on' ? true : false;
$lazy_load              = ! isset( $this->slide_settings['lazyLoad'] ) 
                        || $this->slide_settings['lazyLoad'] == 'on' ? true : false;
$video_url              = get_post_meta( $this->slide->ID, 'ml-slider_youtube_url', true );
        
?>
<div class="thumb-col-settings">
    <?php echo $this->get_admin_slide_thumb(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped  ?>
    <div>
        <input type="text" data-lpignore="true" class="url metaslider-pro-youtube_url" name="attachment[<?php 
        echo esc_attr( $this->slide->ID ); ?>][youtube_url]" value="<?php 
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
                       'attachment[' . esc_attr( $this->slide->ID ) . '][settings][showControls]',
                        (bool) $showControls_checked
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
                        (bool) $auto_play_checked
                    );
                    ?> <span>
                        <?php 
                        esc_html_e( 'Auto play', 'ml-slider-pro' ); 
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->info_tooltip( __(
                            'May require video to be muted', 
                            'ml-slider-pro'
                        ) );
                        ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][lazyLoad]',
                        (bool) $lazy_load
                    ); ?> <span>
                        <?php esc_html_e( 'Lazy load video', 'ml-slider-pro' ); ?>
                    </span>
                </label>
            </li>
            <li>
                <label><?php
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button(
                        'attachment[' . esc_attr( $this->slide->ID ) . '][settings][showRelated]',
                        (bool) $show_related_checked
                    );
                    ?> <span>
                        <?php 
                        esc_html_e(
                            'Show related videos', 
                            'ml-slider-pro'
                        ); 
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->info_tooltip( __(
                            'Disabling this may instead show only recommended videos from the channel', 
                            'ml-slider-pro'
                        ) );
                        ?>
                    </span>
                </label>
            </li>
        </ul>
    </div>
</div>
<?php
