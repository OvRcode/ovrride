<?php
/*
Plugin Name: Enjoy Instagram
Plugin URI: http://www.mediabeta.com/enjoy-instagram/
Description: Instagram Responsive Images Gallery and Carousel, works with Shortcodes and Widgets.
Version: 1.5
Author: F. Prestipino, F. Di Pane - Mediabeta Srl
Author URI: http://www.mediabeta.com/team/
*/

require_once('library/enjoyinstagram_shortcode.php');
require_once('library/instagram.class.php');
class Settings_enjoyinstagram_Plugin {

	private $enjoyinstagram_general_settings_key = 'enjoyinstagram_general_settings';
	private $advanced_settings_key = 'enjoyinstagram_advanced_settings';
	private $plugin_options_key = 'enjoyinstagram_plugin_options';
	private $plugin_settings_tabs = array();

	function __construct() {
		add_action( 'init', array( &$this, 'load_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_enjoyinstagram_client_id' ) );
		add_action( 'admin_init', array( &$this, 'register_advanced_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
	}
	
	function load_settings() {
		$this->general_settings = (array) get_option( $this->enjoyinstagram_general_settings_key );
		$this->advanced_settings = (array) get_option( $this->advanced_settings_key );
		$this->general_settings = array_merge( array(
			'general_option' => 'General value'
		), $this->general_settings );
		
		$this->advanced_settings = array_merge( array(
			'advanced_option' => 'Advanced value'
		), $this->advanced_settings );
	}
	
	function register_enjoyinstagram_client_id() {
		$this->plugin_settings_tabs[$this->enjoyinstagram_general_settings_key] = 'Profile';
		
		register_setting( $this->enjoyinstagram_general_settings_key, $this->enjoyinstagram_general_settings_key );
		add_settings_section( 'section_general', 'General Plugin Settings', array( &$this, 'section_general_desc' ), $this->enjoyinstagram_general_settings_key );
		add_settings_field( 'general_option', 'A General Option', array( &$this, 'field_general_option' ), $this->enjoyinstagram_general_settings_key, 'section_general' );
	}
	
	 
	function register_advanced_settings() {
		$this->plugin_settings_tabs[$this->advanced_settings_key] = 'Settings';
		
		register_setting( $this->advanced_settings_key, $this->advanced_settings_key );
		add_settings_section( 'section_advanced', 'Advanced Plugin Settings', array( &$this, 'section_advanced_desc' ), $this->advanced_settings_key );
		add_settings_field( 'advanced_option', 'An Advanced Option', array( &$this, 'field_advanced_option' ), $this->advanced_settings_key, 'section_advanced' );
	}
	
	 
	function section_general_desc() { echo 'Instagram Settings'; }
	function section_advanced_desc() { echo 'Manage Enjoy Instagram.'; }
	
	 
	function field_general_option() {
		?>
<input type="text" name="<?php echo $this->enjoyinstagram_general_settings_key; ?>[general_option]" value="<?php echo esc_attr( $this->general_settings['general_option'] ); ?>" /><?php
	}
	
	 
	function field_advanced_option() { ?>
<input type="text" name="<?php echo $this->advanced_settings_key; ?>[advanced_option]" value="<?php echo esc_attr( $this->advanced_settings['advanced_option'] ); ?>" />
<?php
	}
	
 
	function add_admin_menus() {
		add_options_page( 'Enjoy Instagram', 'Enjoy Instagram', 'manage_options', $this->plugin_options_key, array( &$this, 'enjoyinstagram_options_page' ) );
	}
	
	 
	function enjoyinstagram_options_page() {
		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->enjoyinstagram_general_settings_key;?>
	<div class="wrap">	
 	<h2><div class="ei_block">
		<div class="ei_left_block">
         		<div class="ei_hard_block">
		 			<?php echo '<img src="' . plugins_url( 'images/enjoyinstagram.png' , __FILE__ ) . '" > '; ?>
                </div>
         
         		<div class="ei_twitter_block">
					<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.mediabeta.com/enjoy-instagram/" data-text="I've just installed Enjoy Instagram for wordpress. Awesome!" data-hashtags="wordpress">Tweet</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
                    </script>
				</div>

				<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/it_IT/sdk.js#xfbml=1&appId=359330984151581&version=v2.0";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>
				<div class="ei_facebook_block">
					<div class="fb-like" data-href="http://www.mediabeta.com/enjoy-instagram/" data-layout="button_count" data-action="like" data-show-faces="true" data-share="true">
                    </div>
				</div>
		</div>
        
        <div id="buy_me_a_coffee" style="background:url(<?php echo  plugins_url( 'images/buymeacoffee.png' , __FILE__ )  ; ?>)#fff no-repeat; ">
          
          <div class="pad_coffee">
          <span class="coffee_title">Buy me a coffee!</span>
          <p><span>If you liked our work please consider to make a kind donation through Paypal.</span></p>
         <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
            ">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/it_IT/i/scr/pixel.gif" width="1" height="1">
      	</form>
		</div>
 	</div>
	</div>
</h2>
         

<?php $this->plugin_options_tabs(); ?>
<?php 
	if($tab == 'enjoyinstagram_general_settings') { 
		if(isset($_GET['code']) && $_GET['code']!=''){
					
				// get access token
					
			$client_id = get_option('enjoyinstagram_client_id');		
			$client_secret = get_option('enjoyinstagram_client_secret');		
			$redirect_uri = admin_url('options-general.php?page=enjoyinstagram_plugin_options&tab=enjoyinstagram_general_settings');		
			$code = $_GET['code'];		
				
					$apiData = array(
					  'client_id'       => $client_id,
					  'client_secret'   => $client_secret,
					  'grant_type'      => 'authorization_code',
					  'redirect_uri'    => $redirect_uri,
					  'code'            => $code
					);
		
		
			$apiHost = 'https://api.instagram.com/oauth/access_token';
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $apiHost);
			curl_setopt($ch, CURLOPT_POST, count($apiData));
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($apiData));
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$jsonData = curl_exec($ch);
			curl_close($ch);
			$user = json_decode($jsonData,true); 
			 
			$enjoyinstagram_user_id = $user['user']['id'];
			$enjoyinstagram_user_username = $user['user']['username']; 
			$enjoyinstagram_user_profile_picture = $user['user']['profile_picture'];
			$enjoyinstagram_user_fullname = $user['user']['full_name'];
			$enjoyinstagram_user_website = $user['user']['website'];
			$enjoyinstagram_user_bio = $user['user']['bio'];
			$enjoyinstagram_access_token = $user['access_token'];
			
			update_option( 'enjoyinstagram_user_id', $enjoyinstagram_user_id );
			update_option( 'enjoyinstagram_user_username', $enjoyinstagram_user_username );
			update_option( 'enjoyinstagram_user_profile_picture', $enjoyinstagram_user_profile_picture );
			update_option( 'enjoyinstagram_user_fullname', $enjoyinstagram_user_fullname );
			update_option( 'enjoyinstagram_user_website', $enjoyinstagram_user_website );
			update_option( 'enjoyinstagram_user_bio', $enjoyinstagram_user_bio );
			update_option( 'enjoyinstagram_access_token', $enjoyinstagram_access_token );
		
					
					// get accee token fine	
			include('library/profile_auth.php'); 
		
		}else{
				
			if(!(get_option('enjoyinstagram_access_token'))){
				include('library/autenticazione.php'); 
			} else {
				include('library/profile_auth.php'); 
			}
			
		}
	}else if($tab == 'enjoyinstagram_advanced_settings'){ 
		include('library/impostazioni_shortcode.php');
 	} ?>
	</div>
<?php
	}
	
	function plugin_options_tabs() {
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->enjoyinstagram_general_settings_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
		}
	};

// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$Settings_enjoyinstagram_Plugin = new Settings_enjoyinstagram_Plugin;' ) );


function enjoyinstagram_default_option()
{
		add_option('enjoyinstagram_client_id', '');
		add_option('enjoyinstagram_client_secret', '');
		add_option('enjoyinstagram_client_code', '');
		add_option('enjoyinstagram_user_instagram', '');
		add_option('enjoyinstagram_user_id', '');
		add_option('enjoyinstagram_user_username', '');
		add_option('enjoyinstagram_user_profile_picture', '');
		add_option('enjoyinstagram_user_fullname', '');
		add_option('enjoyinstagram_user_website', '');
		add_option('enjoyinstagram_user_bio', '');
		add_option('enjoyinstagram_access_token', '');
		add_option('enjoyinstagram_carousel_items_number', 4);
		add_option('enjoyinstagram_carousel_navigation', 'false');
		add_option('enjoyinstagram_grid_rows', '2');
		add_option('enjoyinstagram_grid_cols', '5');
		add_option('enjoyinstagram_hashtag', '');
		add_option('enjoyinstagram_user_or_hashtag', 'user');
}

register_activation_hook( __FILE__, 'enjoyinstagram_default_option');

function enjoyinstagram_register_options_group_auth()
{
		register_setting('enjoyinstagram_options_group_auth', 'enjoyinstagram_client_id');
		register_setting('enjoyinstagram_options_group_auth', 'enjoyinstagram_client_secret');
		register_setting('enjoyinstagram_options_group_auth', 'enjoyinstagram_client_code');
		register_setting('enjoyinstagram_options_group_auth', 'enjoyinstagram_user_instagram');
}

add_action ('admin_init', 'enjoyinstagram_register_options_group_auth');

function enjoyinstagram_register_options_group()
{
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_client_id');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_instagram');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_id');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_username');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_profile_picture');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_fullname');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_website');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_user_bio');
		register_setting('enjoyinstagram_options_group', 'enjoyinstagram_access_token');
}

add_action ('admin_init', 'enjoyinstagram_register_options_group');

function enjoyinstagram_register_options_carousel()
{
		register_setting('enjoyinstagram_options_carousel_group', 'enjoyinstagram_carousel_items_number');
		register_setting('enjoyinstagram_options_carousel_group', 'enjoyinstagram_carousel_navigation');
		register_setting('enjoyinstagram_options_carousel_group', 'enjoyinstagram_grid_cols');
		register_setting('enjoyinstagram_options_carousel_group', 'enjoyinstagram_grid_rows');
		register_setting('enjoyinstagram_options_carousel_group', 'enjoyinstagram_hashtag');
		register_setting('enjoyinstagram_options_carousel_group', 'enjoyinstagram_user_or_hashtag');

}

add_action ('admin_init', 'enjoyinstagram_register_options_carousel');
 
function aggiungi_script_instafeed_owl() {
	
 if(!is_admin()) {
 
 	wp_register_script('owl', plugins_url('/js/owl.carousel.js',__FILE__),'jquery','');
	wp_register_script('swipebox', plugins_url('/js/jquery.swipebox.js',__FILE__),'jquery','');
    wp_register_script('gridrotator', plugins_url('/js/jquery.gridrotator.js',__FILE__),'jquery','');
    wp_register_script('modernizr.custom.26633', plugins_url('/js/modernizr.custom.26633.js',__FILE__),'jquery','');
	wp_register_script('orientationchange', plugins_url('/js/ios-orientationchange-fix.js',__FILE__),'jquery','');

	wp_register_style( 'owl_style', plugins_url('/css/owl.carousel.css',__FILE__) );
	wp_register_style( 'owl_style_2', plugins_url('/css/owl.theme.css',__FILE__) );
	wp_register_style( 'owl_style_3', plugins_url('/css/owl.transitions.css',__FILE__) );
	wp_register_style( 'swipebox_css', plugins_url('/css/swipebox.css',__FILE__) );
	wp_register_style( 'grid_fallback', plugins_url('/css/grid_fallback.css',__FILE__) );
	wp_register_style( 'grid_style', plugins_url('/css/grid_style.css',__FILE__) );
	
	wp_enqueue_script( 'jquery' ); // include jQuery
	wp_enqueue_script('owl');
	wp_enqueue_script('swipebox');
	wp_enqueue_script('modernizr.custom.26633');
	wp_enqueue_script('gridrotator');
	wp_enqueue_script('orientationchange');
	wp_enqueue_style( 'owl_style' );
	wp_enqueue_style( 'owl_style_2' );
	wp_enqueue_style( 'owl_style_3' );
	wp_enqueue_style( 'swipebox_css' );
	wp_enqueue_style( 'grid_fallback' );
	wp_enqueue_style( 'grid_style' );
 }
}
 
add_action( 'wp_enqueue_scripts', 'aggiungi_script_instafeed_owl' );

function aggiungi_script_in_admin(){
	wp_register_style( 'enjoyinstagram_settings', plugins_url('/css/enjoyinstagram_settings.css',__FILE__) );
	wp_enqueue_style( 'enjoyinstagram_settings' );
}

add_action( 'admin_enqueue_scripts', 'aggiungi_script_in_admin' );
add_action( 'admin_head', 'aggiungo_javascript_in_pannello_amministrazione' );

function aggiungo_javascript_in_pannello_amministrazione() {
 ?>
     <script type="text/javascript">
				
				
				function post_to_url(path, method) {
					method = method || "get";
					var params = new Array();  
					var client_id = document.getElementById('enjoyinstagram_client_id').value;
					var client_secret = document.getElementById('enjoyinstagram_client_secret').value;
					params['client_id'] = client_id;
					params['redirect_uri'] = '<?php echo admin_url('options-general.php?page=enjoyinstagram_plugin_options&tab=enjoyinstagram_general_settings'); ?>';
					params['scope'] = 'likes'; 
					params['response_type'] = 'code';
				
					var form = document.createElement("form");
					form.setAttribute("method", method);
					form.setAttribute("action", path);
				
					for(var key in params) {
						if(params.hasOwnProperty(key)) {
							var hiddenField = document.createElement("input");
							hiddenField.setAttribute("type", "hidden");
							hiddenField.setAttribute("name", key);
							hiddenField.setAttribute("value", params[key]);
				
							form.appendChild(hiddenField);
						 }
					}
					
					
						document.body.appendChild(form);
						form.submit();		
					
				}


		</script>
<?php
}

 

function funzioni_in_head() {
   ?>
   <script type="text/javascript">
		jQuery(function($) {
			$(".swipebox_grid").swipebox({
			hideBarsDelay : 0
			});
			
		});   

		jQuery(function(){
		  jQuery(document.body)
			  .on('click touchend','#swipebox-slider .current img', function(e){
				  jQuery('#swipebox-next').click();
				  return false;
			  })
			  .on('click touchend','#swipebox-slider .current', function(e){
				  jQuery('#swipebox-close').trigger('click');
			  });
		});
	
</script>
   <?php
}

 
add_action('wp_head', 'funzioni_in_head');

 
 
function enjoyinstagram_plugin_settings_link($links) { 
		  $settings_link = '<a href="options-general.php?page=enjoyinstagram_plugin_options">' . __( 'Settings' ) . '</a>'; 
		  $widgets_link = '<a href="widgets.php">' . __( 'Widgets' ) . '</a>';
		  $premium_link = '<a href="http://www.mediabeta.com/enjoy-instagram/">' . __( 'Premium Version' ) . '</a>';
		  array_push($links, $settings_link); 
		  array_push($links, $widgets_link); 
		  array_push($links, $premium_link); 
		  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'enjoyinstagram_plugin_settings_link');





add_action( 'admin_footer', 'add_option_client_ajax' );

function add_option_client_ajax() {
?>
<script type="text/javascript" >

jQuery('#button_autorizza_instagram').click(function() {
	var client_id = document.getElementById('enjoyinstagram_client_id').value;
	var client_secret = document.getElementById('enjoyinstagram_client_secret').value;
	var data = {
		action: 'user_option_ajax',
		client_id_value: client_id,
		client_secret_value: client_secret
	};
	

	jQuery.post(ajaxurl, data, function(response) {
		post_to_url('https://api.instagram.com/oauth/authorize/','get');
	});
});
</script>
<?php
}

add_action( 'wp_ajax_user_option_ajax', 'user_option_ajax_callback' );

function user_option_ajax_callback() {
	global $wpdb; 

	$client_id = $_POST['client_id_value'];
$client_secret = $_POST['client_secret_value'];
echo $client_id."<br />".$client_secret;
update_option( 'enjoyinstagram_client_id', $client_id );
update_option( 'enjoyinstagram_client_secret', $client_secret );

	die();
}


add_action( 'admin_footer', 'logout_client_ajax' );

function logout_client_ajax() {
?>
<script type="text/javascript" >

jQuery('#button_logout').click(function() {
	var data = {
		action: 'user_logout_ajax'
	};
	

	jQuery.post(ajaxurl, data, function(response) {
		location.href = '<?php echo get_admin_url(); ?>options-general.php?page=enjoyinstagram_plugin_options&tab=enjoyinstagram_general_settings';
	});
});
</script>
<?php
}

add_action( 'wp_ajax_user_logout_ajax', 'user_logout_ajax_callback' );

function user_logout_ajax_callback() {
	global $wpdb; 

	update_option('enjoyinstagram_user_id','');	
update_option('enjoyinstagram_user_username','');	
update_option('enjoyinstagram_user_profile_picture','');	
update_option('enjoyinstagram_user_fullname','');	
update_option('enjoyinstagram_user_website','');	
update_option('enjoyinstagram_user_bio','');	
update_option('enjoyinstagram_access_token','');

	die(); 
}


//require('custom_editor_button/shortcode_button.php');

include_once ('tinymce/tinymce.php');
require_once ('tinymce/ajax.php');
 
require_once('library/widgets.php');
require_once('library/widgets_grid.php');
require_once('library/enjoyinstagram_shortcode_grid.php');
require_once('library/enjoyinstagram_shortcode_widget.php');
require_once('library/enjoyinstagram_shortcode_grid_widget.php');

?>