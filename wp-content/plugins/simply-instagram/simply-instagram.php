<?php
/**
 * Plugin Name: Simply Instagram
 * Plugin URI: http://www.rollybueno.info/wp-simply-instagram/
 * Description: Promote your Instagram photo through your Wordpress website using Simply Instagram.
 * Version: 1.3.3
 * Author: Rolly G. Bueno Jr.
 * Author URI: http://www.rollybueno.info
 * Text Domain: simply-instagram
 * Domain Path: /languages
 * License: GPL v2.0.
 * Copyright 2012 Rolly G. Bueno Jr.
*/

DEFINE( "simply_instagram_plugin_path", plugin_dir_path(__FILE__)  );
DEFINE('simply_instagram_plugin_url', site_url() . '/wp-content/plugins/simply-instagram/');
require simply_instagram_plugin_path . 'simply-instagram-functions.php';
require simply_instagram_plugin_path . 'simply-instagram-widget.php';

class Simply_Instagram_Plugin {
	
	/*
	 * Plugin page option declaration keys
	*/
	private $general_settings_key = 'si_general_settings';
	private $shortcode_settings_key = 'si_shortcode_settings';
	private $prettyphoto_settings_key = 'si_prettyphoto_settings';
	private $csscontrol_settings_key = 'si_csscontrol_settings';
	private $about_settings_key = 'si_about_settings';
	private $plugin_options_key = 'simply-instagram';
	private $plugin_settings_tabs = array();
	
	
	function __construct() {
		
		/**
		 * The following initialisation and hooks are intended
		 * for plugin core settings. No SI involvement yet
		*/		
		add_action( 'init', array( &$this, 'load_settings' ) );	
		add_action( 'admin_init', array( &$this, 'register_general_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_shortcode_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_prettyphoto_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_csscontrol_settings' ) );
		add_action( 'admin_init', array( &$this, 'register_about_settings' ) );
		add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
		add_action( 'plugins_loaded', array( &$this, 'si_textdomain' ) );
				
		/**
		 * Here comes the Simpy Instagram hook for easier edit
		*/
		add_action( 'init', array( &$this, 'si_follower_cookie' ) );
		add_action('admin_notices', array( &$this, 'si_admin_notice' ));
		add_action('wp_enqueue_scripts', array( &$this, 'si_stylesheet' ), 0 );
		add_action('wp_enqueue_scripts', array( &$this, 'si_script' ), 0 );
		add_action('wp_head', array( &$this, 'si_head_IE' ));
		add_shortcode( 'simply_instagram', array( &$this, 'si_sc' ) );
	}	
	
	/**
	 * The following will be fired upon clicking the
	 * Activate Plugin button. Some option are dropped on
	 * version 1.2.6 but still retained for compatibility
	*/
	static function si_activate() {
            	/**
		 * v1.2.5 uses wp options
		 * Drop old table if exist
		*/
		global $wpdb;
		  
		$table = $wpdb->prefix . "instagram";
		$wpdb->query("DROP TABLE IF EXISTS $table");
		   
		update_option( 'si_general_settings', array( 'si_cache_option' => true, 'gen_cache_expire_option' => 180 ) );
		update_option( 'si_prettyphoto_settings', array( 'galleryTheme' => 'pp_default', 
									'autoPlay' => true,
									'ppPhotoDescription' => false,
									'animationSpeed' => 'normal',
									'overlayGallery' => true,
									'ppDisplayStatistic' => true,
									'ppDisplayPhotographer' => true ) );
		
		update_option( 'mediaViewer', 'builtInMediaViewer' );
		update_option( 'displayCommentMediaViewer', '5' );
		update_option( 'displayPhotographer', 'true' );
		update_option( 'displayComment', '5' );
		update_option( 'displayStatistic', 'true' );
		update_option( 'displayDescription', 'true' );
		update_option( 'galleryTheme', 'pp_default' );
		update_option( 'autoPlay', 'true' );
		update_option( 'ppPhotoDescription', 'true' );
		update_option( 'animationSpeed', 'normal' );
		update_option( 'overlayGallery', 'true' );
		update_option( 'ppDisplayPhotographer', 'true' );
		update_option( 'ppDisplayStatistic', 'true' );
		update_option( 'enableCandCom', 'no' );
		update_option( 'siCacheExpires', '60' );
		update_option( 'siCache', true );
     	}
     	
     	/**
	 * Need to remove all options when deactivation 
	 * for cleaning database
	*/
     	static function si_deactivate(){
     	
		global $wpdb;
		
	        $table = $wpdb->prefix."instagram";
	        
	        $wpdb->query("DROP TABLE IF EXISTS $table");
	        
	        delete_option( 'si_general_settings' );
	        delete_option( 'si_prettyphoto_settings' );
	        
	        delete_option( 'mediaViewer' );
		delete_option( 'displayCommentMediaViewer' );
		delete_option( 'displayPhotographer' );
		delete_option( 'displayComment' );
		delete_option( 'displayStatistic' );
		delete_option( 'displayDescription' );
		delete_option( 'galleryTheme' );
		delete_option( 'autoPlay' );
		delete_option( 'ppPhotoDescription' );
		delete_option( 'animationSpeed' );
		delete_option( 'overlayGallery' );
		delete_option( 'ppDisplayPhotographer' );
		delete_option( 'ppDisplayStatistic' );
		
		delete_option( 'si_access_token' );
		delete_option( 'si_user_id' );
		
		delete_option( 'enableCandCom' );
		delete_option( 'siCacheExpires' );		
		delete_option( 'siCache' );
		
		delete_option( 'si_css' );
	}
	
	/**
	 * Attempt for using cookie
	 * DEPRECIATED!
	*/
	function si_follower_cookie(){
		if( isset( $_GET['access_token'] ) ):
			setcookie( 'visitor_access_token', $_GET['access_token'], strtotime( " + 14 days" ) );
		endif; 
	}
	
	/**
	 * Simply Instagram localization init
	*/
	function si_textdomain(){
		load_plugin_textdomain( 'simply-instagram', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}
	
	/**
	  * Custom style for admin page
	  * It will be called only on your plugin admin page, enqueue our script here
	*/
	function simply_instagram_styles(){			
	     //   wp_enqueue_script( 'common' );
	     //   wp_enqueue_script( 'wp-lists' );
	     //   wp_enqueue_script( 'postbox' );
	}

	function simply_instagram_admin_head() {
	    $siteurl = get_option('siteurl');
	    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/simply-instagram-admin.css';
	    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
	}
	
	function si_admin_notice(){
		global $wpdb;
		
		/*
		 * Info query to check if database
		 * has record.
		*/
		$info = array( 'si_access_token' => get_option( 'si_access_token' ), 'si_user_id' => get_option( 'si_user_id' ) );
		
		if( !$info['si_access_token'] && !$info['si_user_id'] ):
		
		echo '<div class="error">';
	        _e('<p>Simply Instagram has not been setup correctly. Kindly <a href="options-general.php?page=simply-instagram">authorize</a> Simply Instagram. </p>', 'simply-instagram');
	    	echo '</div>';
	    	
	    	endif;
	}
	
	function si_stylesheet(){      
		wp_register_style( 'simplyInstagram', plugins_url('css/simply-instagram.css', __FILE__), '', '3' );
	        wp_enqueue_style( 'simplyInstagram' );
		
	        wp_register_style( 'prettyPhoto', plugins_url('css/simply-instagram-prettyPhoto.css', __FILE__), '', '3.1.6' );
	        wp_enqueue_style( 'prettyPhoto' );
	        
	        wp_register_style( 'tooltipster', plugins_url('css/tooltipster.css', __FILE__), '', '3.0.0'  );
	        wp_enqueue_style( 'tooltipster' );
	}
	
	function si_script(){
		/**
		 * Check if jquery included
		*/
		wp_enqueue_script( 'jquery' );
		
		/**
		 jQuery Tool tip
		*/
		wp_enqueue_script( 'jquery.tools.min-3.3.0.js', plugins_url( '/js/jquery.tooltipster.min.js',__FILE__ ), array(), '3.0.0' );
		
	  	/**
		 * jquery prettyphoto js
		 * v 3.1.4
		*/
	    	wp_enqueue_script( 'jquery.prettyPhoto', plugins_url( '/js/simply-instagram-jquery.prettyPhoto.js',__FILE__ ), array(), '3.1.6' );    	
	}
	
	function si_head_IE(){
		?>
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		
		<!-- BEGIN SimplyInstagram IE -->
		<!-- [if IE 9]>
		<style type="text/css">		
		.comment-profile{margin:2px;width:45px;float:left}
		.comment-profile img{vertical-align:top}
		.comment-holder{top:0;width:200px;float:left!important}
		.comments-holder{width:210px;float:left}
		</style>
		<![endif]-->
		<!-- END SimplyInstagram IE -->
		
		<?php
		$ccc = get_option( $this->csscontrol_settings_key );
		if( !empty( $ccc['ccc_option'] ) ):
		?>
			<!-- Start of Simply Instagram Custom CSS -->
			<!-- Developed by Rolly G. Bueno Jr. - http://www.rollybueno.info -->
			<style type="text/css">
			<?php
				echo $ccc['ccc_option'];
			?>		
			</style>
			<!-- End of Simply Instagram Custom CSS -->
		<?php
		endif;
		
	}
	
	function si_sc( $atts ){
		/**
		 * shortcode standard:
		 * [simply_instagram endpoints="" type="" display="" ]
		 * Endpoints:
		 	users:
		 	 *type
		 		self-feed
		 		recent-media
		 		likes
		 	media
		 	 *type
		 		popular
		*/
		ob_start();		
		//print_r( $atts );
		extract( shortcode_atts( array(
				'endpoints' => 'users',
				'type' => 'self-feed',
				'size' => 'low_resolution', 	// * * size - photo size on slideshow: Choices: thumbnail, low_resolution, standard_resolution. Default: low_resolution
				'presentation' => 'polaroid', 	//* * presentation - option to use masonry or polaroid in image presentation. Default: polaroid.
				'display' => 20, 		// * * display - number of initial photo to be display. Default: 20
				'displayoption' => 'instagram', // * * displayoption - choose either by using prettyphoto slideshow, single image or open directly in Instagram. Default: Instagram.
				'showphotographer' => 'true', 	// * * showphotographer - display username of photo owner. Default: true
				'photocomment' => 0, 		// * * photocomment - comments to be display. 0 to hide, maximum of 5. Default 0
				'stat' => true, 		// * * stat - display comment and like stat total. Default: true
				'photocaption' => true ,		// * * photocaption - display photo caption. Might affect image height. Default: true
				'displaycomment' => true, 	// * * displaycomment - option to display comment in masonry. Might affect image height. Default: true
				
				'width' => '150', 		// * * width - image width. Default: 150
				'customRel' => 'sIntWid', 	// * * customRel - custom rel for prettyphoto 	
				 ), $atts ) );
		//print_r( $atts );
		switch( $atts['endpoints'] ):
			
			/**
			 * Users endpoints
			*/
			case'users':
				/**
				 * endpoints type:
				 * self-feed
				 * recent media
				 * likes
				*/
				switch( $atts['type'] ):
					/**
					 * Self-feed
					 * Display all photos uploaded by user
					 * with user's following
					*/
					case'self-feed':				
						if( $atts['presentation'] === "polaroid" ){
							echo sInstDisplayData( sInstGetSelfFeed( access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], '', $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );
						}else{
							echo '<div id="masonryContainer" class="clearfix masonry">';
							echo sInstDisplayData( sInstGetSelfFeed( access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], "150", $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );
							echo '</div>';	
						}												
					break;
					/**
					 * Recent Media
					 * Display all photos uploaded by user only
					*/
					case'recent-media':
						if( $atts['presentation'] === "polaroid" ){
							echo sInstDisplayData( sInstGetRecentMedia( user_id(), access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], $atts['width'], $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );
						}else{
							echo '<div id="masonryContainer" class="clearfix masonry">';
							echo sInstDisplayData( sInstGetRecentMedia( user_id(), access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], "150", $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );
							echo '</div>';	
						}
					break;
					/**
					 * Likes
					 * Display all photos liked by user
					*/
					case'likes':
						if( $atts['presentation'] === "polaroid" ){
							echo sInstDisplayData( sInstGetLikes( access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], '', $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );	
						}else{
							echo '<div id="masonryContainer" class="clearfix masonry">';
							echo sInstDisplayData( sInstGetLikes( access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], "150", $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );	
							echo '</div>';		
						}
					break;
					
				endswitch;
				
			break;
			/**
			 * Media endpoints
			*/
			case'media':
				/**
				 * Media endpoint only accept currently
				 * trending photos in Instagram
				*/
				if( $atts['presentation'] === "polaroid" ){
					echo sInstDisplayData( sInstGetMostPopular( $atts['type'], access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], '', $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );
				}else{			
					echo '<div id="masonryContainer" class="clearfix masonry">';
					echo sInstDisplayData( sInstGetMostPopular( $atts['type'], access_token() ), $atts['presentation'], $atts['displayoption'], $atts['size'], $atts['display'], '', $customRel, $atts['showphotographer'], $atts['photocomment'], $atts['stat'], $atts['photocaption'], $atts['displaycomment'] );
					echo '</div>';		
				}
			break;
			
		endswitch;
		/**
		 * PrettyPhoto settings for shortcode.
		 * v1.2.3 is using masonry as alternative
		 * to PrettyPhoto which often breaks or
		 * unresponsive when the image has
		 * long description
		*/
		$prettyPhoto = get_option( 'si_prettyphoto_settings' );
		//print_r( $prettyPhoto );
		if( $atts['presentation'] === "masonry" ){
			?>
			<script src="<?php echo plugins_url(); ?>/simply-instagram/js/jquery.masonry.min.js"></script>
			
			<script>
			<?php // if( $this->general_settings['gen_enable_tooltip'] ){  ?>			
				//jQuery('.si-tooltip').tooltipster();
			<?php // } ?>
		jQuery('#masonryContainer').imagesLoaded(function(){jQuery('#masonryContainer').masonry({itemSelector : '.masonryItem',isAnimated: true});});jQuery("a#content_close").live( "click", function() {jQuery("#overlay").fadeOut("slow", function() { jQuery("#overlay").remove(); });jQuery("#contentWrap").fadeOut("slow", function() { jQuery("#contentWrap").remove(); });jQuery("#content_close").fadeOut("slow", function() { jQuery("#content_close").remove(); });});jQuery("#overlay").live( "click", function() {Query(this).fadeOut("slow", function() { jQuery(this).remove(); });jQuery("#contentWrap").fadeOut("slow", function() { jQuery("#contentWrap").remove(); });jQuery("#content_close").fadeOut("slow", function() { jQuery("#content_close").remove(); });});jQuery(function() {jQuery("a.overlay").click(function() {var docHeight = jQuery(document).height();jQuery("body").append("<div id='overlay'></div><a id='content_close' href='#'></a><div id='contentWrap'></div>");	jQuery("#overlay").height(docHeight).css({'opacity' : 0.4,'position': 'fixed','top': 0,'left': 0,'background-color': 'black','width': '100%','z-index': 99999});jQuery("#contentWrap").load( jQuery(this).attr('rel') );jQuery("#image-holder").css({'width': '612px','height': '612px'});});jQuery("div.item-holder").hover(function() { var id = jQuery(this).data("id");jQuery("div.hover-action[data-id=" + id + "]").show("slow");},function() {jQuery("div.hover-action").hide("slow");});});
	jQuery(document).ready(function(){
	jQuery("a[rel^='sIntSC']").prettyPhoto({
		autoplay_slideshow: <?php echo $prettyPhoto['autoPlay'] ;?>,
		social_tools: false,
		theme: '<?php echo $prettyPhoto["galleryTheme"] ;?>',
		animation_speed: '<?php echo $prettyPhoto["animationSpeed"] ;?>',		
	});	
	});
	
	jQuery(document).ready(function(){
		jQuery("a[rel^='prettyphoto']").prettyPhoto({
			autoplay_slideshow: false,
			social_tools: false,
			theme: '<?php echo $prettyPhoto["galleryTheme"] ;?>'
		});
	});
			</script>
			<?php	
		}else{
		?>
			 <script>
	        jQuery(document).ready(function() {
	            <?php //if( $this->general_settings['gen_enable_tooltip'] === "true" ){ echo( $this->general_settings['gen_enable_tooltip'] ); ?>
				jQuery('.si-tooltip').tooltipster();
			<?php //} ?>
	            jQuery(document).ready(function(){jQuery("a[rel^='sIntSC']").prettyPhoto({autoplay_slideshow: <?php echo $prettyPhoto['autoPlay'] ;?>,social_tools: false,theme: '<?php echo $prettyPhoto["galleryTheme"] ;?>',animation_speed: '<?php echo $prettyPhoto["animationSpeed"] ;?>',});});jQuery(document).ready(function(){jQuery("a[rel^='prettyphoto']").prettyPhoto({autoplay_slideshow: false,social_tools: false,theme: '<?php echo $prettyPhoto["galleryTheme"] ;?>'});});
	        });
	    </script>
		<?php
		}
		$content = ob_get_contents();ob_end_clean();wp_reset_postdata();return $content;	
	}
	
	function load_settings() {
		$this->general_settings = (array) get_option( $this->general_settings_key );
		$this->shortcode_settings = (array) get_option( $this->shortcode_settings_key );
		$this->prettyphoto_settings = (array) get_option( $this->prettyphoto_settings_key );
		$this->csscontrol_settings = (array) get_option( $this->csscontrol_settings_key );
		$this->about_settings = (array) get_option( $this->about_settings_key );
		
		$this->general_settings = array_merge( array(
			'general_option' => 'General value'
		), $this->general_settings );
		
		
		$this->shortcode_settings = array_merge( array(
			'general_option' => 'Shortcode value'
		), $this->shortcode_settings );
		
		
		$this->prettyphoto_settings = array_merge( array(
			'prettyPhoto_option' => 'pPrettyPhoto value'
		), $this->prettyphoto_settings );
		
		$this->csscontrol_settings = array_merge( array(
			'csscontrol_option' => 'CSS Control value'
		), $this->csscontrol_settings );
		
		$this->about_settings = array_merge( array(
			'aboutl_option' => 'About Plugin value'
		), $this->about_settings );
	}
	
	/**
	 * Start of General Settings tab.
	*/
		function register_general_settings() {	
			$this->plugin_settings_tabs[$this->general_settings_key] = __('General', 'simply-instagram');
			
			register_setting( $this->general_settings_key, $this->general_settings_key );			
			add_settings_section( 'section_general', __('Cache Settings', 'simply-instagram'), array( &$this, 'general_section_desc' ) , $this->general_settings_key );
			add_settings_field( 'si_enable_cache', __('Enable Cache', 'simply-instagram'), array( &$this, 'gen_enable_cache_option' ), $this->general_settings_key, 'section_general', array( 'label_for' => 'si_enable_cache' ) );
			add_settings_field( 'si_cache_expire', __('Cache Expire', 'simply-instagram'), array( &$this, 'gen_cache_expire_option' ), $this->general_settings_key, 'section_general', array( 'label_for' => 'si_cache_expire' ) );
			//add_settings_field( 'si_tooltip', __('Enable Tooltip', 'simply-instagram'), array( &$this, 'gen_enable_tooltip' ), $this->general_settings_key, 'section_general', array( 'label_for' => 'si_tolltip' ) );
		}
		
		function general_section_desc() { _e('Option to use caching or not. Simply Instagram only cache API data in order to save limit call on Instagram to avoid call excessive penalty( Instagram might return ERROR 400 ). Useful if you have large site traffic.', 'simply-instagram'); }
		
		function gen_enable_cache_option() {
			?>
			<select name="<?php echo $this->general_settings_key; ?>[si_cache_option]"> 		
				<option value="true" <?php echo selected( esc_attr( $this->general_settings['si_cache_option'] ), 'true' ); ?>/><?php _e('Yes', 'simply-instagram'); ?></option>
				<option value="false" <?php echo selected( esc_attr( $this->general_settings['si_cache_option'] ), 'false' ); ?>/><?php _e('No', 'simply-instagram'); ?></option>
			</select>
			<?php
		}	
		
		function gen_cache_expire_option() {
			?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[gen_cache_expire_option]" value="<?php echo esc_attr( $this->general_settings['gen_cache_expire_option'] ); ?>" />
			<br/>
			<span class="tips"><?php _e('Choose when API will recreate new cache in seconds.', 'simply-instagram'); ?></span>
			<?php
		}	
		
		function gen_enable_tooltip() {
			?>
			<select name="<?php echo $this->general_settings_key; ?>[gen_enable_tooltip]"> 		
				<option value="true" <?php echo selected( esc_attr( $this->general_settings['gen_enable_tooltip'] ), 'true' ); ?>/><?php _e('Yes', 'simply-instagram'); ?></option>
				<option value="false" <?php echo selected( esc_attr( $this->general_settings['gen_enable_tooltip'] ), 'false' ); ?>/><?php _e('No', 'simply-instagram'); ?></option>
			</select>
			<br/>
			<span class="tips"><?php _e('Choose to display description when hovering.', 'simply-instagram'); ?></span>
			<?php
		}	
	
	/**
	 * End of General Settings tab.
	*/
	
	/**
	 * Start of Shortcode Settings tab.
	*/	
		function register_shortcode_settings() {	
			$this->plugin_settings_tabs[$this->shortcode_settings_key] = __('Shortcode', 'simply-instagram');
					
			register_setting( $this->shortcode_settings_key, $this->shortcode_settings_key );
			add_settings_section( 'section_shortcode', __('Short Code Settings', 'simply-instagram'), array( &$this, 'sc_section_desc' ), $this->shortcode_settings_key );
			add_settings_field( 'endpoints', __('Endpoints', 'simply-instagram'), array( &$this, 'sc_endpoints_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'type', __('Type', 'simply-instagram'), array( &$this, 'sc_type_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'size', __('Size', 'simply-instagram'), array( &$this, 'sc_size_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'display', __('Display', 'simply-instagram'), array( &$this, 'sc_display_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'presentation', __('Presentation', 'simply-instagram'), array( &$this, 'sc_presentation_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'displayoption', __('Display Option', 'simply-instagram'), array( &$this, 'sc_displayoption_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'showphotographer', __('Show Photographer', 'simply-instagram'), array( &$this, 'sc_show_photographer_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'photocomment', __('Photo Comment', 'simply-instagram'), array( &$this, 'sc_photo_comment_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'stat', __('Stat', 'simply-instagram'), array( &$this, 'sc_stat_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'photocaption', __('Photo Caption', 'simply-instagram'), array( &$this, 'sc_photo_caption_option' ), $this->shortcode_settings_key, 'section_shortcode' );
			add_settings_field( 'displaycomment', __('Display Comment', 'simply-instagram'), array( &$this, 'sc_displaycomment_option' ), $this->shortcode_settings_key, 'section_shortcode' );	
			add_settings_section( 'shortcode_result', '', array( &$this, 'sc_result' ), $this->shortcode_settings_key );
		}
		
		function sc_section_desc() { _e('Use this tool to generate shortcode. Copy and paste the highlighted paragraph below to your post or page.', 'simply-instagram'); }
		
		/**
 		 * Endpoints
 		*/
		function sc_endpoints_option() {
			?>
			<select name="endpoints"> 		
				<option value="users" /><?php _e('Users', 'simply-instagram'); ?></option>
				<option value="media" /><?php _e('Media', 'simply-instagram'); ?></option>
			</select>
			<?php
		}	
		
		/**
 		 * Type
 		*/
		function sc_type_option() {
			?>
			<select name="type" class="sc-generator">	
				 <option value ="self-feed" ><?php _e('Self Feed', 'simply-instagram'); ?></option>
				 <option value ="recent-media" ><?php _e('Recent Media', 'simply-instagram'); ?></option>
				 <option value ="likes" ><?php _e('Likes', 'simply-instagram'); ?></option>
			</select>
			<?php
		}	
		
		/**
 		 * Size - photo size on slideshow: Choices: thumbnail, 
 		 * low_resolution, standard_resolution. 
 		 * Default: low_resolution
 		*/	
		function sc_size_option() {
			?>
			<select name="size" class="sc-generator">	
				 <option value ="thumbnail" ><?php _e('Thumbnail', 'simply-instagram'); ?></option>
				 <option value ="low_resolution" ><?php _e('Low Resolution', 'simply-instagram'); ?></option>
				 <option value ="standard_resolution" ><?php _e('Standard Resolution', 'simply-instagram'); ?></option>
			</select>
			<?php
		}	
		
		/**
 		 * Display - number of initial photo to be display. 
 		 * Default: 20
 		*/		
		function sc_display_option() {
			?>
			<select name="totalphoto" class="sc-generator">	
				<?php for( $i=1; $i<=20; $i++ ): ?>	
				 <option value ="<?php echo $i; ?>" ><?php echo $i; ?></option>
				<?php endfor; ?>
			</select>
			<?php
		}
		
		/**
 		 * Presentation - option to use masonry or polaroid in image presentation. 
 		 * Default: polaroid.
 		*/	
		function sc_presentation_option() {
			?>
			<select name="presentation" class="sc-generator">	
				<option value ="polaroid" ><?php _e('Polaroid', 'simply-instagram'); ?></option>
				<option value ="masonry" ><?php _e('Masonry', 'simply-instagram'); ?></option>
			</select>
			<?php
		}
		
		/**
 		 * DisplayOption - choose either by using prettyphoto slideshow, 
 		 * single image or open directly in Instagram. 
 		 * Default: Instagram.
 		*/		
		function sc_displayoption_option() {
			?>
			<select name="displayoption" class="sc-generator">	
				<option value ="instagram" ><?php _e('Instagram', 'simply-instagram'); ?></option>
				<option value ="prettyPhoto" ><?php _e('prettyPhoto', 'simply-instagram'); ?></option>
				<?php /* Dropped on 1.3.2 <option value ="single" ><?php _e('Single Viewer', 'simply-instagram'); ?></option> */ ?>
			</select>
			<?php
		}
		
		/**
 		 * Showphotographer - display username of photo owner. 
 		 * Default: true
 		*/		
		function sc_show_photographer_option() {
			?>
			<select name="showphotographer" class="sc-generator">	
				<option value ="true" ><?php _e('Yes', 'simply-instagram'); ?></option>
				<option value ="false" ><?php _e('No', 'simply-instagram'); ?></option>
			</select>
			<?php
		}
		
		/**
 		 * Photocomment - comments to be display. 0 to hide, maximum of 5. 
 		 * Default 0
 		*/		
		function sc_photo_comment_option() {
					?>
					<select name="photocomment" class="sc-generator">	
				<?php for( $i=1; $i<=5; $i++ ): ?>	
				 <option value ="<?php echo $i; ?>" ><?php echo $i; ?></option>
				<?php endfor; ?>
			</select>
					<?php
				}		
		
		/**
 		 * Stat - display comment and like stat total. 
 		 * Default: true.
 		*/	
		function sc_stat_option() {
					?>
					<select name="stat" class="sc-generator">	
				<option value ="true" ><?php _e('Yes', 'simply-instagram'); ?></option>
				<option value ="false" ><?php _e('No', 'simply-instagram'); ?></option>
			</select>
					<?php
				}			
				
		/**
 		 * Photocaption - display photo caption. Might affect image height. 
 		 * Default: true
 		*/	
		function sc_photo_caption_option() {
					?>
					<select name="photocaption" class="sc-generator">	
				<option value ="true" ><?php _e('Yes', 'simply-instagram'); ?></option>
				<option value ="false" ><?php _e('No', 'simply-instagram'); ?></option>
			</select>
					<?php
				}	
				
		/**
 		 * displaycomment - option to display photo comment. 
 		 * Default: true.
 		*/	
 		function sc_displaycomment_option() {
			?>
					<select name="displaycomment" class="sc-generator">	
				<option value ="true" ><?php _e('Yes', 'simply-instagram'); ?></option>
				<option value ="false" ><?php _e('No', 'simply-instagram'); ?></option>
				</select>
			<?php
		}		
		
		/**
 		 * Shortcode result displaying in long
 		 * red paragraph. Copy paste shortcode and
 		 * it should work perfectly.
 		*/	
		function sc_result(){ 
			?>
				<p id="generated-sc"></p>
			<?php
		}
	/**
	 * End of Shortcode Settings tab.
	*/	
	
	/**
	 * Start of prettyPhoto Settings tab.
	*/	
		function register_prettyphoto_settings() {
			$this->plugin_settings_tabs[$this->prettyphoto_settings_key] = __('prettyPhoto', 'simply-instagram');
			
			register_setting( $this->prettyphoto_settings_key, $this->prettyphoto_settings_key );
			add_settings_section( 'section_prettyphoto', __('prettyPhoto Settings', 'simply-instagram'), array( &$this, 'prettyPhot_desc' ), $this->prettyphoto_settings_key );
			add_settings_field( 'galleryTheme', __('Gallery Theme', 'simply-instagram'), array( &$this, 'gallery_theme_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );			
			add_settings_field( 'autoPlay', __('Slideshow Auto Play', 'simply-instagram'), array( &$this, 'slideshow_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );
			add_settings_field( 'ppPhotoDescription', __('Display Description', 'simply-instagram'), array( &$this, 'pp_photodescription_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );
			add_settings_field( 'animationSpeed', __('Animation Speed', 'simply-instagram'), array( &$this, 'pp_animationspeed_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );
			//add_settings_field( 'overlayGallery', 'Overlay Gallery', array( &$this, 'pp_overlaygallery_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );
			add_settings_field( 'ppDisplayStatistic', __('Display Statistic', 'simply-instagram'), array( &$this, 'pp_displastatistic_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );
			add_settings_field( 'ppDisplayPhotographer', __('Display photographer profile picture', 'simply-instagram'), array( &$this, 'pp_profilepicture_option' ), $this->prettyphoto_settings_key, 'section_prettyphoto' );
		}	
		
		function prettyPhot_desc() { _e('This prettyPhoto settings is for shortcode implementation only. Widget has different settings under each boxes.', 'simply-instagram'); }
		
		/**
		 * Gallery theme - option to choose prettyPhoto theme
		 * Default: pp_default
		*/
		function gallery_theme_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[galleryTheme]"> 		
				<option value="pp_default" <?php echo selected( esc_attr( $this->prettyphoto_settings['galleryTheme'] ), "pp_default" ); ?>><?php _e('Default', 'simply-instagram'); ?></option>
			 	<option value="facebook" <?php echo selected( esc_attr( $this->prettyphoto_settings['galleryTheme'] ), "facebook" ); ?>><?php _e('Facebook', 'simply-instagram'); ?></option>
			 	<option value="dark_rounded" <?php echo selected( esc_attr( $this->prettyphoto_settings['galleryTheme'] ), "dark_rounded" ); ?>><?php _e('Dark Round', 'simply-instagram'); ?></option>
				<option value="dark_square" <?php echo selected( esc_attr( $this->prettyphoto_settings['galleryTheme'] ), "dark_square" ); ?>><?php _e('Dark Square', 'simply-instagram'); ?></option>
				<option value="light_rounded" <?php echo selected( esc_attr( $this->prettyphoto_settings['galleryTheme'] ), "light_rounded" ); ?>><?php _e('Light Round', 'simply-instagram'); ?></option>
				<option value="light_square" <?php echo selected( esc_attr( $this->prettyphoto_settings['galleryTheme'] ), "light_square" ); ?>><?php _e('Light Square', 'simply-instagram'); ?></option>
			</select>
			<br />
			<span class="tips"><?php _e('Choose prettyPhoto gallery theme. You can compare each themes <a href="http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/" target="_blank">here</a> under Theme Support at the bottom of the page.', 'simply-instagram'); ?></span>
			<?php
		}	
		
		/**
		 * Slideshow - option to auto play prettyPhoto slideshow
		 * Deault: true
		*/
		function slideshow_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[autoPlay]"> 		
				<option value="true" <?php echo selected( esc_attr( $this->prettyphoto_settings['autoPlay'] ), "true" ); ?>><?php _e('Yes', 'simply-instagram'); ?></option>
			 	<option value="false" <?php echo selected( esc_attr( $this->prettyphoto_settings['autoPlay'] ), "false" ); ?>><?php _e('No', 'simply-instagram'); ?></option>
			</select>
			<br />
			<span class="tips"><?php _e('Set slideshow autoplay on/off.', 'simply-instagram'); ?></span>
			<?php
		}	
		
		/**
		 * Photo Description - option to include photo description
		 * in prettyPhoto slideshow. Slideshow might unresponsive if
		 * description is long.
		 * Default: false
		*/
		function pp_photodescription_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[ppPhotoDescription]">	
			 	<option value ="true" <?php selected( esc_attr( $this->prettyphoto_settings['ppPhotoDescription'] ), "true" ); ?>><?php _e('Yes', 'simply-instagram'); ?></option>
			 	<option value ="false" <?php selected( esc_attr( $this->prettyphoto_settings['ppPhotoDescription'] ), "false" ); ?>><?php _e('No', 'simply-instagram'); ?></option>
		     	</select>
			 <br />
		     	<span class="tips"><?php _e('prettyPhoto sometimes unresponsive on long photo description and this is the major drawback in previous version of Simply Instagram. Turn this feature off when it does.', 'simply-instagram'); ?></span>
		    
			<?php
		}	
		
		/**
		 * Animation Speed - option to set animation speed
		 * Default: normal
		*/
		function pp_animationspeed_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[animationSpeed]">	
				 <option value ="slow" <?php selected( esc_attr( $this->prettyphoto_settings['animationSpeed'] ), "slow" ); ?>><?php _e('Slow', 'simply-instagram'); ?></option>
				 <option value ="normal" <?php selected( esc_attr( $this->prettyphoto_settings['animationSpeed'] ), "normal" ); ?>><?php _e('Normal', 'simply-instagram'); ?></option>
				 <option value ="fast" <?php selected( esc_attr( $this->prettyphoto_settings['animationSpeed'] ), "fast" ); ?>><?php _e('Fast', 'simply-instagram'); ?></option>
		     	</select>
			 <br />
		     	<span class="tips"><?php _e('Choose animation speed. Default normal.', 'simply-instagram'); ?></span>
			<?php
		}	
		
		/**
		 * If set to true, a gallery will overlay the fullscreen image on mouse over.
		*/
		function pp_overlaygallery_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[overlayGallery]">	
				 <option value ="true" <?php selected( esc_attr( $this->prettyphoto_settings['overlayGallery'] ), "true" ); ?>><?php _e('True', 'simply-instagram'); ?></option>
				 <option value ="false" <?php selected( esc_attr( $this->prettyphoto_settings['overlayGallery'] ), "false" ); ?>><?php _e('False', 'simply-instagram'); ?></option>
		     	</select>
		     	<br />
		    	<span class="tips"><?php _e('If set to true, a gaullscreen image on mouse over.', 'simply-instagram'); ?></span>
			<?php
		}
		
		/**
		 * Display Stat - option to display Instagram total likes and comments
		 * Default: true
		*/
		function pp_displastatistic_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[ppDisplayStatistic]">	
			 	<option value ="true" <?php selected( esc_attr( $this->prettyphoto_settings['ppDisplayStatistic'] ), "true" ); ?>><?php _e('True', 'simply-instagram'); ?></option>
			 	<option value ="false" <?php selected( esc_attr( $this->prettyphoto_settings['ppDisplayStatistic'] ), "false" ); ?>><?php _e('False', 'simply-instagram'); ?></option>
		     	</select>
		     	<br />
		     	<span class="tips"><?php _e('Display likes and comments count when slideshow starts.', 'simply-instagram'); ?></span>
			<?php
		}
		
		/**
		 * Profile Picture - option to display photographer profile picture
		 * Default: true
		*/
		function pp_profilepicture_option() {
			?>
			<select name="<?php echo $this->prettyphoto_settings_key; ?>[ppDisplayPhotographer]">	
			 <option value ="true" <?php selected( esc_attr( $this->prettyphoto_settings['ppDisplayPhotographer'] ), "true" ); ?>><?php _e('True', 'simply-instagram'); ?></option>
			 <option value ="false" <?php selected( esc_attr( $this->prettyphoto_settings['ppDisplayPhotographer'] ), "false" ); ?>><?php _e('False', 'simply-instagram'); ?></option>
		     </select>
		     <br />
		     <span class="tips"><?php _e('Show photographer profile image when slideshow starts.', 'simply-instagram'); ?></span>
			<?php
		}
	/**
	 * End of prettyPhoto Settings tab.
	*/	
	
	/**
	 * CSS Control Settings tab.
	*/	
		function register_csscontrol_settings() {
			$this->plugin_settings_tabs[$this->csscontrol_settings_key] = __('CSS', 'simply-instagram');
			
			register_setting( $this->csscontrol_settings_key, $this->csscontrol_settings_key );
			add_settings_section( 'section_ccc', __('CSS Control Settings', 'simply-instagram'), array( &$this, 'ccc_desc' ), $this->csscontrol_settings_key );
			add_settings_field( 'ccc_option', __('CSS', 'simply-instagram'), array( &$this, 'ccc_option' ), $this->csscontrol_settings_key, 'section_ccc' );
			add_settings_field( 'ccc_dummy_option', __('CSS', 'simply-instagram'), array( &$this, 'ccc_dummy_option' ), $this->csscontrol_settings_key, 'section_ccc' );
		}
		
		function ccc_desc() { _e('If you want to personalize the CSS of this plugin, please use the box below.', 'simply-instagram'); }
				
		function ccc_option() {
			/** Bug fix on 1.3.1 of non appearance of CSS values in textbox */
			$css  = get_option( $this->csscontrol_settings_key );			
			?>
			<textarea name="<?php echo $this->csscontrol_settings_key; ?>[ccc_option]" style="width:100%;height:250px;"><?php echo $css['ccc_option']; ?></textarea>
			<br />
		     	<span class="tips"><?php _e('Refer the default CSS below for standard Simply Instagram classes and ids.', 'simply-instagram'); ?> </span>
			<?php
		}	
		
		function ccc_dummy_option() {
			?>
			<textarea readonly="readonly" style="width:100%;height:250px;"><?php echo file_get_contents( simply_instagram_plugin_path . '/css/simply-instagram.css' );?></textarea>
			<?php
		}	
	/**
	 * End of CSS Control Settings tab.
	*/	
	
	/**
	 * About Settings tab.
	*/	
		function register_about_settings() {
			$this->plugin_settings_tabs[$this->about_settings_key] = __('About/Debug', 'simply-instagram');			
			register_setting( $this->about_settings_key, $this->about_settings_key );
			add_settings_section( 'section_si_about', __('About Simply Instagram Wordpress Plugin', 'simply-instagram'), array( &$this, 'about_desc' ), $this->about_settings_key );
		}		
				
		function about_desc() { echo sprintf( 
						__('<p>%s<a href="http://www.rollybueno.info/contact/" target="_blank" >%s</a>%s</strong></p><p style="text-align: left !important;">%s<i><b>%s</b></i></p>
								<p style="text-align: left !important;">User ID: <i><b>%s</b></i></p>', 'simply-instagram'), 
							__('Developed by Rolly G. Bueno Jr. If you found a bug or some of Simply Instagram functions are not working, please contact me', 'simply-instagram'),
							__(' here ', 'simply-instagram'),
							__(' and include the information below together your website address:', 'simply-instagram'),
							__('Access Token: ', 'simply-instagram'),
		 					get_option( 'si_access_token') , 
		 					get_option( 'si_user_id' ) 
		 				); }
		 
		 
			
	/**
	 * End of About Settings tab.
	*/	
	
	function add_admin_menus() {
		$page = add_options_page('Simply Instagram', 'Simply Instagram', 'manage_options', $this->plugin_options_key, array( &$this, 'option_page_simply_instagram' ) );
		add_action('admin_print_styles-' . $page, array( $this, 'simply_instagram_styles' ));	 
		add_action( 'admin_head-' . $page, array( $this, 'simply_instagram_admin_head' ) );	
	}
	
	function option_page_simply_instagram() {
		
		if( isset( $_GET['tab'] ) ){
			$_GET['tab'];
		}else{ 	
			//wp_redirect( 'options-general.php?page=' . $this->plugin_options_key . '&tab=' . $this->si_general_settings . '&noheader=true' );
			//exit;
		}
		/**
		 * Save info to database
		 * v1.2.5 uses wp options	 
		*/
		if( isset( $_GET['access_token'] ) && isset( $_GET['id'] ) ):
			//$wpdb->insert( $wpdb->prefix . "instagram", array( 'access_token' => $_GET['access_token'], 'user_id' => $_GET['id'] ) );
			update_option( 'si_access_token', $_GET['access_token'] );
			update_option( 'si_user_id', $_GET['id'] );
		endif;
		
		/*
		 * Info query to check if database
		 * has record.
		*/
		//$info = $wpdb->get_results("select * from " . $wpdb->prefix . "instagram");
		$info = array( 'si_access_token' => get_option( 'si_access_token' ), 'si_user_id' => get_option( 'si_user_id' ) );
		
		if( isset( $_POST['sIntLogout'] ) == "log_out" ):
			/** 
			 * v.1.2.6 Delete from wp options
			*/
			
			//$wpdb->query("delete from " . $wpdb->prefix . "instagram");
			delete_option( 'si_access_token' );
			delete_option( 'si_user_id' );
			
			?> <meta http-equiv="refresh" content="0;url=<?php echo get_admin_url() . 'options-general.php?page=simply-instagram'; ?>"> <?Php
		endif;
			
		?>
		<div class="wrap">
		 	<h2><?php _e('Simply Instagram Settings', 'simply-instagram'); ?></h2>			 	
		 	
		 	<?php if( !$info['si_access_token'] && !$info['si_user_id'] ){ ?> 
				<?php if( ( !empty( $_GET['access_token'] ) && $_GET['access_token'] == "" ) && ( !empty( $_GET['id'] ) && $_GET['id'] == "" ) ): ?>
					<div class="error">
					 <p><?php _e('You did not authorize Simply Instagram. This plugin will not work without your authorization.', 'simply-instagram'); ?> </p>
					</div>
				<?php endif; ?>
				<a href=" <?php echo sInstLogin( '?return_uri=' . base64_encode( get_admin_url() . 'options-general.php?page=simply-instagram'  )  ) ?> "><img src="<?php echo plugins_url() . '/simply-instagram/images/instagram-login.jpg'; ?>" alt="<?php _e('Login to Instagram and authorize Simply Instagram plugin', 'simply-instagram'); ?>" /></a>
			<?php }else{ ?>
				<?php 
					if( isset( $_GET['access_token'] ) && $_GET['id'] ):
					?> <meta http-equiv="refresh" content="0;url=<?php echo get_admin_url() . 'options-general.php?page=simply-instagram'; ?>"> <?Php
					endif;
				?>
				
				<iframe src="https://instagram.com/accounts/logout/" width="0" height="0">Logout</iframe>
				<?php
					$user_info = sInstGetInfo( user_id(), access_token() );
					//print_r( $user_info );
				?>
				<div id="sInts-welcome">
				<img src="<?php echo $user_info['data']['profile_picture']; ?>" id="si_profile_photo"/>
				<p id="si_username"><?php _e('Welcome', 'simply-instagram'); ?> <?php echo $user_info['data']['full_name']; ?>!</p>
				
				
				<form name="itw_logout" method="post" action="<?php echo str_replace( '%7E', '~', htmlentities( get_admin_url() . 'options-general.php?page=simply-instagram'  )  ); ?>">
				<input type="hidden" name="sIntLogout" value="log_out">
				<input type="submit" class="button" value="Log out" name="logout" onclick="" >
				</form>
				
				</div> 
			
			<?php }; ?>	
			
			<?php 
				/**
				 *
				 * If not yet authorized, hide all tabs
				 *
				 */
				if( get_option('si_access_token') && get_option('si_user_id') ){ 
			?>
			
			<?php $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key; ?>
		 	
			<?php $this->plugin_options_tabs(); ?>	
						
			<form method="post" action="options.php?noheader=true">
				<?php wp_nonce_field( 'update-options' ); ?>
				<?php settings_fields( $tab ); ?>
				<?php do_settings_sections( $tab ); ?>
				<?php submit_button(); ?>
			</form>
			
			<?php if ( $tab === $this->about_settings_key ) { ?>
			
			<h3><?php _e('API RESPONSE', 'simply-instagram'); ?></h3>
			<p><?php _e('The following will display API response from Instagram API Server. Please use cache module in order to work.', 'simply-instagram'); ?></p>
			
			<p><strong><?php _e('Self Feed', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("selffeed.json"); ?></p>
			<p><strong><?php _e('Recent Media', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("recentmedia.json"); ?></p>
			<p><strong><?php _e('Likes', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("likes.json"); ?></p>
			<p><strong><?php _e('Followers', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("followers.json"); ?></p>
			<p><strong><?php _e('Following', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("following.json"); ?></p>
			<p><strong><?php _e('Following Info', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("followinginfo.json"); ?></p>
			<p><strong><?php _e('Currently Popular', 'simply-instagram'); ?></strong>: <?php echo sIntCheckResponse("popular.json"); ?></p>
			<br/>
			
			<h3><?php _e('RATE THIS PLUGIN', 'simply-instagram'); ?></h3>
			<p><a href="http://wordpress.org/support/view/plugin-reviews/simply-instagram" target="_blank" class="tooltip" title="Rate this plugin in Wordpress.org"> <img src="<?php echo plugins_url(); ?>/simply-instagram/images/rate.png" ></a></p><br/>
			
			<h3><strong id="help"><?php _e('HELP THIS PLUGIN', 'simply-instagram'); ?></strong></h3><p style="text-align: justify !important;"><strong><?php _e('You can help improve this plugin by donating any amount you want or rate this plugin in Wordpress.org.', 'simply-instagram'); ?></strong></p>
			
			<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
				<input type="hidden" name="cmd" value="_s-xclick">
				<input type="hidden" name="hosted_button_id" value="BUDCX2S6SJ3ZG">
				<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
				<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		 	</form>
		 
				<script>
				
				//remove save button if in about tab
				jQuery(document).ready( function(){
					jQuery("#submit").css( 'display', 'none' );
				});
				
				</script>
			<?php } ?>
			
			<?php if ( $tab === $this->shortcode_settings_key ) { ?>
				<script>
				
				//remove save button if in shortcode tab
				jQuery(document).ready( function(){
					jQuery("#submit").css( 'display', 'none' );
				});
				
				function displayVals() {
				      var endpoints = jQuery("select[name=endpoints]").val();// || [];
				      var type = jQuery("select[name=type]").val();// || [];
				      var size = jQuery("select[name=size]").val();
				      var display = jQuery("select[name=totalphoto]").val();
				      var presentation = jQuery("select[name=presentation]").val();
				      var displayoption = jQuery("select[name=displayoption]").val();
				      var showphotographer = jQuery("select[name=showphotographer]").val();
				      var photocomment = jQuery("select[name=photocomment]").val();
				      var stat = jQuery("select[name=stat]").val();
				      var photocaption = jQuery("select[name=photocaption]").val();
				      var displaycomment = jQuery("select[name=displaycomment]").val();
				      
				      if( endpoints == "media" ){
				        jQuery("p#generated-sc").html(  '[simply_instagram endpoints="' + endpoints + '" type="popular" size="' + size + '" presentation="' + presentation + '" display="' + display + '" displayoption="' + displayoption + '" showphotographer="' + showphotographer + '" photocomment="' + photocomment + '" stat="' + stat + '" photocaption="' + photocaption + '" displaycomment="' + displaycomment + '"]' );
				        jQuery("select[name=type]").attr( "disabled", true );
				      }else{
				      	jQuery("p#generated-sc").html( '[simply_instagram endpoints="' + endpoints + '" type="' + type + '" size="' + size + '" presentation="' + presentation + '" display="' + display + '" displayoption="' + displayoption + '" showphotographer="' + showphotographer + '" photocomment="' + photocomment + '" stat="' + stat + '" photocaption="' + photocaption + '" displaycomment="' + displaycomment + '"]' );
				      	jQuery("select[name=type]").attr( "disabled", false );
				      }
				    }		    
				
				    jQuery("select[name=endpoints]").change(displayVals);
				    jQuery("select[name=type]").change(displayVals);
				    jQuery("select[name=size]").change(displayVals);
				    jQuery("select[name=totalphoto]").change(displayVals);
				    jQuery("select[name=presentation]").change(displayVals);
				    jQuery("select[name=displayoption]").change(displayVals);
				    jQuery("select[name=showphotographer]").change(displayVals);
				    jQuery("select[name=photocomment]").change(displayVals);
				    jQuery("select[name=stat]").change(displayVals);
				    jQuery("select[name=photocaption]").change(displayVals);
				    jQuery("select[name=displaycomment]").change(displayVals);
				    displayVals();
				</script>
			<?php } ?>
			
			<?php } /* End of hiding tabs when not authorized */ ?>
		</div>
		<?php
	}	
	
	function plugin_options_tabs() {
	
		$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;

		screen_icon();
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
			$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
			echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		}
		echo '</h2>';
	}
};

/**
 * Activate and Reactivate hook used for early function
*/
register_activation_hook( __FILE__, array( 'Simply_Instagram_Plugin', 'si_activate' ) );
register_deactivation_hook( __FILE__, array( 'Simply_Instagram_Plugin', 'si_deactivate' ) );

add_action( 'plugins_loaded', create_function( '', '$simply_instagram_plugin = new Simply_Instagram_Plugin;' ) );