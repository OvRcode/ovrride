=== Squelch Tabs and Accordions Shortcodes ===
Contributors: squelch
Donate link: http://squelchdesign.com/wordpress-plugin-squelch-tabs-accordions-shortcodes/
Tags: squelch,tabs, accordions,shortcodes,FAQs,tabbed,user interface,vaccordion,haccordion,thethe,thethe fly
Requires at least: 3.5
Tested up to: 4.5
Stable tag: 0.3.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shortcodes for creating accordions, horizontal accordions and tabs.

== Description ==

[Squelch Tabs and Accordions Shortcodes](http://squelchdesign.com/wordpress-plugin-squelch-tabs-accordions-shortcodes/) provides shortcodes for adding stylish Web 2.0
style accordions and tabs to your WordPress website: Horizontal accordions, vertical accordions and tabs. 

After you have installed the plugin you can use simple shortcodes on any page or post to add tabs or accordions.

Tabs and accordions can help to improve your website in a number of ways:

*   **Add interactivity**: With collapsible accordions and tabs, you can make better use of the available space on the page.
*   **Add style**: Tabs and accordions can help make your site look more professional and better polished than other sites.
*   **Save space**: Tabs and accordions can save a lot of space on the page making your website look less cluttered.
*   **Separating content**: Showing content only when required while the rest remains invisible dividing the content into parts.

If you want to add more interactivity with *Tabs*, *Vertical* and *Horizontal Accordions* on your WordPress website,
**Squelch Tabs and Accordions Shortcodes** is a good option.

**A note on responsivensss:**

We receive a lot of questions as to whether this plugin is responsive. All widgets in this plugin are responsive **excluding** the horizontal accordion widget: It uses a different library to the other widgets, and that library is unfortunately not responsive. We may add a responsive horizontal slider in the future, but it is not available yet.

== Installation ==

### Recommended Installation

1. From your admin interface go to Plugins > Add New
1. Search for *Squelch tabs and accordions*
1. Click "Install Now" under the plugin in the search results
1. Click OK on the popup
1. Click "Activate" to enable the plugin

Updates will be provided automatically through the WordPress updater.

### Manual Installation

1. Unzip the installation zip file
1. Copy the files to your plugins directory (via FTP or whatever)
1. From the admin interface click Plugins
1. Find the plugin in the list of plugins and click "Activate"

You will need to periodically check for updates.

== Frequently Asked Questions ==

= What shortcodes are available? =

Squelch Tabs and Accordions Shortcodes makes available 8 shortcodes: [accordions], [accordion], [haccordions], [haccordion], [tabs], [tab], [toggles] and [toggle]. The plural tags (accordions, haccordions, tabs, toggles) are wrappers that define the group of tabs or accordions. The singular shortcodes (accordion, haccordion, tab, toggle) represent the individual content items within each group.

= How do I use the plugin? =

Full instructions can be found on the [Squelch Tabs and Accordions Shortcodes](http://squelchdesign.com/wordpress-plugin-squelch-tabs-accordions-shortcodes/) project home page on the Squelch Web Design website.

= Can I change the look of the widgets? =

Yes. The plugin creates jQuery UI accordions and tabs, jQuery UI-compatible toggles, and the horizontal accordions have been set up to use jQuery UI themes by default. So changing the jQuery UI theme in use on the page will change the appearance of the widgets. To change the jQuery UI theme simply go to Appearance > Tabs and Accordions and choose a theme from the dropdown menu.

You can also style the widgets with your own custom CSS if you wish by using the .squelch-taas-override class, but that is beyond the scope of this FAQ. Instructions are available on the [Squelch Tabs and Accordions Shortcodes](http://squelchdesign.com/wordpress-plugin-squelch-tabs-accordions-shortcodes/) project home page.

= I used to use TheThe Tabs and Accordions, is this plugin compatible? =

Mostly compatible, yes. The initial goal of the plugin was to provide compatibility with that plugin as a way for those who've been left stranded by the demise of TheThe Tabs and Accordions plugin to continue using their accordions, horizontal accordions, tabs and toggles. I've changed some of the defaults for the widgets from what TheThe Tabs and Accordions used so if you've not been explicit when creating shortcodes you may notice some changes. I believe the defaults are better and more in line with what most people will want. Also, some minor functionality has still not been implemented so if you rely on those functions you might spot a few inconsistencies. Feel free to raise a support request on the plugin forum if functionality you require is missing.

= Is this a fork of TheThe Tabs and Accordions? =

No. This plugin has been written from the ground up as a new plugin that happens to be compatible with TheThe Tabs and Accordions. It provides a number of fixes to issues that were present in TheThe Tabs and Accordions, and even offers some new functionality.

= After installing the plugin I get a warning about a fix for TheThe Tabs and Accordions =

When TheThe Tabs and Accordions plugin stopped working I provided a temporary fix to help keep everyone's sites limping along. This fix was always intended to be temporary. If you receive the following message:

> Squelch Tabs and Accordions Shortcodes has detected that you are using a fix for TheThe Fly’s Accordions and Tabs plugin that was made available by Squelch some time ago. The fix in question is NOT intended as a long-term solution and should be removed as soon as possible. By using Squelch Tabs and Accordions you do NOT need the fix. Please see this article for instructions on how to remove the fix from your website.

then you still have the fix enabled on your website. You will need to find the fix (look for function thethe_fix) and remove it. More information can be found on the [Squelch Tabs and Accordions Shortcodes](http://squelchdesign.com/wordpress-plugin-squelch-tabs-accordions-shortcodes/) project home page.

= Can I see a demo of the widgets? =

Sure, on the [Squelch Tabs and Accordions Shortcodes](http://squelchdesign.com/wordpress-plugin-squelch-tabs-accordions-shortcodes/) project home page.

= Why does this plugin no longer work on versions of WordPress below 3.5? =

The plugin needs a recent version of jQuery UI, which in turn requires a reasonably recent version of jQuery. These are JavaScript libraries that are relied upon by the plugin. Versions of WordPress prior to 3.5 shipped with an older copy of these scripts that is too old for this plugin to use.

If you're on an older version of WordPress and still want to use the plugin then you will need to load a newer version of jQuery and jQuery UI. jQuery is pretty straightforward to upgrade (see the [jQuery updater plugin](http://wordpress.org/plugins/jquery-updater/)) but I've not been able to find a plugin that does the same thing for jQuery UI. Maybe if I can find time I'll write a plugin that offers this functionality, if I can get it past certain members of the WordPress.org plugin review team. It's also possible to add some code to your theme to achieve this goal, I'll try and put some documentation together for this shortly.

= Are these widgets responsive? =

All widgets is in this plugin are responsive excluding the horizontal accordion widget: It uses a different library to the other widgets, and that library is unfortunately not responsive. We may add a responsive horizontal slider in the future, but it is not available yet.

== Screenshots ==

1. Choose from 24 jQuery UI themes. Or, roll your own.
2. Horizontal Accordions come with 4 built-in themes, or you can design your own. Alternatively they can use the active jQuery UI theme.

== Changelog ==

= 0.3.6 =
* Bug fix: It's now possible to use paragraphs or newlines to lay out your shortcodes once again.
* Bug fix: Test for custom jQuery UI theme from jQuery UI Widgets (my thanks to RobWunderlich for reporting this bug)
* New feature: [tab] shortcode now accepts a parameter of 'class' to add additional classes to your tabs.
* New feature: Roll your own theme from the theme roller and store it in your uploads directory so that plugin upgrades do not affect your theme.
* Version bump for WP4.5

= 0.3.5 =
* Minor bug-fix: After changing themes the admin interface would still show the OLD theme, this has now been resolved.
* Version bump for WP4.4

= 0.3.4 =
* Emergency bug-fix: Paragraphs and breaks being interfered with

= 0.3.3 =
* Version bump (which should work)
* Putting icons live as they've been in the dev branch for too long

= 0.3.2 =
* Version bump (which went wrong)

= 0.3.1 =
* Minor fix for a warning in debug mode.

= 0.3 =
* The plugin was banned from the WordPress.org directory for providing jQuery from the Google CDN and allowing the user to choose the version of jQuery they wanted to run on their site. Apparently this contravenes the WordPress.org directory guidelines, despite the fact that the guidelines seem to make no mention of jQuery anywhere and despite the fact there are a number of plugins in the directory that do exactly the same thing. In order to get the plugin relisted it was necessary to use the WordPress-hosted version of jQuery and package the jQuery UI theme files. As a result the plugin no longer works on WP<3.5 and there's not much I can do about that.
* When you upgrade WordPress you may find that none of your widgets work any more due to a change in version of jQuery. This is a WordPress.org enforced change.

= 0.2.3 =
* Code to add .squelch-taas-override was missing, added in.
* Enabled nested accordions.
* Selecting "no jQuery UI theme" was causing the front-end to attempt to pull in a theme named 'none' from the CDN.

= 0.2.2 =
* autoHeight was always on regardless of settings on the accordions shortcode. If you prefer the old behaviour then ensure you have autoHeight="true" on the accordions shortcode.

= 0.2.1 =
* Fixed a bug that was preventing necessary default data being set in the options table on upgrade.

= 0.2 =
* Added in lighter text for haccordion's dark theme, was somehow missed out of 0.1.1
* Added subtabs, subsubtabs, subtab and subsubtab shortcodes to allow tabs in tabs up to 3 levels deep
* Added subhaccordions, subhaccordion, subsubhaccordions and subsubhaccordion shortcodes to allow nested horizontal accordions
* Added toggles and toggle shortcodes and created jQuery plugin to provide toggle functionality
* Added subtoggles, subtoggle, subsubtoggles and subsubtoggle shortocdes to allow nested toggles
* Created admin interface configuration page with theme switcher for jQuery UI themes
* All generated screen widgets get a class of squelch-taas-override to make overriding styles in themes easier
* Added option to allow jQuery and jQuery UI to not be loaded if desired

= 0.1.1 =
* Plugin now works on older versions of WordPress down to 3.3
* pauseonhover attribute was being erroneously ignored
* Fixes for styles in certain themes that might interfere with the horizontal accordions
* Dark theme now has grey text in the content area by default to make it easy to use out of the box
* Scripts and styles loaded from Google CDN for performance
* Fix for WordPress 3.4.2 (and possibly lower)

= 0.1 =
* Initial version

== Upgrade Notice ==

= 0.3.6 =
A couple of new features (ability to use a theme rolled theme stored outside of the plugin!), some bug fixes and version bump for WP4.5

= 0.3.5 =
Minor bug-fix and version bump for WordPress 4.4

= 0.3.4 =
Emergency bug-fix to prevent stripping of paragraphs / breaks

= 0.3.3 =
Version bump for WP 3.9 + introduction of icons in tabs

= 0.3.2 =
Version bump for WP 3.9 (failed)

= 0.3.1 =
Very minor code quality update.

= 0.3 =
This release has been forced by WordPress.org to load jQuery and jQuery UI as packaged with your current version of WordPress and therefore CANNOT support versions of WordPress prior to 3.5. If you are not on WP3.5 or better please use an older version of the plugin.

= 0.2.3 =
Bug-fix release. In 0.2.3 it is possible to nest accorions and it's easier to write your own styles for the widgets.

= 0.2.2 =
Fix for autoHeight being always on. If you want the old (incorrect) behaviour use autoHeight="true" on your accordions shortcode.

= 0.2.1 =
v0.2 introduces the long-awaited toggles widget, a configuration panel allowing you to choose your jQuery UI theme quickly and easily, and some elements can also now be nested! 0.2.1 offers a smoother upgrade experience.

= 0.2 =
v0.2 introduces the long-awaited toggles widget, a configuration panel allowing you to choose your jQuery UI theme quickly and easily, and some elements can also now be nested!

= 0.1.1 =
Fixes for various minor bugs and niggles and better support for some themes. Support for previous versions of WordPress down to 3.3.

= 0.1 =
Initial version
