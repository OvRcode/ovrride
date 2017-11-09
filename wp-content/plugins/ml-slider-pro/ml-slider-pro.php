<?php
/*
 * Plugin Name: Meta Slider - Pro Addon Pack
 * Plugin URI: https://www.metaslider.com
 * Description: Supercharge your slideshows!
 * Version: 2.7
 * Author: Matcha Labs
 * Author URI: https://www.metaslider.com
 * Copyright: Matcha Labs LTD 2017
 */

// disable direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'MetaSliderPro' ) ) :

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
class MetaSliderPro {

    /**
     * @var string
     */
    public $version = '2.7';

    /**
     * Init
     */
    public static function init() {

        $metasliderpro = new self();

    }

    /**
     * Constructor
     */
    public function __construct() {

        if ( ! class_exists( 'MetaSlide' ) ) {
            // check Meta Slider (Lite) is installed and activated

            add_action("admin_notices", array( $this, 'check_metaslider_is_installed' ) );

            return;
        }

        define( 'METASLIDERPRO_VERSION',    $this->version );
        define( 'METASLIDERPRO_BASE_URL',   trailingslashit( plugins_url( 'ml-slider-pro' ) ) );
        define( 'METASLIDERPRO_ASSETS_URL', trailingslashit( METASLIDERPRO_BASE_URL . 'assets' ) );
        define( 'METASLIDERPRO_PATH',       plugin_dir_path( __FILE__ ) );

        $this->includes();

        add_filter( 'metaslider_menu_title', array( $this, 'menu_title' ) );
        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
        add_action( 'metaslider_register_admin_scripts', array( $this, 'register_admin_scripts' ), 10, 1 );
        add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
        add_filter( 'metaslider_css', array( $this, 'get_public_css' ), 11, 3 );

        new WPUpdatesPluginUpdater_136( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__) );

        new MetaSliderThemeEditor();
        new MetaSliderThumbnails();
        new MetaVimeoSlide();
        new MetaYouTubeSlide();
        new MetaLayerSlide();
        new MetaExternalSlide();
        new MetaSliderLoop();

        $post_feed = new MetaPostFeedSlide();
        $post_feed->hooks();
    }

    /**
     * All Meta Slider classes
     */
    private function plugin_classes() {

        return array(
            'metalayerslide'             => METASLIDERPRO_PATH . 'modules/layer/slide.php',
            'metayoutubeslide'           => METASLIDERPRO_PATH . 'modules/youtube/slide.php',
            'metavimeoslide'             => METASLIDERPRO_PATH . 'modules/vimeo/slide.php',
            'metaexternalslide'          => METASLIDERPRO_PATH . 'modules/external/slide.php',
            'metapostfeedslide'          => METASLIDERPRO_PATH . 'modules/post_feed/slide.php',
            'metasliderthumbnails'       => METASLIDERPRO_PATH . 'modules/thumbnails/thumbnails.php',
            'metasliderthemeeditor'      => METASLIDERPRO_PATH . 'modules/theme_editor/theme_editor.php',
            'metasliderloop'             => METASLIDERPRO_PATH . 'modules/extra/loop.php',
            'wpupdatespluginupdater_136' => METASLIDERPRO_PATH . 'inc/wp-updates-plugin.php'
        );

    }


    /**
     * Load required classes
     */
    private function includes() {

        $autoload_is_disabled = defined( 'METASLIDER_AUTOLOAD_CLASSES' ) && METASLIDER_AUTOLOAD_CLASSES === false;

        if ( function_exists( "spl_autoload_register" ) && ! ( $autoload_is_disabled ) ) {

            // >= PHP 5.2 - Use auto loading
            if ( function_exists( "__autoload" ) ) {
                spl_autoload_register( "__autoload" );
            }

            spl_autoload_register( array( $this, 'autoload' ) );

        } else {

            // < PHP5.2 - Require all classes
            foreach ( $this->plugin_classes() as $id => $path ) {
                if ( is_readable( $path ) && ! class_exists( $id ) ) {
                    require_once( $path );
                }
            }

        }

    }


    /**
     * Autoload Meta Slider classes to reduce memory consumption
     */
    public function autoload( $class ) {

        $classes = $this->plugin_classes();

        $class_name = strtolower( $class );

        if ( isset( $classes[$class_name] ) && is_readable( $classes[$class_name] ) ) {
            require_once $classes[$class_name];
        }

    }


    /**
     * Initialise translations
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'metasliderpro', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Registers and enqueues admin JavaScript
     */
    public function register_admin_scripts() {
        wp_enqueue_script( 'metaslider-pro-admin-script', METASLIDERPRO_ASSETS_URL . 'admin.js', array( 'jquery', 'metaslider-admin-script' ), METASLIDERPRO_VERSION );
    }

    /**
     * Registers and enqueues admin CSS
     */
    public function register_admin_styles() {
        wp_enqueue_style( 'metaslider-pro-admin-styles', METASLIDERPRO_ASSETS_URL . 'admin.css', false, METASLIDERPRO_VERSION );
    }

    /**
     * Registers and enqueues public CSS
     *
     * @param string  $css
     * @param array   $settings
     * @param int     $id
     * @return string
     */
    public function get_public_css( $css, $settings, $id ) {
        if ( $settings['printCss'] == 'true' ) {
            wp_enqueue_style( 'metaslider-pro-public', METASLIDERPRO_ASSETS_URL . "public.css", false, METASLIDERPRO_VERSION );
        }

        return $css;
    }

    /**
     * Add "Pro" to the menu title
     *
     * @param string  Meta Slider menu name
     * @return string title
     */
    public function menu_title( $title ) {
        return $title . " " . __("Pro", "metasliderpro");
    }


    /**
     *
     */
    public function check_metaslider_is_installed() {

        if ( is_plugin_active('ml-slider/ml-slider.php') ) {
            return;
        }

        if ( is_plugin_inactive('ml-slider/ml-slider.php') ) :

        ?>
        <div class="updated">
            <p>
                <?php _e( 'Meta Slider Pro requires Meta Slider (free). Please activate the Meta Slider (free) plugin.', 'metasliderpro' ); ?>
            </p>
            <p class='submit'>
                <a href="<?php echo admin_url( "plugins.php" ) ?>" class='button button-secondary'><?php _e("Enable Meta Slider", "metasliderpro"); ?></a>
            </p>
        </div>
        <?php

        else:

        ?>
        <div class="updated">
            <p>
                <?php _e( 'Meta Slider Pro requires Meta Slider (free). Please install the Meta Slider (free) plugin.', 'metasliderpro' ); ?>
            </p>
            <p class='submit'>
                <a href="<?php echo admin_url( "plugin-install.php?tab=search&type=term&s=meta+slider" ) ?>" class='button button-secondary'><?php _e("Install Meta Slider", "metasliderpro"); ?></a>
            </p>
        </div>
        <?php

        endif;

    }

}

endif;

add_action( 'plugins_loaded', array( 'MetaSliderPro', 'init' ), 11 );
