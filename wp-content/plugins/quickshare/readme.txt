=== QuickShare ===
Contributors: celloexpressions
Tags: Social, Share, Sharing, Social Sharing, Social Media, Quick, Easy, Lightweight, No JS, Flexible, Customizable, Responsive, Facebook, Twitter, Pinterest, Linkedin, Google+, Tumblr, Email, Reddit, StumbleUpon
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.5
Description: Add quick social sharing functions to your content. Challenge social sharing norms with a flexible design and fast performance.
License: GPLv2

== Description ==
This plugin, like many others, adds content-sharing functions after your posts (and optionally pages and media attachments). But the similarities end there. 

QuickShare is quick because it doesn't run 3rd-party sharing JavaScript; in fact, there is **no front-end JS**. The sharing functions are built into links that take the user (in new tabs) to the native sharing interface on each social media site.

You can choose which social media sites to include from: Facebook, Twitter, Pinterest, Linkedin, Google+, Tumblr, Reddit, and StumbleUpon. A basic email function is also available.

The share bar appearance is highly customizable and share functions can be displayed as either icons, Genericons or text. Each display method features different customization options; customize through the settings page or with custom CSS.

QuickShare can be displayed at the end of ever post, page, and/or attachment, or all post types automatically. Or, you can use the `[quickshare]` shortcode to display QuickShare wherever you'd like in any of your posts/pages. You can also exclude posts by id, or even use a custom output function in your templates, including the ability to override the default generated sharing data.

As a bonus, QuickShare includes several built-in CSS3 effects for hover state animations.

**Please visit the plugin support forum for help with custom css snippets and/or feature requests!**

*Please note that QuickShare does not and will not support link tracking, because of its goal of being simple and lightweight. You can use a 3rd-party tool like Google Analytics to track shares as external links.*

== Installation ==
1. Take the easy route and install through the WordPress plugin adder OR
1. Download the .zip file and upload the unzipped folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure the settings in Settings -> QuickShare
1. Make sure that your theme has the `wp_head()` action hook and uses `the_content()`; this is where QuickShare hooks into. Alternately, add `<?php do_quickshare_output(); ?>` to your template files.

== Frequently Asked Questions ==
= Showing/Hiding QuickShare on specific pages and in specific parts of pages =
There are several ways to control where QuickShare is displayed:
* You can automatically have QuickShare filter on `the_content` for posts, pages, attachments, or all posts of any type, from the QuickShare Config settings page.
* You can hide QuickShare from certain posts/pages that are using the automatic filter (see above) by listing their post IDs in the field below the automatic filters. The post ID is numeric and can be found in the URL of a post's edit screen. This is the preferred method of hiding QuickShare from the homepage, or a contact page, etc. REQUIRES VERSION 1.4+
* Conversely, you can show QuickShare on specific pages by using the `[quickshare]` shortcode. This shortcode can be placed anywhere within a post or page, including in multiple places. You can use the shortcode with or without the automatic filtering of displaying on posts/pages/attachments, but be careful not to accidentally use the shortcode at the end of a post where the automatic filtering is turned on, because you'll get two instances of QuickShare right next to each other. To use the shortcode, simply add "`[quickshare]`" to your posts/pages wherever you want QuickShare to display. It's that easy! REQUIRES VERSION 1.4+
* Theme developers can use `<?php do_quickshare_output( $url, $title, $source, $description, $imgurl ); ?>` in their template files wherever sharing is desired, with the optional parameters allowing custom share data to be provided (see details below, under "share bar doesn't display").
* Plugin developers can do additional things like automatically displaying QuickShare at the beginning of the post instead of the end, or providing an alternate icon set. QuickShare is designed to be flexible, so (with the exception of stats) you should be able to do pretty much whatever you want.

= Optimizing Pinterest Sharing (and images) =
Pinterest is image-centric, so any content to be shared via Pinterest should include an image. QuickShare attempts to find an image in each post by first looking for a featured image, then grabbing the first attached image, looking for a raw image tag, or finally falling beck to a site-wide default image that you can set. You can also hide Pinterest sharing if no post image is found. The post image will also be used for Facebook and Google+ sharing, by default (if open graph meta tags are enabled). For best results, always set a featured image in your post.

= Customizing the Social Share Images =
QuickShare provides many options for design customization, but if you want to go as far as changing the share images (if Genericons aren't working for you either), you can! Just throw in some custom CSS, based on the code in `quickshare.css`. You could even use your own icon font in place of Genericons!

= Using Custom CSS Styling =
If you look at quickshare.css, you'll notice that the majority of the plugin's options are controlled through CSS. I recommend poking around in there to get some ideas, then adding your styles to the custom CSS field in the admin settings page. Placing the code here will ensure it overrides all plugin options. Depending on the share display type (icons/genericons/text), you can change icon and background colors and sizes, adjust shadows and borders, change how the plugin fits with your theme's layout (margins, padding, horizontal separations), add more CSS3 effects, and more! Unfortunately, it will be necessary to use `!important` in some places.

= Icons / Bullets / Arrows / Text display on top of/behind share icons =
An unfortunately large number of themes have not-fully-thought-out styling that interferes with QuickShare. Please post in the support forums with a link to your site and I can provide the appropriate custom CSS that will fix it for your site (I have seen themes do this in many, many different ways, so the steps QuickShare takes to resolve the issues are somethimes insufficient).

= Share bar doesn't display =
QuickShare can only display after posts use `the_content()`. One example of where QuickShare can't display is in gallery post formats on index and archive (not single view) pages in the Twenty Thirteen theme. However, if you have a custom theme or a child theme, you can customize the QuickShare output by adding `<?php do_quickshare_output(); ?>` to your template files somewhere within the loop.

For additional customization, you can use `<?php do_quickshare_output( $url, $title, $source, $description, $imgurl ); ?>`, where `$url` is the link to the content to share, `$title` is the title, `$source` is the name of your website/blog, `$description` is a short description, and `$imgurl` is the thumbnail image url. This version of the custom QuickShare output can be used anywhere in your templates, inside or outside of the loop, opening the door to many customization options. If any of the parameters are null, QuickShare will attempt to populate them with the default values (using the `$post` global). *Please remember to wrap this and all other plugin functions with `<a href="http://bradblogging.com/coding/stop-breaking-your-wordpress-theme-with-plugins-use-the-if-function-exists-option/">if(function_exists())</a>` to avoid killing your site if/when the plugin is deactivated*.

An example of a website using QuickShare in a custom context is <a href="http://euclidsmuse.com/app?id=269">Euclid's Muse, on app display pages</a>.

= Browser Support =
QuickShare is designed for modern browsers. I do not (and will not make much effort to) support IE8 or below, and do not support features that aren't achievable with minimal CSS in other outdated browsers (including, now, IE9). That being said, if you find a compatibility issue *with an easy fix*, let me know on the support forums and I'll consider including it. In the meantime, you can always add any desired CSS to the custom CSS field without worrying about losing plugin edits to updates. For the most part, everything should fail gracefully; older browsers won't see special effects like rounded corners, transitions/animations, box shadows, and opacity filters, but will be functionally fine. Genericons do not work in several browsers (including IE7 and IE9 mobile, but not IE9 desktop), so don't use that display option if you need lots of browser support.

= Sharing to additional social media networks =
If you think QuickShare should support sharing to additional networks, please let me know in the support forums and I'll consider adding support. I don't intend to add any more networks that would be enabled by default (or automatically enabled after an update), though.

= Sharing Numbers/statistics =
The one caveat to the bloat-less implementation and philosophy that QuickShare uses is that there is no good way to track shares. It is technically possible to track the number of times users take the initial action of pressing the share button on your site; however, there is no way to confirm that the action was completed without pulling in data from the various networks (typically via their javascripts). Furthermore, there is no clean way to store internal statistics (number of times each action was initiated) without polluting the database and making the sharing process more technically complex. Therefore, I don't plan on adding this functionality; if you find a possible alternative solution, please let me know and I will consider it. You should be able to track QuickShare shares as external links with analytics software such as Google Analytics if you're interested in tracking shares (versus displaying the data publicly).

= WordPress Version Support =
QuickShare does **not** work in WordPress versions below 3.5 (it will probably throw a php fatal error when attempting to activate). For best results, always use the latest version of WordPress.


== Screenshots ==
1. Admin settings screen with live-updating preview of design.
2. Default plugin display with the Twenty Thirteen theme.

== Changelog ==
= 1.5 =
* Improved Twitter sharing, with ability to include your username in the tweet by default (enter name on the settings page)
* Add support for Reddit and Stumbleupon with the Genericons display type
* Update Genericons to version 3.0.2 (includes redesigned email icon)
* Prevent QuickShare from displaying in widgets by default (with CSS, easy to override), for better compatibility with Twenty Fourteen's Ephemera Widget

= 1.4 =
* Introduce a shortcode for custom QuickShare output. You can now use `[quickshare]` anywhere in your posts/pages, even if QuickShare is disabled for that post type by default.
* Allow QuickShare to be excluded (hidden) from specific pages or posts, by id, in the settings page.
* Don't display QuickShare on `the_excerpt`, which can get especially nasty if themes strip out html in a later filter. You can manually add QuickShare back to these places in your templates.
* Hide QuickShare when printing web pages.
* Remove the opacity filter when using monochrome Genericons; you can set these colors to exactly the values you want for normal and hover states, so you don't need an opacity change to highlight it on mouseover.
* Tweak defaults to help encourage users to play around with the design options. The QuickShare Design settings page is meant to be fun, you can't really "break" anything there!
* Make the admin preview position draggable.
* Update Genericons to version 3.0.

= 1.3.1 =
* Fix php error from silly missing line of code 

= 1.3 =
* Improve the logic for whether or not to display QuickShare on a given object, fixing several hidden bugs in the process
* Improve base styling of text display type, with better padding and improved small icon positioning
* Add a small share icon before "Share" text when genericons are being used
* Add "skew" hover effect, in place of "spin" effect, for text display type
* Switch from deprecated jQuery live function to jQuery on function on admin settings page
* Update plugin banner image

= 1.2 =
* Change plugin output html structure from `<ul><a><li>` to `<ul><li><a><span>`. This allows the output to be valid html (`<a>`s aren't allowed as direct children of `<ul>`s). Refactored plugin CSS accordingly and was able to remove much of the styling for the share text. Custom CSS will probably also require some minor refactoring.
* Hide the responsive-small option when the size is already set to small.
* Add help text for the responsive design feature.
* Prevent QuickShare from displaying in feeds (ie, RSS).
* Fix broken updating of visible fields when initially changing display type.
* Add a reminder to always use `if_function_exists()` in conjunction with `do_quickshare_output()` to the FAQ

= 1.1 =
* Added the ability to shrink and/or hide QuickShare in smaller viewports/devices (defaults to off), on order to optimize display with responsive themes.
* Remove a stray javascript alert() from the admin page (which was leftover from debugging and occasionally created a popup window with the text "undefined" every time you changed an option...)
* Fix the spelling of "Pinterest" (was "Pintrest" everywhere)
* Note that Google+ also uses Open Graph data, like Facebook, so this option is **highly** recommended. It could help out with SEO too.

= 1.0 =
* First publicly available version of the plugin.
* Compatible with WordPress 3.5-3.6

== Upgrade Notice ==
= 1.5 =
* Improved Twitter sharing for better social engagement, hide from widgets, update Genericons to 3.0.2, adds Reddit and Stumbleupon Genericons.

= 1.4 =
* Better support for displaying QuickShare exactly where you want it, including new `[quickshare]` shortcode. Several other features and tweaks, see changelog.

= 1.3.1 =
* Improve base styling for text display type, add share icon when using Genericons, improve logic for displaying QuickShare, other minor fixes.

= 1.2 =
* Refactored html structure so that it validates, adjusted CSS accordingly. Custom CSS will need similar adjusting. Other minor fixes and enhancements.

= 1.1 =
* A couple of trivial but significant bugfixes, add a UI for basic responsiveness.

= 1.0 =
* Initial public release