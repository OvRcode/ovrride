<?php

if (! defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * Thumbnail Navigation addon
 */
class MetaSliderThumbnails
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_filter('metaslider_nivo_slider_parameters', array($this, 'nivo_enable_thumbnails'), 10, 3);
        add_filter('metaslider_flex_slider_parameters', array($this, 'flex_enable_thumbnails'), 10, 3);

        // standard thumbnail output
        add_filter('metaslider_image_slide_attributes', array($this, 'generate_thumbnail_for_slide'), 10, 3);
        
        // alt attribute for thumbnail (Image type)
        add_filter('metaslider_flex_slider_list_item_attributes', array($this, 'flex_image_thumbnails_alt'), 10, 3);

        // data-thumb and alt attributes for thumbnail (YouTube, Vimeo, Layer and Post Feed types)
        add_filter('metaslider_flex_slider_li_attributes', array($this, 'add_flex_data_thumb_attribute'), 10, 5);

        add_filter('metaslider_nivo_slider_image_attributes', array($this, 'add_nivo_data_thumb_attribute'), 10, 3);

        // add the navigation options to slideshow settings
        add_filter('metaslider_basic_settings', array($this, 'navigation_options'), 10, 2);

        // filmstrip output
        add_filter('metaslider_flex_slider_javascript_before', array($this, 'metaslider_flex_filmstrip'), 11, 2);
        add_filter('metaslider_flex_slider_html_after', array($this, 'metaslider_flex_filmstrip_html'), 10, 3);
        add_filter('metaslider_css_classes', array($this, 'remove_bottom_margin'), 11, 3);
        add_filter('metaslider_css', array($this, 'get_filmstrip_css'), 11, 3);

        // added in MetaSlider 2.10 (image crop positions)
        // triggered in ml-slider\metaslide.image.class.php by do_action("metaslider_ajax_resize_image_slide")
        add_action('metaslider_resize_image_slide', array($this, 'create_thumbnail'), 10, 3);

        // Adds classes for thumbnails and filmstrip navigation
        add_filter('metaslider_css_classes', array($this, 'add_thumbs_class_to_container'), 20, 3);

        // Reposition arrows to be on the slideshow, not the thumbnails
        add_filter('metaslider_flex_slider_parameters', array($this, 'reposition_arrows_flexslider'), 10, 3);

        // Calculate the thumbnail width and inline css (pro css is priority 11)
        add_action('metaslider_css', array($this, 'add_thumbnail_width_to_thumbs'), 12, 3);
    }

    /**
     * Create the slide thumbnail image.
     *
     * @param integer $slide_id Slide ID
     * @param integer $slider_id Slider ID
     * @param array $settings Slider settings
     * @return string
     */
    public function create_thumbnail($slide_id, $slider_id, $settings)
    {
        // create a copy of the correct sized image
        $imageHelper = new MetaSliderImageHelper(
            $slide_id,
            $settings['thumb_width'],
            $settings['thumb_height'],
            'true'
        );
        return $imageHelper->get_image_url(true);
    }

    /**
     * Output the carousel HTML for the filmstrip
     *
     * @param string $html Filmstrip HTML
     * @param integer $slider_id Slider ID
     * @param array $settings SLider settings
     * @return string $html
     */
    public function metaslider_flex_filmstrip_html($html, $slider_id, $settings)
    {
        if (isset($settings["navigation"]) && $settings['navigation'] == 'filmstrip') {
            $slider = new MetaSlider($slider_id, array());
            $query = $slider->get_slides();

            if (isset($settings["noConflict"]) && $settings['noConflict'] == 'true') {
                $class = 'filmstrip';
            } else {
                $class = 'flexslider filmstrip';
            }

            $html .= '<div id="metaslider_' . $slider_id . '_filmstrip" class="' . $class . '">';
            $html .= "\n            <ul class='slides'>";

            while ($query->have_posts()) {
                $query->next_post();

                $type = get_post_meta($query->post->ID, 'ml-slider_type', true);

                if ($type == 'post_feed') {
                    $post_feed = new MetaPostFeedSlide();
                    $post_feed->set_slide($query->post->ID);
                    $post_feed->set_slider($slider_id);

                    $the_query = new WP_Query($post_feed->get_post_args());

                    $slides = array();

                    while ($the_query->have_posts()) {
                        $the_query->the_post();
                        $image_id = get_post_thumbnail_id($the_query->post->ID);

                        if ($override_id = get_post_meta($the_query->post->ID, 'metaslider_post_feed_image', true)) {
                            if (wp_attachment_is_image($override_id)) {
                                $image_id = $override_id;
                            }
                        }

                        $imageHelper = new MetaSliderImageHelper(
                            $query->post->ID,
                            $settings['thumb_width'],
                            $settings['thumb_height'],
                            'true',
                            true,
                            $image_id
                        );

                        $url = $imageHelper->get_image_url();

                        $list_item = "<li class=\"ms-thumb slide-{$query->post->ID} post-{$the_query->post->ID}\" style=\"display: none;\"><img src=\"{$url}\" /></li>";

                        $list_item = apply_filters("metaslider_filmstrip_list_item", $list_item, $query->post, $url);

                        $html .= "\n                {$list_item}";
                    }

                    wp_reset_query();
                } else {
                    if ($type == 'external') {
                        $url = get_post_meta($query->post->ID, 'ml-slider_extimgurl', true);

                        $list_item = "<li class=\"ms-thumb slide-{$query->post->ID}\" style=\"display: none;\"><img src=\"{$url}\" /></li>";

                        $list_item = apply_filters("metaslider_filmstrip_list_item", $list_item, $query->post, $url);

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

                        if (strlen($url)) {
                            $list_item = "<li class=\"ms-thumb slide-{$query->post->ID}\" style=\"display: none;\"><img src=\"{$url}\" /></li>";

                            $list_item = apply_filters(
                                "metaslider_filmstrip_list_item",
                                $list_item,
                                $query->post,
                                $url
                            );

                            $html .= "\n                {$list_item}";
                        }
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
     * @param string $javascript Javascript for filstrip
     * @param integer $slider_id Slider ID
     * @return string $javascript
     */
    public function metaslider_flex_filmstrip($javascript, $slider_id)
    {
        $settings = get_post_meta($slider_id, 'ml-slider_settings', true);

        if (isset($settings["noConflict"]) && $settings['noConflict'] == 'true' && $settings['navigation'] === 'filmstrip') {
            $javascript .= "\n            $('#metaslider_{$slider_id}_filmstrip').addClass('flexslider');";
        }

        if (isset($settings["navigation"]) && $settings['navigation'] === 'filmstrip') {
            $params = apply_filters('metaslider_flex_slider_filmstrip_parameters', array(
                'animation' => "'slide'",
                'controlNav' => "false",
                'animationLoop' => 'false',
                'slideshow' => 'false',
                'itemWidth' => $settings['thumb_width'],
                'itemMargin' => 5,
                'asNavFor' => "'#metaslider_{$slider_id}'"
            ), $slider_id, $settings);

            $javascript .= "\n            $('#metaslider_{$slider_id}_filmstrip').flexslider({";

            $count = 0;

            foreach ($params as $param => $val) {
                $count++;

                $javascript .= "\n                {$param}:{$val}";

                if (count($params) !== $count) {
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
     * @param string $class Specifc slide class
     * @param integer $id Slide ID
     * @param array $settings Slide settimgs
     * @return string $css
     */
    public function remove_bottom_margin($class, $id, $settings)
    {
        if (isset($settings["navigation"]) && $settings['navigation'] === 'filmstrip') {
            return $class .= " nav-hidden";
        }

        return $class;
    }

    /**
     * Return css to ensure our slides are rendered correctly in the carousel
     *
     * @param string $css Slide CSS
     * @param array $settings Slide Settings
     * @param integer $slider_id Slider ID
     * @return string $css
     */
    public function get_filmstrip_css($css, $settings, $slider_id)
    {
        if (isset($settings["navigation"]) && $settings['navigation'] === 'filmstrip') {
            $css .= "\n        #metaslider_{$slider_id}_filmstrip.flexslider .slides li {margin-right: 5px;}";
        }

        return $css;
    }

    /**
     * Add the 'thumbnails' radio selector to the list of available navigation types.
     *
     * @param string $aFields - the HTML for the existing settings row
     * @param object $slider - Speicifc slider for navigation options
     * @return string
     */
    public function navigation_options($aFields, $slider)
    {
        $newFields = array(
            'navigation' => array(
                'priority' => 60,
                'type' => 'navigation',
                'label' => __("Navigation", "ml-slider-pro"),
                'class' => 'coin flex nivo responsive',
                'value' => $slider->get_setting('navigation'),
                'helptext' => __("Show the slide navigation bullets", "ml-slider-pro"),
                'options' => array(
                    'false' => array('label' => __("Hidden", "ml-slider-pro"), 'class' => 'flex nivo responsive coin'),
                    'true' => array('label' => __("Dots", "ml-slider-pro"), 'class' => 'flex nivo responsive coin'),
                    'thumbs' => array('label' => __("Thumbnails", "ml-slider-pro"), 'class' => 'flex nivo'),
                    'filmstrip' => array('label' => __("Filmstrip", "ml-slider-pro"), 'class' => 'flex')
                )
            ),
            'thumb_width' => array(
                'priority' => 70,
                'type' => 'number',
                'size' => 3,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'value' => $slider->get_setting('thumb_width'),
                'label' => __("Thumb Width", "ml-slider-pro"),
                'class' => 'flex nivo',
                'helptext' => __("Thumb width", "ml-slider-pro"),
                'after' => __("px", "ml-slider-pro")
            ),
            'thumb_height' => array(
                'priority' => 80,
                'type' => 'number',
                'size' => 3,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'value' => $slider->get_setting('thumb_height'),
                'label' => __("Thumb Height", "ml-slider-pro"),
                'class' => 'flex nivo',
                'helptext' => __("Thumb height", "ml-slider-pro"),
                'after' => __("px", "ml-slider-pro")
            ),
            'responsive_thumbs' => array(
                'priority' => 85,
                'type' => 'checkbox',
                'label' => __("Responsive Thumbnails", "ml-slider"),
                'class' => 'flex nivo showNextWhenChecked',
                'checked' => filter_var(
                    $slider->get_setting('responsive_thumbs'),
                    FILTER_VALIDATE_BOOLEAN
                ) ? 'checked' : '',
                'helptext' => __("This will use the 'Thumb Width' setting above as the max-width", "ml-slider")
            ),
            'thumb_min_width' => array(
                'priority' => 86,
                'type' => 'number',
                'size' => 3,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'value' => $slider->get_setting('thumb_min_width'),
                'label' => __("Thumb Min Width", "ml-slider-pro"),
                'class' => 'flex nivo',
                'helptext' => __("Set the min-width of the thumnbnails", "ml-slider-pro"),
                'after' => __("px", "ml-slider-pro")
            ),

            /*
            'thumbs_per_row' => array(
                'priority' => 86,
                'type' => 'number',
                'size' => 3,
                'min' => 0,
                'max' => 9999,
                'step' => 1,
                'value' => $slider->get_setting('thumbs_per_row'),
                'label' => __("Thumbs per row", "ml-slider-pro"),
                'class' => 'flex nivo',
                'helptext' => __("Use 0 here for default", "ml-slider-pro"),
                'after' => ''
            ),
            */
        );

        return array_merge($aFields, $newFields);
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Nivo Slider
     *
     * @param array $options - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array $settings - slideshow settings
     *
     * @return array modified javascript parameters
     */
    public function nivo_enable_thumbnails($options, $slider_id, $settings)
    {
        if ($settings['navigation'] === 'thumbs') {
            unset($options['controlNav']);
            $options['controlNavThumbs'] = 'true';
        }

        return $options;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Flex Slider
     *
     * @param array $options - javascript parameters
     * @param integer $slider_id - slideshow ID
     * @param array $settings - slideshow settings
     *
     * @return array modified javascript parameters
     */
    public function flex_enable_thumbnails($options, $slider_id, $settings)
    {
        if ('thumbs' === $settings['navigation']) {
            $options['controlNav'] = "'thumbnails'";
        }

        if ('filmstrip' === $settings['navigation']) {
            $options['sync'] = "'#metaslider_{$slider_id}_filmstrip'";
            $options['controlNav'] = "false";
            $options['before'] = isset($options['before']) ? $options['before'] : array();
            $options['before'] = array_merge($options['before'], array(
                "if (slider.currentSlide + 1 == slider.count) { $('#metaslider_{$slider_id}_filmstrip').flexslider(0); }"
            ));

            unset($options['itemWidth']);
        }
        return $options;
    }

    /**
     * Add data-thumb-alt attribute to generate thumbnail alt for Image slide
     *
     * @since 2.23
     * 
     * @param array     $attributes HTML attributes for <li> tag
     * @param array     $slide      Slide details
     * @param integer   $slider_id  Slideshow ID
     *
     * @return array    Modified javascript attributes
     */
    public function flex_image_thumbnails_alt($attributes, $slide, $slider_id)
    {
        $settings = get_post_meta( $slider_id, 'ml-slider_settings', true );
        if ( $settings['navigation'] === 'thumbs' ) {
            $attributes['data-thumb-alt'] = $slide['alt'];
        }
        
        return $attributes;
    }

    /**
     * Modify the JavaScript parameters to enable thumbnails for Nivo Slider
     *
     * @param array $slide - slide data
     * @param integer $slider_id - slide ID
     * @param array $settings - slideshow settings
     *
     * @return array modified slide data
     */
    public function generate_thumbnail_for_slide($slide, $slider_id, $settings)
    {
        if (($settings['type'] == 'nivo' || $settings['type'] == 'flex') && $settings['navigation'] === 'thumbs') {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide['id'],
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false'
            );

            $slide['data-thumb'] = $imageHelper->get_image_url();

            // for external slides, just use the external url
            $type = get_post_meta($slide['id'], 'ml-slider_type', true);

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
     * @param array $attributes - list item attributes
     * @param integer $slide_id - slide ID
     * @param integer $slider_id - slideshow ID
     * @param array $settings - slideshow settings
     * @param integer $image_id - an option image id (used for post type slider)
     *
     * @return array
     */
    public function add_flex_data_thumb_attribute($attributes, $slide_id, $slider_id, $settings, $image_id = null)
    {
        if ($settings['navigation'] === 'thumbs') {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide_id,
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false',
                true,
                $image_id
            );

            $type = get_post_meta($slide_id, 'ml-slider_type', true);

            if( ! $type ) {
                /* Post Feed
                 * $type return is false due $slide_id is actually a post id different 
                 * to the Post Feed slide id. Which is a different case for the rest of types.
                 * See all the instances of apply_filters('metaslider_flex_slider_li_attributes' ... ) to compare */
                $attributes['data-thumb']       = $imageHelper->get_image_url();
                $attributes['data-thumb-alt']   = get_post_meta(
                    $slide_id, 
                    '_wp_attachment_image_alt', 
                    true
                );
            } elseif ( 'external' == $type ) {
                // External
                $attributes['data-thumb']       = get_post_meta( $slide_id, 'ml-slider_extimgurl', true );
                $attributes['data-thumb-alt']   = get_post_meta( $slide_id, 'ml-slider_alt', true );
            } else {
                // Layer, YouTube, Vimeo
                $attributes['data-thumb']       = $imageHelper->get_image_url();
                $attributes['data-thumb-alt']   = get_post_meta(
                    get_post_thumbnail_id($slide_id),
                    '_wp_attachment_image_alt', 
                    true
                );
            }
        }
        return $attributes;
    }


    /**
     * Add a 'data-thumb' attribute to the nivo slider <img>
     *
     * @param array $attributes - image attributes
     * @param integer $slide - slide data
     * @param array $slider_id - slideshow ID
     *
     * @return array
     */
    public function add_nivo_data_thumb_attribute($attributes, $slide, $slider_id)
    {
        $settings = get_post_meta($slider_id, 'ml-slider_settings', true);

        if ($settings['navigation'] === 'thumbs') {
            // generate thumbnail
            $imageHelper = new MetaSliderImageHelper(
                $slide['id'],
                $settings['thumb_width'],
                $settings['thumb_height'],
                'false'
            );

            $attributes['data-thumb'] = $imageHelper->get_image_url();

            // for external slides, just use the external url
            $type = get_post_meta($slide['id'], 'ml-slider_type', true);

            if ($type == 'external') {
                $attributes['data-thumb'] = get_post_meta($slide_id, 'ml-slider_extimgurl', true);
            }
        }

        return $attributes;
    }

    /**
     * Slider Classes - Filter
     *
     * @param string $classes The class list for the container
     * @param int $slideshow_id The id of the slideshow
     * @param array $slideshow_settings The settings for the slideshow
     *
     * @return string
     * @since 2.10.0
     *
     */
    public function add_thumbs_class_to_container($classes, $slideshow_id, $slideshow_settings)
    {
        // Thumbs only
        if ('thumbs' !== $slideshow_settings['navigation']) {
            return $classes;
        }

        // Add a class to show thumbs are used (a theme might have added this already)
        if (false === strpos($classes, 'has-thumb-nav')) {
            $classes .= ' has-thumb-nav';
        }

        // Add a responsive class as needed
        $responsive = isset($slideshow_settings['responsive_thumbs']) ? filter_var(
            $slideshow_settings['responsive_thumbs'],
            FILTER_VALIDATE_BOOLEAN
        ) : false;
        if (false === strpos($classes, 'has-thumbs-responsive') && $responsive) {
            $classes .= ' has-thumbs-responsive';
        }

        return $classes;
    }

    /**
     * Reposition arrows on FlexSlider.
     *
     * @param string $options The options for flexslider
     * @param int $slideshow_id The id of the slideshow
     * @param array $slideshow_settings The settings for the slideshow
     *
     * @return string
     * @since 2.10.0
     *
     */
    public function reposition_arrows_flexslider($options, $slideshow_id, $slideshow_settings)
    {
        // Thumbs only
        if ('thumbs' !== $slideshow_settings['navigation']) {
            return $options;
        }

        $options['start'] = isset($options['start']) ? $options['start'] : array();
        $options['start'] = array_merge(
            $options['start'],
            array("$('#metaslider_{$slideshow_id}.flexslider > .slides').wrap('<div style=\"position:relative\" class=\"flex-slide-wrap\"></div>');$('#metaslider_{$slideshow_id}.flexslider .flex-direction-nav').appendTo('.metaslider #metaslider_{$slideshow_id}.flexslider .flex-slide-wrap');")
        );
        return $options;
    }

    /**
     * 'thumbs per row' option to limit on the slideshow
     * ! Not being used
     *
     * @param string $css The css extras
     * @param array $slideshow_settings The settings for the slideshow
     * @param int $slideshow_id The slideshow id
     * @param int $thumb_width The max_width of the thumbnail
     * @param int $slideshow_width The width of the slideshow
     *
     * @return string
     * @since 2.10.0
     *
     */
    public function limit_thumbs_per_row($css, $slideshow_settings, $slideshow_id, $thumb_width, $slideshow_width)
    {
        // ! This code below will not run becuase the feature was removed in favor for a min-width option
        // Calc the container width. i.e. if they only want three thumbs per row, constrain them based on thumb width
        $container_max_width = $thumb_width * $slideshow_settings['thumbs_per_row'];

        // If the container width is larger than the slideshow:
        if ($container_max_width >= $slideshow_width) {
            // This logic sets the thumbs per row (and stops an incomplete row from filling 100% remaining space)
            $min_width = ($slideshow_width / $slideshow_settings['thumbs_per_row']) / $slideshow_width * 100;
            return $css . "
            	#metaslider_{$slideshow_id} .flex-control-thumbs li {min-width:{$min_width}%;max-width:{$thumb_width}px}
            	#metaslider_{$slideshow_id} ~ .nivo-controlNav.nivo-thumbs-enabled a {min-width:{$min_width}%;max-width:{$thumb_width}px}
            ";
        }

        // Set the min and max width to override the flex:1 setting. i.e. don't let flexbox handle it
        return $css . "
        	#metaslider_{$slideshow_id} .flex-control-thumbs {max-width:{$container_max_width}px}
        	#metaslider_{$slideshow_id} .flex-control-thumbs li {min-width:{$thumb_width}px;max-width:{$thumb_width}px}
        	#metaslider_{$slideshow_id} ~ .nivo-controlNav.nivo-thumbs-enabled {max-width:{$container_max_width}px}
        	#metaslider_{$slideshow_id} ~ .nivo-controlNav.nivo-thumbs-enabled a {min-width:{$thumb_width}px;max-width:{$thumb_width}px}
        ";
    }

    /**
     * Calculate expect thumbs width
     *
     * @param string $css The css extras
     * @param array $slideshow_settings The settings for the slideshow
     * @param int $slideshow_id The slideshow id
     *
     * @return string
     * @since 2.10.0
     *
     */
    public function add_thumbnail_width_to_thumbs($css, $slideshow_settings, $slideshow_id)
    {
        // Responsive Thumbs only
        $responsive = isset($slideshow_settings['responsive_thumbs']) ? filter_var(
            $slideshow_settings['responsive_thumbs'],
            FILTER_VALIDATE_BOOLEAN
        ) : false;
        if ('thumbs' !== $slideshow_settings['navigation'] || ! $responsive) {
            return $css;
        }

        // Get the width and thumb width (might be 0)
        $thumb_width = isset($slideshow_settings['thumb_width']) ? intval($slideshow_settings['thumb_width']) : 0;
        $slideshow_width = isset($slideshow_settings['width']) ? intval($slideshow_settings['width']) : 0;

        // Some people set the slideshow width to 0, bail
        if (! $slideshow_width || ! $thumb_width) {
            return $css;
        }

        /*
        Optionally, they can define the images per row
        $use_thumb_rows = (isset($slideshow_settings['thumbs_per_row']) && intval($slideshow_settings['thumbs_per_row']));
        ! Abandonded feature to limit the slides by amount per row. Instead went with a min-width option.
        if ($use_thumb_rows) return $this->limit_thumbs_per_row($css, $slideshow_settings, $slideshow_id, $thumb_width, $slideshow_width);
        */

        $min_width = isset($slideshow_settings['thumb_min_width']) ? intval($slideshow_settings['thumb_min_width']) : 0;

        // return the max-width as the set amount, and let flexbox handle the rest
        return $css . "
			#metaslider_{$slideshow_id} .flex-control-thumbs li {max-width:{$thumb_width}px;min-width:{$min_width}px}
			#metaslider_{$slideshow_id} ~ .nivo-controlNav.nivo-thumbs-enabled a {max-width:{$thumb_width}px;min-width:{$min_width}px}
		";
    }
}
