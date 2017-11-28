<?php

class Smps_Simple_Light {

    /**
     * @var Singleton The reference the *Singleton* instance of this class
     */
    private static $instance;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public static function settings() {
        return array(
            'name' => 'Packaged shortcodes',
            'items' => apply_filters('simple_light_shortcode_items',array(
                'tabs' => array(
                    'section' => 'Content',
                    'label' => 'Tabs'
                ),
                'accordion' => array(
                    'section' => 'Content',
                    'label' => 'Accordion'
                ),
                'table' => array(
                    'section' => 'Content',
                    'label' => 'Table'
                ),
                'panel' => array(
                    'section' => 'Content',
                    'label' => 'Panel'
                ),
                'alert' => array(
                    'section' => 'Content',
                    'label' => 'Alert'
                ),
                'heading' => array(
                    'section' => 'Content',
                    'label' => 'Heading'
                ),
                'quote' => array(
                    'section' => 'Content',
                    'label' => 'Quote'
                ),
                'button' => array(
                    'section' => 'Content',
                    'label' => 'Button'
                ),
                'spoiler' => array(
                    'section' => 'Content',
                    'label' => 'Spoiler'
                ),
                'list' => array(
                    'section' => 'Content',
                    'label' => 'List'
                ),
                'highlight' => array(
                    'section' => 'Content',
                    'label' => 'Highlight'
                ),
                'restricted_content' => array(
                    'section' => 'Content',
                    'label' => 'Restricted Content'
                ),
                'note' => array(
                    'section' => 'Content',
                    'label' => 'Note'
                ),
                'youtube' => array(
                    'section' => 'Media',
                    'label' => 'Youtube'
                ),
                'vimeo' => array(
                    'section' => 'Media',
                    'label' => 'Vimeo'
                ),
                'image' => array(
                    'section' => 'Media',
                    'label' => 'Image'
                ),
                'scheduler' => array(
                    'section' => 'Content',
                    'label' => 'Scheduler'
                ),
                'post_loop' => array(
                    'section' => 'Content',
                    'label' => 'Post/Content List'
                ),
                'page_loop' => array(
                    'section' => 'Content',
                    'label' => 'Page List'
                ),
                'post_meta' => array(
                    'section' => 'Content',
                    'label' => 'Post Meta Data'
                ),
                'option' => array(
                    'section' => 'Content',
                    'label' => 'Option'
                ),
                'category_list' => array(
                    'section' => 'Content',
                    'label' => 'Category List'
                ),
                'menu' => array(
                    'section' => 'Content',
                    'label' => 'Menu'
                )

                //'social_media_button' => 'Social Media Button'
            ))
        );
    }

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
        $this->includes();
    }

    public function includes() {
        add_action( 'init',function () {
            include_once 'definitions.php';
            include_once 'admin-panel.php';
        });

        add_action( 'admin_head', function () {
            include 'settings-template.php';
        });
    }

    function wp_enqueue_scripts_styles( $hook ) {
        wp_enqueue_style( 'simple-light-css' , SHORTCODE_MAKER_ASSET_PATH.'/css/simple-light.css' );
        wp_enqueue_script( 'simple-light-js' , SHORTCODE_MAKER_ASSET_PATH.'/js/simple-light.js', array( 'jquery' ) );
    }

    function admin_enqueue_scripts_styles( $hook ) {
        global $post;
        if( in_array( $hook, array(
            'post-new.php',
            'post.php'
        ) ) ) {
            $shortcode_settings_data = apply_filters( 'sm_shortcode_settings_data',
                array(
                    'post_loop' => array(
                        'orderby' => array(
                            'date' => 'Date',
                            'ID' => 'ID',
                            'title' => 'Title'
                        ),
                        'post_statuses' => get_post_statuses()
                    ),
                    'page_loop' => array(
                        'orderby' => array(
                            'date' => 'Date',
                            'ID' => 'ID',
                            'title' => 'Title'
                        ),
                        'post_statuses' => get_post_statuses()
                    )
                )
            );
            $hide_shortcode_panel = get_post_meta( $post->ID,'sm_hide_shortcode_panel', true);
            ?>
            <script>
                var sm_settings_data = JSON.parse('<?php echo json_encode($shortcode_settings_data); ?>');
                var sm_object = {};
                var hide_shortcode_panel = '<?php echo $hide_shortcode_panel; ?>';
            </script>
            <?php
            wp_enqueue_script( 'simple-light-settings-template' , plugins_url('assets/js/settings-templates.js',__FILE__), array( 'jquery' ) );
            do_action( 'simple_light_admin_enqueue_scripts' );
        }
    }
}

Smps_Simple_Light::get_instance();