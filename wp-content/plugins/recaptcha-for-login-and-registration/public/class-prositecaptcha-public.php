<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.prositeweb.ca/
 * @since      1.0.0
 *
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Prositecaptcha
 * @subpackage Prositecaptcha/public
 * @author     Prositeweb Inc <contact@prositeweb.ca>
 */
class Prositecaptcha_Public {

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
	private $public_key, $private_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
        $options = get_option($this->plugin_name);
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		if(is_array($options)) {
			$this->public_key  = $options['api_sid'];
		$this->private_key = $options['api_auth_token'];
}
	}
    public function login_captcha_display() {
        $options = get_option($this->plugin_name);
        	if(!empty($options) && is_array($options) && array_key_exists('api_sid', $options) && array_key_exists('api_auth_token', $options) && array_key_exists('api_version', $options)) {
        	    
        	       if($options['show_on_login'] == 'No') {
	            return; 
	        }
        $this->public_key  = $options['api_sid'];
		$this->private_key = $options['api_auth_token'];
		$this->api_version = $options['api_version'];
	
		if(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V3") {
		?>
	<input type="hidden" name="recaptcha_response" id="recaptchaResponse">

	<?php
		}elseif(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V2") {
		    	?>
	<div class="g-recaptcha" data-sitekey="<?php echo $this->public_key; ?>"></div>

	<?php
		}
		}
	}
	
	public function registration_captcha_display() {
        $options = get_option($this->plugin_name);
        	if(!empty($options) && is_array($options) && array_key_exists('api_sid', $options) && array_key_exists('api_auth_token', $options) && array_key_exists('api_version', $options)) {
        	    
        	      if($options['show_on_registration'] == 'No') {
	            return; 
	        }
        $this->public_key  = $options['api_sid'];
		$this->private_key = $options['api_auth_token'];
		$this->api_version = $options['api_version'];
	
		if(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V3") {
		?>
	<input type="hidden" name="recaptcha_response" id="recaptchaResponse">

	<?php
		}elseif(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V2") {
		    	?>
	<div class="g-recaptcha" data-sitekey="<?php echo $this->public_key; ?>"></div>

	<?php
		}
		}
	}

	public function validate_captcha_field($user, $password) {
$options = get_option($this->plugin_name);

	if(!empty($options) && is_array($options) && array_key_exists('api_sid', $options) && array_key_exists('api_auth_token', $options) && array_key_exists('api_version', $options) && $options['show_on_login'] != 'No') {
        $this->public_key  = $options['api_sid'];
		$this->private_key = $options['api_auth_token'];
		$this->api_version = $options['api_version'];
		if(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V3") {
		    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = $this->private_key;
    $recaptcha_response = $_POST['recaptcha_response'];
		$recaptcha = wp_remote_get($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
		$recaptcha = wp_remote_retrieve_body($recaptcha);
     $recaptcha = json_decode($recaptcha);   
		   if ($recaptcha->score < 0.5) {
		       return new WP_Error( 'invalid_captcha', esc_html__('CAPTCHA response was incorrect','prositecaptcha')); 
		   }
		
	}elseif(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V2") {
	    if(isset($_POST['g-recaptcha-response'])){
          $captcha=$_POST['g-recaptcha-response'];
        }
        
        if(!$captcha){
return new WP_Error( 'invalid_captcha', esc_html__('CAPTCHA response was incorrect','prositecaptcha')); 
        }
        
        $recaptcha_secret = $this->private_key;
       
        
        $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha = wp_remote_get($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $captcha);
		$recaptcha = wp_remote_retrieve_body($recaptcha);
     $recaptcha = json_decode($recaptcha);  
      if (empty($recaptcha->success)) {
		       return new WP_Error( 'invalid_captcha', esc_html__('CAPTCHA response was incorrect','prositecaptcha')); 
		   }
	}
	}
		return $user;
	}
	
		public function validate_captcha_registration_field($errors, $sanitized_user_login, $user_email) {
$options = get_option($this->plugin_name);
	if(!empty($options) && is_array($options) && array_key_exists('api_sid', $options) && array_key_exists('api_auth_token', $options) && array_key_exists('api_version', $options) && $options['show_on_registration'] != 'No') {
         $this->public_key  = $options['api_sid'];
		$this->private_key = $options['api_auth_token'];
		$this->api_version = $options['api_version'];
		if(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V3") {
		    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = $this->private_key;
    $recaptcha_response = $_POST['recaptcha_response'];
		$recaptcha = wp_remote_get($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
		$recaptcha = wp_remote_retrieve_body($recaptcha);
     $recaptcha = json_decode($recaptcha);   
		   if ($recaptcha->score < 0.5) {
		       return new WP_Error( 'invalid_captcha', esc_html__('CAPTCHA response was incorrect','prositecaptcha')); 
		   }
		
	}
		return $errors;
	}
	}
public function is_registration_page() {
    return 'wp-login.php' === $GLOBALS['pagenow'] && isset($_REQUEST['action']) && $_REQUEST['action'] === 'register';
}

public function is_login_page() {
    return 'wp-login.php' === $GLOBALS['pagenow'] && (!isset($_REQUEST['action']) || $_REQUEST['action'] === 'login');
}



public	function captcha_script() {
	    $options = get_option($this->plugin_name);
	    if(!empty($options) && is_array($options) && array_key_exists('api_sid', $options) && array_key_exists('api_auth_token', $options) && array_key_exists('api_version', $options)) {
	        if($this->is_login_page() && $options['show_on_login'] == 'No') {
	            return; 
	        }
	        
	        
	         if($this->is_registration_page() && $options['show_on_registration'] == 'No') {
	            return; 
	        }
	        
	        
	     $this->public_key  = $options['api_sid'];
		$this->private_key = $options['api_auth_token'];
		$this->api_version = $options['api_version'];
		if(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V3") {
    ?>
    <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $options['api_sid']; ?>"></script>
    <script>
        grecaptcha.ready(function () {
            grecaptcha.execute('<?php echo $options['api_sid']; ?>', { action: 'contact' }).then(function (token) {
                var recaptchaResponse = document.getElementById('recaptchaResponse');
                recaptchaResponse.value = token;
            });
        });
    </script>
    <?php 
	    	} elseif(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V2") {
    ?>
	    	
	    	<script src='https://www.google.com/recaptcha/api.js' async defer></script>
	    	 <?php
	    	}
	    }
}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/prositecaptcha-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
 $options = get_option($this->plugin_name);
 if(!empty($options)) {
     if(isset($options['api_sid']) && !empty($options['api_sid'])) {
	     $this->public_key  = $options['api_sid'];
     } else {
         $this->public_key  = "";
     }
     
      if(isset($options['api_auth_token']) && !empty($options['api_auth_token'])) {
	     $this->private_key   = $options['api_auth_token'];
     } else {
         $this->private_key   = "";
     }
	 if(isset($options['api_version']) && !empty($options['api_version'])) {
	     $this->api_version   = $options['api_version'];
     } else {
         $this->api_version   = "";
     }
	
		if(!empty($this->public_key) && !empty($this->private_key) && $this->api_version == "V3") {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/prositecaptcha-public.js', array( 'jquery' ), $this->version, false );
	}
	}
	}

}


