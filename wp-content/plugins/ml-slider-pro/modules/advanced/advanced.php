<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Adds advanced tab to slides
 */
class MetaSliderAdvancedSettings
{
    /**
     * Construct
     */
    public function __construct()
    {
        add_action( 'metaslider_register_admin_scripts', array( $this, 'load_scripts' ) );
        add_action( 'init', array( $this, 'advanced_settings' ), 20 );
    }

    /**
     * Load JS in admin
     * 
     * @since 2.33
     */
    public function load_scripts()
    {
        wp_enqueue_script(
            'metasliderpro-advanced-settings-script',
            plugins_url( 'assets/advanced.js', __FILE__ ),
            array( 'jquery' ),
            METASLIDERPRO_VERSION
        );
    }

    /**
     * Advanced settings - Adds actions + filters
     * 
     * @since 2.33
     * 
     * @return void
     */
    public function advanced_settings()
    {
        add_filter( 'metaslider_slide_tabs', array( $this, 'slide_admin_tab' ), 10, 4 );
        add_action( 'metaslider_save_slide', array( $this, 'save_settings' ), 10, 3 );
    }

    /**
     * Add admin Tab
     *
     * @since 2.33
     * 
     * @param array $tabs Exising tabs
     * @param object $slide Slide
     * @param object $slider Slider
     * @param array $settings Slider Settings
     * 
     * @return array
     */
    public function slide_admin_tab( $tabs, $slide, $slider, $settings )
    {
        $type = get_post_meta( $slide->ID, 'ml-slider_type', true );

        // Exclude Post Feed slides
        if ( $type != 'post_feed' ) {
            ob_start();
            $this->slide_admin_tab_controls( $slide );
            $content = ob_get_contents();
            ob_end_clean();
    
            $tabs['advanced'] = array(
                'title' => __( 'Advanced', 'ml-slider-pro' ),
                'content' => $content
            );
        }
        
        return $tabs;
    }

    /**
     * Renders the Advanced settings tab Controls
     *
     * @since 2.33
     * 
     * @param object $post Slide post object
     * 
     * @return void
     */
    public function slide_admin_tab_controls( $post )
    {
        $is_delayed = $this->option_is_enabled( get_post_meta( $post->ID, '_meta_slider_slide_is_delayed', true ) );
        $delay_time = get_post_meta( $post->ID, '_meta_slider_slide_delayed_time', true );

        $path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'tabs/';
        include $path . 'advanced-tab.php';
    }

    /**
     * Check if a setting is enabled or not
     * 
     * @since 2.33
     * 
     * @param mixed $value
     * 
     * @return bool
     */
    private function option_is_enabled($value)
    {
        return $value === 'yes' || $value === 'on' || $value === true || $value == 1;
    }

    /**
     * Saving the new settings
     *
     * @since 2.33
     * 
     * @param int $slide_id Slide ID
     * @param int $slider_id Slider ID
     * @param array $fields Fields saved
     * 
     * @return void
     */
    public function save_settings( $slide_id, $slider_id, $fields )
    {
        update_post_meta(
            $slide_id,
            '_meta_slider_slide_is_delayed',
            isset( $fields['delay'] ) && sanitize_text_field( $fields['delay'] ) === 'on'
        );

        if ( isset( $fields['delay'] ) ) {
            $delay_time = isset( $fields['delay_time'] ) 
                        && ! empty( $fields['delay_time'] ) 
                        ? (int) $fields['delay_time'] : '';
            
            update_post_meta(
                $slide_id,
                '_meta_slider_slide_delayed_time',
                $delay_time
            );
        }
    }

    /**
     * Add optional custom delay per slide in frontend
     * 
     * @since 2.33
     * 
     * @param array   $options      Slideshow options
     * @param array $settings       Slideshow settings
     * @param array $slides         List of slides
     * 
     * @return array $options
     */
    public function build_custom_delay_js( $options, $settings, $slides )
    {
        if ( filter_var( $settings['autoPlay'], FILTER_VALIDATE_BOOLEAN ) ) {

            $js_start   = '';
            $js_after   = '';
            $count_     = 0; // Counter for all the slides, despite if have or not custom delay

            $js_start_count_ = 0; // Count added slides to $js_start
            $js_after_count_ = 0; // Count added slides to $js_after

            foreach ( $slides as $slide ) {
                $is_delayed = (bool) get_post_meta( $slide->ID, '_meta_slider_slide_is_delayed', true );
                $delay_time = (int) get_post_meta( $slide->ID, '_meta_slider_slide_delayed_time', true );
                
                $is_autoplay_video = $this->is_autoplay_video( $slide->ID );

                if ( $is_delayed && $delay_time > 0 && ! $is_autoplay_video ) {
                    if ( $count_ === 0 ) {
                        // First slide is added top Flexslider's 'start' option
                        $js_start .= "if (slider.currentSlide == {$count_}) {
                            slider.pause();
                            setTimeout(function(){
                                slider.play();
                                slider.flexAnimate(slider.getTarget('next'));
                            }, {$delay_time});
                        }
                        console.log('!!!', {$count_}, {$delay_time});";

                        $js_start_count_++;
                    } else {
                        // Starting from second slide are added top Flexslider's 'after' option
                        $js_after .= "case {$count_}:
                            slider.pause();
                            setTimeout(function(){
                                slider.play();
                                slider.flexAnimate(slider.getTarget('next'));
                            }, {$delay_time});
                            console.log('!!!', {$count_}, {$delay_time});
                        break;";

                        $js_after_count_++;
                    }
                    
                }

                $count_++;
            };

            $options['start'] = isset( $options['start'] ) ? $options['start'] : array();
            $options['after'] = isset( $options['after'] ) ? $options['after'] : array();

            // First slide
            if ( $js_start_count_ > 0 ) {
                $options['start'] = array_merge( $options['start'], array(
                    "{$js_start}"
                ));

                /* We add $js_start here too
                 * because after a complete slides loop, 
                 * the delay should be triggered through 'after' too 
                 * because 'start' code doesn't apply after the first complete loop */
                $options['after'] = array_merge( $options['after'], array(
                    "{$js_start}"
                ));
            }

            // The rest of slides
            if ( $js_after_count_ > 0 ) {
                $options['after'] = array_merge( $options['after'], array(
                    "switch (slider.currentSlide) {
                        {$js_after}
                    }"
                ));
            }
        }

        return $options;
    }

    /**
     * Don't apply the custom delay to videos with autoplay.
     * Return true if autoplay is enabled.
     * 
     * @since 2.33
     * 
     * @param int $id The slide ID
     * 
     * @return bool
     */
    public function is_autoplay_video( $id )
    {
        $video_types = array(
            'youtube',
            'vimeo',
            'local_video',
            'external_video'
        );
        $type = get_post_meta( $id, 'ml-slider_type', true );
        
        if ( in_array( $type, $video_types ) ) {
            $settings = get_post_meta( $id, 'ml-slider_settings', true );

            return isset( $settings['autoPlay'] ) && filter_var( 
                    $settings['autoPlay'], FILTER_VALIDATE_BOOLEAN 
                ) ? true : false;
        }

        return false;
    }
}