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
            'items' => apply_filters('smps_shortcode_items',array(
                'tabs' => array(
                    'section' => 'Content',
                    'label' => __('Tabs','sm')
                ),
                'accordion' => array(
                    'section' => 'Content',
                    'label' => __('Accordion','sm')
                ),
                'table' => array(
                    'section' => 'Content',
                    'label' => __('Table','sm')
                ),
                'panel' => array(
                    'section' => 'Content',
                    'label' => __('Panel','sm')
                ),
                'alert' => array(
                    'section' => 'Content',
                    'label' => __('Alert','sm')
                ),
                'heading' => array(
                    'section' => 'Content',
                    'label' => __('Heading','sm')
                ),
                'quote' => array(
                    'section' => 'Content',
                    'label' => __('Quote','sm')
                ),
                'button' => array(
                    'section' => 'Content',
                    'label' => __('Button','sm')
                ),
                'spoiler' => array(
                    'section' => 'Content',
                    'label' => __('Spoiler','sm')
                ),
                'list' => array(
                    'section' => 'Content',
                    'label' => __('List','sm')
                ),
                'highlight' => array(
                    'section' => 'Content',
                    'label' => __('Highlight','sm')
                ),
                'restricted_content' => array(
                    'section' => 'Content',
                    'label' => __('Restricted Content','sm')
                ),
                'youtube' => array(
                    'section' => 'Media',
                    'label' => __('Youtube','sm')
                ),
                'vimeo' => array(
                    'section' => 'Media',
                    'label' => __('Vimeo','sm')
                ),
                'image' => array(
                    'section' => 'Media',
                    'label' => __('Image','sm')
                ),
                'scheduler' => array(
                    'section' => 'Content',
                    'label' => __('Scheduler','sm')
                ),
                'post_loop' => array(
                    'section' => 'Content',
                    'label' => __('Post/Content List','sm')
                ),
                'page_loop' => array(
                    'section' => 'Content',
                    'label' => __('Page List','sm')
                ),
                'post_meta' => array(
                    'section' => 'Content',
                    'label' => __('Post Meta Data','sm')
                ),
                'option' => array(
                    'section' => 'Content',
                    'label' => __('Option','sm')
                ),
                'category_list' => array(
                    'section' => 'Content',
                    'label' => __('Category List','sm')
                ),
                'menu' => array(
                    'section' => 'Content',
                    'label' => __('Menu','sm')
                )

                //'social_media_button' => 'Social Media Button'
            ))
        );
    }

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );
        $this->includes();
        do_action( 'simple_light_init' );
    }

    public function includes() {
        include_once 'shortcode-definitions.php';
        add_action( 'init',function () {
            include_once 'admin-panel.php';
        });
    }

    function wp_enqueue_scripts_styles( $hook ) {
        global $post;


        if( isset($post->post_content) && has_shortcode( $post->post_content, 'smps_shortcode' )  ) {
            wp_enqueue_style( 'sm-front-bs' , SHORTCODE_MAKER_ASSET_PATH.'/css/bootstrap-4.0.0.min.css' );
            wp_enqueue_script( 'sm-front-bs-js' , SHORTCODE_MAKER_ASSET_PATH.'/js/bootstrap-4.0.0.min.js', array( 'jquery' ) );

            wp_enqueue_script( 'sm-vue', SHORTCODE_MAKER_ASSET_PATH.'/js/vue.js' );
            wp_enqueue_script( 'sm-public-js', SHORTCODE_MAKER_ASSET_PATH.'/js/public.js', array('jquery','sm-vue') );
        }
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
                            's' => array(),
                        'data' => array(
                            'orderby_opts' => array(
                                'date' => 'Date',
                                'ID' => 'ID',
                                'title' => 'Title'
                            ),
                            'post_status_opts' => get_post_statuses()
                        ),
                    ),
                    'page_loop' => array(
                        's' => array(),
                        'data' => array(
                            'orderby_opts' => array(
                                'date' => 'Date',
                                'ID' => 'ID',
                                'title' => 'Title'
                            ),
                            'post_status_opts' => get_post_statuses()
                        )
                    )
                )
            );
            $hide_shortcode_panel = get_post_meta( $post->ID,'sm_hide_shortcode_panel', true);
            $sm_common_props = apply_filters( 'sm_common_props', array(
                    'sizes' => array(
                            'xs' => __('Mini','sm'),
                        'sm' => __('Small','sm'),
                        'md' => __('Medium','sm'),
                        'lg' => __('Large','sm')
                    ),
                'style_types' => array(
                    'primary' => 'Primary',
                    'secondary'=> 'Secondary',
                    'success'=> 'Success',
                    'danger'=> 'Danger',
                    'warning'=> 'Warning',
                    'info'=> 'Info',
                    'light'=> 'Light',
                    'dark'=> 'Dark'
                )
            ));

            ?>
            <script>
                var sm_common_props = JSON.parse(atob('<?php echo base64_encode( json_encode( $sm_common_props ) ); ?>'));
                var sm_settings_data = JSON.parse('<?php echo json_encode($shortcode_settings_data); ?>');
                var sm_object = {};
                var hide_shortcode_panel = '<?php echo $hide_shortcode_panel; ?>';
            </script>
            <?php
            include 'settings-templates.php';
            wp_enqueue_script( 'simple-light-settings-template' , plugins_url('assets/js/settings-templates.js',__FILE__), array( 'jquery' ), false, true );
            do_action( 'smps_admin_enqueue_scripts' );
        }
    }
}

Smps_Simple_Light::get_instance();