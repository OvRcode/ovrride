<?php
/*
Plugin Name: Facebook Page Plugin (Likebox)
Plugin URI: https://smashr.org/facebook-page-plugin-likebox-for-wordpress/
Description: THE Simplest way to bring Facebook Page Plugin to WordPress with lot more Options
Version: 1.5
Author: Smashr
Author URI: https://smashr.org
*/

/*
    Copyright (C) 2015 Smashr.org

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


// Some default options
add_option('smashify_facebook_page_plugin_data_href', 'https://www.facebook.com/WordPress'); 
add_option('smashify_facebook_page_plugin_broder_color', '');
add_option('smashify_facebook_page_plugin_data_hide_cover', 'false');
add_option('smashify_facebook_page_plugin_data_show_facepile', 'true');
add_option('smashify_facebook_page_plugin_data_show_posts', 'false');

add_option('smashify_facebook_page_plugin_widget_data_href', 'https://www.facebook.com/WordPress');
add_option('smashify_facebook_page_plugin_widget_title', 'Like Box');
add_option('smashify_facebook_page_plugin_widget_border_color', '');
add_option('smashify_facebook_page_plugin_widget_data_hide_cover', 'false');
add_option('smashify_facebook_page_plugin_widget_data_show_facepile', 'true');
add_option('smashify_facebook_page_plugin_widget_data_show_posts', 'false');

add_option('smashify_fbmembers_show_sponser_link', '1');

function filter_smashify_facebook_page_plugin_likebox($content)
{
    if (strpos($content, "<!--facebook-page-plugin-->") !== FALSE) {
        $content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
        $content = str_replace('<!--facebook-page-plugin-->', smashify_facebook_page_plugin_likebox(), $content);
    }

    return $content;
}

function facebook_page_plugin_head()
{    
        echo '<div id="fb-root"></div>' . "\n";
        echo '<script>(function(d, s, id) {' . "\n";
        echo 'var js, fjs = d.getElementsByTagName(s)[0];' . "\n";
        echo 'if (d.getElementById(id)) return;' . "\n";
        echo 'js = d.createElement(s); js.id = id;' . "\n";
        echo 'js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";' . "\n";
        echo 'fjs.parentNode.insertBefore(js, fjs);' . "\n";
        echo '}(document, \'script\', \'facebook-jssdk\'));</script>' . "\n";
}

function smashify_facebook_page_plugin_likebox()
{
    $fm_data_href = get_option('smashify_facebook_page_plugin_data_href');
    $fm_brodercolor = get_option('smashify_facebook_page_plugin_broder_color');
    $fm_data_hidecover = get_option('smashify_facebook_page_plugin_data_hide_cover');
    $fm_data_facepile = get_option('smashify_facebook_page_plugin_data_show_facepile');
    $fm_data_showposts = get_option('smashify_facebook_page_plugin_data_show_posts');


    $T1 = '<div class="fb-page" data-href="'.$fm_data_href . '" data-hide-cover="'.$fm_data_hidecover.'" data-show-facepile="'.$fm_data_facepile.'" data-show-posts="'.$fm_data_showposts.'"><div class="fb-xfbml-parse-ignore"></div></div>';
 
    $output = $T1;

    return $output;
}

function smashify_facebook_page_plugin_add_option_page()
{
    if (function_exists('add_options_page')) {
        add_options_page('Facebook Page Plugin', 'Facebook Page Plugin', 8, __FILE__, 'smashify_facebook_page_plugin_options_page');
    }
}

function smashify_facebook_page_plugin_options_page()
{
    $smashify_facebook_page_plugin_data_hide_cover = $_POST['smashify_facebook_page_plugin_data_hide_cover'];
    $smashify_facebook_page_plugin_data_show_facepile = $_POST['smashify_facebook_page_plugin_data_show_facepile'];
    $smashify_facebook_page_plugin_data_show_posts = $_POST['smashify_facebook_page_plugin_data_show_posts'];
    $smashify_facebook_page_plugin_widget_data_hide_cover = $_POST['smashify_facebook_page_plugin_widget_data_hide_cover'];
    $smashify_facebook_page_plugin_widget_data_show_facepile = $_POST['smashify_facebook_page_plugin_widget_data_show_facepile'];
    $smashify_facebook_page_plugin_widget_data_show_posts = $_POST['smashify_facebook_page_plugin_widget_data_show_posts'];
  
    if (isset($_POST['info_update'])) {
    	
    	if (!isset($_POST['my_fmz_update_setting'])) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you! ");
    	if (!wp_verify_nonce($_POST['my_fmz_update_setting'],'fmz-update-setting')) die("<br><br>Hmm .. looks like you didn't send any credentials.. No CSRF for you!");

        update_option('smashify_facebook_page_plugin_data_href', (string)$_POST["smashify_facebook_page_plugin_data_href"]);
        update_option('smashify_facebook_page_plugin_data_hide_cover', (string)$_POST["smashify_facebook_page_plugin_data_hide_cover"]);
        update_option('smashify_facebook_page_plugin_data_show_facepile', (string)$_POST['smashify_facebook_page_plugin_data_show_facepile']);
        update_option('smashify_facebook_page_plugin_data_show_posts', (string)$_POST['smashify_facebook_page_plugin_data_show_posts']);

        update_option('smashify_fbmembers_show_sponser_link', ($_POST['smashify_fbmembers_show_sponser_link'] == '1') ? '1' : '-1');

        update_option('smashify_facebook_page_plugin_widget_data_href', (string)$_POST['smashify_facebook_page_plugin_widget_data_href']);
        update_option('smashify_facebook_page_plugin_widget_title', (string)$_POST['smashify_facebook_page_plugin_widget_title']);
        update_option('smashify_facebook_page_plugin_widget_data_hide_cover', (string)$_POST['smashify_facebook_page_plugin_widget_data_hide_cover']);
        update_option('smashify_facebook_page_plugin_widget_data_show_facepile', (string)$_POST['smashify_facebook_page_plugin_widget_data_show_facepile']);
        update_option('smashify_facebook_page_plugin_widget_data_show_posts', (string)$_POST['smashify_facebook_page_plugin_widget_data_show_posts']);

        echo '<div id="message" class="updated fade"><p><strong>Settings saved.</strong></p></div>';
        echo '</strong>';
    } else {

        $as_facebook_mem_stream = get_option('as_facebook_mem_stream');
        $as_facebook_mem_header = get_option('as_facebook_mem_header');
        $as_facebook_mem_faces = get_option('as_facebook_mem_faces');
        $as_facebook_mem_widget_stream = get_option('as_facebook_mem_widget_stream');
        $as_facebook_mem_widget_faces = get_option('as_facebook_mem_widget_faces');

        $smashify_facebook_page_plugin_data_hide_cover = get_option('smashify_facebook_page_plugin_data_hide_cover');
        $smashify_facebook_page_plugin_data_show_facepile = get_option('smashify_facebook_page_plugin_data_show_facepile');
        $smashify_facebook_page_plugin_data_show_posts = get_option('smashify_facebook_page_plugin_data_show_posts');
        $smashify_facebook_page_plugin_widget_data_hide_cover = get_option('smashify_facebook_page_plugin_widget_data_hide_cover');
        $smashify_facebook_page_plugin_widget_data_show_facepile = get_option('smashify_facebook_page_plugin_widget_data_show_facepile');
        $smashify_facebook_page_plugin_widget_data_show_posts = get_option('smashify_facebook_page_plugin_widget_data_show_posts');
   }
  
    require_once (dirname(__FILE__) . '/includes/settings-page.php');

}

function show_smashify_facebook_page_plugin_likebox_widget($args)
{
    extract($args);

    $fm_widget_data_href = get_option('smashify_facebook_page_plugin_widget_data_href');
    $fm_widget_title = get_option('smashify_facebook_page_plugin_widget_title');
    $fm_widget_border_color = get_option('smashify_facebook_page_plugin_widget_border_color');
    $fm_widget_hide_cover = get_option('smashify_facebook_page_plugin_widget_data_hide_cover');
    $fm_widget_show_facepile = get_option('smashify_facebook_page_plugin_widget_data_show_facepile');
    $fm_widget_show_posts = get_option('smashify_facebook_page_plugin_widget_data_show_posts');
 
    $T1 = '<div class="fb-page" data-href="'.$fm_widget_data_href . '" data-hide-cover="'.$fm_widget_hide_cover.'" data-show-facepile="'.$fm_widget_show_facepile.'" data-show-posts="'.$fm_widget_show_posts.'"><div class="fb-xfbml-parse-ignore"></div></div>';
 
    $sponser = get_option('smashify_fbmembers_show_sponser_link');

    if ($sponser == 1) {
        $sponserlink_profile = "";
    } else {
        $sponserlink_profile = '<div align="left">- <a href="https://smashr.org/facebook-page-plugin-likebox-for-wordpress/" title="Facebook Page Plugin by Smashr.org" target="_blank"> <font size="1">' . 'Facebook Page Plugin by Crunchlr' . '</font></a></div>';
    }


    echo $before_widget;
    echo $before_title . $fm_widget_title . $after_title;
    echo $border_start . $T1 . $border_end . $sponserlink_profile;
    echo $after_widget;
}


function smashify_facebook_page_plugin_likebox_widget_control()
{
    ?>
<p>
    <? _e("Please go to <b>Settings -> Facebook Page Plugin</b> for all required options. "); ?>
</p>
<?php
}

function widget_smashify_facebook_page_plugin_likebox_init()
{
    $widget_options = array('classname' => 'widget_smashify_facebook_page_plugin_likebox', 'description' => _("Display Facebook Like Plugin"));
    wp_register_sidebar_widget('smashify_facebook_page_plugin_likebox_widgets', _('Facebook Page Plugin'), 'show_smashify_facebook_page_plugin_likebox_widget', $widget_options);
    wp_register_widget_control('smashify_facebook_page_plugin_likebox_widgets', _('Facebook Page Plugin'), 'smashify_facebook_page_plugin_likebox_widget_control');
}

function facebook_plugin_admin_init()
{
	wp_enqueue_script('jquery');                    // Enque Default jQuery
	wp_enqueue_script('jquery-ui-core');            // Enque Default jQuery UI Core
	wp_enqueue_script('jquery-ui-tabs');            // Enque Default jQuery UI Tabs
	
    wp_register_script('facebook-plugin-script3', plugins_url('/js/myscript.js', __FILE__));
    wp_enqueue_script('facebook-plugin-script3');

    wp_register_script('facebook-plugin-script4', plugins_url('/js/jquery.powertip.js', __FILE__));
    wp_enqueue_script('facebook-plugin-script4');

    wp_register_style('facebook-plugin-css', plugins_url('/css/jquery-ui.css', __FILE__));
    wp_enqueue_style('facebook-plugin-css');

    wp_register_style('facebook-tip-plugin-css', plugins_url('/css/jquery.powertip.css', __FILE__));
    wp_enqueue_style('facebook-tip-plugin-css');

    wp_register_style('facebook-page-plugin-css', plugins_url('/css/facebook-page-plugin.css', __FILE__));
    wp_enqueue_style('facebook-page-plugin-css');
}

add_action('admin_menu', 'facebook_plugin_admin_init');

add_filter('the_content', 'filter_smashify_facebook_page_plugin_likebox');
add_action('init', 'widget_smashify_facebook_page_plugin_likebox_init');
add_action('admin_menu', 'smashify_facebook_page_plugin_add_option_page');
add_action('wp_head', 'facebook_page_plugin_head');
?>