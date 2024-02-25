<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.prositeweb.ca/
 * @since      1.0.0
 *
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/admin
 * @author     Prositeweb Inc <contact@prositeweb.ca>
 */
class Prositecaptcha_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prositecaptcha_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prositecaptcha_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prositecaptcha-admin.css', array(), filemtime(plugin_dir_path( __FILE__ ) . 'css/prositecaptcha-admin.css'), 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Prositecaptcha_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Prositecaptcha_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/prositecaptcha-admin.js', array( 'jquery' ), $this->version, true );

	}
	
	/**
 *  Register the administration menu for this plugin into the WordPress Dashboard
 * @since    1.0.0
 */

public function add_prositecaptcha_admin_setting() {

    /*
     * Add a settings page for this plugin to the Settings menu.
     *
     * Administration Menus: http://codex.wordpress.org/Administration_Menus
     *
     */
    add_options_page( 'prositecaptcha Recaptcha Page', 'prositecaptcha', 'manage_options', $this->plugin_name, array($this, 'display_prositecaptcha_settings_page')
    );
}

/**
 * Render the settings page for this plugin.( The html file )
 *
 * @since    1.0.0
 */

public function display_prositecaptcha_settings_page() {
    include_once( 'partials/prositecaptcha-admin-display.php' );
}

/**
 * Registers and Defines the necessary fields we need.
 *
 */
public function prositecaptcha_admin_settings_save(){
    register_setting( $this->plugin_name, $this->plugin_name, array($this, 'plugin_options_validate') );

    add_settings_section('prositecaptcha_main',"", array($this, 'prositecaptcha_section_text'), 'prositecaptcha-settings-page');
    add_settings_field('api_version', esc_html__('Google recaptcha Version'), array($this, 'prositecaptcha_api_version'), 'prositecaptcha-settings-page', 'prositecaptcha_main');

    add_settings_field('api_sid', esc_html__('API reCaptcha key', 'prositecaptcha'), array($this, 'prositecaptcha_setting_sid'), 'prositecaptcha-settings-page', 'prositecaptcha_main');

    add_settings_field('api_auth_token', esc_html__('API reCaptcha Secret Key', 'prositecaptcha'), array($this, 'prositecaptcha_setting_token'), 'prositecaptcha-settings-page', 'prositecaptcha_main');
    
     add_settings_field('show_on_login', esc_html__('Show ReCaptcha on Login', 'prositecaptcha'), array($this, 'show_recaptcha_login'), 'prositecaptcha-settings-page', 'prositecaptcha_main');
     
     add_settings_field('show_recaptcha_registration', esc_html__('Show ReCaptcha on Registration', 'prositecaptcha'), array($this, 'show_recaptcha_registration'), 'prositecaptcha-settings-page', 'prositecaptcha_main');
    
}

/**
 * Displays the settings sub header
 *
 */
public function prositecaptcha_section_text() {
} 

/**
 * Renders the sid input field
 *
 */
 public function prositecaptcha_api_version() {
     $options = get_option($this->plugin_name);
$valeurs = array("V3" => esc_html__( 'reCAPTCHA V3','prositecaptcha'),"V2" => esc_html__( 'reCAPTCHA V2','prositecaptcha'), "None" => esc_html__( 'Disable ReCaptcha','prositecaptcha'));
$version = $this->plugin_name.'[api_version]';
$selection = "";
foreach($valeurs as $key => $val) {
   if(isset($options['api_version']) && $options['api_version'] == $key) {
       $extra = "checked";
   }else {
        $extra = "";
   }
   $selection .='<label>
			<input type="radio" name="'.$version.'" value="'.$key.'" ' .$extra.'/>
			<span>'.$val.'</span>
		</label>';
   
}
   echo $selection;
} 
public function prositecaptcha_setting_sid() {

   $options = get_option($this->plugin_name);
   if(isset($options['api_sid']) && !empty($options['api_sid'])){
       $api_sid_name = $options['api_sid'];
   } else {
       $api_sid_name = "";
   }
   echo "<input id='plugin_text_string' name='$this->plugin_name[api_sid]' size='40' type='text' value='{$api_sid_name}' />";
}   

/**
 * Renders the auth_token input field
 *
 */
public function prositecaptcha_setting_token() {
   $options = get_option($this->plugin_name);
   if(isset($options['api_auth_token']) && !empty($options['api_auth_token'])) {
       $token = $options['api_auth_token'];
   } else {
     $token = "";  
   }
   echo "<input id='plugin_text_string' name='$this->plugin_name[api_auth_token]' size='40' type='password' value='{$token}' />";
}


 public function show_recaptcha_login() {
     $options = get_option($this->plugin_name);
$valeurs = array("Yes" => esc_html__( 'Yes','prositecaptcha'),"No" => esc_html__( 'No','prositecaptcha'));
$version = $this->plugin_name.'[show_on_login]';
$selection = "";
foreach($valeurs as $key => $val) {
   if(isset($options['show_on_login']) && $options['show_on_login'] == $key) {
       $extra = "checked";
   }else {
        $extra = "";
   }
   $selection .='<label>
			<input type="radio" name="'.$version.'" value="'.$key.'" ' .$extra.'/>
			<span>'.$val.'</span>
		</label>';
   
}
   echo $selection;
} 
public function show_recaptcha_registration() {
     $options = get_option($this->plugin_name);
$valeurs = array("Yes" => esc_html__( 'Yes','prositecaptcha'),"No" => esc_html__( 'No','prositecaptcha'));
$version = $this->plugin_name.'[show_on_registration]';
$selection = "";
foreach($valeurs as $key => $val) {
   if(isset($options['show_on_registration']) && $options['show_on_registration'] == $key) {
       $extra = "checked";
   }else {
        $extra = "";
   }
   $selection .='<label>
			<input type="radio" name="'.$version.'" value="'.$key.'" ' .$extra.'/>
			<span>'.$val.'</span>
		</label>';
   
}
   echo $selection;
} 
/**
 * Sanitises all input fields.
 *
 */
public function plugin_options_validate($input) {
    $newinput['api_version'] = trim($input['api_version']);
    $newinput['api_sid'] = trim($input['api_sid']);
    $newinput['api_auth_token'] = trim($input['api_auth_token']);
    $newinput['show_on_login'] = trim($input['show_on_login']);
    $newinput['show_on_registration'] = trim($input['show_on_registration']);
    return $newinput;
}
}
