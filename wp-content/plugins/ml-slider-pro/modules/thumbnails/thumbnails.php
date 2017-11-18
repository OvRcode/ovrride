<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Thumbnail Navigation addon
 */
class MetaSliderThumbnails {

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'metaslider_nivo_slider_parameters', array( $this, 'nivo_enable_thumbnails' ), 10, 3 );
        add_filter( 'metaslider_flex_slider_parameters', array( $this, 'flex_enable_thumbnails' ), 10, 3 );

        // standard thumbnail output
        add_filter( 'metaslider_image_slide_attributes', array( $this, 'generate_thumbnail_for_slide' ), 10, 3 );
        add_filter( 'metaslider_flex_slider_li_attributes', array( $this, 'add_flex_data_thumb_attribute' ), 10, 4 );

        add_filter( 'metaslider_nivo_slider_image_attributes', array( $this, 'add_nivo_data_thumb_attribute' ), 10, 3 );

        // add the navigation options to slideshow settings
        add_filter( 'metaslider_basic_settings', array( $this, 'navigation_options' ), 10, 2 );

        // filmstrip output
        add_filter( 'metaslider_flex_slider_javascript_before', array( $this, 'metaslider_flex_filmstrip' ), 11, 2 );
        add_filter( 'metaslider_flex_slider_html_after', array( $this, 'metaslider_flex_filmstrip_html' ), 10, 3 );
        add_filter( 'metaslider_css_classes', array( $this, 'remove_bottom_margin' ), 11, 3 );
        add_filter( 'metaslider_css', array( $this, 'get_filmstrip_css' ), 11, 3 );

        // added in Meta Slider 2.10 (image crop positions)
        add_action( 'metaslider_ajax_resize_image_slide', array( $this, 'ajax_create_thumbnail'), 10, 3 );
    }


    /**
     * Create the slide thumbnail image.
     */
    public function ajax_create_thumbnail( $slide_id, $slider_id, $settings ) {

        // create a copy of the correct sized image
        $imageHelper = new MetaSliderImageHelper(
            $slide_id,
            $settings['thumb_width'],
            $settings['thumb_height'],
            'true'
        );

        $url = $imageHelper->get_image_url( true );

        echo "\n" . $url . " (" . $settings['thumb_width'] . 'x' . $settings['thumb_height'] . ") (thumb)";

        wp_die();
    }

    /**
     * Output the carousel HTML for the filmstrip
     *
     * @param string  $html
     * @param integer $slider_id
     * @param array   $settings
     * @return string $html
     */
    public function metaslider_flex_filmstrip_html( $html, $slider_id, $settings ) {
        if ( isset( $settings["navigation"] ) && $settings['navigation'] == 'filmstrip' ) {
            $slider = new MetaSlider( $slider_id, array() );
            $query = $slider->get_slides();

            if ( isset( $settings["noConflict"] ) && $settings['noConflict'] == 'true' ) {
                $class = 'filmstrip';
            } else {
                $class = 'flexslider filmstrip';
            }

            $html .='<div id="metaslider_' . $slider_id . '_filmstrip" class="' . $class . '">';
            $html .= "\n            <ul class='slides'>";

            while ( $query->have_posts() ) {
                $query->next_post();

                $type = get_post_meta( $query->post->ID, 'ml-slider_type', true );

                if ( $type == 'post_feed' ) {
                    $post_feed = new MetaPostFeedSlide();
                    $post_feed->set_slide( $query->post->ID );
                    $post_feed->set_slider( $slider_id );

                    $the_query = new WP_Query( $post_feed->get_post_args() );

                    $slides = array();

                    while ( $the_query->have_posts() ) {
                        $the_query->the_post();
                        $id = get_post_thumbnail_id( $the_query->post->ID );

                        if ( $override_id = get_post_meta( $the_query->post->ID, 'metaslider_post_feed_image', true ) ) {
                            if ( wp_attachment_is_image( $override_id ) ) {
                                $id = $override_id;
                            }
                        }

                        $imageHelper = new MetaSliderImageHelper(
                            $id,
                            $settings['thumb_width'],
                            $settings['thumb_height'],
                            'true'
                        );

                        $url = $imageHelper->get_image_url();

                        $list_item = "<li class=\"ms-thumb slide-{$query->post->ID} post-{$the_query->post->ID}\" style=\"display: none;\"><img src=\"{$url}\" /></li>";

                        $list_item = apply_filters( "metaslider_filmstrip_list_item", $list_item, $query->post, $url );

                        $html .= "\n                {$list_item}";
                    }

                    wp_reset_query();
                    
                } else if ( $type == 'external' ) {

                    $url = get_post_meta($query->post->ID, 'ml-slider_extimgurl', true);

                    $list_item = "<li class=\"ms-thumb slide-{$query->post->ID}\" style=\"display: none;\"><img src=\"{$url}\" /></li>";

                    $list_item = apply_filters( "metaslider_filmstrip_list_item", $list_item, $query->post, $url );

                    $html .= "\n                {$list_item}";

                } else {
                    // generate thumbnail
                    $imageHelper = new MetaSliderImageHelper(
                        $query->post->ID,
                        $settings['thumb_width'],
                        $settings['thumb_height'],
                        'true'
                    );

                    $url = $imageHelper->get_image_url();

                    if ( strlen( $url ) ) {
                        $list_item = "<li class=\"ms-thumb slide-{$query->post->ID}\" style=\"display: none;\"><img src=\"{$url}\" /></li>";

                        $list_item = apply_filters( "metaslider_filmstrip_list_item", $list_item, $query->post, $url );

                        $html .= "\n                {$list_item}";
                    }
                }
            }

            $html .= "\n            </ul>\n        </div>";

        }

        return $html;
    }

    /**
     * Output the JavaScript for the filmstrip carousel
     *
     * @param string  $javascript
     * @param integer $slider_id
     * @return string $javascript
     */
    public function metaslider_flex_filmstrip( $javascript, $slider_id ) {
        $settings = get_post_meta( $slider_id, 'ml-slider_settings', true );

        if ( isset( $settings["noConflict"] ) && $settings['noConflict'] == 'true' && $settings['navigation'] === 'filmstrip' ) {
            $javascript .= "\n            $('#metaslider_{$slider_id}_filmstrip').addClass('flexslider'); // theme/plugin conflict avoidance";
        }

        if ( isset( $settings["navigation"] ) && $settings['navigation'] === 'filmstrip' ) {

            $params = apply_filters('metaslider_flex_slider_filmstrip_parameters', array(
                'animation' => "'slide'",
                'controlNav' => "false",
                'animationLoop' => 'false',
                'slideshow' => 'false',
                'itemWidth' => $settings['thumb_width'],
                'itemMargin' => 5,
                'asNavFor' => "'#metaslider_{$slider_id}'"
            ), $slider_id, $settings );

            $javascript .= "\n            $('#metaslider_{$slider_id}_filmstrip').flexslider({";

            $count = 0;

            foreach ( $params as $param => $val ) {
                $count++;

                $javascript .= "\n                {$param}:{$val}";

                if ( count( $params ) !== $count ) {
                    $javascript .= ",";
                }

            }

            $javascript .= "\n            });";

        }

        return $javascript;
    }

    /**
     * Add a 'nav-hidden' class to slideshows where the navigation is hidden.
     *
     * @param string  $css
     * @param array   $settings
     * @param integer $slider_id
     * @return string $css
     */
    public function remove_bottom_margin( $class, $id, $settings ) {
        if ( isset( $settings["navigation"] ) && $settings['navigation'] === 'filmstrip' ) {
            return $class .= " nav-hidden";
        }

        return $class;
    }

    /**
     * Return css to ensure our slides are rendered correctly in the carousel
     *
     * @param string  $css
     * @param array   $settings
     * @param integer $slider_id
     * @return string $css
     */
    public function get_filmstrip_css( $css, $settings, $slider_id ) {
        if ( isset( $settings["navigation"] ) && $settings['navigation'] === 'filmstrip' ) {
            $css .= "\n        #metaslider_{$slider_id}_filmstrip.flexslider .slides li {margin-right: 5px;}";
        }

        return $css;
    }

    /**
     * Add the 'thumbnails' radio selector to the list of available navigation types.
     *
     * @param string  $navigation_row - the HTML for the existing settings row
     * @param object  $slider
     * @return string
     */
    public function navigation_options( $aFields, $slider ) {
        $newFields = array(
            'navigation' => array(
                'priority' => 60,
                'type' => 'navigation',
                'label' => __( "Navigation", "metasliderpro" ),
                'class' => 'coin flex nivo responsive',
                'value' => $slider->get_setting( 'navigation' ),
                'helptext' => __( "Show the slide navigation bullets", "metasliderpro" ),
                'options' => array(
                    'false'      => array( 'label' => __( "Hidden", "metasliderpro" ), 'class' => 'flex nivo responsive coin' ),
                    'true'       => array( 'label' => __( "Dots", "metasliderpro" ), 'class' => 'flex nivo responsive coin' ),
                    'thumbs'     => array( 'label' => __( "Thumbnails", "metasliderpro" ), 'class' => 'flex nivo' ),
                    'filmstrip'  => array( 'label' => __( "Filmstrip", "metasliderpro" ), 'class' => 'flex' )
                )
            ),
            'thumb_width' => array(
                'priority' => 70,
                'type' => 'number',
                'size' => 3,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'value' => $slider->get_setting( 'thumb_width' ),
                'label' => __( "Thumb Width", "metasliderpro" ),
                'class' => 'flex nivo',
                'helptext' => __( "Thumb width", "metasliderpro" ),
                'after' => __( "px", "metasliderpro" )
            ),
            'thumb_height' => array(
                'priority' => 80,
                'type' => 'number',
                'size' => 3,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'value' => $slider->get_setting( 'thumb_height' ),
                'label' => __( "Thumb Height", "metasliderpro" ),
                'class' => 'flex nivo',
                'helptext' => __( "Thumb height", "metasliderpro" ),
                'after' => __( "px", "metasliderpro" )
            )
        );

        return array_merge( $aFields, $newFields );
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Nivo Slider
     *
     * @param array   $options   - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array   $settings  - slideshow settings
     *
     * @return array modified javascript parameters
     */
    public function nivo_enable_thumbnails( $options, $slider_id, $settings ) {
        if ( $settings['navigation'] === 'thumbs' ) {
            unset( $options['controlNav'] );
            $options['controlNavThumbs'] = 'true';
        }

        return $options;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Flex Slider
     *
     * @param array   $options   - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array   $settings  - slideshow settings
     *
     * @return array modified javascript parameters
     */
    public function flex_enable_thumbnails( $options, $slider_id, $settings ) {
        if ( $settings['navigation'] === 'thumbs' ) {
            $options['controlNav'] = "'thumbnails'";
        }

        if ( $settings['navigation'] === 'filmstrip' ) {
            $options['sync'] = "'#metaslider_{$slider_id}_filmstrip'";
            $options['controlNav'] = "false";
            $options['before'][] = "    if (slider.currentSlide + 1 == slider.count) { $('#metaslider_{$slider_id}_filmstrip').flexslider(0); }" ;

            if ( isset( $options['itemWidth'] ) ) {
                unset( $options['itemWidth'] );
            }
        }
        return $options;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Nivo Slider
     *
     * @param array   $slide    - slide data
     * @param integer $slide_id - slide ID
     * @param array   $settings - slideshow settings
     *
     * @return array modified slide data
     */
    public function generate_thumbnail_for_slide( $slide, $slider_id, $settings ) {
        if ( ( $settings['type'] == 'nivo' || $settings['type'] == 'flex' ) && $settings['navigation'] === 'thumbs' ) {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide['id'],
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false'
            );

            $slide['data-thumb'] = $imageHelper->get_image_url();

            // for external slides, just use the external url
            $type = get_post_meta( $slide['id'], 'ml-slider_type', true );

            if ($type == 'external') {
                $slide['data-thumb'] = $slide['thumb'];
            }

            return $slide;
        }

        return $slide;
    }


    /**
     * Add a 'data-thumb' attribute to the Flex Slider <li>
     *
     * @param array   $attributes - list item attributes
     * @param integer $slide_id - slide ID
     * @param integer $slider_id - slideshow ID
     * @param array   $settings - slideshow settings
     *
     * @return array
     */
    public function add_flex_data_thumb_attribute( $attributes, $slide_id, $slider_id, $settings ) {

        if ( $settings['navigation'] === 'thumbs' ) {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide_id,
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false'
            );

            $attributes['data-thumb'] = $imageHelper->get_image_url();

            // for external slides, just use the external url
            $type = get_post_meta( $slide_id, 'ml-slider_type', true );

            if ($type == 'external') {
                $attributes['data-thumb'] = get_post_meta( $slide_id, 'ml-slider_extimgurl', true );
            }

        }

        return $attributes;
    }


    /**
     * Add a 'data-thumb' attribute to the nivo slider <img>
     *
     * @param array   $attributes - image attributes
     * @param integer $slide - slide data
     * @param array   $slider_id - slideshow ID
     *
     * @return array
     */
    public function add_nivo_data_thumb_attribute( $attributes, $slide, $slider_id ) {

        $settings = get_post_meta( $slider_id, 'ml-slider_settings', true );

        if ( $settings['navigation'] === 'thumbs' ) {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide['id'],
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false'
            );

            $attributes['data-thumb'] = $imageHelper->get_image_url();

            // for external slides, just use the external url
            $type = get_post_meta( $slide['id'], 'ml-slider_type', true );

            if ($type == 'external') {
                $attributes['data-thumb'] = get_post_meta( $slide_id, 'ml-slider_extimgurl', true );
            }

        }

        return $attributes;
    }
}


?>
