<?php
defined('ABSPATH') or die('No script kiddies please!');
/**
 * Plugin Name: AccessPress Facebook Auto Post
 * Plugin URI: https://accesspressthemes.com/wordpress-plugins/accesspress-facebook-auto-post/
 * Description: A plugin to publish your wordpress posts to facebook profile and fan pages
 * Version: 1.4.2
 * Author: AccessPress Themes
 * Author URI: http://accesspressthemes.com
 * Text Domain: accesspress-facebook-auto-post
 * Domain Path: /languages/
 * License: GPL2
 */
 


if (!class_exists('AFAP_Class')) {

    /**
     * Declaration of plugin main class
     * */
    class AFAP_Class {

        var $afap_settings;
        var $afap_extra_settings;

        /**
         * Constructor
         */
        function __construct() {
            $this->afap_settings = get_option('afap_settings');
            $this->afap_extra_settings = get_option('afap_extra_settings');
            $this->define_constants();
            register_activation_hook(__FILE__, array($this, 'activation_tasks')); //fired when plugin is activated
            add_action('admin_init', array($this, 'plugin_init')); //starts the session and loads plugin text domain on admin_init hook
            add_action('admin_menu', array($this, 'afap_admin_menu')); //For plugin admin menu
            add_action('admin_enqueue_scripts', array($this, 'register_admin_assets')); //registers js and css for plugin
            add_action('admin_post_afap_fb_authorize_action', array($this, 'fb_authorize_action')); //action to authorize facebook
            add_action('admin_post_afap_callback_authorize', array($this, 'afap_callback_authorize')); //action to authorize facebook
            add_action('admin_post_afap_form_action', array($this, 'afap_form_action')); //action to save settings
            add_action('admin_init', array($this, 'auto_post_trigger')); // auto post trigger
            add_action('admin_post_afap_clear_log', array($this, 'afap_clear_log')); //clears log from log table
            add_action('admin_post_afap_delete_log', array($this, 'delete_log')); //clears log from log table
            add_action('admin_post_afap_restore_settings', array($this, 'restore_settings')); //clears log from log table
            add_action('add_meta_boxes', array($this, 'add_afap_meta_box')); //adds plugin's meta box
            add_action('save_post', array($this, 'save_afap_meta_value')); //saves meta value 
            add_action('future_to_publish', array($this, 'auto_post_schedule')); 
            add_action(  'transition_post_status',  array($this,'auto_post'), 10, 3 );
            
        }
        
        /**
         * Necessary constants define
         */
        function define_constants(){
           if (!defined('AFAP_CSS_DIR')) {
                define('AFAP_CSS_DIR', plugin_dir_url(__FILE__) . 'css');
            }
            if (!defined('AFAP_IMG_DIR')) {
                define('AFAP_IMG_DIR', plugin_dir_url(__FILE__) . 'images');
            }
            if (!defined('AFAP_JS_DIR')) {
                define('AFAP_JS_DIR', plugin_dir_url(__FILE__) . 'js');
            }
            if (!defined('AFAP_VERSION')) {
                define('AFAP_VERSION', '1.4.2');
            }
            if (!defined('AFAP_TD')) {
                define('AFAP_TD', 'accesspress-facebook-auto-post');
            }
            if (!defined('AFAP_PLUGIN_FILE')) {
                define('AFAP_PLUGIN_FILE', __FILE__);
            }
            
            if (!defined('AFAP_API_VERSION')) {
                define('AFAP_API_VERSION', 'v2.0');
            }
            
            if (!defined('AFAP_api')) {
                define('AFAP_api', 'https://api.facebook.com/' . AFAP_API_VERSION . '/');
            }
            if (!defined('AFAP_api_video')) {
                define('AFAP_api_video', 'https://api-video.facebook.com/' . AFAP_API_VERSION . '/');
            }
            
            if (!defined('AFAP_api_read')) {
                define('AFAP_api_read', 'https://api-read.facebook.com/' . AFAP_API_VERSION . '/');
            }
            
            if (!defined('AFAP_graph')) {
                define('AFAP_graph', 'https://graph.facebook.com/' . AFAP_API_VERSION . '/');
            }
            
            if (!defined('AFAP_graph_video')) {
                define('AFAP_graph_video', 'https://graph-video.facebook.com/' . AFAP_API_VERSION . '/');
            }
            if (!defined('AFAP_www')) {
                define('AFAP_www', 'https://www.facebook.com/' . AFAP_API_VERSION . '/');
            } 
        }

        /**
         * Activation Tasks
         */
        function activation_tasks() {
            $afap_settings = $this->get_default_settings();
            $afap_extra_settings = array('authorize_status' => 0);
            if (!get_option('afap_settings')) {
                update_option('afap_settings', $afap_settings);
                update_option('afap_extra_settings', $afap_extra_settings);
            }

            global $wpdb;

            $charset_collate = $wpdb->get_charset_collate();
            $log_table_name = $wpdb->prefix . "afap_logs";


            $log_tbl_query = "CREATE TABLE IF NOT EXISTS $log_table_name (
                                log_id INT NOT NULL AUTO_INCREMENT,
                                PRIMARY KEY(log_id),
                                post_id INT NOT NULL,
                                log_status INT NOT NULL,
                                log_time VARCHAR(255),
                                log_details TEXT
                              ) $charset_collate;";
            //echo $log_tbl_query;
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta($log_tbl_query);
            //die();
        }

        /**
         * Starts session on admin_init hook
         */
        function plugin_init() {
            if (!session_id()) {
                session_start();
            }
            load_plugin_textdomain( 'afap', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
        }

        /**
         * Returns Default Settings
         */
        function get_default_settings() {
            $default_settings = array('auto_publish' => 0,
                'application_id' => '',
                'application_secret' => '',
                'facebook_user_id' => '',
                'message_format' => '',
                'post_format' => 'simple',
                'include_image'=>0,
                'post_image' => 'featured',
                'custom_image_url' => '',
                'auto_post_pages' => array(),
                'post_types' => array(),
                'category' => array());
            return $default_settings;
        }

        /**
         * Registers Admin Menu
         */
        function afap_admin_menu() {
            add_menu_page(__('AccessPress Facebook Auto Post', 'accesspress-facebook-auto-post'), __('AccessPress Facebook Auto &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Post', 'accesspress-facebook-auto-post'), 'manage_options', 'afap', array($this, 'plugin_settings'),'dashicons-facebook-alt');
        }

        /**
         * Plugin Settings Page
         */
        function plugin_settings() {
            include('inc/main-page.php');
        }

        /**
         * Registers Admin Assets
         */
        function register_admin_assets() {
            if (isset($_GET['page']) && $_GET['page'] == 'afap') {
                wp_enqueue_style('apsp-fontawesome-css', AFAP_CSS_DIR.'/font-awesome.min.css', AFAP_VERSION);
                wp_enqueue_style('afap-admin-css', AFAP_CSS_DIR . '/admin-style.css', array(), AFAP_VERSION);
                wp_enqueue_script('afap-admin-js', AFAP_JS_DIR . '/admin-script.js', array('jquery'), AFAP_VERSION);
            }
        }

        /**
         * Returns all registered post types
         */
        function get_registered_post_types() {
            $post_types = get_post_types();
            unset($post_types['revision']);
            unset($post_types['attachment']);
            unset($post_types['nav_menu_item']);
            return $post_types;
        }

        /**
         * Prints array in pre format
         */
        function print_array($array) {
            echo "<pre>";
            print_r($array);
            echo "</pre>";
        }

        /**
         * Action to authorize the facebook
         */
        function fb_authorize_action() {
//die('reached');
            if (!empty($_POST) && wp_verify_nonce($_POST['afap_fb_authorize_nonce'], 'afap_fb_authorize_action')) {
                include('inc/cores/fb-authorization.php');
            } else {
                die('No script kiddies please');
            }
        }

        /**
         * Facebook Authorize Callback
         */
        function afap_callback_authorize() {
            if (isset($_COOKIE['afap_session_state']) && isset($_REQUEST['state']) && ($_COOKIE['afap_session_state'] === $_REQUEST['state'])) {
                include('inc/cores/fb-authorization-callback.php');
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Action to save settings
         */
        function afap_form_action() {
            if (!empty($_POST) && wp_verify_nonce($_POST['afap_form_nonce'], 'afap_form_action')) {
                include('inc/cores/save-settings.php');
            } else {
                die('No script kiddies please!!');
            }
        }

        /**
         * Auto Post Trigger
         * */
        function auto_post_trigger() {
            $post_types = $this->get_registered_post_types();
            foreach ($post_types as $post_type) {
                $publish_action = 'publish_' . $post_type;
                $publish_future_action = 'publish_future_'.$post_type;
              //  add_action($publish_action, array($this, 'auto_post'), 10, 2);
              //  add_action($publish_action, array($this, 'auto_post_schedule'), 10, 2);
                
            }
        }

        /**
         * Auto Post Action
         * */
        function auto_post($new_status, $old_status, $post) {
            if($new_status == 'publish'){
                $auto_post = $_POST['afap_auto_post'];
                if ($auto_post == 'yes' || $auto_post == '') {
                    include_once('api/facebook.php'); // facebook api library
                    include('inc/cores/auto-post.php');
                    $check = update_post_meta($post->ID, 'afap_auto_post', 'no');
                    $_POST['afap_auto_post'] = 'no';
                }
            }
        }
        
        function auto_post_schedule($post){
            $auto_post = get_post_meta($post->ID,'afap_auto_post',true);
            if ($auto_post == 'yes' || $auto_post == '') {
                include_once('api/facebook.php'); // facebook api library
                include('inc/cores/auto-post.php');
                $check = update_post_meta($post->ID, 'afap_auto_post', 'no');
                $_POST['afap_auto_post'] = 'no';
            }
        }

        /**
         * Clears Log from Log Table
         */
        function afap_clear_log() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'afap-clear-log-nonce')) {
                global $wpdb;
                $log_table_name = $wpdb->prefix . 'afap_logs';
                $wpdb->query("TRUNCATE TABLE $log_table_name");
                $_SESSION['afap_message'] = __('Logs cleared successfully.', 'accesspress-facebook-auto-post');
                wp_redirect(admin_url('admin.php?page=afap&tab=logs'));
                exit();
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * 
         * Delete Log
         */
        function delete_log() {
            if (!empty($_GET) && wp_verify_nonce($_GET['_wpnonce'], 'afap_delete_nonce')) {
                $log_id = $_GET['log_id'];
                global $wpdb;
                $table_name = $wpdb->prefix . 'afap_logs';
                $wpdb->delete($table_name, array('log_id' => $log_id), array('%d'));
                $_SESSION['afap_message'] = __('Log Deleted Successfully', 'accesspress-facebook-auto-post');
                wp_redirect(admin_url('admin.php?page=afap'));
            } else {
                die('No script kiddies please!');
            }
        }

        /**
         * Plugin's meta box
         * */
        function add_afap_meta_box($post_type) {
            add_meta_box(
                    'afap_meta_box'
                    , __('AccessPress Facebook Auto Post', 'accesspress-facebook-auto-post')
                    , array($this, 'render_meta_box_content')
                    , $post_type
                    , 'side'
                    , 'high'
            );
        }

        /**
         * afap_meta_box html
         * 
         * */
        function render_meta_box_content($post) {
            // Add an nonce field so we can check for it later.
            wp_nonce_field('afap_meta_box_nonce_action', 'afap_meta_box_nonce_field');
            $default_auto_post = in_array($post->post_status, array("future", "draft", "auto-draft", "pending"))?'yes':'no';
            // Use get_post_meta to retrieve an existing value from the database.
            $auto_post = get_post_meta($post->ID, 'afap_auto_post', true);
            //var_dump($auto_post);
            $auto_post = ($auto_post == '' || $auto_post == 'yes') ? $default_auto_post : 'no';

            // Display the form, using the current value.
            ?>
            <label for="afap_auto_post"><?php _e('Enable Auto Post', 'accesspress-facebook-auto-post'); ?></label>
            <p>
                <select name="afap_auto_post">
                    <option value="yes" <?php selected($auto_post, 'yes'); ?>><?php _e('Yes', 'accesspress-facebook-auto-post'); ?></option>
                    <option value="no" <?php selected($auto_post, 'no'); ?>><?php _e('No', 'accesspress-facebook-auto-post'); ?></option>
                </select>
            </p>
            <?php
        }

        /**
         * Saves meta value
         * */
        function save_afap_meta_value($post_id) {
            //$this->print_array($_POST);die('abc');
            /*
             * We need to verify this came from the our screen and with proper authorization,
             * because save_post can be triggered at other times.
             */

            // Check if our nonce is set.
            if (!isset($_POST['afap_auto_post']))
                return $post_id;

            $nonce = $_POST['afap_meta_box_nonce_field'];

            // Verify that the nonce is valid.
            if (!wp_verify_nonce($nonce, 'afap_meta_box_nonce_action'))
                return $post_id;

            // If this is an autosave, our form has not been submitted,
            //     so we don't want to do anything.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                return $post_id;

            // Check the user's permissions.
            if ('page' == $_POST['post_type']) {

                if (!current_user_can('edit_page', $post_id))
                    return $post_id;
            } else {

                if (!current_user_can('edit_post', $post_id))
                    return $post_id;
            }

            /* OK, its safe for us to save the data now. */

            // Sanitize the user input.
            $auto_post = sanitize_text_field($_POST['afap_auto_post']);

            // Update the meta field.
            update_post_meta($post_id, 'afap_auto_post', $auto_post);
        }
        
        /**
         * Restores Default Settings
         */
        function restore_settings(){
            $afap_settings = $this->get_default_settings();
            $afap_extra_settings = array('authorize_status'=>0);
            update_option('afap_extra_settings', $afap_extra_settings);
            update_option('afap_settings', $afap_settings);
            $_SESSION['afap_message'] = __('Default Settings Restored Successfully','accesspress-facebook-auto-post');
            wp_redirect('admin.php?page=afap');
            exit();
        }
        
        	
        	

    }

    $afap_obj = new AFAP_Class();
}// class Termination


