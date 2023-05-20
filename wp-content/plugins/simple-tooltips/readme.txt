=== Simple Tooltips ===
Contributors: clevelandwebdeveloper
Donate link: http://www.clevelandwebdeveloper.com/wordpress-plugins/donate.php
Tags: tooltips, tips
Requires at least: 2.9
Tested up to: 5.0.3
Stable tag: 2.1.4
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily add tooltips to your wordpress site.

== Description ==

Easily add tooltips to your wordpress site. Tooltips will show when target element is hovered over. On mobile devices tooltips show when target element is tapped. You can easily pick your tooltip color settings in <strong>Settings > Simple Tooltips</strong>.

= How to Use =
To define a tooltip  (new, easy way):  Click the tooltip icon on your visual content editor to add the tooltip shortcode. Change the content attribute to whatever you want to be the content inside the tooltip bubble. Change the tooltip trigger to whatever content you want to be used to trigger the tooltip, when hovered. (see screenshots)

To define a tooltip (old way): Add class "tooltips" to the target html element. The title attribute will be used for tooltip content.

= Credits =
Thanks to Stefan Gabos who made the original <a href=\"http://stefangabos.ro/jquery/zebra-tooltips/\">jQuery plugin</a>.

== Installation ==

1. From WP admin > Plugins > Add New
1. Search "Simple Tooltips" under search and hit Enter
1. Click "Install Now"
1. Click the Activate Plugin link

== Frequently asked questions ==

= How do I add a tooltip (new, easy way)? =

Click the tooltip icon on your visual content editor to add the tooltip shortcode. Change the content attribute to whatever you want to be the content inside the tooltip bubble. Change the tooltip trigger to whatever content you want to be used to trigger the tooltip, when hovered. (see screenshots).

= How do I add visual elements inside the tooltip and as tooltip trigger? =

You should be able to edit the tooltip content and/or trigger to allow for html elements via the visual editor (such as links, images, colored text, bold, etc). One note of caution: if you are trying to use an image inside the bubble content, it's a good idea to set the image width and height directly by editing the inline style attribute (which you can access via the image html) for the img tag. This helps ensure that the bubble will register the image size correctly. Also, make sure to adjust the max bubble width to allow for the image. (see screenshots)

= How do I customize individual tooltips? =

The tooltip style settings you set on the Simple Tooltips setting page will apply to all your tooltips by default. However, you can now override the default tooltip style by editing the shortcode for that toolltip.

Here is the list of customizable parameters:
<ul>
	<li>bubblewidth (integer, in pixels) - Sets the max width for tooltip bubble. If you are trying to fit an image inside the bubble, and the image is too big for the tooltip bubble area, try adjusting the max bubble width to make sure it is big enough to the fit the desired image.</li>
	<li>bubbleopacity (integer, between 0 and 1) - Sets the bubble opacity. 0 is invisible and 1 is totally solid.</li>
	<li>bubblebgcolor (color name or hex value) - sets the bubble background color. Could be a color name, like green, or a hex value, like #666666.</li>
	<li>bubbleposition (left | center | right) - the position of the tooltip, relative to the trigger element</li>
	<li>bubblecolor (color name or hex value) - sets the bubble text color. Could be a color name, like green, or a hex value, like #666666. or a hex value.</li>
</ul>

And here's an example of how you could apply this:
<pre>[simple_tooltip bubblewidth='100' bubbleopacity='1' bubblebgcolor='#666666' bubbleposition='left' bubblecolor='blue' content='this content appears in the tooltip bubble']This triggers the tooltip[/simple_tooltip]</pre>
(see screenshots)

= How do I edit the inline style for a trigger element? =

You can add inline style attributes to the trigger element by adding the style attribute to the [simple_tooltips] shortcode. For example, add style='background:blue' to the simple_tooltip shortcode, to make the background color for the trigger element blue. Like this...
<pre class="lang:default decode:true">[simple_tooltip style='background:blue;color:white;' content='This is the content for the tooltip bubble. The tooltip bubble has the default style' ]This triggers the tooltip. The trigger background is blue and text color is white.[/simple_tooltip]</pre>
(see screenshots)

= How do I add a tooltip (older, html way)? =

Add class "tooltips" to the target html element. The title attribute will be used for tooltip content (see screenshot).

= How do I change tooltip color settings? =

Settings > Simple Tooltips.


== Screenshots ==

1. Simple tooltip in action
1. Adding a tooltip (the new, easy way)
1. Adding visual elements inside the tooltip and as tooltip trigger
1. Customizing individual tooltips
1. Editing the inline style for a trigger element
1. To set a tooltip (older, html way): add class "tooltips" to html element, use title attribute for tooltip message.
1. You can easily change tooltip color settings in Settings > Simple Tooltips
1. Simple tooltip in action on a WordPress menu item

== Changelog ==

= 2.1.4 =
* Security update

= 2.1.3 =
* Resolved error noticed in debug mode

= 2.1.2 =
* Adds compatibility with languages that use RTL (right to left) text

= 2.1.1 =
* Adds the option to disable tooltips on mobile

= 2.0 =
* Click tooltip button on visual editor to easily add tooltips
* Add visual elements like images, links, bold text, etc, inside tooltips and as tooltip trigger
* Customize individual tooltips
* Edit inline style for trigger element

= 1.1 =
* Set max width for tooltip
* Set tooltip opacity
* Set tooltip position
* Apply tooltips to WordPress menus

= 1.0 =
* Initial version

== Upgrade Notice ==

= 2.1.3 =
New: Resolves error notices in debug mode.