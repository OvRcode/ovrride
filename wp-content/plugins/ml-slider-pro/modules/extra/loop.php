<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Extra - Loop options. For Flex & Nivo Slider.
 */
class MetaSliderLoop {

    /**
     * Register slide type
     */
    public function __construct() {

        add_filter( 'metaslider_advanced_settings', array( $this, 'add_setting' ), 11, 2 );
        add_filter( 'metaslider_flex_slider_parameters', array($this, 'metaslider_flex_loop'), 99, 3 );
        add_filter( 'metaslider_nivo_slider_parameters', array($this, 'metaslider_nivo_loop'), 99, 3 );

    }

    /**
     * Add JavaScript to stop slideshow
     */
    public function metaslider_flex_loop($options, $slider_id, $settings) {

        if ( isset($settings['loop']) && $settings['loop'] == 'stopOnFirst') {
            $options['after'][] = "    if (slider.currentSlide == 0) { slider.pause(); }" ;
        }

        if ( isset($settings['loop']) && $settings['loop'] == 'stopOnLast') {
            $options['animationLoop'] = "false";
        }

        return $options;
    }

    /**
     * Add JavaScript to stop slideshow
     */
    public function metaslider_nivo_loop($options, $slider_id, $settings) {

        if ( isset($settings['loop']) && $settings['loop'] == 'stopOnFirst') {
            $options['slideshowEnd'][] = "$('#metaslider_{$slider_id}').data('nivoslider').stop();" ;
        }

        if ( isset($settings['loop']) && $settings['loop'] == 'stopOnLast') {
            $options['lastSlide'][] = "$('#metaslider_{$slider_id}').data('nivoslider').stop();";
        }

        return $options;
    }


    /**
     * Add setting to Advanced Settings
     */
    public function add_setting( $fields, $slider ) {

        $fields['loop'] = array(
            'priority' => 25,
            'type' => 'select',
            'label' => __( "Loop", "metasliderpro" ),
            'class' => 'option flex nivo',
            'helptext' => __( "Configure loop", "metasliderpro" ),
            'value' => $slider->get_setting( 'loop' ),
            'options' => array(
                'continuously' => array( 'label' => __( "Continuously", "metasliderpro" ), 'class' => '' ),
                'stopOnLast' => array( 'label' => __( "Stop on Last Slide", "metasliderpro" ), 'class' => '' ),
                'stopOnFirst' => array( 'label' => __( "Stop on First Slide", "metasliderpro" ), 'class' => '' ),
            )
        );

        return $fields;
    }

}