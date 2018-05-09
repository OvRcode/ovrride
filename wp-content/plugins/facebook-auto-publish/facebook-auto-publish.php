<?php
/*
 Plugin Name: WP Facebook Auto Publish
Plugin URI: https://xyzscripts.com/wordpress-plugins/facebook-auto-publish/
Description:   Publish posts automatically from your blog to Facebook social media. You can publish your posts to Facebook as simple text message, text message with image or as attached link to your blog. The plugin supports filtering posts by custom post-types and categories.
Version: 1.4.9
Author: xyzscripts.com
Author URI: https://xyzscripts.com/
License: GPLv2 or later
*/

/*
 This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/
if( !defined('ABSPATH') ){ exit();}
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there!  I'm just a plugin, not much I can do when called directly.";
	exit;
}

//error_reporting(E_ALL);
define('XYZ_FBAP_PLUGIN_FILE',__FILE__);
define('XYZ_FBAP_FB_API_VERSION','v2.6');

//define('XYZ_FBAP_FB_api','https://api.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/');
//define('XYZ_FBAP_FB_api_video','https://api-video.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/');
//define('XYZ_FBAP_FB_api_read','https://api-read.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/');
//define('XYZ_FBAP_FB_graph','https://graph.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/');
//define('XYZ_FBAP_FB_graph_video','https://graph-video.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/');
//define('XYZ_FBAP_FB_www','https://www.facebook.com/'.XYZ_FBAP_FB_API_VERSION.'/');

global $wpdb;
//$wpdb->query('SET SQL_MODE=""');
if(isset($_POST) && isset($_POST['fb_auth'] ) || (isset($_GET['page']) && ($_GET['page']=='facebook-auto-publish-settings')) )
{
	ob_start();
}
require_once( dirname( __FILE__ ) . '/admin/install.php' );
require_once( dirname( __FILE__ ) . '/xyz-functions.php' );
require_once( dirname( __FILE__ ) . '/admin/menu.php' );
require_once( dirname( __FILE__ ) . '/admin/destruction.php' );
if (version_compare(PHP_VERSION, '5.4.0', '>'))
{
	require_once( dirname( __FILE__ ) . '/api/Facebook/autoload.php');
	require_once( dirname( __FILE__ ) . '/admin/publish.php' );
}
require_once( dirname( __FILE__ ) . '/admin/ajax-backlink.php' );
require_once( dirname( __FILE__ ) . '/admin/metabox.php' );
require_once( dirname( __FILE__ ) . '/admin/admin-notices.php' );
if(get_option('xyz_credit_link')=="fbap"){

	add_action('wp_footer', 'xyz_fbap_credit');

}
function xyz_fbap_credit() {
	$content = '<div style="clear:both;width:100%;text-align:center; font-size:11px; "><a target="_blank" title="WP Facebook Auto Publish" href="https://xyzscripts.com/wordpress-plugins/facebook-auto-publish/details" >WP Facebook Auto Publish</a> Powered By : <a target="_blank" title="PHP Scripts & Programs" href="http://www.xyzscripts.com" >XYZScripts.com</a></div>';
	echo $content;
}
if(!function_exists('get_post_thumbnail_id'))
	add_theme_support( 'post-thumbnails' );
?>
