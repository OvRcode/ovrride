<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Theme Editor
 */
class MetaSliderThemeEditor {

    private $theme = array();
    private $theme_slug = "";
    private $slider_id = 0;
    private $slider_settings = array();

    /**
     * Constructor
     */
    public function __construct() {
        add_filter( 'metaslider_css', array( $this, 'get_theme_css' ), 15, 3 );
        add_filter( 'metaslider_css_classes', array( $this, 'get_theme_css_classes' ), 10, 3 );
        add_action( 'admin_menu', array( $this, 'register_theme_editor_menu' ), 9556 );
        add_filter( 'metaslider_get_available_themes', array( $this, 'get_theme_select_options' ), 10, 2 );
    }

    /**
     * Append the shadow effect type onto the list of classes applied to a slideshow
     */
    public function get_theme_css_classes( $classes, $id, $settings ) {
        $theme = isset( $settings['theme'] ) ? $settings['theme'] : 'default';

        // if we're using the theme editor, always use the currently selected theme
        if ( is_admin() && isset( $_REQUEST['theme_slug'] ) ) {
            $theme = $_REQUEST['theme_slug'];
        }

        // bail out if we're not using a custom theme
        if ( substr( $theme, 0, strlen( '_theme' ) ) !== '_theme' ) {
            return $classes;
        }

        // bail out if thumbnails are enabled
        if ( isset( $settings['navigation'] ) && $settings['navigation'] == 'thumbs' ) {
            return $classes;
        }

        // bail out if filmstrip is enabled
        if ( isset( $settings['navigation'] ) && $settings['navigation'] == 'filmstrip' ) {
            return $classes;
        }

        if ( $this->load_theme( $theme ) && isset( $this->theme['shadow'] ) && $this->theme['shadow'] != 'none' ) {
            return $classes .= " " . $this->theme['shadow'];
        }

        return $classes;
    }

    /**
     * Append custom themes to the list of theme options on the slideshow edit page.
     */
    public function get_theme_select_options( $themes, $selected_theme ) {
        $custom_themes = $this->get_themes();

        if ( !is_array( $custom_themes ) ) {
            return $themes;
        }

        foreach ( $custom_themes as $slug => $theme ) {
            $themes .= "<option value='{$slug}' class='option nivo flex responsive coin'";
            if ( $slug == $selected_theme ) {
                $themes .= " selected=selected";
            }
            $themes .= ">{$theme['title']}</option>";
        }

        return $themes;
    }

    /**
     * Add the theme editor menu option to WordPress
     */
    public function register_theme_editor_menu() {

        $capability = apply_filters( 'metaslider_capability', 'edit_others_posts' );

        $page = add_submenu_page( 'metaslider', __( 'Theme Editor', 'metasliderpro' ), __( 'Theme Editor', 'metasliderpro' ), $capability, 'metaslider-theme-editor', array( $this, 'process_admin_page' ) );

        // ensure our JavaScript is only loaded on the Meta Slider admin page
        add_action( 'admin_print_scripts-' . $page, array( $this, 'register_theme_editor_scripts' ) );
        add_action( 'admin_print_styles-' . $page, array( $this, 'register_theme_editor_styles' ) );
    }

    /**
     * Admin styles
     */
    public function register_theme_editor_styles() {
        wp_enqueue_style( 'metaslider-admin-styles', METASLIDER_ASSETS_URL . 'metaslider/admin.css', false, METASLIDER_VERSION );
        wp_enqueue_style( 'metasliderpro-theme-editor-styles', plugins_url( 'assets/style.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
        wp_enqueue_style( 'metasliderpro-spectrum-style', plugins_url( 'assets/spectrum/spectrum.css' , __FILE__ ), false, METASLIDERPRO_VERSION );
    }

    /**
     * Admin scripts
     */
    public function register_theme_editor_scripts() {
        wp_enqueue_script( 'metasliderpro-spectrum', plugins_url( 'assets/spectrum/spectrum.js' , __FILE__ ), array(), METASLIDERPRO_VERSION );
        wp_enqueue_script( 'metasliderpro-themeEditor-script', plugins_url( 'assets/themeEditor.js' , __FILE__ ), array( 'jquery', 'metasliderpro-spectrum' ), METASLIDERPRO_VERSION );
    }

    /**
     * Create a new theme
     */
    public function create_theme() {
        $slug = '_theme_' . time();

        // load existing themes
        $themes = get_option( 'metaslider-themes' );

        // create a new blank theme
        $themes[$slug] = $this->get_theme_defaults();

        // save
        update_option( 'metaslider-themes', $themes );

        // load it up
        $this->load_theme( $slug );
        $_REQUEST['theme_slug'] = $slug;
    }

    /**
     * Return an array of all created themes
     */
    public function get_themes() {
        $themes = get_option( 'metaslider-themes' );
        return $themes;
    }

    /**
     * Return true if the user has created themes
     */
    private function themes_available() {
        $themes = $this->get_themes();
        return !empty( $themes );
    }

    /**
     * Save changes to an existing theme
     *
     * @param string  $slug
     * @param array   $themes
     */
    public function save_theme( $slug, $theme ) {
        foreach ( array( 'enable_custom_caption', 'enable_custom_arrows', 'enable_custom_navigation', 'arrows_always_show' ) as $checkbox ) {
            if ( isset( $theme[$checkbox] ) && $theme[$checkbox] == 'on' ) {
                $theme[$checkbox] = 'enabled';
            } else {
                $theme[$checkbox] = 'disabled';
            }
        }

        $themes = get_option( 'metaslider-themes' );
        $themes[$slug] = $theme;
        update_option( 'metaslider-themes', $themes );
    }

    /**
     * Save changes to an existing theme
     *
     * @param string  $slug
     */
    public function delete_theme( $slug ) {
        $themes = get_option( 'metaslider-themes' );

        if ( isset( $themes[$slug] ) ) {
            unset( $themes[$slug] );
            update_option( 'metaslider-themes', $themes );
            $this->load_default_theme();
            return true;
        }

        return false;
    }

    /**
     * Load an existing theme, identified by the slug
     *
     * @param string  slug
     * @return bool - true if theme successfully loaded
     */
    public function load_theme( $slug ) {
        $themes = get_option( 'metaslider-themes' );

        if ( isset( $themes[$slug] ) ) {
            $this->theme = $themes[$slug];
            $this->theme_slug = $slug;
            return true;
        }

        return false;
    }

    /**
     * Load an existing theme, identified by the slug
     *
     * @return bool - true if default theme found and loaded
     */
    public function load_default_theme() {
        $themes = get_option( 'metaslider-themes' );

        if ( is_array( $themes ) && count( $themes ) ) {
            foreach ( $themes as $theme => $vals ) {
                $this->theme = $vals;
                $this->theme_slug = $theme;
                $_REQUEST['theme_slug'] = $theme;
                return true; // theme loaded
            }
        }

        return false; // no themes found
    }

    /**
     * Contains all the settings for a default (new) theme
     *
     * @return array default theme values
     */
    private function get_theme_defaults() {
        $defaults = array(
            'title' => 'New Theme',
            'dot_fill_colour_start' => 'rgba(0,0,0,0.5)',
            'dot_fill_colour_end' => 'rgba(0,0,0,0.5)',
            'dot_border_colour' => 'rgba(0,0,0,1)',
            'dot_border_width' => 0,
            'dot_border_radius' => 10,
            'dot_size' => 12,
            'dot_spacing' => 4,
            'active_dot_fill_colour_start' => 'rgba(0,0,0,1)',
            'active_dot_fill_colour_end' => 'rgba(0,0,0,1)',
            'active_dot_border_colour' => 'rgba(0,0,0,1)',
            'nav_position' => 'default',
            'nav_vertical_margin' => 10,
            'nav_horizontal_margin' => 0,
            'arrow_type' => 1,
            'arrow_colour' => 'black',
            'arrow_indent' => 5,
            'arrow_opacity' => 70,
            'outer_border_radius' => 0,
            'shadow' => 'default',
            'caption_position' => 'bottomLeft',
            'caption_width' => 100,
            'caption_align' => 'left',
            'caption_vertical_margin' => 0,
            'caption_horizontal_margin' => 0,
            'caption_background_colour' => 'rgba(0,0,0,0.7)',
            'caption_text_colour' => 'rgba(255,255,255,1)',
            'caption_border_radius' => 0,
            'enable_custom_caption' => 'enabled',
            'enable_custom_navigation' => 'enabled',
            'enable_custom_arrows' => 'enabled',
            'custom_arrow_next' => 0,
            'custom_arrow_prev' => 0,
            'arrow_always_show' => 'disabled'
        );

        return $defaults;
    }

    /**
     * Get the theme setting. Fall back to returning the default theme setting.
     *
     * @param string  $name
     */
    private function get_setting( $name ) {
        // try and get a saved setting
        if ( isset( $this->theme[$name] ) ) {
            return $this->theme[$name];
        }

        // fall back to default values
        $defaults = $this->get_theme_defaults();
        if ( isset( $defaults[$name] ) ) {
            return $defaults[$name];
        }

        return false;
    }

    /**
     * Return all available sliders
     *
     * @return array
     */
    private function get_sliders_for_preview() {
        $sliders = false;

        // list the tabs
        $args = array(
            'post_type' => 'ml-slider',
            'post_status' => 'publish',
            'orderby' => 'date',
            'order' => 'ASC',
            'posts_per_page' => -1
        );

        $the_query = new WP_Query( $args );

        while ( $the_query->have_posts() ) {
            $the_query->the_post();

            $sliders[] = array(
                'title' => get_the_title(),
                'id' => $the_query->post->ID
            );
        }

        return $sliders;
    }

    /**
     * Work out which slider to use for the preview
     *
     * @return int|bool - ID of slideshow or false
     */
    private function get_preview_slider_id() {
        if ( isset( $_REQUEST['slider_id'] ) ) {
            return $_REQUEST['slider_id'];
        }

        $all_sliders = $this->get_sliders_for_preview();

        if ( is_array( $all_sliders ) && isset( $all_sliders[0]['id'] ) ) {
            return $all_sliders[0]['id'];
        }

        return false;
    }

    /**
     * Handle deleting/saving themes etc
     */
    private function process() {

        if ( isset( $_REQUEST['save_theme'] ) ) {
            $slug = $_REQUEST['theme_slug'];
            $theme = $_REQUEST['theme'];
            $this->save_theme( $slug, $theme );
        }

        if ( isset( $_REQUEST['delete_theme'] ) ) {
            $slug = $_REQUEST['theme_slug'];
            $this->delete_theme( $slug );
        }

        if ( isset( $_REQUEST['theme_slug'] ) ) {
            $this->load_theme( $_REQUEST['theme_slug'] );
        } else {
            $this->load_default_theme();
        }

        if ( isset( $_REQUEST['add'] ) ) {
            $this->create_theme();
        }

        $this->slider_id = $this->get_preview_slider_id();
        $this->slider_settings = get_post_meta( $this->slider_id, 'ml-slider_settings', true );
    }

    /**
     * Render the admin page
     */
    public function process_admin_page() {
        $this->process();

        // media library dependencies
        wp_enqueue_media();

        $arrow_style = $this->get_arrow_options();
        $arrow_colour = $this->get_arrow_colours();

        ?>

        <div class='metaslider metaslider_themeEditor'>

            <?php
                if ( isset( $_REQUEST['save_theme'] ) ) {
                    echo "<div class='updated'><p>" . __( "Theme Saved. To apply the theme to a slideshow, edit the slideshow and select this theme from the theme dropdown menu.", "metasliderpro" ) . "</p></div>";
                }

                if ( $sliders = $this->get_sliders_for_preview() ) {
                    echo "<form style='position: absolute; right: 20px; top: 27px;' accept-charset='UTF-8' action='?page=metaslider-theme-editor' method='post'>";
                    echo "<input type='hidden' name='theme_slug' value='{$this->theme_slug}' />";
                    echo "<select name='slider_id'>";
                    foreach ( $sliders as $slider ) {
                        $selected = $slider['id'] == $this->slider_id ? 'selected=selected' : '';
                        echo "<option value='{$slider['id']}' {$selected}>{$slider['title']}</option>";
                    }
                    echo "</select>";
                    echo "<input type='submit' class='button button-secondary' value='" . __( "Switch Preview Slider", "metasliderpro" ) . "' /></form>";
                }
            ?>

            <form accept-charset='UTF-8' action='?page=metaslider-theme-editor' method='post'>

                <h3 class="nav-tab-wrapper">
                    <?php
                        if ( $this->themes_available() ) {
                            foreach ( $this->get_themes() as $slug => $theme ) {
                                if ( $this->theme_slug == $slug ) {
                                    echo "<div class='nav-tab nav-tab-active'><input type='text' name='theme[title]'  value='" . $theme['title'] . "' onfocus='this.style.width = ((this.value.length + 1) * 9) + \"px\"' /></div>";
                                } else {
                                    echo "<a href='?page=metaslider-theme-editor&theme_slug={$slug}' class='nav-tab'>{$theme['title']}</a>";
                                }
                            }
                        }
                    ?>

                    <a href="?page=metaslider-theme-editor&add=true" id="create_new_tab" class="nav-tab">+</a>
                </h3>

                <?php if ( !$this->theme_slug ) {
                    // bail out if we have no themes
                    return;
                } ?>

                <div class='theme_editor_left'>
                    <input type='hidden' name='theme_slug' value='<?php echo $this->theme_slug ?>' />
                    <input type='hidden' name='slider_id' value='<?php echo $this->slider_id ?>' />
                    <table class='widefat settings'>
                        <thead>
                            <tr>
                                <th width='40%'><?php _e( "Theme Settings", 'metasliderpro' ) ?></th>
                                <th><input type='submit' name='save_theme' value='<?php _e( "Save", "metasliderpro" ) ?>' class='alignright button button-primary' /></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan='2' class='highlight'>
                                    <?php _e( "Slideshow", 'metasliderpro' ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Outer Border Radius", 'metasliderpro' ) ?></td>
                                <td>
                                    <input class='number' type='number' min='0' max='50' id='theme_outer_border_radius' name='theme[outer_border_radius]' value='<?php echo $this->get_setting( 'outer_border_radius' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "CSS3 Shadow", 'metasliderpro' ) ?><br />
                                    <small><i><?php _e( "Note: Not compatible with 'thumbnail' navigation type", 'metasliderpro' ) ?></i></small>
                                </td>
                                <td>
                                    <select id='shadow' name='theme[shadow]'>
                                        <option value='none' <?php if ( $this->get_setting( 'shadow' ) == 'none' ) echo 'selected=selected'?>><?php _e( "None", "metasliderpro" ) ?></option>
                                        <option value='effect0' <?php if ( $this->get_setting( 'shadow' ) == 'effect0' ) echo 'selected=selected'?>><?php _e( "Default", "metasliderpro" ) ?></option>
                                        <option value='effect1' <?php if ( $this->get_setting( 'shadow' ) == 'effect1' ) echo 'selected=selected'?>><?php _e( "Bottom", "metasliderpro" ) ?></option>
                                        <option value='effect2' <?php if ( $this->get_setting( 'shadow' ) == 'effect2' ) echo 'selected=selected'?>><?php _e( "Page Curl", "metasliderpro" ) ?></option>
                                        <option value='effect3' <?php if ( $this->get_setting( 'shadow' ) == 'effect3' ) echo 'selected=selected'?>><?php _e( "Bottom Curve", "metasliderpro" ) ?></option>
                                        <option value='effect4' <?php if ( $this->get_setting( 'shadow' ) == 'effect4' ) echo 'selected=selected'?>><?php _e( "Top and Bottom Curve", "metasliderpro" ) ?></option>
                                        <option value='effect5' <?php if ( $this->get_setting( 'shadow' ) == 'effect5' ) echo 'selected=selected'?>><?php _e( "Sides", "metasliderpro" ) ?></option>
                                    </select><br />
                                </td>
                            </tr>
                            <tr>
                                <td colspan='2' class='highlight'>
                                    <?php _e( "Caption", 'metasliderpro' ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Enable Custom Caption", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='checkbox' id='enable_custom_caption' name='theme[enable_custom_caption]' <?php if ( $this->get_setting( 'enable_custom_caption' ) == 'enabled' ) echo 'checked=checked'?> />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Position", 'metasliderpro' ) ?></td>
                                <td>
                                    <select id='caption_position' name='theme[caption_position]'>
                                        <option value='bottomLeft' <?php if ( $this->get_setting( 'caption_position' ) == 'bottomLeft' ) echo 'selected=selected'?>><?php _e( "Bottom Left", 'metasliderpro' ) ?></option>
                                        <option value='bottomRight' <?php if ( $this->get_setting( 'caption_position' ) == 'bottomRight' ) echo 'selected=selected'?>><?php _e( "Bottom Right", 'metasliderpro' ) ?></option>
                                        <option value='topLeft' <?php if ( $this->get_setting( 'caption_position' ) == 'topLeft' ) echo 'selected=selected'?>><?php _e( "Top Left", 'metasliderpro' ) ?></option>
                                        <option value='topRight' <?php if ( $this->get_setting( 'caption_position' ) == 'topRight' ) echo 'selected=selected'?>><?php _e( "Top Right", 'metasliderpro' ) ?></option>
                                        <option value='underneath' <?php if ( $this->get_setting( 'caption_position' ) == 'underneath' ) echo 'selected=selected'?>><?php _e( "Underneath", 'metasliderpro' ) ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Width", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='100' id='theme_caption_width' name='theme[caption_width]' value='<?php echo $this->get_setting( 'caption_width' ); ?>' /><span class='after'>%</span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Border Radius", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='100' id='theme_caption_border_radius' name='theme[caption_border_radius]' value='<?php echo $this->get_setting( 'caption_border_radius' ); ?>' /><span class='after'>px</span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Text Color", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-caption-text-colour' name='theme[caption_text_colour]' value='<?php echo $this->get_setting( 'caption_text_colour' ); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Text Align", 'metasliderpro' ) ?></td>
                                <td>
                                    <select id='caption_align' name='theme[caption_align]'>
                                        <option value='left' <?php if ( $this->get_setting( 'caption_align' ) == 'left' ) echo 'selected=selected'?>><?php _e( "Left", 'metasliderpro' ) ?></option>
                                        <option value='center' <?php if ( $this->get_setting( 'caption_align' ) == 'center' ) echo 'selected=selected'?>><?php _e( "Center", 'metasliderpro' ) ?></option>
                                        <option value='right' <?php if ( $this->get_setting( 'caption_align' ) == 'right' ) echo 'selected=selected'?>><?php _e( "Right", 'metasliderpro' ) ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Background Color", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-caption-background-colour' name='theme[caption_background_colour]' value='<?php echo $this->get_setting( 'caption_background_colour' ) ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Vertical Margin", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='500' id='theme_caption_vertical_margin' name='theme[caption_vertical_margin]' value='<?php echo $this->get_setting( 'caption_vertical_margin' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Horizontal Margin", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='500' id='theme_caption_horizontal_margin' name='theme[caption_horizontal_margin]' value='<?php echo $this->get_setting( 'caption_horizontal_margin' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td colspan='2' class='highlight'>
                                    <?php _e( "Arrows", 'metasliderpro' ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Enable Custom Arrows", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='checkbox' id='enable_custom_arrows' name='theme[enable_custom_arrows]' <?php if ( $this->get_setting( 'enable_custom_arrows' ) == 'enabled' ) echo 'checked=checked'?> />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Built In Styles", 'metasliderpro' ) ?></td>
                                <td>
                                    <select id='arrow_style' name='theme[arrow_type]'><?php echo $arrow_style ?></select>
                                    <select id='arrow_colour' name='theme[arrow_colour]'><?php echo $arrow_colour ?></select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Custom Prev Arrow", 'metasliderpro' ) ?></td>
                                <td>
                                <?php
                                                            $custom_prev_arrow = $this->get_setting( 'custom_prev_arrow' );

                                                        if ( $custom_prev_arrow > 0 ) {
                                                            $url = wp_get_attachment_image_src( $custom_prev_arrow , 'full' );
                                                            echo "<button id='open_media_manager_prev' style='display: none;'>" . __( "Select", 'metasliderpro' ) . "</button>";
                                                            echo "<button id='remove_custom_prev_arrow'>" . __( "Remove", 'metasliderpro' ) . "</button>";
                                                            echo "<div id='custom_prev_arrow'><img src='{$url[0]}' width='{$url[1]}' height='{$url[2]}' /></div>";
                                                            echo "<input type='hidden' id='custom_prev_arrow_input' name='theme[custom_prev_arrow]' value='{$custom_prev_arrow}'>";
                                                        } else {
                                                        echo "<button id='open_media_manager_prev'>" . __( "Select", 'metasliderpro' ) . "</button>";
                                                        echo "<button id='remove_custom_prev_arrow' style='display: none;'>" . __( "Remove", 'metasliderpro' ) . "</button>";
                                                        echo "<div id='custom_prev_arrow'></div>";
                                                        echo "<input type='hidden' id='custom_prev_arrow_input' name='theme[custom_prev_arrow]' value='0'>";
                                                    }
?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Custom Next Arrow", 'metasliderpro' ) ?></td>
                                <td>
                                <?php
                                                $custom_next_arrow = $this->get_setting( 'custom_next_arrow' );

                                            if ( $custom_next_arrow > 0 ) {
                                                $url = wp_get_attachment_image_src( $custom_next_arrow , 'full' );
                                                echo "<button id='open_media_manager_next' style='display: none;'>" . __( "Select", 'metasliderpro' ) . "</button>";
                                                echo "<button id='remove_custom_next_arrow'>" . __( "Remove", 'metasliderpro' ) . "</button>";
                                                echo "<div id='custom_next_arrow'><img src='{$url[0]}' width='{$url[1]}' height='{$url[2]}' /></div>";
                                                echo "<input type='hidden' id='custom_next_arrow_input' name='theme[custom_next_arrow]' value='{$custom_next_arrow}'>";
                                            } else {
                                            echo "<button id='open_media_manager_next'>" . __( "Select", 'metasliderpro' ) . "</button>";
                                            echo "<button id='remove_custom_next_arrow' style='display: none;'>" . __( "Remove", 'metasliderpro' ) . "</button>";
                                            echo "<div id='custom_next_arrow'></div>";
                                            echo "<input type='hidden' id='custom_next_arrow_input' name='theme[custom_next_arrow]' value='0'>";
                                        }
?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Opacity", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='100' step='1' id='theme_arrow_opacity' name='theme[arrow_opacity]' value='<?php echo $this->get_setting( 'arrow_opacity' ); ?>' /><span class='after'>%</span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Distance from edge", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='-50' max='50' id='theme_arrow_indent' name='theme[arrow_indent]' value='<?php echo $this->get_setting( 'arrow_indent' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Always show", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='checkbox' id='arrows_always_show' name='theme[arrows_always_show]' <?php if ( $this->get_setting( 'arrows_always_show' ) == 'enabled' ) echo 'checked=checked'?> />
                                </td>
                            </tr>
                            <tr>
                            <tr>
                                <td colspan='2' class='highlight'>
                                    <?php _e( "Navigation", 'metasliderpro' ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Enable Custom Navigation", 'metasliderpro' ) ?></td>
                                <td>
                                     <input type='checkbox' id='enable_custom_navigation' name='theme[enable_custom_navigation]' <?php if ( $this->get_setting( 'enable_custom_navigation' ) == 'enabled' ) echo 'checked=checked'?> />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Position", 'metasliderpro' ) ?></td>
                                <td>
                                    <select id='nav_position' name='theme[nav_position]'>
                                        <option value='default' <?php if ( $this->get_setting( 'nav_position' ) == 'default' ) echo 'selected=selected'?>><?php _e( "Default", 'metasliderpro' ) ?></option>
                                        <option value='topLeft' <?php if ( $this->get_setting( 'nav_position' ) == 'topLeft' ) echo 'selected=selected'?>><?php _e( "Top Left", 'metasliderpro' ) ?></option>
                                        <option value='topCenter' <?php if ( $this->get_setting( 'nav_position' ) == 'topCenter' ) echo 'selected=selected'?>><?php _e( "Top Center", 'metasliderpro' ) ?></option>
                                        <option value='topRight' <?php if ( $this->get_setting( 'nav_position' ) == 'topRight' ) echo 'selected=selected'?>><?php _e( "Top Right", 'metasliderpro' ) ?></option>
                                        <option value='bottomLeft' <?php if ( $this->get_setting( 'nav_position' ) == 'bottomLeft' ) echo 'selected=selected'?>><?php _e( "Bottom Left", 'metasliderpro' ) ?></option>
                                        <option value='bottomCenter' <?php if ( $this->get_setting( 'nav_position' ) == 'bottomCenter' ) echo 'selected=selected'?>><?php _e( "Bottom Center", 'metasliderpro' ) ?></option>
                                        <option value='bottomRight' <?php if ( $this->get_setting( 'nav_position' ) == 'bottomRight' ) echo 'selected=selected'?>><?php _e( "Bottom Right", 'metasliderpro' ) ?></option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Vertical Margin", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='500' id='theme_nav_vertical_margin' name='theme[nav_vertical_margin]' value='<?php echo $this->get_setting( 'nav_vertical_margin' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Horizontal Margin", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='500' id='theme_nav_horizontal_margin' name='theme[nav_horizontal_margin]' value='<?php echo $this->get_setting( 'nav_horizontal_margin' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Fill Color", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-fill-start' name='theme[dot_fill_colour_start]' value='<?php echo $this->get_setting( 'dot_fill_colour_start' ); ?>' />
                                    <input type='text' class='colorpicker' id='colourpicker-fill-end' name='theme[dot_fill_colour_end]' value='<?php echo $this->get_setting( 'dot_fill_colour_end' ); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Fill Color (Active)", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-active-fill-start' name='theme[active_dot_fill_colour_start]' value='<?php echo $this->get_setting( 'active_dot_fill_colour_start' ); ?>' />
                                    <input type='text' class='colorpicker' id='colourpicker-active-fill-end' name='theme[active_dot_fill_colour_end]' value='<?php echo $this->get_setting( 'active_dot_fill_colour_end' ); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Border Color", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-border-colour' name='theme[dot_border_colour]' value='<?php echo $this->get_setting( 'dot_border_colour' ); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Border Color (Active)", 'metasliderpro' ) ?></td>
                                <td>
                                    <input type='text' class='colorpicker' id='colourpicker-active-border-colour' name='theme[active_dot_border_colour]' value='<?php echo $this->get_setting( 'active_dot_border_colour' ); ?>' />
                                </td>
                            </tr>
                            <tr>
                                <td><?php _e( "Size", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='50' id='theme_dot_size' name='theme[dot_size]' value='<?php echo $this->get_setting( 'dot_size' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Spacing", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='50' id='theme_dot_spacing' name='theme[dot_spacing]' value='<?php echo $this->get_setting( 'dot_spacing' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Border Width", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='50' id='theme_dot_border_width' name='theme[dot_border_width]' value='<?php echo $this->get_setting( 'dot_border_width' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                            <tr>
                                <td><?php _e( "Border Radius", 'metasliderpro' ) ?></td>
                                <td><input class='number' type='number' min='0' max='50' id='theme_dot_border_radius' name='theme[dot_border_radius]' value='<?php echo $this->get_setting( 'dot_border_radius' ); ?>' /><span class='after'><?php _e( "px", 'metasliderpro' ) ?></span></td>
                            </tr>
                        </tbody>
                    </table>
                    <input type='submit' class='confirm button button-secondary' name='delete_theme' value='<?php _e("Delete Theme", "metasliderpro"); ?>' />
                </div>

                <div class='theme_editor_right'>
                    <div style='width: 90%'>
                        <?php echo do_shortcode( "[metaslider id=" . $this->slider_id . "]" ) ?>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * The different arrow types are stored as a sprite, this function
     * returns an array containing the details required to position the
     * sprite correctly
     */
    public function get_arrows() {
        $arrows[1]  = array( 'height' => 51, 'width' => 23, 'offset' => 0 );
        $arrows[2]  = array( 'height' => 39, 'width' => 22, 'offset' => 54 );
        $arrows[3]  = array( 'height' => 37, 'width' => 20, 'offset' => 95 );
        $arrows[4]  = array( 'height' => 30, 'width' => 21, 'offset' => 135 );
        $arrows[5]  = array( 'height' => 27, 'width' => 26, 'offset' => 167 );
        $arrows[6]  = array( 'height' => 31, 'width' => 31, 'offset' => 194 );
        $arrows[7]  = array( 'height' => 25, 'width' => 25, 'offset' => 226 );
        $arrows[8]  = array( 'height' => 29, 'width' => 28, 'offset' => 251 );
        $arrows[9]  = array( 'height' => 40, 'width' => 21, 'offset' => 282 );
        $arrows[10] = array( 'height' => 31, 'width' => 21, 'offset' => 325 );
        $arrows[11] = array( 'height' => 23, 'width' => 17, 'offset' => 362 );
        $arrows[12] = array( 'height' => 17, 'width' => 12, 'offset' => 391 );
        $arrows[13] = array( 'height' => 18, 'width' => 22, 'offset' => 411 );
        $arrows[14] = array( 'height' => 25, 'width' => 21, 'offset' => 429 );
        $arrows[15] = array( 'height' => 34, 'width' => 34, 'offset' => 459 );
        $arrows[16] = array( 'height' => 34, 'width' => 34, 'offset' => 498 );

        return $arrows;
    }

    /**
     *
     */
    private function get_selected_arrow() {
        $arrows = $this->get_arrows();
        $selected_arrow_type = $this->get_setting( 'arrow_type' );
        $arrow = $arrows[$selected_arrow_type];

        $url = plugins_url( 'assets/arrows/' , __FILE__ ) . $this->get_setting( 'arrow_colour' ) . ".png";

        $selected_arrow['prev'] = array(
            'url' => $url,
            'width' => $arrow['width'],
            'height' => $arrow['height'],
            'position' => "0 -" . intval( $arrow['offset'] ) . "px"
        );

        $selected_arrow['next'] = array(
            'url' => $url,
            'width' => $arrow['width'],
            'height' => $arrow['height'],
            'position' => "100% -" . intval( $arrow['offset'] ) . "px"
        );

        $custom_prev_arrow = $this->get_setting( 'custom_prev_arrow' );

        if ( $custom_prev_arrow > 0 ) {
            $img = wp_get_attachment_image_src( $custom_prev_arrow , 'full' );

            $selected_arrow['prev'] = array(
                'url' => $img[0],
                'width' => $img[1],
                'height' => $img[2],
                'position' => "0 0"
            );
        }

        $custom_next_arrow = $this->get_setting( 'custom_next_arrow' );

        if ( $custom_next_arrow > 0 ) {
            $img = wp_get_attachment_image_src( $custom_next_arrow , 'full' );

            $selected_arrow['next'] = array(
                'url' => $img[0],
                'width' => $img[1],
                'height' => $img[2],
                'position' => "0 0"
            );
        }


        return $selected_arrow;
    }

    /**
     * Return the CSS for the theme.
     */
    public function get_theme_css( $css, $settings, $id ) {
        $this->slider_id = $id;
        $this->slider_settings = get_post_meta( $id, 'ml-slider_settings', true );
        $theme_slug = $settings['theme'];

        // theme_slug is used for the preview in the back end. This causes the theme to load
        // even if the preview slideshow isn't set to use this theme
        if ( isset( $_REQUEST['theme_slug'] ) ) {
            $theme_slug = $_REQUEST['theme_slug'];
        }

        if ( substr( $theme_slug, 0, strlen( '_theme' ) ) !== '_theme' ) {
            return $css; // we're not using a custom theme
        }

        if ( !$this->load_theme( $theme_slug ) ) {
            return $css; // the theme doesn't exist (deleted maybe)
        }

        $arrow = $this->get_selected_arrow();

        switch ( $settings['type'] ) {
        case "coin":
            $theme_css = $this->get_coin_theme( $arrow );
            break;
        case "flex":
            $theme_css = $this->get_flex_theme( $arrow );
            break;
        case "nivo":
            $theme_css = $this->get_nivo_theme( $arrow );
            break;
        case "responsive":
            $theme_css = $this->get_responsive_theme( $arrow );
            break;
        }

        $theme_css = $this->tidy_css( $theme_css . $this->get_container_margin() );

        return $css . $theme_css;
    }

    /**
     * Properly indent the CSS for output.
     */
    private function tidy_css( $css ) {

        $selector = apply_filters( "metaslider_theme_editor_css_selector", $this->get_default_css_selector(), $this->slider_id );

        // remove all spaces
        $css = str_replace( "  ", "", $css );

        // remove all line breaks
        $css = str_replace( "\n", "", $css );

        // remove all tabs
        $css = str_replace( "\t", "", $css );

        // add a blank line between each selector
        $css = str_replace( "}", "\n\t}\n", $css );

        // break each selector onto a new line
        $css = str_replace( "[metaslider]", "\n\t{$selector}", $css );

        // break each rule onto a new line
        $css = str_replace( ";", ";\n\t\t", $css );

        // break each set of rules onto a new line
        $css = str_replace( "{", "{\n\t\t", $css );

        // clean up blank lines
        $css = str_replace( "\t\t\n", "", $css );

        return "\n" . $css;
    }

    /**
     * @todo: detect themes that use #content and prepend selector
     */
    private function get_default_css_selector() {

        return ".metaslider-{$this->slider_id}";

    }

    /**
     * Work out the correct margin value to apply to the bottom of the slideshow
     */
    public function get_container_margin() {
        $position = $this->get_setting( 'nav_position' );

        if ( $position == 'default' ) {
            $margin = ( $this->get_setting( 'nav_vertical_margin' ) * 2 ) + $this->get_setting( 'dot_size' );
            return "[metaslider] {
                margin-bottom: {$margin}px;
            }";
        }
    }

    /**
     * Return CSS rules for the navigation positioning
     */
    public function get_nav_position_css( $important = false ) {

        $position = $this->get_setting( 'nav_position' );

        $navPosition['width'] = 'auto';
        $navPosition['top'] = 'auto';
        $navPosition['right'] = 'auto';
        $navPosition['bottom'] = 'auto';
        $navPosition['left'] = 'auto';
        $navPosition['position'] = 'absolute';

        if ( $position == 'topCenter' || $position == 'bottomCenter' || $position == 'default' ) {
            $navPosition['text-align'] = 'center';
        }

        if ( $position == 'topCenter' || $position == 'bottomCenter' ) {
            $navPosition['width'] = '100%';
        }

        if ( $position == 'topRight' || $position == 'topCenter' ) {
            $navPosition['top'] = 0;
            $navPosition['right'] = 0;
        }

        if ( $position == 'topLeft' ) {
            $navPosition['top'] = 0;
            $navPosition['left'] = 0;
        }

        if ( $position == 'bottomLeft' || $position == 'bottomCenter' ) {
            $navPosition['bottom'] = 0;
            $navPosition['left'] = 0;
        }

        if ( $position == 'bottomRight' ) {
            $navPosition['bottom'] = 0;
            $navPosition['right'] = 0;
        }

        if ( $position == 'default' ) {
            $navPosition['width'] = '100%';
            $navPosition['top'] = 'auto';
            $navPosition['right'] = 'auto';
            $navPosition['bottom'] = 'auto';
            $navPosition['left'] = 'auto';
        }

        $important = $important ? ' !important' : '';

        foreach ( $navPosition as $key => $value ) {
            $rules[] = $key . ": " . $value . $important . ";";
        }
        return implode( "\n            ", $rules );
    }


    /**
     * Return the CSS required to apply the theme to nivo slider.
     *
     * @param array   $settings slideshow settings
     * @param array   $arrow    arrow information
     * @return string theme CSS
     */
    private function get_nivo_theme( $arrow ) {

        $theme = "";

        if ( $this->get_setting( 'outer_border_radius' ) > 0 ) {
            $theme .= " [metaslider] .nivoSlider,
                        [metaslider] .nivoSlider img {
                            border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                        }";

        }

        if ( $this->slider_settings['navigation'] != 'hidden' && $this->get_setting( 'enable_custom_navigation' ) == 'enabled' ) {
            $theme .= " [metaslider] .theme-default .nivo-controlNav a {
                            padding: 0;
                            box-shadow: none;
                            border-style: solid;
                            border-color: {$this->get_setting( 'dot_border_colour' )};
                            border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            border-width: {$this->get_setting( 'dot_border_width' )}px;
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'dot_border_colour' )};
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            width: {$this->get_setting( 'dot_size' )}px;
                            height: {$this->get_setting( 'dot_size' )}px;
                            margin-left: {$this->get_setting( 'dot_spacing' )}px;
                            margin-right: {$this->get_setting( 'dot_spacing' )}px;
                            background: {$this->rgba_to_rgb( 'dot_fill_colour_start' )};
                            background: {$this->get_setting( 'dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'dot_fill_colour_start' )}), to({$this->get_setting( 'dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                        }

                        [metaslider] .theme-default .nivo-controlNav a.active {
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'active_dot_border_colour' )};
                            background: {$this->rgba_to_rgb( 'active_dot_fill_colour_start' )};
                            background: {$this->get_setting( 'active_dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'active_dot_fill_colour_start' )}), to({$this->get_setting( 'active_dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                        }

                        [metaslider] .theme-default .nivo-controlNav {
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            padding: 0;
                            background: transparent;
                            z-index: 99;
                            margin-top: {$this->get_setting( 'nav_vertical_margin' )}px;
                            margin-bottom: {$this->get_setting( 'nav_vertical_margin' )}px;
                            margin-left: {$this->get_setting( 'nav_horizontal_margin' )}px;
                            margin-right: {$this->get_setting( 'nav_horizontal_margin' )}px;
                            {$this->get_nav_position_css()}
                        }";
        }

        if ( $this->get_setting( 'enable_custom_caption' ) == 'enabled' ) {
            $theme .= " [metaslider] .theme-default .nivo-caption {
                            opacity: 1;
                            margin: {$this->get_setting( 'caption_vertical_margin' )}px {$this->get_setting( 'caption_horizontal_margin' )}px;
                            color: {$this->get_setting( 'caption_text_colour' )};
                            background: {$this->rgba_to_rgb( 'caption_background_colour' )};
                            background: {$this->get_setting( 'caption_background_colour' )};
                            {$this->get_caption_position_css()}
                            {$this->get_caption_text_align_css()}
                            border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                        }";
        }

        if ( $this->get_setting( 'enable_custom_arrows' ) == 'enabled' ) {
            $theme .= " [metaslider] .theme-default .nivoSlider:hover .nivo-directionNav a {
                            opacity: {$this->get_arrow_opacity_hover()};
                        }

                        [metaslider] .theme-default .nivo-prevNav {
                            left: {$this->get_setting( 'arrow_indent' )}px;
                            background: transparent url(" . $arrow['prev']['url'] . ") " . $arrow['prev']['position'] . ";
                            width: " . $arrow['prev']['width'] . "px;
                            height: " . $arrow['prev']['height'] . "px;
                            margin-top: -" . ( $arrow['prev']['height'] / 2 ) . "px;
                            opacity: {$this->get_arrow_opacity()};
                        }

                        [metaslider] .theme-default .nivo-nextNav {
                            right: {$this->get_setting( 'arrow_indent' )}px;
                            background: transparent url(" . $arrow['next']['url'] . ") " . $arrow['next']['position'] . ";
                            width: " . $arrow['next']['width'] . "px;
                            height: " . $arrow['next']['height'] . "px;
                            margin-top: -" . ( $arrow['next']['height'] / 2 ) . "px;
                            opacity: {$this->get_arrow_opacity()};
                        }";
        }

        $theme .= " [metaslider] .theme-default .nivoSlider {
                        -webkit-box-shadow: none;
                        -moz-box-shadow: none;
                        box-shadow: none;
                        overflow: visible;
                    }";

        return $theme;
    }

    /**
     * Return the CSS required to apply the theme to responsive slides.
     *
     * @param int     $id       slideshow ID
     * @param array   $settings slideshow settings
     * @param array   $arrow    arrow information
     * @return string theme CSS
     */
    private function get_responsive_theme( $arrow ) {

        $theme = "";

        if ( $this->get_setting( 'outer_border_radius' ) > 0 ) {
            $theme .= " [metaslider] .rslides,
                        [metaslider] .rslides img {
                            border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                        }";
        }

        if ( $this->slider_settings['navigation'] != 'hidden' && $this->get_setting( 'enable_custom_navigation' ) == 'enabled' ) {
            $theme .= " [metaslider] .rslides_tabs li {
                            line-height: {$this->get_setting( 'dot_size' )}px;
                        }

                        [metaslider] .rslides_tabs li a {
                            padding: 0;
                            box-shadow: none;
                            text-indent: -9999px;
                            border-style: solid;
                            display: inline-block;
                            border-color: {$this->get_setting( 'dot_border_colour' )};
                            border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            border-width: {$this->get_setting( 'dot_border_width' )}px;
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'dot_border_colour' )};
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            width: {$this->get_setting( 'dot_size' )}px;
                            height: {$this->get_setting( 'dot_size' )}px;
                            margin-left: {$this->get_setting( 'dot_spacing' )}px;
                            margin-right: {$this->get_setting( 'dot_spacing' )}px;
                            background: {$this->rgba_to_rgb( 'dot_fill_colour_start' )};
                            background: {$this->get_setting( 'dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'dot_fill_colour_start' )}), to({$this->get_setting( 'dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                        }

                        [metaslider] .rslides_tabs li.rslides_here a {
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'active_dot_border_colour' )};
                            background: {$this->rgba_to_rgb( 'active_dot_fill_colour_start' )};
                            background: {$this->get_setting( 'active_dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'active_dot_fill_colour_start' )}), to({$this->get_setting( 'active_dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                        }

                        [metaslider] .rslides_tabs {
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            padding: 0 !important;
                            background: transparent;
                            z-index: 99;
                            margin: {$this->get_setting( 'nav_vertical_margin' )}px {$this->get_setting( 'nav_horizontal_margin' )}px;
                            {$this->get_nav_position_css()}
                        }";
        }

        if ( $this->get_setting( 'enable_custom_caption' ) == 'enabled' ) {
            $theme .= " [metaslider] .rslides .caption-wrap {
                            opacity: 1;
                            margin: {$this->get_setting( 'caption_vertical_margin' )}px {$this->get_setting( 'caption_horizontal_margin' )}px;
                            color: {$this->get_setting( 'caption_text_colour' )};
                            background: {$this->rgba_to_rgb( 'caption_background_colour' )};
                            background: {$this->get_setting( 'caption_background_colour' )};
                            {$this->get_caption_position_css()}
                            {$this->get_caption_text_align_css()}
                            border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                        }";
        }

        if ( $this->get_setting( 'enable_custom_arrows' ) == 'enabled' ) {
            $theme .= " [metaslider] .rslides_nav {
                            padding: 0;
                            text-indent: -9999px;
                            background-color: transparent;
                            margin-top: -" . ( $arrow['prev']['height'] / 2 ) . "px;
                            opacity: " . $this->get_setting( 'arrow_opacity' ) / 100 . ";
                        }

                        [metaslider] .rslides_nav.prev {
                            left: {$this->get_setting( 'arrow_indent' )}px;
                            background: transparent url(" . $arrow['prev']['url'] . ") " . $arrow['prev']['position'] . ";
                            width: " . $arrow['prev']['width'] . "px;
                            height: " . $arrow['prev']['height'] . "px;
                        }

                        [metaslider] .rslides_nav.next {
                            right: {$this->get_setting( 'arrow_indent' )}px;
                            background: transparent url(" . $arrow['next']['url'] . ") " . $arrow['next']['position'] . ";
                            width: " . $arrow['next']['width'] . "px;
                            height: " . $arrow['next']['height'] . "px;
                        }";
        }

        return $theme;
    }

    /**
     * Return the CSS required to apply the theme to coin slider.
     *
     * @param int     $id       slideshow ID
     * @param array   $settings slideshow settings
     * @param array   $arrow    arrow information
     * @return string theme CSS
     */
    private function get_coin_theme( $arrow ) {

        $theme = "";

        if ( $this->slider_settings['navigation'] != 'hidden' && $this->get_setting( 'enable_custom_navigation' ) == 'enabled' ) {
            $theme .= " [metaslider] .cs-buttons a {
                            padding: 0;
                            box-shadow: none;
                            text-indent: -9999px;
                            border-style: solid;
                            display: inline-block;
                            border-color: {$this->get_setting( 'dot_border_colour' )};
                            border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            border-width: {$this->get_setting( 'dot_border_width' )}px;
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'dot_border_colour' )};
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            width: {$this->get_setting( 'dot_size' )}px;
                            height: {$this->get_setting( 'dot_size' )}px;
                            margin-left: {$this->get_setting( 'dot_spacing' )}px;
                            margin-right: {$this->get_setting( 'dot_spacing' )}px;
                            background: {$this->rgba_to_rgb( 'dot_fill_colour_start' )};
                            background: {$this->get_setting( 'dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'dot_fill_colour_start' )}), to({$this->get_setting( 'dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                        }

                        [metaslider] .cs-buttons a.cs-active {
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'active_dot_border_colour' )};
                            background: {$this->rgba_to_rgb( 'active_dot_fill_colour_start' )};
                            background: {$this->get_setting( 'active_dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'active_dot_fill_colour_start' )}), to({$this->get_setting( 'active_dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                        }

                        [metaslider] .cs-buttons {
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            padding: 0 !important;
                            background: transparent !important;
                            z-index: 99;
                            margin: {$this->get_setting( 'nav_vertical_margin' )}px {$this->get_setting( 'nav_horizontal_margin' )}px;
                            {$this->get_nav_position_css( true )}
                        }";
        }

        if ( $this->get_setting( 'enable_custom_caption' ) == 'enabled' ) {
            $theme .= " [metaslider] .cs-title {
                            margin: {$this->get_setting( 'caption_vertical_margin' )}px {$this->get_setting( 'caption_horizontal_margin' )}px;
                            color: {$this->get_setting( 'caption_text_colour' )};
                            background: {$this->rgba_to_rgb( 'caption_background_colour' )};
                            background: {$this->get_setting( 'caption_background_colour' )};
                            {$this->get_caption_position_css( true )}
                            {$this->get_caption_text_align_css()}
                            border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                        }";
        }

        if ( $this->get_setting( 'enable_custom_arrows' ) == 'enabled' ) {
            $theme .= " [metaslider] .cs-prev,
                        [metaslider] .cs-next {
                            padding: 0 !important;
                            text-indent: -9999px;
                            background-color: transparent;
                            margin-top: -" . ( $arrow['prev']['height'] / 2 ) . "px !important;
                            top: 50% !important;
                            opacity: " . $this->get_setting( 'arrow_opacity' ) / 100 . "!important;
                        }

                        [metaslider] .cs-prev {
                            left: {$this->get_setting( 'arrow_indent' )}px !important;
                            background: transparent url(" . $arrow['prev']['url'] . ") " . $arrow['prev']['position'] . ";
                            width: " . $arrow['prev']['width'] . "px;
                            height: " . $arrow['prev']['height'] . "px;
                        }

                        [metaslider] .cs-next {
                            right: {$this->get_setting( 'arrow_indent' )}px !important;
                            background: transparent url(" . $arrow['next']['url'] . ") " . $arrow['next']['position'] . ";
                            width: " . $arrow['next']['width'] . "px;
                            height: " . $arrow['next']['height'] . "px;
                        }";
        }

        if ( $this->get_setting( 'arrows_always_show' ) == 'enabled' ) {
            $theme .= " [metaslider] #cs-navigation-metaslider_{$id} {
                            display: block !important;
                        }";
        }



        return $theme;

    }

    /**
     * Return the CSS required to apply the theme to flex slider.
     *
     * @param int     $id       slideshow ID
     * @param array   $settings slideshow settings
     * @param array   $arrow    arrow information
     * @return string theme CSS
     */
    private function get_flex_theme( $arrow ) {

        $theme = "";

        if ( $this->get_setting( 'outer_border_radius' ) > 0 ) {
            $theme .= " [metaslider] .flexslider,
                        [metaslider] .flexslider img {
                            border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'outer_border_radius' )}px;
                        }";
        }

        if ( $this->slider_settings['navigation'] != 'hidden' && $this->get_setting( 'enable_custom_navigation' ) == 'enabled' ) {
            $theme .= " [metaslider] .flexslider .flex-control-paging li a,
                        [metaslider] .flexslider .flex-control-paging li a:hover {
                            padding: 0;
                            box-shadow: none;
                            text-indent: -9999px;
                            border-style: solid;
                            display: inline-block;
                            border-color: {$this->get_setting( 'dot_border_colour' )};
                            border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'dot_border_radius' )}px;
                            border-width: {$this->get_setting( 'dot_border_width' )}px;
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'dot_border_colour' )};
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            width: {$this->get_setting( 'dot_size' )}px;
                            height: {$this->get_setting( 'dot_size' )}px;
                            margin: 0 {$this->get_setting( 'dot_spacing' )}px;
                            background: {$this->rgba_to_rgb( 'dot_fill_colour_start' )};
                            background: {$this->get_setting( 'dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'dot_fill_colour_start' )}), to({$this->get_setting( 'dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'dot_fill_colour_start' )}, {$this->get_setting( 'dot_fill_colour_end' )});

                        }

                        [metaslider] .flexslider .flex-control-paging li {
                            margin: 0;
                            text-indent: 0;
                            width: auto;
                        }

                        [metaslider] .flexslider .flex-control-paging li a.flex-active {
                            border: {$this->get_setting( 'dot_border_width' )}px solid {$this->get_setting( 'active_dot_border_colour' )};
                            background: {$this->rgba_to_rgb( 'active_dot_fill_colour_start' )};
                            background: {$this->get_setting( 'active_dot_fill_colour_start' )};
                            background: -webkit-gradient(linear, 0% 0%, 0% 100%, from({$this->get_setting( 'active_dot_fill_colour_start' )}), to({$this->get_setting( 'active_dot_fill_colour_end' )}));
                            background: -webkit-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -moz-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -ms-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: -o-linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                            background: linear-gradient(top, {$this->get_setting( 'active_dot_fill_colour_start' )}, {$this->get_setting( 'active_dot_fill_colour_end' )});
                        }

                        [metaslider] .flexslider .flex-control-paging {
                            line-height: {$this->get_setting( 'dot_size' )}px;
                            z-index: 99;
                            padding: 0;
                            text-align: left;
                            margin: {$this->get_setting( 'nav_vertical_margin' )}px {$this->get_setting( 'nav_horizontal_margin' )}px;
                            {$this->get_nav_position_css()}
                        }";
        }

        if ( $this->get_setting( 'enable_custom_caption' ) == 'enabled' ) {
            $theme .= " [metaslider] .flexslider .caption-wrap {
                            opacity: 1;
                            margin: {$this->get_setting( 'caption_vertical_margin' )}px {$this->get_setting( 'caption_horizontal_margin' )}px;
                            color: {$this->get_setting( 'caption_text_colour' )};
                            background: {$this->rgba_to_rgb( 'caption_background_colour' )};
                            background: {$this->get_setting( 'caption_background_colour' )};
                            {$this->get_caption_position_css()}
                            {$this->get_caption_text_align_css()}
                            border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -webkit-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                            -moz-border-radius: {$this->get_setting( 'caption_border_radius' )}px;
                        }";
        }

        if ( $this->get_setting( 'enable_custom_arrows' ) == 'enabled' ) {
            $theme .= " [metaslider] .flexslider .flex-direction-nav .flex-prev {
                            background: transparent url(" . $arrow['prev']['url'] . ") " . $arrow['prev']['position']  . " no-repeat;
                            margin-top: -" . ( $arrow['prev']['height'] / 2 ) . "px;
                            width: " . $arrow['prev']['width'] . "px;
                            height: " . $arrow['prev']['height'] . "px;
                            opacity: {$this->get_arrow_opacity()};
                            left: {$this->get_arrow_indent()};
                            padding: 0;
                        }

                        [metaslider] .flexslider .flex-direction-nav .flex-next {
                            background: transparent url(" . $arrow['next']['url'] . ") " . $arrow['next']['position'] . " no-repeat;
                            margin-top: -" . ( $arrow['next']['height'] / 2 ) . "px;
                            width: " . $arrow['next']['width'] . "px;
                            height: " . $arrow['next']['height'] . "px;
                            opacity: {$this->get_arrow_opacity()};
                            right: {$this->get_arrow_indent()};
                            padding: 0;
                        }

                        [metaslider] .flexslider:hover .flex-direction-nav .flex-prev {
                            left: {$this->get_arrow_indent_hover()};
                            opacity: {$this->get_arrow_opacity_hover()};
                        }

                        [metaslider] .flexslider:hover .flex-direction-nav .flex-next {
                            right: {$this->get_arrow_indent_hover()};
                            opacity: {$this->get_arrow_opacity_hover()};
                        }";
        }

        return $theme;
    }

    /**
     * Convert RGBA to RGB (for browsers that don't support RGBA)
     */
    private function rgba_to_rgb( $setting ) {
        $rgba_value = $this->get_setting( $setting );

        // find the "(X, X, X, X)" value
        preg_match( '/\((.*?)\)/', $rgba_value, $match );

        if ( isset( $match ) ) {
            // split into separate values
            $rgb_vals = explode( ",", $match[1] );

            $r = isset( $rgb_vals[0] ) ? $rgb_vals[0] : 0;
            $g = isset( $rgb_vals[1] ) ? $rgb_vals[1] : 0;
            $b = isset( $rgb_vals[2] ) ? $rgb_vals[2] : 0;

            // return RGB value (without alpha)
            return "rgb({$r},  {$g},  {$b})";
        }

        // default: return rgb code for black;
        return "rgb(0,  0,  0)";
    }

    /**
     * Return the arrow indent
     */
    public function get_arrow_indent() {
        if ( $this->get_setting( 'arrows_always_show' ) == 'enabled' ) {
            return $this->get_setting( 'arrow_indent' ) . 'px';
        }

        return $this->get_setting( 'arrow_indent' ) - 5 . 'px';
    }

    /**
     * Return the arrow indent when hovering over the slideshow
     */
    public function get_arrow_indent_hover() {
        return $this->get_setting( 'arrow_indent' ) . 'px';
    }


    /**
     * Return the arrow opacity when hovering over the slideshow
     */
    public function get_arrow_opacity_hover() {
        return $this->get_setting( 'arrow_opacity' ) / 100;
    }

    /**
     * Return the arrow opacity
     */
    public function get_arrow_opacity() {
        if ( $this->get_setting( 'arrows_always_show' ) == 'enabled' ) {
            return $this->get_setting( 'arrow_opacity' ) / 100;
        }

        return 0;
    }

    /**
     * Return CSS rules for the caption positioning
     *
     * @param array   $settings
     * @param bool    $important
     * @return string
     */
    public function get_caption_text_align_css() {
        $position = $this->get_setting( 'caption_align' );

        if ( $position != 'left' ) {
            return "text-align: {$position};";
        }
    }

    /**
     * Return CSS rules for the caption positioning
     *
     * @param array   $settings
     * @param bool    $important
     * @return string
     */
    public function get_caption_position_css( $important = false ) {
        $position = $this->get_setting( 'caption_position' );
        $width = $this->get_setting( 'caption_width' );

        $captionPosition['width'] = $width . "%";
        $captionPosition['top'] = 'auto';
        $captionPosition['right'] = 'auto';
        $captionPosition['bottom'] = 'auto';
        $captionPosition['left'] = 'auto';
        $captionPosition['clear'] = 'none';
        $captionPosition['position'] = 'absolute';

        if ( $position == 'topCenter' || $position == 'bottomCenter' ) {
            $captionPosition['width'] = '100%';
        }

        if ( $position == 'topRight' ) {
            $captionPosition['top'] = 0;
            $captionPosition['right'] = 0;
        }

        if ( $position == 'topLeft' ) {
            $captionPosition['top'] = 0;
            $captionPosition['left'] = 0;
        }

        if ( $position == 'bottomLeft' ) {
            $captionPosition['bottom'] = 0;
            $captionPosition['left'] = 0;
        }

        if ( $position == 'bottomRight' ) {
            $captionPosition['bottom'] = 0;
            $captionPosition['right'] = 0;
        }

        if ( $position == 'underneath' ) {
            $captionPosition['width'] = '100%';
            $captionPosition['top'] = 'auto';
            $captionPosition['right'] = 'auto';
            $captionPosition['bottom'] = 'auto';
            $captionPosition['left'] = 'auto';
            $captionPosition['clear'] = 'both';
            $captionPosition['position'] = 'relative';
        }

        $important = $important ? ' !important' : '';

        foreach ( $captionPosition as $key => $value ) {
            $rules[] = $key . ": " . $value . $important . ";";
        }
        return implode( "\n            ", $rules );
    }

    /**
     * Return an HTML select list of the available arrow options
     *
     * @return string
     */
    public function get_arrow_options() {
        $arrow_select_options = "";
        $selected_arrow_type = $this->get_setting( 'arrow_type' );
        $arrows = $this->get_arrows();

        foreach ( $arrows as $id => $vals ) {
            $arrow_select_options .= "<option value='{$id}' data-height='{$vals['height']}' data-width='{$vals['width']}' data-offset='{$vals['offset']}'";

            if ( $id == $selected_arrow_type ) {
                $arrow_select_options .= " selected=selected";
            }

            $arrow_select_options .= ">" . __( "Type", 'metasliderpro' ) . " {$id}</option>";
        }

        return $arrow_select_options;
    }

    /**
     * Return an HTML select list of the available arrow colours
     *
     * @return string
     */
    public function get_arrow_colours() {
        $selected_arrow_colour = $this->get_setting( 'arrow_colour' );

        $colours = array(
            __("Black", "metasliderpro") => 'black',
            __("Blue", "metasliderpro") => 'blue',
            __("Green", "metasliderpro") => 'green',
            __("Grey", "metasliderpro") => 'grey',
            __("Navy", "metasliderpro") => 'navy',
            __("Purple", "metasliderpro") => 'purple',
            __("Red", "metasliderpro") => 'red',
            __("White", "metasliderpro") => 'white',
            __("Yellow", "metasliderpro") => 'yellow'
        );

        $arrow_colour_options = "";

        foreach ( $colours as $name => $colour ) {
            $arrow_colour_options .= "<option value='{$colour}' data-url='" . plugins_url( 'assets/arrows/' , __FILE__ ) . $colour . ".png'";

            if ( $colour == $selected_arrow_colour ) {
                $arrow_colour_options .= " selected=selected";
            }

            $arrow_colour_options .= ">" . $name . "</option>";
        }

        return $arrow_colour_options;
    }
}