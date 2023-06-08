<?php

if (! defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Extra - Loop options. For Flex & Nivo Slider.
 */
class MetaSliderLoop
{

    /**
     * Register slide type
     */
    public function __construct()
    {
        add_filter('metaslider_advanced_settings', array($this, 'add_setting'), 11, 2);
        add_filter('metaslider_flex_slider_parameters', array($this, 'metaslider_flex_loop'), 99, 3);
        add_filter('metaslider_nivo_slider_parameters', array($this, 'metaslider_nivo_loop'), 99, 3);
    }

    /**
     * Add JavaScript to stop slideshow
     *
     * @param array $options SLide options
     * @param integer $slider_id Slider ID
     * @param array $settings Slide settings
     * @return array
     */
    public function metaslider_flex_loop($options, $slider_id, $settings)
    {
        if (isset($settings['loop']) && 'stopOnFirst' === $settings['loop']) {
            $options['after'] = isset($options['after']) ? $options['after'] : array();
            $options['after'] = array_merge(
                $options['after'],
                array("if (slider.currentSlide == 0) { slider.pause(); }")
            );
        }

        if (isset($settings['loop']) && 'stopOnLast' === $settings['loop']) {
            $options['animationLoop'] = "false";
        }

        return $options;
    }

    /**
     * Add JavaScript to stop slideshow
     *
     * @param array $options SLide options
     * @param integer $slider_id Slider ID
     * @param array $settings Slide settings
     * @return array
     */
    public function metaslider_nivo_loop($options, $slider_id, $settings)
    {
        if (isset($settings['loop']) && 'stopOnFirst' === $settings['loop']) {
            $options['slideshowEnd'] = isset($options['slideshowEnd']) ? $options['slideshowEnd'] : array();
            $options['slideshowEnd'] = array_merge($options['slideshowEnd'], array(
                "$('#metaslider_" . esc_js($slider_id) . "').data('nivoslider').stop();"
            ));
        }

        if (isset($settings['loop']) && 'stopOnLast' === $settings['loop']) {
            $options['lastSlide'] = isset($options['lastSlide']) ? $options['lastSlide'] : array();
            $options['lastSlide'] = array_merge(
                $options['lastSlide'],
                array("$('#metaslider_" . esc_js($slider_id) . "').data('nivoslider').stop();")
            );
        }

        return $options;
    }

    /**
     * Add setting to Advanced Settings
     *
     * @param array $fields Settings Fields
     * @param array $slider Slider details
     */
    public function add_setting($fields, $slider)
    {
        $fields['loop'] = array(
            'priority' => 25,
            'type' => 'select',
            'label' => __("Loop", "ml-slider-pro"),
            'class' => 'option flex nivo',
            'helptext' => __("Configure loop", "ml-slider-pro"),
            'value' => $slider->get_setting('loop'),
            'options' => array(
                'continuously' => array('label' => __("Continuously", "ml-slider-pro"), 'class' => ''),
                'stopOnLast' => array('label' => __("Stop on Last Slide", "ml-slider-pro"), 'class' => ''),
                'stopOnFirst' => array('label' => __("Stop on First Slide", "ml-slider-pro"), 'class' => ''),
            )
        );

        return $fields;
    }

}
