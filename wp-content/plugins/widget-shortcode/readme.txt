=== Widget Shortcode ===
Contributors: shazdeh
Plugin Name: Widget Shortcode
Tags: widget, shortcode, theme, admin
Requires at least: 3.0
Tested up to: 3.9.1
Stable tag: 0.2.3

Adds a [widget] shortcode which enables you to output widgets anywhere you like.

== Description ==

The shortcode requires the widget ID, but no need to guess, the plugin generates the code for you. You can use before_widget, after_widget, before_title and after_title parameters to override the widget arguments. Also you can use set 'title' parameter to false to suppress the widget title:
<code>[widget id="text-1" title="0"]</code>
Or override the default widget title in the shortcode:
<code>[widget id="text-1" title="New title"]</code>


== Installation ==

1. Upload the whole plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it. Use the [widget] shortcode anywhere you want
4. Enjoy!

== Screenshots ==

1. The plugin generates the shortcode for you

== Changelog ==

= 0.2.3 =
* Fixed missing widget classes and ID from before_widget

= 0.2.2 =
* Fixed a bug with removing titles from widget
* Compatibility with Widget Title Links and other plugins that change the widget args

= 0.2.1 =
* Added the ability to override the widget title in shortcode
* Support for filtering the default widget args
* Cleared the PHP notice

= 0.2 =
* Added do_widget function
* Added the 'title' option which enables to suppress the widget title