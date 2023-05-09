<?php
/*
 * Plugin Name: MetaSlider - Pro Add-on Pack
 * Plugin URI: https://www.metaslider.com
 * Description: This Add-on pack unlocks the power of video slides, layer slides, post type slides as well as many other features.
 * Version: 2.7.1
 * Author: Team Updraft
 * Author URI: https://www.metaslider.com
 * Copyright: 2017 Simba Hosting Ltd
 */

// disable direct access
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('MetaSliderPro')) :

/**
 * Register the plugin.
 *
 * Display the administration panel, insert JavaScript etc.
 */
class MetaSliderPro {

    /**
     * @var string $version Current version of the Pro Pack
     */
    public $version = '2.7.1';

    /**
     * @var string $lite_version_minimum Minimum required version
     */
    public $lite_version_minimum = '3.6.0';

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

        /*
         * Checks requirements for using this Pro Pack. 
         * if WP_Error or false is returned, don't continue
         */
        $error = $this->passes_requirements();
        if (is_wp_error($error)) {
            require_once(plugin_dir_path(__FILE__) . 'inc/admin-notice.php');
            new Metaslider_Admin_Notice($error->get_error_code(), $error->get_error_message());
        }
        if (is_wp_error($error) || !$error) return false;

        define( 'METASLIDERPRO_VERSION',    $this->version );
        define( 'METASLIDERPRO_BASE_URL',   trailingslashit( plugins_url( 'ml-slider-pro' ) ) );
        define( 'METASLIDERPRO_ASSETS_URL', trailingslashit( METASLIDERPRO_BASE_URL . 'assets' ) );
        define( 'METASLIDERPRO_PATH',       plugin_dir_path( __FILE__ ) );

        $this->includes();

        add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
        add_action( 'metaslider_register_admin_scripts', array( $this, 'register_admin_scripts' ), 10, 1 );
        add_action( 'metaslider_register_admin_styles', array( $this, 'register_admin_styles' ), 10, 1 );
        add_filter( 'metaslider_css', array( $this, 'get_public_css' ), 11, 3 );

        if (!class_exists('Updraft_Manager_Updater_1_5')) {
			include_once(METASLIDERPRO_PATH.'/vendor/davidanderson684/simba-plugin-manager-updater/class-udm-updater.php');
        }
        
        try {
			new Updraft_Manager_Updater_1_5('https://metaslider.com', 1, $this->get_real_file_path('ml-slider-pro/ml-slider-pro.php'));
		} catch (Exception $e) {
			error_log($e->getMessage().' at '.$e->getFile().' line '.$e->getLine());
		}

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
            'metasliderloop'             => METASLIDERPRO_PATH . 'modules/extra/loop.php'
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
        if ( 'true' == $settings['printCss'] ) {
            wp_enqueue_style( 'metaslider-pro-public', METASLIDERPRO_ASSETS_URL . "public.css", false, METASLIDERPRO_VERSION );
        }

        return $css;
    }

    /**
     * Method to check if the base MetaSlider plugin is available and up to date
     * @return bool|WP_Error On failure the method will return details in the error
     */
    protected function passes_requirements() {
        
        if (!(bool) ($plugin = $this->has_metaslider_installed())) {

            // If we're currently installing, don't show the error message
            if (isset($_GET['installing_metaslider'])) return false;

            // Creates a link to auto install the lite plugin
            return new WP_Error('notice-error', sprintf(__("The MetaSlider Pro Add-on Pack requires the MetaSlider plugin to be installed. You may download it by clicking <a href='%s'>here</a>.", "metasliderpro"), wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=ml-slider&installing_metaslider=true'), 'install-plugin_ml-slider')));
        }
        if (!$this->has_metaslider_activated()) {

            // Creates a direct link to auto activate the lite plugin
            $nonce = wp_nonce_url(sprintf(self_admin_url('plugins.php?action=activate&plugin=%s'), str_replace('/', '%2F', $plugin)), 'activate-plugin_' . $plugin);
            return new WP_Error('notice-error', sprintf(__("The MetaSlider Pro Add-on Pack requires the MetaSlider plugin to be activated. You may activate it by clicking <a href='%s'>here</a>.", "metasliderpro"), $nonce));
        }
        if (!$this->has_metaslider_minimum_version()) {

            // Creates a direct link to auto update the lite plugin
            $nonce = wp_nonce_url(sprintf(self_admin_url('update.php?action=upgrade-plugin&plugin=%s'), str_replace('/', '%2F', $plugin)), 'upgrade-plugin_' . $plugin);
            return new WP_Error('notice-warning', sprintf(__("The MetaSlider Pro Add-on Pack requires the MetaSlider plugin to be at least version %s. You may update it by clicking <a href='%s'>here</a>.", "metasliderpro"), $this->lite_version_minimum, $nonce));
        }
        return true;
    }
  
    /**
     * Method to check if the base MetaSlider plugin is installed
     * @return string|bool - the path to the file or false
     */
    protected function has_metaslider_installed() {

        return $this->get_real_file_path('/ml-slider/ml-slider.php');
    }

    /**
     * Method to check get the resolved file path and determine if a plugin
     * exists. Example '/ml-slider/ml-slider.php' will search if the file is
     * there, and if not, checks the WP database an returns the registered path.
     * @param string - the text domain or plugin file location
     * @return string|bool - the path to the file or false
     */
    protected function get_real_file_path($file) {

        // If the file is there, then they have the plugin
        if (file_exists(plugin_dir_path($file))) {
            return $file;
        }

        // Sometimes the file wont be there (user changed folder name/symlinks/etc)
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        foreach (get_plugins() as $plugin => $data) {
            if (pathinfo($file, PATHINFO_FILENAME) == $data['TextDomain']) 
                return $plugin;
        }

        // Finally return false if nothing found
        return false;
    }

    /**
     * Method to check if the base MetaSlider plugin is activated
     * @return bool
     */    
    protected function has_metaslider_activated() {
        return class_exists('MetaSliderPlugin');
    }
  
    /**
     * Method to check if the base MetaSlider plugin is the minimum version required
     * @return bool
     */    
    protected function has_metaslider_minimum_version() {

        // Check the version numbers on the file if it exists (it should always exist at this point)
        if ((bool) ($file = $this->has_metaslider_installed())) {
            $lite = get_file_data(trailingslashit(WP_PLUGIN_DIR) . $file, array('Version' => 'Version'));
            return version_compare($lite['Version'], $this->lite_version_minimum, '>=');
        }
        return false;
    }      
}

endif;

add_action( 'plugins_loaded', array( 'MetaSliderPro', 'init' ), 11 );
