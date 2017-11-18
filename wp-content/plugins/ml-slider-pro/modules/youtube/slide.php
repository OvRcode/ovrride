<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 *
 */
class MetaYouTubeSlide extends MetaSlide {

    public $identifier = "youtube"; // should be lowercase, one word (use underscores if needed)
    public $name = "YouTube"; // slide type title

    /**
     * Register slide type
     */
    public function __construct() {

        if ( is_admin() ) {
            add_filter( 'media_upload_tabs', array( $this, 'custom_media_upload_tab_name' ), 999, 1 );
            add_action( "metaslider_save_{$this->identifier}_slide", array( $this, 'save_slide' ), 5, 3 );
            add_action( "media_upload_{$this->identifier}", array( $this, 'get_iframe' ) );
            add_action( "wp_ajax_create_{$this->identifier}_slide", array( $this, 'ajax_create_slide' ) );
            add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
        }

        add_filter( "metaslider_get_{$this->identifier}_slide", array( $this, 'get_slide' ), 10, 2 );
    }

    /**
     * Extract the slide setings
     */
    public function set_slide( $id ) {

        parent::set_slide( $id );
        $this->slide_settings = get_post_meta( $id, 'ml-slider_settings', true );

    }

    /**
     *
     */
    public function register_admin_styles() {

        wp_enqueue_style( "metasliderpro-{$this->identifier}-style", plugins_url( 'assets/style.css' , __FILE__ ), false, METASLIDERPRO_VERSION );

    }

    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     *
     * @var array existing media manager tabs
     * @return array tabs
     */
    public function custom_media_upload_tab_name( $tabs ) {

        // restrict our tab changes to the meta slider plugin page
        if ( ( isset( $_GET['page'] ) && $_GET['page'] == 'metaslider' ) ||
            ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], array( $this->identifier ) ) ) ) {

            $newtabs = array(
                $this->identifier => __( $this->name, 'metasliderpro' )
            );

            return array_merge( $tabs, $newtabs );
        }

        return $tabs;

    }

    /**
     * Create a new slide and echo the admin HTML
     */
    public function ajax_create_slide() {

        $slider_id = intval( $_POST['slider_id'] );
        $fields['menu_order'] = 9999;
        $fields['video_id'] = $_POST['video_id'];
        $this->create_slide( $slider_id, $fields );
        echo $this->get_admin_slide();
        die(); // this is required to return a proper result

    }

    /**
     * Media Manager Tab
     */
    public function youtube_tab() {
        return $this->get_iframe();
    }

    /**
     * Create a new YouTube slide
     *
     * @var int slider_id - slideshow ID
     * @var array fields - slide details
     */
    private function create_slide( $slider_id, $fields ) {
        $this->set_slider( $slider_id );

        $postinfo = array(
            'post_title'=> "Meta Slider - YouTube Thumbnail - {$fields['video_id']}",
            'post_mime_type' => 'image/jpeg',
            'post_status' => 'inherit',
            'post_content' => '',
            'guid' => "http://www.youtube.com/watch?v={$fields['video_id']}",
            'menu_order' => $fields['menu_order'],
            'post_name' => $fields['video_id']
        );

        $youtube_thumb = new WP_Http();
        $youtube_thumb = $youtube_thumb->request( "http://img.youtube.com/vi/{$fields['video_id']}/0.jpg" );

        if ( !is_wp_error( $youtube_thumb ) && isset( $youtube_thumb['response']['code'] ) && $youtube_thumb['response']['code'] == 200 ) {
            $attachment = wp_upload_bits( "youtube_{$fields['video_id']}.jpg", null, $youtube_thumb['body'] );
            $filename = $attachment['file'];
            $slide_id = wp_insert_attachment( $postinfo, $filename );
            $attach_data = wp_generate_attachment_metadata( $slide_id, $filename );
            wp_update_attachment_metadata( $slide_id,  $attach_data );
        } else {
            $slide_id = wp_insert_attachment( $postinfo );
        }

        if ( method_exists( $this, 'insert_slide' ) ) {
            $slide_id = $this->insert_slide($slide_id, $this->identifier, $slider_id);
            $this->add_or_update_or_delete_meta( $slide_id, 'youtube_url', "http://www.youtube.com/watch?v={$fields['video_id']}");
        } else {
            $this->add_or_update_or_delete_meta( $slide_id, 'type', $this->identifier );
        }

        // store the type as a meta field against the attachment
        $this->set_slide( $slide_id );
        $this->tag_slide_to_slider();

        return $slide_id;
    }

    /**
     * Admin slide html
     *
     * @return string html
     */
    protected function get_admin_slide() {
        $thumb = "";

        // only show a thumbnail if we managed to download one when the slide was created
        if ( get_post_thumbnail_id( $this->slide->ID ) ) {// new slide format
            $thumb = $this->get_thumb();
        } else if ( strlen( get_attached_file( $this->slide->ID ) ) ) {
            $thumb = $this->get_thumb();
        }

        $row  = "<tr class='slide {$this->identifier} flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$thumb})'>";

        if ( method_exists( $this, 'get_delete_button_html' ) ) {
            $row .= $this->get_delete_button_html();
        }

        if ( method_exists( $this, 'get_change_image_button_html' ) ) {
            $row .= $this->get_change_image_button_html();
        }

        $row .= "            <span class='slide-details'>" . __( "YouTube", 'metasliderpro' ) . "</span>";
        $row .= "            <span class='youtube'></span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";

        if ( method_exists( $this, 'get_admin_slide_tabs_html' ) ) {
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . __("Please update to Meta Slider to version 3.2 or above.", "metasliderpro") . "</p>";
        }

        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='youtube' />";
        $row .= "        <input type='hidden' class='menu_order' name='attachment[{$this->slide->ID}][menu_order]' value='{$this->slide->menu_order}' />";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs() {

        $slide_id = absint( $this->slide->ID );
        $showControls_checked = !isset( $this->slide_settings['showControls'] ) || $this->slide_settings['showControls'] == 'on' ? 'checked=checked' : '';
        $showRelated_checked = isset( $this->slide_settings['showRelated'] ) && $this->slide_settings['showRelated'] == 'on' ? 'checked=checked' : '';
        $autoHide_checked = !isset( $this->slide_settings['autoHide'] ) || $this->slide_settings['autoHide'] == 'on' ? 'checked=checked' : '';
        $autoPlay_checked = isset( $this->slide_settings['autoPlay'] ) && $this->slide_settings['autoPlay'] == 'on' ? 'checked=checked' : '';
        $showInfo_checked = isset( $this->slide_settings['showInfo'] ) && $this->slide_settings['showInfo'] == 'on' ? 'checked=checked' : '';
        $light_theme_selected = isset( $this->slide_settings['theme'] ) && $this->slide_settings['theme'] == 'light' ? 'selected' : '';
        $white_color_selected = isset( $this->slide_settings['color'] ) && $this->slide_settings['color'] == 'white' ? 'selected' : '';

        $general_tab = "<ul>
                            <li><label><input type='checkbox' name='attachment[{$slide_id}][settings][showInfo]' {$showInfo_checked}/>" . __( 'Show the title on the video', 'metasliderpro' ) ."</label></li>
                            <li><label><input type='checkbox' name='attachment[{$slide_id}][settings][showControls]' {$showControls_checked}/>" . __( 'Enable controls in the player', 'metasliderpro' ) ."</label></li>
                            <li><label><input type='checkbox' name='attachment[{$slide_id}][settings][showRelated]' {$showRelated_checked}/>" . __( 'Show related videos', 'metasliderpro' ) ."</label></li>
                            <li><label><input type='checkbox' name='attachment[{$slide_id}][settings][autoHide]' {$autoHide_checked}/>" . __( 'Hide video controls after the video begins', 'metasliderpro' ) ."</label></li>
                            <li><label><input type='checkbox' name='attachment[{$slide_id}][settings][autoPlay]' {$autoPlay_checked}/>" . __( 'Auto Play', 'metasliderpro' ) ."</label></li>
                        </ul>"; // vantage backwards compatibility";

        $theme_tab =   "<div class='row'>
                            <label>" . __( "Theme", 'metasliderpro' ) . "</label>
                            <select name='attachment[{$slide_id}][settings][theme]'>
                                <option value='dark'>" . __( 'Dark', 'metasliderpro' ) . "</option>
                                <option value='light' {$light_theme_selected}>" . __( 'Light', 'metasliderpro' ) . "</option>
                            </select>
                        </div>
                        <div class='row'>
                            <label>" . __( "Color", 'metasliderpro' ) . "</label>
                            <select name='attachment[{$slide_id}][settings][color]'>
                                <option value='red'>" . __( 'Red', 'metasliderpro' ) . "</option>
                                <option value='white' {$white_color_selected}>" . __( 'White', 'metasliderpro' ) . "</option>
                            </select>
                        </div>";

        $tabs = array(
            'general' => array(
                'title' => __( "General", "metasliderpro" ),
                'content' => $general_tab
            ),
            'theme' => array(
                'title' => __( "Theme", "metasliderpro" ),
                'content' => $theme_tab
            )
        );

        return apply_filters( "metaslider_youtube_slide_tabs", $tabs, $this->slide, $this->slider, $this->settings );

    }

    /**
     * Public slide html
     *
     * @return string html
     */
    protected function get_public_slide() {

        wp_enqueue_script( 'metasliderpro-youtube-api', plugins_url( 'assets/tubeplayer/jQuery.tubeplayer.min.js' , __FILE__ ), array( 'jquery' ) );

        if ( get_post_meta($this->slide->ID, 'ml-slider_youtube_url', true) ) {
            $url = get_post_meta($this->slide->ID, 'ml-slider_youtube_url', true);
        } else {
            $url = $this->slide->guid;
        }

        $url_parts = explode( "=", $url );
        $video_id = $url_parts[1];
        $ratio = $this->settings['height'] / $this->settings['width'] * 100;

        if ( $this->settings['type'] == 'responsive' ) {

            add_filter( 'metaslider_responsive_slider_parameters', array( $this, 'get_responsive_slider_parameters' ), 10, 2 );
            add_filter( 'metaslider_responsive_slider_javascript', array( $this, 'get_responsive_youtube_javascript' ), 10, 2 );

            return $this->get_video_markup( $video_id, $ratio );
        }

        if ( $this->settings['type'] == 'flex' ) {

            add_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ), 10, 2 );

            return $this->get_flex_slider_markup( $video_id, $ratio );

        }
    }

    /**
     *
     */
    public function get_flex_slider_markup( $video_id, $ratio ) {
        $html = $this->get_video_markup( $video_id, $ratio );

        // store the slide details
        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-youtube",
            'style' => "display: none; width: 100%;"
        );

        $attributes = apply_filters( 'metaslider_flex_slider_li_attributes', $attributes, $this->slide->ID, $this->slider->ID, $this->settings );

        $li = "<li";

        foreach ( $attributes as $att => $val ) {
            $li .= " " . $att . '="' . esc_attr( $val ) . '"';
        }

        $li .= ">" . $html . "</li>";

        $html = $li;

        return $html;
    }

    /**
     *
     */
    public function get_video_markup( $video_id, $ratio ) {
        $attrs = array(
            'style' => "position: relative; padding-bottom: {$ratio}%; height: 0;",
            'class' => "youtube",
            'data-id' => $video_id,
            'data-autoPlay' => isset( $this->slide_settings['autoPlay'] ) && $this->slide_settings['autoPlay'] == true ? "1" : "0",
            'data-showControls' => !isset( $this->slide_settings['showControls'] ) || $this->slide_settings['showControls'] == 'on' ? '1' : '0',
            'data-showRelated' => isset( $this->slide_settings['showRelated'] ) && $this->slide_settings['showRelated'] == 'on' ? '1' : '0',
            'data-showInfo' => isset( $this->slide_settings['showInfo'] ) && $this->slide_settings['showInfo'] == 'on' ? '1' : '0',
            'data-autoHide' => !isset( $this->slide_settings['autoHide'] ) || $this->slide_settings['autoHide'] == 'on' ? '1' : '0',
            'data-theme' => isset( $this->slide_settings['theme'] ) && $this->slide_settings['theme'] == 'light' ? "light" : "dark",
            'data-color' => isset( $this->slide_settings['color'] ) && $this->slide_settings['color'] == 'white' ? "white" : "red"
        );

        $html = "<div";

        foreach ( $attrs as $k => $v ) {
            $html .= " " . $k . '="' . $v . '"';
        }

        $html .= "></div>";

        return $html;
    }

    /**
     * Pause youtube videos when the slide is changed
     *
     * @var array options - JavaScript options
     * @var int slider_id - current slideshow ID
     */
    public function get_responsive_slider_parameters( $options, $slider_id ) {
        // disable hoverpause - there is a bug with flex slider that means it
        // resumes the slideshow even when it has just been told to pause
        if ( isset( $options["pause"] ) ) {
            unset( $options["pause"] );
        }

        // we cannot pause the slideshow automatically with responsive slides
        $options["auto"] = "false";

        $options["before"][] = "    $('#metaslider_{$slider_id} .youtube').each(function(index) {
                        if(typeof $(this).tubeplayer('data') !== 'undefined') {
                            if ($(this).tubeplayer('data').state == 1) {
                                $(this).tubeplayer('pause');
                            }
                        }
                    });";

        $options["after"][]  = "    $('#metaslider_{$slider_id} .rslides1_on .youtube[data-autoPlay=1]').each(function(index) {
                        $(this).tubeplayer('play');
                    });";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_responsive_slider_parameters', array( $this, 'get_responsive_slider_parameters' ) );

        return $options;
    }

    /**
     * Pause youtube videos when the slide is changed
     *
     * @var array options - JavaScript options
     * @var int slider_id - current slideshow ID
     */
    public function get_flex_slider_parameters( $options, $slider_id ) {
        $autoPlay = "";

        if ( $this->settings['autoPlay'] == 'true' ) {
            $autoPlay = ",\n                            onPlayerEnded: function(id) {";
            $autoPlay .= "\n                                $('#metaslider_{$slider_id}').data('flexslider').manualPause = false;";
            $autoPlay .= "\n                                $('#metaslider_{$slider_id}').flexslider('next');";
            $autoPlay .= "\n                                $('#metaslider_{$slider_id}').flexslider('play');";
            $autoPlay .= "\n                            },";
            $autoPlay .= "\n                            onPlayerPaused: function(id) {";
            $autoPlay .= "\n                                $('#metaslider_{$slider_id}').data('flexslider').manualPause = false;";
            $autoPlay .= "\n                            }";
        }

        $options["useCSS"] = "false";

        $options["before"][] = "    $('#metaslider_{$slider_id} .youtube').each(function(index) {
                        if(typeof $(this).tubeplayer('data') !== 'undefined') {
                            if($(this).tubeplayer('data').state == 1) {
                                $(this).tubeplayer('pause');
                            }
                        }
                    });";

        $options["after"][]  = "    $('#metaslider_{$slider_id} .flex-active-slide .youtube[data-autoPlay=1]').each(function(index) {
                        $(this).tubeplayer('play');
                    });";

        $options["start"][]  = "    $('#metaslider_{$slider_id} .youtube').each(function() {
                        var youtube = $(this);
                        var autoplay = false;
                        if (youtube.attr('data-autoPlay') === '1') {
                            if (youtube.parents('.flex-active-slide, .rslides1_on').length) {
                                autoplay = true;
                            }
                        }
                        $(this).tubeplayer({{$this->get_tubeplayer_params()}
                            onPlayerPlaying: function(id) {
                                $('#metaslider_{$slider_id} .flex-active-slide .youtube').attr('data-autoPlay', 0);
                                $('#metaslider_{$slider_id}').flexslider('pause');
                                $('#metaslider_{$slider_id}').data('flexslider').manualPause = true;
                                $('#metaslider_{$slider_id}').data('flexslider').manualPlay = false;
                            }{$autoPlay}
                        });
                    });";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ) );

        return $options;
    }

    /**
     * Return the javascript which creates the YouTube videos in the slideshow
     */
    public function get_responsive_youtube_javascript( $javascript, $slider_id ) {
        $html  = "\n            $('#metaslider_{$this->slider->ID} .youtube').each(function() {";
        $html .= "\n                var youtube = $(this);";
        $html .= "\n                var autoplay = false;";
        $html .= "\n                if (youtube.attr('data-autoPlay') === '1') {";
        $html .= "\n                    if (youtube.parents('.flex-active-slide, .rslides1_on').length) {";
        $html .= "\n                        autoplay = true;";
        $html .= "\n                    }";
        $html .= "\n                }";
        $html .= "\n                $(this).tubeplayer({{$this->get_tubeplayer_params()}";
        $html .= "\n                    onPlayerPlaying: function(id) {";
        $html .= "\n                        $('#metaslider_{$slider_id} .rslides1_on .youtube').attr('data-autoPlay', 0);";
        $html .= "\n                    }";
        $html .= "\n                });";
        $html .= "\n            });";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_responsive_slider_javascript', array( $this, 'get_youtube_javascript' ) );

        return $javascript . $html;
    }

    /**
     * Tubeplayer JavaScript options
     */
    private function get_tubeplayer_params() {
        $params = "";

        $tubeplayer_params = array(
            'width' => $this->settings['width'],
            'height' => $this->settings['height'],
            'protocol' =>  "'{$this->get_server_protocol()}'",
            'allowFullScreen' => "'true'",
            'preferredQuality' => "'hd720'",
            'initialVideo' => "youtube.attr('data-id')",
            'showControls' => "youtube.attr('data-showControls') === '1'",
            'showRelated' => "youtube.attr('data-showRelated') === '1'",
            'showinfo' => "youtube.attr('data-showInfo') === '1'",
            'autoHide' => "youtube.attr('data-autoHide') === '1'",
            'theme' => "youtube.attr('data-theme')",
            'color' => "youtube.attr('data-color')",
            'autoPlay' => "autoplay"
        );

        $tubeplayer_params = apply_filters( 'metaslider_tubeplayer_params', $tubeplayer_params, $this->slider->ID, $this->slide->ID );

        foreach ( $tubeplayer_params as $name => $value ) {
            $params .= "\n                            " . $name . ": " . $value . ",";
        }

        return $params;
    }

    /**
     * Detect if we're running through HTTP or HTTPS
     *
     * @return string protocol (http or https)
     */
    private function get_server_protocol() {
        if ( isset( $_SERVER['HTTPS'] ) && ( $_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1 ) || isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
            $protocol = 'https';
        } else {
            $protocol = 'http';
        }

        return apply_filters("metaslider_tubeplayer_protocol", $protocol);
    }

    /**
     * Return wp_iframe
     */
    public function get_iframe() {
        return wp_iframe( array( $this, 'iframe' ) );
    }

    /**
     * Media Manager iframe HTML
     */
    public function iframe() {

        do_action("metaslider_youtube_iframe");

        wp_enqueue_style( 'media-views' );
        wp_enqueue_style( "metasliderpro-{$this->identifier}-styles", plugins_url( 'assets/style.css' , __FILE__ ) );
        wp_enqueue_script( "metasliderpro-{$this->identifier}-script", plugins_url( 'assets/script.js' , __FILE__ ), array( 'jquery' ) );
        wp_localize_script( "metasliderpro-{$this->identifier}-script", 'metaslider_custom_slide_type', array(
                'identifier' => $this->identifier,
                'name' => $this->name
            ) );
        echo "<div class='metaslider'>
                <div class='youtube'>
                    <div class='media-embed'>
                        <label class='embed-url'>
                            <input type='text' placeholder='http://www.youtube.com/watch?v=J---aiyznGQ' class='youtube_url'>
                            <span class='spinner'></span>
                        </label>
                        <div class='embed-link-settings'></div>
                    </div>
                </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large' disabled='disabled'>" . __("Add to slider", "metasliderpro") . "</a>
                    </div>
                </div>
            </div>";
    }

    /**
     * Save
     */
    protected function save( $fields ) {
        wp_update_post( array(
                'ID' => $this->slide->ID,
                'menu_order' => $fields['menu_order']
            ) );

        foreach ( array( 'showControls', 'showRelated', 'showInfo', 'autoHide' ) as $setting ) {
            if ( !isset( $fields['settings'][$setting] ) ) {
                $fields['settings'][$setting] = 'off';
            }
        }

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'settings', $fields['settings'] );
    }
}
?>