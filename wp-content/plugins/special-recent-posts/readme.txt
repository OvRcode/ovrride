=== Special Recent Posts ===
Contributors: lgrandicelli
Tags: recent posts, recent, post, wordpress, plugin, thumbnail, thumbnails, widget, pagination, custom post type, taxonomy, taxonomies, featured image, featured, home page, preview, categories, tags, content, filter, comment, most commented
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 2.0.4
License: GPLv3 or later

The most beautiful and powerful way to display your Wordpress posts with thumbnails.

== Description ==

Special Recent Posts FREE Edition is one of the most advanced Wordpress plugin to manage your posts with thumbnails. With an incredible easy configuration and a wonderful look, you're just a click away from setting up your awesome layout.
**It's the perfect solution for Web Magazines** or just **Simple Blogs** and it comes with **more than 40+ Customization Options Available**.


= Features Included =

* Wonderful Dynamic Widget Interface
* Responsive Layout
* Advanced Thumbnail Management
* Incredible Powerful Thumbnail Adapative Generation
* New Thumbnail Cache Support
* Advanced Post Filtering Techniques
* Advanced Post Content Display
* Custom CSS Editor
* Multiple Widget Configurations
* PHP Code/Shortcodes Support
* More than 40+ Customization Options


**Plugin Homepage**<br />
[http://www.specialrecentposts.com/](http://www.specialrecentposts.com/ "The Special Recent Posts Official Website")

**Online Documentation**<br />
[http://www.specialrecentposts.com/docs/](http://www.specialrecentposts.com/docs/ "The Special Recent Posts Online Documentation")

Follow Special Recent Posts on [Facebook](https://www.facebook.com/SpecialRecentPosts/ "Follow SRP on Facebook"), [Twitter](https://twitter.com/lucagrandicelli "Follow Luca Grandicelli on Twitter"), [Google+](https://google.com/+Specialrecentposts/ "Follow SRP on Google+")

== Installation ==

= HOW TO INSTALL THE PLUGIN =
The automatic plugin installer should work for most people. Manual installation is easy and takes fewer than five minutes.

1. Download the plugin, unpack it and upload the '<em>special-recent-posts-free</em>' folder to your wp-content/plugins directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to WP Menu -&gt; SRP FREE -&gt; General Settings to configure the global plugin options.
4. In the widgets panel, drag the Special Recent Posts FREE Edition widget onto one of your sidebars or draggable areas and configure its specific settings.
5. You're done. Enjoy.

= HOW TO UPGRADE FROM v1.9.* TO 2.0.0 =

**Before upgrading to the latest version 2.0.0, please DO A BACKUP of your custom CSS styles (if any) and read the changelog carefully. Some CSS classes have been renamed, so make sure you apply the correct changes. A brand new Custom CSS Editor is now available to put all of your custom CSS code in just one place.**


**Shortcodes / PHP Code**<br />
If you need to use the Special Recent Posts FREE Edition in another part of your theme which is not widget-handled, **you need shortcodes or PHP code**. Please refer to the [online documentation](http://www.specialrecentposts.com/docs/ "The Special Recent Posts Online Documentation") to know how to use them.

== Changelog ==
= 2.0.4 =
* Added compatibility check for the PHPThumbFactory Class.
* Fixed issue that prevented correct assets loading in some Wordpress themes.
* Bug Fixing.

= 2.0.3 =
* Fixed URF-8 Encoding on &nbsp; characters added by Wordpress.

= 2.0.2 =
* Improved CSS compatibility with old browsers.
* Fixed wrong characters cut and words count for Cyrillic charset.

= 2.0.1 =
* Fixed issue that prevented SRP to show when invoked by a PHP call.

= 2.0.0 =
* Completely rewritten engine. More flexible, more powerful.
* Redesigned widget style & admin section.
* Added translation support.
* Added new Custom CSS section in the Settings Panel.
* Added new option for Sticky Posts Management.
* Added new option to show all posts/pages without post limit.
* Added two more entries to the 'post_status' filter option: 'Auto Draft' and 'Any Type'.
* Added two more entries to the 'post_type' option: 'Revision' and 'Any Type'.
* Added new option to set the Thumbnail Image Quality Ratio.
* Added new option to select the HTML header for post titles.
* Added new option to enable external plugins shortcodes inside the post content.
* Added new option to enable Wordpress filters before outputting the post content.
* Added compatibility with Wordpress 4.0
* Updated file caching system.
* Updated plugin file structure
* Updated widget style
* Updated widget credits
* Updated jQuery DOM Ready Wrapper.
* Modified: Random mode now randomize all blog posts, and not only the recent ones.
* Modified: CSS class 'srp-widget-excerpt' has become 'srp-post-content'.
* Modified: CSS class 'srp-widget-date' has become 'srp-post-date'.
* Modified: CSS class 'srp-widget-stringbreak-image' has become 'srp-post-stringbreak-image'.
* Modified: CSS class 'srp-widget-stringbreak-link-image' has become 'srp-post-stringbreak-link-image'.
* Modified: CSS class 'srp-widget-stringbreak-link' has become 'srp-post-stringbreak-link'.
* Modified: CSS class 'srp-widget-thmblink' has become 'srp-post-thumbnail-link'.
* Modified: CSS class 'srp-post-thmb' has become 'srp-post-thumbnail'.
* Fixed long filenames encryption failure. Now thumbnails are cached with a new nomenclature that prevents issues with encryption length.
* Fixed issue that prevented correct display of thumbnails with the Jetpack plugin Photon Module installed.
* Fixed little bug that made the accordion tab re-fold if clicked more than once.
* Fixed issue that rendered shrinked images on Google Chrome.
* Fixed issue that prevented the user to click on the Settings link from the plugin page.
* Fixed issue that prevented the user to hit the save button when SRP is embedded in SiteBuilder by Origin.
* Fixed issue that generated extra comments tag when cutting post content with the "allowed Tags" option enabled.
* Fixed issue that generated extra comments tag when cutting post content with the "allowed Tags" option enabled.
* Function that cuts text preserving HTML tags has been fixed and improved.
* Now the featured image search is built inside the wp_query. No more posts buffer. More efficiency, more speed.
* Massive bug fixing.

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
* Rewritten Engine (PRO Edition clone)
* Added a new option to switch between post content or post excerpt in visualization mode.
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
= 2.0.2 =
Improved CSS compatibility with old browsers and fixed wrong characters cut and words count for the Cyrillic charset.


== Frequently Asked Questions ==

= Everything works but i see no thumbnails. =

This issue might be caused by several problems. Please check the following list.

1. First of all, make sure your posts have at least one image inserted in the post content or a featured image assigned. Posts with no images are displayed with the default "No Image" placeholder.
2. Set the correct permissions on the cache folder. In order to generate the thumbnails, the plugin needs to write files in the cache folder, located under special-recent-posts-free/cache/ Be sure this folder is set to 0775 or 0777. Please ask to your system administrator if you're not sure what you are doing.
3. Thumbnails are rendered using the PHP GD libraries. These should be enabled on your server. Do a phpinfo() on your host to check if they're installed properly. Contact your hosting support to know how to enable them.
4. Another problem could be that you're hosting the plugin on a MS Windows based machine. This will probably change the encoding data inside the files and could lead to several malfunctions. Better to host your site on a Unix/Linux based environment.
5. If you're using some **CDN service like Photon from the Jetpack plugin**, be aware that images hosted on a different domain could lead to potential malfunctions. In this case, Special Recent Posts FREE Edition will try to do its best to display your images, but sometimes this can't be done. Please consider to switch off the Jetpack Photon Extension.

= Category Filtering isn't working. / How do i find a category ID to filter posts by? =

In order to properly filter posts by categories, you must provide a numeric value which is the Category ID.
Every Wordpress category has an unique identification number, and this can be found doing the following steps:

1. Go in the  Posts->Categories panel.
2. Mouse over a category name.
3. Look at the status bar at the very bottom of your browser window: you'll find a long string containing a parameter called **tag_ID** and its following value.
4. Take note of that number, which is the relative Category ID to insert in the SRP FREE filtering panel.


**NOTE**: Please remember that this procedure is also valid for post filtering, where instead of category IDs, you have to look for your post IDs by hovering with the mouse on their titles.

= The widget title looks awful. How can i display it just like all other widgets? =

In this case, you have to deactivate the default SRP behaviour which customizes the widget title with custom HTML headings and styles.

1. Go to your Special Recent Posts FREE Edition widget under the "**Layout Options**".
2. Turn on the "**Use Default Wordpress HTML Layout for Widget Title**" option.


== Screenshots ==

1. The Default Layout
2. The Plugin General Settings: The Custom CSS Editor
3. The Wonderful Widget Interface
4. The Thumbnail Options Panel
5. The Advanced Post Options Panel
6. The Filtering Options Panel
7. The Layout Options Panel

== Requirements ==

In order to work, Special Recent Posts FREE Edition plugin needs the following settings:

1. Wordpress release 3.0+
2. PHP version 5+
3. GD libraries installed and enabled on your server.
4. Correct permissions (0775 or 0777) on cache folder under special-recent-posts-free/cache

== Translations ==

* English - default, always included
 
*Note:* This plugin is localized/translatable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful. For translating I recommend the awesome ["Codestyling Localization"](http://wordpress.org/extend/plugins/codestyling-localization/) plugin and for validating the ["Poedit Editor"](http://www.poedit.net/).
 
== Credits ==

* The Special Recent Posts plugin for Wordpress is created, developed and supported by [Luca Grandicelli](http://www.lucagrandicelli.co.uk/ "Luca Grandicelli | Official Website")