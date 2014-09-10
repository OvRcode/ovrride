=== Special Recent Posts ===
Contributors: lgrandicelli
Tags: recent, post, wordpress, plugin, thumbnails, widget, recent posts
Requires at least: 3.0
Tested up to: 3.5
Stable tag: 1.9.9
License: GPLv3 or later

The most beautiful and powerful way to display your recent posts with thumbnails.

== Description ==

<p>Special Recent Posts PRO is a very powerful plugin/widget for WordPress which displays your recent posts with thumbnails. It’s the perfect solution for online magazines or simple blogs and it comes with more than 30+ customization options available. You can dynamically re-size thumbnails to any desired dimension, drag multiple widget instances and configure each one with its specific settings.</p>

<strong>Special features</strong>:
<ul>
	<li>Wonderful Dynamic Widget Interface</li>
	<li>Thumbnail Adaptive Resize</li>
	<li>More than 30 Customization Options</li>
	<li>Advanced Post Filtering Techniques</li>
	<li>Advanced Post Content Display</li>
	<li>Multiple Widget Configurations</li>
	<li>Thumbnail cache support</li>
	<li>PHP Code/Shortcodes support</li>
</ul>

<p>
The complete list of features is available at the following links.
</p>

<p>
<strong>Plugin Homepage</strong><br />
http://www.specialrecentposts.com
</p>

<p>
<strong>Plugin Help Desk</strong><br />
http://www.specialrecentposts.com/support
</p>

<p>
<strong>Plugin online docs</strong><br />
http://www.specialrecentposts.com/docs
</p>

== Installation ==

The automatic plugin installer should work for most people. Manual installation is easy and takes fewer than five minutes.

1. Download the plugin, unpack it and upload the '<em>special-recent-posts</em>' folder to your wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to Settings -&gt; Special Recent Posts FREE page to configure the global options.
4. In the widgets panel, drag the Special Recent Posts FREE Edition widget onto one of your sidebars and configure its specific settings.
5. You're done. Enjoy.

If you wish to use the Special Recent Posts in another part of your theme which is not widget-handled, you can put the following snippet:

`
<?php
	if(function_exists('special_recent_posts')) {
		special_recent_posts($args);
	}
?>
`
where $args is an array of the following options:

`
// Enter the text for the main widget title.
'widget_title' => text

// Select if thumbnails should be displayed or not. (default: 'yes')
'display_thumbnail' => yes|no

// Hide widget title? (default 'no')
'widget_title_hide' => yes|no

// Select the type of HTML header to be used to enclose the widget title. (default: 'H3')
widget_title_header => H1|H2|H3|H4|H5|H6

// Enter a space separated list of additional css classes for this widget title header.
widget_title_header_classes => ''

// Set thumbnail width (default 100)
'thumbnail_width' => digit

// Set thumbnail height (default 100)
'thumbnail_height' => digit

// Link thumbnails to post? (default: 'yes')
'thumbnail_link' => yes|no

// Set thumbnail rotation mode (default 'no')
'thumbnail_rotation' => no|cw|ccw

// Set default displayed post types (default: 'post')
'post_type' => post|page

// Set default displayed post status (default: 'publish')
'post_status' => publish|private|inherit|pending|future|draft|trash

 // Set max number of posts to display (default: 5)
'post_limit' => digit

// Set displayed post content type (default: 'content')
'post_content_type' => 'content' | 'excerpt'

// Set displayed post content length (default: 100)
'post_content_length' => digit

// Set displayed post content length mode (default: 'chars')
'post_content_length_mode' => chars|words|fullcontent

// Set displayed post title length (default 100)
'post_title_length' => digit

// Set displayed post title length mode (default: 'chars')
'post_title_length_mode' => chars|words|fulltitle

// Set post order (default: 'DESC')
'post_order' => 'DESC' | 'ASC

// Set post offset (default: 0)
'post_offset' => digit

// Set random mode (default: 'no')
'post_random' => 'no' | 'yes'

// Hide current post from visualization when in single post view? (default: 'yes')
'post_current_hide' => 'yes' | 'no'

// Set layout content mode (default: 'titleexcerpt')
'post_content_mode' => thumbonly|titleexcerpt|titleonly

// Display post date? (default: 'yes')
'post_date' => yes|no

// Filter posts by including post IDs (default: none)
'post_include' => comma separated list of digits

// Exclude posts from visualization by IDs (default: none)
'post_exclude' => comma separated list of digits

// Filter post by Custom Post Type (default: none)
'custom_post_type' => comma separated list of custom post types

// Set the default 'No posts available' text (default: 'No posts available')
'noposts_text' => text

// Set allowed tags to display in the excerpt visualization.
'allowed_tags' => blankspace separated list of html tags

// Set string break text (default: [...])
'string_break' => text

// Set path to optional image string break.
'image_string_break' => text

// Link (image)string break to post?
'string_break_link'  => yes|no

// Set post date format. (default: 'F jS, Y')
'date_format' => text

// Filter posts by including categories IDs. (default: none)
'category_include' => comma separated list of digits

// When filtering by caqtegories, switch the widget title to a linked category title (default: 'no')
'category_title' => no|yes

// Add the 'no-follow' attribute to all widget links.
'nofollow_links' => no|yes
`

Example:
Show last 5 posts in random order filtering by category IDs 3 and 7. (PHP mode)
`
<?php
// Defining widget options.
$args = array(
	'post_limit'       => 5,
	'post_random'      => 'yes',
	'category_include' => 3, 7
);


// Function call.
special_recent_posts($args);
?>
`

If you wish to use a shortcode, put the following inside any of your post/pages:
`[srp]`

Shortcodes parameters names are the same of direct PHP call ones, but you have to put them with the '=' sign instead of the '=>' arrow.
String values must be enclosed within single/double quotes.

Example:
`[srp post_limit='5' post_random='yes' category_include=3,7]`

== Changelog ==

= 1.9.9 =
* Added new option to define the widget title HTML header.
* Added new option to define additional classes for the widget title.
* Fixed broken settings link in the plugin description.
* Fixed wrong count of post items when in random mode.
* Some CSS fixes.

= 1.9.8 =
* Fixed not valid XHTML <img> tag.
* Fixed bug that prevented category title to be displayed on pages.
* Fixed bug that prevented tag <br> to be displayed when filtered by the "allowed tags" option in shortcodes.

= 1.9.7 =
* Added al missing image attributes of width and height. This should solve some browser rendering problem.
* Removed the !important attribute from css-front.css
* Fixed wrong image path in wp multi site.
* Fixed wrong words count when cutting strings.

= 1.9.6 =
* Fixed a bug that prevented thumbnails from being displayed correctly.

= 1.9.5 =
* Rewritten Engine (PRO clone)
* Added a new opttion to switch between post content or post excerpt in visualization mode.
* Crucial fixes in the jquery handling.
* Better handling of stylesheets and scripts loading within the admin pages. This should solve many theme incompatibility issues.
* Fixed bug that prevented visualization on static front pages.
* Fixed wrong method call that leaded to some syntax error while activating the plugin.
* Added support for NextGen Gallery. Now if you set a post featured image by using the NextGen panel, it will show up instead of the no-image placeholder.
* Fixed Bug that prevented correct visualization when using PHP external calls or Shortcodes.
* Fixes for Wordpress 3.4
* Fixed a bug that prevented correct thumbnails visualization on Chrome and Safari.
* All SRP warnings and notices have now been moved within the SRP Control Panel.
* Fixed characters encoding bug.
* Added WP Multi-Site Support. (experimental)
* Improved tag rebuilding when allowed tags option is on.
* XAMPP compatibility issue fixed.
* Improved image retrievement process.
* Brand new dynamic widget interface.
* Many bugs fixed.

= 1.9.4 =
* Fixed Widget class name notation.

= 1.9.3 =
* Fixed some issue about image generation.

= 1.9.2 =
* Fixed image extraction issue.

= 1.9.1 =
* Fixed issue that prevented images from library to be displayed.
* Image generation logic rewritten.
* Minor bugs fixed

= 1.9 =
* Added Max Title text size option to single widget instance.
* Added Max Post text size option to single widget instance.

= 1.8 =
* Added Post Status support.
* Added Custom Post Type support.
* Added a new widget option for 'rel=nofollow' links.
* Added tabbed navigation on the top of the widget. Basic and Advanced.

= 1.7.2 =
* Minor bugs fixed.

= 1.7.1 =
* Added a "no-content" option on widget display mode. This will display only thumbnails, without titles, dates and excerpts.
* Widget drop-down category filtering has been replaced by a text field. Now you need to specify categories Ids separated by a comma.
* Fixed some IE7 CSS compatibility issue.
* Fixed bug in direct PHP call. (PHP error: argument #1 or #2 is not an array).

= 1.7 =
* Added shortcode support. The SRP shortcode must be inserted into posts/pages with the special code [srp]. Additional parameters are the same used for direct PHP calls. See readme.txt file for examples.
* Added widget option for thumbnail rotation. For very particular setups.
* Added a global post offset option, to skip an arbitrary number of posts from the beginning of the visualization.
* Added option to display/hide Widget Title.
* Fixed issue that lets included IDs override max number of posts option.
* Minor bugs fixed.

= 1.6.4 =
* Fixed widget title. Now when dragging a new SRP widget, the title is styled by the theme's default CSS, to prevent issues where the SRP title looked different from all other widget ones.
When using PHP call, the title is styled the old way, inside the plugin options panel.
* Fixed bug: Fixed issue that prevented puntuaction from displaying when in 'words cut' mode.

= 1.6.3 =
* FIX: Minor bug fixed.

= 1.6.2 =
* FIX: Another couple of security issues fixed.

= 1.6.1 =
* FIX: Important security issue fixed.

= 1.6 =
* Added a new option to include specific posts/pages in widgets.
* Fixed issue: Now thumbnail images paths are dinamically generated from the WP DB options, in case the default "uploads" folder is changed.

= 1.5.1 =
* Fixed some compatibility issue.

= 1.5 =
* Added a new general option which allows to use a button image as stringbreak.
* Added a new general option which allows to display a linked category title instead of the widget custom one when category filter is on.
* Added a new general option which allows particular html tags to be displayed in the generated excerpt text.
* Added an update method which automatically updates db options with newer plugin versions.
* Fixed Bug: Removed unwanted slashes when saving urls in general options
* Fixed Bug: Fixed some compatibility issue with qTranslate.
* Minor bugs fixed

= 1.4 =
* Fixed issue in the deactivation hook. Now plugin settings will be destroyed only when uninstalling and not when deactivating.
* Added custom thumbnails sizes. Now every widget instance has its own thumbnail sizes which will override the default ones.

= 1.3 =
* Added posts/pages exclusion from display view.

= 1.2 =
* Added an option to select whether to display posts or pages.
* Added a simple sanitize function to clear all outputs, avoiding unwanted slashes/backslashes or string breaks.

= 1.1 =
* Fixed issue that displays image captions inside generated excerpt.
* Minor bugs fixed.

= 1.0 = 
* Initial release

== Upgrade Notice ==

If you're upgrading from a version prior to 1.9.5, many of your old settings might be overwritten.
SO PLEASE MAKE SURE YOU MAKE A BACKUP OF YOUR OLD CUSTOM CSS AND WIDGET SETTINGS. 
If the upgrade process fails or if you're experiencing troubles with the plugin behaviour, please consider to completely uninstall the previous version and then re-install Special Recent Posts FREE Edition from scratch.
<br />
<strong>NOTES FOR MANUAL UPGRADE</strong>
If you wish to do a manual upgrade, please read the following steps:

1. Deactivate the old version in the Wordpress Plugin panel
2. Delete the special-recent-posts folder on your server, under wp-content/plugins/
3. Upload the new special-recent-posts folder to wp-content/plugins/
4. Refresh the Wordpress plugin page
5. Activate the plugin.

== Frequently Asked Questions ==

<p>
Please refer to the online Help Desk available at <a href="http://www.specialrecentposts.com/support">http://www.specialrecentposts.com/support</a> for a complete support and knowledge base.
</p>

= How do i edit the CSS? =

The stylesheet is located at wp-content/plugins/special-recent-posts/assets/css/css-front.css

= Everything works but i see no thumbnails. Only the "no image" placeholder =

This issue might be caused by several problems. Check the following list.
<ol>
<li>First of all, make sure the relative post has at least one image inserted in its text content or a featured image assigned. Posts with no images are displayed with the default "no image" placeholder.</li>
<li>Set the correct permissions on the cache folder. In order to generate the thumbnails, the plugin needs to write in the cache folder, located under special-recent-posts/cache/
Be sure this folder is set to 0775 or 0777. Please ask to your system administrator if you're not sure what you are doing.</li>
<li>Thumbnails are rendered using the PHP GD libraries. These should be enabled on your server. Do a phpinfo() on your host to check if they're installed properly. Contact your hosting support to know how to enable them.</li>
<li>Another problem could be that you're hosting the plugin on a MS Windows based machine. This will probably change the encoding data inside the files and could lead to several malfunctions. Better to host your site on a Unix/Linux based environment.</li>
<li>External images are not allowed. This means that if you're trying to generate a thumbnail from an image hosted on a different domain, it just won't work. This is usually not allowed for security reasons.</li>
</ol>

= Category/Post filtering isn't working =

In order to properly filter posts by categories, you must provide a numeric value which is the Category ID.
Every Wordpress category has an unique Identification number, and this can be found doing the following steps:
<ol>
<li>Go in the  Posts->Categories panel</li>
<li>Mouse over a category name.</li>
<li>Look at the status bar at the very bottom of your browser window. There you will find a long string containing a parameter called <strong>tag_ID</strong> and its following value.</li>
<li>Take note of that number, which is the relative Category ID to insert in the SRP PRO filtering panel.</li>
</ol>

NOTE: Please remember that this procedure is also valid for post filtering.

== Screenshots ==

1. The Post List
2. The Widget admin panel: Basic Options
3. The Widget admin panel: Thumbnails Options
4. The Widget admin panel: Posts Options
5. The Widget admin panel: Advanced Posts Options 1
6. The Widget admin panel: Advanced Posts Options 2
7. The Widget admin panel: Filtering Options
8. The Widget admin panel: Layout Options

== Requirements ==

In order to work, Special Recent Posts FREE Edition plugin needs the following settings:

1. PHP version 5+
2. GD libraries installed and enabled on your server.
3. Correct permissions (0775 or 0777) on cache folder under special-recent-posts/cache
