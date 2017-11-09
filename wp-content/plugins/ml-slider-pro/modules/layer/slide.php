<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * HTML Overlay Slide - HTML placed over an image.
 *
 * Renamed on the public side to "Layer Slide"
 */
class MetaLayerSlide extends MetaSlide {

    public $identifier = "html_overlay"; // should be lowercase, one word (use underscores if needed)
    public $name = "Layer Slide"; // slide type title

    /**
     * Register slide type
     */
    public function __construct() {

        if ( is_admin() ) {
            add_filter( "metaslider_advanced_settings", array( $this, 'add_downscale_only_setting' ), 10, 2 );
            add_action( "metaslider_save_{$this->identifier}_slide", array( $this, 'save_slide' ), 5, 3 );
            add_action( "wp_ajax_create_{$this->identifier}_slide", array( $this, 'ajax_create_slide' ) );
            add_action( "metaslider_register_admin_scripts", array( $this, 'register_admin_scripts' ), 10, 1 );
            add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
            add_filter( 'media_view_strings', array( $this, 'custom_media_uploader_tabs' ), 10, 1 );
        }

        add_filter( "metaslider_get_{$this->identifier}_slide", array( $this, 'get_slide' ), 10, 2 );
    }

    /**
     *
     */
    public function add_downscale_only_setting( $aFields, $slider ) {
        $newFields = array(
            'layer_scaling' => array(
                'priority' => 240,
                'type' => 'select',
                'value' => $slider->get_setting( 'layer_scaling' ),
                'label' => __( "Layer Scaling", "metasliderpro" ),
                'class' => 'effect flex responsive',
                'helptext' => __( "Select responsiver layer scaling behaviour", "metasliderpro" ),
                'options' => array(
                    'up_and_down'             => array( 'class' => 'option flex responsive' , 'label' => __( "Scale Up & Down", "metaslider" ) ),
                    'down_only'              => array( 'class' => 'option flex responsive', 'label' => __( "Scale Down Only", "metaslider" ) )
                ),
            )
        );

        return array_merge( $aFields, $newFields );
    }
    /**
     * Creates a new media manager tab
     *
     * @param array   $strings registered media manager tabs
     *
     * @return array
     */
    public function custom_media_uploader_tabs( $strings ) {
        $strings['insertHtmlOverlay'] = __( 'Layer Slide', 'metasliderpro' );
        return $strings;
    }

    /**
     * Registers and enqueues admin CSS
     */
    public function register_admin_styles() {
        wp_enqueue_style( 'metasliderpro-codemirror-style', plugins_url( 'assets/codemirror/lib/codemirror.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
        wp_enqueue_style( 'metasliderpro-codemirror-theme-style', plugins_url( 'assets/codemirror/theme/monokai.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
        wp_enqueue_style( "metasliderpro-{$this->identifier}-style", plugins_url( 'assets/style.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
        wp_enqueue_style( 'metasliderpro-spectrum-style', plugins_url( 'assets/spectrum/spectrum.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
    }

    /**
     * Registers and enqueues admin JavaScript
     */
    public function register_admin_scripts() {
        wp_enqueue_style( 'metasliderpro-spectrum-style', METASLIDERPRO_ASSETS_URL . 'spectrum/spectrum.css', false, METASLIDERPRO_VERSION );

        wp_enqueue_script( 'metasliderpro-html-overlay-script', plugins_url( 'assets/html_overlay.js' , __FILE__ ), array( 'metaslider-admin-script' ), METASLIDERPRO_VERSION, true );
        wp_enqueue_script( 'metasliderpro-layer-editor-script', plugins_url( 'assets/layer_editor.js' , __FILE__ ), array( 'metaslider-admin-script' ), METASLIDERPRO_VERSION, true );
        wp_enqueue_script( 'metasliderpro-codemirror-lib', plugins_url( 'assets/codemirror/lib/codemirror.js' , __FILE__ ), array(), METASLIDERPRO_VERSION );
        wp_enqueue_script( 'metasliderpro-codemirror-xml', plugins_url( 'assets/codemirror/mode/xml/xml.js' , __FILE__ ), array(), METASLIDERPRO_VERSION );
        wp_enqueue_script( 'jquery-ui-resizable' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'ckeditor', plugins_url( 'assets/ckeditor/ckeditor.js' , __FILE__ ), array(), METASLIDERPRO_VERSION );
        wp_enqueue_script( 'metasliderpro-spectrum', plugins_url( 'assets/spectrum/spectrum.js' , __FILE__ ), array(), METASLIDERPRO_VERSION );


        $colors = apply_filters( "metaslider_layer_editor_colors", "" );

        if (strlen($colors) && $colors[0] != ',') {
            $colors = "," . $colors;
        }

        // localise the JS
        wp_localize_script( 'metasliderpro-layer-editor-script', 'metasliderpro', array(
                'newLayer' => __( "New Layer", 'metasliderpro' ),
                'addLayer' => __( "Add Layer", 'metasliderpro' ),
                'duplicateLayer' => __( "Duplicate Layer", 'metasliderpro' ),
                'save' => __( "Save", 'metasliderpro' ),
                'saveChanges' => __( "Save changes?", 'metasliderpro' ),
                'animation' => __( "Animation", 'metasliderpro' ),
                'styling' => __( "Styling", 'metasliderpro' ),
                'px' => __( "px", 'metasliderpro' ),
                'animation' => __( "Animation", 'metasliderpro' ),
                'wait' => __( "Wait", 'metasliderpro' ),
                'thenWait' => __( "then wait", 'metasliderpro' ),
                'secondsAnd' => __( "seconds and", 'metasliderpro' ),
                'padding' => __( "Padding", 'metasliderpro' ),
                'background' => __( "Background", 'metasliderpro' ),
                'areYouSure' => __( "Are you sure?", 'metasliderpro' ),
                'snapToGrid' => __( "Snap to grid", 'metasliderpro' ),
                'insertIntoLayer' => __( "Insert into layer", 'metasliderpro' ),
                'insertFromMediaLibrary' => __( "Insert image from media library", 'metasliderpro' ),
                'layerLink' => __( "Link to", 'metasliderpro' ),
                'ck_editor_font_list' => apply_filters( "metaslider_layer_editor_fonts", "" ),
                'ck_editor_font_sizes' => apply_filters( "metaslider_layer_editor_font_sizes", "" ),
                'ck_editor_colors' => $colors,
                'setWidth' => __( "Please set a width in the slideshow settings", 'metasliderpro' ),
                'setHeight' => __( "Please set a height in the slideshow settings", 'metasliderpro' ),
                'addToSlider' => __( "Add to slider", 'metasliderpro' ),
                'noLayerSelected' => __( "Warning: No layer selected. Please click on a layer to select it before applying changes.", 'metasliderpro')
            ) );
    }

    /**
     * Create a new layer slide.
     *
     * @return string - HTML for the created slide
     */
    public function ajax_create_slide() {

        $slide_id = intval( $_POST['slide_id'] );
        $slider_id = intval( $_POST['slider_id'] );

        $this->set_slider( $slider_id );

        if ( method_exists( $this, 'insert_slide' ) ) { // Meta Slider 3.5+

            $new_slide_id = $this->insert_slide($slide_id, 'html_overlay', $slider_id);

        } else { // backwards compatibility

            // duplicate the attachment - get the source slide
            $attachment = get_post( $slide_id, ARRAY_A );
            unset( $attachment['ID'] );
            unset( $attachment['post_parent'] );
            unset( $attachment['post_date'] );
            unset( $attachment['post_date_gmt'] );
            unset( $attachment['post_modified'] );
            unset( $attachment['post_modified_gmt'] );

            $attachment['post_title'] = 'Meta Slider - HTML Overlay - ' . $attachment['post_title'];

            // insert a new attachment
            $new_slide_id = wp_insert_post( $attachment );

            // copy over the custom fields
            $custom_fields = get_post_custom( $slide_id );

            foreach ( $custom_fields as $key => $value ) {
                if ( $key != '_wp_attachment_metadata' ) {
                    update_post_meta( $new_slide_id, $key, $value[0] );
                }
            }

            // update metadata (regen thumbs also)
            $data = wp_get_attachment_metadata( $slide_id );

            wp_update_attachment_metadata( $new_slide_id, $data );

            // store the file type
            $this->add_or_update_or_delete_meta( $new_slide_id, 'type', 'html_overlay' );
        }

        // set current slide to our new slide
        $this->set_slide( $new_slide_id );

        // tag the new slide to the slider
        $this->tag_slide_to_slider();

        // finally, return the admin table row HTML
        echo $this->get_admin_slide();

        wp_die();
    }

    /**
     * Return the admin slide HTML
     *
     * @return string
     */
    protected function get_admin_slide() {
        $thumb       = $this->get_thumb();

        $row  = "<tr class='slide layer_slide flex responsive'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$thumb})'>";

        if ( method_exists( $this, 'get_delete_button_html' ) ) {
            $row .= $this->get_delete_button_html();
        }

        if ( method_exists( $this, 'get_change_image_button_html' ) ) {
            $row .= $this->get_change_image_button_html();
        }

        $row .= "            <span class='slide-details'>" . __( "Layer Slide", 'metasliderpro' ) . "</span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";

        if ( method_exists( $this, 'get_admin_slide_tabs_html' ) ) {
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . __("Please update to Meta Slider to version 3.2 or above.", "metasliderpro") . "</p>";
        }

        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='html_overlay' />";
        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][menu_order]' class='menu_order' value='{$this->slide->menu_order}' />";
        $row .= "        <input type='hidden' name='resize_slide_id' data-slide_id='{$this->slide->ID}' data-width='{$this->settings['width']}' data-height='{$this->settings['height']}' />";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs() {

        $slide_id = absint( $this->slide->ID);

        $html = get_post_meta( $this->slide->ID, 'ml-slider_html', true );
        $url = esc_attr( get_post_meta( $this->slide->ID, 'ml-slider_url', true ) );
        $webm = esc_attr( get_post_meta( $this->slide->ID, 'ml-slider_webm', true ) );
        $mp4 = esc_attr( get_post_meta( $this->slide->ID, 'ml-slider_mp4', true ) );
        $title = esc_attr( get_post_meta( $this->slide->ID, 'ml-slider_title', true ) );
        $alt = esc_attr( get_post_meta( $this->slide->ID, '_wp_attachment_image_alt', true ) );
        $target = get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) == 'true' ? 'checked=checked' : '';

        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'],
            $this->settings['height'],
            isset( $this->settings['smartCrop'] ) ? $this->settings['smartCrop'] : 'false'
        );

        $background_url = $imageHelper->get_image_url();

        $general_tab = "<button class='openLayerEditor button button-primary' data-thumb='{$background_url}' data-width='{$this->settings['width']}' data-height='{$this->settings['height']}' data-editor_id='editor{$slide_id}'>Launch Layer Editor</button>
                        <div class='rawEdit'></div>"; // vantage backwards compatibility";

        $seo_tab = "<div class='row'><label>" . __( "Background Image Title Text", "metasliderpro" ) . "</label></div>
                    <div class='row'><input type='text' size='50' name='attachment[{$slide_id}][title]' value='{$title}' /></div>
                    <div class='row'><label>" . __( "Background Image Alt Text", "metasliderpro" ) . "</label></div>
                    <div class='row'><input type='text' size='50' name='attachment[{$slide_id}][alt]' value='{$alt}' /></div>";

        $extra_tab =    "<div class='row'><label>" . __( "Background Image Link", "metasliderpro" ) . "</label></div>
                        <input class='url' type='text' name='attachment[{$slide_id}][url]' placeholder='" . __( "URL", 'metasliderpro' ) . "' value='{$url}' />
                        <div class='new_window'>
                                <label>" . __( "New Window", 'metasliderpro' ) . "<input type='checkbox' name='attachment[{$slide_id}][new_window]' {$target} /></label>
                        </div>";

        $video_tab =    "<div class='row'><label>" . __( "MP4 Source", "metasliderpro" ) . "</label></div>
                        <input class='url' type='text' name='attachment[{$slide_id}][mp4]' placeholder='" . __( "URL", 'metasliderpro' ) . "' value='{$mp4}' />
                        <div class='row'><label>" . __( "WebM Source", "metasliderpro" ) . "</label></div>
                        <input class='url' type='text' name='attachment[{$slide_id}][webm]' placeholder='" . __( "URL", 'metasliderpro' ) . "' value='{$webm}' />";

        $source_tab = "<textarea class='wysiwyg' id='editor{$slide_id}' name='attachment[{$slide_id}][html]'>{$html}</textarea>";

        $tabs = array(
            'general' => array(
                'title' => __( "General", "metasliderpro" ),
                'content' => $general_tab
            ),
            'seo' => array(
                'title' => __( "SEO", "metasliderpro" ),
                'content' => $seo_tab
            ),
            'extra' => array(
                'title' => __( "Extra", "metasliderpro" ),
                'content' => $extra_tab
            ),
            'video' => array(
                'title' => __( "Video Background", "metasliderpro" ),
                'content' => $video_tab
            ),
            'source' => array(
                'title' => __( "Edit Source", "metasliderpro" ),
                'content' => $source_tab
            ),
        );

        if ( version_compare( get_bloginfo('version'), 3.9, '>=' ) ) {

            $crop_position = get_post_meta( $slide_id, 'ml-slider_crop_position', true);

            if ( ! $crop_position ) {
                $crop_position = 'center-center';
            }

            $crop_tab = "<div class='row'><label>" . __( "Crop Position", "metaslider" ) . "</label></div>
                        <div class='row'>
                            <select class='crop_position' name='attachment[{$slide_id}][crop_position]'>
                                <option value='left-top' " . selected( $crop_position, 'left-top', false ) . ">" . __( "Top Left", "metaslider" ) . "</option>
                                <option value='center-top' " . selected( $crop_position, 'center-top', false ) . ">" . __( "Top Center", "metaslider" ) . "</option>
                                <option value='right-top' " . selected( $crop_position, 'right-top', false ) . ">" . __( "Top Right", "metaslider" ) . "</option>
                                <option value='left-center' " . selected( $crop_position, 'left-center', false ) . ">" . __( "Center Left", "metaslider" ) . "</option>
                                <option value='center-center' " . selected( $crop_position, 'center-center', false ) . ">" . __( "Center Center", "metaslider" ) . "</option>
                                <option value='right-center' " . selected( $crop_position, 'right-center', false ) . ">" . __( "Center Right", "metaslider" ) . "</option>
                                <option value='left-bottom' " . selected( $crop_position, 'left-bottom', false ) . ">" . __( "Bottom Left", "metaslider" ) . "</option>
                                <option value='center-bottom' " . selected( $crop_position, 'center-bottom', false ) . ">" . __( "Bottom Center", "metaslider" ) . "</option>
                                <option value='right-bottom' " . selected( $crop_position, 'right-bottom', false ) . ">" . __( "Bottom Right", "metaslider" ) . "</option>
                            </select>
                        </div>";

            $tabs['crop'] = array(
                'title' => __( "Crop", "metaslider" ),
                'content' => $crop_tab
            );

        }

        return apply_filters("metaslider_layer_slide_tabs", $tabs, $this->slide, $this->slider, $this->settings);

    }


    /**
     * Public slide html
     *
     * @return string html
     */
    protected function get_public_slide() {
        add_filter( 'metaslider_responsive_slider_javascript', array( $this, 'get_responsive_javascript' ), 10, 2 );
        add_filter( 'metaslider_flex_slider_javascript', array( $this, 'get_responsive_javascript' ), 10, 2 );
        add_filter( 'metaslider_responsive_slider_parameters', array( $this, 'get_responsive_slider_parameters' ), 10, 2 );
        add_filter( 'metaslider_flex_slider_parameters', array( $this, 'get_flex_slider_parameters' ), 10, 2 );
        add_action( 'metaslider_register_public_styles', array( $this, 'enqueue_assets' ) );

        if ( $this->settings['type'] == 'responsive' ) {
            return $this->get_responsive_slides_markup();
        }

        if ( $this->settings['type'] == 'flex' ) {
            return $this->get_flex_slider_markup();
        }
    }

    /**
     * Return the HTML Overlay portion of the slide
     */
    private function html_overlay() {

        $layer_html = get_post_meta( $this->slide->ID, 'ml-slider_html', true );
        $target = get_post_meta( $this->slide->ID, 'ml-slider_new_window', true ) ? '_blank' : '_self';
        $url = get_post_meta( $this->slide->ID, 'ml-slider_url', true );
        $slide_link = strlen( $url ) ? " data-link='{$url}' data-target='{$target}'" : '';

        $inline_styles = apply_filters("metaslider_mshtmloverlay_inline_styles", array(
            'display' => 'none',
            'position' => 'absolute',
            'top' => '0',
            'left' => '0',
            'width' => '100%',
            'height' => '100%'
        ));

        $inline = "";

        foreach ( $inline_styles as $rule => $value ) {
            $inline .= $rule . ": " . $value . "; ";
        }

        $return = "";

        if ( strlen( $layer_html ) ) {
            $return = "<div class='msHtmlOverlay' style='{$inline}' {$slide_link}>" . __( do_shortcode( $layer_html ) ) . "</div>";
        }

        return $return;
    }

    /**
     * Return the background image markup for the slide
     */
    private function slide_background() {

        $imageHelper = new MetaSliderImageHelper(
            $this->slide->ID,
            $this->settings['width'],
            $this->settings['height'],
            isset( $this->settings['smartCrop'] ) ? $this->settings['smartCrop'] : 'false'
        );

        $url = $imageHelper->get_image_url();

        $image_attributes = array(
            'src' => $url,
            'alt' => get_post_meta( $this->slide->ID, '_wp_attachment_image_alt', true ),
            'title' => get_post_meta( $this->slide->ID, 'ml-slider_title', true ),
            'class' => 'msDefaultImage',
            'height' => $this->settings['height'],
            'width' => $this->settings['width']
        );

        $slide = array(
            'id' => $this->slide->ID
        );

        if ( $this->settings['type'] == 'flex' ) {
            $attributes = apply_filters( 'metaslider_flex_slider_image_attributes', $image_attributes, $slide, $this->slider->ID );
        }

        if ( $this->settings['type'] == 'responsive' ) {
            $attributes = apply_filters( 'metaslider_responsive_slider_image_attributes', $image_attributes, $slide, $this->slider->ID );
        }

        $return = $this->build_image_tag( $attributes );

        // video backgrounds
        $webm = esc_attr( get_post_meta( $this->slide->ID, 'ml-slider_webm', true ) );
        $mp4 = esc_attr( get_post_meta( $this->slide->ID, 'ml-slider_mp4', true ) );

        if ( strlen( $webm ) && strlen( $mp4 ) ) {

            $return = $this->build_video_tag( $webm, $mp4, $url ) . $return;

        }

        return $return;

    }

    /**
     * Build HTML5 Video tag
     *
     * @param string $webm WebM source url/path
     * @param string $mp4 Mp4 source url/path
     * @param string $url Slide image url
     */
    private function build_video_tag( $webm, $mp4, $url ) {

        $atts = array(
            'loop' => 'loop',
            //'poster' => $url, // we're loading the slide background behind the video. the poster renders oddly for a split second.
            'width' => $this->settings['width'],
            'height' => $this->settings['height'],
            'muted' => 'muted',
            'autoplay' => 'autoplay',
            'style' => 'position: absolute; top: 0; left: 0; width: 100%; height: auto;'
        );

        $atts = apply_filters("metaslider_layer_video_attributes", $atts, $this->slide->ID, $this->slider->ID, $this->settings, $url);

        $attributes = "";

        foreach ( $atts as $att => $val ) {

            if ( strlen( $val ) ) {
                $attributes .= " " . $att . '="' . esc_attr( $val ) . '"';
            }

        }

        return "<video {$attributes}>
                    <source src='{$mp4}' type='video/mp4' />
                    <source src='{$webm}' type='video/webm' />
                </video>";

    }

    /**
     * Return the slide HTML for responsive slides
     *
     * @return string
     */
    private function get_responsive_slides_markup() {
        $html = $this->slide_background();
        $html .= $this->html_overlay();

        return $html;
    }

    /**
     * Return the slide HTML for flex slider
     *
     * @return string
     */
    private function get_flex_slider_markup() {
        $html = $this->slide_background();
        $html .= $this->html_overlay();

        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-layer",
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
     * Enqueue required public assets
     */
    public function enqueue_assets() {
        if ( $this->settings['printCss'] == 'true' ) {
            wp_enqueue_style( 'metaslider-pro-animate', plugins_url( 'assets/animate/animate.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
        }

        if ( $this->settings['printJs'] == 'true' ) {
            wp_enqueue_script( 'metaslider-pro-scale-layers', METASLIDERPRO_ASSETS_URL . 'public.js', false, METASLIDERPRO_VERSION );
        }
    }

    /**
     * Reset CSS3 Animations when navigating between slides.
     *
     * @param array   $options   The JavaScript options for the slideshow
     * @param int     $slider_id Slideshow ID
     *
     * @return array $options Modified JavaScript options
     */
    public function get_responsive_slider_parameters( $options, $slider_id ) {
        $options["before"][] = "    $('#metaslider_{$slider_id} li:not(\".rslides1_on\") .animated').each(function(index) {
                         var el = $(this);
                         var cloned = el.clone();
                         el.before(cloned);
                         $(this).remove();
                    });";

        return $options;
    }

    /**
     * Reset CSS3 Animations when navigating between slides.
     *
     * @param array   $options
     * @param int     $slider_id
     *
     * @return array
     */
    public function get_flex_slider_parameters( $options, $slider_id ) {

        $options["init"][] = "                $('#metaslider_{$slider_id} .msHtmlOverlay').each(function() {
                $(this).css('display', 'block');
            });";

        $options["before"][] = "    $('li:not(\".flex-active-slide\") .animated', slider).not('.disabled').each(function(index) {
                        var el = $(this);
                        var cloned = el.clone();
                        el.before(cloned);
                        $(this).remove();
                    });";

        return $options;
    }

    /**
     * Return the javascript which creates the YouTube videos in the slideshow
     *
     * @param string  $javascript
     * @param int     $slider_id
     *
     * @return string
     */
    public function get_responsive_javascript( $javascript, $slider_id ) {


        $downscale_only = $this->settings['layer_scaling'] == 'down_only' ? 'true' : 'false';

        $html = "\n            $(window).resize(function(){
               $('#metaslider_{$slider_id}').metaslider_scale_layers({
                   downscale_only: {$downscale_only},
                   orig_width: {$this->settings['width']}
               });
            });
            $('#metaslider_{$slider_id}').metaslider_scale_layers({
                downscale_only: {$downscale_only},
                orig_width: {$this->settings['width']}
            });

            $('#metaslider_{$slider_id} .msHtmlOverlay .layer[data-link], #metaslider_{$slider_id} .msHtmlOverlay[data-link]').each(function() {
                var layer = $(this);

                layer.css('cursor', 'pointer').on('click', function(e) {
                    if( ! $(e.target).closest('a').length) {
                        window.open(layer.attr('data-link'), layer.attr('data-target'));
                    }
                 });
            });

            $('#metaslider_{$slider_id} .msHtmlOverlay').each(function() {
                $(this).css('display', 'block');
            });";

        // we don't want this filter hanging around if there's more than one slideshow on the page
        remove_filter( 'metaslider_flex_slider_javascript', array( $this, 'get_responsive_javascript' ) );
        remove_filter( 'metaslider_responsive_slider_javascript', array( $this, 'get_responsive_javascript' ) );

        return $javascript . $html;
    }


    /**
     * Save
     *
     * @param array   $fields
     */
    protected function save( $fields ) {
        wp_update_post( array(
                'ID' => $this->slide->ID,
                'menu_order' => $fields['menu_order']
            ) );

        // store the URL as a meta field against the attachment
        $this->add_or_update_or_delete_meta( $this->slide->ID, 'url', $fields['url'] );

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'title', $fields['title'] );

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'mp4', $fields['mp4'] );

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'webm', $fields['webm'] );

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'crop_position', $fields['crop_position'] );

        if ( isset( $fields['alt'] ) ) {
            update_post_meta( $this->slide->ID, '_wp_attachment_image_alt', $fields['alt'] );
        }

        // store the 'new window' setting
        $new_window = isset( $fields['new_window'] ) && $fields['new_window'] == 'on' ? 'true' : 'false';

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'new_window', $new_window );

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'html', $fields['html'] );
    }
}
?>
