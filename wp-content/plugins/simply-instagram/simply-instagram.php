<?php
/*
Plugin Name: Simply Instagram
Plugin URI: http://www.rollybueno.info/project/simply-instagram-wordpress-plugin/
Description: Promote your Instagram photo through your Wordpress website using Simply Instagram.
Version: 1.2.6
Author: Rolly G. Bueno Jr.
Author URI: http://www.rollybueno.info
License: GPL v2.0.
Copyright 2012 Rolly G. Bueno Jr.
*/
add_action( 'init', sInstFollowerCookie() );
DEFINE( "simply_instagram_plugin_path", plugin_dir_path(__FILE__)  );
DEFINE('simply_instagram_plugin_url', get_bloginfo('siteurl') . '/wp-content/plugins/simply-instagram/');
require simply_instagram_plugin_path . 'simply-instagram-functions.php';
require simply_instagram_plugin_path . 'simply-instagram-widget.php';
//require simplyInstagramPluginPath . 'simplyInstagramMedia.php';
/**
 * Plugin installation
*/
function simply_instagram_activate()
{	
	/**
	 * v1.2.5 uses wp options
	 * Drop old table if exist
	*/
    global $wpdb;
    
    $table = $wpdb->prefix . "instagram";
    $wpdb->query("DROP TABLE IF EXISTS $table");
    
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
	
}
	
/**
 * Uninstall, drop table
*/

function simply_instagram_deactivate()
{
	global $wpdb;
        $table = $wpdb->prefix."instagram";
        
        $wpdb->query("DROP TABLE IF EXISTS $table");
        
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
	
	delete_option( 'si_css' );
}
/**
 * hook registration
*/
register_activation_hook(__FILE__,'simply_instagram_activate');
register_deactivation_hook( __FILE__, 'simply_instagram_deactivate' );

function sInstFollowerCookie()
{
		if( isset( $_GET['access_token'] ) ):
				setcookie( 'visitor_access_token', $_GET['access_token'], strtotime( " + 14 days" ) );
		endif; 
}

function simply_instagram_styles()
{
	/*
         * It will be called only on your plugin admin page, enqueue our script here
         */	
        wp_enqueue_script( 'common' );
        wp_enqueue_script( 'wp-lists' );
        wp_enqueue_script( 'postbox' );
}

/**
 * admin menu
*/
function register_custom_menu_page() 
{
	$page = add_options_page('Simply Instagram', 'Simply Instagram', 'manage_options', 'simply-instagram', 'option_page_simply_instagram' );
	add_action('admin_print_styles-' . $page, 'simply_instagram_styles');	 
	add_action( 'admin_head-' . $page, 'simply_instagram_admin_head' );		 
}
add_action( 'admin_menu', 'register_custom_menu_page' );

/**
 * Add stylesheet to Simply Instagram
 * option page
*/
function simply_instagram_admin_head() {
    $siteurl = get_option('siteurl');
    $url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/css/simply-instagram-admin.css';
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
    
    	?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			postboxes.add_postbox_toggles('option_page_simply_instagram');
		});
		//]]>
	</script>
	<?php
    
}

function option_page_simply_instagram()
{
	isset( $_POST['mediaViewer'] ) ? update_option( 'mediaViewer', $_POST['mediaViewer'] ): null ;
	isset( $_POST['displayCommentMediaViewer'] ) ? ( $_POST['displayCommentMediaViewer'] < 5 ? update_option( 'displayCommentMediaViewer', '5' ) : update_option( 'displayCommentMediaViewer', $_POST['displayCommentMediaViewer'] ) ): null ;
	isset( $_POST['displayPhotographer'] ) ? update_option( 'displayPhotographer', $_POST['displayPhotographer'] ) : null;
	isset( $_POST['displayComment'] ) ? ( $_POST['displayComment'] > 5 ? update_option( 'displayComment', '5' ) : update_option( 'displayComment', $_POST['displayComment'] ) ): null;
	isset( $_POST['displayStatistic'] ) ? update_option( 'displayStatistic', $_POST['displayStatistic'] ) : null;	
	isset( $_POST['displayDescription'] ) ? update_option( 'displayDescription', $_POST['displayDescription'] ) : null;	
	isset( $_POST['galleryTheme'] ) ? update_option( 'galleryTheme', $_POST['galleryTheme'] ) : null;
	isset( $_POST['autoPlay'] ) ? update_option( 'autoPlay', $_POST['autoPlay'] ) : null;
	isset( $_POST['ppPhotoDescription'] ) ? update_option( 'ppPhotoDescription', $_POST['ppPhotoDescription'] ) : null;
	isset( $_POST['animationSpeed'] ) ? update_option( 'animationSpeed', $_POST['animationSpeed'] ) : null;
	isset( $_POST['overlayGallery'] ) ? update_option( 'overlayGallery', $_POST['overlayGallery'] ) : null;	
	isset( $_POST['ppDisplayPhotographer'] ) ? update_option( 'ppDisplayPhotographer', $_POST['ppDisplayPhotographer'] ) : null;
	isset( $_POST['ppDisplayStatistic'] ) ? update_option( 'ppDisplayStatistic', $_POST['ppDisplayStatistic'] ) : null;
	isset( $_POST['enableCandCom'] ) ? update_option( 'enableCandCom', $_POST['enableCandCom'] ) : null;
	isset( $_POST['si_css'] ) ? update_option( 'si_css', $_POST['si_css'] ) : null;
	
	//clear cache folder
	if( isset( $_POST['sIntClearCache'] ) ):
		sIntClearCache();
		echo '<div id="message" class="updated fade"><p>Cache folder has been cleanup!</p></div>';
	endif;	
	
	//check jpeg compression value
	if( isset( $_POST['JPEGCompression'] ) ):
		if( $_POST['JPEGCompression'] > 100 || $_POST['JPEGCompression'] < 10 ):
			update_option( 'JPEGCompression', '60' );
		else:
			update_option( 'JPEGCompression', $_POST['JPEGCompression'] );
		endif;
		
	endif;
	
	global $wpdb;
			
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
	<div id="icon-plugins" class="icon32"></div><h2>Simply Instagram</h2>	
	<?php if( !$info['si_access_token'] && !$info['si_user_id'] ): ?> 
		<?php if( $_GET['access_token'] == "" && $_GET['id'] == "" ): ?>
			<div class="error">
			 <p>You did not authorize Simply Instagram. This plugin will not work without your authorization. </p>
			</div>
		<?php endif; ?>
	<a href=" <?php echo sInstLogin( '?return_uri=' . base64_encode( get_admin_url() . 'options-general.php?page=simply-instagram'  )  ) ?> "><img src="<?php echo plugins_url() . '/simply-instagram/images/instagram-login.jpg'; ?>" title="Login to Instagram and authorize Simply Instagram plugin" alt="Login to Instagram and authorize Simply Instagram plugin" /></a>
	<?php ?>
	<?php else: ?>
	<?php 
		if( isset( $_GET['access_token'] ) && $_GET['id'] ):
		?> <meta http-equiv="refresh" content="0;url=<?php echo get_admin_url() . 'options-general.php?page=simply-instagram'; ?>"> <?Php
		endif;
	?>
	
	<iframe src="https://instagram.com/accounts/logout/" width="0" height="0">Logout</iframe>
	<?php
		$user = sInstGetInfo( user_id(), access_token() );
	?>
	<div id="sInts-welcome">Welcome <?php echo $user['data']['full_name']; ?>. You can start using Simply Instagram. Please refer Usage and documentation.</div> 
	<form name="itw_logout" method="post" action="<?php echo str_replace( '%7E', '~', htmlentities( get_admin_url() . 'options-general.php?page=simply-instagram'  )  ); ?>">
	<input type="hidden" name="sIntLogout" value="log_out">
	<input type="submit" class="button" value="Log out" name="logout" onclick="" >
	</form>
	
	<div class="clear"></div>
	
	<div id="first-column" class="postbox-container">
						
	<!-- BEGIN metabox-holder -->
	<div class="metabox-holder">
							
	<!-- BEGIN meta-box-sortables ui-sortable -->
	<div class="meta-box-sortables ui-sortable">
	
	<!-- BEGIN SHORTCODE GENERATOR -->
	<div id="left-column" class="postbox">
									
	<div class="handlediv" title="Click to toggle"><br></div>
							
	<h3 class='hndle'><span>Shortcode Generator</span></h3>									
	<!-- BEGIN inside -->
	<div class="inside">
	<p>Use this tool to generate shortcode. Copy and paste the highlighted paragraph below to your post or page.</p>
	
	Endpoints: <select name="endpoints" class="sc-generator">	
		 <option value ="users" >Users</option>
		 <option value ="media" >Media</option>
	</select>
	
	Type: <select name="type" class="sc-generator">	
		 <option value ="self-feed" >Self Feed</option>
		 <option value ="recent-media" >Recent Media</option>
		 <option value ="likes" >Likes</option>
	</select>
	
	Size: <select name="size" class="sc-generator">	
		 <option value ="thumbnail" >Thumbnail</option>
		 <option value ="low_resolution" >Low Resolution</option>
		 <option value ="standard_resolution" >Standard Resolution</option>
	</select>
	
	Display: <select name="totalphoto" class="sc-generator">	
		<?php for( $i=1; $i<=20; $i++ ): ?>	
		 <option value ="<?php echo $i; ?>" ><?php echo $i; ?></option>
		<?php endfor; ?>
	</select>

	<p style="background-color: red;" id="generated-sc"></p>
							
	<!-- END inside -->
	</div>
							
	<!-- END SHORTCODE GENERATOR -->
	</div>
	
	<!-- BEGIN SHORTCODE -->
	<div id="left-column" class="postbox">
									
	<div class="handlediv" title="Click to toggle"><br></div>
							
	<h3 class='hndle'><span>Shortcode Settings</span></h3>									
	<!-- BEGIN inside -->
	<div class="inside">
	<p>Define Simply Instagram settings for shortcode. This settings  are applicable only for shortcode implementation. For widget, you can define your settings on each widget area. </p>
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		 <table class="form-table">
		  <tbody>
		   
		   <tr>
		    <th>Display Photo Information</th>
		    <td>
		     <select name="mediaViewer">	
			 <option value ="prettyPhoto" <?php selected( get_option( 'mediaViewer' ), "prettyPhoto" ); ?>>Slideshow</option>
			 <option value ="prettyPhotoFrame" <?php selected( get_option( 'mediaViewer' ), "prettyPhotoFrame" ); ?>>Single Media Viewer</option>
			 <option value ="builtInMediaViewer" <?php selected( get_option( 'mediaViewer' ), "builtInMediaViewer" ); ?>>Simply Instagram Media Viewer</option>
			 <option value ="instagramLink" <?php selected( get_option( 'mediaViewer' ), "instagramLink" ); ?>>Open in Instagram</option>
		     </select>
		     <br />
		     <span class="tips">Choose what method you want to use for viewing detailed information when visitor click on each photo. prettyPhoto can be set on setting box below.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Comments to display in Media Viewer</th>
		    <td>
		     <input type="text" name="displayCommentMediaViewer" value="<?php echo get_option( 'displayCommentMediaViewer' ); ?>">
		     <br />
		     <span class="tips">Comments to display in Media Viewer. Please do not confuse with Display photo comment below.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Show Photographer</th>
		    <td>
		     <select name="displayPhotographer">	
			 <option value ="true" <?php selected( get_option( 'displayPhotographer' ), "true" ); ?>>Yes</option>
			 <option value ="false" <?php selected( get_option( 'displayPhotographer' ), "false" ); ?>>No</option>
		     </select>
		     <br />
		     <span class="tips">Display photographer in each photo.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Display photo comment</th>
		    <td>
		     <input type="text" name="displayComment" value="<?php echo get_option( 'displayComment' ); ?>">
		     <br />
		     <span class="tips">Comments to display in shortcode. 0 to hide and maximum of 5 comments can be displayed.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Display Statistics</th>
		    <td>
		     <select name="displayStatistic">	
			 <option value ="true" <?php selected( get_option( 'displayStatistic' ), "true" ); ?>>Yes</option>
			 <option value ="false" <?php selected( get_option( 'displayStatistic' ), "false" ); ?>>No</option>
		     </select>
		     <br />
		     <span class="tips">Display how many likes and comments for each photo.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Show Description</th>
		    <td>
		     <select name="displayDescription">	
			 <option value ="true" <?php selected( get_option( 'displayDescription' ), "true" ); ?>>Yes</option>
			 <option value ="false" <?php selected( get_option( 'displayDescription' ), "false" ); ?>>No</option>
		     </select>
		     <br />
		     <span class="tips">Display photos description. NOTE: Photo description has no word limit thus the height of each photo information box varies on its length. If you wish to have a uniform box&#39s size displayed, disable this feature.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th></th>
		    <td align="right">
		    <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options'); ?>" >
		    </td>
		   </tr>
		   
		  </tbody>
		 </table>
		</form>						
	<!-- END inside -->
	</div>
							
	<!-- END SHORTCODE -->
	</div>
	
	<!-- BEGIN PRETTYPHOTO -->
	<div id="left-column" class="postbox">
									
	<div class="handlediv" title="Click to toggle"><br></div>
							
	<h3 class='hndle'><span>PrettyPhoto Settings</span></h3>									
	<!-- BEGIN inside -->
	<div class="inside">
	<p>This prettyPhoto settings is for shortcode implementation only. Widget has different settings under each boxes.</p>
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">		 
		 <table class="form-table">
		  <tbody>
		  
		   <tr>
		    <th>Gallery Theme</th>
		    <td>
		     <select name="galleryTheme">	
			 <option value="pp_default" <?php selected( get_option( 'galleryTheme' ), "pp_default" ); ?>>Default</option>
		 	 <option value="facebook" <?php selected( get_option( 'galleryTheme' ), "facebook" ); ?>>Facebook</option>
		 	 <option value="dark_rounded" <?php selected( get_option( 'galleryTheme' ), "dark_rounded" ); ?>>Dark Round</option>
			 <option value="dark_square" <?php selected( get_option( 'galleryTheme' ), "dark_square" ); ?>>Dark Square</option>
			 <option value="light_rounded" <?php selected( get_option( 'galleryTheme' ), "light_rounded" ); ?>>Light Round</option>
			 <option value="light_square" <?php selected( get_option( 'galleryTheme' ), "light_square" ); ?>>Light Square</option>
		    </select>
		     <br />
		     <span class="tips">Choose prettyPhoto gallery theme. You can compare each themes <a href="http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/" target="_blank">here</a> under Theme Support at the bottom of the page.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Slideshow auto play</th>
		    <td>
		     <select name="autoPlay">	
			 <option value ="true" <?php selected( get_option( 'autoPlay' ), "true" ); ?>>Yes</option>
			 <option value ="false" <?php selected( get_option( 'autoPlay' ), "false" ); ?>>No</option>
		     </select>
		     <br />
		     <span class="tips">Set slideshow autoplay on/off.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Display photo description</th>
		    <td>
		     <select name="ppPhotoDescription">	
			 <option value ="true" <?php selected( get_option( 'ppPhotoDescription' ), "true" ); ?>>Yes</option>
			 <option value ="false" <?php selected( get_option( 'ppPhotoDescription' ), "false" ); ?>>No</option>
		     </select>
		     <br />
		     <span class="tips">prettyPhoto sometimes unresponsive on long photo description and this is the major drawback in previous version of Simply Instagram. Turn this feature off when it does.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Animation Speed</th>
		    <td>
		     <select name="animationSpeed">	
			 <option value ="slow" <?php selected( get_option( 'animationSpeed' ), "slow" ); ?>>Slow</option>
			 <option value ="normal" <?php selected( get_option( 'animationSpeed' ), "normal" ); ?>>Normal</option>
			 <option value ="fast" <?php selected( get_option( 'animationSpeed' ), "fast" ); ?>>Fast</option>
		     </select>
		     <br />
		     <span class="tips">Choose animation speed. Default normal.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Overlay Gallery</th>
		    <td>
		     <select name="overlayGallery">	
			 <option value ="true" <?php selected( get_option( 'overlayGallery' ), "true" ); ?>>True</option>
			 <option value ="false" <?php selected( get_option( 'overlayGallery' ), "false" ); ?>>False</option>
		     </select>
		     <br />
		     <span class="tips">If set to true, a gallery will overlay the fullscreen image on mouse over.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Display Statistic</th>
		    <td>
		     <select name="ppDisplayStatistic">	
			 <option value ="true" <?php selected( get_option( 'ppDisplayStatistic' ), "true" ); ?>>True</option>
			 <option value ="false" <?php selected( get_option( 'ppDisplayStatistic' ), "false" ); ?>>False</option>
		     </select>
		     <br />
		     <span class="tips">Display likes and comments count when slideshow starts.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th>Display photographer profile picture</th>
		    <td>
		     <select name="ppDisplayPhotographer">	
			 <option value ="true" <?php selected( get_option( 'ppDisplayPhotographer' ), "true" ); ?>>True</option>
			 <option value ="false" <?php selected( get_option( 'ppDisplayPhotographer' ), "false" ); ?>>False</option>
		     </select>
		     <br />
		     <span class="tips">Show photographer profile image when slideshow starts.</span>
		    </td>
		   </tr>
		   
		   <tr>
		    <th></th>
		    <td align="right">
		    <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options'); ?>" >
		    </td>
		   </tr>
		   
		  </tbody>
		 </table>
		</form>
							
	<!-- END inside -->
	</div>
							
	<!-- END PRETTYPHOTO -->
	</div>
	
	<!-- BEGIN CSS -->
	<div id="left-column" class="postbox">						
									
	<div class="handlediv" title="Click to toggle"><br></div>
							
	<h3 class='hndle'><span>CSS Settings</span></h3>									
	<!-- BEGIN inside -->
	<div class="inside">
	<p>If you want to personalize the CSS of this plugin, please use the box below.</p>
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">		 
		 <table class="form-table">
		  <tbody>
		  
		   <tr>
		    <th>CSS:</th>
		    <td>
		     <textarea name="si_css" class="large-text code" rows="10"><?php echo get_option( 'si_css' ); ?></textarea>
		     <br />
		     <span class="tips">Refer the default CSS below for standard Simply Instagram classes and ids. </span>
		    </td>
		   </tr>		   
		   
		   <tr>
		    <th></th>
		    <td align="right">
		    <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options'); ?>" >
		    </td>
		   </tr>
		   
		   <tr>		    
		    <td colspan="2">
		    <textarea readonly="readonly" style="width:100%;height:250px;"><?php echo file_get_contents( simply_instagram_plugin_path . '/css/simply-instagram.css' );?></textarea>
		    </td>
		   </tr>
		   
		  </tbody>
		 </table>
		</form>
							
	<!-- END inside -->
	</div>
							
	<!-- END CSS -->
	</div>
	<?php
	
	/**
	 * Caching not working yet, disable from admin
	 * interface
	
	<!-- BEGIN CACHING -->
	<div id="left-column" class="postbox">
									
	<div class="handlediv" title="Click to toggle"><br></div>
							
	<h3 class='hndle'><span>Photo Caching and Compression Settings</span></h3>									
	<!-- BEGIN inside -->
	<div class="inside">
	<p>Use Simply Instagram caching module for saving hotlinking bandwidth and photo compression for site load speed</p>
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">		 
		 <table class="form-table">
		  <tbody>
		   
		   <tr>
		    <th>Enable Caching and JPEG Compression</th>
		    <td>
		     	<select name="enableCandCom" >	
				 <option value ="yes" <?php selected( get_option( 'enableCandCom' ), "yes" ); ?>>Yes</option>
				 <option value ="no" <?php selected( get_option( 'enableCandCom' ), "no" ); ?>>No</option>
			</select>
		     <br />
		     <span class="tips"></span>
		    </td>
		   </tr>

		   <tr>
		    <th>JPEG Compression</th>
		    <td>
		     <input type="text" value="<?php echo get_option( 'JPEGCompression' ); ?>" name ="JPEGCompression" />
		     <br />
		     <span class="tips">Set jpeg compression value. Set value from 10 - 100 where 100 is the maximum. Default is 60</span>
		    </td>
		   </tr>	   
		   
		   <tr>
		    <th></th>
		    <td align="right">
		    <input class="button-primary" type="submit" name="Save" value="<?php _e('Save Options'); ?>" >
		    </td>
		   </tr>
		   
		  </tbody>
		 </table>
		</form>
		
		<form method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
		<table class="form-table">
		  <tbody>
		   
		   <tr>
		    <th>Clean cache folder</th>
		    <td>
		    	<input type="hidden" name="sIntClearCache" value="true" >		    
		     	<input class="button-primary" type="submit" name="Clear" value="<?php _e('Clear'); ?>" >
		     <br />
		     <span class="tips">Clear cache folder if you are running out of disk space</span>
		    </td>
		   </tr>
		   
		 </tbody>
		</table>
		</form>
							
	<!-- END inside -->
	</div>
							
	<!-- END CACHING -->
	</div>
	

	*/
	?>
	
	<!-- END meta-box-sortables ui-sortable -->
	</div>		
						
	<!-- END metabox-holder -->
	</div>
					
	</div>	
	
	<div id="second-column" class="postbox-container" >
	   <!-- BEGIN metabox-holder -->
	   <div class="metabox-holder">
							
	   <!-- BEGIN meta-box-sortables ui-sortable -->
	   <div class="meta-box-sortables ui-sortable">
	
	<!-- BEGIN help -->
	   <div id="bug-reporting" class="postbox" >
	   <div class="handlediv" title="Click to toggle"><br></div>
	   <h3 class='hndle'><span>Bug Reporting</span></h3>
	   <!-- BEGIN inside -->
	   <div class="inside">
		 <h2>Found a Bug?</h2>
		 
		 <div style="text-align: center;">
		 <p style="text-align: justify !important;"><strong>If you found a bug or some of Simply Instagram functions are not working, please contact me <a href="http://www.rollybueno.info/contact/" target="_blank" >here</a> and include the information below together your website address:</strong></p>
		 <p style="text-align: left !important;">Access Token: <?php echo $info['si_access_token']; ?></p>
		 <p style="text-align: left !important;">User ID: <?php echo $info['si_user_id']; ?></p>
		</div>
		
	   </div>
	   </div><!-- END help -->
	
	   <!-- BEGIN help -->
	   <div id="help-columns" class="postbox" >
	   <div class="handlediv" title="Click to toggle"><br></div>
	   <h3 class='hndle'><span><strong id="help">HELP THIS PLUGIN</strong></span></h3>
	   <!-- BEGIN inside -->
	   <div class="inside">
		<div style="width: 90%; ">
		 <h2>Like this Plugin?</h2>
		 
		 <div style="text-align: center;">
		 <p style="text-align: justify !important;"><strong>You can help improve this plugin by donating any amount you want or rate this plugin in Wordpress.org.</strong></p>
		 <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
			<input type="hidden" name="cmd" value="_s-xclick">
			<input type="hidden" name="hosted_button_id" value="BUDCX2S6SJ3ZG">
			<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
			<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
		 </form>
		 
		 <p><a href="http://wordpress.org/support/view/plugin-reviews/simply-instagram" target="_blank" class="tooltip" title="Rate this plugin in Wordpress.org"> <img src="<?php echo plugins_url(); ?>/simply-instagram/images/rate.png" ></a></p>	   	
		</div>
		</div>
		
	   </div>
	   </div><!-- END help -->
	   
	   <!-- BEGIN about me -->
	   <div id="about-me-columns" class="postbox" >
	   <div class="handlediv" title="Click to toggle"><br></div>
	   <h3 class='hndle'><span>About me</span></h3>
	   <!-- BEGIN inside -->
	   <div class="inside">
		<div style="width: 90%; ">
		 <p><strong>Check other Wordpress plugin that I have developed. Dont forget to follow me on Twitter.</strong></p>
		 <p><a href="http://wordpress.org/extend/plugins/simply-youtube/" style=" text-decoration: none; " class="plugin-homepage-simply-youtube" >Simply Youtube</a></p>
		 <p><a href="http://wordpress.org/extend/plugins/real-time-twitter/" style=" text-decoration: none; " class="plugin-homepage-real-time-twitter" >Real Time Twitter</a></p>
		 <p><a href="http://wordpress.org/extend/plugins/advanced-noaa-weather-forecast/" style=" text-decoration: none; " class="plugin-homepage-noaa" >Advanced NOAA Weather Forecast</a></p>
		 
		 <p style="text-align: center; "><a href="https://twitter.com/rbuenojr" class="twitter-follow-button" data-show-count="false" data-size="large" data-dnt="true">Follow @rbuenojr</a></p>
		 <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}	(document,"script","twitter-wjs");</script>

		</div>
	   </div>
	   </div><!-- END about me -->
	   
	   </div>
	   </div>
	   </div>

	
	<script>
	jQuery(document).ready( function() {
	    	jQuery('.postbox h3').prepend('<a class="togbox"></a> ');
	    	jQuery('.handlediv').click( function() {
	        jQuery(jQuery(this).parent().get(0)).toggleClass('closed');
	    	});
	});
		function displayVals() {
		      var endpoints = jQuery("select[name=endpoints]").val();// || [];
		      var type = jQuery("select[name=type]").val();// || [];
		      var size = jQuery("select[name=size]").val();
		      var display = jQuery("select[name=totalphoto]").val();
		      if( endpoints == "media" ){
		        jQuery("p#generated-sc").html( '[simply_instagram endpoints="' + endpoints + '" type="popular" size="' + size + '" display="' + display + '"]' );
		        jQuery("select[name=type]").attr( "disabled", true );
		      }else{
		      	jQuery("p#generated-sc").html( '[simply_instagram endpoints="' + endpoints + '" type="' + type + '" size="' + size + '" display="' + display + '"]' );
		      	jQuery("select[name=type]").attr( "disabled", false );
		      }
		    }
		
		    jQuery("select[name=endpoints]").change(displayVals);
		    jQuery("select[name=type]").change(displayVals);
		    jQuery("select[name=size]").change(displayVals);
		    jQuery("select[name=totalphoto]").change(displayVals);
		    displayVals();
	</script>
	
	<?php endif; ?>
	</div>	
	<?php
}

function sInstAdminNotice()
{
	global $wpdb;
	
	/*
	 * Info query to check if database
	 * has record.
	*/
	$info = array( 'si_access_token' => get_option( 'si_access_token' ), 'si_user_id' => get_option( 'si_user_id' ) );
	
	if( !$info['si_access_token'] && !$info['si_user_id'] ):
	
	echo '<div class="error">';
        echo '<p>Simply Instagram has not been setup correctly. Kindly <a href="options-general.php?page=simply-instagram">authorize</a> Simply Instagram. </p>';
    	echo '</div>';
    	
    	endif;
}

add_action('admin_notices', 'sInstAdminNotice');

/**
* instagram shorcode. It will provide several options
*/
function simply_instagram_sc( $atts )
{
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
	extract( shortcode_atts( array( ), $atts ) );
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
					echo '<div id="masonryContainer" title="self-feed" class="clearfix masonry">';
					echo sInstDisplayData( sInstGetSelfFeed( access_token() ), $atts['size'], $atts['display'], "150", $customRel );
					echo '</div>';													
				break;
				/**
				 * Recent Media
				 * Display all photos uploaded by user only
				*/
				case'recent-media':
					echo '<div id="masonryContainer" title="recent-media" class="clearfix masonry">';
					echo sInstDisplayData( sInstGetRecentMedia( user_id(), access_token() ), $atts['size'], $atts['display'], "150", $customRel );
					echo '</div>';	
				break;
				/**
				 * Likes
				 * Display all photos liked by user
				*/
				case'likes':
					echo '<div id="masonryContainer" title="likes" class="clearfix masonry">';
					echo sInstDisplayData( sInstGetLikes( access_token() ), $atts['size'], $atts['display'], "150", $customRel );	
					echo '</div>';		
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
			
			echo '<div id="masonryContainer" title="media" class="clearfix masonry">';
			echo sInstDisplayData( sInstGetMostPopular( $atts['type'], access_token() ), $atts['size'], $atts['display'], "150", $customRel );
			echo '</div>';		
			
		break;
		
	endswitch;
	/**
	 * PrettyPhoto settings for shortcode.
	 * v1.2.3 is using masonry as alternative
	 * to PrettyPhoto which often breaks or
	 * unresponsive when the image has
	 * long description
	*/
	?>
	<script src="<?php echo plugins_url(); ?>/simply-instagram/js/jquery.masonry.min.js"></script>
	
	<script>
jQuery('#masonryContainer').imagesLoaded(function(){jQuery('#masonryContainer').masonry({itemSelector : '.masonryItem',isAnimated: true});});jQuery("a#content_close").live( "click", function() {jQuery("#overlay").fadeOut("slow", function() { jQuery("#overlay").remove(); });jQuery("#contentWrap").fadeOut("slow", function() { jQuery("#contentWrap").remove(); });jQuery("#content_close").fadeOut("slow", function() { jQuery("#content_close").remove(); });});jQuery("#overlay").live( "click", function() {Query(this).fadeOut("slow", function() { jQuery(this).remove(); });jQuery("#contentWrap").fadeOut("slow", function() { jQuery("#contentWrap").remove(); });jQuery("#content_close").fadeOut("slow", function() { jQuery("#content_close").remove(); });});jQuery(function() {jQuery("a.overlay").click(function() {var docHeight = jQuery(document).height();jQuery("body").append("<div id='overlay'></div><a id='content_close' href='#'></a><div id='contentWrap'></div>");	jQuery("#overlay").height(docHeight).css({'opacity' : 0.4,'position': 'fixed','top': 0,'left': 0,'background-color': 'black','width': '100%','z-index': 99999});jQuery("#contentWrap").load( jQuery(this).attr('rel') );jQuery("#image-holder").css({'width': '612px','height': '612px'});});jQuery("div.item-holder").hover(function() { var id = jQuery(this).data("id");jQuery("div.hover-action[data-id=" + id + "]").show("slow");},function() {jQuery("div.hover-action").hide("slow");});});jQuery(document).ready(function(){jQuery("a[rel^='sIntSC']").prettyPhoto({autoplay_slideshow: <?php echo get_option( 'autoPlay' ) ;?>,social_tools: false,theme: '<?php echo get_option( "galleryTheme" ) ;?>',animation_speed: '<?php echo get_option( "animationSpeed" ) ;?>',overlay_gallery: <?php echo get_option( 'overlayGallery' ) ;?>});});jQuery(document).ready(function(){jQuery("a[rel^='prettyphoto']").prettyPhoto({autoplay_slideshow: false,social_tools: false,theme: '<?php echo get_option( "galleryTheme" ) ;?>'});});
	</script>
	<?php	
	$content = ob_get_contents();ob_end_clean();wp_reset_postdata();return $content;	
}
/**
* Register with instagram shortcode
*/
add_shortcode( 'simply_instagram', 'simply_instagram_sc' );

/**
* Enqueue plugin style-file
*/
function simply_instagram_stylesheet() 
{      
	wp_register_style( 'simplyInstagram', plugins_url('css/simply-instagram.css', __FILE__), '', '3' );
        wp_enqueue_style( 'simplyInstagram' );
	
        wp_register_style( 'prettyPhoto', plugins_url('css/simply-instagram-prettyPhoto.css', __FILE__) );
        wp_enqueue_style( 'prettyPhoto' );
}
/**
* Register with hook 'wp_enqueue_scripts', 
* which can be used for front end CSS and JavaScript
*/
add_action( 'wp_enqueue_scripts', 'simply_instagram_stylesheet' );

/**
* Enqueue plugin script
*/
function simply_instagram_script()
{
	/**
	 * Check if jquery included
	*/
	wp_enqueue_script( 'jquery' );
	
	/**
	 jQuery Tools
	*/
	//wp_enqueue_script( 'jquery.tools.min.js', 'http://cdn.jquerytools.org/1.2.7/full/jquery.tools.min.js' );
	
  	/**
	 * jquery prettyphoto js
	 * v 3.1.4
	*/
    	wp_enqueue_script( 'jquery-prettyphoto-3.1.4', plugins_url( '/js/simply-instagram-jquery.prettyPhoto.js',__FILE__ ), array(), '3.1.4' );    	
}
add_action('wp_enqueue_scripts', 'simply_instagram_script');

/**
 * IE css define in header
*/
function simply_instagram_head_IE(){
?>
<meta http-equiv="X-UA-Compatible" content="IE=8" />
<!-- BEGIN SimplyInstagram IE -->
<!-- [if IE 9]>
<style type="text/css">

.comment-profile{
	margin: 2px;
	width: 45px;
	float: left;
}
.comment-profile img{
	vertical-align: top;
}
.comment-holder{
	top: 0px;
	width: 200px;
	float: left !important;	
}

.comments-holder{
	width: 210px;
	float: left;
}
</style>
<![endif]-->
<!-- END SimplyInstagram IE -->

<?php

if( get_option( 'si_css' ) ):
?>
<!-- Start of Simply Instagram Custom CSS -->
<!-- Developed by Rolly G. Bueno Jr. -->
<style type="text/css">
<?php
	echo get_option( 'si_css' );
?>

</style>
<!-- End of Simply Instagram Custom CSS -->
<?php
endif;

}
add_action('wp_head', 'simply_instagram_head_IE');

?>