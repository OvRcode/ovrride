<?php
// @codingStandardsIgnoreStart
/*
 * Plugin Name: MetaSlider Pro
 * Plugin URI: https://www.metaslider.com
 * Description: MetaSlider Pro unlocks the power of video slides, layer slides, post type slides as well as many other features.
 * Version: 2.23.0
 * Author: MetaSlider
 * Author URI: https://www.metaslider.com
 * Copyright: 2020- MetaSlider LLC
 *
 * Text Domain: ml-slider-pro
 * Domain Path: /languages
 */
// @codingStandardsIgnoreEnd

// disable direct access
if (! defined('ABSPATH')) {
    die('No direct access.');
}

if (! class_exists('MetaSliderPro')) :

    /**
     * Register the plugin.
     *
     * Display the administration panel, insert JavaScript etc.
     */
    class MetaSliderPro
    {

        /**
         * Current version of the Pro Pack
         *
         * @var string $version
         */
        public $version = '2.23.0';

        /**
         * Minimum required version
         *
         * @var string $lite_version_minimum
         */
        public $lite_version_minimum = '3.18.0';

        /**
         * Init
         */
        public static function init()
        {
            $metasliderpro = new self();
        }

        /**
         * Constructor
         */
        public function __construct()
        {
            /*
             * Checks requirements for using this Pro Pack.
             * if WP_Error or false is returned, don't continue
             */
            $error = $this->passes_requirements();
            if (is_wp_error($error)) {
                require_once(plugin_dir_path(__FILE__) . 'inc/admin-notice.php');
                new Metaslider_Admin_Notice($error->get_error_code(), $error->get_error_message());
            }
            if (is_wp_error($error) || ! $error) {
                return;
            }

            define('METASLIDERPRO_VERSION', $this->version);
            define('METASLIDERPRO_BASE_URL', trailingslashit(plugins_url('ml-slider-pro')));
            define('METASLIDERPRO_ASSETS_URL', trailingslashit(METASLIDERPRO_BASE_URL . 'assets'));
            define('METASLIDERPRO_PATH', plugin_dir_path(__FILE__));

            $this->includes();

            add_action('init', array($this, 'load_plugin_textdomain'));
            add_action('metaslider_register_admin_scripts', array($this, 'register_admin_scripts'), 10, 1);
            add_action('metaslider_register_admin_styles', array($this, 'register_admin_styles'), 10, 1);
            add_filter('metaslider_css', array($this, 'get_public_css'), 11, 3);

            add_action('metaslider_register_admin_components', array($this, 'metaslider_pro_add_components'));

            add_filter('udmanager_showcompatnotice', [$this, 'disable_udmanager_compat_notice'], 10, 2);

            if (! class_exists('Updraft_Manager_Updater_1_8')) {
                include_once(METASLIDERPRO_PATH . 'vendor/davidanderson684/simba-plugin-manager-updater/class-udm-updater.php');
            }

            try {
                // This path is the correct thing to use regardless of whether the user moved it - because if they did, then updates won't work anyway
                new Updraft_Manager_Updater_1_8(
                    'https://www.metaslider.com',
                    1,
                    'ml-slider-pro/ml-slider-pro.php',
                    array('require_login' => false)
                );
            } catch (Exception $e) {
                // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
                error_log($e->getMessage() . ' at ' . $e->getFile() . ' line ' . $e->getLine());
            }

            new MetaSliderThemeEditor();
            new MetaSliderThumbnails();
            new MetaVimeoSlide();
            new MetaYouTubeSlide();
            new MetaLayerSlide();
            new MetaExternalSlide();
            new MetaSliderLoop();
            new MetaSliderPro_Schedule_Slides();

            $post_feed = new MetaPostFeedSlide();
            $post_feed->hooks();

            include_once(METASLIDERPRO_PATH . 'modules/css_manager/loader.php');

            // API related
            // Default to WP (4.4) REST API but backup with admin ajax
            require_once(METASLIDERPRO_PATH . 'routes/api.php');
            $this->api = MetaSliderPro_Api::get_instance();
            $this->api->setup();
            $this->api->register_admin_ajax_hooks();
            if (class_exists('WP_REST_Controller')) {
                new MetaSliderPro_REST_Controller();
            }
        }

        public function disable_udmanager_compat_notice($show, $slug)
        {
            if ($slug === 'ml-slider-pro') {
                return false;
            }

            return $show;
        }

        /**
         * All MetaSlider classes
         */
        private function plugin_classes()
        {
            return array(
                'metalayerslide' => METASLIDERPRO_PATH . 'modules/layer/slide.php',
                'metayoutubeslide' => METASLIDERPRO_PATH . 'modules/youtube/slide.php',
                'metavimeoslide' => METASLIDERPRO_PATH . 'modules/vimeo/slide.php',
                'metaexternalslide' => METASLIDERPRO_PATH . 'modules/external/slide.php',
                'metapostfeedslide' => METASLIDERPRO_PATH . 'modules/post_feed/slide.php',
                'metasliderthumbnails' => METASLIDERPRO_PATH . 'modules/thumbnails/thumbnails.php',
                'metasliderthemeeditor' => METASLIDERPRO_PATH . 'modules/theme_editor/theme_editor.php',
                'metasliderloop' => METASLIDERPRO_PATH . 'modules/extra/loop.php',
                'metasliderpro_schedule_slides' => METASLIDERPRO_PATH . 'modules/schedule/schedule.php'
            );
        }


        /**
         * Load required classes
         */
        private function includes()
        {
            spl_autoload_register(array($this, 'autoload'));

            if (is_readable(METASLIDERPRO_PATH . '/vendor/autoload.php')) {
                require_once METASLIDERPRO_PATH . '/vendor/autoload.php';
            }
        }

        /**
         * Autoload MetaSlider classes to reduce memory consumption
         *
         * @param string $class Class to load
         */
        public function autoload($class)
        {
            $classes = $this->plugin_classes();

            $class_name = strtolower($class);

            if (isset($classes[$class_name]) && is_readable($classes[$class_name])) {
                require_once $classes[$class_name];
            }
        }


        /**
         * Initialise translations
         */
        public function load_plugin_textdomain()
        {
            load_plugin_textdomain('ml-slider-pro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
        }

        /**
         * Registers and enqueues admin JavaScript
         */
        public function register_admin_scripts()
        {
            wp_enqueue_style(
                'jquery-ui-datepicker',
                METASLIDERPRO_ASSETS_URL . 'jquery.ui.datepicker.css',
                array(),
                METASLIDERPRO_VERSION
            );
            wp_enqueue_style(
                'jquery-ui-datepicker-theme',
                METASLIDERPRO_ASSETS_URL . 'jquery.ui.datepicker.theme.css',
                array(),
                METASLIDERPRO_VERSION
            );
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script(
                'metaslider-pro-admin-script',
                METASLIDERPRO_ASSETS_URL . 'admin.js',
                array('jquery', 'metaslider-admin-script'),
                METASLIDERPRO_VERSION
            );
        }

        /**
         * Registers and enqueues admin CSS
         */
        public function metaslider_pro_add_components()
        {
            $can_use_rest = class_exists('WP_REST_Controller');
            wp_register_script(
                'metaslider-pro-components-js',
                plugins_url('components.min.js', __FILE__),
                array(),
                METASLIDERPRO_VERSION
            );
            wp_localize_script('metaslider-pro-components-js', 'metasliderpro_api', array(
                'root' => $can_use_rest ? esc_url_raw(rest_url("metaslider-pro/v1/")) : false,
                'nonce' => wp_create_nonce('wp_rest'),
                'ajaxurl' => admin_url('admin-ajax.php'),
                'supports_rest' => $can_use_rest
            ));
            wp_enqueue_script('metaslider-pro-components-js');
        }

        /**
         * Registers and enqueues admin CSS
         */
        public function register_admin_styles()
        {
            wp_enqueue_style(
                'metaslider-pro-admin-styles',
                METASLIDERPRO_ASSETS_URL . 'admin.css',
                false,
                METASLIDERPRO_VERSION
            );
        }

        /**
         * Registers and enqueues public CSS
         *
         * @param string $css CSS
         * @param array $settings CSS settings
         * @param int $id CSS ID
         * @return string
         */
        public function get_public_css($css, $settings, $id)
        {
            if ('true' == $settings['printCss']) {
                wp_enqueue_style(
                    'metaslider-pro-public',
                    METASLIDERPRO_ASSETS_URL . "public.css",
                    false,
                    METASLIDERPRO_VERSION
                );
            }

            return $css;
        }

        /**
         * Method to check if the base MetaSlider plugin is available and up to date
         *
         * @return bool|WP_Error On failure the method will return details in the error
         */
        protected function passes_requirements()
        {
            if (! (bool)($plugin = $this->has_metaslider_installed())) {
                // If we're currently installing, don't show the error message
                if (isset($_GET['installing_metaslider'])) {
                    return false;
                }

                // Creates a link to auto install the lite plugin
                return new WP_Error(
                    'notice-error',
                    sprintf(
                        __(
                            "MetaSlider Pro requires the MetaSlider plugin to be installed. You may download it by clicking <a href='%s'>here</a>.",
                            "ml-slider-pro"
                        ),
                        wp_nonce_url(
                            self_admin_url(
                                'update.php?action=install-plugin&plugin=ml-slider&installing_metaslider=true'
                            ),
                            'install-plugin_ml-slider'
                        )
                    )
                );
            }
            if (! $this->has_metaslider_activated()) {
                // Creates a direct link to auto activate the lite plugin
                $nonce = wp_nonce_url(
                    sprintf(self_admin_url('plugins.php?action=activate&plugin=%s'), str_replace('/', '%2F', $plugin)),
                    'activate-plugin_' . $plugin
                );
                return new WP_Error(
                    'notice-error',
                    sprintf(
                        __(
                            "MetaSlider Pro requires the MetaSlider plugin to be activated. You may activate it by clicking <a href='%s'>here</a>.",
                            "ml-slider-pro"
                        ),
                        $nonce
                    )
                );
            }
            if (! $this->has_metaslider_minimum_version()) {
                // Creates a direct link to auto update the lite plugin
                $nonce = wp_nonce_url(
                    sprintf(
                        self_admin_url('update.php?action=upgrade-plugin&plugin=%s'),
                        str_replace('/', '%2F', $plugin)
                    ),
                    'upgrade-plugin_' . $plugin
                );
                return new WP_Error(
                    'notice-warning',
                    sprintf(
                        __(
                            "MetaSlider Pro requires the MetaSlider plugin (free version) to be at least version %s. You may update it by clicking <a href='%s'>here</a>.",
                            "ml-slider-pro"
                        ),
                        $this->lite_version_minimum,
                        $nonce
                    )
                );
            }
            return true;
        }

        /**
         * Method to check if the base MetaSlider plugin is installed
         *
         * @return string|bool - the path to the file or false
         */
        protected function has_metaslider_installed()
        {
            return $this->get_real_file_path('/ml-slider/ml-slider.php');
        }

        /**
         * Method to check get the resolved file path and determine if a plugin
         * exists. Example '/ml-slider/ml-slider.php' will search if the file is
         * there, and if not, checks the WP database an returns the registered path.
         *
         * @param string $file - the text domain or plugin file location
         * @return string|bool - the path to the file or false
         */
        protected function get_real_file_path($file)
        {
            // Grab the file path from the database
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');
            foreach (get_plugins() as $plugin => $data) {
                if (pathinfo($file, PATHINFO_FILENAME) == $data['TextDomain']) {
                    return $plugin;
                }
            }

            // Finally return false if nothing found
            return false;
        }

        /**
         * Method to check if the base MetaSlider plugin is activated
         *
         * @return bool
         */
        protected function has_metaslider_activated()
        {
            return class_exists('MetaSliderPlugin');
        }

        /**
         * Method to check if the base MetaSlider plugin is the minimum version required
         *
         * @return bool
         */
        protected function has_metaslider_minimum_version()
        {
            // Check the version numbers on the file if it exists (it should always exist at this point)
            if ((bool)($file = $this->has_metaslider_installed())) {
                $lite = get_file_data(trailingslashit(WP_PLUGIN_DIR) . $file, array('Version' => 'Version'));
                return version_compare($lite['Version'], $this->lite_version_minimum, '>=');
            }
            return false;
        }
    }

endif;

add_action('plugins_loaded', array('MetaSliderPro', 'init'), 11);
