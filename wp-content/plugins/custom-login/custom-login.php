<?php

/**
 * Plugin Name: Custom Login 2.0
 * Plugin URI: http://extendd.com/plugin/custom-login
 * Description: A simple way to customize your WordPress <code>wp-login.php</code> screen! Use the built in, easy to use <a href="./options-general.php?page=custom-login">settings</a> page to do the work for you. Share you designs on <a href="http://flickr.com/groups/custom-login/">Flickr</a> or get Custom Login extensions at <a href="http://extendd.com/plugins/tag/custom-login-extension">Extendd.com</a>.
 * Version: 2.1.6
 * Author: Austin Passy
 * Author URI: http://austinpassy.com
 * Text Domain: custom-login
 *
 * @copyright 2012 - 2013
 * @author Austin Passy
 * @link http://austinpassy.com/
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @class custom_login_pro
 */
 
if ( !class_exists( 'Custom_Login' ) ) :
class Custom_Login {
	
	/** Singleton *************************************************************/
	private static $instance;
	
	/**
	 * Version
	 */
	var $version = '2.1.6';
	
	/**
	 * Plugin vars
	 */
	var $id,
		$domain;
	
	/**
	 * Private settings
	 */
	private $settings_api;
	private $sections;
	
	/**
	 * Options page
	 */
	public $options_page;

	/**
	 * Main Instance
	 *
	 * @staticvar 	array 	$instance
	 * @return 		The one true instance
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new custom_login;
			self::$instance->setup_constants();
			self::$instance->plugin_textdomain();
			self::$instance->required_functions();
			self::$instance->init();
		}
		return self::$instance;
	}
	
	/**
	 * To infinity and beyond
	 */
	function init() {
		
		/* vars */
		$this->id		= 'custom_login';
		$this->domain	= 'custom-login';
		
		/* Constants */
		add_action( 'admin_init',							array( $this, 'check_version' ), 1 );
		
		/* Constants */
		add_action( 'init',									array( $this, 'setup_constants' ) );
		
		/* Scripts */
		add_action( 'login_enqueue_scripts',				array( $this, 'enqueue_scripts' ) );
		
		/* Custom jQuery templates */
		add_action( 'login_footer',							array( $this, 'login_footer_jquery' ) );
		
		/* Includes */
		add_action( 'init',									array( $this, 'required_classes' ) );
		add_action( 'init',									array( $this, 'required_functions' ) );
		
		/* Shortcodes */
		add_action( 'init',									array( $this, 'add_shortcodes' ) );
		
		/* Settings */
		add_action( 'admin_init',							array( $this, 'admin_init' ), 9 );
		add_action( 'admin_menu',							array( $this, 'admin_menu' ), 9 );
		
		/* Clear transient cache button */
		add_action( $this->id .
			'_form_bottom_' . $this->id,					array( $this, 'delete_transient_button_output' ) );
			
		/* Delete transient action */
        add_action( 'admin_action_' . 
			$this->id . '-delete_transient',				array( $this, 'delete_custom_login_transient_cache' ) );
			
		/* Notices */
		add_action( 'admin_notices',						array( $this, 'admin_messages' ) );
		
		/* Add a settings page to the plugin menu */
		add_filter( 'plugin_action_links',					array( $this, 'plugin_action_links' ), 10, 2 );
		
		/* Filter in your URL */
		add_filter( 'login_headerurl',						array( $this, 'login_url' ) );
		
		/* Filter in your description */
		add_filter( 'login_headertitle',					array( $this, 'login_title' ) );			
		
		/* Custom HTML */
		add_action( 'login_footer',							array( $this, 'login_footer_html' ) );
	}
	
	/**
	 * WordPress version check
	 *
	 * @since 2.0.3
	 */
	function check_version() {
		global $wp_version;
		
		if ( version_compare( $wp_version, '3.5', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'version_notification' ) );
			if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) deactivate_plugins( plugin_basename( __FILE__ ) );
		}
	}
	
	/**
	 * Deactivation notice
	 *
	 */
	function version_notification() {
		global $wp_version;
		
		$html  = '<div class="error"><p>'; 
		$html .= sprintf( __( 'Custom Login has been deactivated because it requires a WordPress version greater than 3.5. You are running <code>%s</code>', $this->domain ), $wp_version );
		$html .= '</p></div>';
		
		echo $html;
	}
	
	/**
	 * Setup plugin constants
	 *
	 * @since 2.0
	 * @access private
	 * @uses plugin_dir_path()
	 * @uses plugin_dir_url()
	 */
	function setup_constants() {
		
		// Plugin version
		if ( ! defined( 'CUSTOM_LOGIN_VERSION' ) )
			define( 'CUSTOM_LOGIN_VERSION', $this->version );
			
		// Plugin settings
		if ( ! defined( 'CUSTOM_LOGIN_SETTINGS' ) )
			define( 'CUSTOM_LOGIN_SETTINGS', $this->id );

		// Plugin Folder URL
		if ( ! defined( 'CUSTOM_LOGIN_URL' ) )
			define( 'CUSTOM_LOGIN_URL', plugin_dir_url( __FILE__ ) );

		// Plugin Folder Path
		if ( ! defined( 'CUSTOM_LOGIN_DIR' ) )
			define( 'CUSTOM_LOGIN_DIR', plugin_dir_path( __FILE__ ) );

		// Plugin Root File
		if ( ! defined( 'CUSTOM_LOGIN_FILE' ) )
			define( 'CUSTOM_LOGIN_FILE', __FILE__ );
	}
	
	/**
	 * Load the plugin translations
	 *
	 */
	function plugin_textdomain() {
		load_plugin_textdomain( 'custom-login', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	 * Enqueue additional scripts.
	 *
	 * @since	2.0
	 * @updated	2.1
	 */
	function enqueue_scripts() {
		if ( !$this->is_active() )
			return;	
		
		global $cl_css_atts;

		$cl_css_atts = array(
			'version'	=> CUSTOM_LOGIN_VERSION,
		);
		$cl_css_atts = wp_parse_args( get_option( $this->id, array() ), $cl_css_atts );
		
		ob_start();
			echo "<style type=\"text/css\">\n";
				Custom_Login_Templates::get_template_part( 'wp-login', 'style' );
			echo "\n</style>";
		echo ob_get_clean();
		
		/* Custom jQuery */
		$jquery = $this->get_option( 'custom_jquery', $this->id );
		if ( !empty( $jquery ) ) {
			wp_enqueue_script( array( 'jquery' ) );
		}
	}
	
	/**
	 * Database access to the scripts and styles.
	 *
	 * @since	2.1
	 * @return string|void
	 */
	function login_footer_jquery() {		
		$jquery = $this->get_option( 'custom_jquery', $this->id );
		if ( !empty( $jquery ) ) :
					
			global $cl_js_atts;
		
			$cl_js_atts = array(
				'version'	=> CUSTOM_LOGIN_VERSION,
			);
			$cl_js_atts = wp_parse_args( get_option( $this->id, array() ), $cl_js_atts );
			
			foreach( $cl_js_atts as $atts => $value ) {
				if ( 'custom_jquery' !== $atts && 'version' !== $atts )
					unset( $cl_js_atts[$atts] );
			}
			
			ob_start();
				echo "<script type=\"text/javascript\">\n";
					Custom_Login_Templates::get_template_part( 'wp-login', 'script' );
				echo "\n</script>";				
			echo ob_get_clean();
			
		endif; // jQUery
	}
	
	/**
	 * Includes required functions
	 *
	 */
	function required_classes() {
		if ( is_admin() ) {
			require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/class.settings-api.php' );
			$this->settings_api = new Extendd_Plugin_Settings_API;
			$this->settings_api->set_prefix( $this->id );
			$this->settings_api->set_domain( $this->domain );
			$this->settings_api->set_version( $this->version );
		}
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/templates.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/welcome.php' );
	}
	
	/**
	 * Includes required functions
	 *
	 */
	function required_functions() {
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions/upgrades/upgrade-functions.php' );
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions/upgrades/upgrades.php' );
		//require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions/install.php' ); // Deprecated as of 2.0.3
		//require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions/post-types.php' ); // Deprecated as of 2.0.3
		require_once( trailingslashit( plugin_dir_path( __FILE__ ) ) . 'functions/scripts-styles.php' );
	}
	
	/**
	 * Helper function to require classes if they do not exist.
	 *
	 */
	function class_exists_require( $class_name, $dir_path = null ) {
		if ( !class_exists( $class_name ) ) {
			$dir_path = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'classes/' . $dir_path;
			
			if ( file_exists( $dir_path ) )	require_once( $dir_path );
		}
	}
	
	/**
	 * Add shortcodes
	 *
	 */
	function add_shortcodes() {
		add_shortcode( $this->domain, array( $this, 'login_shortcode' ) );
	}
	
	/**
	 * Login shortcode
	 *
	 */
	function login_shortcode( $atts ) {
		return false;
	}
	
	/**
	 * Get the value of a settings field
	 *
	 * $this->get_option( 'field_name', 'section_name', 'default value' );
	 *
	 * @param string $option settings field name
	 * @param string $section the section name this field belongs to
	 * @param string $default default text if it's not found
	 * @return mixed
	 */
	public function get_option( $option, $section, $default = '' ) {
	 
		$options = get_option( $section );
	 
		if ( isset( $options[$option] ) ) {
			return $options[$option];
		}
	 
		return $default;
	}
	
	/** 
	 * Registers settings section and fields
 	 */
    function admin_init() {
				
        $this->sections = array(
            array(
                'id'	=> $this->id,
                'title' => __( 'General Settings', $this->domain )
            ),
        );

        $fields = array(
            $this->id => array(
                array(
                    'name'		=> 'active',
                    'label'		=> __( 'Activate', $this->domain ),
                    'desc'		=> __( 'Toggle this plugin on &amp; off.', $this->domain ),
                    'type'		=> 'checkbox'
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'break_1',
                    'label'		=> __( '<h4><abbr title="Hyper Text Markup Language">HTML</abbr></h4>', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'html',
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'html_background_color',
                    'label' 	=> __( 'HTML Background Color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> '',
                ),
                array(
                    'name' 		=> 'html_background_url',
                    'label' 	=> __( 'HTML Background Image', $this->domain ),
                    'desc' 		=> __( 'Upload an image or a repeating pattern (optional).', $this->domain ),
                    'type' 		=> 'file',
                    'default' 	=> '',
					'page_id'	=> '0',
					'sanitize_callback' => 'esc_url',
                ),
                array(
                    'name' 		=> 'html_background_position',
                    'label' 	=> __( 'HTML Background Position', $this->domain ),
                    'desc' 		=> sprintf( __( '<a href="%s">html background position</a>.', $this->domain ), 'http://www.w3schools.com/cssref/pr_background-position.asp' ),
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'left top'		=> 'left top',
						'left center'	=> 'left center',
						'left bottom'	=> 'left bottom',
						'right top'		=> 'right top',
						'right center'	=> 'right center',
						'right bottom'	=> 'right bottom',
						'center top'	=> 'center top',
						'center center'	=> 'center center',
						'center bottom'	=> 'center bottom',
                    ),
                ),
                array(
                    'name' 		=> 'html_background_repeat',
                    'label' 	=> __( 'HTML Background repeat', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'no-repeat'	=> 'no-repeat',
                        'repeat' 	=> 'repeat',
                        'repeat-x' 	=> 'repeat-x',
                        'repeat-y' 	=> 'repeat-y',
                    )
                ),
                array(
                    'name' 		=> 'html_background_size',
                    'label' 	=> __( 'HTML Background size', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'none'		=> 'none',
                        'cover' 	=> 'cover',
                        'contain' 	=> 'contain',
                        'flex' 		=> 'flex',
                    )
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'break_2',
                    'label'		=> __( '<h4>Logo</h4>', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'html',
                ),
				/** BREAK **/
                array(
                    'name'		=> 'hide_wp_logo',
                    'label'		=> __( 'Hide the WP logo', $this->domain ),
                    'desc'		=> __( 'Works when there is no logo present, otherwise the WP logo is replaced by your logo.', $this->domain ),
                    'type'		=> 'checkbox'
                ),
                array(
                    'name' 		=> 'logo_background_url',
                    'label' 	=> __( 'Logo', $this->domain ),
                    'desc' 		=> __( 'Replace the WordPress logo (optional).', $this->domain ),
                    'type' 		=> 'file',
                    'default' 	=> '',
					'page_id'	=> '0',
					'sanitize_callback' => 'esc_url',
                ),
                array(
                    'name' 		=> 'logo_background_position',
                    'label' 	=> __( 'Logo Background Position', $this->domain ),
                    'desc' 		=> sprintf( __( '<a href="%s">html background position</a>.', $this->domain ), 'http://www.w3schools.com/cssref/pr_background-position.asp' ),
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'left top'		=> 'left top',
						'left center'	=> 'left center',
						'left bottom'	=> 'left bottom',
						'right top'		=> 'right top',
						'right center'	=> 'right center',
						'right bottom'	=> 'right bottom',
						'center top'	=> 'center top',
						'center center'	=> 'center center',
						'center bottom'	=> 'center bottom',
					),
                ),
                array(
                    'name' 		=> 'logo_background_repeat',
                    'label' 	=> __( 'Logo Background repeat', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'no-repeat'	=> 'no-repeat',
                        'repeat' 	=> 'repeat',
                        'repeat-x' 	=> 'repeat-x',
                        'repeat-y' 	=> 'repeat-y',
                    )
                ),
                array(
                    'name' 		=> 'logo_background_size',
                    'label' 	=> __( 'Logo Background size', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'none'		=> 'none',
                        'cover' 	=> 'cover',
                        'contain' 	=> 'contain',
                        'flex' 		=> 'flex',
                    )
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'break_3',
                    'label'		=> __( '<h4>Login form</h4>', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'html',
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'login_form_background_color',
                    'label' 	=> __( 'Login Form Background Color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> ''
                ),
                array(
                    'name' 		=> 'login_form_background_url',
                    'label' 	=> __( 'Login Form Background URL', $this->domain ),
                    'desc' 		=> __( 'Add an image to the form (optional).', $this->domain ),
                    'type' 		=> 'file',
                    'default' 	=> '',
					'page_id'	=> '0',
					'sanitize_callback' => 'esc_url',
                ),
                array(
                    'name' 		=> 'login_form_background_position',
                    'label' 	=> __( 'Login Form Background Position', $this->domain ),
                    'desc' 		=> sprintf( __( '<a href="%s">html background position</a>.', $this->domain ), 'http://www.w3schools.com/cssref/pr_background-position.asp' ),
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'left top'		=> 'left top',
						'left center'	=> 'left center',
						'left bottom'	=> 'left bottom',
						'right top'		=> 'right top',
						'right center'	=> 'right center',
						'right bottom'	=> 'right bottom',
						'center top'	=> 'center top',
						'center center'	=> 'center center',
						'center bottom'	=> 'center bottom',
                    ),
                ),
                array(
                    'name' 		=> 'login_form_background_repeat',
                    'label' 	=> __( 'Login Form Background repeat', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'no-repeat'	=> 'no-repeat',
                        'repeat' 	=> 'repeat',
                        'repeat-x' 	=> 'repeat-x',
                        'repeat-y' 	=> 'repeat-y',
                    )
                ),
                array(
                    'name' 		=> 'login_form_background_size',
                    'label' 	=> __( 'Login Form Background size', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'select',
                    'options' 	=> array(
                        'none'		=> 'none',
                        'cover' 	=> 'cover',
                        'contain' 	=> 'contain',
                        'flex' 		=> 'flex',
                    )
                ),
                array(
                    'name' 		=> 'login_form_border_radius',
                    'label' 	=> __( 'Login Form Border Radius', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'text',
					'size'		=> 'small',
                    'default' 	=> '',
					'sanitize_callback' => 'absint',
                ),
                array(
                    'name' 		=> 'login_form_border_size',
                    'label' 	=> __( 'Login Form Border Size', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'text',
					'size'		=> 'small',
                    'default' 	=> '',
					'sanitize_callback' => 'absint',
                ),
                array(
                    'name' 		=> 'login_form_border_color',
                    'label' 	=> __( 'Login Form Border Color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> ''
                ),
                array(
                    'name' 		=> 'login_form_box_shadow',
                    'label' 	=> __( 'Login Form Box Shadow', $this->domain ),
                    'desc' 		=> sprintf( __( 'Use <a href="%s">box shadow</a> syntax w/ out color. <code>inset h-shadow v-shadow blur spread</code>', $this->domain ), 'http://www.w3schools.com/cssref/css3_pr_box-shadow.asp' ),
                    'type' 		=> 'text',
					'size'		=> 'medium',
                    'default' 	=> '5px 5px 10px'
                ),
                array(
                    'name' 		=> 'login_form_box_shadow_color',
                    'label' 	=> __( 'Login Form Box Shadow Color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> ''
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'break_4',
                    'label'		=> __( '<h4>Misc.</h4>', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'html',
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'label_color',
                    'label' 	=> __( 'Login Form Label Color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> ''
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'break_5',
                    'label'		=> __( '<h4>Below form anchor</h4>', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'html',
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'nav_color',
                    'label' 	=> __( 'Below form nav color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> '',
                ),
                array(
                    'name' 		=> 'nav_text_shadow_color',
                    'label' 	=> __( 'Below form nav text-shadow color', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> '',
                ),
                array(
                    'name' 		=> 'nav_hover_color',
                    'label' 	=> __( 'Below form nav color hover', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> '',
                ),
                array(
                    'name' 		=> 'nav_text_shadow_hover_color',
                    'label' 	=> __( 'Below form nav text-shadow hover', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'colorpicker',
                    'default' 	=> '',
                ),
				/** BREAK **/
                array(
                    'name' 		=> 'break_6',
                    'label'		=> __( '<h4>Custom HTML/CSS &amp; jQuery</h4>', $this->domain ),
                    'desc' 		=> '',
                    'type' 		=> 'html',
                ),
				/** BREAK **/
				array(
					'name' 		=> 'custom_css',
					'label' 	=> __( 'Custom CSS', $this->domain ),
					'desc' 		=> '',
					'type' 		=> 'textarea',
					'sanitize_callback' => 'esc_attr',
				),
				array(
					'name' 		=> 'custom_html',
					'label' 	=> __( 'Custom HTML', $this->domain ),
					'desc' 		=> '',
					'type' 		=> 'textarea',
					'sanitize_callback' => 'stripslashes_deep', //Allow HTML
				),
				array(
					'name' 		=> 'custom_jquery',
					'label' 	=> __( 'Custom jQuery', $this->domain ),
					'desc' 		=> '',
					'type' 		=> 'textarea',
					'sanitize_callback' => 'wp_specialchars_decode',
				),
			),
        );
		
        //set sections and fields
        $this->settings_api->set_sections( $this->sections );
		$this->settings_api->set_fields( $fields );

        //initialize them
        $this->settings_api->admin_init();
		
		add_action( $this->id . '_settings_sidebars', array( $this, 'sidebar' ) );
		
		return $this;
//		wp_die( "Bork! <br><pre>" . print_r( $this->settings_api, true ) . "</pre>" );
    }

    /**
	 * Register the plugin page
	 */
    function admin_menu() {
        $this->options_page = add_options_page( __( 'Custom Login Settings', $this->domain ), __( 'Custom Login', $this->domain ), 'manage_options', $this->domain, array( $this, 'plugin_page' ) );
		
		add_action( 'admin_footer-' . $this->options_page, array( $this->settings_api, 'inline_jquery' ) );
    }
	
	/**
	 * Delete the transient
	 *
	 */
	function delete_transient_button_output() {
		$button  = '<div style="padding-left: 10px">';
		$button .= wpautop( $this->delete_transient_button_link() );
		$button .= '<span class="description">' . __( 'If your stylesheet isn\'t updating click update above to delete the transient cache.', $this->domain ) . '</span>';
		$button .= '</div>';
		
		echo $button;
	}
	
	/**
	 * Delete button link output
	 *
	 * @return string
	 */
	function delete_transient_button_link( $class = 'button' ) {
		return sprintf( '<a href="%s" title="%s" class="%s">%s</a>', 
			esc_url( wp_nonce_url( add_query_arg( array( 'action' => $this->id . '-delete_transient' ), admin_url() ), $this->id . '-delete_transient' ) ),
			esc_attr__( 'Clear the transient cache', $this->domain ),
			sanitize_html_class( $class ),
			__( 'Clear stylesheet cache', $this->domain )
		);
	}
	
	/**
	 * Check for post activation on edit.php, when proper action
	 * is called set the post ID and write the content (CSS) to file.
	 * 
	 * @return array()
	 */
	function delete_custom_login_transient_cache() {
		
		if ( !( isset( $_GET[$this->id . '-delete_transient'] ) || ( isset( $_REQUEST['action'] ) && $this->id . '-delete_transient' == $_REQUEST['action'] ) ) )
			return;
			
		check_admin_referer( $this->id . '-delete_transient' );
		
		delete_transient( $this->id . '_style' );
		delete_transient( $this->id . '_script' );
		
		/* Redirect */
		wp_redirect( admin_url( sprintf( 'options-general.php?page=%s&settings-updated=true&message=1', $this->domain ) ) );
		exit;
	}
	
	/**
	 * Show a message when prompted
	 *
	 * @return string|void
	 */
	function admin_messages() {
		global $pagenow;
		
		if ( ( isset( $_GET['page'] ) && isset( $_GET['message'] ) ) && $pagenow == 'options-general.php' && $this->domain == $_GET['page'] ) {
			
			$html = '<div id="setting-error-transitent_deleted" class="updated"><p>'; 
			
			switch ( $_GET['message'] ) {
				case 1 :
					$html .= __( 'The cache has been deleted.', $this->domain );
					break;
					
				case 2 :
					$html .= '';
					break;					
			}
			
			$html .= '</p></div>';
		
			echo $html;
		}
	}
	
	/**
	 * Plugin Action
	 */
	function plugin_action_links( $links, $file ) {
		if ( plugin_basename( __FILE__ ) === $file ) {
			$settings_link = '<a href="' . admin_url( 'options-general.php?page=custom-login' ) . '">' . __( 'Settings', $this->domain ) . '</a>';
			array_unshift( $links, $settings_link ); // before other links
		}
		return $links;
	}

	/**
	 * Display the plugin settings options page
	 */
    function plugin_page() {
        echo '<div class="wrap">';

        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();

        echo '</div>';
		
		if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV && WP_DEBUG ) {
			/**
			foreach( apply_filters( $this->id . '_add_settings_sections', $this->sections ) as $section )
				echo '<pre data-id="'.$section['id'].'">' . print_r( get_option( $section['id'] ), true ) . '</pre>';
			echo '<pre data-id="custom_login_settings">' . print_r( get_option( 'custom_login_settings' ), true ) . '</pre>';
			
			echo '<pre data-id="' . $this->id . '_ignore_announcement">' . print_r( get_user_meta( get_current_user_id(), $this->id . '_ignore_announcement', true ), true ) . '</pre>';
			echo '<pre data-id="' . $this->id . '_announcement_message">' . print_r( get_option( $this->id . '_announcement_message' ), true ) . '</pre>';
			// */
		}
		
    }

	/**
	 * Sidebar info about this plugin
	 *
	 * @since	2.0
	 * @return	string
	 */
	function sidebar( $args ) {
		$content  = '<ul class="social">';
		$content .= '<li class="donate genericon-user"><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=X4JPT57AWMTYW">' . __( 'Support this plugin and buy me a beer', $this->domain ) . '</a></li>';
		$content .= '<li class="rate genericon-star"><a href="http://wordpress.org/extend/plugins/custom-login/">' . __( 'Rate this plugin on WordPress.org', $this->domain ) . '</a></li>';
		$content .= '<li class="share genericon-share"><a href="http://www.flickr.com/groups/custom-login/">' . __( 'Share your designs on <strong style="color:#0066DC;">Flick</strong><strong style="color:#ff0084;">r</strong>', $this->domain ) . '</a></li>';
		$content .= '<li class="support genericon-wordpress"><a href="http://wordpress.org/support/plugin/custom-login">' . __( 'Get support on WordPress.org', $this->domain ) . '</a></li>';
		$content .= '<li class="contribute genericon-github"><a href="https://github.com/thefrosty/custom-login">' . __( 'Contribute development on GitHub', $this->domain ) . '</a></li>';
		$content .= '<li class="addons genericon-link"><a href="http://extendd.com/plugins/tag/custom-login-extension/">' . __( 'Get Custom Login Add-ons', $this->domain ) . '</a></li>';
		
		if ( defined( 'WP_LOCAL_DEV' ) && WP_LOCAL_DEV ) {
			$content .= '<li class="queries genericon-warning">' . $this->get_queries( true ) . '</li>';
		}
		
		$content .= '<li class="delete genericon-warning">' . $this->delete_transient_button_link( '' ) . '</li>';

		$content .= '</ul>';
		$this->settings_api->postbox( $this->id . '_sidebar', sprintf( __( '<a href="%s">%s</a> | <code>version %s</code>', $this->domain ), 'http://extendd.com/plugin/custom-login', ucwords( str_replace( '-', ' ', $this->domain ) ), $this->version ), $content, true );
	}
	
	/**
	 * Is plugin active
	 *
	 */
	function is_active() {
		$active = $this->get_option( 'active', $this->id );
		if ( isset( $active ) && 'on' === $active )
			return true;
			
		return false;
	}
	
	/**
	 * Replace the default link to your URL
	 *
	 */
	function login_url() {
		if ( !is_multisite() ) return home_url( '/' );
		else return network_home_url( '/' );
	}
	
	/**
	 * Replace the default title to your description
	 *
	 */
	function login_title() {
		return get_bloginfo( 'description' );
	}
	
	/**
	 * If there is custom HTML set in the settings echo it to the
	 * `login_footer` hook in wp-login.php.
	 *
	 */
	function login_footer_html() {
		$custom_html = $this->get_option( 'custom_html', $this->id );
		$html  = '';
		
		if ( !empty( $custom_html ) )
			$html .= $custom_html;
		
		$html .= defined( 'WP_DEBUG' ) && WP_DEBUG ? $this->get_queries() : '';
		$html .= "\n";
		
		echo $html;
	}
	
	/**
	 * Helper function to get total numer of queries and execution time
	 *
	 */
	function get_queries( $display = false ) {
		return sprintf( ( $display ? '' : '<div style="display:none">' ) . esc_attr__( '%s queries in %s seconds.', $this->domain ) . ( $display ? '' : '</div>' ), get_num_queries(), timer_stop() );
	}
	
}
endif;

/**
 * The main function responsible for returning the one true
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $custom_login = CUSTOMLOGIN(); ?>
 *
 * @return The one true Instance
 */
if ( !function_exists( 'CUSTOMLOGIN' ) ) {
	function CUSTOMLOGIN() {
		return Custom_Login::instance();
	}
}

// Out of the frying pan, and into the fire.
CUSTOMLOGIN();