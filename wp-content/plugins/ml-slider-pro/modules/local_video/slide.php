<?php

if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct access.' );
}

/**
 * Local Video Slide
 */
class MetaLocalVideoSlide extends MetaSlide
{

    public $identifier = 'local_video'; // should be lowercase, one word (use underscores if needed)
    public $name;
    public $slug = 'local-video';
    private $slide_settings;


    /**
     * Register slide type
     */
    public function __construct()
    {
        $this->name = __( 'Local Video', 'ml-slider-pro' );

        if ( is_admin() ) {
            add_action( "wp_ajax_create_{$this->identifier}_slide", array( $this, 'ajax_create_slide' ) );
            add_filter( 'media_view_strings', array( $this, 'custom_media_uploader_tabs' ), 10, 1 );
            add_action( 'metaslider_register_admin_scripts', array( $this, 'register_admin_scripts' ), 10, 1 );
            add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
        }

        add_action( 'wp_ajax_update_slide_video', array( $this, 'ajax_update_slide_video' ) );
        add_action( 'wp_ajax_update_slide_track', array( $this, 'ajax_update_slide_track' ) );
        add_action( 'wp_ajax_remove_slide_track', array( $this, 'ajax_remove_slide_track' ) );
        add_action( "metaslider_save_{$this->identifier}_slide", array( $this, 'save_slide' ), 5, 3 );
        add_filter( "metaslider_get_{$this->identifier}_slide", array( $this, 'get_slide' ), 10, 2 );
    }

    /**
     * Creates a new media manager tab
     *
     * @param array $strings registered media manager tabs
     *
     * @return array
     */
    public function custom_media_uploader_tabs( $strings )
    {
        $strings['insertLocalVideo'] = __( 'Local Video', 'ml-slider-pro' );
        return $strings;
    }

    /**
     * Registers and enqueues admin JavaScript
     */
    public function register_admin_scripts()
    {
        wp_enqueue_script(
            "metasliderpro-{$this->slug}-script",
            plugins_url( 'assets/local_video.js', __FILE__ ),
            array( 'metaslider-admin-script' ),
            METASLIDERPRO_VERSION,
            true
        );

        // Nonce loaded through metasliderpro-local-video-script localized script
        wp_localize_script( 
            "metasliderpro-{$this->slug}-script", 
            "metaslider_{$this->identifier}", 
            array( 
                'nonce' => wp_create_nonce( "metaslider_create_{$this->identifier}_nonce" ),
                'update_slide_nonce'    => wp_create_nonce( "metaslider_update_{$this->identifier}_nonce" ),
                'update_video_text'     => esc_html__( 'Select replacement video', 'ml-slider' ),
                'update_image_text'     => esc_html__( 'Select replacement cover', 'ml-slider' ),
                'update_text_text'      => esc_html__( 'Select replacement text track', 'ml-slider' )
            )
        );
    }

    /**
     * Registers and enqueues admin Styles
     */
    public function register_admin_styles()
    {
        wp_enqueue_style(
            "metasliderpro-local-video-style",
            plugins_url( 'assets/style.css', __FILE__ ),
            false,
            METASLIDERPRO_VERSION
        );

        $css = ".{$this->identifier}_slide video {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }
        .{$this->identifier}_slide input.video_url {
            width: 100%;
            min-width: 200px;
        }
        .{$this->identifier}_slide input.video_url[readonly] {
            color: #50575e;
        }";
        wp_add_inline_style( 'metaslider-admin-styles', $css );
    }

    /**
     * Extract the slide setings
     *
     * @param integer $id Slide ID
     */
    public function set_slide( $id )
    {
        parent::set_slide( $id );
        $this->slide_settings = get_post_meta( $id, 'ml-slider_settings', true );
    }

    /**
     * Create a new local video slide.
     */
    public function ajax_create_slide()
    {
        if ( ! isset($_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), "metaslider_create_{$this->identifier}_nonce" ) ) {
            wp_send_json_error( esc_html__( 'Invalid nonce', 'ml-slider-pro' ), 403 );
        }

        if ( ! isset( $_POST['slider_id'] ) || ! isset( $_POST['video_id'] ) ) {
            wp_send_json_error( esc_html__( 'Bad request', 'ml-slider-pro' ), 400 );
        }

        $video_id   = intval( $_POST['video_id'] );
        $slider_id  = intval( $_POST['slider_id'] );

        // Only allow mp4, webm and mov videos
        if( ! $this->video_type_is_allowed( $video_id ) ) {
            wp_send_json_error( 
                esc_html__( 'Not supported video format', 'ml-slider-pro' ),
                422
            );
        }

        // Set the slideshow (that this slide belongs to)
        $this->set_slider( $slider_id );

        // Create a new post for a slide
        $new_slide_id = $this->insert_slide( $video_id, 'local_video', $slider_id );

        // Save video id postmeta
        if( wp_attachment_is( 'video', $video_id ) ) {
            update_post_meta( $new_slide_id, 'ml-slider_video_id', $video_id );
        }

        // Set the slide
        $this->set_slide( $new_slide_id );

        // Tag the slide attachment to the slider tax category
        $this->tag_slide_to_slider();

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
     * Check if video type is allowed (mp4, webm and mov)
     * 
     * @param integer $video_id Video ID from Media library
     * 
     * @return boolean
     */
    protected function video_type_is_allowed( $video_id )
    {
        $allowed = array(
            'video/quicktime',
            'video/mp4',
            'video/webm'
        );

        if( ! in_array( get_post_mime_type( $video_id ), $allowed ) ) {
            return false;
        }

        return true;
    }

    /**
     * Check if text track is valid (txt and vtt)
     * 
     * @param integer $track_id Video ID from Media library
     * 
     * @return boolean
     */
    protected function track_is_allowed( $track_id )
    {
        $allowed = array(
            'text/plain',
            'text/vtt'
        );

        if ( ! in_array( get_post_mime_type( $track_id ), $allowed ) ) {
            return false;
        }

        return true;
    }

    /**
     * Return the admin slide HTML
     *
     * @return string
     */
    protected function get_admin_slide()
    {
        $video_id = $this->get_video_id();

        ob_start();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $this->get_delete_button_html();
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo "<button class='toolbar-button update-video alignright tipsy-tooltip-top' data-slide-type='local_video' data-button-text='" . esc_attr__( 'Update slide video', 'ml-slider' ) . "' title='" . esc_attr__( 'Update slide video', 'ml-slider' ) . "' data-slide-id='" . esc_attr( $this->slide->ID ) . "' data-attachment-id='" . esc_attr( $video_id ) . "'><i><svg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-edit-2'><polygon points='16 3 21 8 8 21 3 21 3 16 16 3'/></svg></i></button>";
        do_action('metaslider-slide-edit-buttons', $this->identifier, $this->slide->ID);
        $edit_buttons = ob_get_clean();

        

        $row = "<tr id='slide-" . esc_attr( $this->slide->ID ) . "' class='slide {$this->identifier}_slide flex'>";
        $row .= "    <td class='col-1'>";
        $row .= "       <div class='metaslider-ui-controls ui-sortable-handle'>";
        $row .= "           <h4 class='slide-details'>";
        $row .= esc_html__( 'Local Video Slide', 'ml-slider-pro' ) . " | ID: ". esc_html( $this->slide->ID );
        $row .= "           </h4>";
        if ( metaslider_this_is_trash( $this->slide ) ) {
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
        $row .= "       </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "       <div class='metaslider-ui-inner flex flex-col h-full'>";

        if ( method_exists( $this, 'get_admin_slide_tabs_html' ) ) {
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . esc_html__( 'Please update to MetaSlider to version 3.2 or above.', 'ml-slider-pro' ) . "</p>";
        }

        $row .= "        <input type='hidden' name='attachment[" . esc_attr( $this->slide->ID ) . "][type]' value='local_video' />";
        $row .= "        <input type='hidden' name='attachment[" . esc_attr( $this->slide->ID ) . "][menu_order]' class='menu_order' value='" . esc_attr( $this->slide->menu_order ) . "' />";
        $row .= "        <input type='hidden' name='resize_slide_id' data-slide_id='" . esc_attr( $this->slide->ID ) . "' data-width='" . esc_attr( $this->settings['width'] ) . "' data-height='" . esc_attr( $this->settings['height'] ) . "' />";
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
        $path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'tabs/';

        ob_start();
        include $path . 'general.php';
        $general_tab = ob_get_clean();

        ob_start();
        include $path . 'cover.php';
        $cover_tab = ob_get_clean();

        $track_tab = $this->track_tab_content();

        $tabs = array(
            'general' => array(
                'title' => esc_html__( 'General', 'ml-slider-pro' ),
                'content' => $general_tab
            ),
            'cover' => array(
                'title' => esc_html__( 'Cover', 'ml-slider-pro' ),
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
            "metaslider_{$this->identifier}_slide_tabs", 
            $tabs, 
            $this->slide, 
            $this->slider, 
            $this->settings 
        );
    }

    /**
     * Get track tab content
     * 
     * @since 2.30
     * 
     * @return html
     */
    public function track_tab_content()
    {
        // Track tab
        $track          = get_post_meta( $this->slide->ID, 'ml-slider_track', true );

        $track_label    = isset( $track['label'] ) ? $track['label'] : '';
        $track_kind     = isset( $track['kind'] ) ? $track['kind'] : 'captions';
        $track_lang     = isset( $track['lang'] ) ? $track['lang'] : '';

        // TXT or VTT url. e.g. 'http://lorem.any/path/to/file.vtt'
        $html   = $this->track_url_field();

        $html  .= '<div class="row">';
        $html  .= '     <div class="flex gap-4 mt-2">';

        // Language label. e.g. 'English'
        $html  .= '         <div style="width:145px">';
        $html  .= '             <div class="row mb-2">';
        $html  .= '                 <label class="tipsy-tooltip-top" title="' . 
                                        esc_attr( 'Which language is the source file?', 'ml-slider-pro' ) . '">' .
                                        __( 'Language label', 'ml-slider-pro' ) . '</label>';
        $html  .= '             </div>';
        $html  .= '             <input type="text" name="attachment[' . 
                                        esc_attr( $this->slide->ID ) . '][track][label]"';
        $html  .=               ' placeholder="' . 
                                        esc_attr__( 'For example: English', 'ml-slider-pro' ) . '"';
        $html  .=               ' value="' . esc_attr( $track_label ) . '">';
        $html  .= '         </div>';

        // Language kind
        $html  .= '         <div style="width:180px;">';
        $html  .= '             <div class="row mb-2">';
        $html  .= '                 <label class="tipsy-tooltip-top" title="' . 
                                        esc_attr( 'Choose the type of text track', 'ml-slider-pro' ) . '">' .
                                        __( 'Language kind', 'ml-slider-pro' ) . '</label>';
        $html  .= '             </div>';
        $html  .= '             <select name="attachment[' . 
                                    esc_attr( $this->slide->ID ) . '][track][kind]">';
        $html  .= '                 <option value="captions"' . 
                                        ( $track_kind === 'captions' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Captions', 'ml-slider-pro' ) . '</option>';
        $html  .= '                 <option value="chapters"' . 
                                        ( $track_kind === 'chapters' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Chapters', 'ml-slider-pro' ) . '</option>';
        $html  .= '                 <option value="descriptions"' . 
                                        ( $track_kind === 'descriptions' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Descriptions', 'ml-slider-pro' ) . '</option>';
        /*$html  .= '             <option value="metadata"' . 
                                    ( $track_kind === 'metadata' ? ' selected' : '' ) .'>' . 
                                    esc_html__( 'Metadata', 'ml-slider-pro' ) . '</option>';*/
        $html  .= '                 <option value="subtitles"' . 
                                        ( $track_kind === 'subtitles' ? ' selected' : '' ) .'>' . 
                                        esc_html__( 'Subtitles', 'ml-slider-pro' ) . '</option>';
        $html  .= '             </select>';
        $html  .= '         </div>';

        // Language code. e.g. 'en'
        $html  .= '         <div style="width:110px;">';
        $html  .= '             <div class="row mb-2">';
        $html  .= '                 <label class="tipsy-tooltip-top" title="' . 
                                        esc_attr( 'If the language is English, the code must be en', 'ml-slider-pro' ) . '">' .
                                        __( 'Language code', 'ml-slider-pro' )  . '</label>';
        $html  .= '             </div>';
        $html  .= '             <input style="width:105px;" type="text" name="attachment[' . 
                                        esc_attr( $this->slide->ID ) . '][track][lang]"';
        $html  .=               ' placeholder="' . 
                                        esc_attr__( 'For example: en', 'ml-slider-pro' ) . '"';
        $html  .=               ' value="' . esc_attr( $track_lang ) . '" maxlength="2">';
        $html  .= '         </div>';

        $html  .= '     </div>';
        $html  .= '</div>';

        return $html;
    }

    /**
     * Get track URL field
     * 
     * @since 2.30
     * 
     * @return html
     */
    protected function track_url_field()
    {
        $track      = get_post_meta( $this->slide->ID, 'ml-slider_track', true );
        $track_id   = isset( $track['id'] ) ? $track['id'] : '';
        $track_url  = $this->get_track( $track_id );

        $html   = '<div class="row mb-2">';
        $html  .= '    <label class="tipsy-tooltip-top" title="' . 
                        esc_attr( 'File must be TXT or VTT format', 'ml-slider-pro' ) . '">' .
                            __( 'Source', 'ml-slider-pro' ) . '</label>';
        $html  .= '</div>';
        $html  .= '<div class="row">';
        $html  .= '     <div class="flex">';
        $html  .= '         <button data-button-text="' . 
                                esc_attr__( 'Update slide text track', 'ml-slider' ) . '" data-slide-id="' . 
                                esc_attr( $this->slide->ID ) . '" data-attachment-id="' . 
                                esc_attr( $track_id ) . '" class="update-text-track button button-secondary flex-1">';
        $html  .=               esc_html__( 'Browse', 'ml-slider-pro' );
        $html  .= '         </button>';
        $html  .= '         <input class="track_url w-100 m-0 border-r-0 border-l-0" type="text" value="' . 
                                esc_url( $track_url ) . '" readonly>';
        $html  .= '         <button data-slide-id="' . 
                                esc_attr( $this->slide->ID ) . '" data-attachment-id="' . 
                                esc_attr( $track_id ) . '" class="remove-text-track button button-secondary ms-button-danger">';
        $html  .=               '<i><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></i>';
        $html  .= '         </button>';
        $html  .= '     </div>';
        $html  .= '</div>';

        return $html;
    }

    /**
     * Check if a URL ends with .vtt or .txt
     * 
     * @since 2.30
     *
     * @param string $url e.g. 'http://lorem.any/path/to/file.vtt'
     * 
     * @return bool
     */
    public function is_track_file( $url )
    {
        if ( ! empty( $url ) && filter_var( $url, FILTER_VALIDATE_URL ) ) {
            return (bool) preg_match('/\.(vtt|txt)$/i', $url);
        }
        return false;
    }

    /**
     * Save track data
     * 
     * @since 2.30
     *
     * @param array $fields Array of all the slide settings including regular settings and track data
     * 
     * @return array
     */
    public function save_track( $fields )
    {
        // Sanitize data
        foreach ( array( 'lang', 'label', 'kind' ) as $item ) {
            $fields['track'][$item] = sanitize_text_field( $fields['track'][$item] );
        }

        // We don't store the url for Local videos, but we do for External videos
        if ( isset( $fields['track']['url'] ) ) {
            $fields['track']['url'] = sanitize_url( $fields['track']['url'] );
        }

        // Make sure track id is not removed if exists in database. We use in Local videos only
        // @TODO - Check if we really use 'id'
        $data = get_post_meta( $this->slide->ID, 'ml-slider_track', true );
        if ( $data && isset( $data['id'] ) ) {
            $fields['track']['id'] = $data['id'];
        }

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'track', $fields['track'] );
    }

    /**
     * Add track attributes to $attributes array
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

        // If slide doesn't have a track id, we stop the process here
        if ( ! isset( $track['id'] ) ) {
            return $attributes;
        }

        $track_url = $this->get_track( $track['id'] );

        $attributes['data-track-url']           = $track_url;
        $attributes['data-track-lang']          = ! empty( $track['lang'] ) 
                                                ? $track['lang']  : 'en';
        $attributes['data-track-label']         = ! empty( $track['label'] ) 
                                                ? $track['label'] : esc_attr__( 'English' );
        $attributes['data-track-kind']          = ! empty( $track['kind'] ) 
                                                ? $track['kind'] : esc_attr__( 'captions' );

        return $attributes;
    }

    /**
     * Add Link URL attributes to $attributes array
     * 
     * @since 2.32
     *
     * @param array $attributes The existing attributes including data-id, data-loop, data-autoplay, etc.
     * 
     * @return array
     */
    public function link_url_attributes( $attributes )
    {
        $url = get_post_meta( $this->slide->ID, 'ml-slider_url', true );

        // If slide doesn't have a Link URL, we stop the process here
        if ( ! $url ) {
            return $attributes;
        }

        $attributes['data-link-url']    = esc_url( $url );
        $attributes['data-link-target'] = get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) ? '_blank' : '_self';
    
        return $attributes;
    }

    /**
     * Save
     *
     * @param array $fields Slide fields
     */
    protected function save( $fields )
    {
        // Update the order
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order']
        ));

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
            if ( ! isset($fields['settings'][$setting] ) ) {
                $fields['settings'][$setting] = 'off';
            }
        }

        // Save all the settings fields serialized
        if ( isset( $fields['settings'] ) ) {
            $this->add_or_update_or_delete_meta( $this->slide->ID, 'settings', $fields['settings'] );
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
    }

    /**
     * Get the video for the slide
     */
    protected function get_video( $id = false )
    {
        $video_id = $id ? $id : $this->get_video_id();

        if( $video_id && wp_attachment_is( 'video', $video_id ) ) {
            $video_url = wp_get_attachment_url( $video_id );
        }

        if ( isset( $video_url ) ) {
            return $video_url;
        }

        return '';
    }

    /**
     * Get the post id from a video
     */
    protected function get_video_id()
    {
        $video_id = get_post_meta( $this->slide->ID, 'ml-slider_video_id', true );

        if ( isset( $video_id ) ) {
            return absint( $video_id );
        }

        return false;
    }

    /**
     * Get the text track for the slide
     * 
     * @since 2.30
     * 
     * @param int $track_id The id of the text track file
     * 
     * @return string
     */
    protected function get_track( $track_id )
    {
        if ( $this->track_is_allowed( $track_id ) ) {
            $track_url = wp_get_attachment_url( $track_id );
        }

        if ( isset( $track_url ) ) {
            return $track_url;
        }

        return '';
    }

    /**
     * Ajax wrapper to update the slide video.
     *
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_update_slide_video()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
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

        if ( ! isset( $_POST['slide_id'] ) || ! isset($_POST['video_id']) || ! isset( $_POST['slider_id'] ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        // Only allow mp4, webm and mov videos
        if( ! $this->video_type_is_allowed( absint( $_POST['video_id'] ) ) ) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'Not supported video format', 'ml-slider-pro' )
                ],
                422
            );
        }

        $result = $this->update_slide_video(
            absint( $_POST['slide_id'] ),
            absint( $_POST['video_id'] ),
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
     * @param int $video_id     The id of the new video to use
     * @param int $slideshow_id The id of the slideshow
     *
     * @return array|WP_error The status message and if success, the thumbnail link
     */
    protected function update_slide_video( $slide_id, $video_id, $slideshow_id = null )
    {   
        /*
         * Verifies that the $video_id is an actual video
         */
        if ( ! wp_attachment_is( 'video', $video_id ) ) {
            return new WP_Error( 
                'update_failed', 
                __( 'The requested video does not exist. Please try again.', 'ml-slider' ), 
                array( 'status' => 409 )
            );
        }

        /*
        * Updates database record and thumbnail if selection changed, assigns it to the slideshow, crops the image
        */
        update_post_meta( $slide_id, 'ml-slider_video_id', $video_id );
        if ( $slideshow_id ) {
            $this->set_slider( $slideshow_id );

            return array(
                'message' => __( 'The video was successfully updated.', 'ml-slider' ),
                'video_url' => $this->get_video( $video_id ),
                'video_type' => get_post_mime_type( $video_id )
            );
        }

        return new WP_Error( 
            'update_failed', 
            __( 'There was an error updating the video. Please try again', 'ml-slider' ), 
            array( 'status' => 409 ) 
        );
    }
    
    /**
     * Ajax wrapper to update the slide text track.
     *
     * @since 2.30
     * 
     * @return String The status message and if success, the thumbnail link (JSON)
     */
    public function ajax_update_slide_track()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
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

        if ( ! isset( $_POST['slide_id'] ) || ! isset($_POST['track_id']) || ! isset( $_POST['slider_id'] ) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        // Only allow mp4, webm and mov videos
        if( ! $this->track_is_allowed( absint( $_POST['track_id'] ) ) ) {
            wp_send_json_error( 
                [
                    'message' => esc_html__( 'Not supported text track format', 'ml-slider-pro' )
                ],
                422
            );
        }

        $result = $this->update_slide_track(
            absint( $_POST['slide_id'] ),
            absint( $_POST['track_id'] ),
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
     * @since 2.30
     * 
     * @param int $slide_id     The id of the slide being updated
     * @param int $track_id     The id of the new video to use
     * @param int $slideshow_id The id of the slideshow
     *
     * @return array|WP_error The status message and if success, the JSON response
     */
    protected function update_slide_track( $slide_id, $track_id, $slideshow_id = null )
    {   
        /*
         * Verifies that the $track_id is a valid file
         */
        $mime_type = get_post_mime_type( $track_id );
        $supported_mime_types = array(
            'text/plain',
            'text/vtt'
        );
        if ( ! in_array( $mime_type, $supported_mime_types) ) {
            return new WP_Error( 
                'update_failed', 
                __( 'The requested text track does not exist. Please try again.', 'ml-slider-pro' ), 
                array( 'status' => 409 )
            );
        }

        /*
         * Updates database record
         */
        $data = get_post_meta( $slide_id, 'ml-slider_track', true );
        if ( ! $data ) {
            $data = array();
        }

        $data['id'] = $track_id;
        
        $this->add_or_update_or_delete_meta( $slide_id, 'track', $data );

        if ( $slideshow_id ) {
            $this->set_slider( $slideshow_id );

            return array(
                'message'       => __( 'The text track was successfully updated.', 'ml-slider-pro' ),
                'track_id'      => $track_id,
                'track_url'     => $this->get_track( $track_id ),
                'track_type'    => $mime_type
            );
        }

        return new WP_Error( 
            'update_failed', 
            __( 'There was an error updating the text track. Please try again', 'ml-slider-pro' ), 
            array( 'status' => 409 ) 
        );
    }

    /**
     * Ajax wrapper to remove the slide text track.
     *
     * @since 2.30
     * 
     * @return String The status message and if success, the JSON response
     */
    public function ajax_remove_slide_track()
    {
        if ( ! isset( $_REQUEST['_wpnonce'] ) 
            || ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), "metaslider_update_{$this->identifier}_nonce") ) {
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

        if ( ! isset( $_POST['slide_id'] ) || ! isset($_POST['track_id']) ) {
            wp_send_json_error(
                [
                    'message' => __( 'Bad request', 'ml-slider' ),
                ],
                400
            );
        }

        $slide_id = absint( $_POST['slide_id'] );
        
        // Updates database record
        $data = get_post_meta( $slide_id, 'ml-slider_track', true );
        unset( $data['id'] );
        
        $this->add_or_update_or_delete_meta( $slide_id, 'track', $data );

        $result = array(
            'message'   => __( 'The text track was successfully removed.', 'ml-slider-pro' ),
            'slide_id'  => $slide_id
        );

        if ( is_wp_error( $result ) ) {
            wp_send_json_error( array(
                'message' => $result->get_error_message()
            ), 409 );
        }
        wp_send_json_success ( $result, 200 );
    }

    /**
     * Public slide html
     *
     * @return string
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

        $settings   = get_post_meta( $this->slider->ID, 'ml-slider_settings', true );
        $video_id   = get_post_meta( $this->slide->ID, 'ml-slider_video_id', true );
        $video_url  = $this->get_video( $video_id );
         
        if (! (int)$this->settings['height'] || ! (int)$this->settings['width']) {
            $ratio = 9 / 16 * 100;
        } else {
            $ratio = $this->settings['height'] / $this->settings['width'] * 100;
        }

        // Flexslider
        if ( $this->settings['type'] == 'flex' ) {
            add_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ), 10, 2 );

            return $this->get_flex_slider_markup( $video_id, $settings, $ratio );
        }
    }

    /**
     * Add inline styles used by videos
     */
    public function add_extra_styles()
    {
        $css  = ".metaslider .ms-{$this->slug} .play_button{position:absolute;top:0;left:0;width:100%;height:100%;display:flex;align-items:center;justify-content:center}";
        $css .= ".metaslider .ms-{$this->slug} .play_button img{width:75px;cursor:pointer;opacity:0.8}";
        $css .= ".metaslider .ms-{$this->slug} .play_button img:hover{opacity:1}";
        $css .= ".metaslider .ms-{$this->slug} div.video-js{position:absolute;top:0;left:0}";

        $css = apply_filters( "metaslider_{$this->identifier}_inline_css", $css, $this->slide, $this->slider->ID );
        wp_add_inline_style( 'metaslider-public', $css );
    }

    /**
     * Return the slide HTML for flex slider
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
     * Return the video wrapper with custom data attributes
     *
     * @return string
     */
    private function video_wrapper()
    {
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
        
        $video_id       = $this->get_video_id();
        $poster         = $imageHelper->get_image_url();
        $controls       = ! isset( $this->slide_settings['controls'] ) || filter_var(
            $this->slide_settings['controls'],
            FILTER_VALIDATE_BOOLEAN
        ) ? 'true' : 'false';

        $attributes = array(
            'class'             => "{$this->slug} video-js",
            'data-id'           => $this->slide->ID, // Slide ID, not video ID!
            'data-lazy-load'    => $this->setting_state( 'lazyLoad' ) ? 'true' : 'false',
            'data-url'          => $this->get_video(),
            'data-type'         => get_post_mime_type( $video_id ),
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

    /**
     * Return the thumb video embed for admin 
     * 
     * @since 2.32
     * 
     * @return string
     */
    public function get_admin_video_thumb( $url, $type )
    {
        $html = '';

        if( isset( $url ) && ! empty( $url ) && $type != '' ) {
            $html  = '<video loop muted';
            $html .= '   onmouseover="this.play()" onmouseout="this.pause()">';
            $html .= '    <source src="' . esc_url( $url ) . '"';
            $html .= '       type="' . esc_attr( $type ) . '"></source>';
            $html .= '</video>';
        } 

        return $html;
    }

    /**
     * Get the title of the video
     *
     * @param int|string $url - Image URL
     * @param int|string $alt - Image alt
     * @return string
     */
    public function temporary_video_image( $url, $alt )
    {
        $html = "";

        if( ! empty( $url ) ) {
            $attributes = array(
                'src' => $url,
                'alt' => $alt,
                'title' => '',
                'class' => 'msDefaultImage',
                'height' => $this->settings['height'],
                'width' => $this->settings['width']
            );

            $html .= $this->build_image_tag( $attributes );
        }

        // Display button when controls are enabled
        if( ! isset( $this->slide_settings['controls'] ) || filter_var(
            $this->slide_settings['controls'],
            FILTER_VALIDATE_BOOLEAN
        ) ) {
            $html .= "<button class='vjs-big-play-button' type='button' title='Play Video' aria-disabled='false'>";
            $html .= "  <span class='vjs-icon-placeholder' aria-hidden='true'></span>";
            $html .= "  <span class='vjs-control-text' aria-live='polite'>Play Video</span>";
            $html .= "</button>";
        }

        return $html;
    }

    /**
     * A setting is enabled or not?
     * 
     * @deprecated since 2.30 - Use setting_state() instead.
     * 
     * @param string $setting The name of the slide setting. e.g. 'autoPlay'
     * 
     * @return boolean
     */
    private function get_setting_state( $setting )
    {
        $this->setting_state( $setting );
    }

    /**
     * A setting is enabled or not?
     * 
     * @since 2.30
     * 
     * @param string $setting The name of the slide setting. e.g. 'autoPlay'
     * 
     * @return boolean
     */
    public function setting_state( $setting )
    {
        return isset( $this->slide_settings[$setting] ) && filter_var(
            $this->slide_settings[$setting],
            FILTER_VALIDATE_BOOLEAN
        ) ? true : false;
    }

    /**
     * Pause videos when the slide is changed
     *
     * @param array $options - current slideshow options
     * @param int $slider_id - current slideshow ID
     * @return array
     */
    public function get_flex_slider_parameters( $options, $slider_id )
    {
        $addActiveClass = "";

        // Add active slide class when carousel mode is enabled
        if ( 'true' == $this->settings['carouselMode'] ) {
            $addActiveClass = "$(slider).find('.slides > li').removeClass('flex-active-slide').eq(slider.currentSlide).addClass('flex-active-slide');";
        }

        /* This is for the slideshow autoplay, not the video autoplay.
         * This will play the slideshow when the video is paused
         * 
         * @TODO - Minify this output
         */
        $autoplay = filter_var( $this->settings['autoPlay'], FILTER_VALIDATE_BOOLEAN ) ?
            "player.on('ended', function() {
                $('#metaslider_{$slider_id}').data('flexslider').manualPause = false;
                $('#metaslider_{$slider_id}').flexslider('next');
            	$('#metaslider_{$slider_id}').flexslider('play');
			});
            player.on('pause', function() {
                $('#metaslider_{$slider_id}').data('flexslider').manualPause = false;
			});" : '';

        /* For slides with lazyLoad enabled, we need to detect touch devices (aka mobile) 
         * through isTouch_{$this->identifier} variable to use 'touchstart' event once as trigger 
         * due 'click' is detected until a second tap
         * 
         * @since 2.30 */
        $touchStarted = "if(eventType === 'touchstart' && !touchStarted) {
            touchStarted = true;
        } else if(eventType === 'touchstart' && touchStarted) {
            return;
        }";

        /* When the slideshow is loaded / first slide
         * To check text tracks in browser console: 
         * videojs.players.{player_id}.textTracks().tracks_ */
        $options['start'] = isset( $options['start'] ) ? $options['start'] : array();
        $options['start'] = array_merge($options['start'], array(
            "{$addActiveClass}
            var isTouch_{$this->identifier} = 'ontouchstart' in document.documentElement;

            $('#metaslider_{$slider_id} .{$this->slug}').each(function(instance) {
                var video = $(this);
                var id = 'ms_videojs_' + video.data('id') + '_' + instance;

                video.data('instance', instance);
                
                var active = video.parent('.flex-active-slide').length;
                var autoplay = video.data('autoplay') && active ? true : false;
                var lazyload = video.data('lazyLoad');
                var eventType = lazyload 
                            ? isTouch_{$this->identifier} ? 'touchstart' : 'click'
                            : 'metaslider/load-{$this->slug}';
                var track = video.data('track-url') && video.data('track-url').length ? video.data('track-url') : false;
                var crossorigin = video.data('track-crossorigin') ? ' crossorigin=\"anonymous\"' : '';
                var haveLink = video.data('link-url') ? true : false;
                var touchStarted = false;

                video.on(eventType, function(){
                    {$touchStarted}

                    if(typeof videojs.players[id] === 'undefined') {
                        $(this).append('<video playsinline class=\"video-js\" id=\"' + id + '\"' + crossorigin + '></video>');

                        var player = videojs(id, {
                            controls: $(this).data('controls'),
                            muted: $(this).data('mute'),
                            poster: $(this).data('poster'),
                            fill: true,
                            loop: $(this).data('loop'),
                            sources: [{
                                src: $(this).data('url'),
                                type:$(this).data('type')
                            }],
                            userActions: {
                                click: video.data('link-url') ? false : true
                            }
                        });

                        if (haveLink) {
                            var link = $('<a></a>').attr({
                                'href': video.data('link-url'),
                                'target': video.data('link-target')
                            });
                            video.wrap(link);
                        }

                        var trackOpts = track
                            ? {
                                src: $(this).data('track-url'),
                                kind: $(this).data('track-kind'),
                                label: $(this).data('track-label'),
                                language: $(this).data('track-lang'),
                                default: true,
                                mode: 'showing'
                            } : false;

                        if (active && autoplay) {
                            $('#metaslider_{$slider_id}').flexslider('pause');
                            player.options_.autoplay = autoplay;
                            player.muted(true);
                        }
                        
                        player.on('click', function() {
                            if (haveLink) {
                                player.pause();
                            }
                        });

                        player.on('loadedmetadata', function() {
                            if (trackOpts) player.addRemoteTextTrack(trackOpts);
                            if (eventType === 'click' || eventType === 'touchstart') player.play();
                        });
                        player.on('play', function() {
                            $('#metaslider_{$slider_id}').flexslider('pause');
                            $('#metaslider_{$slider_id}').data('flexslider').manualPause = true;
                            $('#metaslider_{$slider_id}').data('flexslider').manualPlay = false;
                        });
                        {$autoplay}
                    }
                });
                if (lazyload && autoplay) $(this).trigger(eventType);
                lazyload || $(this).trigger('metaslider/load-{$this->slug}');
            });"
        ) );

        /* Before a slide transitions
         * Access the player already created in start event globally with videojs.players[id] 
         * to not generate new video instances of the same video */
        $options['before'] = isset( $options['before'] ) ? $options['before'] : array();
        $options['before'] = array_merge( $options['before'], array(
            "$('#metaslider_{$slider_id} .flex-active-slide .{$this->slug}').each(function(i) {
                var id = 'ms_videojs_' + $(this).data('id') + '_' + $(this).data('instance');

                if(typeof videojs.players[id] === 'undefined') return;

                var player = videojs.players[id];
                if ($(this).data('mute')) player.muted(true);
                player.pause();

                if($(this).data('autoplay')) $(this).data('st', false);
            });"
        ) );

        /* After a slide transitions
         * Access the player already created in start event globally with videojs.players[id] 
         * to not generate new video instances of the same video
         * 
         * We use $(this).data('st') to autoplay videos only once
         * when slide is active and avoid triggering mute and autoplay 
         * when user decides to pause or unmute
         * https://github.com/MetaSlider/metaslider-pro/issues/265 */
        $options['after'] = isset( $options['after'] ) ? $options['after'] : array();
        $options['after'] = array_merge($options['after'], array(
            "{$addActiveClass}
            var isTouch_{$this->identifier} = 'ontouchstart' in document.documentElement;
            var eventType = isTouch_{$this->identifier} ? 'touchstart' : 'click';

            $('#metaslider_{$slider_id} .flex-active-slide .{$this->slug}').each(function(i) {
                var id = 'ms_videojs_' + $(this).data('id') + '_' + $(this).data('instance');
                
                if ($(this).data('autoplay') && !$(this).data('st')) {
                    $('#metaslider_{$slider_id}').flexslider('pause');
                    $(this).data('st', true);
                        
                    if ( $(this).data('lazyLoad') && typeof videojs.players[id] === 'undefined') {
                        $(this).trigger(eventType);
                    } else {
                        var player = videojs.players[id];
                        player.muted(true);
                        player.play();
                    }
                }
            });"
        ) );

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ) );
        return $options;
    }
}
