<?php
$video_url          = $this->get_video();
$video_id           = $this->get_video_id();
$video_type         = get_post_mime_type( $video_id );
$video_thumb        = $this->get_admin_video_thumb( $video_url, $video_type );

$mute_checked       = isset( $this->slide_settings['mute'] ) && filter_var(
                        $this->slide_settings['mute'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$controls_checked   = ! isset( $this->slide_settings['controls'] ) 
                    || $this->slide_settings['controls'] == 'on' 
                    ? true : false;
$autoPlay_checked   = isset( $this->slide_settings['autoPlay'] ) && filter_var(
                        $this->slide_settings['autoPlay'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$lazyLoad_checked   = isset( $this->slide_settings['lazyLoad'] ) && filter_var(
                        $this->slide_settings['lazyLoad'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$loop_checked       = isset($this->slide_settings['loop']) && filter_var(
                        $this->slide_settings['loop'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;

$url                = get_post_meta( $this->slide->ID, 'ml-slider_url', true );
$target             = (bool) get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) ? true : false;
?>
<div class="thumb-col-settings">
    <div class="metaslider-ui-inner metaslider-slide-thumb">
        <button class="update-video image-button" data-button-text="<?php 
        esc_attr_e( 'Update slide video', 'ml-slider-pro' ); ?>" title="<?php 
        esc_attr_e( 'Update slide video', 'ml-slider-pro' ); ?>" data-slide-id="<?php 
        echo esc_attr( $this->slide->ID ); ?>" data-attachment-id="<?php 
        echo esc_attr( $video_id ); ?>" data-slide-type="local_video">
            <div class="thumb">
                <?php echo $video_thumb; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </div>
        </button>
    </div>
    <div>
        <div class="row mb-0">
            <div class="flex">
                <button data-slide-type="local_video" data-button-text="<?php 
                esc_attr_e( 'Update slide video', 'ml-slider-pro' ) ?>" data-slide-id="<?php
                echo esc_attr( $this->slide->ID ) ?>" data-attachment-id="<?php
                echo esc_attr( $video_id ) ?>" class="update-video button button-secondary flex-1">
                    <?php esc_html_e( 'Browse', 'ml-slider-pro' ); ?>
                </button>
                <input class="url video_url border-l-0 m-0" type="text" data-attachment-id="<?php 
                echo esc_attr( $video_id ); ?>" placeholder="<?php 
                esc_attr_e( 'Video File', 'ml-slider-pro' ); ?>" value="<?php 
                echo esc_attr( $video_url ); ?>" readonly />
            </div>
        </div>
        <div class="row">
            <ul class="ms-split-li">
                <li>
                    <label><?php
                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                        echo $this->switch_button(
                            'attachment[' . esc_attr( $this->slide->ID ) . '][settings][mute]',
                            (bool) $mute_checked
                        );
                        ?><span>
                            <?php _e( 'Mute video', 'ml-slider-pro' ); ?>
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
                        ?><span>
                            <?php _e( 'Show controls', 'ml-slider-pro' ); ?>
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
                        ?><span>
                            <?php 
                            _e( 'Auto play', 'ml-slider-pro' ); 
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
                            (bool) $lazyLoad_checked
                        );
                        ?> <span>
                            <?php _e( 'Lazy load video', 'ml-slider-pro' ); ?>
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
                        ?><span>
                            <?php _e( 'Loop video', 'ml-slider-pro' ); ?>
                        </span>
                    </label>
                </li>
            </ul>
        </div>
        <div class="row mb-2 adjust-tooltip--1">
            <label>
                <?php 
                esc_html_e( 'Link URL', 'ml-slider' ); 
                
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $this->info_tooltip( __(
                    'This may affect how user interacts with the video controls', 
                    'ml-slider-pro'
                ) );
                ?>
            </label>
        </div>
        <div class="row has-right-checkbox mb-0">
            <div>
                <input class="url" type="text" name="attachment[<?php 
                echo esc_attr( $this->slide->ID ); ?>][url]" placeholder="<?php 
                esc_attr_e( 'URL', 'ml-slider-pro' ); ?>" value="<?php 
                echo esc_attr( $url ); ?>" />
            </div>
            <div class="input-label">
                <label>
                    <?php 
                    esc_html_e( 'New window', 'ml-slider' );
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->info_tooltip( __(
                        'Open link in a new window', 
                        'ml-slider'
                    ) );
                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                    echo $this->switch_button( 
                        'attachment[' . esc_attr( $this->slide->ID ) . '][new_window]', 
                        (bool) $target,
                        array(),
                        'mr-0 ml-2'
                    );
                    ?>
                </label>
            </div>
        </div>
    </div>
</div>