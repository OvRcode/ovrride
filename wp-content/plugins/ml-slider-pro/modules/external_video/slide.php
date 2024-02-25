<?php

if ( ! defined('ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
class MetaExternalVideoSlide extends MetaLocalVideoSlide
{

    public $identifier = 'external_video'; // should be lowercase, one word (use underscores if needed)
    public $name;
    public $slug = 'external-video';
    private $slide_settings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = __( 'External Video', 'ml-slider-pro' );

        if ( is_admin() ) {
            add_filter( 'media_upload_tabs', array( $this, 'custom_media_upload_tab_name' ), 999, 1 );
            add_action( 'metaslider_register_admin_scripts', array( $this, 'register_admin_scripts' ), 10, 1 );
            add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
            add_action( "wp_ajax_create_{$this->identifier}_slide", array( $this, 'ajax_create_slide' ) );
            add_action( "wp_ajax_update_{$this->identifier}_slide", array( $this, 'ajax_update_slide' ) );
            add_action( "media_upload_{$this->identifier}", array( $this, 'get_iframe' ) );
        }

        add_filter( "metaslider_get_{$this->identifier}_slide", array( $this, 'get_slide' ), 10, 2 );
        add_action( "metaslider_save_{$this->identifier}_slide", array( $this, 'save_slide' ), 5, 3 );
    }

    /**
     * Registers and enqueues admin JavaScript
     * Overrides parent::register_admin_scripts()
     */
    public function register_admin_scripts()
    {
        wp_enqueue_script(
            "metasliderpro-{$this->slug}-script",
            plugins_url( "assets/external_video.js", __FILE__ ),
            array( 'metaslider-admin-script' ),
            METASLIDERPRO_VERSION,
            true
        );
        wp_localize_script( 
            "metasliderpro-{$this->slug}-script", 
            "metaslider_{$this->identifier}", 
            array( 
                'update_slide_nonce' => wp_create_nonce( "metaslider_update_{$this->identifier}_nonce" ),
                'update_cover_text' => esc_html__( 'Select replacement cover', 'ml-slider' )
            )
        );
    }

    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     *
     * @var array existing media manager tabs
     */
    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     *
     * @param array $tabs existing media manager tabs
     * @return array
     */
    public function custom_media_upload_tab_name($tabs)
    {
        // restrict our tab changes to the MetaSlider plugin page
        if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'metaslider' ) 
            || ( isset( $_GET['tab'] ) 
                && in_array( $_GET['tab'], array( $this->identifier ) ) 
            ) 
        ) {
            $newtabs = array(
                $this->identifier => $this->name
            );

            return array_merge($tabs, $newtabs);
        }

        return $tabs;
    }
    
    /**
     * Create slide via Ajax
     * Overrides parent::ajax_create_slide()
     */
    public function ajax_create_slide()
    {
        if ( ! isset( $_POST['nonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_POST['nonce']), "metaslider_create_{$this->identifier}_nonce" ) 
        ) {
            wp_send_json_error( esc_html__( 'Invalid nonce', 'ml-slider-pro' ), 403 );
        }

        if ( ! isset( $_POST['slider_id'] ) || ! isset( $_POST['video_url'] ) ) {
            wp_send_json_error( 
                esc_html__( 'Bad request', 'ml-slider-pro' ), 
                400 
            );
        }

        $slider_id = intval( $_POST['slider_id'] );
        $fields['video_url'] = sanitize_url( $_POST['video_url'] );

        $new_slide_id = $this->create_slide( $slider_id, $fields );
        
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        $html = $this->get_admin_slide();

        $result = array(
            'slide_id' => $new_slide_id,
            'html' => $html
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Return wp_iframe
     */
    public function get_iframe()
    {
        return wp_iframe( array( $this, 'iframe' ) );
    }

    /**
     * Media Manager iframe HTML
     */
    public function iframe()
    {
        wp_enqueue_style( 'media-views' );
        wp_enqueue_style(
            "metasliderpro-{$this->identifier}-styles",
            plugins_url( 'assets/style.css', __FILE__ ),
            false,
            METASLIDERPRO_VERSION
        );
        wp_enqueue_script(
            "metasliderpro-{$this->identifier}-script",
            plugins_url( 'assets/script.js', __FILE__ ),
            array( 'jquery' ),
            METASLIDERPRO_VERSION
        );

        wp_localize_script( 
            "metasliderpro-{$this->identifier}-script", 
            "metaslider_{$this->identifier}_slide_type", 
            array(
                'identifier' => $this->identifier,
                'name' => $this->name,
                'nonce' => wp_create_nonce( "metaslider_create_{$this->identifier}_nonce" ),
            )
        );

        echo "<div class='metaslider'>
                <div class='external-video'>
                    <div class='media-embed'>
                        <label class='embed-url'>
                            <input type='text' class='external_video_url ms-super-wide' placeholder='" . 
                                esc_attr__( 'Add the URL ..', 'ml-slider-pro' ) 
                                . "'>
                            <span class='spinner' style='display: none;'></span>
                        </label>
                        <div class='embed-link-settings' style='display: none;'>
                            <p>" . esc_html__( 
                                'Click the "Add to slideshow" button ...', 
                                'ml-slider-pro' 
                            ) . "</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large' disabled='disabled'>" . esc_html__(
                'Add to slideshow',
                'ml-slider'
            ) . "</a>
                    </div>
                </div>
            </div>";
    }

    /**
     * Extract the slide setings
     * Overrides parent::set_slide()
     * 
     * @param integer $id Slide ID
     */
    public function set_slide( $id )
    {
        parent::set_slide( $id );
        $this->slide_settings = get_post_meta( $id, 'ml-slider_settings', true );
    }
    
    /**
     * Create a new slide
     *
     * @param integer $slider_id Slider ID
     * @return int ID of the created slide
     */
    public function create_slide( $slider_id, $fields )
    {
        $this->set_slider( $slider_id );

        $slide_id = $this->insert_slide( false, 'external_video', $slider_id );

        $this->add_or_update_or_delete_meta(
            $slide_id,
            'video_url',
            $fields['video_url']
        );

        $this->set_slide( $slide_id );

        $this->tag_slide_to_slider();

        return $slide_id;
    }

    /**
     * Save - called whenever the slideshow is saved.
     *
     * @param array $fields Array of fields options
     */
    protected function save( $fields )
    {
        wp_update_post( 
            array(
                'ID' => $this->slide->ID,
                'menu_order' => $fields['menu_order'],
                'post_excerpt' => $fields['post_excerpt']
            ) 
        );
        
        // Link URL settings
        $this->add_or_update_or_delete_meta( 
            $this->slide->ID, 
            'new_window', 
            isset( $fields['new_window'] ) && $fields['new_window'] === 'on' 
        );
        $this->add_or_update_or_delete_meta( $this->slide->ID, 'url', $fields['url'] );

        // Save track data
        $this->save_track( $fields );

        // Set defaults for non existing fields
        foreach ( array( 'mute', 'controls', 'autoPlay', 'lazyLoad', 'loop' ) as $setting ) {
            if ( ! isset( $fields['settings'][$setting] ) ) {
                $fields['settings'][$setting] = 'off';
            }
        }

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_smartphone',
            isset($fields['hide_slide_smartphone']) && $fields['hide_slide_smartphone'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_tablet',
            isset($fields['hide_slide_tablet']) && $fields['hide_slide_tablet'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_laptop',
            isset($fields['hide_slide_laptop']) && $fields['hide_slide_laptop'] === 'on'
        );

        $this->add_or_update_or_delete_meta(
            $this->slide->ID,
            'hide_slide_desktop',
            isset($fields['hide_slide_desktop']) && $fields['hide_slide_desktop'] === 'on'
        );

        // Save all the settings fields serialized
        if ( isset( $fields['settings'] ) ) {
            $this->add_or_update_or_delete_meta( $this->slide->ID, 'settings', $fields['settings'] );
        }
    }

    /**
     * Check if $file_url uses the same host and adjust to use the same scheme
     * otherwise will return $file_url (aka $file_url is from another domain)
     * 
     * @since 2.30
     * 
     * @param string $file_url A text track or video URL. e.g. 'http://externalsite.com/path/to/file.vtt'
     * 
     * @return string 
     */
    public function adjust_file_url( $file_url )
    {
        $parsed_site_url    = parse_url( site_url() );
        $parsed_file_url    = parse_url( $file_url );
        
        if ( $parsed_site_url 
            && $parsed_file_url 
            && isset( $parsed_site_url['host'] ) 
            && isset( $parsed_file_url['host'] ) 
            && $parsed_site_url['host'] === $parsed_file_url['host']
        ) {
            return $parsed_site_url['scheme'] . '://' . $parsed_site_url['host'] 
                    . ( isset( $parsed_file_url['path'] ) ? $parsed_file_url['path'] : '' );
        }
        
        return $file_url;
    }

    /**
     * Admin slide html
     *
     * @return string html
     */
    protected function get_admin_slide()
    {
        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_delete_button_html();
        do_action( 'metaslider-slide-edit-buttons', $this->identifier, $this->slide->ID );
        $edit_buttons = ob_get_clean();

        // slide row HTML
        $row = "<tr id='slide-" . esc_attr( $this->slide->ID ) . "' class='slide " . $this->identifier . "_slide flex'>";
        $row .= "    <td class='col-1'>";
        $row .= "       <div class='metaslider-ui-controls ui-sortable-handle'>";
        $row .= "           <h4 class='slide-details'>";
        $row .= esc_html__( 'External Video Slide', 'ml-slider-pro' ) . " | ID: ". esc_html( $this->slide->ID );
        $row .= "           </h4>";
        if ( metaslider_this_is_trash( $this->slide ) ) {
            $row .= '<div class="row-actions trash-btns">';
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            $row .= "<span class='untrash'>{$this->get_undelete_button_html()}</span>";
            if( method_exists( $this, 'get_permanent_delete_button_html' ) ) {
                $row .= ' | ';
                $row .= "<span class='delete'>{$this->get_permanent_delete_button_html()}</span>";
            }
            $row .= '</div>';
        } else {
            $row .= $edit_buttons;
        }
        $row .= "       </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "       <div class='metaslider-ui-inner flex flex-col h-full'>";

        if (method_exists($this, 'get_admin_slide_tabs_html')) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . esc_html__("Please update to MetaSlider to version 3.2 or above.", "ml-slider-pro") . "</p>";
        }

        $row .= "           <input type='hidden' name='attachment[" . esc_attr( $this->slide->ID ) . "][type]' value='" . esc_attr( $this->identifier ) . "' />";
        $row .= "           <input type='hidden' class='menu_order' name='attachment[" . esc_attr( $this->slide->ID ) . "][menu_order]' value='" . esc_attr( $this->slide->menu_order ) . "' />";
        $row .= "       </div>";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     * Overrides parent::get_admin_tabs()
     * 
     * @return array
     */
    public function get_admin_tabs()
    {
        $path = trailingslashit(plugin_dir_path(__FILE__)) . 'tabs/';

        ob_start();
        include $path . 'general.php';
        $general_tab = ob_get_clean();

        ob_start();
        include $path . 'cover.php';
        $cover_tab = ob_get_clean();

        // Track
        $track_tab = $this->track_tab_content();

        $tabs = array(
            'general' => array(
                'title' => __( 'General', 'ml-slider' ),
                'content' => $general_tab
            ),
            'cover' => array(
                'title' => __( 'Cover', 'ml-slider-pro' ),
                'content' => $cover_tab
            ),
            'track' => array(
                'title' => esc_html__( 'Text track', 'ml-slider-pro' ),
                'content' => $track_tab
            )
        );

        $global_settings = $this->get_global_settings();
        if (
            !isset($global_settings['mobileSettings']) ||
            (isset($global_settings['mobileSettings']) && true == $global_settings['mobileSettings'])
        ) {
            ob_start();
            include $path . 'mobile.php';
            $mobile_tab = ob_get_clean();

            $tabs['mobile'] = array(
                'title' => __("Mobile", "ml-slider"),
                'content' => $mobile_tab
            );
        }

        return apply_filters(
            "metaslider_" . $this->identifier . "_slide_tabs",
            $tabs,
            $this->slide,
            $this->slider,
            $this->settings
        );
    }

    /**
     * Get track URL field
     * Overrides parent::track_url_field()
     * 
     * @since 2.30
     * 
     * @return html
     */
    protected function track_url_field()
    {
        $track      = get_post_meta( $this->slide->ID, 'ml-slider_track', true );
        $track_url  = isset( $track['url'] ) ? $track['url'] : '';

        $html   = '<div class="row mb-2">';
        $html  .= '    <label class="tipsy-tooltip-top" title="' . 
                        esc_attr( 'File must be TXT or VTT format', 'ml-slider-pro' ) . '">' .
                            __( 'Source', 'ml-slider-pro' ) . '</label>';
        $html  .= '</div>';
        $html  .= '<div class="row">';
        $html  .= '     <input class="w-100" type="text" name="attachment[' . 
                        esc_attr( $this->slide->ID ) . '][track][url]"' .
                        ' placeholder="' . 
                        esc_attr__( 'Add the URL..', 'ml-slider-pro' ) . '"' .
                        ' value="' . esc_url( $track_url ) . '">';
        $html  .= '</div>';

        return $html;
    }

    /**
     * Add track attributes to $attributes array
     * Overrides parent:: track_attributes()
     * 
     * @since 2.30
     *
     * @param array $attributes The existing attributes including data-id, data-loop, data-autoplay, etc.
     * 
     * @return array
     */
    public function track_attributes( $attributes )
    {
        $track = get_post_meta( $this->slide->ID, 'ml-slider_track', true );

        // If slide doesn't have a track url, we stop the process here
        if ( ! isset( $track['url'] ) || empty( $track['url'] ) ) {
            return $attributes;
        }

        $video_url = get_post_meta( $this->slide->ID, 'ml-slider_video_url', true );
        
        /* If slide doesn't have a valid track url 
         * and doesn't have a video url or is invalid 
         * we stop the process here */
        if ( 
            ! $this->is_track_file( $track['url'] ) 
            && ( 
                ! isset( $video_url ) || ! $this->video_type_is_allowed( $video_url ) 
            )
        ) {
            return $attributes;
        }

        $track_url = $this->adjust_file_url( $track['url'] );

        // Cleanup video URL
        $video_url = $this->adjust_file_url( $video_url );

        $attributes['data-track-url']           = $track_url;
        $attributes['data-track-lang']          = ! empty( $track['lang'] ) 
                                                ? $track['lang']  : 'en';
        $attributes['data-track-label']         = ! empty( $track['label'] ) 
                                                ? $track['label'] : esc_attr__( 'English' );
        $attributes['data-track-kind']          = ! empty( $track['kind'] ) 
                                                ? $track['kind'] : esc_attr__( 'captions' );
        $attributes['data-track-crossorigin']   = ! $this->is_same_domain( $track_url ) || ! $this->is_same_domain( $track_url ) ? 'true' : 'false';

        return $attributes;
    }

    /**
     * Check if $file_url uses the same domain as the WordPress site
     * 
     * @since 2.30
     * 
     * @param string $file_url A text track or video URL. e.g. 'http://externalsite.com/path/to/file.vtt'
     * 
     * @return boolean
     */
    public function is_same_domain( $file_url )
    {
        $parsed_site_url    = parse_url( site_url() );
        $parsed_file_url    = parse_url( $file_url );

        if ( isset( $parsed_site_url['host'] ) 
            && isset( $parsed_file_url['host'] ) 
            && $parsed_site_url['host'] === $parsed_file_url['host'] 
        ) {
            return true;
        }

        return false;
    }

    /**
     * Ajax wrapper to update the slide video.
     * Overrides parent::ajax_update_slide()
     * 
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_update_slide()
    {
        if ( ! isset( $_POST['nonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), "metaslider_update_{$this->identifier}_nonce" ) ) {
            wp_send_json_error( array(
                'message' => __( 'The security check failed. Please refresh the page and try again.', 'ml-slider' )
            ), 401 );
        }

        $capability = apply_filters( 'metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES );
        if ( ! current_user_can( $capability ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Access denied', 'ml-slider' )
                ],
                403
            );
        }

        if ( ! isset( $_POST['slide_id'] ) || ! isset( $_POST['video_url'] ) || ! isset( $_POST['slider_id'] ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        // Only allow mp4, webm and mov videos
        if( ! empty( $_POST['video_url'] ) 
            && ! $this->video_type_is_allowed( esc_url( $_POST['video_url'] ) ) 
        ) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'Not supported video format', 'ml-slider-pro' )
                ],
                422
            );
        }

        $result = $this->update_slide(
            absint( $_POST['slide_id'] ),
            sanitize_url( $_POST['video_url'] ),
            absint( $_POST['slider_id'] )
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Updates the slide meta value to a new video.
     *
     * @param int $slide_id     The id of the slide being updated
     * @param int $video_url    The URL of the new video to use
     * @param int $slideshow_id The id of the slideshow
     *
     * @return array|WP_error The status message and if success, the thumbnail link
     */
    protected function update_slide( $slide_id, $video_url, $slideshow_id = null )
    {   
        /*
         * Updates database record and thumbnail if selection changed, assigns it to the slideshow, crops the image
         */
        update_post_meta( $slide_id, 'ml-slider_video_url', $video_url );
        if ( $slideshow_id ) {
            $this->set_slider( $slideshow_id );

            return array(
                'message' => __( 'The video was successfully updated.', 'ml-slider-pro' ),
                'video_url' => $video_url,
                'mime_type' => $this->get_video_mime_type( $video_url )
            );
        }

        return new WP_Error( 
            'update_failed', 
            __( 'There was an error updating the video. Please try again', 'ml-slider-pro' ), 
            array( 'status' => 409 ) 
        );
    }

    /**
     * Returns the HTML for the public slide
     * 
     * @return string slide html
     */
    protected function get_public_slide()
    {
        wp_enqueue_script(
            'metasliderpro-videojs-script',
            METASLIDERPRO_BASE_URL . 'modules/local_video/assets/video.js/video.min.js',
            array(),
            METASLIDERPRO_VERSION,
            true
        );

        wp_enqueue_style(
            'metasliderpro-videojs-default-style',
            METASLIDERPRO_BASE_URL . 'modules/local_video/assets/video.js/video-js.min.css',
            array(),
            METASLIDERPRO_VERSION
        );

        add_action( 'metaslider_register_public_styles', array( $this, 'add_extra_styles' ), 10, 2 );

        $video_url = get_post_meta( $this->slide->ID, 'ml-slider_video_url', true );

        // Flexslider; only when a video URL exists
        if ( $video_url && $this->settings['type'] == 'flex' ) {
            add_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ), 10, 2 );

            return $this->get_flex_slider_markup();
        }
    }

    /**
     * Generate flex slider markup
     * 
     * @param string $video_url The URL of the video. e.g. 'https://website.any/path/to/video.mp4'
     * @param array $settings   The slide settings. Mutem, Auto play, Loop, etc.
     * @param int $ratio        The video aspect ratio
     * 
     * @return string
     */
    private function get_flex_slider_markup()
    {
        $html  = $this->video_wrapper();

        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-{$this->slug} {$this->get_mobile_css_class($this->slide->ID)}",
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
     * Return the video type. e.g. 'mp4'
     *
     * @param string $video_url The URL of the video. e.g. 'https://website.any/path/to/video.mp4'
     * 
     * @return string
     */
    private function get_video_mime_type( $video_url )
    {
        // Empty? Stop the process here
        if ( empty( $video_url ) ) {
            return '';
        }

        // Supported formats
        $allowed = array(
            'mov',
            'mp4',
            'webm'
        );

        /* Extract extension from $video_url. 
         * e.g. 'https://website.any/path/to/video.mp4' -> 'video/mp4' */
        $path       = parse_url( $video_url, PHP_URL_PATH );
        $extension  = pathinfo( $path, PATHINFO_EXTENSION );

        if( ! in_array( $extension, $allowed ) ) {
            return '';
        }

        // mov extension mime is different
        if ( $extension === 'mov' ) {
            $mime_type = 'video/quicktime';
        } else {
            // 'video/webm' or 'video/mp4'
            $mime_type = 'video/' . $extension;
        }

        return $mime_type;
    }

    /**
     * Check if video type is allowed (mp4, webm and mov)
     * 
     * @param string $video_url The URL of the video. e.g. 'https://website.any/path/to/video.mp4'
     * 
     * @return boolean
     */
    protected function video_type_is_allowed( $video_url )
    {
        $allowed = array(
            'video/quicktime',
            'video/mp4',
            'video/webm'
        );

        if ( ! in_array( $this->get_video_mime_type( $video_url ), $allowed ) ) {
            return false;
        }

        return true;
    }

    /**
     * Return the video wrapper with custom data attributes
     *
     * @return string
     */
    private function video_wrapper()
    {
        //$settings       = get_post_meta( $this->slider->ID, 'ml-slider_settings', true );
        $video_url    = get_post_meta( $this->slide->ID, 'ml-slider_video_url', true );

        if ( ! (int) $this->settings['height'] || ! (int) $this->settings['width'] ) {
            $ratio = 9 / 16 * 100;
        } else {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;
        }

        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'],
            $this->settings['height'],
            isset( $this->settings['smartCrop'] ) ? $this->settings['smartCrop'] : 'false'
        );
        
        $video_url  = get_post_meta( $this->slide->ID, 'ml-slider_video_url', true );
        $poster     = $imageHelper->get_image_url();
        $controls   = ! isset( $this->slide_settings['controls'] ) || filter_var(
            $this->slide_settings['controls'],
            FILTER_VALIDATE_BOOLEAN
        ) ? 'true' : 'false';

        $attributes = array(
            'class'             => "{$this->slug} video-js",
            'data-id'           => $this->slide->ID,
            'data-lazy-load'    => $this->setting_state( 'lazyLoad' ) ? 'true' : 'false',
            'data-url'          => $video_url,
            'data-type'         => $this->get_video_mime_type( $video_url ),
            'data-controls'     => $controls,
            'data-loop'         => $this->setting_state( 'loop' ) ? 'true' : 'false',
            'data-poster'       => $poster,
            'data-autoplay'     => $this->setting_state( 'autoPlay' ) ? 'true' : 'false',
            'data-mute'         => $this->setting_state( 'mute' ) ? 'true' : 'false',
            'style'             => 'position:relative;height:0;' . sprintf( 'padding-bottom:%s%%', $ratio )
        );

        // Track settings
        $attributes = $this->track_attributes( $attributes );

        // Link URL settings
        $attributes = $this->link_url_attributes( $attributes );

        $html = $attrs = "";
        foreach ( $attributes as $att => $val ) {
            $attrs .= " " . $att . '="' . esc_attr( $val ) . '"';
        }

        $html .= "<div{$attrs}>";
        
        if( $this->setting_state( 'lazyLoad' ) ) {
            $attachment_id  = $this->get_attachment_id();
            $alt            = esc_attr( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) );
            
            $html .= $this->temporary_video_image( $poster, $alt );
        }

        $html .= "</div>";

        return $html;
    }
}
