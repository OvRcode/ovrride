<?php

if (! defined('ABSPATH')) {
    die('No direct access.');
}

/**
 * You tube Slide class
 */
class MetaYouTubeSlide extends MetaSlide
{

    public $identifier = "youtube"; // should be lowercase, one word (use underscores if needed)
    public $name;

    /**
     * Register slide type
     */
    public function __construct()
    {
        $this->name = __('YouTube', 'ml-slider-pro');

        if (is_admin()) {
            add_filter('media_upload_tabs', array($this, 'custom_media_upload_tab_name'), 999, 1);
            add_action("media_upload_{$this->identifier}", array($this, 'get_iframe'));
            add_action("wp_ajax_create_{$this->identifier}_slide", array($this, 'ajax_create_slide'));
            add_action("wp_ajax_update_{$this->identifier}_thumbnail", array($this, 'ajax_update_thumbnail'));
            add_action('metaslider_register_admin_styles', array($this, 'register_admin_styles'), 10, 1);
            add_action('metaslider_register_admin_components', array($this, 'add_components'));
        }

        add_action("metaslider_save_{$this->identifier}_slide", array($this, 'save_slide'), 5, 3);
        add_action("metaslider_save_{$this->identifier}_slide", array($this, 'update_slide_thumb'), 4, 3);
        add_filter("metaslider_get_{$this->identifier}_slide", array($this, 'get_slide'), 10, 2);
    }

    /**
     * Saving the new settings
     *
     * @param int $slide_id Slide ID
     * @param int $slider_id Slider ID
     * @param array $fields Fields saved
     *
     * @return void;
     */
    public function update_slide_thumb($slide_id, $slider_id, $fields)
    {
        $youtube_url = sanitize_url($fields['youtube_url']);

        $current_url = get_post_meta($slide_id, 'ml-slider_youtube_url', true);
        if ($youtube_url === $current_url) {
            return;
        }

        preg_match(
            '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i',
            $youtube_url,
            $match
        );
        $video_id = $match[1];

        // Continuing, since the url has changed...
        $media_id = $this->create_slide_thumb($video_id, 999);

        // Set the image to the slide
        set_post_thumbnail($slide_id, $media_id);
    }

    /**
     * Extract the slide setings
     *
     * @param integer $id Slide ID
     */
    public function set_slide($id)
    {
        parent::set_slide($id);
        $this->slide_settings = get_post_meta($id, 'ml-slider_settings', true);
    }

    /**
     * Register admin styles
     */
    public function register_admin_styles()
    {
        wp_enqueue_style(
            "metasliderpro-{$this->identifier}-style",
            plugins_url('assets/style.css', __FILE__),
            false,
            METASLIDERPRO_VERSION
        );
    }

    public function add_components()
    {
        wp_enqueue_script(
            "metasliderpro-{$this->identifier}-admin-script",
            plugins_url('assets/admin.js', __FILE__),
            array('jquery'),
            METASLIDERPRO_VERSION
        );
        wp_localize_script("metasliderpro-{$this->identifier}-admin-script", 'metaslider_youtube', array(
            'nonce' => wp_create_nonce('youtube-slide-nonce'),
        ));
    }

    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     *
     * @param array $tabs existing media manager tabs
     * @return array tabs
     */
    public function custom_media_upload_tab_name($tabs)
    {
        // restrict our tab changes to the MetaSlider plugin page
        if ((isset($_GET['page']) && $_GET['page'] == 'metaslider') ||
            (isset($_GET['tab']) && in_array($_GET['tab'], array($this->identifier)))) {
            $newtabs = array(
                $this->identifier => $this->name
            );

            return array_merge($tabs, $newtabs);
        }

        return $tabs;
    }

    /**
     * Create a new slide and echo the admin HTML
     */
    public function ajax_create_slide()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_key($_POST['nonce']), 'youtube-slide-nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'ml-slider-pro'), 403);
        }

        if (!isset($_POST['slider_id']) || !isset($_POST['video_id']) || !isset($_POST['video_type']) || !isset($_POST['video_url'])) {
            wp_send_json_error(esc_html__('Bad request', 'ml-slider-pro'), 400);
        }

        $slider_id = intval($_POST['slider_id']);
        $fields['menu_order'] = 9999;
        $fields['video_id'] = sanitize_text_field($_POST['video_id']);
        $fields['video_type'] = sanitize_text_field($_POST['video_type']);
        $fields['video_url'] = esc_url($_POST['video_url']);
        $fields['settings']['lazyLoad'] = "on";
        $fields['settings']['showControls'] = "on";
        $this->create_slide($slider_id, $fields);
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_admin_slide();
        die(); // this is required to return a proper result

    }

    /**
     * Update the slide's thumbnail
     */
    public function ajax_update_thumbnail()
    {
        if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_key($_POST['nonce']), 'youtube-slide-nonce')) {
            wp_send_json_error(esc_html__('Invalid nonce', 'ml-slider-pro'), 403);
        }

        if (!isset($_POST['video_id']) || !isset($_POST['slide_id']) || !isset($_POST['video_type'])) {
            wp_send_json_error(esc_html__('Bad request', 'ml-slider-pro'), 400);
        }

        $slide_id = intval($_POST['slide_id']);
        $video_id = sanitize_text_field($_POST['video_id']);
        $video_type = sanitize_text_field($_POST['video_type']);

        $media_id = $this->create_slide_thumb($video_id, 999);

        wp_send_json_success([
            'slide_id' => $slide_id,
            'thumbnail' => wp_get_attachment_url($media_id),
        ]);
    }

    /**
     * Add inline styles used by videos
     */
    public function add_extra_styles()
    {
        $css = '.metaslider .youtube iframe{opacity:0;transition:opacity 0.5s ease-in-out}.metaslider .youtube.video-loaded iframe{opacity:1}.metaslider .youtube .play_button{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center}.metaslider .youtube .play_button img{width:75px;cursor:pointer;opacity:0.8}.metaslider .youtube .play_button img:hover{opacity:1}';
        $css = apply_filters('metaslider_youtube_inline_css', $css, $this->slide, $this->slider->ID);
        wp_add_inline_style('metaslider-public', $css);
    }

    /**
     * Media Manager Tab
     */
    public function youtube_tab()
    {
        return $this->get_iframe();
    }

    private function create_slide_thumb($video_id, $menu_order)
    {
        $post_info = array(
            'post_title' => "MetaSlider - YouTube Thumbnail - {$video_id}",
            'post_mime_type' => 'image/jpeg',
            'post_status' => 'inherit',
            'post_content' => '',
            'guid' => 'https://www.youtube.com/watch?v='.$video_id,
            'menu_order' => $menu_order,
            'post_name' => $video_id
        );

        $youtube_thumb = new WP_Http();
        $youtube_thumb = $youtube_thumb->request("https://img.youtube.com/vi/{$video_id}/0.jpg");

        if (
            ! is_wp_error($youtube_thumb)
            && isset($youtube_thumb['response']['code'])
            && $youtube_thumb['response']['code'] == 200
        ) {
            $attachment = wp_upload_bits("youtube_{$video_id}.jpg", null, $youtube_thumb['body']);
            $filename = $attachment['file'];
            $media_id = wp_insert_attachment($post_info, $filename);

            if (! function_exists('wp_generate_attachment_metadata')) {
                include( ABSPATH . 'wp-admin/includes/image.php' );
            }

            $attach_data = wp_generate_attachment_metadata($media_id, $filename);
            wp_update_attachment_metadata($media_id, $attach_data);

            return $media_id;
        }
        
        return wp_insert_attachment($post_info);
    }

    /**
     * Create a new YouTube slide
     *
     * @param integer $slider_id - slideshow ID
     * @param array $fields - slide details
     * @return string html
     */
    private function create_slide($slider_id, $fields)
    {
        $this->set_slider($slider_id);

        $media_id = $this->create_slide_thumb($fields['video_id'], $fields['menu_order']);
        $slide_id = null;

        if (method_exists($this, 'insert_slide')) {
            $slide_id = $this->insert_slide($media_id, $this->identifier, $slider_id);
            $this->add_or_update_or_delete_meta(
                $slide_id,
                'youtube_url',
                $fields['video_url']
            );
        } else {
            $this->add_or_update_or_delete_meta($media_id, 'type', $this->identifier);
        }

        $this->set_slide(! is_null($slide_id) ? $slide_id : $media_id);
        $this->tag_slide_to_slider();
        $this->save($fields);
        return ! is_null($slide_id) ? $slide_id : $media_id;
    }

    /**
     * Admin slide html
     *
     * @return string html
     */
    protected function get_admin_slide()
    {
        $thumb = "";

        // only show a thumbnail if we managed to download one when the slide was created
        if (get_post_thumbnail_id($this->slide->ID)) {// new slide format
            $thumb = $this->get_thumb();
        } else {
            if (strlen(get_attached_file($this->slide->ID))) {
                $thumb = $this->get_thumb();
            }
        }

        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_delete_button_html();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_update_image_button_html();
        do_action('metaslider-slide-edit-buttons', $this->identifier, $this->slide->ID);
        $edit_buttons = ob_get_clean();

        $row = "<tr id='slide-" . esc_attr($this->slide->ID) . "' class='slide " . esc_attr($this->identifier) . " flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='metaslider-ui-controls ui-sortable-handle'>";
        $row .= "           <h4 class='slide-details'>";
        $row .= "<span class='youtube'>YouTube Slide</span> | ID: ". esc_html( $this->slide->ID );
        $row .= "           </h4>";
        if (metaslider_this_is_trash($this->slide)) {
            $row .= '<div class="row-actions trash-btns">';
            $row .= "<span class='untrash'>{$this->get_undelete_button_html()}</span>";
            if( method_exists( $this, 'get_permanent_delete_button_html' ) ) {
                $row .= ' | ';
                $row .= "<span class='delete'>{$this->get_permanent_delete_button_html()}</span>";
            }
            $row .= '</div>';
        } else {
            $row .= $edit_buttons;
        }
        $row .= "        </div>";
        $row .= "        <div class='metaslider-ui-inner metaslider-slide-thumb' data-slide-id='" . esc_attr($this->slide->ID) . "'>";
        $row .= "           <button class='update-image image-button' data-button-text='" . esc_attr__(
                "Update slide image",
                "ml-slider"
            ) . "' title='" . esc_attr__("Update Slide Image", "ml-slider") . "' data-slide-id='" . esc_attr($this->slide->ID) . "'>";
        $row .= "           <div class='thumb' style='background-image: url(" . esc_attr($thumb) . ")'></div>";
        $row .= "           </button>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "       <div class='metaslider-ui-inner flex flex-col h-full'>";

        if (method_exists($this, 'get_admin_slide_tabs_html')) {
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . esc_html__("Please update to MetaSlider to version 3.2 or above.", "ml-slider-pro") . "</p>";
        }

        $row .= "        <input type='hidden' name='attachment[" . esc_attr($this->slide->ID) . "][type]' value='youtube' />";
        $row .= "        <input type='hidden' class='menu_order' name='attachment[" . esc_attr($this->slide->ID) . "][menu_order]' value='" . esc_attr($this->slide->menu_order) . "' />";
        $row .= "       </div>";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs()
    {
        $slide_id = absint($this->slide->ID);
        $showControls_checked = ! isset($this->slide_settings['showControls']) || $this->slide_settings['showControls'] == 'on' ? 'checked=checked' : '';
        $show_related_checked = isset($this->slide_settings['showRelated']) && $this->slide_settings['showRelated'] == 'on' ? 'checked=checked' : '';
        $auto_play_checked = isset($this->slide_settings['autoPlay']) && $this->slide_settings['autoPlay'] == 'on' ? 'checked=checked' : '';
        $mute_checked = isset($this->slide_settings['mute']) && $this->slide_settings['mute'] == 'on' ? 'checked=checked' : '';
        $light_theme_selected = isset($this->slide_settings['theme']) && $this->slide_settings['theme'] == 'light' ? 'selected' : '';
        $white_color_selected = isset($this->slide_settings['color']) && $this->slide_settings['color'] == 'white' ? 'selected' : '';
        $lazy_load = ! isset($this->slide_settings['lazyLoad']) || $this->slide_settings['lazyLoad'] == 'on' ? 'checked=checked' : '';
        $video_url = get_post_meta($slide_id, 'ml-slider_youtube_url', true);

        $general_tab = "<input style='padding:7px 10px;max-width:500px' data-lpignore='true' class='ms-super-wide metaslider-pro-youtube_url' name='attachment[" . esc_attr($slide_id) . "][youtube_url]' value='" . esc_attr($video_url) . "' data-slide-id='" . esc_attr($slide_id) . "'>";
        $general_tab .= "<ul class='ms-split-li'>
							<li>
                                <label><input type='checkbox' name='attachment[" . esc_attr($slide_id) . "][settings][mute]' {$mute_checked}/>
                                <span>" . esc_html__('Mute video','ml-slider-pro') . "</span></label>
                            </li>
							<li>
                                <label><input type='checkbox' name='attachment[" . esc_attr($slide_id) . "][settings][showControls]' {$showControls_checked}/>
                                <span>" . esc_html__('Show controls', 'ml-slider-pro') . "</span></label>
                            </li>
							<li>
                                <label><input type='checkbox' name='attachment[" . esc_attr($slide_id) . "][settings][autoPlay]' {$auto_play_checked}/>
                                <span>" . esc_html__('Auto play (may require video to be muted)', 'ml-slider-pro') . "</span></label>
                            </li>
							<li>
                                <label><input type='checkbox' name='attachment[" . esc_attr($slide_id) . "][settings][lazyLoad]' {$lazy_load}/>
                                <span>" . esc_html__('Lazy load video', 'ml-slider-pro') ."</span></label>
                            </li>
                            <li>
                                <label><input type='checkbox' name='attachment[" . esc_attr($slide_id) . "][settings][showRelated]' {$show_related_checked}/>
                                <span>" . esc_html__('Show related videos (disabling this may instead show only recommended videos from the channel)','ml-slider-pro') . "</span></label>
                            </li>
                        </ul>";

        $theme_tab = "<div class='row'>
                            <label>" . esc_html__("Theme", 'ml-slider-pro') . "</label>
                            <select name='attachment[" . esc_attr($slide_id) . "][settings][theme]'>
                                <option value='dark'>" . esc_html__('Dark', 'ml-slider-pro') . "</option>
                                <option value='light' {$light_theme_selected}>" . esc_html__('Light', 'ml-slider-pro') . "</option>
                            </select>
                        </div>
                        <div class='row'>
                            <label>" . esc_html__("Color", 'ml-slider-pro') . "</label>
                            <select name='attachment[" . esc_attr($slide_id) . "][settings][color]'>
                                <option value='red'>" . esc_html__('Red', 'ml-slider-pro') . "</option>
                                <option value='white' {$white_color_selected}>" . esc_html__('White', 'ml-slider-pro') . "</option>
                            </select>
                        </div>";

        $tabs = array(
            'general' => array(
                'title' => __("General", "ml-slider-pro"),
                'content' => $general_tab
            ),
            'theme' => array(
                'title' => __("Theme", "ml-slider-pro"),
                'content' => $theme_tab
            )
        );

        return apply_filters("metaslider_youtube_slide_tabs", $tabs, $this->slide, $this->slider, $this->settings);
    }

    /**
     * Public slide html
     *
     * @return string html
     */
    protected function get_public_slide()
    {
        wp_enqueue_script(
            'metasliderpro-youtube-api',
            METASLIDERPRO_BASE_URL . 'node_modules/jquery-tubeplayer-plugin/dist/jquery.tubeplayer.js',
            array('jquery'),
            METASLIDERPRO_VERSION
        );

        add_action('metaslider_register_public_styles', array($this, 'add_extra_styles'), 10, 2);

        if (get_post_meta($this->slide->ID, 'ml-slider_youtube_url', true)) {
            $url = get_post_meta($this->slide->ID, 'ml-slider_youtube_url', true);
        } else {
            $url = $this->slide->guid;
        }

        $pattern = '/^(?:https?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:shorts\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/';

        if (preg_match($pattern, $url, $matches)) {
            $video_id = $matches[1];
        } else {
            return;
        }
        
        if (! (int)$this->settings['height'] || ! (int)$this->settings['width']) {
            $ratio = 9 / 16 * 100;
        } else {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;
        }

        if ($this->settings['type'] == 'responsive') {
            add_filter(
                'metaslider_responsive_slider_parameters',
                array($this, 'get_responsive_slider_parameters'),
                10,
                2
            );
            add_filter(
                'metaslider_responsive_slider_javascript',
                array($this, 'get_responsive_youtube_javascript'),
                10,
                2
            );

            return $this->get_video_markup($video_id, $ratio);
        }

        if ($this->settings['type'] == 'flex') {
            add_filter('metaslider_flex_slider_parameters', array($this, 'get_flex_slider_parameters'), 10, 2);

            return $this->get_flex_slider_markup($video_id, $ratio);
        }
    }

    /**
     * Flex slider markup
     *
     * @param integer $video_id Video ID
     * @param float $ratio Video Ratio
     * @return string
     */
    public function get_flex_slider_markup($video_id, $ratio)
    {
        $html = $this->get_video_markup($video_id, $ratio);

        // store the slide details
        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-youtube",
            'style' => "display: none; width: 100%;"
        );

        $attributes = apply_filters(
            'metaslider_flex_slider_li_attributes',
            $attributes,
            $this->slide->ID,
            $this->slider->ID,
            $this->settings
        );

        $li = "<li";

        foreach ($attributes as $att => $val) {
            $li .= " " . $att . '="' . esc_attr($val) . '"';
        }

        $li .= ">" . $html . "</li>";

        $html = $li;

        return $html;
    }

    /**
     * Videon Markup
     *
     * @param integer $video_id Video ID
     * @param float $ratio Video Ratio
     * @return string
     */
    public function get_video_markup($video_id, $ratio)
    {
        $attrs = array(
            'style' => sprintf("position:relative;padding-bottom:%s%%;height:0", $ratio),
            'class' => "youtube",
            'data-id' => $video_id,
            'data-auto-play' => isset($this->slide_settings['autoPlay']) ? (int)filter_var(
                $this->slide_settings['autoPlay'],
                FILTER_VALIDATE_BOOLEAN
            ) : 0,
            'data-mute' => isset($this->slide_settings['mute']) ? (int)filter_var(
                $this->slide_settings['mute'],
                FILTER_VALIDATE_BOOLEAN
            ) : 0,
            'data-show-controls' => isset($this->slide_settings['showControls']) ? (int)filter_var(
                $this->slide_settings['showControls'],
                FILTER_VALIDATE_BOOLEAN
            ) : 0,
            'data-show-related' => isset($this->slide_settings['showRelated']) ? (int)filter_var(
                $this->slide_settings['showRelated'],
                FILTER_VALIDATE_BOOLEAN
            ) : 0,
            'data-theme' => isset($this->slide_settings['theme']) && 'light' == $this->slide_settings['theme'] ? "light" : "dark",
            'data-color' => isset($this->slide_settings['color']) && 'white' == $this->slide_settings['color'] ? "white" : "red",
            'data-lazy-load' => isset($this->slide_settings['lazyLoad']) ? (int)filter_var(
                $this->slide_settings['lazyLoad'],
                FILTER_VALIDATE_BOOLEAN
            ) : 0
        );

        // Backward compatibility 2.21 and below
        if( ! isset( $this->slide_settings['showControls'] ) ) {
            $attrs['data-show-controls'] = 1;
        }
        if( ! isset( $this->slide_settings['lazyLoad'] ) ) {
            $attrs['data-lazy-load'] = 1;
        }

        $html = "<div";
        foreach ($attrs as $key => $value) {
            $html .= " " . $key . '="' . $value . '"';
        }
        $html .= ">";

        if ($attrs['data-lazy-load']) {
            $html .= $this->temporary_video_image($video_id);
        }

        $html .= "</div>";

        return $html;
    }

    /**
     * Get the title of the video
     *
     * @param int|string $video_id - ID of the video
     * @return string
     */
    public function temporary_video_image($video_id)
    {
        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'],
            $this->settings['height'],
            isset($this->settings['smartCrop']) ? $this->settings['smartCrop'] : 'false'
        );
        $url = $imageHelper->get_image_url();

        if (! (bool)$video_title = get_post_meta($this->slide->ID, 'ml-slider_youtube_title', true)) {
            $video_title = $this->youtube_title($video_id);
            $this->add_or_update_or_delete_meta($this->slide->ID, 'youtube_title', $video_title);
        }

        $image_attributes = array(
            'src' => $url,
            'alt' => $video_title,
            'title' => '',
            'class' => 'msDefaultImage',
            'height' => $this->settings['height'],
            'width' => $this->settings['width']
        );

        if ($this->settings['type'] == 'flex') {
            $attributes = apply_filters(
                'metaslider_flex_slider_youtube_attributes',
                $image_attributes,
                $this->slide,
                $this->slider->ID
            );
        }

        if ($this->settings['type'] == 'responsive') {
            $attributes = apply_filters(
                'metaslider_responsive_slider_youtube_attributes',
                $image_attributes,
                $this->slide,
                $this->slider->ID
            );
        }

        $html = $this->build_image_tag($attributes);
        
        $play_button_color  = isset( $this->slide_settings['color'] ) 
                            ? sanitize_text_field( $this->slide_settings['color'] ) : 'red';
        $play_button_url    = METASLIDERPRO_BASE_URL . "modules/youtube/assets/yt-play-" . $play_button_color . '.png';
        $play_button_url    = apply_filters(
            'metaslider_youtube_play_button',
            $play_button_url,
            $this->slide,
            $this->slider->ID
        );
        $html .= "<span class='play_button'><a tabindex='0' role='button' id='toggle'><img width='75' src='{$play_button_url}'></a></span>";

        return $html;
    }

    /**
     * Get the title of the video
     *
     * @param int|string $video_id - ID of the video
     * @return string
     */
    public function youtube_title($video_id)
    {
        $context = apply_filters('metaslider_youtube_title_extra_context', null);
        $json = file_get_contents(
            'https://www.youtube.com/oembed?url=https://www.youtube.com/watch?v=' . $video_id . '&format=json',
            false,
            $context
        );
        $details = json_decode($json, true);
        return $details['title'];
    }

    /**
     * Pause youtube videos when the slide is changed
     *
     * @param array $options - JavaScript options
     * @param int $slider_id - current slideshow ID
     * @return array
     */
    public function get_responsive_slider_parameters($options, $slider_id)
    {
        // disable hoverpause - there is a bug with flex slider that means it
        // resumes the slideshow even when it has just been told to pause
        unset($options["pause"]);

        // we cannot pause the slideshow automatically with responsive slides
        $options["auto"] = "false";
        $options['before'] = isset($options['before']) ? $options['before'] : array();
        $options['before'] = array_merge($options['before'], array(
                "$('#metaslider_{$slider_id} .youtube').each(function(index) {
				if(typeof $(this).tubeplayer('data') !== 'undefined') {
					if ($(this).tubeplayer('data').state == 1) {
						$(this).tubeplayer('pause');
					}
				}
			});"
            )
        );

        $options['after'] = isset($options['after']) ? $options['after'] : array();
        $options['after'] = array_merge($options['after'], array(
                "$('#metaslider_{$slider_id} .rslides1_on .youtube').each(function(index) {
				if ($(this).data('lazyLoad') && $(this).data('autoPlay')) $(this).trigger('click');
				$(this).data('mute') && $(this).tubeplayer('mute');
				$(this).data('autoPlay') && $(this).tubeplayer('play');
			});"
            )
        );

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_responsive_slider_parameters', array($this, 'get_responsive_slider_parameters'));
        return $options;
    }

    /**
     * Pause youtube videos when the slide is changed
     *
     * @param array $options - current slideshow options
     * @param int $slider_id - current slideshow ID
     * @return array
     */
    public function get_flex_slider_parameters($options, $slider_id)
    {
        $autoPlay = "";

        // This is for slideshow autoplay, not video autoplay
        if ('true' == $this->settings['autoPlay']) {
            $autoPlay = ",
			onPlayerEnded: function(id) {
            	$('#metaslider_{$slider_id}').data('flexslider').manualPause = false;
            	$('#metaslider_{$slider_id}').flexslider('next');
            	$('#metaslider_{$slider_id}').flexslider('play');
            },
            onPlayerPaused: function(id) {
            	$('#metaslider_{$slider_id}').data('flexslider').manualPause = false;
            }";
        }

        $options["useCSS"] = "false";

        $options['before'] = isset($options['before']) ? $options['before'] : array();
        $options['before'] = array_merge($options['before'], array(
                "$('#metaslider_{$slider_id} .youtube').each(function(index) {
				if (typeof $(this).tubeplayer('data') !== 'undefined') {
					if ($(this).tubeplayer('data').state == 1) {
						$(this).tubeplayer('pause');
					}
				}
			});"
            )
        );

        $options['after'] = isset($options['after']) ? $options['after'] : array();
        $options['after'] = array_merge($options['after'], array(
                "$('#metaslider_{$slider_id} .flex-active-slide .youtube').each(function(index) {
				$(this).data('mute') && $(this).tubeplayer('mute');
				$(this).data('autoPlay') && $(this).tubeplayer('play');
				if ($(this).data('lazyLoad') && $(this).data('autoPlay')) $(this).trigger('click');
			});"
            )
        );

        $options['start'] = isset($options['start']) ? $options['start'] : array();
        $options['start'] = array_merge($options['start'], array(
                "$('#metaslider_{$slider_id} .youtube').each(function() {
				var youtube = $(this);
				var autoplay = false;
				if (youtube.data('autoPlay')) {
					if (youtube.parents('.flex-active-slide, .rslides1_on').length) {
						autoplay = true;
					}
				}
				var eventType = $(this).data('lazyLoad')  ? 'click' : 'metaslider/load-youtube-video';
				youtube.on(eventType, function() {
					var player = $(this).tubeplayer({{$this->get_tubeplayer_params()}
						onPlayerLoaded: function() {
							$(this).data('mute') && $(this).tubeplayer('mute');
							if ($(this).parents('.flex-active-slide').length) {
								$(this).data('autoPlay') && $(this).tubeplayer('play');
								$(this).data('lazyLoad') && $(this).tubeplayer('play');
							}
							$(this).addClass('video-loaded');
						},
						onPlayerPlaying: function(id) {
							$('#metaslider_{$slider_id} .flex-active-slide .youtube').data('autoplay', 0);
							$('#metaslider_{$slider_id}').flexslider('pause');
							$('#metaslider_{$slider_id}').data('flexslider').manualPause = true;
							$('#metaslider_{$slider_id}').data('flexslider').manualPlay = false;
						}{$autoPlay}
					});
				});
				if ($(this).data('lazyLoad')) {
					autoplay && $(this).trigger('click');
				}
				$(this).data('lazyLoad') || $(this).trigger('metaslider/load-youtube-video');
			});"
            )
        );

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_flex_slider_parameters', array($this, 'get_flex_slider_parameters'));
        return $options;
    }

    /**
     * Return the javascript which creates the YouTube videos in the slideshow
     *
     * @param string $javascript Youtube javascript
     * @param string $slider_id Slider ID
     * @return string
     */
    public function get_responsive_youtube_javascript($javascript, $slider_id)
    {
        $html = "$('#metaslider_{$this->slider->ID} .youtube').each(function() {
        	var youtube = $(this);
        	var autoplay = false;
        	if (youtube.data('autoPlay')) {
        	    if (youtube.parents('.rslides1_on').length) {
        	        autoplay = true;
        	    }
			}
			var eventType = youtube.data('lazyLoad')  ? 'click' : 'metaslider/load-youtube-video';
			youtube.on(eventType, function() {
				$(this).tubeplayer({{$this->get_tubeplayer_params()}
					onPlayerLoaded: function() {
						$(this).data('mute') && $(this).tubeplayer('mute');
						if ($(this).parents('.rslides1_on').length) {
							$(this).data('autoPlay') && $(this).tubeplayer('play');
							$(this).data('lazyLoad') && $(this).tubeplayer('play');
						}
						$(this).addClass('video-loaded');
					},
					onPlayerPlaying: function(id) {
						$('#metaslider_{$slider_id} .rslides1_on .youtube').data('autoplay', 0);
					}
				});
			});
			if (youtube.data('lazyLoad')) {
				autoplay && youtube.trigger('click');
			}
			/* Either lazyload is on, in which we do nothing, or trigger an event to load the video iframe */
			youtube.data('lazyLoad') || youtube.trigger('metaslider/load-youtube-video');
        });";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter('metaslider_responsive_slider_javascript', array($this, 'get_youtube_javascript'));

        return $javascript . $html;
    }

    /**
     * Tubeplayer JavaScript options
     */
    private function get_tubeplayer_params()
    {
        $params = "";

        $tubeplayer_params = array(
            'host' => "'https://www.youtube-nocookie.com'",
            'width' => $this->settings['width'],
            'height' => $this->settings['height'],
            'preferredQuality' => "'hd720'",
            'initialVideo' => "youtube.data('id')",
            'controls' => "youtube.data('showControls')",
            'showRelated' => "youtube.data('showRelated')",
            'theme' => "youtube.data('theme')",
            'color' => "youtube.data('color')",
            'protocol' => is_ssl() ? "'https'" : "'http'"
        );

        $tubeplayer_params = apply_filters(
            'metaslider_tubeplayer_params',
            $tubeplayer_params,
            $this->slider->ID,
            $this->slide->ID
        );

        foreach ($tubeplayer_params as $name => $value) {
            $params .= " \n" . $name . ": " . $value . ",";
        }

        return $params;
    }

    /**
     * Return wp_iframe
     */
    public function get_iframe()
    {
        return wp_iframe(array($this, 'iframe'));
    }

    /**
     * Media Manager iframe HTML
     */
    public function iframe()
    {
        do_action("metaslider_youtube_iframe");

        wp_enqueue_style('media-views');
        wp_enqueue_style(
            "metasliderpro-{$this->identifier}-styles",
            plugins_url('assets/style.css', __FILE__),
            false,
            METASLIDERPRO_VERSION
        );
        wp_enqueue_script(
            "metasliderpro-{$this->identifier}-script",
            plugins_url('assets/script.js', __FILE__),
            array('jquery'),
            METASLIDERPRO_VERSION
        );
        wp_localize_script("metasliderpro-{$this->identifier}-script", 'metaslider_custom_slide_type', array(
            'identifier' => $this->identifier,
            'name' => $this->name,
            'nonce' => wp_create_nonce('youtube-slide-nonce'),
        ));
        echo "<div class='metaslider'>
                <div class='youtube'>
                    <div class='media-embed'>
                        <label class='embed-url'>
                            <input type='text' placeholder='' class='youtube_url ms-super-wide'>
                            <span class='spinner'></span>
                        </label>
                        <div class='embed-link-settings'></div>
                    </div>
                </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large' disabled='disabled'>" . esc_html__(
                "Add to slideshow",
                "ml-slider-pro"
            ) . "</a>
                    </div>
                </div>
            </div>";
    }

    /**
     * Save slide
     *
     * @param array $fields Array of field options
     */
    protected function save($fields)
    {
        // Save the url in case it was updated
        if (isset($fields['youtube_url']) && ! empty($fields['youtube_url'])) {
            update_post_meta($this->slide->ID, 'ml-slider_youtube_url', $fields['youtube_url']);
        }

        // Update the order
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));

        // on/off settings
        foreach (array('lazyLoad', 'showControls', 'showRelated', 'showInfo', 'mute') as $setting) {
            if (! isset($fields['settings'][$setting])) {
                $fields['settings'][$setting] = 'off';
            }
        }

        // Other settings
        if (! isset($fields['settings']['color'])) {
            $fields['settings']['color'] = 'red';
        }

        if (! isset($fields['settings']['theme'])) {
            $fields['settings']['theme'] = 'dark';
        }

        // Save all the settings fields serialized
        if (isset($fields['settings'])) {
            $this->add_or_update_or_delete_meta($this->slide->ID, 'settings', $fields['settings']);
        }
    }
}
