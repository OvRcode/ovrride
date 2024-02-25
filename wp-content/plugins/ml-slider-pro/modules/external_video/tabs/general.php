<?php
$video_url          = get_post_meta( $this->slide->ID, 'ml-slider_video_url', true );
$video_type         = $this->get_video_mime_type( $video_url );

$mute_checked       = isset( $this->slide_settings['mute'] ) && filter_var(
                        $this->slide_settings['mute'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$controls_checked   = ! isset( $this->slide_settings['controls'] ) 
                    || $this->slide_settings['controls'] == 'on' ? true : false;
$autoPlay_checked   = isset( $this->slide_settings['autoPlay'] ) && filter_var(
                        $this->slide_settings['autoPlay'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$lazyLoad_checked   = isset( $this->slide_settings['lazyLoad'] ) && filter_var(
                        $this->slide_settings['lazyLoad'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$loop_checked       = isset( $this->slide_settings['loop'] ) && filter_var(
                        $this->slide_settings['loop'],
                        FILTER_VALIDATE_BOOLEAN
                    ) ? true : false;
$url                = get_post_meta( $this->slide->ID, 'ml-slider_url', true );
$target             = (bool) get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) ? true : false;
?>
<div class='thumb-col-settings'>
	<div class='metaslider-ui-inner metaslider-slide-thumb'>
        <div class="thumb">
            <?php 
            echo $this->get_admin_video_thumb( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
                $video_url, 
                $video_type // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
            );
            ?>
        </div>
	</div>
    <div>
        <input class="url video_url" 
            type="text" 
            name="attachment[<?php esc_attr_e( $this->slide->ID ); ?>][video_url]" 
            placeholder="<?php esc_attr_e( 'Paste the URL of a MP4, WebM, or MOV video', 'ml-slider-pro' ); ?>" 
            value="<?php esc_attr_e( $video_url ); ?>" 
            data-slide-id="<?php esc_attr_e( $this->slide->ID ); ?>"
        />
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
                            <?php _e( 'Mute video', 'ml-slider-pro' ) ?>
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
                            <?php _e( 'Show controls', 'ml-slider-pro' ) ?>
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
                        ?><span>
                            <?php _e( 'Lazy load video', 'ml-slider-pro' ) ?>
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
                            <?php _e( 'Loop video', 'ml-slider-pro' ) ?>
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
