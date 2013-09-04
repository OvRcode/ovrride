<?php
/**
 * Plugin Name: OvRride Custom Functions
 * Plugin URI: https://github.com/AJAlabs/aja_functions
 * Description: Custom WordPress functions.php for OvRride.
 * Author: AJ Acevedo
 * Author URI: http://ajacevedo.com
 * Version: 0.1.0
 * License: MIT License
 */

/* Place custom code below this line. */

// Remove WordPress version meta generator from head
remove_action('wp_head', 'wp_generator');


// Remove Windows Live Writer meta from head
remove_action('wp_head', 'wlwmanifest_link');


// Adds Google Analytics to the footer
add_action('wp_footer', 'add_google_analytics');
  function add_google_analytics() { ?>
<!-- Google Analytics: -->
<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-11964448-1']);
  _gaq.push(['_setDomainName', 'ovrride.com']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<?php }


// Replace Howdy with Whats up, in the Admin Toolbar
function replace_howdy( $wp_admin_bar ) {
    $my_account=$wp_admin_bar->get_node('my-account');
    $newtitle = str_replace( 'Howdy,', 'What up,', $my_account->title );
    $wp_admin_bar->add_node( array(
        'id' => 'my-account',
        'title' => $newtitle,
    ) );
}
add_filter( 'admin_bar_menu', 'replace_howdy',25 );


// Adds the Manning avatar to Settings > Discussion
if ( !function_exists('fb_addgravatar') ) {
	function fb_addgravatar( $avatar_defaults ) {
		$manning_avatar = get_bloginfo('template_directory') . '/images/default_avatar.png';
		$avatar_defaults[$manning_avatar] = 'Manning';

		return $avatar_defaults;
	}

	add_filter( 'avatar_defaults', 'fb_addgravatar' );
}

/* Place custom code above this line. */
?>