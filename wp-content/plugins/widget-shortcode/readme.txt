=== Widget Shortcode ===
Contributors: shazdeh
Plugin Name: Widget Shortcode
Tags: widget, shortcode, theme, admin
Requires at least: 3.0
Tested up to: 3.3.1
Stable tag: 0.2

Adds a [widget] shortcode which enables you to output a widget anywhere you like.

== Description ==

The shortcode requires the widget ID, but no need to guess, the plugin generates the code for you. You can use before_widget, after_widget, before_title and after_title parameters to override the widget arguments. Also you can use set 'title' parameter to false to suppress the widget title. here's an example: <code>[widget id="text-1" before_widget="" after_widget="" title="0"]</code>

Since 0.2 you can use do_widget function which accepts an array of options, so you can do: <code>do_widget( array(
	'id' => 'text-3',
	'title' => false,
	'before_widget' => '<div>'
) )</code>

== Installation ==

1. Upload the whole plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. That's it. use the [widget] shortcode anywhere you want.
4. Enjoy!

== Screenshots ==

1. Admin area

== Changelog ==

= 0.2 =
* Added do_widget function
* Added the 'title' option which enables to suppress the widget title