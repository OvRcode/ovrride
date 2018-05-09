<?php
if( !defined('ABSPATH') ){ exit();}
add_action('admin_menu', 'xyz_fbap_menu');

function xyz_fbap_add_admin_scripts()
{
	wp_enqueue_script('jquery');

	wp_register_script( 'xyz_notice_script_fbap', plugins_url('facebook-auto-publish/js/notice.js') );
	wp_enqueue_script( 'xyz_notice_script_fbap' );
	
	wp_register_style('xyz_fbap_style', plugins_url('facebook-auto-publish/css/style.css'));
	wp_enqueue_style('xyz_fbap_style');
}

add_action("admin_enqueue_scripts","xyz_fbap_add_admin_scripts");



function xyz_fbap_menu()
{
	add_menu_page('Facebook Auto Publish - Manage settings', 'WP Facebook Auto Publish', 'manage_options', 'facebook-auto-publish-settings', 'xyz_fbap_settings',plugin_dir_url( XYZ_FBAP_PLUGIN_FILE ) . 'images/fbap.png');
	add_submenu_page('facebook-auto-publish-settings', 'Facebook Auto Publish - Manage settings', ' Settings', 'manage_options', 'facebook-auto-publish-settings' ,'xyz_fbap_settings'); // 8 for admin
	add_submenu_page('facebook-auto-publish-settings', 'Facebook Auto Publish - Logs', 'Logs', 'manage_options', 'facebook-auto-publish-log' ,'xyz_fbap_logs');
	add_submenu_page('facebook-auto-publish-settings', 'Facebook Auto Publish - About', 'About', 'manage_options', 'facebook-auto-publish-about' ,'xyz_fbap_about'); // 8 for admin
}


function xyz_fbap_settings()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);	
	$_POST = xyz_trim_deep($_POST);
	$_GET = xyz_trim_deep($_GET);
	
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/settings.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}



function xyz_fbap_about()
{
	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/about.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}


function xyz_fbap_logs()
{
	$_POST = stripslashes_deep($_POST);
	$_GET = stripslashes_deep($_GET);
	$_POST = xyz_trim_deep($_POST);
	$_GET = xyz_trim_deep($_GET);

	require( dirname( __FILE__ ) . '/header.php' );
	require( dirname( __FILE__ ) . '/logs.php' );
	require( dirname( __FILE__ ) . '/footer.php' );
}

add_action('wp_head', 'xyz_fbap_insert_og_image_for_fb');
function xyz_fbap_insert_og_image_for_fb(){

 	global $post;
 	if (empty($post))
 		$post=get_post();
 	if (!empty($post)){
	$postid= $post->ID;
	if(isset($postid ) && $postid>0)
	{
		$get_post_meta_insert_og=0;
  		$get_post_meta_insert_og=get_post_meta($postid,"xyz_fbap_insert_og",true);
			if (($get_post_meta_insert_og==1)&&(strpos($_SERVER["HTTP_USER_AGENT"], "facebookexternalhit/") !== false || strpos($_SERVER["HTTP_USER_AGENT"], "Facebot") !== false))
				{
					$attachmenturl=xyz_fbap_getimage($postid, $post->post_content);
						
						if(!empty($attachmenturl))
						{
							echo '<meta property="og:image" content="'.$attachmenturl.'" />';
							update_post_meta($postid, "xyz_fbap_insert_og", "0");
						}
		}
	}
}
}

?>