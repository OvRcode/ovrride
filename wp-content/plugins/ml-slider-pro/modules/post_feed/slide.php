<?php

// disable direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Post Feed Slide
 */
class MetaPostFeedSlide extends MetaSlide {

    public $identifier = "post_feed"; // should be lowercase, one word (use underscores if needed)
    public $name = "Post Feed"; // slide type title


    /**
     * Register slide type
     */
    public function __construct() {
    }

    /**
     * Register hooks
     */
    public function hooks() {

        if ( is_admin() ) {
            add_filter( "media_upload_tabs", array( $this, "custom_media_upload_tab_name" ), 999, 1 );
            add_action( "metaslider_save_{$this->identifier}_slide", array( $this, "save_slide" ), 5, 3 );
            add_action( "media_upload_{$this->identifier}", array( $this, "get_iframe" ) );
            add_action( "wp_ajax_create_{$this->identifier}_slide", array( $this, "ajax_create_slide" ) );
            add_action( "metaslider_register_admin_styles", array( $this, "register_admin_styles" ), 10, 1 );
        }

        add_filter( "metaslider_get_{$this->identifier}_slide", array( $this, "get_slide" ), 10, 2 );

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
     * @param array   existing media manager tabs
     * @return array new media manager tabs
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
     * Create a new post feed slide
     */
    public function ajax_create_slide() {

        $slider_id = intval( $_POST['slider_id'] );
        $this->create_slide( $slider_id );
        echo $this->get_admin_slide();
        die(); // this is required to return a proper result

    }


    /**
     * Extract the slide setings
     */
    public function set_slide( $id ) {

        parent::set_slide( $id );
        $this->slide_settings = get_post_meta( $id, 'ml-slider_settings', true );

    }


    /**
     * Admin slide html
     *
     * @return string html
     */
    protected function get_admin_slide() {

        $row  = "<tr class='slide post_feed flex responsive coin nivo'>";
        $row .= "    <td class='col-1'>";
        $row .= "        <div class='thumb post_feed'>";

        if ( method_exists( $this, 'get_delete_button_html' ) ) {
            $row .= $this->get_delete_button_html();
        }

        $row .= "            <span class='slide-details'>" . __( "Post Feed Slide", "metasliderpro" ) . "</span>";
        $row .= "        </div>";
        $row .= "    </td>";
        $row .= "    <td class='col-2'>";

        if ( method_exists( $this, 'get_admin_slide_tabs_html' ) ) {
            $row .= $this->get_admin_slide_tabs_html();
        } else {
            $row .= "<p>" . __("Please update to Meta Slider to version 3.0 or above.", "metasliderpro") . "</p>";
        }

        $row .= "        <input type='hidden' name='attachment[{$this->slide->ID}][type]' value='post_feed' />";
        $row .= "        <input type='hidden' class='menu_order' name='attachment[{$this->slide->ID}][menu_order]' value='{$this->slide->menu_order}' />";
        $row .= "    </td>";
        $row .= "</tr>";

        return $row;
    }

    /**
     * Build an array of tabs and their titles to use for the admin slide.
     */
    public function get_admin_tabs() {

        $custom_template = isset( $this->slide_settings['custom_template'] ) &&  strlen( $this->slide_settings['custom_template'] ) ? $this->slide_settings['custom_template'] : $this->backwards_compatible_caption();
        $nl2br_checked = ! isset( $this->slide_settings['nl2br'] ) || $this->slide_settings['nl2br'] == 'on' ? 'checked=checked' : '';
        $slide_id = absint( $this->slide->ID);


        $template_tab = "<div style='position: relative;'>{$this->get_template_tags()}</div>
                         <div class='row last'>
                            <textarea class='wysiwyg' id='editor{$slide_id}' name='attachment[{$slide_id}][settings][custom_template]'>{$custom_template}</textarea>
                         </div>";

        $post_types_tab = "<p>" . __( "Select the Post types to include in the feed. Posts must have a Featured Image to appear in the feed.", "metasliderpro" ) . "</p>" . $this->get_post_type_options();

        $taxonomies_tab = "<p>" . __( "Posts must be tagged to at least one of the selected categories to display in the feed.", "metasliderpro" ) . "</p>" . $this->get_tag_options();

        $display_tab = "<div class='row'>
                           <label>" . __( "Slide Link", "metasliderpro" ) . "</label>
                           {$this->get_link_to_options()}
                        </div>
                        <div class='row'>
                            <label>" . __( "Order By", "metasliderpro" ) . "</label>
                            {$this->get_order_by_options()}{$this->get_order_direction_options()}
                        </div>
                        <div class='row'>
                             <label>" . __( "Post Limit", "metasliderpro" ) . "</label>
                             {$this->get_limit_options()}
                        </div>
                        <div class='row'>
                            <label>" . __( "Preserve New Lines", "metasliderpro" ) . "</label>
                            <input type='checkbox' name='attachment[{$this->slide->ID}][settings][nl2br]' {$nl2br_checked}/>
                        </div>";

        $tabs = array(
            'caption_template' => array(
                'title' => __( "Caption Template", "metasliderpro" ),
                'content' => $template_tab
            ),
            'post_types' => array(
                'title' => __( "Post Types", "metasliderpro" ),
                'content' => $post_types_tab
            ),
            'taxonomies' => array(
                'title' => __( "Taxonomies", "metasliderpro" ),
                'content' => $taxonomies_tab
            ),
            'display_settings' => array(
                'title' => __( "Display Settings", "metasliderpro" ),
                'content' => $display_tab
            ),
        );

        return apply_filters("metaslider_post_feed_slide_tabs", $tabs, $this->slide, $this->slider, $this->settings);

    }


    /**
     *
     */
    private function backwards_compatible_caption() {
        $selected_caption_type = isset( $this->slide_settings['caption'] ) ? $this->slide_settings['caption'] : 'title';

        switch ( $selected_caption_type ) {
        case "disabled":
            return "";
        case "title_and_excerpt":
            return "<div class='post_title'>{title}</div><div class='post_excerpt'>{excerpt}</div>";
            break;
        default:
            return "{" . $selected_caption_type . "}";
        }
    }


    /**
     * Returns a nested list of taxonomies
     *
     * @return string html
     */
    private function get_tag_options() {
        ob_start();

        echo "<div class='scroll'>";

        $taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );


        foreach ( $taxonomies as $taxonomy ) {

            $terms = get_terms( $taxonomy->name );

            if ( ! empty( $terms ) ) {
                echo "<ul><li class='header'>{$taxonomy->label}</li>";

                $args = apply_filters("metaslider_post_feed_wp_terms_checklist_args", array(
                        'taxonomy'  => $taxonomy->name,
                        'selected_cats' => $this->get_selected_tags( $taxonomy->name ),
                        'walker' => new Walker_MetaSlider_Checklist( $this->slide->ID ),
                        'checked_ontop' => false,
                        'popular_cats' => false
                ));

                wp_terms_checklist( 0, $args);

                echo "</ul>";
            }
        }

        echo "</div>";

        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }


    /**
     * Generate the template tag drop down list
     *
     * @return string drop down list HTML
     */
    private function get_template_tags() {

        $options = array(
            __( "Post Title", 'metasliderpro' ) => '{title}',
            __( "Post ID", 'metasliderpro' ) => '{id}',
            __( "Post Link", 'metasliderpro' ) => '{link}',
            __( "Post Excerpt", 'metasliderpro' ) => '{excerpt}',
            __( "Post Date", 'metasliderpro' ) => '{date}',
            __( "Post Content", 'metasliderpro' ) => '{content}',
            __( "Post Content (with formatting)", 'metasliderpro' ) => '{content_with_formatting}',
            __( "Author", 'metasliderpro' )  => '{author}',
            __( "Author Link", 'metasliderpro' ) => '{author_link}',
            __( "Tag List", 'metasliderpro' ) => '{tags}',
            __( "Category List", 'metasliderpro' )  => '{cats}'
        );

        // add in WooCommerce options
        if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

            $wc_fields = array(
                __( 'Woo Commerce - Price (Formatted)', 'metasliderpro' ) => '{wc_price_formatted}',
                __( 'Woo Commerce - Price', 'metasliderpro' ) => '{wc_price}',
                __( 'Woo Commerce - Sale Price', 'metasliderpro' ) => '{wc_sale_price}',
                __( 'Woo Commerce - Add to Cart URL', 'metasliderpro' ) => '{wc_add_to_cart_url}',
                __( 'Woo Commerce - SKU', 'metasliderpro' ) => '{wc_sku}',
            );

            $options = array_merge( $options, $wc_fields );
        }

        // add in The Events Calendar options
        if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {

            $tec_fields = array(
                __( "Events Calendar - Date", 'metasliderpro' )  => '{event_date}',
                __( "Events Calendar - Start Date", 'metasliderpro' )  => '{event_start_date}',
                __( "Events Calendar - Start Time", 'metasliderpro' )  => '{event_start_time}',
                __( "Events Calendar - End Date", 'metasliderpro' )  => '{event_end_date}',
                __( "Events Calendar - End Time", 'metasliderpro' )  => '{event_end_time}',
                __( "Events Calendar - Address", 'metasliderpro' )  => '{event_address}',
                __( "Events Calendar - City", 'metasliderpro' )  => '{event_city}',
                __( "Events Calendar - Country", 'metasliderpro' )  => '{event_country}',
                __( "Events Calendar - Full Address", 'metasliderpro' )  => '{event_full_address}',
                __( "Events Calendar - Phone", 'metasliderpro' )  => '{event_phone}',
                __( "Events Calendar - Province", 'metasliderpro' )  => '{event_province}',
                __( "Events Calendar - Region", 'metasliderpro' )  => '{event_region}',
                __( "Events Calendar - State", 'metasliderpro' )  => '{event_state}',
                __( "Events Calendar - State / Province", 'metasliderpro' )  => '{event_stateprovince}',
                __( "Events Calendar - Venue", 'metasliderpro' )  => '{event_venue}',
                __( "Events Calendar - Venue ID", 'metasliderpro' )  => '{event_venue_id}',
                __( "Events Calendar - Venue Link", 'metasliderpro' )  => '{event_venue_link}',
                __( "Events Calendar - Zip", 'metasliderpro' )  => '{event_zip}'
            );

            $options = array_merge( $options, $tec_fields );
        }

        // apply filters
        $options = apply_filters( "metaslider_post_feed_template_tags", $options );

        // start building the HTML
        $html = "<select name='template_tags' id='template_tags'>";

        $html .= "<option disabled='disabled' selected='selected' id='insert_tag'>" . __( "Insert Tag", 'metasliderpro' ) . "</option>";

        foreach ( $options as $title => $value ) {
            $html .= "<option value='{$value}'>{$title}</option>";
        }

        // add in all custom fields as tag options
        $html .= "<optgroup label='" . __( "Custom Fields", 'metasliderpro' ) . "'>";

        foreach ( $this->get_custom_fields() as $key ) {
            $html .= "<option value='{{$key}}'>{$key}</option>";
        }

        $html .= "</optgroup>";

        $html .= "</select>";

        return $html;
    }


    /**
     * Generate the order by drop down list
     *
     * @return string drop down list HTML
     */
    private function get_order_by_options() {
        $selected_option = isset( $this->slide_settings['order_by'] ) ? $this->slide_settings['order_by'] : 'date';

        $options = array(
            __( 'Publish Date', 'metasliderpro') => 'date',
            __( 'Post ID', 'metasliderpro') => 'ID',
            __( 'Author', 'metasliderpro') => 'author',
            __( 'Post Title', 'metasliderpro') => 'title',
            __( 'Post Slug', 'metasliderpro') => 'name',
            __( 'Modified Date', 'metasliderpro') => 'modified',
            __( 'Random', 'metasliderpro') => 'rand',
            __( 'Menu Order', 'metasliderpro') => 'menu_order'
        );

        // add in sort by Event Date option from The Events Calendar
        if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {

            $orderby_event = array(
                __( 'Event Date', 'metasliderpro') => 'meta_value_num'
            );

            $options = array_merge( $options, $orderby_event );
        }

        $html = "<select name='attachment[{$this->slide->ID}][settings][order_by]'>";

        foreach ( $options as $title => $value ) {
            $selected = $value == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "</select>";

        return $html;
    }


    /**
     * Generate the limit drop down list
     *
     * @return string drop down list HTML
     */
    private function get_limit_options() {
        $number_of_posts = isset( $this->slide_settings['number_of_posts'] ) ? $this->slide_settings['number_of_posts'] : 3;

        $html = "<input value='{$number_of_posts}' type='number' step='1' min='1' max='30' name='attachment[{$this->slide->ID}][settings][number_of_posts]'>";

        return $html;
    }


    /**
     * Return a list of all custom fields registered in the database.
     *
     * @return array
     */
    private function get_custom_fields() {
        global $wpdb;

        $limit = (int) apply_filters( 'postmeta_form_limit', 30 );

        $keys = $wpdb->get_col( "
            SELECT meta_key
            FROM $wpdb->postmeta
            GROUP BY meta_key
            HAVING meta_key NOT LIKE '\_%'
            ORDER BY meta_key
            LIMIT $limit" );

        if ( $keys ) {
            natcasesort( $keys );

            return $keys;
        }

        return array();
    }


    /**
     * Generate a drop down list with custom field options
     *
     * @param array   $options
     * @param string  $default
     * @param string  $name
     *
     * @return string drop down list HTML
     */
    private function get_dropdown_select_with_custom_fields( $options, $default, $name ) {
        $selected_option = isset( $this->slide_settings[$name] ) ? $this->slide_settings[$name] : $default;

        $html = "<select class='{$name}' name='attachment[{$this->slide->ID}][settings][{$name}]'>";
        foreach ( $options as $title => $value ) {
            $selected = $value == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "<optgroup label='Custom Fields'>";
        foreach ( $this->get_custom_fields() as $key ) {
            $selected = $key == $selected_option ? "selected='selected'" : "";
            $html .= "<option value='{$key}' {$selected}>{$key}</option>";
        }
        $html .= "</optgroup>";
        $html .= "</select>";

        return $html;
    }


    /**
     * Generate the 'link to' down list
     *
     * @return string drop down list HTML
     */
    private function get_link_to_options() {
        $options = array(
            __( 'Disabled', "metasliderpro" ) => 'disabled',
            __( 'Post', "metasliderpro" ) => 'slug',
        );

        return $this->get_dropdown_select_with_custom_fields( $options, 'slug', 'link_to' );
    }


    /**
     * Generate the sort direction drop down list
     *
     * @return string drop down list HTML
     */
    private function get_order_direction_options() {
        $selected_direction = isset( $this->slide_settings['order'] ) ? $this->slide_settings['order'] : 'DESC';

        $options = array(
            __( 'DESC', "metasliderpro" ) => 'DESC',
            __( 'ASC', "metasliderpro" ) => 'ASC'
        );

        $html = "<select name='attachment[{$this->slide->ID}][settings][order]'>";

        foreach ( $options as $title => $value ) {
            $selected = $value == $selected_direction ? "selected='selected'" : "";
            $html .= "<option value='{$value}' {$selected}>{$title}</option>";
        }

        $html .= "</select>";

        return $html;
    }



    /**
     * Generate post type selection list HTML
     *
     * @return string html
     */
    private function get_post_type_options() {
        $all_post_types = get_post_types( array( 'public' => 'true' ), 'objects' );
        $selected_post_types =$this->get_selected_post_types();

        $exclude = apply_filters( "metaslider_post_feed_exclude_post_types", array( "page", "attachment" ) );

        $options = "";

        foreach ( $all_post_types as $post_type ) {
            if ( ! in_array( $post_type->name, $exclude ) ) {
                $checked = in_array( $post_type->name, $selected_post_types ) ? "checked='checked'" : "";
                $options .= "<li><label><input type='checkbox' name='attachment[{$this->slide->ID}][settings][post_types][]' value='{$post_type->name}' {$checked} /> {$post_type->label}</label></li>";
            }
        }

        return "<div class='scroll'><ul>" . $options . "</ul></div>";
    }


    /**
     * Get the selected order direction
     *
     * @return string ASC or DESC
     */
    private function get_order() {
        return isset( $this->slide_settings['order'] ) ? $this->slide_settings['order'] : 'ASC';
    }


    /**
     * Get the selected order field
     *
     * @return string field name
     */
    private function get_order_by() {

        return isset( $this->slide_settings['order_by'] ) ? $this->slide_settings['order_by'] : 'date';

    }


    /**
     * Get the selected limit
     *
     * @return int number of posts to display
     */
    private function get_number_of_posts() {

        return isset( $this->slide_settings['number_of_posts'] ) ? $this->slide_settings['number_of_posts'] : 5;

    }


    /**
     * Get the selected tags
     *
     * @param string  $taxonomy_name
     * @return array selected tag IDs
     */
    private function get_selected_tags( $taxonomy_name ) {

        $selected = array();

        if ( isset( $this->slide_settings['tags'] ) && count( $this->slide_settings['tags'] ) ) {
            foreach ( $this->slide_settings['tags'] as $tax => $tags ) {
                if ( $tax == $taxonomy_name ) {
                    foreach ( $tags as $tag ) {
                        $selected[] = (int)$tag;
                    }
                }
            }
        }

        return $selected;

    }


    /**
     * Get selected post types
     *
     * @return array selected post types
     */
    private function get_selected_post_types() {

        if ( isset( $this->slide_settings['post_types'] ) && count( $this->slide_settings['post_types'] ) ) {
            foreach ( $this->slide_settings['post_types'] as $key => $value ) {
                $post_types[] = $value;
            }
        } else {
            $post_types[] = 'post';
        }


        return $post_types;

    }


    /**
     * Build the query to extract the posts to display
     *
     * @return WP_Query
     */
    public function get_post_args() {

        $args['post_type'] = $this->get_selected_post_types();
        $args['posts_per_page'] = $this->get_number_of_posts();
        $args['orderby'] = $this->get_order_by();
        $args['order'] = $this->get_order();
        $args['meta_query'][] = array( 'key' => '_thumbnail_id' );

        // The Event Calendar, start date
        if ( $args['orderby'] === 'meta_value_num' ) {
            $args['meta_query'][] = array( 'key' => '_EventStartDate' );
        }

        // add taxonomy limits
        if ( isset( $this->slide_settings['tags'] ) && count( $this->slide_settings['tags'] ) ) {
            $args['tax_query'] = array( 'relation' => 'OR' );

            foreach ( $this->slide_settings['tags'] as $tax => $tags ) {
                $selected = array(); // reset the array

                foreach ( $tags as $tag ) {
                    $selected[] = (int)$tag; // list all checked categories for this taxonomy
                }

                if ( count( $selected ) ) {
                    $args['tax_query'][] = array(
                        'taxonomy' => $tax,
                        'field' => 'id',
                        'terms' => $selected
                    );
                }
            }
        }

        $args = apply_filters( 'metaslider_post_feed_args', $args, $this->slide, $this->settings, $this->slide_settings );

        return $args;

    }



    /**
     * Loop through the posts and build an array of slide HTML.
     *
     * @return array
     */
    protected function get_public_slide() {

        $slider_settings = get_post_meta( $this->slider->ID, 'ml-slider_settings', true );
        $the_query = new WP_Query( $this->get_post_args() );

        $slides = array();

        while ( $the_query->have_posts() ) {

            $the_query->the_post();
            $id = get_post_thumbnail_id( $the_query->post->ID );

            if ( $override_id = get_post_meta( $the_query->post->ID, 'metaslider_post_feed_image', true ) ) {
                if ( wp_attachment_is_image( $override_id ) ) {
                    $id = $override_id;
                }
            }

            $thumb = false;

            if ( $id > 0 ) {
                // initialise the image helper
                $imageHelper = new MetaSliderImageHelper(
                    $id,
                    $slider_settings['width'],
                    $slider_settings['height'],
                    isset( $slider_settings['smartCrop'] ) ? $slider_settings['smartCrop'] : 'false'
                );
                $thumb = $imageHelper->get_image_url();
            }

            // go on to the next slide if we encounter an error
            if ( is_wp_error( $thumb ) || !$thumb ) {
                continue;
            }

            $selected_url_type = isset( $this->slide_settings['link_to'] ) ? $this->slide_settings['link_to'] : 'slug';

            switch ( $selected_url_type ) {
                case "slug":
                    $url = get_permalink();
                    break;
                case "disabled":
                    $url = "";
                    break;
                default:
                    $url = get_post_meta( $the_query->post->ID, $selected_url_type, true );
            }

            $caption = $this->render_custom_template();

            $caption = apply_filters( "metaslider_post_feed_caption", $caption, $this->slider->ID, $slider_settings, $the_query->post );

            $caption = do_shortcode( $caption );

            $image_id = get_post_thumbnail_id( $the_query->post->ID );

            $slide = array(
                'id' => $image_id,
                'thumb' => $thumb,
                'width' => $this->settings['width'],
                'height' => $this->settings['height'],
                'class' => "slider-{$this->slider->ID} slide-{$this->slide->ID} post-{$the_query->post->ID} ms-postfeed",
                'url' => $url,
                'title' => "",
                'alt' => get_post_meta( get_post_thumbnail_id( $the_query->post->ID ), "_wp_attachment_image_alt", true ),
                'target' => "_self",
                'caption' => html_entity_decode( $caption, ENT_NOQUOTES, 'UTF-8' ),
                'caption_raw' => $caption,
                'excerpt' => get_the_excerpt(),
                'rel' => ""
            );

            $slide = apply_filters( 'metaslider_post_feed_slide_attributes', $slide, $this->slider->ID, $slider_settings );

            switch ( $slider_settings['type'] ) {
                case "coin":
                    $slides[] = $this->get_coin_slider_markup( $slide, $the_query->post->ID );
                    break;
                case "flex":
                    $slides[] = $this->get_flex_slider_markup( $slide, $the_query->post->ID );
                    break;
                case "nivo":
                    $slides[] = $this->get_nivo_slider_markup( $slide, $the_query->post->ID );
                    break;
                case "responsive":
                    $slides[] = $this->get_responsive_slides_markup( $slide, $the_query->post->ID );
                    break;
            }
        }

        wp_reset_query();

        return $slides;

    }


    /**
     * Converts placeholder tags into content.
     */
    private function render_custom_template() {


        $content = isset( $this->slide_settings['custom_template'] ) && strlen( $this->slide_settings['custom_template'] ) ? $this->slide_settings['custom_template'] : $this->backwards_compatible_caption();

        // apply filters first, so we can override the default tags
        $content = apply_filters( "metaslider_post_feed_template", $content );

        $content = str_replace( "{title}", __( get_the_title() ), $content );
        $content = str_replace( "{id}", get_the_ID(), $content );

        if ( strpos( $content, "{content}" ) !== false ) {
            $content = str_replace( "{content}", __( get_the_content() ), $content );
        }

        if ( strpos( $content, "{content_with_formatting}" ) !== false ) {
            $content = str_replace( "{content_with_formatting}", __( str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', get_the_content() ) ) ), $content );
        }

        $content = str_replace( "{excerpt}", __( get_the_excerpt() ), $content );
        $content = str_replace( "{excerpt_nl2br}", __( nl2br( get_the_excerpt() ) ), $content );
        $content = str_replace( "{author}", get_the_author(), $content );
        $content = str_replace( "{author_link}", get_the_author_link(), $content );
        $content = str_replace( "{date}", get_the_date(), $content );
        $content = str_replace( "{thumb}", wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) ), $content );
        $content = str_replace( array( "{link}", "{permalink}" ), get_permalink( get_the_ID() ), $content );
        $content = str_replace( array( "{cat}", "{cats}", "{category}", "{categories}" ), get_the_category_list( ", " ), $content );
        $content = str_replace( array( "{tag}", "{tags}" ),  get_the_tag_list( ", " ), $content );

        $content = $this->process_woocommerce_tags( $content );
        $content = $this->process_the_events_calendar_tags( $content );
        $content = $this->process_custom_tags( $content );

        if ( ! isset( $this->slide_settings['nl2br'] ) || $this->slide_settings['nl2br'] == 'on' ) {
            $content = nl2br( $content );
        }

        return $content;
    }


    /**
     * Process tags for custom fields
     */
    private function process_custom_tags( $content ) {

        // Process anything that is left (custom fields)
        if ( preg_match_all( '/{([^}]*)}/', $content, $matches ) ) {
            foreach ( $matches[1] as $custom_field ) {
                $content = str_replace( "{" . $custom_field . "}", get_post_meta( get_the_ID(), $custom_field, true ), $content );
            }
        }

        return $content;
    }


    /**
     * Process WooCommerce tags
     */
    private function process_woocommerce_tags($content) {

        global $product;

        // WooCommerce
        if ( $product ) {
            $content = str_replace( "{wc_price_formatted}", $product->get_price_html(), $content );
            $content = str_replace( "{wc_add_to_cart_url}", $product->add_to_cart_url(), $content );
            $content = str_replace( "{wc_sku}", $product->get_sku(), $content );
        }

        $content = str_replace( "{wc_price}", get_post_meta( get_the_ID(), '_regular_price', true ), $content );
        $content = str_replace( "{wc_sale_price}", get_post_meta( get_the_ID(), '_sale_price', true ), $content );

        return $content;
    }


    /**
     * Process event calendar tags
     */
    private function process_the_events_calendar_tags($content) {

        if ( ! function_exists('tribe_get_start_date') ) {
            return $content;
        }

        // The Events Calendar
        $event_date_format = get_option('date_format');
        $event_time_format = get_option('time_format');

        $event_all_day = get_post_meta( get_the_ID(), '_EventAllDay', true );

        $event_start_date = tribe_get_start_date( get_the_ID(), false, $event_date_format );
        $event_start_time = tribe_get_start_date( get_the_ID(), false, $event_time_format );
        $event_end_date = tribe_get_end_date( get_the_ID(), false, $event_date_format );
        $event_end_time = tribe_get_end_date( get_the_ID(), false, $event_time_format );

        $separator = apply_filters("metaslider_tribe_separator", " - ");

        if ( $event_all_day ) {
            if ( $event_start_date == $event_end_date ) {
                $event_string = $event_start_date;
            } else {
                $event_string = $event_start_date . $separator . $event_end_date;
            }
        } else if ( $event_start_date == $event_end_date ) {
            $event_string = $event_start_date . " " . $event_start_time . $separator . $event_end_time;
        } else {
            $event_string = $event_start_date . $separator . $event_end_date;
        }

        $content = str_replace( "{event_date}", $event_string, $content );
        $content = str_replace( "{event_start_date}", $event_start_date, $content );
        $content = str_replace( "{event_start_time}", $event_start_time, $content );
        $content = str_replace( "{event_end_time}", $event_end_time, $content );
        $content = str_replace( "{event_end_date}", $event_end_date, $content );
        $content = str_replace( "{event_address}", tribe_get_address( get_the_ID() ), $content );
        $content = str_replace( "{event_city}", tribe_get_city( get_the_ID() ), $content );
        $content = str_replace( "{event_country}", tribe_get_country( get_the_ID() ), $content );
        $content = str_replace( "{event_full_address}", tribe_get_full_address( get_the_ID() ), $content );
        $content = str_replace( "{event_phone}", tribe_get_phone( get_the_ID() ), $content );
        $content = str_replace( "{event_province}", tribe_get_province( get_the_ID() ), $content );
        $content = str_replace( "{event_region}", tribe_get_region( get_the_ID() ), $content );
        $content = str_replace( "{event_state}", tribe_get_state( get_the_ID() ), $content );
        $content = str_replace( "{event_stateprovince}", tribe_get_stateprovince( get_the_ID() ), $content );
        $content = str_replace( "{event_venue}", tribe_get_venue( get_the_ID() ), $content );
        $content = str_replace( "{event_venue_id}", tribe_get_venue_id( get_the_ID() ), $content );
        $content = str_replace( "{event_venue_link}", tribe_get_venue_link( get_the_ID(), false ), $content );
        $content = str_replace( "{event_zip}", tribe_get_zip( get_the_ID() ), $content );

        return $content;
    }


    /**
     * Generate nivo slider markup
     *
     * @param array   $slide
     * @return string slide html
     */
    private function get_nivo_slider_markup( $slide, $post_id ) {

        $attributes = apply_filters( 'metaslider_nivo_slider_image_attributes', array(
                'src' => $slide['thumb'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'data-title' => htmlentities( $slide['caption_raw'], ENT_QUOTES, 'UTF-8' ),
                'title' => $slide['title'],
                'alt' => $slide['alt'],
                'class' => $slide['class'],
                'rel' => $slide['rel']
            ), $slide, $this->slider->ID );

        $html = $this->build_image_tag( $attributes );

        $anchor_attributes = apply_filters( 'metaslider_nivo_slider_anchor_attributes', array(
                'href' => $slide['url'],
                'target' => $slide['target']
            ), $slide, $this->slider->ID );

        if ( strlen( $anchor_attributes['href'] ) ) {
            $html = $this->build_anchor_tag( $anchor_attributes, $html );
        }

        return apply_filters( 'metaslider_image_nivo_slider_markup', $html, $slide, $this->settings );

    }


    /**
     * Generate flex slider markup
     *
     * @param array   $slide
     * @return string slide html
     */
    private function get_flex_slider_markup( $slide, $post_id ) {

        $attributes = apply_filters( 'metaslider_flex_slider_image_attributes', array(
                'src' => $slide['thumb'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'alt' => $slide['alt'],
                'rel' => $slide['rel'],
                'title' => $slide['title']
            ), $slide, $this->slider->ID );

        if ( $this->settings['smartCrop'] == 'disabled_pad') {

            $attributes['style'] = $this->flex_smart_pad( $attributes, $slide );

        }


        $html = $this->build_image_tag( $attributes );

        $anchor_attributes = apply_filters( 'metaslider_flex_slider_anchor_attributes', array(
                'href' => $slide['url'],
                'target' => $slide['target']
            ), $slide, $this->slider->ID );

        if ( strlen( $anchor_attributes['href'] ) ) {
            $html = $this->build_anchor_tag( $anchor_attributes, $html );
        }

        // add caption
        if ( strlen( $slide['caption'] ) ) {
            $html .= "<div class='caption-wrap'><div class='caption'>" . $slide['caption'] . "</div></div>";
        }

        // store the slide details
        $attributes = array(
            'class' => "slide-{$this->slide->ID} ms-postfeed post-{$post_id}",
            'style' => "display: none; width: 100%;"
        );

        $attributes = apply_filters( 'metaslider_flex_slider_li_attributes', $attributes, get_post_thumbnail_id($post_id), $this->slider->ID, $this->settings );

        $li = "<li";

        foreach ( $attributes as $att => $val ) {
            $li .= " " . $att . '="' . esc_attr( $val ) . '"';
        }

        $li .= ">" . $html . "</li>";

        $html = $li;

        return apply_filters( 'metaslider_image_flex_slider_markup', $html, $slide, $this->settings );

    }

    /**
     * Calculate the correct width (for vertical alignment) or top margin (for horizontal alignment)
     * so that images are never stretched above the height set in the slideshow settings
     */
    private function flex_smart_pad( $atts, $slide ) {

        $meta = wp_get_attachment_metadata( $slide['id'] );

        if ( isset( $meta['width'], $meta['height'] ) ) {

            $image_width = $meta['width'];
            $image_height = $meta['height'];
            $container_width = $this->settings['width'];
            $container_height = $this->settings['height'];

            $new_image_height = $image_height * ( $container_width / $image_width );

            if ( $new_image_height < $container_height ) {

                $margin_top_in_px = ( $container_height - $new_image_height ) / 2;

                $margin_top_in_percent = ( $margin_top_in_px / $container_width ) * 100;

                return 'margin-top: ' . $margin_top_in_percent . '%';

            } else {

                return 'margin: 0 auto; width: ' . ( $container_height / $new_image_height ) * 100 . '%';

            }

        }

        return "";

    }

    /**
     * Generate coin slider markup
     *
     * @param array   $slide
     * @return string slide html
     */
    private function get_coin_slider_markup( $slide, $post_id ) {

        $attributes = apply_filters( 'metaslider_coin_slider_image_attributes', array(
                'src' => $slide['thumb'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'alt' => $slide['alt'],
                'rel' => $slide['rel'],
                'class' => $slide['class'],
                'title' => $slide['title'],
                'style' => 'display: none;'
            ), $slide, $this->slider->ID );

        $html = $this->build_image_tag( $attributes );

        if ( strlen( $slide['caption'] ) ) {
            $html .= "<span>{$slide['caption']}</span>";
        }

        $attributes = apply_filters( 'metaslider_coin_slider_anchor_attributes', array(
                'href' => strlen( $slide['url'] ) ? $slide['url'] : 'javascript:void(0)'
            ), $slide, $this->slider->ID );

        $html = $this->build_anchor_tag( $attributes, $html );

        return apply_filters( 'metaslider_image_coin_slider_markup', $html, $slide, $this->settings );

    }


    /**
     * Generate responsive slides markup
     *
     * @return string slide html
     */
    private function get_responsive_slides_markup( $slide, $post_id ) {

        $attributes = apply_filters( 'metaslider_responsive_slider_image_attributes', array(
                'src' => $slide['thumb'],
                'height' => $slide['height'],
                'width' => $slide['width'],
                'alt' => $slide['alt'],
                'rel' => $slide['rel'],
                'class' => $slide['class'],
                'title' => $slide['title']
            ), $slide, $this->slider->ID );

        $html = $this->build_image_tag( $attributes );

        $anchor_attributes = apply_filters( 'metaslider_responsive_slider_anchor_attributes', array(
                'href' => $slide['url'],
                'target' => $slide['target']
            ), $slide, $this->slider->ID );

        if ( strlen( $anchor_attributes['href'] ) ) {
            $html = $this->build_anchor_tag( $anchor_attributes, $html );
        }

        if ( strlen( $slide['caption'] ) ) {
            $html .= "<div class='caption-wrap'>";
            $html .= "<div class='caption'>" . $slide['caption'] . "</div>";
            $html .= "</div>";
        }

        return apply_filters( 'metaslider_image_responsive_slider_markup', $html, $slide, $this->settings );

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

        wp_enqueue_style( 'media-views' );
        wp_enqueue_script( "metasliderpro-{$this->identifier}-script", plugins_url( 'assets/script.js' , __FILE__ ), array( 'jquery' ) );
        wp_localize_script( "metasliderpro-{$this->identifier}-script", 'metaslider_custom_slide_type', array(
                'identifier' => $this->identifier,
                'name' => $this->name
            ) );

        echo "<div class='metaslider'>
                    <div class='media-embed'>
                        <div class='embed-link-settings'>" . __("Click 'Add to slider' to create a new post feed slide.", "metasliderpro") . "</div>
                    </div>
            </div>
            <div class='media-frame-toolbar'>
                <div class='media-toolbar'>
                    <div class='media-toolbar-primary'>
                        <a href='#' class='button media-button button-primary button-large'>" . __("Add to slider", "metasliderpro") . "</a>
                    </div>
                </div>
            </div>";

    }


    /**
     * Create a new post_feed slide
     *
     * @param int     $slider_id
     * @param array   $fields
     * @return int ID of the created slide
     */
    public function create_slide( $slider_id ) {

        $this->set_slider( $slider_id );

        if ( method_exists( $this, 'insert_slide' ) ) { // Meta Slider 3.5+

            $slide_id = $this->insert_slide( false, 'post_feed', $slider_id );

        } else { // backwards compatibility

            // Attachment options
            $attachment = array(
                'post_title'=> "Meta Slider - Post Feed",
                'post_mime_type' => 'text/html',
                'post_content' => ''
            );

            $slide_id = wp_insert_attachment( $attachment );

            // store the type as a meta field against the attachment
            $this->add_or_update_or_delete_meta( $slide_id, 'type', 'post_feed' );

        }

        $defaults['custom_template'] = $this->get_default_caption_template();

        $this->add_or_update_or_delete_meta( $slide_id, 'settings', $defaults );

        $this->set_slide( $slide_id );

        $this->tag_slide_to_slider();

        return $slide_id;

    }


    /**
     *
     */
    private function get_default_caption_template() {

        return "<strong>{title}</strong>\n<em>{author}, {date}</em>\n<a href='{link}'>Read more&hellip;</a>";

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
            )
        );

        if ( ! isset( $fields['settings']['nl2br'] ) ) {
            $fields['settings']['nl2br'] = 'off';
        }

        $this->add_or_update_or_delete_meta( $this->slide->ID, 'settings', $fields['settings'] );

    }

}

/**
 * Walker to output an unordered list of category checkbox <input> elements.
 *
 * @see Walker
 * @see wp_category_checklist()
 * @see wp_terms_checklist()
 */
class Walker_MetaSlider_Checklist extends Walker {
    var $tree_type = 'category';
    var $db_fields = array ( 'parent' => 'parent', 'id' => 'term_id' ); //TODO: decouple this
    var $slide_id;

    function __construct( $slide_id ) {
        $this->slide_id = $slide_id;
    }

    function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent<ul class='children'>\n";
    }

    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat( "\t", $depth );
        $output .= "$indent</ul>\n";
    }

    function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        extract( $args );
        if ( empty( $taxonomy ) )
            $taxonomy = 'category';

        $name = "attachment[{$this->slide_id}][settings][tags][$taxonomy]";
        $output .= "\n<li>" . '<label><input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . disabled( empty( $args['disabled'] ), false, false ) . ' /> ' . esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
    }

    function end_el( &$output, $category, $depth = 0, $args = array() ) {
        $output .= "</li>\n";
    }
}