<?php
/*
 * Plugin Name: Shortcode Maker
 * Description: A plugin to to let users make shortcodes of their own and use them in wp editor
 * Plugin URI: http://cybercraftit.com/product/shortcode-maker/
 * Author URI: http://cybercraftit.com/
 * Author: CyberCraft
 * Text Domain: shortcode-maker
 * Domain Path: /languages
 * Version: 5.0.4.2
 * License: GPL2
 */
/**
 * Copyright (c) YEAR Mithu A Quayium (email: cemithu06@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'SHORTCODE_MAKER_VERSION', '5.0.4.2' );
define( 'SHORTCODE_MAKER_ROOT', dirname(__FILE__) );
define( 'SHORTCODE_MAKER_ASSET_PATH', plugins_url('assets',__FILE__) );
define( 'SHORTCODE_MAKER_BASE_FILE', __FILE__ );


class shortcode_maker{

	private $shorcode_array = array();

    function __construct(){

        $this->shorcode_array = get_option('shortcode_list');
        !is_array( $this->shorcode_array ) ? $this->shorcode_array = array() : '';

        add_action('init',array($this,'register_shortcode'));
		add_action('save_post',array($this,'save_post_func'));

        add_action('init',array($this,'shortcode_add'));
		add_action('wp_trash_post',array($this,'remove_shortcode'));

        add_action( 'admin_head', array( $this , 'shortcode_array_js' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_styles' ) );

        //ajax
        add_action( 'wp_ajax_sm_get_shortcode_atts', array( $this, 'get_shortcode_atts_panel' ) );

        add_action( 'init', array($this, 'load_textdomain') );
        register_activation_hook( __FILE__, array( $this, 'plugin_activation_task' ) );

        add_filter( 'widget_text', array( $this, 'render_widget_shortcode' ) );

        //add to the custom item panel
        add_filter( 'smps_shortcode_items', array( $this, 'add_in_packaged_shortcode_panel' ) );
        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array( $this, 'sm_action_links' ) );
        add_action( 'admin_notices', array( $this, 'plugin_admin_notice' ) );
        $this->includes();
	}

	public function add_in_packaged_shortcode_panel( $items ) {

	    foreach ( $this->shorcode_array as $id => $shortcode_name ) {
	        $items[$id] = array(
	            'section' => 'Custom',
                'label' => $shortcode_name
            );
        }

        return $items;
    }

	function render_widget_shortcode( $text ) {
	    return do_shortcode( $text );
    }

	public function plugin_activation_task() {
	    do_action( 'shortcode_maker_activation_task' );
    }

    function includes(){

        if( file_exists( dirname(__FILE__).'/pro/loader.php' )) {
            include_once dirname(__FILE__).'/pro/loader.php';
        }

        require_once dirname(__FILE__).'/ajax-action.php';
        require_once dirname(__FILE__).'/vote.php';
        require_once dirname(__FILE__).'/sm-functions.php';
        require_once dirname(__FILE__).'/shortcode-field.php';
        include_once dirname(__FILE__).'/packaged-shortcodes/packaged-shortcodes.php';
        require_once dirname(__FILE__).'/more-products.php';
        require_once dirname(__FILE__).'/news.php';
        if( !sm_is_pro() ) {
            include_once SHORTCODE_MAKER_ROOT.'/pro-demo.php';
        }
    }

    /**
     * Load the translation file for current language.
     *
     * @since version 2.2
     */
    function load_textdomain() {
        load_plugin_textdomain( 'shortcode-maker', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    /**
     * Convert shortcode array to js
     */
    function shortcode_array_js() {
        ?>
        <script>
            var shortcode_array = '<?php echo json_encode($this->shorcode_array); ?>';
        </script>
    <?php
    }


    /**
     * get shortcode attribute panel
     */
    public function get_shortcode_atts_panel() {
        $shortcode_atts = get_post_meta( $_POST['shortcode_id'], 'sm_shortcode_atts' , true );

        if( !empty( $shortcode_atts ) ) {

            wp_send_json_success( array(
                'id' => $_POST['shortcode_id'],
                'shortcode_atts' => $shortcode_atts
            ) );
        }
        wp_send_json_error();
        ?>

        <?php
        exit;
    }

    /**
     * Register shortcode
     */
	function register_shortcode(){
		$labels = array(
				'name' => _x('Shortcode', 'post type general name', 'shortcode-maker'),
				'singular_name' => _x('Shortcode', 'post type singular name','shortcode-maker'),
				'menu_name' => _x( 'Shortcode', 'admin menu', 'shortcode-maker'),
				'name_admin_bar' => _x( 'Shortcode', 'add new on admin bar', 'shortcode-maker'),
				'add_new' => _x('Add New Shortcode', 'Shortcode' , 'shortcode-maker' ),
				'add_new_item' => __('Add New Shortcode', 'shortcode-maker'),
				'edit_item' => __('Edit Shortcode', 'shortcode-maker'),
				'new_item' => __('New Shortcode' , 'shortcode-maker' ),
				'view_item' => __('View Shortcode', 'shortcode-maker' ),
				'all_items' => __( 'All Shortcode', 'shortcode-maker' ),
				'search_items' => __('Search Shortcode', 'shortcode-maker' ),
				'not_found' =>  __('Nothing found', 'shortcode-maker' ),
				'not_found_in_trash' => __('Nothing found in Trash', 'shortcode-maker' ),
				'parent_item_colon' => '',

			);
			$args = array(
				'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_menu' => true,
			'query_var' => true,
			'rewrite' => array('slug' => 'sm_shortcode'),
			'capability_type' => 'post',
			'has_archive' => false,
			'hierarchical' => false,
			'menu_position' => 3,
			'supports' => array(
				'title',
				'editor',
				),

			);
			register_post_type( 'sm_shortcode' , $args );
	}

    /**
     * shortcode_list option updated
     * @param $post_id
     */
	function save_post_func($post_id){

		if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'sm_shortcode'){
			$this->shorcode_array[$post_id] = get_the_title($post_id);
			update_option('shortcode_list',$this->shorcode_array);

		}
	}

    /**
     * Add shortocode
     */
	function shortcode_add(){

		if(!is_array($this->shorcode_array))return;

		foreach($this->shorcode_array as $each_shortcode){
			add_shortcode($each_shortcode,array($this,'shortcode_content'));
		}
	}

    /**
     * Eacch shortcode definition
     * @param $atts
     * @param null $content
     * @param $tag
     * @return mixed
     */
    function shortcode_content($atts,$content = NULL,$tag){

        if(in_array($tag,$this->shorcode_array)){

            $post_id = array_search($tag,$this->shorcode_array);
            $default_atts = get_post_meta( $post_id, 'sm_shortcode_atts', true );
            !is_array($default_atts) ? $default_atts = array() : '';

            $atts = shortcode_atts(
                $default_atts,
                $atts, $tag );

            $search = array();
            $replace = array();

            foreach( $atts as $att_name => $att_value ) {
                $search[] = '%' . $att_name . '%';
                $replace[] =  $att_value ;
            }

            if( !$content ) {
                $content = do_shortcode( nl2br( get_post($post_id)->post_content ) );
            }

            return str_replace( $search, $replace, $content );
		}
	}


    /**
     * Remove the shortcode if
     * shortcode is deleted
     * @param $post_id
     */
	function remove_shortcode($post_id){

        if(get_post_type($post_id) == 'sm_shortcode'){
            unset($this->shorcode_array[$post_id]);
            update_option('shortcode_list',$this->shorcode_array);
        }
	}


    /**
     * Add scripts and styles
     */
    public function admin_enqueue_scripts_styles( $hook ) {
        global $post;

        if( in_array( $hook, array(
            'post.php',
            'post-new.php'
        ))) {
            /*SHORTCODE_MAKER_ASSET_PATH*/
            wp_enqueue_style( 'sm-style', SHORTCODE_MAKER_ASSET_PATH.'/css/style.css' );
            wp_enqueue_script( 'sm-vue', SHORTCODE_MAKER_ASSET_PATH.'/js/vue.js' );
        }

        if( isset( $post->ID ) && get_post_type( $post->ID ) == 'sm_shortcode' ) {
            wp_enqueue_script( 'sm-script-js', SHORTCODE_MAKER_ASSET_PATH.'/js/script.js', array( 'sm-vue' ), false, true );
        }

        if( in_array( $hook, array(
            'sm_shortcode_page_smps_shortcode_packages',
            'post-new.php',
            'post.php'
        ) ) ) {

            //colorpicker
            wp_enqueue_style('wp-color-picker');
            wp_enqueue_style( 'sm-post-css', SHORTCODE_MAKER_ASSET_PATH.'/css/sm-post.css' );
            //timepicker addon css
            wp_enqueue_style( 'sm-timepicker-css', SHORTCODE_MAKER_ASSET_PATH.'/css/timepicker-addon.css' );

            wp_enqueue_script( 'sm-bs-js', SHORTCODE_MAKER_ASSET_PATH.'/js/bootstrap.min.js', array( 'jquery','wp-color-picker','jquery-ui-datepicker' ), false, true );

            wp_enqueue_script( 'sm-bs-js', SHORTCODE_MAKER_ASSET_PATH.'/js/bootstrap.min.js', array( 'jquery','wp-color-picker','jquery-ui-datepicker' ), false, true );
            //color picker component
            wp_enqueue_script( 'sm-color-picker-js', plugins_url('components/js/vue-color.min.js',__FILE__), array( 'sm-vue' ), false, true );

            wp_enqueue_script( 'sm-admin-js', SHORTCODE_MAKER_ASSET_PATH.'/js/smps-admin-script.js', array( 'jquery','wp-color-picker','jquery-ui-datepicker' ), false, true );

            //wp_enqueue_script( 'sm-post-js', SHORTCODE_MAKER_ASSET_PATH.'/js/sm-post.js', array( 'jquery','sm-vue', 'wp-color-picker','jquery-ui-datepicker' ), false, true );
            //timepicker addon
            wp_enqueue_script('sm-timepicker-addon', SHORTCODE_MAKER_ASSET_PATH.'/js/timepicker-addon.js', array('jquery-ui-datepicker'));
        }

        do_action( 'sm_admin_enqueue_scripts', $hook );

    }

    public function plugin_admin_notice() {
        global $post, $pagenow;
        $admin_notices = sm_get_notice( 'sm_admin_notices' );
        if( !isset( $admin_notices['modification_notice']['is_dismissed'] ) || !$admin_notices['modification_notice']['is_dismissed'] ) {
            if( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
                ?>
                <div class="sm_modification_notice notice notice-success is-dismissible">
                    <p><?php _e( 'Need to modify Shortcode Maker ? Don\'t worry. We are letting you modify it as you need ! Please, feel free to <a href="https://cybercraftit.com/contact/" target="_blank">contact us</a>.', 'sm' ); ?></p>
                </div>
                <?php
            }
        }

    }

    public function sm_action_links( $links ) {
        $links[] = '<a href="https://cybercraftit.com/contact/" target="_blank">'.__( 'Ask for Modification', 'sm' ).'</a>';
        return $links;
    }
}
new shortcode_maker;

// add plugin upgrade notification
add_action('in_plugin_update_message-shortcode-maker/index.php', 'sm_showUpgradeNotification', 10, 2);
function sm_showUpgradeNotification($currentPluginMetadata, $newPluginMetadata){
    // check "upgrade_notice"
    if (isset($newPluginMetadata->upgrade_notice) && strlen(trim($newPluginMetadata->upgrade_notice)) > 0){
        echo '<p style="background-color: #d54e21; padding: 10px; color: #f9f9f9; margin-top: 10px"><strong>Important Upgrade Notice:</strong></p> ';
        echo esc_html($newPluginMetadata->upgrade_notice), '</p>';
    }
}

function wp_upe_upgrade_completed( $upgrader_object, $options ) {
    // The path to our plugin's main file
    $our_plugin = plugin_basename( __FILE__ );
    // If an update has taken place and the updated type is plugins and the plugins element exists
    if( $options['action'] == 'update' && $options['type'] == 'plugin' && isset( $options['plugins'] ) ) {
        // Iterate through the plugins being updated and check if ours is there
        foreach( $options['plugins'] as $plugin ) {
            if( $plugin == $our_plugin ) {
                // Set a transient to record that our plugin has just been updated
                SM_Packaged_Shortcodes::set_data_on_activation();
            }
        }
    }
}
add_action( 'upgrader_process_complete', 'wp_upe_upgrade_completed', 10, 2 );