<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
class MetaExternalSlide extends MetaSlide {

    public $identifier = "external"; // should be lowercase, one word (use underscores if needed)
    public $name = "External URL"; // slide type title

    /**
     * Constructor
     */
    public function __construct() {
        // Add Slide Tabs
        add_filter('media_upload_tabs', array($this,'custom_media_upload_tab_name'), 999, 1);
        add_filter("metaslider_get_{$this->identifier}_slide", array($this, 'get_slide'), 10, 2);
        add_action("metaslider_save_{$this->identifier}_slide", array($this, 'save_slide'), 5, 3);
        add_action("media_upload_{$this->identifier}", array($this, 'get_iframe'));
        add_action("wp_ajax_create_{$this->identifier}_slide", array($this, 'ajax_create_slide'));
    }

    /**
     * Add extra tabs to the default wordpress Media Manager iframe
     *
     * @var array existing media manager tabs
     */
    public function custom_media_upload_tab_name( $tabs ) {
        // restrict our tab changes to the meta slider plugin page
        if ((isset($_GET['page']) && $_GET['page'] == 'metaslider') ||
            (isset($_GET['tab']) && in_array($_GET['tab'], array($this->identifier)))) {

            $newtabs = array(
                $this->identifier => __($this->name, 'metasliderpro')
            );

            return array_merge( $tabs, $newtabs );
        }

        return $tabs;
    }


    /**
     *
     */
    public function ajax_create_slide() {
        $slider_id = intval($_POST['slider_id']);
        $this->create_slide($slider_id);
        echo $this->get_admin_slide();
        die(); // this is required to return a proper result
    }

    /**
     * Return wp_iframe
     */
    public function get_iframe() {
        return wp_iframe(array($this, 'iframe'));
    }

    /**
     * Media Manager iframe HTML
     */
    public function iframe() {
        wp_enqueue_style('media-views');
        wp_enqueue_script("metasliderpro-{$this->identifier}-script", plugins_url( 'assets/script.js' , __FILE__ ), array('jquery'));
        wp_localize_script("metasliderpro-{$this->identifier}-script", 'metaslider_custom_slide_type', array(
            'identifier' => $this->identifier,
            'name' => $this->name
        ));

        echo "<div class='metaslider'>
                    <div class='media-embed'>
                        <div class='embed-link-settings'>Click 'Add to slider' to create a new {$this->name} slide.</div>
                    </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large'>Add to slider</a>
                    </div>
                </div>
            </div>";
    }

    /**
     * Create a new slide
     *
     * @return int ID of the created slide
     */
    public function create_slide($slider_id) {
        $this->set_slider($slider_id);

        if ( method_exists( $this, 'insert_slide' ) ) { // Meta Slider 3.5+

            $slide_id = $this->insert_slide(false, $this->identifier, $slider_id);

        } else { // backwards compatibility

            // Attachment options
            $attachment = array(
                'post_title'=> "Meta Slider - {$this->name}",
                'post_mime_type' => 'text/html',
                'post_content' => ''
            );

            $slide_id = wp_insert_attachment($attachment);

            // store the type as a meta field against the attachment
            $this->add_or_update_or_delete_meta($slide_id, 'type', $this->identifier);

        }

        $this->set_slide($slide_id);

        $this->tag_slide_to_slider();

        return $slide_id;
    }

    /**
     * Save - called whenever the slideshow is saved.
     */
    protected function save($fields) {
        wp_update_post(array(
            'ID' => $this->slide->ID,
            'menu_order' => $fields['menu_order'],
            'post_excerpt' => $fields['post_excerpt']
        ));

        $this->add_or_update_or_delete_meta($this->slide->ID, 'url', $fields['url']);
        $this->add_or_update_or_delete_meta($this->slide->ID, 'extimgurl', $fields['extimgurl']);
        $this->add_or_update_or_delete_meta($this->slide->ID, 'title', $fields['title']);
        $this->add_or_update_or_delete_meta($this->slide->ID, 'alt', $fields['alt']);
        $this->add_or_update_or_delete_meta($this->slide->ID, 'settings', $fields['settings']);
        // store the 'new window' setting
        $new_window = isset($fields['new_window']) && $fields['new_window'] == 'on' ? 'true' : 'false';

        $this->add_or_update_or_delete_meta($this->slide->ID, 'new_window', $new_window);
    }

    /**
     * Admin slide html
     *
     * @return string html
     */
    protected function get_admin_slide() {
        $caption = htmlentities($this->slide->post_excerpt, ENT_QUOTES, 'UTF-8');
        $url     = get_post_meta($this->slide->ID, 'ml-slider_url', true);
        $extimgurl = get_post_meta($this->slide->ID, 'ml-slider_extimgurl', true);
        $title = get_post_meta($this->slide->ID, 'ml-slider_title', true);
        $alt = get_post_meta($this->slide->ID, 'ml-slider_alt', true);
        $target  = get_post_meta($this->slide->ID, 'ml-slider_new_window', true) ? 'checked=checked' : '';


        // slide row HTML
        $row  = "<tr class='slide external flex responsive nivo coin'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb' style='background-image: url({$extimgurl});'>";

        if ( method_exists( $this, 'get_delete_button_html' ) ) {
            $row .= $this->get_delete_button_html();
        }
                $row .= "            <span class='slide-details'>External URL</span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";
        $row .= "        <ul class='tabs'>";
        $row .= "            <li class='selected' rel='tab-1'>" . __("General", "metasliderpro") . "</li>";
        $row .= "            <li rel='tab-2'>" . __("SEO", "metaslider") . "</li>";
        $row .= "            <li rel='tab-3'>" . __("Caption", "metaslider") . "</li>";
        $row .= "        </ul>";
        $row .= "        <div class='tabs-content'>";
        $row .= "            <div class='tab tab-1'>";
        $row .= "                <div class='row'><label>" . __("External Image URL", "metasliderpro") . "</label></div>";
        $row .= "                <div class='row'><input class='url extimgurl' type='text' name='attachment[{$this->slide->ID}][extimgurl]' placeholder='Source Image URL' value='{$extimgurl}' /></div>";
        $row .= "                <div class='row'><label>" . __("Link URL", "metasliderpro") . "</label></div>";
        $row .= "                <input class='url' type='text' name='attachment[{$this->slide->ID}][url]' placeholder='Link to URL' value='{$url}' />";
        $row .= "                <div class='new_window'>";
        $row .= "                    <label>New Window<input type='checkbox' name='attachment[{$this->slide->ID}][new_window]' {$target} /></label>";
        $row .= "                </div>";
        $row .= "            </div>";
        $row .= "            <div class='tab tab-2' style='display: none;'>";
        $row .= "                <div class='row'><label>" . __("Image Title Text", "metasliderpro") . "</label></div>";
        $row .= "                <div class='row'><input class='url' type='text' name='attachment[{$this->slide->ID}][title]' placeholder='Title Text' value='{$title}' /></div>";
        $row .= "            </div>";
        $row .= "            <div class='tab tab-2' style='display: none;'>";
        $row .= "                <div class='row'><label>" . __("Image Alt Text", "metasliderpro") . "</label></div>";
        $row .= "                <div class='row'><input class='url' type='text' name='attachment[{$this->slide->ID}][alt]' placeholder='Alt Text' value='{$alt}' /></div>";
        $row .= "            </div>";
        $row .= "            <div class='tab tab-3' style='display: none;'>";
        $row .= "                <textarea name='attachment[{$this->slide->ID}][post_excerpt]' placeholder='" . __( "Caption", "ml-slider" ) . "'>{$caption}</textarea>";
        $row .= "            </div>";
        $row .= "        </div>";
        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='{$this->identifier}' />";
        $row .= "        <input type='hidden' class='menu_order' name='attachment[{$this->slide->ID}][menu_order]' value='{$this->slide->menu_order}' />";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }
    /**
     * Returns the HTML for the public slide
     *
     * @return string slide html
     */
    protected function get_public_slide() {
        $url = get_post_meta($this->slide->ID, 'ml-slider_url', true);
        $thumb = get_post_meta($this->slide->ID, 'ml-slider_extimgurl', true);

        // store the slide details
        $slide = array(
            'id' => $this->slide->ID,
            'thumb' => $thumb,
            'url' => $url,
            'alt' => get_post_meta($this->slide->ID, '_wp_attachment_image_alt', true),
            'target' => get_post_meta($this->slide->ID, 'ml-slider_new_window', true) ? '_blank' : '_self',
            'title' => get_post_meta($this->slide->ID, 'ml-slider_title', true),
            'alt' => get_post_meta($this->slide->ID, 'ml-slider_alt', true),
            'caption' => html_entity_decode($this->slide->post_excerpt, ENT_NOQUOTES, 'UTF-8'),
            'caption_raw' => $this->slide->post_excerpt,
            'class' => "slider-{$this->slider->ID} slide-{$this->slide->ID}",
            'rel' => "",
            'data-thumb' => ""
        );

        // fix slide URLs
        if (strpos($slide['url'], 'www.') === 0) {
            $slide['url'] = 'http://' . $slide['url'];
        }

        $slide = apply_filters('metaslider_external_slide_attributes', $slide, $this->slider->ID, $this->settings);

        // return the slide HTML
        switch($this->settings['type']) {
            case "coin":
                return $this->get_coin_slider_markup($slide);
            case "flex":
                return $this->get_flex_slider_markup($slide);
            case "nivo":
                return $this->get_nivo_slider_markup($slide);
            case "responsive":
                return $this->get_responsive_slides_markup($slide);
            default:
                return $this->get_flex_slider_markup($slide);
        }
    }

    /**
     * Generate nivo slider markup
     *
     * @return string slide html
     */
    private function get_nivo_slider_markup($slide) {
        $attributes = array(
            'src' => $slide['thumb'],
            'data-title' => htmlentities($slide['caption_raw'], ENT_QUOTES, 'UTF-8'),
            'data-thumb' => $slide['data-thumb'],
            'alt' => $slide['alt'],
            'rel' => $slide['rel'],
            'class' => $slide['class']
        );

        $html = $this->build_image_tag($attributes);

        if (strlen($slide['url'])) {
            $html = '<a href="' . $slide['url'] . '" target="' . $slide['target'] . '">' . $html . '</a>';
        }

        return apply_filters('metaslider_external_nivo_slider_markup', $html, $slide, $this->settings);
    }

    /**
     * Generate flex slider markup
     *
     * @return string slide html
     */
    private function get_flex_slider_markup($slide) {
        $attributes = array(
            'src' => $slide['thumb'],
            'alt' => $slide['alt'],
            'rel' => $slide['rel'],
            'class' => $slide['class'],
            'title' => $slide['title']
        );

        $html = $this->build_image_tag($attributes);

        if (strlen($slide['url'])) {
            $html = '<a href="' . $slide['url'] . '" target="' . $slide['target'] . '">' . $html . '</a>';
        }

        if (strlen($slide['caption'])) {
            $html .= '<div class="caption-wrap"><div class="caption">' . $slide['caption'] . '</div></div>';
        }

        // store the slide details
        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-external",
            'style' => "display: none; width: 100%;"
        );

        $attributes = apply_filters( 'metaslider_flex_slider_li_attributes', $attributes, $this->slide->ID, $this->slider->ID, $this->settings );

        $li = "<li";

        foreach ( $attributes as $att => $val ) {
            $li .= " " . $att . '="' . esc_attr( $val ) . '"';
        }

        $li .= ">" . $html . "</li>";

        $html = $li;

        return apply_filters('metaslider_external_flex_slider_markup', $html, $slide, $this->settings);
    }

    /**
     * Generate coin slider markup
     *
     * @return string slide html
     */
    private function get_coin_slider_markup($slide) {
        $url = strlen($slide['url']) ? $slide['url'] : 'javascript:void(0)'; // coinslider always wants a URL

        $attributes = array(
            'src' => $slide['thumb'],
            'alt' => $slide['alt'],
            'rel' => $slide['rel'],
            'class' => $slide['class']
        );

        $html = $this->build_image_tag($attributes);

        if (strlen($slide['caption'])) {
            $html .= "<span>{$slide['caption']}</span>";
        }

        $html  = '<a href="' . $url . '" style="display: none;">"' . $html . '</a>';

        return apply_filters('metaslider_external_coin_slider_markup', $html, $slide, $this->settings);
    }

    /**
     * Generate responsive slides markup
     *
     * @return string slide html
     */
    private function get_responsive_slides_markup($slide) {
        $attributes = array(
            'src' => $slide['thumb'],
            'alt' => $slide['alt'],
            'rel' => $slide['rel'],
            'class' => $slide['class'],
            'title' => $slide['title']
        );

        $html = $this->build_image_tag($attributes);

        if (strlen($slide['caption'])) {
            $html .= '<div class="caption-wrap"><div class="caption">' . $slide['caption'] . '</div></div>';
        }

        if (strlen($slide['url'])) {
            $html = '<a href="' . $slide['url'] . '" target="' . $slide['target'] . '">'. $html . '</a>';
        }

        return apply_filters('metaslider_external_responsive_slider_markup', $html, $slide, $this->settings);
    }


}