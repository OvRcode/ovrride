<?php
//Prevent direct access of file.

if( !defined('ABSPATH') ){
    exit;
}

if( !class_exists('WOOOE', false) ){

    class WOOOE{

        static $_instance;

        /*
         * Version of plugin
         */
        public $version = '3.0.13';

        /*
         * Plugin settings array
         */
        public $settings = array();

        //constructor
        protected function __construct() {

            spl_autoload_register(array($this, 'autoload'));
            $this->hooks();
        }

        //Prevent cloning and unserialization.
        private function __clone() {}
        private function __wakeup() {}

        /*
         * Instantiate the class
         */
        public static function instance() {

            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }

        /*
         * SPL Autoloader Function
         */
        function autoload($class_name){
            //Load trait in advance.
            include trailingslashit(WOOOE_BASE). 'classes/controllers/WOOOE_Trait_GetValue.php';

            if( strstr($class_name, 'WOOOE_Fetch') !== FALSE ){
                include trailingslashit(WOOOE_BASE). 'classes/controllers/'. $class_name .'.php';
            }elseif( strstr($class_name, 'WOOOE') !== FALSE ){
                include trailingslashit(WOOOE_BASE). 'classes/'. $class_name .'.php';
            }
        }

        /*
         * Adds all necessary hooks.
         */
        function hooks(){
            add_filter( 'plugin_action_links_'.WOOOE_BASENAME, 'woooe_action_link' );
            add_action('admin_enqueue_scripts', array($this, 'scripts'));
            add_filter('woocommerce_get_settings_pages', function($settings){ $settings[] = include trailingslashit(WOOOE_BASE).'classes/WOOOE_Setting_Tab.php'; return $settings;});
            add_action('wp_ajax_woooe_get_report', array('WOOOE_Report_Handler', 'fetch_report_stats') );
            add_action('wp_ajax_woooe_fetch_report', array('WOOOE_Report_Handler', 'fetch_report') );
            add_action('init', array('WOOOE_File_Handler', 'download'),11);
            add_action('init', array($this, 'init'),1);
        }

        /*
         * Init function of plugin
         */
        function init(){

            /*
             * Include woocommerce functions if they are not present.
             */
            if(!function_exists('woocommerce_settings_get_option')){
                include_once WC_ABSPATH . 'includes/admin/wc-admin-functions.php';
            }

            load_plugin_textdomain( 'woooe', false, basename( WOOOE_BASE ) . '/languages' );

            /*
             * If older version of add-on plugin is installed and
             * activated, deactivate it by removing its initialization call.
             */
            remove_action( 'woocommerce_init', 'wsoe_add_on_initialize' );
        }

        /*
         * Get settings
         */
        function get_settings($context = 'general'){

            $setting = '';

            switch($context){
                
                case 'general':
                    $setting = include trailingslashit(WOOOE_BASE).'classes/admin-settings/general-settings.php';
                break;
            
                case 'advanced':
                    $setting = include trailingslashit(WOOOE_BASE).'classes/admin-settings/advanced-settings.php';
                break;
            }

            return $setting;
        }

        /*
         * Add scripts and styles.
         */
        function scripts(){
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script( 'woooe-script', trailingslashit(WOOOE_BASE_URL).'assets/js/dest/woooe.min.js', array('jquery-ui-datepicker'), false, true );
            wp_enqueue_style('jquery-ui-datepicker');
            wp_enqueue_style('woooe-style', trailingslashit(WOOOE_BASE_URL).'assets/css/dest/woooe.css', array());
        }
    }
}